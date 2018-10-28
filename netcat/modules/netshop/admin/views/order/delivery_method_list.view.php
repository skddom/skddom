<div class="nc-netshop-order-delivery-list">
<?php

if (!class_exists('nc_core')) {
    die;
}

$nc_core = nc_core::get_object();

/** @var nc_netshop_delivery_method_collection $delivery_methods */
/** @var nc_netshop_order $order */
/** @var string $delivery_variant_id */
/** @var string $delivery_point_id */

if (!count($delivery_methods)) {
    ?>
    <div class="nc-alert nc--red">
        <i class="nc-icon-l nc--status-error"></i>
        <?= NETCAT_MODULE_NETSHOP_CHECKOUT_NO_AVAILABLE_DELIVERY_METHODS ?>
    </div>
    <?php
}

// --- НАЧАЛО ШАБЛОНА ВЫВОДА СПОСОБА ДОСТАВКИ ---
$print_delivery_method = function (nc_netshop_delivery_method $method, $options_block = '') use ($order, $delivery_variant_id) {
    static $is_first = true; // флаг для пред-выбора первого элемента

    $method_id = $method->get_id();
    $is_checked = !empty($delivery_variant_id) ? $delivery_variant_id == $method_id : $is_first;
    $estimate = $method->get_estimate($order);
    
    $is_first = false;

    ?>
    <div class="nc-netshop-order-delivery-method">
        <label>
            <div class="nc-netshop-order-delivery-method-header">
                <input type="radio"
                       name="delivery_variant_id"
                       value="<?= $method_id ?>"
                       data-delivery-cost="<?= $estimate->get('price') ?: 0 ?>"
                       <?= ($is_checked ? 'checked' : '') ?>
                       class="nc-netshop-order-delivery-method-radio" />

                <span class="nc-netshop-order-delivery-method-name">
                    <?= $method->get('name'); ?>
                </span>

                <span class="nc-netshop-order-delivery-method-estimate">
                <?php if ($estimate->has_error()): ?>
                    <div class="nc-alert nc--red">
                        <i class="nc-icon-l nc--status-error"></i>
                        <?= NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_ESTIMATE_ERROR . ": " . $estimate->get('error') ?>
                    </div>
                <?php else: ?>
                    <span class="nc-netshop-order-delivery-estimate-price">
                        <?= $estimate->get_formatted_price_and_discount() ?>
                    </span>
                    <span class="nc-netshop-order-delivery-estimate-dates">
                        <?= $estimate->get_dates_string() ?>
                    </span>
                <?php endif; ?>
                </span>
            </div>

            <div class="nc-netshop-order-delivery-method-description">
                <?= $method->get('description') ?>
            </div>
        </label>
        <?php if ($options_block): ?>
            <div class="nc-netshop-order-delivery-method-options" style="<?= $is_checked ? '' : 'display: none' ?>">
                <?= $options_block ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
};
// --- КОНЕЦ ШАБЛОНА ВЫВОДА СПОСОБА ДОСТАВКИ ---

// --- БЛОКИ ВЫБОРА СПОСОБА ДОСТАВКИ ---

// 1) ДОСТАВКА КУРЬЕРОМ
/** @var nc_netshop_delivery_method_collection $courier_delivery_methods */
$courier_delivery_methods = $delivery_methods->where('delivery_type', nc_netshop_delivery::DELIVERY_TYPE_COURIER);
if (count($courier_delivery_methods)) {
    // «Доставка курьером по указанному адресу»
    echo "<div class='nc-netshop-order-delivery-type'>", NETCAT_MODULE_NETSHOP_DELIVERY_TYPE_COURIER, "</div>";
    echo "<div class='nc-netshop-order-delivery-type-courier'>";
    /** @var nc_netshop_delivery_method $method */
    foreach ($courier_delivery_methods as $method) {
        $print_delivery_method($method);
    }
    echo "</div>";
}

