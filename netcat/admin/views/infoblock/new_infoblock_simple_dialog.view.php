<?php

if (!class_exists('nc_core')) { die; }

/** @var nc_core $nc_core */
/** @var int $subdivision_id */
/** @var int $infoblock_id */
/** @var string $position */
/** @var array $component_templates */

// Стили диалога находятся в _special_parts.scss

$nc_core = nc_core::get_object();

?>
<div class="nc-modal-dialog" data-confirm-close="false" data-width="756">
    <style scoped>
        .nc-infoblock-create-simple-dialog {
            padding: 0 !important;
            margin-left: -30px;
        }

        .nc-infoblock-create-simple-dialog-container {
            display: flex;
            height: 300px;
        }

        .nc-infoblock-create-simple-dialog-container > div {
            overflow: auto;
            height: 100%;
            padding-top: 30px;
            flex-grow: 1;
        }

        .nc-infoblock-create-simple-dialog-components {
            min-width: 240px;
            max-width: 240px;
        }

        .nc-infoblock-create-simple-dialog-component {
            padding: 10px 10px 10px 27px;
            border-left: 3px solid #fff;
            cursor: default;
        }

        .nc-infoblock-create-simple-dialog-component.nc--selected {
            border-left: 3px solid #2196f3;
            background: #f2f5f7;
        }

        .nc-infoblock-create-simple-dialog-component:last-child {
            margin-bottom: 30px;
        }

        .nc-infoblock-create-simple-dialog-component-templates {
            padding-left: 30px;
        }

        .nc-infoblock-create-simple-dialog-component-template {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 30px;
            vertical-align: top;
            cursor: pointer;
        }

        .nc-infoblock-create-simple-dialog-component-template-preview {
            width: 215px;
            height: 150px;
            border: 1px solid #eee;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center center;
        }

        .nc-infoblock-create-simple-dialog-component-template-name {
            width: 215px;
            padding-top: 10px;
            color: #727272;
            line-height: 110%;
        }

    }
    </style>

    <div class="nc-modal-dialog-header">
        <h2><?= NETCAT_MODERATION_ADD_BLOCK_TITLE ?></h2>
    </div>
    <div class="nc-modal-dialog-body nc-infoblock-create-simple-dialog">
        <form action="<?= $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH ?>action.php" method="post" class="nc-form">

            <input type="hidden" name="ctrl" value="admin.infoblock">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="subdivision_id" value="<?= $subdivision_id ?>">
            <input type="hidden" name="position_infoblock_id" value="<?= $infoblock_id ?>">
            <input type="hidden" name="position" value="<?= htmlspecialchars($position) ?>">

            <input type="hidden" name="data[Class_ID]" value="">
            <input type="hidden" name="data[Class_Template_ID]" value="">

            <div class="nc-infoblock-create-simple-dialog-container">
                <div class="nc-infoblock-create-simple-dialog-components">
                <?
                    $previous_component = null;
                    foreach ($component_templates as $component) {
                        if (!$component['ClassTemplate'] && $component['Class_ID'] != $previous_component) {
                            ?>
                            <div class="nc-infoblock-create-simple-dialog-component"
                             data-component-id="<?= $component['Class_ID'] ?>">
                                <?= $component['Class_Name'] ?>
                            </div>
                            <?
                        }
                        $previous_component = $component['Class_ID'];
                    }
                ?>
                </div>

                <div class="nc-infoblock-create-simple-dialog-component-templates">
                <? foreach ($component_templates as $component): ?>
                    <? if (!$component['IsOptimizedForMultipleMode']) { continue; } ?>
                    <div class="nc-infoblock-create-simple-dialog-component-template"
                     data-component-id="<?= $component['ClassTemplate'] ?: $component['Class_ID'] ?>"
                     data-component-template-id="<?= $component['Class_ID'] ?>">
                        <div class="nc-infoblock-create-simple-dialog-component-template-preview"
                         style="background-image: url('<?= $nc_core->component->get_list_preview_relative_path($component['Class_ID']) ?>')">
                        </div>
                        <div class="nc-infoblock-create-simple-dialog-component-template-name">
                            <?= $component['Class_Name']; ?>
                        </div>
                    </div>
                <? endforeach; ?>
                </div>
            </div>

        </form>
    </div>

    <script>
        (function() {
            var dialog = nc.ui.modal_dialog.get_current_dialog();
            var components = dialog.find('.nc-infoblock-create-simple-dialog-component'),
                templates = dialog.find('.nc-infoblock-create-simple-dialog-component-template');

            components.click(function() {
                var c = $nc(this);
                components.removeClass('nc--selected');
                c.addClass('nc--selected');
                templates.hide().filter('[data-component-id=' + c.data('component-id') + ']').show();
            });

            components.eq(0).click();

            dialog.set_option('on_resize', function() {
                dialog.find('.nc-infoblock-create-simple-dialog-container')
                    .height($nc('#simplemodal-container').height());
            });

            templates.click(function() {
                var t = $nc(this);
                dialog.find('input[name="data[Class_ID]"]').val(t.data('component-id'));
                dialog.find('input[name="data[Class_Template_ID]"]').val(t.data('component-template-id'));
                dialog.submit_form();
            });
        })();
    </script>

</div>
