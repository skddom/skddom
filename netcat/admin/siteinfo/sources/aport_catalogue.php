<?php

$source = array(
        "name" => NETCAT_MODULE_AUDITOR_APORT,
        "href" => "http://sm.aport.ru/scripts/template.dll?That=std&Tn=6&CL=0&r=url=".$this->url,
        "url" => "http://sm.aport.ru/scripts/template.dll",
        "params" => array(That => "std", Tn => 6, CL => 0, r => "url=".$this->url),
        "method" => "get",
        "pattern" => "!(http://(?:www\.)?".preg_quote($this->url)."/)!",
        "replace" => array('/.+/', 'да'),
        "negative" => NETCAT_MODULE_AUDITOR_NOT_DATA
);
?>