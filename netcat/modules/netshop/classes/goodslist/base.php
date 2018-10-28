<?php

abstract class nc_netshop_goodslist_base {

    protected $initialized = false; // if session is initialized

    //--------------------------------------------------------------------------

    protected $cookie_id = null; // cookie id in DB

    //--------------------------------------------------------------------------

    protected $user_id = null; // user id in DB

    //--------------------------------------------------------------------------

    protected $db; // db object

    //--------------------------------------------------------------------------

    protected $tablename; // db table name

    //--------------------------------------------------------------------------

    protected $all_items_cache; // all items

    //--------------------------------------------------------------------------

    protected $cookie_lifetime = 157680000; // 5 years


    public function __construct(nc_netshop $netshop) {
        $this->db = nc_db();
    }

    //--------------------------------------------------------------------------

    /**
     * Initializes the session id
     *
     * @return bool
     */
    private function initialize() {
        if (!$this->initialized && $this->tablename) {
            $nc_core = nc_core::get_object();

            $user_id = (int)$GLOBALS['AUTH_USER_ID'];
            $cookie_name = 'nc_' . $this->tablename . '_sid';
            $cookie_id = '';
            $existing_cookie_expiration = 0;

            if (!$user_id) {
                if (isset($_COOKIE[$cookie_name])) {
                    $cookie_id = $_COOKIE[$cookie_name];
                    if (strpos($cookie_id, ':') !== false) {
                        list($cookie_id, $existing_cookie_expiration) = explode(':', $cookie_id, 2);
                    }
                } else {
                    $cookie_id = session_id();
                }

                $time = time();
                $set_new_cookie =
                    empty($_COOKIE[$cookie_name]) ||
                    ($existing_cookie_expiration - $time) < ($this->cookie_lifetime / 3);

                if ($set_new_cookie) {
                    $new_cookie_expiration = $time + $this->cookie_lifetime;
                    $nc_core->cookie->set($cookie_name, "$cookie_id:$new_cookie_expiration", $new_cookie_expiration);
                }
            } else {
                if (isset($_COOKIE[$cookie_name])) {
                    $cookie_escaped = $this->db->escape($_COOKIE[$cookie_name]);
                    $sql = "UPDATE `{$this->tablename}` SET `User_ID` = {$user_id}, `Cookie_ID` = '' WHERE `Cookie_ID` = '{$cookie_escaped}'";
                    $this->db->query($sql);
                    $nc_core->cookie->remove("nc_{$this->tablename}_sid");
                }
            }

            $this->user_id = $user_id;
            $this->cookie_id = $cookie_id;

            $this->initialized = true;
            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------------

    /**
     * Caches all items in the list
     *
     * @param bool $force_reload
     * @return bool
     */
    protected function cache_all($force_reload = false) {
        $this->initialize();
        if ($this->initialized) {
            if ($this->all_items_cache && !$force_reload) {
                return true;
            }

            $this->all_items_cache = $this->get_all('ASC', 512, false);
            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------------

    /**
     * Adds goods to list
     *
     * @param int $item_id
     * @param int $class_id
     * @return bool
     */
    public function add($item_id, $class_id) {
        $this->initialize();
        if ($this->initialized) {
            $db = $this->db;
            $cookie_id = $db->escape($this->cookie_id);
            $user_id = $this->user_id;
            $item_id = (int)$item_id;
            $class_id = (int)$class_id;

            if (!$class_id || !$item_id) {
                return false;
            }

            $sql = "SELECT `Parent_Message_ID` FROM `Message{$class_id}` WHERE `Message_ID` = {$item_id}";
            $parent_message_id = (int)$db->get_var($sql);

            $item_id = $parent_message_id ? $parent_message_id : $item_id;

            $this->remove($item_id, $class_id);

            $sql = "INSERT INTO `{$this->tablename}` (`Cookie_ID`, `User_ID`, `Item_ID`, `Class_ID`, `Added`) VALUES " .
                "('{$cookie_id}', {$user_id}, {$item_id}, {$class_id}, NOW())";
            $db->query($sql);

            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------------

    /**
     * Remove goods from list
     * by class id and item id
     *
     * @param int $item_id
     * @param int $class_id
     * @return bool
     */
    public function remove($item_id, $class_id) {
        $this->initialize();
        if ($this->initialized) {
            $cookie_id = $this->db->escape($this->cookie_id);
            $user_id = $this->user_id;
            $item_id = (int)$item_id;
            $class_id = (int)$class_id;

            $sql = "DELETE FROM `{$this->tablename}` WHERE `Item_ID` = {$item_id} AND `Class_ID` = {$class_id} AND ";
            $sql .= ($user_id ? "`User_ID` = {$user_id}" : "`Cookie_ID` = '{$cookie_id}'");
            $this->db->query($sql);

            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------------

    /**
     * Remove goods from list
     * by index
     *
     * @param int $index
     * @param string $sort
     * @return bool
     */
    public function remove_by_index($index, $sort = 'ASC') {
        $this->initialize();
        if ($this->initialized) {
            $sort = $sort == 'DESC' ? $sort : 'ASC';

            $cookie_id = $this->db->escape($this->cookie_id);
            $user_id = $this->user_id;
            $index = (int)$index;

            $sql = "DELETE FROM `{$this->tablename}` WHERE ";
            $sql .= ($user_id ? "`User_ID` = {$user_id}" : "`Cookie_ID` = '{$cookie_id}'");
            $sql .= " ORDER BY `ID` {$sort} LIMIT {$index}, 1";
            $this->db->query($sql);

            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------------

    /**
     * Clears list
     *
     * @return bool
     */
    public function clear() {
        $this->initialize();
        if ($this->initialized) {
            $cookie_id = $this->db->escape($this->cookie_id);
            $user_id = $this->user_id;

            $sql = "DELETE FROM `{$this->tablename}` WHERE ";
            $sql .= ($user_id ? "`User_ID` = {$user_id}" : "`Cookie_ID` = '{$cookie_id}'");
            $this->db->query($sql);

            return true;
        }

        return false;
    }

    //--------------------------------------------------------------------------

    /**
     * Returns goods list
     *
     * @param string $sort
     * @param int $limit
     * @return array
     */
    public function get_all($sort = 'ASC', $limit = 3, $skip_filter = false) {
        $this->initialize();
        if ($this->initialized) {
            $sort = $sort == 'DESC' ? $sort : 'ASC';

            if ($this->all_items_cache && !$skip_filter) {
                $result = $this->all_items_cache;
                if ($sort == 'DESC') { $result = array_reverse($result); }
                if ($limit) { array_slice($result, 0, $limit); }
                return $result;
            }

            $cookie_id = $this->db->escape($this->cookie_id);
            $user_id = $this->user_id;
            $limit = (int)$limit;

            $sql = "SELECT DISTINCT `Item_ID`, `Class_ID` FROM `{$this->tablename}`";
            if (!$skip_filter) {
                $sql .= " WHERE ";
                $sql .= ($user_id ? "`User_ID` = {$user_id}" : "`Cookie_ID` = '{$cookie_id}'");
            }
            $sql .= " ORDER BY `ID` {$sort} LIMIT {$limit}";

            return (array)$this->db->get_results($sql, ARRAY_A);
        }

        return array();
    }

    //--------------------------------------------------------------------------

    /**
     * Returns one item from list
     *
     * @param int $index
     * @param string $sort
     * @return null|array
     */
    public function get($index, $sort = 'ASC') {
        $this->initialize();
        if ($this->initialized) {
            $sort = $sort == 'DESC' ? $sort : 'ASC';

            if ($this->all_items_cache) {
                if ($sort == 'ASC') {
                    return $this->all_items_cache[$index];
                }
                else {
                    return $this->all_items_cache[count($this->all_items_cache) - 1 - $index];
                }
            }

            $cookie_id = $this->db->escape($this->cookie_id);
            $user_id = $this->user_id;
            $index = (int)$index;

            $sql = "SELECT `Item_ID`, `Class_ID` FROM `{$this->tablename}` WHERE ";
            $sql .= ($user_id ? "`User_ID` = {$user_id}" : "`Cookie_ID` = '{$cookie_id}'");
            $sql .= " ORDER BY `ID` {$sort} LIMIT 0, {$index}";

            return $this->db->get_row($sql, ARRAY_A);
        }

        return null;
    }


    /**
     * Toggles item in list
     *
     * @param int $item_id
     * @param int $class_id
     * @return bool
     */
    public function toggle($item_id, $class_id) {
        if ($this->check($item_id, $class_id)) {
            $this->remove($item_id, $class_id);
        } else {
            $this->add($item_id, $class_id);
        }

        return true;
    }

    /**
     * Checks if item exists
     * in list
     *
     * @param int $item_id
     * @param int $class_id
     * @return bool
     */
    public function check($item_id, $class_id) {
        $this->initialize();
        if ($this->initialized) {

            if ($this->all_items_cache) {
                foreach ($this->all_items_cache as $row) {
                    if ($row['Item_ID'] == $item_id && $row['Class_ID'] == $class_id) {
                        return true;
                    }
                }
                return false;
            }

            $db = $this->db;
            $cookie_id = $db->escape($this->cookie_id);
            $user_id = $this->user_id;
            $item_id = (int)$item_id;
            $class_id = (int)$class_id;

            $sql = "SELECT `Parent_Message_ID` FROM `Message{$class_id}` WHERE `Message_ID` = {$item_id}";
            $parent_message_id = (int)$db->get_var($sql);

            $item_id = $parent_message_id ? $parent_message_id : $item_id;

            $sql = "SELECT `ID` FROM `{$this->tablename}` WHERE `Item_ID` = {$item_id} AND `Class_ID` = {$class_id} AND ";
            $sql .= ($user_id ? "`User_ID` = {$user_id}" : "`Cookie_ID` = '{$cookie_id}'");

            return $db->get_var($sql) ? true : false;
        }

        return false;
    }
}