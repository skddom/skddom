<?php
// make user's undivine
@ignore_user_abort(true);

// load system
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)) . (strstr(__FILE__, "/") ? "/" : "\\");
include_once($NETCAT_FOLDER . "vars.inc.php");
require_once($ROOT_FOLDER . "connect_io.php");


if (is_file($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php")) {
    require_once($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php");
} else {
    require_once($MODULE_FOLDER . "netshop/en.lang.php");
}

@set_time_limit(0);

include_once($INCLUDE_FOLDER . "index.php");

// system superior object
$nc_core = nc_Core::get_object();

$source_data = $nc_core->db->get_row("SELECT *
	FROM `Netshop_ImportSources`
	WHERE `source_id` = '" . $source_id . "'", ARRAY_A);

// classifier_id catalog_id
list($classifier_id, $catalog_id) = explode(' ', $source_data['external_id']);

$netshop = nc_netshop::get_instance($source_data['catalogue_id']);
if ($netshop->is_netshop_v1_in_use($source_data['catalogue_id'])) {
    $MODULE_VARS = $nc_core->modules->get_module_vars();
    $goods_tables_raw = explode(',', $MODULE_VARS['netshop']["GOODS_TABLE"]);
} else {
    $goods_tables_raw = $netshop->get_goods_components_ids();
}

$goods_tables = array();

foreach ($goods_tables_raw as $goods_table) {
    $goods_table = (int)$goods_table;
    if ($goods_table && !in_array($goods_table, $goods_tables)) {
        $goods_tables[] = $goods_table;
    }
}

if (!function_exists('xmlspecialchars')) {
    function xmlspecialchars($text) {
        return str_replace('&#039;', '&apos;', htmlspecialchars($text, ENT_QUOTES));
    }
}

require_once('cml2_misc.inc.php');

// set headers
header("Content-Type: application/xml");
header("Content-Disposition: attachment; filename=offers.xml");

echo '<?xml version="1.0" encoding="' . $nc_core->NC_CHARSET . '"?>' . PHP_EOL;
?>
<КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="<?= date("Y-m-d"); ?>T<?= date("H:i:s"); ?>">
    <ПакетПредложений СодержитТолькоИзменения="false">
        <Ид><?= $catalog_id; ?></Ид>
        <Наименование><?= xmlspecialchars($source_data['name']) ?></Наименование>
        <ИдКаталога><?= $catalog_id ?></ИдКаталога>
        <ИдКлассификатора><?= $classifier_id ?></ИдКлассификатора>
        <?php echo get_owner_data($source_id); ?>
        <?php
        $_curr_data = $nc_core->db->get_results("SELECT *
	FROM `Classificator_ShopCurrency`", ARRAY_A);
        $curr_data = array();
        foreach ($_curr_data as $row) {
            $curr_data[$row['ShopCurrency_ID']] = $row['ShopCurrency_Name'];
        }

        $_units_data = $nc_core->db->get_results("SELECT *
	FROM `Classificator_ShopUnits`", ARRAY_A);
        $units_data = array();
        foreach ($_units_data as $row) {
            $units_data[$row['ShopUnits_ID']] = $row['ShopUnits_Name'];
        }

        $stores = array();

        $sql = "SELECT `Netshop_Store_ID`, `Import_Store_ID`, `Name` FROM `Netshop_Stores` WHERE `Import_Source_ID` = {$source_id} ORDER BY `Netshop_Store_ID` ASC";
        $result = (array)$nc_core->db->get_results($sql, ARRAY_A);

        foreach ($result as $row) {
            $stores[$row['Netshop_Store_ID']] = $row;
        }

        $price_data = (array)$nc_core->db->get_results("SELECT nim.*, fld.`Field_Name`, fld.`Description`
	FROM `Netshop_ImportMap` AS nim
	LEFT JOIN `Field` AS fld ON nim.`value` = fld.`Field_Name`
	WHERE nim.`source_id` = '" . $source_id . "'
		AND nim.`type` = 'price'
		AND fld.`Class_ID` = '{$goods_table}'", ARRAY_A);
        ?>
        <ТипыЦен>
            <?php foreach ($price_data as $row) { ?>
                <ТипЦены>
                    <Ид><?= $row['source_string'] ?></Ид>
                    <Наименование><?= xmlspecialchars($row['Description']) ?></Наименование>
                    <Валюта><?=
                        $curr_data[ /* sry */
                        $nc_core->db->get_var("SELECT `Currency` FROM `Message{$goods_table}` WHERE `" . $row['Field_Name'] . "` > 0")] ?></Валюта>
                </ТипЦены>
            <?php } ?>
        </ТипыЦен>
        <?php ?>
        <Предложения>
            <?php

            foreach ($goods_tables as $goods_table) {
                $properties_data = $nc_core->db->get_results("SELECT nim.*, fld.`Field_Name`
	FROM `Netshop_ImportMap` AS nim
	LEFT JOIN `Field` AS fld ON nim.`value` = fld.`Field_ID`
	WHERE nim.`source_id` = '" . $source_id . "'
		AND nim.`type` = 'oproperty'
		AND fld.`Class_ID` = {$goods_table}
	ORDER BY nim.`parent_tag`", ARRAY_A);

                $goods_data = (array)$nc_core->db->get_results("SELECT *
	FROM `Message{$goods_table}`
	WHERE `ImportSourceID` = '" . $source_id . "'", ARRAY_A);

                foreach ($goods_data as $row) {
                    $message_id = (int)$row['Message_ID'];

                    $item_stores = array();
                    $total_quantity = 0;

                    foreach ($stores as $store_id => $store) {
                        $store_id = (int)$store_id;
                        $sql = "SELECT `Quantity` FROM `Netshop_StoreGoods` WHERE " .
                            "`Netshop_Store_ID` = {$store_id} " .
                            "AND `Class_ID` = {$goods_table} " .
                            "AND `Netshop_Item_ID` = {$message_id}";

                        $quantity = (float)$nc_core->db->get_var($sql);

                        if ($quantity != 0) {
                            $item_stores[] = array(
                                'Name' => $store['Name'],
                                'Import_Store_ID' => $store['Import_Store_ID'],
                                'Quantity' => $quantity,
                            );

                            $total_quantity += $quantity;
                        }
                    }


                    ?>
                    <Предложение>
                    <Ид><?= $row['ItemID'] ?></Ид>
                    <Наименование><?=
                        xmlspecialchars($row['Name'])
                        ?></Наименование>
                    <?php foreach ($properties_data as $r) { ?>
                        <?php
                        if (
                            (!trim($row[$r['Field_Name']]) && $r['source_string'] != 'Количество') ||
                            (count($stores) > 0 && $r['source_string'] == 'Количество')
                        ) {
                            continue;
                        }
                        ?>
                        <<?= $r['source_string'] ?>><?= xmlspecialchars($row[$r['Field_Name']]) ?></<?= $r['source_string'] ?>>
                    <?php } ?>
                    <Цены>
                        <?php foreach ($price_data as $ro) { ?>
                            <?php if ($ro['value'] == -1)
                                continue; ?>
                            <Цена>
                                <Представление><?= $row[$ro['value']] ?> <?= $curr_data[$row['Currency']] ?></Представление>
                                <ИдТипаЦены><?= $ro['source_string'] ?></ИдТипаЦены>
                                <ЦенаЗаЕдиницу><?= $row[$ro['value']] ?></ЦенаЗаЕдиницу>
                                <Валюта><?= $curr_data[$row['Currency']] ?></Валюта>
                                <?php if (isset($units_data[$row['Units']])) { ?>
                                    <Единица><?= $units_data[$row['Units']] ?></Единица>
                                <?php } ?>
                                <Коэффициент>1</Коэффициент>
                            </Цена>
                        <?php } ?>
                    </Цены>
                    <?php if (count($stores) > 0) { ?>
                        <Количество><?= $total_quantity; ?></Количество>
                        <?php if (count($item_stores) > 0) { ?>
                            <ОстаткиПоСкладам>
                                <?php foreach ($item_stores as $store) { ?>
                                    <Остаток>
                                        <Склад><?= xmlspecialchars($store['Name']); ?></Склад>
                                        <СкладИД><?= xmlspecialchars($store['Import_Store_ID']); ?></СкладИД>
                                        <КоличествоОстаток><?= $store['Quantity']; ?></КоличествоОстаток>
                                    </Остаток>
                                <?php } ?>
                            </ОстаткиПоСкладам>
                        <?php } ?>
                    <?php } ?>
                    </Предложение>
                <?php } ?>
            <?php } ?>
        </Предложения>
    </ПакетПредложений>
</КоммерческаяИнформация>
