<?php
if (!defined('FLUX_ROOT')) exit;
$title = 'Upgrades Info';

require_once 'Flux/TemporaryTable.php';
$fromTables = $this->DatabasesList($server->charMapDatabase, Flux::config('FluxTables.ItemsTable')->toArray(), $server->isRenewal);
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$UpgradesTable = "{$server->charMapDatabase}.".Flux::config('FluxTables.UpgradeTable');

$sql = "SELECT COUNT(ItemID) AS total FROM $UpgradesTable GROUP BY ItemID";
$sth = $server->connection->getStatement($sql);
$sth->execute();
$total = $sth->fetchAll();

$perPage       = FLUX::config('ResultsPerPage');
$paginator     = $this->getPaginator(count($total), array('perPage' => $perPage));
$paginator->setSortableColumns(array('name'));

$sql  = "SELECT $UpgradesTable.*, items.name_english FROM $UpgradesTable ";
$sql .= "LEFT JOIN items ON items.id = $UpgradesTable.ItemID";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);
$sth->execute();
$Upgrades = $sth->fetchAll();

foreach ($Upgrades as $Upgrade) {
	$temps = explode(",", $Upgrade->TargetItems);
	$TargetTemp = array();
	foreach ($temps as $temp) {
		$sql  = "SELECT name_english FROM items WHERE id = ?";
		$sth  = $server->connection->getStatement($sql);
		$sth->execute(array($temp));
		$result = $sth->fetch();
		$TargetTemp[$temp] = $result->name_english;
	}
	$Upgrade->TargetItems = $TargetTemp;
}
?>
