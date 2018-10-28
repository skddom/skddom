<?php

class nc_backup_driver_base {

    //--------------------------------------------------------------------------

    const SQL_OR  = 'OR';
    const SQL_AND = 'AND';

    //--------------------------------------------------------------------------

    protected $backup;
    protected $db;

    // Словарь соответствия идентификаторов
    protected $dict         = array();
    protected $export_dict  = array();
    protected $export_info  = array();
    protected $dict_exclude = array();
    protected $cross_data   = array();

    protected $info;

    // Результат экспорта/импорта
    protected $result;

    // Идентификатор текущего экспорта
    protected $export_id;

    // Этапы
    protected $export_steps = false;
    protected $import_steps = false;

    protected $save_ids   = false;
    protected $require_id = true;

    protected $version  = 1;
    protected $type     = '';
    protected $name     = '';
    protected $id       = 0;
    protected $new_id   = 0;
    protected $file     = '';

    //--------------------------------------------------------------------------

    public function __construct($backup) {
        @set_time_limit(0);

        $this->backup   = $backup;
        $this->nc_core  = nc_core::get_object();
        $this->db       = $this->nc_core->db;
        $this->save_ids = (bool)$this->backup->config('save_ids');

        if (!$this->type) {
            $class = get_class($this);
            if ($class != 'nc_backup_driver') {
                $this->type = substr($class, strlen('nc_backup_'));
            }
        }
        if (!$this->name) {
            $this->name = ucfirst($this->type);
        }

        if ($this->export_steps) {
            $this->export_steps = array('init'=>TOOLS_DATA_BACKUP_STEP_INIT) + $this->export_steps + array('end'=>TOOLS_DATA_BACKUP_STEP_END);
        }

        if ($this->import_steps) {
            $this->import_steps = array('init'=>TOOLS_DATA_BACKUP_STEP_INIT) + $this->import_steps + array('end'=>TOOLS_DATA_BACKUP_STEP_END);
        }

        $this->init();
    }



    //--------------------------------------------------------------------------
    // PUBLIC API
    //--------------------------------------------------------------------------

    // public function validate($exceprion = false) {
    //     $valid = true;

    //     if ($this->require_id && !$id) {
    //         $valid = false;
    //         if ($exceprion) throw new Exception("ID id required!", 1);
    //     }

    //     return $valid;
    // }

    //--------------------------------------------------------------------------

    public function import($file) {
        $this->import_init($file);
        $this->import_process();
        return $this->import_result();
    }

    //--------------------------------------------------------------------------

    public function import_step($file, $step, $export_id) {
        $this->import_init($file, $export_id);
        $this->load_export_info();

        $fn        = 'import_step_' . $step;
        $next_step = false;
        foreach ($this->import_steps as $key => $name) {
            if ($next_step) {
                $this->result('next_step', $key);
                break;
            }
            if ($key == $step) $next_step = true;
        }

        $this->$fn();

        if ($this->cross_data) {
            $this->result('cross_data', $this->cross_data);
        }

        return $this->result;
    }

    //--------------------------------------------------------------------------

    public function export($id, $export_id = null, $save = false) {
        $this->export_init($id, $export_id);
        $this->load_export_info();
        $this->export_process();

        if ($save) {
            return $this->save_result($this);
        }
        else {
            return $this->export_result();
        }
    }

    //--------------------------------------------------------------------------

    public function export_step($id, $step, $export_id) {
        $this->export_init($id, $export_id);
        $this->load_export_info();

        $fn        = 'export_step_' . $step;
        $next_step = false;
        foreach ($this->export_steps as $key => $name) {
            if ($next_step) {
                $this->result('next_step', $key);
                break;
            }
            if ($key == $step) $next_step = true;
        }

        $this->$fn();

        if ($this->cross_data) {
            $this->result('cross_data', $this->cross_data);
        }

        return $this->result;
    }

    //--------------------------------------------------------------------------

    public function load_export_info() {
        if ( ! $this->export_id) return;

        $dict_file = $this->export_dir() . 'export_info.php';
        if (file_exists($dict_file)) {
            $this->export_info = include $dict_file;
            $this->export_dict =& $this->export_info['dict'];
        }

        return $this->export_info;
    }

    //--------------------------------------------------------------------------

    public function save_export_info() {
        if ( ! $this->export_id) return;

        if ($this->export_dict) {
            $this->export_info['dict'] = $this->export_dict;
        }
        if ($this->export_info) {
            $this->save_file('export_info.php', '<?php return ' . var_export($this->export_info, true) . ';', true);
        }
    }

