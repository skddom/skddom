<?php

return array(
    'settings_dict_fields' => array(
        'OrderComponentID' => 'Class_ID',
        'DefaultCurrencyID' => 'ShopCurrency_ID',
        'ExternalCurrencyID' => 'ShopCurrency_ID',
        'PaidOrderStatusID' => 'ShopOrderStatus_ID',
        'PrevOrdersSumStatusID' => 'ShopOrderStatus_ID',
        '1cExportOrdersStatusID' => 'ShopOrderStatus_ID',
        'City' => 'Region_ID',
    ),

    'extensions' => array(
        'site' => array(
            'nc_netshop_currency_backup',
            'nc_netshop_delivery_backup',
            'nc_netshop_payment_backup',
            'nc_netshop_promotion_backup',
            'nc_netshop_mailer_backup',
//            'nc_netshop_order_backup',
            nc_netshop::get_instance()->is_feature_enabled('pricerule') ? 'nc_netshop_pricerule_backup' : null,
            nc_netshop::get_instance()->is_feature_enabled('1c') ? 'nc_netshop_export_backup' : null,
            'nc_netshop_market_backup',
            'nc_netshop_itemindex_backup',
        ),
    ),

);
