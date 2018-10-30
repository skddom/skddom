<?php if (!class_exists('nc_core')) {
    die;
    /**
     * @var $act nc_bills_act
     * @var $bills nc_bills_bill[]
     */
} ?>
    <script>
        $nc(function () {
            $nc('SELECT[name=bill_id]').on('change',function () {
                var bill_id = $nc(this).val();
                if (bill_id) {
                    nc.process_start('load_bill_' + bill_id);
                    $nc.get('<?= nc_module_path('bills'); ?>admin/', {
                        controller: 'acts',
                        action: 'get_bill',
                        id: bill_id
                    }, function (data) {
                        $nc('.nc-bill-container').html(data);
                        nc.process_stop('load_bill_' + bill_id);
                    })
                } else {
                    $nc('.nc-bill-container').html('');
                }

                return true;
            }).triggerHandler('change');

            $nc('INPUT[name=date]').datepicker();
        });
    </script>
    <!-- del -->
<?php if ($type == 'add') { ?>
    <h2><?= NETCAT_MODULE_BILLS_ACT_ADD ?></h2>
<?php } else { ?>
    <h2><?= NETCAT_MODULE_BILLS_ACT_EDIT ?></h2>
<?php } ?>
<?php if ($type == 'edit' && !$act->get_id()) { ?>
    <?php nc_print_status(NETCAT_MODULE_BILLS_ACT_NOT_FOUND, 'error'); ?>
<?php } else { ?>
    <?php if ($act->get_last_error()) { ?>
        <?php nc_print_status($act->get_last_error(), 'error'); ?>
    <?php } ?>
    <!-- /del -->
    <form action="<?= nc_core()->HTTP_ROOT_PATH; ?>modules/bills/admin/?controller=acts&action=save" method="POST" class="nc-form nc--vertical">
        <input type="hidden" name="id" value="<?= $act->get_id(); ?>"/>

        <div class="nc-form-row"><?= NETCAT_MODULE_BILLS_ACT_NUMBER ?>
            <input type="text" name="number" value="<?= $act->get('number'); ?>" class="nc--small"> от
            <input type="text" name="date" value="<?= $act->get_formatted_date(); ?>" class="nc--medium">
        </div>
        <div class="nc-form-row"><?= NETCAT_MODULE_BILLS_ACT_BY_BILL ?>
            <div class="nc-select">
                <select name="bill_id" style="width:250px">
                    <option value=""><?= NETCAT_MODULE_BILLS_SELECT ?></option>
                    <?php foreach ($bills as $item) { ?>
                        <option value="<?= $item->get_id(); ?>" <?= $item->get_id() == $act->get('bill_id') ? 'selected="selected"' : ''; ?>><?= $item->get('number'); ?> от <?= $item->get_formatted_date(); ?></option>
                    <?php } ?>
                </select>
                <i class="nc-caret"></i>
            </div>
        </div>
        <div class="nc-bill-container"></div>

        <?php if ($type == 'edit') { ?>
            <div style="padding: 20px 0">
                <a href="<?= $act->get_pdf_link(); ?>" target="_blank"><?= NETCAT_MODULE_BILLS_ACT_LINK_TO_PDF ?></a><br>
                <a href="<?= $act->get_pdf_link(true); ?>" target="_blank"><?= NETCAT_MODULE_BILLS_ACT_LINK_TO_PDF_WITH_SIGN ?></a>
            </div>
        <?php } ?>
    </form>
<?php } ?>