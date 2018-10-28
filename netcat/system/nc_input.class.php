<?php

/* $Id: nc_input.class.php 8405 2012-11-13 07:40:36Z ewind $ */
if (!class_exists("nc_System"))
    die("Unable to load file.");

class nc_Input extends nc_System {

    private $_variables;

    public function __construct() {
        parent::__construct();

        $this->_variables = array(
                "_SERVER", "_ENV", "_FILES", "_GET", "_POST", "_COOKIE", "GLOBALS",
                "ADMIN_AUTHTIME", "ADMIN_AUTHTYPE", "ADMIN_LANGUAGE",
                "AUTHORIZATION_TYPE", "AUTHORIZE_BY", "AUTH_USER_GROUP", "AUTH_USER_ID",
                "DIRCHMOD", "FILECHMOD", "CHARSET", "SOURCE_CHARSET",
                "DOCUMENT_ROOT", "DOC_DOMAIN", "DOMAIN_NAME", "EDIT_DOMAIN", "HTTP_HOST",
                "HTTP_IMAGES_PATH", "HTTP_FILES_PATH", "HTTP_DUMP_PATH", "HTTP_ROOT_PATH", "HTTP_CACHE_PATH", "HTTP_TRASH_PATH",
                "ADMIN_PATH", "ADMIN_TEMPLATE", "NC_JQUERY_PATH",
                "SECURITY_XSS_CLEAN",
                "NC_CHARSET", "NC_UNICODE", "NC_ADMIN_HTTPS",
                "MYSQL_CHARSET", "MYSQL_DB_NAME", "MYSQL_HOST", "MYSQL_PASSWORD", "MYSQL_USER", "MYSQL_PORT", "MYSQL_SOCKET", "MYSQL_ENCRYPT", "MYSQL_TIMEZONE", "SHOW_MYSQL_ERRORS",
                "SYSTEM_FOLDER", "SUB_FOLDER", "NETCAT_ROOT_FOLDER", "ROOT_FOLDER", "TMP_FOLDER", "FILES_FOLDER",
                "MODULE_FOLDER", "INCLUDE_FOLDER", "ADMIN_TEMPLATE_FOLDER", "DUMP_FOLDER", "CACHE_FOLDER", "TRASH_FOLDER", "ADMIN_FOLDER",
                "HTTP_TEMPLATE_PATH", "TEMPLATE_FOLDER", "CLASS_TEMPLATE_FOLDER", "WIDGET_TEMPLATE_FOLDER", "JQUERY_FOLDER", "MODULE_TEMPLATE_FOLDER",
                "PHP_TYPE", "REDIRECT_STATUS", "NC_REDIRECT_DISABLED", "NC_DEPRECATED_DISABLED", "BBCODE", "AJAX_SAVER", "DEVELOPER_NAME", "DEVELOPER_URL", "nc_core", "use_gzip_compression");

        $nc_core = nc_Core::get_object();
        $this->prepare_extract();
    }

    public function clear_system_vars($result) {
        foreach ($this->_variables as $var) {
            unset($result[$var]);
        }
        return $result;
    }

    public function filter($result) {
        $filter_vars = array('REQUEST_URI', 'REQUEST_METHOD');

        foreach ($filter_vars as $v) {
            $result[$v] = str_replace('$', '', htmlspecialchars($result[$v]));
            if (isset($_SERVER[$v]))
                $_SERVER[$v] = $result[$v];
        }

        return $result;
    }

