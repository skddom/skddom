<?php

abstract class nc_netshop_promotion_discount_collection extends nc_netshop_record_conditional_collection {

    /** @var nc_netshop_condition_context  */
    protected $context;

    /** @var nc_netshop_promotion_discount[] */
    protected $items = array();

    /** @var string    MUST BE DEFINED IN THE CHILD CLASS */
    protected $items_class;

    /** @var  string   MUST BE DEFINED IN THE CHILD CLASS */
    protected $deal_type;

    /** @var string    MUST BE DEFINED IN THE CHILD CLASS */
    protected $discounts_table_name;

    /**
     * @param nc_netshop_condition_context $context
     * @return $this
     */
    public function set_context(nc_netshop_condition_context $context) {
        $this->context = $context;
        return $this;
    }

    /**
     * @return nc_netshop_promotion_discount_item_collection
     */
    public function load_all_discounts() {
        $this->load_discounts();

        // add deals from coupons
        $all_coupons = $this->context->get_coupons();
        if ($all_coupons && $all_coupons->count()) {
            $coupons = $all_coupons->where('deal_type', $this->deal_type);
            /** @var nc_netshop_promotion_coupon $coupon */
            foreach ($coupons as $coupon) {
                $deal = $coupon->get_deal();
                if ($deal->get('enabled')) { $this->items[] = $deal; }
            }
        }

        return $this;
    }

    /**
     *
     */
    protected function load_discounts() {
        if (!$this->context) {
            trigger_error("Cannot load discount collection: no context set");
            return $this;
        }

        $query = "SELECT * 
                    FROM `$this->discounts_table_name`
                   WHERE `Catalogue_ID` = " . ((int)$this->context->get_catalogue_id()) .
                   " AND `Enabled` = 1
                     AND `CouponRequired` = 0";


        return parent::select_from_database($query);
    }

    /**
     *
     */
    protected function make_new_collection() {
        $collection = parent::make_new_collection();
        if ($collection instanceof self) {
            $collection->set_context($this->context);
        }
        return $collection;
    }

}