<?php
if (!defined('FLUX_ROOT')) exit;
$title = 'Map Database';
$mapsTable = "{$server->charMapDatabase}.".FLUX::config('FluxTables.MapsTable');
$map_name = $params->get('map');
$sqlpartial = "WHERE 1 = 1 ";

if($map_name)
	$sqlpartial .= "AND (`name` = '$map_name' OR `name` LIKE '%$map_name%')";

$sql = "SELECT COUNT(`name`) AS total FROM $mapsTable $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$perPage       = FLUX::config('MapsResultsPerPage');
$paginator     = $this->getPaginator($sth->fetch()->total, array('perPage' => $perPage));
$paginator->setSortableColumns(array('name' ));

$sql  = "SELECT * FROM $mapsTable $sqlpartial";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);
$sth->execute();

$maps_list = $sth->fetchAll();

$authorized = $auth->actionAllowed('map', 'view');

if ($maps_list && count($maps_list) === 1 && $authorized && Flux::config('SingleMatchRedirect')) {
    $this->redirect($this->url('map', 'view', array('map' => $maps_list[0]->name)));
}
?>