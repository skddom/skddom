<?php if (!class_exists('nc_core')) {
    die;
    /**
     * @var $customers nc_bills_company[]
     * @var $bill nc_bills_bill
     */
} ?>
    <script>
        $nc(function () {
            var calculate_positions = function () {
                var $table = $nc('INPUT[name^="positions["]').eq(0).closest('TABLE');
                var $rows = $table.find('TR');

                var float_inputs = ['amount', 'sum'];

                $rows.each(function () {
                    var $row = $nc(this);
                    var values = {};
                    for (var i in float_inputs) {
                        var input = float_inputs[i];
                        var $input = $row.find('[name*="[' + input + ']"]');
                        var value = $input.val();
                        value = value ? value.replace(',','.') : '';
                        value = parseFloat(value);
                        value = isNaN(value) ? 0 : value;
                        values[input] = value;
                        $input.val(value);
                    }

                    var sum = values.sum * values.amount;
                    $row.find('.nc-total-sum').text(sum);
                });
            }

            $nc('A.nc-add-position').on('click',function () {
                var $last_row_input = $nc('INPUT[name^="positions["]').last();

                if ($last_row_input.length) {
                    var name = $last_row_input.attr('name');
                    var index = parseInt(name.replace('positions[', '')) + 1;
                } else {
                    var index = 0;
                }

                var $row = $nc(
                    '<tr>' +
                    '<td><input type="text" name="positions[' + index + '][name]" class=""></td>' +
                    '<td><input type="text" name="positions[' + index + '][unit]" class="nc--small"></td>' +
                    '<td><input type="text" name="positions[' + index + '][amount]" class="nc--small"></td>' +
                    '<td class="nc--nowrap"><input type="text" name="positions[' + index + '][sum]" class="nc--medium"> р.</td>' +
                    '<td class="nc--nowrap"><span class="nc-total-sum"></span> р.</td>' +
                    '<td><a href="#" class="nc-delete-position"><i class="nc-icon nc--remove"></i></a></td>' +
                    '</tr>');
                $nc(this).closest('TR').before($row);
                calculate_positions();
                return false;
            }).triggerHandler('click');

            $nc(document).on('click', 'A.nc-delete-position', function () {
                if (confirm('<?= NETCAT_MODULE_BILLS_BILL_CONFIRM_DELETE?>')) {
                    $nc(this).closest('TR').remove();
                }
                return false;
            });

            $nc(document).on('change', 'INPUT[name^="positions["]', function () {
                calculate_positions($nc(this).closest('TABLE'));
                return true;
            });

            $nc('INPUT[name=date]').datepicker();

            calculate_positions();
        });
    </script>
    <!-- del -->
