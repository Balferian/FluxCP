<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Vendors';
require_once 'Flux/TemporaryTable.php';

$fromTables = $this->DatabasesList($server->charMapDatabase, Flux::config('FluxTables.ItemsTable')->toArray(), $server->isRenewal);
$tableName = "{$server->charMapDatabase}.items";
$tempTable = new Flux_TemporaryTable($server->connection, $tableName, $fromTables);

$item_name	= $params->get('item_name');
$item_id	= $params->get('item_id');
$card_id	= $params->get('card_id');
$enhants	= $params->get('enhants');

// Get total count and feed back to the paginator.
$sql  = "SELECT COUNT(cart_inventory.nameid) AS total ";
$sql .= "FROM {$server->charMapDatabase}.vending_items ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.vendings ON vendings.id = vending_items.vending_id ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.cart_inventory ON cart_inventory.id = vending_items.cartinventory_id ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.items ON items.id = cart_inventory.nameid ";
$sql .= "WHERE 1 = 1 ";
if($item_name)	$sql .= "AND items.name_english LIKE '%$item_name%' ";
if($item_id)	$sql .= "AND items.id = $item_id ";
if($card_id)	$sql .= "AND (cart_inventory.card0 = $card_id OR cart_inventory.card1 = $card_id OR cart_inventory.card2 = $card_id OR cart_inventory.card3 = $card_id) ";
if($enhants)	$sql .= "AND (cart_inventory.option_id0 = $enhants OR cart_inventory.option_id1 = $enhants OR cart_inventory.option_id2 = $enhants OR cart_inventory.option_id3 = $enhants OR cart_inventory.option_id4 = $enhants) ";

$sth = $server->connection->getStatement($sql);
$sth->execute();
$paginator = $this->getPaginator($sth->fetch()->total);

// Set the sortable columns
$sortable = array(
    'cart_inventory.nameid' => 'asc', 'vending_items.amount', 'vending_items.price'
);

$paginator->setSortableColumns($sortable);

// Create the main request.
$sql  = "SELECT vendings.char_id, `char`.`name`, vending_items.vending_id, vendings.title, vendings.map, ";
$sql .= "cart_inventory.nameid, items.name_english, items.type, ";
$sql .= "cart_inventory.refine, cart_inventory.card0, cart_inventory.card1, cart_inventory.card2, cart_inventory.card3, ";
$sql .= "cart_inventory.option_id0, cart_inventory.option_val0, cart_inventory.option_id1, cart_inventory.option_val1, cart_inventory.option_id2, cart_inventory.option_val2, ";
$sql .= "cart_inventory.option_id3, cart_inventory.option_val3, cart_inventory.option_id4, cart_inventory.option_val4, cart_inventory.enchantgrade, ";
$sql .= "vending_items.amount, vending_items.price ";
$sql .= "FROM {$server->charMapDatabase}.vending_items ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.vendings ON vendings.id = vending_items.vending_id ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.`char` ON `char`.char_id = vendings.char_id ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.cart_inventory ON cart_inventory.id = vending_items.cartinventory_id ";
$sql .= "LEFT JOIN {$server->charMapDatabase}.items ON items.id = cart_inventory.nameid ";
$sql .= "WHERE 1 = 1 ";
if($item_name)	$sql .= "AND items.name_english LIKE '%$item_name%' ";
if($item_id)	$sql .= "AND items.id = $item_id ";
if($card_id)	$sql .= "AND (cart_inventory.card0 = $card_id OR cart_inventory.card1 = $card_id OR cart_inventory.card2 = $card_id OR cart_inventory.card3 = $card_id) ";
if($enhants)	$sql .= "AND (cart_inventory.option_id0 = $enhants OR cart_inventory.option_id1 = $enhants OR cart_inventory.option_id2 = $enhants OR cart_inventory.option_id3 = $enhants OR cart_inventory.option_id4 = $enhants) ";
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
