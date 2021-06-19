<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'List Logins';

$loginLogTable = Flux::config('FluxTables.LoginLogTable');
$sqlpartial    = "WHERE 1=1 ";
$bind          = array();

$accountID   = (int)$params->get('account_id');
$username    = trim($params->get('username'));
$ipAddress   = trim($params->get('ip'));
$errorCode   = $params->get('error_code');

if ($accountID) {
	if (Flux::config('MasterAccount'))
		$sqlpartial .= 'AND user_id = ? ';
	else
		$sqlpartial .= 'AND account_id = ? ';
	$bind[]      = $accountID;
}

if ($username) {
	$sqlpartial .= 'AND username LIKE ? ';
	$bind[]      = "%$username%";
}

if ($ipAddress) {
	$sqlpartial .= 'AND ip LIKE ? ';
	$bind[]      = "%$ipAddress%";
}

if (!is_null($errorCode) && strtolower($errorCode) != 'all') {
	if (strtolower($errorCode) == 'none') {
		$sqlpartial .= 'AND error_code IS NULL ';
	}
	else {
		$sqlpartial .= 'AND error_code = ? ';
		$bind[]      = $errorCode;
	}
}

$sql = "SELECT COUNT(id) AS total FROM {$server->loginDatabase}.$loginLogTable $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'user_id', 'account_id', 'username', 'ip',
	'login_date' => 'desc', 'error_code'
));

$sql = "SELECT user_id, account_id, username, ip, login_date, error_code FROM {$server->loginDatabase}.$loginLogTable $sqlpartial";
$sql = $paginator->getSQL($sql);
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$logins = $sth->fetchAll();
$loginErrors = Flux::config('LoginErrors');

if ($logins) {
	foreach ($logins as $_tmplogin) {
		$_tmplogin->error_type = $loginErrors->get($_tmplogin->error_code);
		if (is_null($_tmplogin->error_type)) {
			$_tmplogin->error_type = $_tmplogin->error_code;
		}
	}
}
?>
