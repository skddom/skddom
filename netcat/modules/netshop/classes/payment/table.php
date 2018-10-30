<?php

class nc_netshop_payment_table extends nc_netshop_table {

    protected $table = 'Netshop_PaymentMethod';
    protected $primary_key = 'PaymentMethod_ID';

    protected $fields = array(
        'PaymentMethod_ID' => array(
            'field' => 'hidden',
        ),
        'Catalogue_ID' => array(
            'field' => 'hidden',
        ),
        'Priority' => array(),
        'Checked' => array(
            'default' => 1,
            'field' => 'hidden',
        ),
        'Name' => array(
            'title' => NETCAT_MODULE_NETSHOP_NAME_FIELD,
            'field' => 'string',
            'required' => 1,
        ),
        'Description' => array(
            'title' => NETCAT_MODULE_NETSHOP_DESCRIPTION_FIELD,
            'field' => 'textarea',
            'size' => 3,
            'codemirror' => false,
        ),
        'Condition' => array(
            'title' => NETCAT_MODULE_NETSHOP_CONDITION_FIELD,
            'field' => 'custom',
            'html' => "<div id='nc_netshop_condition_editor'></div>",
            'wrap' => true,
        ),
        'PaymentFromDelivery' => array(
            'title' => NETCAT_MODULE_NETSHOP_PAYMENT_METHOD_DELIVERY,
            'field' => 'checkbox',
            'value_for_off' => 0,
            'value_for_on' => 1,
        ),
        'PaymentSystem_ID' => array(
            'title' => NETCAT_MODULE_NETSHOP_PAYMENT_METHOD_PAYMENT_SYSTEM,
            'field' => 'select',
            'subtype' => 'classificator',
            'classificator' => 'PaymentSystem',
            'empty_option_text' => NETCAT_MODULE_NETSHOP_PAYMENT_METHOD_NO_PAYMENT_SYSTEM_OPTION,
        ),
        'PaymentOnDeliveryCash' => array(
            'title' => NETCAT_MODULE_NETSHOP_DELIVERY_PAYMENT_CASH,
            'field' => 'checkbox',
            'value_for_off' => 0,
            'value_for_on' => 1,
        ),
        'PaymentOnDeliveryCard' => array(
            'title' => NETCAT_MODULE_NETSHOP_DELIVERY_PAYMENT_CARD,
            'field' => 'checkbox',
            'value_for_off' => 0,
            'value_for_on' => 1,
        ),
            'ExtraChargeAbsolute' => array(
            'title' => NETCAT_MODULE_NETSHOP_PAYMENT_METHOD_EXTRA_CHARGE_ABSOLUTE,
            'field' => 'float',
            'size' => 10,
        ),
        'ExtraChargeRelative' => array(
            'title' => NETCAT_MODULE_NETSHOP_PAYMENT_METHOD_EXTRA_CHARGE_RELATIVE,
            'field' => 'float',
            'size' => 10,
        ),
    );

    public function make_form($data) {
        if (!nc_core::get_object()->modules->get_by_keyword('payment', false, false)) {
            $this->fields['PaymentSystem_ID'] = array(
                'title' => NETCAT_MODULE_NETSHOP_PAYMENT_METHOD_PAYMENT_SYSTEM,
                'field' => 'custom',
                'html' => "<div class='nc-alert'><i class='nc-icon-l nc--status-info'></i>" .
                          NETCAT_MODULE_NETSHOP_NO_PAYMENT_MODULE .
                          "</div>",
                'wrap' => true,
            );
        }
        return parent::make_form($data);
    }


}