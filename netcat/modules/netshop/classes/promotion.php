<?php

/**
 * SALES PROMOTIONS 'SUBMODULE': discounts, coupons etc.
 */

class nc_netshop_promotion {

    //--------------------------------------------------------------------------

    /** @var nc_netshop_promotion_discount_item_collection */
    protected $item_discounts_cache;

    /** @var  nc_netshop_promotion_discount_delivery_collection */
    protected $delivery_discounts_cache;

    /** @var  nc_netshop_promotion_discount_cart_collection */
    protected $cart_discounts_cache;

    /** @var nc_netshop_promotion_discount_item_collection */
    protected $activated_item_discounts_cache;

    /** @var  nc_netshop_promotion_coupon_notifications */
    protected $coupon_notifications;
    /** @var nc_netshop_promotion_coupon_collection */
    protected $registered_coupons;
    protected $coupon_cache = array();

    /** @var nc_netshop_condition_context $context */
    protected $context;

    /** @var nc_netshop */
    protected $netshop;

    protected $are_item_discounts_enabled;
    protected $are_delivery_discounts_enabled;
    protected $are_coupons_enabled;

    //--------------------------------------------------------------------------

    /**
     * @param nc_netshop $netshop
     */
    public function __construct(nc_netshop $netshop) {
        $this->netshop = $netshop;

        $this->are_item_discounts_enabled = $netshop->is_feature_enabled('promotion_discount_item');
        $this->are_delivery_discounts_enabled = $netshop->is_feature_enabled('promotion_discount_delivery');
        $this->are_coupons_enabled = $netshop->is_feature_enabled('promotion_coupon');
    }

    /**
     * @return nc_netshop_condition_context
     */
    protected function get_context() {
        if (!$this->context) {
            /* @todo review, simplify */
            $this->context = $this->netshop->get_condition_context();
        }
        return $this->context;
    }

    /**
     * Устанавливает свойства, относящиеся к подмодулю promotion, в контексте $context
     * @param nc_netshop_condition_context $context
     * @return nc_netshop_condition_context
     */
    public function set_condition_context_data(nc_netshop_condition_context $context) {
        $context->set_coupons($this->get_registered_coupons());
        $context->set_activated_item_discounts($this->get_activated_discounts_ids());

        return $context;
    }

    // --- ОБРАБОТКА ЗАКАЗА НА ЭТАПЕ ОФОРМЛЕНИЯ --------------------------------

    /**
     * Проверка заказа перед оформлением
     * @param nc_netshop_order $order
     * @param nc_netshop_condition_context $context
     * @return array
     */
    public function check_new_order(nc_netshop_order $order, nc_netshop_condition_context $context) {
        $errors = array();
        return $errors;
    }

    /**
     * @param nc_netshop_order $order
     */
    public function checkout(nc_netshop_order $order) {
        $this->save_order_cart_discounts($order);
        $this->save_order_delivery_discounts($order);

        // Mark coupons as used; deactivate item discounts
        $this->use_registered_coupons();
        $this->deactivate_all_item_discounts();
    }

    /**
     * @param $order_id
     * @param int $component_id
     * @param int $object_id
     * @param array $discount_info
     */
    protected function save_discount_info($order_id, $component_id, $object_id, array $discount_info) {
        $db = nc_db();
        $query = "INSERT INTO `Netshop_OrderDiscounts`
                     SET `Order_Component_ID` = " . (int)$this->netshop->get_setting('OrderComponentID') . ",
                         `Order_ID` = " . (int)$order_id .",
                         `Item_Type` = " . (int)$component_id . ",
                         `Item_ID` = " . (int)$object_id . ",
                         `Discount_Type` = '" . $db->escape(nc_array_value($discount_info, 'type', '')) . "',
                         `Discount_ID` = " . (int)$discount_info['id'] . ",
                         `Discount_Name` = '" . $db->escape($discount_info['name']) . "',
                         `Discount_Description` = '" . $db->escape($discount_info['description']) . "',
                         `Discount_Sum` = '" . $db->escape(str_replace(',', '.', $discount_info['sum'])) . "',
                         `PriceMinimum` = " . intval($discount_info['price_minimum']) . ",
                         `IsComponentBased` = 0";

        $db->query($query);
    }

