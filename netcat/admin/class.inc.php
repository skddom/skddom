<?php

function GetClassNameByID($ClassID) {
    global $db;
    return $db->get_var("select Class_Name from Class where Class_ID='" . intval($ClassID) . "'");
}

/**
 * Пропарсить формат поля
 * Для поля типа "Файл" возвращаемое значение - хэш-массив с ключами:
 * size - размер;
 * type - массив с mimetype. Каждый элемент - массив, 0 - то, что стоит до /, 1 - то, что стоит после
 * fs - тип файловой системы
 * disposition - content-disposition, 0 - inline, 1 - attachment
 * download - считать скачивания?
 *
 * Для поле "Текстовы блок":
 * html - разрешить тэги
 * br - перенос строки - <br>
 * fck - встроить редактор в поле
 * rows, cols - высота и шириина
 * bbcode - доступены bb-коды
 *
 * @param string format
 * @param int field type
 * @return array
 */
function nc_field_parse_format($format, $fieldtype) {
    $ret = array(); //возвращаемое значение
    $format = str_replace(' ', '', $format); // уберем пробелы

    switch ($fieldtype) {
        case NC_FIELDTYPE_FILE:
        case NC_FIELDTYPE_MULTIFILE:
            // значения по умолчанию
            $ret['size'] = 0;
            $ret['type'] = '';
            $ret['fs'] = NC_FS_PROTECTED;
            $ret['download'] = 0;
            $ret['disposition'] = 0;
            // если формат пустой - вернуть значения по умолчанию
            if (!$format) break;
            // формат в общем случае:   size:type1/type,type2/type:fs1|fs2|fs3:inline|attachment:download

            //уберем из формата лишнее
            $format = array_shift(explode(';', $format));

            // определение фс
            if (preg_match('/(:?)(fs)(\d+)/', $format, $match)) {
                $ret['fs'] = $match[3];
                // уберем из формата тип фс
                $format = nc_preg_replace('/(:?)(fs)(\d+)/', '', $format);
            }

            if (!$format) break;
            // определение download
            if (strstr($format, 'download') !== false) $ret['download'] = 1;
            $format = nc_preg_replace('/(:?)(download)/', '', $format); // уберем download
            // определение content-disposition
            if (strstr($format, 'attachment') !== false)
                $ret['disposition'] = 1;
            $format = nc_preg_replace('/(:?)((attachment)|(inline))/', '', $format); // уберем attachment

            $format_array = explode(':', $format);
            if (empty($format_array)) break;
            if ($format_array[0])
                $ret['size'] = $format_array[0]; // размер
            //определение mimetype
            if ($format_array[1]) {
                $fileformat = explode(",", $format_array[1]); // определим каждый тип
                foreach ($fileformat as $k => $v) {
                    $ret['type'][$k] = explode('/', $v);
                }
            }

            break;

        case NC_FIELDTYPE_TEXT:
            // значения по умолчанию
            $ret['rows'] = 5; // количество строк
            $ret['cols'] = 60; // и столбцов
            $ret['html'] = 0; // разрешить тэги
            $ret['br'] = 0; // перенос строки - br
            $ret['fck'] = 0; // редактор встроен в поле
            $ret['panel'] = 0;
            $ret['typo'] = 0;
            $ret['bbcode'] = 0;

            if (!$format) return $ret;

            $params = array('html', 'br', 'fck', 'panel', 'typo', 'bbcode');
            // пробуем найти каждый параметр
            foreach ($params as $param) {
                if (($start = nc_strpos($format, $param)) !== false) {
                    $ret[$param] = intval(nc_substr($format, $start + nc_strlen($param) + 1, 1));
                }
            }

            // высоту и ширину ищем отдельно
            if ($format{0} > 0) {
                $format = strtok($format, ';');
                $ret['rows'] = strtok($format, ':');
                $ret['cols'] = strtok(':');
            }

            break;

        case NC_FIELDTYPE_DATETIME:
            $ret['type'] = '';
            $ret['calendar'] = 0;
            if (nc_strpos($format, 'calendar') !== false) {
                $ret['calendar'] = 1;
                $format = str_replace(array(';', 'calendar'), '', $format);
            }
            if ($format) $ret['type'] = $format;
            break;
        case NC_FIELDTYPE_STRING:
            $format = explode(':', $format);
            $ret['format'] = $format[0];
            $ret['protect_email'] = isset($format[1]) && $format[1] == 'protect';
            $ret['use_transliteration'] = $format[2];
            $ret['transliteration_field'] = $format[3];
            $ret['use_url_rules'] = $format[4];
            break;
    }

    return $ret;
}

