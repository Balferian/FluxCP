<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

require_once 'Flux/VipShop.php';

if ($server->cartvip && $server->cartvip->clear()) {
	$session->setMessageData("Your cart has been emptied.");
}
else {
	$session->setMessageData("Couldn't empty your cart, maybe it's already empty.");
}

$this->redirect($this->url('vipshop'));
?>
