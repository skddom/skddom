<?php

class nc_Catalogue extends nc_Essence {

    protected $db;

    /**
     * Constructor function
     */
    public function __construct() {
        // load parent constructor
        parent::__construct();

        // system superior object
        $nc_core = nc_Core::get_object();
        // system db object
        if (is_object($nc_core->db)) $this->db = $nc_core->db;

        $this->load_all();
        $this->register_event_listeners();

        // Определяем текущий сайт по хосту
        if (is_null($_REQUEST['current_catalogue_id'])) {
            $catalogue = $this->get_by_host_name($nc_core->HTTP_HOST, true);
            $this->set_current_by_id($catalogue['Catalogue_ID']);
        }
        // Устанавливаем текущий сайт по спец. параметру
        elseif ($_REQUEST['current_catalogue_id']) {
            $this->set_current_by_id($_REQUEST['current_catalogue_id']);
        }
    }

    /**
     * @param $id
     * @param bool $reset
     * @return array|bool
     */
    public function set_current_by_id($id, $reset = false) {
        $result = parent::set_current_by_id($id, $reset);

        // установка режима фильтров (на случай, если у сайта есть собственные настройки фильтров)
        $nc_core = nc_core::get_object();
        if ($result) {
            $nc_core->security->init_filters();
        }
        return $result;
    }

    /**
     * Обработчики для обновления и сброса кэша
     */
    protected function register_event_listeners() {
        $event = nc_core::get_object()->event;
        $on_change = array($this, 'update_cache_on_change');
        $event->add_listener(nc_event::AFTER_SITE_CREATED, $on_change);
        $event->add_listener(nc_event::AFTER_SITE_UPDATED, $on_change);
        $event->add_listener(nc_event::AFTER_SITE_ENABLED, $on_change);
        $event->add_listener(nc_event::AFTER_SITE_DISABLED, $on_change);
        $event->add_listener(nc_event::AFTER_SITE_DELETED, array($this, 'update_cache_on_delete'));
    }

    /**
     * Идентификатор текущего сайта
     *
     * @return int
     */
    public function id() {
        return (int) $this->get_current('Catalogue_ID');
    }

    /**
     *
     */
    public function load_all() {
        // load all sites data
        $this->data = (array)$this->db->get_results(
            "SELECT *, 0 AS `_nc_final` FROM `Catalogue` ORDER BY `Priority`",
            ARRAY_A,
            'Catalogue_ID'
        );
    }

    /**
     * @param int $id
     * @param string $item
     * @param bool $reset
     * @return null|string|array
     * @throws Exception
     */
    public function get_by_id($id, $item = "", $reset = false) {
        if (!$id) {
            return null;
        }
        if ($reset) $this->load_all();

        if (!$this->data[$id]) {
            throw new Exception("Catalog with id  ".$id." does not exist");
        }

        if (!$this->data[$id]['_nc_final']) {
            $this->data[$id] = $this->convert_system_vars($this->data[$id], $reset);
            $this->data[$id]['_nc_final'] = 1;
        }

        if ($item) {
            return array_key_exists($item, $this->data[$id]) ? $this->data[$id][$item] : "";
        }

        return $this->data[$id];
    }

    public function get_all() {
        return $this->data;
    }

    /**
     * Get catalogue data by the hostname
     *
     * @param string $host
     * @param bool $current use returned value as current catalogue data
     * @param bool $reset reset stored data in the static variable
     *
     * @return array|null site data associative array
     */
    public function get_by_host_name($host, $current = false, $reset = false) {
        if ($reset) {
            $this->load_all();
        }

        if (!$this->data) {
            return null; // $this->get_by_id(null) вернёт null
        }

        $full_match_site_id = null;
        $host_name_match_site_id = null;
        $first_enabled_site_id = null;
        $first_site_id = null;

        $host = strtolower($host);
        // если порт не 80, $_SERVER['HTTP_HOST'] содержит номер порта
        list($host_without_port) = explode(':', $host, 2);
        $host_has_port_number = ($host != $host_without_port);

        // поиск по доменом и зеркалам
        foreach ($this->data as $site) {
            $domains = array(trim(strtolower($site['Domain'])));
            $mirrors = explode("\n", $site['Mirrors']);
            foreach ($mirrors as $mirror) {
                $mirror = trim(str_replace(array('http://', 'https://', '/'), '', $mirror));
                if ($mirror) {
                    $domains[] = strtolower($mirror);
                }
            }

            // полное совпадение по домену и порту — такое совпадение имеет абсолютный приоритет, дальше не смотрим
            if (in_array($host, $domains)) {
                $full_match_site_id = $site['Catalogue_ID'];
                break;
            }

            // в настройках сайта может быть домен без порта; но совпадение с портом имеет приоритет выше неполного совпадения
            if ($host_has_port_number && in_array($host_without_port, $domains)) {
                $host_name_match_site_id = $site['Catalogue_ID'];
            }

            // запомним ID первого включённого сайта ($this->data отсортированы по Priority)
            if (!$first_enabled_site_id && $site['Checked']) {
                $first_enabled_site_id = $site['Catalogue_ID'];
            }

            // запомним ID первого сайта ($this->data отсортированы по Priority)
            if (!$first_site_id) {
                $first_site_id = $site['Catalogue_ID'];
            }
        }

        $site_id = $full_match_site_id ?: $host_name_match_site_id ?:
                   $first_enabled_site_id ?: $first_site_id;

        $res = $this->get_by_id($site_id);

        if ($current) {
            // set current catalogue data
            $this->current = $res;
        }

        // return result
        return $res;
    }

