<?php
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ADMIN_FOLDER."function.inc.php");

if (!$_GET['url']) die('no url');

if (!require_once($ADMIN_FOLDER."siteinfo/".MAIN_LANG.".lang.php"))
        require_once($ADMIN_FOLDER."siteinfo/en.lang.php");

if (!$perm->isSupervisor()) {
    nc_print_status($NO_RIGHTS_MESSAGE, 'error');
    exit;
}

//LoadModuleEnv();
//$MODULE_VARS = $nc_core->modules->get_module_vars();
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?=MAIN_ENCODING;
?>" />
        <title><?=htmlspecialchars($_GET['url'])
?></title>
        <style>
            body, td { font: 8pt Tahoma, sans-serif }
            h1, h4 { font: bold 12pt Arial, sans-serif; padding: 0px; margin:10px 0px 5px; }
            h1 { font-size:14pt }
        </style>
    </head>
    <body bgcolor="#FFFFFF">
        <?
        print "<h1>".htmlspecialchars($_GET['url'])."</h1>";
        ?>
        <script type="text/javascript" src="audit.js"></script>
        <form id="siteAuditForm" onsubmit="audit_start(); return false;">
            <div id='nc_site_seo_form'>
                <div class='nc_clear'></div>
                <div id='nc_site_seo'>
    <?=NETCAT_MODULE_AUDITOR_URL
        ?> &nbsp; <?=nc_admin_input_simple("url", $_GET['url'], 20, '', "id='url_to_audit'") ?>
                </div>
                <div id='nc_site_seo_status'>
                    <div id='please_wait_div' style='display:none'><?=NETCAT_MODULE_AUDITOR_WAIT
        ?> <a href='javascript:audit_stop()'><?=NETCAT_MODULE_AUDITOR_STOP
        ?></a></div>
                    <div id='loading_done' style='display:none'><?=NETCAT_MODULE_AUDITOR_DONE
        ?></div>
                </div>
                <div class='nc_clear'></div>
            </div>
        </form>
<? require("iframe.inc.php"); ?>
                    <div id=audit_results width=100%></div>
                    <script>
                        audit_start('<?=htmlspecialchars($_GET['url']) ?>');
        </script>

    </body>
</html>