    /**
     * @param nc_netshop_order $order
     */
    protected function save_order_cart_discounts(nc_netshop_order $order) {
        $cart_discounts = $this->get_all_cart_discounts()
                               ->get_applicable_discounts();

        /** @var nc_netshop_promotion_discount_cart $discount */
        foreach ($cart_discounts as $discount) {
            $discount_info = array(
                                'type' => 'cart',
                                'id' => $discount->get_id(),
                                'name' => $discount->get('name'),
                                'description' => $discount->get('description'),
                                'sum' => $discount->get_discount_sum($this->get_context()),
                                'price_minimum' => 0,
                            );

            $this->save_discount_info($order->get_id(), 0, 0, $discount_info);
            $order->add_cart_discount($discount_info);
        }
    }

    /**
     * @param nc_netshop_order $order
     */
    protected function save_order_delivery_discounts(nc_netshop_order $order) {
        if (!$this->are_delivery_discounts_enabled) { return; }

        $delivery_estimate = $order->get_delivery_estimate();
        $delivery_discount_sum = $delivery_estimate && !$delivery_estimate->has_error()
            ? $delivery_estimate->get('discount')
            : 0;

        // Если нет скидки на доставку — сохранять нечего
        if (!$delivery_discount_sum) {
            return;
        }

        $full_delivery_price = $delivery_estimate->get('full_price');

        $delivery_discounts = $this->get_all_delivery_discounts()
                                   ->get_applicable_discounts($full_delivery_price);

        /** @var nc_netshop_promotion_discount_delivery $discount */
        foreach ($delivery_discounts as $discount) {
            $discount_info = array(
                                'type' => 'delivery',
                                'id' => $discount->get_id(),
                                'name' => $discount->get('name'),
                                'description' => $discount->get('description'),
                                'sum' => $discount->get_discount_sum($this->get_context(), $full_delivery_price),
                                'price_minimum' => 0,
                            );

            $this->save_discount_info($order->get_id(), 0, 0, $discount_info);
            $order->add_cart_discount($discount_info);
        }
    }

    // --- ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ ----------------------------------------------

    /**
     * @return int
     */
    protected function get_catalogue_id() {
        return $this->netshop->get_catalogue_id();
    }

    /**
     * @return nc_netshop_item_collection
     */
    protected function get_cart_contents() {
        return $this->netshop->get_cart_contents();
    }

    /**
     * @return string
     */
    protected function get_coupon_session_key() {
        return "nc_netshop_" . $this->get_catalogue_id() . "_coupons";
    }

    protected function get_activated_item_discounts_session_key() {
        return "nc_netshop_" . $this->get_catalogue_id() . "_activated_discounts";
    }

    // --- СКИДКИ НА ТОВАРЫ ----------------------------------------------------

    /**
     * Список всех действующих скидок на данном сайте (в том числе активированных
     * введёнными купонами)
     *
     * @return nc_netshop_promotion_discount_item_collection|null
     */
    public function get_all_item_discounts() {
        if (!$this->are_item_discounts_enabled) { return null; }

        if (!$this->item_discounts_cache) {
            $discounts = new nc_netshop_promotion_discount_item_collection();
            $discounts->set_context($this->get_context())
                      ->load_all_discounts();
            $this->item_discounts_cache = $discounts;
        }

        return $this->item_discounts_cache;
    }

    /**
     * Возвращает сумму скидки для указанного товара.
     *
     * @param nc_netshop_item $item
     * @param bool $check_conditions_only
     * @return float|int
     */
    public function get_item_discount_sum(nc_netshop_item $item, $check_conditions_only = false) {
        if ($this->are_item_discounts_enabled) {
            return $this->get_all_item_discounts()->get_discount_sum($item, $check_conditions_only);
        }
        else {
            return 0;
        }
    }

