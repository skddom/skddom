<?php

/* $Id: array_to_form.inc.php 5946 2012-01-17 10:44:36Z denis $ */
if (!class_exists("nc_System")) die("Unable to load file.");

if (!isset($nc_core)) {
    $nc_core = nc_core::get_object();
}

require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field_checkbox.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field_custom.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field_datetime.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field_divider.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field_file.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field_float.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field_hidden.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field_int.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field_rel.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field_select.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field_string.class.php';
require_once $nc_core->SYSTEM_FOLDER . 'a2f/nc_a2f_field_textarea.class.php';