    //--------------------------------------------------------------------------

    public function cross_data($keys = array()) {
        $keys = (array)$keys;

        foreach ($keys as $key) {
            if (isset($_REQUEST[$key])) {
                $this->cross_data[$key] = $_REQUEST[$key];
            }
        }
        return $this->cross_data;
    }

    //--------------------------------------------------------------------------

    public function get_type($file) {
        $this->import_init($file);
        return $this->info->export->type;
    }

    //--------------------------------------------------------------------------

    public function id($id = null) {
        if ($id !== null) {
            $this->id = $id;
        }
        return $this->id;
    }

    //--------------------------------------------------------------------------

    public function file($file = null) {
        if ($file !== null) {
            $this->file = $file;
        }
        return $this->file;
    }

    //--------------------------------------------------------------------------

    public function type() {
        return $this->type;
    }

    //--------------------------------------------------------------------------

    public function name() {
        return $this->name;
    }

    //--------------------------------------------------------------------------

    public function version() {
        return $this->version;
    }

    //--------------------------------------------------------------------------

    public function step_mode() {
        return $this->export_steps || $this->import_steps;
    }

    //--------------------------------------------------------------------------

    public function export_steps() {
        return $this->export_steps;
    }

    //--------------------------------------------------------------------------

    public function import_steps() {
        return $this->import_steps;
    }

    //--------------------------------------------------------------------------

    public function export_id($export_id = null) {
        if ($export_id) {
            $this->export_id = $export_id;
        }


        if ( ! $this->export_id) {
            $this->export_id = $this->type() . '_' . $this->id() . '_' . uniqid();
        }

        return $this->export_id;
    }



    //--------------------------------------------------------------------------
    // Методы-заглушки итогово класса экспорта/импорта
    //--------------------------------------------------------------------------

    protected function init(){}

    //--------------------------------------------------------------------------

    protected function before_insert($table, $data) {
        $fname = 'before_insert_' . strtolower($table);

        if (method_exists($this, $fname)) {
            return $this->$fname($data);
        }

        return $data;
    }

    //--------------------------------------------------------------------------

    protected function after_insert($table, $data, $inser_id) {
        $fname = 'after_insert_' . strtolower($table);

        if (method_exists($this, $fname)) {
            return $this->$fname($data, $inser_id);
        }
    }

    //--------------------------------------------------------------------------

    protected function before_extract($path, $file) {
        return $path . $file;
    }

    //--------------------------------------------------------------------------

    protected function after_extract($path, $file) {}

    //--------------------------------------------------------------------------

    public function export_form(){}

    protected function export_data(){}
    protected function export_table(){}
    protected function export_file(){}
    protected function export_custom(){}
    protected function export_init($id, $export_id){}
    protected function export_process(){}
    protected function export_result(){}

    protected function import_data(){}
    protected function import_table(){}
    protected function import_file(){}
    protected function import_custom(){}
    public function import_init($file){}
    protected function import_process(){}
    protected function import_result(){}



    //--------------------------------------------------------------------------
    // Общие методы
    //--------------------------------------------------------------------------

    //--------------------------------------------------------------------------

    protected function import_step_init() {

    }

    //--------------------------------------------------------------------------

    protected function import_step_end() {
        remove_dir($this->export_dir());
    }

    protected function export_step_init() {
        $this->export_dir(true);

        $this->export_info['type']  = $this->type();
        $this->export_info['id']    = $this->id();
        $this->export_info['steps'] = array_keys($this->export_steps);
        $this->result('export_id', $this->export_id());

        $this->save_export_info();
    }

    //--------------------------------------------------------------------------

    protected function export_step_end() {
        require_once $this->nc_core->ADMIN_FOLDER . 'tar.inc.php';
        $export_path  = $this->backup->export_dir();
        $current_path = $this->export_dir();

        $ext         = '.tgz';
        $suffix      = '';
        $archive     = $this->backup->file_name($this->type(), $this->id(), '', '');
        $index       = 1;
        while (file_exists($export_path . $archive . $suffix . $ext)) {
            $index++;
            $suffix = '_' . $index;
        }

        $dirname = $archive . $suffix;
        $archive = $archive . $suffix . $ext;

        rename($current_path, $export_path . $dirname);
        nc_tgz_create($archive, $dirname, $this->backup->export_http_path());

        $this->result('file', $export_path. $archive);

        remove_dir($export_path . $dirname);

        $this->backup->file_rotation();
    }

