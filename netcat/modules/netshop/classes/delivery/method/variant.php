<?php

/**
 * Вариант способа доставки.
 *
 * Когда служба расчёта доставки (nc_netshop_delivery_service) может возвращать
 * более одного варианта (can_provide_multiple_variants() == true), при подборе
 * подходящих под условие способов (nc_netshop_delivery_method_collection::matching())
 * соответствующий способ доставки заменяется на варианты доставки (экземпляры этого
 * класса).
 *
 * У вариантов должно быть установлено два дополнительных свойства:
 *   — external_id — внешний идентификатор варианта доставки
 *   — method_id — идентификатор метода доставки
 * Значение id должно состоять из external_id и method_id, разделённых двоеточием
 * (например: "12:3356")
 */
class nc_netshop_delivery_method_variant extends nc_netshop_delivery_method {

    protected $table_name = "";

    /**
     * @param nc_netshop_order $order
     * @return nc_netshop_delivery_method_variant[]
     */
    public function get_variants(nc_netshop_order $order) {
        return array($this);
    }

    /**
     * Возвращает название варианта и способа доставки (для шаблонов для
     * панели управления)
     *
     * @return string
     */
    public function get_variant_and_method_name() {
        $result = $this->get('name');
        $delivery_method = $this->get_delivery_method();
        if ($delivery_method) {
            $result .= ' (' . $delivery_method->get('name') . ')';
        }
        return $result;
    }

    /**
     * @return nc_netshop_delivery_method|null
     */
    protected function get_delivery_method() {
        list($delivery_method_id) = explode(':', $this->get_id());
        try {
            return new nc_netshop_delivery_method($delivery_method_id);
        }
        catch (nc_record_exception $e) {
            return null;
        }
    }

}