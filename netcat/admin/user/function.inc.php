<?php
/* $Id: function.inc.php 8449 2012-11-22 08:49:33Z vadim $ */
if (!class_exists("nc_System"))
    die("Unable to load file.");
$systemMessageID = $UserID;
$systemTableName = 'User';
$systemTableID = GetSystemTableID($systemTableName);

/**
 * Return array with error discrp.
 *
 * @return array array[code_error] = error
 */
function GetArrayWithError_User() {
    global $NO_RIGHTS_MESSAGE;
    return array(2 => $NO_RIGHTS_MESSAGE,
            3 => CONTROL_USER_RIGHTS_ERROR_NOSELECTED,
            4 => CONTROL_USER_RIGHTS_ERROR_DATA,
            5 => CONTROL_USER_RIGHTS_ERROR_DB,
            6 => CONTROL_USER_RIGHTS_ERROR_POSSIBILITY,
            7 => CONTROL_USER_RIGHTS_ERROR_NOTSITE,
            8 => CONTROL_USER_RIGHTS_ERROR_NOTSUB,
            9 => CONTROL_USER_RIGHTS_ERROR_NOTCCINSUB,
            10 => CONTROL_USER_RIGHTS_ERROR_NOTTYPEOFRIGHT,
            11 => CONTROL_USER_RIGHTS_ERROR_START,
            12 => CONTROL_USER_RIGHTS_ERROR_END,
            13 => CONTROL_USER_RIGHTS_ERROR_STARTEND,
            14 => NETCAT_MODULE_MAILER_NO_ONE_MAILER,
            15 => CONTROL_USER_RIGHTS_ERROR_GUEST);
}

/**
 * Return html-code with form for user search
 *
 * @return string html-code
 */
function SearchUserForm($totalUsers) {
    global $db, $ROOT_FOLDER, $INCLUDE_FOLDER, $admin_mode, $MODULE_VARS;
    global $systemTableID, $systemMessageID, $systemTableName, $ADMIN_PATH;
    global $UserID, $Checked, $grpID, $sort_by, $sort_order, $objcount, $nonConfirmed, $rightsIds, $srchPat;

    $module_subscriber = 0;
    if (nc_module_check_by_keyword('subscriber', 0)) {
      $module_subscriber = ( $MODULE_VARS['subscriber']['VERSION'] > 1 ) ? 2 : 1;
    }

    if (!$UserID)
        $UserID = '';
    if (!$Checked)
        $Checked = 0;
    if (!$grpID || !is_array($grpID))
        $grpID = array();
    if (!$rightsIds || !is_array($rightsIds))
        $rightsIds = array();
    if (!$sort_by)
        $sort_by = 0;
    if (!$sort_order)
        $sort_order = 0;
    if (!$objcount)
        $objcount = 20;
    if (!$nonConfirmed)
        $nonConfirmed = 0;
    if ($nonConfirmed)
        $Checked = 2;

    require_once $INCLUDE_FOLDER . "s_list.inc.php";
    $is_there_any_files = 0;

    require $ROOT_FOLDER . "message_fields.php";

    $flds = array_flip($fld);
    $login_num = $flds['Login'];
    $email_num = $flds['Email'];

    $html .= "<div id='userFormSearch'><legend>".CONTROL_USER_FUNCS_USERSGET."</legend>
              <form method='get' action='index.php' id='userSearchForm' >
                <table border='0' cellpadding='0' cellspacing='0'>
                      <tr>
                        <td style='padding-right: 10px'>" . $fldName[$login_num] . ": </td>
                        <td>
                            <input type='text' name='srchPat[".$login_num."]' size='50' maxlength='255' value='" . htmlspecialchars(stripcslashes($srchPat[$login_num]), ENT_QUOTES) . "'>
                        </td>
                        <td rowspan='2' style='padding-left: 30px'>
                            <input style='background: #EEE; margin-top:15px; padding: 8px 6px 12px 6px; font-size: 15px; color: #333; border: 2px solid #1A87C2;' type='submit' class='s' value='" . CONTROL_USER_FUNCS_DOGET . "' title='" . CONTROL_USER_FUNCS_DOGET . "'/>
                        </td>
                      </tr>
                      <tr>
                        <td style='padding-right: 10px'>" . $fldName[$email_num] . ": </td>
                        <td>
                            <input type='text' name='srchPat[".$email_num."]' size='50' maxlength='255' value='" . htmlspecialchars(stripcslashes($srchPat[$email_num]), ENT_QUOTES) . "'>
                        </td>
                      </tr>
               </table>
            <input type='hidden' name=phase value='2'/>
            <input type='hidden' name='order_by' value='" . $order_by . "'/>
	    <input type='submit' class='hidden'/>
	    <input type='hidden' name='isSearch' value='1'/>
              </form>";
    $html .= "</div>";


    $html .= "<fieldset id='userFormSearchOff' style='cursor: pointer;' onclick=\"\$nc('#userFormSearchOff').css('display', 'none'); \$nc('#userFormSearchOn').css('display', ''); \$nc('#userFormSearch').css('display', 'none');\">";
    $html .= "<legend ><span style='color: #1A87C2; border-bottom: 1px dashed;'>" . CONTROL_USER_FUNCS_USERSGET_EXT . "</span>&nbsp;[" . $totalUsers . "]</legend>";
    $html .= "</fieldset>";
    $html .= "<fieldset id='userFormSearchOn' style='display: none'>";
    $html .= "<legend  style='cursor: pointer;' onclick=\"\$nc('#userFormSearchOn').css('display', 'none'); \$nc('#userFormSearchOff').css('display', ''); \$nc('#userFormSearch').css('display', '');\">";
    $html .= "<span style='color: #1A87C2; border-bottom: 1px dashed;'>" . CONTROL_USER_FUNCS_USERSGET_EXT . "</span>&nbsp;[" . $totalUsers . "]:";
    $html .= "</legend>";

    $html .= "
  <form method='get' action='index.php' id='userSearchForm' style='background-color: #EEE; padding: 16px; margin-right: 16px;'>
    <table border='0' cellpadding='0' cellspacing='0' width='97%'>
      <tr>
        <td width='5%'><nobr>ID: " . nc_admin_input_simple('UserID', $UserID, 5, '', "maxlength='15'") . "</nobr></td>
        <td rowspan='2' style='padding-left: 45px'>" . CONTROL_USER_GROUP . "<br>";

    $html .="<select name='grpID[]' multiple size='3'>"; //<option value='0'>".CONTROL_USER_MAIL_ALLGROUPS;
    if ($Result = $db->get_results("SELECT `PermissionGroup_ID`, `PermissionGroup_Name` FROM `PermissionGroup`", ARRAY_N)) {
        foreach ($Result AS $GroupArray)
            $html .= "<option value='" . $GroupArray[0] . "' " . ( in_array($GroupArray[0], $grpID) ? 'selected' : '') . ">" . $GroupArray[0] . ": " . $GroupArray[1]."</option>";
    }
    $html .= "</select>";

    $html .= "</td>";

    $html .= "<td rowspan=2>".CONTROL_USER_RIGHTS_TYPE_OF_RIGHT."<br>";
    $html .= "<select name='rightsIds[]' multiple size=3>";
    $html .= "<option value='".DIRECTOR."' ".(in_array(DIRECTOR, $rightsIds) ? 'selected':'').">".CONTROL_USER_RIGHTS_DIRECTOR."</option>";
    $html .= "<option value='".SUPERVISOR."' ".(in_array(SUPERVISOR, $rightsIds) ? 'selected':'').">".CONTROL_USER_RIGHTS_SUPERVISOR."</option>";
    $html .= "<option value='".EDITOR."' ".(in_array(EDITOR, $rightsIds) ? 'selected':'').">".CONTROL_USER_RIGHTS_EDITOR."</option>";
    $html .= "<option value='".MODERATOR."' ".(in_array(MODERATOR, $rightsIds) ? 'selected':'').">".CONTROL_USER_RIGHTS_MODERATOR."</option>";
    $html .= "<option value='".DEVELOPER."' ".(in_array(DEVELOPER, $rightsIds) ? 'selected':'').">".CONTROL_USER_RIGHTS_CLASSIFICATORADMIN."</option>";
    if ($module_subscriber == 2) {
      $html .= "<option value='".SUBSCRIBER."' ".(in_array(SUBSCRIBER, $rightsIds) ? 'selected':'').">".CONTROL_USER_RIGHTS_SUBSCRIBER."</option>";
    }
    $html .= "<option value='".BAN."' ".(in_array(BAN, $rightsIds) ? 'selected':'').">".CONTROL_USER_RIGHTS_BAN."</option>";
    $html .= "<option value='".GUEST."' ".(in_array(GUEST, $rightsIds) ? 'selected':'').">".CONTROL_USER_RIGHTS_GUESTONE."</option>";
    $html .= "</select>";
                                               
    $html .= "</td>";

    
    $html .= "</tr>
              <tr>
                  <td><nobr>
                  " . nc_admin_radio_simple('Checked', '', CONTROL_USER_FUNCS_ALLUSERS, !$Checked, 'chk1', 'checked') . "
                  " . nc_admin_radio_simple('Checked', 1, CONTROL_USER_FUNCS_ONUSERS, $Checked == 1, 'chk2') . "
                  " . nc_admin_radio_simple('Checked', 2, CONTROL_USER_FUNCS_OFFUSERS, $Checked == 2, 'chk3') . "
                  </nobr>
                  </td>

              </tr>
              <tr>
                    <td colspan='3' align='right' style='padding-right: 10px;'>
                        <input style='background: #EEE; padding: 8px 6px 12px 6px; font-size: 15px; color: #333; border: 2px solid #1A87C2;' type='submit' class='s' value='" . CONTROL_USER_FUNCS_DOGET . "' title='" . CONTROL_USER_FUNCS_DOGET . "' />
                    </td>
              </tr>";


    if ($searchForm = showSearchForm($fldName, $fldType, $fldDoSearch, $fldFmt)) {

        $html .= " <tr>
                <td colspan ='3' style='padding:0'>
                  <fieldset>";

        $html .= $searchForm;
        $html .= "

                  </fieldset>
                </td>
              </tr>";
    }

    $html .= "<tr><td colspan='3' style='padding:0'>

      <br>
      <fieldset>
        <legend>" . CONTROL_USER_FUNCS_VIEWCONTROL . "</legend>
        <table border ='0'>
          <tr>
            <td> " . CONTROL_USER_FUNCS_SORTBY . " </td>
            <td>";

    $html .= "<select name='sort_by' style='width: 100%'>";
    $html .= "<option value='0' " . ( $sort_by == 0 ? 'selected' : '' ) . " >" . CONTROL_USER_GROUP . "</option>";
    $html .= "<option value='1' " . ( $sort_by == 1 ? 'selected' : '' ) . " >ID</option>";
    foreach ($fldID as $k => $v)
        $html .= "<option value='" . $fldID[$k] . "' " . ( $sort_by == $fldID[$k] ? 'selected' : '' ) . " >" . $fldName[$k] . "</option>";
    $html .= "</select><br></td></tr>";
    $html .= "<tr><td>" . CONTROL_USER_FUNCS_SORT_ORDER . "</td><td>" .
            nc_admin_select_simple('', 'sort_order', array(CONTROL_USER_FUNCS_SORT_ORDER_ACS, CONTROL_USER_FUNCS_SORT_ORDER_DESC), $sort_order, "style='width: 100%'") . "</td><tr>
      <tr><td>" . CONTROL_CLASS_CLASS_OBJECTSLIST_SHOW . "</td>
      <td>" . nc_admin_input_simple('objcount', $objcount, 3) . "&nbsp;" . CONTROL_USER_FUNCS_USER_NUMBER_ON_THE_PAGE . "</td>
      </tr></table>
      </fieldset>

      </td></tr><tr><td valign='bottom' align='right' colspan='3' style='padding-right: 10px;'/>
      <input style='background: #EEE; padding: 8px 6px 12px 6px; font-size: 15px; color: #333; border: 2px solid #1A87C2;' type='submit' class='s' value='" . CONTROL_USER_FUNCS_DOGET . "' title='" . CONTROL_USER_FUNCS_DOGET . "'/>
      <input type='hidden' name=phase value='2'/>
      <input type='hidden' name='order_by' value='" . $order_by . "'/>
	    <input type='submit' class='hidden'/>
	    <input type='hidden' name='isSearch' value='1'/>
      </form></td></tr></table>";
    $html .="</fieldset><br>";
    return $html;
}

/**
 * Листинг пользоватлей
 *
 * @param int $totRows
 * @param str $queryStr
 * @param array $grpID
 * @param int $Checked
 * @param int $sort_by
 * @param int $sort_order
 * @param int $objcount
 * @param array $rightsIds
 * @return str html code
 */
