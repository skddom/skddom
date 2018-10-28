<?php

$nc_temp_key = md5($this->url);

$source = array(
        "name" => "Nigma.ru",
        "url" => "http://nigma.ru/index.php",
        "href" => "http://nigma.ru/index.php?s=-".$nc_temp_key."+site%3A+".$this->url,
        "params" => array(s => "-".$nc_temp_key." site: ".$this->url),
        "method" => "get",
        "pattern" => '/<span>([0-9]+)<\/span> результатов<\/td>/isu',
        "replace" => array('/&nbsp;/', '')
);

unset($nc_temp_key);
?>