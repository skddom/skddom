<?php

/**
 * Диалог создания вариантов товара по сочетанию характеристик
 *
 * Входящие параметры:
 *  - component_id
 *  - parent_item_id
 */

$NETCAT_FOLDER = realpath(dirname(__FILE__) . "/../../../../../") . "/";
require $NETCAT_FOLDER . "vars.inc.php";
require $INCLUDE_FOLDER . "index.php";

$nc_core = nc_core::get_object();

$item = new nc_netshop_item(array(
    'Class_ID' => $nc_core->input->fetch_get_post('component_id'),
    'Message_ID' => $nc_core->input->fetch_get_post('parent_item_id')
));

$cc_env = $nc_core->sub_class->get_by_id($item['Sub_Class_ID']);

if (!$cc_env || !s_auth($cc_env, 'add', 1)) {
    die(NETCAT_MODERATION_ERROR_NORIGHT);
}

$component = new nc_component($item['Class_ID']);
$variant_fields = array(
    NC_FIELDTYPE_INT, NC_FIELDTYPE_FLOAT, NC_FIELDTYPE_STRING,
    NC_FIELDTYPE_BOOLEAN, NC_FIELDTYPE_SELECT
);
$fields = array();
$classifier_values = array();
foreach ($component->get_fields() as $field) {
    if (!in_array($field['type'], $variant_fields)) { continue; }
    $fields[$field['name']] = $field['description'];
    if ($field['type'] == NC_FIELDTYPE_SELECT) {
        $classifier_values[$field['name']] = nc_db()->get_col(
            "SELECT `$field[table]_ID`, `$field[table]_Name`
               FROM `Classificator_$field[table]`",
            1, 0);
    }
    elseif ($field['type'] == NC_FIELDTYPE_BOOLEAN) {
        $classifier_values[$field['name']] = array(
            1 => NETCAT_MODULE_NETSHOP_CONDITION_TRUE,
            0 => NETCAT_MODULE_NETSHOP_CONDITION_FALSE,
        );
    }
}

?>

<div class="nc-modal-dialog">
    <div class="nc-modal-dialog-header">
        <h2><?= sprintf(NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_ADD_MULTIPLE_HEADER, $item["Name"]) ?></h2>
    </div>
    <div class="nc-modal-dialog-body">
        <div class="nc-alert nc--blue">
<!--            <i class="nc-icon-l nc--status-info"></i>-->
            <?= NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_ADD_MULTIPLE_DESCRIPTION ?>
        </div>
        <form class="nc-netshop-variant-multiple-field-form" method="POST"
         onsubmit="return false;"
         action="<?= nc_module_path('netshop') . 'admin/item/create_bulk.php' ?>">
            <?= nc_core('token')->get_input() ?>
            <input type="hidden" name="component_id" value="<?= htmlspecialchars($item['Class_ID']) ?>" />
            <input type="hidden" name="parent_item_id" value="<?= htmlspecialchars($item['Message_ID']) ?>" />
            <input type="hidden" name="infoblock_id" value="<?= htmlspecialchars($item['Sub_Class_ID']) ?>" />
            <table class="nc-table nc--wide nc-netshop-variant-multiple-field-table-no-drag">
                <tbody></tbody>
                <tr>
                    <td class="nc--compact"><i class="nc-icon nc--plus"></i></td>
                    <td colspan="3">
                        <select class="nc-netshop-variant-multiple-field-select">
                            <option class="nc--disabled" value=""><?= NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_SELECT_PROPERTY ?>
                            <?php
                                foreach ($fields as $k => $v) {
                                    echo "<option value='$k'";
                                    if (isset($classifier_values[$k])) {
                                        echo ' data-select-values="' .
                                             htmlspecialchars(nc_array_json($classifier_values[$k]), ENT_QUOTES) .
                                             '"';
                                    }
                                    echo ">" . htmlspecialchars($v) . "</option>\n";
                                }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>

            <?php if ($item['Article']): ?>
            <?php $article_field_name = $nc_core->get_component($item['Class_ID'])->get_field('Article', 'description'); ?>
            <div class="nc-netshop-variant-article">
                <label>
                    <span class="nc-netshop-variant-checkbox">
                        <input type="checkbox" name="fill_article_field" value="1">
                    </span><?= sprintf(NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_FILL_ARTICLE, $article_field_name) ?>
                </label>
                <div class="nc-netshop-variant-article-details">
                    <?= sprintf(NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_FILL_ARTICLE_COMMENT, $article_field_name) ?>
                </div>
            </div>
            <?php endif; ?>

        </form>

    </div>
    <div class="nc-modal-dialog-footer">
        <div class="nc-modal-dialog-footer-text nc-netshop-variant-multiple-count">
            <?= NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_COUNT ?> <span>0</span>
        </div>
        <button class="nc-btn nc--blue nc--disabled" data-role="submit"><?= NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_CREATE ?></button>
        <button class="nc-btn nc--red nc--bordered" data-action="close"><?= CONTROL_BUTTON_CANCEL ?></button>
    </div>
</div>

<?php
// Javascript, обрабатывающий события, находится в файле variant_admin.js