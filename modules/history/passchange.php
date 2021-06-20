<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('HistoryPassChangeTitle');
$passwordChangeTable = Flux::config('FluxTables.ChangePasswordTable');

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$passwordChangeTable WHERE account_id = ? OR user_id = ?";
$sth = $server->connection->getStatement($sql);
$sth->execute(array($session->account->account_id, $session->account->user_id));

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array('change_date', 'change_ip'));

$sql = "SELECT change_date, change_ip FROM {$server->loginDatabase}.$passwordChangeTable WHERE account_id = ? OR user_id = ?";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute(array($session->account->account_id, $session->account->user_id));

$changes = $sth->fetchAll();
?>
