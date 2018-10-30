<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Счёт на оплату</title>
    <style>
        body { background-color: #fff; color: #000; font: 12px/18px "DejaVu Sans", sans-serif; }
        .wrapper { width: 700px; padding: 15px 10px }
        table { border: 1px solid #000; width: 100% }
        td { padding: 2px 3px; vertical-align: top }
        hr { border: none }
        hr { border-bottom: 1px #000 solid }
        .header { height: 150px; line-height: 22px }
        .border-r { border-right: 1px solid #000 }
        .border-b { border-bottom: 1px solid #000 }
        .border-l { border-left: 1px solid #000 }
        .border-n { border: 0 }
        .table-list { border-bottom: 0; border-left: 0 }
        .table-footer { border: 0; margin-top: 50px }
        .table-footer td { padding-right: 30px }
        .table-footer td p { font: 10px/10px "DejaVu Sans", sans-serif }
        .text-center { text-align: center }
        .bill-caption { margin: 20px 0 25px; text-align: center; font: bold 20px/30px "DejaVu Sans", sans-serif }
        .act-caption { margin-bottom: 10px; font: bold 20px/30px "DejaVu Sans", sans-serif; border-bottom: 1px solid #000 }
        .mb10px { margin-bottom: 10px }
        .pt50px td { padding-top: 50px }
        .prop-table { }
        .prop-caption td { font: bold 16px/30px "DejaVu Sans", sans-serif; width: 50% }
        .prop-name td { padding-bottom: 30px }
        .padding-left { padding-left: 7px; }
        .padding-top { padding-top: 7px; }
        .result { margin-bottom: 30px; }
        .signs { page-break-inside: avoid; }
        .sign-accountant { position: absolute; right: 20px; z-index: 2; }
        .sign-accountant img { position: absolute; left: 78px; top: -22px; }
        .sign-director { position: relative; z-index: 2; }
        .sign-director img { position: absolute; left: 185px; top: -22px; }
        .stamp { position: relative; }
        .stamp img { margin-top: -40px; margin-right: -20px; }
    </style>
</head>
<?php
/**
 * @var $bill nc_bills_bill
 * @var $customer nc_bills_company
 * @var $company nc_bills_company
 */
?>
<body>
<div class="wrapper">
    <?php if ($logo) { ?>
        <img src="<?= $logo; ?>" alt=""/><br>
    <?php } ?>
    <div class="header">
        <strong><?= $company->get('opf'); ?> <?= $company->get('name'); ?></strong><br>
        ИНН: <?= $company->get('inn'); ?><br>
        <?= $company->get('address'); ?><br>
        Телефон: <?= $company->get('phone'); ?><br>

    </div>
    <table cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
            <td class="border-b padding-left padding-top" width="120">ИНН <?= $company->get('inn'); ?></td>
            <td class="border-r border-b padding-top">КПП <?= $company->get('kpp'); ?></td>
            <td class="border-r" width="50">&nbsp;</td>
            <td width="230">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2" rowspan="1" class="border-r padding-left padding-top">Получатель</td>
            <td class="border-r">&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2" rowspan="1" class="border-r border-b padding-left"><?= $company->get('opf'); ?> <?= $company->get('name'); ?></td>
            <td class="border-r border-b padding-left">Сч. №</td>
            <td class="border-b padding-left"><?= $company->get('bank_account'); ?></td>
        </tr>
        <tr>
            <td colspan="2" rowspan="1" class="border-r padding-left padding-top">Банк получателя</td>
            <td class="border-r border-b padding-left padding-top">БИК</td>
            <td class="border-b padding-left padding-top"><?= $company->get('bank_bik'); ?></td>
        </tr>
        <tr>
            <td colspan="2" rowspan="1" class="border-r padding-left"><?= $company->get('bank_name'); ?></td>
            <td class="border-r padding-left">Сч. №</td>
            <td class="padding-left"><?= $company->get('bank_corr_account'); ?></td>
        </tr>
        </tbody>
    </table>

    <div class="bill-caption">Счёт №<?= $bill->get('number'); ?> от <?= $bill->get_formatted_date(); ?></div>

    <table class="border-n" cellspacing="0" cellpadding="0">
        <tr>
            <td width="100">Плательщик</td>
            <td><?= $customer->get('opf'); ?> <?= $customer->get('name'); ?>, <?= $customer->get('address'); ?>, ИНН <?= $customer->get('inn'); ?>, КПП <?= $customer->get('kpp'); ?>, р/сч <?= $customer->get('bank_account'); ?>, банк <?= $customer->get('bank_name'); ?>, кор.счет <?= $customer->get('bank_corr_account'); ?>, БИК <?= $customer->get('bank_bik'); ?></td>
        </tr>
    </table>

    <table cellspacing="0" cellpadding="5" class="table-list">
        <thead style="background: #ffc">
        <tr>
            <td width="1%" class="border-r border-b border-l">№</td>
            <td class="border-r border-b">Наименование товара, работ, услуг</td>
            <td width="50" class="border-r border-b">Ед. изм.</td>
            <td width="50" class="border-r border-b">Кол-во</td>
            <td width="70" class="border-r border-b">Цена</td>
            <td width="70" class="border-b">Сумма</td>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        foreach ($bill->get_positions_array() as $position) {
            $i++;
            ?>
            <tr>
                <td class="border-r border-b border-l"><?= $i; ?></td>
                <td class="border-r border-b"><?= $position['name']; ?></td>
                <td class="border-r border-b"><?= $position['unit']; ?></td>
                <td class="border-r border-b"><?= $position['amount']; ?></td>
                <td class="border-r border-b"><?= $position['formatted_sum']; ?>р.</td>
                <td class="border-b"><?= $position['formatted_total']; ?>р.</td>
            </tr>
        <?php } ?>

        <tr>
            <td colspan="3" rowspan="1">&nbsp;</td>
            <td colspan="2" class="border-r" align="right"><strong>Итого без НДС:</strong></td>
            <td class="border-b"><?= $bill->get_formatted_sum_without_vat(); ?>р.</td>
        </tr>
        <tr>
            <td colspan="3" rowspan="1">&nbsp;</td>
            <td colspan="2" class="border-r" align="right"><strong>Итого НДС</strong></td>
            <?php
            $vat_sum = $bill->get_formatted_vat_sum();
            ?>
            <td class="border-b"><?= $vat_sum ? $vat_sum : '&mdash;'; ?></td>
        </tr>
        <tr>
            <td colspan="3" rowspan="1">&nbsp;</td>
            <td colspan="2" class="border-r" align="right"><strong>Всего к оплате:</strong></td>
            <td class="border-b"><?= $bill->get_formatted_sum(); ?>р.</td>
        </tr>
        </tbody>
    </table>

    <div class="result">
        Всего наименований <?= $bill->get_positions_count(); ?>, на сумму <?= $bill->get_formatted_sum(); ?> р.<br>
        <strong><?= $bill->get_sum_words(); ?></strong>
    </div>

    <div class="signs">
        <div class="sign-accountant">
            Бухгалтер: ______________________
            <?php if ($accountant_sign) { ?>
                <img height="40" src="<?= $accountant_sign; ?>" alt=""/>
            <?php } ?>
        </div>
        <div class="sign-director" style="">
            Руководитель предприятия: ______________________
            <?php if ($director_sign) { ?>
                <img height="40" src="<?= $director_sign; ?>" alt=""/>
            <?php } ?>
        </div>
        <div class="stamp" style="">
            <?php if ($stamp) { ?>
                <img width="170" src="<?= $stamp; ?>" alt=""/>
            <?php } ?>
        </div>
    </div>
</div>
</body>
</html>