<?php

/**
 * Параметры формы в разделе
 */
class nc_requests_form_settings_subdivision extends nc_record {

    static $cache = array();

    protected $table_name = "Requests_Form_SubdivisionSetting";
    protected $primary_key = "Requests_Form_SubdivisionSetting_ID";
    protected $mapping = false;

    protected $properties = array(
        'Requests_Form_SubdivisionSetting_ID' => null,
        'Subdivision_ID' => null,
        'FormType' => 'default',
        
        'StandaloneForm_ComponentTemplate_ID' => 0,
        'StandaloneForm_Header' => '',
        'StandaloneForm_TextAfterHeader' => '',
        'StandaloneForm_SubmitButton_Text' => NETCAT_MODULE_REQUESTS_FORM_BUTTON_DEFAULT_TEXT,
        'StandaloneForm_SubmitButton_BackgroundColor' => null,
        'StandaloneForm_SubmitButton_ShowPrice' => false,

        'Subdivision_VisibleFields' => array(),
        'Subdivision_NotificationEmail' => '',

        'Subdivision_OpenPopupButton_AnalyticsCategories' => '',
        'Subdivision_OpenPopupButton_AnalyticsLabels' => '',
        'Subdivision_SubmitButton_AnalyticsCategories' => '',
        'Subdivision_SubmitButton_AnalyticsLabels' => '',
    );

    protected $serialized_properties = array('Subdivision_VisibleFields');
    protected $custom_settings_form;

    /**
     * Загрузка настроек формы для раздела (или значения по умолчанию, если настроек нет)
     * @param $subdivision_id
     * @param $form_type
     * @return nc_requests_form_settings_subdivision
     */
    static public function for_subdivision($subdivision_id, $form_type) {
        $subdivision_id = (int)$subdivision_id;

        if (!isset(self::$cache[$subdivision_id][$form_type])) {
            $result = new self(array('Subdivision_ID' => $subdivision_id, 'FormType' => $form_type));

            $form_type = nc_db()->escape($form_type);
            $loaded = $result->select_from_database("SELECT * FROM `%t%` WHERE `Subdivision_ID` = $subdivision_id AND `FormType` = '$form_type'");

            if (!$loaded) {
                // Записи в БД нет. Для настроек по умолчанию записываем в Fields все
                // отображаемые поля компонента заявок.
                $nc_core = nc_core::get_object();
                $site_id = $nc_core->subdivision->get_by_id($subdivision_id, 'Catalogue_ID');
                $requests = nc_requests::get_instance($site_id);
                $field_names = array_values($requests->get_request_component_visible_fields('name'));
                $result->set('Subdivision_VisibleFields', $field_names);
            }

            self::$cache[$subdivision_id][$form_type] = $result;
        }

        return self::$cache[$subdivision_id][$form_type];
    }

    /**
     * Делает копию настроек для всех типов форм
     * @param $source_subdivision_id
     * @param $target_subdivision_id
     * @return bool|int
     */
    static public function duplicate_settings_for_all_form_types($source_subdivision_id, $target_subdivision_id) {
        $settings = self::get_all_form_types_settings_in_subdivision($source_subdivision_id);
        $settings->each('set_id', null);
        $settings->each('set', 'Subdivision_ID', $target_subdivision_id);
        $settings->each('save');
    }

    /**
     * Возвращает коллекцию с настройками форм всех типов для указанного раздела,
     * проиндексированную по свойству FormType
     * @param $subdivision_id
     * @return nc_record_collection
     * @throws nc_record_exception
     */
    static public function get_all_form_types_settings_in_subdivision($subdivision_id) {
        $subdivision_id = (int)$subdivision_id;

        $collection = new nc_record_collection();
        $collection->set_items_class(__CLASS__)->set_index_property('FormType');
        $collection->select_from_database("SELECT * FROM `%t%` WHERE `Subdivision_ID` = $subdivision_id");

        return $collection;
    }

    /**
     * Возвращает массив c настройками форм всех типов для раздела в виде массива
     * @param $subdivision_id
     * @return array
     */
    static public function get_all_form_types_settings_in_subdivision_as_array($subdivision_id) {
        return self::get_all_form_types_settings_in_subdivision($subdivision_id)->each('to_array');
    }

}