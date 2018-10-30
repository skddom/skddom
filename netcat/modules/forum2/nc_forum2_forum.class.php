<?php

/**
 * class nc_forum2_forum
 *
 * @package nc_forum2
 */
class nc_forum2_forum extends nc_forum2 {

    /**
     * Constructor method
     *
     * @access protected
     */
    protected function __construct() {
        // parent
        parent::__construct();

        // system superior object
        $nc_core = nc_Core::get_object();

        // parent class
        $this->classID = $this->MODULE_VARS['TOPIC_CLASS_ID'];

        // RSS class
        $this->rss_classID = $this->MODULE_VARS['TOPIC_RSS_CLASS_ID'];

        // bind events
        if ($this->MODULE_VARS['TRACKING_MODE']) {
            $nc_core->event->bind($this, array(nc_Event::AFTER_INFOBLOCK_CREATED => 'event_add_cc'));
        }
        $nc_core->event->bind($this, array(nc_Event::AFTER_INFOBLOCK_DELETED => 'event_delete_cc'));
        $nc_core->event->bind($this, array(nc_Event::AFTER_SUBDIVISION_DELETED => 'event_delete'));
    }

    /**
     * Get or instance self object
     *
     * @static
     * @access public
     *
     * @return nc_forum2_forum self object
     */
    public static function get_object() {
        // call as static
        static $storage;
        // check inited object
        if (!isset($storage)) {
            // init object
            $storage = new self();
        }
        // return object
        return is_object($storage) ? $storage : false;
    }

