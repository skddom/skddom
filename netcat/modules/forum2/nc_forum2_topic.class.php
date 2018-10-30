<?php

/**
 * class nc_forum2_topic
 *
 * @package nc_forum2
 */
class nc_forum2_topic extends nc_forum2 {

    /**
     * Constructor method
     *
     * @access protected
     */
    protected function __construct() {
        parent::__construct();

        $nc_core = nc_Core::get_object();
        $this->classID = $this->MODULE_VARS['TOPIC_CLASS_ID'];
        $this->rss_classID = $this->MODULE_VARS['TOPIC_RSS_CLASS_ID'];

        $nc_core->event->bind($this, array(nc_Event::AFTER_OBJECT_CREATED => 'event_add'));
        $nc_core->event->bind($this, array(nc_Event::AFTER_OBJECT_DELETED => 'event_delete'));
    }

    /**
     * Get or instance self object
     *
     * @static
     * @access public
     *
     * @return nc_forum2_topic self object
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
     * Update replies in `Forum_Topics` method
     *
     * @access public
     *
     * @param string $action "+" or "-"
     * @param int $forum forum Subdivision_ID
     * @param int $topic topic Message_ID
     * @param int $reply reply Message_ID
     */
    public function update_replies_count($action, $forum, $topic, $reply) {

        if (!in_array($action, array("+", "-"))) {
            return false;
        }

        $forum = intval($forum);
        $topic = intval($topic);
        $reply = intval($reply);

        // get exist data
        $exist_id = $this->db->get_var("SELECT `ID` FROM `Forum_Topics` WHERE `Subdivision_ID` = '" . $forum . "' AND `Topic_ID` = '" . $topic . "'");

        // insert or update data in topic relation table
        if ($exist_id) {
            $this->db->query("UPDATE `Forum_Topics` SET `Replies` = `Replies` " . $action . " 1, `Last_Reply_ID` = '" . $reply . "' WHERE `ID` = '" . $exist_id . "'");
        }
        else {
            if ($action == "+") {
                $this->db->query("INSERT INTO `Forum_Topics` (`Subdivision_ID`, `Topic_ID`, `Replies`, `Last_Reply_ID`) VALUES ('" . $forum . "', '" . $topic . "', 1, '" . $reply . "')");
            }
        }
    }

