<?php if (!class_exists('nc_core')) { die; } ?>

<?= $ui->controls->site_select($catalogue_id) ?>

<form class='nc-form' method='post'>
<input type='hidden' name='action' value='settings_save'>
<input type='hidden' name='next_action' value='index'>
<?php

    // свойства организации
    $fields = array(
        'ShopName' => array(
            'caption' => NETCAT_MODULE_NETSHOP_SHOP_NAME,
            'type' => 'string',
            'required' => true
        ),
        'CompanyName' => array(
            'caption' => NETCAT_MODULE_NETSHOP_COMPANY_NAME,
            'type' => 'string',
            'required' => true
        ),
        'Address' => array(
            'caption' => NETCAT_MODULE_NETSHOP_ADDRESS,
            'type' => 'string',
            'required' => false
        ),
        'City' => array(
            'caption' => NETCAT_MODULE_NETSHOP_CITY,
            'type' => 'select',
            'subtype' => 'classificator',
            'classificator' => 'Region',
            'required' => false
        ),
        'Phone' => array(
            'caption' => NETCAT_MODULE_NETSHOP_PHONE,
            'type' => 'string',
            'required' => false
        ),
        'MailFrom' => array(
            'caption' => NETCAT_MODULE_NETSHOP_MAIL_FROM,
            'type' => 'string',
            'required' => true
        ),
        'ManagerEmail' => array(
            'caption' => NETCAT_MODULE_NETSHOP_MANAGER_EMAIL,
            'type' => 'string',
            'required' => false
        ),
        'INN' => array(
            'caption' => NETCAT_MODULE_NETSHOP_INN,
            'type' => 'string',
            'required' => false
        ),
        'BankName' => array(
            'caption' => NETCAT_MODULE_NETSHOP_BANK_NAME,
            'type' => 'string',
            'required' => false
        ),
        'BankAccount' => array(
            'caption' => NETCAT_MODULE_NETSHOP_BANK_ACCOUNT,
            'type' => 'string',
            'required' => false
        ),
        'CorrespondentAccount' => array(
            'caption' => NETCAT_MODULE_NETSHOP_CORRESPONDENT_ACCOUNT,
            'type' => 'string',
            'required' => false
        ),
        'KPP' => array(
            'caption' => NETCAT_MODULE_NETSHOP_KPP,
            'type' => 'string',
            'required' => false
        ),
        'BIK' => array(
            'caption' => NETCAT_MODULE_NETSHOP_BIK,
            'type' => 'string',
            'required' => false
        ),
        'VAT' => array(
            'caption' => NETCAT_MODULE_NETSHOP_VAT,
            'type' => 'float',
            'required' => false
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