// Resize modal on window.resize
//
// Если у элемента, из которого был создан modal, в качестве data-свойства onResize
// установлена функция [.data('onResize', someFunction)], она будет выполнена при
// событии resize
function nc_register_modal_resize_handler() {
    if ( ! $nc._resize_modal_event) {
        $nc(window).resize(function(){
            var modal = $nc('#simplemodal-container').not(".simplemodal-container-fixed-size");
            if (modal.length !== 0 && !modal.find('.nc-modal-dialog-body').length) {
                var w = $nc(window).width() - 100 * 2;
                var h = $nc(window).height() - 100 * 2;
                w = w > 1200 ? 1200 : (w < 600 ? 600 : w);

                modal.css({width: w, height: h});

                var modalResizeHandler = modal.find(".simplemodal-data").data("onResize");
                if (modalResizeHandler && typeof modalResizeHandler == "function") {
                    modalResizeHandler(modal);
                }
            }
        });

        $nc._resize_modal_event = true;
    }
}


function nc_save_editor_values() {
    // в случае удаления nc_form() перенести эту функцию в nc.ui.modal_dialog (?)

    if (typeof CKEDITOR != 'undefined' && CKEDITOR.instances) {
        for (var instance_name in CKEDITOR.instances) {
            var editor = CKEDITOR.instances[instance_name],
                $textarea = $nc(editor.element.$),
                value = editor.getData();

            if ($textarea.length) {
                // CKEditor не фильтрует контент, если редактор находится не в режиме
                // WYSIWIG (as of version 4.4.1)
                if (editor.mode != 'wysiwyg' && (!('allowedContent' in editor.config) || editor.config.allowedContent !== true)) {
                    var fragment = CKEDITOR.htmlParser.fragment.fromHtml(value),
                        writer = new CKEDITOR.htmlParser.basicWriter();

                    editor.filter.applyTo(fragment);
                    fragment.writeHtml(writer);
                    value = writer.getHtml();
                }
                $textarea.val(value);
            }
        }
    }

    if (window.FCKeditorAPI) {
        for (fckeditorName in FCKeditorAPI.Instances) {
            var editor = FCKeditorAPI.GetInstance(fckeditorName);
            if ( editor.IsDirty() ) {
                $nc('#' + fckeditorName).val( editor.GetHTML() );
            }
        }
    }

    CMSaveAll();
}


