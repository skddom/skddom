<?php

/* $Id: nc_forum2.class.php 3557 2009-10-30 13:22:13Z vadim $ */

/**
 * class nc_forum2
 * @package nc_forum2
 * @category nc_forum2
 * @abstract
 */
abstract class nc_forum2 {

    /**
     * @var nc_Db object (ezSQL_mysql)
     */
    protected $db;
    /**
     * @var array forum2 module parameters array
     */
    protected $MODULE_VARS;
    /**
     * @var int objects relation Class_ID
     */
    protected $classID;
    /**
     * @var int RSS Class_ID
     */
    protected $rss_classID;
    /**
     * @var array Sub_Class_ID storage
     * used into the get_subclasses_ids() method
     */
    static $get_subclass_id_storage = array();

    /**
     * Constructor method
     * @access protected
     */
    protected function __construct() {
        // system superior object
        $nc_core = nc_Core::get_object();

        $this->db = &$nc_core->db;
        $this->MODULE_VARS = $nc_core->modules->get_vars("forum2");
    }

    /**
     * Get or instance object
     * @static
     * @access public
     */
    public static function get_object() {}

    /**
     * Get related object Class_ID
     * @access public
     *
     * @return int Class_ID
     */
    public function get_class_id() {
        // return Class_ID
        return $this->classID;
    }

    /**
     * Get RSS Class_ID
     * @access public
     *
     * @return int RSS Class_ID
     */
    public function get_rss_class_id() {
        // return RSS Class_ID
        return $this->rss_classID;
    }

    /**
     * Get all Sub_Class_ID array for the concrete sub
     * @access public
     *
     * @param int Subdivision_ID
     *
     * @return array Sub_Class_IDs
     */
    public function get_subclasses_ids($sub) {
        //  validate
        $sub = intval($sub);

        if (!$sub) return false;

        if (!isset(self::$get_subclass_id_storage[$sub])) {
            self::$get_subclass_id_storage[$sub] = $this->db->get_results("SELECT `Sub_Class_ID`, `Class_ID`, `Class_Template_ID`
        FROM `Sub_Class`
        WHERE `Subdivision_ID` = '".$sub."'", ARRAY_A);
        }

        // return Sub_Class_ID array for the $sub parameter
        return self::$get_subclass_id_storage[$sub];
    }

    /**
     * Get related object Sub_Class_ID into the concrete sub
     * @access public
     *
     * @param int Subdivision_ID
     *
     * @return int Sub_Class_ID
     */
    public function get_subclass_id($sub, $class_template = 0) {
        //  validate
        $sub = intval($sub);
        $class_template = intval($class_template);
        $result = 0;

        if (!$sub) return false;

        // get subclasses for the $sub
        $subclass_array = $this->get_subclasses_ids($sub);

        if (!empty($subclass_array)) {
            foreach ($subclass_array as $value) {
                if (
                        $value['Class_ID'] == $this->get_class_id() &&
                        $value['Class_Template_ID'] == $class_template
                ) {
                    $result = $value['Sub_Class_ID'];
                    break;
                }
            }
        }

        // return Sub_Class_ID
        return $result;
    }

    /**
     * Get related object RSS Sub_Class_ID into the concrete sub
     * @access public
     *
     * @param int Subdivision_ID
     *
     * @return int Sub_Class_ID
     */
    public function get_rss_subclass_id($sub) {
        // return RSS Sub_Class_ID
        return $this->get_subclass_id($sub, $this->get_rss_class_id());
    }

    /**
     * Check RSS $isNaked param
     * @access public
     *
     * @return bool RSS $isNaked param
     */
    /* public function check_rss_is_naked () {
      // system superior object
      $nc_core = nc_Core::get_object();

      // return RSS possibility
      return $nc_core->input->fetch_get("isNaked");
      } */
}
?>