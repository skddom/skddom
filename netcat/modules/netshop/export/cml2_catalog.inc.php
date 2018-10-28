<?php
require_once('cml2_misc.inc.php');

if (!function_exists('xmlspecialchars')) {
    function xmlspecialchars($text) {
        return str_replace('&#039;', '&apos;', htmlspecialchars($text, ENT_QUOTES));
    }
}

class CML2_Catalog_Export {
    private $source_id;

    private $nc_core;

    private $db;

    public function __construct($source_id, nc_Core $nc_core) {
        $this->source_id = (int)$source_id;
        $this->nc_core = $nc_core;
        $this->db = $nc_core->db;
    }

    public function export($with_headers = false) {
        global $TMP_FOLDER;

        $tmp_dir = $TMP_FOLDER . '1c_export_' . md5(uniqid('', true));
        $images_tmp_dir = $tmp_dir . '/import_files';

        mkdir($tmp_dir, 0777);

        $source_id = $this->source_id;
        $nc_core = $this->nc_core;
        $db = $this->db;

        $source_data = $db->get_row("SELECT *
	FROM `Netshop_ImportSources`
	WHERE `source_id` = '" . $source_id . "'", ARRAY_A);

        // classifier_id catalog_id
        list($classifier_id, $catalog_id) = explode(' ', $source_data['external_id']);

        $owner_data = get_owner_data($source_id);

        ob_start();

        echo '<?xml version="1.0" encoding="' . $nc_core->NC_CHARSET . '"?>' . PHP_EOL;
        ?>
        <КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="<?= date("Y-m-d"); ?>T<?= date("H:i:s"); ?>">
            <Классификатор>
                <Ид><?= $classifier_id ?></Ид>
                <Наименование><?= xmlspecialchars($source_data['name']) ?></Наименование>
                <?php echo $owner_data; ?>
                <?php
                $sections_data = $db->get_results("SELECT nim.*, sub.`Parent_Sub_ID`, sub.`Subdivision_Name`
	FROM `Netshop_ImportMap` AS nim
	LEFT JOIN `Subdivision` AS sub ON nim.`value` = sub.`Subdivision_ID`
	WHERE nim.`source_id` = '" . $source_id . "'
		AND nim.`type` = 'section'
	ORDER BY sub.`Parent_Sub_ID`", ARRAY_A);

                $sections_tree = array();

                foreach ($sections_data as $row) {
                    $sections_tree[$row['Parent_Sub_ID']][] = $row;
                }

                $netshop = nc_netshop::get_instance($source_data['catalogue_id']);

                if ($netshop->is_netshop_v1_in_use($source_data['catalogue_id'])) {
                    $MODULE_VARS = $nc_core->modules->get_vars('netshop');
                    $shop = GetSubdivisionByType($MODULE_VARS["SHOP_TABLE"], "Subdivision_ID, Subdivision_Name", $source_data['catalogue_id']);
                    $root_subdivision_id = $shop['Subdivision_ID'];
                    $goods_tables_raw = explode(',', $MODULE_VARS["GOODS_TABLE"]);
                } else {
                    $root_subdivision_id = $source_data['root_subdivision_id'];
                    $goods_tables_raw = $netshop->get_goods_components_ids();
                }

                $goods_tables = array();

                foreach ($goods_tables_raw as $goods_table) {
                    $goods_table = (int)$goods_table;
                    if ($goods_table && !in_array($goods_table, $goods_tables)) {
                        $goods_tables[] = $goods_table;
                    }
                }

                // shop's subdivision data
                function export_groups_tree($data, $parent_sub_id = 0, $level = 0) {
                    foreach ($data[$parent_sub_id] as $row) {
                        echo str_repeat('  ', $level) . '      <Группа>' . PHP_EOL;
                        echo str_repeat('  ', $level) . '        <Ид>' . $row['source_string'] . '</Ид>' . PHP_EOL;
                        echo str_repeat('  ', $level) . '        <Наименование>' . xmlspecialchars($row['Subdivision_Name']) . '</Наименование>' . PHP_EOL;
                        if (isset($data[$row['value']]))
                            export_groups_tree($data, $row['value'], $level + 1);
                        echo str_repeat('  ', $level) . '      </Группа>' . PHP_EOL;
                    }
                }

                ?>
                <Группы>
                    <?php export_groups_tree($sections_tree, $root_subdivision_id); ?>
                </Группы>
                <?php ?>
            </Классификатор>
            <Каталог СодержитТолькоИзменения="false">
                <Ид><?= $catalog_id ?></Ид>
                <ИдКлассификатора><?= $classifier_id ?></ИдКлассификатора>
                <Наименование><?= xmlspecialchars($source_data['name']) ?></Наименование>
                <?php echo $owner_data; ?>
                <Товары>
                    <?php

                    foreach ($goods_tables as $goods_table) {
                    $properties_data = $db->get_results("SELECT nim.*, fld.`Field_ID`, fld.`Field_Name`, fld.`TypeOfData_ID`
	FROM `Netshop_ImportMap` AS nim
	LEFT JOIN `Field` AS fld ON nim.`value` = fld.`Field_ID`
	WHERE nim.`source_id` = '" . $source_id . "'
		AND nim.`type` = 'property'
		AND fld.`Class_ID` = {$goods_table}
	ORDER BY nim.`parent_tag`", ARRAY_A);

                    $goods_data = (array)$db->get_results("SELECT *
	FROM `Message{$goods_table}`
	WHERE `ImportSourceID` = '" . $source_id . "'", ARRAY_A);

                    $sub_1c_rel = array();
                    foreach ($sections_data as $row) {
                        $sub_1c_rel[$row['value']] = $row['source_string'];
                    }

                    foreach ($goods_data as $row){
                    ?>
                    <Товар>
                        <Ид><?= $row['ItemID']; ?></Ид>
                        <Группы>
                            <Ид><?= $sub_1c_rel[$row['Subdivision_ID']] ?></Ид>
                        </Группы>
                        <?php $parent_tag = ''; ?>
                        <?php foreach ($properties_data as $r){ ?>
                        <?php
                        if ($r['value'] == -1 || $r['source_string'] == 'Ид') {
                            continue;
                        }
                        if ($r['parent_tag'] == "ЗначенияСвойств") {
                            $r['parent_tag'] = '';
                        }
                        ?>
                        <?php if ($parent_tag != $r['parent_tag']){ ?>
                        <?php if ($parent_tag) { ?>
                    </<?= $parent_tag ?>>
                <?php } ?>
                <?php $parent_tag = $r['parent_tag']; ?>
                <?php if ($parent_tag) { ?>
                    <<?= $parent_tag ?>>
                <?php } ?>
                <?php } ?>
                    <?php if ($parent_tag){ ?>
                        <?php if ($parent_tag == 'ХарактеристикиТовара') { ?>
                            <ХарактеристикаТовара>
                                <Наименование><?= xmlspecialchars($r['source_string']) ?></Наименование>
                                <Значение><?= xmlspecialchars($row[$r['Field_Name']]) ?></Значение>
                            </ХарактеристикаТовара>
                        <?php } else if ($parent_tag == 'ЗначенияРеквизитов') { ?>
                            <ЗначениеРеквизита>
                                <Наименование><?= xmlspecialchars($r['source_string']) ?></Наименование>
                                <Значение><?= xmlspecialchars($row[$r['Field_Name']]) ?></Значение>
                            </ЗначениеРеквизита>
                        <?php } else if ($parent_tag == 'СтавкиНалогов') { ?>
                            <СтавкаНалога>
                                <Наименование><?= xmlspecialchars($r['source_string']) ?></Наименование>
                                <Ставка><?= xmlspecialchars($row[$r['Field_Name']]) ?></Ставка>
                            </СтавкаНалога>
                        <?php } ?>
                    <?php } else { ?>
                    <?php
                    if ($r['TypeOfData_ID'] == 6) {
                        $file_info = nc_core('file_info')->get_file_info($goods_table, $row['Message_ID'], $r['Field_Name'], false);
                        if (!file_exists($images_tmp_dir)) {
                            mkdir($images_tmp_dir, 0777);
                        }
                        $file_name = $file_info['name'];
                        $extension = explode('.', $file_name);
                        $extension = strtolower($extension[count($extension) - 1]);
                        $random_name = md5(uniqid('', true));
                        if ($extension) {
                            $random_name .= '.' . $extension;
                        }
                        copy($nc_core->FILES_FOLDER . '..' . $file_info['url'], $images_tmp_dir . '/' . $random_name);

                        $row[$r['Field_Name']] = 'import_files/' . $random_name;
                    } else if ($r['TypeOfData_ID'] == 11) {
                        $sql = "SELECT `Path` FROM `Multifield` WHERE `Field_ID` = {$r['Field_ID']} AND `Message_ID` = {$row['Message_ID']} ORDER BY `Priority`";
                        $files = (array)$db->get_col($sql);
                        if (!file_exists($images_tmp_dir)) {
                            mkdir($images_tmp_dir, 0777);
                        }
                        foreach ($files as $index => $file) {
                            $file_name = $file;
                            $extension = explode('.', $file_name);
                            $extension = strtolower($extension[count($extension) - 1]);
                            $random_name = md5(uniqid('', true));
                            if ($extension) {
                                $random_name .= '.' . $extension;
                            }
                            copy($nc_core->FILES_FOLDER . '..' . $file, $images_tmp_dir . '/' . $random_name);
                            $files[$index] = $random_name;
                        }
                        $row[$r['Field_Name']] = $files;
                    }
                    ?>
                    <<?= $r['source_string'] ?>>
                    <?php if (is_array($row[$r['Field_Name']])) { ?>
                        <?php
                        foreach ($row[$r['Field_Name']] as $index => $value) {
                            $row[$r['Field_Name']][$index] = xmlspecialchars($value);
                        }
                        echo implode('</' . $r['source_string'] . '><' . $r['source_string'] . '>', $row[$r['Field_Name']]);
                        ?>
                    <?php } else { ?>
                        <?= xmlspecialchars($row[$r['Field_Name']]) ?>
                    <?php } ?>
                </<?= $r['source_string'] ?>>
            <?php } ?>
            <?php } ?>
                <?php if ($parent_tag){ ?>
            </<?= $parent_tag ?>>
            <?php } ?>
            </Товар>
            <?php } ?>
            <?php } ?>
            </Товары>
            </Каталог>
        </КоммерческаяИнформация>
        <?php
        $import = ob_get_clean();

        file_put_contents($tmp_dir . '/import.xml', $import);

        $zip = new ZipArchive();
        $zip->open($tmp_dir . '/import.zip', ZIPARCHIVE::CREATE);
        $zip->addFile($tmp_dir . '/import.xml', 'import.xml');
        if (file_exists($images_tmp_dir)) {
            $files = (array)scandir($images_tmp_dir);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $zip->addFile($tmp_dir . '/import_files/' . $file, 'import_files/' . $file);
                }
            }

        }
        $zip->close();

        if ($with_headers) {
            // set headers
            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=import.zip");
            echo file_get_contents($tmp_dir . '/import.zip');
            nc_delete_dir($tmp_dir);
        }
    }
}