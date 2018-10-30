urlDispatcher.addRoutes( {
    'module.netshop.forms': NETCAT_PATH + 'modules/netshop/admin/redirect.php?script=forms',
    'module.netshop.1c.sources': NETCAT_PATH + 'modules/netshop/admin/redirect.php?script=sources',
    'module.netshop.1c.import': NETCAT_PATH + 'modules/netshop/admin/redirect.php?script=import',
    'module.netshop.1c.interceptor': NETCAT_PATH + 'modules/netshop/admin/?controller=external_1c_interceptor&action=index',

    'module.netshop.order': NETCAT_PATH + 'modules/netshop/admin/?controller=order&action=index&site_id=%1',
    'module.netshop.order.view': NETCAT_PATH + 'modules/netshop/admin/?controller=order&action=view&site_id=%1&order_id=%2',
    'module.netshop.order.statuses': NETCAT_PATH + 'modules/netshop/admin/?controller=order&action=statuses&site_id=%1',

    'module.netshop.statistics': NETCAT_PATH + 'modules/netshop/admin/?controller=statistics&action=index&site_id=%1',
    'module.netshop.statistics.goods': NETCAT_PATH + 'modules/netshop/admin/?controller=statistics&action=goods&site_id=%1',
    'module.netshop.statistics.customers': NETCAT_PATH + 'modules/netshop/admin/?controller=statistics&action=customers&site_id=%1',
    'module.netshop.statistics.coupons': NETCAT_PATH + 'modules/netshop/admin/?controller=statistics&action=coupons&site_id=%1',

    'module.netshop.mailer.template': NETCAT_PATH + 'modules/netshop/admin/?controller=mailer_template&action=master_template_index&site_id=%1',
    'module.netshop.mailer.template.add': NETCAT_PATH + 'modules/netshop/admin/?controller=mailer_template&action=master_template_add&site_id=%1',
    'module.netshop.mailer.template.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=mailer_template&action=master_template_edit&id=%1',
    'module.netshop.mailer.customer_mail': NETCAT_PATH + 'modules/netshop/admin/?controller=mailer_template&action=message_template_edit&site_id=%1&recipient_role=customer&order_status=%2',
    'module.netshop.mailer.manager_mail': NETCAT_PATH + 'modules/netshop/admin/?controller=mailer_template&action=message_template_edit&site_id=%1&recipient_role=manager&order_status=%2',
    'module.netshop.mailer.rule': NETCAT_PATH + 'modules/netshop/admin/?controller=mailer_rule&action=index&site_id=%1',
    'module.netshop.mailer.rule.add': NETCAT_PATH + 'modules/netshop/admin/?controller=mailer_rule&action=add&site_id=%1',
    'module.netshop.mailer.rule.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=mailer_rule&action=edit&id=%1',

    'module.netshop.promotion.discount.item': NETCAT_PATH + 'modules/netshop/admin/?controller=promotion_discount&discount_type=item&action=index&site_id=%1',
    'module.netshop.promotion.discount.item.add': NETCAT_PATH + 'modules/netshop/admin/?controller=promotion_discount&discount_type=item&action=edit&site_id=%1',
    'module.netshop.promotion.discount.item.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=promotion_discount&discount_type=item&action=edit&discount_id=%1',

    'module.netshop.promotion.discount.cart': NETCAT_PATH + 'modules/netshop/admin/?controller=promotion_discount&discount_type=cart&action=index&site_id=%1',
    'module.netshop.promotion.discount.cart.add': NETCAT_PATH + 'modules/netshop/admin/?controller=promotion_discount&discount_type=cart&action=edit&site_id=%1',
    'module.netshop.promotion.discount.cart.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=promotion_discount&discount_type=cart&action=edit&discount_id=%1',

    'module.netshop.promotion.discount.delivery': NETCAT_PATH + 'modules/netshop/admin/?controller=promotion_discount&discount_type=delivery&action=index&site_id=%1',
    'module.netshop.promotion.discount.delivery.add': NETCAT_PATH + 'modules/netshop/admin/?controller=promotion_discount&discount_type=delivery&action=edit&site_id=%1',
    'module.netshop.promotion.discount.delivery.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=promotion_discount&discount_type=delivery&action=edit&discount_id=%1',

    'module.netshop.promotion.coupon': NETCAT_PATH + 'modules/netshop/admin/promotion/coupon.php?deal_type=%1&deal_id=%2',
    'module.netshop.promotion.coupon.generate': NETCAT_PATH + 'modules/netshop/admin/promotion/coupon.php?action=generate_ask&deal_type=%1&deal_id=%2',
    'module.netshop.promotion.coupon.edit': NETCAT_PATH + 'modules/netshop/admin/promotion/coupon.php?action=edit&coupon_code=%1',

    'module.netshop.settings': NETCAT_PATH + 'modules/netshop/admin/?controller=settings&action=index&site_id=%1',
    'module.netshop.settings.module': NETCAT_PATH + 'modules/netshop/admin/?controller=settings&action=module&site_id=%1',

    'module.netshop.payment': NETCAT_PATH + 'modules/netshop/admin/?controller=payment&action=index&site_id=%1',
    'module.netshop.payment.add': NETCAT_PATH + 'modules/netshop/admin/?controller=payment&action=add&site_id=%1',
    'module.netshop.payment.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=payment&action=edit&id=%1',

    'module.netshop.delivery': NETCAT_PATH + 'modules/netshop/admin/?controller=delivery_method&action=index&site_id=%1',
    'module.netshop.delivery.method': NETCAT_PATH + 'modules/netshop/admin/?controller=delivery_method&action=index&site_id=%1',
    'module.netshop.delivery.method.add': NETCAT_PATH + 'modules/netshop/admin/?controller=delivery_method&action=add&site_id=%1',
    'module.netshop.delivery.method.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=delivery_method&action=edit&id=%1',
    'module.netshop.delivery.point': NETCAT_PATH + 'modules/netshop/admin/?controller=delivery_point&action=index&site_id=%1',
    'module.netshop.delivery.point.add': NETCAT_PATH + 'modules/netshop/admin/?controller=delivery_point&action=add&site_id=%1',
    'module.netshop.delivery.point.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=delivery_point&action=edit&id=%1',

    'module.netshop.currency': NETCAT_PATH + 'modules/netshop/admin/?controller=currency&action=index&site_id=%1',
    'module.netshop.currency.add': NETCAT_PATH + 'modules/netshop/admin/?controller=currency&action=add&site_id=%1',
    'module.netshop.currency.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=currency&action=edit&id=%1',
    'module.netshop.currency.settings': NETCAT_PATH + 'modules/netshop/admin/?controller=currency&action=settings&id=%1',

    'module.netshop.currency.officialrate': NETCAT_PATH + 'modules/netshop/admin/?controller=officialrate&action=index&site_id=%1',
    'module.netshop.currency.officialrate.edit': NETCAT_PATH + 'modules/netshop/admin?controller=officialrate&action=edit&id=%1',

    'module.netshop.pricerule': NETCAT_PATH + 'modules/netshop/admin/?controller=pricerule&action=index&site_id=%1',
    'module.netshop.pricerule.add': NETCAT_PATH + 'modules/netshop/admin/?controller=pricerule&action=add&site_id=%1',
    'module.netshop.pricerule.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=pricerule&action=edit&id=%1',
    
    'module.netshop.market.yandex': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=yandex&action=index&site_id=%1',
    'module.netshop.market.yandex.bundle.add': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=yandex&action=edit&site_id=%1',
    'module.netshop.market.yandex.bundle.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=yandex&action=edit&bundle_id=%1',
    'module.netshop.market.yandex.bundle.edit_fields': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=yandex&action=edit_fields&bundle_id=%1',
    'module.netshop.market.yandex.order': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=yandex_order&action=order&site_id=%1',

    'module.netshop.market.google': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=google&action=index&site_id=%1',
    'module.netshop.market.google.bundle.add': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=google&action=edit&site_id=%1',
    'module.netshop.market.google.bundle.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=google&action=edit&bundle_id=%1',
    'module.netshop.market.google.bundle.edit_fields': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=google&action=edit_fields&bundle_id=%1',
    
    'module.netshop.market.mail': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=mail&action=index&site_id=%1',
    'module.netshop.market.mail.bundle.add': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=mail&action=edit&site_id=%1',
    'module.netshop.market.mail.bundle.edit': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=mail&action=edit&bundle_id=%1',
    'module.netshop.market.mail.bundle.edit_fields': NETCAT_PATH + 'modules/netshop/admin/?controller=market&place=mail&action=edit_fields&bundle_id=%1',

    1: '' // dummy entry
} );