<?php

if (DIRECTORY_SEPARATOR == '/') {
    putenv('PATH=' . getenv('PATH') . ':/usr/local/bin');
}

function showUploadForm() {
    $maxfilesize = min(ini_get('upload_max_filesize'), ini_get('upload_post_max_size') - 100);
    global $maxfilesize, $HTTP_ROOT_PATH, $HTTP_FILES_PATH, $HTTP_IMAGES_PATH, $HTTP_TEMPLATE_PATH;
    ?>
    <form method="post">
        <fieldset>
            <legend><?= TOOLS_DUMP_CREATE_HEADER ?></legend>
            <?= nc_admin_radio_simple('backup_mode', 'full', TOOLS_DUMP_CREATE_OPT_FULL, true, 'backup_mode_full') ?>
            <br/>
            <?= nc_admin_radio_simple('backup_mode', 'simple', TOOLS_DUMP_CREATE_OPT_DATA, false, 'backup_mode_simple') ?>
            <br/>
            <?= nc_admin_radio_simple('backup_mode', 'sql', TOOLS_DUMP_CREATE_OPT_SQL, false, 'backup_mode_sql') ?>
            <br/>
            <input type="hidden" name="phase" value="1"/>
            <input type="submit" value="<?= TOOLS_DUMP_CREATE_SUBMIT ?>"/>
        </fieldset>
    </form>

    <form enctype='multipart/form-data' action='dump.php' method='post'>
        <input type='hidden' name='MAX_FILE_SIZE' value='<?=
        $maxfilesize
        ?>'>
        <fieldset>
            <legend><?= TOOLS_DUMP_INC_TITLE ?></legend>
            <div style='margin:10px;'>
                <input size='40' name='filename' type='file'>
                <input type='submit' value='<?= TOOLS_DUMP_INC_DORESTORE ?>'
                       title='<?= TOOLS_DUMP_INC_DORESTORE ?>'>
            </div>
            <input type='hidden' name='phase' value='7'>
            <table border='0' cellpadding='6' cellspacing='0' width='100%'>
                <tr>
                    <td>
                        <font color='gray'>
                            <?= nc_admin_checkbox_simple('what[]', 'database', TOOLS_DUMP_INC_DBDUMP, true, 'database') ?>
                            <br/>
                            <?= nc_admin_checkbox_simple('what[]', 'netcat_template', TOOLS_DUMP_INC_FOLDER . ' <b>' . $HTTP_TEMPLATE_PATH . '</b>', true, 'netcat_template') ?>
                            <br/>
                            <?= nc_admin_checkbox_simple('what[]', 'netcat_files', TOOLS_DUMP_INC_FOLDER . ' <b>' . $HTTP_FILES_PATH . '</b>', true, 'netcat_files') ?>
                            <br/>
                            <?= nc_admin_checkbox_simple('what[]', 'images', TOOLS_DUMP_INC_FOLDER . ' <b>' . $HTTP_IMAGES_PATH . '</b>', true, 'images') ?>
                            <br/>
                            <?= nc_admin_checkbox_simple('what[]', 'modules', TOOLS_DUMP_INC_FOLDER . ' <b>' . $HTTP_ROOT_PATH . '</b>', true, 'modules') ?>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
    <?php
}

function GetRandom($length) {
    srand((double)microtime() * 1000000);
    while (1) {
        $val = rand(65, 122);
        if (!($val > 90 && $val < 97)) {
            $len++;
            $Ret .= chr($val);
            if ($len >= $length) {
                break;
            }
        }
    }
    return $Ret;
}

# удаление файла дампа с диска
# $file - полное название файла

function DeleteDump($file) {
    global $db_path, $DUMP_FOLDER;

    $count_file = count($file);

    for ($i = 0; $i < $count_file; $i++) {
        $arr = explode("/", $file[$i]);
        $arr2 = explode("\\", $file[$i]);
        if (count($arr) == 1 && count($arr2) == 1) {
            $file_deleted = @unlink($DUMP_FOLDER . $file[$i]);
            if ($file_deleted) {
                nc_print_status(str_replace("%FILE", $file[$i], TOOLS_DUMP_DELETED), "ok");
            }
        }
        else {
            nc_print_status(str_replace("%FILE", $file[$i], TOOLS_DUMP_ERROR_CANTDELETE), "error");
        }
    }
}

function prepend_file($file, $string) {
    if (!file_exists($file)) {
        return file_put_contents($file, $string);
    }

    $tmp_file = $file . '.tmp';
    $src_handle = fopen($file, 'r');
    $dest_handle = fopen($tmp_file, 'w');

    fwrite($dest_handle, $string);

    while (!feof($src_handle)) {
        fwrite($dest_handle, fread($src_handle, 5120));
    }

    fclose($src_handle);
    fclose($dest_handle);
    unlink($file);

    return rename($tmp_file, $file);
}

# создание дампа БД
# $mysql_dump в ./dump.inc.php

