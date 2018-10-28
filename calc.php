<?php
/*
 * Калькулятор для сайта SKD-DOM.RU
 * Автор: Александр В. Кузнецов
 */
 
include "vars.inc.php";
$passed_thru_404=true;
require ($INCLUDE_FOLDER."index.php");
$house_id = isset($_POST['house']) ? $_POST['house'] : 0;
$data = array("house_id"=>$house_id);
$data = $db->get_row("select * from Message210 where house_id=".intval($data['house_id']),ARRAY_A);

$br = CBranding::get_object();
include_once ($DOCUMENT_ROOT.$br->current['Projects']);

//include_once 'base.php'; // массив данных по всем домам
?>

<!--<script type="text/javascript" src="http://www.skd-dom.ru/images/skd-new/js/jquery-1.8.3.min.js"></script>-->
<script>
    // Калькуляция
    $('document').ready(function(){
		$('input').click(function () {
			calc();
		});
		$('#frmCalc').submit(function () {
			calc();
			return false;
		});

		$('.notice_wrapper').on('click', '.button', function(){
			$(this).next('.notice_inner').css('display', 'block');
		}).on('click', '.close_btn', function(){
			$(this).closest('.notice_inner').css('display', 'none');
		});
	});
    
    /* 
     * Расчет калькуляций
     */
    function calc() {
		summa1 = 0 // сумма комплектации 1
		summa2 = 0 // сумма комплектации 2
		summa3 = 0 // сумма комплектации 2

			for (i=0; i<=25; i++) {
				id_price = '#price_' + i;
				price = $(id_price).val();
				if ( price !== 'undefined') {
					id_chk3 = '#chk3_' + i;
					if($(id_chk3).prop("checked")) summa3 += price*1;
				}
			}
			
		// $('#c1').html('<b>' + number_format(Math.round(summa1, 0), {decimals: 0, thousands_sep: " "}) + ' pублей</b>');
		// $('#c2').html('<b>' + number_format(Math.round(summa2, 0), {decimals: 0, thousands_sep: " "}) + ' pублей</b>');
		$('#ind').html('<b>' + number_format(Math.round(summa3, 0), {decimals: 0, thousands_sep: " "}) + ' pублей</b>');
		$('.js-b3').html('' + number_format(Math.round(summa3, 0), {decimals: 0, thousands_sep: " "}) + ' руб.');
	return;
    }
	
	// форматирование чисел по разрядности
	function number_format(_number, _cfg){
		function obj_merge(obj_first, obj_second){
			var obj_return = {};
			for (key in obj_first){
				if (typeof obj_second[key] !== 'undefined') obj_return[key] = obj_second[key];
				else obj_return[key] = obj_first[key];
			}
			return obj_return;
		}
		function thousands_sep(_num, _sep){
			if (_num.length <= 3) return _num;
			var _count = _num.length;
			var _num_parser = '';
			var _count_digits = 0;
			for (var _p = (_count - 1); _p >= 0; _p--){
				var _num_digit = _num.substr(_p, 1);
				if (_count_digits % 3 == 0 && _count_digits != 0 && !isNaN(parseFloat(_num_digit))) _num_parser = _sep + _num_parser;
				_num_parser = _num_digit + _num_parser;
				_count_digits++;
			}
			return _num_parser;
		}
		if (typeof _number !== 'number'){
			_number = parseFloat(_number);
			if (isNaN(_number)) return false;
		}
		var _cfg_default = {
			before: '',
			after: '',
			decimals: 2,
			dec_point: '.',
			thousands_sep: ','
		};
		if (_cfg && typeof _cfg === 'object'){
			_cfg = obj_merge(_cfg_default, _cfg);
		}
		else _cfg = _cfg_default;
		_number = _number.toFixed(_cfg.decimals);
		if(_number.indexOf('.') != -1){
			var _number_arr = _number.split('.');
			var _number = thousands_sep(_number_arr[0], _cfg.thousands_sep) + _cfg.dec_point + _number_arr[1];
		}
		else var _number = thousands_sep(_number, _cfg.thousands_sep);
		return _cfg.before + _number + _cfg.after;
	}	
</script>

<?php


if (preg_match('/[a-zA-Z]+/', $house_id)) {
    echo "ID дома должен быть цифрой<br />";
    return;
}
if (!isset($arrSale[$house_id])) {
    echo "Неверный ID дома<br />";
    return;
}

