<?php


/**
 *
 * Обязательные входные (get/post) параметры:
 * — action: list|edit|save|delete|toggle
 * — discount_type: item|delivery|...
 *
 * Прочие параметры:
 * — discount_id — при редактировании, сохранении
 * — catalogue_id — при добавлении скидки
 * — discount_ids — при удалении
 *
 *
 * Серая магия:
 * — К view автоматически добавляются переменные:
 *      catalogue_id
 *      discount_type = item|delivery|...
 *      discount_class = nc_netshop_promotion_discount_item|...
 *      discount = (instance of nc_netshop_promotion_discount) только для action_edit
 *      promo_link_prefix = "/netcat/admin/#module.netshop.promotion"
 *
 */
class nc_netshop_promotion_discount_admin_controller extends nc_netshop_admin_controller {

    /** @var  nc_netshop_settings_admin_ui */
    protected $ui_config;

    protected $site_id;
    protected $discount_class;
    protected $discount_type;
    protected $discount_id;

    /** @var nc_netshop_promotion_discount */
    protected $discount;

    /**
     *
     */
    protected function init() {
        parent::init();
//        $this->bind('remove', array('discount_type', 'discount_id'));
    }

    /**
     * @param string $view
     * @param array $data
     * @return nc_ui_view
     */
    protected function view($view, $data = array()) {
        $view = parent::view($view, $data)
                ->with('discount_class', $this->discount_class)
                ->with('discount_type', $this->discount_type)
                ->with('promo_link_prefix', nc_core('ADMIN_PATH') . '#module.netshop.promotion');

        return $view;
    }

    /**
     *
     */
    protected function redirect_to_index_action($action = 'index', $params = '') {
        $params = $params . '&discount_type=' . $this->discount_type;
        parent::redirect_to_index_action($action, $params);
    }

    /**
     *
     */
    protected function before_action() {
        $this->discount_type = $type = $this->input->fetch_post_get('discount_type');
        $this->discount_id = $id = (int)$this->input->fetch_post_get('discount_id');
        $this->discount_class = $class = "nc_netshop_promotion_discount_$type";

        // INCORRECT DISCOUNT TYPE: must be [a-z] string
        if (!ctype_alpha($type)) {
            echo '<div>Incorrect discount_type parameter</div>';
            return false;
        }

        if ($id) {
            try {
                $this->discount = new $class($id);
                $this->site_id = $this->discount->get('catalogue_id');
            }
            catch (Exception $e) {
                echo '<div>Wrong discount ID</div>';
                return false;
            }
        }

        $this->ui_config = new nc_netshop_promotion_admin_discount_ui(
            "promotion.discount.$type",
            constant("NETCAT_MODULE_NETSHOP_PROMOTION_" . strtoupper($type) . "_DISCOUNTS")
        );

        return true;
    }


    /**
     *
     */
    protected function action_index() {
        if ($this->discount_type != 'cart' && !$this->netshop->is_feature_enabled("promotion_discount_" . $this->discount_type)) {
            return $this->show_dummy_feature_page('promotion_' . $this->discount_type);
        }

        $add_link = "promotion.discount.{$this->discount_type}.add({$this->site_id})";
        $this->ui_config->add_create_button($add_link);
        $this->ui_config->locationHash .= "($this->site_id)";

        $discounts = $this->get_discount_list();

        if ($discounts) {
            $view = $this->view('discount_list')
                         ->with('discounts', $discounts);
        }
        else {
            $message = constant("NETCAT_MODULE_NETSHOP_PROMOTION_NO_" . strtoupper($this->discount_type) . "_DISCOUNTS");
            $view = $this->view('empty_list')->with('message', $message);
        }

        return $view;
    }