    /**
     * Возвращает список скидок, которые могут быть применены для указанного товара.
     *
     * @param nc_netshop_item $item
     * @return nc_netshop_promotion_discount_item_collection|null
     */
    public function get_item_discounts_for(nc_netshop_item $item) {
        if (!$this->are_item_discounts_enabled) { return null; }
        return $this->get_all_item_discounts()
                    ->get_applicable_discounts($item, true);
    }

    // --- «АКТИВИРУЕМЫЕ» («СИЮМИНУТНЫЕ») СКИДКИ НА ТОВАР ----------------------

    /**
     * Проверка, активирована ли уже скидка для товара.
     * @param $discount_id
     * @param nc_netshop_item $item
     * @return bool
     */
    public function is_item_discount_activated($discount_id, nc_netshop_item $item) {
        if (!$this->are_item_discounts_enabled) { return false; }
        return $this->get_context()->is_item_discount_activated($discount_id, $item);
    }

    /**
     * «Активирует» скидку для товара в текущей сессии.
     * (Для скидок, требующих активации для отдельных товаров.)
     *
     * @param $discount_id
     * @param nc_netshop_item $item
     */
    public function activate_item_discount($discount_id, nc_netshop_item $item) {
        if (!$this->are_item_discounts_enabled) { return; }

        $session_key = $this->get_activated_item_discounts_session_key();
        if (!$_SESSION[$session_key]) {
            $_SESSION[$session_key] = array();
        }
        $item_key = $item["_ItemKey"];
        if (!isset($_SESSION[$session_key][$item_key])) {
            $_SESSION[$session_key][$item_key] = array();
        }
        if (!in_array($discount_id, $_SESSION[$session_key][$item_key])) {
            $_SESSION[$session_key][$item_key][] = $discount_id;
        }
    }

    /**
     * «Деактивирует» указанную скидку для товара.
     *
     * @param $discount_id
     * @param nc_netshop_item $item
     */
    public function deactivate_item_discount($discount_id, nc_netshop_item $item) {
        if (!$this->are_item_discounts_enabled) { return; }

        $session_key = $this->get_activated_item_discounts_session_key();
        $item_key = $item["_ItemKey"];
        if (!isset($_SESSION[$session_key][$item_key])) {
            return;
        }
        $discount_key = array_search($discount_id, $_SESSION[$session_key][$item_key]);
        if ($discount_key !== false) {
            array_splice($_SESSION[$session_key][$item_key], $discount_key, 1);
        }
    }

    /**
     * «Деактивирует» все скидки для всех товаров.
     */
    public function deactivate_all_item_discounts() {
        if (!$this->are_item_discounts_enabled) { return; }
        unset($_SESSION[$this->get_activated_item_discounts_session_key()]);
    }


    /**
     * @return array  ["class_id:message_id" => [discount_id, ...]]
     */
    protected function get_activated_discounts_ids() {
        $session_key = $this->get_activated_item_discounts_session_key();
        if (!isset($_SESSION[$session_key])) {
            return array();
        }
        return (array)$_SESSION[$session_key];
    }

    /**
     * Возвращает список активируемых (не только активированных) скидок для товара.
     * @param nc_netshop_item $item
     * @return nc_netshop_promotion_discount_item_collection|null
     */
    public function get_activated_item_discounts_for_item(nc_netshop_item $item) {
        if (!$this->are_item_discounts_enabled) { return null; }
        return $this->get_all_activated_item_discounts()
                    ->get_applicable_discounts($item, true);
    }

    /**
     * @return nc_netshop_promotion_discount_item_collection
     */
    protected function get_all_activated_item_discounts() {
        if (!$this->activated_item_discounts_cache) {
            $this->activated_item_discounts_cache =
                $this->get_all_item_discounts()->where('item_activation_required', 1);
        }
        return $this->activated_item_discounts_cache;
    }

