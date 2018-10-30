<?php if (!class_exists('nc_core')) { die; } ?>

 <div class="nc-modal-dialog" data-width="400" data-height="auto">
     <div class="nc-modal-dialog-header">
         <h2><?= NETCAT_MODULE_REQUESTS_FORM_SETTINGS_FIELDS_HEADER ?></h2>
     </div>
     <div class="nc-modal-dialog-body">
         <form class="nc-form nc--vertical" action="<?= $current_url ?>" method="POST">
             <input type="hidden" name="controller" value="form">
             <input type="hidden" name="action" value="save_settings">
             <input type="hidden" name="infoblock_id" value="<?= $infoblock_id ?>">
             <input type="hidden" name="form_type" value="<?= $form_type ?>">

             <div style="padding: 10px 0">
                 <?
                 /** @var array $selectable_fields */
                 /** @var array $enabled_fields */
                 foreach ($selectable_fields as $field_name => $field_properties) {
                     if ($field_name == 'Item_VariantName' && !$has_item_variants) {
                         echo "<input type='hidden' name='settings[Subdivision_VisibleFields][]' value='$field_name'>";
                         continue;
                     }

                     echo "<div>\n";
                     if ($field_properties['not_null'] || $field_name == 'Item_VariantName') {
                         echo '<label><input type="checkbox" checked disabled> ' .
                              htmlspecialchars($field_properties['description']) .
                              '</label>' .
                              '<input type="hidden" name="settings[Subdivision_VisibleFields][]" value="' . $field_name . '">';
                     }
                     else {
                         echo '<label><input type="checkbox" name="settings[Subdivision_VisibleFields][]"' .
                              ' value="' . $field_name . '"' .
                              (in_array($field_name, $enabled_fields) ? ' checked' : '') .
                              '> ' .
                              htmlspecialchars($field_properties['description']) .
                              '</label>';
                     }
                     echo "</div>\n";
                 }
                 ?>
             </div>

             <div class="nc-hint">
                 <?= NETCAT_MODULE_REQUESTS_FORM_SUBDIVISION_SYNC_HINT ?>
             </div>

         </form>
     </div>
     <div class="nc-modal-dialog-footer">
         <button data-action="submit"><?= NETCAT_REMIND_SAVE_SAVE ?></button>
         <button data-action="close"><?= CONTROL_BUTTON_CANCEL ?></button>
     </div>

 </div>
