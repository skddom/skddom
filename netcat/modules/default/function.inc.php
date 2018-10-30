<?php
define( "DATE_FORMAT", "d.m.Y" );
include $_SERVER['DOCUMENT_ROOT'] . "/netcat/modules/default/geo.php";
include $_SERVER['DOCUMENT_ROOT'] . "/netcat/modules/default/bt.inc.php";

function syncMailchimp( $data ) {
	$apiKey = 'f78a666e68bd0b0a98736d3628391cbc-us14';
	$listId = '4be983751b';

	$memberId   = md5( strtolower( $data['email'] ) );
	$dataCenter = substr( $apiKey, strpos( $apiKey, '-' ) + 1 );
	$url        = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

	$json = json_encode( array(
		'email_address' => $data['email'],
		'status'        => $data['status'], // "subscribed","unsubscribed","cleaned","pending"
		'merge_fields'  => array(
			'FNAME' => $data['firstname'],
			'LNAME' => $data['lastname']
		)
	) );

	$ch = curl_init( $url );

	curl_setopt( $ch, CURLOPT_USERPWD, 'user:' . $apiKey );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $json );

	$result   = curl_exec( $ch );
	$httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
	curl_close( $ch );

	return $httpCode;
}

class CMailChimp {
	const KEY = 'f78a666e68bd0b0a98736d3628391cbc-us14';

	function Add( $list_id, $email, $name ) {
		$postdata = http_build_query(
			array(
				'apikey'        => CMailChimp::KEY,
				'email_address' => $email,
				'status'        => 'subscribed',
				'merge_fields'  => array(
					'FNAME' => $name
				)
			)
		);

		$opts = array(
			'http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => $postdata
				)
		);

		$context = stream_context_create( $opts );

		$result = file_get_contents( 'https://us2.api.mailchimp.com/3.0/lists/' . $list_id . '/members/', FALSE, $context );

		return ( $result );
	}
}

if ( ! $_SESSION['USERGEO'] ) {
	$geo                 = new Geo();
	$USERGEO             = $geo->get_value();
	$_SESSION['USERGEO'] = $USERGEO;
}

if ( ! $_COOKIE['skd_token'] ) {
	$token  = uniqid();
	$expire = time() + 60 * 60 * 24 * 365;//however long you want
	setcookie( 'skd_token', $token, $expire );
	$_COOKIE['skd_token'] = $token;
}