    public function get_scheme_by_id($id) {
        return $this->get_by_id($id, 'ncHTTPS') === '1' ? 'https' : 'http';
    }

    public function get_scheme_by_host_name($host) {
        $catalogue = $this->get_by_host_name($host);
        return $catalogue['ncHTTPS'] === '1' ? 'https' : 'http';
    }

    public function get_url_by_id($id) {
        $catalogue = $this->get_by_id($id);
        return ($catalogue['ncHTTPS'] === '1' ? 'https' : 'http') . '://' . $catalogue['Domain'];
    }

    public function get_url_by_host_name($host) {
        $catalogue = $this->get_by_host_name($host);
        return ($catalogue['ncHTTPS'] === '1' ? 'https' : 'http') . '://' . $host;
    }

    public function get_mobile($id = 0, $current = false, $item = '') {
        $id = $current ? $this->current['Catalogue_ID'] : intval($id);

        foreach ($this->data as $catalog) {
            if ($catalog['ncMobileSrc'] == $id) {
                $mobile_data = $catalog;
            }
        }

        if ($item) {
            return array_key_exists($item, $mobile_data) ? $mobile_data[$item] : "";
        }

        return $mobile_data;
    }

    /**
     * Возвращает массив с настройками макета дизайна указанного сайта
     * с учётом значений по умолчанию
     * @param $catalogue_id
     * @return array|null
     */
    public function get_template_settings($catalogue_id) {
        $nc_core = nc_core::get_object();
        $site_data = $this->get_by_id($catalogue_id);

        $template_id = $site_data['Template_ID'];

        $nc_a2f = new nc_a2f($nc_core->template->get_custom_settings($template_id));
        $nc_a2f->set_value($site_data['TemplateSettings']);
        $own_settings = $nc_a2f->get_values_as_array();

        $defaults = $nc_core->template->get_settings_default_values($template_id);

        if ($own_settings) {
            return $defaults ? array_merge($defaults, $own_settings) : $own_settings;
        }
        else {
            return $defaults;
        }

    }

    /**
     * Создаёт сайт, раздел главной страницы и страницы 404
     * @param array $site_properties
     * @return int|false ID сайта
     * @throws Exception
     */
    public function create(array $site_properties) {
        $nc_core = nc_core::get_object();

        unset($site_properties['Catalogue_ID']);

        // Значения по умолчанию
        $now = new nc_db_expression("NOW()");
        $next_priority = 1 + $nc_core->db->get_var("SELECT MAX(`Priority`) FROM `Catalogue`");
        $defaults = array(
            'Catalogue_Name' => CONTROL_CONTENT_CATALOUGE_ONESITE,
            'Read_Access_ID' => 1,
            'Write_Access_ID' => 3,
            'Edit_Access_ID' => 3,
            'Checked_Access_ID' => 3,
            'Delete_Access_ID' => 3,
            'Moderation_ID' => 1,
            'Domain' => 'site-' . $next_priority, // cannot be empty, has an unique index on it
            'Mirrors' => '',
            'Priority' => $next_priority,
            'Checked' => 1,
            'Language' => MAIN_LANG,
            'Created' => $now,
            'LastUpdated' => $now,
            'LastModified' => $now,
        );

        foreach ($defaults as $k => $v) {
            if (empty($site_properties[$k])) {
                $site_properties[$k] = $v;
            }
        }

        $site_table = nc_db_table::make('Catalogue');

        while ($site_table->where('Domain', $site_properties['Domain'])->get_value(1)) {
            $site_properties['Domain'] = preg_match('/\d$/', $site_properties['Domain'])
                ? ++$site_properties['Domain']
                : $site_properties['Domain'] . '-1';
        }

        // Вызов обработчиков событий, сохранение сайта
        $nc_core->event->execute(nc_event::BEFORE_SITE_CREATED);
        $site_id = $site_table->insert($site_properties);
        $nc_core->event->execute(nc_event::AFTER_SITE_CREATED, $site_id);

        if (!$site_id) {
            throw new Exception("Unable to create site\n" . $nc_core->db->last_error);
        }

        // Создаём главную страницу
        $title_sub_id = $nc_core->subdivision->create(array(
            'Catalogue_ID' => $site_id,
            'Checked' => 0,
            'Subdivision_Name' => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TITLEPAGE,
        ));

        // Создаём страницу 404
        $e404_sub_id = $nc_core->subdivision->create(array(
            'Catalogue_ID' => $site_id,
            'Checked' => 0,
            'Subdivision_Name' => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NOTFOUND,
        ));

        $site_table->where_id($site_id)->update(array(
            'Title_Sub_ID' => $title_sub_id,
            'E404_Sub_ID' => $e404_sub_id,
        ));

        return $site_id;
    }

    /**
     * @param $site_id
     */
    public function update_cache_on_change($site_id) {
        nc_core::get_object()->file_info->clear_object_cache('Catalogue', $site_id);
        $this->load_all(); // get_by_id() все равно вызывает load_all()
    }

    /**
     * @param $site_id
     */
    public function update_cache_on_delete($site_id) {
        nc_core::get_object()->file_info->clear_object_cache('Catalogue', $site_id);
        foreach ((array)$site_id as $id) {
            unset($this->data[$id]);
        }
    }

}