$dir = $data['SliderPath'];
$img = $dir.$data['MainImgPath'];

$arPrice = getPriceData($house_id);

?>
<!--
<link rel="stylesheet" type="text/css" href="/calc.css">

<div style=" border:1px solid #ff0000; text-align:center; font-size:14px; padding:10px; margin-bottom:5px;">

<b style="color:#ff0000">ВНИМАНИЕ!</b> <br />
Прекращает свое действия АКЦИИ «Комплектация «С2» - мягкая кровля в подарок».<br />
С 17 марта 2014 года в стоимость комплектации «С2» будет включена кровля из метало черепицы и пластиковая водосточная система.<br />
</div>
-->

<div class='row top_project_info'>
    <div class='col-md-9'>
        <div class="img">
            <img class='img-responsive' src='<?=$img?>'>
<?php
$nachnets = date("d-m-Y", strtotime("next monday"));

if($house_id>=200 && $house_id<=254)
$end = date("d-m-Y", strtotime("+80 day next Monday"));
else if($house_id>=263 && $house_id<=352)
$end = date("d-m-Y", strtotime("+90 day next Monday"));
else if($house_id>=360 && $house_id<=415)
$end = date("d-m-Y", strtotime("+100 day next Monday"));
else $end = date("d-m-Y", strtotime("+80 day next Monday"));
?>
<style>
.title.yellow_title {
    color: #b76c00;
}
.calc2 .clue-bottom a.btn.btnico1 {padding-top:14px;padding-bottom:14px;}
.calc2 .clue-bottom a.btn.btnico2 {padding-top:20px;padding-bottom:20px;}
.calc2 .clue-bottom a.btn:last-child {padding-top:23px;padding-bottom:23px;}
.calc2 .clue-bottom a.btn.mgt-button {text-align:left;padding-left:10px;}
.calc2 .mgt-button i.fa:before {content:"←";font-size:22px;line-height:0px;position:relative;top:3px;}
</style>
<div class="nachnets"> 
	<p style="">Заключая с нами договор на этой неделе,</p>
	<p>строительство Вашего дома начнется <span class="text-bold"><?php echo $nachnets?></span> </p>
	<p>а готов он будет уже <span class="text-bold"><?php echo $end?></span></p>
</div>            
        </div>
    </div>
    <div class='col-md-3'>
        <div class='row'>
        <div class='clue-bottom'>
			<div class="right_short_project_info">
				<div class="col">
					<i class="svg-icon svg-cub_meters"></i>
					<div class="inner_text">
						<div class="title">Площадь</div>
						<b><?=intval($house_id)?> М<sup>2</sup></b>
					</div>
				</div>
				<div class="col">
					<i class="svg-icon svg-basic_price"></i>
					<div class="inner_text">
						<div class="title">Тепловой контур</div>
						<b><?=number_format($arPrice['set1']['discount_price'],0,' ',' ')?> руб</b>
					</div>
				</div>
				<div class="col">
					<i class="svg-icon svg-basic_price"></i>
					<div class="inner_text">
						<div class="title ">+Инжерные сети</div>
						<b><?=number_format($arPrice['set2']['discount_price'],0,' ',' ')?> руб</b>
					</div>
				</div>
				<div class="col">
					<i class="svg-icon svg-basic_price"></i>
					<div class="inner_text">
						<div class="title ">+Черновая отделка</div>
						<b><?=number_format($arPrice['set3']['discount_price'],0,' ',' ')?> руб</b>
					</div>
				</div>  
				<div class="col">
					<i class="svg-icon svg-basic_price"></i>
					<div class="inner_text">
						<div class="title yellow_title">Индивидуальная комплектация</div>
						<b><span class="js-b3"></span> руб</b>
					</div>
				</div>                 
			</div>
		</div>
		</div>
	</div>

	<div class='col-md-3 project_bottom_buttons'>
        <div class='row'>
			<div class='clue-bottom'>
            <a class="btn btnico1 hvr-bounce-to-top mgt-button mgt-style-bordered mgt-size-normal mgt-align-left mgt-display-inline mgt-text-size-normal mgt-button-icon-position-left mgt-text-transform-uppercase " href="" target=" _blank">Отправить комплектацию <br>мне на email </a>
            <a class="btn btnico2 hvr-bounce-to-top mgt-button mgt-style-bordered mgt-size-normal mgt-align-left mgt-display-inline mgt-text-size-normal mgt-button-icon-position-left mgt-text-transform-uppercase " href="" target=" _blank">Запросить смету </a>
            <a class="btn mgt-button mgt-style-solid-invert mgt-size-normal mgt-align-left mgt-display-inline mgt-text-size-normal mgt-button-icon-position-left mgt-text-transform-uppercase " href="<?=$url?>" target=" _blank"><i class="fa"></i>Вернуться к описанию </a>
        	</div>
        </div>
        </div>
    </div>
