<?php


class nc_backup_driver extends nc_backup_driver_base {

    //--------------------------------------------------------------------------

    // Объекты для работы с XML
    protected $xpath;
    protected $doc;
    // ссылки на объекты в
    protected $xml = array();

    //--------------------------------------------------------------------------

    protected function reset() {
        parent::reset();
        $this->xpath = null;
        $this->doc   = null;
        $this->xml   = array();
    }

    //--------------------------------------------------------------------------
    // EXPORT METHODS:
    //--------------------------------------------------------------------------

    protected function export_init($id, $export_id = null) {
        $this->reset();
        $this->id($id);
        if ($export_id) {
            $this->export_id($export_id);
        }

        $this->info = array();
        $this->info['export'] = array(
            'version'   => $this->version(),
            'driver'    => $this->backup->config('driver'),
            'type'      => $this->type(),
            'id'        => $this->id(),
            'date'      => date('Y-m-d H:i:s'),
            'user'      => $GLOBALS['perm']->getLogin() . ':' . $GLOBALS['AUTH_USER_ID'],
            'http_host' => $this->nc_core->HTTP_HOST,
        );

        // Netcat info
        $SystemID  = $this->nc_core->get_settings("SystemID");
        $LastPatch = $this->nc_core->get_settings("LastPatch");
        list($SystemName, $SystemColor) = nc_system_name_by_id($SystemID);

        $this->info['netcat'] = array(
            'version' => $this->nc_core->get_settings("VersionNumber"),
            'type'    => $SystemName,
        );


        $this->doc   = new DOMDocument;
        $this->xpath = new DOMXpath($this->doc);

        $this->doc->preserveWhiteSpace = false;
        $this->doc->formatOutput       = true;
        $this->doc->encoding           = 'utf-8';

        $this->make_elem('root', $this->doc);
        $this->make_elem('info');
        $this->make_elem('dict');
        $this->make_elem('data');
        $this->make_elem('files');
    }

    //--------------------------------------------------------------------------

    protected function export_custom($attr = array(), $data = null) {
        $this->make_elem('custom', 'data', $attr, $data);
    }

    //--------------------------------------------------------------------------

    protected function export_data($table, $pkey, $where = null) {
        $data = $this->get_data($table, $where);
        // $this->dict($pkey, $data);

        if (!$data) {
            return false;
        }

        $this->make_elem('insert', 'data', array('table'=>$table, 'key'=>$pkey));
        $this->make_elem('fields', 'insert', null, implode(' ', array_keys($data[0])));

        foreach ($data as $row) {

            $this->dict($pkey, $row[$pkey], $row[$pkey]);
            $values = json_safe_encode( array_values($row) );
            $this->make_elem('values', 'insert', array($pkey=>$row[$pkey]), $values);
        }

        return $data;
    }

    //--------------------------------------------------------------------------

    protected function export_table($table) {
        $sql = $this->sql_make_create($table);
        if (!$sql) {
            return false;
        }
        $this->make_elem('create', 'data', array('table'=>$table), $sql);

        return $sql;
    }

    //--------------------------------------------------------------------------

    protected function export_file($path, $file) {

        $path = rtrim($path, DIRECTORY_SEPARATOR);

        if (!file_exists($this->nc_core->DOCUMENT_ROOT . $path . DIRECTORY_SEPARATOR . $file)) {
            return false;
        }


        require_once $this->nc_core->ADMIN_FOLDER . 'tar.inc.php';

        $full_path    = $this->nc_core->DOCUMENT_ROOT . $path;
        $archive_name = md5( $this->type() . $this->id() . microtime() );
        $tmp_file     = $this->nc_core->TMP_FOLDER . $archive_name . '.tgz';
        $dump_file    = nc_tgz_create($tmp_file, $file, $path);
        $tar_contents = file_get_contents($tmp_file);
        $tar_contents = base64_encode($tar_contents);
        unlink($tmp_file);

        $attr = array(
            'type' => 'tar',
            'path' => rtrim($path, '/') . '/',
            'file' => $file
        );

        $this->make_elem('file', 'files', $attr, $tar_contents);
    }

    //--------------------------------------------------------------------------

    protected function export_result() {
        if (empty($this->dict)) {
            return;
        }

        $this->clear_elem('dict');
        foreach ($this->dict as $name => $values) {
            $this->make_elem($name, 'dict', array('values'=>implode(',', $values)));
        }

        $this->clear_elem('info');
        foreach ($this->info as $name => $values) {
            $this->make_elem($name, 'info', $values);
        }

        return $this->doc->saveXML();
    }

