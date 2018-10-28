<?php

/**
 * Класс для реализации поля типа "Логическая переменная"
 */
class nc_a2f_field_checkbox extends nc_a2f_field {

    protected $value_for_on = 'on';
    protected $value_for_off = '';
    protected $value_for_inherit = '#INHERIT';

    public function render_value_field($html = true) {

        if ($this->can_inherit_values) { // "наследовать (#INHERIT#) / нет / да ("on")
            $inherit = ($this->value == $this->value_for_inherit || !$this->is_set);
            $is_off = (!$inherit && $this->value == $this->value_for_off);
            $is_on = (!$inherit && ($this->value == $this->value_for_on || ($this->value && !$is_off)));

            $ret = "<select name='{$this->get_field_name()}'>" .
                   "<option value='{$this->value_for_inherit}'" . ($inherit ? ' selected' : '') . ">" .
                        CONTROL_CUSTOM_SETTINGS_INHERIT .
                   "<option value='{$this->value_for_off}'" . ($is_off ? ' selected' : '') . ">" .
                        CONTROL_CUSTOM_SETTINGS_OFF .
                   "<option value='{$this->value_for_on}'" . ($is_on ? ' selected' : '') . ">" .
                        CONTROL_CUSTOM_SETTINGS_ON .
                   "</select>";
        }
        else {
            $ret = "<input name='" . $this->get_field_name() . "' type='hidden' value='{$this->value_for_off}'>" .
                   "<input name='" . $this->get_field_name() . "' type='checkbox' value='{$this->value_for_on}'" .
                   ($this->value && $this->value != $this->value_for_off ? " checked='checked'" : "") .
                   "  class='ncf_value_checkbox'>";
}

        if ($html) {
            $ret = "<div class='ncf_value'>" . $ret . "</div>\n";
        }

        return $ret;
    }


    /**
     *
     */
    protected function get_displayed_default_value() {
        return $this->default_value ? CONTROL_CUSTOM_SETTINGS_ON : CONTROL_CUSTOM_SETTINGS_OFF;
    }

}