<?php

$id_data = $this->request_and_process(array(
                "name" => NETCAT_MODULE_AUDITOR_SPYLOG,
                "url" => "http://".$this->url,
                "params" => array(),
                "method" => "get",
                "pattern" => '!spylog\.com\/cnt\?cid=(\d+)!xs'
                )
);

if ($id_data["value"]) {
    $ret = $this->request_and_process(array(
                    "name" => NETCAT_MODULE_AUDITOR_SPYLOG,
                    "url" => "http://dir1.spylog.ru/rstat.phtml?site=".$id_data['value'],
                    "href" => "http://dir1.spylog.ru/rstat.phtml?site=".$id_data['value'],
                    "params" => array(site => $id_data['value']),
                    "method" => "get",
                    "pattern" => '!Суммарная\s+статистика.*?
        &nbsp;Количество\s+хитов.*?<.*?><.*?>(\d+).*?
        &nbsp;Количество\s+хостов.*?<.*?><.*?>(\d+)
        !xs',
                    "negative" => NETCAT_MODULE_AUDITOR_NOT_DATA
                    )
    );
} else {
    $ret["name"] = NETCAT_MODULE_AUDITOR_SPYLOG;
    $ret["code"] = 200;
    $ret["value"] = "счетчик не найден";
    $ret["ok"] = true;
}
?>