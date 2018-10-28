<?php
if (!class_exists('nc_core')) {
  die;
}
?>
<div class="nc_admin_fieldset_head"><?= NETCAT_MODULE_NETSHOP_GOOGLE_MARKET_BUNDLES_EDIT_FIELDS . " '" . $bundle->get('name') . "'" ?></div>
<?php
$form = $ui->form("?controller=$controller_name&action=save_fields")->vertical();
$form->add()->input('hidden', 'catalogue_id', $catalogue_id);
$form->add()->input('hidden', 'action', 'save_fields');
$form->add()->input('hidden', 'place', $place);
$form->add()->input('hidden', 'data[bundle_id]', $bundle_id);
$form->add()->input('hidden', 'data[catalogue_id]', $catalogue_id);

$to_js = "";
foreach ($goods as $good) {
  $row = $form->add_row('<b style="display: block; margin-top: 30px;">' . $good['component_name'] . ' [' . $good['goods_table'] . ']</b>')->horizontal();

  foreach ($good['xml_fields'] as $xml_field => $attrs) {
      $options_arr = array('-1' => NETCAT_MODULE_NETSHOP_SOURCES_FIELD_NOT_SELECTED);
      $selected = "-1";

      foreach ($good['netcat_fields'] as $field_id => $field_name) {
        $options_arr[$field_id] = $field_name;
        if (isset($good['map_values'][$xml_field]) && $good['map_values'][$xml_field] == $field_id) {
          $selected = $field_id;
        }
      }
      $row = $form->add_row()->horizontal();
      $row->div($xml_field . ": ")->style('display: inline-block; width: 200px;');
      $row->select('map_fields[' . $good['goods_table'] . '][' . $xml_field . ']', $options_arr, $selected)
              ->style('width: 200px;');
    
  }
}
echo $form;