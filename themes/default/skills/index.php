<?php if (!defined('FLUX_ROOT')) exit; ?>
<pre>
<?php
$text = '
	[SKID.BD_SIEGFRIED] = {
		"BD_SIEGFRIED",
		SkillName = "???? ??u??????",
		MaxLv = 5,
		SpAmount = { 40, 44, 48, 52, 56 },
		bSeperateLv = false,
		AttackRange = { 1, 1, 1, 1, 1 },
		NeedSkillList = { [JOBID.JT_BARD] = {
				{ SKID.BA_POEMBRAGI, 10 }
			}, [JOBID.JT_DANCER] = {
				{ SKID.DC_FORTUNEKISS, 10 }
			} }
	},
';

$texts = explode("\n", $text);
foreach($texts as $text) {
	$tabs = substr_count($text, "\t");
	$insert = "";
	for($i = 0; $i < $tabs; $i++)
		$insert = $insert."\t";
	// { [
	if(preg_match('/(.*)\{(.*)\[/', $text)) {
		$text = preg_replace('/(.*)\{(.*)\[/', "$1{\n$insert$2[", $text);
	}
	// } }
	if(preg_match('/(.*)\}(.*)\}/', $text)) {
		$text = preg_replace('/(.*)\}(.*)\}/', "$1}\n$insert$2}", $text);
	}
	// }, [
	if(preg_match('/(.*)\},(.*)\[/', $text)) {
		$text = preg_replace('/(.*)\},(.*)\[/', "$1},\n$insert$2[", $text);
	}
	echo $text;
}
?>
</pre>


<hr>
<h2>Skills</h2>
<?php if ($Skills): ?>
<?php echo $paginator->infoText() ?>
	<table class="horizontal-table">
		<tr>
			<th colspan="2">Name</th>
			<th>Max Level</th>
			<th>Description</th>
		</tr>
		<?php foreach($Skills as $Skill): ?>
		<tr>
			<td width="24"><img src="<?php echo htmlspecialchars($this->skillImage($Skill->skill_id)) ?>?nocache=<?php echo rand() ?>" /></td>
			<td>
				<?php if ($auth->actionAllowed('skills', 'view')): ?>
					<a href="<?php echo htmlspecialchars($Skill->skill_id) ?>"><?php echo htmlspecialchars($Skill->skill_name) ?></a>
				<?php else: ?>
					<?php echo htmlspecialchars($Skill->skill_name) ?>
				<?php endif ?>
			</td>
			<td>
				<?php echo htmlspecialchars($Skill->skill_max) ?>
			</td>
			<td>
				<?php echo nl2br($Skill->skill_descript) ?>
			</td>
		</tr>
		<?php endforeach ?>
	</table>
	<?php echo $paginator->getHTML() ?>
<?php else: ?>
<p>No items found. <a href="javascript:history.go(-1)">Go back</a>.</p>
<?php endif ?>
