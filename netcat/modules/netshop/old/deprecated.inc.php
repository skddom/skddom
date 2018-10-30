<?php

$NETSHOP_DEPRECATED_FOLDER = dirname(__FILE__);

// Старые переменные -----------------------------------------------------------
$GLOBALS["NETSHOP_MONTHS_GENETIVE"] = explode("/", NETCAT_MODULE_NETSHOP_MONTHS_GENITIVE);

// Старые функции --------------------------------------------------------------
require_once "$NETSHOP_DEPRECATED_FOLDER/kxlib.php";

function netshop_language_count($num, $words) {
    if (!is_array($words)) { $words = nc_preg_split("/,\s*/", $words); }
    return nc_netshop_word_form($num, $words[0], $words[1], $words[2]);
}

function netshop_language_in_words($sum, $currency_names="", $decimal_part_names="") {
    return nc_netshop_amount_in_full($sum, $currency_names, $decimal_part_names);
}

// Старые классы ---------------------------------------------------------------
require_once nc_core('ADMIN_FOLDER') . "modules/ui.php";
require_once "$NETSHOP_DEPRECATED_FOLDER/ui_config.php";

require_once "$NETSHOP_DEPRECATED_FOLDER/NetShopDeprecated.class.php";
require_once "$NETSHOP_DEPRECATED_FOLDER/nc_mod_netshop.class.php";

class NetShop extends nc_mod_netshop {}

unset($NETSHOP_DEPRECATED_FOLDER);