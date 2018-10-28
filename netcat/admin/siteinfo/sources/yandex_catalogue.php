<?php

$source = array(
        "name" => "Яndex",
        "url" => "http://search.yaca.yandex.ru/yandsearch",
        "href" => "http://search.yaca.yandex.ru/yandsearch?text=".$this->url."&rpt=rs2",
        "params" => array(text => $this->url, rpt => "rs2"),
        "method" => "get",
        "pattern" => '/((?:сайтов.*\d+)|(?:страниц&nbsp;&#151;\s*<b>\d+))/iU',
        "replace" => array('/.+/', 'да'),
        "negative" => NETCAT_MODULE_AUDITOR_NOT_DATA
);
?>