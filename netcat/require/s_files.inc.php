<?php

function getFileCount($classID, $systemTableID) {
    global $db;

    return $db->get_var("SELECT COUNT(*) FROM `Field`
    WHERE ".($systemTableID ? "`System_Table_ID` = '".intval($systemTableID)."'" : "`Class_ID` = '".intval($classID)."'")."
    AND `TypeOfData_ID` = 6");
}

function unhtmlentities($string) {
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);

    return strtr($string, $trans_tbl);
}

# удаление конкретного файла

function DeleteFile($field_id, $field_name, $classID, $systemTableName, $message, $trashxml = NULL) {
    global $nc_core, $db;

    $field_id = intval($field_id);
    $classID = intval($classID);
    $message = intval($message);
    $systemTableName = $db->escape($systemTableName);
    if (!nc_preg_match("/^[a-z0-9_]+$/i", $field_name)) return 0;

    global ${'f_' . $field_name . '_old'};

    $field = $db->get_var("SELECT `".$field_name."`
    FROM `".($systemTableName ? $systemTableName : "Message".$classID."")."` 
    WHERE `".($systemTableName ? $systemTableName : "Message")."_ID` = '".$message."'");

    if (is_object($trashxml)) {
        $field = $trashxml->query("/netcatml/messages/message[@message_id='".$message."']/".$field_name);
        $field = $field->item(0)->textContent;
    }

    if (!$field) return;

    $field_array = explode(':', $field);
    $name = $field_array[0];
    // имя на диске при использовании ФС Original
    $name_on_disk = $field_array[3] ? $field_array[3] : '';
    // Расширение файла
    $ext = substr($name, strrpos($name, "."));
    // Полный пусть к файлу
    $fullPathToFile = $nc_core->FILES_FOLDER . ($name_on_disk ? $name_on_disk : $field_id."_".$message.$ext);
    $parts = explode('/', $fullPathToFile);
    $parts[count($parts) - 1] = 'preview_' . $parts[count($parts) - 1];
    $fullPathToPreviewFile = implode('/', $parts);

    //путь в случае использовании Filetable
    if ($systemTableName == 'User') {
        $File_Path = "u/";
    } elseif ($systemTableName == 'Template') {
        $File_Path = "t/";
    } elseif ($systemTableName == 'Catalogue') {
        $File_Path = "c/";
    } elseif ($systemTableName) {
        $File_Path = $message."/";
    } else {
        list($subdivid, $subclassid) = $db->get_row("SELECT `Subdivision_ID`, `Sub_Class_ID` FROM `Message".$classID."` WHERE `Message_ID` = '".$message."'", ARRAY_N);
        $File_Path = $subdivid."/".$subclassid."/";
    }

    $q = $db->get_row("SELECT `ID`, `Virt_Name` FROM `Filetable` WHERE `Message_ID` = '".$message."' AND `Field_ID` = '".$field_id."'", ARRAY_N);

    if ($db->num_rows) {
        list($fs_id, $fs_virt_name) = $q;
        //delte file
        if (is_writable($nc_core->FILES_FOLDER.$File_Path.$fs_virt_name)) {
            unlink($nc_core->FILES_FOLDER.$File_Path.$fs_virt_name);
            @unlink($nc_core->FILES_FOLDER.$File_Path.'preview_'.$fs_virt_name);
        }
        $db->query("DELETE FROM `Filetable` WHERE `ID` = '".$fs_id."' LIMIT 1");
    }

    global ${'f_' . $field_name . '_old'};

    $res = $db->query("UPDATE `".($systemTableName ? $systemTableName : "Message".$classID)."` SET `LastUpdated` = `LastUpdated`, ".$field_name." = '' WHERE `".($systemTableName ? $systemTableName : "Message")."_ID` = '".$message."'");

    if ($res && is_writable($fullPathToFile)) {
        unlink($fullPathToFile);
        @unlink($fullPathToPreviewFile);
    }
    if (is_object($trashxml) && is_writable($fullPathToFile)) {
        unlink($fullPathToFile);
        @unlink($fullPathToPreviewFile);
    }
}

# выборка файлов из объекта при его удалении, чтобы их также удалить

function DeleteMessageFiles($classID, $message, $trashfile = '') {
    global $db, $FILES_FOLDER;
    static $storage = array();

    $classID = intval($classID);
    $message = intval($message);

    if (empty($storage[$classID])) {
        $storage[$classID] = $db->get_results("SELECT a.`Field_ID`, a.`Field_Name`, b.`System_Table_Name` FROM `Field` AS a
      LEFT JOIN `System_Table` AS b ON a.`System_Table_ID` = b.`System_Table_ID`
      WHERE a.`Class_ID` = '".$classID."' AND a.`TypeOfData_ID` = 6", ARRAY_N);
    }

    if (!empty($storage[$classID])) {
        if ($trashfile) {
            $ncCore = nc_Core::get_object();
            /* @var $ncCore nc_Core */
            $doc = new DOMDocument('1.0', 'utf-8');
            $doc->load($ncCore->TRASH_FOLDER.$trashfile);
            $xpath = new DOMXPath($doc);
        }

        foreach ($storage[$classID] as $field) {
            list($field_id, $field_name, $systemTableName) = $field;
            DeleteFile($field_id, $field_name, $classID, $systemTableName, $message, $xpath);
        }
    }

    //удаление файлов из поля Мультизагрузка
    //поле "Мультифайл"
    $nc_core = nc_Core::get_object();

    $sql = "SELECT m.`Field_ID`, m.`Path`, m.`Preview` FROM `Multifield` AS m " .
        "LEFT JOIN `Field` as f ON m.`Field_ID` = f.`Field_ID` " .
        "WHERE m.`Message_ID` = {$message} AND f.`Class_ID` = {$classID} AND f.`TypeOfData_ID` = 11";

    $multifields = (array)$db->get_results($sql, ARRAY_A);
    foreach($multifields as $multifield) {
        $field_id = $multifield['Field_ID'];

        $settings_http_path = nc_standardize_path_to_folder($nc_core->HTTP_FILES_PATH . "/multifile/{$field_id}/");
        $settings_path = nc_standardize_path_to_folder($nc_core->DOCUMENT_ROOT . '/' . $nc_core->SUB_FOLDER . '/' . $settings_http_path);

        foreach(array('Path', 'Preview') as $path) {
            $file_path = $multifield[$path];

            if ($file_path) {
                $parts = explode('/', nc_standardize_path_to_file($file_path));
                $file_name = array_pop($parts);

                @unlink($settings_path . $file_name);
            }
        }

        $sql = "DELETE FROM `Multifield` " .
            "WHERE `Message_ID` = {$message} AND `Field_ID` = {$field_id}";
        $db->query($sql);
    }
}

function DeleteSystemTableFiles($table, $message_id) {
    global $db;

    $systables = array('Catalogue' => 1, 'Subdivision' => 2, 'User' => 3, 'Template' => 4);

    if (!in_array($table, array_keys($systables))) {
        trigger_error("Wrong parameter \$table for DeleteSystemTableMessageFiles() [".$table."]", E_USER_WARNING);
        return;
    }

    $res = $db->get_results("SELECT `Field_ID`, `Field_Name`
    FROM `Field`
    WHERE `Class_ID` = 0
    AND `System_Table_ID` = '".$systables[$table]."'
    AND `TypeOfData_ID` = 6", ARRAY_N);

    if (!empty($res)) {
        foreach ($res as $field) {
            list($field_id, $field_name) = $field;
            DeleteFile($field_id, $field_name, 0, $table, $message_id);
        }
    }

    if ($systables[$table] == 3) {
        //удаление файлов из поля Мультизагрузка
        //поле "Мультифайл"
        $nc_core = nc_Core::get_object();

        $sql = "SELECT m.`Field_ID`, m.`Path`, m.`Preview` FROM `Multifield` AS m " .
            "LEFT JOIN `Field` as f ON m.`Field_ID` = f.`Field_ID` " .
            "WHERE m.`Message_ID` = {$message_id} AND f.`Class_ID` = 0 " .
            "AND f.`System_Table_ID` = {$systables[$table]} AND f.`TypeOfData_ID` = 11";

        $multifields = (array)$db->get_results($sql, ARRAY_A);
        foreach($multifields as $multifield) {
            $field_id = $multifield['Field_ID'];

            $settings_http_path = nc_standardize_path_to_folder($nc_core->HTTP_FILES_PATH . "/multifile/{$field_id}/");
            $settings_path = nc_standardize_path_to_folder($nc_core->DOCUMENT_ROOT . '/' . $nc_core->SUB_FOLDER . '/' . $settings_http_path);

            foreach(array('Path', 'Preview') as $path) {
                $file_path = $multifield[$path];

                if ($file_path) {
                    $parts = explode('/', nc_standardize_path_to_file($file_path));
                    $file_name = array_pop($parts);

                    @unlink($settings_path . $file_name);
                }
            }

            $sql = "DELETE FROM `Multifield` " .
                "WHERE `Message_ID` = {$message_id} AND `Field_ID` = {$field_id}";
            $db->query($sql);
        }
    }
}

/**
 * Удаление директории файлов шаблона в разделе $cc
 * @param int $cc идентификатор шаблона в разделе
 * @return bool
 */
function DeleteSubClassDir($cc) {
    global $nc_core, $db, $FILES_FOLDER;

    $dir_path = $nc_core->db->get_row("SELECT * FROM `Sub_Class` WHERE `Sub_Class_ID` = '".intval($cc)."' ", ARRAY_A);

    $path = $FILES_FOLDER.$dir_path['Subdivision_ID']."/".$dir_path['Sub_Class_ID'];

    if (is_dir($path) && count(glob($path."/*")) === 0 && is_writable($path)) {
        return @rmdir($path);
    }

    return false;
}

/**
 * Удаление директории раздела с идентификатором $sub
 * @param int $sub идентификатор раздела
 * @return bool
 */
function DeleteSubdivisionDir($sub) {
    global $FILES_FOLDER, $nc_core;

    $path = $FILES_FOLDER.$sub;
    try {
        $nc_core->files->delete_dir($path);
    } catch (nc_Exception_Files_Not_Rights $e) {
        ; //nc_print_status(sprintf(NETCAT_ERROR_UNABLE_TO_DELETE_FILES, $path), 'error');
    }

    return false;
}

/**
 * Удаление директории компонента $cc в  разделе $sub
 *
 * @param int $sub идентификатор раздела
 * @param int $cc идентификатор компонента в разделе
 *
 * @return bool
 */
function DeleteSubClassDirAlways($sub, $cc) {
    global $FILES_FOLDER;

    $path = $FILES_FOLDER.$sub."/".$cc;

    if (!is_dir($path)) return false;

    $files = array();
    $dh = opendir($path);

    if (!is_resource($dh)) return false;

    while (false !== ($filename = readdir($dh))) {
        if ($filename == "." || $filename == "..") continue;
        if (is_writable($path."/".$filename)) {
            unlink($path."/".$filename);
        }
    }
    closedir($dh);

    if (count(glob($path."/*")) === 0 && is_writable($path)) {
        return rmdir($path);
    }
}

/**
 * Удаление файлов шаблона в разделе $cc с идентификатором шаблона $classID
 * @param int $cc идентификатор шаблона в разделе
 * @param int $classID идентификатор шаблона
 * @return bool
 */
function DeleteSubClassFiles($cc, $classID) {
    global $db;

    $cc+= 0;
    $classID+= 0;

    $res = $db->get_results("SELECT `Field_ID`, `Field_Name`
    FROM `Field`
    WHERE `Class_ID` = '".$classID."'
    AND `System_Table_ID` = 0
    AND `TypeOfData_ID` = 6", ARRAY_A);

    if ($res) {
        foreach ($res as $field) {
            $messages = $db->get_col("SELECT `Message_ID`, `".$field['Field_Name']."` FROM `Message".$classID."` WHERE `Sub_Class_ID` = '".$cc."'");
            if ($messages) {
                foreach ($messages as $message_id) {
                    DeleteFile($field['Field_ID'], $field['Field_Name'], $classID, "", $message_id);
                }
            }
        }
    }

    // delete dir
    DeleteSubClassDir($cc);

    return true;
}

/**
 * Возвращает mime-тип файла
 * @param string $file_path
 * @param string $default_mime_type   значение, если не удалось определить MIME-type
 * @return string
 */
function nc_file_mime_type($file_path, $default_mime_type = 'application/octet-stream') {
    // (1) finfo
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $result = finfo_file($finfo, $file_path);
        finfo_close($finfo);
        return $result;
    }

    // (2) fallback: mime_content_type
    if (function_exists('mime_content_type')) {
        return mime_content_type($file_path);
    }

    // (3) fallback: try to guess by file content (images only)
    $file_type = null;

    if (filesize($file_path) > 4) {
        // для картинок пробуем угадать по первым байтам
        $headers = array(
            "\xFF\xD8\xFF" => 'image/jpeg',
            "\x89\x50\x4E\x47" => 'image/png',
            "\x47\x49\x46\x38" => 'image/gif',
        );

        $fp = fopen($file_path, 'r');
        $file_header = fread($fp, 4);
        foreach ($headers as $signature => $mime_type) {
            if (substr($file_header, 0, strlen($signature)) == $signature) {
                $file_type = $mime_type;
                break;
            }
        }

        if (!$file_type) { // look for <svg
            $more_content = $file_header . fread($fp, 1020); // read up to 1K (in addition to 4 bytes we read before)
            if (strpos($more_content, '<?xml') && preg_match('/<svg\b/', $more_content)) {
                $file_type = 'image/svg+xml';
            }
        }

        fclose($fp);
    }

    return $file_type ?: $default_mime_type;

}