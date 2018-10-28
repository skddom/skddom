/**
 * Отправка событий NetCat в Яндекс.Метрику, Google Analytics
 *
 * 1) Слушает события, отправляет статистику для элементов, которые имеют атрибуты:
 *    data-analytics-click-category — категория события GA / цель ЯМ — атрибут обязателен для срабатывания события
 *    data-analytics-click-label — ярлык события GA / дополнительный параметр label для ЯМ
 *    data-analytics-click-action — действие события GA / дополнительный параметр action для ЯМ.
 *          По умолчанию равны типу события, например, 'click'.
 *
 *    Значения category, label могут содержать несколько категорий и ярлыков
 *    соответственно, перечисленных через запятую. При наличии нескольких значений
 *    будут сформированы все возможные варианты событий.
 *
 *    Типы событий:
 *       click
 *       submit
 *
 * 2) Отправляет события E-Commerce через объект dataLayer.
 *    События передаются из NetCat автоматически через куки.
 *
 *
 * Поддержка IE: 9+
 * Часть функциональности не поддерживается в до IE 11 (наблюдения за изменениями DOM —
 * нужно, например, если на странице динамически добавляются формы)
 *
 * При использовании Google Tag Manager переменная контейнера данных должна называться
 * dataLayer.
 * Для передачи событий в аналитику через Google Tag Manager:
 * 1) Нужно добавить пользовательские переменные уровня данных:
 *    gaEventCategory
 *    gaEventAction
 *    gaEventLabel
 * 2) Должен быть настроен тег:
 *    Продукт: Google Analytics
 *    Тип тега: Universal Analytics
 *    Настройка тега:
 *     — необходимо указать идентификатор отслеживания Google Analytics
 *     — тип отслеживания: событие
 *     — категория: {{gaEventCategory}}
 *     — действие: {{gaEventAction}}
 *     — ярлык: {{gaEventLabel}}
 *    Условия активации: новый триггер
 *     — событие: пользовательское событие
 *     — имя события: gaEvent
 *
 */

(function(window, document) {
    if (!document.addEventListener) {
        return; // IE8 and below is not supported
    }

    var yandexMetrikaObject;
    function getYandexMetrikaObject() {
        if (!yandexMetrikaObject) {
            for (var i in window) {
                if (/yaCounter\d+/.test(i) && typeof window[i] == 'object') {
                    yandexMetrikaObject = window[i];
                    break;
                }
            }
        }
        return yandexMetrikaObject;
    }

    function getGoogleAnalyticsObject() {
        var ga = window.GoogleAnalyticsObject ? window[GoogleAnalyticsObject] : null;
        return ga && ga.loaded ? ga : null;
    }

    var valueSplitRegexp = /\s*,\s*/;
    var gtmDataLayer = 'dataLayer';

    // -------------------------------------------------------------------------

    /**
     * Отправка события в Google Analytics и Яндекс.Метрику.
     * Доступна как глобальная функция nc_stats_analytics_event().
     *
     * @param {String} eventCategories   Категории события (можно несколько через запятую)
     * @param {String} eventAction       Действие события, например, 'click'
     * @param {String} eventLabels       Ярлыки события (можно несколько через запятую)
     */
    var sendEvent = nc_stats_analytics_event = function(eventCategories, eventAction, eventLabels) {
        eventCategories = eventCategories.split(valueSplitRegexp);
        eventLabels = eventLabels && eventLabels.length ? eventLabels.split(valueSplitRegexp) : [undefined];

        var googleAnalytics = getGoogleAnalyticsObject(); // есть analytics.js
        var yandexMetrika = getYandexMetrikaObject(); // есть Яндекс.Метрика

        eventCategories.forEach(function(eventCategory) {
            eventLabels.forEach(function(eventLabel) {
                // Google Tag Manager
                if (window[gtmDataLayer]) {
                    window[gtmDataLayer].push({ event: 'gaEvent', gaEventCategory: eventCategory, gaEventAction: eventAction, gaEventLabel: eventLabel });
                }

                // Google Analytics
                if (googleAnalytics) { // analytics.js
                    googleAnalytics('send', 'event', eventCategory, eventAction, eventLabel);
                }
                else if (window._gaq) { // ga.js
                    _gaq.push(['_trackEvent', eventCategory, eventAction, eventLabel]);
                }

                // Яндекс.Метрика
                if (yandexMetrika) {
                    yandexMetrika.reachGoal(eventCategory, {label: eventLabel, action: eventAction});
                }
            })
        });
    };

    // -------------------------------------------------------------------------

    function getDataAttribute(target, eventType, attributeType) {
        return target.getAttribute('data-analytics-' + eventType + '-' + attributeType);
    }

    // -------------------------------------------------------------------------

    /**
     * Слушатель событий для элементов с атрибутами data-analytics-*
     * @param event
     */
    function analyticsEventListener(event) {
        var target = event.target,
            eventType = event.type,
            checkParentElements = eventType == 'click';

        do {
            var eventCategories = getDataAttribute(target, eventType, 'category');
            if (eventCategories) {
                sendEvent(
                    eventCategories,
                    getDataAttribute(target, eventType, 'action') || eventType,
                    getDataAttribute(target, eventType, 'label')
                );
            }
        } while (checkParentElements && (target = target.parentNode) && target.getAttribute);
    }

    document.addEventListener('click', analyticsEventListener, true);

    // -------------------------------------------------------------------------

    /**
     * Добавление слушателей событий для отправки форм
     */
    var prevFormCount = 0; // dummy way to determine if there's a need to attach submit event listener
    function initFormsListeners() {
        var forms = document.forms,
            formCount = forms.length;

        if (formCount == prevFormCount) { return; }

        for (var i = 0; i < formCount; i++) {
            // no need to remove existing listeners: https://developer.mozilla.org/en-US/docs/Web/API/EventTarget/addEventListener#Multiple_identical_event_listeners
            forms[i].addEventListener('submit', analyticsEventListener, true);
        }
        prevFormCount = formCount;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // инициализация с небольшой задержкой, чтобы не замедлять первоначальную отрисовку из-за MutationObserver
        setTimeout(initAfterDomLoaded, 1000);
    });

    // -------------------------------------------------------------------------

    function initAfterDomLoaded() {
        // Добавление слушателя событий для отправки форм
        initFormsListeners();

        /**
         * Наблюдение за изменениями DOM: добавление форм, проверка изменения кук с информацией
         * о действиях с товарами
         */
        if (window.MutationObserver) {
            new MutationObserver(function(mutations) {
                initFormsListeners();
            }).observe(document, { childList: true, subtree: true });
        }

    }

    // -------------------------------------------------------------------------


})(window, document);