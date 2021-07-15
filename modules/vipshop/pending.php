<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'Pending Redemption';
$categories    = Flux::config("VipCategories")->toArray();

try {
	$redeemTable = Flux::config('FluxTables.VipRedemptionTable');

	$accounts = Flux::config('MasterAccount') ? $session->account->game_accounts['account_ids'] : array($session->account->account_id);
	$account_list = array();
	foreach($accounts as $key => $account) {
        $account_list[] = " {$server->charMapDatabase}.$redeemTable.account_id = $account ";
    }
	// JOINs, conditions etc
	$sqlpartial = "LEFT OUTER JOIN {$server->loginDatabase}.login ON {$server->charMapDatabase}.$redeemTable.account_id = {$server->loginDatabase}.login.account_id ";
    $sqlpartial .= "WHERE (".implode('OR', $account_list).") ";
    $sqlpartial .= "AND redeemed < 1 ORDER BY purchase_date DESC";
	
	// Fetch item count.
	$sql = "SELECT COUNT($redeemTable.id) AS total FROM {$server->charMapDatabase}.$redeemTable $sqlpartial";
	$sth = $server->connection->getStatement($sql);
	
	$sth->execute(array());
	$total = $sth->fetch()->total;

	// Fetch items.
	$col = "login.userid, category, quantity, purchase_date, cost, credits_before, credits_after";
	$sql = "SELECT $col FROM {$server->charMapDatabase}.$redeemTable $sqlpartial";
	$sth = $server->connection->getStatement($sql);
	
	$sth->execute(array($session->account->account_id));
	$items = $sth->fetchAll();
}
catch (Exception $e) {
	if (isset($tempTable) && $tempTable) {
		// Ensure table gets dropped.
		$tempTable->drop();
	}
	
	// Raise the original exception.
	$class = get_class($e);
	throw new $class($e->getMessage());
}
?>
