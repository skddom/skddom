<?php

$NETCAT_FOLDER = realpath(__DIR__ . '/../..') . DIRECTORY_SEPARATOR;
@include_once ($NETCAT_FOLDER."vars.inc.php");
require_once($INCLUDE_FOLDER."unicode.inc.php");
require_once ($ROOT_FOLDER.'connect_io.php');

$catalogue = isset($catalogue) ? intval($catalogue) : 0;
$sub = isset($sub) ? intval($sub) : 0;
$cc = isset($cc) ? intval($cc) : 0;
$classID = isset($classID) ? intval($classID) : 0;
$template = isset($template) ? intval($template) : 0;
$classPreview = isset($classPreview) ? intval($classPreview) : 0;
$templatePreview = isset($templatePreview) ? intval($templatePreview) : 0;
$is_there_any_files = 0;
$message = is_array($message) ? array_map('intval', $message) : intval($message);
$warnText = "";
$nc_core->url->parse_url();
$nc_core->page->set_url($nc_core->url);

if ($templatePreview > 0) {
    require_once($INCLUDE_FOLDER."preview/templatePreview.php");
}

if ($classPreview > 0) {
    $file_action = array(
            'index' => 'classPreview',
            'add' => 'addPreview',
            'change' => 'messagePreview',
            'search' => 'searchPreview');
    require_once($INCLUDE_FOLDER."preview/".$file_action[$action].".php");
    unset($file_action);
}


if (strstr($nc_core->url->get_parsed_url('path'), $nc_core->SUB_FOLDER.$nc_core->HTTP_ROOT_PATH."modules/")) {
    $posting = true;
}

$inside_admin = $nc_core->inside_admin = (!$passed_thru_404 && $nc_core->input->fetch_get_post('inside_admin') );


