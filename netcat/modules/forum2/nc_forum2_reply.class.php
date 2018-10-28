<?php

/**
 * class nc_forum2_reply
 * @package nc_forum2
 */
class nc_forum2_reply extends nc_forum2 {

    /**
     * Constructor method
     * @access protected
     */
    protected function __construct() {
        // parent
        parent::__construct();

        // system superior object
        $nc_core = nc_Core::get_object();

        // this class
        $this->classID = $this->MODULE_VARS['REPLY_CLASS_ID'];

        // RSS class
        $this->rss_classID = $this->MODULE_VARS['REPLY_RSS_CLASS_ID'];

        // bind events
        $nc_core->event->bind($this, array(nc_Event::AFTER_OBJECT_CREATED => "event_add"));
        $nc_core->event->bind($this, array(nc_Event::AFTER_OBJECT_UPDATED => "event_update"));
        $nc_core->event->bind($this, array(nc_Event::AFTER_OBJECT_DELETED => "event_delete"));
    }

    /**
     * Get or instance self object
     * @static
     * @access public
     *
     * @return nc_forum2_reply self object
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
     * Get topic url by reply id method
     * @access public
     *
     * @param int $reply_id Message_ID
     *
     * @return string topic url in simple or admin_mode
     */
    public function get_topic_url($reply_id) {
        // validate
        $reply_id = intval($reply_id);

        // system superior object
        $nc_core = nc_Core::get_object();

        // get objects
        $topic_obj = nc_forum2_topic::get_object();

        if (!$nc_core->admin_mode) {
            $result = nc_object_path($topic_obj->get_class_id(), $this->get_topic_id($reply_id));
        }
        else {
            // admin mode
            $result = $this->db->get_var(
                "SELECT CONCAT(
                            '".$nc_core->SUB_FOLDER.$nc_core->HTTP_ROOT_PATH."full.php?catalogue=', cc.`Catalogue_ID`,
                            '&sub=', tm.`Subdivision_ID`,
                            '&cc=', tm.`Sub_Class_ID`,
                            '&message=', tm.`Message_ID`
                        )
                   FROM `Message".$this->classID."` AS rm
                        LEFT JOIN `Message".$topic_obj->get_class_id()."` AS tm ON rm.`Topic_ID` = tm.`Message_ID`
                        LEFT JOIN `Sub_Class` AS cc ON tm.`Sub_Class_ID` = cc.`Sub_Class_ID`
                   WHERE rm.`Message_ID` = '".$reply_id."'");
        }

        // return topic url
        return $result;
    }

    /**
     * Get topic id by reply id method
     * @access public
     *
     * @param int $reply_id Message_ID
     *
     * @return int Topic_ID (Message_ID)
     */
    public function get_topic_id($reply_id) {
        // validate
        $reply_id = intval($reply_id);

        return $this->db->get_var("SELECT `Topic_ID` FROM `Message".$this->classID."` WHERE `Message_ID` = '".$reply_id."'");
    }

