<?php if (!defined('FLUX_ROOT')) exit; ?>
<h2><?php echo FLUX::message('SpawnGeneratorLabel'); ?></h2>
<h3><?php echo FLUX::message('UnitsDBLabel'); ?></h3>
<div id="parser_table">
<table class="table">
</table>
</div>
<div class="units_parser">
    <?php echo sprintf(Flux::message('ScriptMobsFoundedLabel'), $MobSpawnBase); ?><br>
    <?php echo sprintf(Flux::message('ScriptNpcsFoundedLabel'), $npcsBase); ?><br>
    <?php echo sprintf(Flux::message('ScriptShopsFoundedLabel'), $shopsBase); ?><br>
    <?php echo sprintf(Flux::message('ScriptWarpsFoundedLabel'), $warpsBase); ?><br>
	<form method="post" enctype="multipart/form-data">
		<?php echo sprintf(Flux::message('ScriptUploadScriptLabel'), ini_get('upload_max_filesize')); ?><br>
		<input type="file" name="npc_zip"><br>
		<input class="btn" type="submit">
	</form>
	<form  method="POST">
		<input type="hidden" name="act" value="truncate_db" />
		<input class="btn" type="submit" value="<?php echo FLUX::message('ScriptCleanUnitsLabel'); ?>" onclick="return confirm('<?php echo FLUX::message('ScriptCleanUnitsConfirmLabel'); ?>')"/>
	</form>

<h3><?php echo FLUX::message('MapsDBLabel'); ?></h3>
    <?php echo sprintf(Flux::message('ScriptMapsFoundedLabel'), $mapIndexBase); ?><br>
	<form method="post" enctype="multipart/form-data">
		<?php echo sprintf(Flux::message('ScriptUploadMapLabel'), ini_get('upload_max_filesize')); ?><br>
		<input type="file" name="map_index"><br>
		<input class="btn" type="submit">
	</form>
	<form method="POST">
		<input type="hidden" name="act" value="truncate_map" />
		<input class="btn" type="submit" value="<?php echo FLUX::message('ScriptCleanMapsLabel'); ?>" onclick="return confirm('<?php echo FLUX::message('ScriptCleanMapsConfirmLabel'); ?>')" />
	</form>

<?php if(sizeof($file)){ ?>
    <script>
        $(document).ready(function() {
            var FILES = ['<?php echo join('\',\'', $file)?>'];
            var COUNT = 0;
            var errors = [];
            $('.loading').show();
            var block = document.getElementById('parser_table');
            startInsert();
            function startInsert() {
                if (errors[COUNT] >= 3) {
                    COUNT++;
                }
                if (typeof errors[COUNT] === 'undefined') {
                    errors[COUNT] = 0;
                }
                if (COUNT >= FILES.length) {
                    $('.loading').hide();
                    $.get('?module=script_parser&action=get&type=delDir');
                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: '?module=script_parser&action=get',
                    dataType: 'json',
                    data: {
                        file_name: FILES[COUNT]
                    },
                    success: function (data) {
                        if(data.isError){
                            $('.table').append('<tr style="color: red;"><td>' +
                                (COUNT + 1) + '/' + FILES.length + ' ' +
                                'File <b>' + FILES[COUNT] + '</b> unsuccessfly load. </td>' +
                                '<td colspan=4>' + data.error + '</td></tr>');
                        } else {
                            $('.table').append('<tr><td>' +
                                (COUNT + 1) + '/' + FILES.length + ' ' +
                                'File <b>' + data.file_short + '</b> successfly load. </td>' +
                                '<td>Mobs: <b>' + data.data.mobs + '</b>, </td>' +
                                '<td>Warps: <b>' + data.data.warps + '</b>, </td>' +
                            '<td>NPCs: <b>' + data.data.npcs + '</b></td>' +
                            '<td>Shops: <b>' + data.data.shops + '</b></td>' +
                            '</tr>');
                            $('#monNum').text(parseInt($('#monNum').text()) + data.data.mobs);
                            $('#warpNum').text(parseInt($('#warpNum').text()) + data.data.warps);
                            $('#npcNum').text(parseInt($('#npcNum').text()) + data.data.npcs);
                            $('#shopNum').text(parseInt($('#shopNum').text()) + data.data.shops);
                        }
                        COUNT++;
                        block.scrollTop = 9999;
                        startInsert();
                    },
                    error: function () {
                        errors[COUNT]++;
                        $('.table').append('<tr><td class="reds">' +
                            (COUNT + 1) + '/' + FILES.length + ' ' +
                            'File <b>' + FILES[COUNT] + '</b> unsuccessfly load </td>' +
                            '<td colspan=4>(attempt ' + errors[COUNT] + ' of 3)</td></tr>');
                        block.scrollTop = block.scrollHeight;
                        startInsert();
                    }
                })
            }
        });
    </script>
<?php } ?>
</div>
