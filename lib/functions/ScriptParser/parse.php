<?php
if (!defined('FLUX_ROOT')) exit;
class parse{

    private $pref = null;
    private $server = null;
    private $items = array();

    function __construct($server){
        $this->pref = $server->isRenewal ? 're' : 'pre-re';
        $this->server = $server;
    }

    function getFiles($path = false){
        $files = array();
        if(!$path) {
            $path = FLUX_DATA_DIR . '/uploads/npc/' . $this->pref . '/scripts_main.conf';
            if(!file_exists($path)){
                throw new Flux_Error('file scripts_main.conf not found');
            }
        }
        if(!file_exists($path)){
            return array();
        }
        $data = file_get_contents($path);
        preg_match_all('/(.*)(npc|import): (.*)/', $data, $match);
        foreach($match[3] as $key => $item){
            if(trim($match[1][$key]) == '//'){
                continue;
            }
            switch(trim($match[2][$key])){
				case 'npc':
					$item = trim($item);
					$files = array_merge($files, $this->getFiles(FLUX_DATA_DIR . '/uploads/' . $item));
					$files[] = FLUX_DATA_DIR . '/uploads/' . $item;
					break;
				case 'import':
					$item = trim($item);
					$files = array_merge($files, $this->getFiles(FLUX_DATA_DIR . '/uploads/' . $item));
					break;
			}
        }
        return $files;
    }

    function loadFiles(array $files){
        $array = array(
            'mobs' => 0,
            'npcs' => 0,
            'warps' => 0,
            'shops' => 0,
        );
        foreach($files as $file){
            $npcs = $this->getNpc($file);
            if(is_array($npcs) && sizeof($npcs)){
                $array['npcs'] += $this->loadNpc($npcs);
            }
            $warps = $this->getWarps($file);
            if(is_array($warps) && sizeof($warps)){
                $array['warps'] += $this->loadWarps($warps);
            }
            $monsters = $this->getMonsters($file);
            if(is_array($monsters) && sizeof($monsters)){
                $array['mobs'] += $this->loadMonsters($monsters);
            }
            $shops = $this->getShops($file);
            if(is_array($shops) && sizeof($shops)){
                $array['shops'] += $this->loadShops($shops);
            }
        }
        return $array;
    }

    private function loadShops(array $data){
		$npcsDB = 	"{$this->server->charMapDatabase}.".FLUX::config("FluxTables.NpcsSpawnTable");
		$shopsDB = 	"{$this->server->charMapDatabase}.".FLUX::config("FluxTables.VendorsTable");
		$debug1 = array();
		$debug2 = array();

        $sql = "insert into $npcsDB (`map`, `x`, `y`, `name`, `sprite`, `id`, `is_shop`) values ";
        $sql_item = "insert into $shopsDB (`item`, `price`, `id_shop`) values ";
		$debug1 = array();
		$debug2 = array();
        $sth = $this->server->connection->getStatement("select max(id) id from $npcsDB");
        $sth->execute();
        $id = $sth->fetch();
        $id = (int)$id->id + 1;

        $array = array();
        $sells_items = array();
        $import_array = array();
        $import_sql = array();
        foreach($data as $item){
            $import = explode(',', $item['npc']);
            $sells = explode(',', $item['item']);
            if(sizeof($import) != 5){
                continue;
            }
           foreach($sells as $sel_item){
                $array[] = '(?, ?, ?)';
                $sel_item = explode(':', $sel_item);
                $sel_item[1] = trim($sel_item[1]);
                $sells_items = array_merge($sells_items, $sel_item);
                $sells_items[] = $id;
				$debug2[] = "(".$sel_item[0].", ".$sel_item[0].", $id)";
            }
            $import[] = $id ++;
            $import[] = 1;
            $import_array = array_merge($import_array, $import);
            $import_sql[] = '(?, ?, ?, ?, ?, ?, ?)';
			$debug1[] = "('".$import[0]."', '".$import[1]."', '".$import[2]."', '".$import[3]."', '".$import[4]."', '".$import[5]."', '".$import[6]."')";
        }
        if(sizeof($import_sql)) {
            $sth = $this->server->connection->getStatement($sql . join(',', $import_sql). ';');
            $sth->execute($import_array);
            $sth = $this->server->connection->getStatement($sql_item . join(',', $array). ';');
            $sth->execute($sells_items);
        }
		if(FLUX::config('DebugMode')) {
			$fd = fopen(FLUX_DATA_DIR . "/debug/shops.txt", 'a');
			fwrite($fd, "insert into $npcsDB (`map`, `x`, `y`, `name`, `sprite`, `id`, `is_shop`) values \n".implode(",\n", $debug1). ";\n");
			fclose($fd);
			$fd = fopen(FLUX_DATA_DIR . "/debug/sell_items.txt", 'a');
			fwrite($fd, "insert into $shopsDB (`item`, `price`, `id_shop`) values \n".implode(",\n", $debug2). ";\n");
			fclose($fd);
		}
        return sizeof($import_sql);
    }