function MakeBackUp($dump_dir = null) {
    global $db_path, $MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DB_NAME, $MYSQL_PORT, $MYSQL_SOCKET, $MYSQL_CHARSET, $DOCUMENT_ROOT, $SUB_FOLDER, $TMP_FOLDER, $LinkID;

    $dump_dir = $dump_dir !== null ? $dump_dir : $TMP_FOLDER;

    // try to exec mysqldump
    $err_code = 127;
    if (strpos($MYSQL_HOST, ":")) {
        list($host, $port) = explode(":", $MYSQL_HOST);
        $host = "--host=$host --port=$port";
    }
    else {
        $host = "--host=$MYSQL_HOST";
    }

    @exec("mysqldump $host -u $MYSQL_USER " . ($MYSQL_PASSWORD ? "-p$MYSQL_PASSWORD " : "") .
        ((float)mysqli_get_server_info($LinkID) > 4 ? " --default-character-set=$MYSQL_CHARSET " : "") .
        ($MYSQL_PORT ? " --port=$MYSQL_PORT" : '') .
        ($MYSQL_SOCKET ? " --socket=$MYSQL_SOCKET" : '') .
        " --add-drop-table --disable-keys --quick" .
        " --add-drop-table --disable-keys --quick" .
        " --result-file=" . $dump_dir . "netcat.sql $MYSQL_DB_NAME 2>&1", $output, $err_code);

    if (!$err_code) {
        if (!prepend_file($dump_dir . 'netcat.sql', "SET NAMES '" . $MYSQL_CHARSET . "';\n\n")) {
            $err_code = 1;
        }
    }

    if ($err_code) {
        $mysql_dump = new MYSQL_DUMP($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_PORT, $MYSQL_SOCKET, $MYSQL_CHARSET);
        $result = $mysql_dump->dump_to_file($dump_dir . 'netcat.sql', $MYSQL_DB_NAME);
        if (!$result) {
            echo $mysql_dump->error();
        }
    }
}

function DumpQuery($file) {
    global $HTTP_HOST, $ROOT_FOLDER, $SUB_FOLDER, $HTTP_ROOT_PATH, $ADMIN_PATH, $DUMP_FOLDER, $HTTP_DUMP_PATH;
    global $UI_CONFIG;
    global $HTTP_ROOT_PATH, $HTTP_FILES_PATH, $HTTP_IMAGES_PATH, $HTTP_TEMPLATE_PATH;
    ?>
    <table class='admin_table' width='100%'>
        <tr>
            <td>
                <?= TOOLS_DUMP_INC_ARCHIVE ?>:
            </td>
            <td>
                <b><?= $file ?></b> [<a
                    href='<?= $SUB_FOLDER . $HTTP_DUMP_PATH . $file ?>'><?= TOOLS_DUMP_INC_DOWNLOAD ?></a>]
            </td>
        </tr>
        <tr>
            <td>
                <?= TOOLS_DUMP_INC_DATE ?>:
            </td>
            <td>
                <?= date("Y-m-d H:i:s", filemtime($DUMP_FOLDER . $file)) ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= TOOLS_DUMP_INC_SIZE ?>:
            </td>
            <td>
                <?= nc_bytes2size(filesize($DUMP_FOLDER . $file)) ?>
            </td>
        </tr>
    </table>
    <br/>
    <form method='post' action='<?= $ADMIN_PATH ?>dump.php'>
        <font color='gray'>
            <?=
            nc_admin_checkbox_simple('what[]', 'database', TOOLS_DUMP_INC_DBDUMP, true, 'database')
            ?><br/>
            <?= nc_admin_checkbox_simple('what[]', 'netcat_template', TOOLS_DUMP_INC_FOLDER . ' <b>' . $HTTP_TEMPLATE_PATH . '</b>', true, 'netcat_template') ?>
            <br/>
            <?= nc_admin_checkbox_simple('what[]', 'netcat_files', TOOLS_DUMP_INC_FOLDER . ' <b>' . $HTTP_FILES_PATH . '</b>', true, 'netcat_files') ?>
            <br/>
            <?= nc_admin_checkbox_simple('what[]', 'images', TOOLS_DUMP_INC_FOLDER . ' <b>' . $HTTP_IMAGES_PATH . '</b>', true, 'images') ?>
            <br/>
            <?= nc_admin_checkbox_simple('what[]', 'modules', TOOLS_DUMP_INC_FOLDER . ' <b>' . $HTTP_ROOT_PATH . '</b>', true, 'modules') ?>
            <br><br>
            <input type='hidden' name='file' value='<?= $file ?>'>
            <input type='hidden' name='phase' value='6'>
            <input type='submit' class='hidden'>
    </form>
    <?php
    $UI_CONFIG->actionButtons[] = array("id" => "submit",
        "caption" => TOOLS_DUMP_INC_DORESTORE,
        "action" => "mainView.submitIframeForm()");
}

# не используется

function mkArch($Folder1, $FolderToArch, $DestFile) {
    global $isWin, $Wrar;

    echo "$Folder1, $FolderToArch, $DestFile<br>";
    # $newfile = new gzip_file($DestFile.".tgz");
    $newfile = new tar_file($DestFile . ".tar");
    $newfile->set_options(array('basedir' => $Folder1, 'overwrite' => 1));
    $newfile->add_files($FolderToArch);
    $newfile->create_archive();
    if (count($newfile->errors) > 0) {
        print ("Errors occurred.");
    }
}

# сборка архива проекта в файлы, затем в один файл tgz

