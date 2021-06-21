<?php
if (!defined('FLUX_ROOT')) exit;
$adminMenuItems = $this->getAdminMenuItems();
$menuItems = $this->getMenuItems();
?>

<?php if (!empty($adminMenuItems) && !Flux::config('AdminMenuNewStyle')): ?>
<div id="admin_sidebar">
	<div class="admin_sidebar_box">
		<div class="menuitem1"><strong>Admin Menu</strong></div>
		<?php foreach ($adminMenuItems as $menuItem):  ?>
		<div class="menuitem2">
			<a href="<?php echo $menuItem['url'] ?>"<?php
				if ($menuItem['module'] == 'account' && $menuItem['action'] == 'logout')
					echo ' onclick="return confirm(\'Are you sure you want to logout?\')"' ?>>
				<span><?php echo htmlspecialchars(Flux::message($menuItem['name'])) ?></span>
			</a>
		</div>
		<?php endforeach ?>
	</div>
</div>
<?php endif ?>

<?php if (!empty($menuItems)): ?>
<div id="sidebar">
	<div class="sidebar_box">
		<?php foreach ($menuItems as $menuCategory => $menus): ?>
		<?php if (!empty($menus)): ?>
		<div class="menuitem1"><strong><?php echo htmlspecialchars(Flux::message($menuCategory)) ?></strong></div>
		<?php foreach ($menus as $menuItem):  ?>
		<div class="menuitem2">
			<a href="<?php echo $menuItem['url'] ?>"<?php
				if ($menuItem['module'] == 'account' && $menuItem['action'] == 'logout')
					echo ' onclick="return confirm(\'Are you sure you want to logout?\')"' ?>>
				<span><?php echo htmlspecialchars(Flux::message($menuItem['name'])) ?></span>
			</a>
		</div>
		<?php endforeach ?>
		<?php endif ?>
		<?php endforeach ?>
	</div>
</div>
<?php endif ?>
