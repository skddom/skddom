<?php

/***************************************************************************
    nc_netshop_form_data
***************************************************************************/

class nc_netshop_form_data {
    public $_data = array();
    public $_titles = array();
    public $_editable;
    public $_suffix;
    public $_preview;
    public function __construct($data = array(), $editable = false) {
        $this->_data      = $data;
        $this->_edit_mode = $editable && nc_netshop::get_instance()->forms->edit_mode;
    }
    public function set_titles($data){
        $this->_titles = $data;
    }
    public function __get($name){
        // $val = isset($this->_data[$name]) ? $this->_data[$name] : $name;
        $val = isset($this->_data[$name]) ? $this->_data[$name] : null;
        $title = isset($this->_titles[$name]) ? $this->_titles[$name] : null;

        if ($this->_edit_mode) {
            return nc_core::get_object()->ui->html->input('text',$name)->value($val)->placeholder($title);
        }

        return $val ? $val : '&nbsp;';
    }
}

