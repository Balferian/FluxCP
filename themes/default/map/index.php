<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo FLUX::message('MapsDBLabel'); ?></h2>
<p class="toggler"><a href="javascript:toggleSearchForm()"><?php echo FLUX::message('SearchBtnDBLabel'); ?>...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="map"><?php echo FLUX::message('SearchMapNameDBLabel'); ?>:</label>
		<input type="text" name="map" id="map" value="<?php echo htmlspecialchars($params->get('map')) ?>" />
		...
		<input type="submit" value="<?php echo FLUX::message('SearchBtnDBLabel'); ?>" />
		<input type="button" value="<?php echo FLUX::message('ResetBtnDBLabel'); ?>" onclick="reload()" />
	</p>
</form>
<?php if($maps_list): ?>
	<?php echo $paginator->infoText() ?>
	<?php foreach($maps_list as $map): ?>
		<div class="maps_list">
			<?php if($auth->actionAllowed('map', 'view')): ?>
				<a href="<?php echo $this->url('map', 'view', array('map' => $map->name)); ?>">
					<img src="<?php echo $this->mapImage($map->name) ?>" class="map_image"/>
					<p><?php echo $map->name; ?></p>
				</a>
			<?php else: ?>
				<img src="<?php echo $this->mapImage($map->name) ?>" class="map_image"/>
				<p><?php echo $map->name; ?></p>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
	<?php echo $paginator->getHTML() ?>
<?php else: ?>
	<p><?php echo FLUX::message('MapsNotFoundDBLabel'); ?></p>
<?php endif; ?>
