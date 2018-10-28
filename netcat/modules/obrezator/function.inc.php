<?php

//Aglion Crop module for NetCat


function AG_CR_GET_DR(){
	return rtrim( getenv("DOCUMENT_ROOT"), "/\\" );
}

require_once (AG_CR_GET_DR()."/netcat/connect_io.php");

$MODULE_VARS = $nc_core->modules->get_module_vars();

function AGLION_RESIZE($url, $width=0, $height=0, $mode=''){
	global $MODULE_VARS;
	if($width===0 && isset($MODULE_VARS[obrezator][width]))
		$width=$MODULE_VARS[obrezator][width];
	if($height===0 && isset($MODULE_VARS[obrezator][height]))
		$height=$MODULE_VARS[obrezator][height];
	if($mode=='')
		$mode=$MODULE_VARS[obrezator][mode];
	
	$SAVE_PATH="/netcat_files/obrezator/";
	
	$FileName= explode('/',$url);
	$FileName= $FileName[count($FileName)-1];
	$FileName= explode('.',$FileName);
	
	$FileType= $FileName[1]; //name_file
	$FileName= $FileName[0]; //type file
	
	if (file_exists(AG_CR_GET_DR().$url)) {
		$TO_SAVE_PATH= $SAVE_PATH.$FileName."_".$width."x".$height."_".$mode.".";
		//if(file_exists(AG_CR_GET_DR().))
		if(file_exists(AG_CR_GET_DR().$TO_SAVE_PATH.$FileType) && filemtime(AG_CR_GET_DR().$url)<filemtime(AG_CR_GET_DR().$TO_SAVE_PATH.$FileType))
			return $TO_SAVE_PATH.$FileType;
		
		switch ($FileType) {
			case "jpg":
				$imgInput= imagecreatefromjpeg(AG_CR_GET_DR().$url);
				break;
			case "jpeg":
				$imgInput= imagecreatefromjpeg(AG_CR_GET_DR().$url);
				break;
			case "png":
				$imgInput= imagecreatefrompng(AG_CR_GET_DR().$url);
				break;
			case "gif":
				$imgInput= imagecreatefromgif(AG_CR_GET_DR().$url);
				break;
		}
			
		
		$ratio=imagesy($imgInput)/imagesx($imgInput);		
		
		if($mode=='r'){
			if(($width=='' || $width==0)  && ( $height!='' && $height!=0)){
				$width=$height/$ratio;
			} else if(($width!='' && $width!=0)  && ( $height=='' || $height==0)){
				$height=$width*$ratio;
			} else if(($width=='' || $width==0) && ($height=='' || $height==0))
				return $url;
			$sWidth=imagesx($imgInput);
			$sHeight=imagesy($imgInput);
			$sX=0;
			$sY=0;
		} else if($mode=='c'){
			if(($width=='' || $width==0) && ($height=='' || $height==0))
				return $url;
			else if($width=='' || $width==0)
					$width=$height;
				else if($height=='' || $height==0)
					$height=$width;
			if($height/$width==$ratio){//Если соотношение сторон совпадает
				$sWidth=imagesx($imgInput);
				$sHeight=imagesy($imgInput);
				$sX=0;
				$sY=0;
			} else {
				if($height/$width>$ratio){//обрезаем по краям
					$tmpWidth=$width*(imagesy($imgInput)/$height);
					$tmpPadd=(imagesx($imgInput)-$tmpWidth)/2;
					$sY=0;
					$sX=$tmpPadd;
					$sWidth=$tmpWidth;
					$sHeight=imagesy($imgInput);
				
				} else { //обрезаем сверху и низзу
					$tmpHeight=$height*(imagesx($imgInput)/$width);
					$tmpPadd=(imagesy($imgInput)-$tmpHeight)/2;
					$sX=0;
					$sY=$tmpPadd;
					$sHeight=$tmpHeight;
					$sWidth=imagesx($imgInput);
				}
			}		
		}	
		
		$imgOutPut = imagecreatetruecolor($width, $height);		
		$transparent = imagecolorallocatealpha( $imgOutPut, 0, 0, 0, 127 ); 
		imagefill( $imgOutPut, 0, 0, $transparent ); 
		
		imagecopyresampled($imgOutPut,$imgInput,0,0,$sX,$sY,$width,$height,$sWidth,$sHeight);
		
		//imagejpeg($imgOutPut, $TO_SAVE_PATH, 85);
		switch ($FileType) {
			case "jpg":
				imagejpeg($imgOutPut, AG_CR_GET_DR().$TO_SAVE_PATH.$FileType, 85);
				break;
			case "jpeg":
				imagejpeg($imgOutPut, AG_CR_GET_DR().$TO_SAVE_PATH.$FileType, 85);
				break;
			case "png":
				imagesavealpha($imgOutPut, true);
				imagepng($imgOutPut, AG_CR_GET_DR().$TO_SAVE_PATH.$FileType);
				break;
			case "gif":
				imagegif($imgOutPut, AG_CR_GET_DR().$TO_SAVE_PATH.$FileType);
				break;
		}		
		imagedestroy($imgInput);
		imagedestroy($imgOutPut);
		return $TO_SAVE_PATH.$FileType;
	} else {
		return "";
	}	
}