    //--------------------------------------------------------------------------

    protected function save_result($driver, $overwrite = false) {
        $file = $this->backup->file_name($driver->type(), $driver->id(), '', '.xml', false);
        $driver->save_export_info();
        $result = $driver->export_result();
        return $result ? $this->save_file($file, $result, $overwrite) : false;
    }

    //--------------------------------------------------------------------------

    protected function save_file($file, $content, $overwrite = false) {
        $dir = $this->export_dir(true);

        if (file_exists($dir . $file)) {
            if (!$overwrite) return;
            unlink($dir . $file);
        }

        if (file_put_contents($dir . $file, $content)) {
            return $dir . $file;
        }

        return false;
    }

    //--------------------------------------------------------------------------

    protected function export_dir($mk_dir = false) {
        $dir = $this->backup->export_dir();

        if ($mk_dir && ! file_exists($dir)) {
            mkdir($dir);
        }

        if ($this->export_id()) {
            $dir .= $this->export_id() . DIRECTORY_SEPARATOR;
            if ($mk_dir && ! file_exists($dir)) {
                mkdir($dir);
            }
        }

        return $dir;
    }

    //--------------------------------------------------------------------------

    protected function reset() {
        $this->result      = null;
        $this->info        = null;
        $this->dict        = array();
        $this->export_dict = array();
        $this->id          = 0;
        $this->new_id      = 0;
        $this->file        = '';
    }

    //--------------------------------------------------------------------------

    // Setter / Getter
    protected function result($key, $val = null) {
        if ( ! is_null($val)) {
            $this->result[$key] = $val;
        }
        return $this->result[$key];
    }

    //--------------------------------------------------------------------------

    protected function dict_exclude($exclude_keys) {
        $exclude_keys = (array)$exclude_keys;

        foreach ($exclude_keys as $key) {
            $this->dict_exclude[$key] = $key;
        }
    }

    //--------------------------------------------------------------------------

    // Setter / Getter
    protected function dict($name = null, $val = null, $alias = null) {
        if (in_array($name, $this->dict_exclude)) {
            return;
        }

        if (is_null($val)) {
            return is_null($name) ? $this->export_dict : $this->export_dict[$name];
        }

        if (empty($this->dict[$name])) {
            $this->dict[$name] = array();

            if (empty($this->export_dict[$name])) {
                $this->export_dict[$name] = array();
            }
        }

        if ( ! is_null($alias)) {
            $this->dict[$name][$val]        = $alias ? $alias : $val;
            $this->export_dict[$name][$val] = $alias ? $alias : $val;
        }

        return $this->export_dict[$name][$val];
    }

    //--------------------------------------------------------------------------

    protected function current_dict($name = null, $val = null, $alias = null) {
        if (in_array($name, $this->dict_exclude)) {
            return;
        }

        if (is_null($val)) {
            return is_null($name) ? $this->dict : $this->dict[$name];
        }

        if (empty($this->dict[$name])) {
            $this->dict[$name] = array();

            if (empty($this->dict[$name])) {
                $this->dict[$name] = array();
            }
        }

        if ( ! is_null($alias)) {
            $this->dict[$name][$val] = $alias ? $alias : $val;
        }

        return $this->dict[$name][$val];
    }

    //--------------------------------------------------------------------------

    protected function info() {
        return $this->info;
    }

    //--------------------------------------------------------------------------

    protected function get_col($table, $where, $column) {
        $result = array();
        $data   = (array)$this->get_data($table, $where, 'DISTINCT ' . $column);

        foreach ($data as $row) {
            $result[] = $row[$column];
        }

        return $result;
    }

    //--------------------------------------------------------------------------

    protected function get_data($table, $where=null, $select='*', $order_by='') {
        if ($where) $sql_where = ' WHERE ' . $this->sql_make_where($where);
        if ($order_by) $order_by = " ORDER BY {$order_by}";
        return $this->db->get_results("SELECT {$select} FROM `{$table}`{$sql_where}{$order_by}", ARRAY_A);
    }

    //--------------------------------------------------------------------------

    protected function insert($table, $data, $exclude=array()) {
        $sql = $this->sql_make_insert($table, $data, $exclude);
        $this->db->query( $sql );
        return $this->db->insert_id;
    }

    //--------------------------------------------------------------------------

    protected function update($table, $data, $where) {
        $sql = $this->sql_make_update($table, $data, $where, array_keys($where));
        return $this->db->query($sql);
    }