function mkDump($console_run = false, $dump_options = null) {
    global $DOCUMENT_ROOT, $SUB_FOLDER, $DOMAIN_NAME, $HTTP_FILES_PATH, $HTTP_ROOT_PATH, $HTTP_DUMP_PATH, $TMP_FOLDER, $HTTP_IMAGES_PATH, $DUMP_FOLDER, $HTTP_TEMPLATE_PATH, $ADMIN_PATH;
    $nc_core = nc_Core::get_object();
    // Check permissions if running from web
    if (!$console_run) {
        if (!checkPermissions($HTTP_DUMP_PATH, $DOCUMENT_ROOT)) {
            return;
        }
        if (!checkPermissions($HTTP_ROOT_PATH . "tmp/", $DOCUMENT_ROOT)) {
            return;
        }
    }

    // Determine what items to backup
    // Default settings (as in 5.0.2):
    $dump_items = array(
        'template' => true,
        'files' => true,
        'images' => true,
        'modules' => true,
        'everything' => false,
        'sql' => true
    );
    $include_standalone_script = false;

    // Modify settings if options specified
    //
    // NOTE: modifications rely on default settings. If you're changing default settings above, you have to review this part of code too.
    if ($dump_options) {
        // Modify items based on mode selection
        $backup_mode = "data";
        if (isset($dump_options['mode'])) {
            switch ($dump_options['mode']) {
                case 'full':
                    $dump_items['everything'] = true;
                    $backup_mode = "full";
                    break;
                case 'sql':
                    $backup_mode = "sql";
                    $dump_items['template'] = false;
                    $dump_items['files'] = false;
                    $dump_items['images'] = false;
                    $dump_items['modules'] = false;
                    break;
            }
        }

        // Determine if standalone restore script should be included
        if (isset($dump_options['standalone']) && $dump_options['standalone'] = true) {
            $include_standalone_script = true;
        }
    }

    $dump_filename = date("YmdHis") . "_" . $DOMAIN_NAME . "_" . $backup_mode . "_" . GetRandom(5);
    $dump_dirname = $dump_filename;
    $dump_dir = $TMP_FOLDER . $dump_dirname . '/';
    if (!file_exists($dump_dir)) {
        mkdir($dump_dir);
    }

    DeleteFilesInDirectory($dump_dir);

    $dump_file = array();

    // Pack appropriate items

    if ($dump_items['template']) {
        $dump_file[] = nc_tgz_create($dump_dir . "netcat_template.tgz", ltrim($HTTP_TEMPLATE_PATH, "/"));
    }

    if ($dump_items['files']) {
        $dump_file[] = nc_tgz_create($dump_dir . "netcat_files.tgz", ltrim($HTTP_FILES_PATH, "/"));
    }

    if ($dump_items['images'] && file_exists($nc_core->DOCUMENT_ROOT . $nc_core->SUB_FOLDER . $HTTP_IMAGES_PATH)) {
        $dump_file[] = nc_tgz_create($dump_dir . "images.tgz", trim($HTTP_IMAGES_PATH, "/"));
    }

    if ($dump_items['modules']) {
        $dump_file[] = nc_tgz_create($dump_dir . "modules.tgz", ltrim($HTTP_ROOT_PATH, "/") . "modules");
    }

    if ($dump_items['everything']) {
        $path_prefix_length = strlen($nc_core->DOCUMENT_ROOT . $nc_core->SUB_FOLDER);
        $exclude_dirs = array(
            trim(substr($nc_core->TMP_FOLDER, $path_prefix_length), '/'),
            trim(substr($nc_core->MODULE_FOLDER, $path_prefix_length), '/'),
            trim($nc_core->HTTP_TEMPLATE_PATH, '/'),
            trim($nc_core->HTTP_FILES_PATH, '/'),
            trim($nc_core->HTTP_IMAGES_PATH, '/'),
            trim($nc_core->HTTP_DUMP_PATH, '/'),
        );

        $dump_file[] = nc_tgz_create($dump_dir . "everything.tgz", '.', '', $exclude_dirs);
    }

    // Create SQL dump
    if ($dump_items['sql']) {
        MakeBackUp($dump_dir);
    }

    // Add standalone script if requested
    if ($include_standalone_script) {
        // Hosts that miss tar, mysql and/or mysqldump utilities (such as Windows) needs these classes to make restore script work
        file_put_contents($dump_dir . '/restore.php', file_get_contents($DOCUMENT_ROOT . $ADMIN_PATH . 'dump/mysql.class.php') . "\n\n\n");
        file_put_contents($dump_dir . '/restore.php', file_get_contents($DOCUMENT_ROOT . $ADMIN_PATH . 'dump/PEAR5.php') . "\n?>\n\n", FILE_APPEND);
        file_put_contents($dump_dir . '/restore.php', preg_replace('/include_once\ .PEAR5.php.\;/', '', file_get_contents($DOCUMENT_ROOT . $ADMIN_PATH . 'dump/PEAR.php')) . "\n?>\n\n", FILE_APPEND);
        file_put_contents($dump_dir . '/restore.php', preg_replace('/require_once\ .PEAR.php.\;/', '', file_get_contents($DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_ROOT_PATH . 'require/lib/Tar.php')) . "\n?>\n\n", FILE_APPEND);
        file_put_contents($dump_dir . '/restore.php', '<?php $dump_options = ' . var_export($dump_options, true) . '; ?>' . "\n\n\n", FILE_APPEND);
        file_put_contents($dump_dir . '/restore.php', preg_replace('/%ADMIN_PATH%/', '/netcat/admin/', file_get_contents($DOCUMENT_ROOT . $ADMIN_PATH . 'dump/functions.php')) . "\n\n\n", FILE_APPEND);
        file_put_contents($dump_dir . '/restore.php', file_get_contents($DOCUMENT_ROOT . $ADMIN_PATH . 'dump/restore.php') . "\n\n\n", FILE_APPEND);
    }

    // Compress final archive
    $dump_file[] = nc_tgz_create($DUMP_FOLDER . "$dump_filename.tgz", ltrim($HTTP_ROOT_PATH, "/") . "tmp/" . $dump_dirname);

    DeleteFilesInDirectory($dump_dir);
    rmdir($dump_dir);

    // Check results
    $dump_success = true;
    foreach ($dump_file as $_check) {
        if (!$_check) {
            $dump_success = false;
            break;
        }
    }
    if ($dump_success) {
        if (!$console_run) {
            nc_print_status(str_replace("%FILE", "$dump_filename.tgz", TOOLS_DUMP_CREATED), "ok");
        }
        else {
            echo "Backup created: $dump_filename.tgz\n";
            return 0;
        }
    }
    else {
        if (!$console_run) {
            nc_print_status(TOOLS_DUMP_CREATION_FAILED, "error");
        }
        else {
            echo "Backup creation failed: $dump_filename.tgz\n";
            return 1;
        }
    }
}