function nc_form(url, backurl, target, modalWindowSize, httpMethod, httpData) {
    var path_re = new RegExp("^\\w+://[^/]+" + NETCAT_PATH + "(add|message)\\.php");
    if (path_re.test(url)) {
        return nc.load_dialog(url);
    }

    if ( ! target && window.event) {
        target = window.event.target || window.event.srcElement;
    }

    if (!modalWindowSize) {
        modalWindowSize = null;
    }

    nc_register_modal_resize_handler();

    var $target = target ? $nc(target) : false;
    if ($target) {
        if ($target.hasClass('nc--disabled')) {
            return;
        }
        $target.addClass('nc--disabled');
    }

    if (!backurl) backurl = '';


    nc.process_start('nc_form()');



    if (!httpMethod) {
        httpMethod = 'GET';
    }

    if (!httpData) {
        httpData = {};
    }

    $nc.ajax({
        'type' : httpMethod,
        'url': url + '&isNaked=1',
        'data': httpData,
        'success' : function(response) {

            nc.process_stop('nc_form()');
            if ($target) $target.removeClass('nc--disabled');

            nc_remove_content_for_modal();
            $nc('body').append('<div style="display: none;" id="nc_form_result"></div>');
            $nc('#nc_form_result').html(response).modal({
                position: [120, null],
                onShow: function (dialog) {
                    $nc('#nc_form_result').children().not('.nc_admin_form_menu, .nc_admin_form_body, .nc_admin_form_buttons').hide();

                    var container = dialog.container;

                    if (modalWindowSize) {
                          var currentLeft = parseInt(container.css('left'));
                          var currentWidth = container.width();

                          var currentTop = parseInt(container.css('top'));
                          var currentHeight = container.height();

                          container.css({
                                  width: modalWindowSize.width,
                                  height: modalWindowSize.height,
                                  left: currentLeft + (currentWidth - modalWindowSize.width) / 2,
                                  top: currentTop + (currentHeight - modalWindowSize.height) / 2
                          }).addClass('simplemodal-container-fixed-size');
                    }
                    else {
                        container.removeClass('simplemodal-container-fixed-size');
                        $nc(window).resize();
                    }

                    $nc('#nc_form_result #adminForm').append("<input type='hidden' name='nc_token' value='" + nc_token + "' />");
                },
                closeHTML: "<a class='modalCloseImg'></a>",
                onClose: function (e) {
                    if (typeof CKEDITOR != 'undefined' && CKEDITOR.instances) {
                        for (var instance_name in CKEDITOR.instances) {
                            if (!/_edit_inline$/i.test(instance_name)) {
                                CKEDITOR.instances[instance_name].destroy();
                            } else {
                                var $element = $nc('#' + instance_name);
                                var oldValue = $element.attr('data-oldvalue');
                                $element.html(oldValue);
                            }
                        }
                    }
                    $nc.modal.close();
                    if (typeof nc_autosave_use !== "undefined" && nc_autosave_use == 1 && autosave !== null && typeof autosave !== "undefined" && autosave.timeout != 0) {
                        autosave.stopTimer();
                    }
                    $nc(document).unbind('keydown.simplemodal');
                    nc_remove_content_for_modal();
                }
            });

        $nc('#nc_form_result #adminForm').ajaxForm({
            beforeSerialize: nc_save_editor_values,

            // modal layer button submit
            success: function(response, status, event, form) {

                nc.process_stop('nc_form()');
                var error = nc_check_error(response);
                if (error) {
                    var $form_buttons = $nc('.nc_admin_form_buttons');
                    $form_buttons.append(
                        "<div id='nc_modal_error' class='nc-alert nc--red' style='position:absolute; z-index:3000; width:"+($form_buttons.width()-55)+"px; bottom:70px; text-align:left; line-height:20px '>"
                        + "<div class='simplemodal_error_close'></div>"
                        + "<i class='nc-icon-l nc--status-error'></i>"
                        + error
                        + "</div>");
                    $nc('.simplemodal_error_close').click(function(){
                        $nc('#nc_modal_error').remove();
                    });
                    return false;
                }

                // if (response == 'OK') {
                //     window.location.reload(true);
                //     return false;
                // }

                var cc = form.find('input[name=cc]').val();

                var loc = window.location,
                    newUrlMatch = (/^NewHiddenURL=(.+?)$/m).exec(response), // в ответе есть строка "NewHiddenUrl=something"
                    newUrl = newUrlMatch ? $nc.trim(newUrlMatch[1]) : null; // новый HiddenURL страницы

                if ((/^ReloadPage=1$/m).test(response)) { // в ответе есть строка "ReloadPage=1"
                    // не режим "редактирование", изменился путь страницы
                    if (newUrl && !(/\.php/.test(window.location.pathname))) {
                    // сохранить имя страницы, если оно было (изменение свойств раздела со страницы объекта)
                    var pageNameMatch = /\/([^\/]+)$/.exec(loc.pathname);
                    if (pageNameMatch) { newUrl += pageNameMatch[1]; }
                        loc.pathname = newUrl;
                    }
                    else {
                        loc.reload(true);
                    }
                    return false;
                }
                else {
                    $nc.ajax({
                        'type' : 'GET',
                        'url': (backurl ? backurl : nc_page_url()) + '&isNaked=1&admin_modal=1&cc_only=' + cc,
                        success: function(response) {
                            nc_update_admin_mode_content(response, null, cc);
                            $nc.modal.close();
                        }
                    });
                }
            }
        });
    return false;
    }
});
}

