<?php
if (!defined('FLUX_ROOT')) exit;

$title = 'Service Shop';

require_once 'Flux/VipShop.php';

$category      = $params->get('category');
$categories    = Flux::config("VipCategories")->toArray();
$categoryName  = Flux::config("VipCategories.$category.0");
$categoryCount = array();
$shop          = new Flux_VipShop($server);
$sql           = sprintf("SELECT COUNT(id) AS total FROM %s.%s WHERE category = ?", $server->charMapDatabase, Flux::config('FluxTables.VipServiceShopTable'));
$sql2          = sprintf("SELECT COUNT(id) AS total FROM %s.%s", $server->charMapDatabase, Flux::config('FluxTables.VipServiceShopTable'));
$sth           = $server->connection->getStatement($sql);
$sth2          = $server->connection->getStatement($sql2);
$sth2->execute();
$total         = $sth2->fetch()->total;

foreach ($categories as $catID => $catName) {
	$sth->execute(array($catID));
	$categoryCount[$catID] = $sth->fetch()->total;
}

$categoryTotal = isset($category) ? $categoryCount[$category] : $total;
$perPage       = Flux::config("VipShopItemPerPage");
$paginator     = $this->getPaginator($categoryTotal, array('perPage' => $perPage));
$items         = $shop->getItems($paginator, $category);

?>
