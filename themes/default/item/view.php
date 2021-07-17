<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Viewing Item</h2>
<?php if ($item): ?>
<?php $icon = $this->iconImage($item->item_id); ?>
<h3>
	<?php if ($icon): ?><img src="<?php echo $icon ?>" /><?php endif ?>
	#<?php echo htmlspecialchars($item->item_id) ?>: <?php echo htmlspecialchars($item->name) ?>
	<?php if ($this->getCheckMVPCard($item->item_id)): ?>
		<span class="mvp">MVP Card!</span>
	<?php endif ?>
</h3>
<table class="vertical-table">
	<tr>
		<th>Item ID</th>
		<td><?php echo htmlspecialchars($item->item_id) ?></td>
		<?php if ($image=$this->itemImage($item->item_id)): ?>
		<td rowspan="<?php echo ($server->isRenewal)?9:8 ?>" style="width: 150px; text-align: center; vertical-alignment: middle">
			<img src="<?php echo $image ?>" />
		</td>
		<?php endif ?>
		<th>For Sale</th>
		<td>
			<?php if ($item->cost): ?>
				<span class="for-sale yes">
					Yes
				</span>
			<?php else: ?>
				<span class="for-sale no">
					No
				</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Identifier</th>
		<td><?php echo htmlspecialchars($item->identifier) ?></td>
		<th>Credit Price</th>
		<td>
			<?php if ($item->cost): ?>
				<?php echo number_format((int)$item->cost) ?>
			<?php else: ?>
				<span class="not-applicable">Not For Sale</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Name</th>
		<td><?php echo htmlspecialchars($item->name) ?></td>
		<th>Type</th>
		<td><?php echo $this->itemTypeText($item->type) ?><?php if($item->subtype) echo ' - '.$this->itemSubTypeText($item->type, $item->subtype) ?></td>
	</tr>
	<tr>
		<th>NPC Buy</th>
		<td><?php echo number_format((int)$item->price_buy) ?></td>
		<th>Weight</th>
		<td><?php echo round($item->weight, 1) ?></td>
	</tr>
	<tr>
		<th>NPC Sell</th>
		<td>
			<?php if (is_null($item->price_sell) && $item->price_buy): ?>
				<?php echo number_format(floor($item->price_buy / 2)) ?>
			<?php else: ?>
				<?php echo number_format((int)$item->price_sell) ?>
			<?php endif ?>
		</td>
		<th>Weapon Level</th>
		<td><?php echo number_format((int)$item->weapon_level) ?></td>
	</tr>
	<tr>
		<th>Range</th>
		<td><?php echo number_format((int)$item->range) ?></td>
		<th>Defense</th>
		<td><?php echo number_format((int)$item->defense) ?></td>
	</tr>
	<tr>
		<th>Slots</th>
		<td><?php echo number_format((int)$item->slots) ?></td>
		<th>Refineable</th>
		<td>
			<?php if ($item->refineable): ?>
				Yes
			<?php else: ?>
				No
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Attack</th>
		<td><?php echo number_format((int)$item->attack) ?></td>
		<th>Min Equip Level</th>
		<td>
			<?php if ($item->equip_level_min == 0): ?>
				<span class="not-applicable">None</span>
			<?php else: ?>
				<?php echo number_format((int)$item->equip_level_min) ?>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<?php if($server->isRenewal): ?>
			<th>MATK</th>
			<td><?php echo number_format((int)$item->magic_attack) ?></td>
		<?php endif ?>
		<th>Max Equip Level</th>
		<td colspan="<?php echo $image ? 0 : 3 ?>">
			<?php if ($item->equip_level_max == 0): ?>
				<span class="not-applicable">None</span>
			<?php else: ?>
				<?php echo number_format((int)$item->equip_level_max) ?>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Equip Locations</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($equip_locations=$this->equipLocations($equip_locs)): ?>
				<?php echo $equip_locations ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Equip Upper</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($this->equipUpper($upper)): ?>
				<?php echo htmlspecialchars(implode(' / ', $this->equipUpper($upper))) ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Equippable Jobs</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($this->equippableJobs($jobs)): ?>
				<?php echo htmlspecialchars(implode(' / ', $this->equippableJobs($jobs))) ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Equip Gender</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($item->gender == 'Female'): ?>
				Female
			<?php elseif ($item->gender == 'Male'): ?>
				Male
			<?php elseif ($item->gender == 'Both' || $item->gender == NULL): ?>
				Both (Male and Female)
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Trade restriction</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($this->tradeRestrictions($restrictions)): ?>
				<?php echo htmlspecialchars(implode(' / ', $this->tradeRestrictions($restrictions))) ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<?php if (($isCustom && $auth->allowedToSeeItemDb2Scripts) || (!$isCustom && $auth->allowedToSeeItemDbScripts)): ?>
	<tr>
		<th>Item Use Script</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($script=$this->displayScript($item->script)): ?>
				<?php echo $script ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Equip Script</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($script=$this->displayScript($item->equip_script)): ?>
				<?php echo $script ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<tr>
		<th>Unequip Script</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if ($script=$this->displayScript($item->unequip_script)): ?>
				<?php echo $script ?>
			<?php else: ?>
				<span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
	<?php endif ?>
    <?php if(Flux::config('ShowItemDesc')):?>
	<tr>
		<th>Description</th>
		<td colspan="<?php echo $image ? 4 : 3 ?>">
			<?php if($description = Flux::config('UseDivinePrideItemDesc') ? $this->DP_Item_description($item->item_id) : $item->itemdesc): ?>
                <?php echo $description ?>
            <?php else: ?>
                <span class="not-applicable">None</span>
			<?php endif ?>
		</td>
	</tr>
    <?php endif ?>
    
