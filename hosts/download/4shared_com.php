<?php

if (!defined('RAPIDLEECH')) {
	require_once('index.html');
	exit();
}

class d4shared_com extends DownloadClass {
	private $page, $cookie, $pA, $DL_regexp, $noTrafficFreeDl;
	public $link;
	public function Download($link) {
$api_key = '3007219';
$api_token = 'api00003298';
$url = "http://api.fastrapidleech.com/sammobile/link.php?key=$api_key&token=$api_token&link=$link";
$xurl = file_get_contents($url);
$yurl = json_decode($xurl, true);
$zurl = $yurl["link"] ;
$msg = $yurl["msg"];
if($msg !== 'OK')
html_error("Download Link Not Available!");
$this->RedirectDownload($zurl);
	}
}

/*
 *              API Plugin
 * [21-06-2017] Plugin by FastRapidleech.com
 */
?>