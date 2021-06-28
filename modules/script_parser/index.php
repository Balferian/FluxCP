<?php
if (!defined('FLUX_ROOT')) exit;
require_once 'functions/ScriptParser/parse.php';
require_once 'functions/ScriptParser/mapImage.php';
error_reporting(0);

$title = 'Spawn Monsters';
$shopsDB = 		"{$server->charMapDatabase}.".FLUX::config("FluxTables.VendorsTable");
$mobsDB = 		"{$server->charMapDatabase}.".FLUX::config("FluxTables.MobsSpawnTable");
$mapsDB = 		"{$server->charMapDatabase}.".FLUX::config("FluxTables.MapsTable");
$warpsDB = 		"{$server->charMapDatabase}.".FLUX::config("FluxTables.WarpsTable");
$npcsDB = 		"{$server->charMapDatabase}.".FLUX::config("FluxTables.NpcsSpawnTable");
$mapflagsDB =	"{$server->charMapDatabase}.".FLUX::config("FluxTables.MapflagsTable");

if($params->get('act')){
    switch($params->get('act')){
        case 'truncate_db':
            try {
                $sth = $server->connection->getStatement("
                truncate table $npcsDB;
                truncate table $mobsDB;
                truncate table $warpsDB;
                truncate table $shopsDB;
                truncate table $mapflagsDB;
                ");
                $sth->execute();
            } catch(Exception $e){}

            $successMessage = FLUX::message('ScriptCleanUnitsCleanLabel');
            break;
        case 'truncate_map':
            try {
                $sth = $server->connection->getStatement("
                truncate table $mapsDB;
                ");
                $sth->execute();
            } catch(Exception $e){}

            $successMessage = FLUX::message('ScriptCleanMapsCleanLabel');
            break;
    }
}

$flieLoad = new FileLoad();

// upload and parse map.
if($files->get('map_index')) {
    $map_cache = FLUX_DATA_DIR . '/map_cache.dat';
    $is_loaded = $flieLoad->load($files->get('map_index'), $map_cache);
    if($is_loaded === true) {
        if(function_exists('mime_content_type') && mime_content_type($map_cache) != 'application/octet-stream'){
            $errorMessage = Flux::message('ScriptError1Label');
        } else {

            $array_insert = array();
            $data = file_get_contents($map_cache);

            $array = array(
                array('A12', 12),
                array('S', 2),
                array('S', 2),
                array('L', 4),
            );

            $count = 0;
            $i = 8;
            while ($i < strlen($data)) {
                $byte = '';
                for ($k = $i; $k < $i + $array[$count][1]; $k++) {
                    $byte .= $data[$k];
                }
                $datas = unpack($array[$count][0], $byte);
                if ($count != 3) {
                    $array_insert[] = trim($datas[1]);
                }
                $i += $array[$count][1];
                $count++;
                if (!isset($array[$count])) {
                    $count = 0;
                    $i += $datas[1];
                }
            }

            if (sizeof($array_insert) % 3 == 0) {
                $rows = sizeof($array_insert) / 3;
                $sql = "insert ignore into $mapsDB (`name`, `x`, `y`)values";
                $insert = array();
                for ($i = 0; $i < $rows; $i++) {
                    $insert[] = '(?, ?, ?)';
                }

                try {
                    $sql .= join(',', $insert);
                    $sth = $server->connection->getStatement($sql);
                    $sth->execute($array_insert);
                    $successMessage = sprintf(Flux::message('ScriptCleanMapsAddedLabel'), $rows);
                } catch (Exception $e) {
                    $errorMessage = $e->getMessage();
                }
            } else {
                $errorMessage = Flux::message('ScriptError2Label');
            }
        }
        $flieLoad->delete();
    } else {
        $errorMessage = $is_loaded;
    }
}

// upload and parse all npcs.
if($files->get('npc_zip')) {
    $npc_zip = FLUX_DATA_DIR . '/npc_zip.zip';
    $is_loaded = $flieLoad->load($files->get('npc_zip'), $npc_zip);
    if($is_loaded === true) {
        $dirExtract = FLUX_DATA_DIR . '/uploads';
        $zip = new ZipArchive;
        if ($zip->open($npc_zip) === true) {
            $zip->extractTo($dirExtract);
            $zip->close();
            $parse = new parse($server);
            $file = $parse->getFiles();
            $successMessage = sprintf(Flux::message('ScriptCleanUnitsAddedLabel'), sizeof($file));
        } else {
            $errorMessage = Flux::message('ScriptError3Label');
        }
        if (sizeof($file) == 0) {
            $errorMessage = Flux::message('ScriptError4Label');
        }
        $flieLoad->delete();
    } else {
        $errorMessage = $is_loaded;
    }
}

// get data from tables
$tables = array(
    "$mobsDB"					=> "MobSpawnBase",
    "$mapsDB"					=> "mapIndexBase",
    "$warpsDB"					=> "warpsBase",
    "$npcsDB where is_shop = 0"	=> "npcsBase",
    "$npcsDB where is_shop = 1"	=> "shopsBase",
    "$mapflagsDB"				=> "mapflagsBase"
);

foreach($tables as $table => $var) {
    try {
        $sth = $server->connection->getStatement('select count(*) as count from ' . $table);
        $sth->execute();
        $$var = $sth->fetch()->count;
        if ($$var === false || $$var === null) {
            throw new Flux_Error('db not found');
        }
    } catch (Exception $e) {
        $$var = false;
    }
}