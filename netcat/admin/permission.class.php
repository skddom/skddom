<?php

/* $Id: permission.class.php 7691 2012-07-17 05:46:42Z alive $ */

class Permission {

    private $_UserID;
    private $_PermissionGroupID;
    // массив с группами либо индетификатор группы
    private $_InsideAdminAccess;
    //берется значение из поля таблицы User
    private $db;
    private $_director, $_supervisor, $_guest;
    // 1 - если есть соответствующее право, 0 нету
    private $_catalogue, $_sub, $_cc;
    // Массивы, индекс - айди сущности, значение - permission set
    private $_allSite;
    // Значение - это permission set для всех сайтов
    private $_classificator;
    // Массив, нулевой элемент - для всех списков
    private $_countPerm;
    private $_user;
    // значение - permission set
    private $_subscriber;
    // Массив, индекс - номер рассылки.
    private $_banCat, $_banSub, $_banCC;
    // аналагочино, как у $_catalogue, $_sub....
    private $_fckeditor;
    private $_ckeditor;
    private $instanceTypes = array(
        'catalogue'   => CATALOGUE_ADMIN,
        'subdivision' => SUBDIVISION_ADMIN,
        'subclass'    => SUB_CLASS_ADMIN
    );

    /**
     * @param int $UserID
     * @param int $PermissionGroupID
     * @param array $user_result массив с результатами выборки
     */
    public function __construct($UserID, $PermissionGroupID = 0, $user_result = null) {
        global $db;

        $this->db = $db;
        $this->_UserID = (int)$UserID;
        $this->_fckeditor = false;
        $this->_ckeditor = false;

        // Если есть user_result - то данные можно взять оттуда
        if ($UserID && $user_result) {
            $this->_InsideAdminAccess = $user_result[0]['InsideAdminAccess'];
            foreach ($user_result as $row) {
                $this->_PermissionGroupID[] = $row['PermissionGroups_ID'];
            }
        } elseif ($UserID && !$user_result) { // инчае запросом
            $this->_InsideAdminAccess = $this->db->get_var("SELECT `InsideAdminAccess` FROM `User` WHERE User_ID='" . $this->_UserID . "'");
            $this->_PermissionGroupID = nc_usergroup_get_group_by_user($this->_UserID);
        } else { // идет работа только с группой
            $this->_PermissionGroupID = array((int)$PermissionGroupID);
        }

        $this->_countPerm = 0;

        $query_where_user = $this->_UserID > 0 ? " `User_ID`='{$UserID}' OR " : '';
        $query_where_permission_group_ids = implode(',', $this->_PermissionGroupID);
        $SelectPerm = "SELECT `AdminType`, `Catalogue_ID`, `PermissionSet`
                       FROM `Permission`
                       WHERE (
                           ({$query_where_user} `PermissionGroup_ID` IN ({$query_where_permission_group_ids}))
                           AND (
                               (`PermissionBegin` IS NULL OR UNIX_TIMESTAMP(`PermissionBegin`) <= UNIX_TIMESTAMP())
                               AND (`PermissionEnd` IS NULL OR UNIX_TIMESTAMP(`PermissionEnd`) >= UNIX_TIMESTAMP())
                           )
                       )";

        $PermResult = $this->db->get_results($SelectPerm, ARRAY_A);

        if (!empty($PermResult)) {
            foreach ($PermResult as $PermArray) {
                switch ($PermArray['AdminType']) {
                    case DIRECTOR:
                        $this->_director = 1;
                        $this->_fckeditor = true;
                        $this->_ckeditor = true;
                        break;
                    case SUPERVISOR:
                        $this->_supervisor = 1;
                        $this->_fckeditor = true;
                        $this->_ckeditor = true;
                        break;
                    case GUEST:
                        $this->_guest = 1;
                        $this->_fckeditor = false;
                        $this->_ckeditor = false;
                        break;
                    case CATALOGUE_ADMIN:
                        $this->_catalogue[$PermArray['Catalogue_ID']] |= $PermArray['PermissionSet'];
                        if ($PermArray['PermissionSet'] & (MASK_ADD | MASK_EDIT | MASK_MODERATE)) {
                            $this->_fckeditor = true;
                            $this->_ckeditor = true;
                        }
                        break;
                    case SUBDIVISION_ADMIN:
                        $this->_sub[$PermArray['Catalogue_ID']] |= $PermArray['PermissionSet'];
                        if ($PermArray['PermissionSet'] & (MASK_ADD | MASK_EDIT | MASK_MODERATE)) {
                            $this->_fckeditor = true;
                            $this->_ckeditor = true;
                        }
                        break;
                    case SUB_CLASS_ADMIN:
                        $this->_cc[$PermArray['Catalogue_ID']] |= $PermArray['PermissionSet'];
                        if ($PermArray['PermissionSet'] & (MASK_ADD | MASK_EDIT | MASK_MODERATE)) {
                            $this->_fckeditor = true;
                            $this->_ckeditor = true;
                        }
                        break;
                    case MODERATOR: // управляет пользователями
                        $this->_user |= $PermArray['PermissionSet'];
                        break;
                    case CLASSIFICATOR_ADMIN:
                        $this->_classificator[$PermArray['Catalogue_ID']] |= $PermArray['PermissionSet'];
                        break;
                    case SUBSCRIBER:
                        $this->_subscriber[$PermArray['Catalogue_ID']] |= 1;
                        break;
                    case BAN_SITE: // ограничение в правах
                        $this->_banCat[$PermArray['Catalogue_ID']] |= $PermArray['PermissionSet'];
                        break;
                    case BAN_SUB:
                        $this->_banSub[$PermArray['Catalogue_ID']] |= $PermArray['PermissionSet'];
                        break;
                    case BAN_CC:
                        $this->_banCC[$PermArray['Catalogue_ID']] |= $PermArray['PermissionSet'];
                        break;
                }
                $this->_countPerm++;
            }
        }

        // нулевое значенеи Catalogue_ID означает все сайты
        if ($this->_catalogue[0] >= 0) {
            $this->_allSite = $this->_catalogue[0];
        }

        // привязка системных событий
        $nc_core = nc_Core::get_object();
        $nc_core->event->bind($this, array(nc_Event::AFTER_SITE_DELETED => 'dropCataloguePerm'));
        $nc_core->event->bind($this, array(nc_Event::AFTER_SUBDIVISION_DELETED => 'dropSubdivisionPerm'));
        $nc_core->event->bind($this, array(nc_Event::AFTER_COMPONENT_DELETED => 'dropSubClassPerm'));
        $nc_core->event->bind($this, array(nc_Event::AFTER_USER_DELETED => 'dropUserPerm'));
    }

    /**
     * Возвращает user id
     *
     * @return int usr id
     */
    public function GetUserID() {
        return $this->_UserID;
    }

    /**
     * Возвращает значение поля, по которрому происходит авторизация
     *
     * @return string login
     */
    public function getLogin() {
        static $cache = array();

        if (!array_key_exists($this->_UserID, $cache)) {
            global $AUTHORIZE_BY;
            $select = "SELECT `{$AUTHORIZE_BY}` From `User` WHERE User_ID='{$this->_UserID}'";
            $cache[$this->_UserID] = $this->db->get_var($select);
        }

        return $cache[$this->_UserID];
    }

