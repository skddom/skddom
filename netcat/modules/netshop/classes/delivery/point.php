<?php

/**
 * Пункт выдачи заказов
 */
abstract class nc_netshop_delivery_point extends nc_record {

    protected $properties = array(
        'id' => null,
        'catalogue_id' => null,
        'name' => '',
        'description' => '',
        'phones' => '',
        'location_name' => '',
        'address' => '',
        'latitude' => '',
        'longitude' => '',
        'group' => '',
        'payment_on_delivery_cash' => false,
        'payment_on_delivery_card' => false,
        'enabled' => true,
    );

    /** @var  nc_netshop_delivery_schedule */
    protected $schedule;

    /** @var array  */
    protected $serialized_object_properties = array('properties', 'schedule');

    /**
     * Возвращает строку с городом и адресом
     *
     * @return string
     */
    public function get_full_address() {
        $address = array();
        foreach (array('location_name', 'address') as $property) {
            $value = trim($this->get($property));
            if (strlen($value)) {
                $address[] = $value;
            }
        }
        return implode(', ', $address);
    }

    /**
     * Проверяет, установлено ли расписание у данного пункта выдачи
     *
     * @return bool
     */
    public function has_schedule() {
        return $this->get_schedule()->count() > 0;
    }

    /**
     * Возвращает коллекцию с рабочими интервалами
     *
     * @return nc_netshop_delivery_schedule
     */
    public function get_schedule() {
        if (!$this->schedule) {
            $this->schedule = nc_netshop_delivery_point_schedule::for_delivery_point($this->get_id());
        }
        return $this->schedule;
    }

    /**
     * @param nc_netshop_delivery_schedule $schedule
     * @return $this
     */
    public function set_schedule(nc_netshop_delivery_schedule $schedule) {
        $this->schedule = $schedule;
        return $this;
    }

    /**
     * @return array
     */
    public function to_array() {
        $result = parent::to_array();
        $result['compact_schedule'] = $this->get_schedule()->get_compact_schedule_string();
        return $result;
    }

    /**
     * Удаление из БД
     *
     * @throws nc_record_exception
     * @return static
     */
    public function delete() {
        nc_db()->query('DELETE FROM `Netshop_DeliveryPointInterval` WHERE `DeliveryPoint_ID` = ' . $this->get_id());
        return parent::delete();
    }


}