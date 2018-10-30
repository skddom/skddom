<?php
if (!class_exists('nc_core')) {
    die;
}

/**
 * @var nc_ui $ui
 * @var array $files
 * @var string $current_url
 * @var string $controller_name
 * @var string $intercept_url
 */
?>
<?= $ui->alert(NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_INTERCEPT_URL . ': ' . $intercept_url); ?>
<h2><?= NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_FILES_LIST; ?></h2>
<?php

$table = $ui->table()->wide()->striped()->bordered()->hovered();
$th = $table->thead();
$th->th('#');
$th->th(NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_FILE);
$th->th(NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_CREATED_AT);
$th->th();

$i = 1;
foreach ($files as $file) {
    $row = $table->row();
    $row->td($i++);
    $row->td($file['filename']);
    $row->td(date('d.m.Y H:i:s', $file['created_at']));

    $import_link = $current_url . 'import' . '&file=' . urlencode($file['filename']);

    $a = $ui->html('a')
        ->attr('href', $import_link)
        ->style('margin-right: 20px;')
        ->text(NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_IMPORT);

    $delete_button = $ui->controls()->delete_button(
        NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_CONFIRM_DELETE_FILE,
        array(
            'controller' => $controller_name,
            'filename' => $file['filename'],
            'action' => 'delete_file',
        )
    );

    $row->td()->text_right()->text($a->render() . $delete_button);
    $table->add_row($row);
}
?>
<?= $table->render(); ?>
<?php
$button = $ui->btn($current_url . 'delete_all_files', NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_DELETE_ALL_FILES);
$button->red();
$button->attr(
    'onclick',
    'return confirm(\'' . NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_CONFIRM_DELETE_ALL_FILES . '\');'
);
?>
<div align="right" style="margin-top: 30px;">
    <?= $button->render(); ?>
</div>