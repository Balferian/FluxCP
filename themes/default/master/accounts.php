<?php if (!defined('FLUX_ROOT')) exit; ?>

<h2><?php echo htmlspecialchars(Flux::message('GameAccountsViewHeading')) ?></h2>
<?php if ($account): ?>
    <?php foreach ($userAccounts as $serverName => $userAccount): ?>
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
							<td><?php echo $server->loginServer->AccountVipTime($acct->account_id, $server->charMapDatabase); ?></td>
						<?php endif ?>
						<td><?php echo (int)$acct->cashpoints ?></td>
                        <td>
                            <?php if (($state = $this->accountStateText($acct->state)) && !$acct->unban_time): ?>
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
