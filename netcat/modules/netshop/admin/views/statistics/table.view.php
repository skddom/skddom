<?php class_exists('nc_core') OR die ?>

<div class="nc-panel nc--left">
    <div class="nc-panel-header">
        <?=$table_title ?>
        <div class="nc--right">
            <? foreach ($table_limits as $l): ?>
                <a href="<?=$limit_url . $l ?>" class="nc-label <?=$l==$limit ? 'nc--light' : 'nc--white nc-text-blue'?>"><?=$l ?></a>
            <? endforeach ?>
        </div>
        <div class="nc--clearfix"></div>
    </div>
    <div class="nc-panel-content nc-bg-lighten">
        <table class='nc-table nc--striped'>
            <tr>
                <? foreach ($table_headers as $field => $header): ?>
                    <th>
                        <? if (in_array($field, $table_ordering_fields)): ?>
                            <? if ($order == $field): ?>
                                <b><?=$header ?></b>
                            <? else: ?>
                                <a href="<?=$order_url . $field ?>"><?=$header ?></a>
                            <? endif ?>
                        <? else: ?>
                            <?=$header ?>
                        <? endif ?>
                    </th>
                <? endforeach ?>
            </tr>
            <? foreach ($table_data as $row): ?>
            <tr class='nc-text-right'>
                <? foreach ($row as $value): ?>
                    <td><?=$value ?></td>
                <? endforeach ?>
            </tr>
            <? endforeach ?>
        </table>
    </div>
</div>

<div class="nc--clearfix"></div>

<? $total_pages = ceil($total/$limit) ?>
<? if ($total_pages > 1): ?>
    <div class="nc-pagination nc-margin-10">
        <? for ($p = 1; $p < $total_pages+1; $p++): ?>
            <a<?=$p == $page ? " class='nc--active'" : ''?> href="<?=$page_url . $p ?>"><?=$p ?></a>
        <? endfor ?>
    </div>
<? endif ?>