/* расчет калькулятора сроков не в ЛК */
function CalculateHouse( $square, $start0, &$pro ) {
	global $db;
	$lk                 = new stdClass;
	$lk->data['Square'] = $square;
	$start_time         = strtotime( $start0 );

	$pro->rs = $db->get_results( "/*select distinct a.Message_ID as ObjectID, c.CustomSettings,
        UNIX_TIMESTAMP(a.Date1) as Time1,UNIX_TIMESTAMP(a.Date2) as Time2,
     
        a.Prorab, a.Text, a.Auto,
        if (a.Sub_Class_ID=c.Sub_Class_ID, st.EtapStatus_ID, 0) as StatusID, 
        a.Photo,
        if (a.Sub_Class_ID=c.Sub_Class_ID, st.EtapStatus_Name, 'Планируются работы') as Status,
        s.Subdivision_ID as sub, 
        c.Sub_Class_ID as cc,  c.Sub_Class_Name as Name, 
        s.Subdivision_Name as `Option`, s.Hidden_URL as Url, 
        CONCAT(s.Hidden_URL,c.EnglishName,'.html') as Link 
        from Sub_Class c
        LEFT JOIN Subdivision s ON s.Subdivision_ID=c.Subdivision_ID
        LEFT JOIN Message" . CProgress::IB . " a ON (a.Project is null and c.Sub_Class_ID=a.Sub_Class_ID) 
        LEFT JOIN Classificator_EtapStatus as st ON a.Status=st.EtapStatus_ID
        where c.Class_Template_ID <= 0 and s.Parent_Sub_ID=" . $pro->get_sub() . "  and c.Class_ID=" . CProgress::IB . " 
        and c.Priority>0
        
        order by s.Priority, c.Priority*/
        
	select  c.CustomSettings, null as Time1,null as Time2, 0 as StatusID,         
        s.Subdivision_ID as sub, c.Sub_Class_ID as cc,  c.Sub_Class_Name as Name, 
        s.Subdivision_Name as `Option`, s.Hidden_URL as Url
        
    from Sub_Class c
    LEFT JOIN Subdivision s ON s.Subdivision_ID=c.Subdivision_ID
        
       
    where  s.Parent_Sub_ID=" . $pro->get_sub() . "  and c.Class_ID=" . CProgress::IB . "  
       and   c.Priority>0 and c.Checked=1
        
    order by s.Priority, c.Priority        
        
        ", ARRAY_A );
	/**
	 * @var $CustomSettings array
	 */
	foreach ( $pro->rs as $k => $v ) {
		eval( $v['CustomSettings'] );
		$v['CustomSettings'] = $CustomSettings;
		// echo "<pre>".print_r($v,1)."</pre>";
		if ( $lk->data['Square'] > 0 ) {
			$CustomSettings['Days'] = round( $CustomSettings['Days'] * ( $lk->data['Square'] / 200 ) );
		}
		$item = array();

		if ( $CustomSettings['From'] > 0 ) {
			$StartFrom = $pro->rs[ $CustomSettings['From'] - 1 ];
		} else {
			$StartFrom = FALSE;
		}
		if ( $k == 0 ) {

			$time = $v['Time1'] ? $v['Time1'] : $start_time;
			if ( $StartFrom ) {
				$time = $v['Time1'] = $StartFrom['Time2'];
			}

			if ( ! $v['Time1'] ) {
				$v['Time1'] = $item['Time1'] = $time;
			} else {
				$item['Time1'] = $time;
			}

			if ( $v['StatusID'] <= 2 ) {
				$v['Time2']    = $time + 86400 * $CustomSettings['Days'];
				$item['Time2'] = $v['Time2'];
				//$time += 86400*$CustomSettings['Delay'];
			} elseif ( ! $v['Time2'] ) {
				$time = $v['Time2'] = $time + $time + 86400 * $CustomSettings['Days'];
			} else {
				$time = $item['Time2'] = $v['Time2'];
			}
			$time  = $item['Time2'];
			$debug = "$item[Time1] => $item[Time2] => $time<br>";
		} else {

			if ( $StartFrom ) {
				$time = $v['Time1'] = $StartFrom['Time2'];
			}

			$time += 86400 * $CustomSettings['Delay'];
			if ( $StartFrom['Time1'] ) {
				$v['Time1'] = $item['Time1'] = $StartFrom['Time2'];
			} else {
				$v['Time1'] = $time;
			}
			$item['Time1'] = $time;
			$item['Time2'] = $time + 86400 * $CustomSettings['Days'];
			$time          = $item['Time2'];

			//if ($GLOBALS['current_user']['Login'] == "developer") echo "{$v['Name']}:  {$CustomSettings['Days']} ".date("d.m.Y",$v['Time1'])."-".date("d.m.Y",$time)."<br>";
			/**
			 * @var $CustomgSettings array
			 */

			if ( $CustomSettings['Skip1'] && $CustomSettings['Skip2'] ) {
				$start = (int) date( "m", $item['Time1'] );
				$end   = (int) date( "m", $item['Time2'] );
				//$debug .= "$start-$end ({$CustomSettings['Skip1']}-{$CustomSettings['Skip2']})...<br>";
				if ( ( $start >= $CustomSettings['Skip1'] && $start >= $CustomgSettings['Skip2'] )
				     ||
				     ( $end >= $CustomSettings['Skip1'] && $end >= $CustomgSettings['Skip2'] )
				) {
					$month = (int) $CustomSettings['Skip2'] + 1;
					$year  = ( $CustomSettings['Skip1'] > $CustomSettings['Skip2'] ? ( date( "Y", $item['Time1'] ) + 1 ) : date( "Y", $item['Time1'] ) );

					$item['Time1'] = mktime( 0, 0, 0, $month, 1, $year );
					$item['Time2'] = $item['Time1'] + 86400 * $CustomSettings['Days'];
					$time          = $item['Time2'];
				}
			}
		}
		$pro->rs[ $k ]              = array_merge( $v, $item );
		$debug                      .= $v['Name'] . ": " . $v['Status'] . "[" . date( "d.m.Y", $item['Time1'] ) . "-" . date( "d.m.Y", $item['Time2'] ) . "]<br>";
		$toChange[ $v['ObjectID'] ] = $item;
		$option                     = $v['Option'];
		$sub                        = $v['sub'];
	}

	return $debug;
}

/*
# Функция создает превью на лету, если она не существует
# Возвращает путь к файлу режим уменьшения: 
# -1 — вписывает   в указанные размеры с полями по краям с фоном $rgb
#  0 — пропорционально уменьшает (по умолчанию); 
#  1 — вписывает   в указанные размеры с обрезкой; 
#  3 — аналогично 1, но с приоритетов по ширине; 
#  4 — аналогично 1 но с приоритетом по высоте;
function getThumbNow($src, $width, $height, $mode=0, $nocache = false, $quality = 100, $rgb = 0xFFFFFF) {
    global $HTTP_IMAGES_PATH, $INCLUDE_FOLDER;
    
    $imageFile =     $_SERVER['DOCUMENT_ROOT'].$src;
if(!is_file($imageFile)) return false; // Если файла не существует, то возвращаем false
	
	$ext = pathinfo($imageFile, PATHINFO_EXTENSION);
	$newFileName = md5_file($imageFile)."_".$width."x".$height."x$mode".(!empty($ext) ? ".".$ext : NULL);
    
    $folder = $HTTP_IMAGES_PATH."cache/"; // Создаем директорию для хранения изображений
    if(!$dh=opendir($_SERVER['DOCUMENT_ROOT'].$folder)) {mkdir($_SERVER['DOCUMENT_ROOT'].$folder, 0777);} else closedir($dh); // создаем папке, если нет

    $newFile = $_SERVER['DOCUMENT_ROOT'].$folder.$newFileName;
    if(!is_file($newFile) || $nocache) 
    {
        if($mode == -1) img_resize1($imageFile, $newFile, $width,  $height, $rgb, $quality);
        else {
            require_once($INCLUDE_FOLDER."classes/nc_imagetransform.class.php");
            nc_ImageTransform::imgResize($imageFile, $newFile, $width,  $height, $mode);
        }
    }
    
    return $folder.$newFileName;    
}

# Функция расайза изображений с подставлением фона
function img_resize1($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
{
    if (!file_exists($src)) return false;
    
    $size = getimagesize($src);
    
    if ($size === false) return false;
    $quality=(int)$quality; // приводим качество к инту, чтобы не было проблем
    $width=(int)$width; // тоже и с размерами
    $height=(int)$height;
    
    // если качество меньше 1 или больше 99, тогда ставим его 100
    if($quality<1 OR $quality>99)
        $quality=100;
    
    // если вдруг не пришла высота или ширина, тогда размеры будем оставлять как размеры самой картинки, без уменьшения
    if(!$width OR !$height)    {
        $width=$size[0];
        $height=$size[1];
    }
    
    // если реальная ширина и высота рисунка меньше, чем размеры до которых надо уменьшить,
    // тогда уменьшаемые размеры станут равны реальным размерам, чтобы не произошло увеличение
    if($size[0]<$width AND $size[1]<$height)    {
        $width=$size[0];
        $height=$size[1];
    }

    // Определяем исходный формат по MIME-информации, предоставленной
    // функцией getimagesize, и выбираем соответствующую формату
    // imagecreatefrom-функцию.
    $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
    
    $icfunc = "imagecreatefrom" . $format;
    
    if (!function_exists($icfunc)) return false;
    
    $x_ratio = $width / $size[0];
    $y_ratio = $height / $size[1];
    
    $ratio = min($x_ratio, $y_ratio);
    $use_x_ratio = ($x_ratio == $ratio);
    
    $new_width = $use_x_ratio ? $width : floor($size[0] * $ratio);
    $new_height = !$use_x_ratio ? $height : floor($size[1] * $ratio);
    $new_left = $use_x_ratio ? 0 : floor(($width - $new_width) / 2);
    $new_top = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);
    
    
    $isrc = $icfunc($src);
    
    $idest = imagecreatetruecolor($width, $height); // так создается картинка узаканного размера, а все где картинки нет, заполнится фоном. чтобы так создавать картинку, нижнюю строку надо удалить, а с этой снять комментарии
        
    imagefill($idest, 0, 0, $rgb);
    imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);
    
    imagejpeg($idest, $dest, $quality);
    
    imagedestroy($isrc);
    imagedestroy($idest);
    
    return true;
}
*/
function x_resize_crop( $file, $resizeToWidth, $resizeToHeight ) {

	ini_set( 'memory_limit', '128M' );

	global $HTTP_HOST, $DOCUMENT_ROOT, $HTTP_FILES_PATH, $FILES_FOLDER;

	$file = str_replace( "h_", "", $file );

	$imgcache = "Resize/" . $resizeToWidth . $resizeToHeight . substr( $file, strrpos( $file, "/" ) + 1, strlen( $file ) );

	if ( ! file_exists( $FILES_FOLDER . $imgcache ) and $file and file_exists( $DOCUMENT_ROOT . $file ) ) {


		list( $width, $height, $itype ) = getimagesize( $DOCUMENT_ROOT . $file );

		switch ( $itype ) {

			case 1:
				$image = imagecreatefromgif( $DOCUMENT_ROOT . $file );
				break;

			case 2:
				$image = imagecreatefromjpeg( $DOCUMENT_ROOT . $file );
				break;

			case 3:
				$image = imagecreatefrompng( $DOCUMENT_ROOT . $file );
				break;
		}

		$resizeWidthRatio = $width / $resizeToWidth;

		$resizeHeightRatio = $height / $resizeToHeight;

		$resizeRatio = min( $resizeHeightRatio, $resizeWidthRatio );

		$resizeX = ( $width / 2 ) - ( $resizeToWidth / 2 ) * $resizeRatio;

		$resizeY = ( $height / 2 ) - ( $resizeToHeight / 2 ) * $resizeRatio;

		$image_n = imagecreatetruecolor( $resizeToWidth, $resizeToHeight );

		if ( $itype == 3 ) {

			imagealphablending( $image, FALSE );

			imagesavealpha( $image, TRUE );

			imagealphablending( $image_n, FALSE );

			imagesavealpha( $image_n, TRUE );
		}

		imagecopyresampled( $image_n, $image, 0, 0, $resizeX, $resizeY, $resizeToWidth, $resizeToHeight, $width - 2 * $resizeX, $height - 2 * $resizeY );

		if ( $itype == 3 ) {

			imagepng( $image_n, $FILES_FOLDER . $imgcache );
		} else {

			imagejpeg( $image_n, $FILES_FOLDER . $imgcache, 90 );
		}
	}

	return $HTTP_FILES_PATH . $imgcache;
}

function getPriceData( $house_id, &$rs ) {
	global $db;
	$rs           = $db->get_row( "select c1,c2,base,custom from Message2276 where house_id=$house_id", ARRAY_A );
	$rs['base']   = unserialize( $rs['base'] );
	$rs['custom'] = unserialize( $rs['custom'] );
	$works        = $db->get_results( "SELECT s.Subdivision_Name AS Cycle, a.Name,a.Price, c.CustomSettings, a.Message_ID AS ID
    FROM Message2275 a
    LEFT JOIN Subdivision s ON a.Subdivision_ID=s.Subdivision_ID
    LEFT JOIN Sub_Class c ON a.Sub_Class_ID=c.Sub_Class_ID
    ORDER BY s.Priority, a.Priority", ARRAY_A );
	/**
	 * @var $data array
	 */
	foreach ( $works as $v ) {
		for ( $i = 1; $i < 4; $i ++ ) {
			if ( $rs['base'][ $v['ID'] ]["set$i"] ) {
				$data["set$i"]["price"]          += $rs['base'][ $v['ID'] ]['vol'] * $v['Price'];
				$data["set$i"]["discount_price"] += ( $rs['base'][ $v['ID'] ]['vol'] * $v['Price'] ) * ( ( 100 - $rs['base'][ $v['ID'] ]['discount'] ) / 100 );
			}
			$data["set$i"]["items"][ $v['ID'] ]['price']          = $rs['base'][ $v['ID'] ]['vol'] * $v['Price'];
			$data["set$i"]["items"][ $v['ID'] ]['vol']            = $rs['base'][ $v['ID'] ]['vol'];
			$data["set$i"]["items"][ $v['ID'] ]['fee']            = $v['Price'];
			$data["set$i"]["items"][ $v['ID'] ]['checked']        = $rs['base'][ $v['ID'] ]["set$i"];
			$data["set$i"]["items"][ $v['ID'] ]['discount']       = $rs['base'][ $v['ID'] ]['discount'];
			$data["set$i"]["items"][ $v['ID'] ]['discount_price'] = ( $rs['base'][ $v['ID'] ]['vol'] * $v['Price'] ) * ( ( 100 - $rs['base'][ $v['ID'] ]['discount'] ) / 100 );
		}
	}

	return $data;
}

function custom_modify_output( &$content ) {
	$content = content_replacer( $content );
	$br      = CBranding::get_object();
	$br->ModifyBuffer( $content );
}

function calc_dates( &$pro, $start, $square ) {
	$lk       = new stdClass();
	$lk->data = array( "Square" => $square );
	/**
	 * @var $CustomSettings array
	 * @var $toChange array
	 */

	foreach ( $pro->rs as $k => $v ) {

		eval( $v['CustomSettings'] );
		$v['CustomSettings'] = $CustomSettings;
		//echo "<pre>".print_r($v,1)."</pre>";
		if ( $lk->data['Square'] > 0 ) {
			$CustomSettings['Days'] = round( $CustomSettings['Days'] * ( $lk->data['Square'] / 200 ) );
		}
		$item = array();

		if ( $CustomSettings['From'] > 0 ) {
			$StartFrom = $pro->rs[ $CustomSettings['From'] - 1 ];
			if ( $toChange[ $StartFrom['ObjectID'] ] ) {
				$StartFrom = $toChange[ $StartFrom['ObjectID'] ];
			}
		} else {
			$StartFrom = FALSE;
		}
		if ( $k == 0 ) {

			$time = strtotime( $start );

			$v['Time1'] = $item['Time1'] = $time;

			if ( $v['StatusID'] <= 2 ) {
				$v['Time2']    = $time + 86400 * $CustomSettings['Days'];
				$item['Time2'] = $v['Time2'];
				//$time += 86400*$CustomSettings['Delay'];
			}
			$time          = $item['Time2'];
			$debug         = "$item[Time1] => $item[Time2] => $time<br>";
			$pro->rs[ $k ] = array_merge( $v, $item );
		} else {
			/**
			 * @var $time
			 */

			if ( $v['StatusID'] <= 0 ) {
				if ( $StartFrom ) {
					$v['Time1'] = $item['Time1'] = $StartFrom['Time2'];
				} else {
					$v['Time1'] = $item['Time1'] = $time;
				}
				$item['Time2'] = $time + 86400 * $CustomSettings['Days'];
				$time          = $time + 86400 * $CustomSettings['Days'] + 86400 * $CustomSettings['Delay'];
			} elseif ( $v['StatusID'] <= 2 ) {
				if ( ! $v['Time1'] || 1 ) {
					if ( $StartFrom ) {
						$v['Time1'] = $StartFrom['Time2'];
					} else {
						$v['Time1'] = $time;
					}
					$item['Time1'] = $time;
				}
				$item['Time1'] = $v['Time1'] += 86400 * $CustomSettings['Delay'];
				//$debug .= "StartFrom=$StartFrom<br>";
				$v['Time2'] = $v['Time1'] + 86400 * $CustomSettings['Days'];
				$time       = $item['Time2'] = $v['Time2'];
				//$time += 86400*$CustomSettings['Delay'];
			} else {
				$time = $v['Time2'] + 86400 * $CustomSettings['Delay'];
			}
			if ( $CustomSettings['Skip1'] && $CustomSettings['Skip2'] ) {
				$start = (int) date( "m", $item['Time1'] );
				$end   = (int) date( "m", $item['Time2'] );
				/**
				 * @var $debug string
				 * @var $CustomgSettings array
				 */

				$debug .= "$start-$end ({$CustomSettings['Skip1']}-{$CustomSettings['Skip2']})...<br>";
				if ( ( $start >= $CustomSettings['Skip1'] && $start >= $CustomgSettings['Skip2'] )
				     ||
				     ( $end >= $CustomSettings['Skip1'] && $end >= $CustomgSettings['Skip2'] )
				) {
					$month = (int) $CustomSettings['Skip2'] + 1;
					$year  = ( $CustomSettings['Skip1'] > $CustomSettings['Skip2'] ? ( date( "Y", $item['Time1'] ) + 1 ) : date( "Y", $item['Time1'] ) );

					$item['Time1'] = mktime( 0, 0, 0, $month, 1, $year );
					$item['Time2'] = $item['Time1'] + 86400 * $CustomSettings['Days'];
					$time          = $item['Time2'];
				}
			}
			$pro->rs[ $k ] = array_merge( $v, $item );
		}
		$debug                      .= $v['Name'] . ": " . $v['Status'] . "[" . date( "d.m.Y", $item['Time1'] ) . "-" . date( "d.m.Y", $item['Time2'] ) . "]<br>";
		$toChange[ $v['ObjectID'] ] = $item;
		$option                     = $v['Option'];
		$sub                        = $v['sub'];
	}
	//$debug =  "<pre>". print_r($pro->rs,1)."</pre>";
}

class CFile {
	function getExtension( $filename ) {
		$path_info = pathinfo( $filename );

		return $path_info['extension'];
	}

	function formatSize( $size ) {
		$filesizename = array( " Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB" );

		return $size ?
			round( $size / pow( 1024, ( $i = floor( log( $size, 1024 ) ) ) ), 2 ) . $filesizename[ $i ] :
			'0 ' . $filesizename[0];
	}

	function getType( $filename ) {
		$ext = strtolower( CFile::getExtension( $filename ) );
		switch ( $ext ) {
			case "pdf":
				return "pdf";
				break;
			case "png":
			case "jpg":
			case "bmp":
			case "gif":
			case "jpeg":
				return "image";
				break;
			case "zip":
			case "rar":
			case "7z":
				return "archive";
				break;
			case "doc":
			case "docx":
				return "word";
				break;
			case "xls":
			case "xlsx":
				return "excel";
				break;
			default:
				return "archive";
				break;
		}
	}
}

$nc_core = nc_Core::get_object();
$nc_core->register_macrofunc( 'CONTENT_REPLACER', 'content_replacer' );
function content_replacer( $buffer ) {
	if ( $_REQUEST['admin_modal'] ) {
		return $buffer;
	}
	if ( $_REQUEST['inside_admin'] ) {
		return $buffer;
	}
	global $Branding;
	if ( ! is_object( $Branding ) ) {
		$Branding = new CBranding();
	}
	$str = '';
	foreach ( $Branding->params as $k => $v ) {
		$buffer                 = str_replace( "$k", $v, $buffer );
		$k                      = str_replace( "#", "", $k );
		$k                      = "{" . $k . "}";
		$buffer                 = str_replace( "$k", $v, $buffer );
		$Branding->params[ $k ] = $v;
		$str                    .= "<!-- $k=>$v -->";
	}
	$str = ob_get_clean();

	$buffer .= "<!-- " . strlen( $str ) . " -->";
	$Branding->ModifyBuffer( $buffer );

	return $buffer;
}

class CSKDProjects {
	const IB = 2226;
	const CL = "Projects";

	function Listeter( $site_id, $subdivision_id, $infoblock_id, $component_id, $object_id ) {
		CSKDProjects::AddMessageHandler( $component_id, $object_id );
	}

	function AddMessageHandler( $component_id, $object_id ) {
		if ( $component_id != self::IB ) {
			return;
		}
		self::AddItem( $object_id );
	}

	function AddItem( $message, $v = FALSE ) {
		global $db;
		if ( ! $v ) {
			$v = $db->get_row( "select Message_ID id, Name name, Priority as sort from Message" . self::IB . " where Message_ID=$message", ARRAY_A );
		}
		extract( $v );
		/**
		 * @var $name
		 * @var $sort
		 */
		$insert = "insert into Classificator_" . self::CL . "(Projects_Name,Projects_Priority,Value,Checked) 
		VALUES('$name','$sort','$message',1)";
		$db->query( $insert );
		if ( ! $db->insert_id ) {
			echo $db->last_error;
		}

		return $db->insert_id;
	}

	function GetAllList() {
		global $db;
		$rs = $db->get_results( "SELECT * FROM Classificator_" . self::CL, ARRAY_A );
		foreach ( $rs as $v ) {
			$ret[ $v['Value'] ] = $v;
		}

		return $ret;
	}

	function InitList() {
		global $db;
		$all = self::GetAllList();
		$rs  = $db->get_results( "SELECT Message_ID id, Name name, Priority AS sort FROM Message" . self::IB . " ORDER BY Name", ARRAY_A );
		foreach ( $rs as $v ) {
			if ( $all[ $v['id'] ] ) {
				continue;
			}
			// Add Item
			echo self::AddItem( $v['id'], $v );
		}
	}
}

$CSKDProjects = new CSKDProjects;
$nc_core->event->bind( $CSKDProjects, array( "addMessage" => "Listeter" ) );

/*$nc_core->add_listener('addMessage', function($site_id, $subdivision_id, $infoblock_id, $component_id, $object_id) {
    CSKDProjects::AddMessageHandler($component_id, $object_id);
});*/

class CBranding {
	const CITY_VAR = "skd_city";

	function __construct() {
		global $db;
		$this->db  = $db;
		$this->geo = $_SESSION['USERGEO'];
		$q         = "select *, (IF(City='{$this->geo['city']}',10,IF(Region='{$this->geo['region']}',5,0))) as Rank
		from Message2226
		/*where City='{$this->geo['city']}' or Region='{$this->geo['region']}' or (City='' and Region='')*/
        where Checked=1
		order by (IF(City='{$this->geo['city']}',1,IF(Region='{$this->geo['region']}',5,IF(City='' and Region='',10,100)))) asc";
		$rs        = $this->db->get_results( $q, ARRAY_A );

		if ( $_SESSION[ self::CITY_VAR ] || $_REQUEST["city"] ) {
			foreach ( $rs as $v ) {
				if ( $v['Message_ID'] == $_REQUEST["city"] ) {
					$this->current = $v;

					ob_clean();
					$_SESSION[ self::CITY_VAR ] = $v['Message_ID'];
					$s                          = parse_url( $_SERVER['REQUEST_URI'] );
					$url                        = $_SERVER['REQUEST_URI'];
					$url                        = str_replace( array(
						"?city={$v['Message_ID']}",
						"&amp;city={$v['Message_ID']}"
					), "", $url );
					header( "Location: $url" );
					//echo "<pre>";print_r($_SESSION);
					die();
					break;
				}
				if ( $v['Message_ID'] == $_SESSION[ self::CITY_VAR ] ) {
					$this->current    = $v;
					$_SESSION['city'] = $v['Message_ID'];
					continue;
				}
			}
		} else {
			$this->current = $rs[0];
		}
		$this->id = $this->db->get_var( "SELECT Projects_ID
				FROM Classificator_Projects
				WHERE Value=" . (int) $this->current["Message_ID"] );
		if ( ! $this->current['Projects'] ) {
			$this->current['Projects'] = "/base.php";
		}
		$this->current['Params'] = str_replace( array( "{", "}" ), "#", $this->current['Params'] );
		$t                       = explode( "\n", $this->current['Params'] );
		foreach ( $t as $v ) {
			$v                     = explode( "=", trim( $v ) );
			$this->params[ $v[0] ] = trim( $v[1] );
		}
		$this->params['{Phone}'] = $this->current['Phone'];
		$this->params['{Email}'] = $this->current['Email'];
		foreach ( $rs as $v ) {
			if ( $v["Message_ID"] == $this->current["Message_ID"] ) {
				$v["CHECKED"] = "Y";
			}
			$cities[ $v["City"] . ", " . $v["Region"] ] = $v;
		}
		ksort( $cities );
		reset( $cities );

		$this->cities = array();
		foreach ( $cities as $city ) {
			$this->cities[] = $city;
		}
	}

	function isOn() {
		return TRUE; //isAdmin() || $_REQUEST["showCity"];
	}

	function get( $var, $def = "" ) {
		return $this->current[ $var ] ? $this->current[ $var ] : $def;
	}

	function getProjects() {
		/*$f_Projects = nc_load_multifield(1988, $this->current['Message_ID']);
		$file = $f_Projects->to_array();
		$file = $file[0]->path;
		  if(file_exists($_SERVER['DOCUMENT_ROOT'].$file)) {
			$fp=fopen($_SERVER['DOCUMENT_ROOT'].$file, "r");
			$projects = array();
			while ( ($data=fgetcsv($fp,10000,";"))!==false ) {
				$row++;
				if ($row==1) {$fld=$data;continue;}
				$line = array();
				foreach ($fld as $k=>$v) $line["f_".$v] = $data[$k];
				$projects[$data[0]] = $line;
			}
		  }*/
		if ( $this->current['Projects'] && file_exists( $_SERVER['DOCUMENT_ROOT'] . $this->current['Projects'] ) ) {
			include $_SERVER['DOCUMENT_ROOT'] . $this->current['Projects'];
			$rs = $this->db->get_results( "SELECT Message_ID, house_id FROM Message210", ARRAY_A );
			/**
			 * @var $arrSale
			 */
			foreach ( (array) $rs as $v ) {
				$h2id[ $v['house_id'] ] = $v['Message_ID'];
			}
			foreach ( (array) $arrSale as $house_id => $ar ) {
				$arProject                      = array(
					"f_Message_ID" => $h2id[ $house_id ],
					"f_house_id"   => $house_id,
					"f_Price1"     => preg_replace( '~[^0-9\s]+~', '', $ar['c1'] ),
					"f_Price"      => preg_replace( '~[^0-9\s]+~', '', $ar['c2'] )
				);
				$projects[ $h2id[ $house_id ] ] = $arProject;
			}
		}
		$this->projects = $projects;
	}

	function ExModifyQueryWhere( &$query_where, $cc_env, $inside_admin, $admin_mode, $allow_all = TRUE ) {
		$db     = $this->db;
		$br     = CBranding::get_object();
		$bylist = $db->get_var( "SELECT Projects_ID FROM Classificator_Projects WHERE Value='" . $br->get( 'Message_ID' ) . "'" );
		if ( strlen( $query_where ) > 1 ) {
			$query_where .= " ";
		} else {
			$query_where = " 1 ";
		}
		$query_where .= " AND (a.Cities not like '%,$bylist,%' or a.Cities is null)";
	}

	function ModifyQueryWhere( &$query_where, $cc_env, $inside_admin, $admin_mode, $allow_all = TRUE ) {

		$db = $this->db;
		$cc = $cc_env['Sub_Class_ID'];
		if ( strlen( $query_where ) > 1 ) {
			$query_where .= " and ";
		} else {
			$query_where = "";
		}

		if ( $this->isOn() ) {
			$br = CBranding::get_object();

			// is there a city content
			$classID     = $cc_env['Class_ID'];
			$bylist      = $db->get_var( "SELECT Projects_ID FROM Classificator_Projects WHERE Value='" . $br->get( 'Message_ID' ) . "'" );
			$q           = "select count(a.Message_ID) from Message$classID a where $query_where  a.City={$br->current['Message_ID']} and a.Checked=1 and a.Sub_Class_ID=$cc";
			$this->query = $q;

			$rs = $db->get_var( $q );
			if ( ( ( $rs > 0 && $br->current['Message_ID'] ) or ! $allow_all ) && ! $inside_admin ) {
				$query_where .= "(a.City=" . $br->get( 'Message_ID' ) . ( $cc_env["Class_ID"] == 210 ? " or a.Cities is NULL or a.Cities like '%,$bylist,%'" : "" ) . ")";
			} elseif ( ! $inside_admin && $allow_all ) {
				$query_where .= "(a.City is null or a.City='')";
			}
		} else {
			$query_where .= "(a.City is null or a.City='')";
		}
		//echo "<!-- $query_where -->";
	}

	function ModifyCCSettings( &$cc_settings, $cc ) {
		$pre = "cc_" . $cc . "_";
		foreach ( $this->params as $k => $v ) {
			if ( strpos( $k, $pre ) !== FALSE ) {
				$key                 = str_replace( $pre, "", $k );
				$cc_settings[ $key ] = $v;
			}
		}
	}

	function ModifyBuffer( &$s ) {

		if ( $_REQUEST['admin_modal'] ) {
			return;
		}
		if ( $_REQUEST['inside_admin'] ) {
			return;
		}
		//if(isAdmin()) print_r($_REQUEST);
		preg_match_all( '#\{(.*?)\}#si', $s, $out );
		foreach ( $this->cities as $city ) {
			$t = explode( "\n", $city['Params'] );
			foreach ( $t as $v ) {
				$v    = explode( "=", trim( $v ) );
				$v[0] = "{" . str_replace( "#", "", $v[0] ) . "}";
				if ( ! isset( $this->params[ $v[0] ] ) ) {
					$this->params[ $v[0] ] = "";
				}
			}
		}
		if ( $_GET['show'] == "ModifyBuffer" ) {
			echo "<pre>";
			print_r( $this->params );
			print_r( $out[0] );
			die();
		}
		foreach ( $this->params as $k => $v ) {
			if ( strpos( $k, "#" ) !== FALSE ) {
				$k                  = "{" . str_replace( "#", "", $k ) . "}";
				$this->params[ $k ] = $v;
			}
		}
		//if(isAdmin()) print_r($this->params);
		foreach ( $out[0] as $v ) {
			if ( ! isset( $this->params[ $v ] ) ) {
				continue;
			}
			$val = $this->params[ $v ];
			if ( ! $val ) {
				$val = "";
			}

			$s = str_replace( $v, $val, $s );
			/**/
		}
	}

	/**
	 * Get or instance self object
	 *
	 * @return self object
	 */
	public static function get_object() {
		static $storage;
		// check cache
		if ( ! isset( $storage ) ) {
			// init object
			$storage = new self();
		}

		// return object
		return is_object( $storage ) ? $storage : FALSE;
	}
}

function IsAdmin() {
	global $current_user;

	return $current_user["Login"] == "developer" || $current_user["Login"] == "admin";
}

function RecomNotifyAgent( $debug = TRUE ) {
	CPersonalEvent::SetUpAll( $debug );
	global $db;
	$q  = "select a.Name, a.Date as DateCompleted, a.Done, a.Period, a.PeriodText, a.Phase, DATE_ADD(b.Date2, interval (a.Period*CEIL(ABS((TO_DAYS(CURDATE())-TO_DAYS(b.Date2))/a.Period))) DAY) as Date0,
          c.Title1, c.Name as ProjectName, a.Date0, 
          p.MailBody, 
          p.MailSubject,
          " . ( $debug ? "'voxdei@ya.ru'" : "u.Email" ) . " as Email, 
          u.ForumName
          from Message356 a
          left join Message348 b ON (a.Phase=b.Sub_Class_ID and b.Project=a.Project)
          left join Message295 c ON a.Project=c.Message_ID
          left join Message356 p ON a.Parent_Message_ID=p.Message_ID
          left join User u ON c.Owner=u.User_ID
          where a.Project>0 " . ( $debug ? NULL : " and c.Send=1 and DATE_ADD(b.Date2, interval (a.Period*CEIL(ABS((TO_DAYS(CURDATE())-TO_DAYS(b.Date2))/a.Period))) DAY) = CURDATE()" ) . "
          order by DATE_ADD(b.Date2, interval (a.Period*CEIL(ABS((TO_DAYS(CURDATE())-TO_DAYS(b.Date2))/a.Period))) DAY)";
	$rs = $db->get_results( $q, ARRAY_A );
	foreach ( $rs as $v ) {
		$v['NAME'] = ( $v['Title1'] ? $v['Title1'] : $v['ForumName'] );
		$tpl       = "Здравствуйте, " . ( $v['Title1'] ? $v['Title1'] : $v['ForumName'] ) . "!
        
        Рекомендуем произвести {$v['Name']} для поддержания лучшего качества работ.
        ";
		if ( $v['MailBody'] ) {
			$tpl = $v['MailBody'];
		}

		foreach ( $v as $f => $t ) {
			$tpl = str_replace( "#" . $f . "#", $t, $tpl );
		}

		$subject = "Рекомендация  для Вашего дома от СКД";
		if ( $v['MailSubject'] ) {
			$subject = $v['MailSubject'];
		}
		$from = "sales@skd-dom.ru";
		$to   = $v['Email'];
		$sent = mail( $to, $subject, $tpl, "from: $from" );
		if ( $debug ) {
			echo "$v[Name]: $to => " . ( $sent ? "ok" : "fail" ) . "\n";
		}
	}
}

class CPersonal {
	var $lk;
	var $id;
	var $data;
	var $obj;

	function __construct( $lk = FALSE ) {
		global $AUTH_USER_ID, $db;
		if ( ! $AUTH_USER_ID ) {
			return FALSE;
		}
		if ( $lk > 0 ) {
			$this->lk = $lk;
			$this->id = $db->get_var( "select Owner from Message295 where Checked=1 and Message_ID=$this->lk" );
		} else {
			$this->id = $AUTH_USER_ID;
			$this->lk = $db->get_var( "SELECT Message_ID FROM Message295 WHERE Checked=1" . ( $GLOBALS['current_user']['Login'] == "developer" ? " limit 1" : " and Owner=$this->id" ) );
		}
		$this->data = $db->get_row( "select * from Message295 where Message_ID=$this->lk", ARRAY_A );
		if ( $this->data['Object'] > 0 ) {
			$this->obj = $db->get_row( "SELECT * FROM Message255 WHERE Message_ID=" . (int) $this->data['Object'], ARRAY_A );
		}
	}
}

class CPersonalEvent {

	function SetUpAll( $debug = FALSE ) {
		global $db;
		$rs = $db->get_results( "SELECT Message_ID AS id FROM Message295", ARRAY_A );
		foreach ( $rs as $v ) {
			CPersonalEvent::SetUp( $v["id"], $debug );
		}
	}

	/*
		@param int $lk Message295 Message_ID
	*/
	function SetUp( $lk, $debug = FALSE ) {
		$lk = new CPersonal( $lk );
		global $db;
		$rs = $db->get_results( "select *,UNIX_TIMESTAMP(Date) as Time from Message356 where Project=$lk->lk order by Message_ID desc", ARRAY_A );
		foreach ( $rs as $v ) {
			$rec[ $v['Parent_Message_ID'] ][] = $v;
		}

		foreach ( $rec as $p => $items ) {
			$last = $items[0];

			if ( $last['Checked'] != 1 || $last['Done'] ) {
				if ( $debug ) {
					echo "нужно добавлять событие\n";
				}
				if ( ! $last['Time'] ) {
					if ( $debug ) {
						echo "вычислим дату события, если ее нет\n";
					}
					// если есть предыдущее событие
					$time = $items[1]['Time'];
					if ( ! $time ) {
						echo "предыдущей даты нет\n";
						$phase = $db->get_row( "select UNIX_TIMESTAMP(Date2) as Time2,UNIX_TIMESTAMP(Date4) as Time4 from Message348 where Sub_Class_ID={$last['Phase']} and Project=$lk->lk", ARRAY_A );
						$time  = $phase['Time4'] ? $phase['Time4'] : $phase['Time2'];
					}
					if ( $debug ) {
						echo "Время конца этапа $time\n";
					}
					$time_event = $time;
					do {
						$time_event += $last['Period'] * 86400;
					} while ( $time_event < time() );
					if ( $debug ) {
						echo "Дата события " . date( "d.m.Y", $time_event ) . "\n";
					}
				}
				$times = ( ( $time_event - $time ) / ( 86400 ) ) / $last['Period'];
				if ( $debug ) {
					echo "Итерация: $times\n";
				}
				if ( $last['Times'] > 0 && $times < $last['Times'] ) {
					continue;
				}
				if ( $debug ) {
					echo "Запись события...\n";
				}
				$add = $last;
				unset( $add['Message_ID'] );
				unset( $add['Time'] );
				unset( $add['Date'] );
				unset( $add['Created'] );
				$add['Checked'] = 1;
				unset( $add['Done'] );
				foreach ( $add as $k => $v ) {
					$add[ $k ] = "'$v'";
				}
				$add['Date0'] = "FROM_UNIXTIME($time_event)";
				$sql          = "INSERT INTO Message356(`" . join( "`, `", array_keys( $add ) ) . "`) VALUES(" . join( ", ", $add ) . ")";
				$db->query( $sql );
			}
		}
	}
}

class CProgress {
	const IB = 348;
	private $sub;

	function __construct( $lk, $sub ) {
		if ( $lk->lk <= 0 ) {
			return;
		}
		$this->lk  = $lk;
		$this->sub = $sub;
		$this->get_objects();
		$this->completed = array( - 1 );
		foreach ( $this->rs as $k => $v ) {
			if ( $v['StatusID'] == 3 ) {
				$this->current       = $v['ObjectID'];
				$this->current_index = $k;
				$this->completed[]   = $v['cc'];
			}
		}
	}

	function get_sub() {
		return $this->sub;
	}

	function get_objects() {
		global $db;
		$query_from   = "Sub_Class c, Subdivision s";
		$query_select = "a.Sub_Class_ID, if (a.Sub_Class_ID=c.Sub_Class_ID, st.EtapStatus_ID, 0) as StatusID,if (a.Sub_Class_ID=c.Sub_Class_ID, st.EtapStatus_Name, 'В ожидании') as Status,s.Subdivision_ID as sub, c.Sub_Class_ID as cc, a.Message_ID as ObjectID, c.Sub_Class_Name as Name, s.Subdivision_Name as `Option`, s.Hidden_URL as Url, CONCAT(s.Hidden_URL,c.EnglishName,'.html') as Link";
		$query_order  = "s.Priority, c.Priority";
		$query_where  = "c.Class_Template_ID <= 0 and s.Parent_Sub_ID=$this->sub and s.Subdivision_ID=c.Subdivision_ID and c.Class_ID=" . self::IB;
		$query_join   = "LEFT JOIN Message" . self::IB . " a ON (a.Subdivision_ID=s.Subdivision_ID and a.Project={$this->lk->lk})
      LEFT JOIN Classificator_EtapStatus as st ON a.Status=st.EtapStatus_ID";
		$query        = "select distinct a.Message_ID as ObjectID, c.CustomSettings,
        UNIX_TIMESTAMP(a.Date1) as Time1,UNIX_TIMESTAMP(a.Date2) as Time2,
       /* UNIX_TIMESTAMP(a.Date3) as Time3,UNIX_TIMESTAMP(a.Date4) as Time4,*/
        a.Prorab, a.Text, a.Auto,
        if (a.Sub_Class_ID=c.Sub_Class_ID, st.EtapStatus_ID, 0) as StatusID, 
        a.Photo,
        if (a.Sub_Class_ID=c.Sub_Class_ID, st.EtapStatus_Name, 'Планируются работы') as Status,
        s.Subdivision_ID as sub, 
        c.Sub_Class_ID as cc,  c.Sub_Class_Name as Name, 
        s.Subdivision_Name as `Option`, s.Hidden_URL as Url, 
        CONCAT(s.Hidden_URL,c.EnglishName,'.html') as Link 
        from Sub_Class c
        LEFT JOIN Subdivision s ON s.Subdivision_ID=c.Subdivision_ID
        LEFT JOIN Message" . self::IB . " a ON (a.Project={$this->lk->lk} and c.Sub_Class_ID=a.Sub_Class_ID) 
        LEFT JOIN Classificator_EtapStatus as st ON a.Status=st.EtapStatus_ID
        where c.Class_Template_ID <= 0 and s.Parent_Sub_ID=$this->sub  and c.Class_ID=" . self::IB . " 
        and c.Priority>0
        
        order by s.Priority, c.Priority";

		$this->rs = $db->get_results( $query, ARRAY_A );
		/**
		 * @var $CustomSettings array
		 */
		foreach ( $this->rs as $v ) {
			eval ( $v['CustomSettings'] );
			$v['CustomSettings']                          = $CustomSettings;
			$this->tree[ $v['Option'] ]['ITEMS'][]        = $v;
			$this->tree[ $v['Option'] ]['Url']            = $v['Url'];
			$this->tree[ $v['Option'] ]['Subdivision_ID'] = $v['sub'];
		}
	}
}

if ( $_GET['full_version'] == "Y" ) {

	ob_clean();
	setcookie( "full_version", "Y", time() + 7200, "/", ".skd-dom.ru" );
	$uri = str_replace( "?full_version=Y", "", $_SERVER['REQUEST_URI'] );
	$uri = "http://www.skd-dom.ru$uri";
	// print_r($_COOKIE);
	header( "location: $uri" );
	die();
}

/* @ function void sendwebform() 1.0
 * @ отправляет письмо с данными формы (компонент-добавление)
 * TODO: Связь с другим объектом
 *
 * @param integer $classID ID компонента
 * @param string $subject Тема письма
 * @param string $from E-mail отправителя
 * @param string $to E-mail адресата
 * @param string $encoding Кодировка письма
 * @param string $from_name Имя отправителя
 * @param string $prev_text Текст в начале письма
 * @param boolean $inhtml Отправить также и в html формате
 *
 * @access private
 */
function sendwebform( $classID, $subject, $from, $to, $encoding = 'windows-1251', $from_name = '', $prev_text = '', $inhtml = FALSE, $reply = FALSE, $message_id ) {

	global $db;

// Инициализация
	$mailer = new CMIMEMail();
// послать в кодировке
	define( MAIN_EMAIL_ENCODING, $encoding );

// поля компонента
	$q      = "SELECT Field_Name as Name, Description,TypeOfData_ID,Format FROM Field WHERE Class_ID='$classID' ORDER BY Priority";
	$fields = $db->get_results( $q, ARRAY_A );

	$prev_text .= "<br>\n";
	$text      = strip_tags( $prev_text );
	$html      = $prev_text;

	foreach ( $fields as $f ) {

		$index = "f_" . $f["Name"];
		$value = $_POST[ $index ];

		switch ( $f["TypeOfData_ID"] ) {
// Список
			case 4:
				$list  = $f["Format"];
				$value = $db->get_var( "SELECT {$list}_Name FROM Classificator_{$list} WHERE {$list}_ID=" . intval( $value ) );
				if ( $value ) {
					$text .= "{$f['Description']}: $value\n";
					$html .= "<b>{$f['Description']}</b>: $value<br>\n";
				}
				break;
// Boolean
			case 5:
				$value = $value ? "Да" : "Нет";
				$text  .= "{$f['Description']}: $value\n";
				$html  .= "<b>{$f['Description']}</b>: $value<br>\n";
				break;
// File
			case 6:
				if ( $_FILES[ $index ]["tmp_name"] && ! $_FILES[ $index ]["error"] ) {
					$mailer->attachFile( $_SERVER["DOCUMENT_ROOT"] . nc_file_path( $classID, $message_id, $f["Name"] ), $_FILES[ $index ]["name"], "application/octet-stream" );
					$value = "прикреплен файл - " . $_FILES[ $index ]["name"];
					$text  .= "{$f['Description']}: $value\n";
					$html  .= "<b>{$f['Description']}</b>: $value<br>\n";
				} else {
//$text.=print_r($_FILES,1);
				}
				break;
// Date and Time
			case 8:
				$Y = $_POST["{$index}_year"];
				$m = $_POST["{$index}_month"];
				$d = $_POST["{$index}_day"];
				$H = $_POST["{$index}_hours"];
				$i = $_POST["{$index}_minutes"];
				$s = $_POST["{$index}_seconds"];
				if ( $Y ) {
					$value = "$Y-$m-$d $H:$i:$s";
					$text  .= "{$f['Description']}: $value\n";
					$html  .= "<b>{$f['Description']}</b>: $value<br>\n";
				}
				break;
// Object Link
			case 9:
				$tmp   = explode( ":", $f["Format"] );
				$value = $db->get_var( "select $tmp[1] from " . ( $tmp[0] > 0 ? "Message$tmp[0]" : $tmp[0] ) . "
where" . ( $tmp[0] > 0 ? "Message_ID" : "{$tmp[0]}_ID" ) . "=$value" );
				break;

// Multiselect Link
			case 10:

				$list  = $f["Format"];
				$val   = $value;
				$value = array();
				foreach ( $val as $v ) {
					$value[] = $db->get_var( "SELECT {$list}_Name FROM Classificator_{$list} WHERE {$list}_ID=" . intval( $v ) );
				}

				if ( count( $value ) > 0 ) {
					$value = join( ",", $value );
					$text  .= "{$f['Description']}: $value\n";
					$html  .= "<b>{$f['Description']}</b>: $value<br>\n";
				}

				break;

// String & Numbers
			default:
				if ( $value ) {

					$text .= "{$f['Description']}: $value\n";
					if ( $f["Format"] == "email" ) {
						$value = "<a href='mailto:$value'>$value</a>";
					}
					$html .= "<b>{$f['Description']}</b>: $value<br>\n";
				}
				break;
		}
	}
	$html = "<html><head></head><body>$html</body></html>";
	/**
	 * @var $ishtml
	 */
	if ( ! $ishtml ) {
		$html = FALSE;
	}
	if ( ! $reply ) {
		$reply = $from;
	}
	$mailer->mailbody( $text, $html );

	$to1 = explode( ",", $to );
	foreach ( $to1 as $to ) {
		$mailer->send( $to, $from, $reply, $subject, $from_name );
	}
//mail($to,$subject,$text,"from:$from_name <$from>");
//echo "mail($to,$subject,$text,from:$from_name <$from>);";
	return;
}

if ( ! function_exists( "nc_file_path" ) ) {
	/**
	 * Получить путь к файлу в поле $field_name_or_id объекта $message_id из шаблона $class_id
	 *
	 * @param mixed string or int id шаблона/название системной таблицы
	 * @param int id сообщения
	 * @param mixed string or int имя или ID поля
	 * @param string использовать префикс для новых файлов (optional).
	 * "h_" для получения ссылки для скачивания файла под оригинальным именем
	 *
	 * @return string путь до файла
	 */
	function nc_file_path( $class_id, $message_id, $field_name_or_id, $file_name_prefix = "" ) {
		global $db, $HTTP_FILES_PATH, $SUB_FOLDER;
		static $file_field_info;

		$message_id = (int) $message_id;
		if ( ! $message_id ) {
			return FALSE;
		}

# системные таблицы с идентификаторами
		$system_tables = array(
			1 => "Catalogue",
			2 => "Subdivision",
			3 => "User",
			4 => "Template"
		);

		if ( $class_id && is_numeric( $class_id ) ) {
# query to Message and Filetable
			$message_table = "Message" . $class_id;
			$ft_id_field   = "Message_ID";
# 'Field' query parts
			$query_where = "Class_ID=" . $class_id;
		} elseif ( $class_id && in_array( $class_id, $system_tables ) ) {
# query to
			$message_table = $class_id;
			$ft_id_field   = $class_id . "_ID";
# 'Field' query parts
			$tmp_array   = array_flip( $system_tables );
			$query_where = "Class_ID = 0 AND System_Table_ID = '" . $tmp_array[ $class_id ] . "'";
		} else {
			trigger_error( "<b>nc_file_path()</b>: Wrong class ID (" . $class_id . ")", E_USER_WARNING );

			return FALSE;
		}

		if ( ! $file_field_info[ $class_id ] ) {
			$res = $db->get_results( "SELECT `Field_ID`, `Field_Name` FROM `Field` WHERE " . $query_where . " AND TypeOfData_ID = 6", ARRAY_A );
			if ( ! empty( $res ) ) {
				foreach ( $res AS $row ) {
					$file_field_info[ $class_id ][ $row['Field_Name'] ] = $row['Field_ID'];
				}
			}
		}

# get correct field_name and field_id
		if ( ! $file_field_info[ $class_id ][ $field_name_or_id ] && is_numeric( $field_name_or_id ) && in_array( $field_name_or_id, $file_field_info[ $class_id ] ) ) {
# i.e. Field_ID supplied
			$field_id   = $field_name_or_id;
			$tmp_array  = array_flip( $file_field_info[ $class_id ] );
			$field_name = $tmp_array[ $field_id ];
		} elseif ( $file_field_info[ $class_id ][ $field_name_or_id ] ) {
# Field_Name
			$field_name = $field_name_or_id;
			$field_id   = $file_field_info[ $class_id ][ $field_name ];
		} else {
# it doesn't seems like name nor id
			trigger_error( "<b>nc_file_path()</b>: Wrong field name or ID (" . $field_name_or_id . ")", E_USER_WARNING );

			return FALSE;
		}

# query database
		$res = $db->get_row( "SELECT m.`" . $field_name . "` AS old_name, ft.`File_Path` AS new_path, ft.`Virt_Name` AS new_name
FROM `" . $message_table . "` AS m
LEFT JOIN `Filetable` AS ft
ON (m." . $ft_id_field . " = ft.Message_ID AND ft.Field_ID = " . $field_id . ")
WHERE m." . $ft_id_field . " = " . $message_id . "", ARRAY_A );

		if ( $res["old_name"] ) {
# возвращаем результат
# protected fs
			if ( $res['new_name'] ) {
				return $SUB_FOLDER . rtrim( $HTTP_FILES_PATH, "/" ) . $res['new_path'] . $file_name_prefix . $res['new_name'];
			}
// значение из таблицы
			$file_data = explode( ':', $res['old_name'] );
			$file_name = $file_data[0]; // оригинальное имя
			$ext       = substr( $file_name, strrpos( $file_name, "." ) ); //расширение
// папка
			$file_path = $SUB_FOLDER . $HTTP_FILES_PATH;
// путь плностью, в зависимоти от типа файловой системы
			$file_path .= ( $file_data[3] ) ? $file_data[3] : $field_id . "_" . $message_id . $ext;

			return $file_path;
		}

		return FALSE;
	}
}

function minCssFile( $path ) {
	if ( is_array( $path ) ) {
		$buffer = '';
		foreach ( $path as $v ) {
			$buffer .= file_get_contents( $_SERVER['DOCUMENT_ROOT'] . $v );
		}
		$path = dirname( $v ) . "/combined.css";
	} else {
		$buffer = file_get_contents( $_SERVER['DOCUMENT_ROOT'] . $path );
	}
	// Remove comments
	$buffer = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer );
	// Remove space after colons
	$buffer = str_replace( ': ', ':', $buffer );
	// Remove whitespace
	$buffer     = str_replace( array(
		"\r\n",
		"\r",
		"\n",
		"\t",
		'  ',
		'    ',
		'    '
	), '', $buffer );
	$name       = explode( ".", basename( $path ) );
	$c          = count( $name ) - 1;
	$name[]     = $name[ $c ];
	$name[ $c ] = "min";
	$name       = join( ".", $name );
	$dest       = dirname( $path ) . "/" . $name;
	$fp         = fopen( $_SERVER['DOCUMENT_ROOT'] . $dest, "w" );
	fwrite( $fp, $buffer );
	fclose( $fp );

	return $dest;
}

# Функция создает превью на лету, если она не существует
# Возвращает путь к файлу режим уменьшения: 
# -1 — вписывает   в указанные размеры с полями по краям с фоном $rgb
#  0 — пропорционально уменьшает (по умолчанию); 
#  1 — вписывает   в указанные размеры с обрезкой; 
#  3 — аналогично 1, но с приоритетов по ширине; 
#  4 — аналогично 1 но с приоритетом по высоте;
function getThumbNow( $src, $width, $height, $mode = 0, $nocache = FALSE, $quality = 100, $rgb = 0xFFFFFF ) {
	global $HTTP_IMAGES_PATH, $INCLUDE_FOLDER;

	$imageFile = $_SERVER['DOCUMENT_ROOT'] . $src;
	if ( ! is_file( $imageFile ) ) {
		return FALSE;
	} // Если файла не существует, то возвращаем false

	$ext         = pathinfo( $imageFile, PATHINFO_EXTENSION );
	$newFileName = md5_file( $imageFile ) . "_" . $width . "x" . $height . "x$mode" . ( ! empty( $ext ) ? "." . $ext : NULL );

	$folder = $HTTP_IMAGES_PATH . "cache/"; // Создаем директорию для хранения изображений
	if ( ! $dh = opendir( $_SERVER['DOCUMENT_ROOT'] . $folder ) ) {
		mkdir( $_SERVER['DOCUMENT_ROOT'] . $folder, 0777 );
	} else {
		closedir( $dh );
	} // создаем папке, если нет

	$newFile = $_SERVER['DOCUMENT_ROOT'] . $folder . $newFileName;
	if ( ! is_file( $newFile ) || $nocache ) {
		if ( $mode == - 1 ) {
			img_resize( $imageFile, $newFile, $width, $height, $rgb, $quality );
		} else {
			require_once( $INCLUDE_FOLDER . "classes/nc_imagetransform.class.php" );
			nc_ImageTransform::imgResize( $imageFile, $newFile, $width, $height, $mode );
		}
	}

	return $folder . $newFileName;
}

# Функция расайза изображений с подставлением фона
function img_resize( $src, $dest, $width, $height, $rgb = 0xFFFFFF, $quality = 100 ) {
	if ( ! file_exists( $src ) ) {
		return FALSE;
	}

	$size = getimagesize( $src );

	if ( $size === FALSE ) {
		return FALSE;
	}
	$quality = (int) $quality; // приводим качество к инту, чтобы не было проблем
	$width   = (int) $width; // тоже и с размерами
	$height  = (int) $height;

	// если качество меньше 1 или больше 99, тогда ставим его 100
	if ( $quality < 1 OR $quality > 99 ) {
		$quality = 100;
	}

	// если вдруг не пришла высота или ширина, тогда размеры будем оставлять как размеры самой картинки, без уменьшения
	if ( ! $width OR ! $height ) {
		$width  = $size[0];
		$height = $size[1];
	}

	// если реальная ширина и высота рисунка меньше, чем размеры до которых надо уменьшить,
	// тогда уменьшаемые размеры станут равны реальным размерам, чтобы не произошло увеличение
	if ( $size[0] < $width AND $size[1] < $height ) {
		$width  = $size[0];
		$height = $size[1];
	}

	// Определяем исходный формат по MIME-информации, предоставленной
	// функцией getimagesize, и выбираем соответствующую формату
	// imagecreatefrom-функцию.
	$format = strtolower( substr( $size['mime'], strpos( $size['mime'], '/' ) + 1 ) );

	$icfunc = "imagecreatefrom" . $format;

	if ( ! function_exists( $icfunc ) ) {
		return FALSE;
	}

	$x_ratio = $width / $size[0];
	$y_ratio = $height / $size[1];

	$ratio       = min( $x_ratio, $y_ratio );
	$use_x_ratio = ( $x_ratio == $ratio );

	$new_width  = $use_x_ratio ? $width : floor( $size[0] * $ratio );
	$new_height = ! $use_x_ratio ? $height : floor( $size[1] * $ratio );
	$new_left   = $use_x_ratio ? 0 : floor( ( $width - $new_width ) / 2 );
	$new_top    = ! $use_x_ratio ? 0 : floor( ( $height - $new_height ) / 2 );

	$isrc = $icfunc( $src );

	$idest = imagecreatetruecolor( $width, $height ); // так создается картинка узаканного размера, а все где картинки нет, заполнится фоном. чтобы так создавать картинку, нижнюю строку надо удалить, а с этой снять комментарии

	imagefill( $idest, 0, 0, $rgb );
	imagecopyresampled( $idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1] );

	imagejpeg( $idest, $dest, $quality );

	imagedestroy( $isrc );
	imagedestroy( $idest );

	return TRUE;
}

include_once( dirname( __FILE__ ) . "/jsCssCompressor.class.php" );

?>