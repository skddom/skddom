<?php
/*=========== Skylab interactive - 1.1.2 ========================*/
const SITESECURE_HOST = 'https://sitesecure.ru';

function sitesecure_Domains() {
    global $nc_core, $db;
    $domains = $db->get_col("SELECT `Domain` FROM `Catalogue` WHERE `Checked` = 1 ORDER BY `Priority`");
    if (!empty($domains)) {
        return implode(',', $domains);
    }
    return "";
}

function sitesecure_Request($endpoint, $params = array()) {
    global $nc_core;
    if (empty($params['websites'])) {
        $params['websites'] = sitesecure_Domains();
    }
    if (empty($params['api_key'])) {
        $params['api_key'] = $nc_core->get_settings('apikey', 'sitesecure');
    }
    if (empty($params['websites'])) {
        return false;
    }
    if (empty($params['api_key'])) {
        return false;
    }

    $url = SITESECURE_HOST . '/api/netcat/' . $endpoint . '.json?' . http_build_query($params);
    return json_decode(file_get_contents($url));
}

function sitesecure_PostRequest($endpoint, $params = array()) {
    global $nc_core;
    if (empty($params['websites'])) {
        $params['websites'] = sitesecure_Domains();
    }
    if (empty($params['api_key'])) {
        $params['api_key'] = $nc_core->get_settings('apikey', 'sitesecure');
    }
    if ($endpoint != "register" && empty($params['websites'])) {
        return false;
    }
    if ($endpoint != "register" && empty($params['api_key'])) {
        return false;
    }

    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($params),
        ),
    );
    $url = SITESECURE_HOST . '/api/netcat/' . $endpoint . '.json';
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result);
}
