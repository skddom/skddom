<?php

/**
 * Класс для реализации поля типа "Цвет"
 */
class nc_a2f_field_color extends nc_a2f_field {

    protected $empty_option_text = NETCAT_MODERATION_LISTS_CHOOSE;
    protected $has_default = 1;

    //  возможные значения (значение => описание)
    protected $values = array(
        'transparent' => NETCAT_CUSTOM_TYPENAME_COLOR_TRANSPARENT,
        '#ffffff' => '1',
        '#e5e5e5' => '2',
        '#cccccc' => '3',
        '#999999' => '4',
        '#666666' => '5',
        '#333333' => '6',
        '#616b7e' => '7',
        '#59090a' => '8',
        '#15224d' => '9',
        '#2872bf' => '10',
        '#f89515' => '11',
        '#ec7669' => '12',
        '#774f8f' => '13',
        '#76872b' => '14',
        '#e9004a' => '15',
        '#ff5633' => '16',
        '#fac437' => '17',
        '#33bd4e' => '18',
        '#3caff1' => '19',
        '#51eec3' => '20',
        '#f0e9da' => '21',
        '#f0fcff' => '22',
        '#f9fffd' => '23',
        '#feeced' => '24',
        '#fffbf5' => '25',
        '#f2dffc' => '26',
        '#f9ad81' => '27',
        '#fff200' => '28',
        '#c69c6e' => '29',
        '#f49ac2' => '30',
        '#3bb878' => '31',
    );

    /**
     * @param bool $html
     * @return string
     */
    public function render_value_field($html = true) {
        // текущее значение
        $current_value = $this->get_value_for_input();

        $ret = "<select name='" . $this->get_field_name() . "'  class='ncf_value_select'>\n";

        $ret = "<div id='nc-field-color-control-" . $this->name . "' class='nc-field-color-container'>";
        $ret .= "<a href='#' class='nc-field-color-box " . ($current_value == "transparent" ? "nc-field-color-transparent" : "") . "' style='" . ($current_value != "transparent" ? "background: {$current_value};" : "") . "'>";
        $ret .= "<input type='hidden' name='" . $this->get_field_name() . "' value='{$current_value}' />";
        $ret .= "</a>";
        $ret .= "<div class='nc-field-color-popup'>";

        foreach ((array)$this->values as $k => $v) {
            $ret .= "<a href='#' data-value='{$k}' class='" . ($k == $current_value ? "nc-field-color-selected" : "") . " " . ($k == "transparent" ? "nc-field-color-transparent" : "") . "' style='" . ($k != "transparent" ? "background: {$k};" : "") . "'><span></span></a>";
        }
        $ret .= "</div>";
        $ret .= "</div>";
        $ret .= "<script>
    \$nc(function () {
        var \$colorFieldContainer = \$nc('#nc-field-color-control-" . $this->name . "');
        \$colorFieldContainer.find('.nc-field-color-box').on('click', function (e) {
            e.preventDefault();
            \$nc(this).parent().find('.nc-field-color-popup').fadeToggle();
        });

        \$colorFieldContainer.find('.nc-field-color-popup A').on('click', function (e) {
            e.preventDefault();
            var \$this = \$nc(this);
            \$this.addClass('nc-field-color-selected').siblings().removeClass('nc-field-color-selected');
            var value = \$this.attr('data-value');
            if (value == 'transparent') {
                \$colorFieldContainer.find('.nc-field-color-box').css({
                    background: ''
                }).addClass('nc-field-color-transparent');
            } else {
                \$colorFieldContainer.find('.nc-field-color-box').css({
                    background: value
                }).removeClass('nc-field-color-transparent');
            }            
            \$colorFieldContainer.find('.nc-field-color-box INPUT').val(value);
            \$colorFieldContainer.find('.nc-field-color-popup').fadeToggle();
        });
    });
</script>";

        if ($html) {
            $ret = "<div class='ncf_value'>" . $ret . "</div>\n";
        }

        return $ret;
    }

    /**
     *
     */
    protected function get_displayed_default_value() {
        return $this->values[$this->default_value];
    }

}