# покажем список имеющихся архивов проекта

function ShowBackUps() {
    global $db_path, $ADMIN_PATH, $ADMIN_TEMPLATE, $DUMP_FOLDER, $UI_CONFIG;

    $dir_read = dir($DUMP_FOLDER);
    $dir_count = dir($DUMP_FOLDER);

    $total = 0;
    $read = 0;
    while (($entry = $dir_count->read()) !== false) {
        $total++;
    }
    $total -= 2;
    $dir_count->close();

    while (($entry = $dir_read->read()) !== false) {
        $entry_str = substr($entry, -4);
        if ($entry != "." && $entry != ".." && ($entry_str == ".tgz" || $entry_str == ".rar")) {
            if (($total - 1) > $read) {
                $read++;
            }
            $countDumps = 1;
            $filename = $entry; //substr($entry, 0, strlen($entry) - 20);
            $filesize = filesize($DUMP_FOLDER . $entry);
            $filetime = filemtime($DUMP_FOLDER . $entry);
            $table = "";
            $table .= "<tr>";
            $table .= "<td><font size='-1'><b><a href='" . $ADMIN_PATH . "dump.php?phase=3&file=" . $entry . "'>" . $filename . "</a></b></td>\r\n";
            $table .= "<td><font size='-1'>" . date("Y-m-d H:i:s", $filetime) . "</td>";
            $table .= "<td><font size='-1'>" . nc_bytes2size($filesize) . "</td>";
            $table .= "<td align='center'>" . nc_admin_checkbox_simple('del[]', $entry) . "</td>";
            $table .= "</tr>";
            $table_arr[$filetime] = $table;
        }
    }
    $dir_read->close();

    if ($countDumps != 1) {
        nc_print_status(TOOLS_DUMP_NOONE, "info");
    }
    else {
        ?>
        <form id='backups_form' method='post'>
        <fieldset>
        <legend><?= TOOLS_DUMP_BACKUPLIST_HEADER ?></legend>
        <table border='0' cellpadding='0' cellspacing='0' width='100%'>
            <tr>
                <td>
                    <table class='admin_table' width='100%'>
                        <tr>
                            <th width='45%'><?= TOOLS_DUMP_INC_ARCHIVE ?></th>
                            <th width='25%'><?= TOOLS_DUMP_DATE ?></th>
                            <th width='20%'><?= TOOLS_DUMP_SIZE ?></th>
                            <td class='align-center'>
                                <div class='icons icon_delete'
                                     title='<?= CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DELETE ?>'></div>
                            </td>
                        </tr>
                        <?php
                        if (is_array($table_arr) && !empty($table_arr)) {
                            ksort($table_arr);
                            echo join("", $table_arr);
                        }
                        ?>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <?php
    }

    if ($countDumps) {
        $UI_CONFIG->actionButtons[] = array(
            "id" => "submit",
            "caption" => TOOLS_DUMP_REMOVE_SELECTED,
            "action" => "mainView.submitIframeForm('backups_form')",
            "align" => "right",
            "red_border" => true,
        );
        ?>
        <input type='hidden' name='phase' value='2'>
        <input type='submit' class='hidden'>
        </fieldset>
        </form>
        <?php
    }
}

function RemoveOldBackups($keep_count) {
    global $DUMP_FOLDER;
    $dir = scandir($DUMP_FOLDER);
    $archives = array();
    foreach ($dir as $entry) {
        $entry_str = substr($entry, -4);
        if ($entry == ".." || $entry == "." || $entry_str != ".tgz") {
            continue;
        }
        $archives[] = $entry;
    }
    rsort($archives);
    for ($i = $keep_count; $i < count($archives); $i++) {
        unlink($DUMP_FOLDER . '/' . $archives[$i]);
    }

}

# распаковка дампа в БД
# $mysql_dump в ./dump.inc.php

