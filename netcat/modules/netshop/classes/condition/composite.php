<?php

abstract class nc_netshop_condition_composite extends nc_netshop_condition {

    /** @var nc_netshop_condition[] */
    protected $children = array();

    /**
     * @param array $parameters
     */
    public function __construct($parameters = array()) {
        $children_params = (array)$parameters['conditions'];
        foreach ($children_params as $child_param) {
            $this->children[] = self::create($child_param);
        }
    }

    /**
     * @return array|nc_netshop_condition[]
     */
    public function get_children() {
        return $this->children;
    }

    /**
     *
     */
    public function has_condition_of_type($type) {
        foreach ($this->children as $child) {
            if ($child->has_condition_of_type($type)) { return true; }
        }
        return false;
    }

    /**
     * @param nc_netshop $netshop
     * @param string $glue
     * @param string $same_type_glue
     * @return array
     */
    protected function get_children_descriptions(nc_netshop $netshop, $glue, $same_type_glue) {
        $descriptions = array();
        $previous_child_class = null;
        $previous_op = null;
        $last = -1;
        foreach ($this->children as $child) {
            $child_class = get_class($child);
            $op = $child->get('op');

            if ($child_class !== $previous_child_class || $op != $previous_op) {
                $descriptions[] = $child->get_full_description($netshop);
                $last++;
            }
            else {
                $descriptions[$last] .= $same_type_glue . $child->get_short_description($netshop);
            }

            $previous_child_class = $child_class;
            $previous_op = $op;
        }
        return join($glue, $descriptions);
    }

    /**
     * Короткое описание (только значение, для повторяющихся условий)
     * @param nc_netshop $netshop
     * @return string
     */
    public function get_short_description(nc_netshop $netshop) {
        return $this->get_full_description($netshop);
    }


    /**
     * @param nc_backup_dumper $dumper
     * @return array
     */
    protected function get_updated_parameters_on_import(nc_backup_dumper $dumper) {
        $children_parameters = array();
        foreach ($this->children as $child) {
            $children_parameters[] = $child->get_updated_raw_options_array_on_import($dumper);
        }

        return array('conditions' => $children_parameters);
    }

}