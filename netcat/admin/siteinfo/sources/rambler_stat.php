<?php

$source = array(
        "name" => 'Rambler (ИП/Посетители/Просмотры/Просм. Гл. Стр)',
        "href" => "http://top100.rambler.ru/?query=http%3A%2F%2F".$this->url."&stat=1",
        "url" => "http://top100.rambler.ru/?query=http%3A%2F%2F".$this->url."&stat=1",
        "params" => array(stat => 1, query => "http://".$this->url."/"),
        "method" => "get",
        "pattern" => '!.*?
        <td>.*?http://(?:www\.)?'.$this->url.'.*?</td>
        .*?
        <td.*?>(.*?)</td>
        .*?
        <td.*?>(.*?)</td>
        .*?
        <td.*?>(.*?)</td>
        .*?
        <td.*?>(.*?)</td>.*?
     !xsui',
        "replace" => array('/,/', ''),
        "negative" => "нет данных"
);
?>