<?php

$source = array(
        "name" => 'Liveinternet (хиты/хосты)',
        "url" => "http://www.liveinternet.ru/stat/".$this->url."/index.html?period=day",
        "href" => "http://www.liveinternet.ru/stat/".$this->url."/index.html?period=day",
        "params" => array(),
        "method" => "get",
        "pattern" => '!<label.+?>Просмотры</label>.*?
              <td>([\d,]+).*?
              <label.+?>Хосты</label>.*?
              <td>([\d,]+)
              !xs',
        "replace" => array('/,/', ''),
        "negative" => 'нет данных'
);
?>