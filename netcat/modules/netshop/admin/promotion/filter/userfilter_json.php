<?php

/**
 * Input:
 *   - conditions [string]
 *   - catalogue_id [int]
 *
 * Output: json object
 *   - count: number of users matching the conditions
 *   - user_ids: string with comma-separated user ids
 *   - user_email_field: name of the field in the `User` table which contains user email address
 *
 */

require '../../no_header.inc.php';

/** @var nc_input $input */
$input = nc_core('input');

$conditions = $input->fetch_get_post('conditions');
$catalogue_id = $input->fetch_get_post('catalogue_id');

$user_email_field = $input->fetch_get_post('user_email_field');
$has_email_condition = preg_match("/^\w+$/", $user_email_field)
    ? "(User.`$user_email_field` IS NOT NULL AND User.`$user_email_field` != '')"
    : "";

$filter = new nc_netshop_promotion_userfilter($conditions, $catalogue_id);
$user_data = $filter->filter("User.User_ID", $has_email_condition);

// php5.5: array_column
$user_ids = array();
foreach ($user_data as $u)  { $user_ids[] = $u['User_ID']; }

$result = array(
    "count" => count($user_data),
    "user_ids" => join(",", $user_ids)
);

echo nc_array_json($result);