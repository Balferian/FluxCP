<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('AccountViewTitle');

require_once 'Flux/TemporaryTable.php';

$fromTables = $this->DatabasesList($server->charMapDatabase, Flux::config('FluxTables.ItemsTable')->toArray(), $server->isRenewal);
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$usersTable    = Flux::config('FluxTables.MasterUserTable');
$creditsTable  = Flux::config('MasterAccount') ? Flux::config('FluxTables.MasterCreditsTable') : Flux::config('FluxTables.CreditsTable');
$creditColumns = "$creditsTable.balance, $creditsTable.last_donation_date, $creditsTable.last_donation_amount";
$createTable   = Flux::config('FluxTables.AccountCreateTable');
$createColumns = 'created.confirmed, created.confirm_code, created.reg_date';
$isMine        = false;
$accountID     = $params->get('id');
$account       = false;

if (!$accountID && Flux::config('MasterAccount')) {
	$this->deny();
}

if (!$accountID || $accountID == $session->account->account_id) {
	$isMine    = true;
	$accountID = $session->account->account_id;
	$account   = $session->account;
}

if ($accountID && Flux::config('MasterAccount')) {
	$account = $session->loginServer->getGameAccount($session->account->id, $accountID, $session->getAthenaServer($server->serverName));
	$isMine = !empty($account);
}

if (!$isMine) {
	// Allowed to view other peoples' account information?
	if (!$auth->allowedToViewAccount) {
		$this->deny();
	}

	$sql  = "SELECT {$server->charMapDatabase}.login.*, {$creditColumns}, {$createColumns}, ";
	$sql .= "(SELECT value FROM {$server->charMapDatabase}.`acc_reg_num` WHERE account_id = ".$accountID." AND `key` = '#CASHPOINTS') as 'cashpoints' ";
	$sql .= "FROM {$server->charMapDatabase}.login ";
	if(Flux::config('MasterAccount')) {
		$sql .= "LEFT OUTER JOIN $usersTable ON login.email = $usersTable.email ";
		$sql .= "LEFT OUTER JOIN cp_credits_master ON $usersTable.user_id = cp_credits_master.user_id ";
	} else
		$sql .= "LEFT OUTER JOIN {$server->loginDatabase}.{$creditsTable} AS credits ON login.account_id = credits.account_id ";
	$sql .= "LEFT OUTER JOIN {$server->loginDatabase}.{$createTable} AS created ON login.account_id = created.account_id ";
	$sql .= "WHERE login.sex != 'S' AND login.group_id >= 0 AND login.account_id = ? LIMIT 1";
	$sth  = $server->connection->getStatement($sql);
	$sth->execute(array($accountID));
	
	// Account object.
	$account = $sth->fetch();
	
	if ($account) {
		$title = sprintf(Flux::message('AccountViewTitle2'), $account->userid);
	}
}
else {
	$title = Flux::message('AccountViewTitle3');
}

$level       = AccountLevel::getGroupLevel($account->group_id);

$banSuperior = $account && (($level > $session->account->group_level && $auth->allowedToBanHigherPower) || $level <= $session->account->group_level);
$canTempBan  = !$isMine && $banSuperior && $auth->allowedToTempBanAccount;
$canPermBan  = !$isMine && $banSuperior && $auth->allowedToPermBanAccount;
$tempBanned  = $account && $account->unban_time > 0;
$permBanned  = $account && $account->state == 5;
$showTempBan = !$isMine && !$tempBanned && !$permBanned && $auth->allowedToTempBanAccount;
$showPermBan = !$isMine && !$permBanned && $auth->allowedToPermBanAccount;
$showUnban   = !$isMine && ($tempBanned && $auth->allowedToTempUnbanAccount) || ($permBanned && $auth->allowedToPermUnbanAccount);

$vipexpires = $server->loginServer->AccountVipTime($account->account_id, $server->charMapDatabase);

if (count($_POST) && $account) {
	$reason = (string)$params->get('reason');
	
	if ($params->get('tempban') && ($tempBanDate=$params->get('tempban_date'))) {
		if ($canTempBan) {
			if ($server->loginServer->temporarilyBan(Flux::config('MasterAccount') ? $session->account->id : $session->account->account_id, $reason, $account->account_id, $tempBanDate)) {
				$formattedDate = $this->formatDateTime($tempBanDate);
				$session->setMessageData("Account has been temporarily banned until $formattedDate.");
				$this->redirect($this->url('account', 'view', array('id' => $account->account_id)));
			}
			else {
				$errorMessage = Flux::message('AccountTempBanFailed');
			}
		}
		else {
			$errorMessage = Flux::message('AccountTempBanUnauth');
		}
	}
	elseif ($params->get('permban')) {
		if ($canPermBan) {
			if ($server->loginServer->permanentlyBan(Flux::config('MasterAccount') ? $session->account->id : $session->account->account_id, $reason, $account->account_id)) {
				$session->setMessageData("Account has been permanently banned.");
				$this->redirect($this->url('account', 'view', array('id' => $account->account_id)));
			}
			else {
				$errorMessage = Flux::message('AccountPermBanFailed');
			}
		}
		else {
			$errorMessage = Flux::message('AccountPermBanUnauth');
		}
	}
	elseif ($params->get('unban')) {
		$tbl = Flux::config('FluxTables.AccountCreateTable');
		$sql = "SELECT account_id FROM {$server->loginDatabase}.$tbl WHERE confirmed = 0 AND account_id = ?";
		$sth = $server->connection->getStatement($sql);
		
		$sth->execute(array($account->account_id));
		$confirm = $sth->fetch();
		
		$sql = "UPDATE {$server->loginDatabase}.$tbl SET confirmed = 1, confirm_expire = NULL WHERE account_id = ?";
		$sth = $server->connection->getStatement($sql);
		
		if ($tempBanned && $auth->allowedToTempUnbanAccount &&
				$server->loginServer->unban(Flux::config('MasterAccount') ? $session->account->id : $session->account->account_id, $reason, $account->account_id)) {
					
			if ($confirm) {
				$sth->execute(array($account->account_id));
			}
					
			$session->setMessageData(Flux::message('AccountLiftTempBan'));
			$this->redirect($this->url('account', 'view', array('id' => $account->account_id)));
		}
		elseif ($permBanned && $auth->allowedToPermUnbanAccount &&
				$server->loginServer->unban(Flux::config('MasterAccount') ? $session->account->id : $session->account->account_id, $reason, $account->account_id)) {
					
			if ($confirm) {
				$sth->execute(array($account->account_id));
			}
					
			$session->setMessageData(Flux::message('AccountLiftPermBan'));
			$this->redirect($this->url('account', 'view', array('id' => $account->account_id)));
		}
		else {
			$errorMessage = Flux::message('AccountLiftBanUnauth');
		}
	}
}

