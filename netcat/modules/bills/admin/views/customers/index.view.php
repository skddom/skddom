<?php
if (!class_exists('nc_core')) {
    die;
}
/**
 * @var $customers nc_bills_company[]
 */
?>
<h2><?= NETCAT_MODULE_BILLS_CUSTOMERS?></h2>
<?php if ($status && $status == 'ok') { ?>
    <?php nc_print_status(NETCAT_MODULE_BILLS_CLIENT_CHANGES_OK, 'ok'); ?>
<?php } ?>
<!-- /del -->
<?php if (count($customers)) { ?>
<table class="nc-table nc--bordered nc--striped" width="100%">
    <tbody>
    <tr>
        <th><?= NETCAT_MODULE_BILLS_CLIENT_NAME?></th>
        <th width="1%"></th>
    </tr>
    <?php foreach($customers as $customer) { ?>
        <tr>
            <td><a href="<?= nc_module_path('bills'); ?>admin/?controller=customers&action=edit&id=<?= $customer->get_id(); ?>"><?= $customer->get('opf'); ?> <?= $customer->get('name'); ?></a></td>
            <td><a onclick="return confirm('<?= NETCAT_MODULE_BILLS_CONFIRM_DELETE?>')" href="<?= nc_module_path('bills'); ?>admin/?controller=customers&action=remove&id=<?= $customer->get_id(); ?>"><i class="nc-icon nc--remove"></i></a></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<?php } else { ?>
    <?php nc_print_status(NETCAT_MODULE_BILLS_CLIENT_EMPTY, 'info'); ?>
<?php } ?>