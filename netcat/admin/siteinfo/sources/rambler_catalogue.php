<?php

$source = array(
        "name" => "Rambler",
        "url" => "http://search.rambler.ru/cgi-bin/rambler_search",
        "href" => "http://search.rambler.ru/cgi-bin/rambler_search?where=0&words=".$this->url,
        "params" => array(where => 0, words => $this->url),
        "method" => "get",
        "pattern" => "!(http://(?:www\.)?".preg_quote($this->url)."/)!",
        "replace" => array('/^.+$/', 'да'),
        "negative" => 'нет данных'
);
?>