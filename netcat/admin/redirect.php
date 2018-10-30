<?

/* $Id: redirect.php 7302 2012-06-25 21:12:35Z alive $ */

require ("function.inc.php");
require ("redirect.inc.php");

$Delimeter = " &gt ";
$main_section = "settings";
$item_id = 6;
$Title2 = REDIRECT_TAB_LIST;
$Title3 = "<a href=\"".$ADMIN_PATH."redirect.php\">".REDIRECT_TAB_LIST."</a>";
$Title4 = CONTROL_SETTINGSFILE_TITLE_ADD;
$Title5 = CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_EDIT;

$UI_CONFIG = new ui_config_tool(REDIRECT_TAB_LIST, REDIRECT_TAB_LIST, 'i_settings_big.gif', 'redirect.settings');


if (!isset($phase)) $phase = 0;

if (in_array($phase, array(2, 3))) {
    if (!$nc_core->token->verify()) {
        BeginHtml($Title2, $Title2, "http://".$DOC_DOMAIN."/settings/sql/");
        nc_print_status(NETCAT_TOKEN_INVALID, 'error');
        EndHtml();
        exit;
    }
}

switch ($phase) {
    case 0:
        # список переадресаций
        BeginHtml($Title2, $Title2, "http://".$DOC_DOMAIN."/settings/redirect/");
        $perm->ExitIfNotAccess(NC_PERM_REDIRECT, 0, 0, 0, 0);
        if ($nc_core->NC_REDIRECT_DISABLED) {
            nc_print_status(TOOLS_REDIRECT_DISABLED, 'info');
        }
        RedirectList ();
        break;

    case 1:
        # форма для добавления / изменения
        BeginHtml($Title4, $Title3.$Delimeter.$Title4, "http://".$DOC_DOMAIN."/settings/redirect/");
        $perm->ExitIfNotAccess(NC_PERM_REDIRECT, 0, 0, 0, 0);

        if ($RedirectID) {
            $UI_CONFIG = new ui_config_tool(REDIRECT_TAB_LIST, REDIRECT_TAB_EDIT, 'i_settings_big.gif', 'redirect.edit('.$RedirectID.')');
        } else {
            $UI_CONFIG = new ui_config_tool(REDIRECT_TAB_LIST, REDIRECT_TAB_ADD, 'i_settings_big.gif', 'redirect.add');
        }

        RedirectForm($RedirectID);

        break;

    case 2:
        # собственно добавление / изменение
        BeginHtml($Title2, $Title2, "http://".$DOC_DOMAIN."/settings/redirect/");
        $perm->ExitIfNotAccess(NC_PERM_REDIRECT, 0, 0, 0, 1);

        RedirectCompleted();

        RedirectList();
        break;

    case 3:
        # удаление
        BeginHtml($Title2, $Title2, "http://".$DOC_DOMAIN."/settings/redirect/");
        $perm->ExitIfNotAccess(NC_PERM_REDIRECT, 0, 0, 0, 1);

        while (list($key, $val) = each($_POST))
            if (strcmp(substr($key, 0, 6), "Delete") == 0)
                    DeleteRedirect($val);

        RedirectList ();

        break;
}


EndHtml ();