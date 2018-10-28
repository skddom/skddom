<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Счёт на оплату</title>
    <style>
        body { background-color: #fff; color: #000; font: 12px/18px "DejaVu Sans", sans-serif; }
        .wrapper { width: 700px; padding: 15px 10px }
        table { border: 1px solid #000; width: 100% }
    </style>
</head>
<?php
/**
 * @var $bill nc_bills_bill
 * @var $customer nc_bills_company
 * @var $company nc_bills_company
 */
?>
<?php
$positions = $bill->get_positions_array();
$position = $positions[0];
?>
<body>
<div class="wrapper">
    <table CELLSPACING="0" BORDER="1" CELLPADDING="3" bordercolorlight="#000000" bordercolordark="#FFFFFF">
        <tr>
            <td ALIGN="left" WIDTH="200" VALIGN="middle">&nbsp;&nbsp;<b>ИЗВЕЩЕНИЕ</b>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                &nbsp;&nbsp;Кассир<br>
            </td>
            <td ALIGN="right" WIDTH="" VALIGN="middle">
                <table CELLSPACING="0" BORDER="1" CELLPADDING="3" bordercolorlight="#000000" height=100% bordercolordark="#FFFFFF">
                    <tr>
                        <td colspan="3">
                            Получатель платежа: <?= $company->get('opf'); ?> <?= $company->get('name'); ?><br>
                            ИНН: <?= $company->get('inn'); ?><br>
                            Р/c: <?= $company->get('bank_account'); ?>, <?= $company->get('bank_name'); ?><br>
                            Корр.сч.: <?= $company->get('bank_corr_account'); ?><br>
                            БИК: <?= $company->get('bank_bik'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td COLSPAN="3"><br>
                            <?= $bill->get('customer_name'); ?>
                            <?php if ($bill->get('customer_address')) { ?>
                                , <?= $bill->get('customer_address'); ?>
                            <?php } ?>
                            <hr color="#000000" style="height: 1px; border: none; background: #000;">
                            <div align="center" style="font-size: xx-small; position: relative; top: -13px;">фамилия, и.о., адрес</div>
                        </td>
                    </tr>
                    <tr>
                        <td ALIGN="center">Вид платежа</td>
                        <td ALIGN="center" width="15%">Дата</td>
                        <td ALIGN="center" width=23%>Сумма</td>
                    </tr>

                    <tr>
                        <td ALIGN="left"><?= $position['name']; ?></td>
                        <td valign="bottom"><?= $bill->get_formatted_date(); ?></td>
                        <td valign="bottom"><?= $position['formatted_total']; ?></td>
                    </tr>
                    <tr>
                        <td ALIGN="left" colspan="3" valign="center">Плательщик:</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td ALIGN="left" WIDTH="" VALIGN="middle">&nbsp;&nbsp;<b>КВИТАНЦИЯ</b>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                &nbsp;&nbsp;Кассир<br>
            </td>
            <td ALIGN="right" VALIGN="middle">
                <table CELLSPACING="0" BORDER="1" CELLPADDING="3" bordercolorlight="#000000" height=100% bordercolordark="#FFFFFF">
                    <tr>
                        <td colspan="3">
                            Получатель платежа: <?= $company->get('name'); ?><br>
                            ИНН: <?= $company->get('inn'); ?><br>
                            Р/c: <?= $company->get('bank_account'); ?>, <?= $company->get('bank_name'); ?><br>
                            Корр.сч.: <?= $company->get('bank_corr_account'); ?><br>
                            БИК: <?= $company->get('bank_bik'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td COLSPAN="3"><br>
                            <?= $bill->get_physical_customer(); ?>
                            <hr color="#000000" style="height: 1px; border: none; background: #000;">
                            <div align="center" style="font-size: xx-small; position: relative; top: -13px;">фамилия, и.о., адрес</div>
                        </td>
                    </tr>
                    <tr>
                        <td ALIGN="center">Вид платежа</td>
                        <td ALIGN="center" width="15%">Дата</td>
                        <td ALIGN="center" width=23%>Сумма</td>
                    </tr>

                    <tr>
                        <td ALIGN="left"><?= $position['name']; ?></td>
                        <td valign="bottom"><?= $bill->get_formatted_date(); ?></td>
                        <td valign="bottom"><?= $position['formatted_total']; ?></td>
                    </tr>
                    <tr>
                        <td ALIGN="left" colspan="3" valign="center">Плательщик:</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>