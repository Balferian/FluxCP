<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo FLUX::message('NpcsViewLabel'); ?></h2>
<?php if($npc): ?>
<table>
	<tr>
		<td style="width: 512px;">
			<div class="map_block">
				<img src="<?php echo $this->mapImage($map->name, $map->x, $map->y); ?>" 
					style="<?php
						if($map->x == $map->y) echo "width:100%;height:100%;";
						if($map->x > $map->y) echo "width:100%;";
						if($map->x < $map->y) echo "height:100%;";
					?>">
					
				<?php if($npc->x && $npc->y): ?>
					<div class="you_here" style="
					left:<?php echo conv((int)$npc->x, $map->x, $map) - 5; ?>px;
					bottom:<?php echo conv((int)$npc->y, $map->y, $map) - 5; ?>px;
					"></div>
				<?php endif; ?>
			</div>
		</td>
		<td style="padding: 0 10px;">
			<h3><?php echo $npc->name . ($npc->is_shop ? ' (shop)' : ''); ?></h3>
			<img src="<?php echo $this->monsterImage($npc->sprite); ?>" />
			<hr>
			<b><?php echo FLUX::message('SearchNameDBLabel'); ?></b>
			<?php echo $npc->name; ?>
			<br>
			<b><?php echo FLUX::message('SearchTypeDBLabel'); ?></b>
			<?php echo $npc->is_shop ? FLUX::message('ShopTypeDBLabel') : FLUX::message('NpcTypeDBLabel'); ?>
			<br>
			<b><?php echo FLUX::message('SearchMapDBLabel'); ?></b>
			<?php if($auth->actionAllowed('map', 'view')): ?>
				<a href="<?php echo $this->url('map', 'view', array('map' => $npc->map)); ?>"><?php echo $npc->map; ?></a>
			<?php else: ?>
				<?php echo $npc->map; ?>
			<?php endif; ?>
			<br>
			<b><?php echo FLUX::message('CoordinatesDBLabel'); ?></b>
		</td>
	</tr>
	<tr>
		<?php if($items): ?>
			<td colspan="2" class="shops_list">
				<h3><?php echo FLUX::message('NpcsSaleListLabel'); ?></h3>
					<ul>
						<?php foreach($items as $item): ?>
							<li>
								<img src="<?php echo htmlspecialchars($this->iconImage($item->item)); ?>?nocache=<?php echo rand(); ?>" />
								<div>
									<?php echo $auth->actionAllowed('item', 'view') ? $this->linkToItem($item->item, $item->name) : htmlspecialchars($item->name); ?>
									<br>
									<span><?php echo ($item->price == -1 ? $item->price_buy : $item->price).' '.FLUX::message('ServerInfoZenyLabel'); ?></span>
								</div>
							</li>
						<?php endforeach; ?>
						<?php for($i = 1; $i <= 4-(count($items)%4); $i++) echo "<li></li>"; ?>
					</ul>
			</td>
		<?php endif; ?>
	</tr>
</table>
<?php else: ?>
    <p><?php echo FLUX::message('NpcsNotFound2DBLabel'); ?></p>
<?php endif; ?>