function AGLION_MARK($url, $murl=''){
	global $MODULE_VARS;
	
	if($murl=='')
		$murl=$MODULE_VARS[obrezator][watermark];
	
	$SAVE_PATH="/netcat_files/obrezator/mark/";
	
	$FileName= explode('/',$url);
	$FileName= $FileName[count($FileName)-1];
	$FileName= explode('.',$FileName);
	
	$FileType= $FileName[1]; //name_file
	$FileName= $FileName[0]; //type file
	
	if (file_exists(AG_CR_GET_DR().$url)) {
		$TO_SAVE_PATH= $SAVE_PATH.$FileName."_m.";
		//if(file_exists(AG_CR_GET_DR().))
		if(file_exists(AG_CR_GET_DR().$TO_SAVE_PATH.$FileType) && filemtime(AG_CR_GET_DR().$url)<filemtime(AG_CR_GET_DR().$TO_SAVE_PATH.$FileType))
			return $TO_SAVE_PATH.$FileType;
		
		switch ($FileType) {
			case "jpg":
				$imgOutPut= imagecreatefromjpeg(AG_CR_GET_DR().$url);
				break;
			case "jpeg":
				$imgOutPut= imagecreatefromjpeg(AG_CR_GET_DR().$url);
				break;
			case "png":
				$imgOutPut= imagecreatefrompng(AG_CR_GET_DR().$url);
				break;
			case "gif":
				$imgOutPut= imagecreatefromgif(AG_CR_GET_DR().$url);
				break;
		}
		
		$fHeight=imagesy($imgOutPut);
		$fWidth=imagesx($imgOutPut);
		
		//markfile
		$imgInput = imagecreatefrompng(AG_CR_GET_DR().$SUB_FOLDER.$murl);
		
		$mHeight=imagesy($imgInput);
		$mWidth=imagesx($imgInput);
				
		
		imagecopy ($imgOutPut, $imgInput, ($fWidth/2)-($mWidth/2), ($fHeight/2)-($mHeight/2), 0,0, $mWidth,$mHeight);

		switch ($FileType) {
			case "jpg":
				imagejpeg($imgOutPut, AG_CR_GET_DR().$TO_SAVE_PATH.$FileType, 100);
				break;
			case "jpeg":
				imagejpeg($imgOutPut, AG_CR_GET_DR().$TO_SAVE_PATH.$FileType, 100);
				break;
			case "png":
				imagesavealpha($imgOutPut, true);
				imagepng($imgOutPut, AG_CR_GET_DR().$TO_SAVE_PATH.$FileType);
				break;
			case "gif":
				imagegif($imgOutPut, AG_CR_GET_DR().$TO_SAVE_PATH.$FileType);
				break;
		}
		
		imagedestroy($imgInput);
		imagedestroy($imgOutPut);

		return $TO_SAVE_PATH.$FileType;
	} else {
		return "";
	}
}
?>