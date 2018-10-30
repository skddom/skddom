<?

$ret = array(
        "name" => "Google Page Rank",
        "value" => google_getrank($this->url),
        "ok" => true,
        "code" => 200
);

// ---------------------------------------------------------------------------------------
// PHP Google PageRank Calculator Script
// ------------------------- August 2004
// Contact author: pagerankscript@googlecommunity.com
// for updates, visit:
// http://www.googlecommunity.com/scripts/google-pagerank.php
// provided by www.GoogleCommunity.com
//  an unofficial community of Google fans
// ---------------------------------------

/*
  This code is released unto the public domain
 */
//header("Content-Type: text/plain; charset=utf-8");
//unsigned shift right
function zeroFill($a, $b) {
    $z = hexdec(80000000);
    if ($z & $a) {
        $a = ($a >> 1);
        $a &= ( ~$z);
        $a |= 0x40000000;
        $a = ($a >> ($b - 1));
    } else {
        $a = ($a >> $b);
    }
    return $a;
}

function mix($a, $b, $c) {
    $a -= $b;
    $a -= $c;
    $a ^= ( zeroFill($c, 13));
    $b -= $c;
    $b -= $a;
    $b ^= ( $a << 8);
    $c -= $a;
    $c -= $b;
    $c ^= ( zeroFill($b, 13));
    $a -= $b;
    $a -= $c;
    $a ^= ( zeroFill($c, 12));
    $b -= $c;
    $b -= $a;
    $b ^= ( $a << 16);
    $c -= $a;
    $c -= $b;
    $c ^= ( zeroFill($b, 5));
    $a -= $b;
    $a -= $c;
    $a ^= ( zeroFill($c, 3));
    $b -= $c;
    $b -= $a;
    $b ^= ( $a << 10);
    $c -= $a;
    $c -= $b;
    $c ^= ( zeroFill($b, 15));

    return array($a, $b, $c);
}

function GoogleCH($url, $length=null, $init=0xE6359A60) {
    if (is_null($length)) {
        $length = sizeof($url);
    }
    $a = $b = 0x9E3779B9;
    $c = $init;
    $k = 0;
    $len = $length;
    while ($len >= 12) {
        $a += ( $url[$k + 0] + ($url[$k + 1] << 8) + ($url[$k + 2] << 16) + ($url[$k + 3] << 24));
        $b += ( $url[$k + 4] + ($url[$k + 5] << 8) + ($url[$k + 6] << 16) + ($url[$k + 7] << 24));
        $c += ( $url[$k + 8] + ($url[$k + 9] << 8) + ($url[$k + 10] << 16) + ($url[$k + 11] << 24));
        $mix = mix($a, $b, $c);
        $a = $mix[0];
        $b = $mix[1];
        $c = $mix[2];
        $k += 12;
        $len -= 12;
    }

    $c += $length;
    switch ($len) /* all the case statements fall through */ {
        case 11: $c+= ( $url[$k + 10] << 24);
        case 10: $c+= ( $url[$k + 9] << 16);
        case 9 : $c+= ( $url[$k + 8] << 8);
        /* the first byte of c is reserved for the length */
        case 8 : $b+= ( $url[$k + 7] << 24);
        case 7 : $b+= ( $url[$k + 6] << 16);
        case 6 : $b+= ( $url[$k + 5] << 8);
        case 5 : $b+= ( $url[$k + 4]);
        case 4 : $a+= ( $url[$k + 3] << 24);
        case 3 : $a+= ( $url[$k + 2] << 16);
        case 2 : $a+= ( $url[$k + 1] << 8);
        case 1 : $a+= ( $url[$k + 0]);
        /* case 0: nothing left to add */
    }
    $mix = mix($a, $b, $c);
    /* -------------------------------------------- report the result */
    return $mix[2];
}

//converts a string into an array of integers containing the numeric value of the char
function strord($string) {
    for ($i = 0; $i < nc_strlen($string); $i++) {
        $result[$i] = ord($string{$i});
    }
    return $result;
}

function google_getrank($url) {
    // [url]http://www.example.com/[/url] - Checksum: 6540747202
    //$url = 'info:'.$_GET['url'];
    $url = 'info:'.$url;
    //print("url:\t{$_GET['url']}\n");
    $ch = GoogleCH(strord($url));
    //printf("ch:\t6%u\n",$ch);

    $file = "http://www.google.com/search?client=navclient-auto&ch=6$ch&features=Rank&q=$url";
    //print "<hr>$file<hr>";
//  $data = file($file);

    global $site_auditor;
    $site_auditor->user_agent->get($file);
    $response = $site_auditor->user_agent->currentResponse();
    if ($response["code"] != "200") {
        return "<a href='$file' target='_blank'>Failed</a>";
    }

    //echo $data[2];
    //echo "<a href='$file'>view pagerank</a>";
//   $rankarray = explode (':', $data[2]);

    $rankarray = explode(':', $response["body"]);
    $rank = $rankarray[2];
    return trim($rank);
}
?>