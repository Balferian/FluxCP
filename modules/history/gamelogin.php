<?php if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = Flux::message('HistoryGameLoginTitle');

if (Flux::config('MasterAccount')) {
	require_once 'includes/mastergamelogin.php';
} else {
	require_once 'includes/defaultgamelogin.php';
}
?>