<?php

if ($dump_options['mode'] == 'full') {
    $known_arrays = array('everything', 'images', 'modules', 'netcat_template', 'netcat_files');
} else {
    $known_arrays = array('images', 'modules', 'netcat_template', 'netcat_files');
}

function get_lang() {
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    switch ($lang) {
        case 'en':
        case 'ru':
            break;
        default:
            $lang = 'en';
    }
    return $lang;
}

function get_tr() {
    $en['header'] = 'NetCat system restore';
    $en['mysqlsettings'] = 'Please, specify your MySQL settings:';
    $en['restore_button'] = 'Restore backup';
    $en['hostname'] = 'Hostname';
    $en['username'] = 'User';
    $en['password'] = 'Password';
    $en['dbname'] = 'Database name';
    $en['port'] = 'Port';
    $en['socket'] = 'Socket';
    $en['charset'] = 'Charset';
    $en['nosuchfile'] = 'no such file';
    $en['ismissing'] = 'is missing';
    $en['restorefailed'] = 'Restore failed due to an errors';
    $en['extracting'] = 'Extracting';
    $en['done'] = 'done';
    $en['err_nosettings'] = 'You did not specified all required SQL settings. Please do.';
    $en['err_restore'] = 'Error while restoring SQL dump';
    $en['finish_text'] = 'Restore completed successfully. <br /><br /><b>IMPORTANT:</b> remove restore.php, netcat.sql and *.tgz archives from your site!<br /><br />To open administration panel, open ';
    $en['finish_link_title'] = 'this link';
    $en['restore_init'] = 'Running restore, please wait...';
    $en['dbrestore'] = 'Restoring database...';


    $ru['header'] = 'Восстановление NetCat';
    $ru['mysqlsettings'] = 'Пожалуйста, укажите настройки MySQL:';
    $ru['restore_button'] = 'Восстановить из резервной копии';
    $ru['hostname'] = 'Хост';
    $ru['username'] = 'Имя пользователя';
    $ru['password'] = 'Пароль';
    $ru['dbname'] = 'Имя базы данных';
    $ru['port'] = 'Порт';
    $ru['socket'] = 'Сокет';
    $ru['charset'] = 'Кодировка';
    $ru['nosuchfile'] = 'файл не найден';
    $ru['ismissing'] = 'отсутствует';
    $ru['restorefailed'] = 'Восстановление прервано из-за ошибок';
    $ru['extracting'] = 'Распаковка';
    $ru['done'] = 'готово';
    $ru['err_nosettings'] = 'Вы не указали все необходимые настройки MySQL. Пожалуйста, сделайте это.';
    $ru['err_restore'] = 'При восстановлении базы данных MySQL произошла ошибка';
    $ru['finish_text'] = 'Восстановление успешно завершено. <br /><br /><b>ВАЖНО:</b> обязательно удалите файлы restore.php, netcat.sql и архивы *.tgz из корня вашего сайта!<br /><br />Чтобы открыть панель администрирования, откройте ';
    $ru['finish_link_title'] = 'эту ссылку';
    $ru['restore_init'] = 'Выполняется восстановление, подождите пожалуйста...';
    $ru['dbrestore'] = 'Восстановление базы данных...';


    $lang = get_lang();

    switch ($lang) {
        case 'en': $str = $en;
            break;
        case 'ru': $str = $ru;
            break;
        default: $str = $en;
            break;
    }
    return $str;
}

function check($known_arrays) {
    // To restore whole system, we need all of these archives, and an SQL dump
    // Also, we need a working tar to do this.
    // Let's check that:

    $str = get_tr();
    $errors = array();
    foreach ($known_arrays as $item) {
        if (!file_exists($item . '.tgz')) {
            $errors[] = $item . '.tgz: ' . $str['nosuchfile'];
        }
    }
    if (!file_exists('netcat.sql')) {
        $errors[] = 'netcat.sql ' . $str['ismissing'];
    }


    if (count($errors) > 0) {
        foreach ($errors as $e) {
            echo $e . "<br />\n";
        }
        return $str['restorefailed'];
    } else
        return true;
}

function tgz_check_exec() {
    // check whether to use system() call to tar [faster]
    if (!preg_match('/Windows/i', php_uname())) {  // it's not Windows
        $err_code = 127;
        $tgz_version = @exec('tar --version', $output, $err_code);
        define('SYSTEM_TAR', ($err_code ? false : true));
    } else {
        define('SYSTEM_TAR', false);
    }
}

