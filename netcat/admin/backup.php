<?php

/*--------------------------------------------------------------------------
    Параметры скрипта ($_GET):
----------------------------------------------------------------------------
    [mode]:
        export - режим экспорта (default);
        import - режим импорта;

    [type]: тип данных (файлы netcat/system/backup/types/nc_backup_*)
        class - Компоненты

    [id] (mode:export): Идентификатор экспортируемых данных

    [raw] (mode:export):
        0 - вызов диалога сохранения файла (default);
        1 - выводит файл;
--------------------------------------------------------------------------*/

$NETCAT_FOLDER = realpath(dirname(__FILE__) . '/../..') . DIRECTORY_SEPARATOR;

defined('DS') || define('DS', DIRECTORY_SEPARATOR);

require_once $NETCAT_FOLDER . 'vars.inc.php';
require_once $ADMIN_FOLDER . 'function.inc.php';

//--------------------------------------------------------------------------

$UI_CONFIG = new ui_config(array(
    'headerText'   => TOOLS_DATA_BACKUP,
    'treeMode'     => 'sitemap',
    'locationHash' => "tools.databackup.export",
    'tabs'         => array(
        array('id' => 'index', 'caption' => TOOLS_DATA_BACKUP_SYSTEM),
    ),
    'toolbar'         => array(
        array(
            'id'       => 'export',
            'caption'  => TOOLS_EXPORT,
            'location' => 'tools.databackup.export',
            'group'    => "group1"
        ),
        array(
            'id'       => 'import',
            'caption'  => TOOLS_IMPORT,
            'location' => 'tools.databackup.import',
            'group'    => "group1"
        ),
    ),
    // 'activeToolbarButtons' => array('export'),
    'activeTab'    => 'index',
));
$UI_CONFIG->actionButtons[] = array(
    // 'id'   => 'nc_dashboard_add_widget',
    'caption' => WIDGET_ADD_CONTINUE,
    'action'  => 'nc.view.main(\'form\').submit(); return false;',
    'align'   => 'right',
    'style'   => 'nc_dashboard_add_widget', // className
);

//--------------------------------------------------------------------------

