<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

if (!$auth->allowedToDeleteShopItem) {
	$this->deny();
}

require_once 'Flux/VipShop.php';

$shop       = new Flux_VipShop($server);
$shopItemID = $params->get('id');
$deleted    = $shopItemID ? $shop->delete($shopItemID) : false;

if ($deleted) {
	$session->setMessageData('Service successfully deleted from the service shop.');
	$this->redirect($this->url('vipshop'));
}
?>
