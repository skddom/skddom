<?php

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require_once ($ROOT_FOLDER."connect_io.php");

header("Content-type: text/xml");
$catalogue = $nc_core->catalogue->get_by_host_name($HTTP_HOST);
$catalogue = $catalogue["Catalogue_ID"];
if (!$catalogue) $catalogue = 1;

if (is_file($MODULE_FOLDER."netshop/".MAIN_LANG.".lang.php")) {
    require_once($MODULE_FOLDER."netshop/".MAIN_LANG.".lang.php");
    $modules_lang = "Russian";
} else {
    require_once($MODULE_FOLDER."netshop/en.lang.php");
    $modules_lang = "English";
}

class Netshop_ExportTorgMail extends nc_mod_netshop {

    private $_CurrencyArray;
    private $_class_ids;

    public function __construct() {
        global $nc_core;

        parent::__construct();
        $this->_CurrencyArray = Array('RUR', 'USD', 'EURO');
        $this->_class_ids = $nc_core->modules->get_vars('netshop', 'GOODS_TABLE');
    }

    /**
     * Экспорт в формате XML
     * @param int раздел, который надо экспортировать (по умолчанию - весь магазин)
     */
    public function Export($section=0) {
        global $HTTP_HOST, $SUB_FOLDER;
        global $db;
        global $nc_core;
        global $catalogue;

        if (!$this->shop_id) return false;
        $shopName = $this->ShopName;
        $default_currency = $this->Currencies[$this->DefaultCurrencyID];

        header("Content-type: text/xml");
        $ret = "<?xml version=\"1.0\" encoding=\"".$nc_core->NC_CHARSET."\"?>
              <torg_price date=\"".(strftime("%Y-%m-%d %H:%M"))."\">
              <shop>
                <update_type>0</update_type>
                <shopname>".xmlspecialchars($shopName)."</shopname>
                <company>".xmlspecialchars($this->CompanyName)."</company>
                <url>" . nc_get_scheme() . '://' . $HTTP_HOST . $SUB_FOLDER . "/</url>
                  <currencies>
                    <currency id=\"".$default_currency."\" rate=\"1\" />";
        foreach ((array) $this->Currencies as $k => $v) {
            if ($v != $default_currency && $this->Rates[$k] && in_array($v, $this->_CurrencyArray)) {
                $ret .= "<currency id=\"$v\" rate=\"".$this->Rates[$k]."\" />";
            }
        }

        $ret .= "</currencies>
              <categories>\n";

        // output categories (shop structure) ---------------------------
        if (!$section) $section = $this->_class_ids;
        $structure = GetStructureYandexml($section, $catalogue);
        if (!$structure) return;

        $all_sections_ids = array(); // потом вытащим на основе этих данных товары

        foreach ($structure as $row) {
            $ret .= "<category id=\"$row[Subdivision_ID]\"".
                    ($row["Parent_Sub_ID"] == $section ? "" : " parentId=\"$row[Parent_Sub_ID]\"").
                    ">".xmlspecialchars($row["Subdivision_Name"])."</category>\n";

            $all_sections_id[] = $row["Subdivision_ID"];
        }

        $ret .= "</categories>\n<offers>";

        // GOODS CATALOGUE -----------------------------------------------
        $output = array(
                "URL" => "url",
                "Price" => "price",
                "CurrencyID" => "currencyId",
                "Subdivision_ID" => "categoryId",
                "Image" => "picture",
                "Name" => "name",
                "Vendor" => "vendor",
                "Description" => "description"
        );

        // получить типы товаров
        $goods_class_ids = $this->GuessGoodsTypeIDs();

        // все разделы магазина
        $subdivision_id = join(",", $all_sections_id);

        foreach ($goods_class_ids as $class_id) {
            $query = "SELECT m.*,
                         ShopCurrency_Name AS CurrencyID,
                         CONCAT(u.Hidden_URL, s.EnglishName, '_', m.Message_ID, '.html') as URL,
                         IFNULL(m.$this->PriceColumn, parent.$this->PriceColumn) as Price4User,
                         IF(m.$this->PriceColumn IS NULL, parent.$this->CurrencyColumn, m.$this->CurrencyColumn) as Currency4User

                FROM (`Message".$class_id."` as m, `Subdivision` as u, `Sub_Class` as s)
                  LEFT JOIN Message".$class_id." as parent
                    ON (m.`Parent_Message_ID` != 0 AND m.`Parent_Message_ID` = parent.`Message_ID`)
                  LEFT JOIN `Classificator_ShopCurrency`
                    ON Classificator_ShopCurrency.`ShopCurrency_ID` = m.`Currency`
                WHERE m.`Checked` = 1
                    AND m.`Subdivision_ID` IN (".$subdivision_id.")
                    AND s.`Subdivision_ID` = m.`Subdivision_ID`
                    AND s.`Sub_Class_ID` = m.`Sub_Class_ID`
                    AND u.`Subdivision_ID` = m.`Subdivision_ID`
                HAVING `Price4User` > 0
                    ";

            $rows = $db->get_results($query, ARRAY_A);
            foreach ((array) $rows as $row) {

                // convert to default currency
                $row["Price"] = $this->ConvertCurrency($row["Price4User"], $row["Currency4User"]);
                // we'll need an absolute url
                $row["URL"] = nc_get_scheme() . '://' . $HTTP_HOST . $SUB_FOLDER . "$row[URL]";

                if ($row["Image"]) { // replace to image url
                    $row["Image"] = nc_get_scheme() . '://' . $HTTP_HOST . $SUB_FOLDER . nc_file_path($class_id, $row["Message_ID"], "Image", "h_");
                }

                $ret .= "<offer id=\"".sprintf("%d%05d", $class_id, $row["Message_ID"])."\" type=\"good\">\n";


                $curr_comp = new nc_Component($class_id);
                $fields = $curr_comp->get_fields();
                foreach ($fields as $f) {
                    $fields_assoc[$f['name']] = $f;
                }

                $classificators;

                foreach ($output as $idx => $tag) {
                    if ($row[$idx]) {

                        $value = $row[$idx];

                        if ($fields_assoc[$idx]['type'] == 4) {  // список
                            $list_name = $db->escape(strtok($fields_assoc[$idx]['format'], ':'));
                            if (!isset($classificators[$list_name])) {
                                $db->query("SELECT `".$list_name."_ID`, `".$list_name."_Name` FROM `Classificator_".$list_name."`");
                                $classificators[$list_name] = array_combine($db->get_col(NULL, 0), $db->get_col(NULL, 1));
                            }
                            $value = $classificators[$list_name][$value];
                        } elseif ($fields_assoc[$idx]['type'] == 10) {  //множественный выбор
                            $list_name = $db->escape(strtok($fields_assoc[$idx]['format'], ':'));
                            if (!isset($classificators[$list_name])) {
                                $db->query("SELECT `".$list_name."_ID`, `".$list_name."_Name` FROM `Classificator_".$list_name."`");
                                $classificators[$list_name] = array_combine($db->get_col(NULL, 0), $db->get_col(NULL, 1));
                            }
                            $value_ids = explode(",", $value);
                            $value = '';
                            foreach ($value_ids as $val_id) {
                                if ($val_id) {
                                    $value .= $classificators[$list_name][$val_id].", ";
                                }
                            }
                            $value = nc_substr($value, 0, -2);
                        }

                        $ret .= "<$tag>".xmlspecialchars(strip_tags($value))."</$tag>\n";
                    }
                }

                $ret .= "</offer>\n";
            }
        }
        // ---------------------------------------------------------------

        $ret .= "</offers>\n</shop>\n</torg_price>";
        print $ret;
        // return $ret;
    }

}

//End Class Netshop_ExportYML
//---------------

$shop = new Netshop_ExportTorgMail();
$shop->Export();