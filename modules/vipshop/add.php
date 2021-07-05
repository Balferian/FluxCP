<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired('Please log-in to add services to your cart.');

require_once 'Flux/VipShop.php';

$id   = $params->get('id');
$shop = new Flux_VipShop($server);
$item = $shop->getItem($id);
$categories    = Flux::config("VipCategories")->toArray();

if ($item) {
	$server->cartvip->add($item);
	$session->setMessageData($categories[$item->shop_category][0]." has been added to your cart.");
}
else {
	$session->setMessageData("Couldn't add service to your cart.");
}

$action = $params->get('cart') ? 'cart' : 'index';
$this->redirect($this->url('vipshop', $action));
?>
