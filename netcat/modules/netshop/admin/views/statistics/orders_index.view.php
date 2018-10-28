<?php class_exists('nc_core') OR die ?>

<?=$chart_init ?>
<?=$stat_init ?>

<?= $ui->controls->site_select($catalogue_id) ?>

<script>
    nc_netshop_stat_settings.action   = 'orders_by_period';
    nc_netshop_stat_settings.group_by = 'day';
</script>

<div id="nc_netshop_stat">
    <h4><?=NETCAT_MODULE_NETSHOP_ORDER_STATUS ?>:</h4>
    <div class="nc-dashboard-grid">
        <? foreach ($order_status_counts as $status_id => $status): ?>
            <div class="nc-widget nc--lighten">
                <table class="nc-widget-grid nc-widget-link" onclick="window.location='<?= $SUB_FOLDER . $HTTP_ROOT_PATH ?>modules/netshop/admin/?controller=order&amp;action=index&amp;site_id=<?= $site_id ?>&amp;order_status=<?=$status_id ?>'">
                    <tr>
                        <td>
                            <dl class="nc-info nc--vertical nc--small">
                                <dt><?=$status['count'] ?></dt>
                                <dd class='nc-text-grey'><?=$status['name'] ?></dd>
                            </dl>
                        </td>
                    </tr>
                </table>
            </div>
        <? endforeach ?>
    </div>

    <div class="nc--clearfix"></div>

    <?=$period_stat ?>

</div>
