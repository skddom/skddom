jQuery(document).ready(function($){
	//open popup
	$('.cd-popup-trigger').on('click', function(event){
		event.preventDefault();
		$('.cd-popup-one').addClass('is-visible');
        var id = $(this).parent().parent().parent().attr("id");
        $('.cd-popup-three').append("<input id='item_id' type='hidden' name='item_id' value='"+id+"'>");
	});
    $("#fio1, #fio2, #fio3, #fio4, #fio5, #fio6").on("keyup",function(e){
        e.preventDefault();
        if($(this).next().hasClass("error")) $(this).next().remove();
    })
	$("#call_time").on("keyup",function(e){
        e.preventDefault();
        if($(this).next().hasClass("error")) $(this).next().remove();
    })
    $("#email1, #email2, #email3, #email4, #email5, #email6, #text1, #text2, #text3, #text4, #text5, #text6").on("keyup",function(e){
        e.preventDefault();
        if($(this).next().hasClass("error")) $(this).next().remove();
    })
    $("input[name=\"mobile\"]").inputmask({ 'mask': '+7 (999) 999-99-99', 'placeholder' : 'x' });
    $("input[name=\"mobile\"]").on("keyup",function(e){
        e.preventDefault();
        if($(this).next().hasClass("error")) $(this).next().remove();
    })

	$('#submit1').on('click', function(event){
		event.preventDefault();
        var email = $("#email1").val();
        var text = $("#text1").val();
        var amount = $("#amount").val();
        var amount2 = $("#amount2").val();
        //var email = $("#email1").val();
        $(".error").remove();
        if(email == "" || email == undefined) {
            $("<p class=\"error\">Укажите ваш телефон/эл. почту.</p>").insertAfter("#email1");
            return false;
        }
        if(text == "" || text == undefined) {
            $("<p class=\"error\">Укажите ваше сообщение.</p>").insertAfter("#text1");
            return false;
        }
        // if(email == "" || email == undefined) {
            // $("<p class=\"error\">Укажите ваш e-mail.</p>").insertAfter("#email1");
            // return false;
        // }
        var _this = $(this);
        $.post("ajax.php?act=1", { email: email, text: text, amount: amount, amount2: amount2 })
            .done(function( data ) {
                $(_this).parent().parent().parent().html(data);
            });
	});
	
	$('#submit2').on('click', function(event){
		event.preventDefault();
        var email = $("#email2").val();
        var text = $("#text2").val();
        //var email = $("#email1").val();
        $(".error").remove();
        if(email == "" || email == undefined) {
            $("<p class=\"error\">Укажите ваш телефон/эл. почту.</p>").insertAfter("#email2");
            return false;
        }
        if(text == "" || text == undefined) {
            $("<p class=\"error\">Укажите ваше сообщение.</p>").insertAfter("#text2");
            return false;
        }
        // if(email == "" || email == undefined) {
            // $("<p class=\"error\">Укажите ваш e-mail.</p>").insertAfter("#email1");
            // return false;
        // }
        var _this = $(this);
        $.post("ajax.php?act=2", { email: email, text: text })
            .done(function( data ) {
                $("#form1").hide();
                $("#message1").show();
				
            });
	});
	$('#submit3').on('click', function(event){
		event.preventDefault();
        var email = $("#email3").val();
        var text = $("#text3").val();
        //var email = $("#email1").val();
        $(".error").remove();
        if(email == "" || email == undefined) {
            $("<p class=\"error\">Укажите ваш телефон/эл. почту.</p>").insertAfter("#email3");
            return false;
        }
        if(text == "" || text == undefined) {
            $("<p class=\"error\">Укажите ваше сообщение.</p>").insertAfter("#text3");
            return false;
        }
        // if(email == "" || email == undefined) {
            // $("<p class=\"error\">Укажите ваш e-mail.</p>").insertAfter("#email1");
            // return false;
        // }
        var _this = $(this);
        $.post("ajax.php?act=2", { email: email, text: text })
            .done(function( data ) {
                $("#form2").hide();
                $("#message2").show();
            });
	});	
	$('#submit4').on('click', function(event){
		event.preventDefault();
        var email = $("#email4").val();
        var text = $("#text4").val();
        //var email = $("#email1").val();
        $(".error").remove();
        if(email == "" || email == undefined) {
            $("<p class=\"error\">Укажите ваш телефон/эл. почту.</p>").insertAfter("#email4");
            return false;
        }
        if(text == "" || text == undefined) {
            $("<p class=\"error\">Укажите ваше сообщение.</p>").insertAfter("#text4");
            return false;
        }
        // if(email == "" || email == undefined) {
            // $("<p class=\"error\">Укажите ваш e-mail.</p>").insertAfter("#email1");
            // return false;
        // }
        var _this = $(this);
        $.post("ajax.php?act=2", { email: email, text: text })
            .done(function( data ) {
                $("#form3").hide();
                $("#message3").show();
            });
	});
	$('#submit5').on('click', function(event){
		event.preventDefault();
        var email = $("#email5").val();
        var text = $("#text5").val();
        //var email = $("#email1").val();
        $(".error").remove();
        if(email == "" || email == undefined) {
            $("<p class=\"error\">Укажите ваш телефон/эл. почту.</p>").insertAfter("#email5");
            return false;
        }
        if(text == "" || text == undefined) {
            $("<p class=\"error\">Укажите ваше сообщение.</p>").insertAfter("#text5");
            return false;
        }
        // if(email == "" || email == undefined) {
            // $("<p class=\"error\">Укажите ваш e-mail.</p>").insertAfter("#email1");
            // return false;
        // }
        var _this = $(this);
        $.post("ajax.php?act=2", { email: email, text: text })
            .done(function( data ) {
                $("#form4").hide();
                $("#message4").show();
            });
	});
	$('#submit6').on('click', function(event){
		event.preventDefault();
        var email = $("#email6").val();
        var text = $("#text6").val();
        //var email = $("#email1").val();
        $(".error").remove();
        if(email == "" || email == undefined) {
            $("<p class=\"error\">Укажите ваш телефон/эл. почту.</p>").insertAfter("#email6");
            return false;
        }
        if(text == "" || text == undefined) {
            $("<p class=\"error\">Укажите ваше сообщение.</p>").insertAfter("#text6");
            return false;
        }
        // if(email == "" || email == undefined) {
            // $("<p class=\"error\">Укажите ваш e-mail.</p>").insertAfter("#email1");
            // return false;
        // }
        var _this = $(this);
        $.post("ajax.php?act=3", { email: email, text: text })
            .done(function( data ) {
                $("#form5").hide();
                $("#message5").show();
            });
	});
	$('#submit7').on('click', function(event){
		event.preventDefault();
        var email = $("#email7").val();
        var text = $("#text7").val();
        //var email = $("#email1").val();
        $(".error").remove();
        if(email == "" || email == undefined) {
            $("<p class=\"error\">Укажите ваш телефон</p>").insertAfter("#email7");
            return false;
        }
        if(text == "" || text == undefined) {
            $("<p class=\"error\">Укажите ваше сообщение</p>").insertAfter("#text7");
            return false;
        }
        // if(email == "" || email == undefined) {
            // $("<p class=\"error\">Укажите ваш e-mail.</p>").insertAfter("#email1");
            // return false;
        // }
        var _this = $(this);
        $.post("ajax.php?act=4", { email: email, text: text })
            .done(function( data ) {
                $(_this).parent().parent().parent().html(data);
            });
	});
	$('#submit8').on('click', function(event){
		event.preventDefault();
        var email = $("#email8").val();
        var text = $("#text8").val();
        //var email = $("#email1").val();
        $(".error").remove();
        if(email == "" || email == undefined) {
            $("<p class=\"error\">Укажите ваш телефон/эл. почту</p>").insertAfter("#email8");
            return false;
        }
        if(text == "" || text == undefined) {
            $("<p class=\"error\">Укажите ваше сообщение</p>").insertAfter("#text8");
            return false;
        }
        // if(email == "" || email == undefined) {
            // $("<p class=\"error\">Укажите ваш e-mail.</p>").insertAfter("#email1");
            // return false;
        // }
        var _this = $(this);
        $.post("ajax.php?act=5", { email: email, text: text })
            .done(function( data ) {
                $(_this).parent().parent().parent().html(data);
            });
	});
	$('#submit9').on('click', function(event){
		event.preventDefault();
        var fio = $("#fio2").val();
        var mobile = $("#mobile2").val();
        var email = $("#email2").val();
        $(".error").remove();
        if(mobile == "" || mobile == undefined) {
            $("<p class=\"error\">Укажите ваш телефон.</p>").insertAfter("#mobile2");
            return false;
        }
        if(fio == "" || fio == undefined) {
            $("<p class=\"error\">Укажите ваше имя.</p>").insertAfter("#fio2");
            return false;
        }
        if(email == "" || email == undefined) {
            $("<p class=\"error\">Укажите ваш e-mail.</p>").insertAfter("#email2");
            return false;
        }
        var _this = $(this);
        $.post("ajax.php?act=2", { fio: fio, mobile: mobile, email: email })
            .done(function( data ) {
                $(_this).parent().parent().html(data);
            });
	});
	$('#submit10').on('click', function(event){
		event.preventDefault();
        var fio = $("#fio3").val();
        var mobile = $("#mobile3").val();
        var email = $("#email3").val();
        $(".error").remove();
        if(mobile == "" || mobile == undefined) {
            $("<p class=\"error\">Укажите ваш телефон.</p>").insertAfter("#mobile3");
            return false;
        }
        if(fio == "" || fio == undefined) {
            $("<p class=\"error\">Укажите ваше Имя.</p>").insertAfter("#fio3");
            return false;
        }
        if(email == "" || email == undefined) {
            $("<p class=\"error\">Укажите ваш e-mail.</p>").insertAfter("#email3");
            return false;
        }
        var _this = $(this);
        $.post("ajax.php?act=3", { fio: fio, mobile: mobile, email: email })
            .done(function( data ) {
                $(_this).parent().parent().html(data);
            });
	});
	$("#button1").on("click",function(e){
		e.preventDefault();
		$(this).parent().parent().hide();
		$("#form1").show();
	})
	$("#button2").on("click",function(e){
		e.preventDefault();
		$(this).parent().parent().hide();
		$("#form2").show();
	})
	$("#button3").on("click",function(e){
		e.preventDefault();
		$(this).parent().parent().hide();
		$("#form3").show();
	})
	$("#button4").on("click",function(e){
		e.preventDefault();
		$(this).parent().parent().hide();
		$("#form4").show();
	})
	$("#button5").on("click",function(e){
		e.preventDefault();
		$(this).parent().parent().hide();
		$("#form5").show();
	})

	//close popup
	$('.cd-popup').on('click', function(event){
		if( $(event.target).is('.cd-popup-close') || $(event.target).is('.cd-popup') ) {
			event.preventDefault();
			$(this).removeClass('is-visible');
		}
	});
	//close popup when clicking the esc keyboard button
	$(document).keyup(function(event){
    	if(event.which=='27'){
    		$('.cd-popup').removeClass('is-visible');
	    }
    });
	
	
});