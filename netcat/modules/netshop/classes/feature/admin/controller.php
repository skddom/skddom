<?php


class nc_netshop_feature_admin_controller extends nc_netshop_admin_controller {

    /** @var string  Должен быть задан, или должен быть переопределён метод before_action() */
    protected $ui_config_class = 'nc_netshop_feature_admin_ui';


    protected function init() {
        $this->bind('index', array(
            'controller' => $this->input->fetch_post_get('controller'),
            'action' => $this->input->fetch_post_get('action'),
        ));
    }

    /**
     * @param string $requested_controller_name
     * @param string $requested_action_name
     * @return nc_ui_view
     */
    protected function action_index($requested_controller_name, $requested_action_name) {
        return $this->view('default');
    }

}