<?php
if (!defined('FLUX_ROOT')) exit;
?>
<h2>Service Shop</h2>
<h3>Add Service to the Shop</h3>

<form action="<?php echo $this->urlWithQs ?>" method="post">
<table class="vertical-table">
	<tr>
		<th><label for="category">Category</label></th>
		<td>
			<select name="category" id="category">
				<?php foreach ($categories as $categoryID => $cat): ?>
					<option value="<?php echo (int)$categoryID ?>"<?php if ($category === (string)$categoryID) echo ' selected="selected"' ?>><?php echo htmlspecialchars($cat[0]) ?></option>
				<?php endforeach ?>
			</select>
		</td>
	</tr>
	<tr>
		<th><label for="qty">Quantity</label></th>
		<td><input type="text" class="short" name="qty" id="qty" value="<?php echo htmlspecialchars($params->get('qty')) ?>" /> (ea. / days)</td>
	</tr>
	<tr>
		<th><label for="cost">Credits</label></th>
		<td><input type="text" class="short" name="cost" id="cost" value="<?php echo htmlspecialchars($params->get('cost')) ?>" /></td>
	</tr>
	<tr>
		<th><label for="info">Info</label></th>
		<td>
			<textarea name="info" id="info"><?php echo htmlspecialchars($params->get('info')) ?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right">
			<input type="submit" value="Add" />
		</td>
	</tr>
</table>
</form>
