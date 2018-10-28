<?php

abstract class nc_System {

    protected $debug_mode = 0;
    protected $debug_access = false;
    protected $debug_level_arr = array("error" => "#FFE5E5", "info" => "#F0F7FF", "ok" => "#EDFFEB");
    protected $system_start_mctime;
    protected $debug_arr = array();

    protected function __construct() {
//        $this->system_start_mctime = microtime(1);
        //$this->debug_access = isset($perm) ? $perm->isSupervisor() : false;
    }

    /**
     * Collect debug info function
     * for critical errors!
     *
     * @param Exception object
     */
    public function errorMessage(Exception $e) {
        // if disabled - return
        if (!$this->debug_mode) {
            return;
        }
        // append debug message
        $this->debug_arr[] = array(
            "message" => $e->getMessage(),
            "file" => $e->getFile(),
            "line" => $e->getLine(),
            "level" => "error"
        );
    }

    /**
     * Collect debug info function
     *
     * @param Exception object
     */
    public function debugMessage($message, $file = "", $line = 0, $level = "info") {

        // if disabled or no access - return
        if (!$this->debug_mode) {
            return;
        }
        // append debug message
        $this->debug_arr[] = array(
            "message" => $message,
            "file" => $file,
            "line" => $line,
            "level" => array_key_exists($level, $this->debug_level_arr) ? $level : "info"
        );
    }

    protected function debugInfo() {
        // compile debug info
        $result = '';
        if (!empty($this->debug_arr)) {
            $result = "<div style='font-family:Arial; font-size:14px; padding:10px'>";
            $result .= "<h2 style='padding-bottom:5px'>System debug info, <span style='color:#A00'>" . get_class($this) . "</span> class</h2>";
            $result .= "<table cellpadding=5 cellspacing=1 style='font-size:12px; border:none; background:#CCC; width:100%'>";
            $result .= "<col style='width:1%'/><col style='width:45%'/><col style='width:44%'/><col style='width:10%'/>";
            $result .= "<tr><td style='background:#EEE'><b>!</b></td><td style='background:#EEE'><b>Message</b></td><td style='background:#EEE'><b>File</b></td><td style='background:#EEE'><b>Line</b></td></tr>";
            foreach ($this->debug_arr as $debug) {
                $background = $this->debug_level_arr[$debug['level']] ? $this->debug_level_arr[$debug['level']] : "#FFFFFF";
                $result .= "<tr><td style='background:" . $background . "'></td><td style='background:#FFF'>" . $debug['message'] . "</td><td style='background:#FFF'>" . $debug['file'] . "</td><td style='background:#FFF'>" . $debug['line'] . "</td></tr>";
            }
            $result .= "</table>";
            $result .= "</div>";
        }
        // return result
        return $result;
    }

    protected function check_system_install() {
        global $DOCUMENT_ROOT, $SUB_FOLDER;
        global $MYSQL_PASSWORD, $MYSQL_DB_NAME;

        if (
            !$MYSQL_PASSWORD &&
            !$MYSQL_DB_NAME &&
            file_exists($DOCUMENT_ROOT . $SUB_FOLDER . "/install/index.php")
        ) {
            if (file_exists($DOCUMENT_ROOT . $SUB_FOLDER . "/install/index.php")) {
                // make install
                header("Location: " . $SUB_FOLDER . "/install/");
            }
            // dummy
            print "<b style='color:#A00'>System not installed!</b>";
            // halt
            exit;
        }

        return true;
    }

//    /**
//     * Destructor function
//     */
//    public function __destruct() {
//        // calculate system uptime
//        global $inside_admin;
//        if ($inside_admin) {
//            return;
//        }
//
//
//        if ((get_class($this)) == "nc_Core") {
//            $this->debugMessage("System uptime <b style='color:#A00'>" . (microtime(1) - $this->system_start_mctime) . "</b>", __FILE__, __LINE__, "ok");
//            //echo $this->debugInfo();
//        }
//
//        if ((get_class($this)) == "nc_Db") {
//            //echo $this->debugInfo();
//        }
//        // debug info enabled and Supervisor access
//        //if ( (get_class($this)=="nc_Core" ? !$this->inside_admin : !$this->core->inside_admin) && ($this->debug_mode==2 || ( $this->debug_mode==1 && isset($perm) && $perm->isSupervisor() ) ) ) {
//        //  echo $this->debugInfo();
//        //}
//    }

}
