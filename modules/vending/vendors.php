<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Vendors';

// Get total count and feed back to the paginator.
$sth = $server->connection->getStatement("SELECT COUNT(id) AS total FROM {$server->charMapDatabase}.vendings");
$sth->execute();
$paginator = $this->getPaginator($sth->fetch()->total);

// Set the sortable columns
$sortable = array(
    'id' => 'asc', 'map', 'char_name'
    
);
$paginator->setSortableColumns($sortable);

// Create the main request.
$sql    = "SELECT `char`.name as char_name, `char`.char_id, `vendings`.id, `vendings`.sex, `vendings`.map, `vendings`.x, `vendings`.y, `vendings`.title, autotrade ";
$sql    .= "FROM {$server->charMapDatabase}.vendings ";
$sql    .= "LEFT JOIN {$server->charMapDatabase}.`char` on vendings.char_id = `char`.char_id ";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);
$sth->execute();

$vendings = $sth->fetchAll();
?>
