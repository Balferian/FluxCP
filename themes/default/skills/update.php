<h3>PHP Configuration</h3>
<p>These values must be larger than the size of your skills file.</p>
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

<h3>Upload Files</h3>
<form class="forms" method="post" enctype="multipart/form-data">
	skillid.lua<br>
    <input type="file" name="skillid_lua"><br>
	skilldescript.lua<br>
    <input type="file" name="skilldescript_lua"><br>
	skillinfolist.lua<br>
    <input type="file" name="skillinfolist_lua"><br>
    <input class="btn" type="submit">
</form>

<h3>Current Count</h3>
<p>There are currently <?php echo number_format($return->count) ?> skills in the database</p>
