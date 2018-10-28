<?

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)) . ( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER . "vars.inc.php");
require ($ADMIN_FOLDER . "function.inc.php");
require ($ADMIN_FOLDER . "patch/function.inc.php");
require ($ADMIN_FOLDER . "install.inc.php");
require ($ADMIN_FOLDER . "tar.inc.php");

global $db;

$Delimeter = " &gt ";
$main_section = "settings";
$item_id = 4;
$Title2 = TOOLS_PATCH;
$Title3 = "<a href=" . $ADMIN_PATH . "patch/>" . TOOLS_PATCH . "</a>";
$Title4 = TOOLS_PATCH_CHEKING;
$Title5 = TOOLS_PATCH_INSTRUCTION;

if (!$phase) $phase = 1;

$UI_CONFIG = new ui_config_tool(TOOLS_PATCH, TOOLS_PATCH, "i_tool_patch_big.gif", "tools.patch" . ($phase && $phase != 5 ? "(" . $phase . ")" : ""));
$UI_CONFIG->tabs[] = array(
    "id"       => "path-info",
    "caption"  => TOOLS_PATCH_INSTRUCTION_TAB,
    "location" => 'tools.patch(5)'
);

/*
// установка обновления не доступна в демо-версии
if ($nc_core->is_trial && $phase != 5) {
    BeginHtml($Title2, $Title2, "http://" . $DOC_DOMAIN . "/settings/patch/");
    $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
    nc_print_status(TOOLS_PATCH_NOTAVAIL_DEMO, 'error');
    print TOOLS_ACTIVATION_DEMO_DISABLED;
    EndHtml();
    exit();
}
*/


$perm->ExitIfNotAccess(NC_PERM_PATCH, 0, 0, 0, 0);


if (in_array($phase, array(2, 4))) {
    if (!$nc_core->token->verify()) {
        BeginHtml($Title13, $Title3 . $Delimeter . $Title6 . $Delimeter . $Title13, "http://" . $DOC_DOMAIN . "/management/sites/settings/");
        nc_print_status(NETCAT_TOKEN_INVALID, 'error');
        EndHtml();
        exit;
    }
}

