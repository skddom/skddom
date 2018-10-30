<?php

$GLOBALS["PRINT_QUERIES"] = 0;
$GLOBALS["ENABLE_BENCHMARK"] = 0;

if (!function_exists("int")) { //  can be included several times despite require_once!

    /**
     * Cast to integer or double
     */
    function int(&$int) {
        if ($int > 2147483647) {
            $int = (double) $int;
        } else {
            $int = (int) $int;
        }

        return $int;
    }

    /**
     * Запрос к БД
     *
     * @param  string query
     * @return mixed query result
     * @global bool ENABLE_BENCHMARK
     * @global bool PRINT_QUERIES
     * @global integer LAST_INSERT_ID
     */
    function q($query, $ignore_errors = false) {
        global $LinkID;

        if ($GLOBALS["ENABLE_BENCHMARK"]) starttimer("SQLQ0");

        $res = mysqli_query($LinkID, $query);
        if ($GLOBALS["PRINT_QUERIES"])
                print "<![CDATA[<pre><font color=red>$query</font> [<font color=blue>".mysqli_affected_rows($LinkID)."</font>]</pre><hr>]]>";

        if (mysqli_error ($LinkID)) {
            //if (!$ignore_errors) { user_error(mysqli_error()."\n<br><b>Query:</b> <pre>$query</pre>\n"); }
            print mysqli_error($LinkID)."\n<br><b>Query:</b> <pre>$query</pre>\n";
            return false;
        } else {

            if ($GLOBALS["ENABLE_BENCHMARK"]) {
                $e = stoptimer("SQLQ0", "SQL", $query); // NOT LOGGED FOR QUERIES CAUSED ERROR
                if ($GLOBALS["PRINT_QUERIES"]) {
                    print "<font color=green>$e</font>";
                }
            }

            return $res;
        }
    }

    /**
     * Возвращает первую строку результата запроса к БД
     *
     * @param  string $query
     * @return array|null  ассоциативный массив
     */
    function row($query) {
        return nc_db()->get_row($query, ARRAY_A);
    }

    /**
     * возвращает первое значение
     *
     * @param  string $query
     * @return mixed  value
     */
    function value1($query) {
        return nc_db()->get_var($query);
    }

    /**
     * Получить данные объекта по типу шаблона
     * @var integer Тип объекта (ID шаблона)lib.php
     * @var string Поле, которое надо получить
     * @return mixed значение Subdivision.$what или массив
     */
    function GetSubdivisionByType($class_id, $what="*", $catalogue=0) {
        if (!int($class_id) || !(int($GLOBALS['catalogue']) || intval($catalogue)))
                return false;

        $qry = "FROM Subdivision as s, Sub_Class as c
             WHERE c.Class_ID = $class_id
               AND c.Subdivision_ID = s.Subdivision_ID
               AND s.Catalogue_ID = ".($catalogue ? $catalogue : $GLOBALS['catalogue'])."
             LIMIT 1";

        return (strpos($what, ",") || $what == "*" ? row("SELECT s.$what $qry") : value1("SELECT s.$what $qry"));
    }

    /**
     * Получить первый шаблон типа $type_id в разделе $subdivision_id
     */
    function GetTemplateByType($type_id, $subdivision_id, $fields = "*") {
        return row("SELECT $fields
                  FROM Sub_Class as c, Subdivision as s
                  WHERE c.Class_ID = $type_id
                    AND c.Subdivision_ID = $subdivision_id
                    AND c.Subdivision_ID = s.Subdivision_ID
                  ORDER BY s.Priority
                  LIMIT 1");
    }

    /**
     * Получить "Структуру" подразделов $section
     * @param integer subdivision_id
     * @param string WHERE
     * @param string (plain | get_children)
     * @return array
     */
    function GetStructure($section, $where=1, $mode="plain") {
        if (!int($section)) return false;
        if (!$where) $where = 1;

        global $_structure_level;

        $ret = array();

        $res = nc_db()->get_results(
           "SELECT *
            FROM Subdivision
            WHERE $where AND Parent_Sub_ID = $section",
            ARRAY_A
        );

        if ($res) {
            foreach ($res as $row) {
                $row["level"] = (int) $_structure_level;
                $ret[$row["Subdivision_ID"]] = $row;
                $_structure_level++;
                $children = GetStructure($row["Subdivision_ID"], $where);
                $_structure_level--;

                foreach ($children as $idx => $row2) {
                    $ret[$idx] = $row2;
                }
            }
        }

        if ($mode == "get_children") {
            foreach ($ret as $idx => $row) {
                while ($row["Parent_Sub_ID"] != $section) {
                    $ret[$row["Parent_Sub_ID"]]["Children"][] = $row["Subdivision_ID"];
                    $row = $ret[$row["Parent_Sub_ID"]];
                }
            }
        }

        return $ret;
    }

    ///////////////////////////////////////
    //Возвращает структуру для class Netshop_ExportYML
    function GetStructureYandexml($class_id, $cat_id = 1, $where = '`Subdivision`.`Checked` = 1') {
        global $db;
        $query = "SELECT *
                   FROM `Subdivision`, `Sub_Class`
                      WHERE `Sub_Class`.`Subdivision_ID` = `Subdivision`.`Subdivision_ID`
                      AND `Sub_Class`.`Class_ID` IN ($class_id)".
                ($cat_id ? " AND `Subdivision`.`Catalogue_ID`='".intval($cat_id) : NULL)."'".
                ($where ? " AND ".$where : NULL);
        $result = $db->get_results($query, ARRAY_A);
        if ($result) {
            foreach ($result as $value) {
                $result_array[$value['Subdivision_ID']] = $value;
            }
            return $result_array;
        } else {
            return false;
        }
    }

    //////////////////////////////////////
    // transliterate
    function tr($str) {
        $abc = array(
                "А" => "A", "а" => "a", "Б" => "B", "б" => "b",
                "В" => "V", "в" => "v", "Г" => "G", "г" => "g",
                "Д" => "D", "д" => "d", "Е" => "E", "е" => "e",
                "Ё" => "E", "ё" => "e", "Ж" => "Zh", "ж" => "zh",
                "З" => "Z", "з" => "z", "И" => "I", "и" => "i",
                "Й" => "Y", "й" => "y", "К" => "K", "к" => "k",
                "Л" => "L", "л" => "l", "М" => "M", "м" => "m",
                "Н" => "N", "н" => "n", "О" => "O", "о" => "o",
                "П" => "P", "п" => "p", "Р" => "R", "р" => "r",
                "С" => "S", "с" => "s", "Т" => "T", "т" => "t",
                "У" => "U", "у" => "u", "Ф" => "F", "ф" => "f",
                "Х" => "H", "х" => "h", "Ц" => "Ts", "ц" => "ts",
                "Ч" => "Ch", "ч" => "ch", "Ш" => "Sh", "ш" => "sh",
                "Щ" => "Sch", "щ" => "sch", "Ы" => "Y", "ы" => "y",
                "Ь" => "", "ь" => "", "Э" => "E", "э" => "e",
                "Ъ" => "", "ъ" => "", "Ю" => "Yu", "ю" => "yu",
                "Я" => "Ya", "я" => "ya"
        );

        foreach ($abc as $r => $l) {
            $str = str_replace($r, $l, $str);
        }
        return $str;
    }

    // из ngene с приветом
    function timestamp($value) {
        // convert to timestamp:
        //              1=yr              2=mon    3=day        4=hr    5=min     6=sec
        if (preg_match("/^((?:19|20)\d{2})-?(\d{2})-?(\d{2})(?:\s?(\d{2}):?(\d{2}):?(\d{2}))?/",
                        $value, $regs)) {
            return mktime($regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1]);
        } else {
            return $value;
        }
    }

    /**
     * из ngene с приветом
     *
     * Возвращает ассоциативный массив key=>value (например, для формирования <options> в <select>)
     * из первых двух значений каждой строки, полученной при выполнении q($query)
     *
     * @param string SQL-query
     * @return array Associative array ($col1=>$col2 OR $col1=>$row)
     */
    function assoc_array($query) {
        $res = q($query);
        if (!mysqli_num_rows($res)) {
            return array();
        } else {
            $arr = array();

            while ($row = mysqli_fetch_assoc($res)) {
                list ($col1, $col2) = array_keys($row);

                if (sizeof($row) == 1) {
                    $arr[] = $row[$col1];
                } elseif (sizeof($row) == 2) { // значением будет значение второй колонки
                    $arr[$row[$col1]] = $row[$col2];
                } else  {
                    $arr[$row[$col1]] = $row;
                }
            }
        }

        return $arr;
    }

    function xmlspecialchars($str) {
        $str = str_replace('&', "&amp;", $str);
        $str = str_replace('"', "&quot;", $str);
        $str = str_replace('>', "&gt;", $str);
        $str = str_replace('<', "&lt;", $str);
        $str = str_replace("'", "&apos;", $str);

        return $str;
    }

}


