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
							<td><?php echo $server->loginServer->AccountVipTime($acct->account_id, $server->charMapDatabase); ?></td>
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

	<?php foreach($StorageTables as $table => $data): ?>
		<?php if($data[1] != "master") continue; $items = $storage[$table]; ?>
		<h3><?php echo htmlspecialchars(sprintf(Flux::message('StorageLabel'), $data[0], $account->name)) ?></h3>
		<?php if ($items): ?>
			<p><?php echo htmlspecialchars(sprintf(Flux::message('AccountViewStorageCount'), $account->name, count($items))) ?></p>
			<table class="vertical-table">
				<tr>
					<th><?php echo htmlspecialchars(Flux::message('ItemIdLabel')) ?></th>
					<th colspan="2"><?php echo htmlspecialchars(Flux::message('ItemNameLabel')) ?></th>
					<th><?php echo htmlspecialchars(Flux::message('ItemAmountLabel')) ?></th>
					<th><?php echo htmlspecialchars(Flux::message('ItemIdentifyLabel')) ?></th>
					<th><?php echo htmlspecialchars(Flux::message('ItemBrokenLabel')) ?></th>
					<th><?php echo htmlspecialchars(Flux::message('ItemCard0Label')) ?></th>
					<th><?php echo htmlspecialchars(Flux::message('ItemCard1Label')) ?></th>
					<th><?php echo htmlspecialchars(Flux::message('ItemCard2Label')) ?></th>
					<th><?php echo htmlspecialchars(Flux::message('ItemCard3Label')) ?></th>
					<?php if($server->isRenewal): ?>
						<th><?php echo htmlspecialchars(Flux::message('ItemRandOptionsLabel')) ?></th>
					<?php endif ?>
					<th>Extra</th>
					</th>
				</tr>
				<?php foreach ($items AS $item): ?>
				<?php $icon = $this->iconImage($item->nameid) ?>
				<tr>
					<td align="right">
						<?php if ($auth->actionAllowed('item', 'view')): ?>
							<?php echo $this->linkToItem($item->nameid, $item->nameid) ?>
						<?php else: ?>
							<?php echo htmlspecialchars($item->nameid) ?>
						<?php endif ?>
					</td>
					<?php if ($icon): ?>
					<td>
						<img src="<?php echo htmlspecialchars($icon) ?>" />
						<?php if($item->enchantgrade): ?>
							<div class="enchantgrade grade_<?php echo $item->enchantgrade; ?>"></div>
						<?php endif ?>
					</td>
					<?php endif ?>
					<td<?php if (!$icon) echo ' colspan="2"' ?><?php if ($item->cardsOver) echo ' class="overslotted' . $item->cardsOver . '"'; else echo ' class="normalslotted"' ?>>
						<?php if ($item->refine > 0): ?>
							+<?php echo htmlspecialchars($item->refine) ?>
						<?php endif ?>
						<?php if ($item->card0 == 255 && intval($item->card1/1280) > 0): ?>
							<?php $itemcard1 = intval($item->card1/1280); ?>
							<?php for ($i = 0; $i < $itemcard1; $i++): ?>
								Very
							<?php endfor ?>
							Strong
						<?php endif ?>
						<?php if ($item->card0 == 254 || $item->card0 == 255): ?>
							<?php if ($item->char_name): ?>
								<?php if ($auth->actionAllowed('character', 'view') && ($isMine || (!$isMine && $auth->allowedToViewCharacter))): ?>
									<?php echo $this->linkToCharacter($item->char_id, $item->char_name, $session->serverName) . "'s" ?>
								<?php else: ?>
									<?php echo htmlspecialchars($item->char_name . "'s") ?>
								<?php endif ?>
							<?php else: ?>
								<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>'s
							<?php endif ?>
						<?php endif ?>
						<?php if ($item->card0 == 255 && array_key_exists($item->card1%1280, $itemAttributes)): ?>
							<?php echo htmlspecialchars($itemAttributes[$item->card1%1280]) ?>
						<?php endif ?>
						<?php if ($item->name_english): ?>
							<span class="item_name"><?php echo htmlspecialchars($item->name_english) ?></span>
						<?php else: ?>
							<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('UnknownLabel')) ?></span>
						<?php endif ?>
						<?php if ($item->slots): ?>
							<?php echo htmlspecialchars(' [' . $item->slots . ']') ?>
						<?php endif ?>
					</td>
					<td><?php echo number_format($item->amount) ?></td>
					<td>
						<?php if ($item->identify): ?>
							<span class="identified yes"><?php echo htmlspecialchars(Flux::message('YesLabel')) ?></span>
						<?php else: ?>
							<span class="identified no"><?php echo htmlspecialchars(Flux::message('NoLabel')) ?></span>
						<?php endif ?>
					</td>
					<td>
						<?php if ($item->attribute): ?>
							<span class="broken yes"><?php echo htmlspecialchars(Flux::message('YesLabel')) ?></span>
						<?php else: ?>
							<span class="broken no"><?php echo htmlspecialchars(Flux::message('NoLabel')) ?></span>
						<?php endif ?>
					</td>
					<td>
						<?php if($item->card0 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
							<?php if (!empty($cards[$item->card0])): ?>
								<?php if ($auth->actionAllowed('item', 'view')): ?>
									<?php echo $this->linkToItem($item->card0, $cards[$item->card0]) ?>
								<?php else: ?>
									<?php echo htmlspecialchars($cards[$item->card0]) ?>
								<?php endif ?>
							<?php else: ?>
								<?php if ($auth->actionAllowed('item', 'view')): ?>
									<?php echo $this->linkToItem($item->card0, $item->card0) ?>
								<?php else: ?>
									<?php echo htmlspecialchars($item->card0) ?>
								<?php endif ?>
							<?php endif ?>
						<?php else: ?>
							<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
						<?php endif ?>
					</td>
					<td>
						<?php if($item->card1 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 255 && $item->card0 != -256): ?>
							<?php if (!empty($cards[$item->card1])): ?>
								<?php if ($auth->actionAllowed('item', 'view')): ?>
									<?php echo $this->linkToItem($item->card1, $cards[$item->card1]) ?>
								<?php else: ?>
									<?php echo htmlspecialchars($cards[$item->card1]) ?>
								<?php endif ?>
							<?php else: ?>
								<?php if ($auth->actionAllowed('item', 'view')): ?>
									<?php echo $this->linkToItem($item->card1, $item->card1) ?>
								<?php else: ?>
									<?php echo htmlspecialchars($item->card1) ?>
								<?php endif ?>
							<?php endif ?>
						<?php else: ?>
							<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
						<?php endif ?>
					</td>
					<td>
						<?php if($item->card2 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
							<?php if (!empty($cards[$item->card2])): ?>
								<?php if ($auth->actionAllowed('item', 'view')): ?>
									<?php echo $this->linkToItem($item->card2, $cards[$item->card2]) ?>
								<?php else: ?>
									<?php echo htmlspecialchars($cards[$item->card2]) ?>
								<?php endif ?>
							<?php else: ?>
								<?php if ($auth->actionAllowed('item', 'view')): ?>
									<?php echo $this->linkToItem($item->card2, $item->card2) ?>
								<?php else: ?>
									<?php echo htmlspecialchars($item->card2) ?>
								<?php endif ?>
							<?php endif ?>
						<?php else: ?>
							<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
						<?php endif ?>
					</td>
					<td>
						<?php if($item->card3 && ($item->type == $type_list['armor'] || $item->type == $type_list['weapon']) && $item->card0 != 254 && $item->card0 != 255 && $item->card0 != -256): ?>
							<?php if (!empty($cards[$item->card3])): ?>
								<?php if ($auth->actionAllowed('item', 'view')): ?>
									<?php echo $this->linkToItem($item->card3, $cards[$item->card3]) ?>
								<?php else: ?>
									<?php echo htmlspecialchars($cards[$item->card3]) ?>
								<?php endif ?>
							<?php else: ?>
								<?php if ($auth->actionAllowed('item', 'view')): ?>
									<?php echo $this->linkToItem($item->card3, $item->card3) ?>
								<?php else: ?>
									<?php echo htmlspecialchars($item->card3) ?>
								<?php endif ?>
							<?php endif ?>
						<?php else: ?>
							<span class="not-applicable"><?php echo htmlspecialchars(Flux::message('NoneLabel')) ?></span>
						<?php endif ?>
					</td>
					<?php if($server->isRenewal): ?>
						<td>
							<?php if($item->rndopt): ?>
								<ul>
									<?php foreach($item->rndopt as $rndopt) echo "<li>".$this->itemRandOption($rndopt[0], $rndopt[1])."</li>"; ?>
								</ul>
							<?php else: ?>
								<span class="not-applicable">None</span>
							<?php endif ?>
						</td>
					<?php endif ?>
					<td>
					<?php if($item->bound == 1):?>
						Account Bound
					<?php elseif($item->bound == 2):?>
						Guild Bound
					<?php elseif($item->bound == 3):?>
						Party Bound
					<?php elseif($item->bound == 4):?>
						Character Bound
					<?php else:?>
							<span class="not-applicable">None</span>
					<?php endif ?>
					</td>
				</tr>
				<?php endforeach ?>
			</table>
		<?php else: ?>
			<p><?php echo htmlspecialchars(Flux::message('AccountViewNoStorage')) ?></p>
		<?php endif ?>
	<?php endforeach ?>
<?php else: ?>
    <p>
        <?php echo htmlspecialchars(Flux::message('AccountViewNotFound')) ?>
        <a href="javascript:history.go(-1)"><?php echo htmlspecialchars(Flux::message('GoBackLabel')) ?></a>
    </p>
<?php endif ?>
