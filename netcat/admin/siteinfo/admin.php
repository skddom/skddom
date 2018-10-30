<?php
/* $Id: admin.php 8189 2012-10-11 15:43:20Z vadim $ */

require_once("header.inc.php");
require_once("function.inc.php");

$path = nc_get_scheme() . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);

$nc_core = nc_Core::get_object();
$cur_domain = $nc_core->db->get_var("SELECT `Domain` FROM `Catalogue` WHERE `Catalogue_ID` = '".intval($_GET['CatalogueID'])."'");

$html_res = '';
if ($cur_domain) {
	$html_res = '<input type="text" name="url" value="'.$cur_domain.'" id="url_to_audit">';
}
else {
	$domains = $nc_core->db->get_col("SELECT `Domain` FROM `Catalogue`");
	
	$html_res = '<input type="text" value="'.(isset($domains[0]) ? $domains[0] : '').'" id="url_to_audit" />';
	$html_res.= '<select id="url_to_audit_select" onchange="(this.value == -1 ? $nc(\'#url_to_audit\').val(\'\').show() : (function (site) {$nc(\'#url_to_audit\').hide(); $nc(\'#url_to_audit\').val(site);})(this.value));">';
	
	$i = 0;
	foreach ($domains as $domain) {
		$html_res.= '<option value="'.$domain.'">'.$domain.'</option>';
		$i++;
	}
	
	$html_res.= '<option value=-1>-- другое --</option>';
	$html_res.= '</select>';
	$html_res.= '<script type="text/javascript">$nc(\'#url_to_audit\').hide();</script>';
}

?>

<form id="siteAuditForm">
    <div id='nc_site_seo_form'>
        <div class='nc_clear'></div>
        <div id='nc_site_seo'>
            <?= NETCAT_MODULE_AUDITOR_URL; ?>
            <?=$html_res; ?>
            <a href="" onClick="audit_start(); return false;"><?= NETCAT_MODULE_AUDITOR_GO; ?></a>
        </div>

        <div id='nc_site_seo_status'>
            <div id='please_wait_div' style='display:none'><?=NETCAT_MODULE_AUDITOR_WAIT; ?>
                <a href='javascript:audit_stop()'><?=NETCAT_MODULE_AUDITOR_STOP; ?></a>
            </div>

            <div id='loading_done' style='display:none'><?=NETCAT_MODULE_AUDITOR_DONE; ?></div>
        </div>

        <div class='nc_clear'></div>
    </div>
</form>

<?php

include_once("iframe.inc.php");
EndHtml ();
