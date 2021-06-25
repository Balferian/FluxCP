<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo Flux::message('HelloWorld') ?></h2>
<p><?php echo Flux::message('HelloInfoText') ?></p>
<p><?php printf(Flux::message('HelloVersionText'), $fluxVersion) ?></p>

<?php
if(isset($_FILES["screenshots"])) {
	foreach ($_FILES["screenshots"]["error"] as $key => $error) {
		if ($error == UPLOAD_ERR_OK && $key < Flux::config('SDMaxUplodScreenshots') ) {
			$tmp_name = $_FILES["screenshots"]["tmp_name"][$key];
			$name = basename($_FILES["screenshots"]["name"][$key]);
			$this->make_upload($tmp_name, $max_id, $name);
		}
	}	
}

?>
<form action="<?php echo $this->urlWithQs ?>" enctype="multipart/form-data" method="post">

<input type="text" name="image_id" id="image_id" style="display:none;" value="<?php echo mt_rand(0, 1000000000); ?>"/>
<b>No more than 5 screenshots</b>
<div id='upload_screenshots'>
    <p>Скриншот: <input type="file" name="screenshots[]" class="upload_screenshot" style="padding: 0;"></p>
</div>
<button type="button" class="btn btn-info btn-sm btn_clone">Add Screenshot</button>
<input type="submit" class="btn btn-primary btn-sm" value="Create Ticket" />
</form>


<script type="text/javascript">
	let i = 0;
	$('.btn_clone').click(function(){
		i++;
		if (i < 5) {
			var $upload_screenshots = $('#upload_screenshots');
			var $clone = $upload_screenshots.find('p:last').clone().appendTo('#upload_screenshots');
			$clone.find('.upload_screenshot').attr("id",'').removeClass('hasupload_screenshot').upload_screenshot();
		}
	})
</script>
