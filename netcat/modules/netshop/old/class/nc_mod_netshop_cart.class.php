<?php



class nc_mod_netshop_cart {

    //--------------------------------------------------------------------------

    protected $content;

    /** @var nc_mod_netshop  */
    protected $netshop;
    protected $db;

    //--------------------------------------------------------------------------

    public static function get_instance()
    {
        static $instance;

        return is_null($instance) ? ($instance = new self) : $instance;
    }

    //--------------------------------------------------------------------------

    private function __construct()
    {
        $this->netshop = nc_mod_netshop::get_instance();
        $this->db      = nc_core('db');

        if ($shop_id = $this->netshop->shop_id) {
            $this->content =& $_SESSION["cart_{$shop_id}"]['goods'];
        }
    }

    //--------------------------------------------------------------------------

    public function __destruct()
    {
        if ($shop_id = $this->netshop->shop_id) {
            $_SESSION["cart_{$shop_id}"]['goods'] = $this->content;
        }
    }

    //--------------------------------------------------------------------------

    /**
     * Добавляет товар в корзину.
     * Удаляет, если количество = 0
     * @param array  $data          [$type_id][$id] = $new_nc_Component
     * @param string $mode          "add": qty=qty+new_qty; otherwise: qty=new_qty
     * @param array  $custom_params
     */
    public function add($class_id, $row_id = null, $qty = 1, $custom_params = array())
    {
        if ( ! $this->netshop->shop_id) {
            return false;
        }

        if (is_array($class_id)) {
            $data   = $class_id;
            $append = $row_id;
        } else {
            $data = array(
                $class_id => array(
                    $row_id => $qty
                ),
            );
            $append = false;
        }

        foreach ((array) $data as $class_id => $items) {
            $class_id    = (int) $class_id;
            $component   = new nc_Component($class_id);
            $fields      = $component->get_fields();
            $typeof_unit = 'intval';

            foreach ($fields as $k => $v) {
                if ($v['name'] == 'StockUnits' && $v['type'] == 7) {
                    $typeof_unit = 'doubleval';
                }
            }

            foreach ((array) $items as $id => $qty) {
                $id       = (int) $id;
                $qty      = str_replace(",", ".", $qty);
                $qty      = $typeof_unit($qty);

                if ($qty <= 0) {
                    $this->remove($class_id, $id);
                } else {
                    $row = array(
                        'Qty'         => $qty,
                        'cart_params' => (array) $custom_params,
                    );

                    if ($append && isset($this->content[$class_id][$id])) {
                        $row['Qty']        += $this->content[$class_id][$id]['Qty'];
                        $row['cart_params'] = (array) $this->content[$class_id][$id]['cart_params'] + (array) $cart_params;
                    }

                    $this->content[$class_id][$id] = $row;
                }
            }
        }

        return true;
    }

    //--------------------------------------------------------------------------

    public function remove($class_id, $id)
    {
        if (isset($this->content[$class_id][$id])) {
            unset($this->content[$class_id][$id]);

            if (empty($this->content[$class_id])) {
                unset($this->content[$class_id]);
            }

            return true;
        }
    }

    //--------------------------------------------------------------------------

