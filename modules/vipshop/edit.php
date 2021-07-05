<?php
if (!defined('FLUX_ROOT')) exit; 

$this->loginRequired();

$title = 'Modify Service in the Shop';

require_once 'Flux/TemporaryTable.php';
require_once 'Flux/VipShop.php';

$shopItemID  = $params->get('id');
$shop        = new Flux_VipShop($server);
$categories  = Flux::config('VipCategories')->toArray();
$item        = $shop->getItem($shopItemID);

if ($item) {
	if (count($_POST)) {
		$maxCost     = (int)Flux::config('VipShopMaxCost');
		$maxQty      = (int)Flux::config('VipShopMaxQuantity');
		$category    = $params->get('category');
		$cost        = (int)$params->get('cost');
		$quantity    = (int)$params->get('qty');
		$info        = trim($params->get('info'));

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
			if ($shop->edit($shopItemID, $category, $cost, $quantity, $info)) {
				$session->setMessageData('Service has been successfully modified.');
				$this->redirect($this->url('vipshop'));
			}
			else {
				$errorMessage = 'Failed to modify the service.';
			}
		}
	}
	
	if (empty($category)) {
		$category = $item->shop_item_category;
	}
	if (empty($cost)) {
		$cost = $item->shop_item_cost;
	}
	if (empty($quantity)) {
		$quantity = $item->shop_item_qty;
	}
	if (empty($info)) {
		$info = $item->shop_item_info;
	}
}

if (!$stackable) {
	$params->set('qty', 1);
}
?>
