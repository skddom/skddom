<?php if (!class_exists('nc_core')) {
    die;
    /**
     * @var $bills nc_bills_bill[]
     */
} ?>
    <script>
        $nc(function () {
            $nc('SELECT[name^="paid["]').on('change', function () {
                var $this = $nc(this);

                if (confirm('Изменить статус счета?')) {
                    var value = $this.val();
                    $this.attr('data-nc-original', value);
                    var id = /\[(\d+)\]/.exec($this.attr('name'));
                    id = id[1];
                    nc.process_start('status_change_' + id);
                    $nc.get('<?= nc_module_path('bills'); ?>admin/', {
                        controller: 'bills',
                        action: 'save_status',
                        id: id,
                        status: value
                    }, function () {
                        nc.process_stop('status_change_' + id);
                    });
                } else {
                    $this.val($this.attr('data-nc-original'));
                }

                return true;
            });

            $nc("SELECT[name=batch]").on('change', function () {
                var $this = $nc(this);
                var $form = $this.closest('FORM');
                if ($this.val() != 0 && $form.find('[name="bill_id[]"]:checked').length > 0) {
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
            }).on('keydown', function(e) {
                // не отправлять форму при нажатии Enter
                if (e.which == 13) {
                    e.preventDefault();
                    return false;
                }

            });
        });
    </script>
    <h2>Счета</h2>
<?php if (count($bills)) { ?>
    <form action="<?= nc_module_path('bills'); ?>admin/?controller=bills&action=batch" method="POST"
     target="_blank" class="nc-form nc--horizontal">
        <select name="batch">
            <option value="0"><?= NETCAT_MODULE_BILLS_BATCH_LOADING?></option>
            <option value="1"><?= NETCAT_MODULE_BILLS_PDF ?></option>
            <option value="2"><?= NETCAT_MODULE_BILLS_PDF_WITH_SIGN_AND_PRINT ?></option>
        </select>
        <input type="text" name="search" class="nc--large" placeholder="<?= NETCAT_MODULE_BILLS_BILL_SEARCH ?>">

        <table class="nc-table nc--bordered nc--striped" width="100%">
            <tbody>
            <tr>
                <th width="1%"></th>
                <th>№</th>
                <th><?= NETCAT_MODULE_BILLS_DATE ?></th>
                <th><?= NETCAT_MODULE_BILLS_CLIENT ?></th>
                <th><?= NETCAT_MODULE_BILLS_SUM ?></th>
                <th width="100"><?= NETCAT_MODULE_BILLS_BILL_IS_PAID_SHORT ?></th>
                <th></th>
                <th width="1%"></th>
            </tr>
            <?php foreach ($bills as $bill) { ?>
                <?
                $paid = $bill->get('paid');
                $customer = new nc_bills_company();
                try {
                    $customer->load($bill->get('customer_id'));
                } catch (nc_record_exception $e) {

                }
                ?>
                <tr>
                    <td><input type="checkbox" name="bill_id[]" value="<?= $bill->get_id(); ?>"></td>
                    <td>
                        <a href="<?= nc_module_path('bills'); ?>admin/?controller=bills&action=edit&id=<?= $bill->get_id(); ?>" alt="<?= $bill->get('number'); ?>" title="<?= $bill->get('number'); ?>"><?= $bill->get('number'); ?></a>
                    </td>
                    <td><?= $bill->get_formatted_date(); ?></td>
                    <td class="nc-customer-name">
                        <?php if ($bill->get('type') == 'juridical') { ?>
                            <?php if ($customer->get_id()) { ?>
                                <a href="<?= nc_module_path('bills'); ?>admin/?controller=customers&action=edit&id=<?= $customer->get_id(); ?>" alt="<?= $customer->get('opf'); ?> <?= $customer->get('name'); ?>" title="<?= $customer->get('opf'); ?> <?= $customer->get('name'); ?>"><?= $customer->get('opf'); ?> <?= $customer->get('name'); ?></a>
                            <?php } ?>
                        <?php } else { ?>
                            <?= $bill->get('customer_name'); ?>
                        <?php } ?>
                    </td>
                    <td><?= $bill->get_formatted_sum(); ?> руб.</td>
                    <td>
                        <div class="nc-select">
                            <select name="paid[<?= $bill->get_id(); ?>]" data-nc-original="<?= $paid; ?>">
                                <option value="1" <?= $paid ? 'selected="selected"' : ''; ?>><?= NETCAT_MODULE_BILLS_YES ?></option>
                                <option value="0" <?= !$paid ? 'selected="selected"' : ''; ?>><?= NETCAT_MODULE_BILLS_NO ?></option>
                            </select>
                            <i class="nc-caret"></i>
                        </div>
                    </td>
                    <td>
                        <a href="<?= $bill->get_pdf_link(); ?>" target="_blank"><?= NETCAT_MODULE_BILLS_PDF ?></a>
                        <?php if ($bill->get('type') == 'juridical') { ?>
                            <br>
                            <a href="<?= $bill->get_pdf_link(true); ?>" target="_blank"><?= NETCAT_MODULE_BILLS_PDF_WITH_SIGN ?></a>
                        <?php } ?>
                    </td>
                    <td>
                        <a onclick="return confirm('<?= NETCAT_MODULE_BILLS_CONFIRM_DELETE ?>')" href="<?= nc_module_path('bills'); ?>admin/?controller=bills&action=remove&id=<?= $bill->get_id(); ?>"><i class="nc-icon nc--remove"></i></a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </form>
<?php } else { ?>
    <?php nc_print_status(NETCAT_MODULE_BILLS_BILL_EMPTY, 'info'); ?>
<?php } ?>