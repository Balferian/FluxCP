<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$account_id = $params->get('account_id');

$SDCategory = ($params->get('category') > 0) ? $params->get('category') : false;
$sqlpartial = "WHERE 1=1 ";

$tbl = Flux::config('FluxTables.ServiceDeskTable'); 
$tblcat = Flux::config('FluxTables.ServiceDeskCatTable'); 
$usersTable = Flux::config('FluxTables.MasterUserTable');
$userColumns = Flux::config('FluxTables.MasterUserTableColumns');
$title = Flux::message('SDHeader');

if($SDCategory) $sqlpartial .= "AND status = 'SDStatus_$SDCategory' ";
if($account_id) $sqlpartial .= "AND $tbl.account_id = $account_id ";

$sql  = "SELECT * FROM {$server->loginDatabase}.$tbl ";
$sql .= "LEFT JOIN {$server->loginDatabase}.login ON $tbl.account_id = login.account_id ";
$sql .= "LEFT JOIN {$server->loginDatabase}.{$usersTable} ON login.email = {$usersTable}.email ";
$sql .= "$sqlpartial ORDER BY ticket_id DESC";
$rep  = $server->connection->getStatement($sql);
$rep->execute();
$ticketlist = $rep->fetchAll();
$rowoutput=NULL;
foreach($ticketlist as $trow){
$catsql = $server->connection->getStatement("SELECT * FROM {$server->loginDatabase}.$tblcat WHERE cat_id = ?");
$catsql->execute(array($trow->category));
$catlist = $catsql->fetch();

$rowoutput.='<tr >
				<td><a href="'. $this->url('servicedesk', 'staffview', array('ticketid' => $trow->ticket_id)) .'" >'. $trow->ticket_id .'</a></td>';
$rowoutput.='<td>'. $trow->name .'</td>
				<td>'. $this->linkToMasterAccount($trow->user_id, $trow->email) .'</td>';
$rowoutput.='<td>'. $this->linkToMasterAccount($trow->account_id, $trow->account_id) .'</td>
				<td><a href="'. $this->url('servicedesk', 'staffview', array('ticketid' => $trow->ticket_id)) .'" >'. $trow->subject .'</a></td>
				<td><a href="'. $this->url('servicedesk', 'staffview', array('ticketid' => $trow->ticket_id)) .'" >
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

?>