    //--------------------------------------------------------------------------
    // Методы-помошники (helpers)
    //--------------------------------------------------------------------------

    protected function array_combine($keys, $values) {
        $out = array();
        foreach ($keys as $i => $key) {
            $out[$key] = $values[$i];
        }
        return $out;
    }

    //--------------------------------------------------------------------------

    protected function make_group_list($data, $key, $group_key, $name_key) {
        $list = array();
        foreach ($data as $row) {
            $list[$row[$group_key]][$row[$key]] = $row[$key] . '. ' . $row[$name_key];
        }
        return $list;
    }

    //--------------------------------------------------------------------------

    protected function make_list($data, $key, $name_key) {
        $list = array();
        foreach ($data as $row) {
            $list[$row[$key]] = $row[$key] . '. ' . $row[$name_key];
        }
        return $list;
    }

    //--------------------------------------------------------------------------

    protected function make_tree_list($data, $key, $parent_key, $name_key) {
        $tree = array();

        foreach ($data as $row) {
            $data[$row[$key]] = $row;
            $tree[$row[$parent_key]][$row[$key]] = null;
        }

        foreach ($tree as $pid => $subs) {
            foreach ($subs as $id => $row) {
                if (isset($tree[$id])) {
                    $tree[$pid][$id] =& $tree[$id];
                }
            }
        }

        return $this->_tree_to_list($tree[0], $data, $name_key);
    }

    //--------------------------------------------------------------------------

    protected function _tree_to_list($tree, $data, $name_key, $level=0) {
        $space        = '&nbsp;&middot;&nbsp;&nbsp;&nbsp;';
        $space_length = strlen($space) * $level;
        $list         = array();

        foreach ($tree as $id => $childs) {
            $list[$id] = str_pad('', $space_length, $space) . $id . '. ' . $data[$id][$name_key];
            if (is_array($childs)) {
                $list += $this->_tree_to_list($childs, $data, $name_key, $level+1);
            }
        }
        return $list;
    }

    //--------------------------------------------------------------------------

    protected function sql_make_where($where) {
        $sql_where = '';
        $sql_and   = '';

        foreach ($where as $key => $value) {
            if (is_numeric($key)) {
                if (in_array($value, array(self::SQL_OR, self::SQL_AND))) {
                    $sql_and = " {$value} ";
                }
                else {
                    $sql_where .= $sql_and . $value;
                }
                continue;
            }
            if (is_array($value)) {
                $sql_where .= $sql_and . "`{$key}` IN ('".implode("', '", $value)."')";
            }
            else {
                $sql_where .= $sql_and . "`{$key}`='{$value}'";
            }
            $sql_and = ' ' . self::SQL_AND . ' ';
        }
        return $sql_where;
    }

    //--------------------------------------------------------------------------

    protected function sql_make_insert($table, $data, $exclude=array()) {
        $values = array();

        foreach ($exclude as $field) {
            unset($data[$field]);
        }
        foreach ($data as &$row) {
            $row = $this->db->escape($row);
        }

        $values[$i] = "('" . implode("', '", $data) . "')";
        $values = implode(",\n", $values);
        $fields = "`".implode("`, `", array_keys($data)) . "`";
        return "INSERT INTO `{$table}` ({$fields}) VALUES \n{$values}";
    }

    //--------------------------------------------------------------------------

    protected function sql_make_update($table, $data, $where, $exclude=array()) {
        $set = '';
        foreach ($exclude as $field) {
            unset($data[$field]);
        }
        foreach ($data as $key => $row) {
            $row = $this->db->escape($row);
            if ($set) $set .= ',';
            $set .= " `{$key}` = \"{$row}\"";
        }
        $query_where = $this->sql_make_where($where);

        // $values[$i] = "('" . implode("', '", $data) . "')";
        // $values = implode(",\n", $values);
        // $fields = "`".implode("`, `", array_keys($data)) . "`";
        return "UPDATE `{$table}` SET {$set} WHERE {$query_where}";
    }

    //--------------------------------------------------------------------------

    protected function sql_make_create($table) {
        $this->db->query("SET SQL_QUOTE_SHOW_CREATE = 1");
        $result = $this->db->get_row("SHOW CREATE TABLE `{$table}`", ARRAY_N);
        if (!$result) {
            return false;
        }
        $result = $result[1];
        $result = substr($result, 0, strrpos($result, ')'));
        $result .= ") ENGINE=MyISAM;";
        return $result;
    }

    //--------------------------------------------------------------------------

}