function lightbox_print() {
        var $this = lightbox;
        var currentImage = $this.album[$this.currentImageIndex];
        $('#lightboxPrintFrame').remove();
        var iframe = $('<iframe/>', {id:'lightboxPrintFrame', name:'lightboxPrintFrame', src:'about:blank'});
        iframe.load(function(e){
            var ifr = iframe.get(0);
            var idoc = ifr.contentDocument || ifr.contentWindow.document;
            var iwin = ifr.contentWindow || ifr.contentDocument.defaultView;
            
            var body = idoc.getElementsByTagName('body')[0];
            var img = idoc.createElement('img');
            img.setAttribute('src', currentImage.link);
            body.appendChild(img);
            
            if (currentImage.hasOwnProperty('title') && currentImage.title) {
                var div = idoc.createElement('div');
                div.innerHTML = currentImage.title;
                body.appendChild(div);
            }
            
            setTimeout(function(){
                iwin.focus();
                window.frames['lightboxPrintFrame'].print();
            }, 500);
        }).appendTo('body:eq(0)');
    
}

function initSliders() {
  $('.bxslider').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    fade: true,
    asNavFor: '#bx-pager'
  });
  $('#bx-pager').slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    asNavFor: '.bxslider',
    dots: false,
    centerMode: false,
    focusOnSelect: true,
    arrows: true
  });
}
function initSliders2() {
  $('.bxslider').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    fade: true,
    asNavFor: '#bx-pager'
  });
  $('#bx-pager').slick({
    slidesToShow: 6,
    slidesToScroll: 3,
    asNavFor: '.bxslider',
    dots: false,
    centerMode: false,
    focusOnSelect: true,
    arrows: true
  });
}

function setFlag(el, flag) {
    var url = el.closest("table").data("url");
    var id = el.closest("tr").data("id");
    $.get(url, {id: id, flag: flag, isNaked: 1});
}

$(function(){
    $(document).on({
        click: function(){
            $id = $(this).closest("tr").data("id");
            $("[data-id="+$id+"]").fadeOut();
            setFlag($(this), "skip");
            return false;
        }
    },"[data-skip]");
    $(document).on({
        click: function(){
            $id = $(this).closest("tr").data("id");
            $("[data-id="+$id+"]").fadeOut();
            setFlag($(this), "done");
            return false;
        }
    },"[data-done]");    
    $(document).on({
        click: function() { lightbox_print(); return false; }
    },".lb-print");
    $(document).on({
       click: function(){
          $form = $(this).closest("form");
          $.post($form.attr("action")+"?isNaked=1&set=notify",$form.serialize());
       }
    },".js-notify");
    $(document).on({
       click: function(){
           $($(this).data("target")).toggle();
           return false;
       }
    },".js-expand");
    $(document).on({
        click: function(){
            var target = $(this).data("target");
            var url = $(this).data("load1");
            url = url.split("?");
            if (url[1] == undefined) url[1] = "";
            url = url[0] + "?" + url[1] + "&isNaked=1";
            $this = $(this);
            $(target).load(url,function(){
                eval($this.data("callback"));
            });
            return false;
        }
    },"[data-load1]");
    
    $("body").prepend("<div class='js-callback-wrap'></div>");
    
    /*$(document).on({
        click: function(){
          var elm = $(this).data('href');
          var show = $(this).data('show');
          var url = $(this).data('show-popup')
          url = url.split('?');
          url = url[0] + '?' + url[1] + '&isNaked=1&show=1'
          $(elm).load(url,function(data){
              cleanForm=data;
              $(show).fadeIn(100);
          });
          return false;        
        }
    },".js-openPopup");
    $(document).on({
        submit: function(){
          $form = $(this);
          $.post($form.attr('action'), $form.serialize()+'&isNaked=1&ajax=1', function(data){
              $('.jsCB-wrap').html(data);
          })
          return false;        
        }
    },".js-callback");  */
});