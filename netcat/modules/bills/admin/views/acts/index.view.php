<?php if (!class_exists('nc_core')) {
    die;
    /**
     * @var $acts nc_bills_act[]
     */
} ?>
    <script>
        $nc(function () {
            $nc("SELECT[name=batch]").on('change', function(){
                var $this = $nc(this);
                var $form = $this.closest('FORM');
                if ($this.val() != 0 && $form.find('[name="act_id[]"]:checked').length > 0) {
                    $form.submit();
                }
                $this.val(0);
                return true;
            });

            $nc('INPUT[name=search]').on('keyup', function () {
                var value = $nc.trim($nc(this).val()).toLowerCase();
                $nc('.nc-customer-name').closest('TR').show().each(function () {
                    if (value == '') {
                        return false;
                    }
                    var $tr = $nc(this);
                    var text = $tr.find('.nc-customer-name').text().toLowerCase();
                    if (text.search(value) == -1) {
                        $tr.hide();
                    }
                    return true;
                });

                return true;
            });
        });
    </script>
    <h2><?= NETCAT_MODULE_BILLS_ACTS?></h2>
<?php if (count($acts)) { ?>
    <form action="<?= nc_module_path('bills'); ?>admin/?controller=acts&action=batch" method="POST" target="_blank" class="nc-form nc--horizontal">
        <div class="nc-select">
            <select name="batch">
                <option value="0"><?= NETCAT_MODULE_BILLS_BATCH_LOADING?></option>
                <option value="1"><?= NETCAT_MODULE_BILLS_PDF ?></option>
                <option value="2"><?= NETCAT_MODULE_BILLS_PDF_WITH_SIGN_AND_PRINT ?></option>
            </select>
            <i class="nc-caret"></i>
        </div>
        <input type="text" name="search" class="nc--large" placeholder="<?= NETCAT_MODULE_BILLS_ACT_SEARCH?>">

        <table class="nc-table nc--bordered nc--striped" width="100%">
            <tbody>
            <tr>
                <th width="1%"></th>
                <th>№</th>
                <th><?= NETCAT_MODULE_BILLS_DATE?></th>
                <th><?= NETCAT_MODULE_BILLS_CLIENT?></th>
                <th><?= NETCAT_MODULE_BILLS_SUM?></th>
                <th></th>
                <th width="1%"></th>
            </tr>
            <?php foreach ($acts as $act) { ?>
                <?
                $bill = new nc_bills_bill();
                $customer = new nc_bills_company();
                try {
                    $bill->load($act->get('bill_id'));
                    $customer->load($bill->get('customer_id'));
                } catch (nc_record_exception $e) {

                }
                ?>
                <tr>
                    <td><input type="checkbox" name="act_id[]" value="<?= $act->get_id(); ?>"></td>
                    <td>
                        <a href="<?= nc_module_path('bills'); ?>admin/?controller=acts&action=edit&id=<?= $act->get_id(); ?>" alt="<?= $act->get('number'); ?>" title="<?= $act->get('number'); ?>"><?= $act->get('number'); ?></a>
                    </td>
                    <td><?= $act->get_formatted_date(); ?></td>
                    <td class="nc-customer-name">
                        <?php if ($bill->get('type') == 'juridical') { ?>
                            <?php if ($customer->get_id()) { ?>
                                <a href="<?= nc_module_path('bills'); ?>admin/?controller=customers&action=edit&id=<?= $customer->get_id(); ?>" alt="<?= $customer->get('opf'); ?> <?= $customer->get('name'); ?>" title="<?= $customer->get('opf'); ?> <?= $customer->get('name'); ?>"><?= $customer->get('opf'); ?> <?= $customer->get('name'); ?></a>
                            <?php } ?>
                        <?php } else { ?>
                            <?= $bill->get('customer_name'); ?>
                        <?php } ?>
                    </td>
                    <td class="nc--nowrap"><?= $bill->get_formatted_sum(); ?> руб.</td>
                    <td>
                        <a href="<?= $act->get_pdf_link(); ?>" target="_blank"><?= NETCAT_MODULE_BILLS_PDF?></a><br>
                        <a href="<?= $act->get_pdf_link(true); ?>" target="_blank"><?= NETCAT_MODULE_BILLS_PDF_WITH_SIGN?></a>
                    </td>
                    <td>
                        <a onclick="return confirm('<?= NETCAT_MODULE_BILLS_CONFIRM_DELETE?>')" href="<?= nc_module_path('bills'); ?>admin/?controller=acts&action=remove&id=<?= $act->get_id(); ?>"><i class="nc-icon nc--remove"></i></a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </form>
<?php } else { ?>
    <?php nc_print_status(NETCAT_MODULE_BILLS_ACT_EMPTY, 'info'); ?>
<?php } ?>