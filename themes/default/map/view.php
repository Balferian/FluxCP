<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo FLUX::message('MapsDBLabel'); ?><?php if($map->name) echo ': '.$map->name; ?></h2>
<?php if($map): ?>
	<table>
		<tr>
			<td>
				<div class="map_block">
					<img src="<?php echo $this->mapImage($map->name, $map->x, $map->y)?>" 
						style="<?php
							if($map->x == $map->y) echo "width:100%;height:100%;";
							if($map->x > $map->y) echo "width:100%;";
							if($map->x < $map->y) echo "height:100%;";
						?>">
						
					<?php if((int)$params->get('x') && (int)$params->get('y')): ?>
						<div class="you_here" style="
						left:<?php echo conv((int)$params->get('x'), $map->x, $map) - 5?>px;
						bottom:<?php echo conv((int)$params->get('y'), $map->y, $map) - 5?>px;
						"></div>
					<?php endif; ?>

					<?php foreach($npcs as $npc): ?>
						<div class="npc_<?php echo $npc->x?>-<?php echo $npc->y?> points_npcs map_hide npc_resp" style="
						left:<?php echo conv($npc->x, $map->x, $map) - 5?>px;
						bottom:<?php echo conv($npc->y, $map->y, $map) - 5?>px;
						"></div>
					<?php endforeach; ?>
					
					<?php foreach($shops as $shop): ?>
						<div class="npc_<?php echo $shop->x?>-<?php echo $shop->y?> points_npcs map_hide npc_resp" style="
						left:<?php echo conv($shop->x, $map->x, $map) - 5?>px;
						bottom:<?php echo conv($shop->y, $map->y, $map) - 5?>px;
						"></div>
					<?php endforeach; ?>

					<?php $isResp = false; foreach($mobs as $mob): ?>
						<?php if(!$mob->x){continue;} $isResp = true; ?>
						<div class="mob_spawn_<?php echo $mob->id?> points map_hide mob_resp" style="
						width:<?php echo conv($mob->range_x, $map->x)?>px;
						height:<?php echo conv($mob->range_y, $map->y)?>px;
						left:<?php echo conv($mob->x, $map->x, $map) - conv($mob->range_x, $map->x, $map) / 2?>px;
						bottom:<?php echo conv($mob->y, $map->y, $map) - conv($mob->range_y, $map->y, $map) / 2?>px;
						"></div>
					<?php endforeach; ?>

					<?php foreach($warps as $warp): ?>
						<a href="<?php echo $this->url('map', 'view', array('map' => $warp->to, 'x' => $warp->tx, 'y' => $warp->ty))?>">
						<div class="warps" style="
						left:<?php echo conv($warp->x, $map->x, $map) - 10?>px;
						bottom:<?php echo conv($warp->y, $map->y, $map) - 10?>px;
						"><div class="portal"></div></div></a>
					<?php endforeach; ?>
				</div>
			</td>
			<?php if($mapflags): ?>
				<td style="padding-left: 10px;">
					<h3><?php echo FLUX::message('MapflagsMapDBLabel'); ?></h3>
					<ul>
						<?php foreach($mapflags as $mapflag): ?>
							<?php if(in_array($mapflag->mapflag ,FLUX::config('AllowedMapflags')->toArray())) echo '<li>'.FLUX::message('Mapflag_'.$mapflag->mapflag).'</li>'; ?>
						<?php endforeach; ?>
					</ul>
				</td>
			<?php endif; ?>
		</tr>
	</table>

	<h3><?php echo FLUX::message('MobsOnMapDBLabel'); ?> <?php if($map->name) echo $map->name; ?></h3>
	<?php if($mobs): ?>
		<?php foreach($mobs as $mob): ?>
			<div class="npcs_list">
				<img class="npcs_hover" src="<?php echo $this->monsterImage($mob->mob_id)?>"/>
				<p>
					<?php if($auth->actionAllowed('monster', 'view')): ?>
						<?php echo $this->linkToMonster($mob->mob_id, $mob->name) ?>
					<?php else: ?>
						<?php echo $mob->name?>
					<?php endif; ?>
				</p>
				<p><?php echo $mob->count?> <?php echo FLUX::message('EALabel'); ?> (<?php echo ceil($mob->time_to / 60000); ?></b> <?php echo FLUX::message('MinuteLabel'); ?><?php echo ($mob->time_from ? '-<b>'.(ceil($mob->time_to / 60000) + ceil($mob->time_from / 60000)).'</b> '.FLUX::message('MinuteLabel') : ''); ?>)</p>
			</div>
		<?php endforeach; ?>
	<?php else: ?>
		<p><?php echo FLUX::message('NoMobsOnMapDBLabel'); ?></p>
	<?php endif; ?>

	<h3><?php echo FLUX::message('NpcsOnMapDBLabel'); ?> <?php if($map->name) echo $map->name; ?></h3>
	<?php if($npcs): ?>
		<?php foreach($npcs as $npc): ?>
			<div class="npcs_list">
				<img class="npcs_hover" src="<?php echo $this->monsterImage($npc->sprite)?>"  data="npc_<?php echo $npc->x; ?>-<?php echo $npc->y; ?>"/>
				<p>
					<?php if($auth->actionAllowed('npcs', 'view')): ?>
						<a href="<?php echo $this->url('npcs', 'view', array('id' => $npc->id)); ?>"><?php echo $npc->name; ?></a>
					<?php else: ?>
						<?php echo $npc->name?>
					<?php endif; ?>
				</p>
				<p><?php echo $npc->x.','.$npc->y; ?></p>
			</div>
		<?php endforeach; ?>
	<?php else: ?>
		<p><?php echo FLUX::message('NoNpcsOnMapDBLabel'); ?></p>
	<?php endif; ?>

	<h3><?php echo FLUX::message('ShopsOnMapDBLabel'); ?> <?php if($map->name) echo $map->name; ?></h3>
	<?php if($shops): ?>
		<table class="shops_list">
		<?php foreach($shops as $shop):?>
			<tr class="npcs_hover" data="npc_<?php echo $shop->x?>-<?php echo $shop->y?>">
				<th>
					<img src="<?php echo $this->monsterImage($shop->sprite); ?>" />
					<p>
						<?php if($auth->actionAllowed('npcs', 'view')): ?>
							<a href="<?php echo $this->url('npcs', 'view', array('id' => $shop->id)); ?>"><?php echo $shop->name; ?></a>
						<?php else: ?>
							<?php echo $shop->name; ?>
						<?php endif; ?>
					</p>
					<p><?php echo $shop->x.','.$shop->y; ?></p>
				</th>
				<td><ul><?php echo $shop->items; ?></ul>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
	<?php else: ?>
		<p><?php echo FLUX::message('NoShopsOnMapDBLabel'); ?></p>
	<?php endif; ?>
<?php else: ?>
	<p><?php echo FLUX::message('MapsNotFoundDBLabel'); ?></p>
<?php endif; ?>
