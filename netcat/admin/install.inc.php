<?php

function InstallationAborted($msg='', $action='') {
    global $TMP_FOLDER;

    switch ($action) {
        case 'patch':
            nc_print_status(($msg ? $msg."<br>" : "").TOOLS_PATCH_ERR_CANTINSTALL, "error");
            PatchForm();
            PatchList();
            break;
        case 'module':
            nc_print_status(($msg ? $msg."<br>" : "").TOOLS_MODULES_ERR_INSTALL, "error");
            break;
        case 'activation':
            nc_print_status(($msg ? $msg : TOOLS_PATCH_ERROR), "error");
            nc_activation_show_form();
            break;
        default:
            nc_print_status(($msg ? $msg : TOOLS_PATCH_ERROR), "error");
            break;
    }

    DeleteFilesInDirectory($TMP_FOLDER);
    EndHtml();
    exit;
}

function CopyFiles($action = '') {
    global $TMP_FOLDER, $MODULE_FOLDER, $ROOT_FOLDER, $DOCUMENT_ROOT, $SUB_FOLDER;
    global $Keyword;

    if ('module' == $action) {
        @mkdir($MODULE_FOLDER.$Keyword, 0775);

        copy($TMP_FOLDER."index.php", $MODULE_FOLDER.$Keyword."/index.php");
        copy($TMP_FOLDER."function.inc.php", $MODULE_FOLDER.$Keyword."/function.inc.php");
        copy($TMP_FOLDER."en.lang.php", $MODULE_FOLDER.$Keyword."/en.lang.php");
        copy($TMP_FOLDER."ru.lang.php", $MODULE_FOLDER.$Keyword."/ru.lang.php");

        if (is_readable($TMP_FOLDER."admin.php"))
                copy($TMP_FOLDER."admin.php", $MODULE_FOLDER.$Keyword."/admin.php");
        if (is_readable($TMP_FOLDER."admin.inc.php"))
                copy($TMP_FOLDER."admin.inc.php", $MODULE_FOLDER.$Keyword."/admin.inc.php");
        if (is_readable($TMP_FOLDER."setup.php"))
                copy($TMP_FOLDER."setup.php", $MODULE_FOLDER.$Keyword."/setup.php");
    }

    $FileWithFileList = "files.txt";
    $COPY_FOLDER = $DOCUMENT_ROOT.$SUB_FOLDER;

    # сколько файлов скопировано, сколько всего файлов
    $result = array("files" => 0, "total" => 0);

    $fpAny = fopen($TMP_FOLDER.$FileWithFileList, "r");

    while (!feof($fpAny)) {
        $file_name = chop(fgets($fpAny, 4096));
        if (strlen($file_name) == 0) break;

        $directory = dirname($file_name);

        $tmpDirectory = $COPY_FOLDER;
        $tok = strtok($directory, "/");
        while ($tok) {
            $tmpDirectory.= "/".$tok;
            @mkdir($tmpDirectory, 0775);
            $tok = strtok("/");
        }
        $file_copied = @copy($TMP_FOLDER.$file_name, $COPY_FOLDER."/".$file_name);
        if ($file_copied) $result["files"]++;

        $result["total"]++;
    }
    fclose($fpAny);

    return $result;
}

function LoadID($action = '') {
    global $TMP_FOLDER, $db, $nc_core;
    global $PatchName, $SystemID, $VersionID, $Description;
    global $Keyword, $SystemVersion, $Patch, $Name;
    global $ExampleURL, $HelpURL, $Parameters, $SysMessage;

    $FileID = "id.txt";
    if (!file_exists($TMP_FOLDER.$FileID)) return false;

    if ($action == 'patch') {
        $fp = fopen($TMP_FOLDER.$FileID, "r");
        $PatchName = chop(fgets($fp, 4096));
        $SystemID = chop(fgets($fp, 4096));
        $VersionID = chop(fgets($fp, 4096));
        $Description = chop(fgets($fp, 4096));
        fclose($fp);
        if (!$nc_core->NC_UNICODE) {
            $Description = $nc_core->utf8->utf2win($Description);
        }
    } elseif ($action == 'module') {
        $fp = fopen($TMP_FOLDER.$FileID, "r");
        $Keyword = chop(fgets($fp, 4096));
        $SystemID = chop(fgets($fp, 4096));
        $SystemVersion = chop(fgets($fp, 4096));
        $Patches = chop(fgets($fp, 4096));

        $tok = strtok($Patches, " ");
        while ($tok) {
            $Patch [] = $tok;
            $tok = strtok(" ");
        }

        $Name = chop(fgets($fp, 4096));
        $ExampleURL = chop(fgets($fp, 4096));
        $HelpURL = chop(fgets($fp, 4096));
        $Description = chop(fgets($fp, 4096));

        fclose($fp);

        $FileParam = "parameters.txt";
        $fp = fopen($TMP_FOLDER.$FileParam, "r");
        while (!feof($fp)) {
            $Parameters .= fgets($fp, 4096);
        }
        fclose($fp);
        if (MAIN_LANG != "ru") {
            $SysMessage = join("", @file($TMP_FOLDER."message_int.txt"));
        } else {
            $SysMessage = join("", @file($TMP_FOLDER."message.txt"));
        }
    }
}

function CheckDeps($action) {
    global $TMP_FOLDER, $db, $Patch;
    global $PatchName, $SystemID, $VersionID, $Description, $Required;

    $ReturnValue = 1;
    LoadID($action);

    if ($action == 'module') {
        if (!count($Patch)) return $ReturnValue;
    }
    if ($action == 'patch') {
        LoadRequired();
    }

    $Array = $db->get_col("SELECT `Patch_Name` FROM `Patch`");
    $listed = ($action == 'patch') ? $Required : $Patch;
    while (list($key, $val) = each($listed)) {
        $cmp = 0;
        for ($i = 0; $i < count($Array); $i++) {
            if (strcmp((int) sprintf("%0-3s", $Array[$i]), (int) sprintf("%0-3s", $val)) == 0) {
                $cmp++;
                break;
            }
        }
        if ($cmp == 0) {
            nc_print_status(TOOLS_MODULES_ERR_PATCH." ".$val.".<br>\r\n", 'error');
            $ReturnValue = 0;
        }
    }

    return $ReturnValue;
}

function LoadRequired() {
    global $TMP_FOLDER, $db, $Required;

    $FileRequired = "required.txt";
    $fp = fopen($TMP_FOLDER.$FileRequired, "r");
    while (!feof($fp)) {
        $buffer = chop(fgets($fp, 4096));
        if (strlen($buffer) > 0) {
            $Required[] = $buffer;
        } else {
            break;
        }
    }

    fclose($fp);
}

function IsAlreadyInstalled($action='') {
    global $db, $PatchName, $TMP_FOLDER, $Keyword;
    if ($action == 'patch') {
        if ($db->get_var("SELECT `Patch_Name` FROM `Patch` WHERE `Patch_Name` = '".$db->escape($PatchName)."'"))
                return 1;
    }
    if ($action == 'module') {

        $select = "SELECT `Keyword` FROM `Module` WHERE `Keyword`='".$db->escape($Keyword)."'";
        $Result = $db->query($select);
        if ($db->num_rows > 0) {
            return 1;
        }
    }

    return 0;
}