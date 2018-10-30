<?php

$source = array(
        "name" => "Yahoo",
        "url" => "http://ru.search.yahoo.com/search",
        "href" => "http://ru.search.yahoo.com/search?p=site%3A" . $this->url . "&toggle=1&cop=mss&ei=UTF-8",
        "params" => array("p" => "site:" . $this->url, "toggle" => 1, "cop" => "mss", "ei" => "UTF-8"),
        "method" => "get",
        "pattern" => '!<span.*?resultCount.*?>(.*?)<\/span>!iu'
);
?>