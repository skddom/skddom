<?php


class NetShopDeprecated {

    /**
     * ID типа магазина
     */
    var $shop_table;

    /**
     * ID магазина
     */
    var $shop_id;

    /**
     * корзинка (array) -- для добавления/удаления (сгруппировано по type, id)
     * хранится в session
     */
    var $Cart;

    /**
     * Свойства заказа
     */
    var $Order;

    /**
     * корзина: подробная информация
     */
    var $CartContents;
    var $DefaultCurrencyID;
    // -- id
    var $Currencies;
    // -- name [Classificator_ShopCurrencies]
    var $CurrencyDetails;
    // currency details
    var $Rates;
    // -- array[id] = rate
    var $DepartmentSettings;
    // cache
    var $SendMails;
    // [адрес@почты] => [индексы в CartContents]
    var $OrderID;
    // for loaded from db orders
    var $TotalDiscountSum;
    // сумма товарных скидок и скидок на корзину
    var $CartDiscounts;
    // информация примененных скидках на корзину
    var $CartDiscountSum;
    // сумма скидок на корзину
    var $CartSumBeforeCartDiscounts;
    // whoa... сумма по корзине до применения "корзинных" скидок
    var $GoodsTypeIDs; // array

    var $PriceColumn;
    var $CurrencyColumn;
    var $CartSumBeforeDiscounts;
    var $CurrencyConversionPercent;

    var $order_table;
    var $cart_table;
    var $discount_table;
    var $currency_rates_table;
    var $price_rules_table;
    var $official_rates_table;
    var $prev_orders_sum_status_id;
    var $department_table;
    var $payment_methods_table;
    var $delivery_methods_table;
    var $email_template_table;
    var $order_mail_subject_lenght;

    var $AssistShopId;
    var $MailFrom;
    var $MailHash;
    var $MailSecretKey;
    var $MailShopID;
    var $PayCashSettings;
    var $PaymasterID;
    var $PaymasterWord;
    var $PaypalBizMail;
    var $QiwiFrom;
    var $QiwiPwd;
    var $RobokassaLogin;
    var $RobokassaPass1;
    var $RobokassaPass2;
    var $ShopName;
    var $WebmoneyPurse;
    var $WebmoneySecretKey;

