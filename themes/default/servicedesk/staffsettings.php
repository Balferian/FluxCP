<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();

if (Flux::config('MasterAccount')) {
	require_once 'includes/masterstaffsettings.php';
} else {
	require_once 'includes/defaultstaffsettings.php';
}
?>
