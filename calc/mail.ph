<?

function email_storey_html($data, $area, $title){
	$options = '';
	foreach (array_values($data) as $key=>$val){
		$options .=
			'<tr'.($key%2==1 ? ' class="even" bgcolor="#f4f4f4" style="background: #f4f4f4;"' : '').'>
				<td height="26px" width="20px">&nbsp;</td>
				<td class="param" height="26px" valign="center" width="150px" style="color: #686868; font-family: Tahoma, Helvetica, sans-serif; font-size: 14px;">'.$val['title'].'</td>
				<td class="param" height="26px" valign="center" style="color: #686868; font-family: Tahoma, Helvetica, sans-serif; font-size: 14px;">'.$val['area'].' м<sup style="display: inline-block; margin-top: -5px;">2</sup></td>
			</tr>';
	}

	return $data!==false ?
		'<table width="100%" cellspacing="0" cellpadding="0" border="0" style="border: none; border-collapse: collapse;">
		<tr>
			<td class="storey_title" height="40px" align="center" valign="center" style="color: #686868; font-family: Tahoma, Helvetica, sans-serif; font-size: 18px; height: 40px; letter-spacing: 0.1em; text-align: center; text-transform: uppercase; vertical-align: middle;">'.$title.'</td>
		</tr>
		<tr>
			<td class="gray_bg" align="center" valign="center" bgcolor="#f4f4f4" style="-moz-border-radius: 5px; -webkit-border-radius: 5px; background: #f4f4f4; border-radius: 5px; height: 50px;">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" style="border: none; border-collapse: collapse;">
					<tr>
						<td height="50px" width="21px">&nbsp;</td>
						<td class="storey_name" height="50px" align="left" valign="center" width="70px" style="color: #686868; font-family: Tahoma, Helvetica, sans-serif; font-size: 10px; height: 50px; letter-spacing: 0.1em; line-height: 15px; text-align: left; text-transform: uppercase; vertical-align: middle;">площадь этажа</td>
						<td class="storey_val" height="50px" align="right" valign="center" style="color: #3b3b3b; font-family: Tahoma, Helvetica, sans-serif; font-size: 20px; height: 50px; text-align: right; text-transform: uppercase; vertical-align: middle;">'.str_replace('.',',', $area).' м<sup style="display: inline-block; margin-top: -5px;">2</sup></td>
						<td height="50px" width="21px">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="10px" align="center" valign="center"></td>
		</tr>
		<tr>
			<td class="border_radius" style="-moz-border-radius: 5px; -webkit-border-radius: 5px; border: 1px solid #dfe1e4; border-radius: 5px; display: block;">
				<table width="100%" cellspacing="0" cellpadding="0" border="0" style="border: none; border-collapse: collapse;">
					<tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="0" border="0" style="border: none; border-collapse: collapse;">
								'.$options.'
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>' : '';
}

function other_houses($houses){
	$houses_html = '';
	foreach ($houses as $val){
		if(!empty($houses_html)){
			$houses_html .= '<tr><td height="20px">&nbsp;</td></tr>';
		}

		$houses_html .=
			'<tr>
				<td align="left" valign="top" height="175px">
					<a href="'.$val['url'].'" target="_blank">
						<img src="'.$val['img'].'" width="530px" height="175px" style="border: 0px; display: block;">
					</a>
				</td>
			</tr>';
	}
	
	return $houses_html;
}

function socials($socials){
	$socials_html = '';
	foreach ($socials as $val){
		$socials_html .=
			'<td width="47px" align="right">
				<a href="'.$val['url'].'" target="_blank">
					<img src="'.$val['img'].'" width="37px" height="37px" style="border: 0px; display: block;">
				</a>
			</td>';
	}

	return $socials_html;
}

$data = json_decode($_POST['f_Data'], true);

$path_to_images = 'http://www.skd-dom.ru/calc/images/email';
$other_houses = array(
	array(
		'url'=>'http://www.skd-dom.ru/',
		'img'=>$path_to_images.'/example/house_1.jpg' //530x175 px
	),
	array(
		'url'=>'http://www.skd-dom.ru/',
		'img'=>$path_to_images.'/example/house_1.jpg' //530x175 px
	),
	array(
		'url'=>'http://www.skd-dom.ru/',
		'img'=>$path_to_images.'/example/house_1.jpg' //530x175 px
	),
);
$socials = array(
	array(
		'url'=>'http://www.skd-dom.ru/',
		'img'=>$path_to_images.'/telegram.png' //37x37 px
	),
	array(
		'url'=>'http://www.skd-dom.ru/',
		'img'=>$path_to_images.'/vk.png' //37x37 px
	),
	array(
		'url'=>'http://www.skd-dom.ru/',
		'img'=>$path_to_images.'/facebook.png' //37x37 px
	),
	array(
		'url'=>'http://www.skd-dom.ru/',
		'img'=>$path_to_images.'/google-plus.png' //37x37 px
	),
	array(
		'url'=>'http://www.skd-dom.ru/',
		'img'=>$path_to_images.'/youtube.png' //37x37 px
	),
	array(
		'url'=>'http://www.skd-dom.ru/',
		'img'=>$path_to_images.'/instagram.png' //37x37 px
	),
);

//$to = 'test@test.com';
//$subject = 'СКД дом';
// @ to be provided
$text = file_get_contents(__DIR__ . '/email_template.html');

$replace = array(
	'%path_to_images%'=>$path_to_images,
	'%subject%'=>'СКД дом',
	'%home_url%'=>'http://www.skd-dom.ru/',
	'%first_storey%'=>email_storey_html($data['first_storey'], $data['areas']['first_storey'], '1 этаж'),
	'%second_storey%'=>email_storey_html($data['second_storey'], $data['areas']['second_storey'], '1 этаж'),
	'%total_area%'=>str_replace('.',',', $data['areas']['total_area']),
	'%total_building_area%'=>str_replace('.',',', $data['areas']['total_building_area']),
	'%price%'=>number_format($data['price'], 0, '.', ' '),
	'%foundation%'=>$data['options']['foundation'],
	'%wall_panel%'=>$data['options']['wall_panel'],
	'%roof_covering%'=>$data['options']['roof_covering'],
	'%other_houses%'=>other_houses($other_houses),
	'%socials%'=>socials($socials),
);

$text = str_replace(array_keys($replace), array_values($replace), $text);
/*
$subject = "=?utf-8?B?".base64_encode($subject)."?=";

$from = 'noreply@skd-dom.com';

$headers = "MIME-Version: 1.0\r\n".
			"Content-type: text/html; charset=utf-8\r\n".
			"From: {$from}\r\n".
			"Reply-To: {$from}\r\n".
			"Return-Path: {$from}\r\n".
			"X-Mailer: PHP/".phpversion();

$status = mail($to, $subject, $message, $headers);


*/