<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Synthesis</h2>
<?php if ($Synthesis): ?>
<style>.items{}</style>	<?php echo $paginator->infoText() ?>
	<table class="horizontal-table">
		<tr>
			<th colspan="2">Name</th>
			<th>Items</th>
			<th>Info</th>
		</tr>
		<?php foreach($Synthesis as $Synthes): ?>
		<tr>
			<td width="24" style="vertical-align: top;"><img src="<?php echo htmlspecialchars($this->iconImage($Synthes->ItemID)) ?>?nocache=<?php echo rand() ?>" /></td>
			<td style="vertical-align: top;">
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($Synthes->ItemID, $Synthes->name_english) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($Synthes->ItemID) ?>
				<?php endif ?>
			</td>
			<td>
				<?php foreach($Synthes->SourceItem as $SourceKey => $SourceData): ?>
					<div class="items">
						<img src="<?php echo htmlspecialchars($this->iconImage($SourceKey)) ?>?nocache=<?php echo rand() ?>" style="vertical-align: middle;"/>
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<?php echo $this->linkToItem($SourceKey, $SourceData[0]) ?>
						<?php else: ?>
							<?php echo htmlspecialchars($SourceData[0]) ?>
						<?php endif ?>
						- <?php echo $SourceData[1] ?> ea.
					</div>
				<?php endforeach ?>
			</td>
			<td style="vertical-align: top;">
				Need count: <?php echo htmlspecialchars($Synthes->NeedCount) ?> ea.<br>
				Need refine: <?php echo htmlspecialchars($Synthes->NeedRefineMin) ?>~<?php echo htmlspecialchars($Synthes->NeedRefineMax) ?><br>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
	<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>No items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
