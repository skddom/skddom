<?
//ob_start();


//include_once ("../vars.inc.php");
//require ($INCLUDE_FOLDER."index.php");

//echo $NETCAT_FOLDER; die();

//// content here /////


/*
?><!DOCTYPE html>
<html lang="en">
<head>
	
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<title>Калькулятор</title>
	<meta name="description" content="">
	<meta name="imagetoolbar" content="no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="msthemecompatible" content="no">
	<meta name="cleartype" content="on">
	<meta name="HandheldFriendly" content="True">
	<meta name="format-detection" content="telephone=no">
	<meta name="format-detection" content="address=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	
</head>
<body><?
*/
$popups = true; //$_REQUEST['popups'];
	?><style>body .content-block{background: #f4f4f4;}body .st-sidebar-container, body  .st-sidebar-pusher, body  .st-sidebar-content{height:auto;}</style><link rel="stylesheet" href="/calc/css/style.css"><div id="calculator">

		<div class="calculator">
			<div class="widget_title">Как рассчитать площадь и стоимость дома</div>
<div class="instructions">
        <div class="swiper-container">
          <div class="swiper-wrapper">
            <div class="swiper-slide">
              <div class="info" data-num="1">
                <div class="img" style="background-image:url('images/info_1.png');"></div>
                <div class="text">Регулируя бегунок, выбирайте необходимый размер помещений, убирайте или
                  добавляйте их</div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="info" data-num="2">
                <div class="img" style="background-image:url('images/info_2.png');"></div>
                <div class="text">Удобная визуализация меблированных комнат поможет определиться с площадью</div>
              </div>
            </div>
            <div class="swiper-slide">
              <div class="info" data-num="3">
                <div class="img" style="background-image:url('images/info_3.png');"></div>
                <div class="text">После заполнения форм, сервис рассчитает общую площадь дома и цену строительства</div>
              </div>
            </div>
          </div>
          <div class="swiper-pagination"></div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-button-next"></div>
        </div>
      </div>
			<div class="cols">
				<div class="left_col"><?
echo s_list_class(493,634,"",true);

				?></div>
				<div class="right_col">
					<div class="room_preview" id="room_preview" style="margin-top:59px;">
						<div class="room_title">&nbsp;</div>
						<img class="room_img">
						<div class="total_info">
							<div class="group only-d ">
								<div class="field" data-first_storey_area="1">
									<div class="title medium">площадь 1 этажа</div>
									<div class="value">
										<span></span>
										<div class="suffiex">м<sup>2</sup></div>
									</div>
								</div>
								<div class="field" data-second_storey_area="1">
									<div class="title medium">площадь 2 этажа</div>
									<div class="value">
										<span></span>
										<div class="suffiex">м<sup>2</sup></div>
									</div>
								</div>
							</div>
<div class="group only-m">
  <div class="field big" data-area="1">
    <div class="title medium">общая площадь 1 и 2 этажей</div>
    <div class="value">
      <span></span>
      <div class="suffiex">м<sup>2</sup></div>
    </div>
  </div>
</div>
						</div>
					</div>
          			<div id="fixed_sidebar">
            <div class="sidebar_wrapper">
              <div class="cols">
                <div class="left_col">
                  <div class="title">общая площадь 1 и 2 этажей</div>
                  <div class="total_info">
                    <div class="field big" data-area="1">
                      <div class="value">
                        <span></span>
                        <div class="suffiex">м<sup>2</sup></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="right_col">
                  <div class="buttons">
                    <div class="btn large green wide" data-action="show_results">Рассчитать стоимость</div>
                    <div class="btn large white wide" data-action="reset">Сбросить параметры</div>
                  </div>
                </div>
              </div>
            </div>
				</div>
				</div>
			</div>
		</div>
		<div class="result" id="results">
			<div class="cols">
				<div class="left_col">
					<div class="house_params">
						<div class="big_title">Комплектация</div>
						<div class="params_cols">
							<div class="options" id="price_options">
								<div class="group" id="foundation">
									<div class="title">Фундамент</div>
									<div class="option">
										<input id="radio_1" type="radio" name="radio_group_1" data-price="<?=$cc_settings['fund1']?>"
										 data-default_check="0">
										<label for="radio_1">Свайно-ростверковый</label>
									</div>
									<div class="option">
										<input id="radio_2" type="radio" name="radio_group_1" data-price="<?=$cc_settings['fund2']?>"
										 data-default_check="1" checked>
										<label for="radio_2">Свайно-ростверковый с ж/б плитой</label>
									</div>
								</div>
								<div class="group" id="wall_panel">
									<div class="title">Стеновой брус</div>
									<div class="option">
										<input id="radio_3" type="radio" name="radio_group_2" data-first_storey_price="<?=$cc_settings['brus1']?>"
										 data-second_storey_price="<?=$cc_settings['brus2']?>" data-default_check="0">
										<label for="radio_3">160х185 мм</label>
									</div>
									<div class="option">
										<input id="radio_4" type="radio" name="radio_group_2" data-first_storey_price="<?=$cc_settings['brus3']?>"
										 data-second_storey_price="<?=$cc_settings['brus4']?>" data-default_check="1" checked>
										<label for="radio_4">200х185 мм</label>
									</div>
								</div>
								<div class="group" id="roof_covering">
									<div class="title">Кровельное покрытие</div>
									<div class="option">
										<input id="radio_5" type="radio" name="radio_group_3" data-price="<?=$cc_settings['krov1']?>"
										 data-default_check="1" checked>
										<label for="radio_5">Металлочерепица</label>
									</div>
									<div class="option">
										<input id="radio_6" type="radio" name="radio_group_3" data-price="<?=$cc_settings['krov2']?>"
										 data-default_check="0">
										<label for="radio_6">Мягкая кровля</label>
									</div>
								</div>
							</div>
							<div class="house_img">
								<div class="roof"></div>
								<div class="storey" id="second_storey_height">
									<div class="green_borders">
										<div class="top_border"></div>
									</div>
									<div class="storey_height">
										<div class="text">Высота аттиковой стены</div>
										<label class="spinner_wrapper">
											<input class="spinner" type="text" value="1.7" data-default_value="1.7"
											 data-storey_height="1">
											<div class="suffiex">м<sup>2</sup></div>
										</label>
									</div>
								</div>
								<div class="floor_partition"></div>
								<div class="storey" id="first_storey_height">
									<div class="green_borders">
										<div class="top_border"></div>
									</div>
									<div class="storey_height">
										<div class="text">Высота первого этажа</div>
										<label class="spinner_wrapper">
											<input class="spinner" type="text" value="2.5" data-default_value="2.5"
											 data-storey_height="1">
											<div class="suffiex">м<sup>2</sup></div>
										</label>
									</div>
								</div>
								<div class="bottom_floor"></div>
<img class="first_storey only-m" src="/calc/images/house_1_storey.png">
<img class="second_storey only-m" src="/calc/images/house_2_storey.png">
							</div>
						</div>
					</div>
				</div>
				<div class="right_col">
					<div class="total_info">
						<div class="big_title">Расчет площади</div>
						<div class="group">
							<div class="field" data-first_storey_area="1">
								<div class="title medium">площадь 1 этажа</div>
								<div class="value">
									<span></span>
									<div class="suffiex">м<sup>2</sup></div>
								</div>
							</div>
							<div class="field" data-second_storey_area="1">
								<div class="title medium">площадь 2 этажа</div>
								<div class="value">
									<span></span>
									<div class="suffiex">м<sup>2</sup></div>
								</div>
							</div>
						</div>
						<div class="field" data-area="1">
							<div class="title">Общая площадь по экспликации</div>
							<div class="value">
								<span></span>
								<div class="suffiex">м<sup>2</sup></div>
							</div>
						</div>
						<div class="field" data-building_area="1">
							<div class="title">Общая строительная площадь*</div>
							<div class="value">
								<span></span>
								<div class="suffiex">м<sup>2</sup></div>
							</div>
							<div class="descr">*Площадь по осям стен, включая террасы</div>
						</div>
						<div class="warning" id="warning">
							<span class="red">Внимание!</span> <span data-second_light="1">С учетом второго света, жилая</span>
              <span data-second_light="0">Жилая</span> площадь второго этажа больше площади первого на
							<span
							 class="value">
								<span>26</span> м<sup>2</sup></span>!</div>
					</div>
				</div>
			</div>
			<div class="total_price">
				<div class="cols">
					<div class="left_col">
						<div class="text">Предварительная стоимость теплового контура</div>
						<div class="border"></div>
						<div class="price" id="price">&nbsp;</div>
					</div>
					<div class="right_col"><?
                    if ($popups) {
                        ?><div class="btn large green wide" id="send_house">Отправить мне на почту</div><?
                    } else {
						?><div class="btn large green wide"><a data-action="/calc/save.html" data-show="#parent_popup_form_detail_phone" data-href=".js-callback-wrap" class="js-openPopup js-callback js-sendCalc">Отправить мне на почту</a></div><?
                    }    
			?></div>
				</div>
				<div class="bottom_text">Вы также можете <?
                if($popups) {
                    ?><span id="send_request">отправить заявку менеджеру</span><?
                } else {
                   ?><a class="js-openPopup js-callback" data-action="/callback1/" data-show="#parent_popup_form_detail_phone" data-href=".js-callback-wrap"  href="#" target=" _blank">отправить заявку менеджеру</a><?
                }
				?>	 на проектирование</div>
			</div>
		</div>
<div id="mobile_or_desktop"></div>
<?
        if ($popups) {
        ?><div class="popup_wrapper" id="send_house_popup">
      <div class="popup">
        <div class="close"></div><div data-load="/calc/save.html">
        <form>
          <div class="title">Получите проект на почту</div>
          <div class="field">
            <input type="text" placeholder="Ваше имя" name="name">
          </div>
          <div class="field">
            <input type="text" placeholder="Ваш email" name="email">
          </div>
          <button class="btn large green" type="submit">Отправить</button>
          <div class="agreement">Нажав на кнопку, Вы принимаете условия
            <a href="#" target="_blank">Пользовательского соглашения.</a>
          </div>
        </form></div>
      </div>
    </div>
    <div class="popup_wrapper" id="send_request_popup">
      <div class="popup">
        <div class="close"></div><div data-load="/callback1/?send_request_popup">
        <form>
          <div class="title">Отправьте заявку менеджеру</div>
          <div class="field">
            <input type="text" placeholder="Ваше имя" name="name">
          </div>
          <div class="field">
            <input type="text" placeholder="Ваш телефон" name="phone">
          </div>
          <div class="field">
            <input type="text" placeholder="Ваш email" name="email">
          </div>
          <button class="btn large green" type="submit">Отправить</button>
          <div class="agreement">Нажав на кнопку, Вы принимаете условия
            <a href="/policy-personal-data/" target="_blank">Пользовательского соглашения.</a>
          </div>
        </form></div>
      </div>
    </div>
    <div class="popup_wrapper" id="message_popup">
      <div class="popup">
        <div class="close"></div>
        <div class="title"></div>
      </div>
     </div><?
        }
?>
	</div>
<script>
    $(function(){
        $(document).on({
            submit: function(){
                
                    $data = $("<input/>");
                    $data.attr("type","hidden").attr("name","f_Data").val(JSON.stringify(email_data));
                    $("#adminForm").append($data);                
               
            }
        },".js-sendCalcForm");
    });
    </script>    
<script>
function reloadProjects(area){
  $("#rekom").slideUp(100,function(){
      $("#rekom").load("/catalog/by-area/?isNaked=1&area="+area,function(){
          $("#rekom").slideDown(300);
      });
  });
}
$(function(){
    $("#results").append("<div class=container><div class=vc_row id=rekom></div></div>");
});
</script>    
	<script src="/calc/js/libraries.min.js"></script>
	<script src="/calc/js/calculator.js"></script><?
    /*
?></body>
</html><?

$nc_result_msg = ob_get_clean();

if ($File_Mode) {
    require_once $INCLUDE_FOLDER.'index_fs.inc.php';

    echo $template_header;
    echo $nc_result_msg;
    echo $template_footer;
} else {
    eval("echo \"".$template_header."\";");
    echo $nc_result_msg;
    eval("echo \"".$template_footer."\";");
}*/
?>