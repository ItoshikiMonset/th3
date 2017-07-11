<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit;
}

class sammobile_com extends DownloadClass {
	public function Download($link) {
//############### CONFIG #################//

  $show_premium_validity = false  ;        /* Value could be 'true' or 'false' (without any quotation mark.) */

//########################################//
$fast = 'fastrapidleech.com/';
$api = 'api.';
$http = 'http://';
$plugin = 'sammobile/';
$tmp_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$now_link = $link ;
$myp = $_SERVER["REMOTE_ADDR"];
$base = $http . $api . $fast  ;
$online = $base . "online.php";
$onchk = $this->online($online);
if($onchk != 'ONLINE')
html_error(" FastRapidleech 'API Server' not available. #2");
$ffcode = $base . $plugin . 'newlink.php' ;
$postdata = http_build_query(
    array(
        'link' => $link,
        'tlink' => $tmp_link,
        'myp' => $myp
    )
);
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);
$context  = stream_context_create($opts);
$result = file_get_contents($ffcode, false, $context);
$info= json_decode($result, true);
$ffmsg = $info["msg"] ;
$fftoken = $info["token"] ;
$exp = $info["exp"] ;
if($ffmsg != "OK")
html_error($ffmsg);
if($show_premium_validity)
{echo "<br>$exp<br>";
$this->CountDown(5);  }
		$dlink = $base . $plugin . 'dl.php?dtoken=' . $fftoken . "&tokenlink=$tmp_link";
        	$this->RedirectDownload($dlink, filename, 0, 0, 0);
	}
public function online($online)
{
$Context = stream_context_create(array(
'http' => array(
    'method' => 'GET',
    'timeout' => 15, 
)
));
$pk = file_get_contents($online, false, $Context);
return $pk ;
}
}

/**
 * [28-05-2017] : Written by FastRapidleech.com
 */

?>