function nc_action_message(url, httpMethod, httpData) {
    var ajax_url = url + '&isNaked=1&posting=1' + '&nc_token=' + nc_token,
        cc_match = url.match(/\bcc=(\d+)/),
        cc = cc_match[1];

    if (!httpMethod) {
        httpMethod = 'GET';
    }

    if (!httpData) {
        httpData = {};
    }

    $nc.ajax({
        'type' : httpMethod,
        'data': httpData,
        'url': ajax_url,
        'success' : function(response) {
            response = $nc.trim(response);
            if (response == 'deleted') {
                $nc('body', nc_get_current_document()).append("<div id='formAsyncSaveStatus'>Объект помещен в корзину</div>");
                $nc('div#formAsyncSaveStatus', nc_get_current_document()).css({
                    backgroundColor: '#39B54A'
                });
                setTimeout(function () {
                    $nc('div#formAsyncSaveStatus', nc_get_current_document()).remove();
                },
                1000);
            }

            if (response.indexOf('trashbin_disabled') > -1) {

                nc_print_custom_modal();

                $nc('div#nc_cart_confirm_footer button.nc_admin_metro_button').click(function() {
                    $nc.modal.close();
                    nc_action_message(url + '&force_delete=1')
                });

                return null;
            }

            var $status_message = $nc('<div />').html(response).find('#statusMessage');

            $nc.ajax({
                'type': 'GET',
                'url' : nc_page_url() + '&isNaked=1',
                'success' : function(response) {
                    response ? nc_update_admin_mode_content(response, $status_message, cc)
                    : nc_page_url(nc_get_back_page_url());
                }
            });
    }
    });
}

function nc_is_frame() {
    return typeof mainView != "undefined";
}

function nc_has_frame() {
    return 'mainView' in top.window && top.window.mainView.oIframe;
}

function nc_get_back_page_url() {
    return NETCAT_PATH + '?' + nc_page_url().match(/sub=[0-9]+/) + (nc_is_frame() ? '&inside_admin=1' : '');
}

function nc_page_url(url) {
    return nc_correct_page_url(url ? nc_get_location().href = url : nc_get_location().href);
}

