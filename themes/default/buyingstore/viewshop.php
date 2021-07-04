<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars($title); ?></h2>
<?php if ($store): ?>
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
					
				<?php if($store->x && $store->y): ?>
					<div class="you_here" style="
					left:<?php echo conv((int)$store->x, $map->x, $map) - 5; ?>px;
					bottom:<?php echo conv((int)$store->y, $map->y, $map) - 5; ?>px;
					"></div>
				<?php endif; ?>
			</div>
		</td>
		<td style="padding: 0 10px;">
			<h3><?php echo sprintf(Flux::message('BuyingstoreTitle'), $store->title); ?></h3>
			<img src="<?php echo $this->monsterImage(858); ?>" />
			<hr>
			<b><?php echo FLUX::message('SearchNameDBLabel'); ?>:</b>
			<?php echo $store->char_name; ?>
			<br>
			<b><?php echo FLUX::message('SearchMapDBLabel'); ?>:</b>
			<?php if($auth->actionAllowed('map', 'view')): ?>
				<a href="<?php echo $this->url('map', 'view', array('map' => $store->map)); ?>"><?php echo $store->map; ?></a>
			<?php else: ?>
				<?php echo $store->map; ?>
			<?php endif; ?>
			<br>
			<b><?php echo FLUX::message('CoordinatesDBLabel'); ?>:</b>
			<?php echo $store->x; ?>,<?php echo $store->y; ?>
		</td>
	</tr>
	<tr>
		<?php if($items): ?>
			<td colspan="2" class="shops_list">
				<h3><?php echo FLUX::message('BuyingstoreBuyTitle'); ?></h3>
				<ul>
					<?php foreach($items as $item): ?>
						<li>
							<img src="<?php echo htmlspecialchars($this->iconImage($item->nameid)); ?>?nocache=<?php echo rand(); ?>" />
							<div>
								<?php echo $auth->actionAllowed('item', 'view') ? $this->linkToItem($item->nameid, $item->item_name) : htmlspecialchars($item->item_name); ?> <b>[<?php echo $item->amount; ?> <?php echo FLUX::message('EALabel'); ?>]</b>
								<br>
								<span><?php echo ($item->price == -1 ? $item->price_buy : $item->price).' '.FLUX::message('ServerInfoZenyLabel'); ?></span>
							</div>
						</li>
					<?php endforeach; ?>
					<?php for($i = 1; $i <= 4-(count($items)%4); $i++) echo "<li></li>"; ?>
				</ul>
			</td>
		<?php else: ?>
			<td>No Items found. <a href="javascript:history.go(-1)">Go back</a>.</td>
		<?php endif ?>
	</tr>
</table>
<?php else: ?>
	<p>No Buyer found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
