<?php

// Яндекс ТИЦ
// используем свой алгоритм для получения данных вместо стандартного из $site_auditor->get()
//if ($this->domain_level > 2) // lookup in the catalogue first
//{
/**
 * Варианты ответов: 
 *   "Индекс цитирования (тИЦ) ресурса меньше 10"
 *   "Индекс цитирования (тИЦ) ресурса — 2200"
 *   (каталог)
 */
$ret = $this->request_and_process(array(
                "name" => "Яndex ТИЦ (каталог)",
                "url" => "http://search.yaca.yandex.ru/yca/cy/ch/".$this->url."/",
                "href" => "http://search.yaca.yandex.ru/yca/cy/ch/".$this->url."/",
                "params" => null,
                "method" => "get",
                "pattern" => '!
        Индекс\s+цитирования[^<]*
        \<a\s+[^>]+>(\d+)\</a>
        !x'
                )
);

if ($ret["code"] == 200 && !$ret["ok"]) {
    // recieved smth, but not processed
    if (preg_match("/Индекс\s+цитирования\s+\(тИЦ\)\s+ресурса\s+меньше\s+10/", $ret["body"])) {
        $ret["ok"] = 1;
        $ret["value"] = "&lt; 10";
    }

    if (!$ret["ok"]) {
        if (preg_match('!<td[^>]+class="current"
        .+?
        <a[^>]+title="Кто\s+ссылается"[^>]*>
        (\d+)
        </a>
        !xs', $ret["body"], $regs)) {
            $ret["ok"] = 1;
            $ret["value"] = $regs[1];
        }
    }
}
//}
// if not found in catalogue or level==2
if (!$ret["ok"]) {
    $ret = $this->request_and_process(array(
                    "name" => "Яndex ТИЦ (бар)",
                    "url" => "http://bar-navig.yandex.ru/u",
                    "href" => "http://search.yaca.yandex.ru/yca/cy/ch/".$this->url."/",
                    "params" => array(ver => 2, lang => 1049, show => 1, thc => 0, url => "http://".$this->url."/"),
                    "method" => "get",
                    "pattern" => '|\<tcy\s+[^>]*value="(\d+)"|'
                    )
    );
}
?>