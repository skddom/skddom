<div style='display:none; z-index:10000' id='nc_calendar_popup_from_d'></div>
<div style='display:none; z-index:10000' id='nc_calendar_popup_to_d'></div>

<script>
function nc_netshop_period_form(show, el) {
    if (show) {
        nc('#nc_netshop_stat_period_form').slideDown();
    } else {
        nc('#nc_netshop_stat_period_form').hide();
    }

    if (el) {
        nc(el).parents('ul').find('li').removeClass('nc--active');
        nc(el).parents('li').addClass('nc--active');

        var $panel_content = nc(el).parents('div.nc-panel').find('div.nc-panel-content');
        $panel_content.animate({opacity:.2}, 100);
    }

    return false;
}
function nc_netshop_show_period_stat() {
    var v = function(name) {
        return nc('#nc_netshop_stat_period_form input[name='+name+']').val();
    }
    var d = function(name) {
        return v(name + '_y') + '-' + v(name + '_m') + '-' + v(name + '_d');
    }

    return nc_netshop_get_stat(nc('#nc_netshop_period_btn'), {period:d('from') + ':' + d('to')});
}
</script>

<div class="nc-panel">
    <ul class="nc-nav-pills nc--right">
        <li><?= NETCAT_MODULE_NETSHOP_GROUP_BY ?>:</li>
        <li class='nc--active'><a onclick="return nc_netshop_get_stat(this, {group_by:'day'})" href="#"><?=NETCAT_MODULE_NETSHOP_BY_DAYS ?></a></li>
        <li><a onclick="return nc_netshop_get_stat(this, {group_by:'week'})" href="#"><?=NETCAT_MODULE_NETSHOP_BY_WEEKS ?></a></li>
        <li><a onclick="return nc_netshop_get_stat(this, {group_by:'month'})" href="#"><?=NETCAT_MODULE_NETSHOP_BY_MONTHS ?></a></li>
    </ul>
    <ul class="nc-tabs nc--small">
        <li><a onclick="nc_netshop_period_form(0); return nc_netshop_get_stat(this, {period:7})" href="#"><?=NETCAT_MODULE_NETSHOP_7_DAYS ?></a></li>
        <li><a onclick="nc_netshop_period_form(0); return nc_netshop_get_stat(this, {period:30})" href="#"><?=NETCAT_MODULE_NETSHOP_30_DAYS ?></a></li>
        <li><a id='nc_netshop_period_btn' onclick="return nc_netshop_period_form(1, this)" href="#"><?=NETCAT_MODULE_NETSHOP_OVER_PERIOD ?></a></li>
    </ul>

    <div id='nc_netshop_stat_period_form' class='nc-form nc-padding-10 nc--hide'>
        <?= NETCAT_MODULE_NETSHOP_FILTER_FROM ?>
        <input name="from_d" type="text" class='nc--mini' /> .
        <input name="from_m" type="text" class='nc--mini' /> .
        <input name="from_y" type="text" class='nc--small' />
        <span style='position:relative'>
            <button id='nc_calendar_popup_img_from_d' class='nc-btn nc--light' onclick="nc_calendar_popup('from_d', 'from_m', 'from_y')"><i class="nc-icon nc--calendar"></i></button>
        </span>
        &nbsp;&nbsp;&nbsp;
        <?= NETCAT_MODULE_NETSHOP_FILTER_TO ?>
        <input name="to_d" type="text" class='nc--mini' /> .
        <input name="to_m" type="text" class='nc--mini' /> .
        <input name="to_y" type="text" class='nc--small' />
        <span style='position:relative'>
            <button id='nc_calendar_popup_img_to_d' class='nc-btn nc--light' onclick="nc_calendar_popup('to_d', 'to_m', 'to_y')"><i class="nc-icon nc--calendar"></i></button>
        </span>

        <button onclick="nc_netshop_show_period_stat()" class='nc-btn nc--blue'><?= NETCAT_MODULE_NETSHOP_FILTER_SHOW ?></button>
    </div>
    <div class="nc-panel-content nc-bg-lighten"></div>
</div>

<script>
    // activate first tab in all panels
    nc('#nc_netshop_stat ul.nc-tabs>li>a').first().click();
</script>
