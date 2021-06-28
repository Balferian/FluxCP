<?php
if (!defined('FLUX_ROOT')) exit;
require_once 'functions/ScriptParser/mapImage.php';
$title = 'List NPCs';
$npcsDB =	"{$server->charMapDatabase}.".FLUX::config('FluxTables.NpcsSpawnTable');
$npc_map = $params->get('map');
$npc_name = $params->get('name');
$npc_type = $params->get('npc_type');
$npc_types = array('all' => -1, 'npc' => 0, 'shop' => 1);

$sqlpartial = "WHERE 1 = 1 ";
$ignored_npcs = " AND (`name` != 'No Name' AND sprite != 45 AND sprite != 139 AND sprite != 111 AND sprite != 844 AND sprite != 'WARPNPC' AND sprite != 'CLEAR_NPC' AND sprite != 'HIDDEN_WARP_NPC')";

if($npc_map)
	$sqlpartial .= " AND (map = '$npc_map' OR map LIKE '%$npc_map%')";
if($npc_name)
	$sqlpartial .= " AND (`name` = '$npc_name' OR `name` LIKE '%$npc_name%')";
if($npc_type && $npc_type != "all")
	$sqlpartial .= " AND `is_shop` = ".$npc_types[$npc_type];


$sth = $server->connection->getStatement("SELECT COUNT(*) AS total FROM $npcsDB $sqlpartial $ignored_npcs");
$sth->execute();
$perPage       = FLUX::config('NpcsResultsPerPage');
$paginator     = $this->getPaginator($sth->fetch()->total, array('perPage' => $perPage));
$paginator->setSortableColumns(array(
	'name' => 'asc'
));

$sql  = $paginator->getSQL("SELECT * FROM $npcsDB $sqlpartial $ignored_npcs");
$sth  = $server->connection->getStatement($sql);

$sth->execute();
$npcs = $sth->fetchAll();
?>