<?php if (!defined('FLUX_ROOT')) exit; ?>

<h2><?php echo htmlspecialchars($headerTitle) ?></h2>
<?php if ($account): ?>
    <table class="vertical-table">
        <tr>
            <th><?php echo htmlspecialchars(Flux::message('AccountNameLabel')) ?></th>
            <td><?php echo htmlspecialchars($account->name) ?></td>
            <th><?php echo htmlspecialchars(Flux::message('MasterAccountIdLabel')) ?></th>
            <td>
                <?php if ($auth->allowedToSeeAccountID): ?>
                    <?php echo $this->getMasterId($account->id) ?>
                <?php else: ?>
                    <span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NotApplicableLabel')) ?></span>
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <th><?php echo htmlspecialchars(Flux::message('EmailAddressLabel')) ?></th>
            <td>
                <?php if ($account->email): ?>
                    <?php if ($auth->actionAllowed('account', 'index')): ?>
                        <?php echo $this->linkToAccountSearch(array('email' => $account->email), $account->email) ?>
                    <?php else: ?>
                        <?php echo htmlspecialchars($account->email) ?>
                    <?php endif ?>
                <?php else: ?>
                    <span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
                <?php endif ?>
            </td>
            <th><?php echo htmlspecialchars(Flux::message('AccountGroupIDLabel')) ?></th>
            <td><?php echo (int)$account->group_id ?></td>
        </tr>
        <tr>
            <th><?php echo htmlspecialchars(Flux::message('AccountBirthdateLabel')) ?></th>
            <td><?php echo $account->birth_date ?></td>
			<th><?php echo htmlspecialchars(Flux::message('CreditBalanceLabel')) ?></th>
			<td><?php echo (int)$account->balance ?></td>
        </tr>
        <tr>
			<th><?php echo htmlspecialchars(Flux::message('LastLoginDateLabel')) ?></th>
			<td><?php echo htmlspecialchars($account->last_login) ?></td>
			<th><?php echo htmlspecialchars(Flux::message('LastUsedIpLabel')) ?></th>
			<td>
				<?php if ($account->last_ip): ?>
					<?php if ($auth->actionAllowed('account', 'index')): ?>
						<?php echo $this->linkToAccountSearch(array('last_ip' => $account->last_ip), $account->last_ip) ?>
					<?php else: ?>
						<?php echo htmlspecialchars($account->last_ip) ?>
					<?php endif ?>
				<?php else: ?>
					<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
				<?php endif ?>
			</td>
       </tr>
    </table>

    <?php if ($auth->allowedToViewAccountBanLog && $banInfo): ?>
        <h3><?php echo htmlspecialchars(sprintf(Flux::message('AccountBanLogSubHeading'), $account->userid)) ?></h3>
        <table class="vertical-table">
            <tr>
                <th><?php echo htmlspecialchars(Flux::message('BanLogBanTypeLabel')) ?></th>
                <th><?php echo htmlspecialchars(Flux::message('BanLogBanDateLabel')) ?></th>
                <th><?php echo htmlspecialchars(Flux::message('BanLogBanDateUntilLabel')) ?></th>
                <th><?php echo htmlspecialchars(Flux::message('BanLogBanReasonLabel')) ?></th>
                <th><?php echo htmlspecialchars(Flux::message('BanLogBannedByLabel')) ?></th>
            </tr>
            <?php foreach ($banInfo as $ban): ?>
                <tr>
                    <td align="right"><?php echo htmlspecialchars($this->banTypeText($ban->ban_type)) ?></td>
                    <td><?php echo $ban->ban_date ?></td>
                    <td><?php echo $ban->ban_until ?></td>
                    <td><?php echo nl2br(htmlspecialchars($ban->ban_reason)) ?></td>
                    <td>
                        <?php if ($ban->userid): ?>
                            <?php if ($auth->allowedToViewAccount): ?>
                                <?php echo $this->linkToAccount($ban->banned_by, $ban->userid) ?>
                            <?php else: ?>
                                <?php echo htmlspecialchars($ban->userid) ?>
                            <?php endif ?>
                        <?php else: ?>
                            <strong><?php echo htmlspecialchars(Flux::message('BanLogBannedByCP')) ?></strong>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>

    <?php foreach ($userAccounts as $serverName => $userAccount): ?>
        <h3><?php echo htmlspecialchars(sprintf(Flux::message('MasterAccountViewSubHead'), $serverName)) ?></h3>
        <?php if ($userAccount): ?>
            <table class="vertical-table">
                <tr>
                    <th><?php echo htmlspecialchars(Flux::message('UsernameLabel')) ?></th>
                    <th><?php echo htmlspecialchars(Flux::message('AccountGroupIDLabel')) ?></th>
                    <th><?php echo htmlspecialchars(Flux::message('LoginCountLabel')) ?></th>
					<?php if($server->VipSystem): ?>
						<th><?php echo htmlspecialchars(Flux::message('VIPStateLabel')) ?></th>
					<?php endif ?>
					<th><?php echo htmlspecialchars(Flux::message('CashPointLabel')) ?></th>
					<th><?php echo htmlspecialchars(Flux::message('AccountStateLabel')) ?></th>
                    <th><?php echo htmlspecialchars(Flux::message('LastLoginDateLabel')) ?></th>
                    <th><?php echo htmlspecialchars(Flux::message('LastUsedIpLabel')) ?></th>
                </tr>
                <?php foreach ($userAccount as $acct):?>
                    <tr>
                        <td align="right">
                            <a href="<?php echo $this->url('account', 'view', array('id' => $acct->account_id)); ?>">
                                <?php echo htmlspecialchars($acct->userid) ?>
                            </a>
                        </td>
                        <td><?php echo (int)$acct->group_id ?></td>
                        <td><?php echo (int)$acct->logincount ?></td>
						<?php if($server->VipSystem): ?>
							<td><?php echo $server->loginServer->AccountVipTime($acct->account_id); ?></td>
						<?php endif ?>
						<td><?php echo (int)$acct->cashpoints ?></td>
                        <td>
                            <?php if (!$acct->confirmed && $acct->confirm_code): ?>
                                <span class="account-state state-pending">
                                    <?php echo htmlspecialchars(Flux::message('AccountStatePending')) ?>
                                </span>
                            <?php elseif (($state = $this->accountStateText($acct->state)) && !$acct->unban_time): ?>
                                <?php echo $state ?>
                            <?php elseif ($acct->unban_time): ?>
                                <span class="account-state state-banned">
                                    <?php printf(htmlspecialchars(Flux::message('AccountStateTempBanned')), date(Flux::config('DateTimeFormat'), $acct->unban_time)) ?>
                                </span>
                            <?php else: ?>
                                <span class="account-state state-unknown"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
                            <?php endif ?>
						</td>
                        <td><?php echo $acct->lastlogin ? date(Flux::config('DateTimeFormat'), strtotime($acct->lastlogin)) : null ?></td>
                        <td><?php echo $acct->last_ip ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        <?php else: ?>
            <p><?php echo htmlspecialchars(sprintf(Flux::message('AccountViewNoChars'), $serverName)) ?></p>
        <?php endif ?>
    <?php endforeach ?>
<?php else: ?>
    <p>
        <?php echo htmlspecialchars(Flux::message('AccountViewNotFound')) ?>
        <a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
    </p>
<?php endif ?>
