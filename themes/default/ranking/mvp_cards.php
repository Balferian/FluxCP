<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>MVP Cards Ranking</h2>
<h3>
	Total amount MVP Cards on <?php echo htmlspecialchars($server->serverName) ?>
</h3>
<?php if ($cards): ?>
<table class="horizontal-table">
	<tr>
		<th>MVP Card</th>
		<th>Amount</th>
	</tr>
	<?php foreach($cards_count as $card): ?>
		<?php if($card[1] > 0): ?>
			<tr align="center">
				<td>
					<?php $illustration = $this->cardImage($card[0]); ?>
					<?php if ($illustration): ?><img src="<?php echo $illustration ?>" style="height: 250px;"/><br><?php endif ?>
					<?php if ($auth->actionAllowed('item', 'view')): ?>
						<?php echo $this->linkToItem($card[0], $mvp_cards[$card[0]]) ?>
					<?php else: ?>
						<?php echo $card[2]; ?>
					<?php endif ?>
				</td>
				<td>
					<?php echo $card[1]; ?> <?php echo FLUX::message('EALabel'); ?>
				</td>
			</tr>
		<?php endif ?>
	<?php endforeach; ?>
</table>
<?php else: ?>
	<p>There are no MVP cards. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
