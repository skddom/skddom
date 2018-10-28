<?php

/**
 * Класс для врезок (вспомогательных шаблонов, partials) в макетах дизайна
 */
class nc_tpl_template_partial {

    /** @var int счётчик partials с отложенной загрузкой */
    static protected $partial_counter = 0;

    /** @var array переменные, которые не могут быть установлены через $data (в дополнение свойствам nc_core) */
    static protected $protected_variables = array(
        'GLOBALS' => true, '_SERVER' => true, '_GET' => true, '_POST' => true, '_FILES' => true,
        '_COOKIE' => true, '_SESSION' => true, '_REQUEST' => true, '_ENV' => true,
        'MODULE_VARS' => true, 'NETCAT_FOLDER' => true, 'admin_url_prefix' => true, 'classPreview' => true,
        'AUTH_USER_ID' => true, 'AUTH_USER_GROUP' => true, 'current_user' => true, 'perm' => true,
        'current_catalogue' => true, 'current_sub' => true, 'current_cc' => true, 'cc_array' => true,
        'sub_level_count' => true, 'parent_sub_tree' => true,
        'cc_keyword' => true, 'nc_parent_template_folder_path' => true, 'subHost' => true,
        'subLink' => true, 'ccLink' => true,
        'fullLink' => true, 'fullDateLink' => true, 'fullRSSLink' => true, 'msgLink' => true,
        'addLink' => true, 'editLink' => true, 'deleteLink' => true, 'dropLink' => true, 'checkedLink' => true,
        'versionLink' => true, 'subscribeLink' => true, 'subscribeMessageLink' => true, 'searchLink' => true,
        'nc_prev_object' => true, 'nc_next_object' => true,
        'f_AdminCommon' => true, 'f_AdminButtons' => true,
    );

    /** @var  int */
    protected $template_id;

    /** @var string  */
    protected $keyword;

    /** @var string  */
    protected $partial_file;

    /** @var array  */
    protected $data = array();

    // --- Свойства, связанные с отложенной загрузкой ---

    /** @var bool использовать отложенную загрузку */
    protected $defer = false;

    /** @var string что выводится вместо partial, когда $defer = true */
    protected $stub = '';

    /** @var bool хранение в браузере в localStorage */
    protected $store_in_browser = false;

    /** @var bool всегда перезагружать partial, даже если он есть в localStorage */
    protected $always_reload = false;

    /**
     *
     * @param int $template_id
     * @param $partial_file
     * @param array $data
     */
    public function __construct($template_id, $partial_file, array $data = array()) {
        $this->template_id = nc_core::get_object()->template->get_root_id($template_id);
        $this->keyword = basename($partial_file, '.' . nc_template::TEMPLATE_EXT);

        if (nc_check_file($partial_file)) {
            $this->partial_file = $partial_file;
        } else {
            trigger_error('File not found: ' . htmlspecialchars($partial_file), E_USER_WARNING);
        }

        foreach ($data as $key => $value) {
            $this->with($key, $value);
        }
    }

    /**
     * @return bool
     */
    public function exists() {
        return $this->partial_file ? true : false;
    }

    /**
     * Рендеринг шаблона
     *
     * @return string
     */
    public function make() {
        if (!$this->partial_file) {
            return '';
        }

        $nc_partial_async = false; // фрагмент загружен отдельно через /netcat/partial.php?
        $nc_partial_inside_partial = false; // фрагмент внутри другого фрагмента?

        // Переменные для использования внутри partial
        extract($GLOBALS);
        extract($this->data);

        $nc_core = nc_core::get_object();
        $db = $nc_core->db;

        ob_start();

        // Разметка для partial с разрешённой асинхронной загрузкой
        $nc_template_partial_id = ($nc_partial_inside_partial ? 'I' : '') . (++self::$partial_counter);
        $nc_add_partial_markup =
            $this->is_async_load_enabled() &&
            (!$nc_partial_async || $nc_partial_inside_partial) &&
            !$nc_core->inside_admin;

        if ($nc_add_partial_markup) {
            $nc_core->page->add_deferred_partials_script();

            echo "<!-- nc_template_partial $nc_template_partial_id " .
                nc_array_json(array(
                    'template' => $this->template_id,
                    'partial' => $this->keyword,
                    'data' => $this->data ?: null, // JS полагается на то, что вместо пустого объекта будет null
                    'defer' => $this->defer,
                    'reload' => $this->always_reload,
                    'store' => $this->store_in_browser,
                )) .
                ' -->';
        }

        // Вывод контента partial
        if ($this->defer) {
            echo $this->stub;
        } else {
            include $this->partial_file;
        }

        // Разметка для partial с разрешённой отложенной загрузкой
        if ($nc_add_partial_markup) {
            echo "<!-- /nc_template_partial $nc_template_partial_id -->";
        }

        return ob_get_clean() ?: '';
    }

    /**
     * Возвращает сведения о врезке из таблицы Template_Partial
     *
     * @param string $property
     * @return mixed
     */
    protected function get_meta($property) {
        return nc_core::get_object()->template->get_partials_data($this->template_id, $this->keyword, $property);
    }

    /**
     * Проверяет, разрешено ли использование врезки отдельно от макета дизайна
     * (через /netcat/partial.php).
     *
     * @return bool
     */
    public function is_async_load_enabled() {
        return (bool)$this->get_meta('EnableAsyncLoad');
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->make();
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value) {
        $this->with($name, $value);
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name) {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    /**
     * Присвоение переменной шаблона
     * @param  string $key  Название переменной
     * @param  mixed $value Значение переменой
     * @return $this
     */
    public function with($key, $value) {
        if (!isset(self::$protected_variables[$key]) && nc_core::get_object()->get_variable($key) === null) {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function value($name, $default = null) {
        return isset($this->data[$name]) ? $this->data[$name] : $default;
    }

    /**
     * @param $name
     * @param array $data
     * @return nc_tpl_template_partial
     */
    public function partial($name, $data = array()) {
        $data = array_merge(array('nc_partial_inside_partial' => true), $this->data, $data);
        return nc_core::get_object()->template->get_file_template($this->template_id)->partial($name, $data);
    }

    /**
     * Устанавливает (по умолчанию — включает) отложенную загрузку врезки
     *
     * @param bool $defer
     * @return $this
     */
    public function defer($defer = true) {
        $this->defer = (bool)$defer;
        return $this;
    }

    /**
     * Устанавливает «заглушку», когда включена отложенная загрузка
     *
     * @param string $stub
     * @return $this
     */
    public function set_stub($stub) {
        $this->stub = $stub;
        return $this;
    }

    /**
     * Синоним для метода set_stub()
     *
     * @param $stub
     * @return nc_tpl_template_partial
     */
    public function stub($stub) {
        return $this->set_stub($stub);
    }

    /**
     * Включает (по умолчанию и когда аргумент равен true) или выключает (если аргумент равен false)
     * хранение результата, загруженного отдельно от страницы, в браузере в sessionStorage
     *
     * @param bool $store_in_browser
     * @return $this
     */
    public function store_in_browser($store_in_browser = true) {
        $this->store_in_browser = (bool)$store_in_browser;
        return $this;
    }

    /**
     * Переключает (по умолчанию включает) режим принудительного обновления partial
     * при отложенной загрузке после загрузки страницы
     *
     * @param bool $always_reload
     * @return $this
     */
    public function always_reload($always_reload = true) {
        $this->always_reload = (bool)$always_reload;
        return $this;
    }

}