try {

    // Проверка входных данных ($_GET)

    $id    = $nc_core->input->fetch_get_post('id');
    $type  = (string)preg_replace('@[^a-z0-9_]+@i', '', $nc_core->input->fetch_get_post('type'));
    $raw   = (bool)$nc_core->input->fetch_get_post('raw');
    $mode  = $nc_core->input->fetch_get_post('mode');
    $debug = (bool)$nc_core->input->fetch_get_post('debug');

    if ( ! $mode) {
        $mode = 'export';
    }

    $UI_CONFIG->activeToolbarButtons = array($mode);
    $UI_CONFIG->locationHash = 'tools.databackup.' . $mode;

    // if ($mode == 'export' && ! $id) {
    //     throw new Exception(TOOLS_COPYSUB_ERROR_PARAM, 1);
    // }

    // // Проверка токена
    // if ( $mode == 'export' AND ! $nc_core->token->verify()) {
    //     throw new Exception(NETCAT_TOKEN_INVALID, 1);
    // }

    //--------------------------------------------------------------------------

    require_once $SYSTEM_FOLDER . 'backup' . DS . 'nc_backup.class.php';

    // создаем объект импорта/экспорта соответствующий типу импортируемых данных
    $backup = new nc_backup();

    //--------------------------------------------------------------------------

    switch ($mode) {

        // ИМПОРТ
        case 'import':
            $file = $nc_core->input->fetch_files('import');
            $step = $nc_core->input->fetch_get_post('step');

            if ($file || $step) {

                if ($file) {
                    if ( ! $type) {
                        $type = $backup->detect_type($file);
                    }
                    $file = $file['tmp_name'];
                }

                if ( ! $type) throw new Exception("Type not set", 1);

                $save_ids = (bool)$nc_core->input->fetch_post('save_ids');
                $backup->config('save_ids', $save_ids);

                // Пошаговый импорт (архив)
                if ($backup->$type->step_mode()) {
                    $view      = $nc_core->ui->view($ADMIN_FOLDER . 'views/databackup/steps');
                    $export_id = $nc_core->input->fetch_get_post('export_id');

                    if ($step) {
                        echo json_safe_encode($backup->$type->import_step($file, $step, $export_id));
                        exit;
                    }
                    else {
                        $step = current(array_keys($backup->$type->import_steps()));
                    }

                    $backup->$type->import_init($file);

                    $view->with('cross_data', $backup->$type->cross_data());
                    $view->with('steps',      $backup->$type->import_steps());
                    $view->with('export_id',  $backup->$type->export_id());
                    $view->with('id',         $backup->$type->id());
                    $view->with('mode',       'import');
                    $view->with('type',       $type);
                    $view->with('step',       $step);

                    echo BeginHtml() . $view->make() . EndHtml();
                }

                else {
                    $result = $backup->import($type, $file);

                    if ($result['redirect']) {
                        ob_get_length() && ob_clean();
                        header('Location: ' . $result['redirect']);
                        exit;
                    }

                    $view = $nc_core->ui->view($ADMIN_FOLDER . 'views/databackup/import_result');
                    $view->with('result', $result);
                    echo BeginHtml() . $view->make() . EndHtml();
                }
            }

            // Форма импортирования
            else {
                $view = $nc_core->ui->view($ADMIN_FOLDER . 'views/databackup/import');
                $view->with('debug', $debug);
                echo BeginHtml() . $view->make() . EndHtml();
            }

            break; //end import



        // ЭКСПОРТ
        case 'export':
            switch (true) {
                // Выбор данных для экспорта
                case !$type || is_null($id):
                    $view = $nc_core->ui->view($ADMIN_FOLDER . 'views/databackup/export');

                    $backup->file_rotation();

                    $view->with('types',              array_merge(array(''=>''), $backup->types()));
                    $view->with('backup',             $backup);
                    $view->with('export_files',       $backup->export_files());
                    $view->with('export_limit_size',  $backup->config('export_limit_size') * 1024 * 1024);
                    $view->with('export_limit_count', $backup->config('export_limit_count'));

                    echo BeginHtml() . $view->make() . EndHtml();
                    break;

                // Пошаговый экспорт (архив)
                case $backup->$type->step_mode():
                    $view      = $nc_core->ui->view($ADMIN_FOLDER . 'views/databackup/steps');
                    $step      = $nc_core->input->fetch_get_post('step');
                    $export_id = $nc_core->input->fetch_get_post('export_id');

                    if ($step) {
                        echo json_safe_encode($backup->$type->export_step($id, $step, $export_id));
                        break;
                    }
                    else {
                        $step = current(array_keys($backup->$type->export_steps()));
                    }

                    $view->with('cross_data', $backup->$type->cross_data());
                    $view->with('steps',      $backup->$type->export_steps());
                    $view->with('export_id',  '');
                    $view->with('mode',       'export');
                    $view->with('step',       $step);
                    $view->with('type',       $type);
                    $view->with('id',         $id);

                    echo BeginHtml() . $view->make() . EndHtml();
                    break;

                // Простой экспорт (xml-файл)
                default:
                    if ($raw) {
                        // header("Content-type: text/plane");
                        header("Content-type: text/xml");
                        echo $backup->export($type, $id);
                    }
                    else {
                        $backup->export_download($type, $id); // Сохранение файла
                    }
                    break;
            }
            break;


        case 'index':
            $view = $nc_core->ui->view($ADMIN_FOLDER . 'views/databackup/index');
            echo BeginHtml() . $view->make() . EndHtml();
            break;

        case 'get_form':
            if (!$type) {
                throw new Exception('Type not set' , 1);
            }
            echo $backup->$type->export_form();
            break;

        case 'remove_export_files':
            $backup->remove_export_files();
            header('Location: ' . $ADMIN_PATH . 'backup.php?mode=export');
            exit;
            break;

        default: throw new Exception('Unknown mode: ' . $mode, 1);
    }
}
// FATAL ERROR:
catch (Exception $e) {
    header("Content-type: text/html");
    BeginHtml();
    nc_print_status($e->getMessage(), 'error');
    EndHtml();
}