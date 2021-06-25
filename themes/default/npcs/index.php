<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo FLUX::message('NpcsDBLabel'); ?></h2>
<p class="toggler"><a href="javascript:toggleSearchForm()"><?php echo FLUX::message('SearchBtnDBLabel'); ?>...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="map"><?php echo FLUX::message('SearchMapDBLabel'); ?>:</label>
		<input type="text" name="map" id="map" value="<?php echo htmlspecialchars($params->get('map')) ?>" />
		...
		<label for="name"><?php echo FLUX::message('SearchNameDBLabel'); ?>:</label>
		<input type="text" name="name" id="name" value="<?php echo htmlspecialchars($params->get('name')) ?>" />
		...
		<label for="npc_type"><?php echo FLUX::message('SearchTypeDBLabel'); ?>:</label>
		<select name="npc_type" id="npc_type">
			<option value="all"<?php if ($npc_type == "all") echo ' selected="selected"' ?>><?php echo FLUX::message('AllTypeDBLabel'); ?></option>
			<option value="npc"<?php if ($npc_type == "npc") echo ' selected="selected"' ?>><?php echo FLUX::message('NpcTypeDBLabel'); ?></option>
			<option value="shop"<?php if ($npc_type == "shop") echo ' selected="selected"' ?>><?php echo FLUX::message('ShopTypeDBLabel'); ?></option>
		</select>
		...
		<input type="submit" value="<?php echo FLUX::message('SearchBtnDBLabel'); ?>" />
		<input type="button" value="<?php echo FLUX::message('ResetBtnDBLabel'); ?>" onclick="reload()" />
	</p>
</form>
<?php if ($npcs): ?>
    <?php echo $paginator->infoText() ?>
		<?php foreach ($npcs as $npc): ?>
			<div class="npcs_list">
				<img src="<?php echo $this->monsterImage($npc->sprite)?>"/>
				<p>
                    <?php if ($auth->actionAllowed('npcs', 'view')): ?>
                        <?php echo '<a href="' . $this->url('npcs', 'view', array('id' => $npc->id)) . '">' . htmlspecialchars($npc->name) . '</a>' ?>
                    <?php else: ?>
                        <?php echo htmlspecialchars($npc->name) ?>
                    <?php endif ?>
				</p>
				<p><?php echo $npc->map.','.$npc->x.','.$npc->y; ?></p>
			</div>
		<?php endforeach ?>
    <?php echo $paginator->getHTML() ?>
<?php else: ?>
    <p><?php echo FLUX::message('NpcsNotFoundDBLabel'); ?> <a href="javascript:history.go(-1)"><?php echo FLUX::message('GoBackLabel'); ?></a>.</p>
<?php endif ?>
