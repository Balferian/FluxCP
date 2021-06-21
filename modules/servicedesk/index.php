<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();

$tbl = Flux::config('FluxTables.ServiceDeskTable'); 
$tblcat = Flux::config('FluxTables.ServiceDeskCatTable');

if (Flux::config('MasterAccount')) {
	$accountIds = $session->account->game_accounts['account_ids'];
	$accountIdsIn = str_repeat("?,", count($accountIds));
	$accountIdsIn = rtrim($accountIdsIn, ',');
	$sql = "SELECT * FROM {$server->loginDatabase}.$tbl WHERE account_id IN ({$accountIdsIn}) AND status != 'SDStatus_5' ORDER BY ticket_id DESC";
	$rep = $server->connection->getStatement($sql);
	$rep->execute($accountIds);
} else {
	$rep = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tbl WHERE account_id = ? AND status != 'SDStatus_5' ORDER BY ticket_id DESC");
	$rep->execute(array($session->account->account_id));
}

$ticketlist = $rep->fetchAll();
$rowoutput=NULL;
foreach($ticketlist as $trow){
$catsql = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tblcat WHERE cat_id = ?");
$catsql->execute(array($trow->category));
$catlist = $catsql->fetch();

$rowoutput.='<tr >
				<td><a href="'. $this->url('servicedesk', 'view', array('ticketid' => $trow->ticket_id)) .'" >'. $trow->ticket_id .'</a></td>
				<td><a href="'. $this->url('servicedesk', 'view', array('ticketid' => $trow->ticket_id)) .'" >'. $trow->subject .'</a></td>
				<td><a href="'. $this->url('servicedesk', 'view', array('ticketid' => $trow->ticket_id)) .'" >
					'. $catlist->name .'</a></td>
				<td>
					<font color="'. Flux::config($trow->status) .'"><strong>'. Flux::message($trow->status) .'</strong></font>
				</td>
				<td width="50">';
					if($trow->lastreply=='0'){$rowoutput.='<i>None</i>';} else {$rowoutput.= Flux::message($trow->lastreply);}
$rowoutput.='</td>
				<td>
					'. Flux::message('SDGroup'. $trow->team) .'
				</td>
				<td>'. date(Flux::config('DateFormat'),strtotime($trow->timestamp)) .'</td>
			</tr>';
}

if (Flux::config('MasterAccount')) {
	$sql = "SELECT * FROM {$server->loginDatabase}.$tbl WHERE account_id IN ({$accountIdsIn}) AND status = 'SDStatus_5' ORDER BY ticket_id DESC";
	$oldrep = $server->connection->getStatement($sql);
	$oldrep->execute($accountIds);
} else {
	$oldrep = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tbl WHERE account_id = ? AND status = 'SDStatus_5' ORDER BY ticket_id DESC");
	$oldrep->execute(array($session->account->account_id));
}
$oldticketlist = $oldrep->fetchAll();
$oldrowoutput=NULL;
foreach($oldticketlist as $oldtrow){
$catsql = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tblcat WHERE cat_id = ?");
$catsql->execute(array($oldtrow->category));
$catlist = $catsql->fetch();

$oldrowoutput.='<tr >
				<td><a href="'. $this->url('servicedesk', 'view', array('ticketid' => $oldtrow->ticket_id)) .'" >'. $oldtrow->ticket_id .'</a></td>
				<td><a href="'. $this->url('servicedesk', 'view', array('ticketid' => $oldtrow->ticket_id)) .'" >'. $oldtrow->subject .'</a></td>
				<td><a href="'. $this->url('servicedesk', 'view', array('ticketid' => $oldtrow->ticket_id)) .'" >
					'. $catlist->name .'</a></td>
				<td>
					<font color="'. Flux::config($oldtrow->status) .'"><strong>'. Flux::message($oldtrow->status) .'</strong></font>
				</td>
				<td width="50">';
					if($oldtrow->lastreply=='0'){$oldrowoutput.='<i>None</i>';} else {$oldrowoutput.= Flux::message($oldtrow->lastreply);}
$oldrowoutput.='</td>
				<td>
					'. Flux::message('SDGroup'. $oldtrow->team) .'
				</td>
				<td>'. date(Flux::config('DateFormat'),strtotime($oldtrow->timestamp)) .'</td>
			</tr>';
}
?>
