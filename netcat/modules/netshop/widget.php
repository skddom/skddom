<?php

$NETCAT_FOLDER  = realpath(dirname(__FILE__) . '/../../../') . DIRECTORY_SEPARATOR;

require_once $NETCAT_FOLDER . 'vars.inc.php';
require_once $ADMIN_FOLDER . 'function.inc.php';

global $MODULE_VARS;

if ( ! $MODULE_VARS) {
	$MODULE_VARS = $nc_core->modules->get_module_vars();
}

$Catalogue_ID = (int)$_GET['Catalogue_ID'];
$order_table  = 'Message' . $MODULE_VARS['netshop']['ORDER_TABLE'];
$orders_cc    = $db->get_var('SELECT Sub_Class_ID FROM Sub_Class WHERE Catalogue_ID='.$Catalogue_ID.' AND Class_ID='. (int)$MODULE_VARS['netshop']['ORDER_TABLE'] . ' ORDER BY Class_Template_ID');

$stat_link  = $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH . 'modules/netshop/admin/?controller=statistics&action=index&site_id=' . $Catalogue_ID;
?>

<table class="nc-widget-grid nc-widget-link nc-text-center" onclick="return nc.ui.dashboard.fullscreen(null, '<?=$stat_link ?>')">
	<!-- <col width="50%" />
	<col width="50%" /> -->
	<tr>
		<td class="nc-bg-light" style="height:1%" colspan="2">
			<?=NETCAT_MODULE_NETSHOP ?>
		</td>
	</tr>
	<tr>
		<td class="-nc--gradient">
			<dl class="nc-info nc--large"><dt><?=$db->get_var('SELECT COUNT(*) FROM '.$order_table.' WHERE DATE(`Created`) = CURDATE()') ?></dt></dl>
		</td>
		<td>
			<dl class="nc-info nc--mini nc--vertical">
				<dt><i class="nc-icon nc--mod-netshop nc--white"></i></dt>
				<dd><?=DASHBOARD_TODAY ?></dd>
			</dl>
		</td>
	</tr>
	<tr>
		<td class="nc-bg-dark">
			<dl class="nc-info nc--mini"><dt><dt><?=$db->get_var('SELECT COUNT(*) FROM '.$order_table.' WHERE DATE(`Created`) = ( CURDATE() - INTERVAL 1 DAY )') ?></dt> <dd><?=DASHBOARD_YESTERDAY ?></dd></dl>
		</td>
		<td class="nc-bg-darken">
			<dl class="nc-info nc--mini"><dt><dt><?=$db->get_var('SELECT COUNT(*) FROM '.$order_table.' WHERE `Status` IS NULL') ?></dt> <dd><?=DASHBOARD_WAITING ?></dd></dl>
		</td>
	</tr>
</table>