    /**
     * @param nc_netshop_item $item
     * @return string
     */
    protected function get_special_offer_cookie_name(nc_netshop_item $item) {
        return 'nc_netshop_hide_special_offer_' . $item['Class_ID'] . '_' . $item['Message_ID'];
    }

    /**
     * @param nc_netshop_item $item
     */
    protected function set_special_offer_cookie(nc_netshop_item $item) {
        $cookie_name = $this->get_special_offer_cookie_name($item);
        nc_core::get_object()->cookie->set($cookie_name, true, time() + 31536000);
    }

    /**
     * Проверка, можно ли предложить
     * клиенту сиюминутную скидку на товар
     * или активировать ее
     *
     * @param nc_netshop_item $item
     * @return bool
     */
    public function is_special_offer_available(nc_netshop_item $item) {
        if (!$this->are_item_discounts_enabled) { return null; }

        $cookie_name = $this->get_special_offer_cookie_name($item);
        $cookie_allow = isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] != true : true;

        $discount_activated = false;

        $discounts = $this->get_activated_item_discounts_for_item($item);
        foreach ($discounts as $discount) {
            if ($this->is_item_discount_activated($discount->get('id'), $item)) {
                $discount_activated = true;
                break;
            }
        }

        return $cookie_allow && !$discount_activated && count($discounts) != 0;
    }

    /**
     * Отклонить сиюминутное предложение
     *
     * @param nc_netshop_item $item
     */
    public function reject_special_offer(nc_netshop_item $item) {
        if (!$this->are_item_discounts_enabled) { return; }
        $this->set_special_offer_cookie($item);
    }

    /**
     * Принять сиюминутное предложение и положить товар в корзину
     *
     * @param nc_netshop_item $item
     */
    public function accept_special_offer(nc_netshop_item $item) {
        if (!$this->are_item_discounts_enabled) { return; }

        $this->set_special_offer_cookie($item);

        $discounts = $this->get_activated_item_discounts_for_item($item);
        foreach ($discounts as $discount) {
            $this->activate_item_discount($discount->get('discount_id'), $item);
        }

        $this->netshop->cart->add_item($item['Class_ID'], $item['Message_ID']);
    }

    // --- СКИДКИ НА КОРЗИНУ ---------------------------------------------------
    /**
     * Список всех действующих скидок на состав заказа на данном сайте (в том числе
     * активированных введёнными купонами)
     *
     * @return nc_netshop_promotion_discount_cart_collection
     */
    public function get_all_cart_discounts() {
        if (!$this->cart_discounts_cache) {
            $discounts = new nc_netshop_promotion_discount_cart_collection();
            $discounts->set_context($this->get_context())
                      ->load_all_discounts();
            $this->cart_discounts_cache = $discounts;
        }

        return $this->cart_discounts_cache;
    }

    /**
     * Возвращает сумму скидки на корзину (состав заказа)
     *
     * @return number
     */
    public function get_cart_discount_sum() {
        return $this->get_all_cart_discounts()->get_discount_sum();
    }

    // --- СКИДКИ НА ДОСТАВКУ --------------------------------------------------
    /**
     * Список всех действующих скидок на доставку на данном сайте (в том числе
     * активированных введёнными купонами)
     *
     * @return nc_netshop_promotion_discount_delivery_collection|null
     */
    public function get_all_delivery_discounts() {
        if (!$this->are_delivery_discounts_enabled) { return null; }

        if (!$this->delivery_discounts_cache) {
            $discounts = new nc_netshop_promotion_discount_delivery_collection();
            $discounts->set_context($this->get_context())
                      ->load_all_discounts();
            $this->delivery_discounts_cache = $discounts;
        }

        return $this->delivery_discounts_cache;
    }

    /**
     * Возвращает сумму скидки на доставку
     * @param number $full_delivery_price            Расчитанная стоимость доставки
     * @param int|null $current_delivery_method_id   ID метода доставки
     * @return float|int
     */
    public function get_delivery_discount_sum($full_delivery_price, $current_delivery_method_id = null) {
        if (!$this->are_delivery_discounts_enabled) { return 0; }
        return $this->get_all_delivery_discounts()
                    ->get_discount_sum($full_delivery_price, $current_delivery_method_id);
    }

    // --- КУПОНЫ --------------------------------------------------------------

    /**
     * Привязывает купон к сессии пользователя.
     * @param $coupon_code
     * @return bool   false, если не удалось привязать купон.
     *    Текст возникших ошибок можно получить через $netshop->promotion->get_coupon_notifications()
     */
    public function register_coupon_code($coupon_code) {
        if (!$this->are_coupons_enabled) { return false; }

        $key = $this->get_coupon_session_key();
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = array();
        }

        $coupon_code = nc_netshop_promotion_coupon::sanitize_code($coupon_code);
        if (!strlen($coupon_code)) {
            return false;
        }

        $notifications = $this->get_coupon_notifications();

        // Check coupon:
        // (1) code should exist and (2) coupon is enabled and (3) deal is enabled
        // @todo move? to separate method and to get_coupon() or to the coupon class?
        $coupon = $this->get_coupon($coupon_code);
        if (!$coupon || !$coupon->get('enabled') || !$coupon->get_deal() || !$coupon->get_deal()->get('enabled')) {
            $notifications->add('error', NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_CODE_IS_INVALID, $coupon_code);
            return false;
        }
        // (4) coupon is not expired
        if ($coupon->is_expired()) {
            $notifications->add('error', NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_IS_EXPIRED, $coupon_code);
            return false;
        }
        // (5) coupon should apply to the current site
        $deal = $coupon->get_deal();
        if (!$deal || $deal->get('catalogue_id') != $this->get_catalogue_id()) {
            $notifications->add('error', NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_IS_NOT_VALID_ON_THIS_SITE, $coupon_code);
            return false;
        }
        // (6) coupon is not used up
        if ($coupon->is_used_up()) {
            $notifications->add('error', NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_IS_USED_UP, $coupon_code);
            return false;
        }
        // (7) only one coupon of each deal is allowed
        /** @var $other_coupon nc_netshop_promotion_coupon */
        foreach ($this->get_registered_coupons() as $other_coupon) {
            if ($other_coupon->get_id() != $coupon->get_id() &&
                $other_coupon->get('deal_type') == $coupon->get('deal_type') &&
                $other_coupon->get('deal_id') == $coupon->get('deal_id')
            ) {
                $notifications->add('error', NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_ONLY_ONE_OF_ITS_KIND_IS_ALLOWED, $coupon_code);
                return false;
            }
        }
        /* @todo CHECK: COUPONS OF THIS TYPE HAVEN’T BEEN USED IN THE PAST (?) */

        // Ok, coupon will be registered
        // issue a notice if there are no goods in the cart to which this coupon can be applied
        $cart_contents = $this->get_cart_contents();
        if (sizeof($cart_contents)) {
            if ($this->coupon_can_be_applied_to_cart($coupon, $cart_contents)) {
                $notifications->add('ok', NETCAT_MODULE_NETSHOP_PROMOTION_REGISTERED_COUPON_CODE_IS_APPLIED_TO_CART, $coupon_code);
            } else {
                $notifications->add('notice', NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_CANNOT_BE_APPLIED_TO_ANY_ITEM, $coupon_code);
            }
        } else {
            $notifications->add('ok', NETCAT_MODULE_NETSHOP_PROMOTION_REGISTERED_COUPON_CODE_WILL_BE_APPLIED_TO_CART, $coupon_code);
        }

        $_SESSION[$key][$coupon_code] = $coupon_code;
        return true;
    }

    /**
     * @param nc_netshop_promotion_coupon $coupon
     * @param nc_netshop_item_collection $cart_contents
     * @return bool
     */
    protected function coupon_can_be_applied_to_cart(nc_netshop_promotion_coupon $coupon, nc_netshop_item_collection $cart_contents) {
        if (!$this->are_coupons_enabled) { return false; }

        if (!sizeof($cart_contents)) {
            return false;
        }

        $deal = $coupon->get_deal();

        $context = clone $this->get_context();
        $context->set_cart_contents($cart_contents);

        if ($coupon->get('deal_type') == 'discount_item') {
            /* @var nc_netshop_promotion_discount_item $deal */
            foreach ($cart_contents as $item) {
                if ($deal->applies_to($item, $context)) {
                    return true;
                }
            }
            return false;
        }
        else {
            /** @var nc_netshop_promotion_discount $deal */
            return $deal->evaluate_conditions($context);
        }

    }

    /**
     * Удаляет купон из пользовательской сессии
     * @param $coupon_code
     */
    public function unregister_coupon_code($coupon_code) {
        if (!$this->are_coupons_enabled) { return; }

        unset($_SESSION[$this->get_coupon_session_key()][$coupon_code]);
        $this->get_coupon_notifications()->add('info', NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_IS_REMOVED_FROM_SESSION, $coupon_code);
        $this->registered_coupons = null; // clear cache
    }

    /**
     * @return string[]
     */
    protected function get_registered_coupon_codes() {
        return (array)$_SESSION[$this->get_coupon_session_key()];
    }

    /**
     * Возвращает объект nc_netshop_promotion_coupon по коду купона (или NULL,
     * если купон с таким кодом не найден).
     * @param $coupon_code
     * @return nc_netshop_promotion_coupon|null
     */
    public function get_coupon($coupon_code) {
        if (!$this->are_coupons_enabled) { return null; }
        $coupon_code = nc_netshop_promotion_coupon::sanitize_code($coupon_code);

        if (!array_key_exists($coupon_code, $this->coupon_cache)) {
            $coupon = new nc_netshop_promotion_coupon();
            if (!$coupon->load_by_code($this->get_catalogue_id(), $coupon_code)) {
                $coupon = null;
            }
            $this->coupon_cache[$coupon_code] = $coupon;
        }
        return $this->coupon_cache[$coupon_code];
    }

    /**
     * Возвращает список купонов, привязанных к пользовательской сессии
     * @return nc_netshop_promotion_coupon_collection|null
     */
    public function get_registered_coupons() {
        if (!$this->are_coupons_enabled) { return null; }

        if (!$this->registered_coupons) {
            $codes = $this->get_registered_coupon_codes(); // those should be only valid coupon codes
            $this->registered_coupons = new nc_netshop_promotion_coupon_collection();
            foreach ($codes as $code) {
                $coupon = $this->get_coupon($code);
                if ($coupon) {
                    /** @todo double-check: num_usages, expiration date */
                    $this->registered_coupons->add($this->get_coupon($code));
                }
            }
        }
        return $this->registered_coupons;
    }

    /**
     * Возвращает сообщения, связанные с активацией купонов
     * (To use on the cart page)
     * @return nc_netshop_promotion_coupon_notifications|null
     */
    public function get_coupon_notifications() {
        if (!$this->are_coupons_enabled) { return null; }

        if (!$this->coupon_notifications) {
            $this->coupon_notifications = new nc_netshop_promotion_coupon_notifications();
        }
        return $this->coupon_notifications;
    }

    /**
     * Записывает информацию об использовании привязанных к сессии пользователя
     * купонов и удаляет их из текущей сессии.
     * (Используется при оформлении заказа)
     */
    protected function use_registered_coupons() {
        if (!$this->are_coupons_enabled) { return; }

        $cart_contents = $this->get_cart_contents();
        /** @var nc_netshop_promotion_coupon $coupon */
        foreach ($this->get_registered_coupons() as $coupon) {
            if ($this->coupon_can_be_applied_to_cart($coupon, $cart_contents)) {
                $coupon->register_usage();
            }
            $this->unregister_coupon_code($coupon->get('code'));
        }
    }

}