<?php if ($type == 'add') { ?>
    <h2><?= NETCAT_MODULE_BILLS_BILL_ADD ?> <?= NETCAT_MODULE_BILLS_BILL_JURIDICAL ?></h2>
<?php } else { ?>
    <h2><?= NETCAT_MODULE_BILLS_BILL_EDIT ?> <?= NETCAT_MODULE_BILLS_BILL_JURIDICAL ?></h2>
<?php } ?>
<?php if ($type == 'edit' && !$bill->get_id()) { ?>
    <?php nc_print_status(NETCAT_MODULE_BILLS_BILL_SEARCH_EMPTY, 'error'); ?>
<?php } else { ?>
    <?php if ($bill->get_last_error()) { ?>
        <?php nc_print_status($bill->get_last_error(), 'error'); ?>
    <?php } ?>
    <!-- /del -->
    <form action="<?= nc_core()->HTTP_ROOT_PATH; ?>modules/bills/admin/?controller=bills&action=save" method="POST" class="nc-form nc--vertical">
        <input type="hidden" name="id" value="<?= $bill->get_id(); ?>"/>
        <input type="hidden" name="type" value="juridical"/>

        <div class="nc-form-row"><?= NETCAT_MODULE_BILLS_BILL_NUMBER ?>
            <input type="text" name="number" value="<?= $bill->get('number'); ?>" class="nc--small"> <?= NETCAT_MODULE_BILLS_BILL_FROM ?>
            <input type="text" name="date" value="<?= $bill->get_formatted_date(); ?>" class="nc--medium">
        </div>
        <div class="nc-form-row"><?= NETCAT_MODULE_BILLS_CLIENT ?>:
            <div class="nc-select">
                <select name="customer_id" style="width:250px">
                    <option value=""><?= NETCAT_MODULE_BILLS_SELECT ?></option>
                    <?php foreach ($customers as $customer) { ?>
                        <option value="<?= $customer->get_id(); ?>" <?= $customer->get_id() == $bill->get('customer_id') ? 'selected="selected"' : ''; ?>><?= $customer->get('opf'); ?> <?= $customer->get('name'); ?></option>
                    <?php } ?>
                </select>
                <i class="nc-caret"></i>
            </div>
        </div>
        <table class="nc-table nc--bordered nc--striped" width="100%">
            <tbody>
            <tr>
                <th style="width: 100%"><?= NETCAT_MODULE_BILLS_NAME ?></th>
                <th><?= NETCAT_MODULE_BILLS_UNIT ?></th>
                <th><?= NETCAT_MODULE_BILLS_COUNT ?></th>
                <th><?= NETCAT_MODULE_BILLS_PRICE ?></th>
                <th><?= NETCAT_MODULE_BILLS_SUM ?></th>
                <th width="1%"></th>
            </tr>
            <?
            $positions = $bill->get_positions_array();
            $i = 0;
            ?>
            <?php foreach ($positions as $position) { ?>
                <tr>
                    <td>
                        <input type="text" name="positions[<?= $i; ?>][name]" class="" value="<?= $position['name']; ?>">
                    </td>
                    <td>
                        <input type="text" name="positions[<?= $i; ?>][unit]" class="nc--small" value="<?= $position['unit']; ?>">
                    </td>
                    <td>
                        <input type="text" name="positions[<?= $i; ?>][amount]" class="nc--small" value="<?= $position['amount']; ?>">
                    </td>
                    <td class="nc--nowrap">
                        <input type="text" name="positions[<?= $i; ?>][sum]" class="nc--medium" value="<?= $position['sum']; ?>"> р.
                    </td>
                    <td class="nc--nowrap"><span class="nc-total-sum"></span> р.</td>
                    <td><a href="#" class="nc-delete-position"><i class="nc-icon nc--remove"></i></a></td>
                </tr>
                <?php $i++; ?>
            <?php } ?>
            <tr>
                <td colspan="6"><a href="#" class="nc-add-position"><?= NETCAT_MODULE_BILLS_BILL_ADD_POSITION ?></a>
                </td>
            </tr>
            </tbody>
        </table>
        <?php if ($type == 'edit') { ?>
            <div style="margin-top:20px">
                <?= NETCAT_MODULE_BILLS_BILL_IS_PAID ?>
                <div>
                    <select name="paid">
                        <option value="0" <?= $bill->get('paid') == 0 ? 'selected="selected"' : ''; ?>><?= NETCAT_MODULE_BILLS_NO ?></option>
                        <option value="1" <?= $bill->get('paid') == 1 ? 'selected="selected"' : ''; ?>><?= NETCAT_MODULE_BILLS_YES ?></option>
                    </select>
                </div>
                <div style="padding: 20px 0">
                    <a href="<?= $bill->get_pdf_link(); ?>" target="_blank"><?= NETCAT_MODULE_BILLS_BILL_LINK_TO_PDF ?></a><br>
                    <a href="<?= $bill->get_pdf_link(true); ?>" target="_blank"><?= NETCAT_MODULE_BILLS_BILL_LINK_TO_PDF_WITH_SIGN ?></a>
                </div>
            </div>
        <?php } ?>
    </form>
<?php } ?>