function ListUserPages($totRows, $queryStr, $grpID, $Checked, $sort_by, $sort_order, $objcount, $rightsIds) {
    global $db, $curPos;

    $html = ""; // результат работы функции

    $range = 10;
    $maxRows = intval($objcount);
    if ($maxRows < 1)
        $maxRows = 20;

    $curPos += 0;
    $Checked += 0;
    $sort_by += 0;
    $sort_order += 0;

    if (!$maxRows || !$totRows)
        return;

    $page_count = ceil($totRows / $maxRows);
    $half_range = ceil($range / 2);
    $cur_page = ceil($curPos / $maxRows) + 1;

    if ($page_count < 2)
        return;

    $maybe_from = $cur_page - $half_range;
    $maybe_to = $cur_page + $half_range;

    if ($maybe_from < 0) {
        $maybe_to = $maybe_to - $maybe_from;
        $maybe_from = 0;

        if ($maybe_to > $page_count) {
            $maybe_to = $page_count;
        }
    }

    if ($maybe_to > $page_count) {
        $maybe_from = $page_count - $range;
        $maybe_to = $page_count;

        if ($maybe_from < 0) {
            $maybe_from = 0;
        }
    }

    $html .= "<div align='center'>";
    $native_pars = "&sort_by=" . $sort_by . "&sort_order=" . $sort_order . "&objcount=" . $objcount;

    //в ссылку добавим группы
    if (is_array($grpID) && !empty($grpID)) {
        foreach ($grpID as $v)
            $native_pars.="&grpID[]=" . intval($v);
    }
    //в ссылку добавим права
    if (is_array($rightsIds) && !empty($rightsIds)) {
        foreach ($rightsIds as $v) {
            $native_pars.="&rightsIds[]=" . intval($v);
        }
    }

    // включен\ выключен
    if ($Checked)
        $native_pars.="&Checked=" . $Checked;


    if ($cur_page > 1 && $page_count > $range) {
        $url = "?phase=2" . $native_pars . "&amp;" . $queryStr . "&curPos=" . ($curPos - $maxRows);
        $html .= "| <a href='{$url}' title='" . CONTROL_USER_FUNCS_PREV_PAGE . "'>&laquo;</a> | ";
    }

    for ($i = $maybe_from; $i < $maybe_to; $i++) {
        $page_number = $i + 1;
        $page_from = $i * $maxRows;
        $page_to = $page_from + $maxRows;
        $url = "?phase=2" . $native_pars . "&amp;" . $queryStr . "&curPos=" . $page_from;

        $html .= ( $curPos == $page_from) ? "$page_number" : "<a href='$url'>$page_number</a>";

        if ($i != ($maybe_to - 1))
            $html .= " | ";
    }

    if ($cur_page != $page_count && $page_count > $range) {
        $url = "?phase=2" . $native_pars . "&amp;" . $queryStr . "&curPos=" . ($curPos + $maxRows);
        $html .= " | <a href='{$url}' title='" . CONTROL_USER_FUNCS_NEXT . "'>&raquo;</a> |";
    }

    $html .= "</div><br>";

    return $html;
}

/**
 * Show table with all users
 *
 */
function SearchUserResult() {
    global $db, $perm, $ROOT_FOLDER, $INCLUDE_FOLDER;
    global $UserID, $PermissionGroupID, $Checked, $sort_by, $sort_order, $objcount, $isSearch, $nonConfirmed;
    global $srchPat, $admin_mode, $curPos;
    global $systemTableID, $systemMessageID, $systemTableName;
    global $AUTHORIZE_BY, $ADMIN_PATH, $ADMIN_TEMPLATE;

    $nc_core = nc_Core::get_object();

    $curPos += 0;
    $grpID = $_GET['grpID'];
    $rightsIds = $_GET['rightsIds'];
    $Checked += 0;
    $sort_by += 0;
    $sort_order += 0;
    $objcount += 0;
    $nonConfirmed += 0;

    require ($ROOT_FOLDER . "message_fields.php");
    require_once ($INCLUDE_FOLDER . "s_list.inc.php");

    //кол-во выводимых пользователей на странице
    if ($objcount < 1)
        $objcount = 20;

    //имя поля, по которому будет производиться сортировка
    switch ($sort_by) {
        case -2:
            $order_by_fld = "a." . $AUTHORIZE_BY;
            break;
        case -1:
            $order_by_fld = "a.`User_ID`";
            break;
        case 0:
            $order_by_fld = "a.PermissionGroup_ID";
            break;
        default:
            foreach ($fld as $k => $v) {
                if ($fldID[$k] == $sort_by) {
                    $order_by_fld = "a.`" . $fld[$k] . "`";
                    break;
                }
            }
            break;
    }

    if (!$order_by_fld)
        $order_by_fld = "g.PermissionGroup_ID";
    $order = " ORDER BY " . $order_by_fld . ($sort_order ? " DESC" : " ASC");

    //параметры поиска
    $search_params = getSearchParams($fld, $fldType, $fldDoSearch, $srchPat);
    $fullSearchStr = $search_params['query'];

    // формирование ссылки, чтобы при переходе по навигации\ сортировки не сбивались рез-ты выборки
    $native_pars = "";
    if (is_array($grpID) && !empty($grpID)) {
        foreach ($grpID as $v)
            $native_pars.="&grpID[]=" . intval($v);
    }

    if (is_array($rightsIds) && !empty($rightsIds)) {
        foreach ($rightsIds as $v){
            $native_pars.="&rightsIds[]=" . intval($v);
        }
    }
    if ($Checked)
        $native_pars.="&amp;Checked=" . $Checked;
    if ($nonConfirmed)
        $native_pars.="&amp;nonConfirmed=" . $nonConfirmed;
    $url = $native_pars . "&amp;" . $search_params['link'] . "&amp;curPos=" . $curPos . "&amp;objcount=" . $objcount;

    // -= Определение параметров выборки =-
    $tables = "";
    $where = " WHERE ug.`User_ID` = a.`User_ID` AND ug.`PermissionGroup_ID` = g.`PermissionGroup_ID` ";
    $where .= $fullSearchStr;

    // В выборке участвует группы
    if (is_array($grpID) && !empty($grpID)) {
        $user_in_group = array();

        foreach ($grpID as $v) {
            // Получим всех пользователей, находящихся в данной группе
            $user_in_group[] = nc_usergroup_get_users_from_group($v);
        }

        if (count($user_in_group) > 1) { // если выбрано больше одной группы, то массивы нудно объединить
            $to_eval = " \$users_id = array_intersect(";
            for ($i = 0; $i < count($user_in_group) - 1; $i++) {
                $to_eval .= " \$user_in_group[$i], ";
            }
            $to_eval .= " \$user_in_group[$i] );";
            eval($to_eval);
        } else { // выбрана одна группа
            $users_id = $user_in_group[0];
        }
        if (empty($users_id))
            $users_id[] = 0; // на случай, если ничего не нашлось
        $where .= "AND a.`User_ID` IN (" . join(',', (array) $users_id) . ")";
    }

    // В выборке участвуют права
    if (is_array($rightsIds) && !empty($rightsIds)) {
      $tables .= ", `Permission` as p ";
      $where .= " AND p.AdminType IN (".  implode(",", $rightsIds).") AND a.`User_ID`=p.`User_ID` ";
    }

    //условия выборки
    if ($nonConfirmed) {
        $where .= " AND a.Confirmed = 0 AND a.`RegistrationCode` <> '' ";
        $Checked = 2;
    }
    if ($UserID)
        $where .= " AND a.User_ID = '" . $UserID . "'";
    if ($Checked != "" && $Checked != 2)
        $where .= " AND a.Checked = '" . $Checked . "'";
    if ($Checked == 2)
        $where .= " AND a.Checked = 0";


    // ограничение по количеству
    $limit = " LIMIT " . $curPos . "," . $objcount;


    // Основоной запрос на выбору
    $select = "SELECT SQL_CALC_FOUND_ROWS a.`User_ID` AS id,  a.`Checked` AS checked, a.`" . $AUTHORIZE_BY . "` AS login, `Email` AS email,
             g.`PermissionGroup_ID` AS grp, GROUP_CONCAT( CONCAT(g.`PermissionGroup_ID`, '. ', g.`PermissionGroup_Name`) SEPARATOR '<br>') AS groups
             FROM `User` AS a,
             `User_Group` AS ug,
             `PermissionGroup` as g".$tables
            . $where . " GROUP BY a.`User_ID` " .
            $order . $limit;

    $Users = $db->get_results($select, ARRAY_A);
    // общее количество пользоватлей
    $totRows = $db->get_var("SELECT FOUND_ROWS()");
	
	//Форма для выборки пользователей
    $searchForm = SearchUserForm($totRows);
	
    // листинг пользователей
    $listing = ListUserPages($totRows, $search_params['link'], $grpID, $Checked, $sort_by, $sort_order, $objcount, $rightsIds);

    // информация о количестве найденных пользователей
    if (false && $totRows) {
        echo ( $isSearch ? CONTROL_USER_FUNCS_SEARCHEDUSER : CONTROL_USER_FUNCS_USERCOUNT ) . ": " . $totRows . "\n";
    }

    echo "<div id='mainForm_c'>";
    echo $searchForm;
    echo $listing;

    if (!empty($Users)) {

        $morePreference = $perm->GetUserWithMoreRights(); //id пользователей, которых данный пользователь не может трогать
        $edit_access = $perm->isAccess(NC_PERM_ITEM_USER, NC_PERM_ACTION_EDIT, -1);  // Если ли в приницпе доступ к редактированию
        $del_access = $perm->isAccess(NC_PERM_ITEM_USER, NC_PERM_ACTION_DEL, -1);  //                             и удалению
        //$right_access   = $perm->isAccess(NC_PERM_ITEM_USER, NC_PERM_ACTION_RIGHT, -1);  //                           и к правам
        $right_access = $edit_access; // на данный момент право редактирование = право измениие прав
        ?>
        <form method='post' action='index.php' id='mainForm'>
            <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                <tr>
                    <td>
                        <table class='nc-table nc--striped nc--small' width='100%'>
                            <tr>
                                <th>
                                    <a href='?sort_by=-1&amp;sort_order=<?= ($sort_by == -1) ? !$sort_order : 0 ?>&<?= $url ?>'>ID</a>
                                </th>
                                <th width="40%">
                                    <a href='?sort_by=-2&amp;sort_order=<?= ($sort_by == -2) ? !$sort_order : 0 ?>&<?= $url ?>'><?= CONTROL_USER ?></a>
                                </th>
                                <th>
                                    <a href='?sort_by=0&amp;sort_order=<?= ($sort_by == 0) ? !$sort_order : 0 ?>&<?= $url ?>'><?= CONTROL_USER_GROUP ?></a>
                                </th>
                                <? if ($edit_access) : ?>
								<th class='nc-text-right' width='25%'><?= CONTROL_USER_ACTIONS ?></th>
                                <? endif;
                                if ($right_access): ?>
								<th class='nc-text-center'><?= CONTROL_USER_RIGHTS ?></th>
                                <? endif;
                                if ($del_access): ?>
								<th class='nc-text-center'>
                                    <i class='nc-icon nc--remove' title='<?= CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DELETE ?>'></i>
                                </th>
                            <? endif; ?>
                            </tr>
                            <?
                            // сообственно вывд пользователя
                            foreach ($Users as $User) {
                                $this_user_edit = !in_array($User['id'], $morePreference); //Может ли редактировать данного пользователя

                                print "<tr>\n";
                                print"<td >" . $User['id'] . "</td>\n
            <td >\n";
                                if ($edit_access && $this_user_edit) {
                                    print "<a href=\"index.php?phase=4&UserID=" . $User['id'] . "\" " . (!$User['checked'] ? "style='color:#cccccc;'" : "") . ">\n";
                                }
                                print (($AUTHORIZE_BY != "User_ID") && !empty($User['login'])) ? $User['login'] : $User['email'];
                                print "</a></td>";

                                print "<td nowrap>" . $User['groups'] . "</td>";

                                if ($edit_access) {
                                    print "<td align=right nowrap>\n";
                                    if ($this_user_edit)
                                        print "<a href=index.php?" . $nc_core->token->get_url() . "&amp;phase=12&UserID=" . $User['id'] . ">" . ($User['checked'] ? NETCAT_MODERATION_TURNTOOFF : NETCAT_MODERATION_TURNTOON) . "</a> | <a href=\"index.php?phase=6&UserID=" . $User['id'] . "\">" . CONTROL_USER_CHANGEPASS . "</a>\n";
                                    print "</td>\n";
                                }
                                if ($right_access) {
                                    print "<td align=center>\n";
                                    if ($this_user_edit)
                                        print "<a href=\"index.php?phase=8&UserID=" . $User['id'] . "\"><i class='nc-icon nc--settings nc--hovered' title='" . CONTROL_USER_FUNCS_EDITACCESSRIGHT . "'></div></a>";
                                    print "</td>";
                                }

                                if ($del_access) {
                                    print "<td align=center>\n";
                                    if ($this_user_edit)
                                        print nc_admin_checkbox_simple("User" . $User['id'], $User['id']);
                                    print "</td>\n";
                                }
                                print "</tr>\n";
                            }
                            ?>
                        </table>
                    </td>
                </tr>
            </table>&nbsp;<br />&nbsp;<br />&nbsp;
            <?php
            global $UI_CONFIG;
            if ($perm->isAccess(NC_PERM_ITEM_USER, NC_PERM_ACTION_ADD))
                $UI_CONFIG->actionButtons[] = array("id" => "adduser",
                        "caption" => CONTROL_USER_REG,
                        "align" => "left",
                        "location" => "user.add()");

            if ($del_access)
                $UI_CONFIG->actionButtons[] = array(
                    "id" => "submit",
                    "caption" => NETCAT_ADMIN_DELETE_SELECTED,
                    "align" => "right",
                    "action" => "document.getElementById('mainViewIframe').contentWindow.sumbit_form(14)",
                    "red_border" => true,
                );
?>
		<script type='text/javascript'>
		function sumbit_form ( phase ) {
			document.getElementById('mainForm').phase.value =  phase;
			parent.mainView.submitIframeForm('mainForm');
			return 0;
		}
		</script>

		<input type='hidden' name=phase id='phase' value=14 />
		<input type='submit' class='hidden' />
	</form>
<?
            echo $listing;
        }
        else {
            nc_print_status(CONTROL_USER_MSG_USERNOTFOUND, 'info');
        }
            echo "</div>&nbsp;<br />&nbsp;";
        return;
    }

