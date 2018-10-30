<?php

class nc_Event extends nc_System {

    const AFTER_MODULES_LOADED              = 'modulesLoaded';
    const AFTER_MODULE_ENABLED              = 'checkModule';
    const AFTER_MODULE_DISABLED             = 'uncheckModule';
    const BEFORE_MODULES_LOADED             = 'modulesLoadedPrep';
    const BEFORE_MODULE_ENABLED             = 'checkModulePrep';
    const BEFORE_MODULE_DISABLED            = 'uncheckModulePrep';

    const AFTER_SITE_CREATED                = 'addCatalogue';
    const AFTER_SITE_UPDATED                = 'updateCatalogue';
    const AFTER_SITE_DELETED                = 'dropCatalogue';
    const AFTER_SITE_ENABLED                = 'checkCatalogue';
    const AFTER_SITE_DISABLED               = 'uncheckCatalogue';
    const BEFORE_SITE_CREATED               = 'addCataloguePrep';
    const BEFORE_SITE_UPDATED               = 'updateCataloguePrep';
    const BEFORE_SITE_DELETED               = 'dropCataloguePrep';
    const BEFORE_SITE_ENABLED               = 'checkCataloguePrep';
    const BEFORE_SITE_DISABLED              = 'uncheckCataloguePrep';

    const AFTER_SUBDIVISION_CREATED         = 'addSubdivision';
    const AFTER_SUBDIVISION_UPDATED         = 'updateSubdivision';
    const AFTER_SUBDIVISION_DELETED         = 'dropSubdivision';
    const AFTER_SUBDIVISION_ENABLED         = 'checkSubdivision';
    const AFTER_SUBDIVISION_DISABLED        = 'uncheckSubdivision';
    const BEFORE_SUBDIVISION_CREATED        = 'addSubdivisionPrep';
    const BEFORE_SUBDIVISION_UPDATED        = 'updateSubdivisionPrep';
    const BEFORE_SUBDIVISION_DELETED        = 'dropSubdivisionPrep';
    const BEFORE_SUBDIVISION_ENABLED        = 'checkSubdivisionPrep';
    const BEFORE_SUBDIVISION_DISABLED       = 'uncheckSubdivisionPrep';

    const AFTER_INFOBLOCK_CREATED           = 'addSubClass';
    const AFTER_INFOBLOCK_UPDATED           = 'updateSubClass';
    const AFTER_INFOBLOCK_DELETED           = 'dropSubClass';
    const AFTER_INFOBLOCK_ENABLED           = 'checkSubClass';
    const AFTER_INFOBLOCK_DISABLED          = 'uncheckSubClass';
    const BEFORE_INFOBLOCK_CREATED          = 'addSubClassPrep';
    const BEFORE_INFOBLOCK_UPDATED          = 'updateSubClassPrep';
    const BEFORE_INFOBLOCK_DELETED          = 'dropSubClassPrep';
    const BEFORE_INFOBLOCK_ENABLED          = 'checkSubClassPrep';
    const BEFORE_INFOBLOCK_DISABLED         = 'uncheckSubClassPrep';

    const AFTER_COMPONENT_CREATED           = 'addClass';
    const AFTER_COMPONENT_UPDATED           = 'updateClass';
    const AFTER_COMPONENT_DELETED           = 'dropClass';
    const AFTER_COMPONENT_TEMPLATE_CREATED  = 'addClassTemplate';
    const AFTER_COMPONENT_TEMPLATE_UPDATED  = 'updateClassTemplate';
    const AFTER_COMPONENT_TEMPLATE_DELETED  = 'dropClassTemplate';
    const BEFORE_COMPONENT_CREATED          = 'addClassPrep';
    const BEFORE_COMPONENT_UPDATED          = 'updateClassPrep';
    const BEFORE_COMPONENT_DELETED          = 'dropClassPrep';
    const BEFORE_COMPONENT_TEMPLATE_CREATED = 'addClassTemplatePrep';
    const BEFORE_COMPONENT_TEMPLATE_UPDATED = 'updateClassTemplatePrep';
    const BEFORE_COMPONENT_TEMPLATE_DELETED = 'dropClassTemplatePrep';

    const AFTER_OBJECT_CREATED              = 'addMessage';
    const AFTER_OBJECT_UPDATED              = 'updateMessage';
    const AFTER_OBJECT_DELETED              = 'dropMessage';
    const AFTER_OBJECT_ENABLED              = 'checkMessage';
    const AFTER_OBJECT_DISABLED             = 'uncheckMessage';
    const BEFORE_OBJECT_CREATED             = 'addMessagePrep';
    const BEFORE_OBJECT_UPDATED             = 'updateMessagePrep';
    const BEFORE_OBJECT_DELETED             = 'dropMessagePrep';
    const BEFORE_OBJECT_ENABLED             = 'checkMessagePrep';
    const BEFORE_OBJECT_DISABLED            = 'uncheckMessagePrep';

