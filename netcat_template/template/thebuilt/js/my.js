var email_data;
var projectID = 0,
    cleanForm = !1;

function initFavStars() {
    for (var e = 0; e < arFav.length; e++) $el = $(".portfolio-item-block[data-id=" + arFav[e] + "]"), $el.append('<a class="favorites" href="/favorites/"><i class="fa fa-star"></i><i class="fa fa-star"></i></a>')
}

function initAddFaq() {
    "#add" == window.location.hash && $(".js-add-toggle")
        .toggle()
}
function appendCalcData($this){
            var f_Data = $.param({data:JSON.stringify(email_data)});
            $data = $("<input/>");
            $data.attr("name","f_Data").attr("type","hidden").val(f_Data);
            $this.parent().find("form").append($data);
}
function initLine() {
    $hash = window.location.hash.split("#"), $hash = $hash[1], void 0 != $hash && ($hash = "." + $hash, $(".line-desc,.portfolio-item-block")
        .hide(), $($hash)
        .show(), $($hash)
        .find(".line-slides")
        .css("position", "static"), $("[data-filter]")
        .removeClass("active"), $("[data-filter]")
        .each(function () {
            $(this)
                .data("filter") == $hash && $(this)
                .addClass("active")
        }), window.location.hash = "")
}

function InitProjectPage() {
    jQuery(".post-content > .row > .col-md-3")
        .css("min-height", $(".post-content > .row > .col-md-9")
            .height() + "px")
}

function sInit() {
    fixModal()
}

function fixModal() {
    $modal = jQuery("#popup_form_detail");
    var e = $modal.height(),
        t = jQuery(window)
        .height(),
        a = $(window)
        .scrollTop() + t / 2 - e / 2 - 50;
    $modal.css("top", a + "px")
}

