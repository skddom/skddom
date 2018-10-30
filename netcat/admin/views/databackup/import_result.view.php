<table class="nc-table nc--bordered nc--small">

<? if ($result['import_table']): ?>
    <tr class='nc-bg-lighten'>
        <th colspan="2"><?=TOOLS_DATA_BACKUP_NEW_TABLES ?></th>
    </tr>
    <? foreach ($result['import_table'] as $table => $new_table): ?>
    <tr>
        <td><?=$table ?></td>
        <td><span class="nc-label nc--green"><?=$new_table ?></span></td>
    </tr>
    <? endforeach ?>
<? endif ?>



<? if ($result['import_data']): ?>
    <tr class='nc-bg-lighten'>
        <th colspan="2"><?=TOOLS_DATA_BACKUP_STEP_DATA ?></th>
    </tr>
    <? foreach ($result['import_data'] as $table => $count): ?>
    <tr>
        <td><?=$table ?></td>
        <td><span class="nc-label nc--green"><?=$count ?></span></td>
    </tr>
    <? endforeach ?>
<? endif ?>



<? if ($result['import_file']): ?>
    <tr class='nc-bg-lighten'>
        <th colspan="2"><?=TOOLS_DATA_BACKUP_STEP_FILES ?></th>
    </tr>
    <? foreach ($result['import_file'] as $path => $files): ?>
    <? if ($path): ?>
        <tr>
            <td colspan="2"><span class="nc-label"><?=$path ?></span></td>
        </tr>
    <? endif ?>
    <? foreach ($files as $file => $status): ?>
    <tr>
        <td><?=$file ?></td>
        <td><span class="nc-label nc--<?=$status=='OK' ? 'green' : ($status =='SKIP' ? 'yellow' : 'red') ?>"><?=$status ?></span></td>
    </tr>
    <? endforeach ?>
    <? endforeach ?>
<? endif ?>

</table>