    //--------------------------------------------------------------------------
    // IMPORT METHODS:
    //--------------------------------------------------------------------------

    public function import_init($file, $export_id = null) {
        $this->reset();
        $file = $this->file($file);

        $this->result = array(
            'import_table' => array(),
            'import_data'  => array(),
            'import_file'  => array(),
            'redirect'     => false,
        );

        if ($export_id) {
            $this->export_id($export_id);
            $is_xml = false;
        }
        else {
            $fh = fopen($file, 'r');
                $is_xml = substr(fgets($fh), 0, 5) == '<?xml';
            fclose($fh);
        }

        if ( ! $is_xml) {
            if ( ! $export_id) {
                require_once $this->nc_core->ADMIN_FOLDER . 'tar.inc.php';
                $uniqid     = uniqid();
                $tmp_uniqid = 'tmp_' . $uniqid;

                $this->export_id( $tmp_uniqid );
                $tmp_dir  = $this->export_dir(true);
                $tar_file = $this->backup->export_dir() . $uniqid . '.tgz';

                copy($file, $tar_file);
                nc_tgz_extract($tar_file, $tmp_dir);
                unlink($tar_file);

                $files = scandir($tmp_dir);
                foreach ($files as $file ) {
                    if ($file{0} != '.') break;
                }

                $this->export_id( $uniqid );

                rename($tmp_dir . $file, $this->export_dir());
                remove_dir($tmp_dir);
            }

            $info = $this->load_export_info();
            $file = $this->export_dir() . $info['type'] . '_' . $info['id'] . '.xml';
        }

        $previous_state = libxml_use_internal_errors(TRUE);
        $this->doc = DOMDocument::load($file);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_state);

        if (!$this->doc) {
            throw new Exception("XML file not valid", 1);
        }

        $this->xpath = new DOMXpath($this->doc);

        $this->dict        = $this->read_dict();
        $this->export_dict = $this->dict;
        $this->info        = $this->read_info();


        if (empty($this->info)
            OR is_null($this->info->export->id) // 0 - allowed
            OR empty($this->info->export->type)
            OR empty($this->info->export->version)
        ) {
            throw new Exception("XML file not support", 1);
        }

        if ($this->version() != $this->info->export->version) {
            throw new Exception("Version of Import/Export not supported", 1);
        }
        if ($this->type() && $this->type() != $this->info->export->type) {
            throw new Exception("Type of Import/Export not supported", 1);
        }