function getComplTableHTML() {
    return $("#frmCalc")
        .find("input[type=checkbox],input[type=radio]")
        .each(function () {
            $this = $(this), $this.is(":checked") ? $(this)
                .attr("checked", "checked") : $this.removeAttr("checked")
                .prop("checked", !1)
        }), $table = $("<div/>"), $table.html($("#frmCalc")
            .html()), $table.find(".mob")
        .remove(), $table.find("table")
        .attr("border", "0")
        .attr("cellspacing", "0")
        .attr("cellpadding", "0")
        .css({
            border: "0"
        }), $table.find("th")
        .removeAttr("style")
        .css({
            "background-color": "#2A2F35",
            color: "#ffffff",
            "font-size": "12px"
        }), $table.find(".notices_wrapper,.tooltip_wrapper")
        .remove(), $table.find("td,th")
        .css("padding", "10px 25px"), $table.find("th:nth-child(3),th:nth-child(4)")
        .attr("width", "1%"), $table.find("th:nth-child(2)")
        .attr("width", "80%"), $table.find("tbody")
        .find("tr")
        .each(function (e, t) {
            e % 2 == 0 && $(t)
                .find("td")
                .css("background-color", "#F7F7F7"), 0 != e && e % 2 == 0 || $(t)
                .find("td")
                .css("background-color", "#FDFDFD")
        }), $table.find("[data-rowspan]")
        .each(function () {
            $(this)
                .parent()
                .remove()
        }), $table.html()
}
jQuery(document)
    .ready(function () {
if ($("[name=f_Phone]").size()>0) {
    jQuery.prototype.mask=function(mask){
	    cleave = new Cleave("[name=f_Phone]", {
            phone: true,
            phoneRegionCode: 'ru',
	        prefix: '+7 '
        });
    }
}
    $("div[data-load]").each(function(){
        $this = $(this);
        var url = $this.data("load");
        url = url.split("?");
        if (url[1]==undefined) url[1]="";
        url = url[0] + "?" + url[1] + "&isNaked=1";
        $this.load(url,function(){
            appendCalcData($this);
if ($("[name=f_Phone]").size()>0) {
	    cleave = new Cleave("[name=f_Phone]", {
            phone: true,
            phoneRegionCode: 'ru',
	        prefix: '+7 '
        });
}
        });
    });    
        initAddFaq(), jQuery(".vc_tta-tab")
            .click(function () {
                jQuery(".vc_tta-tab")
                    .removeClass("vc_active"), jQuery(this)
                    .addClass("vc_active")
            }), jQuery(document)
            .on({
                submit: function () {
                    return $form = jQuery(this), jQuery.post($form.data("action"), $form.serialize(), function (e) {
                        $form.find(".result,.error")
                            .html(""), $form.find("." + e.status)
                            .html(e.text), $("html, body")
                            .animate({
                                scrollTop: $("#toform")
                                    .offset()
                                    .top
                            }, 500)
                    }, "json"), !1
                }
            }, "[data-action]"), initFavStars(), jQuery("body")
            .prepend("<div class='js-callback-wrap'></div>"), jQuery(document)
            .on({
                click: function () {
                    if (!$(this)
                        .hasClass("in-fav")) return $form = jQuery(this)
                        .closest("form"), $this = jQuery(this), jQuery.post($form.attr("action"), $form.serialize(), function (e) {
                            $this.remove(), $form.append(e), jQuery("a.favorites i")
                                .removeClass("fa-star-o")
                                .addClass("fa-star")
                                .addClass("full"), $("header .favorites i")
                                .text($("#myfav")
                                    .val())
                        }), !1
                }
            }, ".add_to_favorite"), jQuery(document)
            .on({
                click: function (e) {
                    return $this = $(this), $.post($this.attr("href") + "izbrannoe.html?isNaked=1&del_fav=" + $this.data("fav"), function (e) {
                        $this.text(e.text)
                            .removeClass("in-fav")
                            .removeAttr("data-fav"), $this.prepend('<i class="svg-icon svg-favorite"></i>')
                    }, "json"), e.stopImmediatePropagation(), !1
                }
            }, "[data-fav]"), $(".button-warn")
            .click(function () {
                $(this)
                    .closest("td")
                    .toggleClass("hover_tooltip")
            }), $(".tooltiptd")
            .hover(function () {}, function () {
                $(this)
                    .removeClass("hover_tooltip")
            }), $(".button")
            .click(function () {
                $(".notice_inner")
                    .hide()
            }), jQuery(document)
            .scroll(function () {
                jQuery(window)
                    .scrollTop() > 40 ? jQuery(".header-menu-bg+header.main-header")
                    .addClass("white-header") : jQuery(".header-menu-bg+header.main-header")
                    .removeClass("white-header")
            }), jQuery(".js-subscribe")
            .submit(function () {
                return $form = jQuery(this), jQuery.post($form.attr("action"), $form.serialize() + "&isNaked=1", function (e) {
                    $res = jQuery("<div/>"), $res.attr("id", "parent_popup_form_detail_phone"), $res.append('<div class="nc_full nc_callback mfeedback phone_form" id="popup_form_detail"><a class="close_backcall_detail" title="" onclick="$(\'#parent_popup_form_detail_phone\').hide();"></a><div class="vhod_title thankssubs">Спасибо за подписку!</div></div>'), jQuery(".js-callback-wrap")
                        .html("")
                        .append($res), fixModal(), jQuery("#parent_popup_form_detail_phone")
                        .fadeIn()
                }), !1
            }), jQuery(document).on({
            	submit: function(){
                return jQueryform = jQuery(this), jQueryform.find('input[name="f_file1"]')
                    .length > 0 ? jQueryform.ajaxSubmit({
                        data: {
                            isNaked: 0,
                            ajax1: 1
                        },
                        success: function (e) {
                            jQueryform.parent()
                                .html(e), ga("send", "event", "send", "CalculateYourProject"), yaCounter4312879.reachGoal("SendCalculateYourProject"), sInit()
                        }
                    }) : jQueryform.closest(".form_requestshow")
                    .length > 0 ? jQuery.post(jQueryform.attr("action"), jQueryform.serialize() + "&isNaked=0&ajax1=1", function (e) {
                        jQuery(".js-callback-wrap")
                            .html('<div id="parent_popup_form_detail_phone" style="display: block;"><div style="margin-top:3%;" class="nc_full nc_callback mfeedback phone_form" id="popup_form_detail"><a class="close_backcall_detail" title="" onclick="jQuery(\'#parent_popup_form_detail_phone\').hide();"></a>' + e + "</div></div>"), ga("send", "event", "send", "RequestShowingForm2"), yaCounter4312879.reachGoal("SendRequestShowingForm2"), jQuery(".form_requestshow_left input.textinput, .form_requestshow_right textarea")
                            .val("")
                    }) : ($data = $("<input/>"), $data.attr("type", "hidden")
                        .attr("name", "f_Data")
                        .val(JSON.stringify(email_data)), jQueryform.append($data), jQuery.post(jQueryform.attr("action"), jQueryform.serialize() + "&isNaked=0&ajax1=1", function (e) {
                            if (jQueryform.parent()
                                .html(e), fixModal(), "/catalog/zakaz/" == window.location.pathname) ga("send", "event", "send", "RequestShowingForm3"), yaCounter4312879.reachGoal("SendRequestShowingForm3");
                            else switch (jQueryform.parent()
                                .parent()[0].children[1].innerHTML) {
                            case "Р—Р°РїСЂРѕСЃ РїР»Р°РЅРёСЂРѕРІРєРё":
                                ga("send", "event", "send", "RequestOtherPlan"), yaCounter4312879.reachGoal("SendRequestOtherPlan");
                                break;
                            case "Р—Р°СЏРІРєР° РЅР° РїРѕР»СѓС‡РµРЅРёРµ СЃРјРµС‚С‹":
                                ga("send", "event", "send", "ReceiveEstimates"), yaCounter4312879.reachGoal("SendReceiveEstimates");
                                break;
                            case "Р—Р°СЏРІРєР° РЅР° РїРѕСЃС‚СЂРѕР№РєСѓ РґРѕРјР°":
                                ga("send", "event", "send", "ContactManager"), yaCounter4312879.reachGoal("SendContactManager");
                                break;
                            default:
                                ga("send", "event", "send", "RequestShowingForm1"), yaCounter4312879.reachGoal("SendRequestShowingForm1")
                            }
                            sInit()
                        })), !1            	
            	}
            },".js-callback"),
            /*jQuery(".js-callback")
            .live("submit", function () {

            }),*/ projectID > 0 && setTimeout(function () {
                InitProjectPage()
            }, 500), jQuery(".js-click-block .wpb_content_element")
            .click(function () {
                top.location.href = jQuery(this)
                    .find("a.btn")
                    .attr("href")
            }), jQuery(".js-turn-video")
            .click(function () {
                return $this = jQuery(this), $parent = $this.parent(), $video = $parent.find(".js-video"), src = $video.find("iframe")
                    .attr("src"), $video.find("iframe")
                    .attr("src", src + "&autoplay=1"), $this.slideUp(), $video.show("slide"), $this.closest(".wpb_text_column")
                    .addClass("video-wrap"), !1
            }), jQuery(document)
            .on({
                click: function () {
                    $this = jQuery(this), $this.parent()
                        .find(".js-filter")
                        .removeClass("active"), $this.addClass("active");
                    var e = $this.data("class");
                    return "" == e ? jQuery("div[data-class]")
                        .show() : (jQuery("div[data-class]")
                            .hide(), jQuery("div[data-class=" + e + "]")
                            .show()), !1
                }
            }, ".js-filter"), jQuery(".js-openPopup")
            .click(function () {
                jQuery("body")
                    .css("position", "relative");
                var e = jQuery(this)
                    .data("href"),
                    a = jQuery(this)
                    .data("show");
                if (0 != cleanForm) return jQuery(e)
                    .html(cleanForm), jQuery(a)
                    .fadeIn(100), !1;
                t = jQuery(this)
                    .data("action")
                    .split("?");
                var i = t[0] + "?" + t[1] + "&isNaked=0&show=1";
                console.log(i);
                var r, o = jQuery(window)
                    .scrollTop() + 10;
                return console.log(o), void 0 != $(this)
                    .data("compl") && ($form = $("<form/>"), $inp = $("<input/>"), $inp.attr("type", "hidden")
                        .attr("name", $(this)
                            .data("compl"))
                        .val(getComplTableHTML()), $form.append($inp), r = $form.serialize(), void 0 != $(this)
                        .data("data") && (r = r + "&" + $(this)
                            .data("data"))), $form = $("#adminForm"), $data = $("<input/>"), $data.attr("type", "hidden")
                    .attr("name", "f_Data")
                    .val($.param({
                        data: JSON.stringify(email_data)
                    })), $form.append($data), $.post(i, r, function (t) {
                        jQuery(e)
                            .html(t), jQuery("#popup_form_detail")
                            .css("top", o + "px"), jQuery(a)
                            .fadeIn(100)
                    }), !1
            }), jQuery(".nc-navbar")
            .hide(), jQuery(".main_subscribe_new a")
            .click(function () {
                jQuery.ajax({
                        type: "POST",
                        url: "/subscribe_new.php",
                        data: "user_mail=" + $(".subscribe_new")
                            .val(),
                        success: function (e) {
                            jQuery(".subscribe_result")
                                .html(e)
                        }
                    }), jQuery(".subscribe_new")
                    .val("")
            }), jQuery(document)
            .on({
                click: function () {
                    return jQuery(this)
                        .closest("div")
                        .find("ul")
                        .toggle(), !1
                }
            }, ".js-ch-city"), jQuery(document)
            .on({
                click: function () {
                    jQuery(this)
                        .closest(".ch_city")
                        .find(".js-ch-city")
                        .text(jQuery(this)
                            .text()), jQuery(this)
                        .closest(".ch_city")
                        .find("ul ul")
                        .toggle();
                    var e = document.location.pathname;
                    return document.location.search.length < 1 && (e += "?"), e += document.location.search, document.location.search.length > 1 && (e += "&"), e = e + "city=" + jQuery(this)
                        .data("id"), top.location.href = e, !1
                }
            }, ".ch_city ul li a")
    });

$(function () {

    $("div#modalmap+i")
        .click(function () {
            $(".showmap")
                .removeClass("showmap");

        });
    $(document)
        .on({
            click: function () {
                $("div#modalmap+i")
                    .click();
                $(this)
                    .closest(".map-wrap")
                    .html("");
            }
        }, ".map-wrap > i");
    $(".hasMap")
        .click(function () {
            $(".map-wrapper")
                .html("");
            $(this)
                .closest(".js-wrapper")
                .find(".map-wrap")
                .load($(this)
                    .attr("href") + "&isNaked=1" + "&h=" + ($(this)
                        .closest(".js-wrapper")
                        .height()),
                    function () {
                        $("body")
                            .addClass("showmap")
                    });
            return false;
        })
});