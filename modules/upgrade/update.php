<?php
if (!defined('FLUX_ROOT')) exit;
require_once 'Flux/FileLoad.php';
require_once 'functions/lua_parser.php';

$UpgradesTable = "{$server->charMapDatabase}.".Flux::config('FluxTables.UpgradeTable');
$title = 'Upgrades Update';
$fileLoad = new FileLoad();

// upload and parse map.
if($files->get('lua_file')) {
    $lua_file = FLUX_DATA_DIR . '/luafiles514/lua files/datainfo/lapineupgradebox.lub';
    $is_loaded = $fileLoad->load($files->get('lua_file'), $lua_file);
    if($is_loaded === true) {
		$sql  = "TRUNCATE TABLE $UpgradesTable";
		$sth = $server->connection->getStatement($sql);
		$sth->execute();
		$parser = new LUAParser();
		try {
			$parser->parseFile($lua_file);
			foreach($parser->data['tblLapineUpgradeBox']['targets'] as $key => $data) {
				$temp = array();
				foreach($data['TargetItems'] as $TargetItems) {
					$temp[] = preg_replace("~.*\"(.*)\", (\d+).*~is", "$2", $TargetItems);
				}
				if(!isset($data['NeedRefineMax'])) $data['NeedRefineMax'] = 0;
				$sql  = "INSERT INTO $UpgradesTable (`ItemID`, `NeedRefineMin`, `NeedRefineMax`, `NeedOptionNumMin`, `NotSocketEnchantItem`, `TargetItems`, `NeedSource_String`) VALUES (?, ?, ?, ?, ?, ?, ?);";
				$sth = $server->connection->getStatement($sql);
				$sth->execute(array($data['ItemID'], $data['NeedRefineMin'], $data['NeedRefineMax'], $data['NeedOptionNumMin'], $data['NotSocketEnchantItem'], implode(",", $temp), iconv("CP949", "UTF-8", str_replace('"', '', $data['NeedSource_String']))));
			}
		}
		catch(Exception $e) {
			echo 'Exception: ',  $e->getMessage(), PHP_EOL;
		}
        $fileLoad->delete();
    } else {
        $errorMessage = $is_loaded;
    }
}

$sth = $server->connection->getStatement("SELECT COUNT(ItemID) AS count FROM $UpgradesTable GROUP BY ItemID");
$sth->execute();
$return = $sth->fetchAll();
