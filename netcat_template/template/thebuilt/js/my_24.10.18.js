var projectID=0;
var cleanForm=false;

function initFavStars() {
  var favStar = '<a class="favorites" href="/favorites/"><i class="fa fa-star"></i><i class="fa fa-star"></i></a>';
  for(var i=0;i<arFav.length;i++) {
     $el = $(".portfolio-item-block[data-id="+arFav[i]+"]");
     $el.append(favStar);
  }
}

function initAddFaq() {
    var hash = window.location.hash;
    if (hash == "#add") $(".js-add-toggle").toggle();
}
function appendCalcData($this){
            var f_Data = $.param({data:JSON.stringify(email_data)});
            $data = $("<input/>");
            $data.attr("name","f_Data").attr("type","hidden").val(f_Data);
            $this.parent().find("form").append($data);
}
function initLine() {
    $hash = window.location.hash.split("#"); $hash = $hash[1];
    if ($hash!=undefined) {
        $hash = "."+$hash;
        $(".line-desc,.portfolio-item-block").hide();
        $($hash).show();
        $($hash).find(".line-slides").css("position","static");
        $("[data-filter]").removeClass("active");
        $("[data-filter]").each(function(){if($(this).data("filter")==$hash) $(this).addClass("active")});
        window.location.hash = "";
    }
}
function InitProjectPage() {
   jQuery(".post-content > .row > .col-md-3").css("min-height",$(".post-content > .row > .col-md-9").height()+"px");
}
function sInit() {
    fixModal();
    return;
    jQuery("#phone").mask("+7 (999) 999-99-99");
    jQuery("input[name=f_Phone]").mask("+7 (999) 999-99-99").attr('placeholder',"+7 (___) ___-__-__");
    jQuery("img.date").on("click", function(){
        jQuery('input.date').trigger("click");
    });
    jQuery('input.date').pickmeup({
        change : function (val) {
            jQuery('.date').pickmeup('hide');
        }
    });
}
function fixModal() {
    $modal = jQuery("#popup_form_detail");
    var h = $modal.height()
    var w = jQuery(window).height();
    var offset = $(window).scrollTop()+ (w/2) - (h/2) - 50;
    $modal.css("top",offset+"px");
}
function getComplTableHTML() {
    $("#frmCalc").find("input[type=checkbox],input[type=radio]").each(function(){
    	$this = $(this);
    	if ($this.is(":checked"))
    		$(this).attr("checked","checked");
    	else $this.removeAttr("checked").prop("checked",false);
    });
    $table = $("<div/>");
    $table.html($("#frmCalc").html());
    $table.find(".mob").remove();
    $table.find("table").attr("border","0").attr("cellspacing","0").attr("cellpadding","0").css({"border":"0"});
    $table.find("th").removeAttr("style").css({"background-color": "#2A2F35","color": "#ffffff","font-size":"12px"});
    $table.find(".notices_wrapper,.tooltip_wrapper").remove();
    $table.find("td,th").css("padding","10px 25px");
    $table.find("th:nth-child(3),th:nth-child(4)").attr("width","1%");
    $table.find("th:nth-child(2)").attr("width","80%");
    $table.find("tbody").find("tr").each(function(i,el){
        if( i%2==0) $(el).find("td").css("background-color","#F7F7F7");
        if(i==0 || i%2!=0) $(el).find("td").css("background-color","#FDFDFD");
    });
    $table.find("[data-rowspan]").each(function(){$(this).parent().remove()});
    return $table.html();
}
var cleave;
jQuery(document).ready(function(){
    jQuery.prototype.mask=function(mask){
	    cleave = new Cleave("[name=f_Phone]", {
            phone: true,
            phoneRegionCode: 'ru',
	        prefix: '+7 '
        });
    }
    $("[data-load]").each(function(){
        $this = $(this);
        var url = $this.data("load");
        url = url.split("?");
        if (url[1]==undefined) url[1]="";
        url = url[0] + "?" + url[1] + "&isNaked=1";
        $this.load(url,function(){
            appendCalcData($this);
	    cleave = new Cleave("[name=f_Phone]", {
            phone: true,
            phoneRegionCode: 'ru',
	        prefix: '+7 '
        });
        });
    });
    initAddFaq();
    jQuery(".vc_tta-tab").click(function(){jQuery(".vc_tta-tab").removeClass("vc_active");jQuery(this).addClass("vc_active");});
    jQuery(document).on({
        submit: function(){
            $form = jQuery(this);
            jQuery.post($form.data("action"), $form.serialize(), function(data){

                $form.find(".result,.error").html("");

                $form.find("."+data.status).html(data.text);
                $('html, body').animate({ scrollTop: $("#toform").offset().top }, 500);
            },"json");
            return false;
        }
    },"[data-action]");

    initFavStars();
    jQuery("body").prepend("<div class='js-callback-wrap'></div>");

    jQuery(document).on({
        click: function(){
          if($(this).hasClass("in-fav")) return;
          $form = jQuery(this).closest("form");
          $this = jQuery(this);
          jQuery.post($form.attr("action"), $form.serialize(), function(data){
              //$form.html(data);
              $this.remove();
              $form.append(data);
              jQuery("a.favorites i").removeClass("fa-star-o").addClass("fa-star").addClass("full");
              $("header .favorites i").text($("#myfav").val());
          });
          return false;
        }
    },".add_to_favorite");
    jQuery(document).on({
        click:function(e){
          $this = $(this);
          $.post($this.attr("href")+"izbrannoe.html?isNaked=1&del_fav="+$this.data("fav"),function(data){
              $this.text(data.text).removeClass("in-fav").removeAttr("data-fav");
              $this.prepend('<i class="svg-icon svg-favorite"></i>');
          },"json");
          e.stopImmediatePropagation();
          return false;
        }
    },"[data-fav]");
    $(".button-warn").click(function(){$(this).closest("td").toggleClass("hover_tooltip")});

    $(".tooltiptd").hover(function(){},function(){$(this).removeClass("hover_tooltip")});

    $(".button").click(function(){
        $(".notice_inner").hide();
    });

    jQuery(document).scroll(function(){
        var t = jQuery(window).scrollTop();
        if (t>40) {
            jQuery(".header-menu-bg+header.main-header").addClass("white-header")//.css("position","relative");
        } else {
            jQuery(".header-menu-bg+header.main-header").removeClass("white-header")//.css("position","fixed");
        }
    });

    jQuery('.js-subscribe').submit(function(){
        $form = jQuery(this);
        jQuery.post($form.attr("action"), $form.serialize()+"&isNaked=1", function(data){
            $res = jQuery("<div/>");
            $res.attr("id","parent_popup_form_detail_phone");
            $res.append('<div class="nc_full nc_callback mfeedback phone_form" id="popup_form_detail"><a class="close_backcall_detail" title="" onclick="$(\'#parent_popup_form_detail_phone\').hide();"></a><div class="vhod_title thankssubs">'+"\u0421\u043f\u0430\u0441\u0438\u0431\u043e \u0437\u0430 \u043f\u043e\u0434\u043f\u0438\u0441\u043a\u0443!"+'</div></div>');
            jQuery('.js-callback-wrap').html('').append($res);
            fixModal();
            jQuery("#parent_popup_form_detail_phone").fadeIn();
        });
        return false;
    });

    jQuery(document).on({
    	submit: function(){
            console.log('182');
         jQueryform = jQuery(this);
         $this1 = jQueryform.parent().parent();
        if(jQueryform.find('input[name="f_file1"]').length>0){
            jQueryform.ajaxSubmit({data: {isNaked: 0, ajax1: 1}, success: function(data){
                jQueryform.parent().html(data);
ga('send', 'event', 'send', 'CalculateYourProject'); yaCounter4312879.reachGoal('SendCalculateYourProject');
                sInit()
            }});
        }
        else if(jQueryform.closest('.form_requestshow').length>0){
            jQuery.post(jQueryform.attr('action'), jQueryform.serialize()+'&isNaked=0&ajax1=1', function(data){
                jQuery('.js-callback-wrap').html('<div id="parent_popup_form_detail_phone" style="display: block;"><div style="margin-top:3%;" class="nc_full nc_callback mfeedback phone_form" id="popup_form_detail"><a class="close_backcall_detail" title="" onclick="jQuery(\'#parent_popup_form_detail_phone\').hide();"></a>'+
                    data
                    +'</div></div>');
ga('send', 'event', 'send', 'RequestShowingForm2'); yaCounter4312879.reachGoal('SendRequestShowingForm2');
                jQuery('.form_requestshow_left input.textinput, .form_requestshow_right textarea').val('');
            });
        }
        else{
            jQuery.post(jQueryform.attr('action'), jQueryform.serialize()+'&isNaked=0&ajax1=1', function(data){
                jQueryform.parent().html(data);
appendCalcData( $this1);
console.log("112");
                fixModal();
                if(window.location.pathname == '/catalog/zakaz/')
                {
                    ga('send', 'event', 'send', 'RequestShowingForm3'); yaCounter4312879.reachGoal('SendRequestShowingForm3');
                }
                else
                {
                    if(jQueryform.parent().parent()[0]!=undefined) {
                      switch(jQueryform.parent().parent()[0].children[1].innerHTML)
                      {
                          case "Р—Р°РїСЂРѕСЃ РїР»Р°РЅРёСЂРѕРІРєРё":
                              ga('send', 'event', 'send', 'RequestOtherPlan'); yaCounter4312879.reachGoal('SendRequestOtherPlan');
                          break;
                          case "Р—Р°СЏРІРєР° РЅР° РїРѕР»СѓС‡РµРЅРёРµ СЃРјРµС‚С‹":
                              ga('send', 'event', 'send', 'ReceiveEstimates'); yaCounter4312879.reachGoal('SendReceiveEstimates');
                          break;
                          case "Р—Р°СЏРІРєР° РЅР° РїРѕСЃС‚СЂРѕР№РєСѓ РґРѕРјР°":
                              ga('send', 'event', 'send', 'ContactManager'); yaCounter4312879.reachGoal('SendContactManager');
                          break;
                          default:
                              ga('send', 'event', 'send', 'RequestShowingForm1'); yaCounter4312879.reachGoal('SendRequestShowingForm1');
                          break;
                      }
                    }
                }
                sInit()
            })
        }

        return false;
    	}
    },".js-callback");

   /* jQuery('.js-callback').live('submit',function(){

    });*/


    if (projectID>0) setTimeout(function(){InitProjectPage()},500);
    jQuery(".js-click-block .wpb_content_element").click(function(){
        top.location.href = jQuery(this).find("a.btn").attr("href");
    });
    jQuery(".js-turn-video").click(function(){
      $this = jQuery(this);
      $parent = $this.parent();
      $video = $parent.find(".js-video");
      src = $video.find("iframe").attr('src');
      $video.find("iframe").attr('src', src + '&autoplay=1');
      $this.slideUp();$video.show('slide');
      $this.closest(".wpb_text_column").addClass("video-wrap");
      return false;
    });
    jQuery(document).on({
        click: function(){
            $this = jQuery(this);
            $this.parent().find(".js-filter").removeClass("active");
            $this.addClass("active");
            var id = $this.data("class");
            if (id=="")
                jQuery("div[data-class]").show();
            else {
                jQuery("div[data-class]").hide();
                jQuery("div[data-class="+id+"]").show();
            }
            return false;
        }
    },".js-filter");


    jQuery('.js-openPopup').click(function(){ jQuery("body").css("position","relative");
        var elm = jQuery(this).data('href');
        var show = jQuery(this).data('show');
        if(cleanForm!=false) {
            jQuery(elm).html(cleanForm);
            jQuery(show).fadeIn(100);
            return false;
        }
        t = jQuery(this).data('action').split("?");
        var url = t[0] + '?' + t[1] + '&isNaked=0&show=1';
        console.log(url);
        var top = jQuery(window).scrollTop()+10;
        console.log(top);
        var data;
if ($(this).data("compl")!=undefined) {
   // url = url+"&"+$(this).data("compl")+"="+getComplTableHTML();
   $form = $("<form/>");
   $inp = $("<input/>");
   $inp.attr("type","hidden").attr("name",$(this).data("compl")).val(getComplTableHTML());
   $form.append($inp);
   data = $form.serialize();
   if ($(this).data("data")!=undefined) data = data+"&"+$(this).data("data");
}
        $.post(url,data,function(data){
            jQuery(elm).html(data);
            jQuery("#popup_form_detail").css("top",top+"px");
            jQuery(show).fadeIn(100);
        });
        /*jQuery(elm).load(url,function(data){
            //cleanForm=data;
            jQuery("#popup_form_detail").css("top",top+"px");
            jQuery(show).fadeIn(100);
            //sInit()
        });*/
        return false;
    });

    jQuery(".nc-navbar").hide();

		jQuery('.main_subscribe_new a').click(function(){
			 jQuery.ajax({
				type: "POST",
				url: "/subscribe_new.php",
				data: "user_mail="+$(".subscribe_new").val(),
				success: function(response){
					jQuery('.subscribe_result').html(response);
				}
			});

			jQuery(".subscribe_new").val('');
		});

    jQuery(document).on({
        click: function(){
            jQuery(this).closest('div').find('ul').toggle();
            return false;
        }
    },'.js-ch-city');
    jQuery(document).on({
        click: function(){
            jQuery(this).closest('.ch_city').find('.js-ch-city').text(jQuery(this).text());
            jQuery(this).closest('.ch_city').find('ul ul').toggle();

            var url = document.location.pathname;
            if (document.location.search.length<1) url = url + '?';
            url = url + document.location.search;
            if (document.location.search.length>1) url = url + '&';
            url = url + 'city=' + jQuery(this).data('id');
            top.location.href=url;
            return false;
        }
    },'.ch_city ul li a');
});
$(function(){

$("div#modalmap+i").click(function(){
    $(".showmap").removeClass("showmap")
});
$(document).on({click:function(){ $("div#modalmap+i").click(); }},".map-wrap > i");
$(".hasMap").click(function(){
   $(".map-wrapper").html("");
    $(this).closest(".js-wrapper").find(".map-wrap").load($(this).attr("href")+"&isNaked=1"+"&h="+($(this).closest(".js-wrapper").height()),function(){
         $("body").addClass("showmap")
    });
	return false;
})
});