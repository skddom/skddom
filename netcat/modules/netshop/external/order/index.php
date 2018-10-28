<?php

// Подключение функций и переменных NetCat
require_once $_SERVER['DOCUMENT_ROOT'] . '/netcat/connect_io.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/netcat/modules/netshop/function.inc.php';
    
// Инициализация и авторизация
nc_netshop_external_order::authorize($_POST['secret_key']);
    
switch($_POST['action']){    
    // Добавление нового заказа
    case 'add':
    
        $new_order = new nc_netshop_external_order($_POST['order_data']);
        nc_netshop_external_order::return_json(array(
            'message' => 'Order #' . $new_order->id . ' created successfully',
            'data' => array('code' => 100, 'order_id' => $new_order->id)
        ));
            
        break;
                
    // Получение данных конкретного заказа
    case 'get':
    
        $order_data = nc_netshop_external_order::get_by_id($_POST['order_id']);
        nc_netshop_external_order::return_json($order_data);

        break;
        
    default:
        nc_netshop_external_order::return_json(array(
            'message' => 'Wrong action: ' . $_POST['action'],
            'data' => array('code' => 4)
        ));
}