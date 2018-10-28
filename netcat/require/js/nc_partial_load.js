// IE9+

(
/**
 * Параметры, передаваемые внутрь замыкания для улучшения сжатия (~ +10%):
 * @param {Window} window
 * @param {Document} document
 * @param {Storage} sessionStorage
 * @param {Function} encodeURIComponent
 * @param {String} customEventString
 * @param {Console} console
 * @param {undefined} [undefined]
 */
function(window, document, sessionStorage, encodeURIComponent, customEventString, console, undefined) {

    /**
     * Загружает с сервера врезку макета (partial).
     *
     * @param {String|Array} [partialConditions] правила выбора врезок для обновления.
     *      Если правила не указаны (undefined, null, false, пустая строка),
     *      то будут обновлены все врезки на странице.
     *
     *      Все врезки, удовлетворяющие условиям, будут получены с сервера одним запросом.
     *
     *      Правила выбора врезок могут быть указаны:
     *      — в виде строки с одним ключевым словом врезки, например: 'footer'
     *        (будут обновлены все врезки с ключевым словом footer, независимо от
     *        наличия или отсутствия у них дополнительных параметров data)
     *      — в виде строки с несколькими ключевыми словами через пробел или запятую, например: 'header footer'
     *      — в виде массива, где каждый элемент также представляет из себя массив:
     *        1) первый элемент — ключевое слово врезки (или несколько через пробел или запятую)
     *        2) второй элемент (опционально) — параметры для выборки по дополнительным параметрам (data).
     *           Если значение — null, то будут обновлены только врезки без параметров.
     *           Если передан объект со значениями, будут обновлены врезки, у которых параметры
     *           имеют соответствующие значения. При выборке по значению параметров не поддерживаются
     *           многомерные массивы.
     *        Например:
     *        [
     *          'header',
     *          ['footer', { show_menu: true, page_type: 'front' }],
     *          ['aside, null]
     *        ]
     *        Будут обновлены:
     *        1) врезки 'header' c любыми параметрами или без них
     *        2) врезки 'footer', у которых одновременно есть параметры show_menu = true и page_type = 'front'
     *        3) врезки 'aside' без параметров
     *
     * @param {Function} [successCallback]  обработчик, который будет выполнен
     *      при успешном выполнении запроса (аргумент: полученные в виде JSON данные)
     * @param {Function} [failureCallback]  обработчик, который будет выполнен
     *      при ошибке (аргумент: объект XMLHttpRequest)
     */
    window.nc_partial_load = function(partialConditions, successCallback, failureCallback) {
        reloadPartials(getPartials(partialConditions), successCallback || noOp, failureCallback || noOp);
    };

    /**
     * Удаляет сохранённые в sessionStorage врезки макетов
     */
    window.nc_partial_clear_cache = function() {
        for (var key in sessionStorage) {
            if (sessionStorage.hasOwnProperty(key) && !key.indexOf(sessionStoragePrefix)) { // == 0
                sessionStorage.removeItem(key);
            }
        }
    };

    /**
     * Загрузка врезок при готовности страницы
     */
    document.addEventListener('DOMContentLoaded', function() {
        var partialsToLoad = [], restoredPartials = {}, hasRestoredPartials;
        forEach(getPartials(), function(partial) {
            var param = partial.param, restoredHTML, shouldLoadDeferred;

            if (param.defer) {
                shouldLoadDeferred = true;
                if (param.store && (restoredHTML = sessionStorage.getItem(partial.cacheKey)) !== null) {
                    replacePartialContent(partial, restoredHTML);
                    restoredPartials[partial.key] = restoredHTML;
                    hasRestoredPartials = true;
                    shouldLoadDeferred = false;
                }
            }

            if (shouldLoadDeferred || param.reload) {
                partialsToLoad.push(partial);
            }
        });

        if (hasRestoredPartials) {
            dispatchTemplatePartialUpdateEvent(restoredPartials);
        }

        reloadPartials(partialsToLoad, noOp, noOp);
    });

    /**
     * Событие document.ncPartialUpdate — завершена загрузка врезок.
     * Вызывается после добавления врезок в DOM.
     * В event.detail.newTemplateContent будет доступен объект с ключевыми словами
     * врезок в качестве ключа и содержимым соответствующих врезок в качестве значения.
     * (В jQuery-событии — event.originalEvent.detail.newTemplateContent)
     *
     * @param newTemplateContent
     */
    function dispatchTemplatePartialUpdateEvent(newTemplateContent) {
        dispatchEvent('ncPartialUpdate', { newTemplateContent: newTemplateContent });
    }


    var partialStartRegExp = /nc_template_partial (\S+) ({.+})/;
    var sessionStoragePrefix = 'nc_partial_';

    // --- Функции для получения (выборки) массивов с объектами с информацией о врезках ---

    /**
     *
     * @param {Array} [partialConditions]
     * @returns {Object[]}
     */
    function getPartials(partialConditions) {
        var partials = [],
            // NodeIterator доступен в IE9+. Четвёртый аргумент нужен для IE.
            nodeIterator = document.createNodeIterator(document.body, NodeFilter.SHOW_COMMENT, null, false),
            commentNode,
            commentNodeParts,
            partial,
            partialParam,
            partialKey;

        if (partialConditions) {
            partialConditions = normalizePartialConditions(partialConditions);
        }
        
        while (commentNode = nodeIterator.nextNode()) {
            commentNodeParts = commentNode.nodeValue.match(partialStartRegExp);
            if (commentNodeParts) {
                try {
                    partialParam = JSON.parse(commentNodeParts[2]);
                    partialKey = partialParam.partial + makeUrlQueryString(partialParam.data || {});
                    partial = {
                        // начальный узел
                        start: commentNode,
                        // идентификатор комментария в разметке страницы
                        id: commentNodeParts[1],
                        // параметры врезки из разметки страницы
                        param: partialParam,
                        // ключевое слово врезки + data в виде query-строки (partial?param1=1&param2=2)
                        key: partialKey,
                        // ключ для sessionStorage
                        cacheKey: sessionStoragePrefix + partialParam.template + '_' + partialKey
                    };

                    getPartialNodes(partial); // проверяем, есть ли конец врезки (бросит ошибку, если его нет)

                    if (!partialConditions || partialMatchesConditions(partial, partialConditions)) {
                        partials.push(partial);
                    }
                } catch (e) {
                    console && console.log('Partial error:', e.message, commentNode);
                }
            }
        }

        return partials;
    }

    /**
     *
     * @param {Array|String} conditions
     * @return {Array[]}
     *   [
     *      [ ['keyword1', 'keyword2'], data ],
     *      ...
     *   ]
     */
    function normalizePartialConditions(conditions) {
        if (isString(conditions)) {
            // 'keyword' → [['keyword', undefined]]
            conditions = [[conditions]];
        }

        forEach(conditions, function(condition, n) {
            if (isString(condition)) {
                // ['keyword'] → [['keyword', undefined]]
                conditions[n] = condition = [ condition ];
            }
            // [['keyword1 keyword2', data]] → [[['keyword1', 'keyword2], data]]
            condition[0] = condition[0].split(/\W+/); // ключевые слова могут содержать только \w
        });

        return conditions;
    }

    // --- функции для работы с объектами врезок ---
    // (если сделать через Partial.prototype, сжатая версия будет немного [~10%] больше)

    /**
     *
     * @param {Object} partial
     * @returns {Node[]}
     */
    function getPartialNodes(partial) {
        var nodes = [],
            sibling = partial.start;

        while (sibling = sibling.nextSibling) {
            if (sibling.nodeType == 8 && sibling.nodeValue.indexOf('/nc_template_partial ' + partial.id) != -1) {
                return nodes;
            }
            nodes.push(sibling);
        }

        throw new Error('No end comment');
    }

    /**
     *
     * @param {Object} partial
     * @param {String} newHTML
     */
    function replacePartialContent(partial, newHTML) {
        var parent = partial.start.parentNode;

        // remove old
        forEach(getPartialNodes(partial), function(node) {
            parent.removeChild(node);
        });

        // add new
        var tmpElement = parent.insertBefore(document.createElement('DIV'), partial.start.nextSibling);
        tmpElement.insertAdjacentHTML('afterend', newHTML);
        parent.removeChild(tmpElement);

        // scripts are not executed when added this way...
        evaluateScripts(getPartialNodes(partial));

        // cache
        if (partial.param.store) {
            sessionStorage.setItem(partial.cacheKey, newHTML);
        }
    }

    /**
     *
     * @param {Object} partial
     * @param {Array} conditions
     * @returns {boolean}
     */
    function partialMatchesConditions(partial, conditions) {
        var hasMatch = false,
            param = partial.param,
            partialData = param.data;

        forEach(conditions, function(condition) {
            var dataCondition = condition[1],
                conditionMatch =
                    condition[0].indexOf(param.partial) != -1 &&
                    (
                        dataCondition === undefined ||
                        (dataCondition === null && partialData === null) ||
                        (dataCondition && compareObjectProperties(partialData || {}, dataCondition))
                    );

            if (conditionMatch) {
                hasMatch = true;
                return false; // exit forEach
            }
        });

        return hasMatch;
    }

    // --- Функции для массивов объектов с информацией о врезках ---

    /**
     *
     * @param {Object[]} partials
     * @param {Function} successCallback
     * @param {Function} failureCallback
     */
    function reloadPartials(partials, successCallback, failureCallback) {
        if (!partials.length) {
            return;
        }

        var requestData = { partial: [] };
        forEach(partials, function(partial) {
            requestData.template = partial.param.template;
            var key = partial.key, value = requestData.partial;
            if (value.indexOf(key) == -1) {
                value.push(key);
            }
        });

        requestData.referer = location.toString();
        requestData.json = 1;

        var xhr = new XMLHttpRequest(),
            url = (window.NETCAT_PATH || '/netcat/') + 'partial.php' + makeUrlQueryString(requestData);

        xhr.open('GET', url, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    try {
                        var newContent = JSON.parse(xhr.responseText);
                        updatePartials(partials, newContent);
                        dispatchTemplatePartialUpdateEvent(newContent);
                        successCallback(newContent);
                    } catch (e) {
                        failureCallback(xhr);
                    }
                } else {
                    failureCallback(xhr);
                }
            }
        };
        xhr.send();
    }

    /**
     *
     * @param {Object[]} partials
     * @param {Object} newContent
     */
    function updatePartials(partials, newContent) {
        forEach(partials, function(partial) {
            if (partial.key in newContent) {
                replacePartialContent(partial, newContent[partial.key]);
            }
        });
    }

    // --- Вспомогательные функции ---

    /**
     *
     * @param {Object} inspectedObject
     * @param {Object} referenceValues
     * @returns {boolean} true, если в inspectedObject все свойства из referenceValues
     *      имеют указанные в последнем значения
     */
    function compareObjectProperties(inspectedObject, referenceValues) {
        for (var key in referenceValues) {
            if (inspectedObject[key] != referenceValues[key]) {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * @param {Object} data
     * @param {String} [prefix]
     * @returns {String|Array}
     */
    function makeUrlQueryString(data, prefix) {
        var result = [], key, value;
        for (key in data) {
            value = data[key];
            key = encodeURIComponent(key);
            if (prefix) {
                key = prefix + '[' + key + ']';
            }

            if (value instanceof Array) {
                // сейчас поддерживаются только одномерные массивы с последовательными индексами
                forEach(value, function(v) {
                    result.push(key + '[]=' + encodeURIComponent(v));
                });
            } else if (typeof value == 'object') {
                result = result.concat(makeUrlQueryString(value, key));
            } else {
                // прочие значения расцениваются как скалярные
                result.push(key + '=' + encodeURIComponent(value));
            }
        }

        if (!prefix) {
            result = result.length ? '?' + result.join('&') : '';
        }

        return result;
    }

    /**
     * Инициирует кастомное событие
     *
     * @param {String} name
     * @param {Object} detail
     */
    function dispatchEvent(name, detail) {
        var event;
        if (typeof window[customEventString] != 'function') { // IE не поддерживает конструктор new CustomEvent
            event = document.createEvent(customEventString);
            event['init' + customEventString](name, false, false, detail); // event.iniCustomEvent()
        } else { // на MDN написано: «не используйте event.initCustomEvent» (хотя работает)
            event = new window[customEventString](name, { detail: detail });
        }
        document.dispatchEvent(event);
    }

    /**
     *
     * @param nodes
     */
    function evaluateScripts(nodes) {
        forEach(nodes, function(node) {
            if (node.tagName == 'SCRIPT') {
                var head = document.head,
                    script = document.createElement('SCRIPT');
                script.appendChild(document.createTextNode(node.text));
                head.appendChild(script);
                head.removeChild(script);
            } else if (node.childNodes.length) {
                evaluateScripts(node.childNodes);
            }
        });
    }

    /**
     *
     */
    function noOp() {}

    /**
     * (В IE нет NodeList.forEach)
     *
     * @param {Array|NodeList} arrayLikeObject
     * @param {Function} fn
     */
    function forEach(arrayLikeObject, fn) {
        for (var i = 0; i < arrayLikeObject.length; i++) {
            if (fn(arrayLikeObject[i], i) === false) {
                break;
            }
        }
    }

    /**
     *
     * @param value
     * @returns {boolean}
     */
    function isString(value) {
        return typeof value == 'string';
    }


})(window, document, sessionStorage, encodeURIComponent, 'CustomEvent', console);
