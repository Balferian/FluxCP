<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2>Master accounts</h2>
<p class="toggler"><a href="javascript:toggleSearchForm()">Search...</a></p>
<form action="<?php echo $this->url ?>" method="get" class="search-form">
	<?php echo $this->moduleActionFormInputs($params->get('module'), $params->get('action')) ?>
	<p>
		<label for="use_login_after">Login Date Between:</label>
		<input type="checkbox" name="use_login_after" id="use_login_after"<?php if ($params->get('use_login_after')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('login_after') ?>
		<label for="use_login_before">&mdash;</label>
		<input type="checkbox" name="use_login_before" id="use_login_before"<?php if ($params->get('use_login_before')) echo ' checked="checked"' ?> />
		<?php echo $this->dateField('login_before') ?>
		<?php if ($auth->allowedToSearchCpLoginLogPw): ?>
		...
		<label for="password">Password:</label>
		<input type="text" name="password" id="password" value="<?php echo htmlspecialchars($params->get('password')) ?>" />
		<?php endif ?>
	</p>
	<p>
		<label for="user_id">Master ID:</label>
		<input type="text" name="user_id" id="user_id" value="<?php echo htmlspecialchars($params->get('user_id')) ?>" />
		...
		<label for="email">Email:</label>
		<input type="text" name="email" id="email" value="<?php echo htmlspecialchars($params->get('email')) ?>" />
		...
		<label for="username">Username:</label>
		<input type="text" name="username" id="username" value="<?php echo htmlspecialchars($params->get('username')) ?>" />
		...
		<label for="ip">IP Address:</label>
		<input type="text" name="ip" id="ip" value="<?php echo htmlspecialchars($params->get('ip')) ?>" />
		...
		
		
		<input type="submit" value="Search" />
		<input type="button" value="Reset" onclick="reload()" />
	</p>
</form>
<?php if ($logins): ?>
<?php echo $paginator->infoText() ?>
<table class="horizontal-table">
	<tr>
		<th><?php echo $paginator->sortableColumn('user_id', 'Master ID') ?></th>
		<th><?php echo $paginator->sortableColumn('email', 'Email') ?></th>
		<th><?php echo $paginator->sortableColumn('user_id', 'Username') ?></th>
		<?php if (($showPassword=Flux::config('CpLoginLogShowPassword')) && ($seePassword=$auth->allowedToSeeCpLoginLogPass)): ?>
		<th><?php echo $paginator->sortableColumn('user_pass', 'Password') ?></th>
		<?php endif ?>
		<th><?php echo $paginator->sortableColumn('reg_ip', 'IP Address') ?></th>
		<th><?php echo $paginator->sortableColumn('reg_date', 'Login Date') ?></th>

	</tr>
	<?php foreach ($logins as $login): ?>
	<tr>
		<td align="right">
			<?php if ($auth->actionAllowed('master', 'view') && $auth->allowedToViewAccount): ?>
				<?php echo $this->linkToMasterAccount($login->user_id, $login->user_id) ?>
			<?php else: ?>
				<?php echo $login->user_id ?>
			<?php endif ?>
		</td>
		<td>
			<?php if ($auth->actionAllowed('master', 'view') && $auth->allowedToViewAccount): ?>
				<?php echo $this->linkToMasterAccount($login->user_id, $login->email) ?>
			<?php else: ?>
				<?php echo $login->email ?>
			<?php endif ?>
		</td>
		<td><?php echo htmlspecialchars($login->name) ?></td>
		<?php if ($showPassword && $seePassword): ?>
		<td><?php echo htmlspecialchars($login->user_pass) ?></td>
		<?php endif ?>
		<td>
			<?php if ($auth->actionAllowed('master', 'index')): ?>
				<?php echo $this->linkToAccountSearch(array('reg_ip' => $login->reg_ip), $login->reg_ip) ?>
			<?php else: ?>
				<?php echo htmlspecialchars($login->reg_ip) ?>
			<?php endif ?>
		</td>
		<td><?php echo $this->formatDateTime($login->reg_date) ?></td>
	</tr>
	<?php endforeach ?>
</table>
<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>No logs were found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
