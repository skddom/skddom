<?php if (!class_exists('nc_core')) { die; } ?>

<?php
/** @var nc_netshop_delivery_point_local $point **/
/** @var int $site_id */
?>

<form method="post" class="nc-form" onsubmit="return nc_netshop_delivery_point_form_submit()">
    <input type="hidden" name="controller" value="delivery_point">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="site_id" value="<?= $site_id ?>">

    <?php

    $fields = array(
        'id' => array(
            'type' => 'hidden',
        ),
        'catalogue_id' => array(
            'type' => 'hidden',
            'default_value' => $site_id,
        ),
        'name' => array(
            'caption' => NETCAT_MODULE_NETSHOP_NAME_FIELD . ' (*)',
            'type' => 'string',
        ),
        'description' => array(
            'caption' => NETCAT_MODULE_NETSHOP_DESCRIPTION_FIELD,
            'type' => 'textarea',
            'codemirror' => false,
        ),
        'phones' => array(
            'caption' => NETCAT_MODULE_NETSHOP_PHONE,
            'type' => 'string',
        ),
        'location_name' => array(
            'caption' => NETCAT_MODULE_NETSHOP_DELIVERY_POINT_LOCATION_NAME . ' (*)',
            'type' => 'string',
        ),
        'address' => array(
            'caption' => NETCAT_MODULE_NETSHOP_DELIVERY_POINT_ADDRESS,
            'type' => 'string',
        ),
        'latitude' => array(
            'type' => 'hidden',
        ),
        'longitude' => array(
            'type' => 'hidden',
        ),
        'map' => array(
            'type' => 'custom',
            'html' => '<div id="nc_netshop_delivery_point_map"></div>',
        ),
        'schedule' => array(
            'type' => 'custom',
            'html'=>
                '<div class="nc-field">' .
                '<div class="nc-field-caption">' . NETCAT_MODULE_NETSHOP_DELIVERY_POINT_SCHEDULE . ':</div>' .
                '<div>' .
                $this->include_view('schedule')->with('schedule', $point->get_schedule()) .
                '</div>'.
                '</div>',
        ),
        'payment_on_delivery_cash' => array(
            'caption' => NETCAT_MODULE_NETSHOP_DELIVERY_PAYMENT_CASH,
            'type' => 'checkbox',
            'value_for_off' => 0,
            'value_for_on' => 1,
        ),
        'payment_on_delivery_card' => array(
            'caption' => NETCAT_MODULE_NETSHOP_DELIVERY_PAYMENT_CARD,
            'type' => 'checkbox',
            'value_for_off' => 0,
            'value_for_on' => 1,
        ),
        'group' => array(
            'caption' => NETCAT_MODULE_NETSHOP_DELIVERY_POINT_GROUP,
            'type' => 'string',
        ),
    );

    $form = new nc_a2f($fields, 'point');
    $form->set_field_defaults('string', array('size' => 64))
         ->show_default_values(false)
         ->show_header(false)
         ->set_values($point);

    echo $form->render(
        false,
        array(
            'checkbox' => '<div class="nc-field %CLASS"><label>%VALUE %CAPTION</label></div>',
            'default' => '<div class="nc-field %CLASS"><span class="nc-field-caption">%CAPTION:</span>%VALUE</div>',
        ),
        false,
        false
    );

    ?>
</form>

<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
<script>
function nc_netshop_delivery_point_form_submit() {
    var result = true;
    $nc('input[name="point[name]"], input[name="point[location_name]"]').each(function() {
        var input = $nc(this);
        if (!input.val().trim().length) {
            var field_caption = input.closest('.nc-field').find('.nc-field-caption, label').text()
                    .replace(/[:()*]/g, '').trim(),
                message = '<?= NETCAT_MODERATION_MSG_ONE ?>'.replace('%NAME', field_caption);
            alert(message);
            input.focus();
            result = false;
            return false;
        }
    });
    return result;
}

ymaps.ready(function() {
    var center = [
            <?= sprintf('%0.15F', $point->get('latitude')  ?: 55.751461877262) ?>,
            <?= sprintf('%0.15F', $point->get('longitude') ?: 37.618929550785) ?>
        ],
        map = new ymaps.Map('nc_netshop_delivery_point_map', {
            center: center,
            zoom: 16,
            controls: [ 'searchControl', 'zoomControl', 'typeSelector' ]
        }),
        placemark = new ymaps.Placemark(
            center,
            { hintContent: <?= nc_array_json(NETCAT_MODULE_NETSHOP_DELIVERY_POINT_DRAG) ?> },
            { draggable: true }
        );

    map.geoObjects.add(placemark);

    // перенос адреса в карту
    var city_input = $nc('input[name="point[location_name]"]'),
        address_input = $nc('input[name="point[address]"]');

    function set_map_address() {
        $nc('ymaps input[class*=searchbox]').val(city_input.val() + ' ' + address_input.val());
    }

    city_input.on('keyup', set_map_address);
    address_input.on('keyup', set_map_address);
    set_map_address();

    var map_search = map.controls.get('searchControl');
    map_search.options.set({ noPlacemark: true });
    map_search.events.add('resultselect', function(result) {
        map_search.getResult(result.get('index')).then(function(point) {
            placemark.geometry.setCoordinates(point.geometry.getCoordinates());
            update_coordinates();
        });
    });

    // сохранение координат
    var lat_input = $nc('input[name="point[latitude]"]'),
        lon_input = $nc('input[name="point[longitude]"]');

    function update_coordinates() {
        var coord = placemark.geometry.getCoordinates();
        lat_input.val(coord[0]);
        lon_input.val(coord[1]);
    }

    placemark.events.add('dragend', update_coordinates);

});
</script>