<?php

if (!class_exists('nc_core')) { die; }

/** @var nc_netshop $netshop */
/** @var nc_ui $ui */
/** @var array $discounts */
/** @var string $discount_class */
/** @var string $promo_link_prefix */
/** @var string $discount_type */

echo $ui->controls->site_select($catalogue_id);

$table = $ui->table()->wide()->striped()->bordered()->hovered();

$coupons_header = $netshop->is_feature_enabled('promotion_coupon') ? NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_COUPONS : '';

$thead = $table->thead(); // chaining produces invalid code
$thead->th()->compact();
$thead->th(NETCAT_MODULE_NETSHOP_NAME_AND_CONDITIONS_HEADER);
$thead->th(NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_AMOUNT);
$thead->th($coupons_header)->text_center();
//$thead->th(NETCAT_MODULE_NETSHOP_LIST_ACTIONS_HEADER)->text_center();
$thead->th()->compact();
$thead->th()->compact();

$tr = $table->row();
$tr->enabled = $tr->td();
$tr->name = $tr->td();
$tr->value = $tr->td();
$tr->coupons = $tr->td();
$tr->actions = $tr->td()->text_center();
$action_edit = $ui->html->a()->title(NETCAT_MODULE_NETSHOP_ACTION_EDIT)->icon('edit');
$tr->delete_button = $tr->td()->text_center();

foreach ($discounts as $row) {
    /** @var nc_netshop_promotion_discount $discount */
    $discount = new $discount_class;
    $discount->set_values_from_database_result($row);
    $discount_id = $discount->get_id();
    $edit_link = "$promo_link_prefix.discount.$discount_type.edit($discount_id)";

    $post_actions_params = array('controller' => $controller_name, 'discount_type' => $discount_type, 'discount_id' => $discount_id);

    $tr->enabled->text($ui->controls->toggle_button(
        $discount->get('enabled'),
        $post_actions_params
    ));

    $comment = array();
    if ($discount->get('cumulative')) {
        $comment[] = NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_IS_CUMULATIVE_SHORT;
    }

    // special case:
    if ($discount_type == 'item' && $discount->get('item_activation_required')) {
        $comment[] = NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_REQUIRES_ITEM_ACTIVATION_SHORT;
    }

    $comment = $comment ? ' (' . join(', ', $comment) . ')' : '';

    $tr->name->text(
        "<a href='$edit_link' target='_top' class='nc-netshop-list-item-title'>" . $discount->get('name') . "</a>" .
        $comment .
        "<div class='nc-netshop-list-condition-info'>" . $discount->get_condition_description() . "</div>"
    );
    $tr->value->text($discount->get_full_formatted_amount());

    if ($discount->get('coupon_required') || $row['coupon_count']) {
        $coupon_link = "$promo_link_prefix.coupon(discount_{$discount_type},{$discount->get_id()})";
        $coupon_label_color = ($discount->get('coupon_required') ? 'blue' : 'grey');
        $tr->coupons->reset()->text_center()
           ->span(nc_ui_label::get($row['coupon_count'])
                     ->href($coupon_link)->attr('target', '_top')
                     ->$coupon_label_color());
    }
    else {
        $tr->coupons->reset();
    }

//    $tr->delete_box = $tr->td()->text_center()->checkbox('discount_ids[]');

    $tr->actions->text($action_edit->href($edit_link)->attr('target', '_top'));

    $tr->delete_button->text($ui->controls->delete_button(
        sprintf(NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_CONFIRM_DELETE, $discount->get('name')),
        $post_actions_params
    ));

    $table->add_row($tr);
}


echo $table, "<br>";
