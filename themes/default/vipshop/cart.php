<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>View Cart</h2>
<p class="cart-info-text">You have <span class="cart-item-count"><?php echo number_format(count($items)) ?></span> service(s) in your cart.</p>
<p class="cart-total-text">Your current subtotal is <span class="cart-sub-total"><?php echo number_format($total=$server->cartvip->getTotal()) ?></span> credit(s).</p>
<br />
<p class="checkout-text"><a href="<?php echo $this->url('vipshop', 'checkout') ?>">Proceed to Checkout Area</a></p>
<form action="<?php echo $this->url('vipshop', 'remove') ?>" method="post">
	<table class="vertical-table cart">
		<?php foreach ($items as $num => $item): ?>
		<tr>
			<td class="shop-item-image">
			<?php if ($image=$this->itemImage($categories[$item->shop_category][1])): ?>
				<img src="<?php echo $image ?>?nocache=<?php echo rand() ?>" />
			<?php endif ?>
			</td>
			<td>
				<h4>
					<label>
						<input type="checkbox" name="num[]" value="<?php echo $num ?>" />
						<?php echo $categories[$item->shop_category][0]; ?>
					</label>
				</h4>
				<?php if ($item->shop_item_qty > 1): ?>
				<p class="shop-item-qty">Quantity: <span class="qty"><?php echo number_format($item->shop_item_qty) ?></span></p>
				<?php endif ?>
				<p class="shop-item-cost"><span class="cost"><?php echo number_format($item->shop_item_cost) ?></span> credits</p>
				<p class="shop-item-action">
					<a href="<?php echo $this->url('vipshop', 'remove', array('num' => $num)) ?>">Remove from Cart</a> /
					<a href="<?php echo $this->url('vipshop', 'add', array('id' => $item->shop_item_id, 'cart' => true)) ?>">Add Another to Cart</a>
				</p>
				<p><?php echo nl2br(htmlspecialchars($item->shop_item_info)) ?><?php if(Flux::config('MultiserverVipTime') && $item->shop_category == 2) echo "<br><b style=\"color: blue;\">Aplly for all servers.</b>"; ?></p>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
	<p class="remove-from-cart">
		<input type="submit" value="Remove Selected Services from Cart" />
	</p>
</form>
<form action="<?php echo $this->url('vipshop', 'clear') ?>" method="post">
	<p class="remove-from-cart">
		<input type="submit" value="Empty Out Your Cart" />
	</p>
</form>