/**
 * Replace array_combine()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @link        http://php.net/function.array_combine
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 8300 $
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
if (!function_exists('array_combine')) {

    function array_combine($keys, $values) {
        if (!is_array($keys)) {
            user_error('array_combine() expects parameter 1 to be array, '.
                    gettype($keys).' given', E_USER_WARNING);
            return;
        }

        if (!is_array($values)) {
            user_error('array_combine() expects parameter 2 to be array, '.
                    gettype($values).' given', E_USER_WARNING);
            return;
        }

        $key_count = count($keys);
        $value_count = count($values);
        if ($key_count !== $value_count) {
            user_error('array_combine() Both parameters should have equal number of elements', E_USER_WARNING);
            return false;
        }

        if ($key_count === 0 || $value_count === 0) {
            user_error('array_combine() Both parameters should have number of elements at least 0', E_USER_WARNING);
            return false;
        }

        $keys = array_values($keys);
        $values = array_values($values);

        $combined = array();
        for ($i = 0; $i < $key_count; $i++) {
            $combined[$keys[$i]] = $values[$i];
        }

        return $combined;
    }

}

function toUTF($text) {
    global $NC_CHARSET;
    return strtolower($NC_CHARSET) == 'utf-8' ? $text : iconv('CP1251', 'UTF-8', $text);
}

function toCP1251($text) {
    global $NC_CHARSET;
    return strtolower($NC_CHARSET) == 'cp1251' ? $text : iconv('UTF-8', 'CP1251', $text);
}

function toLog($text) {
    global $db;
    $db->query("INSERT INTO log VALUES ('$text')");
}

function nc_show_button_compare(array $params, array $template = array()) {
    $nc_class_id = +$params['classID'];
    $nc_item_id = +$params['f_RowID'];

    $add_style = '';
    $del_style = '';

    if ($_SESSION['nc_netshop_compare'][$nc_class_id][$nc_item_id]) {
        $add_style = " style='display: none;'";
    } else {
        $del_style = " style='display: none;'";
    }

    $array_params = array(
            "classID=$nc_class_id",
            "f_RowID=$nc_item_id",
            "nc_compare=1");

    foreach ($params as $key => $value) {
        $array_params[] = $key . '=' . $value;
    }

    static $form_counter = -1;

    return "
        <form id='nc_netshop_compare_form_".++$form_counter."'>
            <span id='nc_netshop_compare_add'$add_style>{$template['add']}</span>
            <span id='nc_netshop_compare_del'$del_style>{$template['del']}</span>
        </form>

        <script type='text/javascript'>
            var nc_cf_{$form_counter}_add = jQuery('#nc_netshop_compare_form_$form_counter > #nc_netshop_compare_add');
            var nc_cf_{$form_counter}_del = jQuery('#nc_netshop_compare_form_$form_counter > #nc_netshop_compare_del');

            nc_cf_{$form_counter}_add.click(function() {
                nc_cf_{$form_counter}_add.hide();
                nc_cf_{$form_counter}_del.show();
                jQuery.get('?" . join('&', $array_params) . "');
            });

            nc_cf_{$form_counter}_del.click(function() {
                nc_cf_{$form_counter}_del.hide();

                if(jQuery('#nc_netshop_compare_form_$form_counter').closest('table').hasClass('nc_compare_goods')) {
                    jQuery('.' + jQuery('#nc_netshop_compare_form_$form_counter').closest('td').attr('class')).remove();

                }


                nc_cf_{$form_counter}_add.show();
                jQuery.get('?" . join('&', $array_params) . "&nc_compare_delete=1');
            });
        </script>";
}

function nc_action_compare() {
    if ($_GET['nc_compare']) {
        $_SESSION['nc_netshop_compare'][+$_GET['classID']][+$_GET['f_RowID']] = $_GET['nc_compare_delete'] ? null : array_map('strip_tags', $_GET);
        exit;
    }
}


function nc_count_compare() {
    $count = 0;
    foreach ((array) $_SESSION['nc_netshop_compare'] as $class) {
        foreach ((array) $class as $item) {
            if ($item !== null) {
                ++$count;
            }
        }
    }
    return $count;
}

function nc_get_compare_item($restart = false) {
    if (!($_SESSION['nc_netshop_compare'])) { return array(); }
    static $compare_array = array();
    static $i = -1;

    if ($restart) { $i = -1; }

    if (empty($compare_array)) {
        foreach ((array) $_SESSION['nc_netshop_compare'] as $class) {
            foreach ((array) $class as $item) {
                if ($item !== null) {
                    $compare_array[] = $item;
                }
            }
        }
        $compare_array[] = array();
    }
    return $compare_array[++$i];
}

function nc_compare_goods() {
    return "
        <script type='text/javascript'>
            var j = 0;
            var i = 0;
            var tr = null;
            var td = null;
            var result = [];
            var marks = [];

            while ((td = jQuery('td:eq(' + i + ')', jQuery('.nc_compare_goods tr:eq(0)'))).length) {
                marks[i] = td.attr('class');
                i++;
            }

            while ((tr = jQuery('.nc_compare_goods tr:eq(' + j++ + ')')).length) {
                i = 0;
                while ((td = jQuery('td:eq(' + i + ')', tr)).length) {
                    if (!result[i]) { result[i] = []; }
                    result[i].push(td.html());
                    i++;
                }
            }

            function nc_new_compare_table(table, marks) {
                var new_table = null;
                var mark_max = null;
                var mark_min = null;
                var result_shift = [];

                for (tr in table) {
                    new_table += '<tr class=\"nc_compare_row_' + (parseInt(tr) + 1) + '\">';
                    mark_max = null;
                    mark_min = null;

                    for (td in table[tr]) {
                        if (td == 0 && marks[tr]) {
                            (result_shift = table[tr].concat()).shift();
                            switch (marks[tr]) {
                                case 'nc_mark':
                                    mark_max = Math.max.apply({}, result_shift);
                                    mark_min = Math.min.apply({}, result_shift);

                                    break;

                                case 'nc_mark_reverse':
                                    mark_max = Math.min.apply({}, result_shift);
                                    mark_min = Math.max.apply({}, result_shift);
                                    break;
                            }
                        }
                        new_table += '<td class=\"nc_compare_cell_' + (parseInt(td) + 1);
                        if (mark_max == table[tr][td]) { new_table += ' nc_mark_max' }
                        else if (mark_min == table[tr][td]) { new_table += ' nc_mark_min' }

                        new_table += '\">' + table[tr][td] + '</td>';
                    }
                    new_table += '</tr>';
                }
                return new_table;
            }

            jQuery('.nc_compare_goods').html(nc_new_compare_table(result, marks)).show();
        </script>";
}
?>