</table>

<?php if($item->SourceItem): ?>
<h3><?php echo htmlspecialchars($item->name) ?> can synthes items</h3>
<table class="vertical-table">
	<tr>
		<th colspan="2">Items</th>
		<th>Amount</th>
		<th>Info</th>
	</tr>
	<?php foreach($item->SourceItem as $SourceKey => $SourceData): ?>
		<tr>
			<td width="24" style="vertical-align: top;">
				<img src="<?php echo htmlspecialchars($this->iconImage($SourceKey)) ?>?nocache=<?php echo rand() ?>" style="vertical-align: middle;"/>
			</td>
			<td>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($SourceKey, $SourceData[0]) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($SourceData[0]) ?>
				<?php endif ?>
			</td>
			<td>
				<?php echo $SourceData[1] ?> ea.
			</td>
			<?php if(!isset($synth)): ?>
				<?php $synth = true; ?>
				<td rowspan="<?php echo count($item->SourceItem) ?>">
					Need count: <?php echo htmlspecialchars($item->SNeedCount) ?> ea.<br>
					Need refine: <?php echo htmlspecialchars($item->SNeedRefineMin) ?>~<?php echo htmlspecialchars($item->SNeedRefineMax) ?>
				</td>
			<?php endif ?>
		</tr>
	<?php endforeach ?>
</table>
<?php endif ?>

<?php if($item->TargetItems): ?>
<h3><?php echo htmlspecialchars($item->name) ?> can upgrade items</h3>
<table class="vertical-table">
	<tr>
		<th colspan="2">Items</th>
		<th>Info</th>
	</tr>
	<?php foreach($item->TargetItems as $TargetKey => $TargetData): ?>
		<tr>
			<td width="24" style="vertical-align: top;">
				<img src="<?php echo htmlspecialchars($this->iconImage($TargetKey)) ?>?nocache=<?php echo rand() ?>" style="vertical-align: middle;"/>
			</td>
			<td>
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($TargetKey, $TargetData) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($TargetData) ?>
				<?php endif ?>
			</td>
			<?php if(!isset($upgrade)): ?>
				<?php $upgrade = true; ?>
				<td rowspan="<?php echo count($item->TargetItems) ?>">
					Need Options: <?php echo htmlspecialchars($item->UNeedOptionNumMin) ?><br>
					Need refine: <?php echo htmlspecialchars($item->UNeedRefineMin) ?>~<?php echo htmlspecialchars($item->UNeedRefineMax) ?><br>
					Not Socket Enchant Item: <?php echo htmlspecialchars($item->UNotSocketEnchantItem) ?>
				</td>
			<?php endif ?>
		</tr>
	<?php endforeach ?>
