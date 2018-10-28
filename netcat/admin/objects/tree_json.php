<?php

/* $Id: tree_json.php 8012 2012-08-23 14:15:46Z ewind $ */

ob_start("ob_gzhandler");

define("NC_ADMIN_ASK_PASSWORD", false);
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once ($NETCAT_FOLDER."vars.inc.php");
require ($ADMIN_FOLDER."function.inc.php");

list($node_type, $node_id) = explode("-", $node);
$node_id = (int) $node_id;

$ret_sites = array();
$ret_sub = array();


// список сайтов
if ($node_type == "root") {

    // получим id всех каталогов, к которому пользователь имеет доступ админа\модер
    // или имеет доступ к его разделам, тоже админ\модер
    // если ф-ция вернет не массив, то значит есть доступ ко всем
    $array_id = $perm->GetAllowSite(MASK_READ, true);

    /**
     * Получить список сайтов
     */
    $sites = $db->get_results("SELECT Catalogue_ID, Catalogue_Name, Domain, Mirrors, Checked, ncMobile, ncResponsive
                               FROM Catalogue
                               WHERE ". ((is_array($array_id) && !$perm->isGuest()) ? "Catalogue_ID IN(" . join(',', (array) $array_id) . ")" : "1"). "
                               ORDER BY Priority", ARRAY_A);

    $found_current_site = false;
    foreach ((array) $sites as $site) {
        $image = 'icon_site';
        $image .= $site['ncMobile'] ? '_mobile' : '';
        $image .= $site['ncResponsive'] ? '_adapt' : '';
        $image .= $site['Checked'] ? '' : '_disabled';
        $ret_sites[] = array("nodeId" => "site-$site[Catalogue_ID]",
                "name" => $site[Catalogue_ID].". ".$site["Catalogue_Name"],
                "href" => "#",
                "image" => $image,
                "hasChildren" => true
        );
        $domain_name = preg_quote($site['Domain']);
        if ($site['Mirrors']) {
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
        $qry_where = "sub.Catalogue_ID=$node_id AND sub.Parent_Sub_ID=0";
    } else {
        $qry_where = "sub.Parent_Sub_ID=$node_id";
    }

    // Получить разделы, которые пользователь может видеть
    $allow_id = $perm->GetAllowSub($current_site, MASK_ADMIN | MASK_MODERATE, true, true, true);
    $qry_where .= ( is_array($allow_id) && !$perm->isGuest() ) ? " AND sub.Subdivision_ID IN(".join(',', (array) $allow_id).") " : " AND 1";

    $subdivisions = $db->get_results("SELECT sub.Subdivision_ID,
                                           sub.Subdivision_Name,
                                           sub.Catalogue_ID,
                                           sub.Hidden_URL,
                                           sub.Parent_Sub_ID,
                                           sub.Checked,
                                           sub.Catalogue_ID,
                                           catalogue.Domain
                                      FROM Subdivision AS sub
                                 	  JOIN Catalogue AS catalogue ON catalogue.Catalogue_ID = sub.Catalogue_ID
                                     WHERE $qry_where
                                     ORDER BY sub.Priority", ARRAY_A);

    $nc_core = nc_Core::get_object();
    foreach ((array) $subdivisions as $sub) {
        $action = "";
        $buttons = array();
        $scheme = $nc_core->catalogue->get_scheme_by_id($sub['Catalogue_ID']);
        $buttons[] = array(
                "label" => CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_VIEW,
                "action" => "window.open('{$scheme}://".(($site['Domain']) ? $site['Domain'] : $HTTP_HOST).$SUB_FOLDER."');",
                'icon' => 'icons icon_preview'
        );
        $action = "top.loadSubClasses($sub[Subdivision_ID], $cc, $classID, $message);tree.selectNode('sub-$sub[Subdivision_ID]');";

        $tree_image = "icon_folder".($sub["Checked"] ? "" : "_disabled")."";
        $ret_sub[$sub['Subdivision_ID']] = array("nodeId" => "sub-$sub[Subdivision_ID]",
                "parentNodeId" => ($sub['Parent_Sub_ID'] ? "sub-$sub[Parent_Sub_ID]" : "site-$sub[Catalogue_ID]"),
                "name" => $sub[Subdivision_ID].". ".$sub["Subdivision_Name"],
                "href" => "#",
                "action" => $action,
                "image" => $tree_image,
                "hasChildren" => false,
                "dragEnabled" => $drag_enabled,
                "buttons" => $buttons,
                "className" => ($sub["Checked"] ? "" : "disabled"));
    }

    // check hasChildren
    if ($ret_sub) {
        $only_allowed = "";

        $children = $db->get_results("SELECT DISTINCT Parent_Sub_ID
                                    FROM Subdivision
                                   WHERE Parent_Sub_ID IN (".join(",", array_keys($ret_sub)).")
                                         $only_allowed", ARRAY_A);
        foreach ((array) $children as $sub) {
            $ret_sub[$sub['Parent_Sub_ID']]['hasChildren'] = true;
        }
    } // of "hasChildren?"
}

$ret = array_merge(array_values($ret_sites), array_values($ret_sub));
print nc_array_json($ret);
?>