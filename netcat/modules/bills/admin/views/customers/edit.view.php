<?php
if (!class_exists('nc_core')) {
    die;
}
/**
 * @var $company nc_bills_company
 */
?>
<?php if ($company) { ?>
    <?php if ($type == 'add') { ?>
        <h2><?= NETCAT_MODULE_BILLS_CLIENT_ADD?></h2>
    <?php } else { ?>
        <h2><?= NETCAT_MODULE_BILLS_CLIENT_EDIT?></h2>
    <?php } ?>
    <form action="<?= nc_core()->HTTP_ROOT_PATH; ?>modules/bills/admin/?controller=customers&action=save" method="POST" class="nc-form nc--vertical">
        <input type="hidden" name="id" value="<?= $company->get_id(); ?>"/>

        <div class="nc-form-row"><label><?= NETCAT_MODULE_BILLS_CLIENT_OPF?><br>

                <div class="nc-select">
                    <select style="width:150px" name="opf">
                        <?
                        $opf = array(
                            'ИП',
                            'ООО',
                            'ОАО',
                        );
                        ?>
                        <option value=""><?= NETCAT_MODULE_BILLS_SELECT?></option>
                        <?php foreach ($opf as $value) { ?>
                            <option value="<?= $value; ?>" <?= ($company->get('opf') == $value) ? 'selected="selected"' : ''; ?>><?= $value; ?></option>
                        <?php } ?>
                    </select>
                    <i class="nc-caret"></i>
                </div>
            </label>
        </div>
        <div class="nc-form-row">
            <label><?= NETCAT_MODULE_BILLS_CLIENT_NAME?><br><input type="text" name="name" class="nc--xlarge" value="<?= $company->get('name'); ?>"></label>
        </div>
        <div class="nc-form-row">
            <label><?= NETCAT_MODULE_BILLS_CLIENT_LEGAL_ADDRESS?><br><input type="text" name="address" class="nc--xlarge" value="<?= $company->get('address'); ?>"></label>
        </div>
        <div class="nc-form-row">
            <label><?= NETCAT_MODULE_BILLS_CLIENT_PHONE?><br><input type="text" name="phone" class="nc--large" value="<?= $company->get('phone'); ?>"></label>
        </div>
        <div class="nc-form-row">
            <label><?= NETCAT_MODULE_BILLS_CLIENT_INN?><br><input type="text" name="inn" class="nc--large" value="<?= $company->get('inn'); ?>"></label>
        </div>
        <div class="nc-form-row">
            <label><?= NETCAT_MODULE_BILLS_CLIENT_KPP?><br><input type="text" name="kpp" class="nc--large" value="<?= $company->get('kpp'); ?>"></label>
        </div>
        <h2><?= NETCAT_MODULE_BILLS_CLIENT_PAYMENT_DETAILS?></h2>

        <div class="nc-form-row">
            <label><?= NETCAT_MODULE_BILLS_CLIENT_BANK_NAME?><br><input type="text" name="bank_name" class="nc--large" value="<?= $company->get('bank_name'); ?>"></label>
        </div>
        <div class="nc-form-row">
            <label><?= NETCAT_MODULE_BILLS_CLIENT_BANK_CURRENT_ACCOUNT?><br><input type="text" name="bank_account" class="nc--large" value="<?= $company->get('bank_account'); ?>"></label>
        </div>
        <div class="nc-form-row">
            <label><?= NETCAT_MODULE_BILLS_CLIENT_BANK_CORRESPONDENT_ACCOUNT?><br><input type="text" name="bank_corr_account" class="nc--large" value="<?= $company->get('bank_corr_account'); ?>"></label>
        </div>
        <div class="nc-form-row">
            <label><?= NETCAT_MODULE_BILLS_CLIENT_BANK_INN?><br><input type="text" name="bank_inn" class="nc--large" value="<?= $company->get('bank_inn'); ?>"></label>
        </div>
        <div class="nc-form-row">
            <label><?= NETCAT_MODULE_BILLS_CLIENT_BANK_BIK?><br><input type="text" name="bank_bik" class="nc--large" value="<?= $company->get('bank_bik'); ?>"></label>
        </div>
    </form>
<?php } else { ?>
    <?php nc_print_status(NETCAT_MODULE_BILLS_CLIENT_NOT_FOUND, 'error'); ?>
<?php } ?>