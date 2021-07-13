<?php
if (!defined('FLUX_ROOT')) exit;
require_once 'Flux/TemporaryTable.php';
require_once 'functions/ScriptParser/mapImage.php';

// Monsters table.
$mobDB      = "{$server->charMapDatabase}.monsters";
$fromTables = $this->DatabasesList($server->charMapDatabase, Flux::config('FluxTables.MobsTable')->toArray(), $server->isRenewal);
$tempMobs   = new Flux_TemporaryTable($server->connection, $mobDB, $fromTables);

// Items table.
$itemDB = "{$server->charMapDatabase}.items";
$fromTables = $this->DatabasesList($server->charMapDatabase, Flux::config('FluxTables.ItemsTable')->toArray(), $server->isRenewal);
$tempItems = new Flux_TemporaryTable($server->connection, $itemDB, $fromTables);

$shopsDB = 		"{$server->charMapDatabase}.".FLUX::config("FluxTables.VendorsTable");
$mobsDB = 		"{$server->charMapDatabase}.".FLUX::config("FluxTables.MobsSpawnTable");
$mapsDB = 		"{$server->charMapDatabase}.".FLUX::config("FluxTables.MapsTable");
$warpsDB = 		"{$server->charMapDatabase}.".FLUX::config("FluxTables.WarpsTable");
$npcsDB = 		"{$server->charMapDatabase}.".FLUX::config("FluxTables.NpcsSpawnTable");
$mapflagsDB = 	"{$server->charMapDatabase}.".FLUX::config("FluxTables.MapflagsTable");

$ignored_npcs = " AND (`name` != 'No Name' AND sprite != 45 AND sprite != 139 AND sprite != 111 AND sprite != 844 AND sprite != 'WARPNPC' AND sprite != 'CLEAR_NPC' AND sprite != 'HIDDEN_WARP_NPC')";

$title = 'Map Database';
if($params->get('npc_id')){
    $sth = $server->connection->getStatement("select * from $shopsDB where id_shop = ?");
    $sth->execute(array($params->get('npc_id')));
    $items = $sth->fetchAll();
    $json = array();
    foreach($items as $item){
        $img = $this->iconImage($item->item);
        $json[] = array(
            'id' => $item->item,
            'link' => $auth->actionAllowed('item_new', 'view') ? $this->url('item_new', 'view', array('id' => $item->item)) : '',
            'img' => $img ? $img : '',
            'name' => $item->name,
            'price' => preg_replace('/(\d)(?=(\d\d\d)+([^\d]|$))/', '$1 ', $item->price)
        );
    }
    echo json_encode($json);
    die();
}

$sth = $server->connection->getStatement("select * from $mapsDB where name = ?");
$sth->execute(array($params->get('map')));
$map = $sth->fetchAll();
$map = $map[0];

if($map){
	// [0] - table name
	// [1] - columns
	// [2] - where ...
    $tables = array(
        'mobs'	=> array(
			"$mobsDB",
			", $mobDB.`name_english` as `name` ",
			" WHERE map = ?"
		),
        "warps"	=> array(
			"$warpsDB",
			"",
			" WHERE map = ?"
		),
        "npcs"	=> array(
			"$npcsDB",
			"",
			" WHERE map = ? AND is_shop = 0 $ignored_npcs"
		),
        "shops"	=> array(
			"$npcsDB",
			"",
			" WHERE map = ? AND is_shop = 1 $ignored_npcs"
		),
        "mapflags"	=> array(
			"$mapflagsDB",
			"",
			" WHERE name = ? GROUP BY mapflag"
		)
    );
    foreach($tables as $var => $table) {
		$sql = "SELECT ".$table[0].".* ".$table[1]." FROM ".$table[0]." ";
		if($var == "mobs")
			$sql .= "LEFT JOIN $mobDB ON ".$table[0].".mob_id = $mobDB.ID ";
		$sql .= $table[2];
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array($map->name));
		$$var = $sth->fetchAll();
    }
	foreach($shops as $shop) {
		$shop_items = array();
		$sql = "SELECT $shopsDB.*, $itemDB.name_english as `name`, $itemDB.price_buy FROM $shopsDB ";
		$sql .= "LEFT JOIN $itemDB ON $shopsDB.item = $itemDB.id ";
		$sql .= "WHERE id_shop = ? ";
		$sth = $server->connection->getStatement($sql);
		$sth->execute(array($shop->id));
		$sale_list = $sth->fetchAll();
		$shop->items = '';
		foreach($sale_list as $item){
			$shop->items .= '<li>';
			// icon
			$icon = $this->iconImage($item->item);
			if($icon)
				$shop->items .= '<img src="'.htmlspecialchars($icon).'?nocache='.rand().'" /><div>';
			$shop->items .= $auth->actionAllowed('item', 'view') ? $this->linkToItem($item->item, $item->name) : htmlspecialchars($item->name);
			$shop->items .= '<br><span>'.($item->price == -1 ? $item->price_buy : $item->price).' '.FLUX::message('ServerInfoZenyLabel').'</span>';
			$shop->items .= '</div></li>';
		}
			for($i = 1; $i <= 4-(count($sale_list)%4); $i++)
				$shop->items .= "<li></li>";
	}
}

if (isset($tempMobs) && $tempMobs)
	$tempMobs->drop();
if (isset($tempItems) && $tempItems)
	$tempItems->drop();
