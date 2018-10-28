<?php

/**
 * Индекс товаров
 */
class nc_netshop_itemindex {

    const NON_TERM_REGEXP_UTF8 = '/[^\p{L}\d]+/u';
    const NON_TERM_REGEXP_8BIT = '/[^0-9A-ZА-ЯЁ]+';

    protected $site_id;
    protected $indexed_fields;

    protected $non_term_regexp = self::NON_TERM_REGEXP_UTF8;

    protected $term_symbol_substitutes = array(
        "Ё" => "Е",
    );

    /**
     * @param nc_netshop $netshop
     */
    public function __construct(nc_netshop $netshop) {
        $this->site_id = (int)$netshop->get_catalogue_id();
        $this->indexed_fields = preg_split('/\W+/', $netshop->get_setting('ItemIndexFields'));

        if (!nc_core::get_object()->NC_UNICODE) {
            $this->term_symbol_substitutes = nc_core::get_object()->utf8->array_utf2win($this->term_symbol_substitutes);
            $this->non_term_regexp = self::NON_TERM_REGEXP_8BIT;
        }
    }

    /**
     * Добавление товара в индекс
     * @param nc_netshop_item $item
     */
    public function add_item(nc_netshop_item $item) {
        if (!$item['Checked']) {
            return;
        }

        $terms = array();
        foreach ($this->indexed_fields as $field) {
            $terms = array_merge($terms, $this->extract_terms($item[$field]));
        }

        $query = "INSERT INTO `Netshop_ItemIndex`
                     SET `Catalogue_ID` = " . $this->site_id . ",
                         `Class_ID` = " . (int)$item['Class_ID'] . ",
                         `Message_ID` = " . (int)$item['Message_ID'] . ",
                         `Term` = ";
        $db = nc_db();

        foreach ($terms as $term) {
            $db->query($query . "'" . $db->escape($term) . "'");
        }
    }

    /**
     * Обновление товара в индексе
     */
    public function update_item(nc_netshop_item $item) {
        $this->remove_item($item['Class_ID'], $item['Message_ID']);
        $this->add_item($item);
    }

    /**
     * Удаление товара из индекса
     * @param int $component_id
     * @param int $item_id
     */
    public function remove_item($component_id, $item_id) {
        nc_db()->query(
            "DELETE FROM `Netshop_ItemIndex`
              WHERE `Class_ID` = " . (int)$component_id . "
                AND `Message_ID` = " . (int)$item_id
        );
    }

    /**
     * Переиндексирование всех товаров на сайте
     *
     */
    public function reindex_site() {
        $this->remove_site_index();
        $db = nc_db();
        set_time_limit(0);

        $db->query("ALTER TABLE `Netshop_ItemIndex` DISABLE KEYS");

        foreach (nc_netshop::get_instance($this->site_id)->get_goods_components_ids() as $component_id) {
            $item_ids = (array)$db->get_col(
                "SELECT m.`Message_ID`
                   FROM `Message$component_id` AS m
                        JOIN `Subdivision` AS sub USING (`Subdivision_ID`)
                  WHERE sub.`Catalogue_ID` = $this->site_id"
            );

            foreach ($item_ids as $item_id) {
                $this->add_item(nc_netshop_item::by_id($component_id, $item_id));
            }
        }

        $db->query("ALTER TABLE `Netshop_ItemIndex` ENABLE KEYS");
    }

    /**
     * Удаление всех товаров на сайте из индекса
     */
    public function remove_site_index() {
        nc_db()->query("DELETE FROM `Netshop_ItemIndex` WHERE `Catalogue_ID` = $this->site_id");
    }

    /**
     * Поиск товаров
     * @param string $terms    Строка для поиска
     * @param int|null $limit  Максимальное количество результатов
     * @return nc_netshop_item_collection
     */
    public function find($terms, $limit = null) {
        $result = new nc_netshop_item_collection();

        $terms = $this->extract_terms($terms);
        $num_terms = count($terms);

        if (!$terms) {
            return $result;
        }

        $db = nc_db();

        // Были опробованы способы: JOIN, JOIN-subquery;
        // на большом «индексе» с большим количеством слов в запросе
        // объединение результатов в скрипте оказалось значительно быстрее.

        $matching_items = array();
        for ($i = 0; $i < $num_terms; $i++) {
            $term_matches = $db->get_col(
                "SELECT CONCAT(`Class_ID`, ':', `Message_ID`)
                   FROM `Netshop_ItemIndex`
                  WHERE `Catalogue_ID` = $this->site_id
                    AND `Term` LIKE '" . $db->escape($terms[$i]) . "%'"
            );

            if ($term_matches) {
                if ($i) {
                    $matching_items = array_intersect($matching_items, $term_matches);
                }
                else {
                    $matching_items = $term_matches;
                }
            }
            else {
                $matching_items = array();
            }

            if (!$matching_items) {
                break;
            }
        }

        $matching_items = array_unique($matching_items);
        if ($limit) {
            $matching_items = array_slice($matching_items, 0, $limit);
        }

        foreach ($matching_items as $row) {
            list($component_id, $item_id) = explode(":", $row);
            $result->add(nc_netshop_item::by_id($component_id, $item_id));
        }
        return $result;
    }

    /**
     * Извлечение терминов из строки
     * @param $string
     * @return array
     */
    protected function extract_terms($string) {
        $string = nc_strtoupper(strip_tags(trim($string)));
        if (!strlen($string)) {
            return array();
        }

        $string = strtr($string, $this->term_symbol_substitutes);

        $string = preg_replace('/(\d+)/', ' $1 ', $string);
        $terms = preg_split($this->non_term_regexp, $string);
        $terms = array_filter($terms, 'strlen');
        $terms = array_unique($terms);

        return array_values($terms);
    }

}