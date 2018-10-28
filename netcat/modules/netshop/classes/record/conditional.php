<?php

/**
 * Базовый класс для записей, имеющих свойство 'condition'
 */
abstract class nc_netshop_record_conditional extends nc_record {

    /** @var nc_netshop_condition */
    protected $conditions;

    /**
     * @return nc_netshop_condition
     */
    protected function get_conditions() {
        if (!$this->conditions) {
            $condition_data = json_decode($this->get('condition'), true);
            $this->conditions = $condition_data
                                    ? nc_netshop_condition::create($condition_data)
                                    : new nc_netshop_condition_always();
        }
        return $this->conditions;
    }

    /**
     * @param bool $uppercase_first
     * @return string
     */
    public function get_condition_description($uppercase_first = true) {
        $catalogue_id = ($this->has_property('catalogue_id')) ? $this->get('catalogue_id') : null;
        $netshop = nc_netshop::get_instance($catalogue_id);

        $description = $this->get_conditions()->get_full_description($netshop);
        if ($uppercase_first) { return nc_ucfirst($description); }
                         else { return $description; }
    }

    /**
     * @param $type
     * @return bool
     */
    public function has_condition_of_type($type) {
        return $this->get_conditions()->has_condition_of_type($type);
    }

    /**
     * @param nc_netshop_condition_context $context
     * @param nc_netshop_item|mixed $current_item
     * @return bool
     */
    public function evaluate_conditions(nc_netshop_condition_context $context, $current_item = null) {
        return $this->get_conditions()->evaluate($context, $current_item);
    }

    /**
     * @param nc_netshop_condition_visitor $visitor
     * @return mixed
     */
    public function visit_conditions(nc_netshop_condition_visitor $visitor) {
        return $this->get_conditions()->visit($visitor);
    }

}