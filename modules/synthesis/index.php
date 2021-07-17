<?php
if (!defined('FLUX_ROOT')) exit;
$title = 'Synthesis Info';

require_once 'Flux/TemporaryTable.php';
$fromTables = $this->DatabasesList($server->charMapDatabase, Flux::config('FluxTables.ItemsTable')->toArray(), $server->isRenewal);
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$SynthesisTable = "{$server->charMapDatabase}.".Flux::config('FluxTables.SynthesisTable');

$sql = "SELECT COUNT(ItemID) AS total FROM $SynthesisTable GROUP BY ItemID";
$sth = $server->connection->getStatement($sql);
$sth->execute();
$total = $sth->fetchAll();

$perPage       = FLUX::config('ResultsPerPage');
$paginator     = $this->getPaginator(count($total), array('perPage' => $perPage));
$paginator->setSortableColumns(array('name'));

$sql  = "SELECT $SynthesisTable.*, items.name_english FROM $SynthesisTable ";
$sql .= "LEFT JOIN items ON items.id = $SynthesisTable.ItemID";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);
$sth->execute();
$Synthesis = $sth->fetchAll();

foreach ($Synthesis as $Synthes) {
	$temps = explode(",", $Synthes->SourceItem);
	$SourceTemp = array();
	foreach ($temps as $temp) {
		$items = explode(":", $temp);
		$sql  = "SELECT name_english FROM items WHERE id = ?";
		$sth  = $server->connection->getStatement($sql);
		$sth->execute(array($items[0]));
		$result = $sth->fetch();
		$SourceTemp[$items[0]] = array($result->name_english, $items[1]);
	}
	$Synthes->SourceItem = $SourceTemp;
}
?>
