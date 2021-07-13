<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();
if (!Flux::config('MasterAccount')) {
    $this->deny();
}
$title = Flux::message('AccountViewTitle');

require_once 'Flux/TemporaryTable.php';
$account       = $session->account;

$userAccounts = array();
$userAccountTable = Flux::config('FluxTables.MasterUserAccountTable');
$serverName = $server->serverName;

$athena = $session->getAthenaServer($serverName);

$sql  = "SELECT *, login.account_id, login.userid, login.logincount, login.lastlogin, login.last_ip, login.sex";
$sql .= " ,(SELECT value FROM {$athena->charMapDatabase}.`acc_reg_num` WHERE account_id = login.account_id AND `key` = '#CASHPOINTS') as 'cashpoints' ";
$sql .= " FROM {$athena->loginDatabase}.{$userAccountTable} AS ua";
$sql .= " JOIN {$athena->charMapDatabase}.login ON login.account_id = ua.account_id ";
$sql .= " WHERE ua.user_id = ? ORDER BY ua.id ASC";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($account->id));

$userAccount = $sth->fetchAll();
$userAccounts[$athena->serverName] = $userAccount;
