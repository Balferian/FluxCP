<?php
if (!defined('FLUX_ROOT')) exit;
$title = 'Skills database';

$SkillsTable = "{$server->charMapDatabase}.".Flux::config('FluxTables.SkillsTable');
$ignore_skills = array(
	"`skill_aegis` != 'LAST'",
	"`skill_aegis` != 'FOLLOWER_NPC_RESET'",
	"`skill_aegis` NOT LIKE '%_LAST'",
	"`skill_aegis` NOT LIKE '%_END'",
	"`skill_aegis` NOT LIKE '%_BEGIN'",
	"`skill_aegis` NOT LIKE 'SYS_%'",
	"`skill_aegis` NOT LIKE 'SCRIPT_%'",
	"`skill_aegis` NOT LIKE 'ITEM_%'",
	"`skill_aegis` NOT LIKE 'EFST_%'",
);
$sqlpartial = $ignore_skills ? ' WHERE '.implode(" AND ", $ignore_skills) : '';
$sql = "SELECT COUNT(skill_id) as total FROM $SkillsTable $sqlpartial";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$perPage       = FLUX::config('ResultsPerPage');
$paginator     = $this->getPaginator($sth->fetch()->total, array('perPage' => $perPage));
$paginator->setSortableColumns(array('name'));

$sql  = "SELECT * FROM $SkillsTable $sqlpartial";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);
$sth->execute();
$Skills = $sth->fetchAll();
?>