/**
 * Parses resize options
 * from field format
 *
 * @param string $format
 * @return array
 */
function nc_field_parse_resize_options($format) {
    $result = array(
        'use_resize' => 0,
        'resize_width' => 0,
        'resize_height' => 0,
        'use_crop' => 0,
        'crop_mode' => 0,
        'crop_width' => 0,
        'crop_height' => 0,
        'crop_x0' => 0,
        'crop_y0' => 0,
        'crop_x1' => 0,
        'crop_y1' => 0,
        'crop_ignore' => 0,
        'crop_ignore_width' => 0,
        'crop_ignore_height' => 0,
        'use_preview' => 0,
        'preview_use_resize' => 0,
        'preview_width' => 0,
        'preview_height' => 0,
        'preview_use_crop' => 0,
        'preview_crop_mode' => 0,
        'preview_crop_width' => 0,
        'preview_crop_height' => 0,
        'preview_crop_x0' => 0,
        'preview_crop_y0' => 0,
        'preview_crop_x1' => 0,
        'preview_crop_y1' => 0,
        'preview_crop_ignore' => 0,
        'preview_crop_ignore_width' => 0,
        'preview_crop_ignore_height' => 0,
        'multifile_min' => 0,
        'multifile_max' => 0,
    );

    $format = explode(';', $format);
    foreach ($format as $parameter) {
        $parameter = explode(':', $parameter);
        if (isset($parameter[1]) && isset($result[$parameter[0]])) {
            $result[$parameter[0]] = (int)$parameter[1];
        }
    }

    return $result;
}

/**
 * Функция копирует один файл из первого поля во второй
 * в пределах одного объекта
 * новый файл будет в ФС, которая задана в формате поле-приемника
 *
 * @param int message - id объекта
 * @param int field_src - id поля источника
 * @param int field_dst - id поля приемниеп
 * @param int classID (оппоционально) id компонента
 *
 * @todo Реализовать копирование файлов системных таблиц
 * @todo Реализовать копирование файлов различных объектов (возможно, из разных компонентов)
 * @return bool
 */
