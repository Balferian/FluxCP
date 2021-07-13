<?php if (!defined('FLUX_ROOT')) exit; ?>
								</div>
							</td>
						</tr>
					</table>
				</td>
				<!-- Spacing between content and horizontal end-of-page -->
				<td style="padding: 10px"></td>
			</tr>
			<?php if (Flux::config('ShowCopyright')): ?>
			<tr>
				<td colspan="3"></td>
				<td id="copyright">
					<p>
						<strong>Powered by <a href="https://github.com/rathena/FluxCP" target="_blank">FluxCP</a></strong>
					</p>
				</td>
				<td></td>
			</tr>
			<?php endif ?>
			<?php if (Flux::config('ShowRenderDetails')): ?>
			<tr>
				<td colspan="3"></td>
				<td id="info">
					<p>
						Page generated in <strong><?php echo round(microtime(true) - __START__, 5) ?></strong> second(s).
						Number of queries executed: <strong><?php echo (int)Flux::$numberOfQueries ?></strong>.
						<?php if (Flux::config('GzipCompressOutput')): ?>Gzip Compression: <strong>Enabled</strong>.<?php endif ?>
					</p>
				</td>
				<td></td>
			</tr>
			<?php endif ?>
	
			<?php if (count($athenaServerNames=$session->getAthenaServerNames()) > 1): ?>
			<tr>
				<td colspan="3"></td>
				<td align="right">
					<span>Server:
						<select name="preferred_server" onchange="updatePreferredServer(this)"<?php if (count($athenaServerNames=$session->getAthenaServerNames()) === 1) echo ' disabled="disabled"'  ?>>
							<?php foreach ($athenaServerNames as $serverName): ?>
							<option value="<?php echo htmlspecialchars($serverName) ?>"<?php if ($server->serverName == $serverName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($serverName) ?></option>
							<?php endforeach ?>
						</select>
					</span>
					
					<form action="<?php echo $this->urlWithQs ?>" method="post" name="preferred_server_form" style="display: none">
						<input type="hidden" name="preferred_server" value="" />
					</form>
				</td>
				<td></td>
			</tr>
			<?php endif ?>

			<?php if (count(Flux::$appConfig->get('ThemeName', false)) > 1): ?>
			<tr>
				<td colspan="3"></td>
				<td align="right">
					<span>Theme:
						<select name="preferred_theme" onchange="updatePreferredTheme(this)">
							<?php foreach (Flux::$appConfig->get('ThemeName', false) as $themeName): ?>
							<option value="<?php echo htmlspecialchars($themeName) ?>"<?php if ($session->theme == $themeName) echo ' selected="selected"' ?>><?php echo htmlspecialchars($themeName) ?></option>
							<?php endforeach ?>
						</select>
					</span>
					
					<form action="<?php echo $this->urlWithQs ?>" method="post" name="preferred_theme_form" style="display: none">
						<input type="hidden" name="preferred_theme" value="" />
					</form>
				</td>
				<td></td>
			</tr>
			<?php endif ?>

            <tr>
                <td colspan="3"></td>
                <td align="right">
                            <span>Language:
                                <select name="preferred_language" onchange="updatePreferredLanguage(this)">
                                    <?php foreach (Flux::getAvailableLanguages() as $lang_key => $lang): ?>
                                        <option value="<?php echo htmlspecialchars($lang_key) ?>"<?php if (!empty($_COOKIE['language']) && $_COOKIE['language'] == $lang_key) echo ' selected="selected"' ?>><?php echo htmlspecialchars($lang) ?></option>
                                    <?php endforeach ?>
                                    </select>
                                </span>
                </td>
                <td></td>
            </tr>
		</table>
	</body>
</html>
