<?php

if (!class_exists('nc_core')) { die; }

/** @var array $infoblock_data */
/** @var nc_core $nc_core */

?>
<div class="nc-modal-dialog nc-infoblock-settings-dialog" data-width="400" data-height="auto">
    <div class="nc-modal-dialog-header">
        <h2><?= $infoblock_data['Sub_Class_Name'] ?></h2>
    </div>
    <div class="nc-modal-dialog-body">
        <form action="<?= $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH ?>action.php" method="post" class="nc-form">

            <input type="hidden" name="ctrl" value="admin.infoblock">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="infoblock_id" value="<?= $infoblock_data['Sub_Class_ID'] ?>">

            <? /*
            <div class="nc-field">
                <span class="nc-field-caption">
                    <?= CONTROL_CONTENT_SUBCLASS_CLASSNAME ?>:
                </span>
                <?= nc_admin_input_simple(
                        'data[Sub_Class_Name]',
                        $infoblock_data["Sub_Class_Name"],
                        50,
                        '',
                        "maxlength='255'"
                    );
                ?>
            </div>

            <div class="nc-field">
                <span class="nc-field-caption">
                    <?= CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_KEYWORD ?>:
                </span>
                <?= nc_admin_input_simple(
                        'data[EnglishName]',
                        $infoblock_data['EnglishName'],
                        50,
                        '',
                        "maxlength='255' data-type='transliterate' data-from='data[Sub_Class_Name]' data-is-url='yes'"
                    );
                ?>
            </div>
            */ ?>

            <div class="custom_settings">
                <?= $custom_settings ?>
            </div>

        </form>
    </div>
    <div class="nc-modal-dialog-footer">
        <button data-action="submit"><?= NETCAT_REMIND_SAVE_SAVE ?></button>
        <button data-action="close"><?= CONTROL_BUTTON_CANCEL ?></button>
    </div>
</div>
