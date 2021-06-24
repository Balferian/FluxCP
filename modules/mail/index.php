<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('MailerTitle');
$whoto = trim($params->get('whoto'));
$template = trim($params->get('template'));
$subject = trim($params->get('subject'));
$selectedtemplate = $template.'.php';

// Select Template
$template_dir = FLUX_DATA_DIR."/templates/";
$myDirectory = opendir($template_dir);
while($entryName = readdir($myDirectory)) {$dirArray[] = $entryName;}
closedir($myDirectory);
$indexCount	= count($dirArray);
sort($dirArray);

if (count($_POST)) {
	if (Flux::config('MasterAccount')) {
		if($whoto == '1'){
			// please leave blank
		}elseif($whoto == '2'){
			$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.cp_users WHERE group_id = '99'");
		}elseif($whoto == '3'){
			$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.cp_users WHERE group_id >= '2'");
		}elseif($whoto == '4'){
			$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.cp_users");
		}
	} else {
		if($whoto == '1'){
			// please leave blank
		}elseif($whoto == '2'){
			$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.login WHERE `group_id` = '99'");
		}elseif($whoto == '3'){
			$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.login WHERE (group_id=2 OR group_id=99)");
		}elseif($whoto == '4'){
			$sth = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.login");
		}	
	}

	$sth->execute();
	$list = $sth->fetchAll();

	foreach($list as $lrow){
		$email = $lrow->email;
		require_once 'Flux/Mailer.php';
		$mail = new Flux_Mailer();
		$sent = $mail->send($email, $subject, $template, array(
			'emailtitle'		=> $subject,
			'username'		=> $lrow->userid,
			'email'		=> $lrow->email,
		));
	}
	
	$session->setMessageData(Flux::message('MailerEmailHasBeenSent'));
	
	if(Flux::config('DiscordUseWebhook')) {
		if(Flux::config('DiscordSendOnMarketing')) {
			sendtodiscord(Flux::config('DiscordWebhookURL'), 'Mass Email Sent: '. $subject);
		}
	}

}
?>
