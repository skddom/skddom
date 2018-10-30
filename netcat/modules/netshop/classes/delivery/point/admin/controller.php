<?php

/**
 *
 */
class nc_netshop_delivery_point_admin_controller extends nc_netshop_admin_controller {

    /** @var string  */
    protected $ui_config_class = 'nc_netshop_delivery_admin_ui';

    /** @var  nc_netshop_delivery_admin_ui */
    protected $ui_config;

    /**
     * @return nc_ui_view
     */
    protected function action_index() {
        $query = "SELECT * FROM `%t%` WHERE `Catalogue_ID` = " . (int)$this->site_id;
        $points = nc_record_collection::load('nc_netshop_delivery_point_local', $query);

        if (count($points)) {
            $view = $this->view('point_list')->with('points', $points);
        } else {
            $view = $this->view('empty_list')
                         ->with('message', NETCAT_MODULE_NETSHOP_SETTINGS_NO_DELIVERY_POINTS_ON_SITE);
        }

        $this->ui_config->set_delivery_location_suffix("point($this->site_id)");
        $this->ui_config->add_create_button("delivery.point.add($this->site_id)");

        return $view;
    }

    /**
     * @return nc_ui_view
     */
    protected function action_add() {
        $this->ui_config->set_delivery_location_suffix("point.add($this->site_id)");
        $this->ui_config->add_save_and_cancel_buttons();
        return $this->view('point_edit')->with('point', new nc_netshop_delivery_point_local());
    }

    /**
     * @param $id
     * @return nc_ui_view
     */
    protected function action_edit() {
        $id = $this->input->fetch_get_post('id');
        $this->ui_config->set_delivery_location_suffix("point.edit($id)");
        $this->ui_config->add_save_and_cancel_buttons();
        return $this->view('point_edit')->with('point', new nc_netshop_delivery_point_local($id));
    }

    /**
     *
     */
    protected function action_save() {
        $point_data = $this->input->fetch_post('point');

        $point = new nc_netshop_delivery_point_local($point_data);
        $point->save();

        $intervals = (array)$this->input->fetch_post('schedule');
        $schedule = new nc_netshop_delivery_schedule();
        foreach ($intervals as $interval) {
            if (strlen($interval['time_from']) && strlen($interval['time_to'])) {
                $delete = $interval['delete'];
                unset($interval['delete']);
                $interval['parent_id'] = $point->get_id();
                $interval = new nc_netshop_delivery_point_interval($interval);
                if (!$delete) {
                    $interval->save();
                    $schedule->add($interval);
                } else if ($interval->get_id()) {
                    $interval->delete();
                }
            }
        }
        $point->set_schedule($schedule);

        $this->redirect_to_index_action();
    }

    /**
     *
     */
    protected function action_toggle() {
        $id = $this->input->fetch_post('id');
        if ($id) {
            $point = new nc_netshop_delivery_point_local($id);
            $point->set('enabled', $this->input->fetch_post('enable'));
            $point->save();
        }
        $this->redirect_to_index_action();
    }

    /**
     *
     */
    protected function action_remove() {
        $id = $this->input->fetch_post('id');
        if ($id) {
            $point = new nc_netshop_delivery_point_local($id);
            $point->delete();
        }
        $this->redirect_to_index_action();
    }

}