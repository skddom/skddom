<?
class jsCssCompressor { 
    static $block_css,$block_js; 

    public function makeJs($pathes,$to) { 
        if(file_exists($to)) unlink($to); 
        $fp=fopen($to,"a+"); 
        foreach($pathes as $v) { 
            $data=file_get_contents($v); 
            fwrite($fp, self::clear($data)); 
        } 
        fclose($fp); 
        if(file_exists($to)) {return true;}else{return false;} 
    } 
    public function makeCss($pathes,$to) { 
        if(file_exists($to)) unlink($to); 
        $fp=fopen($to,"a+"); 
        foreach($pathes as $v) { 
            $data=file_get_contents($v); 
            fwrite($fp, self::clear($data)); 
        } 
        fclose($fp); 
        if(file_exists($to)) {return true;}else{return false;} 
    } 
    public function compressJs($data) { 
        return self::clear_js($data); 
    } 
    public function compressCss($data) { 
        return self::clear_css($data); 
    } 
    private function clear($data) {
        $data = self::clear_js($data);
        return self::clear_css($data);
    }
    private function clear_js($data) { 
        $data=self::strip_comments_js($data); 
        $data=self::make_one_line($data); 
        $data=self::strip_multipled_spaces($data); 
        $data=self::strip_spaces_js($data); 

        return $data; 
    } 
    private function clear_css($data) { 
        $data=self::strip_comments_css($data); 
        $data=self::strip_multipled_spaces($data); 
        $data=self::make_one_line($data); 
        return $data; 
    } 
    private function strip_comments_js($data) { 
        $data=@eregi_replace("\/\/[^\n]*",'',$data); 
        $data=preg_replace("|\/\*[\s\S]*\*\/|iU",'',$data); 
        return $data; 
    } 
    private function strip_comments_css($data) { 
        $data=preg_replace("|\/\*[\s\S]*\*\/|iU",'',$data); 
        return $data; 
    } 
    private function strip_multipled_spaces($data) { 
        return @eregi_replace("[ ]+",' ',$data); 
    } 
    private function make_one_line($data) { 
        return @eregi_replace("[\n\r]+",' ',$data); 
    } 
    private function strip_spaces_js($data) { 
      $strip_r=array('function','else','typeof','case','new','var','return','in','\|\|','\&\&'); 
      $strip_l=array('typeof','in','\|\|','\&\&','else'); 
      $strip_ap=array('while','for','foreach','do','switch'); 
           if(!self::$block_js) { 
                function strip_zap($m) { 
                    if($m[1]=="}") { 
                        return '};'.$m[2]; 
                    } 
                } 
                
                function strip_right($m) { 
                    return ' '.$m[2].$m[3].' {*}'.$m[4].' '; 
                } 
                 function strip_left($m) { 
                    return ' '.$m[2].'{*}'.$m[3].$m[4].' '; 
                } 
                
                self::$block_js=1; 
          } 
          foreach($strip_r as $v) { 
              $f='/(([\}\{\)\(\; =]{1})('.$v.')([\}\{\)\(; ]{1}))+/i'; 
            $data=preg_replace_callback($f,'strip_right',$data); 
          } 
          foreach($strip_l as $v) { 
              $f='/(([\}\{\)\(\; =]{1})('.$v.')([\}\{\)\(; ]{1}))+/i'; 
            $data=preg_replace_callback($f,'strip_left',$data); 
          } 
      
        foreach($strip_ap as $v) { 
            $f1[0]='/([}]{1}'.$v.'( ||{*}))+/i'; 
            $r1[0]='};'.$v; 
            $data=preg_replace($f1,$r1,$data); 
        } 
        
        $data=@eregi_replace("[ ]+",'',$data); 
        $data=@eregi_replace("\{\*\}",' ',$data); 
         
          $f='/([\}]{1})([a-z]{1})/i'; 
          $data=preg_replace_callback($f,'strip_zap',$data); 
        return $data; 
    } 
    
} 