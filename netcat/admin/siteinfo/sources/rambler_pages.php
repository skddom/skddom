<?php

$source = array(
        "name" => "Rambler",
        "url" => "http://search.rambler.ru/srch",
        "href" => "http://search.rambler.ru/srch?sort=0&oe=1251&limit=10&filter=".$this->url,
        "params" => array("sort" => 0, "oe" => 1251, "limit" => 10, "filter" => $this->url),
        "method" => "get",
        "pattern" => "/По запросу найдено ([\d]+\s?т?ы?с?м?л?н?+\.?)/iu"
);
?>