    const AFTER_SYSTEM_TABLE_CREATED        = 'addSystemTable';
    const AFTER_SYSTEM_TABLE_UPDATED        = 'updateSystemTable';
    const AFTER_SYSTEM_TABLE_DELETED        = 'dropSystemTable';
    const BEFORE_SYSTEM_TABLE_CREATED       = 'addSystemTablePrep';
    const BEFORE_SYSTEM_TABLE_UPDATED       = 'updateSystemTablePrep';
    const BEFORE_SYSTEM_TABLE_DELETED       = 'dropSystemTablePrep';

    const AFTER_TEMPLATE_CREATED            = 'addTemplate';
    const AFTER_TEMPLATE_UPDATED            = 'updateTemplate';
    const AFTER_TEMPLATE_DELETED            = 'dropTemplate';
    const BEFORE_TEMPLATE_CREATED           = 'addTemplatePrep';
    const BEFORE_TEMPLATE_UPDATED           = 'updateTemplatePrep';
    const BEFORE_TEMPLATE_DELETED           = 'dropTemplatePrep';

    const AFTER_USER_CREATED                = 'addUser';
    const AFTER_USER_UPDATED                = 'updateUser';
    const AFTER_USER_DELETED                = 'dropUser';
    const AFTER_USER_ENABLED                = 'checkUser';
    const AFTER_USER_DISABLED               = 'uncheckUser';
    const AFTER_USER_AUTHORIZED             = 'authorizeUser';
    const BEFORE_USER_CREATED               = 'addUserPrep';
    const BEFORE_USER_UPDATED               = 'updateUserPrep';
    const BEFORE_USER_DELETED               = 'dropUserPrep';
    const BEFORE_USER_ENABLED               = 'checkUserPrep';
    const BEFORE_USER_DISABLED              = 'uncheckUserPrep';
    const BEFORE_USER_AUTHORIZED            = 'authorizeUserPrep';

    const AFTER_COMMENT_CREATED             = 'addComment';
    const AFTER_COMMENT_UPDATED             = 'updateComment';
    const AFTER_COMMENT_DELETED             = 'dropComment';
    const AFTER_COMMENT_ENABLED             = 'checkComment';
    const AFTER_COMMENT_DISABLED            = 'uncheckComment';
    const BEFORE_COMMENT_CREATED            = 'addCommentPrep';
    const BEFORE_COMMENT_UPDATED            = 'updateCommentPrep';
    const BEFORE_COMMENT_DELETED            = 'dropCommentPrep';
    const BEFORE_COMMENT_ENABLED            = 'checkCommentPrep';
    const BEFORE_COMMENT_DISABLED           = 'uncheckCommentPrep';

    const AFTER_WIDGET_COMPONENT_CREATED    = 'addWidgetClass';
    const AFTER_WIDGET_COMPONENT_UPDATED    = 'editWidgetClass';
    const AFTER_WIDGET_COMPONENT_DELETED    = 'dropWidgetClass';
    const AFTER_WIDGET_CREATED              = 'addWidget';
    const AFTER_WIDGET_UPDATED              = 'editWidget';
    const AFTER_WIDGET_DELETED              = 'dropWidget';
    const BEFORE_WIDGET_COMPONENT_CREATED   = 'addWidgetClassPrep';
    const BEFORE_WIDGET_COMPONENT_UPDATED   = 'editWidgetClassPrep';
    const BEFORE_WIDGET_COMPONENT_DELETED   = 'dropWidgetClassPrep';
    const BEFORE_WIDGET_CREATED             = 'addWidgetPrep';
    const BEFORE_WIDGET_UPDATED             = 'editWidgetPrep';
    const BEFORE_WIDGET_DELETED             = 'dropWidgetPrep';

    private $_binded_obj, $_events_arr;
    private $_events_name;

