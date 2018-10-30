<?php

/**
 * Формирование PDF акта
 * (используется контроллер nc_bills_acts_admin_controller, действие print)

 * Параметры:
 *   - hash
 *   - signed
 */

define('NC_BILLS_CONTROLLER_TYPE', 'acts');
require_once './make_pdf.inc.php';