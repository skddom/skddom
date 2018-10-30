<?php

$source = array(
        "name" => "Mail.ru (посетители/хосты/визиты)",
        "url" => "http://top.mail.ru/stat/?url=http://".$this->url,
        "href" => "http://top.mail.ru/stat/?url=http://".$this->url,
        "params" => array(url => "http://".$this->url),
        "method" => "get",
        "pattern" => '!Посетители.*?
        <td.*?>Сегодня</td>
        .*?
        <td>.*?</td>
        .*?
        <td>.*?</td>
        .*?
        <td>.*?</td>
        .*?
       <td>.*?<b>([0-9,.]+)</b>.*?
       Хосты.*?
        <td.*?>Сегодня</td>
        .*?
        <td>.*?</td>
        .*?
        <td>.*?</td>
        .*?
        <td>.*?</td>
        .*?
       <td>.*?<b>([0-9,.]+)</b>.*?
      Визиты.*?
        <td.*?>Сегодня</td>
        .*?
        <td>.*?</td>
        .*?
        <td>.*?</td>
        .*?
        <td>.*?</td>
        .*?
       <td>.*?<b>([0-9,.]+)</b>.*?
     !xsui',
        "replace" => array('/,/', ''),
        "negative" => "нет данных"
);
?>