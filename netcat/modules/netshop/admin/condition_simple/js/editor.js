/**
 * «Заглушка» вместо полноценного редактора условий ()
 */

/**
 * @constructor
 * @param {Object} options
 *      container: selector for the condition editor container
 *      input_name: name of the hidden input field (will be created)
 *      conditions: conditions to set
 *      site_id: ID of the current site
 *      mode:
 *        - 'cart_totalprice' for cart sum 'from .. to ..' condition
 *        if mode is not set, outputs '...not available...' message
 */
function nc_netshop_condition_editor(options) {
    this.init(options);
}

(function($) {
    var Class = nc_netshop_condition_editor,
        Instance = Class.prototype,
        lang = nc_netshop_condition_messages,
        filesPath = Class.filesPath = NETCAT_PATH + 'modules/netshop/admin/condition_simple/';

    // ****** PROPERTIES ******
    Instance.siteId = null;
    Instance.root = null;
    Instance.mode = null;
    Instance.inputField = null;
    Instance.isInitializing = false;
    Instance.inputField = null;

    // ****** METHODS ******
    /**
     * Initialization:
     */
    Instance.init = function(options) {
        this.loadStylesIn(window);

        this.siteId = options.site_id;
        this.root = $(options.container);
        this.mode = options.mode;

        if (options.mode == 'cart_totalprice') {
            this.createInputField(options);
            this.showCartSumEditor();
        }
        else {
            this.showNotification();
        }
    };

    /**
     *
     */
    Instance.showNotification = function() {
        this.root.append(
            '<div class="nc-alert"><i class="nc-icon-l nc--status-info"></i>' +
            lang.NOTICE +
            '</div>'
        );
    };

    /**
     * @param {window} targetWindow    (window  or  window.top)
     */
    Instance.loadStylesIn = function(targetWindow) {
        var id = "nc_condition_editor_stylesheet";
        if (targetWindow.$nc("#" + id).length == 0) {
            targetWindow.$nc("head").append(
                '<link rel="stylesheet" id="' + id + '" type="text/css" href="' + filesPath + 'css/editor.css">'
            );
        }
    };

    /**
     * @param options
     */
    Instance.createInputField = function(options) {
        this.inputField = $("<input type='hidden' name='" + options.input_name + "'/>")
                          .appendTo(this.root);
        var data = options.conditions;
        if (data && !$.isEmptyObject(data)) { this.inputField.val(JSON.stringify(data)); }
    };

    /**
     *
     */
    Instance.showCartSumEditor = function() {
        var sumFrom = '',
            sumTo = '',
            input = this.inputField.val() || "{}",
            conditionTree = (JSON && JSON.parse) ? JSON.parse(input) : eval("(" + input + ")");

        if (conditionTree && conditionTree.type == 'and' && $.isArray(conditionTree.conditions)) {
            var c = conditionTree.conditions;
            if (c[0] && c[0].type == 'cart_totalprice' && c[0].op == 'ge') {
                sumFrom = c[0].value;
            }
            if (c[1] && c[1].type == 'cart_totalprice' && c[1].op == 'le') {
                sumTo = c[1].value;
            }
        }

        var html = "<div class='condition'>" +

                    "<span class='condition-string'>" + lang.CART_TOTALPRICE_FROM + "</span>" +
                    "<span class='condition-param'>" +
                    "<input class='condition-param-value' type='number' name='cart_totalprice_from' autocomplete='off' value='" + sumFrom + "'>" +
                    "</span>" +

                    "<span class='condition-string'>" + lang.CART_TOTALPRICE_TO + "</span>" +
                    "<span class='condition-param'>" +
                    "<input class='condition-param-value' type='number' name='cart_totalprice_to' autocomplete='off' value='" + sumTo + "'>" +
                    "</span>" +

                    "</div>";

        this.root.addClass('nc-netshop-condition-editor nc-netshop-condition-editor-simple').append(html);
    };

    /**
     *
     */
    Instance.save = function() {
        if (this.mode == 'cart_totalprice') {
            var sumFrom = this.root.find("input[name=cart_totalprice_from]").val(),
                sumTo = this.root.find("input[name=cart_totalprice_to]").val();

            if (sumFrom || sumTo) {
                var conditions = [];
                conditions[0] = { type: "cart_totalprice", op: "ge", value: sumFrom || 0 };
                if (sumTo) {
                    conditions[1] = { type: "cart_totalprice", op: "le", value: sumTo };
                }
                var result = { type: "and", conditions: conditions };
                this.inputField.val(JSON.stringify(result));
                return result;
            }
        }
    };

    /**
     *
     */
    Instance.onFormSubmit = function() {
        if (this.mode == 'cart_totalprice') {
            this.save();
            return true;
        }
    };

})($nc);