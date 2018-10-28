<?php

/**
 * Собирает общий файл стилей компонентов для сайта.
 */
class nc_tpl_stylesheet_assembler {

    /**
     * Shortcut for (new nc_tpl_stylesheet_assembler)->assemble();
     *
     * @param int $site_id
     * @param int[] $component_template_ids
     * @return null|string
     */
    public static function get_site_component_styles_path($site_id, array $component_template_ids) {
        $compiler = new self;
        return $compiler->assemble($site_id, $component_template_ids);
    }

    /**
     *
     */
    public function __construct() {
    }

    /**
     * Собирает CSS-файл со стилями для компонентов для указанного сайта.
     * Пересборка осуществляется только если файлы стилей компоненты изменились
     * с момента предыдущей пересборки или собранный в прошлый раз файл стилей
     * содержит стили не для всех компонентов.
     *
     * Возвращает путь к файлу стилей компонентов от корня сайта,
     * или null, если файл стилей пуст.
     * Путь в query-части содержит timestamp последней пересборки файла
     * для снижения вероятности проблем из-за кэширования.
     *
     * @param int $site_id
     * @param int[] $component_template_ids
     * @return null|string
     */
    public function assemble($site_id, array $component_template_ids) {
        if (!$component_template_ids) {
            return null;
        }

        $site_id = (int)$site_id;
        $component_template_ids = array_map('intval', $component_template_ids);

        $nc_core = nc_core::get_object();
        $db = $nc_core->db;

        $css_folder = $nc_core->SUB_FOLDER . $nc_core->HTTP_TEMPLATE_PATH . 'css/' . $site_id;
        $css_relative_path = $css_folder . '/components.css';
        $css_absolute_path = $nc_core->DOCUMENT_ROOT . $css_relative_path;

        $tmp_path = $nc_core->DOCUMENT_ROOT . $css_folder . '/components';
        if (!file_exists($tmp_path)) {
            mkdir($tmp_path, $nc_core->DIRCHMOD, true);
        }

        $template_styles_file_last_update = $db->get_col(
            "SELECT `Class_Template_ID`, `LastUpdate`
               FROM `Class_StyleCache`
              WHERE `Catalogue_ID` = $site_id
                AND `Class_Template_ID` IN (" . join(',', $component_template_ids) . ")",
            1, 0
        );

        $has_changes = false;
        $has_styles = false;

        foreach ($component_template_ids as $template_id) {
            $template_source_css_file =
                rtrim($nc_core->CLASS_TEMPLATE_FOLDER, '/') .
                $nc_core->component->get_by_id($template_id, 'File_Path') .
                'SiteStyles.css';

            $template_processed_css_file = "$tmp_path/$template_id.css";

            if (!file_exists($template_source_css_file)) { // когда стили не заданы, файл не существует
                if (isset($template_styles_file_last_update[$template_id])) {
                    if (file_exists($template_processed_css_file)) {
                        unlink($template_processed_css_file);
                    }
                    $db->query("DELETE FROM `Class_StyleCache` WHERE `Catalogue_ID` = $site_id AND `Class_Template_ID` = $template_id");
                    $has_changes = true;
                }
                continue;
            }

            $has_styles = true;
            $template_timestamp = filemtime($template_source_css_file);
            if (
                !isset($template_styles_file_last_update[$template_id]) ||
                $template_styles_file_last_update[$template_id] < $template_timestamp ||
                !file_exists($template_processed_css_file)
            ) {
                $this->assemble_component_css($template_id, $template_source_css_file, $template_processed_css_file);
                $has_changes = true;

                $db->query("INSERT INTO `Class_StyleCache`
                            SET `Catalogue_ID` = $site_id,
                                `Class_Template_ID` = $template_id,
                                `LastUpdate` = $template_timestamp
                            ON DUPLICATE KEY UPDATE `LastUpdate` = $template_timestamp"
                );
            }
        }

        if ($has_changes || !file_exists($css_absolute_path)) {
            $this->assemble_site_css($tmp_path, $css_absolute_path);
        }

        if ($has_styles) {
            return $css_relative_path . '?' . filemtime($css_absolute_path);
        }

        return null;
    }

    /**
     * Обрабатывает файл $source_file (добавляет класс блока и делает пути абсолютными)
     * и записывает результат в $destination_file
     * @param $template_id
     * @param $source_file
     * @param $destination_file
     */
    protected function assemble_component_css($template_id, $source_file, $destination_file) {
        $nc_core = nc_core::get_object();
        $stylesheet = nc_tpl_stylesheet::from_file($source_file);

        $block_class = str_replace(' ', '.', $nc_core->component->get_css_class_name($template_id));
        $url_prefix =
            $nc_core->SUB_FOLDER .
            $nc_core->HTTP_TEMPLATE_PATH .
            'class' .
            $nc_core->component->get_by_id($template_id, 'File_Path');

        $result = $stylesheet->transform($block_class, $url_prefix);
        file_put_contents($destination_file, $result);
    }

    /**
     * Склеивает все файлы .css из $source_path в файл $target_file
     * @param $source_path
     * @param $target_file
     */
    protected function assemble_site_css($source_path, $target_file) {
        $fh = fopen($target_file, 'w');
        foreach (glob("$source_path/*.css") as $file) {
            fputs($fh, file_get_contents($file));
        }
        fclose($fh);
    }
}