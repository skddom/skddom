<?php class_exists('nc_core') OR die ?>

<div class="nc--clearfix"></div>

<div class="nc-margin-20">
    <div class='nc-chart' id='nc_purchased_goods'></div>
</div>

<div class="nc--clearfix"></div>

<script type="text/javascript">
(function(){
    var stat = <?=$chart_stat ?>;
    nc.chart.set_defaults(<?=$chart_defaults ?>);
    if (nc.key_exists('purchased_goods', stat)) {
        nc.chart(nc('#nc_purchased_goods'), [stat.purchased_goods], {nc_xaxis_format:'date'});
    } else {
        nc_netshop_stat_error('#nc_chart_goods');
    }
})();
</script>