    private function getShops($file){
        if(!file_exists($file)){
            return false;
        }
        $text = file_get_contents($file);
        preg_match_all("/((.*),([0-9]+)\t(shop|duplicate\(([^\)]+)\))\t(.*?)\t([0-9a-zA-Z_]+),?(.*))/", $text, $match);
        $data = $match[1];
        foreach($data as $key => &$item){
            if(substr(trim($item), 0, 2) == '//'){
                unset($data[$key]);
                continue;
            }
            preg_match("/\tduplicate\(([^\)]+)\)\t/", $item, $match);
            $duplicate = $match[1];
            if($duplicate){
                preg_match("/\tshop\t" . preg_quote($duplicate) . "\t([0-9a-zA-Z_]+),?(.*)/", $text, $sell_items);
                if(!sizeof($sell_items)) {
                    unset($data[$key]);
                    continue;
                } else {
                    $sell_items = $sell_items[2];
                }
            } else {
                preg_match("/\tshop\t(.*?)\t([0-9a-zA-Z_]+),?(.*)/", $item, $sell_items);
                $sell_items = $sell_items[3];
            }
            $item = preg_replace("/,([0-9]+)\t(shop|duplicate\(([^\)]+)\))\t(.*?)\t([0-9a-zA-Z_]+),?(.*)/", ',$4,$5', $item);
            $item = explode(',', $item);
            $item[3] = explode('#', $item[3]);
            $item[3] = $item[3][0] ? $item[3][0] : 'No Name';
            $item = join(',', $item);
            $item = explode('::', $item);
            $item = $item[0];
            $item = array(
                'npc' => $item,
                'item' => $sell_items
            );
        }unset($item);
       return $data;
    }

    private function getNpc($file){
        if(!file_exists($file)){
            return false;
        }
        $text = file_get_contents($file);
        preg_match_all("/((.*),([0-9]+)\t(script|duplicate\(([^\)]+)\))\t(.*?)\t([0-9a-zA-Z_]+),?([0-9]+,)?([0-9]+,)?(.*))/", $text, $match);
        $data = $match[1];
        foreach($data as $key => &$item){
            if(substr(trim($item), 0, 2) == '//'){
                unset($data[$key]);
                continue;
            }
            preg_match("/\tduplicate\(([^\)]+)\)\t/", $item, $match);
            $duplicate = $match[1];
            if($duplicate && !preg_match("/\tscript\t" . $duplicate . "\t/", $text)){
                unset($data[$key]);
                continue;
            }
            $item = preg_replace("/,([0-9]+)\t(script|duplicate\(([^\)]+)\))\t(.*?)\t([0-9a-zA-Z_]+),?([0-9]+,)?([0-9]+,)?(.*)/", ',$4,$5', $item);
            $item = explode(',', $item);
            $item[3] = explode('#', $item[3]);
            $item[3] = $item[3][0] ? $item[3][0] : 'No Name';
            $item = join(',', $item);
            $item = explode('::', $item);
            $item = $item[0];
        }unset($item);
        return $data;
    }

    private function loadNpc(array $data){
		$npcsDB = 	"{$this->server->charMapDatabase}.".FLUX::config("FluxTables.NpcsSpawnTable");

        $sql = "insert into $npcsDB (`map`, `x`, `y`, `name`, `sprite`, `id`, `is_shop`)values";
        $sth = $this->server->connection->getStatement("select max(id) id from $npcsDB");
        $sth->execute();
        $id = $sth->fetch();
        $id = (int)$id->id + 1;
        $array = array();
        $insert = array();
        $debug = array();
        foreach($data as $item){
            $import = explode(',', $item);
            if(sizeof($import) != 5){
                continue;
            }
            $import[] = $id++;
            $import[] = 0;
            $array = array_merge($array, $import);
            $insert[] = '(?, ?, ?, ?, ?, ?, ?)';
 			$debug[] = "('".$import[0]."', '".$import[1]."', '".$import[2]."', '".$import[3]."', '".$import[4]."', '".$import[5]."', '".$import[6]."')";
       }
        if(sizeof($insert)) {
            $sql .= join(',', $insert);
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($array);
        }
 		if(FLUX::config('DebugMode')) {
			$fd = fopen(FLUX_DATA_DIR . "/debug/npcs.txt", 'a');
			fwrite($fd, ("insert into $npcsDB (`map`, `x`, `y`, `name`, `sprite`, `id`, `is_shop`)values" . "\n" . implode(",\n", $debug). ";\n"));
			fclose($fd);
		}
       return sizeof($insert);
    }

    private function getWarps($file){
        if(!file_exists($file)){
            return false;
        }
        $text = file_get_contents($file);
        preg_match_all("/((.*),([0-9]+)\twarp\t(.*?)\t([0-9]+),([0-9]+),(.*))/", $text, $match);
        $data = $match[1];
        foreach($data as $key => &$item){
            if(substr(trim($item), 0, 2) == '//'){
                unset($data[$key]);
                continue;
            }
            $item = preg_replace("/,([0-9]+)\twarp\t(.*?)\t([0-9]+),([0-9]+),/", ',', $item);
        }unset($item);
        return $data;
    }

