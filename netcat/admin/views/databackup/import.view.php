<?php
$form = $ui->form($ADMIN_PATH . 'backup.php?mode=import')->multipart()->horizontal()->id('import');
$form->add_row(NETCAT_IMPORT_FIELD)->file('import');
$form->add_row('&nbsp;')->checkbox('save_ids', false, TOOLS_DATA_SAVE_IDS);
if ($debug) $form->actions()->button('submit', 'Submit')->blue()->large();
?>

<?=$form ?>