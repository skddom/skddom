<?php if (!class_exists('nc_core')) { die; } ?>

<?= $this->include_view('../form_with_condition', get_defined_vars()) ?>

<script type='text/javascript'>
(function() {
    // управление дополнительными полями для автоматического расчёта доставки
    var delivery_service_select = $nc('SELECT[name="data[ShopDeliveryService_ID]"]'),
        delivery_mapping_field = $nc('INPUT[name="data[ShopDeliveryService_Mapping]"]');

    // restore mapping
    var delivery_service = delivery_service_select.val();
    if (delivery_service) {
        var mapping = delivery_mapping_field.val(),
            mapped_fields = null;

        try {
            mapped_fields = JSON.parse(mapping);
        } catch (e) {
        }

        if (mapped_fields) {
            for (var field in mapped_fields) {
                $nc('SELECT[name=delivery_service_' + delivery_service + '_field_' + field + ']').val(mapped_fields[field]);
            }
        }
    }

    var save_mapping = function() {
        var delivery_service = delivery_service_select.val(),
            mapping = '';

        if (delivery_service) {
            mapping = {};
            var mapped_fields = $nc('SELECT[name^=delivery_service_' + delivery_service + '_field_]');
            mapped_fields.each(function(){
                var el = $nc(this),
                    match = /^delivery_service_(.+?)_field_(.+)/.exec(el.attr('name'));
                if (match) {
                    mapping[match[2]] = el.val();
                }
                return true;
            });

            if (!$nc.isEmptyObject(mapping)) {
                mapping = JSON.stringify(mapping);
            }
            else {
                mapping = '';
            }
        }

        delivery_mapping_field.val(mapping);

        return true;
    };

    $nc('SELECT[name^=delivery_service_]').change(save_mapping);

    delivery_service_select.on('change', function(){
        var service = $nc(this).val();
        $nc('.nc-netshop-delivery-service').hide().filter('.nc-netshop-delivery-service--' + service).show();
        save_mapping();
        return true;
    }).change();

    // При выборе автоматического расчёта доставки прячем выбор типа доставки —
    // его должен устанавливать соответствующий класс nc_netshop_delivery_service
    var delivery_type_select = $nc('select[name="data[DeliveryType]"]'),
        delivery_type_row = delivery_type_select.closest('.nc-field'),
        delivery_point_group_select = $nc('select[name="data[DeliveryPointGroup]"]'),
        delivery_point_group_row = delivery_point_group_select.closest('.nc-field'),
        delivery_service_types = <?= json_encode($delivery_service_types) ?>,
        pickup = '<?= nc_netshop_delivery::DELIVERY_TYPE_PICKUP ?>';

    delivery_service_select.change(function() {
        var value = $nc(this).val();
        // если выбран автоматический расчёт, тип доставки определяет соответствующий класс
        delivery_type_row.toggle(value == 0);
        // если выбран класс расчёта с типом доставки «до пункта выдачи», даём выбрать группу пунктов выдачи
        if (value) {
            delivery_point_group_row.toggle(delivery_service_types[value] == pickup);
        }
    }).change();

    delivery_type_select.on('change', function() {
        delivery_point_group_row.toggle(this.value == pickup);
    }).change();

})();
</script>
