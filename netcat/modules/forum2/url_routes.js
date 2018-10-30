// $Id: url_routes.js 3602 2009-12-02 09:37:09Z vadim $

urlDispatcher.addRoutes( { 
    // append tab
    'module.forum2': NETCAT_PATH + 'modules/forum2/admin.php',
    'module.forum2.settings': NETCAT_PATH + 'modules/forum2/admin.php?page=settings',
    'module.forum2.converter': NETCAT_PATH + 'modules/forum2/admin.php?phase=3&page=converter'
} );