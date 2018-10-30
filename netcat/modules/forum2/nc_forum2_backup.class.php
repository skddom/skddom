<?php

/**
 *
 */
class nc_forum2_backup extends nc_backup_extension {

    /**
     * @param string $type
     * @param int $id
     */
    public function export($type, $id) {
        if ($type != 'site') { return; }

        $sub_ids = $this->dumper->get_dict('Subdivision_ID');

        $forum_groups = nc_db_table::make('Forum_Groups')->where_in('Subdivision_ID', $sub_ids)->get_result();
        $this->dumper->export_data('Forum_Groups', 'ID', $forum_groups);

        $forum_subdivisions = nc_db_table::make('Forum_Subdivisions')->where_in('Subdivision_ID', $sub_ids)->get_result();
        $this->dumper->export_data('Forum_Subdivisions', 'ID', $forum_subdivisions);

        $forum_topics = nc_db_table::make('Forum_Topics')->where_in('Subdivision_ID', $sub_ids)->get_result();
        $this->dumper->export_data('Forum_Topics', 'ID', $forum_topics);

        $forum_count = nc_db_table::make('Forum_Count')->where_in('Subdivision_ID', $sub_ids)->get_result();
        $this->dumper->export_data('Forum_Count', 'ID', $forum_count);
    }

    /**
     * @param string $type
     * @param int $id
     */
    public function import($type, $id) {
        if ($type != 'site') { return; }

        // При правильном экспорте и импорте таблицы TOPIC_CLASS_ID и REPLY_CLASS_ID
        // должны быть помечены как IsAuxiliary и не будут создаваться как новые;
        // соответственно и настройки модуля менять не надо

        $this->dumper->import_data('Forum_Groups');
        $this->dumper->import_data('Forum_Subdivisions', null, array('Group_ID' => 'Forum_Groups.ID'));
        $this->dumper->import_data('Forum_Topics', null, array('Topic_ID' => 'map_topics_topic_id'));
        $this->dumper->import_data('Forum_Count', null, array(
                                        'Last_Topic_ID' => 'map_topics_topic_id',
                                        'Last_Reply_ID' => 'map_topics_reply_id'));
    }


    protected function get_topic_class_id() {
        return intval(nc_core::get_object()->modules->get_vars("forum2", "TOPIC_CLASS_ID"));
    }

    protected function get_reply_class_id() {
        return intval(nc_core::get_object()->modules->get_vars("forum2", "REPLY_CLASS_ID"));
    }

    public function map_topics_topic_id($row, $field) {
        $value = $row[$field];
        $value = $this->dumper->get_dict("Message{$this->get_topic_class_id()}.Message_ID", $value);
        return $value;
    }

    public function map_topics_reply_id($row, $field) {
        $value = $row[$field];
        $value = $this->dumper->get_dict("Message{$this->get_reply_class_id()}.Message_ID", $value);
        return $value;
    }

}