    /**
     * Get curPos for the last reply
     * @access public
     *
     * @param int $topic_id Message_ID
     *
     * @return int curPos
     */
    public function get_curpos_by_topic_id($topic_id) {
        // storage variable
        static $storage = array();

        // validate
        $topic_id = intval($topic_id);

        if (!isset($storage[$topic_id])) {
            // system superior object
            $nc_core = nc_Core::get_object();

            // get RecordsPerPage value from the component
            $records_per_page = $nc_core->component->get_by_id($this->classID, "RecordsPerPage");

            // return if 0
            if (!$records_per_page) return 0;

            // get curPos value
            $mess_counted = $this->db->get_var("SELECT COUNT(`Message_ID`) FROM `Message".$this->classID."`
        WHERE `Topic_ID` = '".$topic_id."'");

            // determine curPos
            $storage[$topic_id] = $mess_counted > 0 ? floor(--$mess_counted / $records_per_page) * $records_per_page : 0;
        }

        // return curPos value
        return $storage[$topic_id];
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
            $storage[$forum] = $this->db->get_col("SELECT `Message_ID` FROM `Message".$this->classID."`
        WHERE `Subdivision_ID` = '".$forum."'");
        }

        $result = ($limit ? array_slice($storage[$forum], 0, $limit) : $storage[$forum]);

        // return result
        return $result;
    }

    /**
     * Check nc_objects_list() or s_list_class() parent function by backtrace
     * @access private
     *
     * @return bool
     */
    private function _possibility() {
        // this function must be called only from s_list_class() function
        $debug_backtrace = debug_backtrace();
        // get function from calling this method
        $deb_value = $debug_backtrace[2];
        if ($deb_value['function'] == "s_list_class" || $deb_value['function'] == "nc_objects_list") {
            return true;
        }
        // wrong
        return false;
    }

    /**
     * System events callback function
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
        if ($class != $this->classID) return false;

        // validate
        $forum = intval($sub);
        $replies = (array) $message;

        $last_reply = array_pop($replies);

        // get topic id
        $topic = $this->db->get_var("SELECT `Topic_ID` FROM `Message".$this->classID."`
      WHERE `Message_ID` = '".$last_reply."'");

        // get objects
        $forum_obj = nc_forum2_forum::get_object();
        $topic_obj = nc_forum2_topic::get_object();

        // update data
        $forum_obj->update_replies_count("+", $forum, $last_reply);
        $topic_obj->update_replies_count("+", $forum, $topic, $last_reply);
        // LastUpdated SQL field refresh
        $topic_obj->update_last_updated($topic);

        return true;
    }

    /**
     * System events callback function
     * @access public
     *
     * @param int $catalogue Catalogue_ID
     * @param int $sub Subdivision_ID
     * @param int $cc Sub_Class_ID
     * @param int $class Class_ID
     * @param mixed $message array of Message_ID
     * @param mixed $message_data array of data of deleted messages
     */
    public function event_delete($catalogue, $sub, $cc, $class, $message, $message_data=NULL) {
        // only forum2 replies class need it!
        if ($class != $this->classID) return false;

        $forum = intval($sub);
        $replies = (array) $message;
        if (!is_array($message)) {
            $message_data = array($message => $message_data);
        }

        $nc_core = nc_Core::get_object();
        $forum_obj = nc_forum2_forum::get_object();
        $topic_obj = nc_forum2_topic::get_object();

        foreach ($replies as $reply) {

            $reply_topic = intval($message_data[$reply]['Topic_ID']);

            // get last reply id
            $last_reply_id_in_forum = $this->db->get_var("SELECT MAX(`Message_ID`) FROM `Message".$this->classID."`
      WHERE `Subdivision_ID` = '".$forum."'");
            $last_reply_id_in_topic = $this->db->get_var("SELECT MAX(`Message_ID`) FROM `Message".$this->classID."`
      WHERE `Subdivision_ID` = '".$forum."' AND Topic_ID='".$reply_topic."'");

            // update data
            $forum_obj->update_replies_count("-", $forum, $last_reply_id_in_forum);
            $topic_obj->update_replies_count("-", $forum, $reply_topic, $last_reply_id_in_topic);
        }

        return true;
    }

    /**
     * System events callback function
     * @access public
     *
     * @param int $catalogue Catalogue_ID
     * @param int $sub Subdivision_ID
     * @param int $cc Sub_Class_ID
     * @param int $class Class_ID
     * @param int $message Message_ID
     */
    public function event_update($catalogue, $sub, $cc, $class, $message) {
        // only forum2 replies class need it!
        if ($class != $this->classID) return false;

        $forum = intval($sub);
        $replies = (array) $message;

        $last_reply = array_pop($replies);

        // system superior object
        $nc_core = nc_Core::get_object();

        // get topic id
        $reply_topic = $nc_core->message->get_by_id($this->classID, $last_reply, "Topic_ID");

        // get objects
        $topic_obj = nc_forum2_topic::get_object();

        // LastUpdated SQL field refresh
        $topic_obj->update_last_updated($reply_topic);

        return true;
    }

}
