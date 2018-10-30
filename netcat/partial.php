<?php

/**
 * Возвращает врезку макета дизайна.
 * В настройках врезки должно быть разрешено его отдельное использование (асинхронная
 * врезка).
 * 
 * Входящие параметры:
 *  — template — идентификатор или ключевое слово макета дизайна;
 *  — partial — ключевое слово врезки (можно несколько через пробел или запятую, или в массиве),
 *    если в partial передаются данные через параметр $data, то к ключевому слову дописывается '?' и
 *    дополнительные параметры в виде query-строки (например: 'footer?mobile=1&user_name=John+Doe');
 *  — json — если 1, вернёт результат в виде JSON;
 *  — referer — страница, для которой загружается врезка;
 *  — любые другие переданные параметры будут также доступны внутри врезки
 *    в соответствующих переменных.
 */

const NC_PARTIAL = true;
require __DIR__ . '/connect_io.php';

$nc_core = nc_core::get_object();
$nc_core->modules->load_env();
$nc_core->user->attempt_to_authorize();

// Входящие данные
$nc_partial_template = $nc_core->input->fetch_get_post('template');
$nc_json = $nc_core->input->fetch_get_post('json');
$nc_partial_keywords = $nc_core->input->fetch_get_post('partial');
if (!is_array($nc_partial_keywords)) {
    $nc_partial_keywords = preg_split('/[\s,]+/', $nc_partial_keywords, -1, PREG_SPLIT_NO_EMPTY);
}
$nc_referer = $nc_core->input->fetch_post_get('referer');

// Функция отправки ответа
$nc_send_response = function(array $response) use ($nc_json) {
    if ($nc_json) {
        ob_clean();
        header('Content-Type: application/json');
        echo nc_array_json($response);
    } else {
        echo join('', $response);
    }
    die;
};

// Загрузка параметров макета и получение прочих переменных для страницы, указанной в referer
$template = $nc_partial_template;
$template_settings = null;
if ($nc_referer) {
    $nc_core->url = new nc_url($nc_referer);
    $REQUEST_URI = $_SERVER['REQUEST_URI'] = $_ENV['REQUEST_URI'] = $nc_core->url->get_local_url();

    if (empty($sub)) {
        $sub = $nc_core->catalogue->get_current('E404_Sub_ID');
    }

    // разбор пути
    if (strpos($nc_core->url->get_parsed_url('path'), $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH) !== 0) {
        require $nc_core->INCLUDE_FOLDER . 'e404.php';
    }

    $template = $nc_core->subdivision->get_current('Template_ID');

    // определение стандартных глобальных переменных
    require $nc_core->INCLUDE_FOLDER . 'index.php';

    if (!isset($action)) {
        $action = 'index';
    }

    // проверка прав на раздел
    if (!s_auth($nc_core->sub_class->get_current(), $action, false)) {
        $nc_send_response(array('_error' => NETCAT_MODERATION_ERROR_NORIGHT));
    }

    // загрузка $template_settings раздела
    if ($nc_core->template->get_root_id($template) == $nc_core->template->get_root_id($nc_partial_template)) {
        $template_settings = $nc_core->subdivision->get_template_settings($sub);
    }
}

if ($template_settings === null) {
    $template_settings = $nc_core->template->get_settings_default_values($nc_partial_template);
}

// подключение шаблонов вывода навигации макета (~ /netcat/require/index_fs.inc.php)
// ($template либо пришёл как GET-параметр, либо при наличии referer мог быть переопределён в /netcat/require/index.php)
$nc_template_view = $nc_core->template->get_file_template($template);
if (!$nc_template_view) {
    $nc_send_response(array('_error' => 'Wrong template'));
}

foreach ($nc_template_view->get_all_settings_path_in_array() as $nc_template_settings_path) {
    include_once $nc_template_settings_path;
}

// Подготовка результатов для partial
$nc_partials_result = array();
$nc_template_view = $nc_core->template->get_file_template($nc_partial_template);
if (!$nc_template_view) {
    $nc_send_response(array('_error' => 'Wrong template'));
}

foreach ($nc_partial_keywords as $nc_partial_keyword_with_data) {
    $nc_partial_keyword = $nc_partial_keyword_with_data;
    $nc_partial_data = array();
    if (strpos($nc_partial_keyword_with_data, '?')) {
        list($nc_partial_keyword, $nc_partial_data_string) = explode('?', $nc_partial_keyword_with_data, 2);
        parse_str($nc_partial_data_string, $nc_partial_data);
        $nc_core->security->add_checked_input(array('partial_data' => $nc_partial_data));
    }

    $nc_partial = $nc_template_view->partial($nc_partial_keyword, $nc_partial_data);
    if (!$nc_partial->exists()) {
        continue;
    }
    if ($nc_partial->is_async_load_enabled()) {
        $nc_partial_result = $nc_partial->with('nc_partial_async', true)->make();
        $nc_partial_result = $nc_core->security->xss_filter->filter($nc_partial_result);
        $nc_partials_result[$nc_partial_keyword_with_data] = $nc_partial_result;
    } else {
        $nc_error_message = "Asynchronous load is not enabled for template partial '" . htmlspecialchars($nc_partial_keyword) . "'";
        $nc_partials_result[$nc_partial_keyword_with_data] = $nc_error_message;
    }
}

$nc_send_response($nc_partials_result);
