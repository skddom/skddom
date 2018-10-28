/**
 * @global {Function} nc_netshop_init_variant_drag
 */
$nc(function() {
    var $ = $nc,
        iframe = $("iframe#mainViewIframe").get(0);

    if (iframe) {
        // Если есть iframe (мы внутри админки) — все действия нужно производить в контексте фрейма
        $ = iframe.contentWindow.$nc;
    }

    var bulk_mode_option_separator = ";";

    // --- Event handlers ------------------------------------------------------
    /**
     * Включить/выключить все товары в таблице.
     * Что именно нужно сделать — передаётся в event.data ('enable' = включить, иначе выключить)
     * @param event
     */
    function toggle_all(event) {
        event.preventDefault();
        var button = $(event.target),
            container = get_container(button);

        if (button.hasClass('nc--disabled')) { return; }
        button.addClass('nc--disabled');

        var parameters = $.extend(
            get_param(container, 'request_parameters'),
            get_message_id_parts(container, true), // второй параметр: переключить основной товар тоже
            { checked: (event.data == 'enable' ? 2 : 1) }
        );

        parent.nc_action_message(get_message_script_url(), 'POST', parameters);
    }

    /**
     * Обработчик для кнопки «Редактировать всё»
     * @param event
     */
    function edit_all(event) {
        event.preventDefault();

        var container = get_container(event.target);
        var parameters = $.extend(
            get_param(container, 'request_parameters'),
            {
                nc_ctpl: get_param(container, 'edit_template_id'),
                isModal: 1
            }
        );

        get_variant_rows(container, true).each(function(index) {  // второй параметр get_variant_rows(): редактировать также и основной товар
            parameters["item_ids[" + index + "]"] = $(this).data('itemId');
        });

        nc.load_dialog(get_index_script_url(), parameters);

    }

    /**
     * Обработчик для кнопки «Удалить всё»
     * @param event
     */
    function delete_all(event) {
        event.preventDefault();

        var container = get_container(event.target);

        var parameters = $.extend(
                get_param(container, 'request_parameters'),
                get_message_id_parts(container),
                { 'delete': 1, posting: 0 }
            );

        var form_html = '';
        for (var p in parameters) {
            form_html += hidden_input(p, parameters[p]);
        }

        $('<form />', {
            action: get_message_script_url(),
            method: 'POST',
            html: form_html
        }).appendTo('body').submit();
    }

    var reorder_requests = {};
    /**
     * Обработчик бросания при перетаскивании строк таблицы вариантов
     * @param {HTMLTableElement} table
     */
    function reorder_table_rows(table) {
        var parent_item_id = get_param(table, 'parent_item_id');
        if (reorder_requests[parent_item_id]) { reorder_requests[parent_item_id].abort(); }

        var container = get_container(table),
            parameters = $.extend(
                get_param(container, 'request_parameters'),
                {
                    posting: 1,
                    multiple_changes: 1,
                    isNaked: 1,
                    nc_token: nc_token,
                    message: -1
                }
            );
        get_variant_rows(container).each(function(index) {
            var id = $(this).data('itemId');
            parameters["nc_multiple_changes[" + id + "][Priority]"] = index + 1;
        });

        reorder_requests[parent_item_id] = $.post(get_message_script_url(), parameters);
    }

    function show_add_multiple_dialog(event) {
        event.preventDefault();
        var container = get_container(event.target);
        var url = get_add_multiple_dialog_script() +
                    '?component_id=' + get_param(container, 'component_id') +
                    '&parent_item_id=' + get_param(container, 'parent_item_id');
        nc.ui.modal_dialog({ url: url, on_show: on_bulk_dialog_show, confirm_close: false }).open();
    }

    // --- Helper functions ----------------------------------------------------
    /**
     * Возвращает параметр, указанный в атрибуте data-parameters у основного
     * div.nc-netshop-variant
     * @param element
     * @param {string} [item]
     * @returns {*}
     */
    function get_param(element, item) {
        var params = get_container(element).data('parameters') || {};
        if (item && item in params) { return params[item]; }
        return params;
    }

    /**
     * Возвращает все строки с вариантами товара (если второй параметр true —
     * включая строку основного товара, если таковая имеется)
     * @param some_element_inside_div
     * @param {bool} [include_parent]
     * @returns {jQuery}
     */
    function get_variant_rows(some_element_inside_div, include_parent) {
        return get_container(some_element_inside_div)
                    .find('tr[data-item-id]' + (include_parent ? '' : ':not(.nc-netshop-variant-parent)'));
    }

    /**
     * @returns {string}
     */
    function get_message_script_url() {
        return NETCAT_PATH + 'message.php?';
    }

    /**
     * @returns {string}
     */
    function get_index_script_url() {
        return NETCAT_PATH + 'index.php?';
    }

    /**
     * @returns {string}
     */
    function get_add_multiple_dialog_script() {
        return NETCAT_PATH + 'modules/netshop/admin/item/create_bulk_dialog.php';
    }

    /**
     * Возвращает «основной» контейнер элементов управления вариантами (div.nc-netshop-variant)
     * @param some_element_inside_div
     * @returns {jQuery}
     */
    function get_container(some_element_inside_div) {
        return $(some_element_inside_div).closest('.nc-netshop-variant');
    }


    /**
     * Возвращает объект для передачи NetCat’у идентификаторов строк в таблице
     * вариантов (в виде {'message[ID]': ID, ...}) для групповых действий над ними
     * @param some_element_inside_div
     * @param {bool} [include_parent]
     * @returns {Object}
     */
    function get_message_id_parts(some_element_inside_div, include_parent) {
        var result = {};
        // add "message[X]=X" for each variant item in the table
        get_variant_rows(some_element_inside_div, include_parent).each(function() {
            var id = $(this).data('itemId');
            result["message[" + id + "]"] = id;
        });
        return result;
    }

    /**
     * Создаёт HTML-код hidden-поля
     * @param name
     * @param value
     * @returns {string}
     */
    function hidden_input(name, value) {
        return '<input type="hidden" name="' + name.toString().replace('"', '&quot;') +
               '" value="' + value.toString().replace('"', '&quot;') + '" />\n';
    }

    /**
     * @global {Function} nc_netshop_init_variant_drag
     */
    var init_variant_drag = window.nc_netshop_init_variant_drag = function nc_netshop_init_variant_drag() {
        $('.nc-netshop-variant-table').tableDnD({
            onDrop: reorder_table_rows,
            dragHandle: '.nc-netshop-variant-drag',
            onDragClass: 'nc--dragged'
        });
    };

    // --- Bulk variant creation dialog ----------------------------------------
    /**
     * Инициализация обработчиков событий при открытии диалога добавления
     * нескольких вариантов товара
     * @param dialog
     */
    function on_bulk_dialog_show(dialog) {
        dialog.find('.nc-netshop-variant-multiple-field-select').change(on_bulk_dialog_field_add);
        dialog.get_part('footer').find('button[data-role=submit]').click(on_bulk_dialog_submit);
        dialog.find('.nc-netshop-variant-article input[name=fill_article_field]').change(on_bulk_dialog_article_toggle)
    }

    /**
     * Обработка взаимодействия с выпадающим списком полей
     */
    function on_bulk_dialog_field_add() {
        var select = window.top.$nc(this),
            option = select.find('option:selected'),
            table = select.closest('table'),
            tbody = table.find('tbody').eq(0),
            input_name = "options[" + option.val() + "][]",
            input_selector = "[name='" + input_name +"']";

        if (!tbody.find(input_selector).length) {
            var new_row = bulk_dialog_create_field_row(option, input_name);
            new_row.hide().find(input_selector).on('change keyup', function() {
                bulk_dialog_update_count(table);
            });

            tbody.append(new_row);
            new_row.fadeIn();

            table.tableDnD({ dragHandle: '.nc--move', onDrop: $.noop });

            bulk_dialog_update_submit_button(table);
        }

        tbody.find(input_selector).focus();
        select.val('');
    }

    /**
     * Создаёт строку в таблице для выбранного в выпадающем списке поля
     * @param option
     * @param input_name
     * @returns {}
     */
    function bulk_dialog_create_field_row(option, input_name) {
        var option_select_values = option.data('selectValues'),

            row = '<tr class="nc-netshop-variant-multiple-field-row">' +
                  '<td class="nc--compact"><i class="nc-icon nc--move"></i></td>' +
                  '<td class="nc-netshop-variant-multiple-field-row-name">' + option.text() + '</td>' +
                  '<td>';

        if (option_select_values) {
            var select_size = Math.min((Object.keys ? Object.keys(option_select_values).length : 4), 4);
            row += '<select name="' + input_name + '" multiple size="' + select_size + '">';
            for (var i in option_select_values) {
                row += '<option value="' + i + '">' + option_select_values[i] + '</option>';
            }
            row += '</select>';
        }
        else {
            row += '<input type="text" name="' + input_name + '">';
        }

        row += '</td>' +
               '<td class="nc--compact"><i class="nc-icon nc--remove"></i></td>' +
               '</tr>';

        row = $(row);
        row.find('.nc--remove').click(function() {
            var $this = $(this), table = $this.closest('table');
            $this.closest('tr').remove();
            bulk_dialog_update_submit_button(table);
            bulk_dialog_update_count(table);
        });

        return row;
    }

    /**
     * Обновляет счётчик вариантов и состояние кнопки «Создать» при изменении
     * значений полей значений
     * @param table
     */
    function bulk_dialog_update_count(table) {
        var fields = table.find('tbody').eq(0).children('tr');

        // считаем, сколько должно получиться вариантов
        var num_variants = 0;
        fields.find('select, input').each(function() {
            var field_value = $(this).val();
            if (!$.isArray(field_value)) {
                field_value = $.trim(field_value);
                if (!field_value.length) { return; }
                field_value = field_value.split(bulk_mode_option_separator);
            }
            if (!field_value.length) { return; }
            if (num_variants == 0) { num_variants = field_value.length; }
                              else { num_variants *= field_value.length; }
        });

        // включить или выключить кнопку «сохранить»
        var dialog_footer = nc.ui.modal_dialog.get_opened_dialog().get_part('footer'),
            submit_button = dialog_footer.find('button[data-role=submit]');

        if (num_variants == 0) {
            submit_button.addClass('nc--disabled');
        }
        else {
            submit_button.removeClass('nc--disabled');
        }

        dialog_footer.find('.nc-netshop-variant-multiple-count span').text(num_variants);
    }

    /**
     * Показывает или прячет иконку перетаскивания в зависимости от количества полей
     * @param table
     */
    function bulk_dialog_update_submit_button(table) {
        var fields = table.find('tbody').eq(0).children('tr');

        var single_option_class = 'nc-netshop-variant-multiple-field-table-no-drag';
        if (fields.length > 1) {
            table.removeClass(single_option_class);
        }
        else {
            table.addClass(single_option_class);
        }
    }

    /**
     * Обработчик нажатия на кнопку «Создать» в диалоге множественного добавления
     * вариантов
     */
    function on_bulk_dialog_submit() {
        var button = $(this);
        if (button.hasClass('nc--disabled')) { return; }

        button.addClass('nc--loading');

        var dialog = nc.ui.modal_dialog.get_opened_dialog(),
            form = dialog.find('.nc-netshop-variant-multiple-field-form'),
            cc = form.find('input[name=infoblock_id]').val();

        form.ajaxSubmit({
            success: function() {
                $.ajax({
                    'type' : 'GET',
                    'url': nc_page_url() + '&isNaked=1&admin_modal=1&cc_only=' + cc,
                    success: function(response) {
                        nc_update_admin_mode_content(response, null, cc);
                        dialog.close();
                    }
                });
            },
            error: function(response) {
                alert('Error!\n' + response);
                dialog.close();
            }
        });
    }

    function on_bulk_dialog_article_toggle() {
        $('.nc-netshop-variant-article-details').slideToggle(300);
    }

    // -------------------------------------------------------------------------
    // attach event handlers
    $('body')
        .on('click', '.nc-netshop-variant-enable-all', 'enable', toggle_all)
        .on('click', '.nc-netshop-variant-disable-all', 'disable', toggle_all)
        .on('click', '.nc-netshop-variant-edit-all', edit_all)
        .on('click', '.nc-netshop-variant-delete-all', delete_all)
        .on('click', '.nc-netshop-variant-add-multiple', show_add_multiple_dialog)
    ;

    init_variant_drag();

});