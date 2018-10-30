<?php
if (!function_exists('xmlspecialchars')) {
    function xmlspecialchars($text) {
        return str_replace('&#039;', '&apos;', htmlspecialchars($text, ENT_QUOTES));
    }
}

function get_owner_data($source_id) {
    $nc_core = nc_Core::get_object();
    $db = $nc_core->db;

    $source_id = (int)$source_id;
    $sql = "SELECT `catalogue_id` FROM `Netshop_ImportSources` WHERE `source_id` = {$source_id}";
    $catalogue_id = (int)$db->get_var($sql);

    $xml = "";
    if ($catalogue_id) {
        $id = 'netcat_' . $catalogue_id;
        $netshop = nc_netshop::get_instance($catalogue_id);

        if ($netshop->is_netshop_v1_in_use($catalogue_id)) {
            $MODULE_VARS = $nc_core->modules->get_module_vars();
            $shop_table = (int)$MODULE_VARS['netshop']['SHOP_TABLE'];

            $sql = "SELECT `Sub_Class_ID` FROM `Sub_Class` WHERE " .
                "`Catalogue_ID` = {$catalogue_id} " .
                "AND `Class_ID` = {$shop_table} " .
                "ORDER BY `Sub_Class_ID` DESC LIMIT 1";
            $sub_class_id = (int)$db->get_var($sql);

            if ($sub_class_id) {
                $sql = "SELECT `Message_ID`, `ShopName`, `CompanyName`, `Address`, `INN`, `KPP`, " .
                    "`BankName`, `BankAccount`, `CorrespondentAccount`, `BIK` " .
                    "FROM `Message{$shop_table}` WHERE " .
                    "`Sub_Class_ID` = {$sub_class_id} ORDER BY `Message_ID` DESC LIMIT 1";
                $shop_data = $db->get_row($sql, ARRAY_A);

                if ($shop_data) {
                    $name = $shop_data['ShopName'];
                    $fullname = $shop_data['CompanyName'];
                    $address = $shop_data['Address'];
                    $inn = $shop_data['INN'];
                    $kpp = $shop_data['KPP'];
                    $bankname = $shop_data['BankName'];
                    $bankaccount = $shop_data['BankAccount'];
                    $corraccount = $shop_data['CorrespondentAccount'];
                    $bik = $shop_data['BIK'];
                }
            }
        } else {
            $name = $netshop->get_setting('ShopName');
            $fullname = $netshop->get_setting('CompanyName');
            $address = $netshop->get_setting('Address');
            $inn = $netshop->get_setting('INN');
            $kpp = $netshop->get_setting('KPP');
            $bankname = $netshop->get_setting('BankName');
            $bankaccount = $netshop->get_setting('BankAccount');
            $corraccount = $netshop->get_setting('CorrespondentAccount');
            $bik = $netshop->get_setting('BIK');
        }


        $name = xmlspecialchars($name);
        $fullname = xmlspecialchars($fullname);
        $address = xmlspecialchars($address);
        $inn = xmlspecialchars($inn);
        $kpp = xmlspecialchars($kpp);
        $bankname = xmlspecialchars($bankname);
        $bankaccount = xmlspecialchars($bankaccount);
        $corraccount = xmlspecialchars($corraccount);
        $bik = xmlspecialchars($bik);

        $xml = "<Владелец>
    <Ид>{$id}</Ид>
    <Наименование>{$name}</Наименование>
    <ОфициальноеНаименование>{$fullname}</ОфициальноеНаименование>
    <ЮридическийАдрес>
        <Представление>{$address}</Представление>
    </ЮридическийАдрес>
    <ИНН>{$inn}</ИНН>
    <КПП>{$kpp}</КПП>
    <РасчетныеСчета>
        <РасчетныйСчет>
            <НомерСчета>{$bankaccount}</НомерСчета>
            <Банк>
                <СчетКорреспондентский>{$corraccount}</СчетКорреспондентский>
                <Наименование>{$bankname}</Наименование>
                <БИК>{$bik}</БИК>
            </Банк>
        </РасчетныйСчет>
    </РасчетныеСчета>
</Владелец>";
    }

    return $xml;
}