<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Vending Items</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()"><?php echo FLUX::message('SearchBtnDBLabel'); ?>...</a></p>
<form class="search-form" method="get">
	<?php echo $this->moduleActionFormInputs($params->get('module')) ?>
	<p>
		<label for="item_name">Item Name:</label>
		<input type="text" name="item_name" id="item_name" value="<?php echo htmlspecialchars($params->get('item_name')) ?>" />
		...
		<label for="item_id">Item ID:</label>
		<input type="number" name="item_id" id="item_id" value="<?php echo htmlspecialchars($params->get('item_id')) ?>" />
		...
		<label for="card_id">Cards ID:</label>
		<input type="number" name="card_id" id="card_id" value="<?php echo htmlspecialchars($params->get('card_id')) ?>" />
		...
		<?php if($server->isRenewal): ?>
			<label for="enhants">Enchants:</label>
			<select name="enhants" id="enhants">
				<option value="">Any</option>
				<?php foreach($rndoptions_list as $key => $rndopt): ?>
					<option value="<?php echo $key;?>"<?php if($params->get('enhants') == $key) echo ' selected="selected"'; ?>><?php echo sprintf($rndopt, '?'); ?></option>
				<?php endforeach; ?>
			</select>
			...
		<?php endif; ?>
		<input type="submit" value="<?php echo FLUX::message('SearchBtnDBLabel'); ?>" />
		<input type="button" value="<?php echo FLUX::message('ResetBtnDBLabel'); ?>" onclick="reload()" />
	</p>
