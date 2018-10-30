<?php
$form = $ui->form($action_url . 'import_run')->multipart()->horizontal()->id('import');
$form->add_row(TOOLS_DATA_BACKUP_IMPORT_FILE)->file('file');
$form->add_row('&nbsp;')->checkbox('save_ids', false, TOOLS_DATA_SAVE_IDS);
if ($debug) $form->actions()->button('submit', 'Submit')->blue()->large();
?>

<?=$form ?>