<?php if (!class_exists('nc_core')) { die; } ?>

<?php
$block_id = uniqid('nc_netshop_delivery_schedule_', false);
?>

<div class="nc-netshop-delivery-schedule" id="<?= $block_id ?>">
<div class="nc-netshop-delivery-schedule-intervals">
<?php

/** @var nc_ui $ui */
/** @var nc_netshop_delivery_schedule $schedule */
$n = 0;

$print_interval = function($n, nc_netshop_delivery_interval $interval) use ($ui) {
    $field_prefix = ($n === false ? 'schedule_template[X]' : "schedule[$n]");
    $field_name = function($name) use ($field_prefix) {
        return $field_prefix . '[' . $name . ']';
    };

    echo '<div class="nc-netshop-delivery-schedule-interval">';
    echo $ui->html->input('hidden', $field_name('id'), $interval->get_id());

    echo '<div class="nc-netshop-delivery-schedule-interval-days-of-week">';
    for ($i = 1; $i <= 7; $i++) {
        $value = (int)$interval->get("day$i");
        $color = $value ? 'green' : 'lighten';
        $text_label = nc_netshop_delivery_interval::get_day_of_week_short_name($i);
        echo '<span class="nc-netshop-delivery-schedule-interval-day-of-week">',
             $ui->label($text_label)->$color(),
             $ui->html->input('hidden', $field_name("day$i"), $value),
             '</span>';
    }
    echo '</div>';

    echo '<div class="nc-netshop-delivery-schedule-interval-time-from">',
        '<span class="nc--caption">' . NETCAT_MODULE_NETSHOP_DELIVERY_SCHEDULE_TIME_FROM . '</span> ',
         $ui->html->input('string', $field_name('time_from'), $interval->get('time_from'))
             ->placeholder(NETCAT_MODULE_NETSHOP_DELIVERY_SCHEDULE_TIME_PLACEHOLDER)
             ->class_name('nc-input')->small(),
         '</div>';

    echo '<div class="nc-netshop-delivery-schedule-interval-time-to">',
         '<span class="nc--caption">' . NETCAT_MODULE_NETSHOP_DELIVERY_SCHEDULE_TIME_TO . '</span> ',
         $ui->html->input('string', $field_name('time_to'), $interval->get('time_to'))
             ->placeholder(NETCAT_MODULE_NETSHOP_DELIVERY_SCHEDULE_TIME_PLACEHOLDER)
             ->class_name('nc-input')->small(),
         '</div>';

    echo '<div class="nc-netshop-delivery-schedule-interval-remove">',
         $ui->html->input('hidden', $field_name('delete'), 0),
         '<a href="#"><i class="nc-icon-s nc--remove"></i></a>',
         '</div>';

    echo '</div>';
};

foreach ($schedule as $interval) {
    $print_interval($n++, $interval);
}

?>
</div>
<div class="nc-netshop-delivery-schedule-interval-add">
    <a href="#"><?= NETCAT_MODULE_NETSHOP_BUTTON_ADD ?></a>
</div>
<div class="nc-netshop-delivery-schedule-interval-template" style="display: none" data-next="<?= $n + 1 ?>">
    <? $print_interval(false, new nc_netshop_delivery_interval()); ?>
</div>
<script>
    $nc(function() {
        var block = $nc('#<?= $block_id ?>');
        function select(class_suffix) {
            return block.find('.nc-netshop-delivery-schedule-' + class_suffix);
        }

        function toggle_day() {
            var container = $nc(this),
                label = container.find('.nc-label'),
                input = container.find('input');
            if (input.val() == 0) {
                label.removeClass('nc--lighten').addClass('nc--green');
                input.val(1);
            } else {
                label.removeClass('nc--green').addClass('nc--lighten');
                input.val(0);
            }
       }
        block.on('click', '.nc-netshop-delivery-schedule-interval-day-of-week', toggle_day);

        function add_interval() {
            var template_div = select('interval-template'),
                n = template_div.data('next'),
                new_div = $nc(template_div.html().replace(/_template\[X\]/g, '[' + n + ']'));
            template_div.data('next', n + 1);
            select('intervals').append(new_div);
            new_div.find('select').focus();
            return false;
        }

        function remove_interval() {
            if (confirm('<?= htmlspecialchars(NETCAT_MODULE_NETSHOP_DELIVERY_SCHEDULE_INTERVAL_REMOVE, ENT_QUOTES) ?>')) {
                var button = $nc(this);
                button.closest('.nc-netshop-delivery-schedule-interval-remove').find('input').val(1);
                button.closest('.nc-netshop-delivery-schedule-interval').hide();
            }
            return false;
        }

        select('interval-add').click(add_interval);
        block.on('click', '.nc-netshop-delivery-schedule-interval-remove', remove_interval);
    })
</script>
</div>