function extract_files($known_arrays) {
    // Check what type of tar to use
    tgz_check_exec();
    $str = get_tr();

    // If all OK, let's make some fun :)
    // Extract stuff
    foreach ($known_arrays as $item) {
        echo $str['extracting'] . $item . '.tgz...' . "\n";
        flush();
        if (SYSTEM_TAR) {
            nl2br(system('tar xf ' . $item . '.tgz 2>&1', $unpack_result));
        } else {
            $tar_object = new Archive_Tar($item . '.tgz', 'gz');
            $tar_object->setErrorHandling(PEAR_ERROR_PRINT);
            $tar_object->extract('.');
        }
        echo $str['done'] . "<br />\n";
        flush();
    }
}

// Guess DB connection settings from vars.inc.php
function guess_sql_settings() {
    if (!file_exists('vars.inc.php')) {
        system('tar xf everything.tgz ./vars.inc.php >/dev/null 2>&1');
    }
    if (file_exists('vars.inc.php')) {
        require_once 'vars.inc.php';
        /** @var $MYSQL_USER */
        /** @var $MYSQL_PASSWORD */
        /** @var $MYSQL_HOST */
        /** @var $MYSQL_DB_NAME */
        /** @var $MYSQL_CHARSET */
        /** @var $MYSQL_PORT */
        /** @var $MYSQL_SOCKET */
        return array($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DB_NAME, $MYSQL_PORT, $MYSQL_SOCKET, $MYSQL_CHARSET);
    } else {
        return NULL;
    }
}

// Import SQL dump
function restore_dump($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DB_NAME, $MYSQL_PORT, $MYSQL_SOCKET, $MYSQL_CHARSET) {
    // Try via command-line mysql first
    $err_code = 127;

    $path = dirname(__FILE__) . DIRECTORY_SEPARATOR;

    $exec_str = 'mysql --host=' . escapeshellarg($MYSQL_HOST) .
      ' --user=' . escapeshellarg($MYSQL_USER) .
      ' --password=' . escapeshellarg($MYSQL_PASSWORD) .
      ' --default-character-set=' . $MYSQL_CHARSET . ' ' .
      '  ' . escapeshellarg($MYSQL_DB_NAME) . ' < ' . $path . 'netcat.sql 2>&1';
    exec($exec_str, $output, $err_code);
    if ($err_code) {
        $mysql_dump = new MYSQL_DUMP($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_PORT, $MYSQL_SOCKET, $MYSQL_CHARSET);

        if ($mysql_dump->restoreDB($path . 'netcat.sql', $MYSQL_DB_NAME) == false) {
            echo 'Failed to import MySQL dump:' . "<br />\n";
            echo $mysql_dump->error();
            echo 'Cmdline output: ' . "\n<br />\n";
            foreach ($output as $l) {
                echo $l . "\n<br />";
            }
            echo "\n<br />\n";
            return false;
        }
    }
    return true;
}

function update_varsinc($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DB_NAME, $MYSQL_PORT, $MYSQL_SOCKET, $MYSQL_CHARSET) {
    $code = file_get_contents('vars.inc.php');
    $c = array('MYSQL_HOST' => $MYSQL_HOST, 'MYSQL_USER' => $MYSQL_USER, 'MYSQL_PASSWORD' => $MYSQL_PASSWORD, 'MYSQL_DB_NAME' => $MYSQL_DB_NAME, 'MYSQL_PORT' => $MYSQL_PORT, 'MYSQL_SOCKET' => $MYSQL_SOCKET, 'MYSQL_CHARSET' => $MYSQL_CHARSET);
    foreach ($c as $key => $value) {
        $code = preg_replace('/\$' . $key . '\s*=.*/', '$' . $key . ' = "' . $value . '";', $code);
    }
    file_put_contents('vars.inc.php', $code);
}

