<?php

/**
 * Сохранение данных фильтра в $_SESSION
 */
class nc_netshop_filter_driver_session extends nc_netshop_filter_driver {

    /**
     * Сбрасывает данные фильтра
     */
    public function remove_filter_data() {
        unset($_SESSION['netshop_filter'][$this->get_session_key()]);
        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 7) === 'filter_') {
                unset($_POST[$key]);
            }
        }
    }

    /**
     * @param array $fields
     * @return array
     */
    public function get_filter_data(array $fields) {
        $key = $this->get_session_key();

        foreach ($fields as $field_name => $field_data) {
            $value = null;
            $input_name = 'filter_' . $field_name;

            if ($field_data['field'] == 'checkbox' && !isset($_POST[$input_name])) {
                $_POST[$input_name] = null;
            }

            if (isset($_POST[$input_name])) {
                $value = $_POST[$input_name];
            } else if (isset($_SESSION['netshop_filter'][$key][$field_name])) {
                continue;
            }

            // фильтр по диапазону будет сброшен, если значения равны
            // предыдущему «полному» диапазону (filter_ИМЯПОЛЯ___min,
            // filter_ИМЯПОЛЯ___max)
            $is_full_range = (
                is_array($value) &&
                isset($_POST["{$input_name}___min"]) &&
                isset($_POST["{$input_name}___max"]) &&
                $value[0] == $_POST["{$input_name}___min"] &&
                $value[1] == $_POST["{$input_name}___max"]
            );

            $unset_field_filter_data = (
                $value === null || // value is null
                (is_array($value) && !strlen(implode('', $value))) || // empty array
                (is_scalar($value) && !strlen($value)) || // empty string
                $is_full_range // is "full range"
            );

            if ($unset_field_filter_data) {
                unset($_SESSION['netshop_filter'][$key][$field_name]);
            }
            else {
                $_SESSION['netshop_filter'][$key][$field_name] = $value;
            }
        }

        return $_SESSION['netshop_filter'][$key];
    }

    /**
     * @return string
     */
    public function get_form_method() {
        return "POST";
    }

    /**
     * @return bool
     */
    public function should_include_range_margins_in_form() {
        return true;
    }

    /**
     * Возвращает ключ массива в $_SESSION, в котором хранятся данные фильтра
     * @return string
     */
    protected function get_session_key() {
        return $this->filter->options('ignore_cc')
                    ? "c" . $this->filter->get_component_id()
                    : "i" . $this->filter->get_infoblock_id();
    }

}