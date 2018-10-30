<?php

$source = array(
        "name" => "MSN",
        "url" => "http://www.bing.com/search",
        "href" => "http://www.bing.com/search?q=site%3A" . $this->url,
        "params" => array("q" => "site:" . $this->url),
        "method" => "get",
        "pattern" => '!<span.*?count.*?>.*?Результатов: (\d+?)</span>!iu',
        "replace" => array('/[^\d]/', '')
);
?>