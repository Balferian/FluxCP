<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Pending Redemption</h2>
<?php if ($items): ?>
<p>You have <?php echo number_format($total) ?> service(s) pending redemption.</p>
<table class="vertical-table">
	<tr>
		<th>Account</th>
		<th>Service Name</th>
		<th>Quantity</th>
		<th>Cost</th>
		<th>Balance (Before)</th>
		<th>Balance (After)</th>
		<th>Purchase Date</th>
		<th>Status</th>
	</tr>
	<?php foreach ($items as $item): ?>
	<tr>
		<td>
			<a href="<?php echo $this->url('account', 'view', array('id' => $item->account_id)); ?>">
				<?php echo htmlspecialchars($item->userid) ?>
			</a>
		</td>
		<td>
			<img src="<?php echo htmlspecialchars($this->iconImage($categories[$item->category][1])); ?>?nocache=<?php echo rand(); ?>" style="vertical-align: middle;" />
			<?php echo $categories[$item->category][0]; ?>
		</td>
		<td><?php echo number_format($item->quantity) ?></td>
		<td><?php echo number_format($item->cost) ?></td>
		<td><?php echo number_format($item->credits_before) ?></td>
		<td><?php echo number_format($item->credits_after) ?></td>
		<td><?php echo $this->formatDateTime($item->purchase_date) ?></td>
		<td><?php echo $item->redeemed ? "Finished" : "Waiting" ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php else: ?>
<p>You currently have no services pending redemption.
	If you would like to make a purchase, please go to the <a href="<?php echo $this->url('vipshop') ?>">shop</a>.</p>
<?php endif ?>
