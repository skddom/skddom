<?php

if (!class_exists('nc_core')) { die; }

echo $ui->controls->site_select($catalogue_id);

?>
<div class="nc_admin_fieldset_head"><?= NETCAT_MODULE_NETSHOP_MAIL_BUNDLES; ?></div>
<?php

$table = $ui->table()->wide()->striped()->bordered()->hovered();

$thead = $table->thead(); // chaining produces invalid code

$thead->th(NETCAT_MODULE_NETSHOP_MAIL_BUNDLE_ID)->compact()->text_center();
$thead->th(NETCAT_MODULE_NETSHOP_MAIL_BUNDLES_NAME);
$thead->th(NETCAT_MODULE_NETSHOP_MAIL_BUNDLES_EXPORT_URL)->text_center()->wide();
$thead->th(NETCAT_MODULE_NETSHOP_MAIL_BUNDLES_UPDATED)->compact();
$thead->th(NETCAT_MODULE_NETSHOP_MAIL_BUNDLES_EDIT_FIELDS)->compact();
$thead->th()->compact();

$tr = $table->row();
$tr->id = $tr->td();
$tr->name = $tr->td();
$tr->export_url = $tr->td()->text_center();
$tr->last_updated = $tr->td();
$tr->edit_fields = $tr->td()->text_center();
$tr->delete_button = $tr->td()->text_center();

foreach ($bundles as $row) {
    $bundle = new $bundle_class;
    $bundle->set_values_from_database_result($row);
    $bundle_id = $bundle->get_id();
    $edit_link = "$link_prefix.bundle.edit($bundle_id)";

    $post_actions_params = array('controller' => $controller_name, 'bundle_id' => $bundle_id, 'place' => $place);

    $tr->id->text($bundle_id);
    $tr->name->text(
        "<a href='$edit_link' target='_top' class='nc-netshop-list-item-title'>" . $bundle->get('name') . "</a>"
    );
    $tr->export_url->text(nc_get_scheme() . '://' . $domain . nc_module_path('netshop') . 'export/mail/bundle' . $bundle_id . '.xml');
    $tr->last_updated->text($bundle->get('last_updated'));
    $edit_fields_link = "$link_prefix.bundle.edit_fields($bundle_id)";
    $tr->edit_fields->text( "<a href='$edit_fields_link' target='_top' class='nc-netshop-list-item-title'>" . NETCAT_MODULE_NETSHOP_ACTION_EDIT . "</a>");

    $tr->delete_button->text($ui->controls->delete_button(
        sprintf(NETCAT_MODULE_NETSHOP_MAIL_CONFIRM_DELETE, $bundle->get('name')),
        $post_actions_params
    ));

    $table->add_row($tr);
}


echo $table, "<br>";
