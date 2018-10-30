<?php

/* $Id: personal.inc.php 5946 2012-01-17 10:44:36Z denis $ */
/* ФАЙЛ ДЛЯ Personal
  @todo удалить в полнофункциональной версии
 */

class Permission_Personal {

    private $desc;

    public function __construct() {
        $addonText = "<br><br>Для получения возможности пользоваться этой функции необходимо обновить Вашу редакцию до уровня <b>Standard</b> и выше.<br>
Об условиях перехода с версии <b>Personal</b> до более старших версий читайте информацию на <a target='_blank' href='http://www.netcat.ru'>официальном сайте</a> разработчика NetCat.";

        $this->desc[NC_PERM_SQL][0]['text'] = "Пункт «Командная строка SQL» предназначен для формирования SQL-запросов к системе.".$addonText;
        $this->desc[NC_PERM_SQL][0]['name'] = "Командная строка SQL";

        $this->desc[NC_PERM_REDIRECT][0]['text'] = "Пункт «Переадресации» предназначен для настройки переадресаций между страницами и разделами веб-сайта.".$addonText;
        $this->desc[NC_PERM_REDIRECT][0]['name'] = "Переадресации";

        $this->desc[NC_PERM_SEO][NC_PERM_ACTION_VIEW]['text'] = "Пункт «SEO-анализ» предназначен для отслеживания основных параметров сайта в поисковых системах (ТИЦ, PageRank, ссылки и пр.).".$addonText;
        $this->desc[NC_PERM_SEO][NC_PERM_ACTION_VIEW]['name'] = "SEO-анализ";

        $this->desc[NC_PERM_MODULE][0]['text'] = "Модули расширяют возможности системы.".$addonText;
        $this->desc[NC_PERM_MODULE][0]['name'] = "Модули";

        $this->desc[NC_PERM_CRON][0]['text'] = "Пункт «Управление задачами» предназначен для запуска задач по расписанию.".$addonText;
        $this->desc[NC_PERM_CRON][0]['name'] = "Управление задачами";

        $this->desc[NC_PERM_ITEM_GROUP][0]['text'] = "Пункт «Группы пользователей» предназначен для создания групп пользователей, имеющих в системе различные права.".$addonText;
        $this->desc[NC_PERM_ITEM_GROUP][0]['name'] = "Группы пользователей";

        $this->desc[NC_PERM_ITEM_GROUP][NC_PERM_ACTION_MAIL]['text'] = "Пункт «Рассылка по базе» предназначен для рассылки электронных писем по базе пользователей сайта.".$addonText;
        $this->desc[NC_PERM_ITEM_GROUP][NC_PERM_ACTION_MAIL]['name'] = "Рассылка по базе";




        $this->desc[NC_PERM_TOOLSHTML][0]['text'] = "Пункт «HTML-редактор» предназначен для настройки встроенного HTML-редактора.".$addonText;
        $this->desc[NC_PERM_TOOLSHTML][0]['name'] = "HTML-редактор";

        $this->desc[NC_PERM_SYSTABLE][0]['text'] = "Системные таблицы недоступны";
        $this->desc[NC_PERM_SYSTABLE][0]['name'] = "Системные таблицы";

        $this->desc[NC_PERM_FIELD][0]['text'] = "Пункт «Системные таблицы» предназначен для работы с полями системных таблиц.".$addonText;
        $this->desc[NC_PERM_FIELD][0]['name'] = "Системные таблицы";

        $this->desc[NC_PERM_ITEM_USER][NC_PERM_ACTION_ADD]['text'] = "Пункт «Регистрация пользователя» предназначен для добавления в систему нового пользователя.".$addonText;
        $this->desc[NC_PERM_ITEM_USER][NC_PERM_ACTION_ADD]['name'] = "Регистрация пользователя";

        $this->desc[NC_PERM_ITEM_USER][NC_PERM_ACTION_RIGHT]['text'] = "Пункт «Права пользователя» предназначен для добавления и изменения прав пользователя.".$addonText;
        $this->desc[NC_PERM_ITEM_USER][NC_PERM_ACTION_RIGHT]['name'] = "Права пользователя";

        $this->desc[NC_PERM_ITEM_SITE][NC_PERM_ACTION_ADD]['text'] = "Пункт «Добавить сайт» предназначен для добавления нового сайта в систему.".$addonText;
        $this->desc[NC_PERM_ITEM_SITE][NC_PERM_ACTION_ADD]['name'] = "Добавить сайт";

        $this->desc[NC_PERM_CLASS][NC_PERM_ACTION_ADD]['text'] = "В системе уже установлено максимально число компонентов - 20.".$addonText;
        $this->desc[NC_PERM_CLASS][NC_PERM_ACTION_ADD]['name'] = "Добавить компонент";


        //$this->desc[NC_PERM_CLASS][NC_PERM_ACTION_WIZARDCLASS]['text'] = "Мастер позволяет легко и быстро создавать новые компоненты.".$addonText;
        // $this->desc[NC_PERM_CLASS][NC_PERM_ACTION_WIZARDCLASS]['name'] = "Мастер создания компонетов";

        $this->desc[NC_PERM_ITEM_SUB][NC_PERM_ACTION_SUBCLASSADD]['text'] = "Возможен только один компонент в разделе.".$addonText;
        $this->desc[NC_PERM_ITEM_SUB][NC_PERM_ACTION_SUBCLASSADD]['name'] = "Добавление компонента";

        $this->desc[NC_PERM_ITEM_SUB][NC_PERM_ACTION_ADD]['text'] = "Превышен уровень вложности разделов".$addonText;
        $this->desc[NC_PERM_ITEM_SUB][NC_PERM_ACTION_ADD]['name'] = "Добавление раздела";
    }