</div>

		  <!--<div class="spec">
		  <i></i>
		  <div class="head-calc">
	  		<h2>СКД-<?php /*echo $house_id*/?></h2>
		  <table border="0" width="100%" class="prices">
			  <tbody>
			  <tr class="border-bot">
				  <td>Стоимость в комплектации «С1»:</td>
				  <td width="30%" align="right"><span id="c1"><?php /*echo $arrSale[$house_id]['c1']; */?></span></td>
			  </tr>
			  <tr class="border-bot">
				  <td>Стоимость в комплектации «С2»:</td>
				  <td width="30%" align="right"><span id="c2"><?php /*echo $arrSale[$house_id]['c2']; */?></span></td>
			  </tr>
			  <tr>
				  <td><b>Стоимость в комплектации «ИНДИВИД»:</b><br>
					  <span class="no-trans"> (измените опции в калькуляторе)</span></td>
				  <td width="30%" align="right"><span id="ind"><b>(измените опции в калькуляторе)</b></span></td>
			  </tr>
			  </tbody>
		  </table>
		  </div>
		  </div>-->
		  
	<?php/*	  
<div id="head-calc">
	<h2>СКД-<?php echo $house_id?></h2>
	<table border="0" >
		<tr><td width="300"><b>Стоимость в комплектации «С1»:</b></td><td><b><span id="c1" class="no-trans"><?php echo $arrSale[$house_id]['c1'];?></span></b></td></tr>
		<tr><td><b>Стоимость в комплектации «С2»:</b></td><td><b><span id="c2" class="no-trans"><?php echo $arrSale[$house_id]['c2'];?></span></b></td></tr>
		<tr><td><b>Стоимость в комплектации «ИНДИВИД»:</b></td><td><b><span id="ind" class="no-trans"></span></b><span class="no-trans"> (измените опции в калькуляторе)</span></td></tr>
	</table>
</div>*/?>
<div class="vc_row wpb_row vc_row-fluid about_notices">
	<div class="wpb_column vc_column_container vc_col-sm-4">
		<div class="vc_column-inner">
			<div class="icon checkbox_icon"></div>
			<div class="title">Комплектация и цена</div>
			<div class="text">Выберайте или отключайте любой пункт комплктации. При этом, стоимость будет автоматически пересчитываеться.</div>
		</div>
	</div>


	<div class="wpb_column vc_column_container vc_col-sm-4">
		<div class="vc_column-inner">
			<div class="icon">?</div>
			<div class="title">Комплектация и цена</div>
			<div class="text">Кликните на специальный знак вопроса что бы получить подробный данные по работ.</div>
		</div>
	</div>


	<div class="wpb_column vc_column_container vc_col-sm-4">
		<div class="vc_column-inner">
			<div class="icon">!</div>
			<div class="title">Ограничения</div>
			<div class="text">Кликните на специальный знак “!” что бы получить подробные данные по ограничениям.</div>
		</div>
	</div>

</div>

          <div class="info">
            <div class="inner">
				<div class="mgt-header-block text-black wpb_content_element mgt-header-block-style-1 mgt-header-texttransform-header">
					<h2 class="mgt-header-block-title" id="titlecalc">«ТЕПЛОВОЙ КОНТУР» БАЗОВЫЙ СОСТАВ РАБОТ И МАТЕРИАЛОВ</h2>
					<div class="mgt-header-line"></div>
				</div>

