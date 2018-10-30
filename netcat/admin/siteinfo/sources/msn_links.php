<?php

$source = array(
        "name" => "MSN",
        "url" => "http://search.msn.com/results.aspx",
        "href" => "http://search.msn.com/results.aspx?q=".$this->url."+-%73%69%74%65%3a".$this->url."&FORM=QBRE",
        "params" => array(q => $this->url." -site:".$this->url, FORM => "QBRE"),
        "method" => "get",
        "pattern" => '!<span.*?count.*?>.*?из(.+?)</span>!',
        "replace" => array('/(&#160;)/', '')
);
?>