    /* Большинство методо не нужно для версии personal
      они сразу возвращают 0 или 1 */

    public function GetUserID() {
        return 1;
    }

    public function isInsideAdmin() {
        return 1;
    }

    public function isDirector() {
        return 1;
    }

    public function isGuest() {
        return 0;
    }

    public function isSupervisor() {
        return 1;
    }

    public function isCatalogue($CatalogueID, $mask) {
        return 1;
    }

    public function isAllSiteAdmin() {
        return 1;
    }

    public function isCatalogueAdmin($CatalogueID) {
        return 1;
    }

    public function IsAnyCatalogueAdmin() {
        return 1;
    }

    public function isSubdivision($SubdivisionID, $mask, $checkParents=true) {
        return 1;
    }

    public function isSubdivisionAdmin($SubdivisionID, $checkParents = true) {
        return 1;
    }

    public function IsAnySubdivisionAdmin() {
        return 1;
    }

    public function isSubClass($SubClassID, $mask, $useParentSubTree = false) {
        return 1;
    }

    public function isSubClassAdmin($SubClassID) {
        return 1;
    }

    public function isAnySubClassAdmin() {
        return 1;
    }

    public function isInstanceModeratorAdmin($instance_type) {
        return 1;
    }

    public function isAnyClassificator() {
        return 1;
    }

    public function isAccessDevelopment() {
        return 1;
    }

    public function isAccessSiteMap() {
        return 1;
    }

    public function isDirectAccessClassificator($action = '', $id = 0) {
        return 0;
    }

    public function ExitIfGuest() {
        return 1;
    }

    public function isBanned($cc_env, $action) {
        return 0;
    }

    public function GetAllowSite($mask = MASK_ADMIN, $withSubClass = true) {
        return array(1);
    }

    public function GetAllowSub($CatalogueID, $mask = MASK_ADMIN, $withParent = true, $withChild = true, $withSubClass = true) {
        return;
    }

    public function isUserMenuShow() {
        return 1;
    }

    public function accessToFCKEditor() {
        return 1;
    }

    public function accessToTrash() {
        return 1;
    }

    public function GetUserWithMoreRights() {
        return array(0);
    }

    public function GetDirectorsGroup() {
        return array(1);
    }

    private static function GetPermNameByID($id) {
        return CONTROL_USER_RIGHTS_DIRECTOR;
    }

    public function listItems($instance_type, $type_of_access=null) {
        return array(0);
    }

    public function ExitIfNotSupervisor() {
        return;
    }

    public function GetMaxPerm() {
        return BEGINHTML_PERM_DIRECTOR;
    }

    public function getLogin() {
        global $db;
        global $AUTHORIZE_BY;

        $select = "SELECT `".$AUTHORIZE_BY."` From `User` WHERE User_ID='1'";
        return $db->get_var($select);
    }

