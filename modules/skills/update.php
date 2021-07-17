<?php
if (!defined('FLUX_ROOT')) exit;
require_once 'Flux/FileLoad.php';
require_once 'functions/lua_parser.php';

$SkillsTable = "{$server->charMapDatabase}.".Flux::config('FluxTables.SkillsTable');
$title = 'Skills Update';
$fileLoad = new FileLoad();

// upload and parse map.
if($files->get('skillid_lua') && $files->get('skilldescript_lua') && $files->get('skillinfolist_lua')) {
	$sql  = "TRUNCATE TABLE $SkillsTable";
	$sth = $server->connection->getStatement($sql);
	$sth->execute();

    $skillid_lua = FLUX_DATA_DIR . '/luafiles514/lua files/skillinfoz/skillid.lub';
    $is_loaded = $fileLoad->load($files->get('skillid_lua'), $skillid_lua);
    if($is_loaded === true) {
		$parser = new LUAParser();
		try {
			$parser->parseFile($skillid_lua);
			foreach($parser->data['SKID'] as $skill_name => $skill_id) {
				$sql  = "INSERT INTO $SkillsTable (`skill_id`, `skill_aegis`) VALUES (?, ?)";
				$sth = $server->connection->getStatement($sql);
				$sth->execute(array($skill_id, $skill_name));
			}
		}
		catch(Exception $e) {
			echo 'Exception: ',  $e->getMessage(), PHP_EOL;
		}
        $fileLoad->delete();
    } else {
        $errorMessage = $is_loaded;
    }
	
    $skilldescript_lua = FLUX_DATA_DIR . '/luafiles514/lua files/skillinfoz/skilldescript.lub';
    $is_loaded = $fileLoad->load($files->get('skilldescript_lua'), $skilldescript_lua);
    if($is_loaded === true) {
		$parser = new LUAParser();
		try {
			$parser->parseFile($skilldescript_lua);
			foreach($parser->data['SKILL_DESCRIPT'] as $skill_aegis => $skill_desc) {
				if(!is_array($skill_desc))
					continue;
				$skill_desc = iconv("CP949", "UTF-8", implode("\n", $skill_desc));
				$sql  = "UPDATE $SkillsTable SET `skill_descript` = ? WHERE `skill_aegis` = ?";
				$sth = $server->connection->getStatement($sql);
				$sth->execute(array(str_replace('"', '', $skill_desc), preg_replace('/.*\.(.*?)/', '$1', $skill_aegis)));
			}
		}
		catch(Exception $e) {
			echo 'Exception: ',  $e->getMessage(), PHP_EOL;
		}
        $fileLoad->delete();
    } else {
        $errorMessage = $is_loaded;
    }
	
    $skillinfolist_lua = FLUX_DATA_DIR . '/luafiles514/lua files/skillinfoz/skillinfolist.lub';
    $is_loaded = $fileLoad->load($files->get('skillinfolist_lua'), $skillinfolist_lua);
    if($is_loaded === true) {
		$parser = new LUAParser();
		try {
			$parser->parseFile($skillinfolist_lua);
			foreach($parser->data['SKILL_INFO_LIST'] as $skill_aegis => $skill_info) {
				$skill_info['SkillName'] = iconv("CP949", "UTF-8", str_replace('"', '', $skill_info['SkillName']));
				$sql  = "UPDATE $SkillsTable SET `skill_name` = ?, `skill_max` = ? , `skill_separate` = ? WHERE `skill_aegis` = ?";
				$sth = $server->connection->getStatement($sql);
				$sth->execute(array($skill_info['SkillName'], $skill_info['MaxLv'], $skill_info['bSeperateLv'], preg_replace('/.*\.(.*?)/', '$1', $skill_aegis)));
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

$sth = $server->connection->getStatement("SELECT COUNT(skill_id) AS count FROM $SkillsTable");
$sth->execute();
$return = $sth->fetch();
