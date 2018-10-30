<?php

/* $Id: function.inc.php 6675 2012-04-13 07:42:59Z alive $ */
if (!class_exists("nc_System")) die("Unable to load file.");

global $MODULE_FOLDER;
include_once ($MODULE_FOLDER."tagscloud/nc_tags.class.php");

global $nc_tags;
$nc_tags = new nc_tags();


# функция добавления тегов

function nc_tag_add($Sub_ID, $Sub_Class_ID, $Message_ID, $tags_string) {
    return false;
}

# функция изменения тегов

function nc_tag_edit($Sub_Class_ID, $Message_ID, $tag = "") {
    return false;
}

# функция удаления тегов

function nc_tag_drop($Sub_Class_ID, $Message_ID, $tag="") {
    return false;
}

# сопоставление размерам

function Tag_Size($Value, $Max, $Min, $Sum) {
    global $MODULE_VARS;

    $maxFont = $MODULE_VARS['tagscloud']['MAX_FONT'];
    $minFont = $MODULE_VARS['tagscloud']['MIN_FONT'];
    $fontrange = $maxFont - $minFont;
    // значение размера
    $px = min($fontrange, ($Value - $Min) / max(1, $Max - $Min) * $fontrange);

    return $minFont + round($px);
}

# функция генерации облака тегов

