<?php

/**
 * Контроллер мастера перехвата файлов импорта 1С.
 */
class nc_netshop_external_1c_interceptor_admin_controller extends nc_netshop_admin_controller {
    /** @inheritdoc */
    protected $ui_config_class = 'nc_netshop_external_1c_interceptor_admin_ui';

    /**
     * @var nc_netshop_external_1c_interceptor Менеджер перехваченных файлов
     */
    protected $interceptor;

    protected function init() {
        parent::init();
        $this->interceptor = new nc_netshop_external_1c_interceptor();
    }

    /**
     * Главная страница раздела.
     *
     * @return nc_ui_view
     */
    public function action_index() {
        $intercept_url = nc_get_scheme() . '://' . $_SERVER['HTTP_HOST'] . nc_module_path('netshop') . 'import/intercept/1c8.php';
        return $this->view('1c/interceptor')
            ->with('files', $this->interceptor->get_files_list())
            ->with('intercept_url', $intercept_url);
    }

    /**
     * Удаление одного файла.
     */
    public function action_delete_file() {
        $this->interceptor->delete_file($this->input->fetch_post('filename'));
        $this->redirect_to_index_action();
    }

    /**
     * Удаление всех файлов.
     */
    public function action_delete_all_files() {
        $this->interceptor->delete_all_files();
        $this->redirect_to_index_action();
    }

    /**
     * Перенаправляет пользователя на импорт перехваченного файла.
     */
    public function action_import() {
        $file = $this->input->fetch_get('file');
        $path = $this->interceptor->copy_file_to_temporary_folder($file);

        $location = nc_module_path('netshop') . 'import.php?file=' . urlencode($path);

        ob_clean();
        header('Location: ' . $location);
        die;
    }
}