    public function ExitIfNotAccess($instance_type, $action = "", $id = 0, $text = NETCAT_MODERATION_ERROR_NORIGHTS, $posting = 1) {
        global $UI_CONFIG;

        if ($this->isAccess($instance_type, $action, $id, $posting)) return 1;

        if (!$text) $text = "В редакции Personal эта возможность недоступна";
        // Права нет - на выход
        nc_print_status($text, 'error');

        if (!$action || ($instance_type == NC_PERM_ITEM_GROUP && $action != NC_PERM_ACTION_MAIL))
                $action = 0;

        print "<div>".$this->desc[$instance_type][$action]['text']."</div>";

        $UI_CONFIG = new ui_config();
        $UI_CONFIG->headerText = $this->desc[$instance_type][$action]['name'];
        $UI_CONFIG->headerImage = "";
        $UI_CONFIG->tabs = array(array("id" => "personal", "caption" => $this->desc[$instance_type][$action]['name'], "location" => "personal"));
        $UI_CONFIG->activeTab = "personal";
        //$UI_CONFIG ->locationHash = "personal";

        EndHtml ();
        exit();

        return 1;
    }

    /**
     * Если ли доступ
     *
     * @param int тип сущности, константа NC_PERM_*    см. файл const.inc.php
     * @paramint действие, константа NC_PERM_ACTION_*
     * @param mixed id or array with id
     * @param будет ли запись в БД
     * @return bool
     */
    public function isAccess($instance_type, $action = '', $id = 0, $posting = 1) {
        global $db;

        switch ($instance_type) {

            //Catalogue
            case NC_PERM_ITEM_SITE:
                if ($action == NC_PERM_ACTION_ADD) return false;
                return true;
                break;


            // Subdivision
            case NC_PERM_ITEM_SUB:
                if ($action == NC_PERM_ACTION_SUBCLASSADD) {
                    if ($db->get_var("SELECT COUNT(Sub_Class_ID) FROM `Sub_Class` WHERE `Subdivision_ID` = '".intval($id)."'"))
                            return false;
                }
                if ($action == NC_PERM_ACTION_ADD) {
                    if ($db->get_var("SELECT `Parent_Sub_ID` FROM `Subdivision` WHERE `Subdivision_ID` = '".intval($id)."'"))
                            return false;
                }
                return true;
                break;


            // Sub class
            case NC_PERM_ITEM_CC:
                return true;

                break;

            // User
            case NC_PERM_ITEM_USER:
                if ($db->get_var("SELECT COUNT(User_ID) as c FROM `User`") > 1) {
                    nc_print_status('В системе обнаружено больше одного пользователя', 'error');
                    EndHtml();
                    exit();
                }
                switch ($action) {
                    case NC_PERM_ACTION_ADD:   #  Добавить пользователя
                    case NC_PERM_ACTION_RIGHT:
                    case NC_PERM_ACTION_DEL:
                        return false;
                }

                return true;
                break;

            case NC_PERM_CLASS:
                $count = $db->get_var("SELECT COUNT(Class_ID) as c FROM `Class` WHERE `ClassTemplate` = '0'");
                if ($count > 20) {
                    nc_print_status('В системе обнаружено больше 20ти компонентов', 'error');
                    EndHtml();
                    exit();
                }

                if ($action == NC_PERM_ACTION_ADD) {
                    if ($count + 1 > 20) return false;
                }

                //if ( $action == NC_PERM_ACTION_WIZARDCLASS ) return false;

                return true;
                break;

            case NC_PERM_SYSTABLE:
                if ($action == NC_PERM_ACTION_LIST) return true;
                return false;
                break;

            case NC_PERM_FIELD:
                if ($id) return false;
                return true;
                break;

            // classificator
            case NC_PERM_CLASSIFICATOR:
            case NC_PERM_FAVORITE:
            case NC_PERM_PATCH:
            case NC_PERM_REPORT:
            case NC_PERM_TEMPLATE:


                return true;
                break;


            case NC_PERM_SQL:
            case NC_PERM_MODULE:
            case NC_PERM_CRON:
            case NC_PERM_ITEM_GROUP:
            case NC_PERM_TOOLSHTML:
            case NC_PERM_SEO:
            case NC_PERM_REDIRECT:
                return false;
                break;
        }


        return true;
    }

}
?>
