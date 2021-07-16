<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Buyers Items</h2>
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
			<th><?php echo $paginator->sortableColumn('buyingstore_items.item_id', Flux::message('ItemIdLabel')) ?></th>
			<th colspan="2"><?php echo htmlspecialchars(Flux::message('ItemNameLabel')) ?></th>
			<th><?php echo $paginator->sortableColumn('buyingstore_items.amount', Flux::message('ItemAmountLabel')) ?></th>
			<th><?php echo $paginator->sortableColumn('buyingstore_items.price', 'Price') ?></th>
			</th>
		</tr>
		<?php foreach ($items AS $item): ?>
		<?php $icon = $this->iconImage($item->item_id) ?>
		<tr>
			<td style="font-weight:bold;">
				<?php if ($auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter): ?>
					<?php echo $this->linkToCharacter($item->char_id, $item->name); ?>
				<?php else: ?>
					<?php echo $item->name; ?>
				<?php endif ?>
			</td>
			<td width="50" align="right"  style="">
				<?php if ($auth->actionAllowed('buyingstore', 'viewshop')): ?>
					<a href="<?php echo $this->url('buyingstore', 'viewshop', array("id" => $item->buyingstore_id)); ?>"><?php echo $item->title; ?></a>
				<?php else: ?>
					<?php echo $item->id ?>
				<?php endif ?>
			</td>
			<td><?php echo htmlspecialchars($item->map) ?></td>
			<td align="right">
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($item->item_id, $item->item_id) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($item->item_id) ?>
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
			<td><span style="color:goldenrod; text-shadow:1px 1px 0px brown;"><?php echo number_format($item->price, 0, ',', ' ').' '.FLUX::message('ServerInfoZenyLabel'); ?></span></td>
		</tr>
		<?php endforeach ?>
	</table>
    <?php echo $paginator->getHTML() ?>
<?php else: ?>
    <p>No Items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
