<?php
if (!defined('FLUX_ROOT')) exit;
require_once 'Flux/TemporaryTable.php';
require_once 'functions/ScriptParser/mapImage.php';

$title = 'Viewing NPCs';
$npcID = $params->get('id');
$npcsDB =	"{$server->charMapDatabase}.".FLUX::config('FluxTables.NpcsSpawnTable');
$shopsDB =	"{$server->charMapDatabase}.".FLUX::config("FluxTables.VendorsTable");
$mapsDB =	"{$server->charMapDatabase}.".FLUX::config('FluxTables.MapsTable');
$items = array();

// Items table.
$itemDB = "{$server->charMapDatabase}.items";
if($server->isRenewal) {
	$fromTables = array("{$server->charMapDatabase}.item_db_re", "{$server->charMapDatabase}.item_db2_re");
} else {
	$fromTables = array("{$server->charMapDatabase}.item_db", "{$server->charMapDatabase}.item_db2");
}
$tempItems = new Flux_TemporaryTable($server->connection, $itemDB, $fromTables);

$sql = "SELECT * FROM $npcsDB WHERE id = ?";
$sth = $server->connection->getStatement($sql);
$sth->execute(array($npcID));
$npc = $sth->fetch();

$sql = "SELECT * FROM $mapsDB WHERE name = ?";
$sth = $server->connection->getStatement($sql);
$sth->execute(array($npc->map));
$map = $sth->fetch();

if($npc->is_shop){
	$sql  = "SELECT $shopsDB.*, $itemDB.name_english as `name`, $itemDB.price_buy FROM $shopsDB ";
	$sql .= "LEFT JOIN $itemDB ON $shopsDB.item = $itemDB.id ";
	$sql .= "WHERE id_shop = ?";
	$sth = $server->connection->getStatement($sql);
	$sth->execute(array($npc->id));
	$items = $sth->fetchAll();
}
