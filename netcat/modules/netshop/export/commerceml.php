<?

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)) . (strstr(__FILE__, "/") ? "/" : "\\");
include_once($NETCAT_FOLDER . "vars.inc.php");
require_once($ADMIN_FOLDER . "function.inc.php");

require_once($MODULE_FOLDER . "netshop/function.inc.php");

if (is_file($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php")) {
    require_once($MODULE_FOLDER . "netshop/" . MAIN_LANG . ".lang.php");
} else {
    require_once($MODULE_FOLDER . "netshop/en.lang.php");
}

class Netshop_ExportCML extends nc_mod_netshop {

    function Netshop_ExportCML() {
        parent::__construct();
    }

    /**
     * Biztalk export
     */
    function ExportCML($order_id) {
        if (!int($order_id)) return false;

        $this->LoadOrder($order_id);
        $this->CartContents();
        $nc_core = nc_Core::get_object();
        // работает только если один и тот же " каталог товаров" в 1С
        list($ext_company_id, $ext_catalogue_id) = explode(" ",
            value1("SELECT external_id
                              FROM Netshop_ImportSources
                             WHERE source_id='" . $this->CartContents[0]["ImportSourceID"] . "'"));

        header("Content-Type: Aplication/xml-file");
        header("Content-Disposition: attachment; filename=order{$this->OrderID}.xml");

        print '<?xml version="1.0" encoding="' . $nc_core->NC_CHARSET . '"?><КоммерческаяИнформация>';

        //      if ($this->Order['DeliveryCost'] || $this->Order['PaymentCost'])
        //      {
        print '<Каталог Идентификатор="' . $ext_catalogue_id . '">';

        foreach ($this->CartContents as $item) {
            $item_ext_id = nc_preg_replace("/^ID/", "", $item["ItemID"]);
            if (!$item_ext_id)
                $item_ext_id = "g$item[Class_ID]_$item[Message_ID]";

            print '<Товар Идентификатор="' . $item_ext_id . '"
                 ИдентификаторВКаталоге="' . $item_ext_id . '"
                 Наименование="' . xmlspecialchars($item['Name']) . '" />';
        }


        if ($this->Order['DeliveryCost']) {
            print '<Товар Идентификатор="dlvr' . $this->Order['DeliveryMethod'] . '"
                   ИдентификаторВКаталоге="dlvr' . $this->Order['DeliveryMethod'] . '"
                   Наименование="Доставка (' .
                value1("SELECT Name
                                  FROM Message{$this->delivery_methods_table}
                                 WHERE Message_ID={$this->Order[DeliveryMethod]}") .
                ')" Единица="шт"/>';
        }

        if ($this->Order['DeliveryCost']) {
            print '<Товар Идентификатор="pmnt' . $this->Order['PaymentMethod'] . '"
                   ИдентификаторВКаталоге="pmnt' . $this->Order['PaymentMethod'] . '"
                   Наименование="Наценка (' .
                value1("SELECT Name
                                  FROM Message{$this->payment_methods_table}
                                 WHERE Message_ID={$this->Order[PaymentMethod]}") .
                ')" Единица="шт"/>';
        }
        print "</Каталог>";
        //      }

        $order_timestamp = timestamp($this->Order["Created"]);
        $order_date = strftime("%Y-%m-%d", $order_timestamp);
        $order_time = strftime("%H:%M:%S", $order_timestamp);

        $currency = $this->Currencies[$this->Order["OrderCurrency"]];
        if ($currency == "RUR") $currency = "руб.";

        print '<Документ Дата="' . $order_date . '" Номер="' . $this->Order['Message_ID'] . '/И"
              Время="' . $order_time . '" СрокПлатежа="' . $order_date . '"
              ХозОперация="Order" Сумма="' . $this->CartSum() . '"
              Валюта="' . $currency . '" Курс="1" Кратность="1">

             <ПредприятиеВДокументе Контрагент="' . $ext_company_id . '" Роль="Saler" />
             <ПредприятиеВДокументе Контрагент="siteuser' . $this->Order['User_ID'] . '" Роль="Buyer"
              Контакт="contact' . $this->OrderID . '" />'; // sic! контакты м.б. разными у разных заказов (или случай с незарегистрированными пользователями)

        $cart_discount_ratio = 1;
        if ($this->CartDiscountSum) { // calculate percent of cart discount
            $cart_discount_ratio = 1 - ($this->CartDiscountSum / $this->CartFieldSum('ItemPrice'));
        }

        foreach ($this->CartContents as $item) {
            $item_ext_id = nc_preg_replace("/^ID/", "", $item["ItemID"]);
            if (!$item_ext_id)
                $item_ext_id = "g$item[Class_ID]_$item[Message_ID]";
            print '<ТоварнаяПозиция
      	        Каталог="' . $ext_catalogue_id . '"
      	        Товар="' . $item_ext_id . '"
      	        Единица="' . $item["Units"] . '"
      	        Количество="' . $item["Qty"] . '"
      	        Цена="' . ($item["ItemPrice"] * $cart_discount_ratio) . '"
      	        Сумма="' . ($item["ItemPrice"] * $item['Qty'] * $cart_discount_ratio) . '"
      	        >';

            $vat = $item['VAT'] ? $item['VAT'] : $this->VAT;
            if ($vat) {
                print '<СуммаНалога Налог="AVT" Ставка="' . $vat . '"
                    Сумма="' . ($item['ItemPrice'] * $vat / 100) . '" ВключенВСумму="1"/>';
            }
            print "</ТоварнаяПозиция>";
        }

        // включить стоимость доставки в счет
        if ($this->Order['DeliveryCost']) {
            // <ДополнительныйРасход> почему-то не учитывается в 1С 7.7
            print '<ТоварнаяПозиция Каталог="' . $ext_catalogue_id . '"
                 Товар="dlvr' . $this->Order['DeliveryMethod'] . '"
                 Единица="шт" Количество="1"
                 Цена="' . $this->Order['DeliveryCost'] . '"
                 Сумма="' . $this->Order['DeliveryCost'] . '" />';
        }

        // включить "наценку за способ оплаты"
        if ($this->Order['PaymentCost']) {
            print '<ТоварнаяПозиция Каталог="' . $ext_catalogue_id . '"
                 Товар="pmnt' . $this->Order['PaymentMethod'] . '"
                 Единица="шт" Количество="1"
                 Цена="' . $this->Order['PaymentCost'] . '"
                 Сумма="' . $this->Order['PaymentCost'] . '" />';
        }

        print '</Документ>';

        $contragent = xmlspecialchars($this->Order['CompanyName'] ? $this->Order['CompanyName'] : $this->Order['ContactName']);
        print '<Контрагент Идентификатор="' . $ext_company_id . '" />
             <Контрагент Идентификатор="siteuser' . $this->Order['User_ID'] . '"
              Наименование="' . $contragent . '"
              ОтображаемоеНаименование="' . $contragent . '"
              Адрес="' . xmlspecialchars($this->Order['Address']) . '"
              Комментарий="' . xmlspecialchars($this->Order['Comments']) . '">

              <Контакт Идентификатор="contact' . $this->OrderID . '"
               Наименование="' . xmlspecialchars($this->Order['ContactName']) . '">
                <Телефон>' . xmlspecialchars($this->Order['Phone']) . '</Телефон>
                <Почта>' . xmlspecialchars($this->Order['Email']) . '</Почта>
             </Контакт>
           </Контрагент>';

        print '</КоммерческаяИнформация>';
    }

}

if (!($perm->isSupervisor() || $perm->isGuest())) {
    die("NO RIGHTS");
}

//LoadModuleEnv();
$catalogue = $nc_core->catalogue->get_by_host_name($HTTP_HOST);
$catalogue = $catalogue["Catalogue_ID"];
if (!$catalogue) $catalogue = 1;

$shop = new NetShop_ExportCML();
$shop->ExportCML($_GET["order_id"]);