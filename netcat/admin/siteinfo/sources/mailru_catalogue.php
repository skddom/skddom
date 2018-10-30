<?php

$source = array(
        "name" => "Mail.ru",
        "url" => "http://search.list.mail.ru/",
        "href" => "http://search.list.mail.ru/?q=".$this->url,
        "params" => array(q => $this->url),
        "method" => "get",
        "pattern" => "/(<cite>[https:\/w.]+?".$this->url."[\/]?<\/cite>)/",
        "replace" => array('/^.+$/', 'да'),
        "negative" => 'нет данных'
);
?>