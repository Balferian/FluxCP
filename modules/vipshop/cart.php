<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

if ($server->cartvip->isEmpty()) {
	$session->setMessageData('Your cart is currently empty.');
	$this->redirect($this->url('vipshop'));
}

$title = 'Shopping Cart';
$categories    = Flux::config("VipCategories")->toArray();

require_once 'Flux/VipShop.php';
$items = $server->cartvip->getCartItems();
?>
