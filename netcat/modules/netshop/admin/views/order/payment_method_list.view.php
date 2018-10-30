<div class="nc-netshop-order-payment-list">
<?php

if (!class_exists('nc_core')) {
    die;
}

/** @var nc_netshop_record_conditional_collection $payment_methods */
/** @var nc_netshop_order $order */
/** @var nc_netshop $netshop */

if (!count($payment_methods)) {
    ?>
    <div class="nc-alert nc--red">
        <i class="nc-icon-l nc--status-error"></i>
        <?= NETCAT_MODULE_NETSHOP_CHECKOUT_NO_AVAILABLE_PAYMENT_METHODS_ADMIN ?>
    </div>
    <?php
}

$payment_method_id = $order->get('PaymentMethod');
if (!$payment_methods->any('id', $payment_method_id)) {
    $payment_method_id = null;
}

$is_first = true;

/** @var nc_netshop_payment_method $method */
foreach ($payment_methods as $method) {
    $method_id = $method->get_id();
    $is_checked = $payment_method_id ? $payment_method_id == $method_id : $is_first;
    $is_first = false;
    $extra_cost = $method->get_extra_cost($order);

    ?>
    <div class="nc-netshop-order-payment-method">
        <label>
            <input type="radio"
                   name="f_PaymentMethod"
                   value="<?= $method_id ?>"
                   data-payment-cost="<?= $extra_cost ?: 0 ?>"
                   <?= ($is_checked ? " checked" : "") ?>
                   />
            <span class="nc-netshop-order-payment-method-name"><?= $method->get('name'); ?></span>

            <?php if ($extra_cost): ?>
                <div class="nc-netshop-order-payment-method-extra-cost">
                    <?= NETCAT_MODULE_NETSHOP_CHECKOUT_PAYMENT_EXTRA_CHARGE ?>
                    <?= $netshop->format_price($extra_cost) ?>
                </div>
            <?php endif; ?>
        </label>
    </div>
<?php
}
?>
<script>
(function() {
    $nc('.nc-netshop-order-payment-list :radio').click(function() {
        var radio = $nc(this);
        radio.closest('form').find('input[name=f_PaymentCost]').val(radio.data('payment-cost')).trigger('input');
    });
    $nc('.nc-netshop-order-payment-list :radio:checked').click();
})();
</script>

<style>
.nc-netshop-order-payment-list .nc-netshop-order-payment-method {
    margin: 5px 0;
}
.nc-netshop-order-payment-list .nc-netshop-order-payment-method-name {
    font-weight: bold;
}
.nc-netshop-order-payment-list .nc-netshop-order-payment-method-extra-cost {
    margin-left: 25px;
}
</style>

</div>
