<?php

// set gzip handler
ob_start("ob_gzhandler");

define("NC_ADMIN_ASK_PASSWORD", false);

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -3)) . (strstr(__FILE__, "/") ? "/" : "\\");
include_once($NETCAT_FOLDER . "vars.inc.php");
require($ADMIN_FOLDER . "function.inc.php");
$nc_core = nc_Core::get_object();
$nc_core->load('modules');
$File_Mode = +$_REQUEST['fs'];

// Показываем дерево разработчика, если у пользователя есть на это права
if (!$perm->isAccessDevelopment() && !$perm->isGuest()) {
    exit(NETCAT_MODERATION_ERROR_NORIGHT);
}

if (strpos($node, "-") ===  false) {
    $node_type = $node;
    $node_id = 0;
} else {
    list($node_type, $node_id) = explode("-", $node);
}

if ($node_type != 'group' && $node_type != 'widgetgroup')
    $node_id = (int)$node_id;

if ($_GET['node_action'] != 'root') {
    list($node_type, $node_id) = explode("-", $_GET['node_action']);
}

if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}

$fs_suffix = $_REQUEST['fs'] ? '_fs' : '';

$field_types = array(
    1 => 'field-string',
    2 => 'field-int',
    3 => 'field-text',
    4 => 'field-select',
    5 => 'field-bool',
    6 => 'field-file',
    7 => 'field-float',
    8 => 'field-date',
    9 => 'field-link',
    10 => 'field-multiselect',
    11 => 'field-multifile'
);

$input = nc_core('input');
if ($input->fetch_get('action') == 'search') {
    $term = $db->escape($input->fetch_get('term'));

    switch ($node) {
        case 'dataclass.list':
            $sql = "SELECT `Class_ID` FROM `Class` WHERE `System_Table_ID` = 0 AND `File_Mode` = {$File_Mode} AND `ClassTemplate` = 0 AND `Class_Name` LIKE '%{$term}%'";
            $prefix = 'dataclass-';
            break;
        case 'templates':
            $sql = "SELECT `Template_ID` FROM `Template` WHERE `File_Mode` = {$File_Mode} AND `Description` LIKE '%{$term}%'";
            $prefix = 'template-';
            break;
        case 'widgetclass.list':
            $sql = "SELECT `Widget_Class_ID` FROM `Widget_Class` WHERE `File_Mode` = {$File_Mode} AND `Name` LIKE '%{$term}%'";
            $prefix = 'widgetclass-';
            break;
        case 'classificator.list':
            $sql = "SELECT `Classificator_ID` FROM `Classificator` WHERE `Classificator_Name` LIKE '%{$term}%'";
            $prefix = 'classificator-';
            break;
        default:
            exit();
    }

    $result = array();

    foreach ((array)$db->get_col($sql) as $id) {
        $result[] = $prefix . $id;
    }

    print json_encode($result);
    exit;
}

// Открывать дерево на заданном узле для раздела шаблонов
$allowed_node_types = array('group','dataclass','field','widgetclass','template_partials', 'template_partial');

if ($_GET['action'] == 'get_path' && in_array($node_type, $allowed_node_types) && $node_id) {
    $ret = array();

    switch ($node_type) {
        case 'dataclass':
            $row = $db->get_row("SELECT MD5(`Class_Group`) AS `Class_Group_md5` FROM `Class` WHERE `Class_ID` = '" . $node_id . "' AND `System_Table_ID` = 0", ARRAY_A);
            $ret[] = "group-" . $row['Class_Group_md5'];
            break;
        case 'widgetclass':
            $row = $db->get_row("SELECT MD5(`Category`) AS `Category_md5` FROM `Widget_Class` WHERE `Widget_Class_ID` = '" . $node_id . "'", ARRAY_A);
            $ret[] = "widgetgroup-" . $row['Category_md5'];
            break;
        case 'field':
            if ($node == 'widgetclass.list') {
                $row = $db->get_row("SELECT `Widget_Class_ID` FROM `Field` WHERE `Field_ID` = '" . $node_id . "'", ARRAY_A);
                $ret[] = "widgetclass-" . $row['Widget_Class_ID'];
                $row = $db->get_row("SELECT MD5(`Category`) AS `Category_md5` FROM `Widget_Class` WHERE `Widget_Class_ID` = '" . $row['Widget_Class_ID'] . "'", ARRAY_A);
                $ret[] = "widgetgroup-" . $row['Category_md5'];
            } else {
                $row = $db->get_row("SELECT `Class_ID` FROM `Field` WHERE `Field_ID` = '" . $node_id . "'", ARRAY_A);
                $ret[] = "dataclass-" . $row['Class_ID'];
                $row = $db->get_row("SELECT MD5(`Class_Group`) AS `Class_Group_md5` FROM `Class` WHERE `Class_ID` = '" . $row['Class_ID'] . "' AND `System_Table_ID` = 0", ARRAY_A);
                $ret[] = "group-" . $row['Class_Group_md5'];
            }
            break;
        case 'template_partial':
            $ret[] = 'template_partials-' . $node_id;
        case 'template_partials':

            // if (!is_numeric($node_id)) {
            // }
            $ret[] = 'template-' . $node_id;
            break;
    }

    $ret = array_reverse($ret);
    print "while(1);" . nc_array_json($ret);
    exit;
}