    /**
     * Update `LastUpdated` field
     *
     * @access public
     *
     * @param int $topic topic Message_ID
     *
     * @return bool attempt result
     */
    public function update_last_updated($topic_id) {
        // validate
        $topic_id = intval($topic_id);

        // update last updated field
        $this->db->query("UPDATE `Message" . $this->classID . "` SET `LastUpdated` = CURRENT_TIMESTAMP()
      WHERE `Message_ID` = '" . $topic_id . "'");

        // return attempt result
        return $this->db->rows_affected;
    }

    /**
     * Update topic views count method
     *
     * @access public
     *
     * @param int $topic_id topic Message_ID
     *
     * @return bool attempt result
     */
    public function update_views($cc_array, $topic_id) {
        // validate
        $cc_array = array_map("intval", $cc_array);
        $topic_id = intval($topic_id);

        // update topic count
        $this->db->query("UPDATE `Message" . $this->classID . "` SET `Views` = IF(`Views` IS NULL, 1, `Views` + 1), `LastUpdated` = `LastUpdated`
      WHERE `Message_ID` = '" . $topic_id . "'");

        // drop topics list cache
        //$nc_cache_list = nc_cache_list::getObject();    
        //$nc_cache_list->dropSubdivisionCache($cc_array['Catalogue_ID'], $cc_array['Subdivision_ID']);
        // return attempt result
        return $this->db->rows_affected;
    }

    /**
     * Delete topic replies by topic id method
     *
     * @access private
     *
     * @param int $catalogue_id catalogue Catalogue_ID
     * @param int $forum_id forum Subdivision_ID
     * @param int $topic_id topic Message_ID
     *
     * @return bool
     */
    private function _delete_replies($catalogue_id, $forum_id, $topic_id) {
        // validate
        $catalogue_id = intval($catalogue_id);
        $forum_id = intval($forum_id);
        $topic_id = intval($topic_id);

        // system superior object
        $nc_core = nc_Core::get_object();

        // get objects
        $reply_obj = nc_forum2_reply::get_object();

        // count replies to delete
        $replies = $this->db->get_results("SELECT `Subdivision_ID`, `Sub_Class_ID`, `Message_ID`
      FROM `Message" . $reply_obj->get_class_id() . "`
      WHERE `Topic_ID` = '" . $topic_id . "'", ARRAY_A);

        if (!empty($replies)) {
            // include files api
            // delete replies and files
            foreach ($replies as $reply) {
                // get reply data
                $reply_data[$reply['Message_ID']] = $this->db->get_row("SELECT * FROM `Message" . $reply_obj->get_class_id() . "` WHERE `Message_ID` = '" . $reply['Message_ID'] . "'");

                // execute core action
                $nc_core->event->execute(nc_Event::BEFORE_OBJECT_DELETED, $catalogue_id, $reply['Subdivision_ID'], $reply['Sub_Class_ID'], $reply_obj->get_class_id(), $reply['Message_ID'], $reply_data);

                // delete files
                DeleteMessageFiles($reply_obj->get_class_id(), $reply['Message_ID']);

                // delete all topic replies
                $this->db->query("DELETE FROM `Message" . $reply_obj->get_class_id() . "` WHERE `Message_ID` = '" . $reply['Message_ID'] . "'");
                // execute core action
                $nc_core->event->execute(nc_Event::AFTER_OBJECT_DELETED, $catalogue_id, $reply['Subdivision_ID'], $reply['Sub_Class_ID'], $reply_obj->get_class_id(), $reply['Message_ID'], $reply_data);
            }
        }

        return true;
    }

    /**
     * Get topic url by topic id method
     *
     * @access public
     *
     * @param int $topic_id topic Message_ID
     *
     * @return string topic url in simple or admin_mode
     */
    public function get_url($topic_id) {
        // validate
        $topic_id = intval($topic_id);

        // system superior object
        $nc_core = nc_Core::get_object();

        if (!$nc_core->admin_mode) {
            $result = nc_object_path($this->classID, $topic_id);
        }
        else {
            // admin mode
            $result = $this->db->get_var(
                "SELECT CONCAT(
                            '" . $nc_core->SUB_FOLDER . $nc_core->HTTP_ROOT_PATH . "full.php?catalogue=', cc.`Catalogue_ID`,
                            '&sub=', tm.`Subdivision_ID`,
                            '&cc=', tm.`Sub_Class_ID`,
                            '&message=', tm.`Message_ID`
                        )
                   FROM `Message" . $this->classID . "` AS tm
                        LEFT JOIN `Sub_Class` AS cc ON tm.`Sub_Class_ID` = cc.`Sub_Class_ID`
                  WHERE tm.`Message_ID` = '" . $topic_id . "'");
        }

        // return topic url
        return $result;
    }

    /**
     * Get RSS url
     *
     * @static
     * @access public
     *
     * @param int topic Message_ID
     *
     * @return string RSS url
     */
    public function get_rss_url($forum_id, $topic_id) {
        // storage variable
        static $storage = array();

        // validate
        $forum_id = intval($forum_id);
        $topic_id = intval($topic_id);

        if (!isset($storage[$forum_id])) {
            // get RSS url
            $storage[$forum_id] = $this->db->get_var(
                "SELECT cc.`Sub_Class_ID`
                   FROM `Sub_Class` AS cc
                        LEFT JOIN `Subdivision` AS sub ON cc.`Subdivision_ID` = sub.`Subdivision_ID`
                  WHERE cc.`Subdivision_ID` = '" . $forum_id . "'
                    AND cc.`Class_Template_ID` = '" . $this->get_rss_class_id() . "'");
        }

        // return RSS url
        return $storage[$forum_id] ? nc_infoblock_path($storage[$forum_id], 'index', 'rss') : false;
    }

    /**
     * Get topic views count method
     *
     * @access public
     *
     * @param int $topic_id topic Message_ID
     *
     * @return int counted value
     */
    public function get_views($topic_id) {
        // validate
        $topic_id = intval($topic_id);

        // return topic count
        return $this->db->get_var("SELECT `Views` FROM `Message" . $this->classID . "` WHERE `Message_ID` = '" . $topic_id . "'");
    }

    /**
     * Get topic "Closed" status
     *
     * @access public
     *
     * @param int $topic_id topic Message_ID
     *
     * @return bool closed or not
     */
    public function get_closed_status($topic_id) {
        // validate
        $topic_id = intval($topic_id);

        return $this->db->get_var("SELECT `Closed` FROM `Message" . $this->classID . "` WHERE `Message_ID` = '" . $topic_id . "'");
    }

    /**
     * Get last messages ids method
     *
     * @param int forum id
     * @param int LIMIT
     *
     * @return array messages ids
     */
    public function get_last_ids($sub, $limit = 0) {
        // static storage
        static $storage = array();

        // validate
        $forum = intval($sub);

        if (!isset($storage[$forum])) {
            // get ids
            $storage[$forum] = $this->db->get_col("SELECT `Message_ID` FROM `Message" . $this->classID . "`
        WHERE `Subdivision_ID` = '" . $forum . "'");
        }

        $result = ($limit ? array_slice($storage[$forum], 0, $limit) : $storage[$forum]);

        // return result
        return $result;
    }

    /**
     * System events callback function
     *
     * @access public
     *
     * @param int $catalogue Catalogue_ID
     * @param int $sub Subdivision_ID
     * @param int $cc Sub_Class_ID
     * @param int $class Class_ID
     * @param int $message Message_ID
     */
    public function event_add($catalogue, $sub, $cc, $class, $message) {
        // only forum2 replies class need it!
        if ($class != $this->classID) {
            return false;
        }

        $forum = intval($sub);
        $topics = (array)$message;

        foreach ($topics as $topic) {
            $this->db->query("INSERT INTO `Forum_Topics`
        (`Subdivision_ID`, `Topic_ID`, `Replies`, `Last_Reply_ID`)
        VALUES
        (" . $forum . ", " . $topic . ", 0, 0)");
        }

        // get objects
        $forum_obj = nc_forum2_forum::get_object();

        $forum_obj->update_topic_count("+", $forum, $topic);

        return true;
    }

    /**
     * System events callback function
     *
     * @access public
     *
     * @param int $catalogue Catalogue_ID
     * @param int $sub Subdivision_ID
     * @param int $cc Sub_Class_ID
     * @param int $class Class_ID
     * @param int $message Message_ID
     */
    public function event_delete($catalogue, $sub, $cc, $class, $message) {
        // only forum2 replies class need it!
        if ($class != $this->classID) {
            return false;
        }

        $forum = intval($sub);
        $topics = (array)$message;

        // get last topic
        $last_topic_id = $this->db->get_var("SELECT MAX(`Message_ID`) FROM `Message" . $this->classID . "`
      WHERE `Subdivision_ID` = '" . $forum . "'");

        // get objects
        $forum_obj = nc_forum2_forum::get_object();

        // update forum count
        $forum_obj->update_topic_count("-", $forum, $last_topic_id);

        foreach ($topics as $topic) {
            // delete topic replies
            $this->_delete_replies($catalogue, $forum, $topic);
        }

        return true;
    }

}
