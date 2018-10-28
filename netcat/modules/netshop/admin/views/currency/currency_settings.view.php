<?php if (!class_exists('nc_core')) { die; } ?>

<?= $ui->controls->site_select($catalogue_id) ?>

<form class='nc-form' method='post'>

<?php

    $rub_id = 1;
    $fields = array(
        // настройки валюты

        'DefaultCurrencyID' => array(
            'caption' => NETCAT_MODULE_NETSHOP_DEFAULT_CURRENCY_ID,
            'type' => 'select',
            'subtype' => 'classificator',
            'classificator' => 'ShopCurrency',
            'required' => true,
            'default_value' => $rub_id,
        ),

        // настройки официальных курсов валют

        'ExternalCurrency' => array(
            'caption' => NETCAT_MODULE_NETSHOP_EXTERNAL_CURRENCY,
            'type' => 'select',
            'subtype' => 'classificator',
            'classificator' => 'ShopCurrency',
            'required' => true,
            'default_value' => $rub_id,
        ),
        'CurrencyConversionPercent' => array(
            'caption' => NETCAT_MODULE_NETSHOP_CURRENCY_CONVERSION_PERCENT,
            'type' => 'float',
            'required' => false,
            'size' => 5,
        ),
        'DaysToKeepCurrencyRates' => array(
            'caption' => NETCAT_MODULE_NETSHOP_DAYS_TO_KEEP_CURRENCY_RATES,
            'type' => 'int',
            'required' => false,
            'size' => 5,
        ),

    );

    $values = array();
    foreach ($fields as $name => $field_settings) {
        $values[$name] = $netshop->get_setting($name);
    }

    $form = new nc_a2f($fields, 'settings');
    $form->set_field_defaults('string', array('size' => 64))
         ->show_default_values(false)
         ->show_header(false)
         ->set_values($values);

    echo $form->render();

?>
</form>