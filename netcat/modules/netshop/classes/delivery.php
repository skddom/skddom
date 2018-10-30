<?php

/**
 * nc_netshop_delivery
 * Содержит общую функциональность для расчёта стоимости и времени доставки.
 *
 * В netshop имеются следующие сущности, связанные с доставкой:
 *
 * 1) Способы доставки — nc_netshop_delivery_method.
 *    Создаются и настраиваются в панели управления модулем.
 *
 * 2) Службы расчёта стоимости и сроков доставки и интеграции с внешними API —
 *    nc_netshop_delivery_service.
 *    Привязываются к способу доставки в панели управления (способ доставки может
 *    не иметь привязанной службы расчёта).
 *
 *    Список доступных для выбора служб расчёта доставки задаётся в списке
 *    ShopDeliveryService (в поле Value должен быть указан класс-наследник
 *    nc_netshop_delivery_service).
 *
 * 3) Варианты способов доставки — nc_netshop_delivery_method_variant.
 *    Некоторые службы расчёта доставки могут предлагать более одного варианта
 *    доставки. В этом случае у службы должно быть установлено свойство
 *    $can_provide_multiple_variants = true и реализован метод
 *    nc_netshop_delivery_service::get_variants(), который вернёт коллекцию с
 *    доступными вариантами.
 *
 *    Метод коллекции nc_netshop_delivery_method_collection::with_variants()
 *    возвращает коллекцию, в которой заменяет для всех служб с вариантами
 *    соответствующие способы доставки на имеющиеся варианты.
 *
 */
class nc_netshop_delivery {

    // Доставка курьером до указанного адреса (возможно выбрано время)
    const DELIVERY_TYPE_COURIER = 'courier';
    // Доставка до почтового отделения (соответственно указанному адресу)
    const DELIVERY_TYPE_POST = 'post';
    // Доставка до пункта выдачи (выбран адрес пункта выдачи)
    const DELIVERY_TYPE_PICKUP = 'pickup';
    // Служба доставки предоставляет различные варианты доставки, конкретный тип устанавливается для вариантов
    const DELIVERY_TYPE_MULTIPLE = 'multiple';


    /** @var nc_netshop  */
    protected $netshop;

    protected $handler_classifier_table = "Classificator_ShopDeliveryService";
    protected $handler_classifier_table_pk = "ShopDeliveryService_ID";

    /**
     * @param nc_netshop $netshop
     */
    public function __construct(nc_netshop $netshop) {
        $this->netshop = $netshop;
    }

    /**
     * Список включённых способов доставки для текущего сайта
     * @return nc_netshop_record_conditional_collection nc_netshop_delivery_method[]
     */
    public function get_enabled_methods() {
        $query = "SELECT *
                   FROM `%t%`
                  WHERE `Catalogue_ID` = " . (int)$this->netshop->get_catalogue_id() . "
                    AND `Checked` = 1
                  ORDER BY `Priority`";

        return nc_record_collection::load('nc_netshop_delivery_method', $query);
    }

    /**
     * Список всех способов доставки для текущего сайта
     * @return nc_netshop_record_conditional_collection nc_netshop_delivery_method[]
     */
    public function get_all_methods() {
        $query = "SELECT *
                   FROM `%t%`
                  WHERE `Catalogue_ID` = " . (int)$this->netshop->get_catalogue_id() . "
                  ORDER BY `Priority`";

        return nc_record_collection::load('nc_netshop_delivery_method', $query);
    }

