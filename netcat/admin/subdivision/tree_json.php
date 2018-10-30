<?php

/* $Id: tree_json.php 8551 2012-12-25 09:33:28Z ewind $ */

ob_start("ob_gzhandler");

define("NC_ADMIN_ASK_PASSWORD", false);
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ADMIN_FOLDER."function.inc.php");

list($node_type, $node_id) = explode("-", $node);
$node_id = (int) $node_id;

// (a) path to the node
// [possible optimization point: output node info instead of ids, thus removing extra 'get path' request]
if ($_GET['action'] == 'get_path' && $node_type == 'sub' && $node_id) {
    $parent_sub_id = $node_id;

    while ($parent_sub_id != 0) {
        $ret[] = "sub-{$parent_sub_id}";
        $row = $db->get_row("SELECT Parent_Sub_ID, Catalogue_ID FROM Subdivision WHERE Subdivision_ID = $parent_sub_id", ARRAY_A);
        if (!$row) exit("[]");
        $parent_sub_id = $row['Parent_Sub_ID'];
    }

    $ret[] = "site-$row[Catalogue_ID]";
    // remove the node itself, as it has not to be expanded
    $ret = array_reverse($ret);
    array_pop($ret);
    print "while(1);".nc_array_json($ret);
    exit;
}

$input = nc_core('input');

if ($input->fetch_get('action') == 'search') {
    $term = $db->escape($input->fetch_get('term'));

    $result = array();

    $sql = "SELECT `Subdivision_ID` FROM `Subdivision` WHERE `Subdivision_Name` LIKE '%{$term}%'";
    foreach((array)$db->get_col($sql) as $id) {
        $result[] = 'sub-' . $id;
    }

    $sql = "SELECT `Catalogue_ID` FROM `Catalogue` WHERE `Catalogue_Name` LIKE '%{$term}%'";
    foreach((array)$db->get_col($sql) as $id) {
        $result[] = 'site-' . $id;
    }

    print json_encode($result);
    exit;
}

// (b) contents of the node

$ret = array();
$ret_sites = array();
$ret_sub = array();
$nc_core = nc_Core::get_object();