    /**
     * Return, has user access to admin?
     * Имеет доступ,если стоит галочка доступ в зону администрирования, либо
     * если он директор, супервизор, администратор/модератор сайта/раздела/компонента,
     * разработчик или модератор(упрвавляющий пользователями)
     * @return bool
     */
    public function isInsideAdmin() {
        return (
            $this->_InsideAdminAccess
            || $this->isAccessDevelopment()
            || $this->_user
            || $this->isInstanceModeratorAdmin('site')
            || $this->isInstanceModeratorAdmin('sub')
            || $this->isInstanceModeratorAdmin('cc')
        );
    }

    /**
     * Is user a director?
     *
     * @return bool
     */
    public function isDirector() {
        return $this->_director;
    }

    /**
     * Is user a guest?
     *
     * @return bool
     */
    public function isGuest() {
        if ($this->isSupervisor()) {
            return false;
        }
        return $this->_guest;
    }

    /**
     * is user a supervisor or director
     *
     * @return bool
     */
    public function isSupervisor() {
        return ($this->_director || $this->_supervisor);
    }

    /**
     * Всегда возвращает 0, только для совместимости со старым классом
     *
     * @return int 0
     */
    public function isManager() {
        return 0;
    }

    /* --- Catalogue --- */

    /**
     * Есть ли доступ к сайту с задаными правами
     *
     * @param int $CatalogueID , 0 - all catalogue
     * @param int $mask
     * @return bool
     */
    public function isCatalogue($CatalogueID, $mask) {
        if ($this->_director || $this->_supervisor || ($this->_allSite & $mask)) {
            return 1;
        }
        return ($this->_catalogue[$CatalogueID] & $mask);
    }

    /**
     * Является ли пользователь администратором всех сайтов
     *
     * @return bool
     */
    public function isAllSiteAdmin() {
        if ($this->_director || $this->_supervisor) {
            return 1;
        }
        return $this->_allSite & MASK_ADMIN;
    }

    /**
     * Является ли пользователь администратором сайта
     *
     * @param int $CatalogueID ID
     * @return bool
     */
    public function isCatalogueAdmin($CatalogueID) {
        return $this->isCatalogue($CatalogueID, MASK_ADMIN);
    }

    /**
     * Является ли пользователь админом хотя бы одного сайта?
     *
     * @return bool
     */
    public function IsAnyCatalogueAdmin() {
        if ($this->_director || $this->_supervisor || ($this->_allSite & MASK_ADMIN)) {
            return 1;
        }

        foreach ((array)$this->_catalogue as $v) {
            if ($v & MASK_ADMIN) {
                return true;
            }
        }

        return false;
    }

    /* --- Subdivision --- */