if ($_GET['action'] == 'get_path' && ($node_type == 'classtemplates' || $node_type == 'classtemplate') && $node_id) {

    switch ($node_type) {
        case 'classtemplates':
            $ret[] = "classtemplates-" . $node_id;
            $ret[] = "dataclass-" . $node_id;
            $row = $db->get_row("SELECT MD5(`Class_Group`) AS `Class_Group_md5` FROM `Class` WHERE `Class_ID` = '" . $node_id . "' AND `System_Table_ID` = 0", ARRAY_A);
            $ret[] = "group-" . $row['Class_Group_md5'];
            break;
        case 'classtemplate':
            list($class_template, $system_table_id) = $db->get_row("SELECT `ClassTemplate`, `System_Table_ID` FROM `Class` WHERE `Class_ID` = '" . $node_id . "'", ARRAY_N);
            $ret[] = "classtemplates-" . $class_template;
            if ($system_table_id) {
                $ret[] = "systemclass-" . $system_table_id;
            } else {
                $ret[] = "dataclass-" . $class_template;
                $row = $db->get_row("SELECT MD5(`Class_Group`) AS `Class_Group_md5` FROM `Class` WHERE `Class_ID` = '" . $class_template . "' AND `System_Table_ID` = 0", ARRAY_A);
                $ret[] = "group-" . $row['Class_Group_md5'];
            }
            break;
    }

    $ret = array_reverse($ret);
    print "while(1);" . nc_array_json($ret);
    exit;
}

// Открывать дерево на заданном узле для раздела списков
if ($_GET['action'] == 'get_path' && $node_type == 'classificator' && $node_id) {
    $ret = array_reverse((array)$ret);
    print "while(1);" . nc_array_json($ret);
    exit;
}

