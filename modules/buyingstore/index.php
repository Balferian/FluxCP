<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Buyers';
require_once 'Flux/TemporaryTable.php';

$fromTables = $this->DatabasesList($server->charMapDatabase, Flux::config('FluxTables.ItemsTable')->toArray(), $server->isRenewal);
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$item_name	= $params->get('item_name');
$item_id	= $params->get('item_id');

// Get total count and feed back to the paginator.
$sql  = "SELECT COUNT(buyingstore_items.item_id) AS total ";
$sql .= "FROM {$server->charMapDatabase}.buyingstore_items ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.buyingstores ON buyingstores.id = buyingstore_items.buyingstore_id ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.items ON items.id = buyingstore_items.item_id ";
$sql .= "WHERE 1 = 1 ";
if($item_name)	$sql .= "AND items.name_english LIKE '%$item_name%' ";
if($item_id)	$sql .= "AND items.id = $item_id ";

$sth = $server->connection->getStatement($sql);
$sth->execute();
$paginator = $this->getPaginator($sth->fetch()->total);

// Set the sortable columns
$sortable = array(
    'buyingstore_items.item_id' => 'asc', 'buyingstore_items.amount', 'buyingstore_items.price'
);

$paginator->setSortableColumns($sortable);

// Create the main request.
$sql  = "SELECT buyingstores.char_id, `char`.`name`, buyingstore_items.buyingstore_id, buyingstores.title, buyingstores.map, ";
$sql .= "buyingstore_items.item_id, items.name_english, items.type, ";
$sql .= "buyingstore_items.amount, buyingstore_items.price ";
$sql .= "FROM {$server->charMapDatabase}.buyingstore_items ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.buyingstores ON buyingstores.id = buyingstore_items.buyingstore_id ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.`char` ON `char`.char_id = buyingstores.char_id ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.items ON items.id = buyingstore_items.item_id ";
$sql .= "WHERE 1 = 1 ";
if($item_name)	$sql .= "AND items.name_english LIKE '%$item_name%' ";
if($item_id)	$sql .= "AND items.id = $item_id ";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);
$sth->execute();

$items = $sth->fetchAll();

if ($items) {
	$cardIDs = array();

	foreach ($items as $item) {
		$item->cardsOver = -$item->slots;
		
		if ($item->card0) {
			$cardIDs[] = $item->card0;
			$item->cardsOver++;
		}
		if ($item->card1) {
			$cardIDs[] = $item->card1;
			$item->cardsOver++;
		}
		if ($item->card2) {
			$cardIDs[] = $item->card2;
			$item->cardsOver++;
		}
		if ($item->card3) {
			$cardIDs[] = $item->card3;
			$item->cardsOver++;
		}
		
		if ($item->card0 == 254 || $item->card0 == 255 || $item->card0 == -256 || $item->cardsOver < 0) {
			$item->cardsOver = 0;
		}

		if($server->isRenewal) {
			$temp = array();
			if ($item->option_id0)	array_push($temp, array($item->option_id0, $item->option_val0));
			if ($item->option_id1) 	array_push($temp, array($item->option_id1, $item->option_val1));
			if ($item->option_id2) 	array_push($temp, array($item->option_id2, $item->option_val2));
			if ($item->option_id3) 	array_push($temp, array($item->option_id3, $item->option_val3));
			if ($item->option_id4) 	array_push($temp, array($item->option_id4, $item->option_val4));
			$item->rndopt = $temp;
		}
	}

	if ($cardIDs) {
		$ids = implode(',', array_fill(0, count($cardIDs), '?'));
		$sql = "SELECT id, name_english FROM items WHERE id IN ($ids)";
		$sth = $server->connection->getStatement($sql);

		$sth->execute($cardIDs);
		$temp = $sth->fetchAll();
		if ($temp) {
			foreach ($temp as $card) {
				$cards[$card->id] = $card->name_english;
			}
		}
	}
	
	$itemAttributes = Flux::config('Attributes')->toArray();
	$type_list = Flux::config('ItemTypes')->toArray();
	$rndoptions_list = Flux::config('RandomOptions')->toArray();
}

?>
