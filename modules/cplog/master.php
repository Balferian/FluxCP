<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Master Account Registrations';

$loginLogTable = Flux::config('FluxTables.MasterAccountCreateTable');
$sqlpartial    = "WHERE 1=1 ";
$bind          = array();

$password    = $params->get('password');
$userID   = (int)$params->get('user_id');
$name    = trim($params->get('username'));
$email    = trim($params->get('email'));
$ipAddress   = trim($params->get('ip'));
$loginAfter  = $params->get('login_after_date');
$loginBefore = $params->get('login_before_date');

if ($password && $auth->allowedToSearchCpLoginLogPw) {
	$sqlpartial .= 'AND user_pass = ? ';
	$bind[]      = $session->loginAthenaGroup->loginServer->config->getUseMD5() ? md5($password) : $password;
}

if ($userID) {
	$sqlpartial .= 'AND user_id = ? ';
	$bind[]      = $userID;
}

if ($name) {
	$sqlpartial .= 'AND name LIKE ? ';
	$bind[]      = "%$name%";
}

if ($email) {
	$sqlpartial .= 'AND email LIKE ? ';
	$bind[]      = "%$email%";
}

if ($ipAddress) {
	$sqlpartial .= 'AND reg_ip LIKE ? ';
	$bind[]      = "%$ipAddress%";
}

if ($loginAfter) {
	$sqlpartial .= 'AND login_date >= ? ';
	$bind[]      = $loginAfter;
}

if ($loginBefore) {
	$sqlpartial .= 'AND login_date <= ? ';
	$bind[]      = $loginBefore;
}

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$loginLogTable $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'user_id', 'user_id', 'user_pass', 'reg_ip',
	'reg_date' => 'desc'
));

$sql = "SELECT user_id, name, user_pass, email, reg_ip, reg_date FROM {$server->loginDatabase}.$loginLogTable $sqlpartial";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$logins = $sth->fetchAll();
?>
