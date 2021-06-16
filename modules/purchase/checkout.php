<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'Checkout Area';

if ($server->cart->isEmpty()) {
	$session->setMessageData('Your cart is currently empty.');
	$this->redirect($this->url('purchase'));
}
elseif (!$server->cart->hasFunds()) {
	$session->setMessageData('You do not have sufficient funds to make this purchase!');
	$this->redirect($this->url('purchase'));
}

$items = $server->cart->getCartItems();

if(Flux::config('MasterAccount')) {
	$accounts = $session->account->game_accounts['account_ids'];
	$user_names = $session->account->game_accounts['user_names'];

	if(!$accounts){
		$accountList ='<option value="-1">No Account available</option>';
	} else {
		$accountList = '';
		$acc_list = 0;
		foreach($accounts as $key => $account) {
			$accountList .='<option ';
			$accountList .= (!empty($_POST['select_account_id']) && $_POST['select_account_id'] == $account) ? 'selected' : '';
			$accountList .= ' value="'. $account .'">'. $user_names[$acc_list] .'</option>';
			$acc_list++;
		}
	}
}

if (count($_POST) && $params->get('process')) {
	$redeemTable = Flux::config('FluxTables.RedemptionTable');
	$creditTable = Flux::config('FluxTables.CreditsTable');
	$deduct      = 0;
	$selected_account = $params->get('select_account_id');
	
	$sql  = "INSERT INTO {$server->charMapDatabase}.$redeemTable ";
	$sql .= "(nameid, quantity, cost, account_id, char_id, redeemed, redemption_date, purchase_date, credits_before, credits_after) ";
	$sql .= "VALUES (?, ?, ?, ?, NULL, 0, NULL, NOW(), ?, ?)";
	$sth  = $server->connection->getStatement($sql);
	
	$balance = $session->account->balance;
	
	foreach ($items as $item) {
		$creditsAfter = $balance - $item->shop_item_cost;
		
		$res = $sth->execute(array(
			$item->shop_item_nameid,
			$item->shop_item_qty,
			$item->shop_item_cost,
			(Flux::config('MasterAccount') ? $selected_account : $session->account->account_id),
			$balance,
			$creditsAfter
		));
		
		if ($res) {
			$deduct  += $item->shop_item_cost;
			$balance -= $item->shop_item_cost;
		}
	}
	
	$session->loginServer->depositCredits(Flux::config('MasterAccount') ? $session->account->id : $session->account->account_id, -$deduct);
	
	if ($res) {
		if (!$deduct) {
			$server->cart->clear();
			$session->setMessageData('Failed to purchase all of the items in your cart!');
		}
		elseif ($deduct != $server->cart->getTotal()) {
			$server->cart->clear();
			$session->setMessageData('Items have been purchased, however, some failed (your credits are still there.)');
		}
		else {
			$server->cart->clear();
			$session->setMessageData('Items have been purchased.  You may redeem them from the Redemption NPC.');
		}
	}
	else {
		$session->setMessageData('Purchase went bad, contact an admin!');
	}
	
	$this->redirect();
}
?>