$banInfo = false;
if ($account) {
	if (Flux::config('MasterAccount')){
		$banInfo = $server->loginServer->getBanInfoGameAccount($account->account_id);
	} else {
		$banInfo = $server->loginServer->getBanInfo($account->account_id);
	}
}

$characters = array();
$serverName = $server->serverName;
$athena = $session->getAthenaServer($serverName);

$sql  = "SELECT ch.*, guild.name AS guild_name, ";
if(Flux::config('EmblemUseWebservice'))
	$sql .= "guild_emblems.file_data as guild_emblem_len ";
else
	$sql .= "guild.emblem_len AS guild_emblem_len ";
$sql .= "FROM {$athena->charMapDatabase}.`char` AS ch ";
$sql .= "LEFT OUTER JOIN {$athena->charMapDatabase}.guild ON guild.guild_id = ch.guild_id ";
if(Flux::config('EmblemUseWebservice'))
	$sql .= "LEFT JOIN {$server->charMapDatabase}.`guild_emblems` ON `guild_emblems`.guild_id = guild.guild_id ";	
$sql .= "WHERE ch.account_id = ? ORDER BY ch.char_num ASC";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($accountID));

$chars = $sth->fetchAll();
$characters[$athena->serverName] = $chars;

$StorageTables    = Flux::config('StorageList')->toArray();
foreach($StorageTables as $table => $data) {
	if($data[1] != "account")
		continue;
	$storage[$table] = array();
	$col  = "$table.*, items.name_english, items.type, items.slots, c.char_id, c.name AS char_name";

	$sql  = "SELECT $col FROM {$server->charMapDatabase}.$table ";
	$sql .= "LEFT JOIN {$server->charMapDatabase}.items ON items.id = $table.nameid ";
	$sql .= "LEFT JOIN {$server->charMapDatabase}.`char` AS c ";
	$sql .= "ON c.char_id = IF($table.card0 IN (254, 255), ";
	$sql .= "IF($table.card2 < 0, $table.card2 + 65536, $table.card2) ";
	$sql .= "| ($table.card3 << 16), NULL) ";
	$sql .= "WHERE $table.account_id = ? ";

	if (!$auth->allowedToSeeUnknownItems) {
		$sql .= 'AND $table.identify > 0 ';
	}

	if ($account) {
		$sql .= "ORDER BY $table.nameid ASC, $table.identify DESC, ";
		$sql .= "$table.attribute DESC, $table.refine ASC";

		$sth  = $server->connection->getStatement($sql);
		$sth->execute(array($account->account_id));

		$items = $sth->fetchAll();
		$cards = array();

		if ($items) {
			$cardIDs = array();

			foreach ($items as $item) {
				$item->cardsOver = -$item->slots;
				
				if ($item->card0) {
					$cardIDs[] = $item->card0;
					$item->cardsOver++;
				}
				if ($item->card1) {
					$cardIDs[] = $item->card1;
					$item->cardsOver++;
				}
				if ($item->card2) {
					$cardIDs[] = $item->card2;
					$item->cardsOver++;
				}
				if ($item->card3) {
					$cardIDs[] = $item->card3;
					$item->cardsOver++;
				}
				
				if ($item->card0 == 254 || $item->card0 == 255 || $item->card0 == -256 || $item->cardsOver < 0) {
					$item->cardsOver = 0;
				}

				if($server->isRenewal) {
					$temp = array();
					if ($item->option_id0)	array_push($temp, array($item->option_id0, $item->option_val0));
					if ($item->option_id1) 	array_push($temp, array($item->option_id1, $item->option_val1));
					if ($item->option_id2) 	array_push($temp, array($item->option_id2, $item->option_val2));
					if ($item->option_id3) 	array_push($temp, array($item->option_id3, $item->option_val3));
					if ($item->option_id4) 	array_push($temp, array($item->option_id4, $item->option_val4));
					$item->rndopt = $temp;
				}
			}

			if ($cardIDs) {
				$ids = implode(',', array_fill(0, count($cardIDs), '?'));
				$sql = "SELECT id, name_english FROM {$server->charMapDatabase}.items WHERE id IN ($ids)";
				$sth = $server->connection->getStatement($sql);

				$sth->execute($cardIDs);
				$temp = $sth->fetchAll();
				if ($temp) {
					foreach ($temp as $card) {
						$cards[$card->id] = $card->name_english;
					}
				}
			}
			$storage[$table] = $items;
		}
	}
	
	$itemAttributes = Flux::config('Attributes')->toArray();
	$type_list = Flux::config('ItemTypes')->toArray();
}
?>
