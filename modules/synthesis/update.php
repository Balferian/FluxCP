<?php
if (!defined('FLUX_ROOT')) exit;
require_once 'Flux/FileLoad.php';
require_once 'functions/lua_parser.php';

$SynthesisTable = "{$server->charMapDatabase}.".Flux::config('FluxTables.SynthesisTable');
$title = 'Synthesis Update';
$fileLoad = new FileLoad();

// upload and parse map.
if($files->get('lua_file')) {
    $lua_file = FLUX_DATA_DIR . '/luafiles514/lua files/datainfo/lapineddukddakbox.lub';
    $is_loaded = $fileLoad->load($files->get('lua_file'), $lua_file);
    if($is_loaded === true) {
		$sql  = "TRUNCATE TABLE $SynthesisTable";
		$sth = $server->connection->getStatement($sql);
		$sth->execute();
		$parser = new LUAParser();
		try {
			$parser->parseFile($lua_file);
			foreach($parser->data['tblLapineDdukddakBox']['sources'] as $key => $data) {
				$temp = array();
				foreach($data['SourceItems'] as $SourceItems) {
					$temp[] = preg_replace("~.*\"(.*)\", (\d+), (\d+).*~is", "$3:$2", $SourceItems);
				}
				$sql  = "INSERT INTO $SynthesisTable (`ItemID`, `NeedCount`, `NeedRefineMin`, `NeedRefineMax`, `SourceItem`, `NeedSource_String`) VALUES (?, ?, ?, ?, ?, ?);";
				$sth = $server->connection->getStatement($sql);
				$sth->execute(array($data['ItemID'], $data['NeedCount'], $data['NeedRefineMin'], $data['NeedRefineMax'], implode(",", $temp), iconv("CP949", "UTF-8", str_replace('"', '', $data['NeedSource_String']))));
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

$sth = $server->connection->getStatement("SELECT COUNT(ItemID) AS count FROM $SynthesisTable GROUP BY ItemID");
$sth->execute();
$return = $sth->fetchAll();
