<?php

$source = array(
        "name" => "Google",
        "url" => "http://www.google.com/search",
        "params" => array(hl => 'en', q => "link:".$this->url),
        "href" => "http://www.google.com/search?hl=en&q=link%3A".$this->url,
        "method" => "get",
        "pattern" => '/About ([\d]+.*) results/iU',
        "replace" => array('/,/', '')
);
?>