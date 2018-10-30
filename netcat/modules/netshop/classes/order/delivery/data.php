<?php

/**
 * Служебный класс для сохранения дополнительной информации о доставке для заказов.
 * Не используйте напрямую, используйте методы nc_netshop_order.
 */
class nc_netshop_order_delivery_data extends nc_record {

    protected $table_name = 'Netshop_OrderDelivery';

    protected $properties = array(
        'id' => null,
        'order_component_id' => null,
        'order_id' => null,
        'delivery_method' => null,
        'delivery_point' => null,
        'delivery_interval' => null, // выбранный интервал времени, пока не используется
    );

    protected $mapping = array(
        'id' => 'Netshop_OrderDelivery_ID',
        'order_component_id' => 'Order_Component_ID',
        'order_id' => 'Order_ID',
        'delivery_method' => 'DeliveryMethod',
        'delivery_point' => 'DeliveryPoint',
        'delivery_interval' => 'DeliveryInterval',
    );

    protected $serialized_properties = array(
        'delivery_method',
        'delivery_point',
        'delivery_interval',
    );

    /** @var  nc_netshop_order */
    protected $order;

    /** @var  nc_netshop_delivery_method|null|false */
    protected $order_delivery_method;

    /**
     * @param nc_netshop_order $order
     * @return nc_netshop_order_delivery_data
     */
    static public function for_order(nc_netshop_order $order) {
        $delivery_data = new self();
        $delivery_data->order = $order;

        // Попробуем загрузить данные, если заказ уже есть (данных о доставке может не быть)
        if ($order->get_id()) {
            $delivery_data->load_where(
                'order_component_id', $order->get_order_component_id(),
                'order_id', $order->get_id()
            );
        }

        return $delivery_data;
    }


    /**
     * Особый геттер для способа доставки — для заказов, сохранённых в предыдущих
     * версиях, возвращает способ, указанный в заказе в DeliveryMethod.
     *
     * @return mixed
     */
    public function get($property) {
        if ($property === 'delivery_method' && empty($this->properties['delivery_method']) && $this->order->get('DeliveryMethod')) {
            return $this->get_delivery_method_from_order();
        }

        return parent::get($property);
    }

    /**
     * Возвращает способ доставки, указанный в заказе в DeliveryMethod (или null,
     * если не ID указан или способ больше не существует)
     * @return nc_netshop_delivery_method|null
     */
    protected function get_delivery_method_from_order() {
        if ($this->order_delivery_method === null) {
            try { // пробуем загрузить способ доставки, указанный в заказе
                $this->order_delivery_method = new nc_netshop_delivery_method($this->order->get('DeliveryMethod'));
            }
            catch (Exception $e) {
                // если способа с таким ID нет (например, удалён), будет исключение
                $this->order_delivery_method = false;
            }
        }
        return $this->order_delivery_method ?: null;
    }

    /**
     * Сохранение в БД
     *
     * @throws nc_record_exception
     * @return static
     */
    public function save() {
        // Нет оформленного заказа — нельзя сохранить
        if (!$this->order || !$this->order->get_id()) {
            return $this;
        }
        // Нет данных о доставке?
        $has_data =
            isset($this->properties['delivery_method']) ||
            isset($this->properties['delivery_point']) ||
            isset($this->properties['delivery_interval']);

        if (!$has_data) {
            if ($this->get_id()) {
                // Раньше данные были. Удаляемся
                $this->delete();
            }
            // else — никогда не было. Не сохраняемся
            return $this;
        }

        $this->set('order_component_id', $this->order->get_order_component_id())
             ->set('order_id', $this->order->get_id());

        return parent::save();
    }


}