<?php if (!class_exists('nc_core')) { die; } ?>

<?php

$catalogue_id = ($record['Catalogue_ID'] ? $record['Catalogue_ID'] : $catalogue_id);

$form = $ui->form()->vertical();
$form->attr('enctype', 'multipart/form-data');
$form->add()->input('hidden', 'catalogue_id', $catalogue_id);
$form->add()->input('hidden', 'data[Template_ID]', $record['Template_ID']);
$form->add()->input('hidden', 'data[Catalogue_ID]', $catalogue_id);
$form->add()->input('hidden', 'data[Type]', 'master');


$form->add_row(NETCAT_MODULE_NETSHOP_MAILER_MASTER_TEMPLATE_NAME)
     ->string('data[Name]', $record['Name'])
     ->xlarge();

$form->add_row(NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_BODY)
     ->textarea('data[Body]', $record['Body'])
     ->class_name('no_cm')->attr('id', 'nc_netshop_mailer_template_body')
     ->xlarge();

$form->add_row()
    ->text(nc_mail_attachment_form('netshop_' . $catalogue_id . '_master_' . ($record['Template_ID'] ? $record['Template_ID'] : 0)));

nc_netshop_mailer_admin_helpers::include_template_editor_js($netshop);

echo $form,
"<script>nc_netshop_mailer_template_editor('nc_netshop_mailer_template_body')</script>";
