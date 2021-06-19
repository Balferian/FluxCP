<?php if (!defined('FLUX_ROOT')) exit;
if ($session->isLoggedIn()):
    $loggedInAs = $session->account->userid;
    if (Flux::config('MasterAccount')) {
        $userColumns = Flux::config('FluxTables.MasterUserTableColumns');
        $name = $userColumns->get('name');
        $loggedInAs = $session->account->$name ?: $session->account->userid;
    }
?>

<div id="loginbox">
	<span style="display: inline-block; margin: 2px 2px 2px 0">
		You are currently logged in as
		<strong>
			<a href="<?php echo $this->url(Flux::config('MasterAccount') ? 'master' : 'account', 'view') ?>" title="View account">
				<?php echo htmlspecialchars($loggedInAs)
				?>
			</a>
		</strong>
		on <?php echo htmlspecialchars($session->serverName) ?>.
		
	<?php if (count($athenaServerNames=$session->getAthenaServerNames()) > 1): ?>
		Your preferred server is:
	
	<select name="preferred_server" onchange="updatePreferredServer(this)"<?php if (count($athenaServerNames=$session->getAthenaServerNames()) === 1) echo ' disabled="disabled"'  ?>>
		<?php foreach ($athenaServerNames as $serverName): ?>
		<option value="<?php echo htmlspecialchars($serverName) ?>"<?php if ($server->serverName == $serverName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($serverName) ?></option>
		<?php endforeach ?>
	</select>.
	<?php endif ?>
	<form action="<?php echo $this->urlWithQs ?>" method="post" name="preferred_server_form" style="display: none">
		<input type="hidden" name="preferred_server" value="" />
	</form>
	</span>
	<?php if (!empty($adminMenuItems) && Flux::config('AdminMenuNewStyle')): ?>
	<?php $mItems = array(); foreach ($adminMenuItems as $menuItem) $mItems[] = sprintf('<a href="%s">%s</a>', $menuItem['url'], htmlspecialchars(Flux::message($menuItem['name']))) ?>
		<div class="loginbox-admin-menu"><strong>Admin</strong>: <?php echo implode(' • ', $mItems) ?></div>
	<?php endif ?>
</div>
<?php endif ?>
