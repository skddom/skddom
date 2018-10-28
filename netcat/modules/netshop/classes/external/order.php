<?php

/**
 * nc_netshop_external_order
 *
 * Класс предназначен для обработки внешних запросов на создание заказов.
 * Также может использоваться в отрыве от API создания внешнего заказа,
 * для упрощения процесса работы с заказами в целом.
 *
 * @author Коземиров П. П. pavel@kozemirov.ru
 * @version 1.0
 */
class nc_netshop_external_order {
    /**
     * Массив данных заказа для быстрого доступа (взаимодействие с массивом через геттер и сеттер)
     * @var array
     */
    private $data = array();

    /**
     * Авторизация - проверяем переданные параметры на соответствие настройкам магазина
     *
     * @param $secret_key    // Секретный ключ для авторизации
     */
    static public function authorize($secret_key = null) {
        $netshop = nc_netshop::get_instance();

        // Если указан список разрешенных IP, проверяем, откуда пришел запрос
        $ip_list = $netshop->get_setting('ExternalOrderIPList');
        if ($ip_list) {
            if (!in_array($_SERVER['REMOTE_ADDR'], explode("\r\n", $ip_list))) {
                self::return_json(array(
                    'message' => 'Not allowed IP: ' . $_SERVER['REMOTE_ADDR'],
                    'data' => array('code' => 0)
                ));
            }
        }

        // Проверяем секретный ключ (если в настройках он не указан, внешний заказ для этого сайта невозможен)
        $shop_secret_key = $netshop->get_setting('ExternalOrderSecretKey');
        if (!strlen($shop_secret_key)) {
            self::return_json(array(
                'message' => 'External order is not allowed for this site',
                'data' => array('code' => 1)
            ));
        } else if ($secret_key != $shop_secret_key) {
            self::return_json(array(
                'message' => 'Wrong secret key',
                'data' => array('code' => 2)
            ));
        }

    }

    /**
     * Функция стирает весь вывод, который до этого присутствовал в буфере,
     * обрывает выполнение скрипта и выводит переданные в нее JSON-данные
     * (Значение "Content-type" в заголовках будет равно "application/json")
     *
     * @param $data    // массив данных для вывода в формате JSON
     */
    static public function return_json($data = array()) {
        ob_clean();
        header("Content-Type: application/json");
        echo json_encode($data);
        exit;
    }

    /**
     * Сеттер
     *
     * @param $key      // ключ
     * @param $value    // значение
     */
    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * Геттер
     *
     * @param $key      // ключ
     */
    public function __get($key) {
        return $this->data[$key];
    }

    /**
     * Конструктор
     *
     * @param $order_data    // массив данных заказа,
     *                          формат: array(
     *                              'properties' => ... - поля заказа,
     *                              'items' => ... - массив товаров
     *                          )
     */
    public function __construct($order_data = null) {
        $netshop = nc_netshop::get_instance();

        // Проверяем валидность входных данных заказа
        if (empty($order_data)) {
            self::return_json(array(
                'message' => 'Order data is empty',
                'data' => array('code' => 3)
            ));
        }

        // Дополнительные значения для заказа
        $order_data['properties']['Created'] = date("Y-m-d H:i:s");

        // Создаем новый заказ
        $this->order = $netshop->create_order($order_data['properties']);
        $this->order->set_items(nc_netshop_item_collection::from_array($order_data['items']));
        $this->order->save();
        $netshop->place_order($this->order);

        // Сохраняем данные созданного заказа
        $this->id = $this->order->get_id();
    }

    /**
     * Функция получения данных заказа в виде массива,
     * идентичного тому, что передается в конструктор
     *
     * @param     $order_id      // ID заказа
     * @return    array          // Массив данных заказа
     *                              формат: array(
     *                                  'properties' => ... - поля заказа,
     *                                  'items' => ... - массив товаров
     *                              )
     */
    static public function get_by_id($order_id){
        $order = nc_netshop::get_instance()->load_order($order_id);
        $order_data = array();

        if ($order) {
            $order_data['properties'] = $order->to_array();
            $order_data['items'] = array();

            foreach ($order->get_items() as $item) {
                $item_class_id = $item['Class_ID'];
                $item_message_id = $item['Message_ID'];

                $order_data['items'][$item_class_id][$item_message_id] = array(
                    'Qty' => $item->get('Qty'),
                    'OrderParameters' => $item->get('OrderParameters')
                );
            }
        }

        return $order_data;
    }
}
