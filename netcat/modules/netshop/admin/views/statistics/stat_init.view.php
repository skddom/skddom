<?php class_exists('nc_core') OR die ?>

<? if ($nc_core->modules->get_by_keyword('calendar', 0)): ?>
    <?=nc_set_calendar(0) ?>
<? endif ?>

<script>

var nc_netshop_stat_settings = {};

function nc_netshop_stat_error(query, message) {
    nc(query)
        .addClass('nc-alert nc--red')
        .html(message || "<?=NETCAT_MODULE_NETSHOP_DATA_NOT_AVAILABLE ?>");
}

function nc_netshop_get_stat(link, query) {
    var controller_link = '<?=$controller_link ?>';
    var $tab_elem = nc(link).parents('li');

    if ($tab_elem.hasClass('nc--disabled') || !query) {
        return false;
    }

    for (var k in query) {
        nc_netshop_stat_settings[k] = query[k];
    }

    var $panel_content = $tab_elem.parents('div.nc-panel').find('div.nc-panel-content');
    var params = Object.keys(nc_netshop_stat_settings).map(function(k){return k+'='+nc_netshop_stat_settings[k]}).join('&');
    var action = controller_link + '&' + params;

    // Select tab
    $tab_elem.parent().find('li.nc--active').removeClass('nc--active');
    $tab_elem.addClass('nc--loading nc--active');
    $panel_content.animate({opacity:.2}, 100);

    nc.$.ajax({
        url: action,
        type: 'POST',
        data: {},
        success: function(data){
            $tab_elem.removeClass('nc--loading');
            $panel_content.html(data);
            $panel_content.animate({opacity:1}, 100);
        },
    });

    return false;
}
</script>
