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