###############################################################################

    function GroupList() {
        global $db, $ROOT_FOLDER;
        global $Email;
        global $srchPat;
        global $systemTableID, $systemMessageID, $systemTableName, $ADMIN_TEMPLATE;

        $Result = $db->get_results("select PermissionGroup_ID, PermissionGroup_Name from PermissionGroup ", ARRAY_N)
        ?>
        <form method=post action=group.php>
            <table border=0 cellpadding=0 cellspacing=0 width=100%>
                <tr>
                    <td>
                        <table class='nc-table nc--striped nc--small' width='100%'>
                            <tr>
                                <th>ID</th>
                                <th width='80%'><?= CONTROL_USER_GROUP ?></th>
                                <th class='nc-text-center'><?= CONTROL_USER_RIGHTS ?></th>
                                <th class='nc-text-center'><i class='nc-icon nc--remove' title='<?= CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DELETE ?>'></i></th>
                            </tr>
                            <?
                            foreach ($Result as $Array) {
                                print "
                    <tr>
                        <td>" . $Array[0] . "</td>
                        <td><a href=\"group.php?phase=3&PermissionGroupID=" . $Array[0] . "\">" . $Array[1] . "</a></td>
                        <td align=center><a href=\"group.php?phase=8&PermissionGroupID=" . $Array[0] . "\"><i class='nc-icon nc--settings nc--hovered' title='" . CONTROL_USER_CHANGERIGHTS . "'></i></a></td>";
                                // можно или нет удалть группу
                                $confirmGroupDelete = true;
                                $users = nc_usergroup_get_users_from_group($Array[0]);
                                if (!empty($users)) {
                                    $query = "SELECT COUNT(`ID`) FROM `User_Group` WHERE `User_ID` IN (" . join(',', $users) . ") GROUP BY `User_ID`";
                                    if (in_array(1, $db->get_col($query)))
                                        $confirmGroupDelete = false;
                                }

                                if ($confirmGroupDelete) {
                                    print "
                        <td align=center>" . nc_admin_checkbox_simple("Delete" . $Array[0], $Array[0]) . "</td>";
                                } else {
                                    print "
                        <td align=center></td>";
                                }
                                print "
                    </tr>";
                            }
                            ?>
                        </table>
                    </td>
                </tr>
            </table><br>
            <?php
            global $UI_CONFIG;
            $UI_CONFIG->actionButtons[] = array(
                "id" => "submit",
                "caption" => NETCAT_ADMIN_DELETE_SELECTED,
                "action" => "nc_print_custom_modal_callback(function(){mainView.submitIframeForm();})",
                "red_border" => true,
            );

            $UI_CONFIG->actionButtons[] = array("id" => "adduser",
                    "caption" => CONTROL_USER_ADDNEWGROUP,
                    "align" => "left",
                    "location" => "usergroup.add()");
            ?>
            <input type='hidden' name=phase VALUE=2>
            <input type='submit' class='hidden'>
        </form>
        <?
    }