    public function __construct() {
        // load parent constructor
        parent::__construct();

        // collect objects for events
        $this->_binded_obj = array();

        // allowed events
        $this->_events_arr = array(
            self::AFTER_MODULES_LOADED,
            self::AFTER_MODULE_ENABLED,
            self::AFTER_MODULE_DISABLED,
            self::BEFORE_MODULES_LOADED,
            self::BEFORE_MODULE_ENABLED,
            self::BEFORE_MODULE_DISABLED,
            self::AFTER_SITE_CREATED,
            self::AFTER_SITE_UPDATED,
            self::AFTER_SITE_DELETED,
            self::AFTER_SITE_ENABLED,
            self::AFTER_SITE_DISABLED,
            self::BEFORE_SITE_CREATED,
            self::BEFORE_SITE_UPDATED,
            self::BEFORE_SITE_DELETED,
            self::BEFORE_SITE_ENABLED,
            self::BEFORE_SITE_DISABLED,
            self::AFTER_SUBDIVISION_CREATED,
            self::AFTER_SUBDIVISION_UPDATED,
            self::AFTER_SUBDIVISION_DELETED,
            self::AFTER_SUBDIVISION_ENABLED,
            self::AFTER_SUBDIVISION_DISABLED,
            self::BEFORE_SUBDIVISION_CREATED,
            self::BEFORE_SUBDIVISION_UPDATED,
            self::BEFORE_SUBDIVISION_DELETED,
            self::BEFORE_SUBDIVISION_ENABLED,
            self::BEFORE_SUBDIVISION_DISABLED,
            self::AFTER_INFOBLOCK_CREATED,
            self::AFTER_INFOBLOCK_UPDATED,
            self::AFTER_INFOBLOCK_DELETED,
            self::AFTER_INFOBLOCK_ENABLED,
            self::AFTER_INFOBLOCK_DISABLED,
            self::BEFORE_INFOBLOCK_CREATED,
            self::BEFORE_INFOBLOCK_UPDATED,
            self::BEFORE_INFOBLOCK_DELETED,
            self::BEFORE_INFOBLOCK_ENABLED,
            self::BEFORE_INFOBLOCK_DISABLED,
            self::AFTER_COMPONENT_CREATED,
            self::AFTER_COMPONENT_UPDATED,
            self::AFTER_COMPONENT_DELETED,
            self::AFTER_COMPONENT_TEMPLATE_CREATED,
            self::AFTER_COMPONENT_TEMPLATE_UPDATED,
            self::AFTER_COMPONENT_TEMPLATE_DELETED,
            self::BEFORE_COMPONENT_CREATED,
            self::BEFORE_COMPONENT_UPDATED,
            self::BEFORE_COMPONENT_DELETED,
            self::BEFORE_COMPONENT_TEMPLATE_CREATED,
            self::BEFORE_COMPONENT_TEMPLATE_UPDATED,
            self::BEFORE_COMPONENT_TEMPLATE_DELETED,
            self::AFTER_OBJECT_CREATED,
            self::AFTER_OBJECT_UPDATED,
            self::AFTER_OBJECT_DELETED,
            self::AFTER_OBJECT_ENABLED,
            self::AFTER_OBJECT_DISABLED,
            self::BEFORE_OBJECT_CREATED,
            self::BEFORE_OBJECT_UPDATED,
            self::BEFORE_OBJECT_DELETED,
            self::BEFORE_OBJECT_ENABLED,
            self::BEFORE_OBJECT_DISABLED,
            self::AFTER_SYSTEM_TABLE_CREATED,
            self::AFTER_SYSTEM_TABLE_UPDATED,
            self::AFTER_SYSTEM_TABLE_DELETED,
            self::BEFORE_SYSTEM_TABLE_CREATED,
            self::BEFORE_SYSTEM_TABLE_UPDATED,
            self::BEFORE_SYSTEM_TABLE_DELETED,
            self::AFTER_TEMPLATE_CREATED,
            self::AFTER_TEMPLATE_UPDATED,
            self::AFTER_TEMPLATE_DELETED,
            self::BEFORE_TEMPLATE_CREATED,
            self::BEFORE_TEMPLATE_UPDATED,
            self::BEFORE_TEMPLATE_DELETED,
            self::AFTER_USER_CREATED,
            self::AFTER_USER_UPDATED,
            self::AFTER_USER_DELETED,
            self::AFTER_USER_ENABLED,
            self::AFTER_USER_DISABLED,
            self::AFTER_USER_AUTHORIZED,
            self::BEFORE_USER_CREATED,
            self::BEFORE_USER_UPDATED,
            self::BEFORE_USER_DELETED,
            self::BEFORE_USER_ENABLED,
            self::BEFORE_USER_DISABLED,
            self::BEFORE_USER_AUTHORIZED,
            self::AFTER_COMMENT_CREATED,
            self::AFTER_COMMENT_UPDATED,
            self::AFTER_COMMENT_DELETED,
            self::AFTER_COMMENT_ENABLED,
            self::AFTER_COMMENT_DISABLED,
            self::BEFORE_COMMENT_CREATED,
            self::BEFORE_COMMENT_UPDATED,
            self::BEFORE_COMMENT_DELETED,
            self::BEFORE_COMMENT_ENABLED,
            self::BEFORE_COMMENT_DISABLED,
            self::AFTER_WIDGET_COMPONENT_CREATED,
            self::AFTER_WIDGET_COMPONENT_UPDATED,
            self::AFTER_WIDGET_COMPONENT_DELETED,
            self::AFTER_WIDGET_CREATED,
            self::AFTER_WIDGET_UPDATED,
            self::AFTER_WIDGET_DELETED,
            self::BEFORE_WIDGET_COMPONENT_CREATED,
            self::BEFORE_WIDGET_COMPONENT_UPDATED,
            self::BEFORE_WIDGET_COMPONENT_DELETED,
            self::BEFORE_WIDGET_CREATED,
            self::BEFORE_WIDGET_UPDATED,
            self::BEFORE_WIDGET_DELETED,
        );

        // имена пользовательских событий
        $this->_events_name = array();
    }

