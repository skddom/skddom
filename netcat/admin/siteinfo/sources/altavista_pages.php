<?php

$source = array(
        "name" => "AltaVista",
        "url" => "http://www.altavista.com/web/results",
        "href" => "http://www.altavista.com/web/results?aqmode=s&rc=dmn&swd=".$this->url,
        "params" => array(aqmode => "s", rc => "dmn", swd => $this->url),
        "method" => "get",
        "pattern" => '!AltaVista\s+found\s+([\d,]+)\s+results!',
        "replace" => array('/,/', '')
);
?>