function SQLFromFile($file) {
    global $db, $MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DB_NAME, $MYSQL_PORT, $MYSQL_SOCKET, $MYSQL_CHARSET, $LinkID;

    // try to upload dump via exec("mysql")
    $err_code = 127;
    $command =
        "mysql --host=$MYSQL_HOST --user=$MYSQL_USER" .
        ($MYSQL_PASSWORD ? " --password=$MYSQL_PASSWORD " : "") .
        ((float)mysqli_get_server_info($LinkID) > 4 ? " --default-character-set=$MYSQL_CHARSET " : "") .
        " $MYSQL_DB_NAME < $file 2>&1";

    @exec($command, $output, $err_code);

    // exec failed
    if ($err_code) {
        $mysql_dump = new MYSQL_DUMP($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_PORT, $MYSQL_SOCKET, $MYSQL_CHARSET);

        // $sql = $mysql_dump->dumpDB($MYSQL_DB_NAME);

        if ($mysql_dump->restoreDB($file, $MYSQL_DB_NAME) == false) {
            echo $mysql_dump->error();
            return false;
        }
    }

    return true;
}

# распаковка дампа, закаченного через WEB

function decompressDumpTGZ2($file) {
    global $DOCUMENT_ROOT, $SUB_FOLDER, $TMP_FOLDER;

    $err = 0;
    if (!nc_tgz_extract($TMP_FOLDER . $file, $DOCUMENT_ROOT . $SUB_FOLDER)) {
        $err = "Error while dump file extracting";
    }

    return $err;
}

# распаковка дампа из директории дампов

function decompressDumpTGZ1($file) {
    global $DOCUMENT_ROOT, $SUB_FOLDER, $DUMP_FOLDER;

    $err = 0;
    if (!nc_tgz_extract($DUMP_FOLDER . $file, $DOCUMENT_ROOT . $SUB_FOLDER)) {
        $err = "Error while dump file extracting";
    }

    return $err;
}

# распаковка архива проекта по нужным местам

function ReadBackUP($backupfile, $images, $netcat_files, $sqldump, $modules, $dump, $netcat_template) {
    global $HTTP_TEMPLATE_PATH, $DOCUMENT_ROOT, $SUB_FOLDER, $HTTP_FILES_PATH, $HTTP_ROOT_PATH, $HTTP_IMAGES_PATH, $TMP_FOLDER, $DIRCHMOD;

    if (!checkPermissions($HTTP_ROOT_PATH . "tmp/", $DOCUMENT_ROOT)) {
        return $err = ".";
    }

    $err = 0;

    if (!$dump) {
        if ($err = decompressDumpTGZ1($backupfile)) {
            return $err;
        }
    }
    else {
        if ($err = decompressDumpTGZ2($backupfile)) {
            return $err;
        }
    }
    $backup_dir = pathinfo($backupfile, PATHINFO_FILENAME);
    $backup_folder = $TMP_FOLDER . $backup_dir . DIRECTORY_SEPARATOR;

    //Unpack images
    if ($images && file_exists($backup_folder . "images.tgz")) {
        if (!file_exists($DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_IMAGES_PATH)) {
            mkdir($DOCUMENT_ROOT . $SUB_FOLDER . $HTTP_IMAGES_PATH, $DIRCHMOD);
        }
        if (!checkPermissions($HTTP_IMAGES_PATH, $DOCUMENT_ROOT)) {
            return $err = ".";
        }
        if (!nc_tgz_extract($backup_folder . "images.tgz", $DOCUMENT_ROOT . $SUB_FOLDER)) {
            $err = "Error while images extracting";
        }
    }

    //Unpack netcat_files
    if ($netcat_files) {
        if (!checkPermissions($HTTP_FILES_PATH, $DOCUMENT_ROOT)) {
            return $err = ".";
        }
        if (!nc_tgz_extract($backup_folder . "netcat_files.tgz", $DOCUMENT_ROOT . $SUB_FOLDER)) {
            $err = "Error while netcat_files extracting";
        }
    }

    if ($netcat_template) {
        if (!checkPermissions($HTTP_TEMPLATE_PATH, $DOCUMENT_ROOT)) {
            return $err = ".";
        }
        if (!nc_tgz_extract($backup_folder . "netcat_template.tgz", $DOCUMENT_ROOT . $SUB_FOLDER)) {
            $err = "Error while netcat_template extracting";
        }
    }

    //Restore MySQL dump
    if ($sqldump) {
        if (!SQLFromFile($backup_folder . "netcat.sql")) {
            $err = "Error while MySQL dump extracting";
        }
    }

    //Unpack modules
    if ($modules) {
        if (!checkPermissions($HTTP_ROOT_PATH . "modules/", $DOCUMENT_ROOT)) {
            return $err = ".";
        }
        if (!nc_tgz_extract($backup_folder . "modules.tgz", $DOCUMENT_ROOT . $SUB_FOLDER)) {
            $err = "Error while modules extracting";
        }
    }

    DeleteFilesInDirectory($TMP_FOLDER);

    return $err;
}

function checkBox($box, $value) {

    $box_count = count($box);

    for ($i = 0; $i < $box_count; $i++) {
        if ($box[$i] == $value) {
            return 1;
        }
    }

    return 0;
}

function AskDump() {
    global $ADMIN_PATH;
    ?>
    <?= TOOLS_DUMP_CONFIRM ?>
    <form method='post' action='<?= $ADMIN_PATH ?>dump.php'>
        <input type='hidden' name='phase' value='1'>
        <input type='submit' value='<?= TOOLS_DUMP_CREATEAP ?>'
               title='<?= TOOLS_DUMP_CREATEAP ?>'>
    </form>
    <?php
}

