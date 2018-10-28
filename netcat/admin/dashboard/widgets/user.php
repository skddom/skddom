<?php

$NETCAT_FOLDER  = realpath(dirname(__FILE__) . '/../../../../') . DIRECTORY_SEPARATOR;

require_once $NETCAT_FOLDER . 'vars.inc.php';
require_once $ADMIN_FOLDER . 'function.inc.php';

$logout_link = $MODULE_VARS['auth'] ? $SUB_FOLDER . $HTTP_ROOT_PATH . "modules/auth/?logoff=1&amp;REQUESTED_FROM=" . $REQUEST_URI  : $ADMIN_PATH . "unauth.php";
$edit_link   = $ADMIN_PATH . 'user/index.php?phase=4&amp;UserID=' . $AUTH_USER_ID;
$perm_link   = $ADMIN_PATH . 'user/index.php?phase=8&amp;UserID=' . $AUTH_USER_ID;
?>

<table class="nc-widget-grid">
	<tr>
		<td style="height:1%">
			<a href="<?=$edit_link ?>" onclick="return nc.ui.dashboard.fullscreen(this)">
				<i class="nc-icon nc--user nc--white"></i> <?=$perm->getLogin() ?>
			</a>
		</td>
	</tr>
	<tr>
		<td class="">
			<? $perm_names = array_map('trim', Permission::get_all_permission_names_by_id($AUTH_USER_ID)) ?>
			<a href="<?=$perm_link ?>" onclick="return nc.ui.dashboard.fullscreen(this)">
				<dl title="<?=implode(', ', $perm_names) ?>" class="nc-info nc--small nc--vertical">
					<dt><?=array_shift($perm_names) ?></dt>
					<? if($perm_names): ?>
						<dd style="overflow:hidden; height: 1.1em;"><?=implode(', ', $perm_names) ?></dd>
					<? endif ?>
				</dl>
			</a>
		</td>
	</tr>
	<tr>
		<td style="height:1%">
			<?=$nc_core->ui->btn('#', NETCAT_ADMIN_AUTH_CHANGE_PASS)->click('window.parent.nc_password_change(); return false')->blocked()->small() ?>
		</td>
	</tr>
</table>