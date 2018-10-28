var cleanForm=false;
function sInit() {
    $("#phone").mask("+7 (999) 999-99-99");
    $("input[name=f_Phone]").mask("+7 (999) 999-99-99").attr('placeholder',"+7 (___) ___-__-__");
    $("img.date").on("click", function(){
    	$('input.date').trigger("click");
    });
    $('input.date').pickmeup({
        change : function (val) {
            $('.date').pickmeup('hide');
        }
    });
}
$(document).ready(function(){

    sInit()
	$('.js-callback').live('submit',function(){
		$form = $(this);
		if($form.find('input[name="f_file1"]').length>0){
			$form.ajaxSubmit({data: {isNaked: 0, ajax1: 1}, success: function(data){
				$form.parent().html(data);
ga('send', 'event', 'send', 'CalculateYourProject'); yaCounter4312879.reachGoal('SendCalculateYourProject');
                sInit()
			}}); 
		}
		else if($form.closest('.form_requestshow').length>0){
			$.post($form.attr('action'), $form.serialize()+'&isNaked=0&ajax1=1', function(data){
				$('.js-callback-wrap').html('<div id="parent_popup_form_detail_phone" style="display: block;"><div style="margin-top:3%;" class="nc_full nc_callback mfeedback phone_form" id="popup_form_detail"><a class="close_backcall_detail" title="" onclick="$(\'#parent_popup_form_detail_phone\').hide();"></a>'+
					data
					+'</div></div>');
ga('send', 'event', 'send', 'RequestShowingForm2'); yaCounter4312879.reachGoal('SendRequestShowingForm2');
				$('.form_requestshow_left input.textinput, .form_requestshow_right textarea').val('');
			});
		}
		else{
			$.post($form.attr('action'), $form.serialize()+'&isNaked=0&ajax1=1', function(data){
				$form.parent().html(data);

				if(window.location.pathname == '/catalog/zakaz/')
				{
					ga('send', 'event', 'send', 'RequestShowingForm3'); yaCounter4312879.reachGoal('SendRequestShowingForm3');
				}
				else
				{
					switch($form.parent().parent()[0].children[1].innerHTML)
					{
						case "Запрос планировки":
							ga('send', 'event', 'send', 'RequestOtherPlan'); yaCounter4312879.reachGoal('SendRequestOtherPlan');
						break;
						case "Заявка на получение сметы":
							ga('send', 'event', 'send', 'ReceiveEstimates'); yaCounter4312879.reachGoal('SendReceiveEstimates');
						break;
						case "Заявка на постройку дома":
							ga('send', 'event', 'send', 'ContactManager'); yaCounter4312879.reachGoal('SendContactManager');
						break;
						default:
							ga('send', 'event', 'send', 'RequestShowingForm1'); yaCounter4312879.reachGoal('SendRequestShowingForm1');						
						break;
					}
				}
                sInit()
			})
		}

		return false;
	});
	$('.js-openPopup').live('click',function(){
		var elm = $(this).data('href');
		var show = $(this).data('show');
		if(cleanForm!=false) {
			$(elm).html(cleanForm);
			$(show).fadeIn(100);
			return false;
		}
        t = $(this).attr('href').split("?");
		$(elm).load(t[0] + '?' + t[1] + '&isNaked=0&show=1',function(data){
			//cleanForm=data;
			$(show).fadeIn(100);
            sInit()
		});
		return false;
	});

	$('.js-openPopupButton').live('click',function(){
		var elm = $(this).data('href');
		var show = $(this).data('show');
        	t = $(this).data('url').split("?");

		if(t[0] == '/catalog/zakaz/' && t.length == 1)
		{
			ga('send', 'event', 'open', 'ViewHomeLive'); yaCounter4312879.reachGoal('OpenViewHomeLive');			
		}
		if(t[0] == '/catalog/zakaz/' && t.length >= 2)
		{
			ga('send', 'event', 'open', 'SignUp'); yaCounter4312879.reachGoal('OpenSignUp');
		}
		if(t[0] == '/catalog/calculate-project/')
		{
			ga('send', 'event', 'open', 'CalculateYourProject'); yaCounter4312879.reachGoal('OpenCalculateYourProject');
		}
		if(t[0] == '/catalog/zakaz-smety/' && t.length >= 2)
		{
			ga('send', 'event', 'open', 'ReceiveEstimates'); yaCounter4312879.reachGoal('OpenReceiveEstimates');
		}
		if(t[0] == '/catalog/request-plan/' && t.length >= 2)
		{
			ga('send', 'event', 'open', 'RequestOtherPlan'); yaCounter4312879.reachGoal('OpenRequestOtherPlan');
		}
		if(t[0] == '/catalog/zakaz-smety22/' && t.length >= 2)
		{
			ga('send', 'event', 'open', 'ContactManager'); yaCounter4312879.reachGoal('OpenContactManager');
		}
		$(elm).load(t[0] + '?' + t[1] + '&isNaked=0&show=1',function(data){
			$(show).fadeIn(100);
            sInit()
		});
		return false;
	});

	if($('.form_requestshow').length>0){
        t = $('.form_requestshow').data('url').split("?");
		$('.form_requestshow').load(t[0] + '?' + t[1] + '&isNaked=0&show=1',function(data){
			$(".form_requestshow [name=f_Phone]").mask("+7 (999) 999-99-99");
		});
	}
    var jan = 0;
	setInterval( function() { $( "input[name='f_Email']" ).attr("required","true"); $("#koli").remove(); $( "input[name='f_Email']" ).before('<span class="req" id="koli"></span>');} , 300)

	if(location.href=="http://www.skd-dom.ru/"){$(".photos").after('<div class="item_bottom_wrap" style="margin:15px 0 -35px 0">	<div class="item_bottom_left">	</div>	<div class="item_bottom_right">		<div data-url="/catalog/zakaz/" data-show="#parent_popup_form_detail_phone" data-href=".js-callback-wrap" class="bigsee js-openPopupButton see form_calculateproject_button request_plan_button">Посмотреть дома вживую</div></div></div>');}
});   


$(document).ready(function(){
	
		$('.main_subscribe_new a').click(function(){
			 $.ajax({
				type: "POST",
				url: "/subscribe_new.php",
				data: "user_mail="+$(".subscribe_new").val(),
				success: function(response){
					$('.subscribe_result').html(response);
				}
			});
			
			$(".subscribe_new").val('');
		});
}); 




$('.print_new').click(function(){
var line = $(".crumb_title").html();
var name = $(".content .inner h1").html();
var image = $(".content .inner img").attr('src');
var plan_img = $(".allplans .plan-itm table img").attr('src');
var plan_img2 = $(".allplans .plan-itm img").attr('src');
var etazh = $(".plan-itm").html();

  $.ajax({
        type: "POST",
        url: "/test.php",
        data: {line:line,name:name,image:image,plan_img:plan_img,etazh:etazh,plan_img2:plan_img2}
    }).done(function(result)
        {	
            $("#msg").html(result);
			console.log(result);
        });
});











 