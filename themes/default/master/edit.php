<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo htmlspecialchars(Flux::message('MasterAccountEditHeading')) ?></h2>
<?php if ($account): ?>
	<form action="<?php echo $this->urlWithQs ?>" method="post">
		<table class="vertical-table">
			<tr>
				<th><label for="name"><?php echo htmlspecialchars(Flux::message('MasterNameAccountLabel')) ?></label></th>
				<td><input type="text" name="name" id="name" value="<?php echo $account->name ?>" /></td>
				<th><?php echo htmlspecialchars(Flux::message('MasterAccountIdLabel')) ?></th>
				<td><?php echo $account->id ?></td>
			</tr>
			<tr>
				<th><label for="email"><?php echo htmlspecialchars(Flux::message('EmailAddressLabel')) ?></label></th>
				<td><input type="text" name="email" id="email" value="<?php echo htmlspecialchars($account->email) ?>" /></td>
				<?php if ($auth->allowedToEditAccountGroupID && !$isMine): ?>
					<th><label for="group_id"><?php echo htmlspecialchars(Flux::message('AccountGroupIDLabel')) ?></label></th>
					<td><input type="text" name="group_id" id="group_id" value="<?php echo (int)$account->group_id ?>" /></td>
				<?php else: ?>
					<th><?php echo htmlspecialchars(Flux::message('AccountGroupIDLabel')) ?></th>
					<td>
						<input type="hidden" name="group_id" value="<?php echo (int)$account->group_id ?>" />
						<?php echo number_format((int)$account->group_id) ?>
					</td>
				<?php endif ?>
			</tr>
			<tr>
				<?php if ($auth->allowedToEditAccountBalance): ?>
					<th><label for="balance"><?php echo htmlspecialchars(Flux::message('CreditBalanceLabel')) ?></label></th>
					<td colspan="3"><input type="text" name="balance" id="balance" value="<?php echo (int)$account->balance ?>" /></td>
				<?php else: ?>
					<th><?php echo htmlspecialchars(Flux::message('CreditBalanceLabel')) ?></th>
					<td colspan="3"><?php echo number_format((int)$account->balance) ?></td>
				<?php endif ?>
			</tr>
			<tr>
				<th><label for="use_birthdate"><?php echo htmlspecialchars(Flux::message('AccountBirthdateLabel')) ?></label></th>
				<td colspan="3">
					<input type="checkbox" name="use_birthdate" id="use_birthdate" />
					<?php echo $this->dateField('birthdate', $account->birthdate) ?>
				</td>
			</tr>
			<tr>
				<th><label for="use_lastlogin"><?php echo htmlspecialchars(Flux::message('LastLoginDateLabel')) ?></label></th>
				<td colspan="3">
					<input type="checkbox" name="use_lastlogin" id="use_lastlogin" />
					<?php echo $this->dateTimeField('lastlogin', $account->lastlogin) ?>
				</td>
			</tr>
			<tr>
				<th><label for="last_ip"><?php echo htmlspecialchars(Flux::message('LastUsedIpLabel')) ?></label></th>
				<td colspan="3"><input type="text" name="last_ip" id="last_ip" value="<?php echo htmlspecialchars($account->last_ip) ?>" /></td>
			</tr>
			<tr>
				<td colspan="4" align="right">
					<input type="submit" value="<?php echo htmlspecialchars(Flux::message('AccountEditButton')) ?>" />
				</td>
			</tr>
		</table>
	</form>
<?php else: ?>
<p>
	<?php echo htmlspecialchars(Flux::message('AccountEditNotFound')) ?>
	<a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
</p>
<?php endif ?>