    /**
     * Get forum url by cc id method
     *
     * @access public
     *
     * @param int $cc forum Sub_Class_ID
     *
     * @return string forum url
     */
    public function get_forum_url_by_cc_id($cc) {
        $cc = intval($cc);
        $nc_core = nc_Core::get_object();

        if (!$nc_core->admin_mode) {
            try {
                $folder_id = $nc_core->sub_class->get_by_id($cc, 'Subdivision_ID');
                $result = nc_folder_path($folder_id);
            }
            catch (Exception $e) {
                $result = false;
            }
        }
        else {
            // admin mode
            $result = $this->db->get_var(
                "SELECT CONCAT('" . $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH . "?cc=', `Sub_Class_ID`)
                   FROM `Sub_Class`
                  WHERE `Sub_Class_ID` = '" . $cc . "'");
        }

        return $result;
    }

    /**
     * Get forum views count method
     *
     * @access public
     *
     * @param int $forum_id forum Subdivision_ID
     *
     * @return int counted value
     */
    public function get_views($forum_id) {
        // validate
        $forum_id = intval($forum_id);

        // return forum count
        return $this->db->get_var("SELECT SUM(`Views`) FROM `Message" . $this->classID . "`
      WHERE `Subdivision_ID` = '" . $forum_id . "'");
    }

    /**
     * Get RSS url
     *
     * @static
     * @access public
     *
     * @param int Subdivision_ID
     *
     * @return string RSS url
     */
    public function get_rss_url($sub) {
        // storage variable
        static $storage = array();

        $sub = intval($sub);

        if (!isset($storage[$sub])) {
            // get RSS url
            $storage[$sub] = $this->db->get_var(
                "SELECT cc.`Sub_Class_ID`
                   FROM `Subdivision` AS sub
                        LEFT JOIN `Sub_Class` AS cc ON sub.`Subdivision_ID` = cc.`Subdivision_ID`
                  WHERE cc.`Subdivision_ID` = '" . $sub . "'
                    AND cc.`Class_Template_ID` = '" . $this->get_rss_class_id() . "'");
        }

        // return RSS url
        return $storage[$sub] ? nc_infoblock_path($storage[$sub], 'index', 'rss') : false;
    }

    /**
     * Get users ids method
     *
     * @access public
     *
     * @param int $sub forum Subdivision_ID
     * @param int $type count type (0 - all, 1 - only topics, 2 - only replies)
     *
     * @return array users ids result
     */
    public function get_users($sub, $type = 0) {
        // call as static
        static $storage = array();

        // validate
        $sub = intval($sub);

        if (empty($storage[$sub])) {
            // get users ids
            $topics_users = $this->db->get_col("SELECT DISTINCT `User_ID` FROM `Message" . $this->classID . "`
        WHERE `Subdivision_ID` = '" . $sub . "'");

            // get objects
            $reply_obj = nc_forum2_reply::get_object();

            // get users ids
            $replies_users = $this->db->get_col("SELECT DISTINCT `User_ID` FROM `Message" . $reply_obj->get_class_id() . "`
        WHERE `Subdivision_ID` = '" . $sub . "'");

            // get users ids
            $storage[$sub] = array($topics_users, $replies_users);
        }

        // get values
        list($topics_users, $replies_users) = $storage[$sub];

        // select result
        switch ($type) {
            // topics users
            case 1:
                return (array)$topics_users;
                break;
            // replies usres
            case 2:
                return (array)$replies_users;
                break;
            // all users
            default:
                return array_unique(array_merge((array)$topics_users, (array)$replies_users));
        }
    }

    /**
     * Get data from `Forum_Subdivisions` table
     *
     * @param int forum `Subdivision_ID`
     * @param string concrete row item
     *
     * @return mixed row data array or row item
     */
    public function get_data($id, $item = "") {
        // call as static
        static $storage = array();

        //  validate
        $id = intval($id);

        // check inited object
        if (empty($storage[$id])) {
            $storage[$id] = $this->db->get_row("SELECT * FROM `Forum_Subdivisions`
        WHERE `Subdivision_ID` = '" . $id . "' AND `Type` = 'forum'", ARRAY_A);
        }

        // if item requested return item value
        if ($item && is_array($storage[$id])) {
            return array_key_exists($item, $storage[$id]) ? $storage[$id][$item] : "";
        }

        // return Sub_Class_ID
        return $storage[$id];
    }

    /**
     * System events callback function
     *
     * @access public
     *
     * @param int $catalogue Catalogue_ID
     * @param int $sub Subdivision_ID
     * @param int $cc Sub_Class_ID
     */
    public function event_add_cc($catalogue, $sub, $cc) {
        // validate
        $forum = intval($sub);

        if (is_array($cc)) {
            $cc = array_map('intval', $cc);
            $data = $this->db->get_results("SELECT `Sub_Class_ID`, `Class_ID` FROM `Sub_Class`
        WHERE `Sub_Class_ID` IN (" . join(", ", $cc) . ") AND `Class_Template_ID` = 0", ARRAY_A);

            if (!empty($data)) {
                foreach ($data as $row) {
                    if ($this->classID == $row['Class_ID']) {
                        $_cc = $row['Sub_Class_ID'];
                        break;
                    }
                }
            }
        }
        else {
            // validate
            $cc = intval($cc);

            $class = $this->db->get_var("SELECT `Class_ID` FROM `Sub_Class`
        WHERE `Sub_Class_ID` = '" . $cc . "' AND `Class_Template_ID` = 0");

            if ($this->classID == $class) {
                $_cc = $cc;
            }
        }

        if (!$_cc) {
            return false;
        }

        // insert new forum
        $this->db->query("INSERT INTO `Forum_Subdivisions`
      (`Subdivision_ID`, `Sub_Class_ID`, `Type`) VALUES ('" . $forum . "', '" . $_cc . "', 'forum')");
    }

    /**
     * System events callback function
     *
     * @access public
     *
     * @param int $catalogue Catalogue_ID
     * @param int $sub Subdivision_ID
     * @param int $cc Sub_Class_ID
     */
    public function event_delete_cc($catalogue, $sub, $cc) {
        // validate
        $sub = intval($sub);

        $related_cc = $this->get_data($sub, "Sub_Class_ID");

        if (is_array($cc)) {
            if (!in_array($related_cc, $cc)) {
                return false;
            }
        }
        else {
            // validate
            $cc = intval($cc);
            if ($related_cc != $cc) {
                return false;
            }
        }

        // delete forum
        $this->event_delete($catalogue, $sub);
    }

    /**
     * System events callback function
     *
     * @access public
     *
     * @param int $catalogue Catalogue_ID
     * @param int $sub Subdivision_ID
     */
    public function event_delete($catalogue, $sub) {

        if (is_array($sub) && !empty($sub)) {
            $query_str = "IN (" . join(", ", $sub) . ")";
        }
        else {
            // validate
            $sub = intval($sub);
            $query_str = "= '" . $sub . "'";
        }

        // if forum2 subdivision
        // try to delete this forum
        if ($this->db->query("DELETE FROM `Forum_Subdivisions`
      WHERE `Subdivision_ID` " . $query_str . " AND `Type` = 'forum'")
        ) {
            // delete counted values for this forum
            $this->db->query("DELETE `Forum_Count`, `Forum_Topics`
        FROM `Forum_Count` INNER JOIN `Forum_Topics`
        WHERE `Forum_Count`.`Subdivision_ID` = `Forum_Topics`.`Subdivision_ID`
        AND `Forum_Count`.`Subdivision_ID` " . $query_str);
        }
    }

    /**
     * Update topic in `Forum_Count` method
     *
     * @access public
     *
     * @param string $action "+" or "-"
     * @param int $forum forum Subdivision_ID
     * @param int $topic last topic Message_ID
     */
    public function update_topic_count($action, $forum, $topic) {
        // validate
        if (!in_array($action, array("+", "-"))) {
            return false;
        }

        $forum = intval($forum);
        $topic = intval($topic);

        // get exist data
        $exist_id = $this->db->get_var("SELECT `ID` FROM `Forum_Count` WHERE `Subdivision_ID` = '" . $forum . "'");

        // insert or update data in forum relation table
        if ($exist_id) {
            $this->db->query("UPDATE `Forum_Count` SET `Topics` = `Topics` " . $action . " 1, `Last_Topic_ID` = '" . $topic . "' WHERE `ID` = '" . $exist_id . "'");
        }
        else {
            if ($action == "+") {
                $this->db->query("INSERT INTO `Forum_Count` (`Subdivision_ID`, `Topics`, `Last_Topic_ID`) VALUES ('" . $forum . "', 1, '" . $topic . "')");
            }
        }
    }

    /**
     * Update replies in `Forum_Count` method
     *
     * @access public
     *
     * @param string $action "+" or "-"
     * @param int $forum forum Subdivision_ID
     * @param int $reply reply Message_ID
     */
    public function update_replies_count($action, $forum, $reply) {
        // validate
        if (!in_array($action, array("+", "-"))) {
            return false;
        }

        $forum = intval($forum);
        $reply = intval($reply);

        // get exist data
        $exist_id = $this->db->get_var("SELECT `ID` FROM `Forum_Count` WHERE `Subdivision_ID` = '" . $forum . "'");

        // insert or update data in forum relation table
        if ($exist_id) {
            $this->db->query("UPDATE `Forum_Count` SET `Replies` = `Replies` " . $action . " 1, `Last_Reply_ID` = '" . $reply . "' WHERE `ID` = '" . $exist_id . "'");
        }
        else {
            if ($action == "+") {
                $this->db->query("INSERT INTO `Forum_Count` (`Subdivision_ID`, `Replies`, `Last_Reply_ID`) VALUES ('" . $forum . "', 1, '" . $reply . "')");
            }
        }
    }

    /**
     * Set description into the `Forum_Subdivisions`
     *
     * @param int forum id
     * @param string description
     *
     * @return bool rows affected
     */
    public function set_description($forum, $description) {
        // validate
        $forum = intval($forum);
        $description = $this->db->escape($description);

        $this->db->query("UPDATE `Forum_Subdivisions`
      SET `Description` = '" . $description . "'
      WHERE `Subdivision_ID` = '" . $forum . "'");

        return $this->db->rows_affected;
    }

    /**
     * Set group relation into the `Forum_Subdivisions`
     *
     * @param int forum id
     * @param int group id
     *
     * @return bool rows affected
     */
    public function set_group($forum, $group) {
        // validate
        $forum = intval($forum);
        $group = intval($group);

        $this->db->query("UPDATE `Forum_Subdivisions`
      SET `Group_ID` = '" . $group . "'
      WHERE `Subdivision_ID` = '" . $forum . "'");

        return $this->db->rows_affected;
    }

    /**
     * Check forum existance
     *
     * @access public
     *
     * @param int $parent_id Parent_Sub_ID
     * @param int $keyword EnglishName
     *
     * @return Subdivision_ID
     */
    public function check_existance($parent_id, $keyword) {
        // call as static
        static $storage = array();

        // validate
        $parent_id = intval($parent_id);
        $keyword = $this->db->escape($keyword);

        if (!isset($storage[$parent_id][$keyword])) {
            // get data
            $storage[$parent_id][$keyword] = $this->db->get_var("SELECT `Subdivision_ID` FROM `Subdivision`
        WHERE `EnglishName` = '" . $keyword . "' AND `Parent_Sub_ID` = '" . $parent_id . "'");
        }

        // return result
        return $storage[$parent_id][$keyword];
    }

    /**
     * Create forum method
     *
     * @access public
     *
     * @param int $parent_id parent Subdivision_ID
     * @param string $keyword forum keyword
     * @param string $name forum name
     * @param bool $checked forum checked
     * @param string $topic_keyword topic cc keyword
     * @param string $topic_name topic cc name
     * @param string $reply_keyword reply cc keyword
     * @param string $reply_name reply cc name
     * @param string $topic_rss_keyword topic RSS cc keyword
     * @param string $topic_rss_name topic RSS cc name
     * @param string $reply_rss_keyword reply RSS cc keyword
     * @param string $reply_rss_name reply RSS cc name
     *
     * @return bool
     */
    public function create($parent_id, $keyword, $name, $checked = true, $topic_keyword = "", $topic_name = "", $reply_keyword = "", $reply_name = "", $topic_rss_keyword = "", $topic_rss_name = "", $reply_rss_keyword = "", $reply_rss_name = "") {
        // system superior object
        $nc_core = nc_Core::get_object();

        // validate
        $parent_id = intval($parent_id);
        // forum
        if (!$nc_core->subdivision->validate_hidden_url($keyword)) {
            return false;
        }
        $keyword = $this->db->escape($keyword);
        $name = $this->db->escape($name);
        $name = str_replace('$', '&#36;', $name);
        $checked = (bool)$checked;
        // topic
        $topic_keyword = $topic_keyword ? $topic_keyword : NETCAT_MODULE_FORUM2_COMPONENT_FORUM_DEFAULT_TOPIC_KEYWORD;
        if ($nc_core->sub_class->validate_english_name($topic_keyword)) {
            $topic_keyword = $this->db->escape($topic_keyword);
        }
        else {
            return false;
        }
        $topic_name = $this->db->escape($topic_name ? $topic_name : NETCAT_MODULE_FORUM2_COMPONENT_FORUM_DEFAULT_TOPIC_NAME);
        // reply
        $reply_keyword = $reply_keyword ? $reply_keyword : NETCAT_MODULE_FORUM2_COMPONENT_FORUM_DEFAULT_REPLY_KEYWORD;
        if ($nc_core->sub_class->validate_english_name($reply_keyword)) {
            $reply_keyword = $this->db->escape($reply_keyword);
        }
        else {
            return false;
        }
        $reply_name = $this->db->escape($reply_name ? $reply_name : NETCAT_MODULE_FORUM2_COMPONENT_FORUM_DEFAULT_REPLY_NAME);
        // topic RSS
        $topic_rss_keyword = $topic_rss_keyword ? $topic_rss_keyword : NETCAT_MODULE_FORUM2_COMPONENT_FORUM_DEFAULT_TOPIC_RSS_KEYWORD;
        if ($nc_core->sub_class->validate_english_name($topic_rss_keyword)) {
            $topic_rss_keyword = $this->db->escape($topic_rss_keyword);
        }
        else {
            return false;
        }
        $topic_rss_name = $this->db->escape($topic_rss_name ? $topic_rss_name : NETCAT_MODULE_FORUM2_COMPONENT_FORUM_DEFAULT_TOPIC_RSS_NAME);
        // reply RSS
        $reply_rss_keyword = $reply_rss_keyword ? $reply_rss_keyword : NETCAT_MODULE_FORUM2_COMPONENT_FORUM_DEFAULT_REPLY_RSS_KEYWORD;
        if ($nc_core->sub_class->validate_english_name($reply_rss_keyword)) {
            $reply_rss_keyword = $this->db->escape($reply_rss_keyword);
        }
        else {
            return false;
        }
        $reply_rss_name = $this->db->escape($reply_rss_name ? $reply_rss_name : NETCAT_MODULE_FORUM2_COMPONENT_FORUM_DEFAULT_REPLY_RSS_NAME);

        // get parent data
        $parent_data = $this->db->get_row("SELECT `Catalogue_ID`, `Hidden_URL` FROM `Subdivision`
      WHERE `Subdivision_ID` = '" . $parent_id . "'", ARRAY_A);

        // error
        if (empty($parent_data)) {
            return false;
        }

        // forum priority
        $priority = $this->db->get_var("SELECT MAX(`Priority`) + 1 FROM `Subdivision` WHERE `Parent_Sub_ID` = '" . $parent_id . "'");

        // get objects
        $reply_obj = nc_forum2_reply::get_object();
        $topic_obj = nc_forum2_topic::get_object();

        // current timestamp
        $date = time();
        $catalogue = $parent_data['Catalogue_ID'];
        $parent_Hidden_URL = $parent_data['Hidden_URL'];

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_SUBDIVISION_CREATED, $catalogue, 0);

        // insert forum subdivision
        $this->db->query("INSERT INTO `Subdivision`
      (`Catalogue_ID`, `Parent_Sub_ID`, `Subdivision_Name`, `Template_ID`, `ExternalURL`, `EnglishName`, `LastUpdated`, `Created`, `Hidden_URL`, `Read_Access_ID`, `Write_Access_ID`, `Priority`, `Checked`, `Edit_Access_ID`, `Delete_Access_ID`, `Checked_Access_ID`, `Subscribe_Access_ID`" . ($nc_core->modules->get_by_keyword('cache') ? ", `Cache_Access_ID`" : "") . ")
      VALUES
      ('" . $catalogue . "', '" . $parent_id . "', '" . $name . "', 0, '', '" . $keyword . "', '" . $date . "', '" . $date . "', '" . $parent_Hidden_URL . $keyword . "/', 0, 2, '" . $priority . "', '" . $checked . "', 2, 2, 2, 2" . ($nc_core->modules->get_by_keyword('cache') ? ", 0" : "") . ")");

        if ($forum_id = $this->db->insert_id) {
            // execute core action
            $nc_core->event->execute(nc_Event::AFTER_SUBDIVISION_CREATED, $catalogue, $forum_id);
        }
        else {
            return false;
        }

        // topic component settings template
        $settings = $nc_core->component->get_by_id($topic_obj->get_class_id(), "CustomSettingsTemplate");
        if (empty($settings)) {
            $settings = array();
        }

        // component settings object
        $cc_settings_obj = new nc_a2f($settings, "CustomSettings");

        // component settings values string
        $cc_settings_str = $cc_settings_obj->get_values_as_string();

        // topic RSS component settings template
        $rss_settings = $nc_core->component->get_by_id($topic_obj->get_rss_class_id(), "CustomSettingsTemplate");
        // component settings object
        $rss_cc_settings_obj = new nc_a2f($rss_settings, "CustomSettings");
        // component settings values string
        $rss_cc_settings_str = $rss_cc_settings_obj->get_values_as_string();

        // Sub_Class `Priority` increment
        $sub_class_priority = 0;

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_INFOBLOCK_CREATED, $catalogue, $forum_id, 0);

        // insert topic subclass
        $this->db->query("INSERT INTO `Sub_Class`
            (`Subdivision_ID`, `Class_ID`, `Sub_Class_Name`, `Priority`, `EnglishName`, `Checked`, `Catalogue_ID`, `AllowTags`, `Created`, `LastUpdated`, `DefaultAction`, `NL2BR`, `UseCaptcha`, `CustomSettings`, `Read_Access_ID`, `Write_Access_ID`, `Edit_Access_ID`, `Subscribe_Access_ID`, `Delete_Access_ID`, `Checked_Access_ID`, `AllowRSS`)
            VALUES
            ('" . $forum_id . "', '" . $topic_obj->get_class_id() . "', '" . $topic_name . "', '" . ($sub_class_priority++) . "', '" . $topic_keyword . "', 1, '" . $catalogue . "', -1, '" . $date . "', '" . $date . "', 'index', -1, -1, '" . $this->db->prepare($cc_settings_str) . "', '0', '0', '0', '0', '0', '0', '1')");

        if ($topic_cc_id = $this->db->insert_id) {
            // execute core action
            $nc_core->event->execute(nc_Event::AFTER_INFOBLOCK_CREATED, $catalogue, $forum_id, $topic_cc_id);
        }

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_INFOBLOCK_CREATED, $catalogue, $forum_id, 0);

        // insert reply subclass
        $this->db->query("INSERT INTO `Sub_Class`
            (`Subdivision_ID`, `Class_ID`, `Sub_Class_Name`, `Priority`, `EnglishName`, `Checked`, `Catalogue_ID`, `AllowTags`, `Created`, `LastUpdated`, `DefaultAction`, `NL2BR`, `UseCaptcha`, `CustomSettings`, `Read_Access_ID`, `Write_Access_ID`, `Edit_Access_ID`, `Subscribe_Access_ID`, `Delete_Access_ID`, `Checked_Access_ID`, `AllowRSS`)
            VALUES
            ('" . $forum_id . "', '" . $reply_obj->get_class_id() . "', '" . $reply_name . "', '" . ($sub_class_priority++) . "', '" . $reply_keyword . "', 1, '" . $catalogue . "', -1, '" . $date . "', '" . $date . "', 'index', -1, -1, '', '0', '0', '0', '0', '0', '0', '1')");

        if ($reply_cc_id = $this->db->insert_id) {
            // execute core action
            $nc_core->event->execute(nc_Event::AFTER_INFOBLOCK_CREATED, $catalogue, $forum_id, $reply_cc_id);
        }

        return $forum_id;
    }

    /**
     * Count user messages method
     *
     * @access public
     *
     * @param int $user_id user User_ID
     * @param int $sub forum Subdivision_ID
     * @param int $type count type (0 - all, 1 - only topics, 2 - only replies)
     *
     * @return int counted result
     */
    public function count_user_messages($user_id, $sub = 0, $type = 0) {
        // call as static
        static $storage = array();

        // validate
        $user_id = intval($user_id);
        $sub = intval($sub);

        if (empty($storage[$user_id][$sub])) {
            // get objects
            $reply_obj = nc_forum2_reply::get_object();

            // get counted values
            $topics = $this->db->get_var("SELECT COUNT(`Message_ID`) FROM `Message" . $this->classID . "`
        WHERE `User_ID` = '" . $user_id . "'" . ($sub ? " AND `Subdivision_ID` = '" . $sub . "'" : ""));
            // get counted values
            $replies = $this->db->get_var("SELECT COUNT(`Message_ID`) FROM `Message" . $reply_obj->get_class_id() . "`
        WHERE `User_ID` = '" . $user_id . "'" . ($sub ? " AND `Subdivision_ID` = '" . $sub . "'" : ""));

            // get counted values
            $storage[$user_id][$sub] = array($topics, $replies);
        }

        // get values
        list($topics, $replies) = $storage[$user_id][$sub];

        // select result
        switch ($type) {
            // topics count
            case 1:
                return $topics;
                break;
            // replies count
            case 2:
                return $replies;
                break;
            // all messages count
            default:
                return ($topics + $replies);
        }
    }

}

