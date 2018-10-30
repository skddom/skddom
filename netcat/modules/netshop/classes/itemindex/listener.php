<?php

/**
 * Слушатель изменений товаров. Обновляет «индекс» товаров.
 */
class nc_netshop_itemindex_listener {

    /**
     * Регистрация слушателя
     */
    public static function register() {
        $listener = new self;
        $event_manager = $nc_core = nc_core::get_object()->event;
        $event_manager->bind($listener, array(nc_Event::AFTER_SITE_DELETED   => 'on_site_delete'));
        $event_manager->bind($listener, array(nc_Event::AFTER_OBJECT_CREATED => 'on_object_update'));
        $event_manager->bind($listener, array(nc_Event::AFTER_OBJECT_UPDATED => 'on_object_update'));
        $event_manager->bind($listener, array(nc_Event::AFTER_OBJECT_DELETED => 'on_object_delete'));
    }

    /**
     * @param $site_id
     */
    public function on_site_delete($site_id) {
        nc_netshop::get_instance($site_id)->itemindex->remove_site_index();
    }

    /**
     * @param int $site_id
     * @param int $subdivision_id
     * @param int $infoblock_id
     * @param int $component_id
     * @param int|int[] $item_id
     */
    public function on_object_update($site_id, $subdivision_id, $infoblock_id, $component_id, $item_id) {
        $netshop = nc_netshop::get_instance($site_id);
        $element_item_ids = (array)$item_id;

        if (in_array($component_id, $netshop->get_goods_components_ids()) && $netshop->get_setting('ItemIndexFields')) {
            foreach ($element_item_ids as $element_item_id) {
                $item = nc_netshop_item::by_id($component_id, $element_item_id);
                $netshop->itemindex->update_item($item);
            }
        }
    }

    /**
     * @param int $site_id
     * @param int $subdivision_id
     * @param int $infoblock_id
     * @param int $component_id
     * @param int|int[] $item_id
     */
    public function on_object_delete($site_id, $subdivision_id, $infoblock_id, $component_id, $item_id) {
        $netshop = nc_netshop::get_instance($site_id);
        $element_item_ids = (array)$item_id;

        if (in_array($component_id, $netshop->get_goods_components_ids())) {
            foreach ($element_item_ids as $element_item_id) {
                $netshop->itemindex->remove_item($component_id, $element_item_id);
            }
        }
    }

}