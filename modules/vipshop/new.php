<?php
if (!defined('FLUX_ROOT')) exit; 

$this->loginRequired();

$title = 'Add Service to Shop';

require_once 'Flux/TemporaryTable.php';
require_once 'Flux/VipShop.php';


$category   = null;
$categories = Flux::config('VipCategories')->toArray();

if (count($_POST)) {
	$maxCost     = (int)Flux::config('VipShopMaxCost');
	$maxQty      = (int)Flux::config('VipShopMaxQuantity');
	$category    = $params->get('category');
	$shop        = new Flux_VipShop($server);
	$cost        = (int)$params->get('cost');
	$quantity    = (int)$params->get('qty');
	$info        = trim(htmlspecialchars($params->get('info')));
	
	if (!$cost) {
		$errorMessage = 'You must input a credit cost greater than zero.';
	}
	elseif ($cost > $maxCost) {
		$errorMessage = "The credit cost must not exceed $maxCost.";
	}
	elseif (!$quantity) {
		$errorMessage = 'You must input a quantity greater than zero.';
	}
	elseif ($quantity > $maxQty) {
		$errorMessage = "The service quantity must not exceed $maxQty.";
	}
	elseif (!$info) {
		$errorMessage = 'You must input at least some info text.';
	}
	else {
		if ($id=$shop->add($category, $cost, $quantity, $info)) {
			$message = 'Service has been successfully added to the shop';
			$session->setMessageData($message);
			$this->redirect($this->url('vipshop'));	
		}
		else {
			$errorMessage = 'Failed to add the service to the shop.';
		}
	}
}
?>
