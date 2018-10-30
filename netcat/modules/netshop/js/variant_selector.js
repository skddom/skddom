/**
 * Функция, инициализирующая функционал для переключения между вариантами
 * на странице товара.
 *
 * Обратите внимание, что в nc_netshop_item_variant_selector используется
 * минимизированная версия скрипта.
 */
function nc_netshop_init_variant_selector(options) {
    // настройки по умолчанию, могут быть переопределены параметром options
    var default_options = {
            // селектор для обновляемых областей
            updated_regions: ".tpl-variable-part",
            // селектор для выбора элементов, содержащих переключатели вариантов
            selector: "[data-role='variant-selector']",
            // селектор для элемента, откуда будет взят title страницы
            page_title: "title",
            // параметры, добавляемые к xhr-запросу вариантов товара
            request_params: "&isNaked=1",
            // кешировать результаты?
            cache: true,
            // заменять адрес в адресной строке?
            replace_location: true,
            // тело функции-обработчика события «обновлён вариант». Если задана
            // строка, то будет создана функция function(updated_regions) { "ON_UPDATE" }
            // (параметр updated_regions содержит jQuery-объект с обновлёнными
            // областями страницы)
            on_update: $.noop
        },
        settings = $.extend({}, default_options, options),
        event_namespace = ".netshop_variants";

        if (typeof settings.on_update == 'string') {
            settings.on_update = new Function('updated_regions', settings.on_update);
        }

        /**
         * Обновление частей страницы
         * @param {String} html
         */
    var update_page = function(html) {
            var new_content = $('<div/>').append(html),
                old_parts = $(settings.updated_regions),
                new_parts = new_content.find(settings.updated_regions);

            // обновить фрагменты страницы
            old_parts.each(function(index, old_part) {
                $(old_part).replaceWith(new_parts.eq(index));
            });

            // обновить заголовок страницы
            if (settings.page_title) {
                var title = new_content.find(settings.page_title).text();
                if (title.length) { document.title = title; }
            }

            attach_handlers();
            settings.on_update(new_parts);
        },

        /**
         * Загрузка страницы варианта при помощи xhr
         * @param variant_url
         * @returns promise
         */
        load_variant = function(variant_url) {
            // Already cached? Return cached value
            if (nc_netshop_variant_cache[variant_url]) {
                return $.Deferred().resolveWith(window, [nc_netshop_variant_cache[variant_url]]).promise();
            }

            // Execute a GET xhr request
            var request_url = variant_url +
                              (variant_url.indexOf("?") == -1 ? "?" : "&") +
                              "request_type=get_variant";
            if (settings.request_params) { request_url += settings.request_params; }

            var request = $.get(request_url);

            if (settings.cache) {
                request.done(function(response) { nc_netshop_variant_cache[variant_url] = response; });
            }

            return request;
        },

        /**
         * Обработчик событий для элементов выбора вариантов товара
         * @param event
         */
        handler = function(event) {
            var $this = $(this),
                url = $this.attr('href') || // <a>
                      $this.find('option:selected').attr('value') || // <select>; sic, not .val(), not .prop()
                      $this.val(); // <input type='radio'>

            if (url) {
                if (settings.replace_location && window.history.pushState) {
                    window.history.replaceState(null, null, url);
                }
                load_variant(url).done(update_page);
            }

            event.preventDefault();
        },

        // INIT EVENT HANDLERS
        attach_handlers = function() {
            // <select data-role='variant-selector'>
            // <div class='variant-selector'> ... <select>
            // <div data-role='variant-selector'> ... <input type='radio' value='url'>
            $("select" + settings.selector + ", *" + settings.selector + " select, *" + settings.selector + " input:radio")
                .off(event_namespace)
                .on("change" + event_namespace, handler);

            // <div data-role='variant-selector'> ... <a>
            $("*" + settings.selector + " a")
                .off(event_namespace)
                .on("click" + event_namespace, handler);
        };

    attach_handlers();

}

/**
 * Переменная для хранения кэша вариантов.
 * Сделана глобальной для того, чтобы кэш не сбрасывался в тех случаях, когда
 * скрипт инициализации селекторов (по ошибке) находится в изменяемой области.
 *
 * @type {Object}
 */
if (!window.nc_netshop_variant_cache) {
    var nc_netshop_variant_cache = {};
}
