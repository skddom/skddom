$(document).ready(function() {
    //$('.js-cnt:contains(Отзывы)').parent().after('<li><a data-cnt="0" class="js-cnt">Франчайзинг</a></li>');

    $('.js-cnt').each(function(i,el){
        var cnt = $(el).data('cnt');
        if (cnt>0) $(el).append(' ('+cnt+')');
    });
    if ($('.slider_projects li').length>1) {
        $('.slider_projects li:lt('+Math.floor((Math.random() * $('.slider_projects li').length)+1)+')').each(function(indx, element){
            $(element).appendTo('.slider_projects ul');
        });
    }
    $('#slider').rhinoslider({
        captionsOpacity: 1,
        controlsMousewheel: false,
        controlsKeyboard: false,
        showCaptions: 'always',
        showBullets: 'always',
        showControls: 'always',
        autoPlay: true,
        showTime: 4500,
        randomOrder: false,
        slidePrevDirection: 'toRight',
        slideNextDirection: 'toLeft'
    });
    $('.infoblock').hover(function(){
        $(".boxcaption", this).stop().animate({top:'0px'},{queue:false,duration:600});
    }, function() {
        $(".boxcaption", this).stop().animate({top:'100%'},{queue:false,duration:600});
    });
    
    //$('.fancybox').fancybox();    
    $('.js-level1').click(function(){
        if($(this).closest("li").hasClass("active")) return false;
        $this=$(this);
        $(".js-level1").each(function(i,el){
            if ($this.attr('href') != $(el).attr('href') && !$(el).closest("li").hasClass("active")) $(el).siblings('ul').hide();
        });
        $(this).siblings('ul').slideToggle('fast',function(){
            $(".content").css("min-height",$(".sticky").height()+"px");
            
        });
            
        /* $(".sticky-wrapper").css("height","");
        v ar heigh*t = $(".sticky-wrapper").height();
        $(".content").css("height",height+"px");*/
        return false;
    });
    
    $(".sticky").sticky({ topSpacing: 0, bottomSpacing: 120 });
    
    setEqualHeight($(".overflow .col3 > div"));
    $(".col3 .block:last-child").css({ height:"100%"});
    $('a[href="http://xn--k1abgb.com/lp/lp_ckd/index_v2.html"]').attr('target','_blank');
    $('a[href="http://xn--k1abgb.com/lp/lp_ckd/index_v2.html"]').attr('href','/franchising/index_v2.html');
});

function setEqualHeight(columns) {
    var tallestcolumn = 0;
    columns.each(function(){
        currentHeight = $(this).height();
        if(currentHeight > tallestcolumn) {
            tallestcolumn = currentHeight;
        }
    }
    );
    columns.height(tallestcolumn);
}

(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.0";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
        
        
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
        
ga('create', 'UA-6391226-3', 'auto');
ga('send', 'pageview');
        
/*Cufon.replace('.corbel_bold_italic', { fontFamily: 'Corbel Bold Italic', hover: true });
Cufon.replace('.corbel_italic', { fontFamily: 'Corbel Italic', hover: true });
Cufon.replace('.corbel_bold', { fontFamily: 'Corbel Bold', hover: true });
Cufon.replace('.corbel', { fontFamily: 'Corbel', hover: true });*/

$(function(){  
    $(".fakeSlider").each(function(i,el){
       if (i==0) {
          $(el).closest(".pager").prepend("<div id='i1' style='position:absolute;width:512px;height:370px;background:url("+$(el).data("src")+") no-repeat; background-size:cover;'></div><div id='i2'></div>");
         // $(el).closest(".infoblock").find(".pic").hide();
       } else {}
    });
    $(document).on({
       click: function(){
          $box = $(this).closest(".pager");
          $bg = "url('"+$(this).data("src")+"')";
          
          $box.css("background-image", $bg);
          $this = $(this);
          $("#i1").fadeOut(500,function(){
              $("#i1").css("background-image", $bg);
              $("#i1").show();
              //$box.css("background-image", "");
            
          });
              $('.circles li').removeClass('hover');
              $this.addClass('hover');            
          
          return false;
       }
    },".circles li");
    $(document).on({
        click: function(){
            id = parseInt($('.fakeSlider.hover').data('id')) + 1;
            if(id> ($('.fakeSlider').length-1) ) id=0;
            $('[data-id='+id+']').trigger('click');
            console.log(id);
            return false;
        }
    },".infoblocks .pager .next");
    $(document).on({
        click: function(){
            id = parseInt($('.fakeSlider.hover').data('id')) - 1;
            if (id<0) id = $('.fakeSlider').length-1;
            $('[data-id='+id+']').trigger('click');
            console.log(id);
            return false;
        }
    },".infoblocks .pager .prev");    
});


$(function(){
    $(document).on({
        click: function(){
            $(this).closest('div').find('ul').toggle();
            return false;
        }
    },'.js-ch-city');
    $(document).on({
        click: function(){
            $(this).closest('div').find('.js-ch-city').text($(this).text());
            $(this).closest('div').find('ul').toggle();

            var url = document.location.pathname;
            if (document.location.search.length<1) url = url + '?';
            url = url + document.location.search;
            if (document.location.search.length>1) url = url + '&';
            url = url + 'city=' + $(this).data('id');            
            top.location.href=url;
            return false;
        }
    },'.ch_city ul li a');    
    $(document).mouseup(function (e){ // ������� ����� �� ���-���������
        var div = $(".ch_city ul"); // ��� ��������� ID ��������
        if (!div.is(e.target) // ���� ���� ��� �� �� ������ �����
            && div.has(e.target).length === 0) { // � �� �� ��� �������� ���������
            div.hide(); // �������� ���
        }
    });    
});