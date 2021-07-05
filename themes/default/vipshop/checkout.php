<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Checkout</h2>
<p>The checkout process is fairly simple, and when you're done you'll be ready to redeem your services in-game through our <span class="keyword">Redemption NPC</span>.</p>

<h3>Purchase Information</h3>
<p class="cart-total-text">Your current subtotal is <span class="cart-sub-total"><?php echo number_format($total=$server->cartvip->getTotal()) ?></span> credit(s).</p>
<p class="checkout-info-text">Your remaining balance after this purchase will be <span class="remaining-balance"><?php echo number_format($session->account->balance - $total) ?></span> credit(s).</p>
<p>After reviewing the below service information, you can proceed with your checkout by clicking the “Purchase Services” button.</p>
<p class="important">Note: These services are for redemption on the <span class="server-name"><?php echo htmlspecialchars($server->serverName) ?></span> server ONLY.</p>
<p>
	<form action="<?php echo $this->url ?>" method="post">
		<?php if(Flux::config('MasterAccount')): ?>
			<?php echo Flux::message('PurchaseSelectAccountLabel') ?> <select name="select_account_id"><?php echo $accountList ?></select>
		<?php endif; ?>
		<?php echo $this->moduleActionFormInputs($params->get('module'), 'checkout') ?>
		<input type="hidden" name="process" value="1" />
		<button type="submit" onclick="return confirm('Are you sure you want to continue purchasing the below service(s)?')">
			<strong>Purchase Services</strong>
		</button>
	</form>
</p>

<h3>Services Currently in Your Cart:</h3>
<p class="cart-info-text">You have <span class="cart-item-count"><?php echo number_format(count($items)) ?></span> service(s) in your cart.</p>
<table class="vertical-table cart">
	<?php foreach ($items as $item): ?>
	<tr>
		<td class="shop-item-image">
			<?php if ($image=$this->itemImage($categories[$item->shop_category][1])): ?>
				<img src="<?php echo $image ?>?nocache=<?php echo rand() ?>" />
			<?php endif ?>
		</td>
		<td>
			<h4>
				<?php echo $categories[$item->shop_category][0]; ?>
			</h4>
			<?php if ($item->shop_item_qty > 1): ?>
			<p class="shop-item-qty">Quantity: <span class="qty"><?php echo number_format($item->shop_item_qty) ?></span></p>
			<?php endif ?>
			<p class="shop-item-cost"><span class="cost"><?php echo number_format($item->shop_item_cost) ?></span> credits</p>
			<p><?php echo nl2br(htmlspecialchars($item->shop_item_info)) ?></p>
		</td>
	</tr>
	<?php endforeach ?>
</table>
