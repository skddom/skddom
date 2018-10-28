<?php

class nc_tpl_component_view {
    private $template = null;
    private static $instanse = null;

    public function __construct($path, nc_Db $db) {
        self::$instanse = $this;
        $this->template = new nc_tpl($path, $db);
    }

    public function load($id, $path = null, $hash = null) {
        $this->template->load($id, 'Class', $path);
        if ($hash) {
            nc_tpl_parser::main2parts($this->template, $hash);
        }
    }

    public function get_fields() {
        return $this->template->fields->standart;
    }

    public function get_field_path($field) {
        return $this->template->fields->get_path($field);
    }

    public function get_parent_field_path($field) {
        return $this->template->fields->get_parent_path($field);
    }

    public static function get_instanse() {
        return self::$instanse;
    }
}