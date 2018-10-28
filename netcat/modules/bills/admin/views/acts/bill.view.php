<?php if (!class_exists('nc_core')) {
    die;
    /**
     * @var $bill nc_bills_bill
     */
} ?>
<?php
$customer = new nc_bills_company();
try {
    $customer->load($bill->get('customer_id'));
} catch (nc_record_exception $e) {

}
?>
<div class="nc-form-row"><?= NETCAT_MODULE_BILLS_CLIENT ?>:
    <?php if ($bill->get('type') == 'juridical') { ?>
        <a href="<?= nc_module_path('bills'); ?>?controller=customers&action=edit&id=<?= $customer->get_id(); ?>"><?= $customer->get('opf'); ?> <?= $customer->get('name'); ?></a>
    <?php } else { ?>
        <?= $bill->get_physical_customer(); ?>
    <?php } ?>
</div>
<table class="nc-table nc--bordered nc--striped" width="100%">
    <tbody>
    <tr>
        <th><?= NETCAT_MODULE_BILLS_NAME ?></th>
        <th><?= NETCAT_MODULE_BILLS_UNIT ?></th>
        <th><?= NETCAT_MODULE_BILLS_COUNT ?></th>
        <th><?= NETCAT_MODULE_BILLS_PRICE ?></th>
        <th><?= NETCAT_MODULE_BILLS_SUM ?></th>
    </tr>
    <?php foreach ($bill->get_positions_array() as $position) { ?>
        <tr>
            <td>
                <input type="text" name="" class="nc--xlarge" disabled="" value="<?= $position['name']; ?>">
            </td>
            <td>
                <input type="text" name="" class="nc--small" disabled="" value="<?= $position['unit']; ?>">
            </td>
            <td>
                <input type="text" name="" class="nc--small" disabled="" value="<?= $position['amount']; ?>">
            </td>
            <td class="nc--nowrap">
                <input type="text" name="" class="" disabled="" value="<?= $position['formatted_sum']; ?>"> р.
            </td>
            <td class="nc--nowrap"><?= $position['formatted_total']; ?> р.</td>
        </tr>
    <?php } ?>
    </tbody>
</table>