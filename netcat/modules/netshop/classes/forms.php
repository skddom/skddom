<?php

class nc_netshop_forms {

    //--------------------------------------------------------------------------

    protected static $instance;

    //--------------------------------------------------------------------------

    /** @var nc_netshop  */
    protected $netshop; // ссылка на объект nc_netshop
    /** @var stdClass  */
    protected $objects;

    public $edit_mode = false;

    public $FORMS_FOLDER;
    public $FORMS_TEMPLATE_FOLDER;

    //--------------------------------------------------------------------------

    public function __construct(nc_netshop $netshop) {
        /* @todo CHECK: MIGRATION TO NEW NETSHOP CLASS */
        $this->netshop = $netshop;

        $this->objects = new stdClass;

        $this->FORMS_FOLDER          = realpath(dirname(__FILE__) . '/../forms') . DIRECTORY_SEPARATOR;
        $this->FORMS_TEMPLATE_FOLDER = $this->FORMS_FOLDER . 'templates' . DIRECTORY_SEPARATOR;

        $this->init_forms();
    }

    //--------------------------------------------------------------------------

    private function __clone() {}
    private function __wakeup() {}

    //--------------------------------------------------------------------------

    public function init_forms() {
        $form_files = scandir($this->FORMS_FOLDER);

        foreach ($form_files as $file) {
            if ( ! is_file($this->FORMS_FOLDER . $file)) continue;
            if ( pathinfo($file, PATHINFO_EXTENSION) != 'php' ) continue;

            $form_name  = substr($file, 0, strpos($file, '.'));
            $form_class = $form_name . '_netshop_form';
            require $this->FORMS_FOLDER . $file;

            if (class_exists($form_class)) {
                $this->objects->$form_name = new $form_class($this->netshop);
            }
        }
    }

    //--------------------------------------------------------------------------

    public function get_objects() {
        return $this->objects;
    }

    //--------------------------------------------------------------------------
}

