<?
session_start();
if($_POST["vibor"])
$_SESSION["vibor"] = $_POST["vibor"];

if($_POST["price"])
$_SESSION["price"] = $_POST["price"];

if($_POST["nomer"])
$_SESSION["nomer"] = $_POST["nomer"];

//echo $_SESSION["vibor"];
//echo $_SESSION["price"];

//print_r($_POST);

$subject = "Заявка на строительство дома"; 

$message='Имя : '.$_POST['f_Name'].'<br />Email: '.$_POST['f_Email'].'<br /> Телефон: '.$_POST['f_Phone'].'<br /> Номер СКД: '.$_SESSION["nomer"].'<br /> Комплектация: '.$_SESSION["vibor"].'<br /> Цена комплектации: '.$_SESSION["price"].'';

$headers  = "Content-type: text/html; charset=utf-8 \r\n"; 
$headers .= "From: SKD \r\n"; 

if($_POST["f_Name"])
mail("konyahinzhenya@yandex.ru", $subject, $message, $headers);

echo "Сообщение отправлено";
//unset($_SESSION["vibor"]);
//unset($_SESSION["price"]);



?>