switch ($phase) {
    # список установленных обновлений
    case 1:
        BeginHtml($Title2, $Title2, "http://" . $DOC_DOMAIN . "/settings/patch/");
        $perm->ExitIfNotAccess(NC_PERM_PATCH, 0, 0, 0, 0);
        break;

    # процедура установки обновления
    case 2:
        BeginHtml($Title2, $Title2, "http://" . $DOC_DOMAIN . "/settings/patch/");
        $perm->ExitIfNotAccess(NC_PERM_PATCH, 0, 0, 0, 1);

        $err = 0;

        if (!checkPermissions($HTTP_ROOT_PATH, $DOCUMENT_ROOT))
            break;
        if (!checkPermissions($HTTP_ROOT_PATH . "tmp/", $DOCUMENT_ROOT))
            break;

        if (!$_FILES['FilePatch']['tmp_name']) {
            InstallationAborted(TOOLS_MODULES_ERR_NOTUPLOADED . ".", 'patch');
        }

        $PatchFile = tempnam($TMP_FOLDER, "patch") . '.tgz';
        move_uploaded_file($_FILES['FilePatch']['tmp_name'], $PatchFile);

        // this temporary file will be deleted in the end of this function or in InstallationAborted()
        if (!nc_tgz_extract($PatchFile, $TMP_FOLDER) && !file_exists($TMP_FOLDER . "id.txt")) {
            InstallationAborted(TOOLS_PATCH_ERR_EXTRACT, 'patch');
        }

        # проверка файлов в патче
        if (!CheckDeps('patch')) {
            InstallationAborted(TOOLS_PATCH_INFO_NOTINSTALLED, 'patch');
        }

        $current_minor_version  = preg_replace('/^(\d+\.\d+).*$/', "$1", $VERSION_ID);
        $required_minor_version = preg_replace('/^(\d+\.\d+).*$/', "$1", $VersionID);
        if ($required_minor_version != $current_minor_version) {
            InstallationAborted(str_replace(array("%REQUIRE", "%EXIST"), array($required_minor_version, $current_minor_version), TOOLS_PATCH_INVALIDVERSION), 'patch');
        }

        if ($SystemID && $SystemID != $SYSTEM_ID) {
            list($required_sys_name, $required_sys_color) = nc_system_name_by_id($SystemID);
            list($installed_sys_name, $installed_sys_color) = nc_system_name_by_id($SYSTEM_ID);
            InstallationAborted(str_replace(array("%REQUIRE", "%EXIST"), array($required_sys_name, $installed_sys_name), TOOLS_PATCH_INVALIDVERSION), 'patch');
        }

        if (IsAlreadyInstalled('patch')) {
            InstallationAborted(TOOLS_PATCH_ALREDYINSTALLED, 'patch');
        }

        $InstallFile = $TMP_FOLDER . "install.inc.php";
        if (!is_readable($InstallFile)) {
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN . " install.inc.php.", 'patch');
        } else {
            # подключение файла инсталятора
            require ($InstallFile);
        }

        if (!is_readable($TMP_FOLDER . "id.txt")) {
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN . " id.txt", 'patch');
        }

        if (!is_readable($TMP_FOLDER . "files.txt")) {
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN . " files.txt", 'patch');
        }

        if (!is_readable($TMP_FOLDER . "sql.txt")) {
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN . " sql.txt", 'patch');
        }

        if (!is_readable($TMP_FOLDER . "required.txt")) {
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN . " required.txt", 'patch');
        }

        if (!is_readable($TMP_FOLDER . "symlinks.txt")) {
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN . " symlinks.txt", 'patch');
        }


        $Ability = CheckAbilityOfInstallation();
        if ($Ability["Success"] == 0) {
            InstallationAborted(TOOLS_PATCH_ERROR . ": " . $Ability["ErrorMessage"], 'patch');
        }

        $Install = InstallPatch();
        if ($Install["Success"] == 0) {
            InstallationAborted(TOOLS_PATCH_ERROR_DURINGINSTALL . ": " . $Install["ErrorMessage"], 'patch');
        }

        /* обработка файлов
         * - files.txt
         * - sql.txt
         * - symlinks.txt
         */
        # копирование файлов из files.txt
        $CopyFilesResult = CopyFiles();
        # обработка SQL-запросов из файлов sql.txt (по одному на строчку!)
        $ExecSQLResult = ExecSQL($TMP_FOLDER . "sql.txt");
        # обработка symlinks.txt
        $CreatLinksResult = CreatLinks();

        # прописываем новый патч в базу
        $inserted = $db->query("INSERT INTO `Patch`
			(`Patch_Name`, `Created`, `Description`)
			VALUES
			('" . $db->escape($PatchName) . "', '" . date("Ymd") . "', '" . $db->escape($Description) . "')");
        # установим переменную, чтобы не вылетело окно о неустановленном обновлении, после обновления
        if ($db->insert_id && $PatchName != 'current') {
            $LAST_LOCAL_PATCH = $PatchName;
        }

        # результаты работы патча
        $PatchResult = array();
        $PatchResultText = "";
        # результаты
        if ($CopyFilesResult['files'])
            $PatchResult['files'] = str_replace("%COUNT", $CopyFilesResult['files'], TOOLS_PATCH_INFO_FILES_COPIED);
        if ($ExecSQLResult['sqls'])
            $PatchResult['sqls'] = str_replace("%COUNT", $ExecSQLResult['sqls'], TOOLS_PATCH_INFO_QUERIES_EXEC);
        if ($CreatLinksResult['links'])
            $PatchResult['links'] = str_replace("%COUNT", $CreatLinksResult['links'], TOOLS_PATCH_INFO_SYMLINKS_EXEC);
        # текст результата
        if (!empty($PatchResult))
            $PatchResultText = join("\r\n", $PatchResult) . "\r\n";

        # действия после установки патча, прописываются в файле install.inc.php
        if (function_exists("InstallPatchAfterAction")) {
            InstallPatchAfterAction();
        }

        # удаление инсталляционных файлов
        DeleteFilesInDirectory($TMP_FOLDER);

        # сообщение об успешной установке патча
        nc_print_status(TOOLS_PATCH_INSTALLED . ".", "ok");

        // обновление настроек системы для последующего обновления сведений о доступных патчах
        // (в частности — нужен актуальный LastPatchBuildNumber)
        nc_core::get_object()->get_settings(null, null, true);

        // обновление сведений о доступных патчах
        CheckForNewPatch();

        break;

    case 3:
        BeginHtml($Title4, $Title3 . $Delimeter . $Title4, "http://" . $DOC_DOMAIN . "/settings/patch/");
        $perm->ExitIfNotAccess(NC_PERM_PATCH, 0, 0, 0, 1);
        CheckForNewPatch();
        break;

    // online patch installing
    case 4:
        BeginHtml($Title4, $Title3 . $Delimeter . $Title4, "http://" . $DOC_DOMAIN . "/settings/patch/");
        $perm->ExitIfNotAccess(NC_PERM_PATCH, 0, 0, 0, 1);
        // get patch data
        $patch_file_data = nc_patch_get_patch();
        // get gzip file
        $patch_file_tgz = !empty($patch_file_data) ? $patch_file_data['_FILE'] : "";

        if (!$patch_file_tgz)
            break;
        if (!file_exists($TMP_FOLDER . $patch_file_tgz))
            break;

        if (!checkPermissions($HTTP_ROOT_PATH . "tmp/", $DOCUMENT_ROOT))
            break;

        // this temporary file will be deleted in the end of this function or in InstallationAborted()
        if (!nc_tgz_extract($TMP_FOLDER . $patch_file_tgz, $TMP_FOLDER) && !file_exists($TMP_FOLDER . "id.txt")) {
            InstallationAborted(TOOLS_PATCH_ERR_EXTRACT, 'patch');
        }

        // check files in patch
        if (!CheckDeps('patch')) {
            InstallationAborted(TOOLS_PATCH_INFO_NOTINSTALLED, 'patch');
        }

        // other version
        $current_minor_version  = preg_replace('/^(\d+\.\d+).*$/', "$1", $VERSION_ID);
        $required_minor_version = preg_replace('/^(\d+\.\d+).*$/', "$1", $VersionID);
        if ($required_minor_version != $current_minor_version) {
            InstallationAborted(str_replace(array("%REQUIRE", "%EXIST"), array($required_minor_version, $current_minor_version), TOOLS_PATCH_INVALIDVERSION), 'patch');
        }

        // other system
        if ($SystemID && $SystemID != $SYSTEM_ID) {
            list($required_sys_name, $required_sys_color) = nc_system_name_by_id($SystemID);
            list($installed_sys_name, $installed_sys_color) = nc_system_name_by_id($SYSTEM_ID);
            InstallationAborted(str_replace(array("%REQUIRE", "%EXIST"), array($required_sys_name, $installed_sys_name), TOOLS_PATCH_INVALIDVERSION), 'patch');
        }
        // already installed
        if (IsAlreadyInstalled('patch')) {
            InstallationAborted(TOOLS_PATCH_ALREDYINSTALLED, 'patch');
        }
        // install.inc.php not readable or include them
        $InstallFile = $TMP_FOLDER . "install.inc.php";
        if (!is_readable($InstallFile)) {
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN . " install.inc.php.", 'patch');
        } else {
            require ($InstallFile);
        }
        // files in patch
        if (!is_readable($TMP_FOLDER . "id.txt")) {
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN . " id.txt", 'patch');
        }
        if (!is_readable($TMP_FOLDER . "files.txt")) {
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN . " files.txt", 'patch');
        }
        // check files by listing in files.txt
        if (!nc_patch_check_files_by_list($TMP_FOLDER . "files.txt")) {
            InstallationAborted(TOOLS_PATCH_ERROR_FILELIST_NOT_WRITABLE . "<br/><br/>" . TOOLS_PATCH_ERROR_AUTOINSTALL . "<br/><br/><a href='" . $patch_file_data['LINK'] . "'>" . TOOLS_PATCH_DOWNLOAD_LINK_DESCRIPTION . "</a>", 'patch');
        }
        if (!is_readable($TMP_FOLDER . "sql.txt")) {
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN . " sql.txt", 'patch');
        }
        if (!is_readable($TMP_FOLDER . "required.txt")) {
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN . " required.txt", 'patch');
        }
        if (!is_readable($TMP_FOLDER . "symlinks.txt")) {
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN . " symlinks.txt", 'patch');
        }

        $Ability = CheckAbilityOfInstallation();
        if ($Ability["Success"] == 0) {
            InstallationAborted(TOOLS_PATCH_ERROR . ": " . $Ability["ErrorMessage"], 'patch');
        }

        $Install = InstallPatch();
        if ($Install["Success"] == 0) {
            InstallationAborted(TOOLS_PATCH_ERROR_DURINGINSTALL . ": " . $Install["ErrorMessage"], 'patch');
        }

        // copy files from files.txt
        $CopyFilesResult = CopyFiles();
        // execute queries from sql.txt, one per string
        $ExecSQLResult = ExecSQL($TMP_FOLDER . "sql.txt");
        // symlinks.txt
        $CreatLinksResult = CreatLinks();

        // insert patch info into the database
        $inserted = $db->query("INSERT INTO `Patch`
			(`Patch_Name`, `Created`, `Description`)
			VALUES ('" . $db->escape($PatchName) . "', NOW(), '" . $db->escape($Description) . "')");
        // don't show update notice once more
        if ($db->insert_id) {
            $LAST_LOCAL_PATCH = $PatchName;
        }

        $PatchResult = array();
        // results
        if ($CopyFilesResult['files']) {
            $PatchResult['files'] = str_replace("%COUNT", $CopyFilesResult['files'], TOOLS_PATCH_INFO_FILES_COPIED);
        }
        if ($ExecSQLResult['sqls']) {
            $PatchResult['sqls'] = str_replace("%COUNT", $ExecSQLResult['sqls'], TOOLS_PATCH_INFO_QUERIES_EXEC);
        }
        if ($CreatLinksResult['links']) {
            $PatchResult['links'] = str_replace("%COUNT", $CreatLinksResult['links'], TOOLS_PATCH_INFO_SYMLINKS_EXEC);
        }
        // result text
        if (!empty($PatchResult))
            $PatchResultText = join("\r\n", $PatchResult) . "\r\n";

        // after action in install.inc.php
        if (function_exists("InstallPatchAfterAction")) {
            InstallPatchAfterAction();
        }

        // clear tmp folder
        DeleteFilesInDirectory($TMP_FOLDER);
        // patch installed OK
        nc_print_status(TOOLS_PATCH_INSTALLED . ".", "ok");

        break;

    case 5:
        BeginHtml($Title5, $Title5, "http://" . $DOC_DOMAIN . "/settings/patch/");
        $perm->ExitIfNotAccess(NC_PERM_PATCH, 0, 0, 0, 1);
        $UI_CONFIG->activeTab    = 'path-info';
        $UI_CONFIG->headerText   = TOOLS_PATCH_INSTRUCTION;
        $UI_CONFIG->locationHash = '#tools.patch(5)';

        $lang   = 'ru';
        $encode = $nc_core->NC_UNICODE ? 'utf8' : 'cp1251';
        require $ADMIN_FOLDER . "patch/information.{$lang}.{$encode}.php";

        EndHtml();
        exit;
}

PatchForm();
PatchList();
EndHtml();
?>