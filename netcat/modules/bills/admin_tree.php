<?php

// Если скрипт вызывают напрямую а не через modules_json.php
if (empty($NETCAT_FOLDER)) {

    $NETCAT_FOLDER = realpath(dirname(__FILE__) . '/../../..') . DIRECTORY_SEPARATOR;
    require_once $NETCAT_FOLDER . "vars.inc.php";
    require_once $ADMIN_FOLDER . "function.inc.php";

    // Показываем дерево разработчика, если у пользователя есть на это права
    if (!$perm->isAccess(NC_PERM_MODULE, 0, 0, 0)) {
        exit(NETCAT_MODERATION_ERROR_NORIGHT);
    }
}

//--------------------------------------------------------------------------

if (empty($nc_core)) {
    $nc_core = nc_core();
}

//--------------------------------------------------------------------------

$module_node_id = "module-" . $module['Module_ID'];

// Возвращаем путь (массив с ключами родительских элементов) к текущему разделу
if ($nc_core->input->fetch_get('action') == 'get_path') {
    $ret = array($module_node_id);
    echo nc_array_json($ret);
    exit;
}

//--------------------------------------------------------------------------

$node_children = array();

switch ($node_type) {


    case 'module':
        $node_children = array(
            // Информация
            array(
                "nodeId" => "bills-information",
                "parentNodeId" => $module_node_id,
                "name" => NETCAT_MODULE_BILLS_INFORMATION,
                "href" => "#module.bills.information",
                "sprite" => "folder-dark",
                "hasChildren" => false,
                "expand" => false,
            ),
            // Счета и акты
            array(
                "nodeId" => "bills-manager",
                "parentNodeId" => $module_node_id,
                "name" => NETCAT_MODULE_BILLS_MANAGER,
                "href" => "#module.bills.bills",
                "sprite" => "folder-dark",
                "hasChildren" => true,
                "expand" => false,
            ),
            array(
                "nodeId" => "bills-bills",
                "parentNodeId" => "bills-manager",
                "name" => NETCAT_MODULE_BILLS_BILLS,
                "href" => "#module.bills.bills",
                "sprite" => "folder-dark",
                "hasChildren" => false,
                "expand" => false,
            ),
            array(
                "nodeId" => "bills-acts",
                "parentNodeId" => "bills-manager",
                "name" => NETCAT_MODULE_BILLS_ACTS,
                "href" => "#module.bills.acts",
                "sprite" => "folder-dark",
                "hasChildren" => false,
                "expand" => false,
            ),
            // Справочники
            /*array(
                "nodeId" => "bills-catalogs",
                "parentNodeId" => $module_node_id,
                "name" => NETCAT_MODULE_BILLS_CATALOGS,
                "href" => "#module.bills.catalogs.statuses",
                "sprite" => "folder-dark",
                "hasChildren" => false,
                "expand" => false,
            ),*/
            // Клиенты
            array(
                "nodeId" => "bills-customers",
                "parentNodeId" => $module_node_id,
                "name" => NETCAT_MODULE_BILLS_CUSTOMERS,
                "href" => "#module.bills.customers",
                "sprite" => "folder-dark",
                "hasChildren" => false,
                "expand" => false,
            ),
            // Настройки
            array(
                "nodeId" => "bills-settings",
                "parentNodeId" => $module_node_id,
                "name" => NETCAT_MODULE_BILLS_SETTINGS,
                "href" => "#module.bills.settings",
                "sprite" => "folder-dark",
                "hasChildren" => false,
                "expand" => false,
            ),
        );


}


echo nc_array_json($node_children);