function nc_copy_filefield($message, $field_src, $field_dst, $classID = 0) {
    global $nc_core;

    // Если не задан класс, то вытащим его из базы
    if (!$classID)
        $classID = $nc_core->db->get_var("SELECT `Class_ID` FROM `Field` WHERE  `Field_ID` = '" . intval($field_src) . "'");

    // проверка аргументов
    $message += 0;
    $field_src += 0;
    $field_dst += 0;
    $classID += 0;
    if (!$message || !$field_src || !$field_dst || !$classID) return 0;

    # поиск исходного файла
    // латинское имя поля
    $field_name_src = $nc_core->db->get_var("SELECT `Field_Name` FROM `Field` WHERE `Field_ID` = '" . $field_src . "'");
    if (!$field_name_src) return 0;

    // Значение поля в таблице объектов
    $message_field = $nc_core->db->get_row("SELECT * FROM `Message" . $classID . "` WHERE  `Message_ID` = '" . $message . "'", ARRAY_A);
    $file_data = explode(':', $message_field[$field_name_src]);
    $file_name = $file_data[0];
    $file_type = $file_data[1];
    $file_size = $file_data[2];
    $ext = substr($file_name, strrpos($file_name, ".")); // расширение файла
    // если ли файл в Filetable ?
    $filetable = $nc_core->db->get_row("SELECT * FROM `Filetable`
                                          WHERE `Message_ID` = '" . intval($message) . "' AND `Field_ID` = '" . intval($field_src) . "'", ARRAY_A);
    // определения полного пути к файлу
    if ($filetable) { // исходный файл в protected
        $path_src = rtrim($nc_core->FILES_FOLDER, '/') . $filetable['File_Path'] . $filetable['Virt_Name'];
    } else {
        if ($file_data[3]) { // orignal
            $path_src = $nc_core->FILES_FOLDER . $file_data[3];
        } else { // simple
            $path_src = $nc_core->FILES_FOLDER . $field_src . "_" . $message . $ext;
        }
    }


    # копирование
    // получение информации о поле-приемника
    $field_info_desc = $nc_core->db->get_row("SELECT `Field_Name`, `Format` FROM `Field` WHERE `Field_ID` = '" . $field_dst . "'", ARRAY_A);
    if (!$field_info_desc) return 0;

    //удаление старого файла
    DeleteFile($field_dst, $field_info_desc['Field_Name'], $classID, 0, $message);

    // определение типа фс применика
    $fs = nc_field_parse_format($field_info_desc['Format'], NC_FIELDTYPE_FILE);
    $fs = $fs['fs'];

    // определние имени файла на диске и диретории
    $in_db = $file_name . ":" . $file_type . ":" . $file_size; // то, что запишится в БД
    switch ($fs) {
        case NC_FS_PROTECTED:
            $path_dsc = $message_field['Subdivision_ID'] . '/' . $message_field['Sub_Class_ID'] . '/';
            $name_dsc = md5($file_name . date("H:i:s d.m.Y") . uniqid("netcat"));
            $nc_core->db->query("INSERT INTO `Filetable`(Real_Name, Virt_Name, File_Path, File_Type, File_Size, Message_ID, Field_ID)
                           VALUES('" . $file_name . "', '" . $name_dsc . "', '/" . $path_dsc . "','" . $file_type . "', '" . $file_size . "', '" . $message . "', '" . $field_dst . "')");
            break;
        case NC_FS_ORIGINAL:
            $path_dsc = $message_field['Subdivision_ID'] . '/' . $message_field['Sub_Class_ID'] . '/';
            $name_dsc = nc_get_filename_for_original_fs($file_name, $nc_core->FILES_FOLDER . $path_dsc);
            $in_db .= ":" . $path_dsc . $name_dsc;
            break;
        case NC_FS_SIMPLE:
            $path_dsc = '';
            $name_dsc = $field_dst . "_" . $message . $ext;
            break;
    }

    // обновление инфы в БД
    $nc_core->db->query("UPDATE `Message" . $classID . "` SET `" . $field_info_desc['Field_Name'] . "` = '" . $in_db . "' WHERE `Message_ID` = '" . $message . "'");
    print $nc_core->FILES_FOLDER . $path_dsc . $name_dsc;
    // копирование файла
    copy($path_src, $nc_core->FILES_FOLDER . $path_dsc . $name_dsc);

    return 1;
}

/**
 * Сгенерировать имя файла для записи на диск
 *
 * @param string $file_name оригинальное имя файла
 * @param string $path путь к файлу
 * @param array|null $disallow массив строк с недопустимыми именами
 * @return string
 */
function nc_get_filename_for_original_fs($file_name, $path, $disallow = null) {
    $use_index = false; // надо ли к файлу добавлять индекс
    if (!empty($disallow) && in_array($file_name, $disallow)) {
        $use_index = true;
    }

    $file_name = nc_transliterate($file_name);
    $file_name = preg_replace("/[^a-z0-9.]+/is", "_", $file_name);
    $file_name = preg_replace("/\.php\d*\b/i", ".phps", $file_name);
    $file_name = preg_replace("/^\./", "0$0", $file_name);
    if (file_exists($path . $file_name)) {
        $use_index = true;
    }

    if (!$use_index) {
        return $file_name;
    }

    $k = 0;
    $ext = substr($file_name, strrpos($file_name, "."));

    while (file_exists($path . ($temp = substr($file_name, 0, strrpos($file_name, ".")) . "_" . $k . $ext))
        || in_array($temp, (array)$disallow)) {
        $k++;
    }
    $file_name = $temp;

    return $file_name;
}
