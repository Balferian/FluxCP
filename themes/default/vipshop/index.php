<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Purchase</h2>
<p>Services in this shop are purchased using <span class="keyword">donation credits</span> and not real money.  Donation Credits are rewarded to players who <a href="<?php echo $this->url('donate') ?>">make a donation to our server</a>, helping us cover the costs of maintaining and running the server.</p>
<h2><span class="shop-server-name"><?php echo htmlspecialchars($server->serverName) ?></span> Service Shop</h2>
<p class="action">
	<a href="<?php echo $this->url('vipshop', 'index') ?>"<?php if (is_null($category)) echo ' class="current-shop-category"' ?>>
		<?php echo htmlspecialchars(Flux::message('AllLabel')) ?> (<?php echo number_format($total) ?>)
	</a>
<?php foreach ($categories as $catID => $catName): ?>
	/
	<a href="<?php echo $this->url('vipshop', 'index', array('category' => $catID)) ?>"<?php if (!is_null($category) && $category === (string)$catID) echo ' class="current-shop-category"' ?>>
		<?php echo htmlspecialchars($catName[0]) ?> (<?php echo number_format($categoryCount[$catID]) ?>)
	</a>
<?php endforeach ?>
</p>
<?php if ($categoryName): ?>
<h3>Category: <?php echo htmlspecialchars($categoryName) ?></h3>
<?php endif ?>
<?php if ($items): ?>
<?php if ($session->isLoggedIn()): ?>
	<?php if ($cartItems=$server->cartvip->getCartItemNames()): ?><p class="cart-items-text">Services in your cart: <span class="cart-item-name"><?php echo implode('</span>, <span class="cart-item-name">', array_map('htmlspecialchars', $cartItems)) ?></span>.</p><?php endif ?>
	<p class="cart-info-text">You have <span class="cart-item-count"><?php echo number_format(count($cartItems)) ?></span> Service(s) in your cart.</p>
	<p class="cart-total-text">Your current subtotal is <span class="cart-sub-total"><?php echo number_format($server->cartvip->getTotal()) ?></span> credit(s).</p>
<?php endif ?>
<?php echo $paginator->infoText() ?>
<table class="shop-table">
	<?php foreach ($items as $item): ?>
	<tr>
		<td class="shop-item-image">
			<?php if ($image=$this->itemImage($categories[$item->shop_category][1])): ?>
				<img src="<?php echo $image ?>?nocache=<?php echo rand() ?>" />
			<?php endif ?>
		</td>
		<td>
			<h4 class="shop-item-name">
				<?php	if ($item->shop_category == 0) $ea = "ea.";
						if ($item->shop_category == 1) $ea = "ea.";
						if ($item->shop_category == 2) $ea = "days"; ?>
				<?php echo $categories[$item->shop_category][0]; ?>
				<span class="qty">(<?php echo number_format($item->shop_item_qty)." ".$ea ?>)</span>
			</h4>
			<p class="shop-item-info"><?php echo nl2br($item->shop_item_info) ?><?php if(Flux::config('MultiserverVipTime') && $item->shop_category == 2) echo "<br><b style=\"color: blue;\">Aplly for all servers.</b>"; ?></p>
			<p class="shop-item-action">
				<?php if ($auth->allowedToEditShopItem): ?>
				<a href="<?php echo $this->url('vipshop', 'edit', array('id' => $item->shop_item_id)) ?>">Modify</a>
				<?php endif ?>
				<?php if ($auth->allowedToDeleteShopItem): ?>
				/ <a href="<?php echo $this->url('vipshop', 'delete', array('id' => $item->shop_item_id)) ?>"
					onclick="return confirm('Are you sure you want to remove this service from the service shop?')">Delete</a>
				<?php endif ?>
			</p>
		</td>
		<td class="shop-item-cost-qty">
			<p><span class="cost"><?php echo number_format($item->shop_item_cost) ?></span> credits.</p>
			<p class="shop-item-action">
				<?php if ($auth->actionAllowed('vipshop', 'add')): ?>
				<a href="<?php echo $this->url('vipshop', 'add', array('id' => $item->shop_item_id)) ?>"><strong>Add to Cart</strong></a>
				<?php endif ?>
			</p>
		</td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>There are currently no services for sale.</p>
<?php endif ?>
