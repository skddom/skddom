<?php

/**
 * Интервал работы пункта выдачи или курьерской доставки
 */
class nc_netshop_delivery_interval extends nc_record {

    protected $properties = array(
        'id' => null,
        'parent_id' => null,
        'day1' => false,
        'day2' => false,
        'day3' => false,
        'day4' => false,
        'day5' => false,
        'day6' => false,
        'day7' => false,
        'time_from' => null,
        'time_to' => null,
    );

    /**
     * @param $day
     * @return string
     */
    public static function get_day_of_week_short_name($day) {
        static $day_names;
        if (!$day_names) {
            $day_names = explode('/', NETCAT_MODULE_NETSHOP_DELIVERY_DAYS_OF_WEEK_SHORT);
        }

        return isset($day_names[$day]) ? $day_names[$day] : '';
    }

    /**
     * @param string $property
     * @param mixed $value
     * @param bool $add_new_property
     * @return nc_record
     */
    public function set($property, $value, $add_new_property = false) {
        if ($property === 'time_from' || $property === 'time_to') {
            $value = $this->format_time($value);
        }
        return parent::set($property, $value, $add_new_property);
    }

    /**
     * @param $value
     * @return string
     */
    protected function format_time($value) {
        list($hours, $minutes) = preg_split('/\D+/', trim("$value:"));

        if ($hours > 23) {
            $hours = 23;
            $minutes = 59;
        }

        if ($minutes > 59) {
            $minutes = 59;
        }

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * @return string
     */
    public function get_time_interval_string() {
        $from = $this->get('time_from');
        $to = $this->get('time_to');

        if ($from === '00:00' && $to === '23:59') {
            return NETCAT_MODULE_NETSHOP_DELIVERY_TIME_ALL_DAY;
        }

        return sprintf(NETCAT_MODULE_NETSHOP_COND_TIME_INTERVAL, $from, $to);
    }

}