function nc_tag_cloud($Sub_Class_ID, $design, $adress_str="", $quantity="") {
    global $db, $MODULE_VARS;
    $cloud = "";

    if (!$Sub_Class_ID) return $cloud;

    $sql_where = join(",", (array) $Sub_Class_ID);
    if ($quantity) $quantity_limit = $quantity; else
            $quantity_limit = $MODULE_VARS['tagscloud']['QUANTITY'];

    // выбираем самые популярные значения
    $tags_array = $db->get_results("SELECT T.`Tag_Text`, T.`Tag_ID`, SUM(W.`Tag_Weight`) AS Tag_Count,
    W.*
		FROM `Tags_Weight` AS W
		LEFT JOIN `Tags_Data` AS T ON T.`Tag_ID` = W.`Tag_ID` 
		WHERE W.`Sub_Class_ID` IN (".$sql_where.")
		GROUP BY T.`Tag_ID`
		ORDER BY `Tag_Count` DESC
		LIMIT ".$quantity_limit."", ARRAY_A);

    if ($tags_array) {
        // сортируем по первому значению массива - Tag_Text
        sort($tags_array);

        // определяем максимум и минимум
        $Max_Count = $Min_Count = $tags_array[0]['Tag_Count'];
        foreach ($tags_array AS $value) {
            if ($value['Tag_Count'] > $Max_Count)
                    $Max_Count = $value['Tag_Count'];
            if ($value['Tag_Count'] < $Min_Count)
                    $Min_Count = $value['Tag_Count'];
        }

        // сумма всех значений
        $Sum_Count = 0;
        foreach ($tags_array AS $key => $value) {
            $Sum_Count = $Sum_Count + $value['Tag_Count'];
        }


        // вывод результатов
        $i = 0;
        while ($i < count($tags_array)) {
            $temp_cloud = "";
            $Tag_Size = Tag_Size($tags_array[$i]['Tag_Count'], $Max_Count, $Min_Count, $Sum_Count);
            // обрабатываем внутритекстовые переменные
            $temp_cloud = str_replace("%TAG_HEIGHT", $Tag_Size, $design);
            $temp_cloud = str_replace("%TAG_LINK", "?tag=".$tags_array[$i]['Tag_ID'], $temp_cloud);
            $temp_cloud = str_replace("%TAG_ID", $tags_array[$i]['Tag_ID'], $temp_cloud);
            $temp_cloud = str_replace("%TAG_SUB_LINK", $adress_str, $temp_cloud);
            $temp_cloud = str_replace("%TAG_NAME", $db->escape($tags_array[$i]['Tag_Text']), $temp_cloud);

            $temp_cloud = str_replace('$', '&#36;', $temp_cloud);
            eval(nc_check_eval("\$cloud.=\"$temp_cloud\";"));
            // разделяем пробелом
            if ((count($tags_array) - 1) != $i) $cloud.=" ";
            ++$i;
        }
    }

    return $cloud;
}

# вывод всех тегов сайта или сайтов

function nc_tag_cloud_all($site_ID="", $design, $quantity="") {
    global $db;

    $quantity = (int) $quantity;

    // если задан конкретный сайт или сайты
    if ($site_ID) {
        $site_ID = (array) $site_ID;
        $site_ID = array_map("intval", $site_ID);
        $SQL_where_str = "WHERE Catalogue_ID IN (".join(',', $site_ID).")";
    }

    // выборка из базы
    $Sub_Class_ID = $db->get_col("SELECT Sub_Class_ID FROM Sub_Class ".$SQL_where_str);

    // CLOUDируем
    $cloud = nc_tag_cloud($Sub_Class_ID, $design, "", max(0, $quantity));

    return $cloud;
}

# вывод облака из разделов по Subdivision_ID ( mixed )

function nc_tag_cloud_subdivision($Sub_ID, $design, $quantity="") {
    global $db;

    if (!$Sub_ID) return false;
    else {
        $Sub_ID = (array) $Sub_ID;
        $Sub_ID = array_map("intval", $Sub_ID);
    }
    $quantity = (int) $quantity;

    // формируем строку адреса для тегов
    $adress_str = "";
    if (count($Sub_ID) > 1) {
        foreach ($Sub_ID AS $key => $value) {
            $sym = "&amp;";
            $adress_str .= $sym."tagsub[".$key."]=".$value;
        }
    }
    else $adress_str = "&amp;tagsub=".$Sub_ID[0];

    // проходимся рекурсией по дереву
    $subArray = $Sub_ID;
    while ($tSub = $db->get_col("SELECT Subdivision_ID FROM Subdivision WHERE Parent_Sub_ID IN (".join(",", $Sub_ID).")")) {
        $subArray = array_merge($subArray, $tSub);
        $Sub_ID = $tSub;
    }
    // строка со всеми субами, в т.ч. и вложенными
    $sql_where = join(",", $subArray);

    // выборка из базы
    $Sub_Class_ID = $db->get_col("SELECT Sub_Class_ID FROM Sub_Class WHERE Subdivision_ID IN (".$sql_where.")");

    // CLOUDируем
    $cloud = nc_tag_cloud($Sub_Class_ID, $design, $adress_str, max(0, $quantity));

    return $cloud;
}

# вывод облака из шаблонов в разделе по Sub_Class_ID ( mixed )

function nc_tag_cloud_sub_class($Sub_Class_ID, $design, $quantity="") {
    global $db;

    if (!$Sub_Class_ID) return false;
    else {
        $Sub_Class_ID = (array) $Sub_Class_ID;
        $Sub_Class_ID = array_map("intval", $Sub_Class_ID);
    }
    $quantity = (int) $quantity;

    // формируем строку адреса для тегов
    $adress_str = "";
    if (count($Sub_Class_ID) > 1) {
        foreach ($Sub_Class_ID AS $key => $value) {
            $sym = "&amp;";
            $adress_str .= $sym."tagcc[".$key."]=".$value;
        }
    }
    else $adress_str = "&amp;tagcc=".$Sub_Class_ID[0];

    // CLOUDируем
    $cloud = nc_tag_cloud($Sub_Class_ID, $design, $adress_str, max(0, $quantity));

    return $cloud;
}

# функция вывода результатов выборки по тегу, возвращает массив нужных данных
/*
  $tag - ID тега
  $sub - ID разделов
  $cc - шаблонов в разделе
  $site - ID сайта
 */

function nc_tag_cloud_show_result($tag, $sub="", $cc="", $site = 0) {
    global $db;

    $site = (int) $site;
    $tag = (int) $tag;
    if ($sub) {
        $sub = (array) $sub;
        $sub = array_map("intval", $sub);
    }
    if ($cc) {
        $cc = (array) $cc;
        $cc = array_map("intval", $cc);
    }
    if (!$tag && !$sub && !$cc) return false;
    $sql_where = '';

    // если задан массив разделов или шаблонов в разделе
    if ($sub) {
        // проходимся рекурсией по дереву
        $subArray = $sub;
        while ($tSub = $db->get_col("SELECT Subdivision_ID FROM Subdivision WHERE Parent_Sub_ID IN (".join(",", $sub).")")) {
            $subArray = array_merge($subArray, $tSub);
            $sub = $tSub;
        }
        // строка со всеми субами, в т.ч. и вложенными
        $sql_where = join(",", $subArray);

        $Sub_Classes = $db->get_col("SELECT DISTINCT Sub_Class_ID FROM Sub_Class WHERE Subdivision_ID IN (".$sql_where.")");
        if ($Sub_Classes) $sql_where = join(",", $Sub_Classes); else return false;
    }
    elseif ($cc) {
        $sql_where = join(",", $cc);
    } else {
        $Sub_Classes = $db->get_col("SELECT DISTINCT Sub_Class_ID FROM Tags_Message WHERE Tag_ID=".$tag."");
        if ($Sub_Classes) $sql_where = join(",", $Sub_Classes); else return false;
    }

    if ($sql_where)
            $res = $db->get_results("SELECT sc.Subdivision_ID, s.Subdivision_Name, sc.Sub_Class_ID, ts.Message_ID
							FROM (Sub_Class AS sc, Tags_Message AS ts)
							LEFT JOIN Subdivision AS s ON sc.Subdivision_ID=s.Subdivision_ID
							WHERE sc.Sub_Class_ID IN (".$sql_where.") AND sc.Sub_Class_ID=ts.Sub_Class_ID AND ts.Tag_ID=".$tag."
                                                        ".($site ? " AND s.Catalogue_ID = ".$site : "")."
							ORDER BY sc.Subdivision_ID, sc.Sub_Class_ID, ts.Message_ID DESC", ARRAY_A);

    // возвращаем масиив разделов с названиями и с соответсвующими шаблонами в разделе, массив объектов
    // $messages_array - массив с ключом - CC_ID, значением - массив Message_ID
    if ($res)
            foreach ($res AS $key => $value) {
            $messages_array[] = array("Subdivision_ID" => $value['Subdivision_ID'],
                    "Subdivision_Name" => $value['Subdivision_Name'],
                    "Sub_Class_ID" => $value['Sub_Class_ID'],
                    "Message_ID" => $value['Message_ID']);
        }

    // сам тег
    $tagText = $db->get_var("SELECT Tag_Text FROM Tags_Data WHERE Tag_ID=".$tag."");

    return array($messages_array, $tagText);
}
?>