if ($nc_core->inside_admin) {
    if ($cc && !($sub || $catalogue)) {
        try {
            $cc_data = $nc_core->sub_class->get_by_id($cc);
        } catch (Exception $e) {
            //будет сообщение при проверке в $nc_core->load_env($catalogue, $sub, $cc);
        }
        $catalogue = $cc_data["Catalogue_ID"];
        $sub = $cc_data["Subdivision_ID"];
        unset($cc_data);
    }

    if (!$cc && $classID && $message) {
        list($catalogue, $sub, $cc) = $db->get_row("SELECT s.`Catalogue_ID`, m.`Subdivision_ID`, m.`Sub_Class_ID`
      FROM `Message".intval($classID)."` as m, `Subdivision` as s
      WHERE m.`Message_ID` = '".intval($message)."'
      AND m.`Subdivision_ID` = s.`Subdivision_ID`", ARRAY_N);
    }
}


// set admin mode
switch (true) {
    case ($classPreview || $templatePreview):
        $nc_core->admin_mode = false;
        break;
    case $nc_core->inside_admin:
        $nc_core->admin_mode = true;
        break;
    case !$passed_thru_404 && isset($posting): // add (edit) action
        $nc_core->admin_mode = $admin_mode;
        break;
    case !$passed_thru_404: //front-office
        $nc_core->admin_mode = true;
        break;
    case $passed_thru_404:
        $nc_core->admin_mode = false;
        break;
    default:
        $nc_core->admin_mode = false;
}

// old value
$admin_mode = $nc_core->admin_mode;


if (isset($_GET['developer_mode'])) {
    $_SESSION['developer_mode'] = +$_GET['developer_mode'];
}

$nc_core->developer_mode = $_SESSION['developer_mode'];

try {
    $nc_core->load_env($catalogue, $sub, $cc);
}
catch (Exception $e) {
    // нет сайтов в системе?
    trigger_error($e->getMessage(), E_USER_WARNING);
    header("Location: " . $nc_core->ADMIN_PATH);
    die;
}

$MODULE_VARS = $nc_core->modules->load_env();

$admin_url_prefix = $nc_core->SUB_FOLDER.$nc_core->HTTP_ROOT_PATH;
$current_catalogue = $nc_core->catalogue->get_current();
$catalogue = $nc_core->catalogue->get_current("Catalogue_ID");
$current_sub = $nc_core->subdivision->get_current();
$sub = $nc_core->subdivision->get_current("Subdivision_ID");
$current_cc = $nc_core->sub_class->get_current();
$cc = $nc_core->sub_class->get_current("Sub_Class_ID");
$parent_sub_tree = $nc_core->subdivision->get_parent_tree($sub);
$sub_level_count = $nc_core->subdivision->get_level_count($sub);
// get system settings for compatibillity
$system_env = $nc_core->get_settings();
$subHost = nc_get_scheme() . '://' . ($current_cc['Hidden_Host'] ? (strchr($current_cc['Hidden_Host'], '.') ? $current_cc['Hidden_Host'] : $current_cc['Hidden_Host'] . '.' . $DOMAIN_NAME) : $DOMAIN_NAME);
$subLink = nc_folder_path($sub);

$operation_array = "";
// simple cc variables
if ($cc) {
    $operation_array = "current_cc";
    $moderationID = $current_cc["Moderation_ID"];
    $classID = $current_cc["Class_ID"];
    $DeleteTemplate = $current_cc["DeleteTemplate"];
    $DeleteCond = $current_cc["DeleteCond"];
}

// user mode variables
if ($user_table_mode) {
    $systemTableID = $current_cc["System_Table_ID"];
}
// general user & cc variables
if ($cc || $user_table_mode) {
    $allowTags = $current_cc["AllowTags"];
    $titleTemplate = $current_cc["TitleTemplate"];
    $addTemplate = $current_cc["AddTemplate"];
    $addCond = $current_cc["AddCond"];
    $addActionTemplate = $current_cc["AddActionTemplate"];
    $editTemplate = $current_cc["EditTemplate"];
    $editCond = $current_cc["EditCond"];
    $editActionTemplate = $current_cc["EditActionTemplate"];
    $CheckActionTemplate = $current_cc["CheckActionTemplate"];
    $DeleteActionTemplate = $current_cc["DeleteActionTemplate"];
    $searchTemplate = $current_cc["SearchTemplate"];
    $subscribeCond = $current_cc["SubscribeCond"];
    $subscribeTemplate = $current_cc["SubscribeTemplate"];
}

$nc_core->user->attempt_to_authorize();


if (!$AUTH_USER_ID && $nc_core->modules->get_by_keyword('auth') && ($auth_hash = $nc_core->input->fetch_get_post('auth_hash'))) {
    $nc_auth->hash->authorize_by_hash($auth_hash);
}

$check_auth = s_auth($current_cc, $action, isset($posting) ? $posting : 0);


if (!$AUTH_USER_ID && $nc_core->admin_mode) {
    Refuse();
}

$AUTH_USER_ID += 0;
$AUTH_USER_GROUP += 0;

if ($AUTH_USER_ID) {

    if (!$current_user) {
        $current_user = $db->get_row("SELECT `Language` FROM `User` WHERE `User_ID` = '".$AUTH_USER_ID."'", ARRAY_A);
    }

    $AUTH_LANG = $current_user['Language'];
}

try {
    require_once ($nc_core->ADMIN_FOLDER."lang/".$nc_core->lang->detect_lang().".php");
} catch (Exception $e) {
    die($e->getMessage());
}

if ($nc_core->modules->get_vars('netshop')) {
    // session for NetShop
    $netshop_sid = session_name();
    if ($GLOBALS[$netshop_sid])
        session_id($GLOBALS[$netshop_sid]);
    // init shop object
    if ($nc_core->modules->get_vars('netshop'))
        $GLOBALS['shop'] = new NetShop();
}
$template_env = $nc_core->template->get_by_id($template);
// set template from current subdivision
if (!$template || !$template_env["Template_ID"])
    $template = $nc_core->subdivision->get_current("Template_ID");

// set template as default
if ($template) {
    $nc_core->template->set_current_by_id($template);
}

// init log functional in stats module
if ($nc_core->modules->get_vars('stats'))
    Stats_Log();

if ($nc_core->inside_admin) {
    if ($nc_core->get_settings('EditDesignTemplateID') && !$nc_core->subdivision->get_current('UseEditDesignTemplate')) {
        $template = $nc_core->get_settings('EditDesignTemplateID');
        // set template as current
        $nc_core->template->set_current_by_id($template);
    }
    require_once ($nc_core->ADMIN_FOLDER."admin.inc.php");
}

$template_settings = array();

// get current template env
$template_env = $nc_core->template->get_current();

$File_Mode = $template_env['File_Mode'];

$nc_parent_template_folder_path = null;

if ($File_Mode) {
    $nc_parent_template_folder_path = nc_standardize_path_to_folder(
        nc_get_http_folder($nc_core->TEMPLATE_FOLDER) .
        nc_get_path_to_main_parent_folder($template_env['File_Path'])
    );
}

if ($nc_core->subdivision->get_current("Subdivision_ID") == $nc_core->catalogue->get_current("Title_Sub_ID")) {
    $f_title = $nc_core->catalogue->get_current("Catalogue_Name");
} else {
    $f_title = $nc_core->subdivision->get_current("Subdivision_Name");
}

if ($nc_core->sub_class->get_current("Sub_Class_ID") != $cc_array[0] && $nc_core->sub_class->get_current("Checked")) {
    $f_title = $nc_core->sub_class->get_current("Sub_Class_Name");
}

$nc_core->page->set_h1($f_title);
$nc_core->page->set_current_metatags($current_sub);

if (!$File_Mode) {

    $template_env = $nc_core->template->convert_subvariables($template_env);
    $template_header = "";
    $template_footer = "";

    if (!$isNaked && $nc_core->get_page_type() != 'rss' && $nc_core->get_page_type() != 'xml') {

        if ($nc_core->subdivision->get_current('Template_ID') == $template) {
            $template_settings = $nc_core->subdivision->get_template_settings($sub);
        } else if (isset($nc_use_site_template_settings) && $nc_use_site_template_settings) {
            // используется в скриптах modules/auth
            $template_settings = $nc_core->catalogue->get_template_settings($catalogue);
        }
        else {
            $template_settings = $nc_core->template->get_settings_default_values($template);
        }

        $template_header = $template_env['Header'];
        $template_footer = $template_env['Footer'];

        eval(nc_check_eval($nc_core->template->get_current("Settings")));

        if ($nc_core->subdivision->get_current("UseMultiSubClass") == 2 && strpos($template_header, 's_browse_cc') === false) {
            $template_header .= s_browse_cc(array(
                    'prefix' => "<div id='s_browse_cc'>",
                    'suffix' => "</div><br />",
                    'active' => "<span>%NAME</span>",
                    'active_link' => "<span>%NAME</span>",
                    'unactive' => "<span> <a href=%URL>%NAME</a></span>",
                    'divider' => " &nbsp; "
            ));
        }

        // metatag noindex
        if ($nc_core->subdivision->get_current("DisallowIndexing") == 1) {
            $template_header = nc_insert_in_head($template_header, "<meta name='robots' content='noindex' />");
        }


        if ($nc_core->get_variable("admin_mode") || $nc_core->get_variable("inside_admin")) {
            // reversive direction!
            $template_header = nc_insert_in_head($template_header, "<script type='text/javascript' src='".$nc_core->get_variable("ADMIN_PATH")."js/package.js'></script>\r\n");
            $template_header = nc_insert_in_head($template_header, nc_js(true), true);
        }

        $quick_mode = false;
        if ($nc_core->get_variable("AUTHORIZATION_TYPE") != 'http' && $nc_core->get_settings('QuickBar') && !isset($_GET['rand'])) {
            require_once ($nc_core->get_variable("INCLUDE_FOLDER")."quickbar.inc.php");
            $template_header = nc_quickbar_in_template_header($template_header, $File_Mode);
            $quick_mode = true;
        }


        if ($nc_core->inside_admin) {
            // output interface settings (add 'em to the footer before </body>)
            $UI_CONFIG = new ui_config_objects($cc);

            $js_code = '".$UI_CONFIG->to_json()."';

            if ($template_footer && nc_preg_match("/(<\/body>)/i", $template_footer, $regs)) {
                $template_footer = str_replace($regs[1], $js_code.$regs[1], $template_footer);
            } else {
                $template_footer .= $js_code;
            }
        }
        #if ($quick_mode && nc_quickbar_permission()) {
        #    $template_footer = nc_cut_jquery($template_footer);
        #}
    }
    elseif($admin_modal) {
        eval(nc_check_eval($nc_core->template->get_current("Settings")));
    }

// openstat
    if (NC_OPENSTAT_COUNTER) {
        if (!$admin_mode && !$inside_admin) {
            $pos = nc_strpos($template_header, NC_OPENSTAT_COUNTER);
            if ($pos !== FALSE) {
                $template_header = nc_substr($template_header, 0, $pos).nc_openstat_get_code().nc_substr($template_header, $pos + nc_strlen(NC_OPENSTAT_COUNTER));
                $template_header = str_replace(NC_OPENSTAT_COUNTER, "", $template_header);
                $template_footer = str_replace(NC_OPENSTAT_COUNTER, "", $template_footer);
            } else {
                $pos = nc_strpos($template_footer, NC_OPENSTAT_COUNTER);
                if ($pos !== FALSE) {
                    $template_footer = nc_substr($template_footer, 0, $pos).nc_openstat_get_code().nc_substr($template_footer, $pos + nc_strlen(NC_OPENSTAT_COUNTER));
                    $template_footer = str_replace(NC_OPENSTAT_COUNTER, "", $template_footer);
                }
            }
        }
    }

    $template_header = nc_insert_demo_mode_message($template_header);

    if (!$check_auth && NC_AUTH_IN_PROGRESS !== 1) {

        eval(nc_check_eval("echo \"".$template_header."\";"));

        if ($AUTH_USER_ID || (!$AUTH_USER_ID && !$nc_core->modules->get_vars('auth') )) {
            if ($nc_core->inside_admin) {
                nc_print_status(NETCAT_MODERATION_ERROR_NORIGHTS, 'error');
            } else {
                print NETCAT_MODERATION_ERROR_NORIGHTS;
            }
        } elseif (!$AUTH_USER_ID && $nc_core->modules->get_vars('auth')) {
            $nc_auth->login_form();
        }

        eval(nc_check_eval("echo \"".$template_footer."\";"));

        exit;
    }

    if (!$message && $action == "full")
        exit;

    if ($AUTH_USER_ID) {
        $current_user = $nc_core->user->get_by_id($AUTH_USER_ID);
    }
}