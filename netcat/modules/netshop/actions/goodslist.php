<?php

/**
 * Различные действия со списком товаров (пользовательским)
 *
 * Запросы должны выполняться методом GET.
 * Входящие параметры:
 *
 *   - type: тип списка (recent, favorite, compare)
 *   - action: название действия — toggle, add, remove
 *   - class_id: идентификатор компонента товара
 *   - item_id: идентификатор объекта
 *   - return_url: страница, на которую будет выполнен редирект (по умолчанию — HTTP_REFERER)
 *   - no_redirect: не выполнять редирект 
 *
 */

require realpath(dirname(__FILE__) . "/../../../../") . "/vars.inc.php";
require_once($INCLUDE_FOLDER."unicode.inc.php");
require_once ($ROOT_FOLDER.'connect_io.php');
$nc_core->load_env($catalogue, $sub, $cc);
$MODULE_VARS = $nc_core->modules->load_env($modules_lang);
$nc_core->user->attempt_to_authorize();

/** @var nc_input $input */
$input = nc_core('input');

$type = $input->fetch_get('type');
$action = $input->fetch_get('action');
$class_id = $input->fetch_get('class_id');
$item_id = $input->fetch_get('item_id');

/** @var nc_netshop $netshop */
$netshop = nc_modules('netshop');

switch($type) {
    case 'recent':
        $control = $netshop->goodslist_recent;
        break;
    case 'favorite':
        $control = $netshop->goodslist_favorite;
        break;
    case 'compare':
        $control = $netshop->goodslist_compare;
        break;
    default:
        $control = null;
}

if ($control) {
    switch($action) {
        case 'toggle':
            $control->toggle($item_id, $class_id);
            break;
        case 'add':
            $control->add($item_id, $class_id);
            break;
        case 'remove':
            $control->remove($item_id, $class_id);
            break;
    }
}

$return_url = $input->fetch_get('return_url') ?: $_SERVER['HTTP_REFERER'];
if ($nc_core->security->url_matches_local_site($return_url) && !$input->fetch_get('no_redirect')) {
    header('Location: ' . $return_url);
}