if ($_GET['action'] == 'get_path' && $node_type == 'template' && $node_id) {
    while ($node_id) {
        $templates = $db->get_results("SELECT `Template_ID`, `Description`, `Parent_Template_ID`
            FROM `Template`
            WHERE `Template_ID` = '" . $node_id . "'
            ORDER BY `Priority`, `Template_ID`", ARRAY_A);
        if (!empty($templates)):
            foreach ((array)$templates as $template) {
                $hasChildren = $db->get_var("SELECT COUNT(*) FROM `Template` WHERE `Parent_Template_ID` = '" . $template['Template_ID'] . "'");
                $ret[] = "template-" . $template['Template_ID'];
                $node_id = $template['Parent_Template_ID'];
            }
        else:
            $node_id = false;
        endif;
    }
    if (is_array($ret)):
        $ret = array_reverse($ret);
        array_pop($ret);
        print "while(1);" . nc_array_json($ret);
    endif;
    exit;
}

if ($_GET['action'] == 'get_path' && ($node_type == 'systemclass' || $node_type == 'systemfield') && $node_id) {
    if ($node_type == 'systemfield') {
        $row = $db->get_var("SELECT `System_Table_ID` FROM `Field` WHERE `Field_ID` = '" . $node_id . "'");
        $ret[] = "systemclass-" . $row['System_Table_ID'];
    }
    $ret = array_reverse($ret);
    print "while(1);" . nc_array_json($ret);
    exit;
}

$ret = array();
$ret_dev = array();
$ret_groups = array();
$ret_widgetgroups = array();
$ret_classes = array();
$ret_widgetclasses = array();
$ret_class_group = array();
$ret_class_templates = array();
$ret_fields = array();
$ret_widgetfields = array();
$ret_classificators = array();
$ret_system_class = array();
$ret_system_fields = array();
$ret_templates = array();
$ret_redirects = array();

// Строим дерево шаблонов
if ($node_type == 'root') {
    exit;
    //For each buttons make checking - access or no
    $classes_buttons[] = array(
        "image" => "icon_class_import",
        "label" => CONTROL_CLASS_IMPORT,
        "href" => "dataclass.import()"
    );
    $classes_buttons[] = array(
        "image" => "icon_class_add",
        "label" => CONTROL_CLASS_ADD,
        "href" => "dataclass.add()"
    );

    $widgetclasses_buttons[] = array(
        "image" => "icon_widgetclass_import",
        "label" => CONTROL_WIDGETCLASS_IMPORT,
        "href" => "widgetclass.import()"
    );

    $widgetclasses_buttons[] = array(
        "image" => "icon_widgetclass_add",
        "label" => CONTROL_WIDGETCLASS_ADD,
        "href" => "widgetclass.add()"
    );

    // Menu class
    if ($perm->isSupervisor() || $perm->isGuest()) {
        $ret_dev[] = array("nodeId" => "dataclass.list",
            "name" => SECTION_INDEX_DEV_CLASSES,
            "href" => "#dataclass.list",
            "image" => 'icon_classes',
            "hasChildren" => true,
            "dragEnabled" => false);
    }

    // Menu system tables
    if ($perm->isSupervisor() || $perm->isGuest()) {
        $ret_dev[] = array("nodeId" => "systemclass.list",
            "name" => SECTION_SECTIONS_OPTIONS_SYSTEM,
            "href" => "#systemclass.list",
            "sprite" => 'dev-system-tables' . ($File_Mode ? '' : '-v4'),
            "hasChildren" => true,
            "dragEnabled" => false);
    }

    // Menu template
    if ($perm->isSupervisor() || $perm->isGuest()) {
        $templates_buttons[] = array(
            "image" => "nc-icon nc--dev-templates-add nc--hovered",
            "label" => CONTROL_TEMPLATE_TEPL_CREATE,
            "href" => "template.add(0)"
        );
        $ret_dev[] = array(
            "nodeId" => "templates",
            "name" => SECTION_INDEX_DEV_TEMPLATES,
            "href" => "#template.list",
            "image" => 'dev-templates' . ($File_Mode ? '' : '-v4'),
            "hasChildren" => true,
            "dragEnabled" => false
        );
    }

    // Menu widget-class
    if ($perm->isSupervisor() || $perm->isGuest()) {
        $ret_dev[] = array("nodeId" => "widgetclass.list",
            "name" => SECTION_INDEX_DEV_WIDGET,
            "href" => "#widgetclass.list",
            "image" => 'icon_widgetclasses',
            "hasChildren" => true,
            "dragEnabled" => false);
    }

    // Menu classificator
    if ($perm->isAccess(NC_PERM_CLASSIFICATOR, NC_PERM_ACTION_LIST, 0, 0)) {
        if ($perm->isAccess(NC_PERM_CLASSIFICATOR, NC_PERM_ACTION_ADD, 0, 0)) {
            $classificators_buttons[] = array(
                "image" => "icon_classificator_import",
                "label" => CLASSIFICATORS_IMPORT_HEADER,
                "href" => "classificator.import()"
            );
            $classificators_buttons[] = array(
                "image" => "icon_classificator_add",
                "label" => CONTENT_CLASSIFICATORS_ADDLIST,
                "href" => "classificator.add()"
            );
        }
        $ret_dev[] = array(
            "nodeId" => "classificator.list",
            "name" => SECTION_CONTROL_CONTENT_CLASSIFICATOR,
            "href" => "#classificator.list",
            "image" => 'icon_classificators',
            "hasChildren" => true,
            "dragEnabled" => false
        );
    }
} // Дерево шаблонов данных
elseif ($node_type == 'dataclass.list') {
    // Выборка групп шаблонов
    $SQL = "SELECT `Class_Group`,
                   MD5(`Class_Group`) AS `Class_Group_md5`
                FROM `Class`
                    WHERE `System_Table_ID` = 0
                      AND `ClassTemplate` = 0
                      AND `File_Mode` = $File_Mode
                        GROUP BY `Class_Group`
                            ORDER BY `Class_Group`";

    $class_groups = $db->get_results($SQL, ARRAY_A);

    foreach ((array)$class_groups as $class_group) {
        $classgroup_buttons = array();
        $classgroup_buttons[] = nc_get_array_2json_button(
            CONTROL_CLASS_ADD,
            "dataclass$fs_suffix.add(" . $class_group['Class_Group_md5'] . ")",
            "nc-icon nc--dev-components-add nc--hovered"
        );

        $ret_groups[] = array(
            "nodeId" => "group-" . $class_group['Class_Group_md5'],
            "name" => $class_group["Class_Group"] ? $class_group["Class_Group"] : CONTROL_CLASS_CLASS_NO_GROUP,
            "href" => "#classgroup.edit(" . $class_group['Class_Group_md5'] . ")",
            "sprite" => 'dev-components' . ($File_Mode ? '' : '-v4'),
            "acceptDropFn" => "treeClassAcceptDrop",
            "onDropFn" => "treeClassOnDrop",
            "hasChildren" => true,
            "dragEnabled" => true,
            "buttons" => $classgroup_buttons
        );
    }
} elseif ($node_type == 'group' && $node_id) {
    // Выборка шаблонов определенной группы
    $classes = $db->get_results("
        SELECT `Class_ID`,
               `Class_Name`,
               `ClassTemplate`
            FROM `Class`
                WHERE MD5(`Class_Group`) = '" . $node_id . "'
                  AND `File_Mode` = " . +$_REQUEST['fs'] . "
                  AND `System_Table_ID` = 0
                    ORDER BY `Class_Group`,
                             `Priority`, `Class_ID`", ARRAY_A);

    foreach ((array)$classes as $class) {
        // skip component templates
        if ($class['ClassTemplate']) continue;
        // count component fields
        $hasChildren = $db->get_var("SELECT COUNT(`Field_ID`) FROM `Field`
      WHERE `Class_ID` = '" . $class['Class_ID'] . "'");
        // count component templates
        if (!$hasChildren) {
            $hasChildren = $db->get_var("SELECT COUNT(`Class_ID`) FROM `Class`
        WHERE `ClassTemplate` = '" . $class['Class_ID'] . "'");
        }

        $class_buttons = array();

        $class_buttons[] = nc_get_array_2json_button(
            CONTROL_FIELD_LIST_ADD,
            "field$fs_suffix.add(" . $class['Class_ID'] . ")",
            "nc-icon nc--file-add nc--hovered");

        $class_buttons[] = nc_get_array_2json_button(
            CONTROL_CLASS_DELETE,
            "dataclass$fs_suffix.delete(" . $class['Class_ID'] . ")",
            "nc-icon nc--remove nc--hovered");

        $ret_classes[] = array(
            "nodeId" => "dataclass-" . $class['Class_ID'],
            "name" => $class["Class_ID"] . ". " . $class["Class_Name"],
            "href" => "#dataclass.edit(" . $class['Class_ID'] . ")",
            "sprite" => 'dev-components' . ($File_Mode ? '' : '-v4'),
            "acceptDropFn" => "treeClassAcceptDrop",
            "onDropFn" => "treeClassOnDrop",
            "hasChildren" => $hasChildren,
            "dragEnabled" => true,
            "buttons" => $class_buttons
        );
    }
} elseif ($node_type == 'dataclass' && $node_id) {
    // Выборка полей определенного шаблона
    $fields = $db->get_results("SELECT `Field_ID`, `Field_Name`, `TypeOfData_ID`, `Description`, `NotNull` FROM `Field`
    WHERE `Class_ID` = '" . $node_id . "' AND `System_Table_ID` = 0
    ORDER BY `Priority`", ARRAY_A);

    foreach ((array)$fields as $field) {
        $field_buttons = array();
        $field_buttons[] = nc_get_array_2json_button(
            CONTROL_FIELD_LIST_DELETE,
            "field$fs_suffix.delete(" . $node_id . "," . $field['Field_ID'] . ")",
            "nc-icon nc--remove nc--hovered");

        $ret_fields[] = array(
            "nodeId" => "field-" . $field['Field_ID'],
            "name" => $field["Field_ID"] . ". " . $field["Field_Name"],
            "title" => $field["Description"],
            "href" => "#field.edit(" . $field['Field_ID'] . ")",
            "sprite" => $field_types[$field["TypeOfData_ID"]] . ($field['NotNull'] ? ' nc--required' : ''),
            "acceptDropFn" => "treeFieldAcceptDrop",
            "onDropFn" => "treeFieldOnDrop",
            "hasChildren" => false,
            "dragEnabled" => true,
            "buttons" => $field_buttons
        );
    }

    $hasTemplates = $db->get_var("SELECT COUNT(`Class_ID`) FROM `Class` WHERE `ClassTemplate` = '" . $node_id . "'");

    if ($hasTemplates) {
        $class_template_buttons = array();
        $class_template_buttons[] = nc_get_array_2json_button(
            CONTROL_CLASS_CLASS_TEMPLATE_ADD,
            "classtemplate$fs_suffix.add(" . $node_id . ")",
            "nc-icon nc--file-add nc--hovered");

        $ret_class_templates[] = array(
            "nodeId" => "classtemplates-" . $node_id,
            "name" => CONTROL_CLASS_CLASS_TEMPLATES,
            "href" => "#classtemplates.edit(" . $node_id . ")",
            "sprite" => 'dev-templates' . ($File_Mode ? '' : '-v4'),
            "acceptDropFn" => "treeClassAcceptDrop",
            "onDropFn" => "treeClassOnDrop",
            "hasChildren" => $hasTemplates,
            "dragEnabled" => false,
            "buttons" => $class_template_buttons);
    }
} // Список шаблонов компонента
elseif ($node_type == 'classtemplates' && $node_id) {
    // get component templates
    $class_templates = $db->get_results("SELECT `Class_ID`, `Class_Name` FROM `Class`
    WHERE `ClassTemplate` = '" . $node_id . "'
        ORDER BY `Priority`, `Class_ID`", ARRAY_A);

    foreach ((array)$class_templates as $class_template) {
        $class_templates_buttons = array();
        $class_templates_buttons[] = nc_get_array_2json_button(
            CONTROL_CLASS_DELETE,
            "classtemplate$fs_suffix.delete(" . $class_template['Class_ID'] . ")",
            "nc-icon nc--remove nc--hovered");

        $ret_class_templates[] = array(
            "nodeId" => "classtemplate-" . $class_template['Class_ID'],
            "name" => $class_template["Class_ID"] . ". " . $class_template["Class_Name"],
            "href" => "#classtemplate.edit(" . $class_template['Class_ID'] . ")",
            "sprite" => 'dev-templates' . ($File_Mode ? '' : '-v4'),
            "acceptDropFn" => "treeFieldAcceptDrop",
            "onDropFn" => "treeFieldOnDrop",
            "hasChildren" => false,
            "dragEnabled" => false,
            "buttons" => $class_templates_buttons
        );
    }
} // Дерево системных таблиц
elseif ($node_type == 'systemclass.list') {
    // Выборка системных таблиц
    $system_classes = $db->get_results("SELECT a.`System_Table_ID`, a.`System_Table_Rus_Name`, b.`Class_ID`,
    IF(b.`AddTemplate` <> '' OR b.`AddCond` <> '' OR b.`AddActionTemplate` <> '', 1, 0) AS IsAdd,
    IF(b.`EditTemplate` <> '' OR b.`EditCond` <> '' OR b.`EditActionTemplate` <> '' OR b.`CheckActionTemplate` <> '' OR b.`DeleteActionTemplate` <> '', 1, 0) AS IsEdit,
    IF(b.`SearchTemplate` <> '' OR b.`FullSearchTemplate` <> '', 1, 0) AS IsSearch,
    IF(b.`SubscribeTemplate` <> '' OR b.`SubscribeCond` <> '', 1, 0) AS IsSubscribe
    FROM `System_Table` AS a
    LEFT JOIN `Class` AS b ON (a.`System_Table_ID` = b.`System_Table_ID` AND b.ClassTemplate = 0 )
    " . ($nc_core->modules->get_by_keyword('auth', 0) ? "WHERE IF(b.`System_Table_ID` = 3, (b.`File_Mode` = " . +$_REQUEST['fs'] . ") , 1)" : "") .
        "GROUP BY a.`System_Table_ID` ORDER BY a.`System_Table_ID`", ARRAY_A);

    foreach ((array)$system_classes as $system_class) {
        if (!+$_REQUEST['fs'] && $system_class["System_Table_ID"] != 3) {
            continue;
        }
        $hasChildren = $db->get_var("SELECT COUNT(`Field_ID`) FROM `Field`
      WHERE `System_Table_ID` = '" . $system_class['System_Table_ID'] . "'");
        $system_class_buttons = array();
        $system_class_buttons[] = nc_get_array_2json_button(
            CONTROL_FIELD_LIST_ADD,
            "systemfield$fs_suffix.add(" . $system_class['System_Table_ID'] . ")",
            "nc-icon nc--file-add nc--hovered");

        if ($system_class["Class_ID"] && $nc_core->modules->get_by_keyword('auth', 0)) {
            $href = "#systemclass.edit(" . $system_class['System_Table_ID'] . ")";
        } else {
            $href = "#systemclass.fields(" . $system_class['System_Table_ID'] . ")";
        }
        $ret_system_class[] = array(
            "nodeId" => "systemclass-" . $system_class['System_Table_ID'],
            "name" => $system_class["System_Table_ID"] . ". " . constant($system_class["System_Table_Rus_Name"]),
            "href" => $href,
            "sprite" => 'dev-system-tables' . ($File_Mode ? '' : '-v4'),
            "hasChildren" => $hasChildren,
            "dragEnabled" => false,
            "buttons" => $system_class_buttons
        );
    }
} elseif ($node_type == 'systemclass' && $node_id) {
    // Выборка полей определенного шаблона
    $system_fields = $db->get_results("SELECT field.`Field_ID`, field.`Field_Name`, field.`TypeOfData_ID`, field.`Description`, field.`NotNull` FROM `Field` AS field
    LEFT JOIN `Classificator_TypeOfData` AS type ON type.`TypeOfData_ID` = field.`TypeOfData_ID`
    WHERE field.`System_Table_ID` = '" . $node_id . "'
    ORDER BY field.`Priority`", ARRAY_A);

    foreach ((array)$system_fields as $system_field) {
        if ($system_field["TypeOfData_ID"] == 11 && $node_id != 3 && !($nc_core->modules->get_by_keyword('auth') && nc_auth_openid_possibility()))
            continue;
        $system_field_buttons = array();
        $system_field_buttons[] = nc_get_array_2json_button(
            CONTROL_FIELD_LIST_DELETE,
            "systemfield$fs_suffix.delete(" . $node_id . "," . $system_field['Field_ID'] . ")",
            "nc-icon nc--remove nc--hovered");

        $ret_system_fields[] = array(
            "nodeId" => "systemfield-" . $system_field['Field_ID'],
            "name" => $system_field["Field_ID"] . ". " . $system_field["Field_Name"],
            "href" => "#systemfield.edit(" . $system_field['Field_ID'] . ")",
            "title" => $system_field["Description"],
            "sprite" => $field_types[$system_field["TypeOfData_ID"]] . ($system_field["NotNull"] ? ' nc--required' : ''),
            "acceptDropFn" => "treeSystemFieldAcceptDrop",
            "onDropFn" => "treeSystemFieldOnDrop",
            "hasChildren" => false,
            "dragEnabled" => true,
            "buttons" => $system_field_buttons
        );
    }

    // count component templates
    $hasTemplates = 0;
    if ($node_id == 3 && $nc_core->modules->get_by_keyword('auth')) {
        $hasTemplates = $db->get_var("SELECT COUNT(`Class_ID`) FROM `Class` WHERE `ClassTemplate` > 0 AND `System_Table_ID` = 3 AND File_Mode = " . +$_REQUEST['fs']);
        $user_class_id = $db->get_var("SELECT `Class_ID` FROM `Class` WHERE `ClassTemplate` = 0 AND `System_Table_ID` = 3 AND File_Mode = " . +$_REQUEST['fs']);
    }

    if ($hasTemplates) {
        $class_template_buttons = array();
        $class_template_buttons[] = nc_get_array_2json_button(
            CONTROL_CLASS_CLASS_TEMPLATE_ADD,
            "classtemplate$fs_suffix.add(" . $user_class_id . ")",
            "nc-icon nc--file-add nc--hovered");

        $ret_class_templates[] = array(
            "nodeId" => "classtemplates-" . $user_class_id,
            "name" => CONTROL_CLASS_CLASS_TEMPLATES,
            "href" => "#classtemplates.edit(" . $user_class_id . ")",
            "sprite" => 'dev-templates' . ($File_Mode ? '' : '-v4'),
            "acceptDropFn" => "treeClassAcceptDrop",
            "onDropFn" => "treeClassOnDrop",
            "hasChildren" => $hasTemplates,
            "dragEnabled" => false,
            "buttons" => $class_template_buttons
        );
    }
} // Дерево макетов
elseif (($node_type == 'template' && $node_id) || ($node_type == 'templates')) {
    // Получение дерева макетов
    if (!$node_id) $node_id = 0;
    $tamplate_table = nc_db_table::make('Template');

    $templates = $tamplate_table->select('`Template_ID`, `Description`')
        ->where('Parent_Template_ID', $node_id)->where('File_Mode', $File_Mode)
        ->order_by('Priority')->order_by('Template_ID')
        ->index_by_id()->get_result();

    $childrens_count = $tamplate_table->select('COUNT(*) as total, Parent_Template_ID')
        ->where_in('Parent_Template_ID', array_keys($templates))
        ->group_by('Parent_Template_ID')
        ->get_list('Parent_Template_ID', 'total');

    // Представления макета дизайна
    if ($File_Mode && $node_id) {
        $is_root_template = !$tamplate_table->where_id($node_id)->get_value('Parent_Template_ID');

        if ($is_root_template) {
            $ret_templates[] = array(
                "nodeId"          => "template_partials-{$node_id}",
                "name"            => CONTROL_TEMPLATE_PARTIALS,
                "href"            => "#template.partials_list({$node_id})",
                "sprite"          => 'dev-com-templates',
                "hasChildren"     => (bool)$nc_core->template->has_partial($node_id),
                // "dragEnabled"  => true,
                // "acceptDropFn" => "templateAcceptDrop",
                // "onDropFn"     => "templateOnDrop",
                "buttons"         => array(
                    nc_get_array_2json_button(
                        CONTROL_TEMPLATE_PARTIALS_ADD,
                        "template{$fs_suffix}.partials_add({$node_id})",
                        "nc-icon nc--dev-templates-add nc--hovered"
                    )
                )
            );
        }
    }


    foreach ((array)$templates as $id => $template) {
        $template_buttons = array();

        $template_buttons[] = nc_get_array_2json_button(
            CONTROL_TEMPLATE_TEPL_CREATE,
            "template{$fs_suffix}.add({$id})",
            "nc-icon nc--dev-templates-add nc--hovered");

        $template_buttons[] = nc_get_array_2json_button(
            CONTROL_TEMPLATE_DELETE,
            "template{$fs_suffix}.delete({$id})",
            "nc-icon nc--remove nc--hovered");

        // Для корневых макетов v5 всегда показывать "+", т.к. они имеют partials
        if ($node_id == 0 && $File_Mode) {
            $has_children = true;
        }
        else {
            $has_children = !empty($childrens_count[$id]);
        }

        $ret_templates[] = array(
            "nodeId"       => "template-{$id}",
            "name"         => "{$id}. {$template['Description']}",
            "href"         => "#template.edit({$id})",
            "sprite"       => 'dev-templates' . ($File_Mode ? '' : '-v4'),
            "hasChildren"  => $has_children,
            "dragEnabled"  => true,
            "acceptDropFn" => "templateAcceptDrop",
            "onDropFn"     => "templateOnDrop",
            "buttons"      => $template_buttons
        );
    }
} // Врезки (дополнительные шаблоны, partials) макета дизайна
elseif ($node_type == 'template_partials' && $node_id) {
    $template_partials = $nc_core->template->get_partials_data($node_id);

    foreach ($template_partials as $partial => $partial_data) {
        $ret_templates[] = array(
            "nodeId" => "template_partial-{$node_id}-{$partial}",
            "name" => $partial_data['Description'] ? "$partial_data[Description] ($partial)": $partial,
            "href" => "#template.partials_edit({$node_id}, {$partial})",
            "sprite" => 'dev-com-templates',
            "buttons" => array(
                nc_get_array_2json_button(
                    CONTROL_TEMPLATE_PARTIALS_REMOVE,
                    "template{$fs_suffix}.partials_remove({$node_id}, {$partial})",
                    "nc-icon nc--remove nc--hovered"
                )
            )
        );
    }
} elseif ($node_type == 'widgetclass.list') {
    $SQL = "SELECT `Category`,
                   MD5(`Category`) as `Category_md5`
                FROM `Widget_Class`
                    WHERE File_Mode = $File_Mode
                        GROUP BY `Category`
                            ORDER BY `Category`";
    $widgetclass_groups = $db->get_results($SQL, ARRAY_A);

    foreach ((array)$widgetclass_groups as $widgetclass_group) {
        $widgetclassgroup_buttons = array();
        $widgetclassgroup_buttons[] = nc_get_array_2json_button(
            CONTROL_WIDGETCLASS_ADD,
            "widgetclass$fs_suffix.add(" . $widgetclass_group['Category_md5'] . ")",
            "nc-icon nc--dev-com-widgets-add nc--hovered");
        $ret_widgetgroups[] = array(
            "nodeId" => "widgetgroup-" . $widgetclass_group['Category_md5'],
            "name" => $widgetclass_group["Category"],
            "href" => "#widgetgroup.edit(" . $widgetclass_group['Category_md5'] . ")",
            "sprite" => 'dev-com-widgets' . ($File_Mode ? '' : '-v4'),
            "acceptDropFn" => "treeClassAcceptDrop",
            "onDropFn" => "treeClassOnDrop",
            "hasChildren" => true,
            "dragEnabled" => true,
            "buttons" => $widgetclassgroup_buttons
        );
    }
} elseif ($node_type == 'widgetgroup' && $node_id) {
    $widgetclasses = $db->get_results("
        SELECT `Widget_Class_ID`,
               `Name`,
               `Template`
            FROM `Widget_Class`
                WHERE MD5(`Category`) = '" . $node_id . "'
                  AND File_Mode = " . +$_REQUEST['fs'] . "
                    ORDER BY `Category`,
                             `Widget_Class_ID`", ARRAY_A);

    foreach ((array)$widgetclasses as $widgetclass) {
        $hasChildren = $db->get_var("SELECT COUNT(`Field_ID`) FROM `Field`
      WHERE `Widget_Class_ID` = '" . $widgetclass['Widget_Class_ID'] . "'");
        $widgetclass_buttons = array();
        $widgetclass_buttons[] = nc_get_array_2json_button(
            CONTROL_FIELD_LIST_ADD,
            "widgetfield$fs_suffix.add(" . $widgetclass['Widget_Class_ID'] . ")",
            "nc-icon nc--file-add nc--hovered");

        $widgetclass_buttons[] = nc_get_array_2json_button(
            CONTROL_CLASS_DELETE,
            "widgetclass$fs_suffix.drop(" . $widgetclass['Widget_Class_ID'] . ", 1)",
            "nc-icon nc--remove nc--hovered");

        $ret_widgetclasses[] = array(
            "nodeId" => "widgetclass-" . $widgetclass['Widget_Class_ID'],
            "name" => $widgetclass["Widget_Class_ID"] . ". " . $widgetclass["Name"],
            "href" => "#widgetclass.edit(" . $widgetclass['Widget_Class_ID'] . ")",
            "sprite" => 'dev-com-widgets' . ($File_Mode ? '' : '-v4'),
            "acceptDropFn" => "treeClassAcceptDrop",
            "onDropFn" => "treeClassOnDrop",
            "hasChildren" => $hasChildren,
            "dragEnabled" => true,
            "buttons" => $widgetclass_buttons
        );
    }
} elseif ($node_type == 'widgetclass' && $node_id) {
    $fields = $db->get_results("SELECT `Field_ID`, `Field_Name`, `TypeOfData_ID`, `Description`, `NotNull` FROM `Field`
    WHERE `Widget_Class_ID` = '" . $node_id . "'
    ORDER BY `Priority`", ARRAY_A);
    foreach ((array)$fields as $field) {
        if ($field["TypeOfData_ID"] == 11 && !($nc_core->modules->get_by_keyword('auth') && nc_auth_openid_possibility()))
            continue;
        $widgetfield_buttons = array();
        $widgetfield_buttons[] = nc_get_array_2json_button(
            CONTROL_FIELD_LIST_DELETE,
            "widgetfield$fs_suffix.delete(" . $node_id . "," . $field['Field_ID'] . ")",
            "nc-icon nc--remove nc--hovered");

        $ret_widgetfields[] = array(
            "nodeId" => "field-" . $field['Field_ID'],
            "name" => $field["Field_ID"] . ". " . $field["Field_Name"],
            "href" => "#widgetfield.edit(" . $field['Field_ID'] . ")",
            "title" => $field["Description"],
            "sprite" => $field_types[$field["TypeOfData_ID"]] . ($field["NotNull"] ? ' nc--required' : ''),
            "acceptDropFn" => "treeFieldAcceptDrop",
            "onDropFn" => "treeFieldOnDrop",
            "hasChildren" => false,
            "dragEnabled" => true,
            "buttons" => $widgetfield_buttons
        );
    }
} // Дерево списков
elseif ($node_type == 'classificator.list') {
    // получение дерева списков
    $classificators = $db->get_results("SELECT `Classificator_ID`, `Classificator_Name`, `System` FROM `Classificator`
    ORDER BY `Classificator_ID`", ARRAY_A);

    $admin_cl = $perm->isAccess(NC_PERM_CLASSIFICATOR, NC_PERM_ACTION_DEL, 0, 0);

    foreach ((array)$classificators as $classificator) {
        $c_id = $classificator['Classificator_ID']; //for short
        // Проверка на право
        if (!$classificator['System'] && !$perm->isAccess(NC_PERM_CLASSIFICATOR, NC_PERM_ACTION_VIEW, $c_id))
            continue;
        //Системные списки показываем только при наличии соответствующих прав
        if ($classificator['System'] && !$perm->isDirectAccessClassificator(NC_PERM_ACTION_VIEW, $c_id))
            continue;

        $classificator_buttons = array();
        // Кнопка удалить только для админа всех списков, при условии что список не системный
        if ($admin_cl && !$classificator['System']) {
            $classificator_buttons[] = nc_get_array_2json_button(
                CONTENT_CLASSIFICATORS_LIST_DELETE,
                "classificator.delete(" . $c_id . ")",
                "nc-icon nc--remove nc--hovered");
        }
        $ret_classificators[] = array(
            "nodeId" => "classificator-" . $c_id,
            "name" => $classificator["Classificator_ID"] . ". " . $classificator['Classificator_Name'],
            "href" => "#classificator.edit(" . $c_id . ")",
            "sprite" => 'dev-classificator',
            "hasChildren" => false,
            "dragEnabled" => false,
            "buttons" => $classificator_buttons
        );
    }
}
elseif ($node_type == 'redirect') {
    $redirect_group_table = nc_db_table::make('Redirect_Group');
    $redirect_groups = $redirect_group_table->select()->as_array()->get_result();

    foreach ($redirect_groups as  $redirect_group) {
        $id = $redirect_group['Redirect_Group_ID'];
        $name = $redirect_group['Name'];

        $redirect_buttons = array();

        $redirect_buttons[] = nc_get_array_2json_button(
            TOOLS_REDIRECT_GROUP_EDIT,
            "redirect.group.edit(" . $id . ")",
            "nc-icon nc--edit nc--hovered");
        if ($id != 1) {
            $redirect_buttons[] = nc_get_array_2json_button(
                TOOLS_REDIRECT_GROUP_DELETE,
                "redirect.delete(" . $id . ")",
                "nc-icon nc--remove nc--hovered");
        }

        $ret_redirects[] = array(
            "nodeId" => "redirect-" . $id,
            "name" => $id . ". " . $name,
            "href" => "#redirect.list(" . $id . ")",
            "sprite" => 'dev-classificator',
            "hasChildren" => false,
            "dragEnabled" => false,
            "buttons" => $redirect_buttons,
        );
    }

    $ret_redirects[] = array(
        "nodeId" => "bottom-add",
        "name" => TOOLS_REDIRECT_GROUP_ADD,
        "href" => "#redirect.group.add",
        "sprite" => 'plus',
        "hasChildren" => false,
        "dragEnabled" => false,
    );
}

$ret = array_merge(
    array_values($ret_dev),
    array_values($ret_groups),
    array_values($ret_widgetgroups),
    array_values($ret_classes),
    array_values($ret_widgetclasses),
    array_values($ret_class_templates),
    array_values($ret_fields),
    array_values($ret_widgetfields),
    array_values($ret_classificators),
    array_values($ret_templates),
    array_values($ret_class_group),
    array_values($ret_system_class),
    array_values($ret_system_fields),
    array_values($ret_redirects)
);

print "while(1);" . nc_array_json($ret);
