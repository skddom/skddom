<?php
if (!class_exists('nc_core')) {
  die;
}
?>
<div class="nc_admin_fieldset_head"><?= NETCAT_MODULE_NETSHOP_MAIL_BUNDLES_EDIT_FIELDS . " '" . $bundle->get('name') . "'" ?></div>
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
    if (empty($attrs['multi'])) {
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
    } else {
      $row = $form->add_row()->horizontal();
      $row->div($xml_field . ": ")->style('display: inline-block; width: 200px; vertical-align: top; margin-top: 5px;');
      $row->div("<a id='add_multi_field_".$good['goods_table']."_".$xml_field."' class='add_multi_field' href=# data-goods-table='" . $good['goods_table'] . "' data-field-name='".$xml_field."'>" . NETCAT_MODULE_NETSHOP_BUTTON_ADD . "</a>")->style('width:auto; display: inline-block;');

      if (isset($good['map_values'][$xml_field]) && is_array($good['map_values'][$xml_field])) {
        foreach($good['map_values'][$xml_field]['name'] as $key => $name) {
          $to_js .= "mmf.add(\$nc('#add_multi_field_".$good['goods_table']."_".$xml_field."'), '".$good['goods_table']."', '".$xml_field."', '".$name."', '".$good['map_values'][$xml_field]['units'][$key]."', default_selects[".$good['goods_table']."], '".$good['map_values'][$xml_field]['field'][$key]."');\n";
        }
      }
    }
  }
}
echo $form;
?>

<script>
  (function() {
      default_selects = {<?php
        foreach ($goods as $good) {
          echo "'" . $good['goods_table'] . "': '";
          echo "<option value=\"-1\">" . NETCAT_MODULE_NETSHOP_SOURCES_FIELD_NOT_SELECTED . "</option>";
          foreach ($good['netcat_fields'] as $field_id => $field_name) {
            echo "<option value=\"" . $field_id . "\">" . $field_name . "</option>";
          }
          echo "',\n";
        }
        ?>};

      nc_market_multifields = function() {
        this.nums = 0;
        this.div_id = 'market_multifields';
      };
      nc_market_multifields.prototype = {
        add: function(link_obj, goods_table, field_name, name, units, field, selected_val) {
          this.nums++;
          if (!name)
            name = '';
          if (!units)
            units = '';
          if (!field)
            field = '';

          var con_id = this.div_id + "_con_" + this.nums;
          link_obj.before("<div class='market_multi_field' id='" + con_id + "'></div>");

          $nc('#' + con_id).append("<div class='name'><?php echo NETCAT_MODULE_NETSHOP_MAIL_MULTI_NAME ?><br /><input name='map_fields["+goods_table+"]["+field_name+"][name][]' type='text' value='" + name + "' /></div>");
          $nc('#' + con_id).append("<div class='field'><?php echo NETCAT_MODULE_NETSHOP_MAIL_MULTI_FIELD ?><br /><select name='map_fields["+goods_table+"]["+field_name+"][field][]'>" + field + "</select></div>");
          $nc('#' + con_id).append("<div class='units'><?php echo NETCAT_MODULE_NETSHOP_MAIL_MULTI_UNITS ?><br /><input name='map_fields["+goods_table+"]["+field_name+"][units][]' type='text' value='" + units + "' /></div>");
          $nc('#' + con_id).append("<div class='drop' onclick='mmf.drop(" + this.nums + ")'><i class='nc-icon nc--remove'></i> " + ncLang.Drop + "</div>");
          $nc('#' + con_id).append("<div style='clear:both;'></div>");
          $nc('#' + con_id).find('.field select').val(selected_val);
        },
        drop: function(id) {
          $nc("#" + this.div_id + "_con_" + id).remove();
        }
      };
      mmf = new nc_market_multifields();

      <?php echo $to_js; ?>

      $nc('.add_multi_field').click(function(e) {
        e.preventDefault();
        goods_table = $nc(this).attr('data-goods-table');
        field_name = $nc(this).attr('data-field-name');
        mmf.add($nc(this), goods_table, field_name, '', '', default_selects[goods_table], '-1');
      });
  })();
</script>
