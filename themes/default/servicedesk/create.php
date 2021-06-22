<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
?>
<h2><?php echo htmlspecialchars(Flux::message('SDCreateNew')) ?></h2>
	<h3>Required Information</h3>
	<form action="<?php echo $this->urlWithQs ?>" method="post" class="input_fill" enctype="multipart/form-data">
	<table class="vertical-table" width="100%">
		<tr>
			<?php if (Flux::config('MasterAccount')): ?>
					<th>Account</th>
					<td><select name="select_account_id" onchange="this.form.submit()"><?php echo $accountList ?></select></td>
           <?php else: ?>
                <th>Account ID</th>
			    <td><input type="text" name="account_id" id="account_id" value="<?php echo $session->account->account_id ?>" readonly="readonly" /></td>
            <?php endif; ?>
            <form action="<?php echo $this->urlWithQs ?>" method="post">
                <input type="hidden" name="account_id" id="account_id" value="<?php echo $accountId ?>" />
		</tr>
		<tr>
			<th>Character</th>
			<td><select name="char_id"><?php echo $charselect ?></select></td>
		</tr>
		<tr>
			<th>Subject</th>
			<td><input type="text" name="subject" id="subject" size="50" /><br />Type a very brief description about the ticket.</td>
		</tr>
		<tr>
			<th>Category</th>
			<td><select name="category" id="category" onchange="showInfo()">
				<?php if(!$catlist): ?>
					<option value="-1"><?php echo Flux::message('SDNoCatsAvailable') ?></option>
				<?php else: ?>
				<?php foreach($catlist as $cat):?>
					<option value="<?php echo $cat->cat_id ?>"><?php echo $cat->name ?></option>
				<?php endforeach ?>
				<?php endif ?>
				</select></td>
		</tr>
		<tr>
			<th>Tell us what happened</th>
			<td>
				<textarea name="text"></textarea>
			</td>
		</tr>
	</table>
	
	<h3>Optional Additional Information</h3>
	<table class="vertical-table" width="100%">
		<tbody id="chatrow">
		<tr>
			<th>Chatlog</th>
			<td><input type="text" name="chatlink" id="chatlink" size="50" /><br /><?php echo Flux::message('SDPointerChatLog') ?></td>
		</tr>
		</tbody>
		
		<tbody id="ssrow">
		<tr>
			<th>Screenshot Proof</th>
			<td>
				<?php if(Flux::config('SDAllowUplodScreenshots')): ?>
					<div id='upload_screenshots'>
						<p><input type="file" name="screenshots[]" class="upload_screenshot" style="padding: 0;" accept=".jpg,.png,.gif,.bmp,.jpeg"></p>
					</div>
					<?php if(Flux::config('SDMaxUplodScreenshots') > 1): ?>
						<button type="button" class="btn btn-info btn-sm btn_clone" id="add_screenshot">Add new one</button>
					<?php endif ?>
				<?php else: ?>
					<input type="text" name="sslink" id="sslink" size="50" /><br /><?php echo Flux::message('SDPointerScreenShot') ?>
				<?php endif ?>
			</td>
		</tr>
		</tbody>
		
		<tbody id="videorow">
		<tr>
			<th>Video Capture</th>
			<td><input type="text" name="videolink" id="chatlink" size="50" /><br /><?php echo Flux::message('SDPointerVideoLink') ?></td>
		</tr>
		</tbody>
		
		<?php if (Flux::config('ReCaptchaServiceDesk')): ?>
			<tr>
				<td colspan="2"><div class="g-recaptcha" data-theme = "<?php echo Flux::config('ReCaptchaTheme'); ?>" data-sitekey="<?php echo Flux::config('ReCaptchaPublicKey'); ?>"></div></td>
			</tr>
		<?php endif ?>

		<tr>
			<td colspan="2"><input type="hidden" name="ip" value="<?php echo $_SERVER['REMOTE_ADDR'] ?>" />
					<button value="CreateTicket" name="Submit" onclick="this.form.submit()">Create Ticket</button>
					<input type="submit" style="display:none;"/>
			</td>
		</tr>
    </table>
</form>

<?php if(Flux::config('SDAllowUplodScreenshots')): ?>
	<script type="text/javascript">
		let i = 1;
		$('.btn_clone').click(function(){
			i++;
			if(i > <?php echo Flux::config('SDMaxUplodScreenshots')-1; ?>) {
				document.getElementById("add_screenshot").remove();
			}
			if (i <= <?php echo Flux::config('SDMaxUplodScreenshots'); ?>) {
				var $upload_screenshots = $('#upload_screenshots');
				var $clone = $upload_screenshots.find('p:last').clone().appendTo('#upload_screenshots');
				$clone.find('.upload_screenshot').attr("id",'').removeClass('hasupload_screenshot').upload_screenshot();
			}
		})
	</script>
<?php endif ?>
