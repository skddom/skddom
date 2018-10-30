<?php

/**
 * Class nc_netshop_delivery_estimate
 *
 * Оценка стоимости и сроков доставки заказа
 */
class nc_netshop_delivery_estimate extends nc_record {

    // нет ошибки:
    const ERROR_OK = 0;
    // не удалось загрузить информацию о методе доставке (скорее всего — нет способа доставки с заданным ID)
    const ERROR_WRONG_METHOD_ID = 1;
    // выбранный способ доставки отключён или не принадлежит к указанному сайту
    const ERROR_WRONG_METHOD = 2;
    // не удалось подключиться к службе расчёта стоимости доставки:
    const ERROR_SERVICE_CANNOT_CONNECT_TO_GATE = 10;
    // ошибка в ответе службе расчёта стоимости доставки:
    const ERROR_SERVICE_GATE_ERROR = 11;
    // указан некорректный вес для службы расчёта доставки:
    const ERROR_SERVICE_WRONG_WEIGHT = 12;
    // некорректно указан адрес (индекс, город и т.п.) получателя для службы расчёта доставки:
    const ERROR_SERVICE_WRONG_RECIPIENT = 13;
    // некорректно указан адрес отправителя для службы расчёта доставки:
    const ERROR_SERVICE_WRONG_SENDER = 14;


    protected $properties = array(
        'catalogue_id' => null,
        'delivery_method_id' => null,
        'delivery_method_name' => null,
        'calculation_timestamp' => null,
        'order_id' => null,
        'full_price' => null,
        'price' => null,
        'discount' => null,
        'min_days' => null,
        'max_days' => null,
        'error_code' => null,
        'error' => null,
    );

    // -------------------------------------------------------------------------

    /**
     * @return bool
     */
    public function has_error() {
        return $this->get('error_code') || $this->get('error');
    }
    
    /**
     * Timestamp дня ближайшего возможного срока доставки
     * (Расчёт сроков доставки ведётся с точностью до дня)
     * Если срок доставки не оценен, возвращает NULL.
     *
     * @return int|null
     */
    public function get_closest_delivery_timestamp() {
        $min_days = $this->get('min_days');
        if ($min_days === null) { return null; }
        // переход на зимнее/летнее время игнорируется
        return $this->get('calculation_timestamp') + $min_days * 86400;
    }

    /**
     * Дата ближайшего возможного срока доставки (ISO, yyyy-mm-dd)
     * Если срок доставки не оценен, возвращает NULL.
     *
     * @return string|null
     */
    public function get_closest_delivery_date() {
        $timestamp = $this->get_closest_delivery_timestamp();
        if (!$timestamp) { return null; }
        return date("Y-m-d", $timestamp);
    }

    /**
     * Timestamp дня верхней оценки срока доставки
     * Если срок доставки не оценен, возвращает NULL.
     *
     * @return int|null
     */
    public function get_latest_delivery_timestamp() {
        $max_days = $this->get('max_days');
        if ($max_days === null) { return null; }
        // переход на зимнее/летнее время игнорируется
        return $this->get('calculation_timestamp') + $max_days * 86400;
    }

    /**
     * Дата верхней оценки срока доставки (ISO, yyyy-mm-dd)
     * Если срок доставки не оценен, возвращает NULL.
     *
     * @return string|null
     */
    public function get_latest_delivery_date() {
        $timestamp = $this->get_latest_delivery_timestamp();
        if (!$timestamp) { return null; }
        return date("Y-m-d", $timestamp);
    }

    /**
     * Проверяет, есть ли разброс в оценке срока доставки (TRUE), или минимальная
     * и максимальная оценка срока доставки совпадают (FALSE)
     *
     * @return bool
     */
    public function has_dates_interval() {
        if ($this->get('min_days') === null) { return false; }
        if ($this->get('min_days') == $this->get('max_days')) { return false; }
        return true;
    }

    /**
     * Вспомогательный метод для формирования строки с диапазоном дат
     *
     * @return string|null   Например: завтра; 5 марта; 10—12 апреля; 28 октября — 3 ноября
     */
    public function get_dates_string() {
        $min_date = $this->get_closest_delivery_timestamp();
        $max_date = $this->get_latest_delivery_timestamp();

        if (!$min_date) { return null; }

        // для упрощения логики предполагаем, что доставка всегда занимает не более 11 месяцев

        $month_names = explode("/", NETCAT_MODULE_NETSHOP_MONTHS_GENITIVE);

        $min_date_day = date(NETCAT_MODULE_NETSHOP_GENITIVE_DAY_FORMAT, $min_date);
        $max_date_day = date(NETCAT_MODULE_NETSHOP_GENITIVE_DAY_FORMAT, $max_date);
        $min_date_month = date('n', $min_date);
        $max_date_month = date('n', $max_date);

        if ($min_date_month == $max_date_month) { // разброс дат — в течение одного календарного месяца
            $month_name = $month_names[$min_date_month];
            if ($min_date_day == $max_date_day) { // нет диапазона, только одна дата
                $date = date("Ymd", $min_date);
                if ($date == date("Ymd")) { // сегодня!
                    $dates_string = NETCAT_MODULE_NETSHOP_DATE_TODAY;
                }
                elseif ($date == date("Ymd", strtotime("+1 day"))) { // завтра!
                    $dates_string = NETCAT_MODULE_NETSHOP_DATE_TOMORROW;
                }
                else {
                    $dates_string = sprintf(NETCAT_MODULE_NETSHOP_DAY_AND_MONTH_FORMAT,
                                            $min_date_day, $month_name);
                }
            }
            else { // диапазон дат, один календарный месяц
                $dates_string = sprintf(NETCAT_MODULE_NETSHOP_DATE_RANGE_FORMAT_ONE_MONTH,
                                        $min_date_day, $max_date_day, $month_name);
            }
        }
        else { // даты находятся в разных месяцах
            $dates_string = sprintf(NETCAT_MODULE_NETSHOP_DATE_RANGE_FORMAT,
                                    $min_date_day, $month_names[$min_date_month],
                                    $max_date_day, $month_names[$max_date_month]);
        }

        return $dates_string;
    }

    /**
     * Возвращает строку с отформатированной стоимостью заказа с добавлением
     * скидки (если есть), например:  "230 руб. (скидка: 52 руб.)"
     *
     * @return string
     */
    public function get_formatted_price_and_discount() {
        $netshop = nc_netshop::get_instance($this->get('catalogue_id'));
        $result = ($this->get('price')
                            ? $netshop->format_price($this->get('price'))
                            : NETCAT_MODULE_NETSHOP_DELIVERY_FREE_OF_CHARGE);
        // скидка (если есть):
        if ($this->get('discount')) {
            $result .= ' ' . sprintf(NETCAT_MODULE_NETSHOP_DELIVERY_DISCOUNT_STRING,
                                     $netshop->format_price($this->get('discount')));
        }

        return $result;
    }

}