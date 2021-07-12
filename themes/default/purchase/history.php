<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Pending Redemption History</h2>
<?php if ($history): ?>
    <?php echo $paginator->infoText() ?>
	<table class="vertical-table">
		<tr>
			<th>Account</th>
			<th>Item Name</th>
			<th>Quantity</th>
			<th>Cost</th>
			<th>Balance (Before)</th>
			<th>Balance (After)</th>
			<th>Purchase Date</th>
			<th>Redemption Date</th>
			<th>Status</th>
		</tr>
		<?php foreach ($history as $item): ?>
		<tr>
			<td>
				<a href="<?php echo $this->url('account', 'view', array('id' => $item->account_id)); ?>">
					<?php echo htmlspecialchars($item->userid) ?>
				</a>
			</td>
			<td>
				<img src="<?php echo htmlspecialchars($this->iconImage($item->nameid)); ?>?nocache=<?php echo rand(); ?>" style="vertical-align: middle;" />
				<?php if ($auth->actionAllowed('item', 'view')): ?>
					<?php echo $this->linkToItem($item->nameid, $item->name_english) ?>
				<?php else: ?>
					<?php echo htmlspecialchars($item->name_english) ?>
				<?php endif ?>
			</td>
			<td><?php echo number_format($item->quantity) ?></td>
			<td><?php echo number_format($item->cost) ?></td>
			<td><?php echo number_format($item->credits_before) ?></td>
			<td><?php echo number_format($item->credits_after) ?></td>
			<td><?php echo $this->formatDateTime($item->purchase_date) ?></td>
			<td><?php echo $item->redemption_date ? $this->formatDateTime($item->redemption_date) : "-" ?></td>
			<td><?php echo $item->redeemed ? "Finished" : "Waiting" ?></td>
		</tr>
		<?php endforeach ?>
	</table>
    <?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>You currently have no services pending redemption history.
	If you would like to make a purchase, please go to the <a href="<?php echo $this->url('vipshop') ?>">shop</a>.</p>
<?php endif ?>
