<?php

$source = array(
        "name" => "Google",
        "url" => "http://www.google.com/search",
        "params" => array(hl => 'en', q => "site:".$this->url, filter => 0),
        "href" => "http://www.google.com/search?hl=en&filert=0&q=site%3A".$this->url,
        "method" => "get",
        "pattern" => '/About ([\d]+.*) results/iU',
        "replace" => array('/,/', '')
);
?>