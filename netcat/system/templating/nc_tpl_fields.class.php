<?

class nc_tpl_fields {

    public $standart = null;
    public $Settings = array();
    private $template = null;
    private $fields = array(
            'Template' => array(
                    'Header'   => null,
                    'Footer'   => null,
                    'Settings' => null),

            'Class' => array(
                    'FormPrefix'           => null,
                    'RecordTemplate'       => null,
                    'FormSuffix'           => null,
                    'RecordTemplateFull'   => null,
                    'Settings'             => null,
                    'AddTemplate'          => null,
                    'AddCond'              => null,
                    'AddActionTemplate'    => null,
                    'EditTemplate'         => null,
                    'EditCond'             => null,
                    'EditActionTemplate'   => null,
                    'CheckActionTemplate'  => null,
                    'DeleteTemplate'       => null,
                    'DeleteCond'           => null,
                    'DeleteActionTemplate' => null,
                    'FullSearchTemplate'   => null,
                    'SearchTemplate'       => null,
                    'RecordMockData' => array(
                        'is_optional' => true,
                    ),
                    'SiteStyles' => array(
                        'extension' => '.css',
                        'is_optional' => true
                    ),
                ),

            'Widget_Class' => array(
                    'Template'         => null,
                    'Settings'         => null,
                    'AddForm'          => null,
                    'EditForm'         => null,
                    'AfterSaveAction'  => null,
                    'BeforeSaveAction' => null,
            )
    );

    public function __construct(nc_tpl $template) {
        $this->template = $template;
        $this->standart = $this->fields[$this->template->type];
        if (!is_array($this->standart)) {
            $this->standart = $this->{$this->get_method_name()}();
        }
    }

    private function get_method_name() {
        $method_name = "get_{$this->template->id}_fields";
        return !method_exists('nc_tpl_fields', $method_name) ? 'get_module_fields' : $method_name;
    }

    private function get_comments_fields() {
        $default_path = $this->template->path_to_root_folder.'comments/0';

        $files = scandir(nc_standardize_path_to_folder($default_path));
        $fields = array();

        foreach ($files as $file) {
            if (strpos($file, '.') === 0) { continue; }
            $fields[str_replace($this->template->extension, '', $file)] = null;
        }

        return $fields;
    }

    private function get_module_fields () {
        $files = scandir(nc_standardize_path_to_folder($this->template->absolute_path));
        $fields = array();

        foreach ($files as $file) {
            if ($file{0} == '.') {
                continue;
            }

            $basename = str_replace($this->template->extension, '', $file);

            // Игнорируем файлы без расширений
            if ($basename != $file) {
                $fields[$basename] = null;
            }
        }

        return $fields;
    }

    protected function get_field_file_extension($field) {
        return isset($this->standart[$field]['extension'])
                   ? $this->standart[$field]['extension']
                   : $this->template->extension;
    }

    public function get_path($field) {
        $path = $this->template->absolute_path . $field . $this->get_field_file_extension($field);
        nc_Core::get_object()->page->update_last_modified_if_newer(
            $this->get_last_modified_timestamp_by_path($path),
            'template'
        );
        return $path;
    }

    public function get_parent_path($field) {
        return nc_get_path_to_parent_folder($this->template->absolute_path) . $field . $this->get_field_file_extension($field);
    }

    public function clear_all() {
        $this->standart = $this->fields[$this->template->type];
        $this->Settings = null;
    }

    /**
     * @param string $path
     * @return int
     */
    public function get_last_modified_timestamp_by_path($path) {
        if (file_exists($path) && filemtime($path)) {
            return (int)filemtime($path);
        }

        return 0;
    }

}