function show_form($dump_options) {
    global $known_arrays; // Array of archive names that may present in backup. FIXME: change variable name to something related with it's meaning!
    list($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DB_NAME, $MYSQL_PORT, $MYSQL_SOCKET, $MYSQL_CHARSET) = guess_sql_settings();

    $str = get_tr();


    echo '<h1>' . $str['header'] . '</h1>';

    // Check if files exists
    if ($dump_options['mode'] != 'sql') {
        $check = check($known_arrays);
    } else {
        $check = true;
    }
    if ($check !== true) {
        die($check);
    }

    echo '<p>' . $str['mysqlsettings'] . '</p>
		<form method="post"><table>';
    $opts = array(
      array($str['hostname'], 'mysql_host', $MYSQL_HOST),
      array($str['username'], 'mysql_user', $MYSQL_USER),
      array($str['password'], 'mysql_password', $MYSQL_PASSWORD),
      array($str['dbname'], 'mysql_db_name', $MYSQL_DB_NAME),
      array($str['port'], 'mysql_port', $MYSQL_PORT),
      array($str['socket'], 'mysql_socket', $MYSQL_SOCKET),
      array($str['charset'], 'mysql_charset', $MYSQL_CHARSET)
    );
    foreach ($opts as $a) {
        echo '<tr><td>' . $a[0] . ':</td><td><input type="text" name="' . $a[1] . '" value="' . htmlspecialchars($a[2]) . '" /></td></tr>';
    }
    echo '</table>
		<input type="hidden" name="execute_backup_restore" value="1" />
		<input type="submit" value="' . $str['restore_button'] . '" />
		</form>';
}

function add_tmp_dirs() {
    if (!file_exists('netcat_dump')) {
        mkdir('netcat_dump', 0777, true);
        $fp = fopen('netcat_dump/index.html', 'w');
        fwrite($fp, ' ');
        fclose($fp);
    }
    if (!file_exists('netcat/tmp')) {
        mkdir('netcat/tmp', 0777, true);
        $fp = fopen('netcat/tmp/index.html', 'w');
        fwrite($fp, ' ');
        fclose($fp);
    }
}

function execute_backup_restore($dump_options) {
    global $ADMIN_PATH;

    // Disable time limit
    @set_time_limit(0);
    $str = get_tr();

    echo '<h1>' . $str['restore_init'] . "</h1>\n";
    flush();

    global $known_arrays; // Array of archive names that may present in backup. FIXME: change variable name to something related with it's meaning!
    if (!isset($_POST['mysql_host'], $_POST['mysql_user'], $_POST['mysql_password'], $_POST['mysql_db_name'], $_POST['mysql_charset'], $_POST['mysql_port'], $_POST['mysql_socket']) || !$_POST['mysql_db_name']) {
        echo '<p class="error">' . $str['err_nosettings'] . '</p>';
        return show_form($dump_options);
    }

    // Get MySQL settings
    $MYSQL_HOST = trim($_POST['mysql_host']);
    if ($MYSQL_HOST === '') {
        $MYSQL_HOST = 'localhost';
    }
    $MYSQL_USER = trim($_POST['mysql_user']);
    $MYSQL_PASSWORD = $_POST['mysql_password'];
    $MYSQL_DB_NAME = trim($_POST['mysql_db_name']);
    $MYSQL_CHARSET = trim($_POST['mysql_charset']);
    $MYSQL_PORT = trim($_POST['mysql_port']);
    $MYSQL_SOCKET = trim($_POST['mysql_socket']);
    if ($MYSQL_CHARSET === '') {
        $MYSQL_CHARSET = 'utf8';
    }

    // Restore database dump
    echo $str['dbrestore'] . "\n";
    flush();
    $dump_result = restore_dump($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DB_NAME, $MYSQL_PORT, $MYSQL_SOCKET, $MYSQL_CHARSET);
    if ($dump_result !== true) {
        echo '<p class="error">' . $str['err_restore'] . ': ' . $dump_result . '</p>';
        return show_form($dump_options);
    }
    echo $str['done'] . '<br />';
    flush();

    if ($dump_options['mode'] != 'sql') {
        // Extract files
        extract_files($known_arrays);

        // Update vars.inc.php
        update_varsinc($MYSQL_HOST, $MYSQL_USER, $MYSQL_PASSWORD, $MYSQL_DB_NAME, $MYSQL_PORT, $MYSQL_SOCKET, $MYSQL_CHARSET);
        add_tmp_dirs();
    }
    echo '<p>' . $str['finish_text'] . ' <a href="' . (!empty($ADMIN_PATH) ? $ADMIN_PATH : "%ADMIN_PATH%") . '">' . $str['finish_link_title'] . '</a>.</p>';
}
?>