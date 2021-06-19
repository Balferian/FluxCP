<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'List Account Ban History';

$banTable    = Flux::config('FluxTables.AccountBanTable');
$sqlpartial  = "LEFT JOIN {$server->loginDatabase}.login AS l1 ON l1.account_id = a.account_id ";
$sqlpartial .= "LEFT JOIN {$server->loginDatabase}.cp_users AS l2 ON l2.user_id = a.banned_by ";
$sqlpartial .= "WHERE 1=1 ";
$sqlpartial .= "AND a.banned_by IS NOT NULL ";
$bind        = array();

$account     = trim($params->get('account'));
$bannedBy    = trim($params->get('banned_by'));
$banType     = $params->get('ban_type');
$banDate     = $params->get('ban_date');
$banUntil    = $params->get('ban_until_date');

if ($account) {
	if (preg_match('/^\d+$/', $account)) {
		$sqlpartial .= 'AND a.account_id = ? ';
		$bind[]      = $account;
	}
	else {
		$sqlpartial .= 'AND (l1.userid LIKE ? OR l1.userid = ?) ';
		$bind[]      = "%$account%";
		$bind[]      = $account;
	}
}

if ($bannedBy) {
	if (preg_match('/^\d+$/', $bannedBy)) {
		$sqlpartial .= 'AND a.banned_by = ? ';
		$bind[]      = $bannedBy;
	}
	else {
		$sqlpartial .= 'AND (l2.user_id LIKE ? OR l2.user_id = ?) ';
		$bind[]      = "%$bannedBy%";
		$bind[]      = $bannedBy;
	}
}

if ($banType) {
	if ($banType == 'unban') {
		$sqlpartial .= 'AND a.ban_type = 0 ';
	}
	elseif ($banType == 'tempban') {
		$sqlpartial .= 'AND a.ban_type = 1 ';
	}
	elseif ($banType == 'permban') {
		$sqlpartial .= 'AND a.ban_type = 2 ';
	}
}

if ($banDate) {
	$sqlpartial .= 'AND a.ban_date = ? ';
	$bind[]      = $banDate;
}

if ($banUntil) {
	$sqlpartial .= 'AND a.ban_until = ? ';
	$bind[]      = $banUntil;
}

$sql = "SELECT COUNT(a.id) AS total FROM {$server->loginDatabase}.$banTable AS a $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute($bind);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'a.account_id', 'banned_by', 'ban_type', 'ban_date' => 'desc', 'ban_until'
));

$sql  = "SELECT a.account_id, a.banned_by, a.ban_type, a.ban_until, a.ban_date, a.ban_reason, ";
$sql .= "l1.userid AS banned_userid, l2.user_id AS banned_by_userid, l2.name AS banned_by_name FROM {$server->loginDatabase}.$banTable AS a $sqlpartial";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);
$sth->execute($bind);

$bans = $sth->fetchAll();
?>