    /**
     * Есть ли право к данному разделу с данной маской
     *
     * @param int $SubdivisionID ID
     * @param int $mask
     * @param bool $checkParents - учитывать родителей (т.е. если пользвователь имеет данное право к родителю, то вернет true)
     * @return bool
     * @throws Exception
     */
    public function isSubdivision($SubdivisionID, $mask, $checkParents = true) {
        global $nc_core;
        $SubdivisionID = (int)$SubdivisionID;
        if ($this->_director || $this->_supervisor || ($this->_allSite & $mask)) {
            return true;
        }
        $catalogue_id = $nc_core->subdivision->get_by_id($SubdivisionID, 'Catalogue_ID');
        if ($this->_catalogue[$catalogue_id] & $mask) {
            return true;
        }

        if (empty($this->_sub)) {
            return false;
        }

        if ($this->_sub[$SubdivisionID] & $mask) {
            return true;
        }

        // Проверить на доступ к родителям
        if ($checkParents) {
            // Получим все id родителей данного саба
            $parent_sub = (array)$this->db->get_col(
                "SELECT ths.Subdivision_ID
                 FROM Subdivision as allowed, Subdivision as ths
                 WHERE allowed.Subdivision_ID = '{$SubdivisionID}'
                 AND (allowed.Hidden_URL LIKE CONCAT(ths.Hidden_URL, '%'))
                 AND allowed.Catalogue_ID = ths.Catalogue_ID"
            );


            foreach ($parent_sub as $sub_id) { // Проверка на доступ к родителям раздела
                if ($this->_sub[$sub_id] & $mask) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Пользователь админ раздела?
     *
     * @param int $SubdivisionID ID
     * @param bool $checkParents учитывать родителей?
     * @return bool
     * @throws Exception
     */
    public function isSubdivisionAdmin($SubdivisionID, $checkParents = true) {
        return $this->isSubdivision($SubdivisionID, MASK_ADMIN, $checkParents);
    }

    /**
     * Пользователь админ какого либо раздела?
     *
     * @return bool
     */
    public function IsAnySubdivisionAdmin() {
        if ($this->_director || $this->_supervisor || ($this->_allSite & MASK_ADMIN) || $this->IsAnyCatalogueAdmin()) {
            return true;
        }
        foreach ((array)$this->_sub as $v) {
            if ($v & MASK_ADMIN) {
                return true;
            }
        }
        return false;
    }

    /* --- Sub Class --- */

    /**
     * Проверка заданного доступа к заданому сс
     *
     * @param int $SubClassID
     * @param int $mask
     * @param bool $useParentSubTree разрешить использование $parent_sub_tree;
     * @return bool
     * @throws Exception
     */
    public function isSubClass($SubClassID, $mask, $useParentSubTree = false) {
        global $parent_sub_tree, $inside_admin;

        if ($this->_director || $this->_supervisor || ($this->_allSite & $mask)) {
            return true;
        }
        if ($this->_cc[$SubClassID] & $mask) {
            return true;
        }

        // Проверим на доступ к разделам и сайту
        // Если не в админке и есть parent_sub_tree - то данные возьмем оттуда
        // если объявлена константа NC_ADDED_BY_AJAX - то данные нужно взять из базы, т.к.
        // в этом случаее parent_sub_tree содержит не те данные

        $all_sub = array();

        if (!$inside_admin && is_array($parent_sub_tree) && !defined('NC_ADDED_BY_AJAX') && $useParentSubTree) {
            for ($i = 0; $i < count($parent_sub_tree) - 1; $i++) {
                $all_sub[] = $parent_sub_tree[$i]['Subdivision_ID'];
            }
            $site = $parent_sub_tree[0]['Catalogue_ID'];

            // все разделы и сайт известны - проверим доступ к ним
            if ($this->_catalogue[$site] & $mask) {
                return true;
            }
            foreach ($all_sub as $v) {
                if ($this->_sub[$v] & $mask) {
                    return true;
                }
            }
        } else {
            if ($this->isSubdivision(GetSubdivisionBySubClass($SubClassID), $mask)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Пользователь является ли админом данного сс
     *
     * @param int $SubClassID
     * @return bool
     * @throws Exception
     */
    public function isSubClassAdmin($SubClassID) {
        return $this->isSubClass($SubClassID, MASK_ADMIN);
    }

    /**
     * Пользователь является ли админом какого-либо сс
     *
     * @return bool
     */
    public function isAnySubClassAdmin() {
        if ($this->_director || $this->_supervisor || ($this->_allSite & (MASK_ADMIN || MASK_MODERATE)) || $this->IsAnySubdivisionAdmin()) {
            return true;
        }
        foreach ((array)$this->_cc as $v) {
            if ($v & MASK_ADMIN) {
                return true;
            }
        }
        // Проверка на админа сайта прошла в ф-ции IsAnySubdivisionAdmin
        return false;
    }

    /**
     * Является ли пользователь модератором или администратором хотя бы одной сущности $instance_type?
     * @param string $instance_type тип сущности - 'catalogue', 'subdivision', 'subclass'
     * @return boolean
     */
    public function isInstanceModeratorAdmin($instance_type) {

        if ($this->_director || $this->_supervisor || $this->_allSite & (MASK_ADMIN | MASK_MODERATE)) {
            return true;
        }

        $values = array(); // сюда будут собираться id сущностей (сайтов, разделов либо сс)

        switch ($instance_type) {
            case 'catalogue':
            case 'site':
                $values = array_values((array)$this->_catalogue);
                break;
            case 'subdivision':
            case 'sub':
                $values = array_values((array)$this->_sub);
                break;
            case 'subclass':
            case 'cc':
                $values = array_values((array)$this->_cc);
                break;
        }

        foreach ($values as $v) {
            if ($v & (MASK_ADMIN | MASK_MODERATE)) {
                return true;
            }
        }

        return false;
    }

    public function accessToFCKEditor() {
        return ($this->_fckeditor && !$this->_guest);
    }

    // Доступ к корзине удаленных объектов
    public function accessToTrash() {
        return ($this->_fckeditor && !$this->_guest);
    }

    public function accessToCKEditor() {
        return ($this->_ckeditor && !$this->_guest);
    }

    /**
     * Есть ли у пользователя доступ хотя бы к одному системному списку
     * @return bool
     */
    public function isAnyClassificator() {
        if ($this->_director || $this->_supervisor) {
            return true;
        }
        return !empty($this->_classificator);
    }

    /**
     * Есть ли у пользователя доступ к дереву разработчика
     * @return bool
     */
    public function isAccessDevelopment() {
        return $this->_director || $this->_supervisor || $this->_guest || $this->isAnyClassificator();
    }

    /**
     * Есть ли у пользователя доступ к карте сайта
     * @return bool
     */
    public function isAccessSiteMap() {
        return $this->_director || $this->_supervisor || $this->_guest || !empty($this->_catalogue) || !empty($this->_sub) || !empty($this->_cc);
    }

    /**
     * Проверить по маске доступ к классификатору
     * Нужно для системных списков, когда право нужно задавать явно
     * @param string $action
     * @param int $id classificator id
     * @return bool
     */
    public function isDirectAccessClassificator($action = '', $id = 0) {
        $mask = 0;

        switch ($action) { // По действию узнаем маску
            case NC_PERM_ACTION_VIEW:
                $mask = MASK_READ;
                break;
            case NC_PERM_ACTION_ADDELEMENT:
                $mask = MASK_ADD;
                break;
            case NC_PERM_ACTION_EDIT:
                $mask = MASK_EDIT;
                break;
            case NC_PERM_ACTION_ADMIN:
                $mask = MASK_MODERATE;
                break;
        }
        return ($this->_classificator[$id] & $mask);
    }

    /**
     * Если у пользователя право подписки на рассылку с номером mailer_id
     * @param int $mailer_id номер рассылки
     * @return bool
     */
    public function isSubscriber($mailer_id) {
        return $this->_director || $this->_supervisor || $this->_subscriber[$mailer_id];
    }

    public function ExitIfGuest() {
        global $NO_RIGHTS_MESSAGE;
        if ($this->_guest) {
            nc_print_status($NO_RIGHTS_MESSAGE, 'error');
            EndHtml();
            exit;
        }

        return null;
    }

    public function ExitIfNotSupervisor() {
        global $NO_RIGHTS_MESSAGE;
        if (!$this->_supervisor && !$this->_director) {
            nc_print_status($NO_RIGHTS_MESSAGE, 'error');
            EndHtml();
            exit;
        }

        return null;
    }

    /**
     * Массив с ID объектов, которые пользователь может модерировать
     * @param string $instance_type тип сущности - 'catalogue', 'subdivision', 'subclass'
     * @param string $type_of_access тип доступа - 'moderator', 'admin'  (по умолчанию - оба)
     * @return array
     */
    public function listItems($instance_type, $type_of_access = null) {

        $instance_num = $this->instanceTypes[strtolower($instance_type)];
        if (!$instance_num) {
            trigger_error("Unknown instance type '$instance_type'", E_USER_WARNING);
            return array();
        }

        $rights_mask = MASK_MODERATE | MASK_ADMIN;
        switch (strtolower($type_of_access)) {
            case 'moderator':
                $rights_mask = MASK_MODERATE;
                break;
            case 'admin':
            case 'administrator':
                $rights_mask = MASK_ADMIN;
                break;
            case null:
                break;
            default:
                $rights_mask = $type_of_access;
                break;
        }

        $array = array();

        switch ($instance_type) {
            case 'catalogue' :
            case 'site':
                $array = (array)$this->_catalogue;
                break;
            case 'subdivision':
            case 'sub':
                $array = (array)$this->_sub;
                break;
            case 'subclass':
            case 'cc':
                $array = (array)$this->_cc;
                break;
        }

        $ret = array();

        foreach ($array as $k => $v) {
            if ($v & $rights_mask) {
                $ret[] = $k;
            }
        }

        return $ret;
    }

    /**
     * Выйти, если нет права
     *
     * @param string $instance_type тип сущности
     * @param string $action действие
     * @param int $id id
     * @param string $text текст выводимой в плашке
     * @param int $posting будет ли запись в БД
     * @return int 1
     * @throws Exception
     */
    public function ExitIfNotAccess($instance_type, $action = "", $id = 0, $text = NETCAT_MODERATION_ERROR_NORIGHTS, $posting = 1) {

        if ($this->_guest && !$posting) {
            return 1;
        }

        if ($this->_guest && $posting) {
            nc_print_status(NETCAT_MODERATION_ERROR_NORIGHTGUEST, 'error');
            EndHtml();
            exit();
        }

        if ($this->_director) {
            return 1;
        }

        if ($this->isAccess($instance_type, $action, $id, $posting)) {
            return 1;
        }

        if (!$text) {
            $text = NETCAT_MODERATION_ERROR_NORIGHTS;
        }

        // Права нет - на выход
        nc_print_status($text, 'error');
        EndHtml();
        exit();
    }

    /**
     * Если ли доступ
     *
     * @param string $instance_type тип сущности, константа NC_PERM_*    см. файл const.inc.php
     * @param string $action действие, константа NC_PERM_ACTION_*
     * @param mixed $id id or array with id
     * @param int $posting будет ли запись в БД
     * @return bool
     * @throws Exception
     */
    public function isAccess($instance_type, $action = '', $id = 0, $posting = 1) {
        global $nc_core;
        // гость может смотреть, но не может редактировать ничего
        if ($this->_guest && !$posting) {
            return true;
        }
        if ($this->_guest && $posting) {
            return false;
        }

        // Практически ко всем действиям супервизор имеет доступ
        // кроме управлние пользователями - надо проверять отдельно
        // чтобы он не мог редактировать диреторов
        if ($instance_type == NC_PERM_ITEM_USER || $instance_type == NC_PERM_ITEM_GROUP) {
            if ($this->_director) {
                return true;
            }
        } else {
            if ($this->_director || $this->_supervisor) {
                return true;
            }
        }


        switch ($instance_type) {

            // Catalogue
            case NC_PERM_ITEM_SITE:
                // все действия в админке с работой сайта доступны админу всех сайтов
                // плюс ему доступны viewall, add, del
                if ($this->_allSite & MASK_ADMIN) {
                    return true;
                }

                switch ($action) {
                    case NC_PERM_ACTION_ADMIN:    #
                    case NC_PERM_ACTION_INFO:     # Получить инфу по сайту
                    case NC_PERM_ACTION_EDIT:     # Изменить настроки сайта
                    case NC_PERM_ACTION_ADDSUB:   # Добавить раздел в корнень сайта
                    case NC_PERM_ACTION_DELSUB:   # Удалить корневой раздел
                        if ($this->_catalogue[$id] & MASK_ADMIN) {
                            return true;
                        }
                        break;
                }

                break;

            // Subdivision
            case NC_PERM_ITEM_SUB:
                // Действия доступны админам сайта(ов)
                if ($this->_allSite & MASK_ADMIN) {
                    return true;
                }
                $catalogue_id = $nc_core->subdivision->get_by_id($id, 'Catalogue_ID');
                if ($this->_catalogue[$catalogue_id] & MASK_ADMIN) {
                    return true;
                }

                switch ($action) {
                    case NC_PERM_ACTION_ADD:           # Добавить подраздел
                    case NC_PERM_ACTION_DEL:           # Удалить подраздел
                    case NC_PERM_ACTION_INFO:          # Получить инфу по разделу
                    case NC_PERM_ACTION_EDIT:          # Изменить раздел (настройки)
                    case NC_PERM_ACTION_SUBCLASSLIST:  # Получить список сс
                    case NC_PERM_ACTION_SUBCLASSDEL:   # Удалить сс в разделе
                    case NC_PERM_ACTION_SUBCLASSADD:   # Добавить
                        if ($this->isSubdivision($id, MASK_ADMIN)) {
                            return true;
                        }
                        break;
                    case NC_PERM_ACTION_LIST:           # Список разделов
                        if ($this->isSubdivision($id, MASK_ADMIN | MASK_MODERATE)) {
                            return true;
                        }
                        break;
                }
                break;

            // Sub class
            case NC_PERM_ITEM_CC:
                // Проверка, не является ли пользователем админом вышестоящей сущности
                if ($this->_allSite & MASK_ADMIN) {
                    return true;
                }
                $catalogue_id = $nc_core->subdivision->get_by_id($id[0], 'Catalogue_ID');
                if ($this->_catalogue[$catalogue_id] & MASK_ADMIN) {
                    return true;
                }
                if ($this->isSubdivision($id[0], MASK_ADMIN)) {
                    return true;
                }

                $id = (array)$id; // первый элемент - id раздела, второй - sub class id

                switch ($action) {
                    case NC_PERM_ACTION_ADMIN:      #
                    case NC_PERM_ACTION_EDIT:       # Изменить настроки компонента
                        if ($this->_cc[$id[1]] & MASK_ADMIN) {
                            return true;
                        }
                        break;
                    case NC_PERM_ACTION_INFO:       # Информация о компоненте
                        if ($this->_cc[$id[0]] & MASK_ADMIN) {
                            return true;
                        }
                        break;
                }

                break;

            // User
            case NC_PERM_ITEM_USER:
                // Получим всех пользователей, к которым данный пользователь доступа не имеет
                $not_allow_users = (array)$this->GetUserWithMoreRights();
                // узнаем, к этому пользователю есть доступ
                $not_allow_user = in_array($id, $not_allow_users);

                switch ($action) {
                    case NC_PERM_ACTION_ADD: #  Добавить пользователя
                        if ($this->_user & MASK_ADD || $this->_supervisor) {
                            return true;
                        }
                        break;
                    case NC_PERM_ACTION_LIST: # Просмотреть список пользователей
                        if ($this->_user & MASK_READ || $this->_supervisor) {
                            return true;
                        }
                        break;
                    case NC_PERM_ACTION_EDIT: # Редактирование пользователей
                        if ($not_allow_user) {
                            return false;
                        }
                        if ($this->_user & MASK_EDIT || $this->_supervisor) {
                            return true;
                        }
                        break;
                    case NC_PERM_ACTION_RIGHT: # Редактирование прав
                        if ($not_allow_user) {
                            return false;
                        }
                        if ($this->_user & MASK_EDIT || $this->_supervisor) {
                            return true;
                        }
                        break;
                    case NC_PERM_ACTION_DEL: # Удаление пользователя
                        if ($not_allow_user) {
                            return false;
                        }
                        if ($this->_user & MASK_MODERATE || $this->_supervisor) {
                            return true;
                        }
                        break;
                }

                break;

            //group
            case NC_PERM_ITEM_GROUP:
                // доступ толко у супервизора и диреткора
                if (!$this->_supervisor) {
                    return false;
                }
                // просматривать супервизор может
                if ($action == NC_PERM_ACTION_LIST || $action == NC_PERM_ACTION_MAIL) {
                    return true;
                }
                // а изменть только "недиректорсие" группы
                if (!in_array($id, $this->GetDirectorsGroup())) {
                    return true;
                }
                break;

            // classificator
            case NC_PERM_CLASSIFICATOR:
                switch ($action) {
                    case NC_PERM_ACTION_LIST: # Показать список классификаторов
                        if (!empty($this->_classificator)) {
                            return true;
                        }
                        break;
                    case NC_PERM_ACTION_ADD:  # Добавлять или удалять может только Модератор всех списков
                    case NC_PERM_ACTION_DEL:
                        // нулевой индес означает все списки
                        if ($this->_classificator[0] & MASK_MODERATE) {
                            return true;
                        }
                        break;
                    case NC_PERM_ACTION_VIEW: # Просмотреть определенный список

                        if ($this->_classificator[$id] & MASK_READ) {
                            return true;
                        }
                        if ($this->_classificator[0] & MASK_READ) {
                            return true;
                        }
                        break;
                    case NC_PERM_ACTION_ADMIN:  # Изменить приоритеты элементов, удалить элементы списка
                        if ($this->_classificator[$id] & MASK_MODERATE) {
                            return true;
                        }
                        if ($this->_classificator[0] & MASK_MODERATE) {
                            return true;
                        }
                        break;
                    case NC_PERM_ACTION_ADDELEMENT: # Добавить элемент в список
                        if ($this->_classificator[$id] & MASK_ADD) {
                            return true;
                        }
                        if ($this->_classificator[0] & MASK_ADD) {
                            return true;
                        }
                        break;
                    case NC_PERM_ACTION_EDIT: # Изменить  элемент в списке
                        if ($this->_classificator[$id] & MASK_EDIT) {
                            return true;
                        }
                        if ($this->_classificator[0] & MASK_EDIT) {
                            return true;
                        }
                        break;
                }
                break;

            case NC_PERM_FAVORITE:
                return $this->IsAnySubdivisionAdmin();
                break;

            // only supervisor and director
            case NC_PERM_SQL:
            case NC_PERM_CLASS:
            case NC_PERM_FIELD:
            case NC_PERM_SYSTABLE:
            case NC_PERM_MODULE:
            case NC_PERM_PATCH:
            case NC_PERM_REPORT:
            case NC_PERM_TEMPLATE:
            case NC_PERM_CRON:
            case NC_PERM_TOOLSHTML:
            case NC_PERM_SEO:
            case NC_PERM_REDIRECT:
                break;
        }


        return false;
    }

    /**
     * Проверка, не забанен ли пользователь для данного действия данного cc
     *
     * @param array $cc_env должен включать Sub_Class_ID, Subdivision_ID, Catalogue_ID
     * @param string $action
     * @return bool
     */
    public function isBanned($cc_env, $action) {
        global $parent_sub_tree, $delete, $checked;

        if (!$this->_banCC && !$this->_banSub && !$this->_banCat) {
            return false;
        }

        //По action определим маску
        switch ($action) {
            case 'read':
                $mask = MASK_READ;
                break;
            case 'add':
                $mask = MASK_ADD;
                break;

            case 'change':
                switch (true) {
                    case isset($delete):
                        $mask = MASK_DELETE;
                        break;
                    case isset($checked):
                        $mask = MASK_CHECKED;
                        break;
                    default:
                        $mask = MASK_EDIT;
                }
                break;

            case 'moderate':
                $mask = MASK_MODERATE;
                break;
            case 'subscribe':
                $mask = MASK_SUBSCRIBE;
                break;
            case 'comment':
                $mask = MASK_COMMENT;
                break;
            default:
                $mask = MASK_READ;
                break;
        }

        // Ограничение присвоили "прямо"
        if ($this->_banCC[$cc_env['Sub_Class_ID']] & $mask) {
            return true;
        }
        if ($this->_banSub[$cc_env['Subdivision_ID']] & $mask) {
            return true;
        }
        if ($this->_banCat[$cc_env['Catalogue_ID']] & $mask) {
            return true;
        }

        $parent_sub = array();

        // Так же надо проверить на ограничение родительский разделов
        // Если заполенен массив parent_sub_tree - то взять оттуда
        // иначе - из базы
        // Проверка на родительские разделы
        if (is_array($parent_sub_tree)) {
            // 0 и послдний элемент - это сам раздел и сайт соответсвенно
            for ($i = 1; $i < count($parent_sub_tree) - 1; $i++) {
                $parent_sub[] = $parent_sub_tree[$i]['Subdivision_ID'];
            }
        } else {
            $parent_sub = $this->db->get_col(
                "SELECT parent.`Subdivision_ID`
                 FROM `Subdivision` as parent, `Subdivision` as child
                 WHERE child.`Subdivision_ID` = '{$cc_env['Subdivision_ID']}'
                 AND child.`Hidden_URL` LIKE CONCAT(parent.`Hidden_URL`, '%')
                 AND parent.`Catalogue_ID` = child.`Catalogue_ID`"
            );
        }

        foreach ($parent_sub as $v) {
            if ($this->_banSub[$v] & $mask) {
                return true;
            }
        }

        // Проверрка, не забанене ли пользователь на всем сайте
        if ($this->_banCat[$cc_env['Catalogue_ID']] & $mask) {
            return true;  // на данном сайте
        }
        if ($this->_banCat[0] & $mask) {
            return true;  // на всех сайтах
        }


        return false;
    }

    /**
     * Return perm name
     * нужно для вывода в админку
     * @return int|string
     */
    public function GetMaxPerm() {
        if ($this->_director) {
            return BEGINHTML_PERM_DIRECTOR;
        }
        if ($this->_supervisor) {
            return BEGINHTML_PERM_SUPERVISOR;
        }
        if (!empty($this->_catalogue)) {
            return BEGINHTML_PERM_CATALOGUEADMIN;
        }
        if (!empty($this->_sub)) {
            return BEGINHTML_PERM_SUBDIVISIONADMIN;
        }
        if (!empty($this->_cc)) {
            return BEGINHTML_PERM_SUBCLASSADMIN;
        }
        if ($this->_user) {
            return BEGINHTML_PERM_MODERATOR;
        }
        if (!empty($this->_classificator)) {
            return BEGINHTML_PERM_CLASSIFICATORADMIN;
        }
        if ($this->_guest) {
            return BEGINHTML_PERM_GUEST;
        }

        return 0;
    }

    /**
     * Получить cataqlogue id, всех сайтов к разделам или компонентам раздела которго
     * пользователь имеет доступ, определяемой маской
     *
     * @param int $mask
     * @param bool $withSubClass учитывать sub class
     * @return mixed 1.array([0]=>id, [1] => id...) 2. null - имеет доступ ко всем
     */
    public function GetAllowSite($mask = MASK_ADMIN, $withSubClass = true) {

        if ($this->_director || $this->_supervisor || $this->_guest || ($this->_allSite & $mask)) {
            return null;
        }

        $site_list = (array)$this->listItems('catalogue', $mask);

        $temp = $this->listItems('subdivision', $mask);
        if ($temp) {
            $site_list = array_merge(
                $this->db->get_col(
                    'SELECT `Catalogue_ID` FROM `Subdivision`
                     WHERE `Subdivision_ID` IN (' . implode(', ', $temp) . ')'
                ),
                $site_list
            );
        }

        if ($withSubClass) {
            $temp = $this->listItems('subclass', $mask);
            if ($temp) {
                $site_list = array_merge(
                    $this->db->get_col(
                        'SELECT `Catalogue_ID` FROM `Sub_Class`
                         WHERE `Sub_Class_ID` IN (' . implode(', ', $temp) . ')'
                    ),
                    $site_list
                );
            }
        }

        $site_list = array_unique((array)$site_list);

        if (empty($site_list)) {
            return array(-1); // нет доступа ни к чему
        }

        return $site_list;
    }

    /**
     * Получить все разделы, к котороым пользователь имеет доступ (+ его родителей, если необходимо)
     *
     * @param int $CatalogueID
     * @param int $mask
     * @param bool $withParent - с этой опцией ф-ция вернет разделы, к которым пользователь может не иметь доступа, но они
     * являются родителями к тем разделам, к кторым пользователь имеет досутп
     * @param bool $withChild - вернуть еще разделы, к родителям которого пользователь имеет доступ
     * @param bool $withSubClass - вернуть разделы, к компонентам которого пользователь имеет доступ
     * @return mixed array ([0] => id, [1] => id) или null - если ко всем
     */
    public function GetAllowSub($CatalogueID, $mask = MASK_ADMIN, $withParent = true, $withChild = true, $withSubClass = true) {

        if ($this->_director || $this->_supervisor || ($this->_allSite & $mask) || ($this->_catalogue[$CatalogueID] & $mask)) {
            return null;
        }

        $allow = array(); //возвращаемый массив
        $temp = array();

        // разделы непосредствено из прав
        foreach ((array)$this->_sub as $k => $v) {
            if ($v & $mask) {
                $allow[] = $k;
            }
        }

        if ($withChild) {
            // права на дочерние наследуются
            $allow = array_merge((array)$allow, (array)$this->_GetChildrenSub($allow));
        }

        if ($withSubClass) {
            foreach ((array)$this->_cc as $k => $v) {
                if ($v & $mask) {
                    $temp[] = $k;
                }
            }
            if (!empty($temp)) {
                $temp = $this->db->get_col('SELECT `Subdivision_ID` FROM `Sub_Class` WHERE `Sub_Class_ID` IN (' . implode(',', $temp) . ')');
            }

            $allow = array_merge((array)$allow, (array)$temp);
        }

        if (empty($allow)) {
            return array(-1);
        }

        $allow = array_unique($allow);

        if ($withParent) {
            $temp = $this->db->get_col(
                'SELECT parent.`Subdivision_ID`
                 FROM `Subdivision` AS parent, `Subdivision` AS allowed
                 WHERE allowed.`Subdivision_ID` IN (' . implode(',', $allow) . ")
                 AND allowed.`Hidden_URL` LIKE CONCAT(parent.`Hidden_URL`, '%')"
            );
            $allow = array_merge($allow, (array)$temp);
        }

        if (empty($allow)) {
            return array(-1);
        }

        $allow = array_unique($allow);

        return $allow;
    }

    /* --- Methods for work with users --- */

    /**
     * Показывать или нет "Пользователи" в горизонтальном меню админке
     * @return bool
     */
    public function isUserMenuShow() {
        return ($this->_supervisor || $this->_director || $this->_guest || $this->_user);
    }

    /**
     * Return all users id, which have right > this
     * So, if this - supervisor  return directors id; this = moderator: return supervisors and directors id...
     *
     * @return array array with users id
     */
    public function GetUserWithMoreRights() {
        static $init_static, $ret_static;

        if ($init_static) {
            return (array)$ret_static;
        }

        $ret = array();
        if ($this->_director) {
            return $ret;
        }

        $where = $this->_makeQueryWhereWithMoreRights();

        $ret = $this->db->get_col("SELECT `User_ID` FROM `Permission` WHERE {$where} GROUP BY `User_ID`");

        // В ответ включим пользователей, состоящих в группе, у которой права больше, чем у текущего пользователя
        $ret = array_merge(
            (array)$ret,
            (array)$this->db->get_col(
                "SELECT ug.`User_ID` FROM `Permission` as per, `User_Group` as ug
                 WHERE ug.`PermissionGroup_ID` = per.`PermissionGroup_ID`
                 AND ({$where}) GROUP BY ug.`User_ID`"
            )
        );
        $init_static = true;
        $ret_static = $ret;

        return (array)$ret;
    }

    /**
     * Return all group id, which have right > this
     * So, if this - supervisor  return directors id; this = moderator: return supervisors and directors id...
     *
     * @return array array with users id
     */
    public function GetGroupWithMoreRights() {
        static $init_static, $ret_static;

        if ($init_static) {
            return (array)$ret_static;
        }

        $ret = array();
        if ($this->_director) {
            return $ret;
        }

        $where = $this->_makeQueryWhereWithMoreRights();

        // Группы у которых права больше, чем у текущего пользователя
        $ret = $this->db->get_col(
            "SELECT pg.`PermissionGroup_ID` FROM `Permission` as per,  `PermissionGroup` as pg
             WHERE pg.`PermissionGroup_ID` = per.`PermissionGroup_ID`
             AND ({$where})"
        );
        $init_static = true;
        $ret_static = $ret;

        return (array)$ret;
    }

    private function _makeQueryWhereWithMoreRights() {
        $where = '';

        if ($this->_director) {
            return $where;
        }

        // Если пользователь не директор, то диретокров он трогать не может
        $where = "`AdminType` = '" . DIRECTOR . "'";

        // Если не супервизор, то он не имеет доступа к супервизорам, модераторам(управляющим пользователями) и гостям
        if (!$this->_supervisor) {
            $where .= " OR `AdminType` = '" . SUPERVISOR . "'";
            $where .= " OR `AdminType` = '" . MODERATOR . "'";
            $where .= " OR `AdminType` = '" . GUEST . "'";
        }

        // Если нет прав модератора или администратора к сущности определенного уровня,
        // то не имеет доступа к модераторам или администраторам сущностей этого уровня и выше
        if (!$this->isAllSiteAdmin() && !$this->isInstanceModeratorAdmin('site')) {
            $where .= " OR (`AdminType` = '" . CATALOGUE_ADMIN . "' AND `PermissionSet` & (" . MASK_ADMIN . " | " . MASK_MODERATE . "))";
            if (!$this->isInstanceModeratorAdmin('sub')) {
                $where .= " OR (`AdminType` = '" . SUBDIVISION_ADMIN . "' AND `PermissionSet` & (" . MASK_ADMIN . " | " . MASK_MODERATE . "))";
                if (!$this->isInstanceModeratorAdmin('cc')) {
                    $where .= " OR (`AdminType` = '" . SUB_CLASS_ADMIN . "' AND `PermissionSet` & (" . MASK_ADMIN . " | " . MASK_MODERATE . "))";
                }
            }
        }

        // Если нет права управлять каким нибудь списком, то не может управлять админами списков
        if (!$this->isAnyClassificator()) {
            $where .= " OR `AdminType` = '" . CLASSIFICATOR_ADMIN . "'";
        }

        return $where;
    }

    /**
     * Вернуть массив с группами, имющий права-директор
     *
     * @todo переделать, чтобы возвращала массив групп, имеющие права больше, чем у текщего пользователя
     * @return array
     */
    public function GetDirectorsGroup() {
        return (array)$this->db->get_col("SELECT `PermissionGroup_ID` FROM `Permission` WHERE AdminType = '" . DIRECTOR . "' AND `PermissionGroup_ID` <> 0");
    }

    /**
     * Вернет  массив  с правами пользователя
     * каждая "строчка" - отдельное право, в строчке следующее "столбцы":
     * ID, live (время жизни), AdminType, title,  0, 1,2,3 4,5, ...
     * 0 1 2 3 4 5 - это чтение, добавление, изменнение, подписка, мод-ние и адм-ние (берется из констант, могут быть в другом порядке)
     * эти элменты - тоже массивы, ключи: 'checkbox' - 0 - нету, 1 - есть, 2 - есть всегда, 3 - нету в принипе
     *                                    'mask' -  маска для этого права - берется из констант (1, 2,4,8,16....)
     * но может быть не массив, а "-1" - значит нету ничего в принципе (директор, ...)
     *
     * @param int $UserID
     * @param int $GroupID
     * @return array
     * @throws Exception|ExceptionParam|ExceptionMailer|ExceptionDB
     */
    public static function GetAllPermission($UserID, $GroupID = 0) {
        global $nc_core, $db;
        //Получим все права
        if ($GroupID) {
            $GroupID = (int)$GroupID;
            $Result = $db->get_results("SELECT * FROM `Permission` WHERE `PermissionGroup_ID`='{$GroupID}'", ARRAY_A);
        } else {
            $UserID = (int)$UserID;
            $Result = $db->get_results("SELECT * FROM `Permission` WHERE `User_ID`='{$UserID}'", ARRAY_A);
        }

        foreach ((array)$Result as $prm) {
            $id = $prm['Permission_ID'];
            // если есть дата - преобразуем ее
            if ($prm['PermissionBegin']) {
                $prm['PermissionBegin'] = strtotime($prm['PermissionBegin']);
                $prm['PermissionBegin'] = strftime('%d.%m.%y %H:%M', $prm['PermissionBegin']);
            }
            if ($prm['PermissionEnd']) {
                $prm['PermissionEnd'] = strtotime($prm['PermissionEnd']);
                $prm['PermissionEnd'] = strftime('%d.%m.%y %H:%M', $prm['PermissionEnd']);
            }


            switch (true) { // определение live - времени жизни
                case (!$prm['PermissionBegin'] && !$prm['PermissionEnd']):
                    $ret[$id]['live'] = '<nobr>' . CONTROL_USER_RIGHTS_UNLIMITED . '</nobr>';
                    break;
                case ($prm['PermissionBegin'] && $prm['PermissionEnd']):
                    $ret[$id]['live'] = '<nobr>c ' . $prm['PermissionBegin'] . '</nobr><br><nobr>по ' . $prm['PermissionEnd'] . '</nobr>';
                    break;
                case ($prm['PermissionBegin'] && !$prm['PermissionEnd']):
                    $ret[$id]['live'] = '<nobr>c ' . $prm['PermissionBegin'] . '</nobr>';
                    break;
                case (!$prm['PermissionBegin'] && $prm['PermissionEnd']):
                    $ret[$id]['live'] = '<nobr>по ' . $prm['PermissionEnd'] . '</nobr>';
                    break;
            }

            $ret[$id]['AdminType'] = $prm['AdminType'];
            $ret[$id]['ID'] = $prm['AdminType'];
            $ps = $prm['PermissionSet']; // ps - permission set
            $c_id = $prm['Catalogue_ID'];

            // r - read, e - edit, d - add, s - subscribe, m - moderate, a - admin, l - delete, h -checked
            // в зависимости от этих переменных, включатся-выключатся checkbox'ы
            $r = ($ps & MASK_READ) ? 1 : 0;
            $d = ($ps & MASK_ADD) ? 1 : 0;
            $e = ($ps & MASK_EDIT) ? 1 : 0;
            $s = ($ps & MASK_SUBSCRIBE) ? 1 : 0;
            $m = ($ps & MASK_MODERATE) ? 1 : 0;
            $a = ($ps & MASK_ADMIN) ? 1 : 0;
            $c = ($ps & MASK_COMMENT) ? 1 : 0;
            $l = ($ps & MASK_DELETE) ? 1 : 0;
            $h = ($ps & MASK_CHECKED) ? 1 : 0;

            $ret[$id]['title'] = Permission::GetPermNameByID($prm['AdminType']);
            $ret[$id]['title'] .= ' ';

            switch ($prm['AdminType']) {
                case DIRECTOR:
                case SUPERVISOR:
                case GUEST:
                    // для них нету просмотр-измениние...-администрирование вообще
                    $r = $c = $e = $d = $s = $m = $a = $l = $h = -1;
                    break;
                case SUBSCRIBER:
                    $r = $c = $e = $d = $s = $m = $a = $l = $h = -1;
                    $nc_s = nc_subscriber::get_object();
                    $ret[$id]['title'] = Permission::GetPermNameByID($prm['AdminType']) . ' на рассылку ' . $nc_s->get($c_id, 'Name');
                    break;
                case BAN_SITE:
                    $m = $a = 3; // нету модерирования и администрирования

                    if ($c_id) { // определенный сайт
                        $ret[$id]['title'] .= "\"{$nc_core->catalogue->get_by_id($c_id, 'Catalogue_Name')}\"";
                    } else {  // все сайты
                        $ret[$id]['title'] = CONTROL_USER_RIGHTS_SITEALL;
                    }
                    break;
                case CATALOGUE_ADMIN:
                    if ($c_id) {
                        $ret[$id]['title'] .= "\"{$nc_core->catalogue->get_by_id($c_id, 'Catalogue_Name')}\"";
                    } else {
                        $ret[$id]['title'] = CONTROL_USER_RIGHTS_CATALOGUEADMINALL;
                    }
                    break;
                case BAN_SUB:
                    $m = $a = 3; // нету модерирования и админисрирования
                // здесь break не нужен
                case SUBDIVISION_ADMIN:
                    $catalogue_name = $nc_core->catalogue->get_by_id($nc_core->subdivision->get_by_id($c_id, 'Catalogue_ID'), 'Catalogue_Name');
                    $ret[$id]['title'] .= " \"{$nc_core->subdivision->get_by_id($c_id, "Subdivision_Name")}\" " . CONTROL_USER_FUNCS_FROMCAT . "  \"{$catalogue_name}\"";
                    break;
                case BAN_CC:
                    $m = $a = 3;
                // здесь break не нужен
                case SUB_CLASS_ADMIN:
                    $subdivision_name = $nc_core->subdivision->get_by_id(GetSubdivisionBySubClass($c_id), "Subdivision_Name");
                    $ret[$id]['title'] .= " \"" . GetSubClassName($c_id) . "\" " . CONTROL_USER_FUNCS_FROMSEC . " \"" . $subdivision_name . "\"";
                    break;
                case CLASSIFICATOR_ADMIN:
                    $r = 2;
                    $s = 3;
                    $a = 3;
                    $c = 3;
                    $l = 3;
                    $h = 3; // Просмотр - всегда, подписки и админ-ния нет
                    if ($c_id) {
                        $ret[$id]['title'] .= "\"" . Permission::_GetClassificatorNameByID($c_id) . "\"";
                    } else {
                        $ret[$id]['title'] = CONTROL_USER_RIGHTS_CLASSIFICATORADMINALL;
                    }
                    break;
                case MODERATOR:
                    $r = 2;
                    $s = $a = 3;
                    $c = 3;
                    $h = 3;
                    $l = 3; // Просмотр - всегда, подписки и админ-ния нет
                    break;
            }
            $ret[$id][NC_PERM_READ_ID]['checkbox'] = $r;
            $ret[$id][NC_PERM_READ_ID]['mask'] = MASK_READ;
            $ret[$id][NC_PERM_ADD_ID]['checkbox'] = $d;
            $ret[$id][NC_PERM_ADD_ID]['mask'] = MASK_ADD;
            $ret[$id][NC_PERM_EDIT_ID]['checkbox'] = $e;
            $ret[$id][NC_PERM_EDIT_ID]['mask'] = MASK_EDIT;
            $ret[$id][NC_PERM_SUBCRIBE_ID]['checkbox'] = $s;
            $ret[$id][NC_PERM_SUBCRIBE_ID]['mask'] = MASK_SUBSCRIBE;
            $ret[$id][NC_PERM_MODERATE_ID]['checkbox'] = $m;
            $ret[$id][NC_PERM_MODERATE_ID]['mask'] = MASK_MODERATE;
            $ret[$id][NC_PERM_ADMIN_ID]['checkbox'] = $a;
            $ret[$id][NC_PERM_ADMIN_ID]['mask'] = MASK_ADMIN;
            $ret[$id][NC_PERM_COMMENT_ID]['checkbox'] = $c;
            $ret[$id][NC_PERM_COMMENT_ID]['mask'] = MASK_COMMENT;
            $ret[$id][NC_PERM_CHECKED_ID]['checkbox'] = $h;
            $ret[$id][NC_PERM_CHECKED_ID]['mask'] = MASK_CHECKED;
            $ret[$id][NC_PERM_DELETE_ID]['checkbox'] = $l;
            $ret[$id][NC_PERM_DELETE_ID]['mask'] = MASK_DELETE;
        }

        // отсортируем массив с помощью пользователськой ф-цией cmp
        if (!empty($ret)) {
            uasort($ret, 'Permission::_cmp');
        }
        return $ret;
    }

    public static function DeleteObsoletePerm() {
        global $db;
        $db->query('DELETE FROM `Permission` WHERE `PermissionEnd` < NOW() ');
    }

    /**
     * Удаление прав, касающихся сайтов
     * @param array $catalogue
     */
    public function dropCataloguePerm($catalogue) {
        $catalogue = (array)$catalogue;
        if (!empty($catalogue)) {
            $this->db->query('DELETE FROM `Permission` WHERE `Catalogue_ID` IN (' . implode(', ', $catalogue) . ") AND `AdminType` = '" . CATALOGUE_ADMIN . "'");
        }
    }

    /**
     * Удаление прав, касающихся разделов
     * @param array $sub
     */
    public function dropSubdivisionPerm($sub) {
        $sub = (array)$sub;
        if (!empty($sub)) {
            $this->db->query('DELETE FROM `Permission` WHERE `Catalogue_ID` IN (' . implode(', ', $sub) . ") AND `AdminType` = '" . SUBDIVISION_ADMIN . "'");
        }
    }

    /**
     * Удаление прав, касающихся инфоблоков
     * @param $catalogue
     * @param $sub
     * @param array $cc
     */
    public function dropSubClassPerm($catalogue, $sub, $cc) {
        $cc = (array)$cc;
        if (!empty($cc)) {
            $this->db->query('DELETE FROM `Permission` WHERE `Catalogue_ID` IN (' . implode(', ', $cc) . ") AND `AdminType` = '" . SUB_CLASS_ADMIN . "'");
        }
    }

    /**
     * Удаление прав, касающихся пользователей
     */
    public function dropUserPerm($ids) {
        $ids = (array)$ids;
        if (!empty($ids)) {
            $this->db->query('DELETE FROM `Permission` WHERE `User_ID` IN (' . implode(',', $ids) . ')');
        }
    }

    /**
     * Получить "имя" права по его id
     *
     * @param int $id
     * @return null|string
     */
    private static function GetPermNameByID($id) {
        switch ($id) {
            case 7:
                return CONTROL_USER_RIGHTS_DIRECTOR;
            case 6:
                return CONTROL_USER_RIGHTS_SUPERVISOR;
            case 4:
                return CONTROL_USER_RIGHTS_SITEADMIN;
            case 3:
                return CONTROL_USER_RIGHTS_SUBDIVISIONADMIN;
            case 9:
                return CONTROL_USER_RIGHTS_SUBCLASSADMIN;
            case 15:
                return CONTROL_USER_RIGHTS_CLASSIFICATORADMIN;
            case 8:
                return CONTROL_USER_RIGHTS_GUESTONE;
            case 12:
                return CONTROL_USER_RIGHTS_MODERATOR;
            case 21:
                return CONTROL_USER_RIGHTS_SITE;
            case 22:
                return CONTROL_USER_RIGHTS_SUB;
            case 23:
                return CONTROL_USER_RIGHTS_CC;
            case 30:
                return CONTROL_USER_RIGHTS_SUBSCRIBER;
        }

        return null;
    }

    /**
     * Получить подразделы данного раздела
     *
     * @param array $sub
     * @todo функция такая уже есть, но нельзя подключить, т.к. файл, где она объявлена содержит еще объявление класса,
     * родитель которого объявлен в другом классе
     * @return mixed
     */
    private function _GetChildrenSub($sub) {
        global $db;

        $sub = (array)$sub;

        if (empty($sub)) {
            return null;
        }

        $subdivision_ids = implode(',', array_unique($sub));

        $ret = (array)$db->get_col(
            "SELECT child.`Subdivision_ID`
             FROM `Subdivision` as child, `Subdivision` as parent
             WHERE parent.`Subdivision_ID` IN ({$subdivision_ids})
             AND child.`Hidden_URL` LIKE CONCAT(parent.`Hidden_URL`, '%')
             AND child.`Subdivision_ID` <> parent.`Subdivision_ID`"
        );

        return $ret;
    }

    /**
     * Имя списка по его id
     *
     * @return mixed
     */
    private function _GetClassificatorNameByID($ClassificatorID) {
        global $db;
        $ClassificatorID = (int)$ClassificatorID;

        return $db->get_var("SELECT Classificator_Name FROM Classificator WHERE Classificator_ID='{$ClassificatorID}'");
    }

    /**
     * Функция, используемая для сравнения
     * При сортировке всех прав пользователя
     *
     * @param  array $a , в нем есть AdminType - по ним и будет производиться сортировка
     * @param  array $b
     * @return int 0, 1, - 1 - если права равны, стоят выше или ниже соответсвенно
     */
    private static function _cmp($a, $b) {
        // По этому массиву будет выполнять сортировка, т.е.
        // сначала будут все директоры, потом супервизоры....
        static $order_array = array(
            DIRECTOR, SUPERVISOR, SUBSCRIBER, GUEST,
            MODERATOR, CLASSIFICATOR_ADMIN,
            CATALOGUE_ADMIN, SUBDIVISION_ADMIN, SUB_CLASS_ADMIN,
            BAN_SITE, BAN_SUB, BAN_CC
        );

        if ($a['AdminType'] == $b['AdminType']) {
            return 0;
        }

        $a_id = null;
        $b_id = null;

        // узнаем, какую позицию занимает AdminType в каждом праве в массиве order_array
        foreach ($order_array as $k => $v) {
            if ($v == $a['AdminType']) {
                $a_id = $k;
            }
            if ($v == $b['AdminType']) {
                $b_id = $k;
            }
        }

        return ($a_id < $b_id) ? -1 : 1;
    }


    public static function get_all_permission_names_by_id($user_id) {
        $all_perm = (array)self::GetAllPermission($user_id);
        $result = array();
        foreach ($all_perm as $perm) {
            $result[] = $perm['title'];
        }

        return $result;
    }
}