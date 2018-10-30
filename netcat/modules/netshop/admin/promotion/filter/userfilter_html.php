<?php

require '../../no_header.inc.php';

/** @var nc_input $input */
$input = nc_core('input');
/** @var nc_ui $tpl */
$tpl = nc_core('ui');

$conditions = $input->fetch_get_post('conditions');
$catalogue_id = $input->fetch_get_post('catalogue_id');

$user_email_field = $input->fetch_get_post('user_email_field');
$has_email_condition = preg_match("/^\w+$/", $user_email_field)
    ? "(User.`$user_email_field` IS NOT NULL AND User.`$user_email_field` != '')"
    : "";

$filter = new nc_netshop_promotion_userfilter($conditions, $catalogue_id);
$user_data = $filter->filter("*", $has_email_condition);

if (!$user_data) {
    echo "<div class='no_results'>",
            $tpl->alert->info(NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_NO_USERS),
         "</div>";
}
else {

    // Using nc_ui in this case is too memory-consuming
    /* @todo decide if some kind of js template is worthwhile (it will make the page size ~4 times smaller!) */
    $path = nc_core()->HTTP_ROOT_PATH . "admin/";
    echo "<table class='nc-table nc--wide nc--striped nc--hovered'>\n";
//         "<colgroup><col width='96%'></colgroup>\n";

    foreach ($user_data as $i => $row) {
        $name = "[$row[Login]] $row[ForumName]";
//        echo "<tr><td><span class='essence-id'>$row[User_ID].</span> <span class='essence-caption'>$name</span>",
        echo "<tr><td>$row[User_ID]. $name",
             "</td><td class='nc--compact'>",
                 // @todo add link to the user orders
                "<a href='$path#user.edit($row[User_ID])' target='_blank'><i class='nc-icon nc--user'>&nbsp;</i></a>",
             "</td></tr>\n";

        if ($i % 2000 == 0) { ob_flush(); }
    }
    echo '</table>';

}