    public function content($reload = false)
    {
        static $cart_content = array();

        if ( ! $reload && $cart_content) {
            return $cart_content;
        }

        if ( ! $this->count()) {
            return array();
        }

        $cart_content    = array();
        $price_column    = $this->netshop->PriceColumn;
        $currency_column = $this->netshop->CurrencyColumn;
        $units           = $this->netshop->Units;

        foreach ($this->content as $message_id => $items) {
            $row_ids = array_keys($items);
            $result = $this->db->get_results("SELECT m.*,
                    IFNULL(m.{$price_column}, parent.{$price_column}) as Price4User,
                    IF(m.{$price_column} IS NULL, parent.{$currency_column}, m.{$currency_column}) as Currency4User,
                    IFNULL(m.PriceMinimum, parent.PriceMinimum) as PriceMinimum,
                    IF(m.Keyword IS NULL OR m.Keyword = '', CONCAT(u.Hidden_URL, s.EnglishName, '_', m.Message_ID, '.html'),
                    CONCAT(u.Hidden_URL, m.Keyword, '.html')) as URL
                FROM (Message{$message_id} as m, Subdivision as u, Sub_Class as s)
                LEFT JOIN Message{$message_id} as parent
                    ON (m.Parent_Message_ID != 0 AND m.Parent_Message_ID = parent.Message_ID)
                WHERE m.Message_ID IN (" . implode(',', $row_ids) . ")
                    AND s.Sub_Class_ID = m.Sub_Class_ID
                    AND u.Subdivision_ID = m.Subdivision_ID
            ");

            foreach ($result as $row) {
                // Заказ, загруженный при помощи LoadOrder:
                if ($items[$row->Message_ID]['ItemPrice']) {
                    $price          = $items[$row->Message_ID]['ItemPrice'];     // со скидками
                    $original_price = $items[$row->Message_ID]['OriginalPrice']; // без скидок
                }
                // еще не записанный заказ:
                else {
                    $price          = $this->netshop->FormatCurrency($row->Price4User, $row->Currency4User);
                    $original_price = $price;
                }

                $qty = $items[$row->Message_ID]['Qty'];

                $cart_content[] = array_merge((array) $row, array(
                        'Class_ID' => $message_id,
                        'RowID'    => "[{$type_id}][{$row->Message_ID}]",
                        'Qty'      => $qty,
                        'Units'    => $units[$row->Units],

                        // PriceMinumum is stored to the SHOP CURRENCY:
                        'PriceMinimum' => $this->netshop->FormatCurrency($row->PriceMinimum, $row->CurrencyMinimum),

                        // Formatted prices:
                        'ItemPriceF'  => $this->netshop->FormatCurrency($price),
                        'TotalPriceF' => $this->netshop->FormatCurrency($price * $qty),

                        // Raw prices in default currency
                        'ItemPrice'  => $price,
                        'TotalPrice' => $price * $qty,

                        // Исходные цены (order from db)
                        'OriginalPrice'  => $original_price,
                        'OriginalPriceF' => $this->netshop->FormatCurrency($original_price),
                        'Discounts'      => $items[$row->Message_ID]['Discounts'],
                        'cart_params'    => $items[$row->Message_ID]['cart_params'])
                );

                //TODO: Перенести этот код в новый метод отправки писем (подмодуль оформления заказа)
                // коллекционируем адреса, потом сделаем рассылку
                // $manager_email = $this->GetDepartmentSetting("ManagerEmail", $type_id, $row["Message_ID"], $row["Subdivision_ID"]);
                // $this->SendMails[$manager_email][] = sizeof($ret) - 1;
            }
        }

        return $cart_content;
    }

    //--------------------------------------------------------------------------

    public function clear()
    {
        $this->content = array();
    }

    //--------------------------------------------------------------------------

    /**
     * Количество товаров в корзине
     *
     * @param boolean $count_items При `true` учитывается количество одной позиции (Qty)
     *
     * @return int
     */
    public function count($count_items = false)
    {
        $count = 0;

        foreach ((array) $this->content as $rows) {
            foreach ($rows as $item) {
                $count += $count_items ? $item['Qty'] : 1;
            }
        }

        return (int) $count;
    }

    //--------------------------------------------------------------------------

    public function total()
    {
        return nc_mod_netshop::get_instance()->CartSum();

        // $total = $this->total_field('ItemPrice');
        // $total -= $this->netshop->discount->total_cart_discount();

        // return $total;
    }

    //--------------------------------------------------------------------------

    public function total_field($field)
    {
        return nc_mod_netshop::get_instance()->CartFieldSum($field);

        // $total   = 0;
        // $content = $this->content();

        // foreach ($content as $row) {
        //     $total += $row[$field] * $row['Qty'];
        // }

        // return $total;
    }

    //--------------------------------------------------------------------------
    
    public function discount_sum() {
        return $this->netshop->CartDiscountSum();
    }

    //--------------------------------------------------------------------------

    //--------------------------------------------------------------------------

    private function __clone() {}
    private function __wakeup() {}

    //--------------------------------------------------------------------------
}