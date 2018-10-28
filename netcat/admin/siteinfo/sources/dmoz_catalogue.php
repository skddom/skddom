<?php

$source = array(
        "name" => "DMOZ",
        "url" => "http://www.dmoz.org/search",
        "href" => "http://www.dmoz.org/search?q=".$this->url."&cat=all&type=next&all=no&start=0",
        "params" => array('q' => $this->url, 'cat' => 'all', 'type' => 'next'),
        "method" => "get",
        "pattern" => "!(http://(?:www\.)?".preg_quote($this->url)."/)!",
        "replace" => array('/.+/', 'да'),
        "negative" => 'нет данных'
);