/**
 * Смыв буфера перед длительными операциями
 *
 * @param $message
 */
function nc_dump_flush_buffer($message) {
    // Отключение буферизации в nginx
    header('X-Accel-Buffering: no');
    header('Content-Encoding: identity');

    // let’s flush our dump
    while (@ob_end_flush()) ;
    ob_implicit_flush();

    // Сообщение "подождите"
    echo '<div id="nc_dump_wait_message">',
         nc_print_status($message, 'info', null, true),
         '</div>';

    // Сообщение о возможных действиях в случае потери связи
    if (DIRECTORY_SEPARATOR != '/') { // Windows
        $troubleshooting_suggestion = TOOLS_DUMP_CONNECTION_LOST_INCREASE_PHP_LIMITS;
    }
    else if (defined("SYSTEM_TAR") && !SYSTEM_TAR) { // no system tar under *nix
        $troubleshooting_suggestion = TOOLS_DUMP_CONNECTION_LOST_SYSTEM_TAR;
    }
    else { // system tar is used
        $troubleshooting_suggestion = TOOLS_DUMP_CONNECTION_LOST_INCREASE_SERVER_LIMITS;
    }

    $troubleshooting_message = sprintf(TOOLS_DUMP_CONNECTION_LOST, $troubleshooting_suggestion) .
        '<p><a href="?">' . TOOLS_DUMP_CONNECTION_LOST_GO_BACK . '</a></p>';

    echo '<div id="nc_dump_connection_message" style="display: none">',
         nc_print_status($troubleshooting_message, 'info', null, true),
         '</div>';

    // Напоминаем веб-серверу, что мы живы
    declare(ticks = 10000);
    register_tick_function(function () { echo ' '; });

    // Если скрипт всё же скоропостижно погибнет, выведем возможные варианты исправления проблемы
    // (если на странице не вывалилось явное сообщение об ошибке)
    ?>
    <script>
        $nc(function () {
            setTimeout(function () {
                // см. nc_dump_remove_wait_message() ↓
                if ($nc('#nc_dump_wait_message').length && !(/error/i.test(document.body.innerText || ''))) {
                    $nc('#nc_dump_connection_message').show();
                }
            }, 5000);
        });
    </script>
    <?php
}

/**
 * Убирает сообщение о необходимости ожидания, добавленное в nc_dump_flush_buffer()
 */
function nc_dump_remove_wait_message() {
    echo '<script>$nc("#nc_dump_wait_message").remove();</script>';
}

##################
# Backup of mysql database

define("HAR_LOCK_TABLE", 1);
define("HAR_FULL_SYNTAX", 2);
define("HAR_DROP_TABLE", 4);
define("HAR_NO_STRUCT", 8);
define("HAR_NO_DATA", 16);
define("HAR_ALL_OPTIONS", HAR_LOCK_TABLE | HAR_FULL_SYNTAX | HAR_DROP_TABLE);

define("HAR_ALL_DB", 1);
define("HAR_ALL_TABLES", 1);

define('OS_Unix', 'u');
define('OS_Windows', 'w');
define('OS_Mac', 'm');

class MYSQL_DUMP {

    var $dbhost = "";
    var $dbuser = "";
    var $dbpwd = "";
    var $database = null;
    var $dbport = "";
    var $dbsocket = "";
    var $charset = null;
    var $tables = null;
    var $conn = null;
    var $result = null;
    var $error = "";
    var $OS_FullName = null;
    var $lineEnd = null;
    var $OS_local = "";

