<?php

$form = $ui->form()->multipart()->horizontal()->id('export');
$form->add_row(TOOLS_DATA_BACKUP_DATATYPE)->select('type', $types)->id('export_type');
$form[] = "<div id='type_form'></div>";
?>



<?=$form ?>

<script>
nc('#export_type').change(function(){
    nc('#type_form').html('');
    if (!this.value) return;
    nc.process_start('backup.export_form');
    nc.$.ajax({
        url: '<?=$ADMIN_PATH ?>backup.php?mode=get_form&type=' + this.value,
    }).done(function(data){
        nc.process_stop('backup.export_form');
        nc('#type_form').html(data);
    });
    // console.log(this.value);
});
</script>

<hr>

<? if ($export_files): ?>
<table class="nc-table nc--bordered nc--small nc--striped">
    <tr>
        <th>#</th>
        <th><?=TOOLS_DATA_BACKUP_DATATYPE ?></th>
        <th><?=NETCAT_MODERATION_ID ?></th>
        <th><?=TOOLS_DUMP_INC_SIZE ?></th>
        <th><?=TOOLS_DATA_BACKUP_EXPORT_DATE ?></th>
        <th><?=TOOLS_DOWNLOAD ?></th>
    </tr>
    <? $i = 0 ?>
    <? $total_size = 0 ?>
    <? foreach ($export_files as $file): ?>
    <? $total_size += $file['size'] ?>
    <tr>
        <td class='nc-text-grey'><?=++$i ?></td>
        <td><?=$types[$file['type']] ?></td>
        <td><span class="nc-label"><?=$file['id'] ?></span></td>
        <td><?=$file['size_formated'] ?></td>
        <td><?=date('d.m.Y - H:i:s', $file['time']) ?></td>
        <td><a class='nc-btn nc--mini nc--blue' href='<?=$file['link'] ?>'><i class="nc-icon nc--white nc--download"></i> <?=TOOLS_DOWNLOAD ?></a></td>
    </tr>
    <? endforeach ?>
    <? $size_percent = ceil(($total_size / $export_limit_size) * 100) ?>
    <tr>
        <td colspan="2">
            <div class="nc-progress nc--small nc--<?=$size_percent > 80 ? 'yellow' : 'blue' ?>" style='margin:10px 0'>
                <div class="nc-progress-bar" style='width:<?=$size_percent ?>%'></div>
            </div>
        </td>
        <td colspan="3">
            <?=nc_bytes2size($total_size) ?> <?=TOOLS_DATA_BACKUP_SPACE_FROM ?> <?=nc_bytes2size($export_limit_size) ?> <?=TOOLS_DATA_BACKUP_USED_SPACE ?>
        </td>
        <td><a class='nc-text-red' href='<?=$ADMIN_PATH ?>backup.php?mode=remove_export_files'><i class="nc-icon nc--remove"></i> <?=NETCAT_MODERATION_REMALL ?></a></td>
    </tr>
</table>
<? endif ?>