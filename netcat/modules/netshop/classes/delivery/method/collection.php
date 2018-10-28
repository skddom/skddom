<?php

class nc_netshop_delivery_method_collection extends nc_netshop_record_conditional_collection {

    protected $items_class = 'nc_netshop_delivery_method';

    /**
     * Возвращает коллекцию, в которой содержатся только способы доставки, условия которых
     * удовлетворяют указанному контексту.
     *
     * Если $current_item не строго равно false (в т. ч. когда второй аргумент не передан),
     * вернёт коллекцию, в которой для всех служб доставки с вариантами будут заменены
     * на имеющиеся варианты.
     *
     * @param nc_netshop_condition_context $context
     * @param nc_netshop_item $current_item
     * @return nc_netshop_record_conditional_collection
     */
    public function matching(nc_netshop_condition_context $context, $current_item = null) {
        $result = $this->make_new_collection();
        /** @var nc_netshop_delivery_method $delivery_method */
        foreach ($this->items as $key => $delivery_method) {
            // способы доставки «до пункта выдачи» добавляются только если в городе заданы пункты выдачи
            if ($delivery_method->get_delivery_type() === nc_netshop_delivery::DELIVERY_TYPE_PICKUP) {
                $location = $context->get_order()->get_location_name();
                if (!$delivery_method->has_delivery_points($location)) {
                    continue;
                }
            }

            if ($delivery_method->evaluate_conditions($context, $current_item)) {
                $result->add($delivery_method);
            }
        }

        if ($current_item !== false && $context->get_order()) {
            return $result->with_variants($context->get_order());
        }

        return $result;
    }

    /**
     * Возвращает коллекцию со всеми вариантами доставки
     * (способы доставки, которые могут иметь несколько вариантов, заменяются на варианты доставки)
     *
     * @param nc_netshop_order $order
     * @return static
     */
    public function with_variants(nc_netshop_order $order) {
        $result = $this->make_new_collection();
        /** @var nc_netshop_delivery_method $delivery_method */
        foreach ($this->items as $delivery_method) {
            $result->add_items($delivery_method->get_variants($order));
        }
        return $result;
    }


}