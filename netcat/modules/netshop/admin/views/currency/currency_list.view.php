<?php if (!class_exists('nc_core')) { die; } ?>

<?= $ui->controls->site_select($catalogue_id) ?>

<table class="nc-table nc--bordered nc--wide">
<tr>
    <th class='nc--compact'></th>
    <th><?=$fields['Currency_ID']['title'] ?></th>
    <th><?=$fields['Rate']['title'] ?></th>
    <th class='nc--compact'></th>
    <th class='nc--compact'></th>
</tr>
<? foreach ($currencies as $row): ?>
    <? $edit_action   = $current_url . 'edit&id=' . $row->Netshop_Currency_ID ?>
    <? $remove_action = $current_url . 'remove&id=' . $row->Netshop_Currency_ID ?>
    <? $status_action = $current_url . 'toggle&Checked=' . (int)(!$row->Checked) . '&id=' . $row->Netshop_Currency_ID ?>
    <tr>
        <td><a href="<?=$status_action ?>" class='nc-label nc--<?=$row->Checked ? 'green' : 'red' ?>'><?=$row->Checked ? NETCAT_MODERATION_OBJ_ON : NETCAT_MODERATION_OBJ_OFF ?></a></td>
        <td><a href="<?=$edit_action ?>"><?=$currency_names[$row->Currency_ID] ?></a></td>
        <td><?=$row->Rate ?></td>
        <td><a href="<?=$edit_action ?>"><i class="nc-icon nc--settings"></i></a></td>
        <td><a onclick="return confirm('<?=NETCAT_MODULE_NETSHOP_SOURCES_DELETE_CONFIRM ?>')" href="<?=$remove_action ?>"><i class="nc-icon nc--remove"></i></a></td>
    </tr>
<? endforeach ?>
</table>