// 2) ДОСТАВКА ДО ПУНКТА САМОВЫВОЗА
/** @var nc_netshop_delivery_method_collection $pickup_delivery_methods */
$pickup_delivery_methods = $delivery_methods->where('delivery_type', nc_netshop_delivery::DELIVERY_TYPE_PICKUP);
if (count($pickup_delivery_methods)) {
    // «Доставка до пункта выдачи»
    echo "<div class='nc-netshop-order-delivery-type'>",
         NETCAT_MODULE_NETSHOP_DELIVERY_TYPE_PICKUP;
    if ($pickup_delivery_methods->any('has_delivery_points_with_coordinates', true)) {
        echo "<button class='nc-btn nc--light nc-netshop-order-delivery-type-pickup-map-button' type='button'>",
             NETCAT_MODULE_NETSHOP_DELIVERY_ON_MAP,
             "</button>";
    }
    echo "</div>";

    echo "<div class='nc-netshop-order-delivery-type-pickup'>";

    $city = $order->get_location_name();

    // --- КАРТА ---

    // Готовим данные для карты
    $map_settings = array(
        'balloon_select_point_button_text' => NETCAT_MODULE_NETSHOP_DELIVERY_POINT_SELECT_BUTTON,
        'balloon_price_prefix' => NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_ESTIMATE_PRICE,
        'delivery_methods' => array(),
    );

    // На карте может быть показан адрес, введённый на предыдущем этапе
    if ($order->get('Address')) {
        $map_settings['home_address'] = trim($order->get('Zip') . ' ' . $city . ' ' . $order->get('Address'));
    }
    else {
        $map_settings['home_address'] = '';
    }

    /** @var nc_netshop_delivery_method $method */
    foreach ($pickup_delivery_methods as $method) {
        $map_settings['delivery_methods'][] = array(
            'id' => $method->get_id(),
            'name' => $method->get('name'),
            'price' => $netshop->format_price($method->get_estimate($order)->get('price')),
            'points' => $method->get_delivery_points($city)->to_array(true),
        );
    }

    // Подключение и инициализация скриптов
    ?>
    <div class="nc-netshop-order-delivery-type-pickup-map" id="nc_netshop_dm_pickup_map"></div>
    <script>
    (function() {
        function init_map() {
            var click_event = 'click.netshop_order_map';
            var options = <?= nc_array_json($map_settings) ?>;

            // Выбор точки доставки в списке при выборе его на карте
            options.map_point_selection_callback = function (delivery_point_id) {
                var delivery_point_radio = $nc('#nc_netshop_dm_point_' + delivery_point_id),
                    method_div = delivery_point_radio.closest('.nc-netshop-order-delivery-method'),
                    options_div = method_div.find('.nc-netshop-order-delivery-method-options');

                // выбираем службу доставки, раскрываем список пунктов
                method_div.find('.nc-netshop-order-delivery-method-radio').click();
                // выбираем пункт
                delivery_point_radio.prop('checked', true);
                // проверяем, чтобы выбранный пункт было видно
                if (options_div.length && options_div.get(0).scrollHeight > options_div.height()) {
                    options_div.scrollTop(delivery_point_radio.closest('.nc-netshop-order-delivery-point').position().top);
                }
            };

            // Инициализация карты
            var map = new nc_netshop_delivery_points_yandex_map(options);

            // Снятие отметки при выборе другого способа доставки
            $nc(document)
                .off(click_event, '.nc-netshop-order-delivery-method-radio')
                .on(click_event, '.nc-netshop-order-delivery-method-radio', function() {
                    if ($nc(this).closest('.nc-netshop-order-delivery-type-pickup').length === 0) {
                        map.deselect_current_point();
                    }
                });

            // Выбор точки доставке на карте при выбор его в списке
            $nc(document)
                .off(click_event, '.nc-netshop-order-delivery-point-radio')
                .on(click_event, '.nc-netshop-order-delivery-point-radio', function() {
                    map.select_point_by_id(this.value);
                });

            // Кнопка «на карте» — открыть карту на весь экран
            $nc('.nc-netshop-order-delivery-type-pickup-map-button')
                .off(click_event)
                .on(click_event, function() {
                    if (map.is_ready) {
                        map.get_map().container.enterFullscreen();
                        map.center();
                    }
                });
        }

        if (!('nc_netshop_delivery_points_yandex_map' in window)) {
            nc.load_script('<?= nc_component_path($order->get_order_component_id()) . 'js/yandex_map.js' ?>')
              .done(init_map);
        }
        else {
            init_map();
        }
    })();
    </script>
    <?php

    // Список пунктов самовывоза

    /** @var nc_netshop_delivery_method $method */
    foreach ($pickup_delivery_methods as $method) {
        // Список для выбора пунктов доставки
        /** @var nc_netshop_delivery_point_collection $delivery_points */
        $delivery_points = $method->get_delivery_points($city)->sort_by_property_value('address');
        $delivery_points_div = '';
        if (count($delivery_points)) {
            $delivery_points_div = "<div class='nc-netshop-order-delivery-points'>";
            /** @var nc_netshop_delivery_point $delivery_point */
            foreach ($delivery_points as $delivery_point) {
                $id = $delivery_point->get_id();
                $delivery_points_div .=
                    "<div class='nc-netshop-order-delivery-point'><label>" .
                    "<input type='radio' name='delivery_point_id'" .
                    " class='nc-netshop-order-delivery-point-radio' " .
                    " id='nc_netshop_dm_point_{$id}'" .
                    ($id == $delivery_point_id ? ' checked' : '') .
                    " value='{$id}'> " .
                    "<span class='nc-netshop-order-delivery-point-address'>" . $delivery_point->get('address') . "</span> " .
                    "<span class='nc-netshop-order-delivery-point-schedule'>" .
                    $delivery_point->get_schedule()->get_compact_schedule_string() .
                    "</span>" .
                    "</label></div>";
            }
            $delivery_points_div .= "</div>";
        }

        $print_delivery_method($method, $delivery_points_div);
    }
    echo "</div>";
}

