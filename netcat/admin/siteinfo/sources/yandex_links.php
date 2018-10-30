<?php

$source = array(
        "name" => NETCAT_MODULE_AUDITOR_YANDEX,
        "url" => "http://www.yandex.ru/yandsearch",
        "href" => "http://www.yandex.ru/yandsearch?Link=".$this->url,
        "params" => array(Link => $this->url),
        "method" => "get",
        "pattern" => '!Результат поиска:\s+страниц.+?<b>([\d&nbsp;]+)</b>,\s+сайтов.+?<b>([\d&nbsp;]+)!',
        "replace" => array('/&nbsp;/', '')
);
?>