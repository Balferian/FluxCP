<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('SDCreateNew');

if (Flux::config('MasterAccount')) {
	require_once 'includes/mastercreate.php';
} else {
	require_once 'includes/defaultcreate.php';
}
?>
