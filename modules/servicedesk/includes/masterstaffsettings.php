<?php
if (!defined('FLUX_ROOT')) exit;

$usersTable = Flux::config('FluxTables.MasterUserTable');
$userColumns = Flux::config('FluxTables.MasterUserTableColumns');
$option = trim($params->get('option'));
$emailalerts = $params->get('emailalerts');
$cur = trim($params->get('cur'));
$staffid = trim($params->get('staffid'));
$title = Flux::message('SDHeader');
$tbl = Flux::config('FluxTables.ServiceDeskSettingsTable');
$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tbl WHERE account_name = ?");
$sth->execute(array($session->account->email));
$staff = $sth->fetchAll();
if($staff){
	foreach($staff as $staffsess){}
}

if(isset($option) && $option == 'delete'){
	$sth = $server->connection->getStatement("DELETE FROM {$server->loginDatabase}.$tbl WHERE account_name = ?");
	$sth->execute(array($staffid)); 
	$this->redirect($this->url('servicedesk','staffsettings'));
}

if(isset($option) && $option == 'alerttoggle'){
	if($cur=='1'){
		$sth = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET emailalerts = 0 WHERE account_name = ?");
	} else {
		$sth = $server->connection->getStatement("UPDATE {$server->loginDatabase}.$tbl SET emailalerts = 1 WHERE account_name = ?");
	}

	$sth->execute(array($staffid)); 
	$this->redirect($this->url('servicedesk','staffsettings'));
}

if(isset($_POST['account_name'])){
	$sth = $server->connection->getStatement("SELECT account_name FROM {$server->loginDatabase}.$tbl WHERE account_name = ?");
	$sth->execute(array($_POST['account_name']));
	$fetch = $sth->fetch();
	if($fetch){	$session->setMessageData('Account already exists!'); } else {
	if(!$emailalerts){$emailalerts = 0;}
	$sql = "INSERT INTO {$server->loginDatabase}.$tbl (account_name, prefered_name, team, emailalerts)";
	$sql .= "VALUES (?, ?, ?, ?)";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array($_POST['account_name'],$_POST['prefered_name'],$_POST['team'], $emailalerts)); 
	$this->redirect($this->url('servicedesk','staffsettings'));
}
}

$rep = $server->connection->getStatement("SELECT $tbl.*, {$usersTable}.{$userColumns->get('id')} as user_id, {$usersTable}.{$userColumns->get('name')} as `name` FROM {$server->loginDatabase}.$tbl LEFT JOIN {$server->loginDatabase}.{$usersTable} ON $tbl.account_name = {$usersTable}.{$userColumns->get('email')} ORDER BY $tbl.account_name");
$rep->execute();
$stafflist = $rep->fetchAll();
?>
