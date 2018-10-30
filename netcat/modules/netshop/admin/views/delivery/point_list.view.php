<?php if (!class_exists('nc_core')) { die; } ?>

<?php
/** @var nc_ui $ui */
/** @var string $controller_name */
/** @var nc_netshop_delivery_point_collection $points */
?>

<?= $ui->controls->site_select($catalogue_id) ?>

<table class="nc-table nc--bordered nc--wide">
    <thead>
    <tr>
        <th class='nc--compact'></th>
        <th><?= NETCAT_MODULE_NETSHOP_DELIVERY_POINT ?></th>
        <th><?= NETCAT_MODULE_NETSHOP_DELIVERY_POINT_GROUP_SHORT ?></th>
        <th class='nc--compact'></th>
        <th class='nc--compact'></th>
    </tr>
    </thead>
    <tbody>
    <? foreach ($points as $point): ?>
        <?php

        /** @var nc_netshop_delivery_point_local $point */
        $id = $point->get_id();
        $edit_hash = "#module.netshop.delivery.point.edit($id)";
        $post_actions_params = array('controller' => $controller_name, 'id' => $id);

        ?>
        <tr>
            <td>
                <?= $ui->controls->toggle_button($point->get('enabled'), $post_actions_params) ?>
            </td>
            <td>
                <?= $ui->helper->hash_link($edit_hash, $point->get('name'), 'nc-netshop-list-item-title') ?>
                <div><?= htmlspecialchars($point->get_full_address()); ?></div>
            </td>
            <td><?= htmlspecialchars($point->get('group')); ?></td>
            <td><?= $ui->helper->hash_link($edit_hash, '<i class="nc-icon nc--settings"></i>') ?></td>
            <td>
                <?= $ui->controls->delete_button(
                        sprintf(NETCAT_MODULE_NETSHOP_DELIVERY_POINT_CONFIRM_DELETE, $point->get('name')),
                        $post_actions_params
                    )
                ?>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>