<form name="frmCalc" id="frmCalc" action="#" method="post">
<table class="calc-table">
    <thead>
      <tr>
			<th class="th1" style="width: 250px; padding: 10px 12px !important;" rowspan="3">этап работы</th>
			<th class="th1" style="" rowspan="3">Описание работы</th>
			<th class="th1" style="width: 160px;text-align:center" rowspan="3">КОМПЛЕКТАЦИЯ<br>«С1»</th>
			<th class="th1" style="width: 160px;text-align:center" rowspan="3">КОМПЛЕКТАЦИЯ<br>«ИНДИВИД»</th>
			<!--<th class="border-none c1 whitebg">КОМПЛЕКТАЦИЯ</th>
			<th class="border-none c2 whitebg">КОМПЛЕКТАЦИЯ</th>
		  <th class="border-none c3 whitebg">КОМПЛЕКТАЦИЯ</th>-->
        </tr>
      <!--<tr>
        <th class="th-c1 border-bold">«С1»</th>
        <th class="th-c2 border-bold"><a title="Тепловой контур - базовый состав работ" style="display:block; color:#fff;" href="/catalog/c2/">«С2»</a></th>
        <th class="th-ind border-bold">«ИНДИВИД»</th>
      </tr>-->
    </thead>
    <tbody>
		<tr>
			<td rowspan="3" class="border-bold razdel">Проектирование</td>
			<td class="hover_tooltip">
				<div class="notices_wrapper">
					<div class="notices">
						<div class="notice_wrapper">
							<div class="button">?</div>
							<div class="notice_inner">
								<span class="close_btn">х</span>
								<div class="text">
									<div class="title">Внимание</div>
									В целях построения единого плана работ по проектированию гражданского строительства.
								</div>
							</div>
						</div>

						<div class="notice_wrapper">
							<div class="button">!</div>
							<div class="notice_inner">
								<span class="close_btn">х</span>
								<div class="text">
									<div class="title">Внимание</div>
									В целях построения единого плана работ по проектированию гражданского строительства.
								</div>
							</div>
						</div>
					</div>
				</div>

				Проектирование индивидуальной планировки

				<div class="tooltip_wrapper">
					<div class="tooltip_inner">
						<p><img src="http://www.skd-dom.ru/files/Cvet/1/main2.jpg" alt=""></p>
						<p>По гражданскому строительству охватывали проектные работы для сверхлимитного индивидуального, типового и нижелимитного типового строительства программы местных проектных контор (автономных республик, краевых и областных) охватывали проектные.</p>
					</div>
				</div>
			</td>
			<td align="center"><div class='chk-on-disabled-c1'></div></td>
			<td align="center"><div class='chk-on-disabled-c3'></div></td>
			<!--<td align="center" class="c1 border-bold">
				<div class="img-c1-big"></div>
				Включено<br />в стоимость
			</td>
			<td rowspan="3" align="center" class="c2 border-bold">
				<div class="img-c2-big"></div>
				Включено<br />в стоимость
			</td>
			<td rowspan="3" align="center" class="c3 border-bold">
				<div class="img-c3-big"></div>
				Включено<br />в стоимость
			</td>-->
		</tr>
		<tr>
			<td>Проектирование канализации «0»</td>
			<td align="center"><div class='chk-on-disabled-c1'></div></td>
			<td align="center"><div class='chk-on-disabled-c3'></div></td>
		</tr>
		<tr>
			<td class="border-bold">Доработка фасадов в части изменения световых проемов, корректировка конфигурации и комплектации светопрозрачных конструкций.</td>
			<td align="center"><div class='chk-on-disabled-c1'></div></td>
			<td align="center"><div class='chk-on-disabled-c3'></div></td>
		</tr>

