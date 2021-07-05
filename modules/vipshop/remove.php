<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$num = $params->get('num');
if (!is_null($num)) {
	if ($num instanceOf Flux_Config) {
		$num = $num->toArray();
	}
	
	$nRemoved = $server->cartvip->deleteByItemNum($num);
	if ($nRemoved) {
		if (!$server->cartvip->isEmpty()) {
			$session->setMessageData("Removed $nRemoved service(s) from your cart.");
			$this->redirect($this->url('vipshop', 'cart'));
		}
		else {
			$session->setMessageData("Removed $nRemoved service(s) from your cart. Your cart is now empty.");
		}
	}
	else {
		$session->setMessageData("There were no services to remove from your cart.");
	}
	
	$this->redirect($this->url('vipshop'));
}

$session->setMessageData('No services were removed from your cart because none were selected.');
$this->redirect($this->url('vipshop', 'cart'));
?>
