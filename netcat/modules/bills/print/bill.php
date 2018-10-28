<?php

/**
 * Формирование PDF счёта
 * (используется контроллер nc_bills_bills_admin_controller, действие print)
 * 
 * Параметры:
 *   - hash
 *   - signed
 */

define('NC_BILLS_CONTROLLER_TYPE', 'bills');
require_once './make_pdf.inc.php';