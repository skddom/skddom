<?php if (!class_exists('nc_core')) { die; } ?>

<?= $this->include_view('../form_with_condition', get_defined_vars()) ?>

<script type='text/javascript'>
(function() {
    var payment_from_delivery_checkbox = $nc(':checkbox[name="data[PaymentFromDelivery]"]'),
        payment_system_select = $nc('select[name="data[PaymentSystem_ID]"]'),
        payment_on_delivery_fields = $nc(
            ':checkbox[name="data[PaymentOnDeliveryCash]"],' +
            ':checkbox[name="data[PaymentOnDeliveryCard]"]'
        ).closest('.nc-field');

    function toggle_payment_fields() {
        var hide = payment_from_delivery_checkbox.is(':checked') || payment_system_select.val();
        payment_on_delivery_fields.toggle(!hide);
    }

    payment_from_delivery_checkbox.change(function() {
        payment_system_select.closest('.nc-field').toggle(!this.checked);
        toggle_payment_fields();
    }).change();

    payment_system_select.change(toggle_payment_fields);
})();
</script>
