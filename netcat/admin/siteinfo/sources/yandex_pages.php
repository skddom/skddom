<?php

$source = array(
        "name" => "Яndex",
        "url" => "http://yandex.ru/yandsearch",
        "href" => "http://yandex.ru/yandsearch?text=&ras=1&lr=213&site_manually=true&site=".$this->url, //serverurl=".$this->url
        "params" => array(text => "", site => $this->url, ras => 1, lr => 213, site_manually => true), //serverurl => $this->url
        "method" => "get",
        "pattern" => '/Нашлось<br>([\d]+&nbsp;.*)\s?ответ/'
        //"replace"=> array('/&nbsp;тыс\./', '000')
);
?>