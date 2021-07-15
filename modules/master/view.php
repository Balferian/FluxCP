<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();
if (!Flux::config('MasterAccount')) {
    $this->deny();
}
$title = Flux::message('AccountViewTitle');

require_once 'Flux/TemporaryTable.php';

$fromTables = $this->DatabasesList($server->charMapDatabase, Flux::config('FluxTables.ItemsTable')->toArray(), $server->isRenewal);
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$account   = $session->account;
$userId = $params->get('user_id');
$isMine = false;
$headerTitle = Flux::message('MasterAccountViewHeading');

if ($userId && $session->account->id !== $userId) {
    $isMine = false;
}

if (!$userId || $session->account->id === $userId) {
    $isMine = true;
}

if (!$isMine) {
    // Allowed to view other peoples' account information?
    if (!$auth->allowedToViewAccount) {
        $this->deny();
    }
    $usersTable = Flux::config('FluxTables.MasterUserTable');
	$creditsTable  = Flux::config('FluxTables.MasterCreditsTable');
	$creditColumns = 'credits.balance';

    $sql  = "SELECT *, {$server->loginDatabase}.{$usersTable}.user_id as id, $creditColumns FROM {$server->loginDatabase}.{$usersTable} ";
    $sql .= "LEFT JOIN {$server->loginDatabase}.{$creditsTable} AS credits ON {$server->loginDatabase}.{$usersTable}.user_id = credits.user_id ";
	$sql .= "WHERE {$server->loginDatabase}.{$usersTable}.user_id = ? LIMIT 1";
    $sth = $server->connection->getStatement($sql);
    $sth->execute(array($userId));
    $account = $sth->fetch();
    $headerTitle = $title = sprintf(Flux::message('MasterAccountViewHeading2'), $account->email);
}

$banInfo = false;
if ($account) {
	$banInfo = $server->loginServer->getBanInfoMaster($account->user_id);
}

$userAccounts = array();
$userAccountTable = Flux::config('FluxTables.MasterUserAccountTable');

$serverName = $server->serverName;
$athena = $session->getAthenaServer($serverName);

$sql  = "SELECT *, login.account_id, login.userid, login.logincount, login.lastlogin, login.last_ip, login.sex, login.`vip_time` as `vip_time` ";
$sql .= " ,(SELECT value FROM {$athena->charMapDatabase}.`acc_reg_num` WHERE account_id = login.account_id AND `key` = '#CASHPOINTS') as 'cashpoints' ";
$sql .= " FROM {$athena->loginDatabase}.{$userAccountTable} AS ua";
$sql .= " JOIN {$athena->charMapDatabase}.login ON login.account_id = ua.account_id ";
$sql .= " WHERE ua.user_id = ? ORDER BY ua.id ASC";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($account->id));

$userAccount = $sth->fetchAll();

$userAccounts[$athena->serverName] = $userAccount;

$StorageTables    = Flux::config('StorageList')->toArray();
foreach($StorageTables as $table => $data) {
	if($data[1] != "master")
		continue;
	$storage[$table] = array();
	$col  = "$table.*, items.name_english, items.type, items.slots, c.char_id, c.name AS char_name";

	$sql  = "SELECT $col FROM {$server->charMapDatabase}.$table ";
	$sql .= "LEFT JOIN {$server->charMapDatabase}.items ON items.id = $table.nameid ";
	$sql .= "LEFT JOIN {$server->charMapDatabase}.`char` AS c ";
	$sql .= "ON c.char_id = IF($table.card0 IN (254, 255), ";
	$sql .= "IF($table.card2 < 0, $table.card2 + 65536, $table.card2) ";
	$sql .= "| ($table.card3 << 16), NULL) ";
	$sql .= "WHERE $table.user_id = ? ";

	if (!$auth->allowedToSeeUnknownItems) {
		$sql .= 'AND $table.identify > 0 ';
	}

	if ($account) {
		$sql .= "ORDER BY $table.nameid ASC, $table.identify DESC, ";
		$sql .= "$table.attribute DESC, $table.refine ASC";

		$sth  = $server->connection->getStatement($sql);
		$sth->execute(array($account->id));

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