</form>
<?php if ($items): ?>
    <?php echo $paginator->infoText() ?>
	<table class="vertical-table">
		<tr>
			<th>Character</th>
			<th>Shop title</th>
			<th>Map</th>
			<th><?php echo $paginator->sortableColumn('cart_inventory.nameid', Flux::message('ItemIdLabel')) ?></th>
			<th colspan="2"><?php echo htmlspecialchars(Flux::message('ItemNameLabel')) ?></th>
			<th><?php echo $paginator->sortableColumn('vending_items.amount', Flux::message('ItemAmountLabel')) ?></th>
			<th><?php echo htmlspecialchars(Flux::message('ItemCard0Label')) ?></th>
			<th><?php echo htmlspecialchars(Flux::message('ItemCard1Label')) ?></th>
			<th><?php echo htmlspecialchars(Flux::message('ItemCard2Label')) ?></th>
			<th><?php echo htmlspecialchars(Flux::message('ItemCard3Label')) ?></th>
			<?php if($server->isRenewal): ?>
				<th><?php echo htmlspecialchars(Flux::message('ItemRandOptionsLabel')) ?></th>
			<?php endif ?>
			<th><?php echo $paginator->sortableColumn('vending_items.price', 'Price') ?></th>
			</th>
		</tr>
		<?php foreach ($items AS $item): ?>
		<?php $icon = $this->iconImage($item->nameid) ?>
		<tr>
			<td style="font-weight:bold;">
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($item->char_id, $item->name); ?>
				<?php else: ?>
					<?php echo $item->name; ?>
				<?php endif ?>
			</td>
			<td width="50" align="right"  style="">
				<?php if ($auth->actionAllowed('vending', 'viewshop')): ?>
					<a href="<?php echo $this->url('vending', 'viewshop', array("id" => $item->vending_id)); ?>"><?php echo $item->title; ?></a>
				<?php else: ?>
					<?php echo $item->id ?>
				<?php endif ?>
			</td>
			<td><?php echo htmlspecialchars($item->map) ?></td>
			<td align="right">
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($item->nameid, $item->nameid) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($item->nameid) ?>
				<?php endif ?>
			</td>
			<?php if ($icon): ?>
			<td>
				<img src="<?php echo htmlspecialchars($icon) ?>" />
				<?php if($item->enchantgrade): ?>
					<div class="enchantgrade grade_<?php echo $item->enchantgrade; ?>"></div>
				<?php endif ?>
			</td>
			<?php endif ?>
			<td<?php if (!$icon) echo ' colspan="2"' ?><?php if ($item->cardsOver) echo ' class="overslotted' . $item->cardsOver . '"'; else echo ' class="normalslotted"' ?>>
				<?php if ($item->refine > 0): ?>
					+<?php echo htmlspecialchars($item->refine) ?>
				<?php endif ?>
				<?php if ($item->card0 == 255 && intval($item->card1/1280) > 0): ?>
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
				<?php if ($item->card0 == 255 && array_key_exists($item->card1%1280, $itemAttributes)): ?>
					<?php echo htmlspecialchars($itemAttributes[$item->card1%1280]) ?>
				<?php endif ?>
				<?php if ($item->name_english): ?>
					<span class="item_name"><?php echo htmlspecialchars($item->name_english) ?></span>
				<?php else: ?>
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
				<?php endif ?>
				<?php if ($item->slots): ?>
					<?php echo htmlspecialchars(' [' . $item->slots . ']') ?>
				<?php endif ?>
			</td>
			<td><?php echo number_format($item->amount) ?></td>
			<td>
				<?php if($item->card0 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
					<?php if (!empty($cards[$item->card0])): ?>
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<?php echo $this->linkToItem($item->card0, $cards[$item->card0]) ?>
						<?php else: ?>
							<?php echo htmlspecialchars($cards[$item->card0]) ?>
						<?php endif ?>
					<?php else: ?>
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<?php echo $this->linkToItem($item->card0, $item->card0) ?>
						<?php else: ?>
							<?php echo htmlspecialchars($item->card0) ?>
						<?php endif ?>
					<?php endif ?>
				<?php else: ?>
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
				<?php endif ?>
			</td>
			<td>
				<?php if($item->card1 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 255 && $item->card0 != -256): ?>
					<?php if (!empty($cards[$item->card1])): ?>
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<?php echo $this->linkToItem($item->card1, $cards[$item->card1]) ?>
						<?php else: ?>
							<?php echo htmlspecialchars($cards[$item->card1]) ?>
						<?php endif ?>
					<?php else: ?>
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<?php echo $this->linkToItem($item->card1, $item->card1) ?>
						<?php else: ?>
							<?php echo htmlspecialchars($item->card1) ?>
						<?php endif ?>
					<?php endif ?>
				<?php else: ?>
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
				<?php endif ?>
			</td>
			<td>
				<?php if($item->card2 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
					<?php if (!empty($cards[$item->card2])): ?>
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<?php echo $this->linkToItem($item->card2, $cards[$item->card2]) ?>
						<?php else: ?>
							<?php echo htmlspecialchars($cards[$item->card2]) ?>
						<?php endif ?>
					<?php else: ?>
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<?php echo $this->linkToItem($item->card2, $item->card2) ?>
						<?php else: ?>
							<?php echo htmlspecialchars($item->card2) ?>
						<?php endif ?>
					<?php endif ?>
				<?php else: ?>
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
				<?php endif ?>
			</td>
			<td>
				<?php if($item->card3 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
					<?php if (!empty($cards[$item->card3])): ?>
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<?php echo $this->linkToItem($item->card3, $cards[$item->card3]) ?>
						<?php else: ?>
							<?php echo htmlspecialchars($cards[$item->card3]) ?>
						<?php endif ?>
					<?php else: ?>
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<?php echo $this->linkToItem($item->card3, $item->card3) ?>
						<?php else: ?>
							<?php echo htmlspecialchars($item->card3) ?>
						<?php endif ?>
					<?php endif ?>
				<?php else: ?>
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
				<?php endif ?>
			</td>
			<?php if($server->isRenewal): ?>
				<td>
					<?php if($item->rndopt): ?>
						<ul>
							<?php foreach($item->rndopt as $rndopt) echo "<li>".$this->itemRandOption($rndopt[0], $rndopt[1])."</li>"; ?>
						</ul>
					<?php else: ?>
						<span class="not-applicable">None</span>
					<?php endif ?>
				</td>
			<?php endif ?>
			<td><span style="color:goldenrod; text-shadow:1px 1px 0px brown;"><?php echo number_format($item->price, 0, ',', ' ').' '.FLUX::message('ServerInfoZenyLabel'); ?></span></td>
		</tr>
		<?php endforeach ?>
	</table>
    <?php echo $paginator->getHTML() ?>
<?php else: ?>
    <p>No Items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
