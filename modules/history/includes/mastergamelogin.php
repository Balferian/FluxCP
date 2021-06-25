<?php
if (!defined('FLUX_ROOT')) exit;

$accounts = $session->account->game_accounts['user_names'];
$account_list = array();
foreach($accounts as $key => $account) {
	$account_list[] = " {$server->logsDatabase}.loginlog.user = '$account' ";
}
$where = " (".implode('OR', $account_list).") ";

$sql = "SELECT COUNT(*) AS total FROM {$server->logsDatabase}.loginlog WHERE $where";
$sth = $server->connection->getStatementForLogs($sql);

$sth->execute(array());
$total = $sth->fetch()->total;

$paginator = $this->getPaginator($total);
$paginator->setSortableColumns(array(
	'time' => 'desc', 'ip', 'rcode', 'log'
));

$sql = "SELECT time, ip, rcode, log FROM {$server->logsDatabase}.loginlog WHERE $where";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatementForLogs($sql);
$sth->execute(array($session->account->account_id));

$logins = $sth->fetchAll();
?>