    /**
     * Возвращает объект nc_netshop_delivery_method или nc_netshop_delivery_method_variant
     * с указанным ID, при условии что способ доставки привязан к текущему сайту, включён и
     * удовлетворяет условиям ($context); иначе возвращает NULL
     *
     * @param int|string $method_id  ID может быть составным для варианта доставки
     *      (ID способа и ID варианта через двоеточие)
     * @param nc_netshop_condition_context $context
     * @return nc_netshop_delivery_method|null
     */
    public function get_method_if_enabled($method_id, nc_netshop_condition_context $context) {
        try {
            list($method_id, $variant_id) = explode(':', "$method_id:", 2);
            $method = new nc_netshop_delivery_method($method_id);
            $method_is_enabled =
                $method->get_id() &&
                $method->get('enabled') &&
                $method->get('catalogue_id') == $this->netshop->get_catalogue_id() &&
                $method->evaluate_conditions($context);

            if ($method_is_enabled) {
                return $variant_id ? $method->get_variant($variant_id, $context->get_order()) : $method;
            }
        }
        catch (Exception $e) {}
        return null;
    }

    /**
     * Проверка заказа перед оформлением
     * @param nc_netshop_order $order
     * @param nc_netshop_condition_context $context
     * @return array
     */
    public function check_new_order(nc_netshop_order $order, nc_netshop_condition_context $context) {
        $errors = array();
        // Проверка на существование и применимость метода оплаты
        $method_id = $order->get('DeliveryMethod');
        if ($method_id && !$this->get_method_if_enabled($method_id, $context)) {
            $errors[] = NETCAT_MODULE_NETSHOP_CHECKOUT_INCORRECT_DELIVERY_METHOD;
        }
        return $errors;
    }

    /**
     * @param nc_netshop_order $order
     */
    public function checkout(nc_netshop_order $order) {
        $delivery_method = $order->get_delivery_method();
        if (!$delivery_method) {
            return;
        }

        // Ранее должна была проведена проверка на то, существует ли метод доставки
        // и возможен ли такой способ доставки для оформляемого заказа.

        // Оценка стоимости доставки:
        $estimate = $delivery_method->get_estimate($order);

        // Установить стоимость доставки в заказе:
        $order->set('DeliveryCost', $estimate->get('full_price'));

        // Сохранение ID способа доставки [у вариантов доставки составные ID]
        list($delivery_method_id) = explode(':', $delivery_method->get_id());
        $order->set('DeliveryMethod', $delivery_method_id);
    }

    /**
     * @param $delivery_method_id
     * @param $order
     * @return nc_netshop_delivery_estimate
     */
    public function get_estimate($delivery_method_id, nc_netshop_order $order) {
        try {
            $delivery_method = new nc_netshop_delivery_method($delivery_method_id);
        }
        catch (Exception $e) {
            // Error: cannot instantiate delivery method object (most likely ID is wrong)
            return new nc_netshop_delivery_estimate(array(
                'error_code' => nc_netshop_delivery_estimate::ERROR_WRONG_METHOD_ID,
                'error' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_INCORRECT_METHOD_ID
            ));
        }

        if (!$delivery_method->get_id() ||         // does not exist (e.g. $delivery_method_id = 0)
            !$delivery_method->get('enabled') ||   // is not enabled
             $delivery_method->get('catalogue_id') != $order->get_catalogue_id())   // not from the same site
        {
            return new nc_netshop_delivery_estimate(array(
                'error_code' => nc_netshop_delivery_estimate::ERROR_WRONG_METHOD,
                'error' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_INCORRECT_METHOD_ID
            ));
        }

        return $delivery_method->get_estimate($order);
    }

    /**
     * Возвращает новый экземпляр класса расчёта доставки (nc_netshop_delivery_service_*)
     * по ID в списке ShopDeliveryService
     *
     * @param $handler_id
     * @return null|nc_netshop_delivery_service
     */
    public function get_delivery_service_by_id($handler_id) {
        $handler_id = (int)$handler_id;
        if (!$handler_id) { return null; }

        $handler_class = nc_db()->get_var(
            "SELECT `Value`
               FROM `$this->handler_classifier_table`
              WHERE `$this->handler_classifier_table_pk` = $handler_id"
        );
        if (is_subclass_of($handler_class, "nc_netshop_delivery_service")) {
            return new $handler_class;
        }
        else {
            trigger_error('Wrong delivery service ID or class name', E_USER_WARNING);
        }

        return null;

    }

}

