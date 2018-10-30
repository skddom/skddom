<?php

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ADMIN_FOLDER."function.inc.php");
require_once ($ADMIN_FOLDER."modules/function.inc.php");
require_once ($ADMIN_FOLDER."tar.inc.php");
require_once ($ADMIN_FOLDER."modules/ui.php");
require_once ($ADMIN_FOLDER."install.inc.php");

#LoadEnv();

$main_section = "settings";
$item_id = 3;
$Delimeter = " &gt ";
$Title2 = TOOLS_MODULES;
$Title3 = "<a href=\"".$ADMIN_PATH."modules/\">".TOOLS_MODULES."</a>";

if ($module_name && preg_match("/^\w+$/", $module_name)) {
    $ModuleID = $db->get_var("SELECT `Module_ID` FROM `Module` WHERE `Keyword` = '".$db->escape($module_name)."'");
}

if ($ModuleID) $Title4 = GetModuleName($ModuleID);


// установка модуля не доступна в демо-версии
if ($nc_core->is_trial && ($phase == 4 || $phase == 5)) {
    BeginHtml($Title2, $Title2, "http://".$DOC_DOMAIN."/settings/modules/");
    $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
    $UI_CONFIG = new ui_config_tool(TOOLS_MODULES_LIST, TOOLS_MODULES_LIST, 'i_modules_big.gif', 'module.list');
    nc_print_status(TOOLS_PATCH_NOTAVAIL_DEMO, 'error');
    print NETCAT_PERSONAL_MODULE_DESCRIPTION;
    EndHtml();
    exit();
}



if (!isset($phase)) $phase = 1;

if (in_array($phase, array(3, 4, 6))) {
    if (!$nc_core->token->verify()) {
        BeginHtml($Title2, $Title2, "http://".$DOC_DOMAIN."/settings/modules/");
        nc_print_status(NETCAT_TOKEN_INVALID, 'error');
        EndHtml();
        exit;
    }
}


