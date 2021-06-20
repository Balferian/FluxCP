<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('MasterAccountEditHeading');

$userId = $params->get('id');

$usersTable = Flux::config('FluxTables.MasterUserTable');
$creditsTable  = Flux::config('FluxTables.MasterCreditsTable');
$creditColumns = 'credits.balance';

$sql  = "SELECT *, {$server->loginDatabase}.{$usersTable}.user_id as id, $creditColumns FROM {$server->loginDatabase}.{$usersTable} ";
$sql .= "LEFT JOIN {$server->loginDatabase}.{$creditsTable} AS credits ON {$server->loginDatabase}.{$usersTable}.user_id = credits.user_id ";
$sql .= "WHERE {$server->loginDatabase}.{$usersTable}.user_id = ? LIMIT 1";
$sth  = $server->connection->getStatement($sql);
$sth->execute(array($userId));

// Account object.
$account = $sth->fetch();
$isMine  = false;

if ($account) {
	if ($account->group_id > $session->account->group_id && !$auth->allowedToEditHigherPower) {
		$this->deny();
	}
	
	$isMine = $session->isMine($account->account_id);
	
	if ($isMine) {
		$title = Flux::message('AccountEditTitle2');
	}
	else {
		$title = sprintf(Flux::message('AccountEditTitle3'), $account->userid);
	}
	
	if (count($_POST)) {
		$groups     = AccountLevel::getArray();
	
		$name       = trim($params->get('name'));
		$email      = trim($params->get('email'));
		$birthdate  = $params->get('birthdate_date');
		$lastLogin  = $params->get('lastlogin_date');
		$lastIP     = trim($params->get('last_ip'));
		$group_id   = (int)$params->get('group_id');
		$balance    = (int)$params->get('balance');
		
		if ($isMine && $account->group_id != $group_id) {
			$errorMessage = Flux::message('CannotModifyOwnGroupID');
		}
		elseif ($account->group_id != $group_id && !$auth->allowedToEditAccountGroupID) {
			$errorMessage = Flux::message('CannotModifyAnyGroupID');
		}
		elseif ($group_id > $session->account->group_id) {
			$errorMessage = Flux::message('CannotModifyGroupIDHigh');
		}
		elseif (!isset($groups[$group_id])) {
			$errorMessage = Flux::message('InvalidGroupID');
		}
		elseif ($account->balance != $balance && !$auth->allowedToEditAccountBalance) {
			$errorMessage = Flux::message('CannotModifyBalance');
		}
		elseif ($birthdate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
			$errorMessage = Flux::message('InvalidBirthdate');
		}
		elseif ($lastLogin && !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $lastLogin)) {
			$errorMessage = Flux::message('InvalidLastLoginDate');
		}
		else {
			$bind = array(
				'name'      => $name,
				'email'      => $email,
				'birth_date'  => $birthdate ? $birthdate : $account->birth_date,
				'last_login'  => $lastLogin ? $lastLogin : $account->last_login,
				'last_ip'    => $lastIP,
			);
			
			$sql  = "UPDATE {$server->loginDatabase}.{$usersTable} SET name = :name, email = :email, ";
			$sql .= "birth_date = :birth_date, last_login = :last_login, last_ip = :last_ip";
			
			if ($auth->allowedToEditAccountGroupID) {
				$sql .= ", group_id = :group_id";
				$bind['group_id'] = $group_id;
			}
			
			$bind['user_id'] = $account->user_id;
			
			$sql .= " WHERE user_id = :user_id";
			$sth  = $server->connection->getStatement($sql);
			$sth->execute($bind);

			if ($auth->allowedToEditAccountBalance) {
				$deposit = $balance - $account->balance;
				$session->loginServer->depositCredits(Flux::config('MasterAccount') ? $account->id : $account->account_id, $deposit);
			}
			
			$session->setMessageData(Flux::message('AccountModified'));
			$this->redirect($this->url('master', 'view', array('user_id' => $account->id)));
		}
	}
}
?>