        $this->id($this->info->export->id);
    }

    //--------------------------------------------------------------------------

    protected function import_data($table) {
        $node_list = $this->xpath->query("data/insert[@table='{$table}']");

        if (!$node_list) return;

        $fields      = array();
        $values      = array();
        $insert_rows = 0;

        foreach ($node_list as $insert_node) {

            $PK = $insert_node->getAttribute('key');

            foreach ($insert_node->childNodes as $node) {

                if ($node->nodeType != XML_ELEMENT_NODE) continue;

                if ($node->nodeName == 'fields') {
                    $fields = array_map('trim', explode(' ', $node->nodeValue));
                    continue;
                }

                $id        = $node->getAttribute($PK);
                $values    = json_decode($node->textContent);
                $data      = $this->array_combine($fields, $values);
                $data      = $this->before_insert($table, $data);
                $exclude   = $this->save_ids ? array() : array($PK);
                $insert_id = $this->insert($table, $data, $exclude);

                $insert_rows++;

                if (!$this->save_ids) {
                    $this->dict($PK, $id, $insert_id);
                }

                $this->after_insert($table, $data, $insert_id);
            }
        }

        if (empty($this->result['import_data'][$table])) {
            $this->result['import_data'][$table] = 0;
        }
        $this->result['import_data'][$table] += $insert_rows;
    }

    //--------------------------------------------------------------------------

    protected function import_table($table, $new_table = null) {
        $create_node = $this->xpath->query("data/create[@table='{$table}']")->item(0);
        $create_sql  = $create_node->nodeValue;

        if ($new_table && $new_table != $table) {
            $create_sql = str_replace("`{$table}`", "`{$new_table}`", $create_sql);
        }
        $this->result['import_table'][$table] = $new_table;

        $this->db->query($create_sql);
    }

    //--------------------------------------------------------------------------

    protected function import_file() {
        require_once $this->nc_core->ADMIN_FOLDER . 'tar.inc.php';

        $file_nodes = $this->xpath->query("files/file");

        foreach ($file_nodes as $node) {
            $path = $node->getAttribute('path');
            $file = $node->getAttribute('file');
            $type = $node->getAttribute('type');

            $raw  = base64_decode($node->nodeValue);

            $new_path  = $this->before_extract($path, $file);
            $new_path  = $new_path ? $new_path : $path;
            $full_path = $this->nc_core->DOCUMENT_ROOT . $new_path;

            $archive_name = md5(uniqid());
            $archive_file = $this->nc_core->TMP_FOLDER . $archive_name . '.tgz';

            file_put_contents($archive_file, $raw);
            nc_tgz_extract($archive_file, $this->nc_core->TMP_FOLDER);
            unlink($archive_file);

            $this->result['import_file'] = $this->move_files($this->nc_core->TMP_FOLDER . $file, $full_path);

            if (file_exists($this->nc_core->TMP_FOLDER . $file)) {
                remove_dir($this->nc_core->TMP_FOLDER . $file);
            }

            $this->after_extract($path, $file);
        }
    }

    //--------------------------------------------------------------------------

    protected function move_files($src_dir, $dest_dir) {
        $result   = array();
        $src_dir  = rtrim($src_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $dest_dir = rtrim($dest_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $web_dir  = substr($dest_dir, strlen($this->nc_core->DOCUMENT_ROOT));

        if (!file_exists($dest_dir)) {
            $rename = rename($src_dir, $dest_dir);
            $result[''][$web_dir] = $rename ? 'OK' : 'ERROR';
            return $result;
        }

        $dh = opendir($src_dir);
        while ($f = readdir($dh)) {
            if ($f == '.' || $f == '..') continue;
            if (!file_exists($dest_dir . $f)) {
                $rename = rename($src_dir . $f, $dest_dir . $f);
                $result[$web_dir][$f] = $rename ? 'OK' : 'ERROR';
            }
            else {
                $result[$web_dir][$f] = 'SKIP';
            }
        }
        closedir($dh);

        return $result;
    }

    //--------------------------------------------------------------------------

    protected function import_result() {
        return $this->result;
    }

    //--------------------------------------------------------------------------
    // COMMON METHODS
    //--------------------------------------------------------------------------

    protected function get_node($node) {
        $result = $this->xpath->query($node);
        if (!$result->length) return false;
        $result = $result->item(0);

        return $result;
    }

    //--------------------------------------------------------------------------

    protected function read_node($node) {
        if (!$node) {
            return;
        }

        $result = new stdClass;
        foreach($node->attributes as $k => $attr) {
            $result->$k = $attr->value;
        }

        return $result;
    }

    //--------------------------------------------------------------------------

    protected function read_info() {
        $info_node = $this->get_node('info');
        if (!$info_node) return false;

        $result = new stdClass;

        foreach ($info_node->childNodes as $node) {
            if ($node->nodeType != XML_ELEMENT_NODE) continue;
            $node_name = $node->nodeName;
            $result->$node_name = $this->read_node($node);
        }

        return $result;
    }

    //--------------------------------------------------------------------------

    protected function read_dict() {
        $dict_node = $this->get_node('dict');
        if (!$dict_node) return false;

        $result = array();

        foreach ($dict_node->childNodes as $node) {
            if ($node->nodeType != XML_ELEMENT_NODE) continue;
            $values = explode(',', $node->getAttribute('values'));
            foreach ($values as $val) {
                $val = trim($val);
                $result[$node->nodeName][$val] = $val;
            }
        }

        return $result;
    }

    //--------------------------------------------------------------------------
    // XML NODE HELPERS
    //--------------------------------------------------------------------------

    protected function make_node($name) {
        return $this->xml[$name] = $this->doc->createElement($name);
    }

    //--------------------------------------------------------------------------

    protected function make_elem($name, $parent = 'root', $attr = null, $data = null) {
        $node = $this->make_node($name);

        if (is_string($parent)) {
            $parent = $this->xml[$parent];
        }

        if ($attr) {
            $attr = (array)$attr;
            foreach ($attr as $key => $value) {
                $node->setAttribute($key, $value);
            }
        }

        if ($data) {
            $data = $this->doc->createCDATASection($data);
            $node->appendChild($data);
        }

        $parent->appendChild($node);
        return $node;
    }

    //--------------------------------------------------------------------------

    protected function clear_elem($name) {
        $old = $this->doc->getElementsByTagName($name)->item(0);
        $new = $this->make_node($name);
        $old->parentNode->replaceChild($new, $old);
    }

    //--------------------------------------------------------------------------
}