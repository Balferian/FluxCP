<?php
// Module variables are available in page menus.
// However, access group_id checking must be done directly from the page menu.
// Minimal access checking such as $auth->actionAllowed('moduleName', 'actionName') should be performed.
$groups  = AccountLevel::getArray();

$ServiceDeskItems = $this->getMenuItems('ServiceDeskItems');
$pageMenu = array();

foreach ($ServiceDeskItems as $menuCategory => $menus)
	foreach ($menus as $menuItem)
		$pageMenu[Flux::message($menuItem['name'])] = $this->url($menuItem['module'], $menuItem['action']);
return $pageMenu;
?>

