<?php

class nc_netshop_promotion_discount_cart_collection extends nc_netshop_promotion_discount_collection {

    /** @var nc_netshop_promotion_discount_cart[] */
    protected $items = array();

    /** @var string  */
    protected $items_class = 'nc_netshop_promotion_discount_cart';

    /** @var  string */
    protected $deal_type = 'discount_cart';

    /** @var string */
    protected $discounts_table_name = "Netshop_CartDiscount";

    /**
     * Возвращает сумму скидки на корзину
     *
     * @return float|int
     */
    public function get_discount_sum() {
        $cumulative_sum = 0;
        $exclusive_sum = 0;

        foreach ($this->items as $discount) {
            $discount_value = $discount->get_discount_sum($this->context);
            if ($discount->get('cumulative')) {
                $cumulative_sum += $discount_value;
            }
            else {
                $exclusive_sum = max($exclusive_sum, $discount_value);
            }
        } // of "foreach delivery discount"

        $discount_sum = $cumulative_sum + $exclusive_sum;

        $cart_sum = $this->context->get_cart_contents()->sum('TotalPrice');
        if ($discount_sum > $cart_sum) { $discount_sum = $cart_sum; }

        return $discount_sum;
    }

    /**
     * Выбирает скидки, применимые к корзине. Если есть несколько конкурирующих
     * некумулятивных скидок, выбирает одну с максимальной абсолютной суммой скидки.
     *
     * @return nc_netshop_promotion_discount_cart_collection
     */
    public function get_applicable_discounts() {
        $result = $this->make_new_collection();

        $max_exclusive_discount = null;
        $max_exclusive_discount_value = 0;
        $cumulative_discounts_sum = 0;

        $cart_totals = $this->context->get_cart_contents()->sum('TotalPrice');

        foreach ($this->items as $discount) {
            if (!$discount->evaluate_conditions($this->context)) {
                continue; // next, please!
            }

            $discount_value = $discount->get_discount_sum($this->context);

            if ($discount->get('cumulative') && nc_netshop::get_instance($this->context->get_catalogue_id())->is_feature_enabled('promotion_cumulative_discounts')) {
                $cumulative_discounts_sum += $discount_value;
                $result->add($discount);
            }
            else { // exclusive discount
                if ($discount_value > $max_exclusive_discount_value) {
                    $max_exclusive_discount_value = $discount_value;
                    $max_exclusive_discount = $discount;
                }
            }

            // stop when discounts are equal or more than the original price
            if ($max_exclusive_discount_value + $cumulative_discounts_sum >= $cart_totals) {
                break;
            }
        } // of "foreach discount"

        if ($max_exclusive_discount) { $result->add($max_exclusive_discount); }

        return $result;
    }

}