    public function prepare_extract() {
        $nc_core = nc_Core::get_object();
        static $result = array();

        $nc_core->REQUEST_URI = isset($_GET['REQUEST_URI']) ? $_GET['REQUEST_URI']
                                                            : (isset($_POST['REQUEST_URI']) ? $_POST['REQUEST_URI']
                                                                                            : (isset($_ENV['REQUEST_URI']) ? $_ENV['REQUEST_URI']
                                                                                                                           : getenv("REQUEST_URI")));

        if (nc_substr($nc_core->REQUEST_URI, 0, 1) != "/") {
            $nc_core->REQUEST_URI = "/" . $nc_core->REQUEST_URI;
        }

        $nc_core->REQUEST_URI = trim($nc_core->REQUEST_URI);

        require_once __DIR__ . '/../require/s_common.inc.php';
        $url = nc_get_scheme() . "://" . getenv("HTTP_HOST") . $nc_core->REQUEST_URI;
        $parsed_url = @parse_url($url);

        if (is_array($parsed_url) && array_key_exists('query', $parsed_url) && $parsed_url['query']) {
            parse_str($parsed_url['query'], $parsed_query_arr);
            $parsed_query_arr = $this->clear_system_vars($parsed_query_arr);
            $_GET = $parsed_query_arr ? $parsed_query_arr : array();
        }


        if (!empty($result)) {
            return $result;
        }

        // XSS clean
        if ( isset($nc_core->security) && $nc_core->SECURITY_XSS_CLEAN ) {
            if ( !function_exists('array_map_recursive') ) {
                function array_map_recursive($fn, $arr) {
                    $rarr = array();

                    foreach ($arr as $k => $v) {
                        $rarr[$k] = is_array($v)
                            ? array_map_recursive($fn, $v)
                            : ( is_array($fn) ? call_user_func($fn, $v) : $fn($v) );
                    }

                    return $rarr;
                }
            }

            $_COOKIE = array_map_recursive( array($nc_core->security, 'xss_clean'), $_COOKIE );
            $_GET    = array_map_recursive( array($nc_core->security, 'xss_clean'), $_GET );
            $_ENV    = array_map_recursive( array($nc_core->security, 'xss_clean'), $_ENV );
            $_SERVER = array_map_recursive( array($nc_core->security, 'xss_clean'), $_SERVER );
        }

        $superglobals = array(
            "_COOKIE" => $_COOKIE,
            "_GET"    => $_GET,
            "_POST"   => $_POST,
            "_FILES"  => $_FILES,
            "_ENV"    => $_ENV,
            "_SERVER" => $_SERVER
        );

        foreach ($superglobals as $key => $super_array) {
            $result = array_merge($result, $super_array);
            $this->$key = $this->prepare_superglobals($super_array);
        }
        $result = $this->filter($this->clear_system_vars($result));

        foreach ($this->_variables as $var) {
            if ((array_key_exists($var, $superglobals) || $this->in_superglobal($var)) && !in_array($var, array('HTTP_HOST', 'DOCUMENT_ROOT', 'REDIRECT_STATUS'))) {
                continue;
            }
            global $$var;
            $nc_core->set_variable($var, $$var);
        }

        if (!$nc_core->NC_CHARSET){
            $nc_core->NC_CHARSET = 'windows-1251';
        }

        if (!$nc_core->NC_JQUERY_PATH) {
            $nc_core->NC_JQUERY_PATH = $nc_core->SUB_FOLDER . $nc_core->HTTP_TEMPLATE_PATH . 'jquery/jquery.min.js';
        }

        $nc_core->MYSQL_ENCRYPT = strtoupper($nc_core->MYSQL_ENCRYPT);

        if (!$nc_core->MYSQL_ENCRYPT || !in_array($nc_core->MYSQL_ENCRYPT, array('PASSWORD', 'OLD_PASSWORD', 'MD5', 'SHA', 'SHA1'))) {
            $nc_core->MYSQL_ENCRYPT = 'PASSWORD';
        }

        if (!$nc_core->NC_UNICODE && ($_POST["NC_HTTP_REQUEST"] || $_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $result = $nc_core->utf8->array_utf2win($result);
            foreach ($superglobals as $key => $super_array) {
                $this->$key = $nc_core->utf8->array_utf2win($this->$key);
            }
        }

        if (!get_magic_quotes_gpc()) {
            $result = $this->recursive_add_slashes($result);
        }

        return $result;
    }

    public function recursive_add_slashes($input) {
        if (!is_array($input)) {
            return addslashes($input);
        }
        $output = array();

        foreach ($input as $k => $v) {
            $output[$k] = is_array($v) ? $this->recursive_add_slashes($v) : addslashes($v);
        }

        return $output;
    }

    public function prepare_superglobals($array) {
        if (!get_magic_quotes_gpc())
            return $array;
        return $this->recursive_stripcslashes($array);
    }

    public function recursive_stripcslashes($input) {
        if (!is_array($input)) {
            return stripcslashes($input);
        }
        $output = array();

        foreach ($input as $k => $v) {
            $output[$k] = is_array($v) ? $this->recursive_stripcslashes($v) : stripcslashes($v);
        }

        return $output;
    }


    public function recursive_striptags($input) {
        if (!is_array($input)) {
            return strip_tags($input);
        }
        $output = array();

        foreach ($input as $k => $v) {
            $output[$k] = is_array($v) ? $this->recursive_striptags($v) : strip_tags($v);
        }

        return $output;
    }

    public function recursive_striptags_escape($input) {
        $db = nc_Core::get_object()->db;
        if (!is_array($input)) {
            return $db->escape(strip_tags($input));
        }
        $output = array();

        foreach ($input as $k => $v) {
            $output[$k] = is_array($v) ? $this->recursive_striptags_escape($v) : $db->escape(strip_tags($v));
        }

        return $output;
    }

    public function in_superglobal($var) {
        $superglobals = array("_GET" => $_GET, "_POST" => $_POST, "_COOKIE" => $_COOKIE, "_FILES" => $_FILES, "_ENV" => $_ENV, "_SERVER" => $_SERVER);
        foreach ($superglobals as $v) {
            return array_key_exists($var, $v);
        }
    }

    /**
     * @param string $item
     * @return bool|array|mixed
     */
    public function fetch_get($item = "") {

        if (empty($this->_GET))
            return false;

        if ($item) {
            return array_key_exists($item, $this->_GET) ? $this->_GET[$item] : null;
        } else {
            return $this->_GET;
        }
    }

    /**
     * @param string $item
     * @return bool|array|mixed
     */
    public function fetch_post($item = "") {

        if (empty($this->_POST))
            return false;

        if ($item) {
            return array_key_exists($item, $this->_POST) ? $this->_POST[$item] : null;
        } else {
            return $this->_POST;
        }
    }

    /**
     * @param string $item
     * @return bool|array|mixed
     */
    public function fetch_cookie($item = "") {

        if (empty($this->_COOKIE))
            return false;

        if ($item) {
            return array_key_exists($item, $this->_COOKIE) ? $this->_COOKIE[$item] : null;
        } else {
            return $this->_COOKIE;
        }
    }

    /**
     * @param string $item
     * @return bool|array|mixed
     */
    public function fetch_session($item = "") {

        if (empty($this->_SESSION))
            return false;

        if ($item) {
            return array_key_exists($item, $this->_SESSION) ? $this->_SESSION[$item] : null;
        } else {
            return $this->_SESSION;
        }
    }

    /**
     * @param string $item
     * @return bool|array|mixed
     */
    public function fetch_files($item = "") {

        if (empty($this->_FILES))
            return false;

        if ($item) {
            return array_key_exists($item, $this->_FILES) ? $this->_FILES[$item] : null;
        } else {
            return $this->_FILES;
        }
    }

    /**
     * @param string $item
     * @return bool|array|mixed
     */
    public function fetch_get_post($item = "") {

        if (empty($this->_GET) && empty($this->_POST))
            return false;

        if ($item) {
            return array_key_exists($item, $this->_GET) ? $this->_GET[$item] : (array_key_exists($item, $this->_POST) ? $this->_POST[$item] : null);
        } else {
            return array_merge($this->_POST, $this->_GET);
        }
    }


    /**
     * @param string $item
     * @return bool|array|mixed
     */
    public function fetch_post_get($item = "") {

        if (empty($this->_GET) && empty($this->_POST)) {
            return false;
        }

        if ($item) {
            return array_key_exists($item, $this->_POST)
                ? $this->_POST[$item]
                : (array_key_exists($item, $this->_GET) ? $this->_GET[$item] : null);
        }
        else {
            return array_merge($this->_GET, $this->_POST);
        }
    }

    /**
     * @param $variable
     * @param $key
     * @param $value
     */
    public function set($variable, $key, $value) {
        $this->{$variable}[$key] = $value;
    }

}