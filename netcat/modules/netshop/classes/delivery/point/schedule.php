<?php

class nc_netshop_delivery_point_schedule extends nc_netshop_delivery_schedule {

    protected $items_class = 'nc_netshop_delivery_point_interval';

    /**
     * Возвращает расписание, заданное в настройках указанного пункта выдачи
     *
     * @param $delivery_point_id
     * @return nc_netshop_delivery_point_schedule
     */
    static public function for_delivery_point($delivery_point_id) {
        $schedule = new self;
        $schedule->select_from_database(
            'SELECT * 
               FROM `%t%` 
              WHERE `DeliveryPoint_ID` = ' . (int)$delivery_point_id . '
              ORDER BY `TimeFrom` ASC'
        );
        return $schedule;
    }

}