    protected function action_edit() {
        $discount = $this->discount ? $this->discount : new $this->discount_class($this->discount_id);

        $netshop = nc_netshop::get_instance($this->site_id);
        $currency_name = strip_tags($netshop->get_setting('CurrencyDetails',
                                                $netshop->get_setting('DefaultCurrencyID'),
                                               'NameShort'));

        if ($discount->get_id()) {
            $db = nc_db();
            $query = "SELECT COUNT(*)
                               FROM `Netshop_Coupon`
                              WHERE `Deal_Type` = '" . $db->escape('discount_' . $this->discount_type) . "'
                                AND `Deal_ID` = " . (int)$discount->get_id() . "";
            $total_coupons = $db->get_var($query);

            $active_coupons = $db->get_var(
                "$query
                   AND `Enabled` = 1
                   AND (`MaxUsages` = 0 OR (`UsageCount` < `MaxUsages`))
                   AND (`ValidTill` IS NULL OR `ValidTill` > NOW())");
        }
        else {
            $total_coupons = 0;
            $active_coupons = 0;
        }

        $view = $this->view('discount_edit')
                     ->with('discount', $discount)
                     ->with('currency_name', $currency_name)
                     ->with('total_coupons', $total_coupons)
                     ->with('active_coupons', $active_coupons);

        $this->ui_config->add_save_and_cancel_buttons();
        $this->ui_config->locationHash .=
            ($this->discount_id
                ? ".edit({$this->discount_id})"
                : ".add({$this->site_id})"
            );

        return $view;
    }

    /**
     *
     */
    protected function action_save() {
        $data = (array)$this->input->fetch_post('data');
        /** @var nc_netshop_promotion_discount $discount */
        $discount = new $this->discount_class($data);
        try {
            $discount->save();
            if ($discount->get('coupon_required') && $this->input->fetch_post('generate_coupons')) {
                $discount_index_path = $this->get_script_path() .
                    "index&discount_type={$this->discount_type}&catalogue_id={$this->site_id}";

                ob_end_clean();
                header("Location: " .
                    nc_module_path('netshop') . 'admin/promotion/coupon.php?action=generate_ask' .
                    '&deal_type=' . $discount->get_deal_type() .
                    '&deal_id=' . $discount->get_id() .
                    '&redirect_url=' . urlencode($discount_index_path)
                );

                die;
            }
            else {
                $this->redirect_to_index_action();
            }
            return true;
        }
        catch (nc_record_exception $e) {
            $view = $this->view('error_message');
            $view->message = NETCAT_MODULE_NETSHOP_UNABLE_TO_SAVE_RECORD;
            return $view;
        }
    }

    /**
     *
     */
    protected function action_remove() {
        $id = (int)$this->input->fetch_post('discount_id');
        try {
            /** @var nc_netshop_promotion_deal $deal */
            $deal = new $this->discount_class($id);
            $deal->delete();
        }
        catch (Exception $e) {}

        $this->redirect_to_index_action();
    }

    /**
     * 
     */
    protected function action_toggle() {
        if ($this->discount) { // @see $this->before_action()
            $enabled = $this->input->fetch_post('enable');
            $this->discount->set('enabled', $enabled)->save();
        }
        $this->redirect_to_index_action();
    }

    /**
     *
     */
    protected function get_discount_list() {
        /** @var nc_netshop_promotion_discount $dummy_discount */
        $dummy_discount = new $this->discount_class;
        $discount_table = $dummy_discount->get_table_name();

        return $discounts = nc_db()->get_results("
            SELECT d.*, COUNT(c.Coupon_ID) AS 'coupon_count'
              FROM `$discount_table` AS d
                   LEFT JOIN `Netshop_Coupon` AS c
                   ON (
                       c.`Deal_Type`='discount_{$this->discount_type}'
                       AND d.`Discount_ID` = c.Deal_ID
                       # coupon validity:
                       AND c.`Enabled` = 1
                       AND (c.`MaxUsages` = 0 OR (c.`UsageCount` < c.`MaxUsages`))
                       AND (c.`ValidTill` IS NULL OR c.`ValidTill` > NOW())
                   )
             WHERE d.`Catalogue_ID` = $this->site_id
             GROUP BY d.`Discount_ID`
             ORDER BY d.`Name`",
            ARRAY_A);

    }


}