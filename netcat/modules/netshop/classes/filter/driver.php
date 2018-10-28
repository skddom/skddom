<?php

/**
 * Базовый класс для различных способов передачи выбранных значений фильтра между страницами
 */
abstract class nc_netshop_filter_driver {

    /** @var  nc_netshop_filter */
    protected $filter;

    /**
     * nc_netshop_filter_driver constructor.
     *
     * @param nc_netshop_filter $filter
     */
    public function __construct(nc_netshop_filter $filter) {
        $this->filter = $filter;
    }

    /**
     * Возвращает данные фильтра
     * @param array $fields
     * @return array
     */
    abstract public function get_filter_data(array $fields);

    /**
     * Сбрасывает данные фильтра
     */
    abstract public function remove_filter_data();

    /**
     * Возвращает метод, которым должна быть передана на сервер форма фильтра
     * @return string  'GET', 'POST'
     */
    public function get_form_method() {
        return 'GET';
    }

    /**
     * Включать ли в форму данные о минимальных и максимальных значениях диапазонов
     * (для nc_netshop_filter_driver_session, может быть удалено в будущем).
     * @return bool
     */
    public function should_include_range_margins_in_form() {
        return false;
    }

}