function nc_correct_page_url(url) {
    url = url.replace(/#.*$/, '');
    return url.indexOf('?') == -1 ? url + '?' : url ;
}

function nc_update_admin_mode_infoblock(infoblock_id, callback) {
     $nc.ajax({
         'type' : 'GET',
         'url': nc_page_url() + '&isNaked=1&admin_modal=1&cc_only=' + infoblock_id,
         success: function(response) {
             nc_update_admin_mode_content(response, null, infoblock_id);
             if ($nc.isFunction(callback)) {
                 callback();
             }
         }
     });
}

function nc_update_admin_mode_content(content, $status_message, cc) {
    var scope = nc_has_frame() ? top.window.mainView.oIframe.contentDocument : document,
        block_id_selector = '#nc_admin_mode_content' + (cc || ''),
        $nc_admin_mode_content = $nc(block_id_selector, scope),
        new_content_block_by_id = $nc(content).filter(block_id_selector);

    if ($nc_admin_mode_content.length && new_content_block_by_id.length) {
        $nc_admin_mode_content.replaceWith(new_content_block_by_id);
        $nc_admin_mode_content = new_content_block_by_id;
    }
    else {
        if (!$nc_admin_mode_content.length) {
            $nc_admin_mode_content = $nc('div.nc_admin_mode_content', scope);
        }
        $nc_admin_mode_content.html(content);
    }

    $nc_admin_mode_content.find('LINK[rel=stylesheet]').appendTo($nc('HEAD', scope));

    $nc_admin_mode_content.prev('#statusMessage').remove();

    if (typeof($status_message) != 'undefined' && $status_message) {
        $nc_admin_mode_content.before($status_message);
    }

    if ($nc.fn.addImageEditing) {
        $nc(".cropable").addImageEditing();
    }
}

function nc_get_current_document() {
    return nc_is_frame() ? mainView.oIframe.contentDocument : document;
}

function nc_get_location() {
    return nc_is_frame() ? mainView.oIframe.contentWindow.location : location;
}

function nc_remove_content_for_modal() {
    $nc('#nc_form_result').remove();
    if (typeof(resize_layout) != 'undefined') {
        resize_layout();
    }
}

function nc_password_change() {
    var $password_change = $nc('#nc_password_change');
    $password_change.modal({
        closeHTML: "",
        containerId: 'nc_small_modal_container',
        onShow: function () {
            $nc('div.simplemodal-wrap').css({padding:0, overflow:'inherit'});
            var $form = $password_change.find('form');
            $nc('#nc_small_modal_container').addClass('nc-shadow-large').css({width:$form.width(), height:$form.height()});
            $nc(window).resize();
        }
    });

    // $nc('.password_change_simplemodal_container').css({
    //       backgroundColor: 'white',
    // });

    //FIXME: проверка формы изменения пароля перед отправкой
    if (false) {
        var $submit = $password_change.find('button[type=submit]');
        // var button = $nc('div#nc_password_change_footer button.nc_admin_metro_button');
        $submit.unbind();
        $submit.click(function() {
            if ($nc('input[name=Password1]').val() != $nc('input[name=Password2]').val()) {
                $nc('div#nc_password_change_footer').append(
                    "<div id='nc_modal_error' style='position: absolute; z-index: 3000; width: 200px; border: 2px solid red;background-color: white; bottom: 190px; text-align: left; padding: 10px;'>"
                    + "<div class='simplemodal_error_close'></div>"
                    + ncLang.UserPasswordsMismatch
                    + "</div>");
                return false;
            }
            $nc('div#nc_password_change_body form').submit();
        });
    }

    $nc('div#nc_password_change form').ajaxForm({
        success : function() {
            $nc.modal.close();
        }
    });
}

$nc('button.nc_admin_metro_button_cancel').click(function() {
    $nc.modal.close();
});

function nc_check_error(response) {
    var div = document.createElement('div');
    div.innerHTML = response;
    return $nc(div).find('#nc_error').html();
}

$nc('.simplemodal_error_close').click(function() {
    $nc('#nc_modal_error').remove();
});

function CMSaveAll() {
    /* // pre method
    var editors = null;

    if ( nc_is_frame() ) {
        editors = mainView.oIframe.contentWindow.CMEditors;
    }
    else {
        editors = window.CMEditors;
    }
    if ( typeof(editors) != 'undefined' ) {
        for(var key in editors) {
            editors[key].save();
        }
    }*/

    $nc('textarea.has_codemirror').each(function() {
        $nc(this).data('codemirror').save();
    });
}

function nc_print_custom_modal() {
    $nc('body').append("<div id='nc_cart_confirm' style='display: none;'></div>");

    var cart_confirm = $nc('#nc_cart_confirm');

    cart_confirm.append("<div id='nc_cart_confirm_header'></div>");
    cart_confirm.append("<div id='nc_cart_confirm_body'></div>");
    cart_confirm.append("<div id='nc_cart_confirm_footer'></div>");

    $nc('#nc_cart_confirm_header').append("<div><h2 style='padding: 0px;'>" + ncLang.DropHard + "</h2></div>");
    $nc('#nc_cart_confirm_footer').append("<button type='button' class='nc_admin_metro_button nc-btn nc--blue'>" + ncLang.Drop + "</button>");
    $nc('#nc_cart_confirm_footer').append("<button type='button' class='nc_admin_metro_button_cancel nc-btn nc--red nc--bordered nc--right'>" + ncLang.Cancel + "</button>");

    cart_confirm.modal({
        closeHTML: "",
        containerId: 'cart_confirm_simplemodal_container',
        onShow: function () {
            $nc('.simplemodal-wrap').css({
                backgroundColor: 'white'
            });
        },
        onClose : function () {
            $nc.modal.close();
            $nc('#nc_cart_confirm').remove();
        }
    });

    $nc('div#nc_cart_confirm_footer button.nc_admin_metro_button_cancel').click(function() {
        $nc.modal.close();
    });

    $nc('div#nc_cart_confirm_footer button.nc_admin_metro_button').click(function() {
        if (typeof callback_on_confirm == 'function'){
            callback_on_confirm();
            $nc.modal.close();
        }
    });

}


function nc_print_custom_modal_callback(callback){
    $nc('body').append("<div id='nc_cart_confirm' style='display: none;'></div>");

    var cart_confirm = $nc('#nc_cart_confirm');

    cart_confirm.append("<div id='nc_cart_confirm_header'></div>");
    cart_confirm.append("<div id='nc_cart_confirm_body'></div>");
    cart_confirm.append("<div id='nc_cart_confirm_footer'></div>");

    $nc('#nc_cart_confirm_header').append("<div><h2 style='padding: 0px;'>" + ncLang.DropHard + "</h2></div>");
    $nc('#nc_cart_confirm_footer').append("<button type='button' class='nc_admin_metro_button_cancel nc-btn nc--bordered nc--blue'>" + ncLang.Cancel + "</button>");
    $nc('#nc_cart_confirm_footer').append("<button type='button' class='nc_admin_metro_button nc-btn nc--red nc--bordered nc--right'>" + ncLang.Drop + "</button>");

    cart_confirm.modal({
        closeHTML: "",
        containerId: 'cart_confirm_simplemodal_container',
        onShow: function () {
            $nc('.simplemodal-wrap').css({
                backgroundColor: 'white'
            });
        },
        onClose : function () {
            $nc.modal.close();
            $nc('#nc_cart_confirm').remove();
        }
    });

    $nc('div#nc_cart_confirm_footer button.nc_admin_metro_button_cancel').click(function() {
        $nc.modal.close();
    });

    $nc('div#nc_cart_confirm_footer button.nc_admin_metro_button').click(function() {
        if (typeof callback == 'function'){
            callback();
            $nc.modal.close();
        }
    });
}

function prepare_message_form() {
    $nc(function() {
        $nc('#adminForm').wrapInner('<div class="nc_admin_form_main">');
        $nc('#adminForm').append($nc('#nc_seo_append').html());
        $nc('#adminForm').append('<input type="hidden" name="isNaked" value="1" />');
        $nc('#nc_seo_append').remove();
    });

    //var nc_admin_form_values = $nc('#adminForm').serialize();

    $nc('#nc_show_main').click(function() {
        $nc('.nc_admin_form_main').show();
        $nc('.nc_admin_form_seo').hide();
    });

    $nc('#nc_show_seo').click(function() {
        $nc('.nc_admin_form_main').hide();
        $nc('.nc_admin_form_seo').show();
    });

    $nc('#nc_object_slider_menu li').click(function(){
        $nc('#nc_object_slider_menu li').removeClass('button_on');
        $nc(this).addClass('button_on');
    });

    $nc('.nc_admin_metro_button_cancel').click(function() {
        $nc.modal.close();
    });

    $nc('.nc_admin_metro_button').click(function() {
        if ( $nc(this).hasClass('nc--loading') ) return;
        nc.process_start('nc_form()', this);
        $nc('#adminForm').submit();
    });
    InitTransliterate();
    if (typeof nc_autosave_use !== "undefined" && nc_autosave_use == 1) {
        InitAutosave('adminForm');
        if (autosave !== null && typeof autosave !== "undefined") {
            $nc('.nc_draft_btn').click(function(e) {
                e.preventDefault();
                $nc(this).addClass('nc--loading');
                autosave.saveAllData(autosave);
            });
        }
    }
}

function nc_typo_field(field) {
    var string;
    if (typeof CKEDITOR != 'undefined' && CKEDITOR.instances && typeof(CKEDITOR.instances[field]) != 'undefined') {
        string = CKEDITOR.instances[field].getData();
        string = Typographus_Lite.process(string);
        CKEDITOR.instances[field].setData(string);
    } else if (typeof FCKeditorAPI != 'undefined' && FCKeditorAPI.Instances && typeof(FCKeditorAPI.Instances[field]) != 'undefined') {
        var editor = FCKeditorAPI.GetInstance(field);
        string = editor.GetHTML();
        string = Typographus_Lite.process(string);
        editor.SetHTML(string);
    } else {
        var $textarea = $nc('TEXTAREA[name=' + field + ']');
        string = $textarea.val();
        string = Typographus_Lite.process(string);
        $textarea.val(string);
    }
}


function nc_infoblock_controller_request(el, action, params) {
    return $nc.post(
        NETCAT_PATH + 'action.php',
        $nc.extend(
            {
                ctrl: 'admin.infoblock',
                action: action,
                infoblock_id: $nc(el).closest('.nc-infoblock-toolbar').data('infoblockId')
            },
            params
        )
    );
}

function nc_infoblock_toggle(el) {
    nc_infoblock_controller_request(el, 'toggle')
        .success(function(response) {
            if (response == 'OK') {
                $nc(el).children().toggle();
            }
            else {
                // todo: request: process errors
                alert(response);
            }
        });

    return false;
}

function nc_infoblock_place_before(el, other_infoblock_id) {
    return nc_infoblock_change_order(el, 'before', other_infoblock_id);
}

function nc_infoblock_place_after(el, other_infoblock_id) {
    return nc_infoblock_change_order(el, 'after', other_infoblock_id);
}

function nc_infoblock_change_order(el, position, other_infoblock_id) {
    nc_infoblock_controller_request(el, 'change_order', { position: position, other_infoblock_id: other_infoblock_id})
        .success(function(response) {
            if (response == 'OK') {
                window.location.hash = el.href.split('#')[1];
                window.location.reload(true);
            }
            else {
                // todo: request: process errors
                alert(response);
            }
        });

    return false;
}

function nc_infoblock_set_template(infoblock_id, template_id) {
    nc_infoblock_controller_request(null, 'set_component_template', {
        infoblock_id: infoblock_id,
        template_id: template_id
    }).success(function(response) {
        nc_update_admin_mode_content(response, '', infoblock_id);
    });
    return false;
}

function nc_infoblock_buffer_get_id() {
    return $nc.cookie('nc_admin_buffer_infoblock_id');
}

function nc_infoblock_buffer_update_page() {
    $nc('body').toggleClass('nc-page-buffer-has-infoblock', !!nc_infoblock_buffer_get_id());
}

$nc(function() {
    nc_infoblock_buffer_update_page();
    $nc('body').on('mouseenter.nc_infoblock_paste', '.nc-infoblock', nc_infoblock_buffer_update_page);
});


function nc_infoblock_buffer_copy(infoblock_id) {
    $nc.cookie('nc_admin_buffer_infoblock_id', infoblock_id);
    nc_infoblock_buffer_update_page();
}

function nc_infoblock_buffer_paste(controller_link) {
    var infoblock_id = nc_infoblock_buffer_get_id();
    if (!infoblock_id) { return null; }

    $nc.ajax({
        method: 'POST',
        url: NETCAT_PATH + 'action.php',
        data: controller_link.substr(controller_link.indexOf('?') + 1) + '&copied_infoblock_id=' + infoblock_id,
        success: function(response) {
            if (response == 'OK') {
                location.reload();
            }
            else if (response) {
                // todo: request: process errors
                alert(response);
            }
        }
    });
}

function nc_init_toolbar_dropdowns() {
    // dropdown inside nc-toolbar: open on click, close on mouseleave or click inside the dropdown
    var event_ns = '.nc_toolbar_dropdown',
        toolbar_class = '.nc6-toolbar',
        close_timeout_id,
        clear_close_timeout = function() { clearTimeout(close_timeout_id); };

    $nc('body').on('click' + event_ns, toolbar_class + ' .nc--dropdown', function(e) {
        e.preventDefault();
        var el = $nc(this),
            close = function() {
                el.removeClass('nc--clicked');
                clear_close_timeout();
            };

        if (el.hasClass('nc--clicked')) {
            close();
        }
        else {
            el.siblings().removeClass('nc--clicked');
            clearTimeout(close_timeout_id);

            var body_width = $nc('body').innerWidth();

            el.addClass('nc--clicked')
                .off(event_ns)
                .on('mouseenter' + event_ns, clear_close_timeout)
                .on('mouseleave' + event_ns, function() {
                    close_timeout_id = setTimeout(close, 1000);
                });

            // проверяем, чтобы выпадающее меню не попадало за пределы экрана по горизонтали
            var dropdown = el.children('ul'),
                dropdown_left = dropdown.offset().left,
                toolbar_right = parseInt(el.parents('ul' + toolbar_class).css('right'), 10);
            if (dropdown_left + dropdown.outerWidth() > body_width - toolbar_right) {
                dropdown.css('width', (body_width - dropdown_left - toolbar_right) + 'px');
            }
        }
    });
}

$nc(nc_init_toolbar_dropdowns);

/**
 *
 */
function nc_editable_image_init(c) {
    c = $nc(c);
    c.find('input[type=file]').change(nc_editable_image_upload);
    c.find('.nc-editable-image-remove').click(nc_editable_image_remove);
    c.parents('a').prop('href', '#____'); // не получилось остановить переход по ссылке в FF
    c.find('form').mouseover(function() { c.addClass('nc--hover'); });
    c.mouseleave(function() { c.removeClass('nc--hover'); });
}

/**
 * Удаление изображения при in-place редактировании
 */
function nc_editable_image_remove(event) {
    event.stopPropagation();
    var c = $nc(event.target).closest('.nc-editable-image-container').addClass('nc--empty'),
        form = c.find('form');
    c.find('img').prop('src', nc_edit_no_image);
    form.find('input[name^=f_KILL]').val(1);
    nc.process_start('nc_editable_image_remove');
    function done() { nc.process_stop('nc_editable_image_remove'); }
    form.ajaxSubmit({ success: done, error: done });
}

/**
 * Замена изображения при in-place редактировании
 */
function nc_editable_image_upload(event) {
    var filereader_max_size = 2 * 1024 * 1024,
        input = event.target,
        need_to_reload = true,
        form = $nc(input).closest('form'),
        c = form.closest('.nc-editable-image-container').removeClass('nc--empty');

    if ('FileReader' in window && !c.hasClass('nc--always-reload')) {
        if (input.files[0].size < filereader_max_size) {
            need_to_reload = false;
            var reader = new FileReader();
            reader.onload = function(e) {
                form.find('img').prop('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    nc.process_start('nc_editable_image_upload');
    function stop() { nc.process_stop('nc_editable_image_upload'); }
    function done() {
        if (need_to_reload) {
            var cc = form.find('input[name=cc]').val();
            $nc.ajax({
                'type' : 'GET',
                'url': nc_page_url() + '&isNaked=1&admin_modal=1&cc_only=' + cc,
                        success: function(response) {
                            nc_update_admin_mode_content(response, null, cc);
                            stop();
                        }
            });
        }
        else {
            stop();
        }
    }

    form.ajaxSubmit({ success: done, error: done });
    return false;
}