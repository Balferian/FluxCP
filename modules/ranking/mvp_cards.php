<?php
if (!defined('FLUX_ROOT')) exit;

$title    = 'MVP Cards Ranking';
require_once 'Flux/TemporaryTable.php';
$mvp_cards    = FLUX::config('MvpCardList')->toArray();
$tables  = array("inventory", "cart_inventory", "storage", "guild_storage");
$columns  = array("nameid", "card0", "card1", "card2", "card3");
$cards_count = array();

$fromTables = $this->DatabasesList($server->charMapDatabase, Flux::config('FluxTables.ItemsTable')->toArray(), $server->isRenewal);
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

try {
	foreach($tables as $table) {
		foreach($mvp_cards as $mvp_card => $name) {
			foreach($columns as $column) {
				$sql  = "SELECT $tableName.name_english as name_english, $mvp_card as nameid, sum(amount) as total FROM $table ";
				$sql .= "LEFT JOIN $tableName ON $tableName.id = nameid ";
				$sql .= "WHERE $column = ? LIMIT 1";
				$sth  = $server->connection->getStatement($sql);
				$sth->execute(array($mvp_card));
				$cards = $sth->fetch();
				if(array_key_exists($mvp_card, $cards_count))
					$cards_count[$mvp_card][1] += $cards->total;
				else
					$cards_count[$mvp_card] = array($cards->nameid, $cards->total, $cards->name_english);
					
			}
		}
	}
}
catch (Exception $e) {
	if (isset($tempTable) && $tempTable) {
		// Ensure table gets dropped.
		$tempTable->drop();
	}
	
	// Raise the original exception.
	$class = get_class($e);
	throw new $class($e->getMessage());
}

?>
