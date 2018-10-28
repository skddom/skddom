urlDispatcher.addRoutes({
    'module.bills': NETCAT_PATH + 'modules/bills/admin/',
    'module.bills.information': NETCAT_PATH + 'modules/bills/admin/?controller=information&action=index',
    'module.bills.bills': NETCAT_PATH + 'modules/bills/admin/?controller=bills&action=index',
    'module.bills.bills.add': NETCAT_PATH + 'modules/bills/admin/?controller=bills&action=edit&type=%1',
    'module.bills.bills.edit': NETCAT_PATH + 'modules/bills/admin/?controller=bills&action=edit&id=%1',
    'module.bills.acts': NETCAT_PATH + 'modules/bills/admin/?controller=acts&action=index',
    'module.bills.acts.add': NETCAT_PATH + 'modules/bills/admin/?controller=acts&action=edit',
    'module.bills.acts.edit': NETCAT_PATH + 'modules/bills/admin/?controller=acts&action=edit&id=%1',
    'module.bills.settings': NETCAT_PATH + 'modules/bills/admin/?controller=settings&action=index',
    'module.bills.catalogs': NETCAT_PATH + 'modules/bills/admin/?controller=catalogs&action=index',
    'module.bills.catalogs.statuses': NETCAT_PATH + 'modules/bills/admin/?controller=catalogs&action=statuses',
    'module.bills.catalogs.services': NETCAT_PATH + 'modules/bills/admin/?controller=catalogs&action=services',
    'module.bills.customers': NETCAT_PATH + 'modules/bills/admin/?controller=customers&action=index',
    'module.bills.customers.add': NETCAT_PATH + 'modules/bills/admin/?controller=customers&action=add',
    'module.bills.customers.edit': NETCAT_PATH + 'modules/bills/admin/?controller=customers&action=edit&id=%1',

    1: '' // dummy entry
});