<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Upgrades</h2>
<?php if ($Upgrades): ?>
	<?php echo $paginator->infoText() ?>
	<table class="horizontal-table">
		<tr>
			<th colspan="2">Name</th>
			<th>Items</th>
			<th>Info</th>
		</tr>
		<?php foreach($Upgrades as $Upgrade): ?>
		<tr>
			<td width="24" style="vertical-align: top;"><img src="<?php echo htmlspecialchars($this->iconImage($Upgrade->ItemID)) ?>?nocache=<?php echo rand() ?>" /></td>
			<td style="vertical-align: top;">
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($Upgrade->ItemID, $Upgrade->name_english) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($Upgrade->ItemID) ?>
				<?php endif ?>
			</td>
			<td>
				<?php foreach($Upgrade->TargetItems as $TargetKey => $TargetData): ?>
					<div class="items">
						<img src="<?php echo htmlspecialchars($this->iconImage($TargetKey)) ?>?nocache=<?php echo rand() ?>" style="vertical-align: middle;"/>
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<?php echo $this->linkToItem($TargetKey, $TargetData) ?>
						<?php else: ?>
							<?php echo htmlspecialchars($TargetData) ?>
						<?php endif ?>
					</div>
				<?php endforeach ?>
			</td>
			<td style="vertical-align: top;">
				Need Options: <?php echo htmlspecialchars($Upgrade->NeedOptionNumMin) ?><br>
				Need refine: <?php echo htmlspecialchars($Upgrade->NeedRefineMin) ?>~<?php echo htmlspecialchars($Upgrade->NeedRefineMax) ?><br>
				Not Socket Enchant Item: <?php echo htmlspecialchars($Upgrade->NotSocketEnchantItem) ?><br>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
	<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>No items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
