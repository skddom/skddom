<?php class_exists('nc_core') OR die ?>

<div class="nc--clearfix"></div>


<div class="nc-margin-20">
    <div class='nc-chart' id='nc_sales_amount'></div>
</div>

<div class="nc-margin-20">
    <div class='nc-chart' id='nc_orders_chart'></div>
</div>

<!-- <div class="nc-margin-20">
    <div class='nc-chart' id='nc_successful_orders_chart'></div>
</div> -->

<div class="nc-margin-20">
    <table class="nc-table nc--striped nc--bordered">
        <tr>
            <th></th>
            <th><?=$period_header ?></th>
            <th><?= NETCAT_MODULE_NETSHOP_LAST_PERIOD ?></th>
            <th></th>
        </tr>
        <tr>
            <td><?=NECTAT_MODULE_NETSHOP_SALES_AMOUNT ?></td>
            <td><?=$this->netshop->format_price($totals['sales_amount']) ?></td>
            <td><?=$this->netshop->format_price($totals_last['sales_amount']) ?></td>
            <? $color = $totals_diff['sales_amount'] > 0 ? 'nc--green' : ($totals_diff['sales_amount'] < 0 ? 'nc--red' : '') ?>
            <td><span class="nc-label <?=$color ?>"><?=$this->netshop->format_price($totals_diff['sales_amount']) ?></span></td>
        </tr>
        </tr>
            <td><?=NECTAT_MODULE_NETSHOP_SUCCESSFUL_ORDERS_PERCENTAGE ?></td>
            <td><?=$totals['successful_orders_percentage'] ?>%</td>
            <td><?=$totals_last['successful_orders_percentage'] ?>%</td>
            <? $color = $totals_diff['successful_orders_percentage'] > 0 ? 'nc--green' : ($totals_diff['successful_orders_percentage'] < 0 ? 'nc--red' : '') ?>
            <td><span class="nc-label <?=$color ?>"><?=$totals_diff['successful_orders_percentage'] ?>%</span></td>
        </tr>
        </tr>
            <td><?=NECTAT_MODULE_NETSHOP_TOTAL_ORDERS ?></td>
            <td><?=$totals['total_orders'] ?></td>
            <td><?=$totals_last['total_orders'] ?></td>
            <? $color = $totals_diff['total_orders'] > 0 ? 'nc--green' : ($totals_diff['total_orders'] < 0 ? 'nc--red' : '') ?>
            <td><span class="nc-label <?=$color ?>"><?=$totals_diff['total_orders'] ?></span></td>
        </tr>
        </tr>
            <td><?=NECTAT_MODULE_NETSHOP_AVG_ORDER_AMOUNT ?></td>
            <td><?=$this->netshop->format_price($totals['avg_order_amount']) ?></td>
            <td><?=$this->netshop->format_price($totals_last['avg_order_amount']) ?></td>
            <? $color = $totals_diff['avg_order_amount'] > 0 ? 'nc--green' : ($totals_diff['avg_order_amount'] < 0 ? 'nc--red' : '') ?>
            <td><span class="nc-label <?=$color ?>"><?=$this->netshop->format_price($totals_diff['avg_order_amount']) ?></span></td>
        </tr>
        </tr>
            <td><?=NECTAT_MODULE_NETSHOP_AVG_SALES_ORDER_AMOUNT_BY_DAY ?></td>
            <td><?=$this->netshop->format_price($totals['avg_order_amount_by_day']) ?></td>
            <td><?=$this->netshop->format_price($totals_last['avg_order_amount_by_day']) ?></td>
            <? $color = $totals_diff['avg_order_amount_by_day'] > 0 ? 'nc--green' : ($totals_diff['avg_order_amount_by_day'] < 0 ? 'nc--red' : '') ?>
            <td><span class="nc-label <?=$color ?>"><?=$this->netshop->format_price($totals_diff['avg_order_amount_by_day']) ?></span></td>
        </tr>
        </tr>
        </tr>
    </table>
</div>

<div class="nc--clearfix"></div>

<script type="text/javascript">
(function(){
    var stat = <?=$chart_stat ?>;
    nc.chart.set_defaults(<?=$chart_defaults ?>);

    if (nc.key_exists('sales_amount', stat)) {
        nc.chart(nc('#nc_sales_amount'), [stat.sales_amount], {nc_yaxis_format:'price', nc_xaxis_format:'date'});
    } else {
        nc_netshop_stat_error('#nc_sales_amount');
    }

    if (nc.key_exists('total_orders', stat) && nc.key_exists('completed_orders', stat)) {
        stat.completed_orders.color = 3;
        nc.chart(nc('#nc_orders_chart'), [stat.total_orders, stat.completed_orders], {nc_xaxis_format:'date'});
    } else {
        nc_netshop_stat_error('#nc_orders_chart');
    }

    // nc.chart(nc('#nc_successful_orders_chart'), [stat.successful_orders_percentage]);
})();
</script>
