<?php

/**
 * Типовой контроллер страниц административного интерфейса модуля.
 */

abstract class nc_bills_admin_controller extends nc_ui_controller {

    protected $use_layout = true;

    protected function init() {
        $this->ui_config = new ui_config(array(
            'treeMode' => 'modules',
        ));
    }

    protected function after_action($result) {
        if ( ! $this->use_layout) {
            return $result;
        }

        BeginHtml(NETCAT_MODULE_BILLS_TITLE, '', '');
        echo $result;
        EndHtml();
        return '';
    }

}