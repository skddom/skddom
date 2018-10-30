<?

/**
 * Обёртка для «старых» скриптов для различных «редакций» модуля:
 *   ../forms.php
 *   ../import.php
 *   ../sources.php
 */

require("./no_header.inc.php");

$script = nc_core::get_object()->input->fetch_get_post('script');
if (!preg_match('/^\w+$/', $script)) { $script = "admin"; }

while (@ob_end_clean());

if (file_exists("../$script.php")) {
    header("Location: " . rtrim(nc_module_path("netshop/$script.php"), "/\\"));
}
else {
    header("Location: " . nc_module_path("netshop/admin") . "?controller=$script");
}