<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();
if (!Flux::config('MasterAccount')) {
    $this->deny();
}
$title = Flux::message('AccountViewTitle');

require_once 'Flux/TemporaryTable.php';
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

    $sql  = "SELECT *, ";
	$sql .= "(SELECT balance FROM {$server->loginDatabase}.cp_credits_master WHERE user_id = ?) as balance "; 
	$sql .= "FROM {$server->loginDatabase}.{$usersTable} ";
	$sql .= "WHERE user_id = ? LIMIT 1";
    $sth  = $server->connection->getStatement($sql);
    $sth->execute(array($userId, $userId));
    $account = $sth->fetch();
    $headerTitle = $title = sprintf(Flux::message('MasterAccountViewHeading2'), $account->email);
}

$banInfo = false;
if ($account) {
	$banInfo = $server->loginServer->getBanInfoMaster($account->user_id);
}

$userAccounts = array();
$userAccountTable = Flux::config('FluxTables.MasterUserAccountTable');
foreach ($session->getAthenaServerNames() as $serverName) {
    $athena = $session->getAthenaServer($serverName);

    $sql  = "SELECT *, login.account_id, login.userid, login.logincount, login.lastlogin, login.last_ip, login.sex";
    $sql .= " FROM {$athena->charMapDatabase}.{$userAccountTable} AS ua";
    $sql .= " JOIN {$athena->charMapDatabase}.login ON login.account_id = ua.account_id ";
    $sql .= " WHERE ua.user_id = ? ORDER BY ua.id ASC";
    $sth  = $server->connection->getStatement($sql);
    $sth->execute(array($account->id));

    $userAccount = $sth->fetchAll();
    $userAccounts[$athena->serverName] = $userAccount;
}