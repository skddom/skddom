<?php

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
@include_once ($NETCAT_FOLDER."vars.inc.php");

class site_auditor {

    //URL of the site to check
    var $url;
    // domain level
    var $domain_level;
    //HTTP user object, @var HTTP_Client
    var $user_agent;

    /**
     * Constructor
     *
     * @param string
     * @return site_auditor
     */
    function site_auditor($url = '') {
        if ($url) $this->set_url($url);
        // initialize user agent:
        require_once("HTTP/Client.php");
        $this->user_agent = new HTTP_Client(null,
                        array("Accept" => "*/*",
                                "Accept-Language" => "ru,en",
                                "Accept-Encoding" => "gzip,deflate",
                                "User-Agent" => $_SERVER["HTTP_USER_AGENT"]
                        )
        );
    }

    function set_url($url) {
        // doesn't works for
        preg_match("!^(?:[a-z]+://)? #protocol
        # (?:www\.)?             #www.
        (
           (?:[a-z0-9\-]+\.)+    #domain
           [a-z]{2,}             #tld
        )
      !x", strtolower($url), $regs);

        if (!$regs[1]) {
            user_error(NETCAT_MODULE_AUDITOR_WRONG_URL.": $url", E_USER_WARNING);
            return;
        }

        $this->url = $regs[1];
        $this->domain_level = substr_count($this->url, ".") + 1;
    }

    /**
     * Получить "что-то" :)
     *
     * @param string
     * @return mixed assoc array with values or NULL if failed
     */
    function get($what) {
        if (!$this->url) {
            user_error(NETCAT_MODULE_AUDITOR_NO_URL, E_USER_WARNING);
            return null;
        }
        if (!preg_match("/^\w+$/", $what)) {
            return null;
        }

        $ret = array("code" => false);

        if (!@require($GLOBALS['ADMIN_FOLDER']."siteinfo/sources/".$what.".php")) {
            user_error("File not found: sources/".$what.".php", E_USER_WARNING);
            return false;
        }

        // data wasn't fetched yet
        if (!$ret["code"]) {
            $ret = $this->request_and_process($source);
        }

        return $ret;
    }

    /**
     * Make request and process it
     *
     * @access private
     * @param array
     *   Assoc array
     *    - name
     *    - url
     *    - params (array)
     *    - pattern
     *    - method
     * @return array
     */
    function request_and_process($source) {
        global $nc_core;
        if (!is_array($source)) return array("ok" => false);

        foreach (array("name", "url", "pattern") AS $param) {
            if (!$source[$param]) {
                user_error($source['name'].": ".$param." not set", E_USER_WARNING);
                return array("ok" => false);
            }
        }

        if (!$source["method"]) $source["method"] = "get";

        $ret = array("ok" => false);

        $this->user_agent->{$source["method"]}($source["url"], $source["params"]);
        $response = $this->user_agent->currentResponse();

        if ($response["code"] == "200") {
            // START --- Encode content in preg expressions ---
            $source_encoding = MAIN_ENCODING ? MAIN_ENCODING : "windows-1251";
            // вся обработка идет в utf-8, конвертация в кодировке пользователя - при выдаче результата,
            // в файле get_data.php
            $source_encoding = 'utf-8';

            if ($response['headers']['content-type']) {
                preg_match('/charset=([\w\d-]+)/is', $response['headers']['content-type'], $matches);
                $source_charset = $matches[1] ? $matches[1] : $source_encoding;
            }

            if ($source_encoding && $source_charset && strtolower($source_encoding) != strtolower($source_charset)) {
                $response["body"] = $nc_core->utf8->conv($source_charset, 'utf-8', $response["body"]);
                //$source["pattern"] = $source["pattern"] ? iconv($source_encoding, $source_charset, $source["pattern"]) : $source["pattern"];
                //$source["replace"][0] = $source["pattern"] ? iconv($source_encoding, $source_charset, $source["replace"][0]) : $source["pattern"];
            }
            // END --- Encode content in preg expressions ---
            // extract data
            if (!$source["pattern"]) {
                user_error("NO DATA PATTERN", E_USER_WARNING);
            } else {
                nc_preg_match($source["pattern"], $response["body"], $regs);

                if (nc_strlen($regs[1])) {
                    array_shift($regs);
                    if (sizeof($regs) > 1) {
                        if ($source["reverse"]) $regs = array_reverse($regs);
                    }
                    $ret["value"] = join(" / ", $regs);
                    $ret["ok"] = true;

                    if (is_array($source["replace"]) && nc_strlen($source["replace"][0])) {
                        $ret["value"] = nc_preg_replace($source["replace"][0], $source["replace"][1], $ret["value"]);
                    }
                }
            }
        }

        $ret["name"] = $source["name"];
        $ret["href"] = $source["href"];

        if (!$ret["value"] && $source["negative"])
                $ret["value"] = $source["negative"];

        $ret = array_merge($response, $ret);

        return $ret;
    }

}