    /**
     * Class Object
     *
     * @param string $host
     * @param string $user
     * @param string $dbpwd
     * @param string $port
     * @param string $socket
     * @param string $charset
     * @return MYSQL_DUMP
     */
    function MYSQL_DUMP($host = "", $user = "", $dbpwd = "", $port = "", $socket = "", $charset = "") {
        $this->setDBHost($host, $user, $dbpwd, $port, $socket, $charset);

        $this->OS_FullName = array(OS_Unix => 'UNIX', OS_Windows => 'WINDOWS', OS_Mac => 'MACOS');
        $this->lineEnd = array(OS_Unix => "\n", OS_Mac => "\r", OS_Windows => "\r\n");

        $this->OS_local = OS_Unix;
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->OS_local = OS_Windows;
        }
        elseif (strtoupper(substr(PHP_OS, 0, 3)) === 'MAC') {
            $this->OS_local = OS_Mac;
        }
    }

    /**
     * Set the database connection parameters
     *
     * @param $host
     * @param $user
     * @param $dbpwd
     * @param $port
     * @param $socket
     * @param $charset
     */
    function setDBHost($host, $user, $dbpwd, $port, $socket, $charset) {
        $this->dbhost = $host;
        $this->dbuser = $user;
        $this->dbpwd = $dbpwd;
        $this->dbport = $port ?: ini_get('mysqli.default_port');
        $this->dbsocket = $socket ?: ini_get('mysqli.default_socket');
        $this->charset = $charset;
    }

    /**
     * Return last error
     *
     * @return String
     */
    function error() {
        return $this->error;
    }

    public function dump_to_file($file, $database = HAR_ALL_DB, $tables = HAR_ALL_TABLES, $options = HAR_ALL_OPTIONS) {
        return $this->dumpDB($database, $tables, $options, $file);
    }

    /**
     * Take backup of the database
     *
     * @param Mixed $database (It can be string seperated by coma (,) or single database name on an array of database names
     * @param Mixed $tables (It can be string seperated by coma (,) or single table name on an array of table names
     * @param Int $options
     * @return String SQL Commands
     */
    function dumpDB($database = HAR_ALL_DB, $tables = HAR_ALL_TABLES, $options = HAR_ALL_OPTIONS, $file = false) {
        global $TMP_FOLDER;
        set_time_limit(0);
        $this->_connect();

        if (empty($database)) {
            $this->error = "Specify the database.";
            return false;
        }

        if (empty($tables)) {
            $this->error = "Specify the tables.";
            return false;
        }

        if ($database == HAR_ALL_DB) {
            $sql = "SHOW DATABASES";
            $this->result = @mysqli_query($this->conn, $sql);
            if (mysqli_error($this->conn) !== "") {
                $this->error = "Error : " . mysqli_error($this->conn);
                return false;
            }

            while ($row = mysqli_fetch_array($this->result, MYSQLI_NUM)) {
                $this->database[] = $row[0];
            }
        }
        else if (is_string($database)) {
            $this->database = @explode(",", $database);
        }

        $tmpFileName = $TMP_FOLDER . 'sql_tmp_' . md5(uniqid('', true)) . 'dat';
        $tmpFile = fopen($tmpFileName, 'w+');

        $lineEnd = $this->lineEnd[$this->OS_local];

        fwrite($tmpFile, "# MySql Dump" . $lineEnd);
        fwrite($tmpFile, "# Host: " . $this->dbhost . $lineEnd);
        fwrite($tmpFile, "# Time: " . date("Y.m.d H:i:s") . $lineEnd);

        $sql = "SELECT VERSION()";
        $this->result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_array($this->result, MYSQLI_NUM);
        fwrite($tmpFile, "# Server version " . $row[0] . $lineEnd);
        fwrite($tmpFile, "# -------------------------------------------------" . $lineEnd . $lineEnd);

        fwrite($tmpFile, "SET NAMES '" . $this->charset . "';" . $lineEnd . $lineEnd);

        for ($i = 0; $i < count($this->database); $i++) {
            if (count($this->database) > 1) {
                fwrite($tmpFile, "USE `" . $this->database[$i] . "`;" . $lineEnd . $lineEnd);
            }

            $this->result = @mysqli_query($this->conn, "USE `" . $this->database[$i] . "`");

            if (mysqli_error($this->conn) !== "") {
                $this->error = "Error : " . mysqli_error($this->conn);
                return false;
            }

            mysqli_query($this->conn, "SET NAMES '" . $this->charset . "'");

            $this->tables = array();
            if ($tables == HAR_ALL_TABLES) {
                $sql = "SHOW Tables";
                $this->result = @mysqli_query($this->conn, $sql);
                if (mysqli_error($this->conn) !== "") {
                    $this->error = "Error : " . mysqli_error($this->conn);
                    return false;
                }

                while ($row = mysqli_fetch_array($this->result, MYSQLI_NUM)) {
                    $this->tables[] = $row[0];
                }
            }
            else if (is_string($tables)) {
                $this->tables = @explode(",", $tables);
            }
            for ($j = 0; $j < count($this->tables); $j++) {
                if (($options & HAR_NO_STRUCT) != HAR_NO_STRUCT) {
                    $sql = "SHOW CREATE TABLE `" . $this->tables[$j] . "`";
                    $this->result = @mysqli_query($this->conn, $sql);
                    if (mysqli_error($this->conn) !== "") {
                        $this->error = "Error : " . mysqli_error($this->conn);
                        return false;
                    }
                    $row = mysqli_fetch_array($this->result, MYSQLI_NUM);


                    fwrite($tmpFile, " #" . $lineEnd);
                    fwrite($tmpFile, " # Table structure for table '" . $this->tables[$j] . "'" . $lineEnd);
                    fwrite($tmpFile, " #" . $lineEnd . $lineEnd);

                    if (($options & HAR_DROP_TABLE) == HAR_DROP_TABLE) {
                        fwrite($tmpFile, "DROP TABLE IF EXISTS `" . $this->tables[$j] . "`;" . $lineEnd);
                    }
                    fwrite($tmpFile, $row[1] . ";" . $lineEnd . $lineEnd . $lineEnd);
                }

                if (($options & HAR_NO_DATA) != HAR_NO_DATA) {
                    fwrite($tmpFile, " #" . $lineEnd);
                    fwrite($tmpFile, " # Dumping data for table '" . $this->tables[$j] . "'" . $lineEnd);
                    fwrite($tmpFile, " #" . $lineEnd . $lineEnd);

                    if (($options & HAR_LOCK_TABLE) == HAR_LOCK_TABLE) {
                        fwrite($tmpFile, "LOCK TABLES `" . $this->tables[$j] . "` WRITE;" . $lineEnd);
                    }

                    $temp_sql = "INSERT INTO `" . $this->tables[$j];
                    if (($options & HAR_FULL_SYNTAX == HAR_FULL_SYNTAX)) {
                        $sql = "SHOW COLUMNS FROM " . $this->tables[$j];
                        $this->result = @mysqli_query($this->conn, $sql);
                        if (mysqli_error($this->conn) !== "") {
                            $this->error = "Error : " . mysqli_error($this->conn);
                            return false;
                        }
                        $fields = array();
                        $fields_null = array();
                        while ($row = mysqli_fetch_array($this->result, MYSQLI_NUM)) {
                            $fields[] = $row[0];
                            $fields_null[] = $row[2];
                        }
                        $temp_sql .= '` (`' . @implode('`,`', $fields) . '`)';
                    }

                    $sql = "SELECT * FROM " . $this->tables[$j];
                    $this->result = @mysqli_query($this->conn, $sql);
                    if (mysqli_error($this->conn) !== "") {
                        $this->error = "Error : " . mysqli_error($this->conn);
                        return false;
                    }
                    while ($row = mysqli_fetch_array($this->result, MYSQLI_NUM)) {
                        foreach ($row as $key => $value) {
                            $row[$key] = mysqli_real_escape_string($this->conn, $value);
                        }

                        fwrite($tmpFile, $temp_sql . ' VALUES (');
                        foreach ($row as $key => $value) {
                            fwrite($tmpFile, $key != 0 ? ',' : '');
                            if ($fields_null[$key] == 'YES' && !$row[$key]) {
                                fwrite($tmpFile, 'NULL');
                            }
                            else {
                                fwrite($tmpFile, '"' . $row[$key] . '"');
                            }
                        }
                        fwrite($tmpFile, ');' . $lineEnd);
                        #                           if ($this->tables[$j]!='Sub_Class') $returnSql="";
                        #                           if ($this->tables[$j]=='Sub_Class') echo $returnSql."<br><br>";
                        #$returnSql .=$temp_sql.' VALUES ("'.@implode('","',$row).'");'.$lineEnd;
                    }
                    if (($options & HAR_LOCK_TABLE) == HAR_LOCK_TABLE) {
                        fwrite($tmpFile, "UNLOCK TABLES;" . $lineEnd);
                    }

                }

                fwrite($tmpFile, $lineEnd . $lineEnd);
            }
        }

        fclose($tmpFile);

        if ($file) {
            $result = null;
            rename($tmpFileName, $file);
        }
        else {
            $result = file_get_contents($tmpFileName);
            unlink($tmpFileName);
        }

        return $result;
    }

    /**
     * Save the sql file on server
     *
     * @param String $sql
     * @param String $sqlfile
     * @return Boolean
     */
    function save_sql($sql, $sqlfile = "") {
        if (empty($sqlfile)) {
            $sqlfile = @implode("_", $this->database) . ".sql";
        }
        $fp = @fopen($sqlfile, "wb");
        if (!is_resource($fp)) {
            $this->error = "Error: Unable to save file.";
            return false;
        }
        @fwrite($fp, $sql);
        @fclose($fp);
        return true;
    }

    /**
     * force to download the sql file
     *
     * @param String $sql
     * @param String $sqlfile
     * @return Boolean
     */
    function download_sql($sql, $sqlfile = "") {
        if (empty($sqlfile)) {
            $sqlfile = @implode("_", $this->database) . ".sql";
        }
        @header("Cache-Control: "); // leave blank to avoid IE errors
        @header("Pragma: "); // leave blank to avoid IE errors
        @header("Content-type: application/octet-stream");
        @header("Content-type: application/octet-stream");
        @header("Content-Disposition: attachment; filename=" . $sqlfile);
        echo $sql;
    }

    /**
     * Restore the backup file
     *
     * @param String $sqlfile
     * @param string $dbname
     * @return Boolean
     */
    function restoreDB($sqlfile, $dbname) {
        $this->error = "";
        $this->_connect();

        if (!is_file($sqlfile)) {
            $this->error = "Error : Not a valid file.";
            return false;
        }

        mysqli_select_db($this->conn, $dbname);

        //          $lines=@file($sqlfile);

        $file_size = filesize($sqlfile);
        if (!$file_size) {
            $uploadMsg = "Sql File is empty.";
        }
        else {
            $fp = fopen($sqlfile, "r");
            $sql = "";
            //              foreach($lines as $line)
            while (!feof($fp)) {
                $line = fgets($fp);
                $sql .= trim($line);
                if (empty($sql)) {
                    $sql = "";
                    continue;
                }
                elseif (preg_match("/^[#-].*+\r?\n?/i", trim($line))) {
                    $sql = "";
                    continue;
                }
                elseif (!preg_match("/;[\r\n]+/", $line)) {
                    continue;
                }

                mysqli_query($this->conn, $sql);
                if (mysqli_error($this->conn) != "") {
                    $this->error .= "<br>" . mysqli_error($this->conn);
                }

                $sql = "";
            }
            fclose($fp);
            if (!empty($this->error)) {
                return false;
            }
            return true;
        }
    }

    function _connect() {
        if (!is_resource($this->conn)) {
            $this->conn = @mysqli_connect($this->dbhost, $this->dbuser, $this->dbpwd, '', $this->dbport, $this->dbsocket);
        }
        if (!is_resource($this->conn)) {
            $this->error = mysqli_connect_error();
            return false;
        }

        mysqli_query($this->conn, "SET NAMES '" . $this->charset . "'");
        mysqli_query($this->conn, "SET SQL_QUOTE_SHOW_CREATE = 1");

        return $this->conn;
    }

}

# End of Backup of mysql database