<?php
$i = 1;
$rows = '';
$group_id = 0; // id группы для убирания бордюра между ячейками одной группы
foreach ($arrCalc as $razdel => $arrRazdel) { // перебираем основной массив этапов работ
	if ($razdel!='Отделка* (прединженерный этап) '){
    // дополняем данными выбранного дома
    foreach ($arrRazdel as $key => $description) { // создание разделов таблицы
	$i++;
        if($arrSale[$house_id]['base'][$key][0] == 0 and // если нет расчетной части
                $arrSale[$house_id]['base'][$key][1] == 0 and 
                $arrSale[$house_id]['base'][$key][2] == 0) 
        {
            # открытие раздела
            // если все базовые параметры 0, то производим объединение колонок в одну
            $rows .= "<tr>\n<td>$description</td>\n<td colspan='3' align='center'>\n"; // наименование
			$rows .= $arrSale[$house_id]['base'][$key][3];
			$rows .="</td>\n</tr>\n"; // закрытие раздела
			continue;
		} else {
            $rows .= "<tr>\n<td>$description</td>\n<td align='center'>\n"; // наименование
        }
        
	$rows .="<input type='hidden' name='price_$key' id='price_$key' value='{$arrSale[$house_id]['base'][$key][0]}' />\n"; // стоимость
        
	// комплектация 1	
	if ($arrSale[$house_id]['base'][$key][1] == 1 ) {
            $rows .=
				"<div class='chk-on-disabled-c1'>
				<input type='checkbox' class='chk-none' name='chk1_$key' id='chk1_$key' checked='checked' disabled />
				<label for='chk1_$key'></label>
				</div>\n"; // выбрано без возможности изменить
        } else if ($arrSale[$house_id]['base'][$key][1] == 3 ) {
            $rows .= "<input type='checkbox' class='chk-none' name='chk1_$key' id='chk1_$key' disabled /><label for='chk1_$key'></label>\n"; // не выбрано без возможности изменить
        } else {
            $rows .= "<input type='radio' class='chk-none' name='chk1_$key' id='chk1_$key' disabled /><label for='chk1_$key'></label>\n"; // не выбрано без возможности изменить
        }
	$rows .= "</td>\n";

	/*$rows .= "<td align='center'>\n";
        
	// комплектация 2
	if ($arrSale[$house_id]['base'][$key][2] == 1 ) {
            $rows .= "<div class='chk-on-disabled-c2'><input type='checkbox' class='chk-none' name='chk2_$key' id='chk2_$key' checked='checked' disabled /></div>\n"; // выбрано без возможности изменить
        } else {
            $rows .= "<input type='checkbox' class='chk-none' name='chk2_$key' id='chk2_$key' disabled />\n"; // не выбрано без возможности изменить
        }
	$rows .= "</td>\n";*/
        
	// Индивидуальная комплектация
        # блок if для стирания бордера
        $current_group = $arrSale[$house_id]['custom'][$key][1];
        if($current_group == 0 and $group_id == 0) {
            $rows .= "<td align='center' class='border-ind'>\n"; // группы radio нет
        } else if($current_group !== $group_id and $current_group !== 0){
            $rows .= "<td align='center' style='border-bottom:none;' class='border-ind'>\n"; // начало новой группы
            $group_id = $current_group;
        } else if($current_group == $group_id and $group_id !== 0) {
            $rows .= "<td align='center' style='border-top:none;' class='border-ind'>\n"; // продолжение группы
        } else if($current_group == 0 and $group_id !== 0) {
            $rows .= "<td align='center' class='border-ind'>\n"; // конец группы
            $group_id = $current_group;
        } # конец блока
        
        $checked = ($arrSale[$house_id]['custom'][$key][2] == 1 ) ? "checked='checked'" : '';
	if ($arrSale[$house_id]['custom'][$key][0] == 1 ) {
            $rows .= "<div class='chk-on-disabled-c3'><input type='checkbox' class='chk-none' name='chk3_$key' id='chk3_$key' checked='checked' disabled /><label for='chk3_$key'></label></div>\n"; // выбрано без возможности изменить
        }
        if ($arrSale[$house_id]['custom'][$key][0] == 3 ) { // можно выбрать или отменить
            $rows .= "<input type='checkbox' name='chk3_$key' id='chk3_$key' $checked /><label for='chk3_$key'></label>\n"; // выбрано, возможно изменить
        }
        if ($arrSale[$house_id]['custom'][$key][0] == 2 ) { // можно выбрать только 1 вид работ
            $rows .= "<input type='radio' name='radio3[{$arrSale[$house_id]['custom'][$key][1]}]' id='chk3_$key' value='$key' $checked/><label for='chk3_$key'></label>\n";
        }
	
	$rows .="</td>\n</tr>\n"; // закрытие раздела
		}
	echo  "<tr>\n<td rowspan = '$i' class='razdel'>$razdel</td>\n</tr>\n$rows"; // вывод раздела (этапа)
    $i = 1;
    $rows = '';
	}
}
?>
		<tr class="itogo">
			<td class="razdel">Стоимость, руб.</td>
			<td></td>
			<td class="b1"><?php echo str_replace('рублей', 'руб.', $arrSale[$house_id]['c1']); ?></td>
			<!--<td class="b2"><?php /*echo $arrSale[$house_id]['c2'];*/ ?></td>-->
			<td class="b3 js-b3"></td>
		</tr>
		<!--<tr class="itogo" style="height: 30px;">
			<td style="background:none;" class="b0" colspan="5"></td>
		</tr>
		<tr>
			<td style="background:none;padding-top: 15px;" class="b0" colspan="4"><a
						style="    text-decoration: none;padding: 10px;float: right;font-size: 14px;"
						data-url="/catalog/zakaz-smety22/?f_home=401" data-show="#parent_popup_form_detail_phone"
						data-href=".js-callback-wrap" class="bigsee js-openPopupButton see get_price_button gomanager">Связаться
					с менеджером</a>
			</th>
		</tr>-->
    </tbody>
