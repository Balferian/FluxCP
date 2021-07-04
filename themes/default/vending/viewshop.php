<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars($title); ?></h2>
<?php if ($vending): ?>
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
					
				<?php if($vending->x && $vending->y): ?>
					<div class="you_here" style="
					left:<?php echo conv((int)$vending->x, $map->x, $map) - 5; ?>px;
					bottom:<?php echo conv((int)$vending->y, $map->y, $map) - 5; ?>px;
					"></div>
				<?php endif; ?>
			</div>
		</td>
		<td style="padding: 0 10px;">
			<h3><?php echo sprintf(Flux::message('VendingTitle'), $vending->title); ?></h3>
			<img src="<?php echo $this->monsterImage(858); ?>" />
			<hr>
			<b><?php echo FLUX::message('SearchNameDBLabel'); ?>:</b>
			<?php echo $vending->char_name; ?>
			<br>
			<b><?php echo FLUX::message('SearchMapDBLabel'); ?>:</b>
			<?php if($auth->actionAllowed('map', 'view')): ?>
				<a href="<?php echo $this->url('map', 'view', array('map' => $vending->map)); ?>"><?php echo $vending->map; ?></a>
			<?php else: ?>
				<?php echo $vending->map; ?>
			<?php endif; ?>
			<br>
			<b><?php echo FLUX::message('CoordinatesDBLabel'); ?>:</b>
			<?php echo $vending->x; ?>,<?php echo $vending->y; ?>
		</td>
	</tr>
	<tr>
		<?php if($vending_items): ?>
			<td colspan="2">
				<h3><?php echo FLUX::message('VendingSaleTitle'); ?></h3>
				<table class="vending_list">
					<tr>
						<th colspan="2">Item</th>
						<th>Amount</th>
						<th>Cards</th>
						<th>Random options</th>
					</tr>
					<?php foreach($vending_items as $item): ?>
						<?php $icon = $this->iconImage($item->nameid) ?>
						<tr>
							<?php if ($icon): ?>
								<td style="padding-right: 10px;">
									<img src="<?php echo htmlspecialchars($icon); ?>?nocache=<?php echo rand(); ?>" />
									<?php if($item->enchantgrade): ?>
											<div class="enchantgrade grade_<?php echo $item->enchantgrade; ?>"></div>
									<?php endif ?>
								</td>
							<?php endif ?>
							<td<?php if (!$icon) echo ' colspan="2"'; ?>>
								<?php if ($item->refine > 0): ?>
									+<?php echo htmlspecialchars($item->refine) ?>
								<?php endif ?>
								<?php echo $auth->actionAllowed('item', 'view') ? $this->linkToItem($item->nameid, $item->item_name) : htmlspecialchars($item->item_name); ?>
								<?php if ($item->char_name): ?>
									Of <?php echo $item->char_name ?>
								<?php endif; ?>

								<?php if ($item->card0 == 255 && intval($item->card1 / 1280) > 0): ?>
									<?php $itemcard1 = intval($item->card1/1280); ?>
									<?php for ($i = 0; $i < $itemcard1; $i++): ?>
										Very
									<?php endfor ?>
									Strong
								<?php endif ?>

								<?php if ($item->card0 == 254 || $item->card0 == 255): ?>
									<?php if ($item->char_name): ?>
										<?php if ($auth->actionAllowed('character', 'view') && ($isMine || (!$isMine && $auth->allowedToViewCharacter))): ?>
											<?php echo $this->linkToCharacter($item->char_id, $item->char_name, $session->serverName) . "'s" ?>
										<?php else: ?>
											<?php echo htmlspecialchars($item->char_name . "'s") ?>
										<?php endif ?>
									<?php else: ?>
										<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>'s
									<?php endif ?>
								<?php endif ?>

								<?php if ($item->card0 == 255 && array_key_exists($item->card1 % 1280, $itemAttributes)): ?>
									<?php echo htmlspecialchars($itemAttributes[$item->card1 % 1280]) ?>
								<?php endif ?>
								
								<?php if ($item->slots): ?>
									<?php echo htmlspecialchars(' [' . $item->slots . ']') ?>
								<?php endif ?>
								<br>
								<span style="color:goldenrod; text-shadow:1px 1px 0px brown;"><?php echo number_format($item->price, 0, ',', ' ').' '.FLUX::message('ServerInfoZenyLabel'); ?></span>
							</td>
							<td style="padding: 0 10px;">
								<?php echo $item->amount; ?> <?php echo FLUX::message('EALabel'); ?>
							</td>
							<td style="padding: 0 10px;">
									<?php for($i = 0; $i < 4; $i++): ?>
										<?php $card = "card".$i; ?>
										<?php $card_icon = $this->iconImage($item->$card); ?>
										<?php if ($item->$card && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->$card != 254 && $item->$card != 255 && $item->$card != -256): ?>
											<div>
												<?php if ($icon): ?>
													<img src="<?php echo htmlspecialchars($card_icon); ?>?nocache=<?php echo rand(); ?>" style="vertical-align: middle;"/>
												<?php endif; ?>
												<?php if (!empty($cards[$item->$card])): ?>
													<?php echo $this->linkToItem($item->$card, $cards[$item->$card]) ?>
												<?php else: ?>
													<?php echo $this->linkToItem($item->$card, $item->$card) ?>
												<?php endif ?>
											</div>
										<?php endif ?>
									<?php endfor; ?>
							</td>
							<?php if($server->isRenewal): ?>
								<td>
									<?php if($item->rndopt): ?>
										<ul>
											<?php foreach($item->rndopt as $rndopt) echo "<li>".$this->itemRandOption($rndopt[0], $rndopt[1])."</li>"; ?>
										</ul>
									<?php endif ?>
								</td>
							<?php endif ?>
						</tr>
					<?php endforeach; ?>
				</table>
			</td>
		<?php else: ?>
			<td>No Items found. <a href="javascript:history.go(-1)">Go back</a>.</td>
		<?php endif ?>
	</tr>
</table>
<?php else: ?>
    <p>No Vendor found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
