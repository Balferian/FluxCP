<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'List Master Accounts';
if (!Flux::config('MasterAccount')) {
    $this->deny();
}

$usersTable = Flux::config('FluxTables.MasterUserTable');
$userAccountTable = Flux::config('FluxTables.MasterUserAccountTable');
$userColumns = Flux::config('FluxTables.MasterUserTableColumns');
$bind = [];

$creditsTable   = Flux::config('MasterAccount') ? Flux::config('FluxTables.MasterCreditsTable') : Flux::config('FluxTables.CreditsTable');
$creditColumns  = 'credits.balance as balance, credits.last_donation_date, credits.last_donation_amount';

$sql  = "LEFT OUTER JOIN {$server->loginDatabase}.{$userAccountTable} AS useraccounts ON {$usersTable}.{$userColumns->get('id')} = useraccounts.user_id ";
$sql .= "LEFT OUTER JOIN $creditsTable AS credits ON {$usersTable}.{$userColumns->get('id')} = credits.user_id ";
$sql .= "WHERE {$userColumns->get('group_id')} >= 0 ";
$userId = $params->get('id');
if ($userId) {
    $sql .= "AND {$usersTable}.{$userColumns->get('id')} = ?";
    $bind[]      = $userId;
}
else {
    $opMapping        = array('eq' => '=', 'gt' => '>', 'lt' => '<');
    $opValues         = array_keys($opMapping);
    $user_id          = $params->get('user_id');
    $mastername       = $params->get('mastername');
    $name             = $params->get('name');
    $email            = $params->get('email');
    $lastIP           = $params->get('ip');
    $gender           = $params->get('gender');
    $accountState     = $params->get('account_state');
    $accountGroupIdOp = $params->get('account_group_id_op');
    $accountGroupID   = $params->get('account_group_id');
    $birthdateA       = $params->get('birthdate_after_date');
    $birthdateB       = $params->get('birthdate_before_date');

    if ($user_id) {
        $sql .= "AND (user_id LIKE ? OR user_id = ?) ";
        $bind[]      = "%$user_id%";
        $bind[]      = $user_id;
    }

    if ($mastername) {
        $sql .= "AND (name LIKE ? OR name = ?) ";
        $bind[]      = "%$mastername%";
        $bind[]      = $mastername;
    }

    if ($email) {
        $sql .= "AND (email LIKE ? OR email = ?) ";
        $bind[]      = "%$email%";
        $bind[]      = $email;
    }

    if ($lastIP) {
        $sql .= "AND (last_ip LIKE ? OR last_ip = ?) ";
        $bind[]      = "%$lastIP%";
        $bind[]      = $lastIP;
    }

    if (in_array($accountGroupIdOp, $opValues) && trim($accountGroupID) != '') {
        $op          = $opMapping[$accountGroupIdOp];
        $sql .= "AND group_id $op ? ";
        $bind[]      = $accountGroupID;
    }

    if ($birthdateB && ($timestamp = strtotime($birthdateB))) {
        $sql .= 'AND birthdate <= ? ';
        $bind[]      = date('Y-m-d', $timestamp);
    }

    if ($birthdateA && ($timestamp = strtotime($birthdateA))) {
        $sql .= 'AND birthdate >= ? ';
        $bind[]      = date('Y-m-d', $timestamp);
    }
}
$totalAccounts = "SELECT COUNT({$usersTable}.{$userColumns->get('id')}) AS total FROM {$server->loginDatabase}.{$usersTable} $sql";
$sth = $server->connection->getStatement($totalAccounts);
$sth->execute($bind);

$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
    '{$usersTable}.user_id' => 'asc', 'user_id', 
	'group_id', 'email', 'logincount', 'last_ip',
    'reg_date'
));

$columns = implode(", {$usersTable}.",$userColumns->toArray());
$sql = "SELECT {$usersTable}.{$columns}, count(useraccounts.user_id) as totalAccounts, {$creditColumns} FROM {$server->loginDatabase}.{$usersTable} $sql";
$sql .= "GROUP BY {$usersTable}.{$columns}";
$sth  = $server->connection->getStatement($sql);
$sth->execute($bind);
$accounts   = $sth->fetchAll();

$authorized = $auth->actionAllowed('master', 'view') && $auth->allowedToViewAccount;

if ($accounts && count($accounts) === 1 && $authorized && Flux::config('SingleMatchRedirect')) {
    $this->redirect($this->url('master', 'view', array('user_id' => $accounts[0]->user_id)));
}
?>
