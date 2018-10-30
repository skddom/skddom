<?php

/* $Id: nc_forum2_parent.class.php 4290 2011-02-23 15:32:35Z denis $ */
if (!class_exists("nc_System")) die("Unable to load file.");

/**
 * class nc_forum2_parent
 * @package nc_forum2
 */
class nc_forum2_parent extends nc_forum2 {

    /**
     * Constructor method
     * @access protected
     */
    protected function __construct() {
        // parent
        parent::__construct();

        // system superior object
        $nc_core = nc_Core::get_object();

        // parent class
        $this->classID = $this->MODULE_VARS['FORUM_CLASS_ID'];

        // RSS class
        $this->rss_classID = $this->MODULE_VARS['FORUM_RSS_CLASS_ID'];

        // bind events
        if ($this->MODULE_VARS['TRACKING_MODE']) {
            $nc_core->event->bind($this, array(nc_Event::AFTER_INFOBLOCK_CREATED => 'event_add_cc'));
        }
        // $nc_core->event->bind($this, array(nc_Event::AFTER_INFOBLOCK_DELETED => 'event_delete_cc'));
        $nc_core->event->bind($this, array(nc_Event::AFTER_SUBDIVISION_DELETED => 'event_delete'));
    }

    /**
     * Get or instance self object
     * @static
     * @access public
     *
     * @return nc_forum2_parent self object
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
     * Get RSS url
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
        WHERE `Subdivision_ID` = '".$id."' AND `Type` = 'parent'", ARRAY_A);
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
     * @access public
     *
     * @param int $catalogue Catalogue_ID
     * @param int $sub Subdivision_ID
     * @param int $cc Sub_Class_ID
     */
    public function event_add_cc($catalogue, $sub, $cc) {
        // validate
        $forum = intval($sub);

        // system superior object
        $nc_core = nc_Core::get_object();

        if (is_array($cc)) {
            $cc = array_map('intval', $cc);
            $data = $this->db->get_results("SELECT `Sub_Class_ID`, `Class_ID` FROM `Sub_Class`
        WHERE `Sub_Class_ID` IN (".join(", ", $cc).") AND `Class_Template_ID` = 0", ARRAY_A);

            if (!empty($data)) {
                foreach ($data as $row) {
                    if ($this->classID == $row['Class_ID']) {
                        $_cc = $row['Sub_Class_ID'];
                        break;
                    }
                }
            }
        } else {
            // validate
            $cc = intval($cc);

            $class = $this->db->get_var("SELECT `Class_ID` FROM `Sub_Class`
        WHERE `Sub_Class_ID` = '".$cc."' AND `Class_Template_ID` = 0");

            if ($this->classID == $class) {
                $_cc = $cc;
            }
        }

        if (!$_cc) return false;

        // insert new parent
        $this->db->query("INSERT INTO `Forum_Subdivisions`
      (`Subdivision_ID`, `Sub_Class_ID`, `Type`) VALUES ('".$forum."', '".$_cc."', 'parent')");

        // no cache
        if ($nc_core->modules->get_by_keyword('cache')) {
            $this->db->query("UPDATE `Subdivision` SET `Cache_Access_ID` = 2
        WHERE `Subdivision_ID` = '".$forum."'");
        }
    }

    /**
     * System events callback function
     * @access public
     *
     * @param int $catalogue Catalogue_ID
     * @param int $sub Subdivision_ID
     * @param int $cc Sub_Class_ID
     */
    public function event_delete_cc($catalogue, $sub, $cc) {
        // validate
        $sub = intval($sub);

        /* $related_cc = $this->get_data($sub, "Sub_Class_ID");

          if ( is_array($cc) ) {
          if ( !in_array($related_cc, $cc) ) return false;
          }
          else {
          // validate
          $cc = intval($cc);
          if ($related_cc!=$cc) return false;
          } */
    }

    /**
     * System events callback function
     * @access public
     *
     * @param int $catalogue Catalogue_ID
     * @param int $sub Subdivision_ID
     */
    public function event_delete($catalogue, $sub) {

        if (is_array($sub) && !empty($sub)) {
            $query_str = "IN (".join(",", $sub).")";
        } else {
            // validate
            $sub = intval($sub);
            $query_str = "= '".$forum_id."'";
        }

        // forum2 parent delete
        $this->db->query("DELETE FROM `Forum_Subdivisions`
      WHERE `Subdivision_ID` ".$query_str." AND `Type` = 'parent'");

        if (!$this->db->rows_affected) return false;

        $this->db->query("DELETE FROM `Forum_Groups`
      WHERE `Subdivision_ID` ".$query_str);

        // get all forums into the current parent
        $forums = $this->db->get_col("SELECT `Subdivision_ID` FROM `Subdivision` WHERE `Parent_Sub_ID` ".$query_str);
        if (!empty($forums)) {
            // get forum object
            $forum_obj = nc_forum2_forum::get_object();
            // delete forums
            foreach ($forums as $forum) {
                // delete forums
                $forum_obj->event_delete($catalogue, $forum);
            }
        }
    }

    /**
     * Create parent forum method
     * @access public
     *
     * @param int $parent_id parent Subdivision_ID
     * @param string $keyword forum keyword
     * @param string $name forum name
     * @param bool $checked forum checked
     * @param string $rss_keyword parent RSS cc keyword
     * @param string $rss_name parent RSS cc name
     *
     * @return bool
     */
    public function create($catalogue_id, $parent_id = 0, $keyword, $name, $checked = true, $rss_keyword = "", $rss_name = "") {
        // system superior object
        $nc_core = nc_Core::get_object();

        // validate
        $catalogue_id = intval($catalogue_id);
        $parent_id = intval($parent_id);
        // forum
        if (!$nc_core->subdivision->validate_hidden_url($keyword)) return false;
        $keyword = $this->db->escape($keyword);
        $name = $this->db->escape($name);
        $checked = (bool) $checked;
        // forum RSS
        $rss_keyword = $rss_keyword ? $rss_keyword : NETCAT_MODULE_FORUM2_COMPONENT_FORUM_DEFAULT_PARENT_RSS_KEYWORD;
        if ($nc_core->sub_class->validate_english_name($rss_keyword)) {
            $rss_keyword = $this->db->escape($rss_keyword);
        } else return false;
        $rss_name = $this->db->escape($rss_name ? $rss_name : NETCAT_MODULE_FORUM2_COMPONENT_FORUM_DEFAULT_PARENT_RSS_NAME);

        if ($parent_id) {
            // get parent data
            $parent_data = $this->db->get_row("SELECT `Catalogue_ID`, `Hidden_URL` FROM `Subdivision`
        WHERE `Subdivision_ID` = '".$parent_id."'", ARRAY_A);

            // error
            if (empty($parent_data)) return false;
        }
        else {
            $parent_data['Catalogue_ID'] = $catalogue_id;
            $parent_data['Hidden_URL'] = "/";
        }

        // forum priority
        $priority = $this->db->get_var("SELECT MAX(`Priority`) + 1 FROM `Subdivision`".($parent_id ? " WHERE `Parent_Sub_ID` = '".$parent_id."'" : ""));

        // get objects
        $parent_obj = nc_forum2_parent::get_object();

        // current timestamp
        $date = time();
        $catalogue = $parent_data['Catalogue_ID'];
        $parent_Hidden_URL = $parent_data['Hidden_URL'];

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_SUBDIVISION_CREATED, $catalogue, 0);

        // insert forum subdivision
        $this->db->query("INSERT INTO `Subdivision`
      (`Catalogue_ID`, `Parent_Sub_ID`, `Subdivision_Name`, `Template_ID`, `ExternalURL`, `EnglishName`, `LastUpdated`, `Created`, `Hidden_URL`, `Read_Access_ID`, `Write_Access_ID`, `Priority`, `Checked`, `Edit_Access_ID`, `Delete_Access_ID`, `Checked_Access_ID`, `Subscribe_Access_ID`".($nc_core->modules->get_by_keyword('cache') ? ", `Cache_Access_ID`" : "").")
      VALUES
      ('".$catalogue."', '".$parent_id."', '".$name."', 0, '', '".$keyword."', '".$date."', '".$date."', '".$parent_Hidden_URL.$keyword."/', 0, 2, '".$priority."', '".$checked."', 2, 2, 2, 2".($nc_core->modules->get_by_keyword('cache') ? ", 0" : "").")");

        if ($forum_id = $this->db->insert_id) {
            // execute core action
            $nc_core->event->execute(nc_Event::AFTER_SUBDIVISION_CREATED, $catalogue, $forum_id);
        }
        else return false;

        // parent component settings template
        $settings = $nc_core->component->get_by_id($parent_obj->get_class_id(), "CustomSettingsTemplate");
        // component settings object
        if ($settings) {
            $cc_settings_obj = new nc_a2f($settings, "", "CustomSettings");
            // component settings values string
            $cc_settings_str = $cc_settings_obj->get_values_as_string();
        } else {
            $cc_settings_str = "";
        }

        // parent RSS component settings template
        $rss_settings = $nc_core->component->get_by_id($parent_obj->get_rss_class_id(), "CustomSettingsTemplate");
        // component settings object
        if ($rss_settings) {
            $rss_cc_settings_obj = new nc_a2f($rss_settings, "", "CustomSettings");
            // component settings values string
            $rss_cc_settings_str = $rss_cc_settings_obj->get_values_as_string();
        } else {
            $rss_cc_settings_str = "";
        }

        // Sub_Class `Priority` increment
        $sub_class_priority = 0;

        // execute core action
        $nc_core->event->execute(nc_Event::BEFORE_INFOBLOCK_CREATED, $catalogue, $forum_id, 0);

        // insert topic subclass
        $this->db->query("INSERT INTO `Sub_Class`
			(`Subdivision_ID`, `Class_ID`, `Sub_Class_Name`, `Priority`, `EnglishName`, `Checked`, `Catalogue_ID`, `AllowTags`, `Created`, `LastUpdated`, `DefaultAction`, `NL2BR`, `UseCaptcha`, `CustomSettings`, `Read_Access_ID`, `Write_Access_ID`, `Edit_Access_ID`, `Subscribe_Access_ID`, `Delete_Access_ID`, `Checked_Access_ID`)
			VALUES
			('".$forum_id."', '".$parent_obj->get_class_id()."', '".$name."', '".($sub_class_priority++)."', '".$keyword."', 1, '".$catalogue."', -1, '".$date."', '".$date."', 'index', -1, -1, '".$this->db->prepare($cc_settings_str)."', '0', '0', '0', '0', '0', '0')");

        if ($parent_cc_id = $this->db->insert_id) {
            // execute core action
            $nc_core->event->execute(nc_Event::AFTER_INFOBLOCK_CREATED, $catalogue, $forum_id, $parent_cc_id);
        }

        // return forum Subdivision_ID as result
        return $forum_id;
    }

    /**
     * Count user messages method
     * @access public
     *
     * @param int $user_id user User_ID
     * @param int $sub forums parent Subdivision_ID
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

        if (empty($storage[0][$user_id][$sub])) {
            // get objects
            $topic_obj = nc_forum2_topic::get_object();
            $reply_obj = nc_forum2_reply::get_object();

            if ($sub && empty($storage[1][$user_id][$sub])) {
                $storage[1][$user_id][$sub] = $this->db->get_col("SELECT DISTINCT s.`Subdivision_ID` FROM `Subdivision` AS s
          LEFT JOIN `Sub_Class` AS sc ON s.`Subdivision_ID` = sc.`Subdivision_ID`
          WHERE s.`Parent_Sub_ID` = '".$sub."'
            AND sc.`Class_ID` = '".$topic_obj->get_class_id()."'");
            }
            // forum subs
            $subs = $storage[1][$user_id][$sub];

            // get counted values
            $topics = $this->db->get_var("SELECT COUNT(`Message_ID`) FROM `Message".$topic_obj->get_class_id()."`
        WHERE `User_ID` = '".$user_id."'".(!empty($subs) ? " AND `Subdivision_ID` IN (".join(",", $subs).")" : ""));
            // get counted values
            $replies = $this->db->get_var("SELECT COUNT(`Message_ID`) FROM `Message".$reply_obj->get_class_id()."`
        WHERE `User_ID` = '".$user_id."'".(!empty($subs) ? " AND `Subdivision_ID` IN (".join(",", $subs).")" : ""));

            // get counted values
            $storage[0][$user_id][$sub] = array($topics, $replies);
        }

        // get values
        list($topics, $replies) = $storage[0][$user_id][$sub];

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

    /**
     * Get users ids method
     * @access public
     *
     * @param int $sub forums parent Subdivision_ID
     * @param int $type count type (0 - all, 1 - only topics, 2 - only replies)
     *
     * @return array users ids result
     */
    public function get_users($sub = 0, $type = 0) {
        // call as static
        static $storage = array();

        // validate
        $sub = intval($sub);

        if (empty($storage[0][$sub])) {
            // get objects
            $topic_obj = nc_forum2_topic::get_object();
            $reply_obj = nc_forum2_reply::get_object();

            if ($sub && empty($storage[1][$sub])) {
                $storage[1][$sub] = $this->db->get_col("SELECT DISTINCT s.`Subdivision_ID` FROM `Subdivision` AS s
          LEFT JOIN `Sub_Class` AS sc ON s.`Subdivision_ID` = sc.`Subdivision_ID`
          WHERE s.`Parent_Sub_ID` = '".$sub."'
            AND sc.`Class_ID` = '".$topic_obj->get_class_id()."'");
            }
            // forum subs
            $subs = $storage[1][$sub];

            // get users ids
            $topics_users = $this->db->get_col("SELECT DISTINCT `User_ID` FROM `Message".$topic_obj->get_class_id()."`
        ".(!empty($subs) ? "WHERE `Subdivision_ID` IN (".join(",", $subs).")" : ""));
            // get users ids
            $replies_users = $this->db->get_col("SELECT DISTINCT `User_ID` FROM `Message".$reply_obj->get_class_id()."`
        ".(!empty($subs) ? "WHERE `Subdivision_ID` IN (".join(",", $subs).")" : ""));

            // get users ids
            $storage[0][$sub] = array($topics_users, $replies_users);
        }

        // get values
        list($topics_users, $replies_users) = $storage[0][$sub];

        // select result
        switch ($type) {
            // topics users
            case 1:
                return (array) $topics_users;
                break;
            // replies usres
            case 2:
                return (array) $replies_users;
                break;
            // all users
            default:
                return array_unique(array_merge((array) $topics_users, (array) $replies_users));
        }
    }

    /**
     * Set description into the `Forum_Subdivisions`
     *
     * @param int parent id
     * @param string description
     *
     * @return bool rows affected
     */
    public function set_description($parent, $description) {
        // validate
        $parent = intval($parent);
        $description = $this->db->escape($description);

        $this->db->query("UPDATE `Forum_Subdivisions`
      SET `Description` = '".$description."'
      WHERE `Subdivision_ID` = '".$parent."'");

        return $this->db->rows_affected;
    }

}
?>