// список сайтов
if ($node_type == "root") {

    // получим id всех каталогов, к которому пользователь имеет доступ админа\модер
    // или имеет доступ к его разделам, тоже админ\модер
    // если ф-ция вернет не массив, то значит есть доступ ко всем
    $array_id = $perm->GetAllowSite(MASK_READ, true);

    $sites = $db->get_results("SELECT Catalogue_ID, Catalogue_Name, Domain, Mirrors, Checked, ncMobile, ncResponsive
                               FROM Catalogue
                               WHERE ". ((is_array($array_id) && !$perm->isGuest()) ? "Catalogue_ID IN(" . join(',', (array) $array_id) . ")" : "1"). "
                               ORDER BY Priority", ARRAY_A);

    $found_current_site = false;

    foreach ((array) $sites as $site) {
        $image = 'nc-icon nc--site';
        $image .= $site['ncMobile'] ? '-mobile' : '';
        $image .= $site['ncResponsive'] ? '-adaptive' : '';
        $image .= $site['Checked'] ? '' : ' nc--disabled';
        $scheme = $nc_core->catalogue->get_scheme_by_id($site['Catalogue_ID']);

        $is_site_admin = $perm->isAccess(NC_PERM_ITEM_SITE, NC_PERM_ACTION_ADMIN, $site['Catalogue_ID'], 0);

        $buttons = array();
        $buttons[] = array(
                "label"  => CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_VIEW,
                "action" => "window.open('{$scheme}://".(($site['Domain']) ? $site['Domain'] : $HTTP_HOST).$SUB_FOLDER."');",
                'icon'   => 'arrow-right',
                'sprite' => true
        );

        if ($is_site_admin) {
            $buttons[] = array(
                    "label"  => CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_A_ADDSUBSECTION,
                    "action" => "parent.location.hash = 'subdivision.add(0,$site[Catalogue_ID])'",
                    // 'icon'   => 'icons icon_folder_add'
                    'icon'   => 'folder-add',
                    'sprite' => true
                );
        }

        $ret_sites[] = array("nodeId" => "site-$site[Catalogue_ID]",
                "name"         => $site[Catalogue_ID].". ".strip_tags($site["Catalogue_Name"]),
                "href"         => "#site.map($site[Catalogue_ID])",
                "sprite"       => $image,
                "hasChildren"  => true,
                "acceptDropFn" => "treeSitemapAcceptDrop",
                "onDropFn"     => "treeSitemapOnDrop",
                "dragEnabled"  => $is_site_admin,
                "buttons"      => $buttons,
                "checked"      => $site['Checked'],
                "className"    => "menu_site"
        );

        $domain_name = preg_quote($site['Domain']);
        if ($site['Mirrors']) {
            $site['Mirrors'] = str_replace(array('http://', '/'), '', $site['Mirrors']);
            $domain_name .= "|".nc_preg_replace("/\r\n/", "|", preg_quote($site['Mirrors']));
        }

        if ($domain_name && preg_match("/^(?:$domain_name)$/", $HTTP_HOST)) {
            $ret_sites[sizeof($ret_sites) - 1]["expand"] = true;
            $found_current_site = true;
        }
    }

    if (!$found_current_site) {
        $ret_sites[0]["expand"] = true;
    }
}

// разделы
elseif (($node_type == 'sub' || $node_type == 'site') && $node_id) {

    if ($node_type == 'site') {
        $qry_where = "sub.Parent_Sub_ID=0 AND sub.Catalogue_ID='".$node_id."'";
        $current_site = $node_id;
    } else {
        $qry_where = "sub.Parent_Sub_ID='".$node_id."'";
        $current_site = $nc_core->subdivision->get_by_id($node_id, "Catalogue_ID");
    }

    // Получить разделы, которые пользователь может видеть
    $allow_id = $perm->GetAllowSub($current_site, MASK_ADMIN | MASK_MODERATE | MASK_READ, true, true, true);
    $qry_where .= ( is_array($allow_id) && !$perm->isGuest() ) ? " AND sub.Subdivision_ID IN(".join(',', (array) $allow_id).") " : " AND 1";

    $SQL = "SELECT sub.`Subdivision_ID`,
                   sub.`Subdivision_Name`,
                   sub.`Catalogue_ID`,
                   sub.`ExternalURL`,
                   sub.`Hidden_URL`,
                   sub.`Parent_Sub_ID`,
                   sub.`Checked`,
                   sub.`LabelColor`,
                   catalogue.`Domain`,
                   child.`Subdivision_ID` as hasChildren
                FROM Subdivision AS sub
                  LEFT JOIN `Subdivision` as child ON sub.`Subdivision_ID` = child.`Parent_Sub_ID`
                  LEFT JOIN Catalogue AS catalogue ON catalogue.Catalogue_ID = sub.Catalogue_ID
                    WHERE ".$qry_where."
                        GROUP BY sub.`Subdivision_ID`
                            ORDER BY sub.`Priority`";

    $subdivisions = $db->get_results($SQL, ARRAY_A);
    // получить разделы, кторые пользователь может админить
    $allow_id = $perm->GetAllowSub($current_site, MASK_ADMIN | MASK_READ, false, true, false);
    $qry_where = ( is_array($allow_id) && !$perm->isGuest() ) ? " WHERE Subdivision_ID IN(".join(',', (array) $allow_id).") " : "";
    $sub_admin = (array) $db->get_col("SELECT `Subdivision_ID` FROM `Subdivision` ".$qry_where);

    $is_site_admin = $perm->isAccess(NC_PERM_ITEM_SITE, NC_PERM_ACTION_ADMIN, $site['Catalogue_ID'], 0);

    foreach ((array) $subdivisions as $sub) {
        $is_subdivision_admin = in_array($sub['Subdivision_ID'], $sub_admin);

        $buttons = array();

        /*if ($is_subdivision_admin) {
            $buttons[] = array(
                'label' => TOOLS_COPYSUB_COPY_SUB_LOWER,
                'action' => "parent.location.hash = 'tools.copy(sub, ".$sub['Catalogue_ID'].",".$sub['Subdivision_ID'].")'",
                'icon'   => 'copy',
                'sprite' => true
            );
        }*/

        $buttons[] = array(
            "label"  => CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_VIEW,
            "action" => "window.open('" . nc_folder_url($sub['Subdivision_ID']) . "');",
            'icon'   => 'arrow-right',
            'sprite' => true
        );

        if ($is_subdivision_admin) {
            $buttons[] = array(
                "label"  => CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_A_ADDSUBSECTION,
                "action" => "parent.location.hash = 'subdivision.add(".$sub['Subdivision_ID'].")'",
                'icon'   => 'folder-add',
                'sprite' => true
            );

            $buttons[] = array(
                "label"  => CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_KILL,
                "action" => "parent.location.hash = 'subdivision.delete(".$sub['Subdivision_ID'].")'",
                'icon'   => 'remove',
                'sprite' => true
            );

            $tree_image = "folder" . ($sub["Checked"] ? "" : " nc--dark") . ($sub["LabelColor"] ? " nc--badge-" . $sub["LabelColor"] : "");
        } else {
            $tree_image = "folder" . ($sub["Checked"] ? "" : " nc--dark") . ($sub["LabelColor"] ? " nc--badge-" . $sub["LabelColor"] : "");
        }

        $ret_sub[$sub['Subdivision_ID']] = array("nodeId" => "sub-$sub[Subdivision_ID]",
                "parentNodeId"  => ($sub['Parent_Sub_ID'] ? "sub-$sub[Parent_Sub_ID]" : "site-$sub[Catalogue_ID]"),
                "name"          => $sub[Subdivision_ID].". ".strip_tags(str_replace(array("\r", "\n"), '', $sub["Subdivision_Name"])),
                "href"          => $is_subdivision_admin ? "#subdivision.design($sub[Subdivision_ID])" : "#subdivision.view($sub[Subdivision_ID])",
                "sprite"        => $tree_image,
                //"hasChildren" => false,
                "dragEnabled"   => $is_subdivision_admin,
                "buttons"       => $buttons,
                "acceptDropFn"  => "treeSitemapAcceptDrop",
                "onDropFn"      => "treeSitemapOnDrop",
                "className"     => ($sub["Checked"] ? "" : "disabled"),
                "checked"       => $sub["Checked"],
                "hasChildren"   => ($sub['hasChildren'] ? true : false),
                "subclasses"    => array());
    } // of foreach subdivision

    /* информация о шаблонах в разделах. необходима для:
     *  1. формирования ссылки на список объектов
     *  2. для определения возможности перемещения объекта в конкретный раздел
     */
    $all_subs = join(",", array_keys($ret_sub));
    if ($all_subs) {
        $subclass_data = $db->get_results("SELECT Sub_Class_ID, Class_ID, Subdivision_ID
                                         FROM Sub_Class
                                        WHERE Subdivision_ID IN ($all_subs)
                                        ORDER BY Priority",
                        ARRAY_A);

        foreach ((array) $subclass_data as $row) { //print "A";
            //Если есть сс и есть право на его модерирование - то ссылка на вкладку "Редактирование"
            if (!$ret_sub[$row['Subdivision_ID']]["subclasses"]
                    && ( $perm->isSubClass($row[Sub_Class_ID], MASK_MODERATE | MASK_READ) || $perm->isGuest() )) {
                $ret_sub[$row['Subdivision_ID']]["href"] = "#object.list($row[Sub_Class_ID])";
            }

            $ret_sub[$row['Subdivision_ID']]["subclasses"][] =
                    array("subclassId" => $row["Sub_Class_ID"],
                            "classId" => $row["Class_ID"]);
        }
    }
}

/**
 * выводим результат и свободны
 */
$ret = array_merge(array_values($ret_sites), array_values($ret_sub));
print "while(1);".nc_array_json($ret);
?>