</table>
</form>







<!--
				<div class="att2">				
				<h3>Внимание!</h3>
				Прекращает свое действия АКЦИИ «Комплектация «С2» - мягкая кровля в подарок».
С 17 марта 2014 года в стоимость комплектации «С2» будет включена кровля из метало черепицы и пластиковая водосточная система.
            </div>

				<div class="att2">
					*Объемы работ и материалов посчитаны для базовой планировки.
				</div>

				<br/>
				<div class='notes1'>Вернуться к выбору <a href="/catalog/popular/">по линейкам</a> проектов домов или <a
							href="/catalog/price/">по площади и цене</a>.
				</div>
-->

				<div class="clr"></div>
			</div>
          </div>
<script>calc();</script> 

<Script>



$('.gomanager').click(function(){
	var vibor = $('input[name=vibor]:checked').val();
	var price;
	var nomer;
	//var chk3_2 = $('input[name=chk3_2]:checked').val();
	
	
	if(vibor=='Комплектация c1')
	price = $('.b1').text();
	else if(vibor=='Комплектация c2')
	price = $('.b2').text();
	else price = $('.b3').text();
	nomer = $('.head-calc h2').text();


	$.ajax({
	type: "POST",
	url: "/test/test.php",
	data: {vibor:vibor,price:price,nomer:nomer},
	success: function(msg){
    //alert(msg);
	}
});

});
 
</script>





<div id="parent_popup_form_detail_phone" style="display: none;"><div style="margin-top:3%;" class="nc_full nc_callback mfeedback phone_form" id="popup_form_detail">
<a class="close_backcall_detail" title="" onclick="$('#parent_popup_form_detail_phone').hide();"></a>
  <div class="vhod_title">Заявка на постройку дома</div>
  <div class="jsCB-wrap">
<form class="mfeedback js-callback" name="adminForm" id="adminForm" enctype="multipart/form-data" method="post" action="/test/test.php">
<input name="admin_mode" type="hidden" value="">
<input type="hidden" name="nc_token" value="edc321e67fe815a81ac8f51af724d821"> 
<input name="catalogue" type="hidden" value="1">
<input name="cc" type="hidden" value="360">
<input name="sub" type="hidden" value="333"><input name="home" type="hidden" value="401">
<input name="posting" type="hidden" value="1">
<input name="curPos" type="hidden" value="0">
<input name="f_Parent_Message_ID" type="hidden" value="">
                  <div>
                    <p>
                      <label for="f_Name">Имя</label>
					  <span class="req"></span>
<input name="f_Name" type="text" class="textinput" size="50" maxlength="255" required="" x-webkit-speech="" value="">              
			</p>
                  </div>
                  <div>
                    <p>
                      <label for="f_Email">Email</label>
					  <span class="req"></span>
<span class="req" id="koli"></span><input name="f_Email" type="email" class="textinput" size="50" maxlength="255" required="required" value="">              
			</p>
                  </div>
                  <div>
                    <p>
                      <label for="f_Phone">Телефон</label>
                      <span class="req"></span>
<input name="f_Phone" type="text" class="textinput" id="phone" placeholder="+7 (___) ___-__-__" size="50" maxlength="255" required="" value="">              
			</p>
                  </div>
                  <div>
                    <p>
                      <label for="f_Message">Дополнительно</label>
                    <textarea id="f_Text" name="f_Text" rows="8" cols="40"></textarea>
                  </p></div>

<input value="Отправить" type="submit">
<p class="caps" style="position:relative">				 <span class="req"></span> Поле обязательно для заполнения</p>
</form>

<div class="clr"></div>
		  </div>
		  </div>
		  
        </div>
