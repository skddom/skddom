<?php
$link = mysql_connect("u335095.mysql.masterhost.ru","u335095","BOn-iN5cLIngs") or die("Ошибка соединения : " . mysql_error());
    mysql_select_db("u335095_skd_test") or die("Ошибка выбора базы");
	mysql_set_charset('utf8');
$PermissionGroup_ID = 2;
$Checked = 0;
$Email = $_POST['user_mail'];
$Confirmed = 0;
$Login = $_POST['user_mail'];
$Catalogue_ID = 0;
$InsideAdminAccess = 0;	



	
$res2 = mysql_query("SELECT * FROM User WHERE Email = '$Email' ");	
$myrow = mysql_fetch_array($res2);

if($myrow["User_ID"]) echo "Вы уже подписаны";

else if (filter_var($Email, FILTER_VALIDATE_EMAIL))
{		
$res = mysql_query("INSERT INTO User (PermissionGroup_ID, Checked, Email, Confirmed, Login, Catalogue_ID, InsideAdminAccess) VALUE ('".$PermissionGroup_ID."','".$Checked."','".$Email."','".$Confirmed."','".$Login."','".$Catalogue_ID."','".$InsideAdminAccess."') ");

$res4 = mysql_query("SELECT * FROM User WHERE Email = '$Email' ");
$myrow2 = mysql_fetch_array($res4);	
$res3 = mysql_query("INSERT INTO User_Group (User_ID, PermissionGroup_ID) VALUE ('".$myrow2["User_ID"]."','".$PermissionGroup_ID."') ");
echo "Вы успешно подписались";
}
//echo $_POST["user_mail"];

?>