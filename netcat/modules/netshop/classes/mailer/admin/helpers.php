<?php

class nc_netshop_mailer_admin_helpers {

    /**
     * Выводит HTML-фрагмент для вставки скриптов редактора писем
     * @param nc_netshop $netshop
     * @return void
     */
    static public function include_template_editor_js(nc_netshop $netshop) {
        // prepare variables list for the 'Insert Variable...' drop-down
        // 1) site
        $site_variables = array_merge(array(
                "{site.Catalogue_Name}" => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NAME,
                "{site.Domain}" => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DOMAIN,
            ),
            nc_netshop_mailer_admin_helpers::get_variables('site')
        );

        // 2) user
        $user_variables = nc_netshop_mailer_admin_helpers::get_variables('user');

        // 3) order
        $order_class_id = $netshop->get_setting('OrderComponentID');
        $order_variables = array_merge(
            array(
                '{order.Message_ID}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_ID,
                '{order.Date}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DATE,
            ),
            nc_netshop_mailer_admin_helpers::get_variables($order_class_id, 'order'),
            array(
                // Стоимость товаров в корзине
                '{order.TotalItemPriceWithoutCartDiscountF}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_ITEM_PRICE,
                '{order.TotalItemPriceCartDiscount}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_ITEM_PRICE . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,
                '{order.TotalItemPriceF}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_CART_PRICE,
                '{order.TotalItemPrice}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_CART_PRICE . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,
                '{order.TotalItemOriginalPriceF}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_CART_PRICE_WITHOUT_DISCOUNT,
                '{order.TotalItemOriginalPrice}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_CART_PRICE_WITHOUT_DISCOUNT . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,
                // Скидки на товары
                '{order.TotalItemDiscountSumF}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_CART_ITEMS_DISCOUNT,
                '{order.TotalItemDiscountSum}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_CART_ITEMS_DISCOUNT . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,
                // Скидка на корзину (состав заказа)
                '{order.CartDiscountSumF}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_CART_DISCOUNT,
                '{order.CartDiscountSum}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_CART_DISCOUNT . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,
                // Скидка на доставку
                '{order.DeliveryDiscountSumF}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_DELIVERY_DISCOUNT,
                '{order.DeliveryDiscountSum}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_DELIVERY_DISCOUNT . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,
                // Скидка на корзину + скидки на доставку
                '{order.OrderDiscountSumF}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_ORDER_DISCOUNT,
                '{order.OrderDiscountSum}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_ORDER_DISCOUNT . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,

                // Итоги по заказу (товары + доставка + наценка за оплату)
                '{order.TotalPriceF}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_PRICE,
                '{order.TotalPrice}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_PRICE . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,
                // Сумма всех скидок на заказ
                '{order.DiscountSumF}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_DISCOUNT,
                '{order.DiscountSum}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_DISCOUNT . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,

                // Информация о способе доставки
                '{order.DeliveryMethodName}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_METHOD_NAME,
                '{order.DeliveryVariantAndMethodName}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_METHOD_VARIANT_NAME,
                '{order.DeliveryAddress}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_ADDRESS,
                // информация о точке выдачи
                '{order.DeliveryPointName}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_POINT_NAME,
                '{order.DeliveryPointDescription}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_POINT_DESCRIPTION,
                '{order.DeliveryPointAddress}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_POINT_ADDRESS,
                '{order.DeliveryPointPhones}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_POINT_PHONES,
                '{order.DeliveryPointSchedule}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_POINT_SCHEDULE,

                '{order.DeliveryDates}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_DATES,
                '{order.DeliveryPriceF}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_PRICE,
                '{order.DeliveryPrice}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_PRICE . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,
                '{order.DeliveryPriceWithDiscountF}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_PRICE_WITH_DISCOUNT,
                '{order.DeliveryPriceWithDiscount}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_PRICE_WITH_DISCOUNT . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,

                // Информация о способе оплаты
                '{order.PaymentMethodName}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_PAYMENT_METHOD_NAME,
                '{order.PaymentPriceF}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_PAYMENT_CHARGE,
                '{order.PaymentPrice}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_PAYMENT_CHARGE . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,
            )
        );

        // do not show raw PaymentMethod and DeliveryMethod variables to avoid confusion
        unset($order_variables['{order.DeliveryMethod}'],
              $order_variables['{order.DeliveryCost}'],
              $order_variables['{order.PaymentMethod}'],
              $order_variables['{order.PaymentCost}']);

        // 4) cart
        $cart_variables = array_merge(
            nc_netshop_mailer_admin_helpers::get_variables('item'),
            array(
                '{item.FullName}' => NETCAT_MODULE_NETSHOP_ITEM_FULL_NAME,
                '{item.ItemPriceF}' => NETCAT_MODULE_NETSHOP_ITEM_PRICE,
                '{item.ItemPrice}'  =>  NETCAT_MODULE_NETSHOP_ITEM_PRICE . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,
                '{item.OriginalPriceF}' => NETCAT_MODULE_NETSHOP_PRICE_WITHOUT_DISCOUNT,
                '{item.OriginalPrice}'  => NETCAT_MODULE_NETSHOP_PRICE_WITHOUT_DISCOUNT . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,
                '{item.Qty}' => NETCAT_MODULE_NETSHOP_QTY,
                '{item.Units}' => NETCAT_MODULE_NETSHOP_UNITS,
                '{item.TotalPriceF}' => NETCAT_MODULE_NETSHOP_COST,
                '{item.TotalPrice}' => NETCAT_MODULE_NETSHOP_COST . NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE,
                '{item.URL}' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_URL,
            )
        );

        if ($cart_variables['{item.Price}']) {
            $cart_variables['{item.Price}'] = NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_PRICE_AS_DEFINED;
        }

        // 5) shop
        $shop_variables = array();
        foreach (nc_netshop_admin_helpers::get_shop_fields() as $field_name => $field_options) {
            if (isset($field_options['caption'])) {
                $shop_variables["{shop.$field_name}"] = $field_options['caption'];
            }
        }

        // 6) coupon
        $coupon_variables = array(
            "{coupon.code}" => NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_CODE,
        );

        $variables = array(
            NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_SITE_VARIABLES => $site_variables,
            NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_SHOP_VARIABLES => $shop_variables,
            NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_USER_VARIABLES => $user_variables,
            NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_VARIABLES => $order_variables,
            NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_CART_VARIABLES => $cart_variables,
            NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_COUPON_VARIABLES => $coupon_variables,
        );

        $lang = array(
            "INSERT_VARIABLES" => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_INSERT_VARIABLES,
            "CHILD_TEMPLATE" => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_INSERT_CHILD_TEMPLATE
        );


        if (!class_exists("CKEditor")) {
            include_once(nc_core('ROOT_FOLDER') . "editors/ckeditor4/ckeditor.php");
        }

        $editor = new CKEditor();
        // нужно ли проверять, не загружен ли уже ckeditor?

        $nc = '$nc';
        $variables = nc_array_json($variables);
        $lang = nc_array_json($lang);

        $html = <<<END_OF_FRAGMENT

        <script>var CKEDITOR_BASEPATH = '{$editor->getBasePath()}';</script>
        <script src='{$editor->getScriptPath()}'></script>
        <style>
            .insert_template_variable_combo .cke_combo_inlinelabel { width: 150px; }
            .cke_combopanel__mailtemplatevariables { width: 380px; }
        </style>
        <script>
        (function() {
            var variables = $variables,
                lang = $lang;

            CKEDITOR.plugins.add('MailTemplateVariables', {
                // based on http://ckeditor.com/addon/strinsert
                requires: ['richcombo'],
                init: function (editor) {

                    // add the menu to the editor
                    editor.ui.addRichCombo('MailTemplateVariables', {
                        label: lang.INSERT_VARIABLES,
                        title: lang.INSERT_VARIABLES,
                        voiceLabel: lang.INSERT_VARIABLES,
                        className: 'insert_template_variable_combo',
                        multiSelect: false,
                        toolbar: 'mailtoolbar',
                        panel: {
                            attributes: { 'aria-label': '' },
                            css: [ editor.config.contentsCss, CKEDITOR.skin.getPath('editor') ],
                            voiceLabel: lang.INSERT_VARIABLES
                        },

                        init: function () {
                            this.add("%BODY%", lang.CHILD_TEMPLATE, lang.CHILD_TEMPLATE)
                            for (var groupName in variables) {
                                this.startGroup(groupName);
                                for (var variable in variables[groupName]) {
                                    var caption = variables[groupName][variable];
                                    this.add(variable, caption, caption);
                                }
                            }
                        },

                        onClick: function (value) {
                            editor.focus();
                            editor.fire('saveSnapshot');
                            editor.insertHtml(value);
                            editor.fire('saveSnapshot');
                        }
                    });
                }
            });
        })();

        /**
         * Transforms [inputField] into a CKEditor with a 'mailer toolbar'
         * @param inputFieldId
         */
        function nc_netshop_mailer_template_editor(inputFieldId) {
            var editorConfig = {
                    {$editor->loadToolbarsConfig('CkeditorPanelFull')}
                    extraPlugins: 'MailTemplateVariables',
                    skin: '{$editor->loadSkinConfig()}',
                    language: '{$editor->getLanguage()}',
                    filebrowserBrowseUrl: '{$editor->getFileManagerPath()}',
                    allowedContent: true,
                    entities: true,
                    autoParagraph: false,
                    fullPage: true,
                    protectedSource: [ /<\?[\s\S]+?\?>/g ],
                };
            editorConfig.toolbarGroups.push({ name: 'mailtoolbar' });
            var ckeditor = CKEDITOR.replace(inputFieldId, editorConfig);
            $nc('#' + inputFieldId).data('ckeditor', ckeditor);
        }

        /**
         * Submits [form] to [previewUrl] into the new window.
         * @param form
         * @param previewUrl
         */
        function nc_netshop_mailer_template_open_preview(form, previewUrl) {
            var formClone = $nc(form).clone();
            formClone.attr('action', previewUrl)
                     .attr('id', '')
                     .attr('target', '_blank')
                     .hide()
                     .appendTo('body');
            // jQuery DOES NOT COPY INPUT VALUES ON CLONE()!
            formClone.find("input,select,textarea").each(function() {
                var formElement = form.find("[name='" + this.name + "']");
                if (formElement.data('ckeditor')) {
                    $nc(this).val(formElement.data('ckeditor').getData());
                }
                else {
                    $nc(this).val(formElement.val());
                }
                if (this.type == 'checkbox') { this.checked = formElement.prop('checked'); }
            });
            formClone.submit();
            formClone.remove();
        }
        </script>
END_OF_FRAGMENT;
        $html = preg_replace("/^\s+/m", "", $html);

        echo $html;
    }