// 3) ДОСТАВКА В ПОЧТОВОЕ ОТДЕЛЕНИЕ
/** @var nc_netshop_delivery_method_collection $post_delivery_methods */
$post_delivery_methods = $delivery_methods->where('delivery_type', nc_netshop_delivery::DELIVERY_TYPE_POST);
if (count($post_delivery_methods)) {
    // «Доставка в почтовое отделение»
    echo "<div class='nc-netshop-order-delivery-type'>", NETCAT_MODULE_NETSHOP_DELIVERY_TYPE_POST, "</div>";
    echo "<div class='nc-netshop-order-delivery-type-post'>";
    /** @var nc_netshop_delivery_method $method */
    foreach ($post_delivery_methods as $method) {
        $print_delivery_method($method);
    }
    echo "</div>";
}

?>
<script>
    // показ дополнительных параметров доставки (выбор точки самовывоза) при выборе способа доставки
    (function() {
        var method_radios = $nc('.nc-netshop-order-delivery-method-radio'),
            all_options = $nc('.nc-netshop-order-delivery-method-options');

        method_radios.click(function() {
            var radio = $nc(this),
                method_options = radio.closest('.nc-netshop-order-delivery-method').find('.nc-netshop-order-delivery-method-options');

            // сохранение стоимости в заказе
            radio.closest('form').find('input[name=f_DeliveryCost]').val(radio.data('delivery-cost')).trigger('input');

            if (!method_options.is(':visible')) {
                // показываем опции только для выбранного способа доставки
                all_options.hide();
                method_options.show();
                // предвыбираем первую радиокнопку в списке
                method_options.find(':radio').first().click();
                method_options.scrollTop(0);
            }
            else {
                var delivery_point = method_options.find(':radio:checked').closest('.nc-netshop-order-delivery-point');
                if (delivery_point.length) {
                    method_options.scrollTop(delivery_point.position().top);
                }
            }
        });
        // раскрываем опции для уже выбранного способа
        method_radios.filter(':checked').click();
    })();
</script>
<style>
.nc-netshop-order-delivery-list .nc-netshop-order-delivery-method {
    margin: 5px 0;
}

.nc-netshop-order-delivery-list .nc-netshop-order-delivery-type {
    font-weight: bold;
    margin: 15px 0 5px;
}

.nc-netshop-order-delivery-list .nc-netshop-order-delivery-type button {
    padding: 1px 5px;
    margin-left: 20px;
}

.nc-netshop-order-delivery-list .nc-netshop-order-delivery-method-name {
    display: inline-block;
    width: 400px;
    font-weight: bold;
}

.nc-netshop-order-delivery-list .nc-netshop-order-delivery-estimate-price {
    display: inline-block;
    width: 100px;
}

.nc-netshop-order-delivery-list .nc-netshop-order-delivery-method-description {
    margin-left: 25px;
}

.nc-netshop-order-delivery-list .nc-netshop-order-delivery-method-options {
    margin-left: 20px;
    max-height: 255px;
    overflow-y: auto;
    position: relative;
}

.nc-netshop-order-delivery-list .nc-netshop-order-delivery-point {
    position: relative;
}

.nc-netshop-order-delivery-list .nc-netshop-order-delivery-point-address {
    display: inline-block;
    width: 483px;
}

.nc-netshop-order-delivery-type-pickup-map {
    /* если карту скрыть через display или height:0, могут возникать ошибки с отрисовкой меток при повторном открытии (?) */
    height: 200px;
    width: 200px;
    position: absolute;
    left: -1000px;
}

#simplemodal-container #nc_netshop_dm_pickup_map ymaps {
    box-sizing: content-box !important;
    -moz-box-sizing: content-box !important;
    -webkit-box-sizing: content-box !important;
}

#simplemodal-container #nc_netshop_dm_pickup_map ymaps input, body ymaps input {
    margin: 0 !important;
}

#simplemodal-container #nc_netshop_dm_pickup_map ymaps input:focus, body ymaps input:focus {
    border-color: transparent;
}
</style>
</div>