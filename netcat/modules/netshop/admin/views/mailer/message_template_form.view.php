<?php if (!class_exists('nc_core')) { die; } ?>

<?= $ui->controls->site_select($catalogue_id) ?>

<?php

$form = $ui->form()->vertical();
$form->attr('enctype', 'multipart/form-data');
$form->add()->input('hidden', 'action', 'message_template_save');
$form->add()->input('hidden', 'catalogue_id', $catalogue_id);
$form->add()->input('hidden', 'recipient_role', $recipient_role);
$form->add()->input('hidden', 'order_status', $order_status);

$form->add()->input('hidden', 'data[template_id]', $template->get_id());
$form->add()->input('hidden', 'data[catalogue_id]', $catalogue_id);
$form->add()->input('hidden', 'data[type]', $template_type);

$row = $form->add_row();
$row->input('hidden', 'data[enabled]', '0');
$row->checkbox('data[enabled]', $template->get_id() && $template->get('enabled'), NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_IS_ENABLED)
    ->value('1');

$form->add_row(NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_PARENT_TEMPLATE)
     ->select('data[parent_template_id]',
              $netshop->mailer->get_template_list($catalogue_id),
              $template->get('parent_template_id'));

$form->add_row(NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_SUBJECT)
     ->input('text', 'data[subject]', $template->get('subject'))
     ->xlarge();

$form->add_row(NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_BODY)
     ->textarea('data[body]', $template->get('body'))
     ->class_name('no_cm')->attr('id', 'nc_netshop_mailer_template_body')
     ->xlarge();

$form->add_row()
    ->text(nc_mail_attachment_form('netshop_' . $catalogue_id . '_' . $template_type));

nc_netshop_mailer_admin_helpers::include_template_editor_js($netshop);

echo $form,
     "<script>nc_netshop_mailer_template_editor('nc_netshop_mailer_template_body')</script>";