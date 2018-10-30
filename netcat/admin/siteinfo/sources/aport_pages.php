<?php

$url = !preg_match("/^www\./", $this->url) ? "www.".$this->url : $this->url;

$source = array(
        "name" => NETCAT_MODULE_AUDITOR_APORT,
        "url" => "http://sm.aport.ru/scripts/template.dll",
        "params" => array(That => "std", r => "URL=".$url),
        "href" => "http://sm.aport.ru/scripts/template.dll?That=std&r=URL%3D".$url,
        "method" => "get",
        "pattern" => '!Лучшие\s+<b>(\d+)\s+</b>\s+\(<b>\d+\s+</b>&nbsp;сайт\)\s+документ!'
);
?>