<?php if (!class_exists('nc_core')) {
    die;
} ?>

<?= $ui->controls->site_select($catalogue_id) ?>

    <table class="nc-table nc--bordered nc--wide">
        <thead>
        <tr>
            <th class='nc--compact'></th>
            <th><?= NETCAT_MODULE_NETSHOP_DELIVERY_METHOD ?></th>
            <th><?= NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_COST ?></th>
            <th class="nc-text-center">
                <div class="icons icon_prior" title="<?= CLASSIFICATORS_SORT_PRIORITY_HEADER; ?>"></div>
            </th>
            <th class='nc--compact'></th>
            <th class='nc--compact'></th>
        </tr>
        </thead>
        <tbody>
        <? foreach ($methods as $row): ?>
            <?php

            $checked = $row['Checked'];
            $id = $row['DeliveryMethod_ID'];
            $edit_hash = "#module.netshop.delivery.edit($id)";
            $remove_action = $current_url . 'remove&id=' . $id;
            $status_action = $current_url . 'toggle&Checked=' . (int)(!$row['Checked']) . '&id=' . $id;
            $priority_action = $current_url . 'change_priority&id=' . $id . '&priority=';

            $cost = '';
            if ($row['ShopDeliveryService_ID']) {
                $cost = NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_CALCULATED;
            }
            if ((float)$row['ExtraChargeAbsolute']) {
                $cost .= ($cost ? "&nbsp;+ " : "") . $row['ExtraChargeAbsolute'];
            }
            if ((float)$row['ExtraChargeRelative']) {
                $cost .= ($cost ? "&nbsp;+ " : "") . $row['ExtraChargeRelative'] . "%";
            }

            $method = new nc_netshop_delivery_method();
            $method->set_values_from_database_result($row);
            $condition_info = $method->get_condition_description(false);
            if ($condition_info) {
                $condition_info = NETCAT_MODULE_NETSHOP_COND . $condition_info;
            }

            ?>
            <tr>
                <td><a href="<?= $status_action ?>" class='nc-label nc--<?= $checked ? 'green' : 'red' ?>'>
                        <?= $checked ? NETCAT_MODERATION_OBJ_ON : NETCAT_MODERATION_OBJ_OFF ?></a>
                </td>
                <td>
                    <?= $ui->helper->hash_link($edit_hash, $row['Name'], 'nc-netshop-list-item-title') ?>
                    <div class="nc-netshop-list-condition-info"><?= $condition_info ?></div>
                </td>
                <td><?= $cost ?></td>
                <td width="1">
                    <div class="nc-form">
                        <input type="hidden" name="url" value="<?= $priority_action; ?>"/>
                        <input type="text" name="priority" size="3" value="<?= $row['Priority']; ?>" class="nc--mini" maxlength="5" style="margin-top: -5px; margin-bottom: -7px;"/>
                    </div>
                </td>
                <td><?= $ui->helper->hash_link($edit_hash, '<i class="nc-icon nc--settings"></i>') ?></td>
                <td><a onclick="return confirm('<?= NETCAT_MODULE_NETSHOP_SOURCES_DELETE_CONFIRM ?>')"
                       href="<?= $remove_action ?>"><i class="nc-icon nc--remove"></i></a></td>
            </tr>
        <? endforeach ?>
        </tbody>
    </table>
<?= nc_core('ui')->view(nc_module_folder('netshop/admin/views') . DIRECTORY_SEPARATOR . 'table_priority_sorting.view.php')->make(); ?>