    /**
     * Возвращает поля указанной таблицы ($variable_type) в виде массива,
     * где ключ — псевдопеременная для шаблона писем (вида {site.Domain}),
     * а значение — описание поля
     *
     * @param string $variable_type   'site', 'user', 'item' or class ID
     * @param string|null $variable_name   by defaults equals to $variable_type
     * @return array
     */
    static public function get_variables($variable_type, $variable_name = null) {
        /** @var nc_db $db */
        $db = nc_core('db');

        if (!$variable_name) { $variable_name = $variable_type; }
        $class_ids = array(0);
        if ($variable_type == 'user') {
            $condition = "System_Table_ID = 3";
        }
        elseif ($variable_type == 'site') {
            $condition = "System_Table_ID = 1";
        }
        elseif (is_numeric($variable_type)) {
            $condition = "Class_ID = " . (int)$variable_type;
        }
        else {
            $class_ids = nc_modules('netshop')->get_goods_components_ids();
            $condition = "Class_ID IN (" . join(", ", $class_ids) . ")";
        }

        $rows = $db->get_results("SELECT Field_Name, Description, COUNT(*) AS NumberOfClasses
                                    FROM Field
                                   WHERE Checked = 1 AND $condition
                                   GROUP BY Field_Name, TypeOfData_ID
                                  HAVING NumberOfClasses = " . count($class_ids) . "
                                   ORDER BY Priority", ARRAY_A);

        $variables = array();
        foreach ((array)$rows as $row) {
            if (!$row['Description']) { continue; }
            $variables["{" . $variable_name . "." . $row['Field_Name'] . "}"] = $row['Description'];
        }

        return $variables;

    }

}