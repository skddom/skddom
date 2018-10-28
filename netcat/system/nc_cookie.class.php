<?php

/**
 * Работа с куками
 */
class nc_cookie {

    protected $domain;

    /**
     * @return string
     */
    public function get_domain() {
        if (!$this->domain) {
            $this->domain = $this->determine_current_domain();
        }

        return $this->domain;
    }

    /**
     * @return string
     */
    protected function determine_current_domain() {
        $nc_core = nc_core::get_object();
        $domain = strtolower($nc_core->HTTP_HOST);

        // отбросить порт
        $colon = strpos($domain, ':');
        if ($colon !== false) {
            $domain = substr($domain, 0, $colon);
        }

        // исключение для доменов вида IP-адрес
        if (!preg_match('/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $domain)) {
            if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,32})$/', $domain, $regs)) {
                $domain = '.' . $regs['domain'];
            }
            else {
                $domain = '.' . $domain;
            }
        }

        // отбросить "www."?
        // используется следующая логика:
        // (а) если включён модуль auth, проверяется настройка 'with_subdomain';
        // (б) если модуль auth отсутствует: если в настройках сайта есть этот домен без "www.", то "www." отбрасывается
        if (substr($domain, 0, 4) == "www.") {
            $host_without_www = substr($domain, 4);
            if (nc_module_check_by_keyword('auth')) {
                if ($nc_core->get_settings('with_subdomain', 'auth')) {
                    $domain = $host_without_www;
                }
            }
            else {
                $all_domains = $nc_core->catalogue->get_current('Domain') . " " .
                               $nc_core->catalogue->get_current('Mirrors');

                if (preg_match('/\b' . preg_quote($host_without_www, '/') . '\b/', $all_domains)) {
                    $domain = $host_without_www;
                }
            }
        }

        return $domain;
    }


    /**
     * @param string $name Имя куки
     * @param mixed $value Значение
     * @param int $expires Timestamp, до которого действует кука
     * @param bool $http_only Поставить флаг httponly
     */
    public function set($name, $value, $expires = 0, $http_only = false) {
        $nc_core = nc_core::get_object();
        setcookie($name, $value, $expires, '/', $this->get_domain(), null, $http_only);
        nc_core::get_object()->input->set('_COOKIE', $name, $value);
        $_COOKIE[$name] = $value;
    }

    public function remove($name) {
        setcookie($name, null, 1, '/', $this->get_domain());
        nc_core::get_object()->input->set('_COOKIE', $name, false);
        unset($_COOKIE[$name]);
    }

}