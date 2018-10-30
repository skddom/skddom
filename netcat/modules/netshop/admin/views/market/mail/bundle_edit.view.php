<?php
if (!class_exists('nc_core')) {
  die;
}
?>
<div class="nc_admin_fieldset_head"><?= !empty($bundle_id) ? NETCAT_MODULE_NETSHOP_MAIL_BUNDLE_EDIT : NETCAT_MODULE_NETSHOP_MAIL_BUNDLE_ADD; ?></div>
<?php
$form = $ui->form("?controller=$controller_name&action=save")->vertical();
$form->add()->input('hidden', 'catalogue_id', $catalogue_id);
$form->add()->input('hidden', 'action', 'save');
$form->add()->input('hidden', 'place', $place);
$form->add()->input('hidden', 'data[bundle_id]', (!empty($bundle_id) ? $bundle_id : ''));
$form->add()->input('hidden', 'data[catalogue_id]', $catalogue_id);

$form->add_row(NETCAT_MODULE_NETSHOP_NAME_FIELD)->horizontal()
        ->string('data[name]', $bundle->get('name'));

$row = $form->add_row(NETCAT_MODULE_NETSHOP_MAIL_BUNDLE_TYPE)->horizontal();
$row->select('data[type]', $default_types, $bundle->get('type'));

$form->add_row(NETCAT_MODULE_NETSHOP_UTM_FIELD)->horizontal()
        ->string('data[utm]', $bundle->get('utm'));

echo $form;
?>

<script>
  (function() {
//    $nc
  })();
</script>
