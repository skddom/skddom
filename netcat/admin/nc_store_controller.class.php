<?php


define('NC_STORE_DOMAIN', 'store.netcat.ru/nc/');

class nc_store_controller extends nc_ui_controller {

    protected $is_naked = false;

    //-------------------------------------------------------------------------

    protected function init() {
        // $this->backup = $this->nc_core->backup;

        $this->bind('index',   array('tab'));
        $this->bind('install', array('file'));

        //                   $this->bind('export_form', array('type'));
        //                   $this->bind('export_run',  array('type', 'id', 'raw'));

        // $this->bind('import', array());
        // $this->bind('import_run', array('file', 'save_ids'));
    }

    //-------------------------------------------------------------------------

    protected function init_view($view) {
        // $view->with('backup',     $this->backup);
        // $view->with('action_url', $this->nc_core->SUB_FOLDER . $this->nc_core->HTTP_ROOT_PATH . 'action.php?ctrl=admin.backup&action=');
    }

    //-------------------------------------------------------------------------

    protected function after_action($result) {
        // JSON
        if (is_array($result)) {
            return json_safe_encode($result);
        }
        // With template
        if (!$this->is_naked) {
            return BeginHtml() . $result . EndHtml();
        }

        return $result;
    }

    //-------------------------------------------------------------------------

    public function action_index($tab = null) {
        // $this->is_naked = true;
        // idebug(nc_core());exit;
        $this->ui_config();
        return $this->view('store/index')->with('tab', $tab);
    }

    //-------------------------------------------------------------------------

    public function action_install($file) {
        $file = 'http://' . str_replace(':', '/', $file);
        $tmp_name = md5($file) . '.tgz';
        $tmp_file = nc_core()->TMP_FOLDER . $tmp_name;

        $result = null;

        if (file_put_contents($tmp_file, fopen($file, 'r'))) {
            try {
                $result = nc_core()->backup->import($tmp_file, array(
                    'save_ids'=>false
                ));
            } catch (Exception $e) {
                nc_print_status($e->getMessage(), 'error');
            }
            unlink($tmp_file);
        }

        return $this->view('store/install_complete')->with('result', $result);
    }

    //-------------------------------------------------------------------------

    protected function ui_config($mode = 'index') {
        $this->ui_config = new ui_config(array(
            // 'headerText'   => TOOLS_STORE,
            'headerText'   => '',
            'treeMode'     => 'sitemap',
            'tabs'         => array(
                array('id' => 'index', 'caption' => TOOLS_DATA_BACKUP_SYSTEM),
                // array('id' => 'my',    'caption' => TOOLS_DATA_BACKUP_SYSTEM),
            ),
            'toolbar'         => array(
                // array(
                //     'id'       => 'export',
                //     'caption'  => TOOLS_EXPORT,
                //     'location' => 'tools.databackup.export',
                //     'group'    => "group1"
                // ),
                // array(
                //     'id'       => 'import',
                //     'caption'  => TOOLS_IMPORT,
                //     'location' => 'tools.databackup.import',
                //     'group'    => "group1"
                // ),
            ),
            // 'activeToolbarButtons' => array('export'),
            'activeTab'    => 'index',
        ));
        // $this->ui_config->actionButtons[] = array(
        //     // 'id'   => 'nc_dashboard_add_widget',
        //     'caption' => WIDGET_ADD_CONTINUE,
        //     'action'  => 'nc.view.main(\'form\').submit(); return false;',
        //     'align'   => 'right',
        //     'style'   => 'nc_dashboard_add_widget', // className
        // );
        // if ($mode) {

        // }
        // $this->ui_config->activeToolbarButtons = array($mode);
        // $this->ui_config->locationHash = 'tools.databackup.' . $mode;
    }
}