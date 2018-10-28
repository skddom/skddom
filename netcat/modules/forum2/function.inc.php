<?php

/* $Id: function.inc.php 5279 2011-09-02 12:16:04Z andrey $ */
if (!class_exists("nc_System")) die("Unable to load file.");
// get global value (for admin mode)
global $MODULE_FOLDER;

// include need classes
include_once ($MODULE_FOLDER."forum2/nc_forum2.class.php");
include_once ($MODULE_FOLDER."forum2/nc_forum2_parent.class.php");
include_once ($MODULE_FOLDER."forum2/nc_forum2_forum.class.php");
include_once ($MODULE_FOLDER."forum2/nc_forum2_topic.class.php");
include_once ($MODULE_FOLDER."forum2/nc_forum2_reply.class.php");

// instaninate objects and bind events
nc_forum2_parent::get_object();
nc_forum2_forum::get_object();
nc_forum2_topic::get_object();
nc_forum2_reply::get_object();

function nc_forum2_recalc() {
    global $db, $nc_core;

    $topic_class_id = intval($nc_core->modules->get_vars("forum2", "TOPIC_CLASS_ID"));
    $reply_class_id = intval($nc_core->modules->get_vars("forum2", "REPLY_CLASS_ID"));

    // правим таблицу Forum_Count
    $db->query("TRUNCATE `Forum_Count`");
    $subdivisions = $db->get_col("SELECT `Subdivision_ID` FROM `Forum_Subdivisions`");
    if (!$subdivisions) {
        echo "no subdivisions";
        return;
    }
    $db->get_results("SELECT `Subdivision_ID`, COUNT(`Message_ID`) as `Topics`, MAX(`Message_ID`) as `Last_Topic_ID` FROM `Message".$topic_class_id."` GROUP BY `Subdivision_ID`");
    $topics_info = @array_combine($db->get_col(NULL, 0), $db->get_results(NULL));
    $db->get_results("SELECT `Subdivision_ID`, COUNT(`Message_ID`) as `Replies`, MAX(`Message_ID`) as `Last_Reply_ID` FROM `Message".$reply_class_id."` GROUP BY `Subdivision_ID`");
    $replies_info = @array_combine($db->get_col(NULL, 0), $db->get_results(NULL));
    $query = "INSERT INTO `Forum_Count` (`Subdivision_ID`, `Topics`, `Replies`, `Last_Topic_ID`, `Last_Reply_ID`) VALUES ";
    foreach ($subdivisions as $subdivision) {
        $query .= "('".
                $subdivision."','".
                ($topics_info[$subdivision] ? $topics_info[$subdivision]->Topics : "0")."','".
                ($replies_info[$subdivision] ? $replies_info[$subdivision]->Replies : "0")."','".
                ($topics_info[$subdivision] ? $topics_info[$subdivision]->Last_Topic_ID : "0")."','".
                ($replies_info[$subdivision] ? $replies_info[$subdivision]->Last_Reply_ID : "0")."'), ";
    }
    $query = substr($query, 0, strlen($query) - 2); // delete ", " at the end
    $db->query($query);


    // правим таблицу Forum_Topics
    $db->query("TRUNCATE `Forum_Topics`");
    $topics = $db->get_results("SELECT `Message_ID`, `Subdivision_ID` FROM `Message".$topic_class_id."`");
    if (!$topics) {
        echo "no topics";
        exit;
    }
    if ($db->get_results("SELECT `Topic_ID`, COUNT(`Message_ID`) as `Replies`, MAX(`Message_ID`) as `Last_Reply_ID` FROM `Message".$reply_class_id."` GROUP BY `Topic_ID`")) {
        $replies_info = array_combine($db->get_col(NULL, 0), $db->get_results(NULL));
    }
    $query = "INSERT INTO `Forum_Topics` (`ID`, `Subdivision_ID`, `Topic_ID`, `Replies`, `Last_Reply_ID`) VALUES ";
    foreach ($topics as $topic) {
        $topic_id = $topic->Message_ID;
        $query .= "('".
                $topic_id."','".
                $topic->Subdivision_ID."','".
                $topic_id."','".
                ($replies_info[$topic_id] ? $replies_info[$topic_id]->Replies : "0")."','".
                ($replies_info[$topic_id] ? $replies_info[$topic_id]->Last_Reply_ID : "0")."'), ";
    }
    $query = substr($query, 0, strlen($query) - 2); // delete ", " at the end
    $db->query($query);

    echo "recounting is succsessfully ended";
    return;
}
?>