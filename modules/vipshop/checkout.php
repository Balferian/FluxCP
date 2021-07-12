<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'Checkout Area';
$categories    = Flux::config("VipCategories")->toArray();

if ($server->cartvip->isEmpty()) {
	$session->setMessageData('Your cart is currently empty.');
	$this->redirect($this->url('vipshop'));
}
elseif (!$server->cartvip->hasFunds()) {
	$session->setMessageData('You do not have sufficient funds to make this purchase!');
	$this->redirect($this->url('vipshop'));
}

$items = $server->cartvip->getCartItems();

if(Flux::config('MasterAccount')) {
	$accounts = $session->account->game_accounts['account_ids'];
	$usernames = $session->account->game_accounts['user_names'];

	if(!$accounts){
		$accountList ='<option value="-1">No Account available</option>';
	} else {
		$accountList = '';
		foreach($accounts as $key => $account) {
			$accountList .='<option ';
			$accountList .= (!empty($_POST['select_account_id']) && $_POST['select_account_id'] == $account) ? 'selected' : '';
			$accountList .= ' value="'. $account .'">'. $usernames[$key] .'</option>';
		}
	}
}

if (count($_POST) && $params->get('process')) {
	$redeemTable = Flux::config('FluxTables.VipRedemptionTable');
	$creditTable = Flux::config('FluxTables.CreditsTable');
	$deduct      = 0;
	$selected_account = $params->get('select_account_id');
	
	$sql  = "INSERT INTO {$server->charMapDatabase}.$redeemTable ";
	$sql .= "(category, quantity, cost, account_id, char_id, redeemed, redemption_date, purchase_date, credits_before, credits_after) ";
	$sql .= "VALUES (?, ?, ?, ?, NULL, ?, ?, NOW(), ?, ?)";
	$sth  = $server->connection->getStatement($sql);
	
	$balance = $session->account->balance;
	
	foreach ($items as $item) {
		$creditsAfter = $balance - $item->shop_item_cost;
		
		if(FLUX::config('MultiserverVipTime') && $item->shop_category == 2) {
			foreach ($session->loginAthenaGroup->athenaServers as $athenaServer) {
				$sql2  = "INSERT INTO {$athenaServer->charMapDatabase}.$redeemTable ";
				$sql2 .= "(category, quantity, cost, account_id, char_id, redeemed, redemption_date, purchase_date, credits_before, credits_after) ";
				$sql2 .= "VALUES (?, ?, ?, ?, NULL, ?, ?, NOW(), ?, ?)";
				$sth2  = $server->connection->getStatement($sql2);
				$res = $sth2->execute(array(
					$item->shop_category,
					$item->shop_item_qty,
					$item->shop_item_cost,
					(Flux::config('MasterAccount') ? $selected_account : $session->account->account_id),
					$server->loginServer->CheckOnlineChars($selected_account, $athenaServer->charMapDatabase) ? 0 : 1,
					$server->loginServer->CheckOnlineChars($selected_account, $athenaServer->charMapDatabase) ? NULL : date('Y-m-d H:i:s'),
					$balance,
					$creditsAfter
				));
				if($server->loginServer->CheckOnlineChars($selected_account, $athenaServer->charMapDatabase) == 0) {
					$server->loginServer->AddVipTime($selected_account, $item->shop_item_qty, $athenaServer->charMapDatabase);
				}
			}
		} else {
			$res = $sth->execute(array(
				$item->shop_category,
				$item->shop_item_qty,
				$item->shop_item_cost,
				(Flux::config('MasterAccount') ? $selected_account : $session->account->account_id),
				$server->loginServer->CheckOnlineChars($selected_account, $server->charMapDatabase) ? 0 : 1,
				$server->loginServer->CheckOnlineChars($selected_account, $server->charMapDatabase) ? NULL : date('Y-m-d H:i:s'),
				$balance,
				$creditsAfter
			));
			if($server->loginServer->CheckOnlineChars($selected_account, $server->charMapDatabase) == 0) {
				// Cashpoints
				if($item->shop_category == 0)
					$server->loginServer->AddPoints($selected_account, '#CASHPOINTS', $item->shop_item_qty, $server->charMapDatabase);
				// Kafrapoints
				if($item->shop_category == 1)
					$server->loginServer->AddPoints($selected_account, '#KAFRAPOINTS', $item->shop_item_qty, $server->charMapDatabase);
				// Vip time
				if($item->shop_category == 2)
					$server->loginServer->AddVipTime($selected_account, $item->shop_item_qty, $server->charMapDatabase);
			}
		}
		
		if ($res) {
			$deduct  += $item->shop_item_cost;
			$balance -= $item->shop_item_cost;
		}
	}
	
	$session->loginServer->depositCredits(Flux::config('MasterAccount') ? $session->account->id : $session->account->account_id, -$deduct);
	
	if ($res) {
		if (!$deduct) {
			$server->cartvip->clear();
			$session->setMessageData('Failed to purchase all of the services in your cart!');
		}
		elseif ($deduct != $server->cartvip->getTotal()) {
			$server->cartvip->clear();
			$session->setMessageData('Services have been purchased, however, some failed (your credits are still there.)');
		}
		else {
			$server->cartvip->clear();
			$session->setMessageData('Services have been purchased.  You may redeem them from the Redemption NPC.');
		}
	}
	else {
		$session->setMessageData('Purchase went bad, contact an admin!');
	}
	
	$this->redirect();
}
?>
