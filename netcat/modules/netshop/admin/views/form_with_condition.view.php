<?php if (!class_exists('nc_core')) { die; } ?>

<form class='nc-form' method='post'>
    <?=$form ?>
</form>

<?php

nc_netshop_condition_admin_helpers::include_condition_editor_js();

$condition_json = $record['Condition'];
if (!$condition_json ) { $condition_json = "{}"; }

if (!isset($condition_groups)) {
    $condition_groups = array('GROUP_CART', 'GROUP_USER', 'GROUP_ORDER', 'GROUP_ORDERS', 'GROUP_VALUEOF', 'GROUP_EXTENSION');
}

?>

<script>
    (function() {
        var container = $nc('#nc_netshop_condition_editor'),
            condition_editor = new nc_netshop_condition_editor({
                container: container,
                input_name: 'data[Condition]',
                conditions: <?=$condition_json ?>,
                site_id: <?=$catalogue_id ?>,
                groups_to_show: <?=nc_array_json($condition_groups) ?>
            });

        container.closest('.ncf_value').removeClass('ncf_value');
        container.closest('form').get(0).onsubmit = function() {
            return condition_editor.onFormSubmit();
        };
    })();
</script>