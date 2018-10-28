<?php

/**
 *
 * @param int $sub
 * @param int $cc
 * @param string $query_string
 * @param bool $show_in_admin_mode
 * @param bool $get_current_cc
 * @return bool|string
 */
function nc_objects_list($sub, $cc, $query_string = "", $show_in_admin_mode = false, $get_current_cc = true) {

    // *** Обработка параметров: $query_string ***

    $LIST_VARS = array();
    parse_str($query_string, $LIST_VARS);
    // переменные, которые не будут импортированы из $query_string:
    unset(
        // аргументы функции
        $LIST_VARS['sub'],
        $LIST_VARS['cc'],
        $LIST_VARS['show_in_admin_mode'],
        $LIST_VARS['get_current_cc'],
        // superglobals
        $LIST_VARS['GLOBALS'],
        $LIST_VARS['_SERVER'],
        $LIST_VARS['_GET'],
        $LIST_VARS['_POST'],
        $LIST_VARS['_FILES'],
        $LIST_VARS['_COOKIE'],
        $LIST_VARS['_SESSION'],
        $LIST_VARS['_REQUEST'],
        $LIST_VARS['_ENV']
    );
    extract($LIST_VARS);


    // *** Глобальные переменные ***

    /** @var Permission $perm */
    if ($GLOBALS['perm'] instanceof Permission) {
        $perm = $GLOBALS['perm'];
    }
    else {
        $perm = false; // :)
    }

    global $UI_CONFIG, $_cache, $admin_url_prefix, $classPreview;
    global $AUTH_USER_ID, $AUTH_USER_GROUP, $current_user;
    global $sub_level_count, $parent_sub_tree;
    global $cc_array;
    global $subHost;
    // for old modules (forum)
    global $current_catalogue, $current_sub, $current_cc;
    global $nc_parent_template_folder_path;
    global $nc_minishop;


    // *** Необходимые локальные переменные ***

    $nc_core = nc_Core::get_object();
    $db = $nc_core->db;

    // modules variables
    $MODULE_VARS = $nc_core->modules->get_module_vars();
    // system variables
    $FILES_FOLDER      = $nc_core->get_variable("FILES_FOLDER");
    $HTTP_ROOT_PATH    = $nc_core->get_variable("HTTP_ROOT_PATH");
    $ADMIN_PATH        = $nc_core->get_variable("ADMIN_PATH");
    $ADMIN_TEMPLATE    = $nc_core->get_variable("ADMIN_TEMPLATE");
    $DOMAIN_NAME       = $nc_core->get_variable("DOMAIN_NAME");
    $SHOW_MYSQL_ERRORS = $nc_core->get_variable("SHOW_MYSQL_ERRORS");
    $AUTHORIZE_BY      = $nc_core->get_variable("AUTHORIZE_BY");
    $HTTP_FILES_PATH   = $nc_core->get_variable("HTTP_FILES_PATH");
    $DOCUMENT_ROOT     = $nc_core->get_variable("DOCUMENT_ROOT");
    $SUB_FOLDER        = $nc_core->get_variable("SUB_FOLDER");

    $inside_admin = $nc_core->inside_admin;
    $admin_mode   = $nc_core->admin_mode;

    $system_env        = $nc_core->get_settings();
    $current_catalogue = $nc_core->catalogue->get_current();

    if ($get_current_cc) {
        $current_sub = $nc_core->subdivision->get_current();
        $current_cc  = $nc_core->sub_class->get_current();
    }

    // [MERGE] $ignore_eval is always an empty array ?????????
    $ignore_eval = array();

    //$srchPat дважды urldecodeд и "+" теряется, берем значения из $_REQUEST которые уже один раз urldecodeд
    //если $_REQUEST['srchPat'] пустой, то srchPat передался через s_list_class, сохраняем его
    $srchPat = isset($srchPat)
                ? $nc_core->input->fetch_get_post('srchPat')
                    ? $nc_core->input->fetch_get_post('srchPat')
                    : $srchPat
                : null;
    $srchPatAdd = isset($srchPatAdd)
                ? $nc_core->input->fetch_get_post('srchPatAdd')
                    ? $nc_core->input->fetch_get_post('srchPatAdd')
                    : $srchPatAdd
                : null;


    // *** Санация переменных ***

    $cc = (int)$cc;
    $parent_message = isset($parent_message) ? (int)$parent_message : 0;

    if (!$cc) {
        return false;
    }


    // *** Переменные, которые могут устанавливаться только в системных настройках ***

    $ignore_all       = false;
    $ignore_catalogue = false;
    $ignore_sub       = false;
    $ignore_cc        = false;
    $ignore_check     = false;
    $ignore_parent    = false;
    $ignore_user      = true;
    $ignore_calc      = false;
    $ignore_link      = false;
    $ignore_prefix    = false;
    $ignore_suffix    = false;
    $distinct         = false;
    $distinctrow      = false;
    $message_select   = null;
    $query_from       = null;
    $query_group      = null;
    $query_join       = null;
    $query_order      = null;
    $query_select     = null;
    $query_where      = null;
    $query_having     = null;
    $query_limit      = null;
    $nc_data          = null;

    $result_vars = '';

    // *** Значения по умолчанию / инициализация переменных ***

    if (!isset($nc_title)) { $nc_title = false; }
    if (!isset($isMainContent)) { $isMainContent = false; }
    if (!isset($isSubClassArray)) { $isSubClassArray = false; }
    if (!isset($cur_cc)) { $cur_cc = false; }
    if (!isset($curPos)) { $curPos = 0;}
    if (!isset($recNum)) { $recNum = 0;}
    if (!isset($list_mode)) { $list_mode = null; }

    $nc_ctpl = !empty($nc_ctpl) ? $nc_ctpl : 0;
    if (!$nc_ctpl && $nc_title) { $nc_ctpl = 'title'; }

    // [MERGE] was in _db version only
    //    if (+$_REQUEST['isModal']) {
    //        $inside_admin = false;
    //        $admin_mode = false;
    //    }


    // *** Информация о редактировании инфоблока из другого раздела ***
    try {
        if (($cc != $current_cc['Sub_Class_ID'] || !$isMainContent) && $admin_mode && !$show_in_admin_mode) {
            $Subdivision_ID = $nc_core->sub_class->get_by_id($cc, 'Subdivision_ID');
            $Subdivision_Name = $nc_core->subdivision->get_by_id($Subdivision_ID, 'Subdivision_Name');
            return nc_print_status(
                sprintf(
                    CONTROL_CONTENT_SUBCLASS_EDIT_IN_PLACE,
                    $SUB_FOLDER . $HTTP_ROOT_PATH . "index.php?inside_admin=" . $inside_admin . "&sub=" . $Subdivision_ID . "&cc=" . $cc,
                    $Subdivision_Name),
                'info', null, true
            );
        }


        // *** Получение параметров инфоблока ***

        $cc_env = $nc_core->sub_class->get_by_id($cc, null, $nc_ctpl);

        try {
            $wrong_nc_ctpl = (
                $nc_ctpl &&
                $nc_ctpl != 'title' &&
                $nc_ctpl != $cc_env['Class_ID'] &&
                ($cc_env['Class_ID'] != $nc_core->component->get_component_template_by_keyword($cc_env['Class_ID'], $nc_ctpl, 'ClassTemplate'))
            );

            if ($wrong_nc_ctpl) {
                throw new Exception();
            }
        }
        catch (Exception $e) {
            $wrong_nc_ctpl = "". $e->getMessage();
            $cc_env = $nc_core->sub_class->get_by_id($cc);
        }

        if ($admin_mode && $cc_env['Edit_Class_Template']) {
            $cc_env = $nc_core->sub_class->get_by_id($cc, null, $cc_env['Edit_Class_Template']);
        }

        if ($inside_admin && $cc_env['Admin_Class_Template']) {
            $cc_env = $nc_core->sub_class->get_by_id($cc, null, $cc_env['Admin_Class_Template']);
        }
    }
    catch (Exception $e) {
        trigger_error($e->getMessage(), E_USER_WARNING);
        if ($current_user && $current_user['InsideAdminAccess']) {
            return $e->getMessage();
        }
        else {
            return '';
        }
    }

    if (!isset($isNaked)) {
        $isNaked = $cc_env['isNaked'];
    }

    // set user table mode
    $user_table_mode = (bool)$cc_env['System_Table_ID'];

    // Просмотр в виде таблицы
    $table_view_mode = (bool)$cc_env['TableViewMode'];

    // *** Зеркальные инфоблоки ***

    $_db_cc        = $cc;
    $_db_sub       = $sub = $cc_env['Subdivision_ID'];
    $_db_catalogue = $catalogue = $cc_env['Catalogue_ID'];
    $_db_Class_ID  = $cc_env['Real_Class_ID'];
    $_db_File_Path = $cc_env['File_Path'];
    $_db_File_Hash = $cc_env['File_Hash'];

    // Для зеркальных инфоблоков:

    if ($cc_env['SrcMirror']) {
        $source_env = $nc_core->sub_class->get_by_id($cc_env['SrcMirror'], null, $nc_ctpl);
        $cc         = $source_env['Sub_Class_ID'];
        $sub        = $source_env['Subdivision_ID'];
        $catalogue  = $source_env['Catalogue_ID'];
        $is_mirror  = true;
    }
    else {
        $mirror_env = null;
        $is_mirror = false;
    }

    // записываем реальный номер шаблона компонента
    if ($nc_ctpl === 'title') {
        $nc_ctpl = $cc_env['Real_Class_ID'];
    }


    if ($cc_env['Type'] == 'rss' || $cc_env['Type'] == 'xml') {
        $cc_env['Cache_Access_ID'] = 2;
    }


    // *** Режим работы: шаблоны в файлах или в базе? ***

    $component_file_mode = (bool)$cc_env['File_Mode'];

    if ($table_view_mode && $inside_admin) {
        $component_file_mode = true;
    }

    // *** Подготовка переменных для предварительного просмотра ***

    // если preview для нашего класса, то подменим cc_env из $_SESSION
    if ($classPreview == ($cc_env["Class_Template_ID"] ? $cc_env["Class_Template_ID"] : $cc_env["Class_ID"])) {
        $magic_gpc = get_magic_quotes_gpc();
        if (!empty($_SESSION["PreviewClass"][$classPreview])) {
            foreach ($_SESSION["PreviewClass"][$classPreview] as $tkey => $tvalue) {
                $cc_env[$tkey] = $magic_gpc ? stripslashes($tvalue) : $tvalue;
            }
        }
        // Запретим кеширование в режиме предпросмотра.
        $cc_env['Cache_Access_ID'] = 2;
    }


    // *** Проверка прав доступа ***

    if ($cc_env['Read_Access_ID'] > 1 && !$AUTH_USER_ID) {
        return false;
    }

    if ($AUTH_USER_ID && $cc_env['Read_Access_ID'] > 2) {
        if (!CheckUserRights($cc, 'read', 1)) {
            return false;
        }
    }


    // *** Состояние модулей, влияющих на работу данной функции ***

    $routing_module_enabled = nc_module_check_by_keyword('routing');
    $cache_module_enabled   = nc_module_check_by_keyword('cache');


    // *** Названия CSS-классов для блоков, в которых выводится содержимое ***
    // $nc_component_css_class, $nc_component_css_selector, $nc_block_id — часть API
    $nc_add_block_markup =
        (!$isNaked || $admin_mode) && // не добавлять разметку в режиме просмотра, если есть isNaked
        $nc_core->component->can_add_block_markup($cc_env['Class_Template_ID'] ?: $cc_env['Class_ID']);

    if ($nc_add_block_markup) {
        static $nc_objects_list_count = 0;
        ++$nc_objects_list_count;
        $nc_component_css_class = $nc_core->component->get_css_class_name($cc_env['Class_Template_ID'] ?: $cc_env['Class_ID']);
        $nc_component_css_selector = '.' . str_replace(' ', '.', $nc_component_css_class);
        $nc_block_id = nc_make_block_id("$nc_objects_list_count/$cc");
        $nc_core->page->register_component_usage($cc_env['Class_ID'], $cc_env['Class_Template_ID']);
    }
    else {
        $nc_component_css_class = $nc_component_css_selector = $nc_block_id = null;
    }


    // *** Проверка наличия результата в кэше ***
    $nc_cache_list = null;
    $cached_result = -1;
    $cached_data = "";
    $cached_eval = false;
    $cache_key = null;
    if ($cache_module_enabled && $cc_env['Cache_Access_ID'] == 1 && !$user_table_mode) {
        // startup values

        $nc_cache_list = nc_cache_list::getObject();
        try {
            // cache auth add-on string
            $cache_for_user = $nc_cache_list->authAddonString($cc_env['CacheForUser'], $current_user);
            $cache_key = $query_string . $cache_for_user . "type=" . $cc_env['Type'] . "classtemplate=" . $cc_env['ClassTemplate'];
            // check cached data
            $cached_result = $nc_cache_list->read($_db_sub, $_db_cc, $cache_key, $cc_env['Cache_Lifetime']);
            if ($cached_result != -1) {
                // get cached parameters
                list ($cached_data, $cached_eval, $cache_vars) = $cached_result;
                // debug info
                $cache_debug_info = "Read, sub[" . $_db_sub . "], cc[" . $_db_cc . "], Access_ID[" . $cc_env['Cache_Access_ID'] . "], Lifetime[" . $cc_env['Cache_Lifetime'] . "], bytes[" . strlen($cached_data) . "], eval[" . (int)$cached_eval . "]";
                $nc_cache_list->debugMessage($cache_debug_info, __FILE__, __LINE__);
                // return cache if eval flag is not set
                if (!$cached_eval) {
                    return $cached_data;
                }
            }
// [MERGE] overwritten unconditionally below!
            // set marks into the fields
//            $no_cache_marks = $nc_cache_list->nocacheStore($cc_env);
        }
        catch (Exception $e) {
            $nc_cache_list->errorMessage($e);
        }
    }


    // *** Подготовка прочих переменных; подготовка к вычислению «системных настроек» ***

    // Если присутствует параметр isSubClassArray в вызове функции nc_objects_list(), то добавляем
    // в массив $cc_env элемент cur_cc, который будет участвовать в формировании навигации по страницам
    // при отображении нескольких шаблонов на странице
    if (isset($isSubClassArray) && $isSubClassArray) {
        $cc_env['cur_cc'] = $_db_cc;
    }

    // [MERGE] not used, not in API
    //    $allowTags = $cc_env['AllowTags'];
    //    $NL2BR = $cc_env['NL2BR'];


    $intQueryStr = '?';

    // $cc_settings — пользовательские настройки инфоблока
    $cc_settings = & $cc_env["Sub_Class_Settings"];

    // $subLink, $ccLink, $cc_keyword
    if ($admin_mode) {
        $subLink = $admin_url_prefix .  '?catalogue=' . $_db_catalogue . '&amp;sub=' . $_db_sub;
        $cc_keyword = null;
        $ccLink = $subLink . '&amp;cc=' . $_db_cc;
        $intQueryStr = $ccLink;
    }
    else if ($routing_module_enabled) {
        $subLink = new nc_routing_path_folder($_db_sub);
        $ccLink = new nc_routing_path_infoblock($_db_cc);
        $cc_keyword = $cc_env['EnglishName'];
    }
    else {
        $subLink = $SUB_FOLDER . $cc_env['Hidden_URL'];
        $cc_keyword = $cc_env['EnglishName'];
        $ccLink = $subLink . $cc_keyword . '.html';
    }

    // переменные curPos, recNum нужно привести к "правильному" виду
    // до И после выполнения системных настроек компонента
    $curPos = +$curPos;
    if ($curPos < 0) {
        $curPos = 0;
    }
    $recNum = +$recNum;
    if ($recNum < 0) {
        $recNum = 0;
    }

    $maxRows = $cc_env['RecordsPerPage'];
    $ignore_limit = null;
    $SortBy = $cc_env['SortBy'];
    $classID = $cc_env['Class_ID'];
    $userTableID = $cc_env['System_Table_ID'];

    $no_cache_marks = 0;

    if (isset($MODULE_VARS['searchold']['INDEX_TABLE']) && $MODULE_VARS['searchold']['INDEX_TABLE'] == $classID) {
        $ignore_eval['sort_by'] = true;
    }

    $file_class = null;


    // *** Вычисление «системных настроек» шаблона ***

    if ($component_file_mode) {
        $file_class = new nc_tpl_component_view($nc_core->CLASS_TEMPLATE_FOLDER, $nc_core->db);

        // Переменные, доступные в шаблонах компонента
        $nc_parent_class_folder_path = nc_get_path_to_main_parent_folder($cc_env['File_Path']);
        // два названия: одно без ошибок, другое указано в документации [5.4]
        $nc_class_aggregator_path = $nc_class_agregator_path = $nc_core->INCLUDE_FOLDER . 'classes/nc_class_aggregator_setting.class.php';
        // clear this variable after system settings eval!
        $result = "";

        // check and include component part
        // На странице может подключаться один и тот же компонент, у которого есть системные настройки
        // На текущий момент необходим include, а не include_once
        try {
            if ($table_view_mode && $inside_admin) {
                $component_file_class = new nc_tpl_component_view($nc_core->CLASS_TEMPLATE_FOLDER, $nc_core->db);
                // Обычный вид
                $component_file_class->load($_db_Class_ID, $_db_File_Path, $_db_File_Hash);
                // Табличный вид
                $file_class->load('table', '/table/', $_db_File_Hash);
                // Переменная, доступная в шаблонах компонента
                $nc_parent_field_path = $component_file_class->get_parent_field_path('Settings');
                // Путь к файлу системных настроек для обычного вида
                $nc_component_field_path = $component_file_class->get_field_path('Settings');
                // Путь к файлу системных настроек для табличного вида
                $nc_field_path = $file_class->get_field_path('Settings');
                if (nc_check_php_file($nc_component_field_path)) {
                    // Настройки обычного вида
                    include $nc_component_field_path;
                }
            } else {
                // Обычный вид
                $file_class->load($_db_Class_ID, $_db_File_Path, $_db_File_Hash);
                // Переменная, доступная в шаблонах компонента
                $nc_parent_field_path = $file_class->get_parent_field_path('Settings');
                // Путь к файлу системных настроек для обычного вида
                $nc_field_path = $file_class->get_field_path('Settings');
            }

            if (nc_check_php_file($nc_field_path)) {
                // Файл системных настроек табличного ИЛИ обычного вида
                include $nc_field_path;
            }
        } catch (Exception $e) {
            if ($perm instanceof Permission && $perm->isSubClassAdmin($cc)) {
                // error message
                $result .= sprintf(CONTROL_CLASS_CLASSFORM_CHECK_ERROR, CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_SYSTEM);
            }
        }
        $nc_parent_field_path = null;
        $nc_field_path = null;
    } else {
        // «компоненты v4»
        if ($cc_env['Settings']) { eval(nc_check_eval($cc_env['Settings'])); }
    }

    if ($ignore_limit === null) {
        $ignore_limit = (!$maxRows && !$recNum);
    }


    // *** Сброс переменных после вычисления «системных настроек» ***

    $curPos = +$curPos;
    if ($curPos < 0) {
        $curPos = 0;
    }

    $recNum = +$recNum;
    if ($recNum < 0) {
        $recNum = 0;
    }


    $result = "";


    // *** Данные не из запроса, а подготовленные в системных настройках — $nc_data ***

    $nc_prepared_data = 0;
    if (isset($nc_data) && (is_array($nc_data) || $nc_data instanceof ArrayAccess)) {
        $nc_prepared_data = 1;
    }


    // *** Подготовка переменных для построения запроса к БД ***

    // выйдем, если нет идентификатора шаблона, поскольку дальше работа функции бессмысленна
    if (!$classID) {
        return false;
    }

    $component = $nc_core->get_component($cc_env['System_Table_ID'] ? 'User' : $classID);
    $field_vars = null;
    $date_field = null;

    if (!$nc_prepared_data) { // данные будут получены из запроса к БД

        if (!$SortBy) {
            $sort_by = "a." . ($user_table_mode ? "`" . $AUTHORIZE_BY . "`" : "`Priority` DESC") . ", a.`LastUpdated` DESC";
        }
        else {
            $sort_by = $SortBy;
        }

        $field_names = $component->get_fields_query();
        $field_vars = $component_file_mode ? null : $component->get_fields_vars();
        $multilist_fields = $component->get_fields(NC_FIELDTYPE_MULTISELECT);
        $date_field = $component->get_date_field();
        // разрешить html-теги и перенос строки
        $cc_env['convert2txt'] = "";

        $text_fields = $component->get_fields(NC_FIELDTYPE_TEXT);
        foreach ($text_fields as $field) {
            $format = nc_field_parse_format($field['format'], NC_FIELDTYPE_TEXT);
            $html_disabled = (!$cc_env['AllowTags'] && !$format['html']) || $format['html'] == 2;
            // разрешить html
            if ($html_disabled) {
                $cc_env['convert2txt'] .= "\$f_" . $field['name'] . " = htmlspecialchars(\$f_" . $field['name'] . ", ENT_QUOTES);";
            }
            if ($format['bbcode']) {
                if ($html_disabled) {
                    $cc_env['convert2txt'] .= "\$f_" . $field['name'] . " = nc_bbcode(\$f_" . $field['name'] . ", (\$fullDateLink ?: \$fullLink));";
                } else {
                    $cc_env['convert2txt'] .= "\$f_" . $field['name'] . " = nc_bbcode(htmlspecialchars(\$f_" . $field['name'] . ", ENT_QUOTES), (\$fullDateLink ?: \$fullLink));";
                }
            }
            // перенос строки
            if (($cc_env['NL2BR'] && !$format['br']) || $format['br'] == 1) {
                $cc_env['convert2txt'] .= "\$f_" . $field['name'] . " = nl2br(\$f_" . $field['name'] . ");";
            }
        }

        $text_fields = $component->get_fields(NC_FIELDTYPE_STRING);
        foreach ($text_fields as $field) {
            if (!$cc_env['AllowTags']) {
                $cc_env['convert2txt'] .= "\$f_" . $field['name'] . " = htmlspecialchars(\$f_" . $field['name'] . ", ENT_QUOTES);";
            }
            if ($cc_env['NL2BR']) {
                $cc_env['convert2txt'] .= "\$f_" . $field['name'] . " = nl2br(\$f_" . $field['name'] . ");";
            }
        }

        unset($format, $text_fields);

        // *** «Поиск» по компоненту ***


        $full_search_params = $component->get_search_query($srchPat);
        $full_search_params_add = $component->get_search_query($srchPatAdd, $cc);
        $full_search_query = $full_search_url = '';
        if (!empty($full_search_params['query'])) {
            $full_search_query = $full_search_params['query'];
            $full_search_url = $full_search_params['link'];
        }
        if (!empty($full_search_params_add['query'])) {
            $full_search_query .= " ".$full_search_params_add['query'];
            $full_search_url .= empty($full_search_params_add['link']) ? $full_search_params_add['link'] : "&".$full_search_params_add['link'];
        }

        // *** Подготовка запроса к БД ***

        $cond_catalogue = !$ignore_catalogue
                             ? $cond_catalogue = " AND sub.`Catalogue_ID` = '" . $catalogue . "' "
                             : "";
            // [MERGE] not used, not in API
            // $cond_catalogue_add = " AND a.`Subdivision_ID` = sub.`Subdivision_ID` ";
            // $cond_catalogue_addtable = ", `Subdivision` AS sub ";

        $cond_sub = !$ignore_sub ? " AND a.`Subdivision_ID` = '" . $sub . "' " : "";
        $cond_cc = !$ignore_cc ? " AND a.`Sub_Class_ID` = '" . $cc . "' " : "";
        $cond_user = !$ignore_user ? " AND a.`User_ID` = '" . $AUTH_USER_ID . "' " : "";
        $cond_parent = !$ignore_parent ? " AND a.`Parent_Message_ID` = '" . $parent_message . "' " : "";
        $cond_search = $full_search_query;
        $cond_mod = (!$admin_mode && !$ignore_check) ? $cond_mod = " AND a.`Checked` = 1 " : "";

        $cond_date = (isset($date) && $date && $date_field && strtotime($date) > 0)
                        ? $cond_date = " AND a.`" . $date_field . "` LIKE '" . $db->escape($date) . "%' "
                        : "";

        $cond_distinct = isset($distinct) && $distinct ? "DISTINCT" : "";
        if (!$cond_distinct) {
            $cond_distinct = isset($distinctrow) && $distinctrow ? "DISTINCTROW" : "";
        }

        if (isset($query_select) && $query_select) {
            $cond_select = $component_file_mode
                                ? ", " . $query_select
                                : ", " . nc_add_column_aliases($query_select);
        }
        else {
            $cond_select = "";
        }

        $cond_where = $query_where ? " AND " . $query_where : "";
        $cond_group = $query_group ? " GROUP BY " . $query_group : "";
        $cond_having = $query_having ? " HAVING " . $query_having : "";

        if (isset($query_order) && $query_order) {
            $sort_by = $query_order;
        }

        if ($user_table_mode) {
            $cond_sub = "";
            $cond_cc = "";
            // [MERGE] not used        $cond_catalogue_add = "";
            $cond_catalogue = "";
            // [MERGE] not used        $cond_catalogue_addtable = "";
            $cond_parent = "";
        }

        if ($full_search_url) {
            $intQueryStr .= (($intQueryStr == '?') ? '' : '&amp;') . $full_search_url;
        }

        if (!$recNum) {
            $recNum = $maxRows;
        }
        else {
            $maxRows = $recNum;
            // для совместимости со старыми версиями до 2.4.5 и 3.0.0
            // [MERGE] не удалось выяснить, что это было и насколько распространено;
            // условие всегда выполняется, так как $ignore_eval — пустой массив:
            // if (!$component_file_mode && !isset($ignore_eval['maxRows']) || !$ignore_eval['maxRows']) {
            //     eval("\$maxRows = \"" . $maxRows . "\";");
            // }
        }

        $maxRows = (int)$maxRows;

        // Если установлено значение переменной $nc_page, то вычисляем $curPos:
        if (isset($nc_page)) {
            $nc_page = (int)$nc_page;
            if ($nc_page < 1) { $nc_page = 1; }
            $curPos = ($nc_page - 1) * $maxRows;
        }

        // для совместимости со старыми версиями до 2.4.5 и 3.0.0
        // [MERGE] не удалось выяснить, что это было; условие всегда выполняется
        // (кроме компонента старого модуля поиска), так как $ignore_eval — пустой массив:
        if (!$component_file_mode && !empty($ignore_eval['sort_by'])) {
            eval(nc_check_eval("\$sort_by = \"" . $sort_by . "\";"));
        }

        if (!$ignore_all) {
            $message_select =
                "SELECT" . (!$ignore_calc ? " SQL_CALC_FOUND_ROWS" : "") . " " .
                           $cond_distinct . " " . $field_names . $cond_select . "
                   FROM (" . ($user_table_mode ? "`User`" : "`Message" . $classID . "`") . " AS a " .
                        ($query_from ? ", " . $query_from : "") . ") " .
                        $component->get_joins() . " " .
                        $query_join . "
                  WHERE 1 " . $cond_parent . $cond_where . $cond_catalogue . $cond_sub . $cond_cc . $cond_user . $cond_mod . $cond_search . $cond_date .
                  $cond_group .
                  $cond_having .
                  ($sort_by ? " ORDER BY " . $sort_by : "") .
                  (!$ignore_limit
                      ? " LIMIT " .
                        (!isset($cc_env['cur_cc']) || $cur_cc === false || $cc_env['cur_cc'] == $cur_cc
                          ? $curPos
                          : "0"
                        ) .
                        "," . $maxRows
                      : (isset($query_limit) && $query_limit ? " LIMIT " . $query_limit : "")
                  );
        }
        elseif ($query_select && $query_from) {
            $message_select =
                "SELECT" . (!$ignore_calc ? " SQL_CALC_FOUND_ROWS" : "") . " " . $query_select .
                 " FROM " . $query_from . ($query_join ? " " . $query_join : "") .
                 ($query_where  ? " WHERE " . $query_where : "") .
                 ($query_group  ? " GROUP BY " . $query_group : "") .
                 ($query_having ? " HAVING  " . $query_having : "") .
                 ($query_order  ? " ORDER BY " . $query_order : "") .
                 ($query_limit  ? " LIMIT " . $query_limit : "");
        }

        $cc_env['dateField'] = $date_field;
        $cc_env['fieldCount'] = count($component->get_fields());
    }
    else if (isset($nc_page)) {
        // Если установлено значение переменной $nc_page, то вычисляем $curPos для prepared data:
        $nc_page = (int)$nc_page;
        if ($nc_page < 1) { $nc_page = 1; }
        $curPos = ($nc_page - 1) * $maxRows;
    }

    $cc_env['curPos'] = $curPos;
    $cc_env['recNum'] = $recNum;
    $cc_env['maxRows'] = $maxRows;
    $cc_env['LocalQuery'] = $intQueryStr;

    // *** Ссылки для действий с инфоблоком ***

    if ($routing_module_enabled) {
        $addLink = new nc_routing_path_infoblock($_db_cc, 'add');
        $rssLink = $cc_env['AllowRSS'] ? new nc_routing_path_infoblock($_db_cc, 'index', 'rss') : '';
        $xmlLink = $cc_env['AllowXML'] ? new nc_routing_path_infoblock($_db_cc, 'index', 'xml') : '';
        $xmlFullLink = '';
        $subscribeLink = new nc_routing_path_infoblock($cc, 'subscribe');
        $searchLink = new nc_routing_path_infoblock($cc, 'search');
    }
    else {
        $addLink = $SUB_FOLDER . $cc_env['Hidden_URL'] . 'add_' . $cc_env['EnglishName'] . '.html';
        $rssLink = $cc_env['AllowRSS'] ? $SUB_FOLDER . $cc_env['Hidden_URL'] . $cc_env['EnglishName'] . '.rss' : '';
        $xmlLink = $cc_env['AllowXML'] ? $SUB_FOLDER . $cc_env['Hidden_URL'] . $cc_env['EnglishName'] . '.xml' : '';
        $xmlFullLink = "";
        $subscribeLink = $SUB_FOLDER . $cc_env['Hidden_URL'] . 'subscribe_' . $cc_env['EnglishName'] . '.html';
        $searchLink = $SUB_FOLDER . $cc_env['Hidden_URL'] . 'search_' . $cc_env['EnglishName'] . '.html';
    }

    $cc_env['addLink'] = $addLink;
    $cc_env['subscribeLink'] = $subscribeLink;
    $cc_env['searchLink'] = $searchLink;


    // *** Проверка наличия результата в кэше ***

    // cache eval section
    if ($cache_module_enabled && $cc_env['Cache_Access_ID'] == 1 && is_object($nc_cache_list) && $cached_eval && $cached_result != -1) {
        // get cached objects blocks
        $component_cache_blocks = $nc_cache_list->getCachedBlocks($cached_data);

        // cached prefix
        eval(nc_check_eval("\$result = \"" . $component_cache_blocks['prefix'] . "\";"));

        if (is_array($component_cache_blocks) && !empty($component_cache_blocks)) {
            // concat cached objects
            foreach ($component_cache_blocks['objects'] as $k => $v) {
                // extract cached object variables
                if (!empty($cache_vars) && is_array($cache_vars[$k])) {
                    extract($cache_vars[$k]);
                }
                // append object data
                eval(nc_check_eval("\$result .= \"" . $v . "\";"));
            }
        }

        // cached suffix
        eval(nc_check_eval("\$result .= \"" . $component_cache_blocks['suffix'] . "\";"));

        return $result;
    }


    // *** Проверка наличия формы добавления и формы поиска в коде компонента ***

    if ($component_file_mode) {
        $component_body = nc_check_file($file_class->get_field_path('Class')) ? nc_get_file($file_class->get_field_path('Class')) : null;
        // @todo ↑↑↑ refactor: don’t load template files (use lazy variables instead?)
        if ($cc_env['Class_Template_ID'] && strpos($component_body, '$nc_parent_field_path') !== false) {
            $component_body .= nc_check_file($file_class->get_parent_field_path('Class')) ? nc_get_file($file_class->get_parent_field_path('Class')) : null;
        }
    }
    else {
        // «компоненты v4»
        $cc_env['AddTemplate'] = $cc_env['AddTemplate']
                                    ? $cc_env['AddTemplate']
                                    : $component->add_form($catalogue, $sub, $cc);

        $cc_env['FullSearchTemplate'] = $cc_env['FullSearchTemplate']
                                    ? $cc_env['FullSearchTemplate']
                                    : $component->search_form(1);

        $component_body =
            ($ignore_prefix ? '' : $cc_env['FormPrefix']) .
            ($ignore_suffix ? '' : $cc_env['FormSuffix']) .
            $cc_env['RecordTemplate'] .
            $cc_env['RecordTemplateFull'] .
            $cc_env['Settings'];
    }


    // *** Форма добавления — $addForm ***

    if (strpos($component_body, '$addForm') !== false) {
        $multifield = (array)$component->get_fields(NC_FIELDTYPE_MULTIFILE);
        $multifield_names = array();

        foreach ($multifield as $multifield_row) {
            ${'f_' . $multifield_row['name']} = new nc_multifield($multifield_row['name'], $multifield_row['description'], null, $multifield_row['id']);
            $multifield_names[] = 'f_' . $multifield_row['name'];
        }

        $addForm = '';

        if ($component_file_mode) {
            $nc_parent_field_path = $file_class->get_parent_field_path('AddTemplate');
            $nc_field_path = $file_class->get_field_path('AddTemplate');

            // check and include component part
            try {
                if (nc_check_php_file($nc_field_path)) {
                    ob_start();
                    include $nc_field_path;
                    $addForm = ob_get_clean();
                }
            }
            catch (Exception $e) {
                if ($perm instanceof Permission && $perm->isSubClassAdmin($cc)) {
                    // error message
                    $addForm = sprintf(CONTROL_CLASS_CLASSFORM_CHECK_ERROR, CONTROL_CLASS_CLASS_FORMS_ADDFORM);
                }
            }

            if (!$addForm) {
                $addTemplate = $component->add_form($catalogue, $sub, $cc);
                eval(nc_check_eval("\$addForm = \"" . $addTemplate . "\";"));
            }

            $nc_parent_field_path = null;
            $nc_field_path = null;
        }
        else {
            // «компоненты v4»
            eval(nc_check_eval("\$addForm = \"" . $cc_env["AddTemplate"] . "\";"));
        }

        if ($addForm && $nc_add_block_markup) {
            $addForm = "<div class='tpl-block-add-form $nc_component_css_class'>" . $addForm . "</div>";
        }

        foreach ($multifield_names as $multifield_name) {
            unset(${$multifield_name});
        }

        unset($multifield_names);
    }

    // Фильтр объектов в режиме администратора
    $filter_form_html = '';

    // *** Форма поиска (выборки) — $searchForm ***
    if (strpos($component_body, '$searchForm') !== false) {
        $searchForm = '';

        if ($component_file_mode) {
            $nc_parent_field_path = $file_class->get_parent_field_path('FullSearchTemplate');
            $nc_field_path = $file_class->get_field_path('FullSearchTemplate');
            $searchForm = '';
            // check and include component part
            if (filesize($nc_field_path)) {
                try {
                    if (nc_check_php_file($nc_field_path)) {
                        ob_start();
                        include $nc_field_path;
                        $searchForm = ob_get_clean();
                    }
                }
                catch (Exception $e) {
                    if ($perm instanceof Permission && $perm->isSubClassAdmin($cc)) {
                        // error message
                        $searchForm = sprintf(CONTROL_CLASS_CLASSFORM_CHECK_ERROR, CONTROL_CLASS_CLASS_FORMS_QSEARCH);
                    }
                }
            }
            else {
                eval(nc_check_eval("\$searchForm.= \"" . $component->search_form(1) . "\";"));
            }

            $nc_parent_field_path = null;
            $nc_field_path = null;
        }
        else {
            // «компоненты v4»
            eval(nc_check_eval("\$searchForm = \"" . $cc_env["FullSearchTemplate"] . "\";"));
        }

        if ($searchForm && $nc_add_block_markup) {
            $searchForm = "<div class='tpl-block-search-form $nc_component_css_class'>" . $searchForm . "</div>";
        }
    }
    elseif ($inside_admin && $isMainContent) {
        // Системная форма поиска (фильтр)

        $filter_additional_fields = $component->get_additional_search_fields($cc);

        $filter_view_data = array(
            'cc'      => $cc,
            'form'    => eval(nc_check_eval('return "' . $component->search_form(false, $filter_additional_fields) . '";')),
            'fields'  => array_merge($filter_additional_fields, $component->get_fields()),
          // оставляем всегда закрытым
            'is_open' => false,
        );
        $filter_view      = $nc_core->ADMIN_FOLDER . 'views/component/objects_filter_form.view.php';
        $filter_form_html = $nc_core->ui->view($filter_view, $filter_view_data);
    }
    unset($component_body);


    // *** Выполнение запроса к БД ***

    $db->last_error = "";

    if ($message_select) {
        $res = $db->get_results($message_select, ARRAY_A);
    }
    else {
        $res = false;
    }


    // *** Обработка ошибок, возникших при выполнении запроса ***

    if ($db->last_error) {
        // determine error cause
        switch (true) {
            case preg_match("/Table '\w+\.Classificator_(\w+)' doesn't exist/i", $db->last_error, $regs):
                $err = sprintf(NETCAT_FUNCTION_OBJECTS_LIST_CLASSIFICATOR_ERROR, $regs[1]);
                break;
            case preg_match("/Unknown column '(.+?)' in 'field list'/i", $db->last_error, $regs):
                $err = sprintf(NETCAT_FUNCTION_OBJECTS_LIST_SQL_COLUMN_ERROR_UNKNOWN, $regs[1]);
                break;
            case preg_match("/Unknown column '(.+?)' in 'order clause'/i", $db->last_error, $regs):
                $err = sprintf(NETCAT_FUNCTION_OBJECTS_LIST_SQL_COLUMN_ERROR_CLAUSE, $regs[1]);
                break;
            case $SHOW_MYSQL_ERRORS == "on":
                $err = $db->last_error;
                break;
            default:
                $err = "";
        }

        // error message
        if ($perm instanceof Permission && $perm->isSupervisor()) {
            // error info for the supervisor
            nc_print_status($err ?: $db->last_error, 'error');
            trigger_error(sprintf(NETCAT_FUNCTION_OBJECTS_LIST_SQL_ERROR_SUPERVISOR, $sub, $cc, $query_string, ($err ? $err . ", " : "")), E_USER_WARNING);
        }
        else {
            // error info for the simple users
            echo NETCAT_FUNCTION_OBJECTS_LIST_SQL_ERROR_USER;
        }
        return false;
    }


    // *** Подсчёт количества объектов в результатах ($rowCount) и общего ($totRows) ***

    $totRows = 0;
    if ($message_select) {
        // object in this page
        $rowCount = $db->num_rows;
        // total objects
        $totRows = !$ignore_calc ? $db->get_var("SELECT FOUND_ROWS()") : $rowCount;
        $totRows += 0;
    }
    else if ($nc_prepared_data) {
        $rowCount = sizeof($nc_data);
        $totRows += 0;
        if (!$totRows) {
            $totRows = ($nc_data instanceof nc_record_collection) ? $nc_data->get_total_count() : $rowCount;
        }
    }
    else {
        $rowCount = 0;
        $totRows = 0;
    }


    // *** Подготовка к работе с компонентом-агрегатором ***

    $nc_class_aggregator = null;
    $nc_class_aggregator_data = null;

    if (class_exists('nc_class_aggregator_setting', false)) {
        $nc_class_aggregator_settings = nc_class_aggregator_setting::get_instanse();

        if ($nc_class_aggregator_settings && $res) {
            require_once $nc_core->INCLUDE_FOLDER . "classes/nc_class_aggregator.class.php";

            $class_data = array();

            foreach ($res as $row) {
                $class_data[] = array('db_Class_ID' => $row['db_Class_ID'], 'db_Message_ID' => $row['db_Message_ID']);
            }

            $nc_class_aggregator = new nc_class_aggregator($nc_class_aggregator_settings, $class_data);
            $nc_class_aggregator_data = $nc_class_aggregator->get_full_data();
        }
    }


    // *** Переменные для вывода листалки страниц ***

    // Перенос GET-переменных в пути $nextLink, $prevLink
    $_get_arr = $nc_core->input->fetch_get();
    $get_param = array(
        'cur_cc' => null, // чтобы порядок параметров был как в nc_browse_messages
    );
    if (!empty($_get_arr)) {
        $ignore_arr = array(
            'sid' => true, 'ced' => true, 'inside_admin' => true, 'REQUEST_URI' => true,
            'cur_cc' => true, 'curPos' => true, 'nc_page' => true);

        foreach ($_get_arr as $k => $v) {
            if (!isset($ignore_arr[$k])) {
                $get_param[$k] = $v;
            }
        }
    }
    unset($_get_arr, $ignore_arr);

    $begRow = $curPos + 1;
    $endRow = $curPos + $maxRows;
    if ($endRow > $totRows) { $endRow = $totRows; }

    $prevLink = $nextLink = '';

    if ($curPos > $maxRows && isset($cc_env['cur_cc']) && $cc_env['cur_cc']) {
        $get_param['cur_cc'] = $cc_env['cur_cc'];
    }
    if ($classPreview == $cc_env["Class_ID"]) {
        $get_param["classPreview"] = $classPreview;
    }

    if ($curPos > $maxRows) { // мы сейчас на третьей странице или далее (ссылка на первую страницу — без curPos)
        $get_param['curPos'] = $curPos - $maxRows;
    }

    // готовим $prevLink
    if ($curPos) {
        if ($routing_module_enabled && !$admin_mode) {
            $nc_previous_page = intval($curPos / $maxRows);
            $prevLink = new nc_routing_path(
                                (isset($get_param['cur_cc']) ? 'folder' : 'infoblock'),
                                array(
                                    'site_id' => $_db_catalogue,
                                    'folder_id' => $_db_sub,
                                    'infoblock_id' => $_db_cc,
                                    'action' => 'index',
                                    'format' => 'html',
                                    'variables' => $get_param,
                                    'page' => ($nc_previous_page > 1 ? $nc_previous_page : null),
                                    'date' => (isset($date) ? $date : null),
                                ));
        }
        else {
            $prevLink = ($admin_mode ? $admin_url_prefix : $nc_core->url->get_parsed_url('path')) .
                        nc_array_to_url_query($get_param, '&amp;');
        }
    }

    // готовим $nextLink
    if ($maxRows && $endRow < $totRows) {
        $get_param['curPos'] = $endRow;

        if (isset($cc_env['cur_cc']) && $cc_env['cur_cc']) {
            $get_param['cur_cc'] = $cc_env['cur_cc'];
        }

        if ($routing_module_enabled && !$admin_mode) {
            $nextLink = new nc_routing_path(
                                (isset($get_param['cur_cc']) ? 'folder' : 'infoblock'),
                                array(
                                    'site_id' => $_db_catalogue,
                                    'folder_id' => $_db_sub,
                                    'infoblock_id' => $_db_cc,
                                    'action' => 'index',
                                    'format' => 'html',
                                    'variables' => $get_param,
                                    'page' => intval($curPos / $maxRows + 2),
                                    'date' => (isset($date) ? $date : null),
                                ));
        }
        else {
            $nextLink = ($admin_mode ? $admin_url_prefix : $nc_core->url->get_parsed_url('path')) .
                        nc_array_to_url_query($get_param, '&amp;');
        }
    }

    unset($get_param);


    $cc_env['begRow'] = $begRow;
    $cc_env['endRow'] = $endRow;
    $cc_env['totRows'] = $totRows;
    $cc_env['prevLink'] = $prevLink;
    $cc_env['nextLink'] = $nextLink;


    // *** Подготовка к извлечению полученных данных ***

    if ($component_file_mode) {
        if ($nc_prepared_data && isset($nc_data[0])) {
            $f_Checked = 1;
            $fetch_row = $nc_data;
        }
        else {
            $fetch_row = $res;
        }
    }
    else {
        // «компоненты v4»
        if ($nc_prepared_data && isset($nc_data[0])) {
            $fetch_row = '$f_Checked = 1; ';
            // нужно подготовить $fetch_row вида:
            // $f_a = $nc_data[$f_RowNum]['a']; $f_b = $nc_data[$f_RowNum]['b']; ...
            // элементы $nc_data могут быть как массивом, так и объектом, реализующим Iterator, поэтому array_keys не подходит
            foreach ($nc_data[0] as $key => $value) {
                $fetch_row .= '$f_' . $key . ' = $nc_data[$f_RowNum]["' . $key . '"]; ';
            }
        }
        else if (!$ignore_all) {
            $fetch_row = "list(" . $field_vars . ($result_vars ? ", " . $result_vars : "") . ") = array_values(\$res[\$f_RowNum]);";
        }
        else {
            $fetch_row = $result_vars ? "list(" . $result_vars . ") = array_values(\$res[\$f_RowNum]);" : "";
        }

    }


    // *** Подготовка элементов интерфейса для режима администрирования ***

    $f_AdminCommon = "";
    $f_AdminCommon_cc = "";
    $f_AdminCommon_cc_name = "";
    $f_AdminCommon_add = "";
    $f_AdminCommon_delete_all = "";
    $f_AdminButtons = "";

    // Право на модерирование и изменение объектов.
    $modPerm = false;
    $changePerm = false;

    $nc_show_add_record_button = false;
    $nc_show_delete_record_button = false;

    $nc_can_moderate_infoblock =
        $perm &&
        ($perm->isSubClass($cc, MASK_MODERATE) ||
         $perm->isSubClass($cc, MASK_ADMIN));

    // используется при проверке, показывать ли $f_AdminButtons
    $nc_can_edit_objects =
        ($inside_admin || $admin_mode) && $perm && (
            $nc_can_moderate_infoblock ||
            $perm->isSubClass($cc, MASK_DELETE) ||
            $perm->isSubClass($cc, MASK_ADD) ||
            $perm->isSubClass($cc, MASK_EDIT) ||
            $perm->isSubClass($cc, MASK_CHECKED)
        );

    if ($admin_mode) {
        $modPerm = CheckUserRights($cc, 'moderate', 1); // право модератора
        $changePerm = s_auth($cc_env, 'change', 1); //               или просто на изменение объектов

        if ($perm && $perm->isBanned($cc_env, 'change')) {
            // пользователю запретили изменение объектов
            $modPerm = $changePerm = false;
        }

        $f_AdminCommon_add = $admin_url_prefix . "add.php?inside_admin=" . $inside_admin . "&amp;catalogue=" . $catalogue . "&amp;sub=" . $sub . "&amp;cc=" . $cc;
        $f_AdminCommon_delete_all = $admin_url_prefix . "message.php?inside_admin=" . $inside_admin . "&amp;catalogue=" . $catalogue . "&amp;sub=" . $sub . "&amp;cc=" . $cc . "&amp;classID=" . $classID . "&amp;delete=1";
        $f_AdminCommon_export_csv = $admin_url_prefix . "message.php?inside_admin=" . $inside_admin . "&amp;catalogue=" . $catalogue . "&amp;sub=" . $sub . "&amp;cc=" . $cc . "&amp;classID=" . $classID . "&amp;export=1";
        $f_AdminCommon_import_csv = $admin_url_prefix . "message.php?inside_admin=" . $inside_admin . "&amp;catalogue=" . $catalogue . "&amp;sub=" . $sub . "&amp;cc=" . $cc . "&amp;classID=" . $classID . "&amp;import=1";
        $f_AdminCommon_export_xml = $admin_url_prefix . "message.php?inside_admin=" . $inside_admin . "&amp;catalogue=" . $catalogue . "&amp;sub=" . $sub . "&amp;cc=" . $cc . "&amp;classID=" . $classID . "&amp;export=2";
        $f_AdminCommon_import_xml = $admin_url_prefix . "message.php?inside_admin=" . $inside_admin . "&amp;catalogue=" . $catalogue . "&amp;sub=" . $sub . "&amp;cc=" . $cc . "&amp;classID=" . $classID . "&amp;import=2";

        $addLink = $f_AdminCommon_add;

        // Js и форма для пакетной обработки объектов
        $f_AdminCommon_package = "<script type='text/javascript' language='javascript'>\n";
        $f_AdminCommon_package .= "\tif (typeof(nc_package_obj) != 'undefined') {nc_package_obj.new_cc(" . $cc . ", '" . NETCAT_MODERATION_NOTSELECTEDOBJ . "'); }\n";
        $f_AdminCommon_package .= "</script>\n";
        $f_AdminCommon_package .= "<form id='nc_form_selected_" . $cc . "' action='" . $SUB_FOLDER . $HTTP_ROOT_PATH . "message.php' method='post'>\n";
        $f_AdminCommon_package .= "\t<input type='hidden' name='catalogue' value='" . $catalogue . "'>\n";
        $f_AdminCommon_package .= "\t<input type='hidden' name='sub' value='" . $sub . "'>\n";
        $f_AdminCommon_package .= "\t<input type='hidden' name='cc' value='" . $cc . "'>\n";
        $f_AdminCommon_package .= "\t<input type='hidden' name='curPos' value='" . $curPos . "'>\n";
        $f_AdminCommon_package .= "\t<input type='hidden' name='admin_mode' value='" . $admin_mode . "'>\n";
        $f_AdminCommon_package .= "\t<input type='hidden' name='inside_admin' value='" . $inside_admin . "'>\n";
        $f_AdminCommon_package .= "</form>\n";

        if ($list_mode != "select") {

            $nc_show_add_record_button = (
                    $perm->isSubClass($cc, MASK_ADD) ||
                    $nc_can_moderate_infoblock
                ) && (
                    !strlen($current_cc['MaxRecordsInInfoblock']) ||
                    $current_cc['MaxRecordsInInfoblock'] > $totRows
                );

            $nc_show_delete_record_button = (
                    $perm->isSubClass($cc, MASK_DELETE) ||
                    $nc_can_moderate_infoblock
                ) && (
                    !$current_cc['MinRecordsInInfoblock'] ||
                    $current_cc['MinRecordsInInfoblock'] < $totRows
                );

            if ($inside_admin && $isMainContent && $UI_CONFIG) {
                // в админке нет AdminCommon, но нужна часть для пакетной обработки
                if ($totRows != 0) {
                    $result .= $f_AdminCommon_package;
                }
                // add button
                $UI_CONFIG->actionButtons = array();

                if ($nc_show_add_record_button) {
                    $UI_CONFIG->actionButtons[] = array(
                        "id" => "addObject",
                        "align" => "left",
                        "caption" => NETCAT_MODERATION_BUTTON_ADD,
                        "action" => "nc.load_dialog('{$SUB_FOLDER}{$nc_core->HTTP_ROOT_PATH}add.php?inside_admin=1&cc=$cc')",
                    );
                }

                // кнопки пакетной обработки нужны только если есть объекты
                if ($totRows != 0) {
                    //  button "delete all"
                    if ($nc_show_delete_record_button) {
                        $UI_CONFIG->actionButtons[] = array(
                            "id" => "deleteAll",
                            "caption" => NETCAT_MODERATION_REMALL,
                            "align" => "right",
                            "action" => "urlDispatcher.load('subclass.purge(" . $cc . ")')",
                            "red_border" => true,
                        );
                    }
                    if ($nc_core->get_settings('PacketOperations')) {
                        // button "Удалить выбранные"
                        $UI_CONFIG->actionButtons[] = array(
                            "id" => "deleteChecked",
                            "caption" => NETCAT_MODERATION_DELETESELECTED,
                            "align" => "right",
                            "action" => "document.getElementById('mainViewIframe').contentWindow.nc_package_obj.process('delete', " . $cc . ")",
                            "red_border" => true,
                        );
                        // button "Выключить выбранные"
                        $UI_CONFIG->actionButtons[] = array(
                            "id" => "checkOff",
                            "caption" => NETCAT_MODERATION_SELECTEDOFF,
                            "align" => "left",
                            "action" => "document.getElementById('mainViewIframe').contentWindow.nc_package_obj.process('checkOff', " . $cc . ")"
                        );
                        // button "Включить выбранные"
                        $UI_CONFIG->actionButtons[] = array(
                            "id" => "checkOn",
                            "caption" => NETCAT_MODERATION_SELECTEDON,
                            "align" => "left",
                            "action" => "document.getElementById('mainViewIframe').contentWindow.nc_package_obj.process('checkOn', " . $cc . ")"
                        );
                    }
                }
            }

            if (!$inside_admin) {
                if ($isSubClassArray) {
                    $f_AdminCommon .= nc_AdminCommonMultiBlock($cc, $sub, $nc_show_add_record_button);
                }
                else {
                    $f_AdminCommon = nc_AdminCommon($sub, $cc, $cc_env, $f_AdminCommon_package, $f_AdminCommon_add, $f_AdminCommon_delete_all, $nc_show_add_record_button);
                }
            }

        }
    }


    // *** Массив $row_ids: ID всех объектов в полученной выборке ***

    $row_ids = array();
    if (!$nc_prepared_data && !$ignore_all) {
        $res_key = $user_table_mode ? 'User_ID' : 'Message_ID';
        for ($f_RowNum = 0; $f_RowNum < $rowCount; $f_RowNum++) {
            if (isset($res[$f_RowNum]['Created'])) {
                $nc_core->page->update_last_modified_if_newer($res[$f_RowNum]['Created'], 'content');
            }
            if (isset($res[$f_RowNum]['Updated'])) {
                $nc_core->page->update_last_modified_if_newer($res[$f_RowNum]['Updated'], 'content');
            }
            $row_ids[] = $res[$f_RowNum][$res_key];
        }
        unset($res_key, $f_RowNum);
    }

    // Фильтр объектов в режиме администратора
    if ($filter_form_html) {
        $result .= $filter_form_html;
    }


    // *** Загрузка и кэширование информации о файлах ***

    // ID (символьный для таблицы пользователей, числовой для прочих компонентов) для nc_file_info
    $hybrid_component_id = ($user_table_mode ? 'User' : $classID);
    $multifile_field_values = array();

    if (!empty($row_ids)) {
        // Загрузить все значения полей типа NC_FIELD_MULTIFILE
        $multifile_field_values = nc_get_multifile_field_values($hybrid_component_id, $row_ids);

        // Передать в file_info значения полей типа файл для дальнейшего использования:
        $nc_core->file_info->cache_object_list_data($hybrid_component_id, $res);
        // Загрузить данные о файлах объектов в списке из Filetable:
        $nc_core->file_info->preload_filetable_values($hybrid_component_id, $row_ids);
    }


    // *** Загрузка информации о группах пользователей (user_table_mode) ***

    // требуется получить все группы пользователей
    if ($user_table_mode && !empty($row_ids)) {
        $nc_user_group = $db->get_results("SELECT ug.`User_ID`, ug.`PermissionGroup_ID`, g.`PermissionGroup_Name`
                                       FROM `User_Group` AS ug,`PermissionGroup` AS g
                                       WHERE User_ID IN (" . join(', ', $row_ids) . ")
                                       AND g.`PermissionGroup_ID` = ug.`PermissionGroup_ID` ", ARRAY_A);
        if (!empty($nc_user_group)) {
            foreach ($nc_user_group as $v) {
                $nc_user_group_sort[$v['User_ID']][$v['PermissionGroup_ID']] = $v['PermissionGroup_Name'];
            }
        }
        unset($nc_user_group);
    }

    if ($nc_add_block_markup) {
        $result .= '<div class="tpl-block-list ' . $nc_component_css_class . '" id="'. $nc_block_id .'">';
        if ($isMainContent && $isSubClassArray) {
            $result .= '<div id="' . nc_transliterate($current_cc['EnglishName'], true) . '"></div>';
        }
    }

    if ($admin_mode && $isSubClassArray) {
        $result .= nc_admin_infoblock_insert_toolbar($sub, $cc, 'before');
    }

    // *** Системные уведомления ***

    if ($wrong_nc_ctpl !== false && $perm && $perm->isSupervisor()) {
        $result .= "<div style='color: red;'>" .
                    sprintf(CONTROL_CLASS_CLASS_OBJECTSLIST_WRONG_NC_CTPL, $_db_sub, $_db_cc, $nc_ctpl) .
                    $wrong_nc_ctpl .
                    "</div>";
    }

    // *** Префикс списка объектов ***

    if (!$ignore_prefix) {
        if ($component_file_mode) {
            $nc_parent_field_path = $file_class->get_parent_field_path('FormPrefix');
            $nc_field_path = $file_class->get_field_path('FormPrefix');
            // check and include component part
            try {
                if (nc_check_php_file($nc_field_path)) {
                    ob_start();
                    include $nc_field_path;
                    $result .= ob_get_clean();
                }
            }
            catch (Exception $e) {
                if ($perm && $perm->isSubClassAdmin($cc)) {
                    // show moderation bar
                    $result .= $f_AdminCommon;
                    // error message
                    $result .= sprintf(CONTROL_CLASS_CLASSFORM_CHECK_ERROR, CONTROL_CLASS_CLASS_OBJECTSLIST_PREFIX);
                }
            }
            $nc_parent_field_path = null;
            $nc_field_path = null;
        }
        else {
            // «компоненты v4»
            if ($cc_env['FormPrefix']) {
                eval(nc_check_eval("\$result.= \"" . $cc_env["FormPrefix"] . "\";"));
            }
        }
    }
    else {
        $result .= $f_AdminCommon;
    }

    // если список пуст, внутри админки нужно показать сообщение "нет объектов"
    if ($inside_admin && $totRows == 0 && !strlen(trim($result))) {
        $result .= nc_print_status(NETCAT_MODERATION_NO_OBJECTS_IN_SUBCLASS, 'info', null, 1);
    }


    // *************************** Листинг объектов ****************************

    $cache_vars = array();
    $iteration_RecordTemplate = array();

    // переменные, которые будут созданы при extract’е:
    $f_RowID = $f_User_ID = $f_UserID = $f_LastUserID = $f_LastUser_ID = 0;
    $f_Subdivision_ID = $f_Sub_Class_ID = $f_Message_ID =  0;
    $f_Hidden_URL = $f_Keyword = $f_EnglishName = '';
    $f_Created = $f_LastUpdated = null;
    $f_Priority = $f_Checked = $f_PermissionGroup_ID = 0;
    // переменные, значение которых будет присвоено в случае компонента-агрегатора
    $f_db_Subdivision_ID = $f_db_Class_ID = $f_db_Sub_Class_ID = $f_db_Message_ID = 0;
    $f_db_Keyword = '';
    // переменные, значение которых будет присвоено в цикле
    $f_Created_year = $f_Created_month = $f_Created_day =
        $f_Created_hours = $f_Created_minutes = $f_Created_seconds =
        $f_Created_date = $f_Created_time = null;
    $f_LastUpdated_year = $f_LastUpdated_month = $f_LastUpdated_day =
        $f_LastUpdated_hours = $f_LastUpdated_minutes = $f_LastUpdated_seconds =
        $f_LastUpdated_date = $f_LastUpdated_time = null;
    $f_AdminInterface_user_add = $f_AdminInterface_user_change = '';
    $fullRSSLink = $fullXMLLink = $subscribeMessageLink = $msgLink = '';
    $nc_token_for_drop = ($routing_module_enabled && $nc_core->token->is_use('drop')
                            ? array('nc_token' => $nc_core->token->get())
                            : null);
    $nc_sub_folder_length = ($routing_module_enabled ? strlen($SUB_FOLDER) : null);

    // Список названий переменных для частичного кэширования
    $cache_vars_name = null;
    if ($rowCount && $cache_module_enabled && $no_cache_marks) {
        if ($component_file_mode) {
            $cache_vars_name = array_keys($fetch_row[0]);
            foreach ($cache_vars_name as &$_variable_name) {
                $_variable_name = "f_$_variable_name";
            }
        }
        else {
            if (preg_match('/^list\((.*?)\)/', $fetch_row, $matches)) {
                $cache_vars_name_string = preg_replace('/[$\s]+/', '', $matches[1]);
                $cache_vars_name = explode(",", $cache_vars_name_string);
            }
        }
        unset($_variable_name);
    }

    // Прежние названия переменных в fetch_row (v4): f_UserID, f_LastUserID, f_UserGroup, Hidden_URL
    $nc_compatibility_variable_map = array(
        'f_User_ID' => 'f_UserID',
        'f_LastUser_ID' => 'f_LastUserID',
        'f_PermissionGroup_ID' => 'f_UserGroup',
        'f_Hidden_URL' => 'Hidden_URL',
    );

    if (!$component_file_mode && $result_vars) {
        // Не затирать значения переменных, которые указаны в $result_vars:
        foreach ($nc_compatibility_variable_map as $nc_v5_variable_name => $nc_v4_variable_name) {
            if (preg_match('/\$' . $nc_v4_variable_name . '\b/', $result_vars)) {
                unset($nc_compatibility_variable_map[$nc_v5_variable_name]);
            }
        }
        // Проверить, есть ли $f_RowID в $result_vars:
        $nc_result_vars_has_row_id = (bool)preg_match('/\$f_RowID\b/', $result_vars);
    }
    else {
        $nc_result_vars_has_row_id = false;
    }

    // *** Кнопка модуля landing ***

    $nc_show_landing_button =
        $admin_mode &&
        !$nc_prepared_data &&
        nc_module_check_by_keyword('landing') &&
        nc_landing::get_instance()->has_presets_for_component($classID);

    $nc_show_drag_button = $admin_mode && nc_show_drag_handler($cc, $query_order);

    // *** Проверка кода, который может попасть в eval() в цикле ***

    $system_env['AdminButtons'] = $nc_core->security->php_filter->filter($system_env['AdminButtons'], 'string');
    if ($component_file_mode) {
        $nc_evaluated_record_template = null;
    } else {
        $nc_evaluated_record_template = nc_preg_replace('/\$result\b/', '$row', $cc_env['RecordTemplate']);
        if ($nc_evaluated_record_template) {
            $nc_evaluated_record_template = $nc_core->security->php_filter->filter($nc_evaluated_record_template, 'string');
        }
    }


    // *** Перебор всех полученных записей ***

    for ($f_RowNum = 0; $f_RowNum < $rowCount; $f_RowNum++) {

        // *** Извлечение данных из $res или $nc_data ***

        if ($component_file_mode) {
            if (is_object($fetch_row[$f_RowNum]) && method_exists($fetch_row[$f_RowNum], 'to_array')) {
                // duck typing, прежде всего это nc_record
                extract($fetch_row[$f_RowNum]->to_array(), EXTR_PREFIX_ALL, 'f');
            }
            else {
                extract($fetch_row[$f_RowNum], EXTR_PREFIX_ALL, 'f');
                // добываем старые переменные
                extract($component->get_old_vars($fetch_row[$f_RowNum]), EXTR_PREFIX_ALL, 'f');
            }
            if ($nc_class_aggregator instanceof nc_class_aggregator) {
                $fetch_row[$f_RowNum] = array_merge($fetch_row[$f_RowNum], $nc_class_aggregator_data[$f_RowNum]);
                extract($nc_class_aggregator_data[$f_RowNum], EXTR_PREFIX_ALL, 'f');
            }
        }
        else {
            // «компоненты v4»
            eval($fetch_row);
        }

        // *** Дополнительные переменные, доступные в шаблонах (обратная совместимость) ***

        // Прежние названия переменных в fetch_row (v4): f_RowID, f_UserID, f_LastUserID, f_UserGroup, Hidden_URL
        foreach ($nc_compatibility_variable_map as $nc_v5_variable_name => $nc_v4_variable_name) {
            $$nc_v4_variable_name = $$nc_v5_variable_name;
            if ($component_file_mode && is_array($fetch_row)) {
                $fetch_row[$f_RowNum][$nc_v4_variable_name] = $$nc_v5_variable_name;
            }
        }
        if (!$nc_result_vars_has_row_id) {
            $f_RowID = ($user_table_mode ? $f_User_ID : $f_Message_ID);
        }

        // fix fullLink для системных таблиц, у которых в old_vars не попадает EnglishName
        if ($user_table_mode) {
            $f_EnglishName = $cc_env['EnglishName'];
            $f_Hidden_URL = $cc_env['Hidden_URL'];
        }

        // *** Кэширование ***

        if ($cache_module_enabled && $no_cache_marks && $cache_vars_name) {
            // caching variables array
            $cache_vars[$f_RowNum] = array();

            foreach ($cache_vars_name as $_variable_name) {
                $cache_vars[$f_RowNum][$_variable_name] = $$_variable_name;
            }
            unset($_variable_name);
        }

        // *** Ссылки ***

        // переопределение $subLink и $cc_keyword, чтобы ссылки $fullLink вел в инфоблок,
        // в котором был добавлен объект (иначе будет вести в инфоблок, в котором объект выводится)
        $use_row_path = (!$ignore_link && !$is_mirror);
        if ($use_row_path) {
            if ($routing_module_enabled) {
                if (!$subLink || !($subLink instanceof nc_routing_path_folder) || $subLink->get_folder_id() != $f_Subdivision_ID) {
                    $subLink = new nc_routing_path_folder($f_Subdivision_ID);
                }
            }
            else {
                // $f_Hidden_URL уже содержит SUB_FOLDER
                $subLink = $f_Hidden_URL;
            }

            $cc_keyword = $f_EnglishName;
        }

        $routing_object_parameters = !$routing_module_enabled ? null :
            array(
                'site_id' => $_db_catalogue,
                'folder' => ($use_row_path
                                ? substr($f_Hidden_URL, $nc_sub_folder_length) // $f_Hidden_URL включает SUB_FOLDER
                                : $cc_env['Hidden_URL']),
                'folder_id' => ($use_row_path ? $f_Subdivision_ID : $_db_sub),
                'infoblock_id' => ($use_row_path ? $f_Sub_Class_ID : $_db_cc),
                'infoblock_keyword' => $cc_keyword,
                'object_id' => $f_RowID,
                'object_keyword' => $f_Keyword,
                'action' => 'full',
                'format' => 'html',
                'date' => $date_field && ${"f_$date_field"}
                            ? ${"f_{$date_field}_year"} . "-" . ${"f_{$date_field}_month"} . "-" . ${"f_{$date_field}_day"}
                            : null,
            );

        if (!$user_table_mode && $admin_mode && $AUTHORIZE_BY === 'User_ID') {
            $f_AdminInterface_user_add = $f_UserID;
            $f_AdminInterface_user_change = $f_LastUserID;
        }

        // *** Особые типы полей ***

        // Multiselect
        $iteration_multilist_fields = array();
        if (!empty($multilist_fields)) {
            // просмотр каждого поля типа multiselect
            foreach ($multilist_fields as $multilist_field) {
                // таблицу с элементами можно взять из кэша, если ее там нет — то добавить
                if (!$_cache['classificator'][$multilist_field['table']]) {
                    $db_res = $db->get_results(
                        "SELECT `" . $multilist_field['table'] . "_ID` AS ID, `" . $multilist_field['table'] . "_Name` AS Name, `Value`
                           FROM `Classificator_" . $multilist_field['table'] . "`", ARRAY_A);

                    if (!empty($db_res)) {
                        foreach ($db_res as $v) { // запись в кэш
                            $_cache['classificator'][$multilist_field['table']][$v['ID']] = array($v['Name'], $v['Value']);
                        }
                    }
                    unset($db_res);
                }

                ${"f_" . $multilist_field['name'] . "_id"} = array();
                ${"f_" . $multilist_field['name'] . "_value"} = array();

                if (($value = ${"f_" . $multilist_field['name']})) { // значение из базы
                    ${"f_" . $multilist_field['name']} = array();
                    ${"f_" . $multilist_field['name'] . "_id"} = array();
                    $ids = explode(',', $value);
                    if (!empty($ids)) {
                        foreach ($ids as $id) { // для каждого элемента по id определяем имя и значение
                            if ($id) {
                                array_push(${"f_" . $multilist_field['name']}, $_cache['classificator'][$multilist_field['table']][$id][0]);
                                array_push(${"f_" . $multilist_field['name'] . "_value"}, $_cache['classificator'][$multilist_field['table']][$id][1]);
                                array_push(${"f_" . $multilist_field['name'] . "_id"}, $id);
                            }
                        }
                    }
                }
                // default values
                if (!is_array(${"f_" . $multilist_field['name']})) {
                    ${"f_" . $multilist_field['name']} = array();
                }

                if ($component_file_mode) {
                    $iteration_multilist_fields['f_' . $multilist_field['name']] = ${"f_" . $multilist_field['name']};
                    $iteration_multilist_fields['f_' . $multilist_field['name'] . '_value'] = ${"f_" . $multilist_field['name'] . "_value"};
                    $iteration_multilist_fields['f_' . $multilist_field['name'] . '_id'] = ${"f_" . $multilist_field['name'] . "_id"};
                }
            }

            if ($component_file_mode) {
                $iteration_RecordTemplate[$f_RowNum]['multilist_fields'] = $iteration_multilist_fields;
            }
            unset($ids, $id, $value, $multilist_field, $iteration_multilist_fields);
        }

        // get file fields variables
        if ($component_file_mode) {
            $iteration_RecordTemplate[$f_RowNum]['fields_files'] =
                $nc_core->file_info->get_all_object_file_variables($hybrid_component_id, $f_RowID);

            // get multifile fields variables
            if (sizeof($multifile_field_values)) {
                foreach ($multifile_field_values[$f_RowID] as $field_name => $field_value) {
                    /** @var nc_multifield $field_value */
                    $iteration_RecordTemplate[$f_RowNum]['multifile_fields']['f_' . $field_name] =
                        $field_value->set_template(${'f_' . $field_name . '_tpl'});
                }
            }
        }
        else {
            // «компоненты v4»
            extract($nc_core->file_info->get_all_object_file_variables($hybrid_component_id, $f_RowID));

            // get multifile fields variables
            if (sizeof($multifile_field_values)) {
                foreach ($multifile_field_values[$f_RowID] as $field_name => $field_value) {
                    /** @var nc_multifield $field_value */
                    ${'f_' . $field_name} = $field_value->set_template(${'f_' . $field_name . '_tpl'});;
                }
            }
        }

        if ($nc_class_aggregator instanceof nc_class_aggregator && $nc_class_aggregator->has_multifile_fields($fetch_row[$f_RowNum]['db_Class_ID'])) {
            foreach ($fetch_row[$f_RowNum] as $field_name => $field_value) {
                if ($field_value instanceof nc_multifield && isset(${'f_' . $field_name . '_tpl'})) {
                    $field_value->set_template(${'f_' . $field_name . '_tpl'});
                }
            }
        }

        if ($user_table_mode) {
            $f_PermissionGroup = & $nc_user_group_sort[$f_RowID];
        }
        else {
            $f_PermissionGroup = null;
        }

        // *** Части даты ***
        if (isset($f_Created)) {
            list($nc_tmp_date, $nc_tmp_time) = explode(" ", $f_Created, 2);
            list($f_Created_year, $f_Created_month, $f_Created_day) = explode("-", $nc_tmp_date);
            list($f_Created_hours, $f_Created_minutes, $f_Created_seconds) = explode(":", $nc_tmp_time);
            $f_Created_date = $f_Created_day . "." . $f_Created_month . "." . $f_Created_year;
            $f_Created_time = $f_Created_hours . ":" . $f_Created_minutes . ":" . $f_Created_seconds;
        }

        if (isset($f_LastUpdated) && $f_LastUpdated) {
            $f_LastUpdated_year = substr($f_LastUpdated, 0, 4);
            $f_LastUpdated_month = substr($f_LastUpdated, 4, 2);
            $f_LastUpdated_day = substr($f_LastUpdated, 6, 2);
            $f_LastUpdated_hours = substr($f_LastUpdated, 8, 2);
            $f_LastUpdated_minutes = substr($f_LastUpdated, 10, 2);
            $f_LastUpdated_seconds = substr($f_LastUpdated, 12, 2);
            $f_LastUpdated_date = $f_LastUpdated_day . "." . $f_LastUpdated_month . "." . $f_LastUpdated_year;
            $f_LastUpdated_time = $f_LastUpdated_hours . ":" . $f_LastUpdated_minutes . ":" . $f_LastUpdated_seconds;
        }

        if ($admin_mode && !$nc_prepared_data) {

            // *** Режим редактирования: элементы и ссылки для управления объектом в админке ***

            $dateLink = '';
            if ($date_field && ${"f_{$date_field}"}) {
                $dateLink = "&date=" . ${"f_{$date_field}_year"} . "-" . ${"f_{$date_field}_month"} . "-" . ${"f_{$date_field}_day"};
            }

            // full link for object
            $fullLink = nc_get_fullLink($admin_url_prefix, $_db_catalogue, $_db_sub, $_db_cc, $f_RowID, $inside_admin);
            $fullDateLink = nc_get_fullDateLink($fullLink, $dateLink);

            $subLink = $admin_url_prefix . '?catalogue=' . $_db_catalogue . '&amp;sub=' . $_db_sub;

            // ID объекта в шаблоне
            $f_AdminButtons_id = $f_RowID;

            // Приоритет объекта
            $f_AdminButtons_priority = $f_Priority;

            // ID добавившего пользователя
            $f_AdminButtons_user_add = $f_UserID;

            // ID изменившего пользователя
            $f_AdminButtons_user_change = nc_get_AdminButtons_user_change($f_LastUserID);

            $f_AdminButtons_copy = "";
            $f_AdminButtons_change = "";
            if ($perm->isSubClass($cc, MASK_EDIT) || $nc_can_moderate_infoblock) {
                // копировать объект
                $f_AdminButtons_copy = nc_get_AdminButtons_copy($ADMIN_PATH, $catalogue, $sub, $cc, $classID, $f_RowID);

                // изменить
                $f_AdminButtons_change = nc_get_AdminButtons_change($SUB_FOLDER, $HTTP_ROOT_PATH, $catalogue, $sub, $cc, $f_RowID, $curPos, $inside_admin);
                $editLink = $f_AdminButtons_change;
            }

            if ($nc_core->get_settings('AutosaveUse') == 1) {
                $f_AdminButtons_version = nc_get_AdminButtons_version($SUB_FOLDER, $HTTP_ROOT_PATH, $catalogue, $sub, $cc, $f_RowID, $curPos, $inside_admin);
                $versionLink = $f_AdminButtons_version;
            }
            else {
                $f_AdminButtons_version = "";
            }

            $f_AdminButtons_delete = $deleteLink = $dropLink = "";
            if ($nc_show_delete_record_button) {
                // удалить
                $f_AdminButtons_delete = nc_get_AdminButtons_delete($SUB_FOLDER, $HTTP_ROOT_PATH, $catalogue, $sub, $cc, $f_RowID, $curPos, $inside_admin);
                $deleteLink = $f_AdminButtons_delete;
                $dropLink = nc_get_dropLink($deleteLink, $nc_core);
            }

            $f_AdminButtons_check = $f_AdminButtons_uncheck = $checkedLink = "";
            if ($perm->isSubClass($cc, MASK_CHECKED) || $nc_can_moderate_infoblock) {
                // включить-выключить
                $f_AdminButtons_check = nc_get_AdminButtons_check($f_Checked, $SUB_FOLDER, $HTTP_ROOT_PATH, $catalogue, $sub, $cc, $classID, $f_RowID, $curPos, $admin_mode, $admin_url_prefix, $nc_core);
                $f_AdminButtons_uncheck = nc_get_AdminButtons_check(!$f_Checked, $SUB_FOLDER, $HTTP_ROOT_PATH, $catalogue, $sub, $cc, $classID, $f_RowID, $curPos, $admin_mode, $admin_url_prefix, $nc_core);
                $checkedLink = $f_AdminButtons_check;
            }

            // выбрать связанный (JS код!!!) -- когда список вызван в popup для выбора связанного объекта
            $f_AdminButtons_select = nc_get_AdminButtons_select($f_AdminButtons_id);

            if ($list_mode == 'select') {
                $f_AdminButtons_buttons = nc_get_list_mode_select_AdminButtons_buttons($f_AdminButtons_select, $ADMIN_TEMPLATE);
                $f_AdminButtons = nc_get_list_mode_select($f_Checked, $classID, $f_RowID, $f_AdminButtons_id, $f_AdminButtons_buttons);
            }
            else {
                $nc_show_admin_buttons = $modPerm || ($changePerm && $f_AdminButtons_user_add == $AUTH_USER_ID) || $nc_can_edit_objects;
                if ($system_env['AdminButtonsType']) {
                    // проверка этого кода производится до цикла:
                    eval("\$f_AdminButtons = \"" . $system_env['AdminButtons'] . "\";");
                }
                else if ($isSubClassArray) {
                    if ($nc_show_admin_buttons) {
                        $f_AdminButtons = nc_get_AdminButtonsMultiBlock(
                            $f_RowID,
                            $f_Checked,
                            $f_AdminButtons_check,
                            $f_AdminButtons_uncheck,
                            $f_AdminButtons_copy,
                            $f_AdminButtons_change,
                            $f_AdminButtons_delete,
                            $f_AdminButtons_version,
                            $cc,
                            $sub,
                            $classID,
                            $nc_show_landing_button,
                            $nc_show_drag_button
                        );
                    }
                }
                else {
                    $f_AdminButtons_buttons = nc_get_AdminButtons_buttons($f_RowID, $f_Checked, $f_AdminButtons_check, $f_AdminButtons_uncheck, $f_AdminButtons_copy, $f_AdminButtons_change, $f_AdminButtons_delete, $f_AdminButtons_version, $cc, $sub, $classID, $nc_show_landing_button);
                    $f_AdminButtons = nc_get_AdminButtons_prefix($f_Checked, $cc);
                    // проверка прав
                    if ($nc_show_admin_buttons) {
                        $f_AdminButtons .= nc_get_AdminButtons_modPerm($classID, $f_RowID, $f_AdminButtons_id, $f_AdminButtons_priority, $f_AdminInterface_user_add, $f_AdminButtons_user_add, $f_AdminInterface_user_change, $f_AdminButtons_user_change, $f_AdminButtons_buttons, $cc, $query_order);
                    }
                    else {
                        $f_AdminButtons .= nc_get_AdminButtons_modPerm_else($classID, $f_RowID);
                    }
                    $f_AdminButtons .= nc_get_AdminButtons_suffix();
                }
            }
            if ($user_table_mode) {
                $f_AdminButtons = "";
            }
        }
        else {

            // *** Режим просмотра: ссылки на действия с объектом ***

            $f_AdminButtons_id = "";
            $f_AdminButtons_priority = "";
            $f_AdminButtons_user_add = "";
            $f_AdminButtons_user_change = "";
            $f_AdminButtons_copy = "";
            $f_AdminButtons_change = "";
            $f_AdminButtons_version = "";
            $f_AdminButtons_delete = "";
            $f_AdminButtons_check = "";
            $f_AdminButtons_select = "";
            $f_AdminButtons = "";

            if (!isset($f_Keyword)) {
                $f_Keyword = '';
            }

            // модуль маршрутизации: нет аналога для $msgLink
            $msgLink = $f_Keyword != '' ? $f_Keyword : $cc_keyword . "_" . $f_RowID;

            $dateLink = '';
            if ($date_field && ${"f_{$date_field}"}) {
                $dateLink = ${"f_{$date_field}_year"} . "/" . ${"f_{$date_field}_month"} . "/" . ${"f_{$date_field}_day"} . "/";
            }

            if ($routing_module_enabled) {
                $_add_domain = ($_db_catalogue != $current_catalogue['Catalogue_ID']);

                $fullLink = new nc_routing_path_object($classID, $routing_object_parameters, 'full', 'html', false, null, $_add_domain);
                $fullRSSLink = $cc_env['AllowRSS']
                    ? new nc_routing_path_object($classID, $routing_object_parameters, 'full', 'rss', false, null, $_add_domain)
                    : "";
                $fullXMLLink = $cc_env['AllowXML']
                    ? new nc_routing_path_object($classID, $routing_object_parameters, 'full', 'xml', false, null, $_add_domain)
                    : "";
                $fullDateLink = $dateLink
                    ? new nc_routing_path_object($classID, $routing_object_parameters, 'full', 'html', true, null, $_add_domain)
                    : $fullLink;
                $editLink = new nc_routing_path_object($classID, $routing_object_parameters, 'edit', 'html', false, null, $_add_domain);
                if ($nc_core->get_settings('AutosaveUse') == 1) {
                    $versionLink = new nc_routing_path_object($classID, $routing_object_parameters, 'version', 'html', false, null, $_add_domain);
                }
                $deleteLink = new nc_routing_path_object($classID, $routing_object_parameters, 'delete', 'html', false, null, $_add_domain);
                $dropLink = new nc_routing_path_object(
                                $classID,
                                $routing_object_parameters,
                                'drop',
                                'html',
                                false,
                                $nc_token_for_drop,
                                $_add_domain);
                $checkedLink = new nc_routing_path_object($classID, $routing_object_parameters, 'checked', 'html', false, null, $_add_domain);
                $subscribeMessageLink = new nc_routing_path_object($classID, $routing_object_parameters, 'subscribe', 'html', false, null, $_add_domain);
            }
            else {
                $_host = ($_db_catalogue == $current_catalogue['Catalogue_ID']) ? '' : $subHost;

                $fullLink = $_host . $subLink . $msgLink . ".html"; // полный вывод
                $fullRSSLink = $cc_env['AllowRSS'] ? $_host . $subLink . $msgLink . ".rss" : ""; // rss
                $fullXMLLink = $cc_env['AllowXML'] ? $_host . $subLink . $msgLink . ".xml" : "";
                $fullDateLink = $_host . $subLink . $dateLink . $msgLink . ".html"; // полный вывод с датой
                $editLink = $_host . $subLink . "edit_" . $msgLink . ".html"; // ссылка для редактирования
                if ($nc_core->get_settings('AutosaveUse') == 1) {
                    $versionLink = $_host . $subLink . "version_" . $msgLink . ".html"; // ссылка для черновика
                }
                $deleteLink = $_host . $subLink . "delete_" . $msgLink . ".html"; // удаления
                $dropLink = $_host . $subLink . "drop_" . $msgLink . ".html" . ($nc_core->token->is_use('drop') ? "?" . $nc_core->token->get_url() : ""); // удаления без подтверждения
                $checkedLink = $_host . $subLink . "checked_" . $msgLink . ".html"; // включения\выключения
                $subscribeMessageLink = $_host . $subLink . "subscribe_" . $msgLink . ".html"; // подписка на объект
            }

            // Если это превью данного компонента то, мы добавляем переменную к ссылкам на полный просмотр объекта
            if ($classPreview == $cc_env["Class_ID"]) {
                $fullLink .= "?classPreview=" . $classPreview;
                $fullDateLink .= "?classPreview=" . $classPreview;
            }
        }

        // *** Ссылки для агрегированных объектов ***

        if (is_object($nc_class_aggregator) && $f_db_Subdivision_ID) {
            if ($routing_module_enabled) {
                $fullLink = new nc_routing_path_object($f_db_Class_ID, array_merge(
                        $routing_object_parameters,
                        array(
                            'folder' => $nc_core->subdivision->get_by_id($f_db_Subdivision_ID, 'Hidden_URL'),
                            'folder_id' => $f_db_Subdivision_ID,
                            'infoblock_id' => $f_db_Sub_Class_ID,
                            'infoblock_keyword' => $nc_core->sub_class->get_by_id($f_db_Sub_Class_ID, 'EnglishName'),
                            'object_id' => $f_db_Message_ID,
                            'object_keyword' => $f_db_Keyword,
                        )
                    ));
            }
            else {
                $fullLink = $SUB_FOLDER .
                            $nc_core->subdivision->get_by_id($f_db_Subdivision_ID, 'Hidden_URL') .
                            ($f_db_Keyword
                                ? $f_db_Keyword . '.html'
                                : $nc_core->sub_class->get_by_id($f_db_Sub_Class_ID, 'EnglishName') . '_' . $f_db_Message_ID . '.html'
                            );
            }
        }

        if ($component_file_mode) {
            $vars = array();
            $vars['f_RowID'] = $f_RowID;
            $vars['f_UserID'] = $f_UserID;
            $vars['f_LastUserID'] = $f_LastUserID;
            $vars['f_AdminInterface_user_add'] = $f_AdminInterface_user_add;
            $vars['f_AdminInterface_user_change'] = $f_AdminInterface_user_change;
            $vars['subLink'] = $subLink;
            $vars['cc_keyword'] = $cc_keyword;
            $vars['fullLink'] = $fullLink;
            $vars['fullDateLink'] = $fullDateLink;
            $vars['fullRSSLink'] = $fullRSSLink;
            $vars['fullXMLLink'] = $fullXMLLink;
            $vars['editLink'] = $editLink;
            if ($nc_core->get_settings('AutosaveUse') == 1) {
                $vars['versionLink'] = $versionLink;
            }
            $vars['deleteLink'] = $deleteLink;
            $vars['dropLink'] = $dropLink;
            $vars['checkedLink'] = $checkedLink;
            $vars['subscribeMessageLink'] = $subscribeMessageLink;
            $vars['f_Keyword'] = $f_Keyword;
            $vars['msgLink'] = $msgLink;
            $vars['dateLink'] = $dateLink;
            $vars['date_field'] = $date_field;
            $vars['f_AdminButtons_id'] = $f_AdminButtons_id;
            $vars['f_AdminButtons_priority'] = $f_AdminButtons_priority;
            $vars['f_AdminButtons_user_add'] = $f_AdminButtons_user_add;
            $vars['f_AdminButtons_user_change'] = $f_AdminButtons_user_change;
            $vars['f_AdminButtons_copy'] = $f_AdminButtons_copy;
            $vars['f_AdminButtons_change'] = $f_AdminButtons_change;
            if ($nc_core->get_settings('AutosaveUse') == 1) {
                $vars['f_AdminButtons_version'] = $f_AdminButtons_version;
            }
            $vars['f_AdminButtons_delete'] = $f_AdminButtons_delete;
            $vars['f_AdminButtons_check'] = $f_AdminButtons_check;
            $vars['f_AdminButtons_select'] = $f_AdminButtons_select;
            $vars['f_AdminButtons'] = $f_AdminButtons;
            $vars['f_PermissionGroup'] = $f_PermissionGroup;
            $vars['f_Created_year'] = $f_Created_year;
            $vars['f_Created_month'] = $f_Created_month;
            $vars['f_Created_day'] = $f_Created_day;
            $vars['f_Created_hours'] = $f_Created_hours;
            $vars['f_Created_minutes'] = $f_Created_minutes;
            $vars['f_Created_seconds'] = $f_Created_seconds;
            $vars['f_Created_date'] = $f_Created_date;
            $vars['f_Created_time'] = $f_Created_time;

            if (isset($f_LastUpdated) && $f_LastUpdated) {
                $vars['f_LastUpdated'] = $f_LastUpdated;
                $vars['f_LastUpdated_year'] = $f_LastUpdated_year;
                $vars['f_LastUpdated_month'] = $f_LastUpdated_month;
                $vars['f_LastUpdated_day'] = $f_LastUpdated_day;
                $vars['f_LastUpdated_hours'] = $f_LastUpdated_hours;
                $vars['f_LastUpdated_minutes'] = $f_LastUpdated_minutes;
                $vars['f_LastUpdated_seconds'] = $f_LastUpdated_seconds;
                $vars['f_LastUpdated_date'] = $f_LastUpdated_date;
                $vars['f_LastUpdated_time'] = $f_LastUpdated_time;
            }

            $iteration_RecordTemplate[$f_RowNum]['vars'] = $vars;
            unset($vars);
        }
        else {
            // «компоненты v4»
            $row = "";
            eval($cc_env['convert2txt']);
            // проверка кода производится до цикла:
            eval("\$row = \"" . $nc_evaluated_record_template . "\";");

            // внутри админки: для того, чтобы объекты можно было перетаскивать...
            // ... сделаем "обертку" с ID, номером класса и ID родителя:
            if ($admin_mode) {
                $row = nc_finishing_RecordTemplate($row, $inside_admin, $classID, $f_RowID, $parent_message, $cc, $cc_env["Class_Name"], $no_cache_marks);
            }
            else if ($no_cache_marks) {
                $row = nc_add_no_cache_marks($row, $f_RowID);
            }

            $result .= $row;
        }

    } // "foreach row"

    if ($component_file_mode) {
        $nc_parent_field_path = $file_class->get_parent_field_path('RecordTemplate');
        $nc_field_path = $file_class->get_field_path('RecordTemplate');
        // check and include component part
        try {
            if (nc_check_php_file($nc_field_path)) {
                ob_start();
                include $nc_field_path;
                $result .= ob_get_clean();
            }
        }
        catch (Exception $e) {
            if ($perm && $perm->isSubClassAdmin($cc)) {
                // error message
                $result .= sprintf(CONTROL_CLASS_CLASSFORM_CHECK_ERROR, CONTROL_CLASS_CLASS_OBJECTSLIST_BODY);
            }
        }
        $nc_parent_field_path = null;
        $nc_field_path = null;
        unset($iteration_RecordTemplate);
    }

    // (Конец блока «листинг объектов»)


    // *** Суффикс списка объектов ***

    if (!$ignore_suffix) {
        if ($component_file_mode) {
            $nc_parent_field_path = $file_class->get_parent_field_path('FormSuffix');
            $nc_field_path = $file_class->get_field_path('FormSuffix');
            // check and include component part
            try {
                if (nc_check_php_file($nc_field_path)) {
                    ob_start();
                    include $nc_field_path;
                    $result .= ob_get_clean();
                }
            }
            catch (Exception $e) {
                if ($perm && $perm->isSubClassAdmin($cc)) {
                    // error message
                    $result .= sprintf(CONTROL_CLASS_CLASSFORM_CHECK_ERROR, CONTROL_CLASS_CLASS_OBJECTSLIST_SUFFIX);
                }
            }
            $nc_parent_field_path = null;
            $nc_field_path = null;
        }
        else {
            // «компоненты v4»
            if ($cc_env['FormSuffix']) {
                eval(nc_check_eval("\$result .= \"" . $cc_env["FormSuffix"] . "\";"));
            }
        }
    }

    if ($admin_mode && $isSubClassArray) {
        $result .= nc_admin_infoblock_insert_toolbar($sub, $cc, 'after');
    }

    if ($nc_add_block_markup) {
        $result .= '</div>';
    }

    // добавить скрипт для D&D
    if (($admin_mode || $inside_admin) && !$user_table_mode && $perm->isSubClassAdmin($cc)) {
        // приоритет позволять менять только если отсортировано по умолчанию (Priority DESC)
        $change_priority = nc_show_drag_handler($cc, $query_order) ? 'true' : 'false';
        $result .= "<script>";
        $result .= "if (typeof formAsyncSaveEnabled != 'undefined') { messageInitDrag(" .
                    nc_array_json(array($classID => $row_ids)) . ", " . $change_priority .
                    "); }";
        $result .= "</script>";
    }

    // title
    if ($isMainContent && (!$isSubClassArray || $cc_array[0] == $cc)) {
        $title = '';
        //если для раздела не задан Title, то используется Title от компонента
        if (!$current_sub['Title'] && $cc_env['TitleList']) {
            eval(nc_check_eval("\$title = \"" . $cc_env['TitleList'] . "\";"));
        }

        if ($title) {
            $nc_core->page->set_metatags('title', $title);
            $cc_env['Cache_Access_ID'] = 2;
        }
    }

    // cache section
    if (nc_module_check_by_keyword("cache") && $cc_env['Cache_Access_ID'] == 1 && is_object($nc_cache_list) && !$user_table_mode && !$nc_prepared_data) {
        try {
            $bytes = $nc_cache_list->add($_db_sub, $_db_cc, $cache_key, $result, $cache_vars);
            if ($no_cache_marks) {
                $result = $nc_cache_list->nocacheClear($result);
            }
            // debug info
            if ($bytes) {
                $cache_debug_info = "Written, sub[" . $_db_sub . "], cc[" . $_db_cc . "], Access_ID[" . $cc_env['Cache_Access_ID'] . "], Lifetime[" . $cc_env['Cache_Lifetime'] . "], bytes[" . $bytes . "]";
                $nc_cache_list->debugMessage($cache_debug_info, __FILE__, __LINE__, "ok");
            }
        } catch (Exception $e) {
            $nc_cache_list->errorMessage($e);
        }
    }


    if ($admin_mode && ($cc == $current_cc['Sub_Class_ID'])) {
        // Если на входе есть параметры isNaked=1, cc_only и include_component_style_tag,
        // добавим в разметку ссылку на стили компонентов. Это используется в режиме
        // администрирования для смены шаблона компонента для инфоблока без
        // перезагрузки страницы.
        $styles_tag =
            isset($isNaked) && $isNaked &&
            isset($cc_only) && $cc_only &&
            isset($include_component_style_tag) && $include_component_style_tag
                ? trim($nc_core->page->get_site_component_styles_tag())
                : '';

        $result = "<div id='nc_admin_mode_content{$cc}' class='nc_admin_mode_content" .
                  ($inside_admin ? "" : " nc-admin-mode-content-box nc-infoblock") .
                  "'>" . $styles_tag . $result . "</div>";
    }

    return $result;
}

function s_list_class($sub, $cc, $query_string = "", $show_in_admin_mode = false) {
    return nc_objects_list($sub, $cc, $query_string, $show_in_admin_mode);
}

function nc_widgets_block() {
    return call_user_func_array(array(nc_core('widget'), 'render_widgets_block'), func_get_args());
}

// function nc_widgets_block_array() {
//     return call_user_func_array(array(nc_core('widget'), 'get_block_widgets'), func_get_args());
// }

function showSearchForm($fldName, $fldType, $fldDoSearch, $fldFmt, $isAdd = false) {
    global $systemTableID, $db, $srchPat, $srchPatAdd, $nc_core;

    $result = '';
    $j = 0;
    $srchPatName = ($isAdd === false) ? "srchPat" : "srchPatAdd";
    $srchPatValues = $$srchPatName;
    for ($i = 0; $i < count($fldName); $i++) {
        $fld_prefix = "<div>";
        $fld_suffix = "</div>\n";
        $fldNameTempl = $fld_prefix . "" . $fldName[$i] . ": ";

        if (!$fldDoSearch[$i]) {
            continue;
        }

        $stringValue = htmlspecialchars(stripcslashes($srchPatValues[$j]), ENT_QUOTES);
        $stringValue = addcslashes($stringValue, '$');
        switch ($fldType[$i]) {
            case 1: // Char
                $result .= $fldNameTempl . "<br><input type='text' name='".$srchPatName."[" . $j . "]' size='50' maxlength='255' value='" . $stringValue . "'>" . $fld_suffix;
                $j++;
                break;

            case 3: // Text
                $result .= $fldNameTempl . "<br><input type='text' name='".$srchPatName."[" . $j . "]' size='50' maxlength='255' value='" . $stringValue . "'>" . $fld_suffix;
                $j++;
                break;

            case 6: // File
                $result .= $fldNameTempl . "<br><input type='text' name='".$srchPatName."[" . $j . "]' size='50' maxlength='255' value='" . $stringValue . "'>" . $fld_suffix;
                $j++;
                break;

            case 2: // Int
                $result .= $fldNameTempl . "&nbsp;&nbsp;" . NETCAT_MODERATION_MOD_FROM . " <input type='text' name='".$srchPatName."[" . $j . "]' size='10' maxlength='16' value='" . ($srchPatValues[$j] ? (int)$srchPatValues[$j] : "") . "'>";
                $j++;
                $result .= NETCAT_MODERATION_MOD_DON . "<input type='text' name='".$srchPatName."[" . $j . "]' size='10' maxlength='16' value='" . ($srchPatValues[$j] ? (int)$srchPatValues[$j] : "") . "'>" . $fld_suffix;
                $j++;
                break;

            case 7: // Float
                $result .= $fldNameTempl . "&nbsp;&nbsp;" . NETCAT_MODERATION_MOD_FROM . " <input type='text' name='".$srchPatName."[" . $j . "]' size='10' maxlength='16' value='" . ($srchPatValues[$j] ? (float)$srchPatValues[$j] : "") . "'>";
                $j++;
                $result .= NETCAT_MODERATION_MOD_DON . "<input name='".$srchPatName."[" . $j . "]' type='text' size='10' maxlength='16' value='" . ($srchPatValues[$j] ? (float)$srchPatValues[$j] : "") . "'>" . $fld_suffix;
                $j++;
                break;

            case 4: // List
                if ($fldFmt[$i]) {
                    $result .= $fldNameTempl . "<br><select name='".$srchPatName."[" . $j . "]' size='1'>";
                    $result .= "<option value=''>" . NETCAT_MODERATION_MODA . "</option>";

                    $list_format = explode(":" , $fldFmt[$i]);
                    $fldFmt[$i] = $list_format[0]; //название таблицы

                    $SortType = $SortDirection = 0;
                    $res = $db->get_row("SELECT `Sort_Type`, `Sort_Direction` FROM `Classificator` WHERE `Table_Name` = '" . $db->escape($fldFmt[$i]) . "'", ARRAY_N);
                    if ($db->num_rows != 0) {
                        $row = $res;
                        $SortType = $row[0];
                        $SortDirection = $row[1];
                    }

                    $s = "SELECT * FROM `Classificator_" . $db->escape($fldFmt[$i]) . "` WHERE `Checked` = 1 ORDER BY ";
                    switch ($SortType) {
                        case 1:
                            $s .= "`" . $db->escape($fldFmt[$i]) . "_Name`";
                            break;
                        case 2:
                            $s .= "`" . $db->escape($fldFmt[$i]) . "_Priority`";
                            break;
                        default:
                            $s .= "`" . $db->escape($fldFmt[$i]) . "_ID`";
                    }

                    if ($SortDirection == 1) {
                        $s .= " DESC";
                    }

                    $selected = (int)$srchPatValues[$j];
                    $lstRes = (array)$db->get_results($s, ARRAY_N);
                    foreach ($lstRes as $q) {
                        list($lstID, $lstName) = $q;
                        $lstName = htmlspecialchars($lstName);
                        $result .= "<option value='" . $lstID . "'" . ($selected == $lstID ? "selected" : "") . ">" . $lstName . "</option>";
                    }
                    $result .= '</select>' . $fld_suffix;
                }
                $j++;
                break;

            case 5: // Bool
                $result .= $fldNameTempl;
                $result .= "&nbsp;&nbsp;<input type='radio' name='".$srchPatName."[" . $j . "]' id='t" . $j . "_1' value='' style='vertical-align:middle'" . (!$srchPatValues[$j] ? " checked" : "") . "><label for='t" . $j . "_1'>" . NETCAT_MODERATION_MOD_NOANSWER . '</label> ';
                $result .= "&nbsp;&nbsp;<input type='radio' name='".$srchPatName."[" . $j . "]' id='t" . $j . "_2' value='1' style='vertical-align:middle'" . ($srchPatValues[$j] == '1' ? " checked" : "") . "><label for='t" . $j . "_2'>" . CONTROL_CONTENT_SUBDIVISION_FUNCS_OBJ_YES . '</label> ';
                $result .= "&nbsp;&nbsp;<input type='radio' name='".$srchPatName."[" . $j . "]' id='t" . $j . "_3' value='0' style='vertical-align:middle'" . ($srchPatValues[$j] == '0' ? " checked" : "") . "><label for='t" . $j . "_3'>" . CONTROL_CONTENT_SUBDIVISION_FUNCS_OBJ_NO . '</label>';
                $result .= $fld_suffix;
                $j++;
                break;

            case 8: // DateTime
                $format = nc_field_parse_format($fldFmt[$i], 8);
                $result .= $fldNameTempl . "&nbsp;&nbsp;";
                if ($format['calendar'] && nc_module_check_by_keyword('calendar', 0)) {
                    $result .= nc_set_calendar(0) . "<br/>";
                }
                $result .= NETCAT_MODERATION_MOD_FROM;


                if ($format['type'] != 'event_time') {
                    $result .= "<input type='text' class='no_special_style' name='".$srchPatName."[" . $j . "]' size='2' maxlength='2' value='" . ($srchPatValues[$j] ? sprintf("%02d", $srchPatValues[$j]) : "") . "' placeholder='" . NETCAT_HINT_FIELD_D . "' >.";
                    $j++;
                    $result .= "<input type='text' class='no_special_style' name='".$srchPatName."[" . $j . "]' size='2' maxlength='2' value='" . ($srchPatValues[$j] ? sprintf("%02d", $srchPatValues[$j]) : "") . "' placeholder='" . NETCAT_HINT_FIELD_M . "' >.";
                    $j++;
                    $result .= "<input type='text' class='no_special_style' name='".$srchPatName."[" . $j . "]' size='4' maxlength='4' value='" . ($srchPatValues[$j] ? sprintf("%04d", $srchPatValues[$j]) : "") . "' placeholder='" . NETCAT_HINT_FIELD_Y . "' > ";
                    $j++;
                }
                else {
                    $j += 3;
                }
                if ($format['type'] != 'event_date') {
                    $result .= "<input type='text' class='no_special_style' name='".$srchPatName."[" . $j . "]' size='2' maxlength='2' value='" . ($srchPatValues[$j] ? sprintf("%02d", $srchPatValues[$j]) : "") . "' placeholder='" . NETCAT_HINT_FIELD_H . "' >:";
                    $j++;
                    $result .= "<input type='text' class='no_special_style' name='".$srchPatName."[" . $j . "]' size='2' maxlength='2' value='" . ($srchPatValues[$j] ? sprintf("%02d", $srchPatValues[$j]) : "") . "' placeholder='" . NETCAT_HINT_FIELD_MIN . "' >:";
                    $j++;
                    $result .= "<input type='text' class='no_special_style' name='".$srchPatName."[" . $j . "]' size='2' maxlength='2' value='" . ($srchPatValues[$j] ? sprintf("%02d", $srchPatValues[$j]) : "") . "' placeholder='" . NETCAT_HINT_FIELD_S . "' > ";
                    $j++;
                }
                else {
                    $j += 3;
                }

                if ($format['calendar']&& nc_module_check_by_keyword('calendar', 0) && $format['type'] != 'event_time') {
                    $result .= "<div style='display: inline; position: relative;'>
                         <img  id='nc_calendar_popup_img_".$srchPatName."[" . ($j - 6) . "]' onclick=\\\"nc_calendar_popup('".$srchPatName."[" . ($j - 6) . "]', '".$srchPatName."[" . ($j - 5) . "]', '".$srchPatName."[" . ($j - 4) . "]', '0');\\\" src='" . $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH . "modules/calendar/images/calendar.jpg' style='cursor: pointer; position: absolute; left: 7px; top: -3px;'/>
                       </div>
                       <div style='display: none; z-index: 10000;' id='nc_calendar_popup_".$srchPatName."[" . ($j - 6) . "]'></div><br/>";
                }

                $result .= NETCAT_MODERATION_MOD_DON;
                if ($format['type'] != 'event_time') {
                    $result .= "<input type='text' class='no_special_style' name='".$srchPatName."[" . $j . "]' size='2' maxlength='2' value='" . ($srchPatValues[$j] ? sprintf("%02d", $srchPatValues[$j]) : "") . "' placeholder='" . NETCAT_HINT_FIELD_D . "' >.";
                    $j++;
                    $result .= "<input type='text' class='no_special_style' name='".$srchPatName."[" . $j . "]' size='2' maxlength='2' value='" . ($srchPatValues[$j] ? sprintf("%02d", $srchPatValues[$j]) : "") . "' placeholder='" . NETCAT_HINT_FIELD_M . "' >.";
                    $j++;
                    $result .= "<input type='text' class='no_special_style' name='".$srchPatName."[" . $j . "]' size='4' maxlength='4' value='" . ($srchPatValues[$j] ? sprintf("%04d", $srchPatValues[$j]) : "") . "' placeholder='" . NETCAT_HINT_FIELD_Y . "' > ";
                    $j++;
                }
                else {
                    $j += 3;
                }
                if ($format['type'] != 'event_date') {
                    $result .= "<input type='text' class='no_special_style' name='".$srchPatName."[" . $j . "]' size='2' maxlength='2' value='" . ($srchPatValues[$j] ? sprintf("%02d", $srchPatValues[$j]) : "") . "' placeholder='" . NETCAT_HINT_FIELD_H . "' >:";
                    $j++;
                    $result .= "<input type='text' class='no_special_style' name='".$srchPatName."[" . $j . "]' size='2' maxlength='2' value='" . ($srchPatValues[$j] ? sprintf("%02d", $srchPatValues[$j]) : "") . "' placeholder='" . NETCAT_HINT_FIELD_MIN . "' >:";
                    $j++;
                    $result .= "<input type='text' class='no_special_style' name='".$srchPatName."[" . $j . "]' size='2' maxlength='2' value='" . ($srchPatValues[$j] ? sprintf("%02d", $srchPatValues[$j]) : "") . "' placeholder='" . NETCAT_HINT_FIELD_S . "' > ";
                    $j++;
                }
                else {
                    $j += 3;
                }

                if ($format['calendar'] && $format['type'] != 'event_time') {
                    $result .= "<div style='display: inline; position: relative;'>
                         <img  id='nc_calendar_popup_img_".$srchPatName."[" . ($j - 6) . "]' onclick=\\\"nc_calendar_popup('".$srchPatName."[" . ($j - 6) . "]', '".$srchPatName."[" . ($j - 5) . "]', '".$srchPatName."[" . ($j - 4) . "]', '0');\\\" src='" . $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH . "modules/calendar/images/calendar.jpg' style='cursor: pointer; position: absolute; left: 7px; top: -3px;'/>
                       </div>
                       <div style='display: none; z-index: 10000;' id='nc_calendar_popup_".$srchPatName."[" . ($j - 6) . "]'></div><br/>";
                }

                $result .= $fld_suffix;
                break;

            case 10: //Multi List
                if ($fldFmt[$i]) {
                    list($clft_name, $type_element, $type_size) = explode(":", $fldFmt[$i]);

                    if (!$type_element) {
                        $type_element = "select";
                    }
                    if (!$type_size) {
                        $type_size = 3;
                    }

                    $fldFmt[$i] = $clft_name;

                    $SortType = $SortDirection = 0;
                    $res = $db->get_row("SELECT `Sort_Type`, `Sort_Direction` FROM `Classificator` WHERE `Table_Name` = '" . $db->escape($fldFmt[$i]) . "'", ARRAY_N);
                    if ($db->num_rows != 0) {
                        $row = $res;
                        $SortType = $row[0];
                        $SortDirection = $row[1];
                    }

                    $s = "SELECT * FROM Classificator_" . $fldFmt[$i] . " ORDER BY ";
                    switch ($SortType) {
                        case 1:
                            $s .= $fldFmt[$i] . "_Name";
                            break;
                        case 2:
                            $s .= $fldFmt[$i] . "_Priority";
                            break;
                        default:
                            $s .= $fldFmt[$i] . "_ID";
                    }

                    if ($SortDirection == 1) {
                        $s .= " DESC";
                    }

                    $selected = (int)$srchPatValues[$j];
                    $lstRes = (array)$db->get_results($s, ARRAY_N);

                    $result .= $fldNameTempl . "<br>";

                    if ($type_element == 'select') {
                        $result .= "<select name='".$srchPatName."[" . $j . "][]' size='" . $type_size . "' multiple>";
                        $result .= "<option value=''>" . NETCAT_MODERATION_MODA . "</option>";
                    }


                    foreach ($lstRes as $q) {
                        list($lstID, $lstName) = $q;
                        $lstName = htmlspecialchars($lstName);
                        $temp_str = '';
                        if ($lstID == $selected) {
                            $temp_str = ($type_element == "select") ? " selected" : " checked";
                        }

                        if ($type_element == 'select') { #TODO сделать возможность передавать селектед в виде массива
                            $result .= "<option value='" . $lstID . "' " . $temp_str . ">" . $lstName . "</option>";
                        }
                        else {
                            $result .= "<input type='checkbox' value='" . $lstID . "' name='".$srchPatName."[" . $j . "][]' " . $temp_str . "> " . $lstName . "<br>\r\n";
                        }
                    }

                    if ($type_element == 'select') {
                        $result .= '</select><br>';
                    } //.$fld_suffix;

                    $j++;
                    $result .= "<input type='hidden' name='".$srchPatName."[" . $j . "]' value='0'>\n";
                    $result .= $fld_suffix;
                }
                $j++;
                break;
        }
        $result .= "<br>\n";
    }

    if (!$j) {
        return false;
    }

    return $result;
}



function getSearchParams($field_name, $field_type, $field_search, $srchPat) {
    global $db;

    // return if search params not set
    if (empty($srchPat)) {
        return array("query" => "", "link" => "");
    }
    $search_param = array();
    for ($i = 0, $j = 0; $i < count($field_name); $i++) {
        if ($field_search[$i]) {
            switch ($field_type[$i]) {
                case 1: // Char
                    if ($srchPat[$j] == "") {
                        break;
                    }
                    $srch_str = $db->escape(urldecode($srchPat[$j]));
                    $fullSearchStr .= " AND a." . $field_name[$i] . " LIKE '%" . $srch_str . "%'";
                    $search_param[] = "srchPat[" . $j . "]=" . urldecode($srchPat[$j]);
                    break;
                case 2: // Int
                    if ($srchPat[$j] != "") {
                        $srchPat[$j] += 0;
                        $fullSearchStr .= " AND a." . $field_name[$i] . ">=" . $srchPat[$j];
                        $search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j];
                    }
                    $j++;
                    if ($srchPat[$j] != "") {
                        $srchPat[$j] += 0;
                        $fullSearchStr .= " AND a." . $field_name[$i] . "<=" . $srchPat[$j];
                        $search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j];
                    }
                    break;
                case 3: // Text
                    if ($srchPat[$j] == "") {
                        break;
                    }
                    $srch_str = $db->escape(urldecode($srchPat[$j]));
                    $fullSearchStr .= " AND a." . $field_name[$i] . " LIKE '%" . $srch_str . "%'";
                    $search_param[] = "srchPat[" . $j . "]=" . urldecode($srchPat[$j]);
                    break;
                case 4: // List
                    if ($srchPat[$j] == "") {
                        break;
                    }
                    $srchPat[$j] += 0;
                    $fullSearchStr .= " AND a." . $field_name[$i] . "=" . $srchPat[$j];
                    $search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j];
                    break;
                case 5: // Boolean
                    if ($srchPat[$j] == "") {
                        break;
                    }
                    $srchPat[$j] += 0;
                    $fullSearchStr .= " AND a." . $field_name[$i] . "=" . $srchPat[$j];
                    $search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j];
                    break;
                case 6: // File
                    if ($srchPat[$j] == "") {
                        break;
                    }
                    $srch_str = $db->escape(urldecode($srchPat[$j]));
                    $fullSearchStr .= " AND SUBSTRING_INDEX(a." . $field_name[$i] . ",':',1) LIKE '%" . $srch_str . "%'";
                    $search_param[] = "srchPat[" . $j . "]=" . urldecode($srchPat[$j]);
                    break;
                case 7: // Float
                    if ($srchPat[$j] != "") {
                        $srchPat[$j] = floatval($srchPat[$j]);
                        $fullSearchStr .= " AND a." . $field_name[$i] . ">=" . $srchPat[$j];
                        $search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j];
                    }
                    $j++;
                    if ($srchPat[$j] != "") {
                        $srchPat[$j] = floatval($srchPat[$j]);
                        $fullSearchStr .= " AND a." . $field_name[$i] . "<=" . $srchPat[$j];
                        $search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j];
                    }
                    break;
                case 8: // DateTime
                    $date_from['d'] = ($srchPat[$j] && ($search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                    $j++;
                    $date_from['m'] = ($srchPat[$j] && ($search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                    $j++;
                    $date_from['Y'] = ($srchPat[$j] && ($search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j]) ? sprintf("%04d", $srchPat[$j]) : false);
                    $j++;
                    $date_from['H'] = ($srchPat[$j] && ($search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                    $j++;
                    $date_from['i'] = ($srchPat[$j] && ($search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                    $j++;
                    $date_from['s'] = ($srchPat[$j] && ($search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                    $j++;
                    $date_to['d'] = ($srchPat[$j] && ($search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                    $j++;
                    $date_to['m'] = ($srchPat[$j] && ($search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                    $j++;
                    $date_to['Y'] = ($srchPat[$j] && ($search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j]) ? sprintf("%04d", $srchPat[$j]) : false);
                    $j++;
                    $date_to['H'] = ($srchPat[$j] && ($search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                    $j++;
                    $date_to['i'] = ($srchPat[$j] && ($search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);
                    $j++;
                    $date_to['s'] = ($srchPat[$j] && ($search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j]) ? sprintf("%02d", $srchPat[$j]) : false);

                    $date_format_from = ($date_from['Y'] ? '%Y' : '') . ($date_from['m'] ? '%m' : '') . ($date_from['d'] ? '%d' : '') . ($date_from['H'] ? '%H' : '') . ($date_from['i'] ? '%i' : '') . ($date_from['s'] ? '%s' : '');
                    $date_format_to = ($date_to['Y'] ? '%Y' : '') . ($date_to['m'] ? '%m' : '') . ($date_to['d'] ? '%d' : '') . ($date_to['H'] ? '%H' : '') . ($date_to['i'] ? '%i' : '') . ($date_to['s'] ? '%s' : '');

                    if ($date_format_from) {
                        $fullSearchStr .= " AND DATE_FORMAT(a." . $field_name[$i] . ",'" . $date_format_from . "')>=" . $date_from['Y'] . $date_from['m'] . $date_from['d'] . $date_from['H'] . $date_from['i'] . $date_from['s'];
                    }
                    if ($date_format_to) {
                        $fullSearchStr .= " AND DATE_FORMAT(a." . $field_name[$i] . ",'" . $date_format_to . "')<=" . $date_to['Y'] . $date_to['m'] . $date_to['d'] . $date_to['H'] . $date_to['i'] . $date_to['s'];
                    }

                    break;

                case 10: // MultiList
                    if ($srchPat[$j] == "") {
                        $j++;
                        break;
                    }

                    $id = array(); // массив с id искомых элементов

                    if (is_array($srchPat[$j])) {
                        foreach ((array)$srchPat[$j] as $v) {
                            $id[] = +$v;
                        }
                    }
                    else {
                        $temp_id = explode('-', $srchPat[$j]);
                        foreach ((array)$temp_id as $v) {
                            $id[] = +$v;
                        }
                    }
                    $j++; //второй параметр - это тип посика

                    if (empty($id)) {
                        break;
                    }

                    $fullSearchStr .= " AND (";
                    switch ($srchPat[$j]) {
                        case 1: //Полное совпадение
                            $fullSearchStr .= "a." . $field_name[$i] . " LIKE CONCAT(',' ,  '" . join(',', $id) . "', ',') ";
                            break;

                        case 2: //Хотя бы один. Выбор между LIKE и REGEXP выпал в сторону первого
                            foreach ($id as $v)
                                $fullSearchStr .= "a." . $field_name[$i] . " LIKE CONCAT('%,', '" . $v . "', ',%') OR ";
                            $fullSearchStr .= "0 "; //чтобы "закрыть" последний OR
                            break;
                        case 0: // как минимум выбранные - частичное совпадение - по умолчанию
                        default:
                            $srchPat[$j] = 0;
                            $fullSearchStr .= "a." . $field_name[$i] . "  REGEXP  \"((,[0-9]+)*)";
                            $prev_v = -1;
                            foreach ($id as $v) {
                                $fullSearchStr .= "(," . $v . ",)([0-9]*)((,[0-9]+)*)";
                                $prev_v = $v;
                            }
                            $fullSearchStr .= '"';
                            break;
                    }
                    $fullSearchStr .= ")";

                    $search_param[] = "srchPat[" . ($j - 1) . "]=" . join('-', $id);
                    $search_param[] = "srchPat[" . $j . "]=" . $srchPat[$j];
                    break;
            }
            $j++;
        }
    }

    if (!empty($search_param)) {
        $search_params['link'] = join('&amp;', $search_param);
    }
    $search_params['query'] = $fullSearchStr;

    return $search_params;
}

/**
 * @param int|string $component_id          ID/тип компонента (например: 'User', 'Template', 123)
 * @param int|int[] $object_id_or_ids       ID или массив с ID объектов
 * @param string|null $returned_field_name  Если указано — вернуть значения только для указанного поля
 * @return array|null|nc_multifield
 *    Если $object_ids — массив, то массив с массивом nc_multifield для каждого объекта (ключ — ID объекта).
 *    Если $object_ids — число, то массив с полями для указанного объекта.
 *    Если $object_ids — число и указано $field_name — объект nc_multifile.
 *    Если у компонента нет полей типа MULTIFILE — NULL.
 */
function nc_get_multifile_field_values($component_id, $object_id_or_ids, $returned_field_name = null) {
    $object_ids = (array)$object_id_or_ids;

    $component = new nc_component($component_id);
    $fields = $component->get_fields(NC_FIELDTYPE_MULTIFILE, false);

    if ($returned_field_name) { // fetch data for a single field only
        $returned_field_id = array_search($returned_field_name, $fields);
        $fields = array($returned_field_id => $fields[$returned_field_id]);
    }

    if (!$fields) { return null; }

    /** @var nc_multifield[][] $results */
    $result = array();
    foreach ($object_ids as $object_id) {
        foreach ($fields as $field_id => $field_name) {
            $result[$object_id][$field_name] = new nc_multifield($field_name, null, null, $field_id);
        }
    }

    $rows = (array)nc_db()->get_results(
        "SELECT `Field_ID`,
                `Message_ID`,
                `Priority`,
                `Name`,
                `Size`,
                `Path`,
                `Preview`,
                `ID`
           FROM `Multifield`
          WHERE `Message_ID` IN (" . join(",", array_map('intval', $object_ids)) . ")
            AND `Field_ID` IN (" . join(",", array_keys($fields)) . ")
          ORDER BY `Priority`",
        ARRAY_A
    );

    foreach ($rows as $row) {
        $object_id= $row['Message_ID'];
        $field_name = $fields[$row['Field_ID']];
        $result[$object_id][$field_name]->add_record($row);
    }

    if (!is_array($object_id_or_ids)) {
        if ($returned_field_name) {
            return $result[$object_id_or_ids][$returned_field_name];
        }
        else {
            return $result[$object_id_or_ids];
        }
    }
    else {
        return $result;
    }
}


function nc_AdminCommon($sub, $cc, $cc_env, $f_AdminCommon_package, $f_AdminCommon_add, $f_AdminCommon_delete_all, $show_add_record_button) {
    $nc_core = nc_Core::get_object();
    $system_env = $nc_core->get_settings();
    $ADMIN_TEMPLATE = $nc_core->get_variable("ADMIN_TEMPLATE");
    $f_AdminCommon_cc_name = $cc_env['Sub_Class_Name'];
    $f_AdminCommon_cc = $cc;

    if ($system_env['AdminButtonsType']) {
        eval(nc_check_eval("\$f_AdminCommon = \"" . $system_env['AdminCommon'] . "\";"));
    }
    else {
        $f_AdminCommon_buttons = "\n<li><span>" . $cc_env['Sub_Class_ID'] . "</span></li>\n";
        if ($show_add_record_button) {
            $f_AdminCommon_buttons .= "<li><a onClick='nc.load_dialog(this.href); return false' href='$f_AdminCommon_add'>" . NETCAT_MODERATION_BUTTON_ADD . "</a></li>\n";
        }
        $f_AdminCommon_buttons .= nc_get_AdminCommon_multiedit_button($cc_env) . "
    " . ($nc_core->InsideAdminAccess ? "
        <li><a onClick='parent.nc_form(this.href); return false;' href='{$nc_core->SUB_FOLDER}admin/class/index.php?phase=4&ClassID=" . ($cc_env['Class_Template_ID'] ? $cc_env['Class_Template_ID'] : $cc_env['Class_ID']) . "'>
            <i class='nc-icon nc--dev-components' title='" . CONTROL_CLASS_DOEDIT . "'></i>
        </a></li>
    " : "") . "
    <li><a onClick='parent.nc_form(this.href); return false;' href='{$nc_core->ADMIN_PATH}subdivision/SubClass.php?SubdivisionID={$cc_env['Subdivision_ID']}&sub_class_id={$cc_env['Sub_Class_ID']}'>
        <i class='nc-icon nc--settings' title='" . CONTROL_CLASS_CLASS_SETTINGS . "'></i>
    </a></li>
    <li><a href='$f_AdminCommon_delete_all'>
        <i class='nc-icon nc--remove' title='" . NETCAT_MODERATION_REMALL . "'></i>
    </a></li>";


        if ($nc_core->get_settings('PacketOperations')) {
            $f_AdminCommon_buttons .= "<li class='nc-divider'></li>
                <li class='nc--alt'><a href='#' onclick='nc_package_obj.process(\"checkOn\", " . $cc . "); return false;'>
                    <i class='nc-icon nc--selected-on' title='" . NETCAT_MODERATION_SELECTEDON . "'></i>
                </a></li>
                <li class='nc--alt'><a href='#' onclick='nc_package_obj.process(\"checkOff\", " . $cc . "); return false;'>
                    <i class='nc-icon nc--selected-off' title='" . NETCAT_MODERATION_SELECTEDOFF . "'></i>
                </a></li>
                <li class='nc--alt'><a href='#' onclick='nc_package_obj.process(\"delete\", " . $cc . "); return false;'>
                    <i class='nc-icon nc--selected-remove' title='" . NETCAT_MODERATION_DELETESELECTED . "'></i>
                </a></li>
            ";
        }

        $f_AdminCommon = "<div class='nc_idtab nc_admincommon'>";
        if (CheckUserRights($cc, 'add', 1) == 1) {
            $f_AdminCommon = "<ul class='nc-toolbar nc--right main-toolbar'>" . $f_AdminCommon_buttons . "</ul>
              <div class='nc--clearfix'></div>";
            $f_AdminCommon .= $f_AdminCommon_package;
        }
        else {
            $f_AdminCommon .= "<div class='nc_idtab_id'>
                                  <div class='nc_idtab_messageid error' title='" . NETCAT_MODERATION_ERROR_NORIGHT . "'>
                                      " . NETCAT_MODERATION_ERROR_NORIGHT . "
                                  </div>
                              </div>
                              <div class='ncf_row nc_clear'></div>";
        }
        $f_AdminCommon .= "<div class='nc--clearfix'></div>";
    }
    return $f_AdminCommon;
}

function nc_get_AdminCommon_multiedit_button($cc_env) {
    $nc_core = nc_Core::get_object();
    $result = '';
    $multi_edit_template_id = nc_get_AdminCommon_multiedit_button_template_id($cc_env['Class_ID']);
    if ($multi_edit_template_id) {
        $href = $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH . "index.php?" .
            "isModal=1" .
            "&amp;catalogue={$cc_env['Catalogue_ID']}" .
            "&amp;sub={$cc_env['Subdivision_ID']}" .
            "&amp;cc={$cc_env['Sub_Class_ID']}" .
            "&amp;nc_ctpl=$multi_edit_template_id" .
            ($nc_core->inside_admin ? "&amp;inside_admin=1" : "");
        $result = "
            <li><a onClick='nc.load_dialog(this.href); return false;' href='$href'>
                <i class='nc-icon nc--edit' title='" . CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_MULTI_EDIT . "'></i>
            </a></li>";
    }

    return $result;
}

function nc_get_AdminCommon_multiedit_button_template_id($class_id) {
    static $data = array();

    if (!isset($data[$class_id])) {
        $data[$class_id] = +nc_Core::get_object()->db->get_var("SELECT Class_ID FROM Class WHERE (Class_ID = $class_id OR ClassTemplate = $class_id) AND Type = 'multi_edit'");
    }

    return $data[$class_id];
}

function nc_get_fullLink($admin_url_prefix, $catalogue, $sub, $cc, $f_RowID, $inside_admin = 0) {
    return $admin_url_prefix . "full.php?inside_admin=" . $inside_admin . "&amp;sub=" . $sub . "&amp;cc=" . $cc . "&amp;message=" . $f_RowID;
}

function nc_get_fullDateLink($fullLink, $dateLink) {
    return $fullLink . $dateLink;
}

function nc_get_AdminButtons_user_change($f_LastUserID) {
    return $f_LastUserID ? $f_LastUserID : "";
}

function nc_get_AdminButtons_copy($ADMIN_PATH, $catalogue, $sub, $cc, $classID, $f_RowID) {
    return $ADMIN_PATH . "objects/copy_message.php?catalogue=" . $catalogue . "&amp;sub=" . $sub . "&amp;cc=" . $cc . "&amp;classID=" . $classID . "&amp;message=" . $f_RowID;
}

function nc_get_AdminButtons_change($SUB_FOLDER, $HTTP_ROOT_PATH, $catalogue, $sub, $cc, $f_RowID, $curPos, $inside_admin = 0) {
    return $SUB_FOLDER . $HTTP_ROOT_PATH . "message.php?" . ($inside_admin ? 'inside_admin=1&amp;' : '') . "catalogue=" . $catalogue . "&amp;sub=" . $sub . "&amp;cc=" . $cc . "&amp;message=" . $f_RowID . ($curPos ? "&amp;curPos=" . $curPos : "");
}

function nc_get_AdminButtons_version($SUB_FOLDER, $HTTP_ROOT_PATH, $catalogue, $sub, $cc, $f_RowID, $curPos, $inside_admin = 0) {
    return $SUB_FOLDER . $HTTP_ROOT_PATH . "message.php?" . ($inside_admin ? 'inside_admin=1&amp;' : '') . "isVersion=1&amp;restore=1&amp;catalogue=" . $catalogue . "&amp;sub=" . $sub . "&amp;cc=" . $cc . "&amp;message=" . $f_RowID . ($curPos ? "&amp;curPos=" . $curPos : "");
}

function nc_get_AdminButtons_delete($SUB_FOLDER, $HTTP_ROOT_PATH, $catalogue, $sub, $cc, $f_RowID, $curPos, $inside_admin = 0) {
    return $SUB_FOLDER . $HTTP_ROOT_PATH . "message.php?" . ($inside_admin ? 'inside_admin=1&amp;' : '') . "catalogue=" . $catalogue . "&amp;sub=" . $sub . "&amp;cc=" . $cc . "&amp;message=" . $f_RowID . "&amp;delete=1" . ($curPos ? "&amp;curPos=" . $curPos : "");
}

function nc_get_dropLink($deleteLink, $nc_core) {
    return $deleteLink . "&posting=1" . ($nc_core->token->is_use('drop') ? "&" . $nc_core->token->get_url() : "");
}

function nc_get_AdminButtons_check($f_Checked, $SUB_FOLDER, $HTTP_ROOT_PATH, $catalogue, $sub, $cc, $classID, $f_RowID, $curPos, $admin_mode, $admin_url_prefix, $nc_core) {
    return $admin_url_prefix .
           "message.php?catalogue=" . $catalogue .
           "&amp;sub=" . $sub .
           "&amp;cc=" . $cc .
           "&amp;classID=" . $classID .
           "&amp;message=" . $f_RowID .
           "&amp;checked=" . ($f_Checked ? 1 : 2) .
           "&amp;posting=1" .
           ($curPos ? "&amp;curPos=" . $curPos : "") .
           ($admin_mode ? "&amp;admin_mode=1" : "") .
           ($nc_core->inside_admin ? "&amp;inside_admin=1" : "") .
           ($nc_core->token->is_use('edit') ? "&amp;" . $nc_core->token->get_url() : "");
}

function nc_get_AdminButtons_select($f_AdminButtons_id) {
    return "top.selectItem(" . $f_AdminButtons_id . "); return false;";
}

function nc_get_list_mode_select_AdminButtons_buttons($f_AdminButtons_select, $ADMIN_TEMPLATE) {
    return "<a href='#' onclick='" . $f_AdminButtons_select . "' title='" . NETCAT_MODERATION_SELECT_RELATED . "' > " . NETCAT_MODERATION_SELECT_RELATED . " </a>";
}

function nc_get_list_mode_select($f_Checked, $classID, $f_RowID, $f_AdminButtons_id, $f_AdminButtons_buttons) {
    $f_AdminButtons = "<ul class='nc-toolbar nc--left'>";
    $f_AdminButtons .= "<li><b># " . $f_AdminButtons_id . "</b></li>";
    $f_AdminButtons .= "<li>" . $f_AdminButtons_buttons . "</li>";

    $f_AdminButtons .= "</ul><div class='nc--clearfix'></div>";
    return $f_AdminButtons;
}

function nc_get_AdminButtons_buttons($f_RowID, $f_Checked, $f_AdminButtons_check, $f_AdminButtons_uncheck, $f_AdminButtons_copy, $f_AdminButtons_change, $f_AdminButtons_delete, $f_AdminButtons_version='', $cc, $subdivision_id = '', $class_id = '', $show_landing_buttons = '') {
    $nc_core = nc_Core::get_object();

    $result = "";
    if ($nc_core->get_settings('PacketOperations')) {
        $result .= "
        <li><label>
            <input class='nc_multi_check' type='checkbox' onchange='nc_package_obj.select(" . $f_RowID . ", " . $cc . ");' > " . $f_RowID . "
        </label></li>";
    }

    if ($f_AdminButtons_check != '') {
        $result .= "<li><a onClick='parent.nc_action_message(this.href); return false;' href='" . $f_AdminButtons_check . "'>
	        <span class='nc-text-" . ($f_Checked ? 'green' : 'red') . "'>" . ($f_Checked ? NETCAT_MODERATION_OBJ_ON : NETCAT_MODERATION_OBJ_OFF) . "</span>
	    </a></li>";
    }

    if ($f_AdminButtons_copy != '') {
        $result .= "<li><a href='#' onclick=\"window.open('" . $f_AdminButtons_copy . "', 'nc_popup_test1', 'width=800,height=500,menubar=no,resizable=no,scrollbars=no,toolbar=no,resizable=yes'); return false;\">
        	<i class='nc-icon nc--copy' title='" . NETCAT_MODERATION_COPY_OBJECT . "'></i>
    	</a></li>";
    }

    if ($show_landing_buttons) {
        $site_id = $nc_core->sub_class->get_by_id($cc, 'Catalogue_ID');
        $result .= nc_get_AdminButtons_landing_button($site_id, $class_id, $f_RowID);
    }

    if ($f_AdminButtons_change != '') {
        $result .= "<li><a onClick='nc.load_dialog(this.href); return false;' href='" . $f_AdminButtons_change . "'>
	        <i class='nc-icon nc--edit' title='" . NETCAT_MODERATION_CHANGE . "'></i>
	    </a></li>";
    }

    if ($nc_core->get_settings('AutosaveUse') == 1) {
        $revision = nc_Core::get_object()->revision;
        $revision->set_indexes(array('Subdivision_ID' => $subdivision_id, 'Class_ID' => $class_id, 'Sub_Class_ID' => $cc), $f_RowID);

        if ($revision->check_draft_exists() == true) {
            $result .= "<li><a onClick='parent.nc_form(this.href); return false;' href='" . $f_AdminButtons_version . "'>
                <i class='nc-icon nc--clock' title='" . NETCAT_MODERATION_VERSION . "'></i>
            </a></li>";
        } else {
            $result .= "<li><a href=# style='cursor: default;' onclick='return false;'><i class='nc-icon nc--clock nc--dark' title='" . NETCAT_MODERATION_VERSION_NOT_FOUND . "'></i></a></li>";
        }
    }
    if ($f_AdminButtons_delete != '') {
        $result .= "<li><a" . ($nc_core->admin_mode ? " onClick='parent.nc_action_message(this.href); return false;'" : "") . " href='" . $f_AdminButtons_delete . "'>
            <i class='nc-icon nc--remove' title='" . NETCAT_MODERATION_DELETE . "'></i>
        </a></li>";
    }

    return $result;
}

/**
 * Временная функция, генерирует кнопку создания лендинг-страницы в «старом» стиле
 * @param $site_id
 * @param $component_id
 * @param $object_id
 * @return string
 */
function nc_get_AdminButtons_landing_button($site_id, $component_id, $object_id) {
    $create_landing_link = nc_landing::get_instance($site_id)->get_object_landing_create_dialog_url($component_id, $object_id);
    return "<li><a href='" . htmlspecialchars($create_landing_link, ENT_QUOTES) . "' target='_blank' onclick='nc.load_dialog(this.href); return false'>
	        <i class='nc-icon nc--mod-landing' title='" . NETCAT_MODULE_LANDING_CONSTRUCTOR_BUTTON_TITLE . "'></i>
	        </a></li>";
}

function nc_get_AdminButtonsMultiBlock(
    $f_RowID,
    $f_Checked,
    $f_AdminButtons_check,
    $f_AdminButtons_uncheck,
    $f_AdminButtons_copy,
    $f_AdminButtons_change,
    $f_AdminButtons_delete,
    $f_AdminButtons_version='',
    $cc,
    $subdivision_id,
    $class_id,
    $show_landing_buttons,
    $show_drag_button
) {
    $nc_core = nc_Core::get_object();

    $result = "<ul class='nc6-toolbar nc-object-toolbar'>";
//    if ($nc_core->get_settings('PacketOperations')) {
//        $result .= "
//        <li><label>
//            <input class='nc_multi_check' type='checkbox' onchange='nc_package_obj.select(" . $f_RowID . ", " . $cc . ");' > " . $f_RowID . "
//        </label></li>";
//    }

    // Перетаскивание
    if ($show_drag_button) {
        $result .=
            "<li class='nc-move-place' id='message" . $class_id . "-" . $f_RowID . "_handler'>" .
            "<i class='nc-icon-drag'></i></li>";
    }

    // Редактирование
    if ($f_AdminButtons_change != '') {
        $result .= "<li><a onClick='nc.load_dialog(this.href); return false;' href='" . $f_AdminButtons_change . "'>
	        <i class='nc-icon-edit' title='" . NETCAT_MODERATION_CHANGE . "'></i>
	    </a></li>";
    }

    // Вкл-выкл
    if ($f_AdminButtons_check != '') {

        $result .= "<li><a href='$f_AdminButtons_check' onClick='parent.nc_action_message(this.href); return false;'>";

        if ($f_Checked) {
            $result .= "<span class='nc-text-green'>" . NETCAT_MODERATION_OBJ_ON . "</span>";
        }
        else {
            $result .= "<span class='nc-text-red'>" . NETCAT_MODERATION_OBJ_OFF . "</span>";
        }

        $result .= "</a></li>";
    }

    // Черновик
    if ($nc_core->get_settings('AutosaveUse') == 1) {
        $revision = nc_Core::get_object()->revision;
        $revision->set_indexes(array('Subdivision_ID' => $subdivision_id, 'Class_ID' => $class_id, 'Sub_Class_ID' => $cc), $f_RowID);

        if ($revision->check_draft_exists() == true) {
            $result .= "<li><a onClick='parent.nc_form(this.href); return false;' href='" . $f_AdminButtons_version . "'>
                <i class='nc-icon-object-version' title='" . NETCAT_MODERATION_VERSION . "'></i>
            </a></li>";
        }
    }

    // все остальные пункты идут в подменю ···
    $result .= "<li class='nc--dropdown nc-object-toolbar-more'>" .
        "<a><i class='nc-icon-more' title='" . htmlspecialchars(NETCAT_MODERATION_MORE, ENT_QUOTES) . "'></i></a>" .
        "<ul>";

    // Лендинг
    if ($show_landing_buttons) {
        $site_id = $nc_core->subdivision->get_by_id($subdivision_id, 'Catalogue_ID');
        $create_landing_link = nc_landing::get_instance($site_id)->get_object_landing_create_dialog_url($class_id, $f_RowID);
        $result .= "<li><a href='" . htmlspecialchars($create_landing_link, ENT_QUOTES) . "' target='_blank' onclick='nc.load_dialog(this.href); return false'>
        	<i class='nc-icon-landing'></i><span class='nc--caption'>" . NETCAT_MODULE_LANDING_CONSTRUCTOR_BUTTON_TITLE . "</span>
    	</a></li>";
    }

    // Копировать/перенести
    if ($f_AdminButtons_copy != '') {
        $result .= "<li><a href='#' onclick=\"window.open('" . $f_AdminButtons_copy . "', '', 'width=800,height=500,menubar=no,resizable=no,scrollbars=no,toolbar=no,resizable=yes'); return false;\">
        	<i class='nc-icon-copy'></i><span class='nc--caption'>" . NETCAT_MODERATION_COPY_OBJECT . "</span>
    	</a></li>";
    }

    // Удалить
    if ($f_AdminButtons_delete != '') {
        $result .= "<li class='nc-object-delete'><a onclick='parent.nc_action_message(this.href); return false'" .
                " href='$f_AdminButtons_delete'>" .
                "<i class='nc-icon-trash'></i><span class='nc--caption'>" .
                NETCAT_MODERATION_DELETE . "</span></a></li>";
    }


    $result .= "</ul></li>";
    $result .= "<div class='nc-object-toolbar-bridge'></div>";
    $result .= "</ul>";
    return $result;
}


function nc_AdminCommonMultiBlock($infoblock_id, $subdivision_id, $show_add_record_button) {
    $nc_core = nc_Core::get_object();

    $path = $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH;
    $controller_path = $path . 'action.php?ctrl=admin.infoblock&infoblock_id=' . $infoblock_id;

    $toggle_infoblock_path = $controller_path . '&action=toggle';
    $add_object_path = $path . "add.php?sub=$subdivision_id&cc=$infoblock_id";
    $accusative = $nc_core->sub_class->get_by_id($infoblock_id, "ObjectNameSingular");

    $is_enabled = $nc_core->sub_class->get_by_id($infoblock_id, 'Checked');

    $hash = '#' . nc_transliterate($nc_core->sub_class->get_by_id($infoblock_id, 'EnglishName'), true);

    $result = "<ul class='nc6-toolbar nc--right nc-infoblock-toolbar" . ($is_enabled ? "" : " nc--disabled") . "'" .
              " data-infoblock-id='$infoblock_id'>";

    // Добавление элемента
    if ($show_add_record_button) {
        $result .= "<li class='nc--space-after'>" .
            "<a onclick='parent.nc_form(this.href); return false;' href='" . $add_object_path . "'>" .
            "<i class='nc-icon-add'></i><span class='nc--caption'>" .
            NETCAT_MODERATION_ADD_OBJECT . " " .
            (!empty($accusative) ? $accusative : NETCAT_MODERATION_ADD_OBJECT_DEFAULT) . "</a>" .
            "</span></li>";
    }

    // Шаблоны компонента
    $block_templates = array();
    $component_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Class_ID');
    $default_template_can_be_used = $nc_core->component->get_by_id($component_id, 'IsOptimizedForMultipleMode');
    foreach ($nc_core->component->get_component_templates($component_id, 'useful') as $template) {
        if ($template['IsOptimizedForMultipleMode']) {
            $block_templates[] = $template;
        }
    }

    $component_template_id = $nc_core->sub_class->get_by_id($infoblock_id, 'Class_Template_ID');
    if (count($block_templates) + $default_template_can_be_used > 1) {

        $result .= "<li class='nc--dropdown'>" .
            "<a onclick='return false;' href='" . $hash . "'>" .
            "<i class='nc-icon-component-template'></i>" .
            "<span class='nc--caption'>" . NETCAT_MODERATION_COMPONENT_TEMPLATES . "</span>" .
            "</a>" .
            "<ul class='nc-infoblock-template-list'>";

        if ($default_template_can_be_used) {
            $result .= "<li>" .
                (!$component_template_id
                    ? NETCAT_MODERATION_COMPONENT_NO_TEMPLATE
                    : "<a onclick='return nc_infoblock_set_template($infoblock_id,0)' href='$hash'>" .
                      NETCAT_MODERATION_COMPONENT_NO_TEMPLATE . "</a>"
                ) .
                "</li>";
        }

        foreach ($block_templates as $template) {
            if ($template['Class_ID'] == $component_template_id) {
                $return = "false";
                $class = " class='nc--current'";
            }
            else {
                $return = "nc_infoblock_set_template($infoblock_id,$template[Class_ID])";
                $class = "";
            }

            $result .= "<li$class><a onclick='return $return' href='$hash'>" .
                        htmlspecialchars($template['Class_Name']) .
                        "</a></li>";
        }

        $result .= "</ul></li>";
    }

    // Настройки инфоблока
    $component_template_custom_settings = $nc_core->component->get_by_id($component_template_id ?: $component_id, 'CustomSettingsTemplate');

    if ($component_template_custom_settings) {
        $settings_array = array();
        eval($component_template_custom_settings);
        $has_visible_custom_settings = false;
        foreach ($settings_array as $row) {
            if (!isset($row['skip_in_form']) || !$row['skip_in_form']) {
                $has_visible_custom_settings = true;
                break;
            }
        }

        if ($has_visible_custom_settings) {
            $result .= "<li><a onClick='nc.load_dialog(this.href); return false'" .
                    " href='$controller_path&action=show_settings_dialog'>" .
                    "<i class='nc-icon-settings' title='" . htmlspecialchars(NETCAT_MODERATION_BLOCK_SETTINGS, ENT_QUOTES) . "'></i>" .
                    "</a></li>";
        }
    }

    // Вкл-выкл

    $result .= "<li>" .
        "<a onClick='return nc_infoblock_toggle(this)'" .
        "href='" . $toggle_infoblock_path . "'>" .
        "<span style='display:" . ($is_enabled ? 'inline' : 'none') .
        "' class='nc-text-green'>" . NETCAT_MODERATION_OBJ_ON .
        "</span>" .
        "<span style='display:" . ($is_enabled ? 'none' : 'inline') .
        "' class='nc-text-red'>" . NETCAT_MODERATION_OBJ_OFF .
        "</span>
        </a></li>";

    // ···

    $result .= "<li class='nc--dropdown nc-infoblock-toolbar-more'>" .
        "<a href='$hash'><i class='nc-icon-more' title='" . htmlspecialchars(NETCAT_MODERATION_MORE, ENT_QUOTES) . "'></i></a>" .
        "<ul>";

    $infoblocks_in_subdivision = array();
    $current_infoblock_position = null;

    $i = 0;
    foreach ($nc_core->sub_class->get_by_subdivision_id($subdivision_id) as $infoblock) {
        // Пропуск служебных блоков
        if (!$nc_core->component->get_by_id($infoblock['Class_ID'], 'IsAuxiliary')) {
            $infoblocks_in_subdivision[] = $infoblock;
            if ($infoblock['Sub_Class_ID'] == $infoblock_id) {
                $current_infoblock_position = $i;
            }
            $i++;
        }
    }

    $num_infoblocks = count($infoblocks_in_subdivision);
    $last_position = $num_infoblocks - 1;

    // Перемещение блока
    if ($num_infoblocks > 1) {
        if ($current_infoblock_position != 0) {
            $prev_infoblock_id = $infoblocks_in_subdivision[$current_infoblock_position-1]['Sub_Class_ID'];
            $result .= "<li><a href='$hash' onclick='return nc_infoblock_place_before(this,$prev_infoblock_id)'>" .
                "<i class='nc-icon-arrow-up'></i><span class='nc--caption'>" .
                htmlspecialchars(NETCAT_MODERATION_MOVE_UP, ENT_QUOTES) . "</span></a></li>";
        }

        if ($current_infoblock_position != $last_position) {
            $next_infoblock_id = $infoblocks_in_subdivision[$current_infoblock_position+1]['Sub_Class_ID'];
            $result .= "<li><a href='$hash' onclick='return nc_infoblock_place_after(this,$next_infoblock_id)'>" .
                "<i class='nc-icon-arrow-down'></i><span class='nc--caption'>" .
                htmlspecialchars(NETCAT_MODERATION_MOVE_DOWN, ENT_QUOTES) . "</span></a></li>";
        }
    }

    // Скопировать
    $result .= "<li><a onclick='nc_infoblock_buffer_copy($infoblock_id); return false'" .
            " href='$hash'>" .
            "<i class='nc-icon-copy'></i><span class='nc--caption'>" .
            NETCAT_MODERATION_COPY_BLOCK . "</span></a></li>";

    // Удалить
    $result .= "<li class='nc-infoblock-delete'><a onclick='nc.load_dialog(this.href); return false'" .
            " href='$controller_path&action=show_delete_confirm_dialog'>" .
            "<i class='nc-icon-trash'></i><span class='nc--caption'>" .
            NETCAT_MODERATION_DELETE_BLOCK . "</span></a></li>";

    $result .= "</ul></li></ul><div class='nc--clearfix'></div>";

    return $result;
}

function nc_admin_infoblock_insert_toolbar($subdivision_id, $infoblock_id, $position) {
    $nc_core = nc_core::get_object();
    $path = $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH;
    $controller_path = $path . 'action.php?ctrl=admin.infoblock&infoblock_id=' . $infoblock_id;
    $add_infoblock_path = $controller_path . '&action=show_new_infoblock_simple_dialog&subdivision_id=' . $subdivision_id . '&position=' . $position;
    $paste_infoblock_path = $controller_path . '&action=copy&position=' . $position;

    $result = "<div class='nc-infoblock-insert nc-infoblock-insert-$position'>" .
                  "<div class='nc-infoblock-insert-line'></div>" .
                  "<div class='nc-infoblock-insert-buttons'>" .
                      "<a class='nc-infoblock-insert-button-new' href='$add_infoblock_path'" .
                      " title='" . htmlspecialchars(NETCAT_MODERATION_ADD_BLOCK, ENT_QUOTES) . "'" .
                      " onclick='nc.load_dialog(this.href); return false;'>" .
                          "<i class='nc-icon-add'></i>" .
                      "</a>" .
                      "<a class='nc-infoblock-insert-button-paste' href='$paste_infoblock_path'" .
                      " title='" . htmlspecialchars(NETCAT_MODERATION_PASTE_BLOCK, ENT_QUOTES) . "'" .
                      " onclick='nc_infoblock_buffer_paste(this.href); return false;'>" .
                          "<i class='nc-icon-paste-here'></i>" .
                      "</a>" .
                  "</div>" .
              "</div>";

    return $result;
}

function nc_get_AdminButtons_modPerm($classID, $f_RowID, $f_AdminButtons_id, $f_AdminButtons_priority, $f_AdminInterface_user_add, $f_AdminButtons_user_add, $f_AdminInterface_user_change, $f_AdminButtons_user_change, $f_AdminButtons_buttons, $cc, $query_order) {
    $f_AdminButtons = "<li><span class='nc-move-place' " . (nc_Core::get_object()->inside_admin && nc_show_drag_handler($cc, $query_order) ? '' : "style='display: none;'") . " id='message" . $classID . "-" . $f_RowID . "_handler'>
    <i class='nc-icon nc--move'></i></span></li>";
    // $f_AdminButtons.= "<div class='nc_idtab_buttons'>" . $f_AdminButtons_buttons . "</div>";
    $f_AdminButtons .= $f_AdminButtons_buttons;
    // $f_AdminButtons.= "<div class='ncf_row nc_clear'></div>";
    return $f_AdminButtons;
}

function nc_get_AdminButtons_modPerm_else($classID, $f_RowID) {
    $f_AdminButtons = "<div class='nc_idtab_handler' id='message" . $classID . "-" . $f_RowID . "_handler'></div>";
    $f_AdminButtons .= "<div class='nc_idtab_id'>";
    $f_AdminButtons .= "<div class='nc_idtab_messageid error' title='" . NETCAT_MODERATION_ERROR_NORIGHT . "'>" . NETCAT_MODERATION_ERROR_NORIGHT . "</div>";
    $f_AdminButtons .= "</div>";
    $f_AdminButtons .= "<div class='ncf_row nc_clear'></div>";
    return $f_AdminButtons;
}

function nc_get_AdminButtons_suffix() {
    return "</ul><div class='nc--clearfix'></div>";
}

function nc_get_AdminButtons_prefix($f_Checked, $cc) {
    return "<ul class='nc-toolbar nc--left" . ($f_Checked ? "" : " nc--disabled") . "'>";
}


function nc_show_drag_handler($cc, $query_order) {
    $SortBy = nc_Core::get_object()->sub_class->get_by_id($cc, 'SortBy');
    return !$query_order && (!$SortBy || preg_match('/^\s*(a\.)?`?Priority`?(\s+(desc|asc))?\s*$/i', $SortBy));
}

function nc_add_column_aliases($query_select) {
    $columns = array();

    $column_start = 0;
    $bracket_count = 0;
    $opened_quote = false;
    $escape = false;

    for ($i = 0, $strlen = strlen($query_select); $i < $strlen; $i++) {
        $char = $query_select[$i];
        $next_char = ($i === $strlen-1) ? null : $query_select[$i+1];

        if (!$escape && ($char === '"' || $char === "'") && $next_char !== $char) { // unescaped quote symbol
            if (!$opened_quote) {  // opening quote
                $opened_quote = $char;
            }
            elseif ($opened_quote === $char) {  // closing quote
                $opened_quote = false;
            }
        }
        elseif (!$opened_quote) {
            if ($char === "(") { $bracket_count++; }
            elseif ($char === ")") { $bracket_count--; }
            elseif ($char === "," && !$bracket_count) {
                $columns[] = trim(substr($query_select, $column_start, $i - $column_start));
                $column_start = $i + 1;
            }
        }

        $escape = ($char === "\\");
    }

    $columns[] = trim(substr($query_select, $column_start, $i - $column_start));

    foreach ($columns as $i => $column) {
        if (!preg_match('/\S(?:\s+AS)?\s+[`\'"]?\w+[`\'"]?$/i', $column)) {
            $columns[$i] .= " AS `user_column_$i`";
        }

    }

    $result = join(", ", $columns);
    return $result;
}
