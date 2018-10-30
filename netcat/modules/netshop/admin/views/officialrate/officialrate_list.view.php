<?php if (!class_exists('nc_core')) { die; } ?>

<?= $ui->controls->site_select($catalogue_id) ?>

<table class="nc-table nc--bordered nc--wide">
<tr class="nc-text-center">
    <th class='nc--compact'></th>
    <th class="nc-text-center"><?=$fields['Date']['title'] ?></th>
    <th class="nc-text-center"><?=$fields['Currency']['title'] ?></th>
    <th class="nc-text-center"><?=$fields['Rate']['title'] ?></th>
    <th class='nc--compact'></th>
    <th class='nc--compact'></th>
</tr>
<? foreach ($official_rates as $row): ?>
    <?php
        $id = $row->Netshop_OfficialRate_ID;
        $edit_action   = $current_url . 'edit&id=' . $id ;
        $remove_action = $current_url . 'remove&id=' . $id;
        $status_action = $current_url . 'toggle&Checked=' . (int)(!$row->Checked) . '&id=' . $id
    ?>
    <tr class="nc-text-center">
        <td><a href="<?=$status_action ?>" class='nc-label nc--<?=$row->Checked ? 'green' : 'red' ?>'><?=$row->Checked ? NETCAT_MODERATION_OBJ_ON : NETCAT_MODERATION_OBJ_OFF ?></a></td>
        <td><?=date(NETCAT_MODULE_NETSHOP_DATE_FORMAT, strtotime($row->Date)) ?></td>
        <td><a href="<?=$edit_action ?>"><?=$currency_names[$row->Currency] ?></a></td>
        <td><?=$row->Rate ?></td>
        <td><a href="<?=$edit_action ?>"><i class="nc-icon nc--settings"></i></a></td>
        <td><a onclick="return confirm('<?=NETCAT_MODULE_NETSHOP_SOURCES_DELETE_CONFIRM ?>')" href="<?=$remove_action ?>"><i class="nc-icon nc--remove"></i></a></td>
    </tr>
<? endforeach ?>
</table>