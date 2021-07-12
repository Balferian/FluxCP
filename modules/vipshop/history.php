<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'Pending Redemption History';
$categories    = Flux::config("VipCategories")->toArray();
$redeemTable = "{$server->charMapDatabase}.".Flux::config('FluxTables.VipRedemptionTable');

$accounts = $session->account->game_accounts['account_ids'];
$account_list = array();
foreach($accounts as $key => $account) {
	$account_list[] = " $redeemTable.account_id = $account ";
}
$sqlpartial = "WHERE (".implode('OR', $account_list).") ";

$sth = $server->connection->getStatement("SELECT COUNT(*) AS total FROM $redeemTable $sqlpartial");
$sth->execute();
$perPage       = FLUX::config('PendingResultsPerPage');
$paginator     = $this->getPaginator($sth->fetch()->total, array('perPage' => $perPage));
$paginator->setSortableColumns(array(
	'redemption_date' => 'desc'
));

$sql  = "SELECT *, login.userid FROM $redeemTable ";
$sql .= " JOIN {$server->charMapDatabase}.login ON login.account_id = $redeemTable.account_id ";
$sql .= "$sqlpartial";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);

$sth->execute();
$history = $sth->fetchAll();
?>
