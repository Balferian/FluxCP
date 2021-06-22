<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$AllowedStaffGroup = Flux::config('SDAllowedStaffGroup')->toArray();

if (Flux::config('MasterAccount')) {
	require_once 'includes/masterstaffsettings.php';
} else {
	require_once 'includes/defaultstaffsettings.php';
}
?>
