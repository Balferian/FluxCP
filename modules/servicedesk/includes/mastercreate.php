<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();
if (!Flux::config('MasterAccount')) {
    $this->deny();
}
$tbl = Flux::config('FluxTables.ServiceDeskTable');
$tblcat = Flux::config('FluxTables.ServiceDeskCatTable');
$tblsettings = Flux::config('FluxTables.ServiceDeskSettingsTable');

$accounts = $session->account->game_accounts['account_ids'];
$usernames = $session->account->game_accounts['user_names'];
$accountId = $session->account->account_id;

$charlist = null;

if(!$accounts){
    $accountList ='<option value="-1">No Account available</option>';
} else {
    $accountList = '<option value="0">All Account</option>';
    foreach($accounts as $key => $account) {
        $accountList .='<option ';
        $accountList .= (!empty($_POST['select_account_id']) && $_POST['select_account_id'] == $account) ? 'selected' : '';
        $accountList .= ' value="'. $account .'">'. $usernames[$key] .'</option>';
    }
}

if (isset($_POST['select_account_id'])) {
    $selectedAccount = $server->loginServer->getGameAccount($session->account->id, (int)$_POST['select_account_id']);
    $charselect = NULL;
    if ($selectedAccount) {
        $accountId = $selectedAccount->account_id;
        $charsql = $server->connection->getStatement("SELECT * FROM {$server->charMapDatabase}.char WHERE account_id = ?");
        $charsql->execute(array($accountId));
        $charlist = $charsql->fetchAll();
    }
    if(!$charlist){
        $charselect='<option value="-1">No Chars Available</option>';
    } else {
        $charselect='<option value="0">All Characters</option>';
        foreach($charlist as $char){$charselect.='<option value="'. $char->char_id .'">'. $char->name .'</option>';}
    }
} else {
    $charselect = '<option value="-1">No Chars Available</option>';
}

$catsql = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tblcat WHERE display = 1");
$catsql->execute();
$catlist = $catsql->fetchAll();

if(isset($_POST['account_id']) && isset($_POST['Submit'])){
	if ($this->ReCapchaCheck($_POST['g-recaptcha-response'])) {
		$char_id	= (int)$_POST['char_id'];
		$category	= $_POST['category'];
		$subject	= $_POST['subject'];
		$text	= $_POST['text'];
		$ip	= $_POST['ip'];
		$selectedAccount = $server->loginServer->getGameAccount($session->account->id, $_POST['account_id']);
		$charSQL = $server->connection->getStatement("SELECT * FROM {$server->charMapDatabase}.char WHERE char_id = ? AND account_id = ?");
		$charSQL->execute(array($_POST['char_id'], $selectedAccount->account_id));
		$selectedChar = $charSQL->fetch();

		if($_POST['sslink']==NULL || $_POST['sslink']==''){$_POST['sslink'] = '0';}else{$_POST['sslink'] = $_POST['sslink'];}
		if($_POST['chatlink']==NULL || $_POST['chatlink']==''){$_POST['chatlink'] = '0';}else{$_POST['chatlink'] = $_POST['chatlink'];}
		if($_POST['videolink']==NULL || $_POST['videolink']==''){$_POST['videolink'] = '0';}else{$_POST['videolink'] = $_POST['videolink'];}

		$sql = "INSERT INTO {$server->loginDatabase}.$tbl (account_id, char_id, category, sslink, chatlink, videolink, subject, text, ip, curemail, lastreply)";
		$sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array($selectedAccount->account_id, $char_id, $category, $_POST['sslink'], $_POST['chatlink'], $_POST['videolink'], $subject, $text, $ip, $session->account->email));

		if(Flux::config('DiscordUseWebhook')) {
			if(Flux::config('DiscordSendOnNewTicket')) {
				sendtodiscord(Flux::config('DiscordWebhookURL'), 'New Ticket Created: '. $subject);
			}
		}

		// Send email to all staff with enable email setting.
		$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tblsettings WHERE emailalerts = 1");
		$sth->execute();
		$staff = $sth->fetchAll();
		if($staff){
			foreach($staff as $staffrow){
				$catsql = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tblcat WHERE cat_id = ?");
				$catsql->execute(array($category));
				$catlist = $catsql->fetch();
				$stsql = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.login WHERE account_id = ?");
				$stsql->execute(array($staffrow->account_id));
				$stlist = $stsql->fetch();
				$email = $stlist->email;

				require_once 'Flux/Mailer.php';
				$name = $session->loginAthenaGroup->serverName;
				$mail = new Flux_Mailer();
				$sent = $mail->send($email, 'New Ticket Created', 'newticket', array(
					'Category'		=> $catlist->name,
					'Subject'		=> $subject,
					'Text'			=> $text
				));
			}
		}
		
		if(Flux::config('SDAllowUplodScreenshots')) {
			$sql = "SELECT max(ticket_id) as max FROM {$server->loginDatabase}.$tbl";
			$sth = $server->connection->getStatement($sql);
			$sth->execute(array($mobID));
			$max_id = $sth->fetch();

			$screenshots_path = Flux::config('SDScreenshotUplodFolder').$max_id->max;
			if(isset($_FILES["screenshots"])) {
				foreach ($_FILES["screenshots"]["error"] as $key => $error) {
					if ($error == UPLOAD_ERR_OK && $key < Flux::config('SDMaxUplodScreenshots') ) {
						$tmp_name = $_FILES["screenshots"]["tmp_name"][$key];
						$name = basename($_FILES["screenshots"]["name"][$key]);
						$dir = basename($_FILES["screenshots"]["name"][$key]);
						$this->make_upload($tmp_name, $name, $screenshots_path);
					}
				}	
			}
		}
		
		$this->redirect($this->url('servicedesk','index'));
	} else
		$errorMessage = Flux::message('InvalidSecurityCode');
}
?>
