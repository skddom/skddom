<?php
/* $Id: search.php 8008 2012-08-23 12:32:10Z vadim $ */

$action = "search";

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -2)).( strstr(__FILE__, "/") ? "/" : "\\" );
@include_once ($NETCAT_FOLDER."vars.inc.php");

require ($INCLUDE_FOLDER.'index.php');

if (!isset($use_multi_sub_class)) {
    // subdivision multisubclass option
    $use_multi_sub_class = $nc_core->subdivision->get_current("UseMultiSubClass");
}

if ($classPreview == ($current_cc["Class_Template_ID"] ? $current_cc["Class_Template_ID"] : $current_cc["Class_ID"])) {
    $magic_gpc = get_magic_quotes_gpc();
    $searchTemplate = $magic_gpc ? stripslashes($_SESSION["PreviewClass"][$classPreview]["SearchTemplate"]) : $_SESSION["PreviewClass"][$classPreview]["SearchTemplate"];
}

require_once ($ADMIN_FOLDER.'admin.inc.php');

ob_start();

$nc_core->page->set_current_metatags($current_sub);

$cc_settings = nc_get_visual_settings($cc);

if ($current_cc['File_Mode']) {
    $file_class = new nc_tpl_component_view($CLASS_TEMPLATE_FOLDER, $db);
    $file_class->load($current_cc['Real_Class_ID'], $current_cc['File_Path'], $current_cc['File_Hash']);
    $nc_parent_field_path = $file_class->get_parent_field_path('SearchTemplate');
    $nc_field_path = $file_class->get_field_path('SearchTemplate');
    $action_exists = filesize($nc_field_path) > 0 ? true : false;
}

$nc_add_block_markup = $nc_core->component->can_add_block_markup($current_cc['Class_Template_ID'] ?: $current_cc['Class_ID']);
if ($nc_add_block_markup) {
    $nc_component_css_class = $nc_core->component->get_css_class_name($current_cc['Class_Template_ID'] ?: $current_cc['Class_ID']);
    $nc_component_css_selector = '.' . str_replace(' ', '.', $nc_component_css_class);
    $nc_block_id = nc_make_block_id("search");

    $nc_core->page->register_component_usage($current_cc['Class_ID'], $current_cc['Class_Template_ID']);
    echo "<div class='tpl-block-search-form ". $nc_component_css_class . "' id='". $nc_block_id ."'>";
}
else {
    $nc_component_css_class = $nc_component_css_selector = $nc_block_id = null;
}

if ($current_cc['File_Mode'] && $action_exists) {
    // check and include component part
    try {
        if ( nc_check_php_file($nc_field_path) ) {
            include $nc_field_path;
        }
    }
    catch (Exception $e) {
        if ( $perm instanceof Permission && $perm->isSubClassAdmin($cc) ) {
            // error message
            echo sprintf(CONTROL_CLASS_CLASSFORM_CHECK_ERROR, CONTROL_CLASS_CLASS_FORMS_SEARCH);
        }
    }
    $nc_parent_field_path = null;
    $nc_field_path = null;
} else if ($searchTemplate) {
    eval(nc_check_eval("echo \"".$searchTemplate."\";"));
} else {
    require ($ROOT_FOLDER.'message_fields.php');

    if ($srchFrm = showSearchForm($fldName, $fldType, $fldDoSearch, $fldFmt)) {

        if (nc_module_check_by_keyword('routing')) {
            $form_action = nc_routing::get_infoblock_path($current_cc['Sub_Class_ID']);
        }
        else {
            $form_action = $SUB_FOLDER .
                           $current_sub['Hidden_URL'] .
                           $current_cc['EnglishName'] . '.html';
        }

        ?>
    <form action='<?= $form_action ?>' method='get'>
        <input type='hidden' name='action' value='index'>
        <?= $srchFrm ?>
        <input value='<?= NETCAT_SEARCH_FIND_IT ?>' type='submit'>
    </form>
    <?php
    } else {
        nc_print_status(NETCAT_SEARCH_ERROR, 'error');
    }
}

if ($nc_add_block_markup) {
    echo '</div>';
}


$cc_search = $cc;

if ($cc_array && $use_multi_sub_class && !$inside_admin) {
    foreach ($cc_array as $cc) {
        if (( $cc && $cc_search != $cc ) || $user_table_mode) {
            // поскольку компонентов несколько, то current_cc нужно переопределить
            $current_cc = $nc_core->sub_class->set_current_by_id($cc);
            echo s_list_class($sub, $cc, $nc_core->url->get_parsed_url('query').($date ? "&date=".$date : "")."&isMainContent=1&isSubClassArray=1");
        }
    }
    // current_cc нужно вернуть в первоначальное состояние, чтобы использовать в футере макета
    $current_cc = $nc_core->sub_class->set_current_by_id($cc_search);
}

$nc_result_msg = ob_get_clean();
$nc_core->page->is_processing_template_now();

if ($File_Mode) {
    require_once $INCLUDE_FOLDER.'index_fs.inc.php';
    if (!$templatePreview) {
        echo $template_header;
        echo $nc_result_msg;
        echo $template_footer;
    } else {
        eval('?>'.$template_header);
        echo $nc_result_msg;
        eval('?>'.$template_footer);
    }
} else {
    eval(nc_check_eval("echo \"".$template_header."\";"));
    echo $nc_result_msg;
    eval(nc_check_eval("echo \"".$template_footer."\";"));
}

// выполнить необходимую обработку кода страницы и отдать результат пользователю:
$nc_core->output_page_buffer();
