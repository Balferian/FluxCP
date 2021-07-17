<h3>PHP Configuration</h3>
<p>These values must be larger than the size of your lapineupgradebox file.</p>
<table class="vertical-table">
	<tr>
		<th>PHP Configs</th><td>Value</td>
	</tr>
	<tr>
		<th>post_max_size</th><td><?php echo ini_get('post_max_size') ?></td>
	</tr>
	<tr>
		<th>upload_max_filesize</th><td><?php echo ini_get('upload_max_filesize') ?></td>
	</tr>
</table>

<h3>Upload lapineupgradebox.lua</h3>
<form class="forms" method="post" enctype="multipart/form-data">
    <input type="file" name="lua_file"><br>
    <input class="btn" type="submit">
</form>

<h3>Current Count</h3>
<p>There are currently <?php echo number_format(count($return)) ?> upgrades in the database</p>
