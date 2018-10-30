<?php

if (!class_exists('nc_core')) { die; }

/** @var nc_netshop_promotion_discount $discount */
/** @var int $discount_id */
/** @var nc_netshop $netshop */

$form = $ui->form("?controller=$controller_name&action=save&discount_type=$discount_type")->vertical();
$form->add()->input('hidden', 'catalogue_id', $catalogue_id);
$form->add()->input('hidden', 'action', 'save');
$form->add()->input('hidden', 'data[discount_id]', ($discount_id ? $discount_id : ''));
$form->add()->input('hidden', 'data[catalogue_id]', $catalogue_id);

$form->add_row(NETCAT_MODULE_NETSHOP_NAME_FIELD)
     ->string('data[name]', $discount->get('name'))
     ->xlarge();

$form->add_row(NETCAT_MODULE_NETSHOP_DESCRIPTION_FIELD)
     ->textarea('data[description]', $discount->get('description'))
     ->class_name('no_cm', true) /* @todo HTML editor here */
     ->xlarge();

$row = $form->add_row(NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_VALUE)->horizontal();
$row->string('data[amount]', $discount->get_formatted_amount())->small();
$row->span(' ');
$row->select('data[amount_type]', array(
            nc_netshop_promotion_discount::TYPE_ABSOLUTE => $currency_name,
            nc_netshop_promotion_discount::TYPE_RELATIVE => '%'
        ), $discount->get('amount_type'));


$row = $form->add_row();
$row->input('hidden', 'data[enabled]', '0');
$row->checkbox('data[enabled]',
               $discount->get('enabled'),
               NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_IS_ENABLED)
    ->value('1');

if ($netshop->is_feature_enabled('promotion_cumulative_discounts')) {
    $row = $form->add_row();
    $row->input('hidden', 'data[cumulative]', '0');
    $row->checkbox('data[cumulative]',
                   $discount->get('cumulative'),
                   NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_IS_CUMULATIVE)
        ->value('1');
}

$row = $form->add_row(NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_CONDITIONS)->div('')->id('nc_netshop_condition_editor');

$condition_json = $discount->get('condition');
if (!$condition_json ) { $condition_json = "{}"; }

if ($discount_type == 'item') {
    $row = $form->add_row();
    $row->input('hidden', 'data[item_activation_required]', '0');
    $row->checkbox('data[item_activation_required]',
                   $discount->get('item_activation_required'),
                   NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_REQUIRES_ITEM_ACTIVATION)
        ->value('1')->class_name('item-activation');
}

if ($netshop->is_feature_enabled('promotion_coupon')) {
    $row = $form->add_row();
    $row->input('hidden', 'data[coupon_required]', '0');
    $row->checkbox('data[coupon_required]',
                   $discount->get('coupon_required'),
                   NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_REQUIRES_COUPON_CODE)
        ->value('1')->class_name('coupon-activation');

    if ($total_coupons) {
        $coupon_list_link =
            $ui->helper->hash_link(
                "module.netshop.promotion.coupon(discount_$discount_type,$discount_id)",
                $total_coupons,
                '',
                '_blank');

        $coupons_actions =
            sprintf(NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_NUMBER_OF_COUPONS, $coupon_list_link, $active_coupons) .
            '. ' .
            $ui->helper->hash_link(
                "module.netshop.promotion.coupon.generate(discount_$discount_type,$discount_id)",
                NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_GENERATE_COUPONS,
                '',
                '_blank');

    }
    else if ($discount->get('coupon_required')) {
        $coupons_actions =
            $ui->helper->hash_link(
                "module.netshop.promotion.coupon.generate(discount_$discount_type,$discount_id)",
                NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_GENERATE_COUPONS,
                '',
                '_blank');
    }
    else {
        $coupons_actions =
            '<label><input type="checkbox" name="generate_coupons" value="1" checked> ' .
            NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_CREATE_COUPONS_AFTER_SAVING .
            '</label>';
    }

    $row->div($coupons_actions)
        ->attr('style', 'margin: 5px 24px; ' . ($discount->get('coupon_required') ? '' : 'display: none'))
        ->class_name('coupon-actions');
}

nc_netshop_condition_admin_helpers::include_condition_editor_js();
echo $form;

$condition_groups_to_exclude = array();
$conditions_to_exclude = array();

if ($discount_type != 'item') {
    $condition_groups_to_exclude[] = 'GROUP_GOODS';
}

if ($discount_type == 'item') {
    $conditions_to_exclude[] = 'cart_totalprice';
}

$mode = 'null';
if ($discount_type == 'cart' && !$netshop->is_feature_enabled('advanced_conditions')) {
    $mode = "'cart_totalprice'";
}

?>

<script>
(function() {
    var condition_editor = new nc_netshop_condition_editor({
        container: '#nc_netshop_condition_editor',
        input_name: 'data[condition]',
        conditions: <?=$condition_json ?>,
        site_id: <?=$catalogue_id ?>,
        groups_to_exclude: <?=nc_array_json($condition_groups_to_exclude) ?>,
        conditions_to_exclude: <?=nc_array_json($conditions_to_exclude) ?>,
        mode: <?= $mode ?>
    });

    $nc('#nc_netshop_condition_editor').closest('form').get(0).onsubmit = function() {
        return condition_editor.onFormSubmit();
    };

    var itemCb = $nc('input.item-activation:checkbox'),
        couponCb = $nc('input.coupon-activation:checkbox'),

        toggleCouponActions = function() {
            var actionDiv = $nc('.coupon-actions');
            if (couponCb.prop('checked')) {
                actionDiv.slideDown(300, function() {
                    var w = $nc(window),
                        desiredScrollTop = 20 + actionDiv.position().top + actionDiv.outerHeight() - w.height();
                    if (desiredScrollTop > w.scrollTop()) {
                        $nc('body, html').animate({scrollTop: desiredScrollTop}, 300);
                    }
                });
            }
            else {
                actionDiv.slideUp();
            }
        };

    couponCb.change(function() {
        couponCb.prop('checked') && itemCb.prop('checked', false);
        toggleCouponActions();
    });

    itemCb.change(function() {
        itemCb.prop('checked') && couponCb.prop('checked', false);
        toggleCouponActions();
    });

    $nc(':checkbox').parents('label').css('display', 'inline-block');

})();
</script>
