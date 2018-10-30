<?php
if(!defined("DS_FORM_LOAD") || DS_FORM_LOAD!==true) die();
/**
* 
*/
class DSMain
{
    public $get = array();
    public $post = array();
    public $cookie = array();
    public $files = array();
    public $server = array();
    protected $formConfig;
    protected $formID;

    public function __construct() {
        $this->request();
    }

    protected function getConfig() {
        $this->formConfig = new DSConfig;
    }

    protected function request() {
        $this->get = $this->clean($_GET);
        $this->post = $this->clean($_POST);
        $this->request = $this->clean($_REQUEST);
        $this->cookie = $this->clean($_COOKIE);
        $this->files = $this->clean($_FILES);
        $this->server = $this->clean($_SERVER);
    }

    private function clean($data) {

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);
                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_NOQUOTES, 'UTF-8');
        }

        return $data;
    }

    protected function responseJson($data = array()) {
        $result = $this->jsonEncode($data);
        echo($result);
    }

    protected function renderTemplate($template, $data = array()) {
        extract($data);
        unset($data);
        ob_start();
            if (file_exists(DS_FORM_ROOT . '/template/custom/' . $this->formID . '.' . $template . '.php')) {
                include DS_FORM_ROOT . '/template/custom/' . $this->formID . '.' . $template . '.php';
            } else {
                include DS_FORM_ROOT . '/template/default/' . $template . '.php';
            }
            $renderTemplate = ob_get_contents();
        ob_end_clean();
        return $renderTemplate;
    }

    protected function jsonEncode($data = array()) {
        if(function_exists('json_encode')) {
            return json_encode($data);
        } else {
            switch (gettype($data)) {
                case 'NULL':
                    return 'null';
                case 'integer':
                case 'double':
                    return strval($data);
                case 'string':
                    return '"' . addslashes($data) . '"';
                case 'boolean':
                    return $data ? 'true' : 'false';
                case 'object':
                    $data = (array) $data;
                case 'array':
                    $result = array();
                    foreach ($data as $k => $v) {
                      $result[] = '"' . $k . '"' . ':' . json_encode($v);
                    }
                    return '{' . implode(',', $result) . '}';
            }
        }
    }

    protected function jsonDecode($data = '') {
        if (get_magic_quotes_gpc()) {
            $data = stripslashes($data);
        }

        if(function_exists('json_decode')) {
            $dataDecode = json_decode($data, true);
        } else {
            $data = substr($data, 1, -1);
            $data = str_replace(array(":", "{", "[", "}", "]"), array("=>", "array(", "array(", ")", ")"), $data);
            @eval("\$dataDecode = array({$data});");
        }

        return $dataDecode;
    }

    private function compressCss($filename)
    {
        $css = file_get_contents($filename);
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
        return $css;
    }

    private function globCss($path="") {
        $css = '';
        foreach (glob(DS_FORM_ROOT . "/css/" . $path . "*.css") as $filename) {
            if(!preg_match('|/css/.*/_|siU', $filename)) {
                $css .= '/*path:' . str_replace($_SERVER['DOCUMENT_ROOT'], '', $filename) . '*/' . "\n";
                $css .= $this->compressCss($filename) . "\n";
            }
        }
        return $css;
    }

    public function getCss() {
        header("Content-type: text/css; charset: UTF-8");
        $formCSS  = '';
        $formCSS .= $this->globCSS();
        $formCSS .= $this->globCSS('forms/');
        $formCSS .= $this->globCSS('plugins/');
        die($formCSS);
    }

    static public function routing() {

        $main = new DSMain;

        if (isset($main->post['route']) && !empty($main->post['route'])) {

            $class = $main->post['route'];

            try {
                $obj = new $class;
                if (isset($main->post['m']) && !empty($main->post['m'])) {
                    $method = $main->post['m'];
                    $obj->$method();
                } else {
                    $obj->index();
                }
            } catch (Exception $e) {
                $main->responseJson(
                        array(
                            'error'      => 3,
                            'error_text' => "Error: {$e->getMessage()}",
                        )
                );
            }

            return $obj;

        } elseif(isset($main->get['m']) && !empty($main->get['m'])) {
            $method = $main->get['m'];
            $main->$method();
        }

        return $main;
    }
}
