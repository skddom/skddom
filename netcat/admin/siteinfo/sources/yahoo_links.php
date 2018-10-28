<?php

$source = array(
        "name" => "Yahoo",
        "url" => "http://siteexplorer.search.yahoo.com/search",
        "params" => array('p' => $this->url, 'bwm' => 'i', 'bwmf' => 's', 'bwmo' => '', 'fr2' => 'seo-rd-se'),
        //w3
        "href" => "http://siteexplorer.search.yahoo.com/search?p=".$this->url."&bwm=i&bwmf=s&bwmo=&fr2=seo-rd-se",
        "method" => "get",
        "pattern" => '/Pages \(([\d]+.*)\)/iU',
        "replace" => array('/,/', '')
);