<?php

/**
 * Коллекция интервалов работы (расписание)
 */
class nc_netshop_delivery_schedule extends nc_record_collection {

    protected $items_class = 'nc_netshop_delivery_interval';

    protected $compact_schedule_string = null;

    /**
     * Сброс закэшированного значения compact_schedule_string при изменении коллекции
     */
    public function on_collection_change() {
        $this->compact_schedule_string = null;
    }

    /**
     * Возвращает расписание в виде компактной строки (например: «пн—пт 10:00—20:00, сб 10:00—16:00»
     * @return string
     */
    public function get_compact_schedule_string() {
        if ($this->compact_schedule_string !== null) {
            return $this->compact_schedule_string;
        }

        $day_schedules = array();

        /** @var nc_netshop_delivery_interval $item */
        foreach ($this->items as $item) {
            for ($day = 1; $day <= 7; $day++) {
                if ($item->get("day$day")) {
                    $day_schedules[$day] =
                        (!empty($day_schedules[$day]) ? "$day_schedules[$day], " : '') .
                        $item->get_time_interval_string();
                }
            }
        }

        ksort($day_schedules);

        // пробуем «склеить» одинаковые расписания
        $interval_first_day = null;
        $intervals = array();

        foreach ($day_schedules as $day => $schedule) {
            if ($interval_first_day === null) {
                $interval_first_day = $day;
            }

            $next_day_schedule = nc_array_value($day_schedules, $day + 1);
            if ($next_day_schedule !== $schedule) {
                // дни недели
                $first_day_string = nc_netshop_delivery_interval::get_day_of_week_short_name($interval_first_day);
                $last_day_string = nc_netshop_delivery_interval::get_day_of_week_short_name($day);

                if ($first_day_string == $last_day_string) {
                    $interval = $first_day_string;
                }
                else {
                    $interval = sprintf(NETCAT_MODULE_NETSHOP_SHORT_DAY_OF_WEEK_RANGE, $first_day_string, $last_day_string);
                }

                $intervals[] = $interval . ' ' . $schedule;
                $interval_first_day = null;
            }
        }

        $this->compact_schedule_string = implode(', ', $intervals);
        return $this->compact_schedule_string;
    }

   /**
     * Возвращает расписание в виде компактной строки без разбивки на интервалы
     * (минимальное — максимальное время). Перерывы не учитываются!
     * @return string
     */
    public function get_compact_schedule_summary_string() {
        $day_schedules = array();

        /** @var nc_netshop_delivery_interval $item */
        foreach ($this->items as $item) {
           for ($day = 1; $day <= 7; $day++) {
               if ($item->get("day$day")) {
                   $day_schedules[$day][] = $item->get('time_from');
                   $day_schedules[$day][] = $item->get('time_to');
               }
           }
        }

        $compact_collection = $this->make_new_collection();
        foreach ($day_schedules as $day => $times) {
            natsort($times);
            $compact_collection->add(new nc_netshop_delivery_interval(array(
                'day'. $day => true,
                'time_from' => current($times),
                'time_to' => end($times),
            )));
        }

        return $compact_collection->get_compact_schedule_string();
    }
}