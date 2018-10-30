<?php if (!class_exists('nc_core')) {
    die;
} ?>
<!-- del -->
<h2><?= NETCAT_MODULE_BILLS_INFORMATION?></h2>
<!-- /del -->
<div style="margin-bottom: 15px;"><?= NETCAT_MODULE_BILLS_DESCRIPTION?></div>
<table class="nc-table nc--bordered nc--striped">
    <tbody>
    <tr>
        <th><?= NETCAT_MODULE_BILLS_INFORMATION_STAT_ALL?></th>
        <th><?= NETCAT_MODULE_BILLS_INFORMATION_STAT_PAID?></th>
        <th><?= NETCAT_MODULE_BILLS_INFORMATION_STAT_UNPAID?></th>
    </tr>
    <tr class="nc-text-right">
        <td><?= $total; ?></td>
        <td><?= $paid; ?></td>
        <td><?= $not_paid; ?></td>
    </tr>
    </tbody>
</table>