</table>
<?php endif ?>

<?php if ($itemDrops): ?>
<h3><?php echo htmlspecialchars($item->name) ?> Dropped By</h3>
<table class="vertical-table">
	<tr>
		<th>Monster ID</th>
		<th>Monster Name</th>
		<th><?php echo htmlspecialchars($item->name) ?> Drop Chance</th>
		<th>Can be stealed</th>
		<th>Monster Level</th>
		<th>Monster Race</th>
		<th>Monster Element</th>
	</tr>
	<?php foreach ($itemDrops as $itemDrop): ?>
	<tr class="item-drop-<?php echo $itemDrop['type'] ?>">
		<td align="right">
			<?php if ($auth->actionAllowed('monster', 'view')): ?>
				<?php echo $this->linkToMonster($itemDrop['monster_id'], $itemDrop['monster_id']) ?>
			<?php else: ?>
				<?php echo $itemDrop['monster_id'] ?>
			<?php endif ?>
		</td>
		<td>
			<?php if ($itemDrop['type'] == 'mvp'): ?>
				<span class="mvp">MVP!</span>
			<?php endif ?>
			<?php echo htmlspecialchars($itemDrop['monster_name']) ?>
		</td>
		<td><strong><?php echo $itemDrop['drop_rate'] ?>%</strong></td>
		<td><strong><?php echo htmlspecialchars(Flux::message($itemDrop['drop_steal'])) ?></strong></td>
		<td><?php echo number_format($itemDrop['monster_level']) ?></td>
		<td><?php echo Flux::monsterRaceName($itemDrop['monster_race']) ?></td>
		<td>
			Level <?php echo floor($itemDrop['monster_ele_lv']) ?>
			<em><?php echo Flux::elementName($itemDrop['monster_element']) ?></em>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php endif ?>

<?php if($itemShop !== false): ?>
	<h3><?php echo $item->name?> Buyable At</h3>
	<?php if(sizeof($itemShop)): ?>
		<table class="vertical-table">
			<tr>
				<th>Name</th>
				<th>Map</th>
				<th>Coordinates</th>
				<th>Sell Price</th>
			</tr>
			<?php foreach($itemShop as $it): ?>
				<tr>
					<?php if($auth->actionAllowed('npcs', 'view')): ?>
						<td><a href="<?php echo $this->url('npcs', 'view', array('id' => $it->npc_id)); ?>"><?php echo $it->name; ?></a></td>
					<?php else: ?>
						<td><?php echo $it->name; ?></td>
					<?php endif; ?>
					<?php if($auth->actionAllowed('map', 'view')): ?>
						<td><a href="<?php echo $this->url('map', 'view', array('map' => $it->map, 'x' => $it->x, 'y' => $it->y)); ?>"><?php echo $it->map; ?></a></td>
					<?php else: ?>
						<td><?php echo $it->map; ?></td>
					<?php endif; ?>
					<td><?php echo $it->x; ?>,<?php echo $it->y; ?></td>
					<td><?php echo $it->price == -1 ? $it->price_buy : $it->price; ?> <?php echo FLUX::message('ServerInfoZenyLabel'); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else: ?>
		This item can not be bought
	<?php endif; ?>
<?php endif; ?>

<?php else: ?>
<p>No such item was found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif; ?>