switch ($phase) {
    case 1:
        # покажем список модулей
        BeginHtml($Title2, $Title2, "http://".$DOC_DOMAIN."/settings/modules/");
        $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 0);
        $UI_CONFIG = new ui_config_tool(TOOLS_MODULES_LIST, TOOLS_MODULES_LIST, 'i_modules_big.gif', 'module.list');
        $UI_CONFIG->treeMode = 'modules';
        ModuleList();
        break;

    case 2:
        # покажем форму редактирования
        $custom_location = nc_Core::get_object()->modules->get_vars($module_name, 'ADMIN_SETTINGS_LOCATION');
        if ($custom_location) {
            die("<script>top.urlDispatcher.load('#$custom_location')</script>");
        }

        BeginHtml($Title2, $Title3.$Delimeter.$Title4, "http://".$DOC_DOMAIN.GetHelpURL($ModuleID));
        $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 0);
        $UI_CONFIG = new ui_config_tool(TOOLS_MODULES, TOOLS_MODULES, 'i_modules_big.gif', 'tools.installmodule');
        $UI_CONFIG->treeMode = 'modules';
        ModuleUpdateForm($ModuleID);

        break;

    case 3:
        # запишем результаты редактирования
        BeginHtml($Title2, $Title2, "http://".$DOC_DOMAIN."/settings/modules/");
        $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
        $UI_CONFIG = new ui_config_tool(TOOLS_MODULES, TOOLS_MODULES, 'i_modules_big.gif', 'tools.installmodule');
        $UI_CONFIG->treeMode = 'modules';
        ModuleUpdateCompleted();
        ModuleUpdateForm($ModuleID);


        break;

    case 4:
        # добавим модуль
        BeginHtml($Title2, $Title2, "http://".$DOC_DOMAIN."/settings/modules/");
        $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
        if (!checkPermissions($HTTP_ROOT_PATH."tmp/", $DOCUMENT_ROOT)) break;
        if (!checkPermissions($HTTP_ROOT_PATH."modules/", $DOCUMENT_ROOT))
                break;

        if (!$_FILES['FilePatch']['tmp_name']) {
            //nc_print_status(TOOLS_MODULES_ERR_NOTUPLOADED, 'error');
            InstallationAborted(TOOLS_MODULES_ERR_NOTUPLOADED, 'module');
        }


        $ModuleFile = tempnam($TMP_FOLDER, "module");
        move_uploaded_file($_FILES['FilePatch']['tmp_name'], $ModuleFile);
        // this temporary file will be deleted in the end of this function or in InstallationAborted()

        if (!nc_tgz_extract($ModuleFile, $TMP_FOLDER) && !file_exists($TMP_FOLDER."id.txt")) {
            //print TOOLS_MODULES_ERR_EXTRACT;
            InstallationAborted(TOOLS_MODULES_ERR_EXTRACT, 'module');
        }

        $InstallFile = $TMP_FOLDER."install.php";
        if (!is_readable($InstallFile)) {
            //nc_print_status(TOOLS_MODULES_ERR_CANTOPEN." install.php.", 'error');
            InstallationAborted(TOOLS_MODULES_ERR_CANTOPEN." install.php.", 'module');
        } else {
            require $InstallFile;
        }

        if (!CheckDeps('module')) {
            InstallationAborted();
        }
        CheckFiles();

        if (!nc_version_control($VERSION_ID)) {
            //nc_print_status(TOOLS_MODULES_ERR_VERSION." NetCat.", 'error');
            InstallationAborted(TOOLS_MODULES_ERR_VERSION." NetCat.", 'module');
        }

        if ($SystemID == "1") {
            //nc_print_status(TOOLS_MODULES_ERR_VERSION." NetCat.", 'error');
            InstallationAborted(TOOLS_MODULES_ERR_VERSION." NetCat.", 'module');
        }

        if (IsAlreadyInstalled('module')) {
            //nc_print_status(TOOLS_MODULES_ERR_INSTALLED, 'error');
            InstallationAborted(TOOLS_MODULES_ERR_INSTALLED, 'module');
        }

        $Install = CheckAbilityOfInstallation();
        if ($Install["Success"] == 0) {
            //nc_print_status(TOOLS_MODULES_ERR_ITEMS, 'error');
            //print $Install["ErrorMessage"].".<br><br>\n\n";
            InstallationAborted(TOOLS_MODULES_ERR_ITEMS."<br />".$Install["ErrorMessage"], 'module');
        }
        unset($Install);

        $Install = InstallThisModule();
        if ($Install["Success"] == 0) {
            //nc_print_status(TOOLS_MODULES_ERR_DURINGINSTALL.": ".$Install["ErrorMessage"], 'error');
            InstallationAborted(TOOLS_MODULES_ERR_DURINGINSTALL.": ".$Install["ErrorMessage"], 'module');
        }

        $db->query("INSERT INTO `Module`
        (`Module_Name`, `Keyword`, `Description`, `Parameters`, `Example_URL`, `Help_URL`, `Checked`)
        VALUES
        ('".$db->escape($Name)."', '".$db->escape($Keyword)."', '".$db->escape($Description)."', '".$db->escape($Parameters)."', '".$db->escape($ExampleURL)."', '".$db->escape($HelpURL)."', '1')");

        if ($SysMessage) {
            InsertSystemMessage($SysMessage, $Name);
        }

        CopyFiles('module');

        $current_catalogue = $nc_core->catalogue->get_by_host_name($_SERVER['HTTP_HOST']);

        if ($current_catalogue['Language'] == "ru") {
            ExecSQLMultiline($TMP_FOLDER."sql.txt");
        } else {
            ExecSQLMultiline($TMP_FOLDER."sql_int.txt");
        }

        DeleteFilesInDirectory($TMP_FOLDER);
        // Здесь необходимо сбрасывать кэш запроса относящегося к модулям.
        TitleModule();

        break;
    case 5:
        # показать форму добавления модуля
        BeginHtml($Title2, $Title2, "http://".$DOC_DOMAIN."/settings/modules/");
        $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 0);
        $UI_CONFIG = new ui_config_tool(TOOLS_MODULES, TOOLS_MODULES, 'i_modules_big.gif', 'tools.installmodule');

        AddModuleForm();

        break;
    case 6:
        # включение\выключение модулей
        BeginHtml($Title2, $Title2, "http://".$DOC_DOMAIN."/settings/modules/");
        $perm->ExitIfNotAccess(NC_PERM_MODULE, 0, 0, 0, 1);
        $UI_CONFIG = new ui_config_tool(TOOLS_MODULES_LIST, TOOLS_MODULES_LIST, 'i_modules_big.gif', 'module.list');
        $UI_CONFIG->treeMode = 'modules';
        $need_to_reload = ActionModulesCompleted();
        if ($need_to_reload) {
            // При включении/выключении модуля нужно перезагрузить url_routes,
            // перестроить меню. Если первое можно сделать, то второе на момент
            // внесения этого изменения проблематично.
            // Поэтому поступим просто — перезагрузим всю панель управления
            print "<script>top.window.location.reload();</script>";
        }
        else {
            nc_print_status(TOOLS_MODULES_PREFS_SAVED, 'ok');
            ModuleList();
//            print "<script>top.frames['treeIframe'].window.location.reload(); </script>";
        }
        break;
}

EndHtml();