###############################################################################

    /**
     * Форма для добавления \ изменения пользователя
     *
     * @param int UserID
     * @param str action file
     * @param int  next phase
     * @param int type: 1 - insert; 2 - update
     */
    function UserForm($UserID, $action_file, $phase, $type) {

        global $nc_core, $db, $ROOT_FOLDER, $admin_mode, $perm, $DOMAIN_NAME;
        global $HTTP_FILES_PATH, $FILES_FOLDER;
        global $systemTableID, $systemMessageID, $systemTableName;
        global $Checked, $PermissionGroupID, $InsideAdminAccess;
        global $INCLUDE_FOLDER, $ADMIN_PATH;

        $UserID = intval($UserID);

        //есть ли файлы
        $is_there_any_files = getFileCount(0, $systemTableID);

        $params = array('Checked', 'InsideAdminAccess', 'PermissionGroupID',
                'Catalogue_ID', 'Password1', 'Password2', 'UserID', 'posting');
        foreach ($params as $v)
            global $$v;
        $st = new nc_Component(0, 3);
        foreach ($st->get_fields() as $v) {
            $name = 'f_' . $v['name'];
            global $$name;
            if ($v['type'] == 6) {
                global ${$name . "_old"};
                global ${"f_KILL" . $v['id']};
            }
        }

        if ($type == 1) {
            $User['Checked'] = $Checked;
            $User['PermissionGroup_ID'] = $PermissionGroupID;
            $User['InsideAdminAccess'] = $InsideAdminAccess;
        } elseif ($type == 2) {
            $User = $db->get_row("SELECT `Checked`,  `InsideAdminAccess`, `Catalogue_ID`
                          FROM `User`
                          WHERE `User_ID`='" . $UserID . "'", ARRAY_A);
            if (!$User) {
                nc_print_status(CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DBERROR, 'error');
                exit();
            }
            // узнаем группы, где он состоит
            $User['PermissionGroup_ID'] = nc_usergroup_get_group_by_user($UserID);
        }

        echo "<br /><form name='adminForm' class='nc-form' id='adminForm' " . ($is_there_any_files ? "enctype='multipart/form-data'" : "") . " method='post' action='" . $action_file . "'>";

        if ($type == 2) {
            echo "ID: $UserID&nbsp;&nbsp;";
        }

        // включен / выключен
        echo nc_admin_checkbox_simple('Checked', 1, CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_TURNON, $User['Checked'], 'chk') . "&nbsp;&nbsp;";
        // доступ в админку
        echo nc_admin_checkbox_simple('InsideAdminAccess', 1, NETCAT_MODULE_AUTH_INSIDE_ADMIN_ACCESS, $User['InsideAdminAccess']) . "&nbsp;&nbsp;<br /><br />";

        // PermissionGroupID
        //$UserPermGroupID = ($PermissionGroupID ? (int)$PermissionGroupID : $Array['PermissionGroup_ID']);
        // Группы пользователей
        $Result = $db->get_results("SELECT `PermissionGroup_ID`, `PermissionGroup_Name` FROM `PermissionGroup` ORDER BY `PermissionGroup_ID`", ARRAY_A);
        $groups_with_more_rights = $perm->GetGroupWithMoreRights();
        
        echo (count($Result) == 1 ? CONTROL_USER_GROUP : CONTROL_USER_GROUPS) . ":<br>";
        echo "<div style='overflow-y: auto; overflow-x: hidden; height: 130px; white-space:nowrap; display: inline-block; padding: 5px 25px 5px 5px; '><br>";
        foreach ((array)$Result as $Group) {
            $id = $Group['PermissionGroup_ID'];
            $name = $Group['PermissionGroup_Name'];
            //выключить группы с большими правами
            $disabled = (in_array($id, $groups_with_more_rights)) ? 'disabled' : '';
            echo nc_admin_checkbox_simple("PermissionGroupID[" . $id . "]", $id, $id . ":" . $name, in_array($id, (array) $User['PermissionGroup_ID']), "grp_" . $id, $disabled) . "<br>";
        }
        echo "</div><br/>";

        // если есть модуль авторизации, то можно выбрать сайт, где user сможет авторизоваться
        if (nc_module_check_by_keyword('auth')) {
            // Catalogue_ID
            $UserCatID = (isset($_POST['Catalogue_ID']) ? (int) $_POST['Catalogue_ID'] : $User['Catalogue_ID']);
            $Result = $db->get_results("SELECT Catalogue_ID, Catalogue_Name FROM Catalogue", ARRAY_N);
            echo CONTROL_AUTH_ON_ONE_SITE . ":<br><select name='Catalogue_ID'><option value='0'" . (!$UserCatID ? " selected" : "") . ">" . CONTROL_AUTH_ON_ALL_SITES . "</option>";
            foreach ($Result as $row) {
                echo "<option value='" . $row[0] . "'" . ($User['Catalogue_ID'] == $row[0] ? " selected" : "") . ">" . $row[0] . '. ' . $row[1] . "</option>";
            }
            echo "</select><br><br>";
        }


        if ($type == 1) {
            echo CONTROL_AUTH_HTML_PASSWORD . ":<br><input type='password' name='Password1' size='30' maxlength='50' value='" . $Password1 . "'><br><br>";
            echo CONTROL_AUTH_HTML_PASSWORDCONFIRM . ":<br><input type='password' name='Password2' size='30' maxlength='50' value='" . $Password2 . "'>";
            $action = "add";
        } elseif ($type == 2) {
            $action = "change";
            $message = $systemMessageID;
        }

        require $ROOT_FOLDER . "message_fields.php";

        if ($fldCount) {
            if ($type == 2) {
                $fieldQuery = join($fld, ",");
                $fldValue = $db->get_row("select $fieldQuery from User where User_ID='" . $systemMessageID . "'", ARRAY_N);
            }
            ?>
            <br />
            <style>.nc_admin_form_body > span {display: block;}</style>
			<fieldset>
				<legend><?= CONTROL_USER_TITLE_USERINFOEDIT ?></legend>
                                <div class='nc_admin_form_body nc-admin'>
				<? 
                                $nc_notmodal = 1;
                                require $ROOT_FOLDER . "message_edit.php"; ?>
                                </div>
			</fieldset>
            <?
        } else {
            ?><hr size="1" color="CCCCCC"><?
    }
    print "<input type='hidden' name='UserID' value='" . $UserID . "' />";
    print "<input type='hidden' name='posting' value='1' />";
        ?>
        <div align="right">
            <?
            global $UI_CONFIG;
            $UI_CONFIG->actionButtons[] = array(
				"id" => "submit",
				"caption" => ($type == 1 ? CONTROL_USER_FUNCS_ADDUSER : CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SAVE),
				"action" => "mainView.submitIframeForm()"
			);
            ?>
        </div>

        <?php
        if (nc_module_check_by_keyword('auth')) {
            $nc_auth_token = new nc_auth_token();
            $logins = $nc_auth_token->get_logins($UserID);
            echo "
				<fieldset>
				<legend>" . NETCAT_SETTINGS_USETOKEN . "</legend>";
            if (!empty($logins)) {
                echo "<input type='hidden' id='nc_token_destroy' name='nc_token_destroy' value='' />";
                echo "<div style='margin-bottom: 5px; font-weight: bold;'>" . CONTROL_AUTH_TOKEN_CURRENT_TOKENS . ": </div>";
                foreach ($logins as $id => $v) {
                    echo "<div style='margin: 0px 0px 3px 5px;'>" . $v . " (<a onclick='t_del(" . $id . ", \"" . $v . "\"); return false;' href='#'>" . NETCAT_MODERATION_DELETE . "</a>)</div>";
                }
            }

            echo "<div style='margin: 10px 0px; font-weight: bold;'>" . CONTROL_AUTH_TOKEN_NEW . "</div>";
            echo "<div id='t_plugin_error' class='token_error' style='display:none;'>" . CONTROL_AUTH_TOKEN_PLUGIN_ERROR . "</div>
				<div id='t_usbtoken_error' class='token_error' style='display:none;'>" . CONTROL_AUTH_TOKEN_MISS . "</div>
				<div id='t_pin_error' class='token_error' style='display:none;'>" . CONTROL_AUTH_PIN_INCORRECT . "</div>
				<div id='t_login_error' class='token_error' style='display:none;'>" . CONTROL_AUTH_LOGIN_NOT_EMPTY . "</div>
				<div id='t_key_error' class='token_error' style='display:none;'>" . CONTROL_AUTH_KEYPAIR_INCORRECT . "</div>
				<div>
				" . CONTROL_AUTH_HTML_LOGIN . ": <br/><input name='nc_token_login' id='nc_token_login' /><br/><br/>
				<input type='hidden' name='nc_token_key' id='nc_token_key' value='' />
				<input type='button' onclick='t_reg()' value='" . CONTROL_AUTH_TOKEN_NEW_BUTTON . "' title='" . CONTROL_AUTH_TOKEN_NEW_BUTTON . "' />
				</div>
				</fieldset>
				<div id='nc_token_plugin_wrapper'></div>
				<script src='" . $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH . "modules/auth/auth.js'></script>
				<script>
				var nc_token_obj = null;
				function create_nc_token_object() {
				    if (!nc_token_obj) {
			            \$nc(\"#nc_token_plugin_wrapper\").append(\"<object id='nc_token_plugin' type='application/x-rutoken' width='0' height='0'></object>\");
			            nc_token_obj = new nc_auth_token ( {'token_id' : 'nc_token_key'});
				    }
				}
				function t_reg () {
				create_nc_token_object();
				var r;
				\$nc('.token_error').hide();
				switch ( r = nc_token_obj.reg() ) {
				  case 1:  \$nc('#t_plugin_error').show(); break; // нет плагина
				  case 2:  \$nc('#t_usbtoken_error').show(); break; // нет токена
				  case 3:  \$nc('#t_pin_error').show(); break; // пин неверный
				  case 4:  \$nc('#t_login_error').show(); break; // логин неверный
				  case 5:  \$nc('#t_key_error').show(); break; // ошибка создания ключа
				  case 0 : document.getElementById('adminForm').submit(); break;// все хорошо
				  default: alert('error: ' + r); // непредвиденная ошибка
				}
				}
				function t_del ( id, name ) {
				create_nc_token_object();
				if ( confirm('" . NETCAT_MODERATION_DELETE . "') ) {
				  nc_token_obj.attempt_delete(name);
				  \$nc('#nc_token_destroy').val(id);
				  document.getElementById('adminForm').submit();
				}
				}
				</script>";
        }
        ?>

    <?php echo $nc_core->token->get_input(); ?>
        <input type='hidden' name=phase value=<?= $phase; ?>>
        <input type='submit' class='hidden'>
    </form>
    <?
}

###############################################################################

function ActionUserCompleted($action_file, $type) {
    global $nc_core, $db, $ROOT_FOLDER, $admin_mode, $perm;
    global $systemTableID, $systemTableName, $systemMessageID;
    global $FILES_FOLDER, $INCLUDE_FOLDER;
    global $DIRCHMOD, $FILECHMOD, $AUTHORIZE_BY;

    $params = array(
            'Checked',
            'InsideAdminAccess',
            'PermissionGroupID',
            'Catalogue_ID',
            'Password1',
            'Password2',
            'UserID',
            'posting');
    foreach ($params as $v)
        global $$v;
    $st = new nc_Component(0, 3);
    foreach ($st->get_fields() as $v) {
        $name = 'f_' . $v['name'];
        global $$name;
        if ($v['type'] == 6) {
            global ${$name . "_old"};
            global ${"f_KILL" . $v['id']};
        }
        if ($v['type'] == 8) {
            global ${$name . "_day"};
            global ${$name . "_month"};
            global ${$name . "_year"};
            global ${$name . "_hours"};
            global ${$name . "_minutes"};
            global ${$name . "_seconds"};
        }
    }

    $UserID = intval($UserID);
    $Checked = intval($Checked);

    $ret = 0; // возврщаемое значение (текст ошибки или 0)
    $is_there_any_files = getFileCount(0, $systemTableID);

    $user_table_mode = true;
    if ($type == 1) {
        $action = "add";
    } else {
        $action = "change";
        $message = $UserID;
    }


    $Priority += 0;

    require $ROOT_FOLDER . "message_fields.php";

    if ($posting == 0) {
        return $warnText;
    }

    require $ROOT_FOLDER . "message_put.php";

    if (empty($PermissionGroupID)) {
        return CONTROL_USER_FUNC_GROUP_ERROR;
    }

    // значение, которое пойдет в таблицу User
    // для совместимости со старыми версиями
    $mainPermissionGroupID = intval(min($PermissionGroupID));
    
    $groups_with_more_rights = $perm->GetGroupWithMoreRights();

    //нельзя добавить в группу с большими правами
    $add_groups_with_more_rights = array_intersect($PermissionGroupID, $groups_with_more_rights);
    if (!empty($add_groups_with_more_rights)) {
        return $warnText = NETCAT_MODERATION_ERROR_NORIGHT;
    }

    $Login = ${'f_'. $AUTHORIZE_BY};

    if ($type == 1) {
        $Password = $Password1;


        for ($i = 0; $i < $fldCount; $i++) {
            if (isset(${$fld[$i].'Defined'}) && ${$fld[$i].'Defined'} == true) {
            $fieldString .= "`" . $fld[$i] . "`,";
            $valueString .= ${$fld[$i].'NewValue'} . ",";
            }
        }
        $insert = "INSERT INTO User ( " . $fieldString;
        $insert .= "PermissionGroup_ID, Catalogue_ID, Password, Checked, Created,InsideAdminAccess) values ( " . $valueString;
        $insert .= "'" . $mainPermissionGroupID . "', ";
        if (isset($_POST['Catalogue_ID'])) {
            $insert .= +$_POST['Catalogue_ID'] . ", ";
        }
        else {
            $insert .= "0, ";
        }
        $insert .= $nc_core->MYSQL_ENCRYPT . "('" . $Password . "'),'$Checked','" . date("Y-m-d H:i:s") . "', '" . (int) $InsideAdminAccess . "')";

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_USER_CREATED, 0);

        $Result = $db->query($insert);
        $UserID = $db->insert_id;
        $message = $UserID;


        if ($Result) {
            // execute core action
            $nc_core->event->execute(nc_Event::AFTER_USER_CREATED, $message);

            nc_print_status(CONTROL_USER_NEW_ADDED, 'ok');
            foreach ($PermissionGroupID as $v) {
                nc_usergroup_add_to_group($UserID, $v);
            }
            
            //постобработка файлов с учетом нового $message
            $nc_core->files->field_save_file_afteraction($message);
        } else {
            return CONTROL_USER_NEW_NOTADDED . "<br/>" . sprintf(NETCAT_ERROR_SQL, $db->last_query, $db->last_error);
        }
    }

    if ($type == 2) {
        $cur_checked = $db->get_var("SELECT `Checked` FROM `User` WHERE `User_ID` = '" . $UserID . "'");
        $update = "update User set ";
        for ($i = 0; $i < $fldCount; $i++) {
            if ($fldTypeOfEdit[$i] == 3 || ( $fldTypeOfEdit[$i] == 2 && !nc_field_check_admin_perm()))
                continue; // поле недоступно никому или доступно администратору но нет прав администратора

            if (isset(${$fld[$i].'Defined'}) && ${$fld[$i].'Defined'} == true) {
              $update .= $fld[$i] ."=" . ${$fld[$i].'NewValue'} . ",";
            } else {
              $update .= $fld[$i] . "=" . ($fldValue[$i] ? $fldValue[$i] : "NULL") . ",";
            }
            
        }
        $update .= "Checked=\"" . $Checked . "\",";
        $update .= "PermissionGroup_ID=\"" . $mainPermissionGroupID . "\",";
        $update .= "InsideAdminAccess=" . (int) $InsideAdminAccess;
        if (isset($_POST['Catalogue_ID']))
            $update.= ", Catalogue_ID=" . (int) $_POST['Catalogue_ID'];
        $update .= " where User_ID=" . $UserID;

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_USER_UPDATED, $UserID);
        if ($cur_checked != $Checked) {
            $nc_core->event->execute($Checked ? nc_Event::BEFORE_USER_ENABLED : nc_Event::BEFORE_USER_DISABLED, $UserID);
        }

        $Result = $db->query($update);
        // execute core action
        $nc_core->event->execute(nc_Event::AFTER_USER_UPDATED, $UserID);

        $db->query("DELETE FROM `User_Group` WHERE `User_ID`='" . intval($UserID) . "'");
        foreach ($PermissionGroupID as $v) {
            nc_usergroup_add_to_group($UserID, $v, 0);
        }

        // произошла смена состояния пользователя
        if ($cur_checked != $Checked) {
            $nc_core->event->execute($Checked ? nc_Event::AFTER_USER_ENABLED : nc_Event::AFTER_USER_DISABLED, $UserID);
        }
    }

    $nc_multifield_field_names = $nc_core->get_component('User')->get_fields(NC_FIELDTYPE_MULTIFILE, false);
    foreach ($nc_multifield_field_names as $nc_multifield_field_name) {
        nc_multifield_saver::save_from_post_data('User', $UserID, ${"f_{$nc_multifield_field_name}"}, $action == 'add');
    }

    // привязка токена
    $nc_token_login = $nc_core->input->fetch_get_post('nc_token_login');
    $nc_token_key = $nc_core->input->fetch_get_post('nc_token_key');
    if ($nc_token_login && $nc_token_key && $UserID) {
        $db->query("INSERT INTO `Auth_Token`
                  SET `Login` = '" . $db->escape($nc_token_login) . "',
                      `PublicKey` = '" . $db->escape($nc_token_key) . "',
                      `User_ID` = '" . $UserID . "' ");
    }


    $nc_token_destroy = $nc_core->input->fetch_get_post('nc_token_destroy');
    if ($nc_token_destroy) {
        $nc_auth_token = new nc_auth_token();
        $nc_auth_token->delete_by_id($nc_token_destroy);
    }

    return 0;
}

###############################################################################

function GroupForm($PermissionGroupID, $action, $phase, $type) {
    # type = 1 - это insert
    # type = 2 - это update
    global $db, $ADMIN_PATH, $nc_corr, $AUTHORIZE_BY;

    $PermissionGroupID = intval($PermissionGroupID);

    if ($type == 2) {
        $Group = $db->get_row("SELECT `PermissionGroup_Name`
                               FROM `PermissionGroup`
                               WHERE `PermissionGroup_ID`='" . $PermissionGroupID . "'", ARRAY_A);
        if (!$Group) {
            nc_print_status(CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DBERROR, 'error');
            return;
        }
    }
    ?>
    <br />
    <form method=post action="<?= $action ?>">
        <?= CONTROL_USER_GROUPNAME ?>:<br><?= nc_admin_input_simple('PermissionGroupName', $Group['PermissionGroup_Name'], 50, "maxlength='64'") ?>
        <INPUT type='hidden' NAME=PermissionGroupID VALUE='<?= $PermissionGroupID ?>'>

        <?php
        global $UI_CONFIG;
        $UI_CONFIG->actionButtons[] = array("id" => "submit",
                "caption" => ($type == 1 ? CONTROL_USER_ADDNEWGROUP : CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SAVE),
                "action" => "mainView.submitIframeForm()");
        ?>
        <input type='hidden' name=phase value=<?= $phase; ?> />
        <input type='submit' class='hidden' />
    </form>
    <br />
    <?php
    // Покажем всех пользователей группы
    if ($type == 2) {
        $users = $db->get_results("SELECT u.* FROM `User` as `u`, `User_Group` as `ug` WHERE u.User_ID = ug.User_ID AND ug.PermissionGroup_ID = '" . $PermissionGroupID . "' ", ARRAY_A);

        if (($count = count($users))) {
            echo CONTROL_USER_GROUP_MEMBERS . " (" . CONTROL_USER_GROUP_TOTAL . " " . $count . "):";
            echo "<ul>";
            foreach ($users as $user) {
                echo "<li><a href='" . $ADMIN_PATH . "user/index.php?phase=4&amp;UserID=" . $user['User_ID'] . "'>" . htmlspecialchars($user[$AUTHORIZE_BY] ? $user[$AUTHORIZE_BY] : ( $user['ForumName'] ? $user['ForumName'] : ($user['Name'] ? $user['Name'] : $user['User_ID']))) . "</a>";
                switch ($user['UserType']) {
                    case 'vk':
                        echo ' (vkontakte.ru)';
                        break;
                    case 'fb':
                        echo ' (facebook.com)';
                        break;
                    case 'twitter':
                        echo ' (twitter.com)';
                        break;
                    case 'openid':
                        echo ' (OpenID)';
                        break;
                    case 'oauth':
                        echo ' (OAuth)';
                        break;
                }
                echo "</li>";
            }
            echo "</ul>";
        }
        else {
            echo CONTROL_USER_GROUP_NOMEMBERS;
        }
    }
}

###############################################################################

/**
 * Изменить или добавить группу с прорисовкой дерева
 *
 * @param int type - тип операции: 1 - добавить группу. 2 - обновить группк
 * @return bool true - удачно, false - неудачно
 */
function ActionGroupCompleted($type) {
    global $db, $UI_CONFIG;

    $PermissionGroupID = $_POST['PermissionGroupID'];
    $PermissionGroupName = $_POST['PermissionGroupName'];
    $ret = false; // возвращаемое значение

    if ($type == 1) { // добавить группу
        $ret = nc_usergroup_create($PermissionGroupName);
        if ($ret) { // обновить дерево
            $UI_CONFIG->treeChanges['addNode'][] = array("nodeId" => "usergroup-$ret",
                    "name" => $PermissionGroupName,
                    "href" => "#usergroup.edit($ret)",
                    "image" => "icon_usergroups",
                    "hasChildren" => false,
                    "parentNodeId" => "usergroup");
        }
        else {
            nc_print_status(CONTROL_USER_ERROR_GROUPNAME_IS_EMPTY, 'error');
        }
    } else if ($type == 2) { // переименовать группу
        $ret = nc_usergroup_rename($PermissionGroupID, $PermissionGroupName);
        if ($ret) {
            $UI_CONFIG->treeChanges['updateNode'][] = array("nodeId" => "usergroup-$PermissionGroupID",
                    "name" => "$PermissionGroupName");
        }
        else {
            nc_print_status(CONTROL_USER_ERROR_GROUPNAME_IS_EMPTY, 'error');
        }
    }

    return $ret;
}

###############################################################################

function ChangePasswordFormAdmin($UserID) {
    global $db, $nc_core;
    ?>
    <form method=post action=index.php>

        <?= CONTROL_USER_NEWPASSWORD ?>:<br>
        <INPUT TYPE=PASSWORD NAME=Password1 SIZE=30 MAXLENGTH=32><br><br>
    <?= CONTROL_USER_NEWPASSWORDAGAIN
    ?>:<br>
        <INPUT TYPE=PASSWORD NAME=Password2 SIZE=30 MAXLENGTH=32><br>
        <INPUT type='hidden' NAME=UserID VALUE=<?= $UserID
    ?>>
        <input type='hidden' name=phase value=7>
        <?
        global $UI_CONFIG;
        $UI_CONFIG->actionButtons[] = array("id" => "submit",
                "caption" => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SAVE,
                "action" => "mainView.submitIframeForm()");
        ?>
        <input type='submit' class='hidden'>
    <?php echo $nc_core->token->get_input(); ?>
    </form>
    <?
}

/**
 * Show user permission
 *
 * @param int $UserID - user id
 * @param int $phase  phase in hidden
 * @param string $action action in form
 * @param int $PermissionGroupID PermissionGroupID
 */
function ShowUserPermissions($UserID, $phase, $action = "index.php", $PermissionGroupID = 0) {
    global $db, $UI_CONFIG, $nc_core;
    global $USER_PERM_ARRAY, $USER_PERM_COUNT;
    global $perm, $ADMIN_PATH, $ADMIN_TEMPLATE;

    // удалим права, у которых закончился "срок действия"
    Permission::DeleteObsoletePerm();

    $allPerm = Permission::GetAllPermission($UserID, $PermissionGroupID);
    $count_td_colspan = NC_PERM_COUNT_PERM;
    $module_subsribe = nc_module_check_by_keyword('subscriber', false);
    $module_comments = nc_module_check_by_keyword('comments');

    if (!$module_subsribe)
        $count_td_colspan--;
    if (!$module_comments)
        $count_td_colspan--;

    if (!empty($allPerm)) { // User has rights
        ?>
        <form method='post' action='<?= $action ?>'>
            <table border='0' cellpadding='0' cellspacing='0' width='100%'>
                <tr>
                    <td>
                        <table class='admin_table permission_table' style='width:100%'>
                            <tr>
                                <th class='align-center' width='50%' rowspan='2'><?= SECTION_INDEX_USER_RIGHTS_TYPE  ?></th>
                                <th class='align-center' colspan='<?= $count_td_colspan  ?>'>
                                    <?= SECTION_INDEX_USER_RIGHTS_RIGHTS  ?></th>
                                <th class='align-center' rowspan='2'>
        <?= CONTROL_USER_RIGHTS_LIVETIME
        ?>
                                </th>
                                <td align='center' rowspan='2'>
                                    <div class='icons icon_delete' title='<?= CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DELETE ?>'></div>

                                </td>

                            </tr>
                            <tr>
                                <?php
                                for ($i = 0; $i < NC_PERM_COUNT_PERM; $i++) {
                                    $name = (($i == NC_PERM_READ_ID) ? CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_VIEW :
                                                    (($i == NC_PERM_COMMENT_ID) ? CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_COMMENT :
                                                            (($i == NC_PERM_ADD_ID) ? CONTROL_CONTENT_CATALOUGE_ADD :
                                                                    (($i == NC_PERM_EDIT_ID) ? CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_CHANGE :
                                                                            (($i == NC_PERM_CHECKED_ID) ? CONTROL_CLASS_ACTIONS_CHECKED :
                                                                                    (($i == NC_PERM_DELETE_ID) ? CONTROL_CLASS_ACTIONS_DELETE :
                                                                                            (($i == NC_PERM_SUBCRIBE_ID) ? CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SUBSCRIBE :
                                                                                                    (($i == NC_PERM_MODERATE_ID) ? CONTROL_CLASS_ACTIONS_MODERATE :
                                                                                                            (($i == NC_PERM_ADMIN_ID) ? CONTROL_CLASS_ACTIONS_ADMIN : '')))))))));

                                    if (!$module_subsribe && $i == NC_PERM_SUBCRIBE_ID)
                                        continue;
                                    if (!$module_comments && $i == NC_PERM_COMMENT_ID)
                                        continue;
                                    print "<td width='8%' class='align-center'>" . $name . "</td>";
                                }
                                print "</tr>";


                                foreach ($allPerm as $k => $v) {
                                    /* $k - Permission ID
                                      $v - array with perm. parametrs: title, live, ..
                                     */

                                    print "<tr>\n";
                                    print "<td>\n" . $v['title'] . "\n</td>\n";
                                    if ($v[NC_PERM_READ_ID]['checkbox'] == -1) { // Director, Supervisor, Guest
                                        print "<td colspan='" . $count_td_colspan . "'>\n";
                                    } else { // editor, moderator, developer
                                        for ($i = 0; $i < NC_PERM_COUNT_PERM; $i++) {
                                            if (!$module_subsribe && $i == NC_PERM_SUBCRIBE_ID)
                                                continue;
                                            if (!$module_comments && $i == NC_PERM_COMMENT_ID)
                                                continue;
                                            $hidden = ""; //Скрытые поля требуются для передачи данных недоступных чексбоксов
                                            print "<td align='center'>\n";
                                            if ($v[$i]['checkbox'] != 3) { // не 3 - это 0, 1 или 2 - показываем чексбокс
                                                $checkedAttr = '';
                                                if ($v[$i]['checkbox'] == 1)
                                                    $checkedAttr = " checked ";
                                                if ($v[$i]['checkbox'] == 2) {
                                                    $checkedAttr = " checked disabled ";
                                                    $hidden = "<input type='hidden' name='PermissionID" . $k . "x" . $i . "' value='" . $v[$i]['mask'] . "'>";
                                                }
                                                print nc_admin_checkbox_simple("PermissionID" . $k . "x" . $i, $v[$i]['mask'], '', false, '', "class='wh'" . $checkedAttr);
                                                print $hidden;
                                            }
                                            print "</td>\n";
                                        }
                                    }
                                    print "<td>" . $v['live'] . "</td>\n";
                                    print "<td>" . nc_admin_checkbox_simple("Delete" . $k, $k, '', false, '', "class='wh'") . "</td>\n";
                                    print "</tr>\n";
                                }

                                $UI_CONFIG->actionButtons[] = array("id" => "submit",
                                        "caption" => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SAVE,
                                        "action" => "mainView.submitIframeForm()");
                            } else {
                                nc_print_status($UserID ? CONTROL_USER_FUNCS_NORIGHTS : CONTROL_USER_FUNCS_GROUP_NORIGHTS, 'info');
                            }

                            $UI_CONFIG->actionButtons[] = array("id" => "addrights",
                                    "caption" => CONTROL_USER_FUNCS_ADDNEWRIGHTS,
                                    "align" => "left",
                                    "action" => "mainView.loadIframe('" . $ADMIN_PATH . "user/" . $action . "?phase=9&UserID=" . $UserID . "&PermissionGroupID=" . $PermissionGroupID . "')");
                            ?>
    <?php echo $nc_core->token->get_input(); ?>
                        <input type='hidden' name='phase' value='<?= $phase ?>'>
                        <input type='hidden' name='UserID' value='<?= $UserID ?>'>
                        <input type='hidden' name='PermissionGroupID' value='<?= $PermissionGroupID ?>'>
                        <input type='submit' class='hidden'>
                        </form>
                        <?
                        return;
                    }

###############################################################################

                    function ConfirmDeleteUsers() {
                        global $db, $nc_core;

                        $num_user = 0;
                        $html = "<ul>\n<form action ='index.php' method = 'post'>\n";
                        $html .= "<input type='hidden' name='phase' value='3'>\n";
                        while (list($key, $val) = each($_POST))
                            if (substr($key, 0, 4) == "User") {
                                $html .= "<li>" . $val . ". " . GetLoginByID($val) . "</li>\n";
                                $num_user++;
                                $html .="<input type='hidden' name='Delete" . $val . "' value='" . $val . "'>\n";
                            }

                        $html .= $nc_core->token->get_input();
                        $html .= "</form></ul>";
                        if ($num_user) {
                            nc_print_status(CONTROL_USER_FUNC_CONFIRM_DEL, 'info');
                            echo $html;
                        } else {
                            nc_print_status(CONTROL_USER_FUNC_CONFIRM_DEL_NOT_USER, 'error');
                        }

                        return $num_user;
                    }

                    /**
                     * Функция удаляет пользователей
                     *
                     * @param mixed номер пользователя или массив с индетификаторами
                     * @global  $nc_core, $perm
                     *
                     * @return bool|array массив с id удаленных пользователей
                     */
                    function DeleteUsers($ids) {
                        global $nc_core, $perm;

                        $ids = (array)$ids;

                        if (!$perm instanceof Permission || empty($ids)) {
                            return false;
                        }

                        $db = $nc_core->db;

                        $deleted_users = array(); // массив со всеми удаленными пользователями
                        $DeleteActionTemplate = $db->get_var("SELECT `DeleteActionTemplate` FROM `Class` WHERE `System_Table_ID`='3'");

                        foreach ($ids as $id) {
                            $id += 0;
                            if (!$id) {
                                continue;
                            }
                            // нельзя удалить себя
                            if ($id == $perm->GetUserID()) {
                                continue;
                            }
                            // нельзя удалить пользователя с большими правами
                            if (!$perm->isAccess(NC_PERM_ITEM_USER, NC_PERM_ACTION_DEL, $id)) {
                                continue;
                            }

                            // удаление
                            //$db->query("DELETE FROM `Subscriber` WHERE `User_ID` = '".intval($UserID)."'"); // из подписок
                            DeleteSystemTableFiles('User', $id); // удалениe файлов

                            $message = $id; // чтобы было доступно в действии после удаления

                            if ($DeleteActionTemplate) {
                                eval(nc_check_eval("echo \"" . $DeleteActionTemplate . "\";")); // действие после удаления
                            }

                            $deleted_users[] = $id;
                        }

                        // никого не удалили
                        if (empty($deleted_users)) {
                            return false;
                        }

                        // генерируем событие
                        $nc_core->event->execute(nc_Event::BEFORE_USER_DELETED, $deleted_users);

                        $ids_str = join(',', $deleted_users);

                        $db->query("DELETE FROM `User` WHERE `User_ID` IN (" . $ids_str . ") ");
                        $db->query("DELETE FROM `User_Group`  WHERE `User_ID` IN (" . $ids_str . ") ");

                        if ($nc_core->modules->get_by_keyword('auth')) {
                            nc_auth_delete_all_relation($deleted_users);
                            $db->query("DELETE FROM `Auth_ExternalAuth` WHERE `User_ID` IN (" . $ids_str . ") ");
                        }

                        // генерируем событие
                        $nc_core->event->execute(nc_Event::AFTER_USER_DELETED, $deleted_users);

                        return $deleted_users;
                    }

###############################################################################

                    /**
                     * Удалить группы с прорисовкой дерева
                     *
                     */
                    function DeleteGroups() {
                        global $db, $UI_CONFIG;

                        $deletedGroup = array(); // удаленные группы
                        $grp = array(); // группы, которые хотят удалить
                        // соберем в массив все группы
                        while (list($key, $val) = each($_POST)) {
                            if (substr($key, 0, 6) == "Delete")
                                $grp[] = $val;
                        }

                        // если групп нет - ошибка
                        if (empty($grp)) {
                            nc_print_status(CONTROL_USER_FUNCS_ERR_CANTREMGROUP, 'error', $Array);
                            exit();
                        }

                        // сообственно, удаление
                        $deletedGroup = nc_usergroup_delete($grp);

                        // перерисовка дерева
                        if (!empty($deletedGroup)) {
                            foreach ($deletedGroup as $v) {
                                $UI_CONFIG->treeChanges['deleteNode'][] = "usergroup-" . $v;
                            }
                        }
                    }

###############################################################################

                    /**
                     * Show form to add new permission
                     *
                     * @param int User ID
                     * @param int phase
                     * @param string action in form
                     * @param int Permission Group ID
                     */
                    function AddPermissionForm($UserID, $phase = 10, $action = 'index.php', $PermissionGroupID = 0) {
                        global $nc_core, $ADMIN_PATH, $MODULE_VARS;
                        global $db, $UI_CONFIG;
                        global $perm, $user_login;
                        $MODULE_VARS = $nc_core->modules->get_module_vars();

                        $params = array('AdminType', 'unlimit', 'start_time', 'start_day', 'start_month', 'start_year',
                                'start_hour', 'start_minute', 'end_time', 'end_day', 'end_month', 'end_year', 'end_hour', 'end_minute',
                                'item', 'site_list', 'sub_list', 'subclass_list', 'Read', 'Comment', 'Add', 'Edit', 'Check', 'Delete', 'Moderate', 'Administer',
                                'across_start', 'across_start_type', 'across_end', 'across_end_type');

                        foreach ($_POST AS $key => $val) {
                            if (!in_array($key, $params))
                                continue;
                            $$key = $val;
                        }

                        $module_subscriber = 0;
                        if (nc_module_check_by_keyword('subscriber', 0)) {
                            $module_subscriber = ( $MODULE_VARS['subscriber']['VERSION'] > 1 ) ? 2 : 1;
                        }

                        if (!$AdminType)
                            $AdminType = 0;
                        if (!isset($unlimit))
                            $unlimit = 1;
                        if (isset($unlimit) && !$unlimit)
                            $unlimit = 0;
                        if (!$start_time)
                            $start_time = 0;
                        if (!$end_time)
                            $end_time = 0;

                        $site_list_id = $db->get_col("SELECT `Catalogue_ID`, `Catalogue_Name` From `Catalogue`");
                        $site_list_name = $db->get_col(0, 1);
                        ?>

                        <script language='javascript'>
                            var site_id = new Array();
                            var site_name = new Array();
    <?php
    for ($i = 0; $i < count($site_list_id); $i++) {
        print "site_id[" . $i . "]=" . $site_list_id[$i] . ";";
        print "site_name[" . $i . "]=\"" . addslashes($site_list_name[$i]) . "\";";
    }
    ?>

        var some_const = {
            allclassificator : '<?= CONTENT_CLASSIFICATORS_NAMEALL
    ?>',
            classificator : '<?= CONTENT_CLASSIFICATORS_NAMEONE
    ?>',
            selectsite: '<?= CONTROL_USER_SELECTSITE
    ?>',
            allsite:    '<?= CONTROL_USER_SELECTSITEALL
    ?>',
            siteadmin: '<?= CONTROL_USER_RIGHTS_SITEADMIN
    ?>',
            subadmin : '<?= CONTROL_USER_RIGHTS_SUBDIVISIONADMIN
    ?>',
            ccadmin: '<?= CONTROL_USER_RIGHTS_SUBCLASSADMINS
    ?>',
            site : '<?= SECTION_INDEX_MENU_SITE
    ?>',
            sub : '<?= CONTROL_CONTENT_SUBDIVISION_FUNCS_SECTION
    ?>',
            cc : '<?= CONTROL_USER_FUNCS_CLASSINSECTION
    ?>',
            item : '<?= CONTROL_USER_RIGHTS_ITEM
    ?>',
            selectitem : '<?= CONTROL_USER_RIGHTS_SELECT_ITEM
    ?>',
            load : '<?= CONTROL_USER_RIGHTS_LOAD
    ?>',
            mailer : '<?= NETCAT_MODULE_SUBSCRIBE_MAILER ?>'
        }

                        </script>





                        <form action='<?= $action ?>' method='post' name='admin' id='admin'>
                            <input name='phase' value='<?= $phase ?>' type='hidden'>
                            <input type='hidden' name='UserID' value='<?= $UserID ?>'>
                            <input type='hidden' name='PermissionGroupID' value='<?= $PermissionGroupID ?>'>

                            <br>
                            <table border='0' width='75%' align='left' style='margin-left: 20px'>
                                <tr><td width='30%' valign='top'>

                                        <fieldset><legend><?= CONTROL_USER_RIGHTS_TYPE_OF_RIGHT
    ?></legend>
                                            <? if ($perm->isDirector()): ?>
                                                <?= nc_admin_radio_simple('AdminType', DIRECTOR, CONTROL_USER_RIGHTS_DIRECTOR, $AdminType == DIRECTOR, 'dir', "onclick='nc_user_obj.setType(7)'") ?>
                                                <br>
                                            <? endif; ?>
    <? $disabled = $perm->isSupervisor() ? '' : ' disabled'; ?>
                                            <?= nc_admin_radio_simple('AdminType', SUPERVISOR, CONTROL_USER_RIGHTS_SUPERVISOR, $AdminType == SUPERVISOR, 'sv', "onclick='nc_user_obj.setType(6)'" . $disabled) ?>
                                            <br>
                                            <div style='height: 6px'></div>
                                                <?= nc_admin_radio_simple('AdminType', EDITOR, CONTROL_USER_RIGHTS_EDITOR, $AdminType == EDITOR, 'man', "onclick='nc_user_obj.setType(5)'" . $disabled) ?>
                                            <br>
                                            <nobr>
    <?= nc_admin_radio_simple('AdminType', MODERATOR, CONTROL_USER_RIGHTS_MODERATOR, $AdminType == MODERATOR, 'mod', "onclick='nc_user_obj.setType(12)'" . $disabled) ?>
                                                <br>
                                            </nobr>
                                            <nobr>
    <?= nc_admin_radio_simple('AdminType', DEVELOPER, CONTROL_USER_RIGHTS_CLASSIFICATORADMIN, $AdminType == DEVELOPER, 'devel', "onclick='nc_user_obj.setType(14)'" . $disabled) ?>
                                                <br>
                                            </nobr>

                                            <?php if ($module_subscriber == 2) : ?>
                                                <div style='height: 6px'></div>
                                                <?= nc_admin_radio_simple('AdminType', SUBSCRIBER, CONTROL_USER_RIGHTS_SUBSCRIBER, $AdminType == SUBSCRIBER, 'subscriber', "onclick='nc_user_obj.setType(30)'" . $disabled) ?>
                                                <br>
                                            <?php endif; ?>

                                            <div style='height: 6px'></div>
                                            <?= nc_admin_radio_simple('AdminType', BAN, CONTROL_USER_RIGHTS_BAN, $AdminType == BAN, 'ban', "onclick='nc_user_obj.setType(20)'" . $disabled) ?>
                                            <br>
                                            <div style='height: 6px'></div>
    <?= nc_admin_radio_simple('AdminType', GUEST, CONTROL_USER_RIGHTS_GUESTONE, $AdminType == GUEST, 'guest', "onclick='nc_user_obj.setType(8)'" . $disabled) ?>
                                            <br>
                                            <br><br><br><br><br><br><br>
                                        </fieldset>

                                    </td><td valign='top'>

                                        <div id='div_livetime' name='div_livetime' style='display: none'>
                                            <fieldset><legend><?= CONTROL_USER_RIGHTS_LIVETIME ?></legend>
                                                <?= nc_admin_radio_simple('unlimit', 1, CONTROL_USER_RIGHTS_UNLIMITED, $unlimit, '', "onclick='nc_user_obj.disable_livetime(1)'") ?>
                                                <br>
                                                <?= nc_admin_radio_simple('unlimit', 0, CONTROL_USER_RIGHTS_LIMITED, !$unlimit, '', "onclick='nc_user_obj.disable_livetime(0)'") ?>
                                                <br>
                                                <div name='div_time' id='div_time' style='min-width:350px'>
                                                    <br><?= CONTROL_USER_RIGHTS_STARTING_OPERATIONS ?>:<br>
                                                    <table border='0' cellpadding='2' cellspacing='0'><tr><td>
                                                                <?= nc_admin_radio_simple('start_time', 0, CONTROL_USER_RIGHTS_NOW, !$start_time, 'start_now', "onclick='nc_user_obj.setStartType(0)'") ?>
                                                            </td><td colspan='4'></td></tr><tr><td>
                                                                <?= nc_admin_radio_simple('start_time', 1, CONTROL_USER_RIGHTS_ACROSS . "&nbsp;&nbsp;", $start_time == 1, 'start_across', "onclick='nc_user_obj.setStartType(1)'") ?>
                                                            </td><td>
                                                                <?= nc_admin_input_simple('across_start', $across_start, 2, '', "id='across_start' maxlength='2'") ?>
                                                            </td><td>
    <?= nc_admin_select_simple('', 'across_start_type', array(CONTROL_USER_RIGHTS_ACROSS_MINUTES, CONTROL_USER_RIGHTS_ACROSS_HOURS, CONTROL_USER_RIGHTS_ACROSS_DAYS, CONTROL_USER_RIGHTS_ACROSS_MONTHS), $across_start_type, "id='across_start_type'")
    ?>
                                                            </td><td colspan='2'></td></tr><tr><td>

                                                                <?= nc_admin_radio_simple('start_time', 2, '', $start_time == 2, 'start_define', "onclick='nc_user_obj.setStartType(2)'") ?>
                                                                <?= nc_admin_input_simple('start_day', '', 2, '', "maxlength='2' id='start_day'") ?>
                                                            </td><td>
                                                                <?= nc_admin_input_simple('start_month', '', 2, '', "maxlength='2' id='start_month'") ?>
                                                            </td><td>
                                                                <?= nc_admin_input_simple('start_year', '', 4, '', "maxlength='4' id='start_year'") ?>
                                                            </td><td>
                                                                <?= nc_admin_input_simple('start_hour', '', 2, '', "maxlength='2' id='start_hour'") ?>
                                                                <b> :</b></td><td>
    <?= nc_admin_input_simple('start_minute', '', 2, '', "maxlength='2' id='start_minute'") ?>
                                                            </td></tr></table>

                                                    <br><?= CONTROL_USER_RIGHTS_FINISHING_OPERATIONS ?>:<br>
                                                    <table border='0' cellpadding='2' cellspacing='0'><tr><td colspan='5'>
                                                                <?= nc_admin_radio_simple('end_time', 0, CONTROL_USER_RIGHTS_NONLIMITED, !$end_time, 'end_now', "onclick='nc_user_obj.setEndType(0)'") ?>
                                                            </td></tr><tr><td>
                                                                <?= nc_admin_radio_simple('end_time', 1, CONTROL_USER_RIGHTS_ACROSS, !$end_time, 'end_across', "onclick='nc_user_obj.setEndType(1)'") ?>
                                                            </td><td>
                                                                <?= nc_admin_input_simple('across_end', $across_end, 2, '', "id='across_end' maxlength='2'") ?>
                                                            </td><td>
                                                                <?=
                                                                nc_admin_select_simple('', 'across_end_type', array(CONTROL_USER_RIGHTS_ACROSS_MINUTES, CONTROL_USER_RIGHTS_ACROSS_HOURS, CONTROL_USER_RIGHTS_ACROSS_DAYS, CONTROL_USER_RIGHTS_ACROSS_MONTHS), $across_end_type, "id='across_end_type'")
                                                                ?>
                                                            </td><td colspan='2'></td></tr><tr><td>

                                                                <?= nc_admin_radio_simple('end_time', 2, '', $end_time == 2, 'end_define', "onclick='nc_user_obj.setEndType(2)'")
                                                                ?>
                                                                <?= nc_admin_input_simple('end_day', '', 2, '', "maxlength='2' id='end_day'")
                                                                ?>
                                                            </td><td>
                                                                <?= nc_admin_input_simple('end_month', '', 2, '', "maxlength='2' id='end_month'")
                                                                ?>
                                                            </td><td>
                                                                <?= nc_admin_input_simple('end_year', '', 4, '', "maxlength='4' id='end_year'")
                                                                ?>
                                                            </td><td>
                                                                <?= nc_admin_input_simple('end_hour', '', 2, '', "maxlength='2' id='end_hour'") ?>
                                                                <b> :</b></td><td>
    <?= nc_admin_input_simple('end_minute', '', 2, '', "maxlength='2' id='end_minute'") ?>
                                                            </td></tr></table>
                                                </div>
                                            </fieldset>
                                        </div>

                                    </td></tr><tr><td colspan='2'>

                                        <div name='div_main_right' id='div_main_right' style='display: none'>
                                            <fieldset><legend><?= CONTROL_USER_RIGHTS_RIGHT ?></legend>
                                                <div name='userperm' id='userperm' style='display: none'><br>
                                                    <?= nc_admin_checkbox_simple('user_add', 1, CONTROL_USER_RIGHTS_CONTROL_ADD) ?><br>
    <?= nc_admin_checkbox_simple('user_edit', 1, CONTROL_USER_RIGHTS_CONTROL_EDIT) ?><br>
    <?= nc_admin_checkbox_simple('user_del', 1, CONTROL_USER_RIGHTS_CONTROL_DELETE) ?><br>
                                                </div>

                                                <br>

                                                <table id='tbl_item' name='tbl_item' cellpadding='4' cellspacing='1' width='75%' bgcolor='#CCCCCC'>
                                                    <tbody></tbody>
                                                </table>

                                                <div name='div_perm' id='div_perm' style='display: none'><br>
                                                    <?= nc_admin_checkbox_simple('Read', 1, CONTROL_CLASS_ACTIONS_VIEW, false, 'l01')
                                                    ?><br>
                                                    <? if (nc_module_check_by_keyword("comments")): ?>
                                                        <?= nc_admin_checkbox_simple('Comment', 1, CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_COMMENT, false, 'l07') ?><br>
                                                    <? endif; ?>
                                                    <?= nc_admin_checkbox_simple('Add', 1, CONTROL_CONTENT_CATALOUGE_ADD, false, 'l02') ?><br>
                                                    <?= nc_admin_checkbox_simple('Edit', 1, CONTROL_CLASS_ACTIONS_EDIT, false, 'l03') ?><br>
                                                    <?= nc_admin_checkbox_simple('Check', 1, CONTROL_CLASS_ACTIONS_CHECKED, false, 'l031') ?><br>
                                                    <?= nc_admin_checkbox_simple('Delete', 1, CONTROL_CLASS_ACTIONS_DELETE, false, 'l032') ?><br>
                                                    <? if ($module_subscriber == 1): ?>
                                                        <?= nc_admin_checkbox_simple('Subscribe', 1, CONTROL_CLASS_ACTIONS_MAIL, false, 'l04') ?><br>
                                                    <? endif; ?>
    <?= nc_admin_checkbox_simple('Moderate', 1, CONTROL_CLASS_ACTIONS_MODERATE, false, 'l05', "onclick='nc_user_obj.handler_checkbox(5)'") ?><br>
    <?= nc_admin_checkbox_simple('Administer', 1, CONTROL_CLASS_ACTIONS_ADMIN, false, 'l06', "onclick='nc_user_obj.handler_checkbox(6)'") ?><br>
                                                </div>

                                                <div name='div_perm_ban' id='div_perm_ban' style='display: none'><br>
                                                    <?= nc_admin_checkbox_simple('Read', 1, CONTROL_CLASS_ACTIONS_VIEW, false, 'l1') ?><br>
                                                    <? if (nc_module_check_by_keyword("comments")): ?>
                                                        <?= nc_admin_checkbox_simple('Comment', 1, CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_COMMENT, false, 'l7') ?><br>
                                                    <? endif; ?>
                                                    <?= nc_admin_checkbox_simple('Add', 1, CONTROL_CONTENT_CATALOUGE_ADD, false, 'l2') ?><br>
                                                    <?= nc_admin_checkbox_simple('Edit', 1, CONTROL_CLASS_ACTIONS_EDIT, false, 'l3') ?><br>
                                                    <?= nc_admin_checkbox_simple('Check', 1, CONTROL_CLASS_ACTIONS_CHECKED, false, 'l31') ?><br>
                                                    <?= nc_admin_checkbox_simple('Delete', 1, CONTROL_CLASS_ACTIONS_DELETE, false, 'l32') ?><br>
                                                    <? if (nc_module_check_by_keyword("subscriber", false)): ?>
        <?= nc_admin_checkbox_simple('Subscribe', 1, CONTROL_CLASS_ACTIONS_MAIL, false, 'l4') ?><br>
    <? endif; ?>
                                                </div>

                                                <div name='div_perm_classificator' id='div_perm_classificator' style='display: none'><br>
                                                    <?= nc_admin_checkbox_simple('Edit', 1, CONTROL_CLASS_ACTIONS_EDIT, false, 'l1') ?><br>
    <?= nc_admin_checkbox_simple('Add', 1, CONTROL_CONTENT_CATALOUGE_ADD, false, 'l2') ?><br>
    <?= nc_admin_checkbox_simple('Moderate', 1, CONTROL_CLASS_ACTIONS_MODERATE, false, 'l3') ?><br>
                                                </div>

                                                <div name='div_perm_subscriber' id='div_perm_subscriber' style='display: none'><br>
                                                </div>

                                            </fieldset>
                                        </div>




                                    </td></tr><tr><td colspan='2'>
                                        <div name="div_help" id="div_help" style='display: none'>
                                            <fieldset><legend><?= CONTROL_USER_RIGHTS_CONTROL_HELP ?></legend>
                                                <div id='help' name = 'help' style='padding: 10px'></div>
                                            </fieldset>
                                        </div>
                                    </td></tr></table>
    <?php echo $nc_core->token->get_input(); ?>
                        </form>

                        <script type="text/javascript" src='<?= $ADMIN_PATH ?>js/user.js'></script>
                        <script type="text/javascript">
                            nc_user_obj = new nc_user_perm();
                            nc_user_obj.setType(<?= $AdminType ?>);
                            nc_user_obj.disable_livetime(<?= $unlimit ?>);
                            nc_user_obj.setStartType(<?= $start_time ?>);
                            nc_user_obj.setEndType(<?= $end_time ?>);
                        </script>
                        <?
                        if ($UserID) {
                            $UI_CONFIG->headerText = CONTROL_USER_RIGHT_ADDPERM . " " . addslashes($user_login);
                        } else {
                            $UI_CONFIG->headerText = CONTROL_USER_RIGHT_ADDPERM_GROUP . " " . GetPermissionGroupName($PermissionGroupID);
                        }

                        $UI_CONFIG->actionButtons[] = array("id" => "addright",
                                "caption" => CONTROL_USER_RIGHT_ADDNEWRIGHTS,
                                "action" => "mainView.submitIframeForm()");
                    }

                    /**
                     * Add permission
                     *
                     * @return int code error (0 - ok)
                     */
                    function AddPermissionComleted() {
                        global $db, $perm, $AUTH_USER_ID;

                        $params = array('UserID', 'PermissionGroupID',
                                'AdminType', 'unlimit', 'start_time', 'start_day', 'start_month', 'start_year', 'start_hour', 'start_minute',
                                'end_time', 'end_day', 'end_month', 'end_year', 'end_hour', 'end_minute',
                                'item', 'site_list', 'sub_list', 'subclass_list', 'dev_classificator', 'mailer_id',
                                'Read', 'Comment', 'Add', 'Edit', 'Check', 'Delete', 'Moderate', 'Administer',
                                'across_start', 'across_start_type', 'across_end', 'across_end_type' , 'user_add', 'user_edit', 'user_del');
                        foreach ($_POST AS $key => $val) {
                            if (!in_array($key, $params))
                                continue;
                            $$key = $val;
                        }

                        $day_now = date("d");
                        $month_now = date("m");
                        $year_now = date("Y");
                        $hour_now = date("H");
                        $minute_now = date("i");
                        $temp = 0;

                        //Если не выбран пользователь или группа
                        if (!($UserID + $PermissionGroupID))
                            return 3;

                        if ($UserID && $UserID == $AUTH_USER_ID && $AdminType == GUEST)
                            return 15;

                        // Правами группы управляет только супервизор и выше
                        if ($PermissionGroupID && !$perm->isSupervisor())
                            return 2;

                        // Проверка, редактируемый пользователь не стоит ли выше по правам
                        if ($UserID && in_array($UserID, (array) $perm->GetUserWithMoreRights()))
                            return 2;

                        # Определение времени жизни
                        if ($unlimit) { //бессрочно
                            $start_perm = '';
                            $end_perm = '';
                        } else {
                            // начало действия
                            switch ($start_time) {
                                case 0: // now
                                    $start_perm = '';
                                    $temp_start = mktime($hour_now, $minute_now, 0, $month_now, $day_now, $year_now);
                                    break;
                                case 1: // через ...
                                    $across_start = intval($across_start);
                                    if ($across_start <= 0)
                                        return 11;

                                    $across_start_type = intval($across_start_type);
                                    $start_day = $day_now;
                                    $start_month = $month_now;
                                    $start_year = $year_now;
                                    $start_hour = $hour_now;
                                    $start_minute = $minute_now;
                                    if ($across_start_type == 3)
                                        $start_month += $across_start;
                                    else if ($across_start_type == 2)
                                        $start_day += $across_start;
                                    else if ($across_start_type == 1)
                                        $start_hour += $across_start;
                                    else
                                        $start_minute += $across_start;

                                    $temp_start = mktime($start_hour, $start_minute, 0, $start_month, $start_day, $start_year);
                                    $start_perm = strftime('%Y-%m-%d %H:%M:%S', $temp_start);
                                    break;
                                case 2: // точное время
                                    $temp_start = mktime(intval($start_hour), intval($start_minute), 0, intval($start_month), intval($start_day), intval($start_year));
                                    $start_perm = strftime('%Y-%m-%d %H:%M:%S', $temp_start);
                                    break;
                            }
                            // конец времени действия
                            switch ($end_time) {
                                case 0: // бессрочно
                                    $end_perm = '';
                                    $temp_end = mktime($hour_now, $minute_now, 0, $month_now, $day_now, $year_now + 10);
                                    break;
                                case 1: // через ...
                                    $across_end = intval($across_end);
                                    if ($across_end <= 0)
                                        return 12;

                                    $across_end_type = intval($across_end_type);
                                    $end_day = $day_now;
                                    $end_month = $month_now;
                                    $end_year = $year_now;
                                    $end_hour = $hour_now;
                                    $end_minute = $minute_now;
                                    if ($across_end_type == 3)
                                        $end_month += $across_end;
                                    else if ($across_end_type == 2)
                                        $end_day += $across_end;
                                    else if ($across_end_type == 1)
                                        $end_hour += $across_end;
                                    else
                                        $end_minute += $across_end;

                                    $temp_end = mktime($end_hour, $end_minute, 0, $end_month, $end_day, $end_year);
                                    $end_perm = strftime('%Y-%m-%d %H:%M:%S', $temp_end);
                                    break;
                                case 2: // точное время
                                    $temp_end = mktime(intval($end_hour), intval($end_minute), 0, intval($end_month), intval($end_day), intval($end_year));
                                    $end_perm = strftime('%Y-%m-%d %H:%M:%S', $temp_end);
                                    break;
                            }

                            if ($temp_end < $temp_start)
                                return 13;
                        }


                        $insert = "INSERT INTO `Permission` (`User_ID`, `PermissionGroup_ID`, `AdminType`, `Catalogue_ID`, `PermissionSet`, `PermissionBegin`, `PermissionEnd`)";
                        $insert.= " VALUES ('" . intval($UserID) . "', '" . intval($PermissionGroupID) . "',";


                        switch ($AdminType) {
                            case DIRECTOR:
                                // Директора может добавить только директор
                                if (!$perm->isDirector())
                                    return 2;
                                $insert.= DIRECTOR . ",0,0, ";
                                break;

                            case SUPERVISOR:
                                if (!$perm->isSupervisor())
                                    return 2;
                                $insert.= SUPERVISOR . ",0,0, ";
                                break;

                            case EDITOR:
                                if (!$perm->isSupervisor())
                                    return 2;

                                // Чего именно редактор -  сайта, раздела иил сс?
                                if ($item == 1) { // редактор сайта
                                    $at = CATALOGUE_ADMIN;
                                    $c_id = intval($site_list);
                                } else if ($item == 2) { // редактор раздела
                                    $at = SUBDIVISION_ADMIN;
                                    $c_id = intval($sub_list);

                                    if (!$site_list)
                                        return 7;
                                    if (!$c_id)
                                        return 8;
                                }
                                else if ($item == 3) { // редактор сс
                                    $at = SUB_CLASS_ADMIN;
                                    $c_id = intval($subclass_list);

                                    if (!$site_list)
                                        return 7;
                                    if (!$sub_list)
                                        return 8;
                                    if (!$c_id)
                                        return 9;
                                }
                                else {
                                    return 3;
                                }

                                // возможности

                                $PermissionSet = intval($Read * MASK_READ + $Add * MASK_ADD + $Edit * MASK_EDIT + $Delete * MASK_DELETE + $Check * MASK_CHECKED + $Comment * MASK_COMMENT +
                                        $Subscribe * MASK_SUBSCRIBE + $Moderate * MASK_MODERATE + $Administer * MASK_ADMIN);
                                if (!$PermissionSet)
                                    return 6;


                                $insert.= $at . ",'" . $c_id . "','" . $PermissionSet . "',";

                                break;

                            case MODERATOR:
                                if (!$perm->isSupervisor())
                                    return 2;

                                $PermissionSet = intval(MASK_READ + $user_add * MASK_ADD + $user_edit * MASK_EDIT + $user_del * MASK_MODERATE); // 1 == view -  default
                                if (!$PermissionSet)
                                    return 6;

                                $insert.= MODERATOR . ",'0','" . $PermissionSet . "',";
                                break;

                            case DEVELOPER:
                                if (!$perm->isSupervisor())
                                    return 2;

                                $PermissionSet = intval(MASK_READ + $Edit * MASK_EDIT + $Add * MASK_ADD + $Moderate * MASK_MODERATE); // 1 == view -  default
                                $c_id = intval($dev_classificator);

                                $insert.= CLASSIFICATOR_ADMIN . ",'" . $c_id . "','" . $PermissionSet . "',";
                                break;

                            case SUBSCRIBER:
                                $mailer_id = intval($mailer_id);
                                if (!$mailer_id)
                                    return 14;
                                $insert.= SUBSCRIBER . ", " . $mailer_id . ",0, ";
                                break;

                            case BAN:
                                if ($item == 1) { //  сайт
                                    $at = BAN_SITE;
                                    $c_id = intval($site_list);
                                } else if ($item == 2) { //раздел
                                    $at = BAN_SUB;
                                    $c_id = intval($sub_list);

                                    if (!$site_list)
                                        return 7;
                                    if (!$c_id)
                                        return 8;
                                }
                                else if ($item == 3) { //  сс
                                    $at = BAN_CC;
                                    $c_id = intval($subclass_list);

                                    if (!$site_list)
                                        return 7;
                                    if (!$sub_list)
                                        return 8;
                                    if (!$c_id)
                                        return 9;
                                }
                                else {
                                    return 3;
                                }

                                $PermissionSet = intval($Read * MASK_READ + $Comment * MASK_COMMENT + $Add * MASK_ADD + $Edit * MASK_EDIT + $Delete * MASK_DELETE + $Check * MASK_CHECKED + $Subscribe * MASK_SUBSCRIBE);
                                if (!$PermissionSet)
                                    return 6;

                                $insert.= $at . ",'" . $c_id . "','" . $PermissionSet . "',";
                                break;

                            case GUEST:
                                if (!$perm->isSupervisor())
                                    return 2;
                                $insert.= GUEST . ",'0','0',";
                                break;

                            default:
                                return 10;
                        }

                        $insert .= $start_perm ? "'" . $start_perm . "', " : "NULL, ";
                        $insert .= $end_perm ? "'" . $end_perm . "' )" : "NULL )";
                        if (!$db->query($insert))
                            return 5;

                        return 0;
                    }

                    function UpdatePermission() {
                        global $db;
                        global $perm, $UserID, $PermissionGroupID;

                        $UserID = (int) $UserID;
                        $PermissionGroupID = (int) $PermissionGroupID;

                        $Result = $db->query("UPDATE `Permission` SET `PermissionSet` = 0 WHERE `User_ID` = '" . $UserID . "' AND `PermissionGroup_ID` = '" . $PermissionGroupID . "'");

                        $error = false;
                        foreach ($_POST AS $key => $val) {
                            if (strncmp($key, "Delete", 6) != 0) {
                                if (substr($key, 0, 12) == "PermissionID") {
                                    $PermissionID = substr($key, 12, strlen($key) - 14);
                                    // TODO: для одного PermissionId нужен только один запрос
                                    // сейчас их может быть до 6
                                    $Result = $db->query("UPDATE `Permission` SET `PermissionSet` = (PermissionSet | " . intval($val) . ") WHERE `Permission_ID` = '" . intval($PermissionID) . "'");
                                }
                                continue;
                            }

                            $nonDeletableID = $db->get_var("SELECT `Permission_ID` FROM `Permission` WHERE `User_ID` = '" . $perm->GetUserID() . "' AND `AdminType` = '" . DIRECTOR . "'");

                            $nonDeletableID+= 0;

                            $db->query("DELETE FROM `Permission` WHERE `Permission_ID` = '" . intval($val) . "' AND `Permission_ID` <> '" . $nonDeletableID . "'");

                            if ($db->captured_errors) {
                                nc_print_status(CONTROL_USER_RIGHTS_ERR_CANTREMPRIV, 'error');
                                $db->vardump($db->captured_errors);
                                $error = true;
                            }
                        }

                        if (!$error)
                            nc_print_status(CONTROL_USER_RIGHTS_UPDATED_OK, 'ok');

                        return;
                    }

                    function ChangeCheckedForUser($UserID) {
                        global $nc_core, $db;
                        global $AUTH_USER_ID, $AUTH_USER_GROUP, $perm;

                        $UserID = intval($UserID);
                        if (!$UserID || ($AUTH_USER_ID == $UserID))
                            return false;

                        $CheckActionTemplate = $db->get_var("SELECT `CheckActionTemplate` FROM `Class` WHERE `System_Table_ID`=3 AND `ClassTemplate` = 0");
                        $cur_value = $db->get_var("SELECT `Checked` FROM `User` WHERE `User_ID` = '" . $UserID . "'");

                        $nc_core->event->execute($cur_value ? nc_Event::BEFORE_USER_ENABLED : nc_Event::BEFORE_USER_DISABLED, $UserID);
                        $db->query("UPDATE `User` SET `Checked` = 1 - `Checked` WHERE `User_ID` ='" . $UserID . "'");
                        $nc_core->event->execute($cur_value ? nc_Event::AFTER_USER_ENABLED : nc_Event::AFTER_USER_DISABLED, $UserID);

                        if ($CheckActionTemplate)
                            eval(nc_check_eval("echo \"" . $CheckActionTemplate . "\";"));
                    }

                    /**
                     * Confirm: all values in array must be +3 or 4
                     *
                     * @param array array_data
                     * @return bool
                     */
                    function ConfirmInputData($array_data) {
                        foreach ((array) $array_data as $v) {
                            if (!preg_match("/^(()|([+]?[0-9]+))$/", $v))
                                return 0;
                        }
                        return 1;
                    }

                    function nc_user_move_to_group_form() {
                        global $db, $UI_CONFIG;
                        $users = array();
                        foreach ($_POST as $k => $v) {
                            if (substr($k, 0, 4) == 'User')
                                $users[] = intval($v);
                        }
                        $users = array_unique($users);

                        if (empty($users))
                            return false;

                        $groups = $db->get_results("SELECT `PermissionGroup_ID` as id, `PermissionGroup_Name` as name FROM `PermissionGroup` ORDER BY 1 ", ARRAY_A);

                        echo "<form action='index.php' method='post' >\r\n";
                        echo "<input type='hidden' name='phase' value='18' />\r\n";
                        foreach ($users as $v)
                            echo"<input type='hidden' name='User" . $v . "' value='" . $v . "' />\r\n";

                        echo "<div style='padding-bottom: 10px;'>" . CONTROL_USER_SELECT_GROUP_TO_MOVE . "</div>";
                        foreach ($groups as $v) {
                            $id = $v['id'];
                            echo nc_admin_checkbox_simple("Group" . $id, $id, $v['name']) . '<br />';
                        }
                        echo "</form>\r\n";



                        return true;
                    }

                    function nc_user_move_to_group_completed() {

                        $nc_core = nc_Core::get_object();
                        $db = $nc_core->db;

                        $users = array();
                        $groups = array();

                        foreach ($_POST as $k => $v) {
                            if (substr($k, 0, 4) == 'User')
                                $users[] = $v;
                            if (substr($k, 0, 5) == 'Group')
                                $groups[] = $v;
                        }

                        $users = array_unique($users);
                        $groups = array_unique($groups);

                        if (empty($users) || empty($groups))
                            return false;

                        $users = array_map('intval', $users);
                        $groups = array_map('intval', $groups);

                        $main_group = min($groups);

                        $nc_core->event->execute(nc_Event::BEFORE_USER_UPDATED, $users);

                        $db->query("DELETE FROM `User_Group` WHERE `User_ID` IN (" . join(',', $users) . ") ");

                        $values = array();
                        foreach ($users as $user_id)
                            foreach ($groups as $group_id)
                                $values[] = "('" . $user_id . "','" . $group_id . "')";

                        $db->query("INSERT INTO `User_Group` (`User_ID`, `PermissionGroup_ID`) VALUES " . join(',', $values) . " ");
                        $db->query("UPDATE `User` SET `PermissionGroup_ID` = '" . $main_group . "' WHERE `User_ID` IN (" . join(',', $users) . ") ");

                        $nc_core->event->execute(nc_Event::AFTER_USER_UPDATED, $users);
                        return true;
                    }

                    /**
                     * UI_CONFIG_USER
                     */
                    class ui_config_user extends ui_config {

                        /**
                         * конструктор
                         */
                        function ui_config_user() {
                            $this->treeMode = "users";
                            $this->treeSelectedNode = "users";
                        }

                        /**
                         * cтраница "список пользователей"
                         */
                        function user_list_page() {

                            $this->headerText = CONTROL_USER_USERSANDRIGHTS;
                            $this->headerImage = "i_usergroup_big.gif";
                            $this->tabs = array(
                                    array('id' => 'list',
                                            'caption' => SECTION_CONTROL_USER,
                                            'location' => "user.list()"),
                            );
                            $this->activeTab = 'list'; // i.e. "tab1"
                            $this->locationHash = "user.list()";
                        }

                        /**
                         * страница изменения данных пользователя
                         * (вкладки: пользователь; права)
                         * @param integer user id
                         * @param string login
                         * @param string active tab ('user'; 'rights')
                         * @param string optinal - адрес страницы (если пустой - сформировать)
                         */
                        function user_page($user_id, $user_login, $active_tab, $hash = "") {
                            global $perm, $nc_core;
                            $db = $nc_core->db;
                            $MODULE_VARS = $nc_core->modules->get_module_vars();

                            $this->headerText = CONTROL_USER . ' ' . addslashes($user_login);
                            $this->headerImage = "i_user_big.gif";
                            // пользователь, права
                            $this->tabs[0] = array('id' => 'edit',
                                    'caption' => CONTROL_USER,
                                    'location' => "user.edit($user_id)");
                            if ($perm->isAccess(NC_PERM_ITEM_USER, NC_PERM_ACTION_RIGHT, $user_id)) {

                                $this->tabs[1] = array('id' => 'rights',
                                        'caption' => SECTION_INDEX_USER_RIGHTS_RIGHTS,
                                        'location' => "user.rights($user_id)");
                                if (nc_module_check_by_keyword('subscriber', 0))
                                    $this->tabs[2] = array('id' => 'subscribers',
                                            'caption' => SECTION_INDEX_USER_SUBSCRIBERS,
                                            'location' => "user.subscribers($user_id)");
                            }

                            $this->activeTab = $active_tab;
                            if (!$perm->isAccess(NC_PERM_ITEM_USER, NC_PERM_ACTION_RIGHT, $user_id))
                                $this->activeTab = 'user';
                            $this->locationHash = ($hash ? $hash : "user.$active_tab($user_id)");
                        }

                        function new_user_page() {
                            $this->headerText = CONTROL_USER_REG;
                            $this->headerImage = "i_user_big.gif";
                            $this->tabs = array(
                                    array('id' => 'add',
                                            'caption' => CONTROL_USER_REG,
                                            'location' => "user.add()")
                            );
                            $this->activeTab = "add";
                            $this->locationHash = "user.add()";
                        }

                    }

                    /**
                     * UI_CONFIG_USERGROUP
                     */
                    class ui_config_usergroup extends ui_config {

                        /**
                         * конструктор
                         */
                        function ui_config_usergroup() {
                            $this->treeMode = "users";
                        }

                        /**
                         * cтраница "список пользователей"
                         */
                        function usergroup_list_page() {

                            $this->headerText = CONTROL_USER_GROUPS;
                            $this->headerImage = "i_usergroup_big.gif";
                            $this->tabs = array(
                                    array('id' => 'list',
                                            'caption' => CONTROL_USER_GROUPS,
                                            'location' => "usergroup.list()"),
                            );
                            $this->activeTab = 'list';
                            $this->locationHash = "usergroup.list()";
                            $this->treeSelectedNode = "usergroup";
                        }

                        /**
                         * страница изменения данных пользователя
                         * (вкладки: пользователь; права)
                         * @param integer id
                         * @param string name
                         * @param string active tab ('group'; 'rights')
                         * @param string optinal - адрес страницы (если пустой - сформировать)
                         */
                        function group_page($group_id, $group_name, $active_tab, $hash = "") {
                            $this->headerText = addslashes($group_name);
                            $this->headerImage = "i_usergroup_big.gif";
                            // пользователь, права
                            $this->tabs = array(
                                    array('id' => 'edit',
                                            'caption' => CONTROL_USER_MAIL_GROUP,
                                            'location' => "usergroup.edit($group_id)"),
                                    array('id' => 'rights',
                                            'caption' => SECTION_INDEX_USER_RIGHTS_RIGHTS,
                                            'location' => "usergroup.rights($group_id)")
                            );
                            $this->activeTab = $active_tab;
                            $this->locationHash = ($hash ? $hash : "usergroup.$active_tab($group_id)");
                            $this->treeSelectedNode = "usergroup-{$group_id}";
                        }

                        function new_group_page() {
                            $this->headerText = CONTROL_USER_GROUPS_ADD;
                            $this->headerImage = "i_usergroup_big.gif";
                            $this->tabs = array(
                                    array('id' => 'add',
                                            'caption' => CONTROL_USER_GROUPS_ADD,
                                            'location' => "usergroup.add()")
                            );
                            $this->activeTab = "add";
                            $this->locationHash = "usergroup.add()";
                            $this->treeSelectedNode = "usergroup";
                        }

                    }
