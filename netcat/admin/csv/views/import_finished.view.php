<div class="nc_admin_fieldset_head"><?php echo TOOLS_CSV_FINISHED_HEADER; ?></div>
<?php
if (empty($data['error'])) {
    echo $ui->alert->success(TOOLS_CSV_IMPORT_SUCCESS.$data['success']);
} else {
    echo $ui->alert->error($data['error']);
}
?>