    /**
     * constructor
     * @var array массив с товарами, которые нужно положить в корзину вида [type_id][id]=qty
     */
    function NetShopDeprecated($put_to_cart = false) {
        // Произвести авторизацию, если модуль интернет-магазина загрузился
        // раньше, чем произошла авторизация.
        // (От группы пользователей могут зависеть скидки.)

        global $AUTH_USER_ID, $AUTH_USER_GROUP;

        // map module settings to the object
        foreach ((array) nc_modules()->get_vars('netshop') as $k => $v) {
            $this->{strtolower($k)} = $v;
        }

        // определить ID магазина в этом сайте
        $this->shop_id = GetSubdivisionByType($this->shop_table, "Subdivision_ID");

        if (!$this->shop_id) {
            return false;
        }

        // Сохранить корзину в сессии
        if ($_SESSION["cart_$this->shop_id"])
            $this->Cart = $_SESSION["cart_$this->shop_id"];
        $_SESSION["cart_$this->shop_id"] = &$this->Cart;

        // настройки интернет-магазина -----------------------------------------
        $row = row("SELECT * FROM Message$this->shop_table WHERE Subdivision_ID=$this->shop_id");
        if (!$row["Message_ID"]) {
            print NETCAT_MODULE_NETSHOP_TITLE.": ".NETCAT_MODULE_NETSHOP_ERROR_NO_SETTINGS."<br>";
            return false;
        }

        $stoplist = array("Message_ID", "User_ID", "Subdivision_ID",
            "Sub_Class_ID", "Priority", "Checked", "TimeToDelete",
            "TimeToUncheck", "IP", "UserAgent", "Parent_Message_ID",
            "Created", "LastUpdated", "LastUser_ID", "LastIP",
            "LastUserAgent", "Keyword");

        foreach ($row as $k => $v) {
            if (!in_array($k, $stoplist))
                $this->$k = $v;
        }

        // Настройки раздела имеют приоритет над настройками магазина
        // ----------------------------------------------------------------------
        // курсы валют
        $res = q("SELECT ShopCurrency_ID as Currency_ID, ShopCurrency_Name as Currency_Name
                FROM Classificator_ShopCurrency");

        while (list($cid, $c) = mysqli_fetch_row($res)) {
            $this->Currencies[$cid] = $c;
        }

        // курсы валют ЦБ + Внутренние курсы
        $res = q("SELECT Currency, Rate
                FROM Message$this->official_rates_table
                WHERE Subdivision_ID=$this->shop_id
                ORDER BY Date DESC
                LIMIT " . count($this->Currencies)); // пїЅ GROUP пїЅпїЅпїЅ-пїЅпїЅ пїЅпїЅ пїЅпїЅ
        while (list($cid, $rate) = mysqli_fetch_row($res)) {
            if (!$this->Rates[$cid])
                $this->Rates[$cid] = $rate;
        }

        // внутренние курсы имеют приоритет над официальными курсами
        $res = q("SELECT *
                FROM Message$this->currency_rates_table
                WHERE Subdivision_ID=$this->shop_id
                  AND Checked=1");


        while ($row = mysqli_fetch_assoc($res)) {
            // If rate is set explicitly, it overrides automatically fetched rate
            if ($row["Rate"])
                $this->Rates[$row["Currency"]] = $row["Rate"];
            $this->CurrencyDetails[$this->Currencies[$row["Currency"]]] = $row;
        }


        if ($AUTH_USER_ID && $AUTH_USER_GROUP && $this->price_rules_table) {
            $col = value1("SELECT ActivePriceColumn
                        FROM Message$this->price_rules_table
                        WHERE UserGroup='$AUTH_USER_GROUP'
                          AND Subdivision_ID = $this->shop_id
                        LIMIT 1");

            $this->SetPriceColumn(($col ? $col : "Price"));
        } else {
            $this->SetPriceColumn("Price");
        }

        // units
        $this->Units = array();
        $res = q("SELECT * FROM Classificator_ShopUnits");
        while (list($id, $name) = mysqli_fetch_row($res)) {
            $this->Units[$id] = $name;
        }

        // Положить товары в корзину
        if ($put_to_cart) {
            $this->CartPut($put_to_cart);
        }

        // мэп вэриэблез
        $this->CartContents();
        $this->MapVariables();
    }

    /**
     * Возвращает массив с номерам компонентов, используемых для каталога товаров
     */

    static public function get_goods_table() {
        $result = array();

        $goods = nc_Core::get_object()->modules->get_vars("netshop", "GOODS_TABLE");
        $goods = array_map('intval', explode(',', $goods));
        foreach ($goods as $v)
            if ($v)
                $result[] = $v;

        return $result;
    }

    /**
     *  Установить колонку с ценами и валютами
     */
    function SetPriceColumn($col) {
        // Может меняться e.g. в зависимости от группы пользователя или других факторов
        $this->PriceColumn = $col;
        $this->CurrencyColumn = "Currency" . (str_replace("Price", "", $this->PriceColumn));
    }

    function CartDiscountSum() {
        return $this->CartDiscountSum;
    }

    /**
     * Сумма покупок, совершенных пользователем $user_id
     * @param int id пользователя, по умолчанию - id залогинившегося пользователя
     * @return float
     */
    function PrevOrdersSum($user_id = 0) {
        global $AUTH_USER_ID;
        // cache results in array:
        static $prev_order_sum;

        if ($prev_order_sum[$user_id]) {
            return $prev_order_sum[$user_id];
        }

        if (!$user_id)
            $user_id = $AUTH_USER_ID;

        if (!int($user_id))
            return 0;

        // PREV_ORDERS_SUM_STATUS_ID должен быть в числом или строкой в формате "1,2,3"
        if ($this->prev_orders_sum_status_id) {
            if (!preg_match("/^\s*\d+(?:\s*,\s*\d+)*\s*$/", $this->prev_orders_sum_status_id)) {
                trigger_error(NETCAT_MODULE_NETSHOP_NO_PREV_ORDERS_STATUS_ID, E_USER_WARNING);
                return 0;
            }
        }

        global $db;
        $sum = $db->get_var("SELECT SUM(o.ItemPrice * o.Qty)
                             FROM Netshop_OrderGoods as o,
                                  Message$this->order_table as m
                            WHERE m.User_ID=$user_id
                              AND m.Status IN ($this->prev_orders_sum_status_id)
                              AND o.Order_Component_ID = $this->order_table
                              AND m.Message_ID = o.Order_ID");

        // consider cart discounts also
        $cart_discounts = $db->get_var("SELECT SUM(d.Discount_Sum)
                                        FROM Message$this->order_table as m,
                                             Netshop_OrderDiscounts as d
                                       WHERE m.User_ID=$user_id
                                         AND m.Status IN ($this->prev_orders_sum_status_id)
                                         AND m.Message_ID = d.Order_ID
                                         AND d.Order_Component_ID = $this->order_table
                                         AND d.Item_Type = 0");

        $prev_order_sum[$user_id] = $sum - $cart_discounts;

        return $prev_order_sum[$user_id];
    }

    /**
     * Определить глобальные переменные (так типа удобнее с неткетовскими шаблонами)
     */
    function MapVariables() {
        global $NETSHOP, $SUB_FOLDER;
        $NETSHOP["Netshop_TotalPrice"] = $this->FormatCurrency($this->CartSum());
        $NETSHOP["Netshop_ItemCount"] = $this->CartCount();
        $NETSHOP["Netshop_CartContents"] = &$this->CartContents;
        $NETSHOP["Netshop_CartDiscountSum"] = $this->FormatCurrency($this->CartDiscountSum);

        // static:
        if (!$NETSHOP["Netshop_CartURL"]) {
            $row = GetSubdivisionByType($this->cart_table, "Hidden_URL, s.Subdivision_Name");
            $NETSHOP["Netshop_CartURL"] = $SUB_FOLDER . $row["Hidden_URL"];
            $NETSHOP["Netshop_CartName"] = $row["Subdivision_Name"];

            // links to order template
            $row = GetTemplateByType($this->order_table, $this->shop_id, "c.EnglishName, s.Hidden_URL, c.Sub_Class_Name");

            $NETSHOP["Netshop_OrderURL"] = $SUB_FOLDER . "$row[Hidden_URL]add_$row[EnglishName].html";
        }

        // "В корзине ... товаров на сумму ... "
        if ($NETSHOP["Netshop_ItemCount"]) {
            $NETSHOP["Netshop_CartSum"] =
                    sprintf(NETCAT_MODULE_NETSHOP_CART_CONTENTS, $NETSHOP["Netshop_CartURL"], "$NETSHOP[Netshop_ItemCount] " . netshop_language_count($NETSHOP["Netshop_ItemCount"], NETCAT_MODULE_NETSHOP_ITEM_FORMS), $NETSHOP["Netshop_TotalPrice"]);
            $NETSHOP["Netshop_OrderLink"] = "<a href='$NETSHOP[Netshop_OrderURL]'>" . NETCAT_MODULE_NETSHOP_CART_CHECKOUT . "</a>"; //$row["Sub_Class_Name"];
        } else {
            $NETSHOP["Netshop_CartSum"] = NETCAT_MODULE_NETSHOP_CART_EMPTY;
            $NETSHOP["Netshop_OrderLink"] = "";
        }

        foreach ($GLOBALS["NETSHOP"] as $k => $v) {
            $GLOBALS[$k] = &$GLOBALS["NETSHOP"][$k];
        }
    }

    /**
     * добавление товара в корзину
     * (удаление, если количество = 0)
     * @param array  [$type_id][$id] = $new_nc_Componenty
     * @param string mode ("add": qty=qty+new_qty; otherwise: qty=new_qty)
     */
    function CartPut($array, $mode = "", $custom_params = array()) {
        if (!$this->shop_id) {
            return false;
        }

        foreach ((array) $array as $type_id => $arr) {
            $component = new nc_Component($type_id);
            $fields = $component->get_fields();
            $typeof_unit = 'intval';
            foreach ($fields as $k => $v) {
                if ($v['name'] == 'StockUnits' && $v['type'] == 7)
                    $typeof_unit = 'doubleval';
            }

            $type_id = intval($type_id);

            foreach ((array) $arr as $id => $qty) {
                $id = intval($id);
                $qty = str_replace(",", ".", $qty);
                $qty = call_user_func($typeof_unit, $qty);
                if ($qty <= 0) {
                    unset($this->Cart["goods"][$type_id][$id]);

                    if (!count($this->Cart["goods"][$type_id])) {
                        unset($this->Cart["goods"][$type_id]);
                    }
                } else {
                    if ($mode == "add") {
                        $this->Cart["goods"][$type_id][$id] = array(
                                "Qty" => $this->Cart["goods"][$type_id][$id]["Qty"] + $qty,
                                "cart_params" => (array) $custom_params);
                    } else {
                        $this->Cart["goods"][$type_id][$id] = array(
                                "Qty" => $qty,
                                "cart_params" => $this->Cart["goods"][$type_id][$id]['cart_params']);
                    }
                }
            }
        }

        $_SESSION["cart_$this->shop_id"] = &$this->Cart;
        return true;
    }

    /**
     * Содержимое корзины
     * @param bool заказ из БД (true) / товары из корзины (default)
     * возвращает массив
     *    Type_ID     -- id таблицы message
     *    Qty         -- количество
     *    ItemPrice  -- цена единицы
     *    TotalPrice -- стоимость (цена*количество)
      + свойства товара
     */
    function CartContents() {
        if (!$this->CartCount())
            return;
        $ret = array();

        // получить данные о товарах
        foreach ($this->Cart["goods"] as $type_id => $arr) {
            $res = q("SELECT m.*,

                          IFNULL(m.$this->PriceColumn, parent.$this->PriceColumn) as Price4User,
                          IF(m.$this->PriceColumn IS NULL, parent.$this->CurrencyColumn, m.$this->CurrencyColumn) as Currency4User,

                          IFNULL(m.PriceMinimum, parent.PriceMinimum) as PriceMinimum,

                          IF(m.Keyword IS NULL OR m.Keyword = '', CONCAT(u.Hidden_URL, s.EnglishName, '_', m.Message_ID, '.html'),
                                                CONCAT(u.Hidden_URL, m.Keyword, '.html')) as URL

                     FROM (Message$type_id as m,
                           Subdivision as u,
                           Sub_Class as s)

                     LEFT JOIN Message$type_id as parent
                            ON (m.Parent_Message_ID != 0 AND m.Parent_Message_ID = parent.Message_ID)

                     WHERE m.Message_ID IN (" . join(",", array_keys($arr)) . ")
                       AND s.Sub_Class_ID = m.Sub_Class_ID
                       AND u.Subdivision_ID = m.Subdivision_ID
                     ");

            while ($row = mysqli_fetch_assoc($res)) {
                // Заказ, загруженный при помощи LoadOrder:
                if ($arr[$row["Message_ID"]]["ItemPrice"]) {
                    $price = $arr[$row["Message_ID"]]["ItemPrice"]; // with discounts
                    $original_price = $arr[$row["Message_ID"]]["OriginalPrice"]; // without discounts
                } else { // еще не записанный заказ:
                    $price = $this->ConvertCurrency($row["Price4User"], $row["Currency4User"]);
                    $original_price = $price; // discounts haven't been applied yet
                }

                $qty = $arr[$row["Message_ID"]]["Qty"];

                $ret[] = array_merge($row, array("Class_ID" => $type_id,
                        // to use with 'cart$RowID'
                        "RowID" => "[$type_id][$row[Message_ID]]",
                        "Qty" => $qty,
                        "Units" => $this->Units[$row["Units"]],
                        // PriceMinumum is stored to the SHOP CURRENCY:
                        "PriceMinimum" => $this->ConvertCurrency($row["PriceMinimum"], $row["CurrencyMinimum"]),
                        // Formatted prices:
                        "ItemPriceF" => $this->FormatCurrency($price),
                        "TotalPriceF" => $this->FormatCurrency($price * $qty),
                        // Raw prices in default currency
                        "ItemPrice" => $price,
                        "TotalPrice" => $price * $qty,
                        // Исходные цены (order from db)
                        "OriginalPrice" => $original_price,
                        "OriginalPriceF" => $this->FormatCurrency($original_price),
                        "Discounts" => $arr[$row["Message_ID"]]["Discounts"],
                        'cart_params' => $arr[$row["Message_ID"]]['cart_params']));

                // коллекционируем адреса, потом сделаем рассылку
                $manager_email = $this->GetDepartmentSetting("ManagerEmail", $type_id, $row["Message_ID"], $row["Subdivision_ID"]);
                $this->SendMails[$manager_email][] = sizeof($ret) - 1;
            } // of (foreach row)
        }

        $this->CartContents = $ret;
        if (!$this->OrderID) {
            $this->ApplyDiscounts();
        }
        $this->MapVariables();
        return $ret;
    }

    /**
     *  Сумма по полю $field (в массиве, полученном в CartContents)
     */
    function CartFieldSum($field) {
        //      if (!$this->CartContents) $this->CartContents();

        $sum = 0;

        for ($i = 0; $i < count($this->CartContents); $i++) {
            $sum += $this->CartContents[$i][$field] * $this->CartContents[$i]["Qty"];
        }

        return $sum;
    }

    /**
     * Сумма по корзине
     */
    function CartSum() {
        $sum = $this->CartFieldSum("ItemPrice");
        $sum-= $this->CartDiscountSum;
        if ($this->Order) {
            $sum += $this->Order["PaymentCost"];
            $sum += $this->Order["DeliveryCost"];
        }
        return $sum;
    }

    /**
     * Информация о скидках, которые могут быть применены к товару
     * (для текущего пользователя)
     *
     * @param integer ID шаблона товара
     * @param integer ID товара
     * @return array массив с информацией о скидках, которые могут быть применены к товару
     *   содержит следующие элементы:
     *     Name, Description, UserGroups, Goods, ValidFrom, ValidTo, Condition,
     *     Function, FunctionDestination, FunctionOperator, StopItem
     */
	function ItemDiscountList($subdivision_id, $goods_class_id) {
        global $db, $current_user;
        $subdivision_id = intval($subdivision_id);
        $goods_class_id = intval($goods_class_id);
        $discounts = $db->get_results(
                "SELECT Name, Description, UserGroups, Goods, ValidFrom, ValidTo, `Condition`,
                 Function, FunctionDestination, FunctionOperator, StopItem
            FROM Message{$this->discount_table}
           WHERE AppliesTo = 1
             AND (Subdivisions IS NULL OR Subdivisions='' OR FIND_IN_SET('$subdivision_id', Subdivisions))
             AND (GoodsTypes IS NULL OR GoodsTypes='' OR GoodsTypes = '$goods_class_id')
             AND (UserGroups IS NULL OR UserGroups=''" . (!empty($current_user['Permission_Group']) ? " OR FIND_IN_SET('" . join("', UserGroups) OR FIND_IN_SET('", $current_user['Permission_Group']) . "', UserGroups)" : " OR FIND_IN_SET('" . $current_user['PermissionGroup_ID'] . "', UserGroups)") . ")
             AND ((ValidFrom IS NULL AND ValidTo IS NULL) OR (ValidFrom = '' AND ValidTo = '') OR
                  (ValidFrom <= NOW() AND ValidTo >= NOW()))
             AND (TypeOfPrice IS NULL OR TypeOfPrice='' OR FIND_IN_SET('{$this->PriceColumn}', TypeOfPrice))
             AND Checked = 1
           ORDER BY Priority DESC", ARRAY_A);

        return (array) $discounts;
    }

    /**
     * Получить скидку для товара в абсолютном исчислении
     *
     * Данный метод учитывает только скидки, которые применяются к
     * товарам (не к корзине) и у которых не указано условие примения
     * Cкидки, в результате которых изменяется количество, а не стоимость
     * товара, не учитываются.
     *
     * Внимание! Скидка возвращается в основной валюте!
     *
     * Пример использования:
     *  $shop->ItemDiscountSum($sub, $classID, $f_RowID, $Price, $Currency)
     *
     * @param integer ID шаблона товара
     * @param integer ID товара
     * @param double цена товара в основной валюте
     * @return mixed скидка на товар в абсолютном исчислении в основной валюте.
     *   Отрицательное число означает наценку.
     *
     */
    function ItemDiscountSum($subdivision_id, $goods_class_id, $goods_id, $price, $currency) {
        static $discounts;

        if (!isset($discounts[$subdivision_id][$goods_class_id])) {
            $discounts[$subdivision_id][$goods_class_id] =
                    $this->ItemDiscountList($subdivision_id, $goods_class_id);
        }

        $price_with_discounts = $this->ConvertCurrency($price, $currency);

        foreach ($discounts[$subdivision_id][$goods_class_id] as $discount) {
            // сложные скидки (когда изменяется количество или когда указано условие) не учитываются!
            if ($discount["Condition"])
                continue;
            if ($discount["FunctionDestination"] != '[TotalPrice]')
                continue;

            // относится ли скидка к данному товару:
            if ($discount["Goods"] && strpos(",$discount[Goods],", "$goods_class_id:$goods_id") === FALSE)
                continue;

            $tmp = @eval("return (\$price_with_discounts $discount[FunctionOperator] $discount[Function]);");
            if ($tmp)
                $price_with_discounts = $tmp;
        }

        return ($price - $price_with_discounts);
    }

    /**
     * Find Eligible Discounts
     */
    function ApplyDiscounts() {
        global $db, $AUTH_USER_ID, $current_user;

        if (!$this->CartCount()) {
            return;
        }

        //$mysql_collation = ((double)mysqli_get_server_info() >= 4.1 ? "_cp1251 ":"");
        $this->TotalDiscountSum = 0;

        $cart_goods_types = array_keys($this->Cart['goods']);

        $goods_types = array();
        foreach ($cart_goods_types as $g) {
            //$goods_types[] = "FIND_IN_SET(" . $mysql_collation . "'" . $g . "', GoodsTypes)";
            $goods_types[] = "FIND_IN_SET('" . $g . "', GoodsTypes)";
        }
        $goods_types = implode(" OR ", $goods_types);

        // подходящие нам на первый взгляд (без проверки goods_id, conditions) скидки
        $discounts = $db->get_results("SELECT *
			FROM Message" . $this->discount_table . " as a, User_Group as ug
			WHERE 1 AND
			(
			  (
				a.AppliesTo=2 /* whole cart */ AND
				(a.UserGroups IS NULL OR a.UserGroups = ''" . (!empty($current_user['Permission_Group']) ? " OR FIND_IN_SET('" . implode("', a.UserGroups) OR FIND_IN_SET('", $current_user['Permission_Group']) . "', a.UserGroups)" : " OR FIND_IN_SET('" . $current_user['PermissionGroup_ID'] . "', a.UserGroups)") . ")
			  )
			  OR
			  (
				/* discounts applied to goods */ (a.GoodsTypes = '' OR ". $goods_types . ") AND
				(a.UserGroups IS NULL OR a.UserGroups = ''" . (!empty($current_user['Permission_Group']) ? " OR FIND_IN_SET('" . implode("', a.UserGroups) OR FIND_IN_SET('", $current_user['Permission_Group']) . "', a.UserGroups)" : " OR FIND_IN_SET('" . $current_user['PermissionGroup_ID'] . "', a.UserGroups)") . ")
			  )
			) AND ( /* common part for both cart and goods discounts */
			  (
				(a.ValidFrom IS NULL  AND a.ValidTo IS NULL)  OR
				(a.ValidFrom <= NOW() AND a.ValidTo >= NOW())
			  ) AND
			  (a.TypeOfPrice = '' OR FIND_IN_SET('" . $this->PriceColumn . "', a.TypeOfPrice)) AND
			  Checked = 1
			)
			GROUP BY a.Message_ID
			ORDER BY a.AppliesTo, a.Priority DESC", ARRAY_A);

        if (is_array($discounts) && !empty($discounts)) {
            foreach ($this->CartContents as $idx => $row) {
                foreach ($discounts as $discount) {
                    if ($discount['AppliesTo'] == 1) { // applies to goods
                        // check: subdivision (applies to this subdivision or it's parent)
                        if ($discount['Subdivisions']) {
                            if (strpos(",$discount[Subdivisions],", ",$row[Subdivision_ID],") === false) { // no exact match // check if this item is child of these subdivisions
                                $got_it = false;

                                $parent = $row['Subdivision_ID'];

                                do {
                                    $prev_parent = $parent;
                                    $parent = $parent_cache[$prev_parent] ?: value1("SELECT Parent_Sub_ID FROM Subdivision WHERE Subdivision_ID = $parent");
                                    $parent_cache[$prev_parent] = $parent;

                                    if (strpos(",$discount[Subdivisions],", ",$parent,") !== false) {
                                        $got_it = true;
                                        break; // exit while
                                    }
                                } while ($parent && $parent != $this->shop_id);

                                if (!$got_it) {
                                    continue; // next discount
                                }
                            }
                        }

                        // check: goods ids
                        if ($discount['Goods']) {
                            $tp = ",$discount[Goods],";
                            if (strpos($tp, ",$row[Class_ID]:$row[Message_ID],") === false &&
                                    strpos($tp, ",$row[Class_ID]:$row[Parent_Message_ID],") === false) {
                                continue; // next discount
                            }
                        }

                        // parse condition into evaluable code
                        if (!$discount['cCondition']) { // not "parsed" yet
                            $discount['Condition'] = str_replace('[PrevOrdersSum]', '$this->PrevOrdersSum()', $discount['Condition']);

                            foreach (array('Condition', 'Function') as $k) {
                                $discount["c$k"] = nc_preg_replace("/\[(\w+)\]/", '$row[$1]', $discount[$k]);
                                // replace single '=' to double '=='
                                $discount["c$k"] = nc_preg_replace('/([^=<>]+)=([^=]+)/', '$1==$2', $discount["c$k"]);
                                $discount["c$k"] = str_replace(',', '.', $discount["c$k"]);
                            }
                        }

                        // if there's a condition, evaluate it to determine whether the discount is eligible
                        if ($discount['cCondition']) {
                            if (!@eval("return $discount[cCondition];")) {
                                continue;
                            } // goto next discount
                        }


                        if ($new_value = @eval("return \$row$discount[FunctionDestination] $discount[FunctionOperator] $discount[cFunction];")) {
                            // that's for short
                            $cart = &$this->CartContents[$idx];

                            // changing price
                            if ($discount['FunctionDestination'] === '[TotalPrice]') {
                                $old_price = $cart['TotalPrice'];

                                // check: minimal price reached
                                if ((double) $cart['PriceMinimum'] > $new_value / $cart['Qty']) {
                                    $minimal_price_reached = true;
                                    $new_value = $this->round($cart['PriceMinimum']) * $cart['Qty'];
                                    $cart['ItemPrice'] = $cart['PriceMinimum'];
                                    $cart['TotalPrice'] = $new_value;
                                } else {
                                    $minimal_price_reached = false;
                                    $cart['TotalPrice'] = $this->round($new_value);
                                    $cart['ItemPrice'] = $this->round($new_value / $cart['Qty']);
                                }

                                // двойной пересчёт! (коррекция копеек)
                                if ($cart['TotalPrice'] != $cart['ItemPrice'] * $cart['Qty']) {
                                    // для любопытных: это для того, чтобы избежать видимого несоответствия
                                    // цены и стоимости из-за округления.
                                    $cart['TotalPrice'] = $cart['ItemPrice'] * $cart['Qty'];
                                }
                            }

                            // changing qty
                            if ($discount['FunctionDestination'] === '[Qty]') {
                                $old_price = $cart['ItemPrice'] * $new_value;

                                $cart['Qty'] = $new_value;
                                $cart['ItemPrice'] = $this->round($cart['TotalPrice'] / $new_value);

                                // check: minimal price reached
                                if ((double) $cart['PriceMinimum'] > $cart['ItemPrice']) {
                                    $minimal_price_reached = true;
                                    $cart['ItemPrice'] = $this->round($cart['PriceMinimum']);
                                }

                                $cart['TotalPrice'] = $cart['ItemPrice'] * $new_value;
                            }
                        } // of "apply 'function'"
                        else {
                            continue; // next discount
                        }

                        // Formatted prices:
                        $cart['ItemPriceF'] = $this->FormatCurrency($cart['ItemPrice']);
                        $cart['TotalPriceF'] = $this->FormatCurrency($cart['ItemPrice'] * $cart['Qty']);

                        // total discount sum
                        $discount_sum = $old_price - $cart['TotalPrice'];
                        $this->TotalDiscountSum += $discount_sum; // cart-wide discount sum
                        $cart['DiscountSum'] += $discount_sum; // this item discount sum
                        // discount info
                        $cart['Discounts'][] = array(
                            'Sum'          => $discount_sum,
                            'SumF'         => $this->FormatCurrency($discount_sum),
                            'Discount_ID'  => $discount['Message_ID'],
                            'Name'         => $discount['Name'],
                            'Description'  => $discount['Description'],
                            'PriceMinimum' => $minimal_price_reached
                        );

                        if ($minimal_price_reached || $discount['StopItem']) {
                            break; // go to next goods
                        }
                    } // of "discount applies to goods"
                } // of "foreach discounts"
            } // of "foreach cartcontents"

            // CART-LEVEL DISCOUNTS  - - - - - - - - - - - - - - - - - - - - - - - - - - - -v
            $minimal_price_reached = false;
            $minimal_sum = $this->CartFieldSum('PriceMinimum');

            foreach ($discounts as $discount) {
                $this->CartSumBeforeCartDiscounts = $this->CartFieldSum('ItemPrice');

                if ($discount['AppliesTo'] == 2) { // applies to cart
                    if ($discount['FunctionDestination'] !== '[TotalPrice]') {
                        continue; // only cost can be changed
                    }
                    // parse condition into evaluable code
                    if (!$discount['cCondition']) { // not "parsed" yet
                        $discount['Condition'] = str_replace('[PrevOrdersSum]', '$this->PrevOrdersSum()', $discount['Condition']);

                        foreach (array('Condition', 'Function') as $k) {
                            if (strpos($discount[$k], '[Qty]') !== false) {
                                $discount["c$k"] = str_replace('[Qty]', $this->CartCount(), $discount[$k]);
                            } else {
                                $discount["c$k"] = $discount[$k];
                            }
                            $discount["c$k"] = str_replace('[TotalPrice]', '($this->CartSumBeforeCartDiscounts - $this->CartDiscountSum)', $discount["c$k"]);
                            $discount["c$k"] = nc_preg_replace("/\[(\w+)\]/", "\$this->CartFieldSum('$1')", $discount["c$k"]);
                            // replace single '=' to double '=='
                            $discount["c$k"] = nc_preg_replace('/([^=<>]+)=([^=]+)/', '$1==$2', $discount["c$k"]);
                        }

                        $discount['FunctionOperator'] = str_replace('=', "", $discount['FunctionOperator']);
                    }

                    // check condition (if any)
                    if ($discount['cCondition'] && !@eval("return $discount[cCondition];")) {
                        continue;
                    } // goto next discount

                    $old_value = $this->CartSumBeforeCartDiscounts - $this->CartDiscountSum;

                    if ($new_value = @eval("return \$old_value $discount[FunctionOperator] $discount[cFunction];")) {
                        // minimal price reached???
                        if ($minimal_sum > $new_value) {
                            $minimal_price_reached = true;
                            $new_value = $minimal_sum;
                        }

                        $discount_sum = $this->round($old_value - $new_value);

                        $this->TotalDiscountSum += $discount_sum;
                        $this->CartDiscountSum += $discount_sum;
                        $this->CartDiscounts[] = array(
                            'Sum'          => $discount_sum,
                            'SumF'         => $this->FormatCurrency($discount_sum),
                            'Discount_ID'  => $discount['Message_ID'],
                            'Name'         => $discount['Name'],
                            'Description'  => $discount['Description'],
                            'PriceMinimum' => $minimal_price_reached
                        );
                    }

                    if ($minimal_price_reached || $discount['StopCart']) {
                        break; // done with discounts
                    }
                } // of "applies to cart"
            } // of "each discount"
        }//endif is_array($discounts)
    }

// of "function applydiscounts"

    /**
     * Перевод в [базовую] валюту
     */
    function ConvertCurrency($sum, $from_currency_id, $to_currency = "") {
        if (!$to_currency)
            $to_currency = $this->DefaultCurrencyID;

        // someone might pass Currency_Name instead of Currency_ID by mistake:
        // and i've made such example in docs...
        $to_currency_id = is_numeric($to_currency) ? $to_currency : $this->CurrencyDetails[$to_currency]["Currency"];

        if (!$sum || $from_currency_id == $to_currency_id || !$this->Rates[$from_currency_id])
            return $sum;
        if (!$this->Rates[$to_currency_id])
            $this->Rates[$to_currency_id] = 1;

        // -----------------vvvv т.е. кросс-курс валюты
        $sum = $sum * ($this->Rates[$from_currency_id] / $this->Rates[$to_currency_id]);

        if ($this->CurrencyConversionPercent) {
            $sum = $sum * (100 + $this->CurrencyConversionPercent) / 100;
        }

        // округлить до знака, указанного в настройках
        $sum = $this->round($sum);
        return $sum;
    }

    /**
     * количество товаров в корзине
     */
    function CartCount() {
        $count = 0;
        foreach ((array) $this->Cart["goods"] as $row) {
            $count += count($row);
        }
        return $count;
    }

    /**
     * Форматрирование валюты
     */
    function FormatCurrency($sum, $currency = "", $no_nbsp = false, $font_size = false) {
        // currency_id supplied:
        if (is_numeric($currency)) {
            $currency = $this->Currencies[$currency];
        }
        if (!$currency)
            $currency = $this->Currencies[$this->DefaultCurrencyID];
        $params = &$this->CurrencyDetails[$currency];

        if ($params) {
            $currency = $params["NameShort"];
        }

        if ($params["ThousandSep"] == '[space]') {
            $params["ThousandSep"] = ' ';
        }

        $ret = number_format($sum, $params["Decimals"] ? $params["Decimals"] : NETCAT_MODULE_NETSHOP_CURRENCY_DECIMALS, $params["DecPoint"] ? $params["DecPoint"] : NETCAT_MODULE_NETSHOP_CURRENCY_DEC_POINT, $params["ThousandSep"] ? $params["ThousandSep"] : NETCAT_MODULE_NETSHOP_CURRENCY_THOUSAND_SEP
        );

        $ret = sprintf(str_replace("#", $currency, $params["Format"] ? $params["Format"] : NETCAT_MODULE_NETSHOP_CURRENCY_FORMAT
                ), $ret);

        if (!$no_nbsp) {
            $ret = str_replace(" ", "&nbsp;", $ret);
        }
        if ($font_size) {
            $ret = "<font size='$font_size'>$ret</font>";
        }

        return $ret;
    }

    /**
     * Получить массив со способами (оплаты | доставки), удовлетворяющими условиям
     */
    function EligibleMethodsOf($what, $count_sum = true) {
        $table_id = $this->{"{$what}_methods_table"};
        if (!$table_id) {
            trigger_error("Unknown additional cost '$what', check shop settings", E_USER_ERROR);
            return 0;
        }

        $res = q("SELECT * FROM Message$table_id
                WHERE Subdivision_ID=$this->shop_id
                  AND Checked=1
                ORDER BY Priority DESC");

        $ret = array();

        if ($count_sum)
            $sum = $this->CartSum();

        while ($row = mysqli_fetch_assoc($res)) {
            if ($row["Condition"]) {
                $condition = str_replace("[Qty]", $this->CartCount(), $row["Condition"]);
                $condition = str_replace("[TotalPrice]", "\$this->CartSum()", $condition);
                $condition = nc_preg_replace("/\[(\w+)\]/", "\$this->CartFieldSum('$1')", $condition);
                // replace single '=' to double '=='
                $condition = nc_preg_replace("/([^=<>]+)=([^=]+)/", "$1==$2", $condition);

                // check condition (if any)
                if ($condition) {
                    if (!@eval("return $condition;")) {
                        continue;
                    } // goto next method
                }
            }

            if ($count_sum) {
                $row["Sum"] = 0;
                if ($row["Multiplier"])
                    $row["Sum"] = $sum * ($row["Multiplier"] - 1); // relative sum
                $row["Sum"] += $row["Cost"]; // absolute sum
                $row["Sum"] = $this->round($row["Sum"]);
            }

            $ret[$row["Message_ID"]] = $row;
        }

        return $ret;
    }

    /**
     * Округлить до знака, как указано в настройках валюты (если не указано - до 2 знаков после зпт)
     */
    function round($sum, $currency_symbol = "") {
        if (!$currency_symbol)
            $currency_symbol = $this->Currencies[$this->DefaultCurrencyID];

        $def_currency_settings = $this->CurrencyDetails[$currency_symbol];

        $sum = round($sum, strlen($def_currency_settings["Decimals"]) ?
                        $def_currency_settings["Decimals"] : 2);
        return $sum;
    }

    /**
     * Опустошить корзину
     */
    function ClearCartContents() {
        unset($this->CartContents);
        unset($this->Cart);
        unset($_SESSION["cart_$this->shop_id"]);
        nc_mod_netshop::get_instance()->cart->clear();
        $this->MapVariables();
    }

    /**
     * сохранение содержимого заказа при его оформлении + оповещение менеджеров
     * + e-mail покупателю
     */
    function SaveOrder($order_id) {
        global $HTTP_HOST, $SUB_FOLDER, $HTTP_ROOT_PATH, $LinkID;;

        // system superior object
        $nc_core = nc_Core::get_object();

        if (!int($order_id) || !$this->CartCount())
            return false;
        // get cart contents:

        $this->OrderID = $order_id;

        $payment_method_info = $this->EligibleMethodsOf('payment', 1);
        $payment_method_info = $payment_method_info[$GLOBALS["f_PaymentMethod"]];

        $delivery_method_info = $this->EligibleMethodsOf('delivery', 1);
        $delivery_method_info = $delivery_method_info[$GLOBALS["f_DeliveryMethod"]];

        // UPDATE ORDER PROPERTIES
        // по-хорошему, это должно бы происходить при сохранении объекта "заказ"
        q("UPDATE Message$this->order_table
            SET OrderCurrency=$this->DefaultCurrencyID,
                PaymentCost='$payment_method_info[Sum]',
                DeliveryCost='$delivery_method_info[Sum]'
          WHERE Message_ID='$order_id'");

        $this->Order["PaymentCost"] = $payment_method_info["Sum"];
        $this->Order["DeliveryCost"] = $delivery_method_info["Sum"];

        // get cart contents and save it
        if (!$this->CartContents)
            $this->CartContents();

        ///      dump($this);
        foreach ($this->CartContents as $row) {
            q("INSERT INTO Netshop_OrderGoods
                   SET Order_Component_ID = $this->order_table,
                       Order_ID=$order_id,
                       Item_Type='$row[Class_ID]',
                       Item_ID='$row[Message_ID]',
                       Qty='$row[Qty]',
                       OriginalPrice='$row[OriginalPrice]',
                       ItemPrice='$row[ItemPrice]'
                  ");

            // save discount info for item
            foreach ((array) $row["Discounts"] as $discount) {
                q("INSERT INTO Netshop_OrderDiscounts
                      SET Order_Component_ID = $this->order_table,
                          Order_ID='$order_id',
                          Item_Type='$row[Class_ID]',
                          Item_ID='$row[Message_ID]',
                          Discount_ID='$discount[Discount_ID]',
                          Discount_Name='" . mysqli_real_escape_string($LinkID, $discount["Name"]) . "',
                          Discount_Description='" . mysqli_real_escape_string($LinkID, $discount["Description"]) . "',
                          PriceMinimum='" . (int) $discount["PriceMinimum"] . "',
                          Discount_Sum='$discount[Sum]'
                ");
            }
        }

        // save discount info for the cart
        foreach ((array) $this->CartDiscounts as $discount) {
            q("INSERT INTO Netshop_OrderDiscounts
                   SET Order_Component_ID = $this->order_table,
                       Order_ID='$order_id',
                       Item_Type=0,
                       Item_ID=0,
                       Discount_ID='$discount[Discount_ID]',
                       Discount_Name='" . mysqli_real_escape_string($LinkID, $discount["Name"]) . "',
                       Discount_Description='" . mysqli_real_escape_string($LinkID, $discount["Description"]) . "',
                       PriceMinimum='" . (int) $discount["PriceMinimum"] . "',
                       Discount_Sum='$discount[Sum]'
            ");
        }

        // SEND EMAILS ======================================================
        // check if smtp is configured (windows/demo)
        if (!(ini_get("SMTP") == 'localhost' && !ini_get("sendmail_path"))) {
            include_once("$GLOBALS[ADMIN_FOLDER]/mail.inc.php");
//         include_once('Mail.php');

            $header = sprintf(NETCAT_MODULE_NETSHOP_EMAIL_TO_MANAGER_HEADER, decode_host($GLOBALS["DOMAIN_NAME"]));

            foreach ((array) $this->SendMails as $to => $row) {
                $body = "";
                $sum = 0;

                $res = q("SELECT Field_Name, Description
                      FROM Field
                      WHERE Class_ID=$this->order_table
                        AND TypeOfEdit_ID=1
                      ORDER BY Priority");
                while ($row2 = mysqli_fetch_assoc($res)) {
                    $body .= "$row2[Description]: ";

                    if ($row2["Field_Name"] == "DeliveryMethod") {
                        $body .= $delivery_method_info["Name"];
                    } else if ($row2["Field_Name"] == "PaymentMethod") {
                        $body .= $payment_method_info["Name"];
                    } elseif ($row2["Field_Name"] == "DeliveryCost") {
                        if ($delivery_method_info["Sum"]) {
                            $body .= $this->FormatCurrency($delivery_method_info["Sum"], "", true);
                        }
                    } else {
                        $body .= $GLOBALS["f_$row2[Field_Name]"];
                    }

                    $body .= "\n";
                }

                $body .= str_repeat("-", 75) . "\n";

                $order_mail_subject_length = $nc_core->modules->get_vars("netshop", "ORDER_MAIL_NAME_LENGHT") ? $nc_core->modules->get_vars("netshop", "ORDER_MAIL_NAME_LENGHT") : 35;

                foreach ($row as $i) {
                    $item_id = ($this->CartContents[$i]["ItemID"] ? "[" . $this->CartContents[$i]["ItemID"] . "]" : "");

                    $body .= ( $item_id ? str_pad($item_id, 15) : "") .
                            str_pad(nc_substr(strip_tags($this->CartContents[$i]["Name"]), 0, $order_mail_subject_length), $order_mail_subject_length) . "  " .
                            str_pad($this->CartContents[$i]["Qty"] . " " . $this->CartContents[$i]["Units"], 10, " ") .
                            $this->FormatCurrency($this->CartContents[$i]["TotalPrice"], "", true) .
                            "\n";
                    $sum += $this->CartContents[$i]["TotalPrice"];
                }

                $body .= str_pad('Доставка', $order_mail_subject_length) . "  " .
                $this->FormatCurrency($delivery_method_info["Sum"], "", true) .
                "\n";

                $body .= str_repeat("-", 75) . "\n" .
                        NETCAT_MODULE_NETSHOP_SUM . ": " .
                        $this->FormatCurrency($this->CartSum(), "", true) . "\n\n# $this->OrderID";
                // make link for order
                // links to order template
                $order_tpl = GetTemplateByType($this->order_table, $this->shop_id, "c.Subdivision_ID, c.Sub_Class_ID");
                if ($order_tpl) {
                    $body .= "\n" . nc_get_scheme() . '://' . decode_host($HTTP_HOST) . $SUB_FOLDER . $HTTP_ROOT_PATH . "message.php?catalogue=$GLOBALS[catalogue]&sub=$order_tpl[Subdivision_ID]&cc=$order_tpl[Sub_Class_ID]&message=$this->OrderID&curPos=0\n";
                }

                $nc_core->mail->mailbody(strip_tags($body));
                $nc_core->mail->init(true);
                $nc_core->mail->send($to, $this->MailFrom, $this->MailFrom, $header, $this->ShopName);
            }

            // EMAIL CUSTOMER
            if ($this->email_template_table) {
                $email_template = row("SELECT *  FROM Message$this->email_template_table
                                WHERE Subdivision_ID=$this->shop_id
                                  AND Keyword='OrderConfirmation'");

                if ($email_template && $GLOBALS["f_Email"]) { // we've got what and where to mail
                    // prepare variables
                    $vars = array();

                    $vars["ORDER_ID"] = $this->OrderID;

                    // CUSTOMER
                    foreach ($GLOBALS as $var => $value) {
                        if (nc_substr($var, 0, 2) == "f_") { // properties of the ORDER just posted
                            if (!is_array($value))
                                $vars[("CUSTOMER_" . strtoupper(nc_substr($var, 2)))] = $value;
                        }
                    }

                    // SHOP
                    foreach ($this as $var => $value) {
                        if (!is_array($value) && !is_object($value))
                            $vars["SHOP_" . strtoupper($var)] = $value;
                    }

                    // CART: contents, discounts, delivery, payment, count, sum
                    $vars["CART_CONTENTS"] = str_repeat("-", 78) . "\n";
                    $contents_template = $nc_core->db->get_var("SELECT `Body`  FROM Message$this->email_template_table
                    		WHERE Subdivision_ID=$this->shop_id
                    		AND Keyword='CartContents'");
                    $item_data = '';

                    foreach ($this->CartContents as $item) {
                        $item_id = ($item["ItemID"] ? "[$item[ItemID]]" : "");

                        if ($contents_template) {
	                        $item_line = $contents_template;

	                        foreach ($item as $param => $param_val) {
	                        	if (in_array($param, array("ItemPrice", "TotalPrice"))) {
	                        		$param_val = $this->FormatCurrency($item[$param], "", true);
	                        	}

	                        	$item_line = str_replace("%GOOD_".strtoupper($param)."%", $param_val, $item_line);
	                        }

	                        $item_data .= $item_line."\n";
                        } else {
                        	$item_data .= ( $item_id ? str_pad($item_id, 15) : "") .
	                        	str_pad(nc_substr($item["Name"], 0, $this->order_mail_subject_lenght), 30) . "  " .
	                        	$this->FormatCurrency($item["ItemPrice"], "", true) .
	                        	" x $item[Qty] $item[Units] = " .
	                        	$this->FormatCurrency($item["TotalPrice"], "", true) .
	                        	"\n";
                        }
                    }

                    $vars["CART_CONTENTS"] .= $item_data;
                    $vars["CART_CONTENTS"] .= str_repeat("-", 78) . "\n";

                    // CART: discounts
                    foreach ((array) $this->CartDiscounts as $discount) {
                        $vars["CART_DISCOUNTS"] .= "* $discount[Name]: " .
                                $this->FormatCurrency($discount["Sum"], "", 1) . "\n";
                    }

                    // CART: delivery
                    if ($delivery_method_info["Sum"]) {
                        $vars["CART_DELIVERY"] = "\n" . NETCAT_MODULE_NETSHOP_DELIVERY .
                                " - $delivery_method_info[Name]: " .
                                $this->FormatCurrency($delivery_method_info["Sum"], "", true);
                    }

                    // CART: payment
                    if ($payment_method_info["Sum"]) {
                        $vars["CART_PAYMENT"] = "\n" . NETCAT_MODULE_NETSHOP_PAYMENT .
                                " - $payment_method_info[Name]: " .
                                $this->FormatCurrency($payment_method_info["Sum"], "", true);
                    }

                    // CART: count
                    $vars["CART_COUNT"] = $GLOBALS["NETSHOP"]["Netshop_ItemCount"] . " " .
                            netshop_language_count($GLOBALS["NETSHOP"]["Netshop_ItemCount"], NETCAT_MODULE_NETSHOP_ITEM_FORMS);

                    // CART: sum
                    $vars["CART_SUM"] = $this->FormatCurrency($this->CartSum(), "", true);

                    // До версиях 2.4 — 4.7 заголовок письма был в свойстве Title,
                    // в 5.0 и позднее в LetterTitle
                    foreach (array("LetterTitle", "Title", "Body") as $what) {
                        if (!isset($email_template[$what])) {
                            $email_template[$what] = null;
                            continue;
                        }
                        nc_preg_match_all("/%([\w]+)%/", $email_template[$what], $regs);
                        foreach ($regs[1] as $var) {
                            $email_template[$what] = str_replace("%$var%", $vars[strtoupper($var)], $email_template[$what]);
                        }
                    }

                    $nc_core->mail->mailbody(strip_tags($email_template["Body"]));
                    $nc_core->mail->init(true);
                    $nc_core->mail->send($GLOBALS["f_Email"], $this->MailFrom, $this->MailFrom, $email_template["LetterTitle"] ?: $email_template["Title"], $this->ShopName);
                }
            }
        } // of "send emails"
        // ------------------------------------------------------------------

        if ($payment_method_info["Interface"]) {
            $this->Payment($payment_method_info["Interface"], 'create_bill');
        }

        $this->ClearCartContents();

        return true;
    }

    /**
     * Загрузить заказ
     */
    function LoadDisplayOrder($order_id) {
        if (!int($order_id))
            return false;
        $items = $item_types = array();
        $orders_classID = $GLOBALS['classID'];
        $Cart = array(
            'order' => array(),
            'items' => array(),
            'total' => 0
            );

        $res = q("SELECT * FROM `Message$orders_classID` WHERE `Message_ID`='$order_id'");
        while ($row = mysqli_fetch_assoc($res)) {
            $Cart['order'] = $row;
        }

        $res = q("SELECT * FROM `Netshop_OrderGoods`
                   WHERE `Order_Component_ID` = $this->order_table AND `Order_ID`='$order_id'");
        while ($row = mysqli_fetch_assoc($res)) {
            $items[$row['Item_Type']][] = $row;
            $item_types[$row['Item_Type']][] = $row['Item_ID'];
        }

        foreach ($items as $item_id => $item) {
            $res = q("SELECT m.*,
                IF(m.Keyword IS NULL OR m.Keyword = '', CONCAT(u.Hidden_URL, s.EnglishName, '_', m.Message_ID, '.html'),
                                      CONCAT(u.Hidden_URL, m.Keyword, '.html')) as URL
                FROM (Message$item_id as m,
                 Subdivision as u,
                 Sub_Class as s)
                WHERE m.Message_ID IN (".join($item_types[$item_id], ', ').")
                AND s.Sub_Class_ID = m.Sub_Class_ID
                AND u.Subdivision_ID = m.Subdivision_ID
                ");

            while ($row = mysqli_fetch_assoc($res)) {
                $Cart['items'][] = $row;
                $Cart['total'] += $row['Price'];
            }
        }

        return $Cart;
    }

    /**
     * Загрузить заказ
     */
    function LoadOrder($order_id) {
        if (!int($order_id))
            return false;
        unset($this->Cart);
        $this->CartDiscountSum = 0;
        $this->CartDiscounts = array();
        $this->TotalDiscountSum = 0;
        $this->CartSumBeforeDiscounts = 0;

        $res = q("SELECT * FROM Netshop_OrderGoods WHERE Order_Component_ID=$this->order_table AND Order_ID=$order_id");

        while ($row = mysqli_fetch_assoc($res)) {
            $this->Cart["goods"][$row["Item_Type"]][$row["Item_ID"]] =
                    array("Qty" => $row["Qty"],
                        "OriginalPrice" => $row["OriginalPrice"],
                        "ItemPrice" => $row["ItemPrice"]);

            $this->CartSumBeforeDiscounts += $row["OriginalPrice"] * $row["Qty"];
        }

        $res = q("SELECT * FROM Netshop_OrderDiscounts
                   WHERE Order_Component_ID=$this->order_table
                     AND Order_ID=$order_id");

        while ($row = mysqli_fetch_assoc($res)) {
            $this->TotalDiscountSum += $row["Discount_Sum"];

            $discount = array("Sum" => $row["Discount_Sum"],
                "SumF" => $this->FormatCurrency($row["Discount_Sum"]),
                "Discount_ID" => $row["Discount_ID"],
                "Name" => $row["Discount_Name"],
                "Description" => $row["Discount_Description"],
                "PriceMinimum" => $row["PriceMinimum"]
            );

            if ($row["Item_ID"]) {
                $this->Cart["goods"][$row["Item_Type"]][$row["Item_ID"]]["Discounts"][] = $discount;
            } else { // cart discount
                $this->CartDiscounts[] = $discount;
                $this->CartDiscountSum += $row["Discount_Sum"];
            }
        }

        // order properties
        $this->Order = row("SELECT * FROM Message$this->order_table WHERE Message_ID=$order_id");
        $this->OrderID = $order_id;

        $this->CartContents();
    }

    /**
     * Получить параметр $setting раздела магазина по type/id товара или sub_id
     */
    function GetDepartmentSetting($setting, $goods_type_id = "", $goods_id = "", $sub_id = "") {
        global $db;
        if (!$sub_id && int($goods_type_id) && int($goods_id))
            $sub_id = value1("SELECT Subdivision_ID
                             FROM Message$goods_type_id as m
                             WHERE Message_ID = $goods_id");

        $setting = $db->escape($setting);
        $sub_id = intval($sub_id);
        if (!$sub_id)
            return false;

        // (для кэширования) сюда мы положим ID, для которых будет работать найденное значение
        $sections = array();

        do {
            if ($this->DepartmentSettings[$sub_id][$setting]) {
                return $this->DepartmentSettings[$row["Subdivision_ID"]][$setting];
            }

            $row = row("SELECT m.`" . $setting . "`, s.Parent_Sub_ID
                     FROM Subdivision as s
                       LEFT JOIN `Message" . $this->department_table . "` as m
                            ON (s.Subdivision_ID=m.Subdivision_ID)
                     WHERE s.Subdivision_ID='" . $sub_id . "'");

            $sections[] = $row["Subdivision_ID"];
            $value = $row[$setting];

            if ($value) {
                break;
            } // есть значенье! stop
            $sub_id = $row["Parent_Sub_ID"]; // следующий: родитель
        } while ($sub_id);

        // defaults to shop setting
        if (!$value)
            $value = $this->$setting;

        // fill cache
        foreach ($sections as $id) {
            $this->DepartmentSettings[$id][$setting] = $value;
        }

        return $value;
    }

    // =======================================================================

    /**
     * оплата
     */
    function Payment($system, $stage, $to_string = false) {
        global $nc_core;

        if (!preg_match("/^\w+$/", $system)) {
            trigger_error("Incorrect " . htmlspecialchars($system), E_USER_ERROR);
            return false;
        }

        $file_path = $nc_core->MODULE_FOLDER . "netshop/payment/" . $system . ".php";

        if (!file_exists($file_path)) {
            trigger_error(htmlspecialchars($system) . " NOT FOUND", E_USER_ERROR);
            return false;
        }

        include_once($file_path);
        $payment = 'Payment_' . $system;
        $payment = new $payment($this);
        if ($payment) {
            return $payment->$stage($to_string);
        }
    }

    function PrintCart() {
        global $SUB_FOLDER, $HTTP_ROOT_PATH, $HTTP_HOST;
        if (!$this->CartCount()) {
            return NETCAT_MODULE_NETSHOP_CART_EMPTY;
        }

        $has_item_discounts = ($this->TotalDiscountSum != $this->CartDiscountSum);

        $ret = "<form method=post action='" . $SUB_FOLDER . $HTTP_ROOT_PATH . "modules/netshop/post.php' class=cart_contents id=netshop_cart_contents>
        <input type=hidden name=redirect_url value='$_SERVER[REQUEST_URI]'>";

        if ($has_item_discounts) {
            $ret .= "
<script>
var ns_track_x, ns_track_y; // coordinates of the mouse
var ns_discount_shown = false;

function netshop_show_discount(discount_names_array, full_price, final_price)
{
   var div = document.getElementById('netshop_discount_div'),
       txt = '" . NETCAT_MODULE_NETSHOP_APPLIED_DISCOUNTS . "<p>';

   for (var i in discount_names_array)
   {
      txt += '&mdash; '+discount_names_array[i] + '<br>';
   }

   txt += '</p>" . NETCAT_MODULE_NETSHOP_PRICE_WITHOUT_DISCOUNT . ": '+full_price
        + '<br>" . NETCAT_MODULE_NETSHOP_PRICE_WITH_DISCOUNT . ": '+final_price;

   // snap to cursor pos
   div.style.top = ns_track_y + 5 + 'px';
   div.style.left= ns_track_x + 5 + 'px';
   ns_discount_shown = true;

   div.innerHTML = txt;
   div.style.display = '';
}

function netshop_hide_discount()
{
   document.getElementById('netshop_discount_div').style.display = 'none';
   ns_discount_shown = false;
}

function netshop_track_mouse(event)
{
   if (!event) event = window.event; // MSIE
   ns_track_x = (event.x != undefined) ? event.x  + document.body.scrollLeft : event.pageX;
   ns_track_y = (event.y != undefined) ? event.y  + document.body.scrollTop  : event.pageY;

   if (ns_discount_shown)
   {
      var div = document.getElementById('netshop_discount_div');
      div.style.top = ns_track_y + 5 + 'px';
      div.style.left= ns_track_x + 5 + 'px';
   }
}
</script>
<div id=netshop_discount_div style='display:none; position: absolute'></div>";
        }

        $ret .= "<table border=0 cellspacing=0 cellpadding=0 width=100%>
         <tr>
          <th class=name width=35%>" . NETCAT_MODULE_NETSHOP_ITEM . "</th>" .
                ($has_item_discounts ? "<th>" . NETCAT_MODULE_NETSHOP_DISCOUNT . "</th>" : "") .
                "<th>" . NETCAT_MODULE_NETSHOP_ITEM_PRICE . "</th>" .
                "<th width=10%>" . NETCAT_MODULE_NETSHOP_QTY . "</th>" .
                "<th>" . NETCAT_MODULE_NETSHOP_COST . "</th>" .
                "<th>" . NETCAT_MODULE_NETSHOP_ITEM_DELETE . "</th></tr>";

        $i = 0;
        foreach ($this->CartContents as $row) {
            $ret .= "<tr class=" . (++$i % 2 ? "odd" : "even") . " align=center>
          <td class=name><a href='$row[URL]' target=_blank>$row[Name]</a>";

            if ($has_item_discounts) {
                if ($row["OriginalPrice"] - $row["ItemPrice"]) { // has discount
                    $js_discount_names = array();
                    foreach ($row["Discounts"] as $discount) {
                        $js_discount_names[] = addcslashes($discount["Name"], "'\n");
                    }

                    $js_discount_names = "['" . join("', '", $js_discount_names) . "']";

                    $ret .= "<td><a href='javascript:void(0)' " .
                            "onmouseover=\"netshop_show_discount($js_discount_names, '$row[OriginalPriceF]', '$row[ItemPriceF]')\" " .
                            "onmouseout='netshop_hide_discount()'>" .
                            $this->FormatCurrency($row["OriginalPrice"] - $row["ItemPrice"]) .
                            "</a></td>";
                } else {
                    $ret .= "<td>&mdash;</td>";
                }
            }

            $ret .= "<td>$row[ItemPriceF]</td>
                  <td class=qty><input type=text size=2 name='cart$row[RowID]' value='$row[Qty]'> $row[Units]</td>
                  <td>$row[TotalPriceF]</td>
                  <td><input type=checkbox name='cart$row[RowID]' value=-1></td>
                 </tr>";
        }

        if ($this->CartDiscounts) {
            foreach ($this->CartDiscounts as $discount) {
                $ret .= "<tr align=center class=cart_discount><td colspan=" .
                        ($has_item_discounts ? 4 : 3) . " class=name>
                     <b>$discount[Name]</b>" .
                        ($discount["Description"] ? "<br>$discount[Description]" : "") .
                        "</td><td>" . ($discount["Sum"] > 0 ? "-" : "") . "$discount[SumF]</td>
                     <td>&nbsp;</td></tr>\n";
            }
        }

        $ret .= "<tr align=center class=totals><td colspan=" .
                ($has_item_discounts ? 4 : 3) . " class=name>" . NETCAT_MODULE_NETSHOP_SUM . "</td><td>" .
                ($this->FormatCurrency($this->CartSum())) . "</td><td>&nbsp;</td></tr>";

        if (ini_get("session.use_trans_sid")) {
            $sname = session_name();
            $sid = "?$sname=$GLOBALS[$sname]";
        } else {
            $sid = "";
        }

        $ret .= "</table>
       <div class=cart_buttons>
         <input type=submit value='" . NETCAT_MODULE_NETSHOP_REFRESH . "'>
         <input type=button
          onclick='window.location=\"" . nc_get_scheme() . '://' . $HTTP_HOST . "{$GLOBALS['NETSHOP']['Netshop_OrderURL']}$sid\"'
          value='" . NETCAT_MODULE_NETSHOP_CART_CHECKOUT . "'>
         <noscript>{$GLOBALS['NETSHOP']['Netshop_OrderLink']}</noscript>
       </div>
      </form>";

        if ($has_item_discounts) {
            $ret .= "<script>document.getElementById('netshop_cart_contents').onmousemove = netshop_track_mouse;</script>";
        }

        return $ret;
    }

    function PrintOrderForm() {
        global $SUB_FOLDER, $HTTP_ROOT_PATH, $db;
        if (!$this->CartCount()) {
            return NETCAT_MODULE_NETSHOP_ERROR_CART_EMPTY;
        }
        $ret = "";

        if ($GLOBALS['warnText']) {
            $ret .= "<p class=netshop_error>" . $GLOBALS['warnText'] . "</p>";
        }

        $ret .= "<div class='order_form'>
              <form method='post' action='" . $SUB_FOLDER . $HTTP_ROOT_PATH . "add.php'>
               <input name='cc' type='hidden' value='{$GLOBALS[cc]}'>
               <input name='sub' type='hidden' value='{$GLOBALS[sub]}'>
               <input name='catalogue' type='hidden' value='{$GLOBALS[catalogue]}'>
               <input type='hidden' name='posting' value='1'>";

        $res = q("SELECT *
                FROM Field
                WHERE Class_ID=$this->order_table
                  AND TypeOfEdit_ID=1
                ORDER BY Priority");

        // $GLOBALS["current_user"]
        while ($row = mysqli_fetch_assoc($res)) {
            // Payment and Delivery
            if (preg_match("/^(Payment|Delivery)Method$/", $row["Field_Name"], $regs)) {
                $what = $regs[1];
                $methods = $this->EligibleMethodsOf(strtolower($what), 1);
                if (preg_match("/^Payment$/", $what))
                    $methodtable = $this->payment_methods_table;
                if (preg_match("/^Delivery$/", $what))
                    $methodtable = $this->delivery_methods_table;
                $count_methods = $db->get_var("SELECT COUNT(`Message_ID`) FROM `Message" . $methodtable . "` WHERE `Checked`=1 ");
                if ($count_methods) {
                    $ret .= "$row[Description]" . ($row["NotNull"] ? " (*)" : "") . ":<br>\n";
                    $value = htmlspecialchars($_POST['posting'] ? $_POST["f_" . $row["Field_Name"]] : $GLOBALS["current_user"][$row["Field_Name"]]);
                    foreach ($methods as $i => $method) {

                        $ret.= "<input type='radio' " . ($method["Message_ID"] == $value ? "checked " : "") . "name='f_{$what}Method'
            	           value='$method[Message_ID]'
            	           id='rb$what$method[Message_ID]'> <label for='rb$what$method[Message_ID]'>
            	           $method[Name] " .
                                ($method["Sum"] ? "(" . $this->FormatCurrency($method["Sum"]) . ")" : "") .
                                "</label>";
                        if ($method["Description"]) {
                            $ret .= "<blockquote>$method[Description]</blockquote>";
                        } else {
                            $ret .= "<br />";
                        }
                    }
                }
            } else {
                $value = $_POST['posting'] ? $_POST["f_" . $row["Field_Name"]] : $GLOBALS["current_user"][$row["Field_Name"]];
                switch ($row["TypeOfData_ID"]) {
                    case 1:
                        # String
                        $ret.= nc_string_field("$f_$row[Field_Name]", "", $classID, 1, $value) . "\n";
                        break;

                    case 2:
                        # Int
                        $ret.= nc_int_field("$row[Field_Name]", "", $classID, 1) . "\n";
                        break;

                    case 3:
                        # Text
                        $ret.= nc_text_field("$row[Field_Name]", "", $classID, 1, false, $value) . "\n";
                        break;

                    case 4:
                        # List
                        $ret.= nc_list_field("$row[Field_Name]", "", $classID, 1) . "\n";
                        break;

                    case 5:
                        # Bool
                        $ret.= nc_bool_field("$row[Field_Name]", "", $classID, 1) . "\n";
                        break;

                    case 6:
                        # File
                        $ret.= nc_file_field("$row[Field_Name]", "", $classID, 1) . "\n";
                        break;

                    case 7:
                        # Float
                        $ret.= nc_float_field("$row[Field_Name]", "", $classID, 1) . "\n";
                        break;

                    case 8:
                        # DateTime
                        $ret.= nc_date_field("$row[Field_Name]", "", $classID, 1) . "\n";
                        break;

                    case 9:
                        # Relation
                        $ret.= nc_related_field("$row[Field_Name]") . "\n";
                        break;

                    case 10:
                        # Multiselect
                        $ret.= nc_multilist_field("$row[Field_Name]", "", "", $classID, 1) . "\n";
                        break;
                }
            }

            $ret .="<br /><br />";
        }



        $ret .= "<div class='order_buttons'><input type='submit' title=\"" . NETCAT_MODULE_NETSHOP_CART_CHECKOUT . "\" value=\"" . NETCAT_MODULE_NETSHOP_CART_CHECKOUT . "\"></div>";

        $ret .= "</form></div>";
        return $ret;
    }

    function GuessGoodsTypeIDs() {
        return NetShop::get_goods_table();
    }

    function GetBestsellers($type_ids = "", $number = 5, $section = false) {
        if ($type_ids) {
            $type_ids = nc_preg_split("/,\s*/", $type_ids);
        } else if (!$this->GoodsTypeIDs) {
            $this->GoodsTypeIDs = $this->GuessGoodsTypeIDs();
            $type_ids = &$this->GoodsTypeIDs;
        }
        else {
            $type_ids = &$this->GoodsTypeIDs;
        }

        if (!$type_ids) return array();

        if ($section) {
            $structure = GetStructure($section, "", "get_children");
            $all_children = array_keys($structure);
            foreach ($structure as $row) {
                if ($row["Children"])
                    $all_children = array_merge($all_children, $row["Children"]);
            }
            if ($all_children) {
                $subdivisions_qry = " AND m.Subdivision_ID IN ($section, " . join(", ", $all_children) . ")";
            } else {
                $subdivisions_qry = " AND m.Subdivision_ID = '" . $section > "'";
            }
        } else {
            $subdivisions_qry = "";
        }

        $q = array();
        foreach ($type_ids as $type_id) {
            int($type_id);

            $q[] = "SELECT $type_id as Type_ID,
                        m.Message_ID,
                        m.Name,
                        m.Description,
                        m.Price,
                        m.Currency,
                        m.Image,

                        CONCAT(sd.Hidden_URL, sc.EnglishName, '_',
                               m.Message_ID, '.html') as URL,

                        (IF (m.TopSellingMultiplier IS NOT NULL,
                             SUM(o.Qty)*m.TopSellingMultiplier,
                             SUM(o.Qty)) +
                         IF (m.TopSellingAddition IS NOT NULL,
                             m.TopSellingAddition, 0)
                        ) as Rating

                 FROM Message$type_id as m,
                      Netshop_OrderGoods as o,
                      Subdivision as sd,
                      Sub_Class as sc

                 WHERE o.Item_Type=$type_id
                   AND o.Item_ID = m.Message_ID
                   AND m.Checked = 1
                   $subdivisions_qry

                   AND sd.Subdivision_ID = m.Subdivision_ID
                   AND sc.Class_ID = $type_id
                   AND sc.Subdivision_ID = m.Subdivision_ID

                 GROUP BY o.Item_ID";
        }
        $res = q(join(" UNION ", $q) . " ORDER BY Rating DESC LIMIT $number");
        $ret = array();
        while ($row = mysqli_fetch_assoc($res)) {
            $ret[] = $row;
        }
        return $ret;
    }

    public function check_payment_errors($payment_method) {
        $error = false;
        $nc_core = nc_Core::get_object();
        $db = $nc_core->db;
        switch ($payment_method) {
            case 'assist':
                if (!$this->AssistShopId) {
                    $error = NETCAT_MODULE_NETSHOP_ERROR_ASSIST;
                }
                break;

            case 'paypal':
                if (!$this->PaypalBizMail || !$this->Currencies[$this->DefaultCurrencyID]) {
                    $error = NETCAT_MODULE_NETSHOP_ERROR_PAYPAL_MAIL;
                    break;
                }
                $rates_table = $nc_core->modules->get_vars('netshop', 'OFFICIAL_RATES_TABLE');
                $SQL = "SELECT Rate
                            FROM Message{$rates_table}
                                WHERE Currency=2";
                if ($this->Currencies[$this->DefaultCurrencyID] != "USD" && !$db->get_var($SQL)) {
                    $error = NETCAT_MODULE_NETSHOP_ERROR_PAYPAL_RATES;
                }
                break;

            case 'qiwi':
                if (!$this->QiwiFrom || !$this->QiwiPwd) {
                    $error = NETCAT_MODULE_NETSHOP_ERROR_QIWI;
                }
                break;

            case 'mail':
                if (!$this->MailShopID || !$this->MailHash || !$this->MailSecretKey) {
                    $error = NETCAT_MODULE_NETSHOP_ERROR_MAIL;
                }
                break;

            case 'robokassa':
                if (!$this->RobokassaLogin || !$this->RobokassaPass1 || !$this->RobokassaPass2) {
                    $error = NETCAT_MODULE_NETSHOP_ERROR_ROBOKASSA;
                }
                break;
            case 'webmoney':
                if (!$this->WebmoneyPurse || !$this->WebmoneySecretKey) {
                    $error = NETCAT_MODULE_NETSHOP_ERROR_WEBMONEY;
                }
                break;
            case 'paycash_email':
                if (!$this->PayCashSettings) {
                    $error = NETCAT_MODULE_NETSHOP_ERROR_YANDEX;
                }
                break;
            case 'paymaster':
                if (!$this->PaymasterID || !$this->PaymasterWord) {
                    $error = NETCAT_MODULE_NETSHOP_ERROR_PAYMASTER;
                }
                break;
        }
        return $error;
    }

}