    private function loadWarps(array $data){
		$warpsDB = 	"{$this->server->charMapDatabase}.".FLUX::config("FluxTables.WarpsTable");
        $sql = "insert into $warpsDB (`map`, `x`, `y`, `to`, `tx`, `ty`)values";
        $array = array();
        $insert = array();
		$debug = array();
        foreach($data as $item){
			$item = trim($item);
            $import = explode(',', $item);
            if(sizeof($import) != 6){
                continue;
            }
            $array = array_merge($array, $import);
            $insert[] = '(?, ?, ?, ?, ?, ?)';
			$debug[] = "('".$import[0]."', '".$import[1]."', '".$import[2]."', '".$import[3]."', '".$import[4]."', '".$import[5]."')";
        }
        if(sizeof($insert)) {
            $sql .= join(',', $insert);
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($array);
        }
		if(FLUX::config('DebugMode')) {
			$fd = fopen(FLUX_DATA_DIR . "/debug/warps.txt", 'a');
			fwrite($fd, ("insert into $warpsDB (`map`, `x`, `y`, `to`, `tx`, `ty`)values" . "\n" . implode(",\n", $debug). ";\n"));
			fclose($fd);
		}
        return sizeof($insert);
    }

    private function getMonsters($file){
        if(!file_exists($file)){
            return false;
        }
        $text = file_get_contents($file);
        preg_match_all("/((.*)\t(boss_)?monster\t(.*))/", $text, $match);
        $data = $match[1];
        foreach($data as $key => &$item){
            if(substr(trim($item), 0, 2) == '//'){
                unset($data[$key]);
                continue;
            }
			if(preg_match("/\/\//i", $item))
				$item = preg_replace("/\t(boss_)?monster\t(.*?)\t/", ',$2,', stristr($item, '//', true));
			else
				$item = preg_replace("/\t(boss_)?monster\t(.*?)\t/", ',$2,', $item);
        }unset($item);
        return $data;
    }

    private function loadMonsters(array $data){
		$mobsDB = 	"{$this->server->charMapDatabase}.".FLUX::config("FluxTables.MobsSpawnTable");
        $sql = "insert into $mobsDB (`map`, `x`, `y`, `range_x`, `range_y`, `mob_id`, `count`, `time_to`, `time_from`)values";
        $array = array();
        $insert = array();
        $debug = array();
        foreach($data as $item){
			$item = trim($item);
            $import = explode(',', $item);
			$import_temp = array();
			if(sizeof($import) > 10){
				$import = array_slice($import, 0, 10);
			}
			elseif(sizeof($import) == 4 && preg_match("/[a-zA-Z]/i", $import[1])){
				$x = 0;
				for($i = 0; $i < 10 - $x; $i ++){
					if($i == 1) {
						$import_temp[] = 0;
						$import_temp[] = 0;
						$import_temp[] = 0;
						$import_temp[] = 0;
						$x += 4;
					}
					$import_temp[] = isset($import[$i]) ? $import[$i] : 0;
				}
				$import = $import_temp;
			}
			elseif(sizeof($import) == 7 && preg_match("/[a-zA-Z]/i", $import[1])){
				$x = 0;
				for($i = 0; $i < 10 - $x; $i ++){
					if($i == 1) {
						$import_temp[] = 0;
						$import_temp[] = 0;
						$import_temp[] = 0;
						$import_temp[] = 0;
						$x += 4;
					}
					$import_temp[] = isset($import[$i]) ? $import[$i] : 0;
				}
				$import = $import_temp;
			}
			elseif(sizeof($import) <= 9){
				$x = 0;
				for($i = 0; $i < 10 - $x; $i ++){
					if($i == 3 && preg_match("/[a-zA-Z]/i", $import[$i])) {
						$import_temp[] = 0;
						$import_temp[] = 0;
						$x += 2;
					}
					$import_temp[] = isset($import[$i]) ? $import[$i] : 0;
				}
				$import = $import_temp;
			}
			$import = array_diff($import, [$import[5]]);
			$import = array($import[0], $import[1], $import[2], $import[3], $import[4], $import[6], $import[7], $import[8], $import[9]);
            $array = array_merge($array, $import);
            $insert[] = '(?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $debug[] = "('".$import[0]."', '".$import[1]."', '".$import[2]."', '".$import[3]."', '".$import[4]."', '".$import[6]."', '".$import[7]."', '".$import[8]."', '".$import[9]."')";
        }
        if(sizeof($insert)) {
            $sql .= join(',', $insert);
            $sth = $this->server->connection->getStatement($sql);
            $sth->execute($array);
		}
 		if(FLUX::config('DebugMode')) {
			$fd = fopen(FLUX_DATA_DIR . "/debug/monsters.txt", 'a');
			fwrite($fd, ("insert into $mobsDB (`map`, `x`, `y`, `range_x`, `range_y`, `mob_id`, `count`, `time_to`, `time_from`)values" . "\n" . implode(",\n", $debug). ";\n"));
			fclose($fd);
		}
		return sizeof($insert);
    }
}