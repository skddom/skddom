<?php
$mail = 'op@skd-dom.ru';
$my_mail = 'vipfocus@gmail.com';
$fio = $_POST['fio'];
$mobile = $_POST['mobile'];
$email = $_POST['email'];
$text = $_POST['text'];
$city = $_POST['city'];
$act = $_REQUEST['act'];
$amount = $_REQUEST['amount'];
$amount2 = $_REQUEST['amount2'];
switch($act){
	case"1":
	$s="=?utf-8?b?".base64_encode('Сообщение «Получить бизнес-план» — СКД дом')."?=";
	break;
	case"2":
	$s="=?utf-8?b?".base64_encode('Сообщение «Франшиза» — СКД дом')."?=";
	break;
	case"3":
	$s="=?utf-8?b?".base64_encode('Сообщение «Зарабатывайте с нами» — СКД дом')."?=";	
	break;
	case"4":
	$s="=?utf-8?b?".base64_encode('Сообщение «Обратный звонок» — СКД дом')."?=";	
	break;
	case"5":
	$s="=?utf-8?b?".base64_encode('Сообщение «Задать вопрос» — СКД дом')."?=";	
	break;
}

$from = 'СКД дом <op@skd-dom.ru>';
$header = "Content-Type: text/html; charset=utf-8\r\nFrom: ".$from."\r\n";
if($fio) $fio = 'Имя отправителя: '.$fio.'<br /><br />';
if($city) $city = 'Город отправителя: '.$city.'<br /><br />';
if($email) $email = 'E-mail/Телефон: '.$email.'<br /><br />';
if($mobile) $mobile = 'Номер телефона: '.$mobile.'<br /><br />';
if($text) $text = 'Текст сообщения: '.$text.'<br /><br />';
if($amount) $amount = 'Средний оборот: '.$amount.'<br /><br />';
if($amount2) $amount2 = 'Средняя прибыль: '.$amount2.'<br /><br />';
$mail_text = $fio.$email.$mobile.$city.$text.$amount.$amount2;
@mail($mail, $s, $mail_text, $header);
@mail($my_mail, $s, $mail_text, $header);
switch($act){
	case"1":
	$text = '<p>Спасибо за доверие, мы свяжемся с вами в самые кратчайшие сроки!</p>
<div class="input-message"><a class="btn btn-brown btn-block" id="" data-target="#BusinessPlan" data-toggle="modal">Хорошо</a></div>';
	break;
	case"2":
	$text = '<div class="message-box">
				<p>Спасибо за доверие, мы свяжемся с вами в самые кратчайшие сроки!</p>
				<div class="message">
					<button class="btn btn-green">Хорошо</button>
				</div>
			</div>';	
	break;
	case"3":
	$text = '<div class="message-box-2">
                    <p>Спасибо за доверие, мы свяжемся с вами в самые кратчайшие сроки!</p>
                    <div class="message">
						<button class="btn btn-brown">Хорошо</button>
                    </div>
                    </div>';	
	break;
	case"4":
	$text = '<p>Спасибо за доверие, мы свяжемся с вами в самые кратчайшие сроки!</p>
<div class="input-message"><a id="close" data-dismiss="modal" aria-hidden="true" class="btn btn-brown btn-block">Хорошо</a></div>';	
	break;
	case"5":
	$text = '<p>Спасибо за доверие, мы свяжемся с вами в самые кратчайшие сроки!</p>
<div class="input-message"><a id="close" data-dismiss="modal" aria-hidden="true" class="btn btn-brown btn-block">Хорошо</a></div>';	
	break;
	default:
	$text = '<p>Спасибо за доверие, мы свяжемся с вами в самые кратчайшие сроки!</p>
<div class="input-message"><a class="btn btn-brown btn-block" id="" data-target="#BusinessPlan" data-toggle="modal">Хорошо</a></div>';
	break;
}
print $text;
?>