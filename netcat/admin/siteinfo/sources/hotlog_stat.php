<?php

$url = nc_preg_replace("/^www\./", '', $this->url);

$source = array(
        "name" => "HotLog (хиты/хосты)",
        "url" => "http://hotlog.ru/topsearch",
        "params" => array(cat_id => 1, search => 'www.'.$url),
        "method" => "get",
        "href" => "http://hotlog.ru/topsearch?page2=1&search=www.".$url,
        "pattern" => '/<td.*Количество посетителей.*>(\d+)<\/td>/iU',
        "reverse" => true,
        "negative" => 'нет данных'
);