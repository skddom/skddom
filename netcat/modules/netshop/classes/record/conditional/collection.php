<?php

class nc_netshop_record_conditional_collection extends nc_record_collection {

    /**
     * Возвращает коллекцию, в которой содержатся только элементы, условия которых
     * удовлетворяют указанному контексту.
     *
     * @param nc_netshop_condition_context $context
     * @param nc_netshop_item $current_item
     * @return nc_netshop_record_conditional_collection
     */
    public function matching(nc_netshop_condition_context $context, $current_item = null) {
        return $this->where('evaluate_conditions', true, '==', array($context, $current_item));
    }

}