<?php

class nc_netshop_order_listener {

    protected $previous_statuses = array();

    /**
     *
     */
    public static function register() {
        $listener = new self;
        $event_manager = nc_core::get_object()->event;
        $event_manager->add_listener(nc_event::BEFORE_OBJECT_UPDATED, array($listener, 'before_object_updated'));
        $event_manager->add_listener(nc_event::AFTER_OBJECT_UPDATED, array($listener, 'after_object_updated'));
    }

    /**
     * @param $site_id
     * @param $folder_id
     * @param $infoblock_id
     * @param $component_id
     * @param $object_ids
     */
    public function before_object_updated($site_id, $folder_id, $infoblock_id, $component_id, $object_ids) {
        $netshop = nc_netshop::get_instance($site_id);
        $order_component_id = $netshop->get_setting('OrderComponentID');
        if ($order_component_id != $component_id) {
            return;
        }

        if (!isset($this->previous_statuses[$order_component_id])) {
            $this->previous_statuses[$order_component_id] = array();
        }

        // Сохраняем статусы до обновления
        if ($object_ids) {
            $this->previous_statuses[$order_component_id] += nc_db()->get_col(
                "SELECT `Message_ID`, `Status`
                   FROM `Message{$order_component_id}`
                  WHERE `Message_ID` IN (" . join(', ', (array)$object_ids) . ")",
                1, 0
            ) ?: array();
        }
    }

    /**
     * Слушатель событий updateMessage
     * (события при добавлении обрабатываются отдельно в nc_netshop::place_order())
     *
     * Для записей компонента «Заказ», определённого в настройках модуля,
     * при изменении статуса заказа изменяет в соответствии с настройками
     * SubtractFromStockStatusID и ReturnToStockStatusID значение
     *
     * @param $site_id
     * @param $folder_id
     * @param $infoblock_id
     * @param $component_id
     * @param $object_ids
     */
    public function after_object_updated($site_id, $folder_id, $infoblock_id, $component_id, $object_ids) {
        $netshop = nc_netshop::get_instance($site_id);
        $order_component_id = $netshop->get_setting('OrderComponentID');
        if ($order_component_id != $component_id) {
            return;
        }

        foreach ((array)$object_ids as $order_id) {
            $order = $netshop->load_order($order_id);
            $previous_status = nc_array_value($this->previous_statuses[$component_id], $order_id);
            $current_status = $order->get_status_id();

            if ($current_status != $previous_status) {
                // Обновление StockUnits у товаров
                $order->update_item_stock_units_on_order_change();

                // Вызов метода process_status_change() для заказов, для источника которых
                // имеется класс-обработчик (например, заказы с Яндекс.Маркета)
                $order_source_class = $order->get_order_source_class();
                if (method_exists($order_source_class, 'process_status_change')) { // заменить на интерфейс!
                    $order_source_class::process_status_change($order);
                }

                $this->previous_statuses[$component_id][$order_id] = $current_status;
            }
        }
    }

}