    /**
     * Add object to the listen mode
     *
     * @param object $object examine object
     * @param string|array $event_data
     * @return bool
     */
    public function bind(&$object, $event_data) {
        // validate
        if (!(is_string($event_data) || is_array($event_data))) {
            return false;
        }

        // remap array
        $events_remap_arr = array();

        // имя метода совпадает с именем события
        if (is_string($event_data)) {
            $event_name = $event_data;
        } else {
            // get parameters
            list($event_name, $event_remap_name) = each($event_data);
            // для одного метода названачены несколько событий ( перечислены через запятую )
            if (strpos($event_name, ',') && ($events = explode(',', $event_name))) {
                foreach ($events as $v) {
                    $this->bind($object, array($v => $event_remap_name));
                }
                return true;
            }

            // remap array
            $events_remap_arr = $event_data;
        }

        // already bound
        if (isset($this->_binded_obj[$event_name]) && in_array($object, $this->_binded_obj[$event_name], true)) {
            return true;
        }

        // bind object with remap array
        $this->_binded_obj[$event_name][] = array('object' => $object, 'remap' => $events_remap_arr);

        return true;
    }

    public function add_listener($event_name, $callback) {
        if (!is_callable($callback)) {
            return false;
        }

        $this->_binded_obj[$event_name][] = array('callback' => $callback);

        return true;
    }

    /**
     * Event processor
     * call objects function for current event
     */
    public function execute() {
        // get function args
        $args = func_get_args();

        // check args
        if (empty($args) || empty($this->_binded_obj)) {
            return false;
        }

        // event name
        $event = array_shift($args);

        // check base system event
        if (!$event || !in_array($event, $this->_events_arr, true)) {
            return false;
        }

        // check bound array
        if (empty($this->_binded_obj[$event])) {
            return false;
        }

        foreach ($this->_binded_obj[$event] as $object) {
            $event_method = $event;

            // check remapped events
            if (!empty($object['remap'])) {
                // remap event method
                $event_method = $object['remap'][$event] ?: "";
            }

            $callback = !empty($object['callback']) ? $object['callback'] : array($object['object'], $event_method);

            // check and execute observer method
            if (is_callable($callback)) {
                // execute event method
                call_user_func_array($callback, $args);
            }
        }

        return true;
    }

    /**
     * Get all events as array
     *
     * @return array events list
     */
    public function get_all_events() {
        // return result
        return $this->_events_arr;
    }

    /**
     * Check event by event name
     *
     * @param string $event
     * @return bool result
     */
    public function check_event($event) {
        // check base system event
        if (in_array($event, $this->_events_arr, true)) {
            return true;
        }

        // return result
        return false;
    }

    public function register_event($event, $name) {
        // не существует ли событие уже
        if ($this->check_event($event)) {
            return false;
        }

        if (!$name || !nc_preg_match('/^[_a-z0-9]+$/i', $event)) {
            return false;
        }

        $this->_events_arr[] = $event;
        $this->_events_name[$event] = $name;

        return true;
    }

    /**
     * @param string $event
     * @return mixed
     */
    public function event_name($event) {
        // check base system event
        if (!( $event && in_array($event, $this->_events_arr, true) )) {
            return false;
        }

        // пользовательское имя события
        if (array_key_exists($event, $this->_events_name)) {
            return $this->_events_name[$event];
        }

        $const = 'NETCAT_EVENT_' . strtoupper($event);
        return defined($const) ? constant($const) : $const;
    }
}