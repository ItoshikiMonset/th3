<?php
######## Account Info ########
$upload_acc['pcloud_com']['user'] = ''; //Set your login
$upload_acc['pcloud_com']['pass'] = ''; //Set your password
########################

$_GET['proxy'] = isset($_GET['proxy']) ? $_GET['proxy'] : '';
$not_done = true;

if ($upload_acc['pcloud_com']['user'] && $upload_acc['pcloud_com']['pass']) {
	$default_acc = true;
	$_REQUEST['up_login'] = $upload_acc['pcloud_com']['user'];
	$_REQUEST['up_pass'] = $upload_acc['pcloud_com']['pass'];
	$_REQUEST['action'] = 'FORM';
	echo "<b><center>Using Default Login.</center></b>\n";
} else $default_acc = false;

if (empty($_REQUEST['action']) || $_REQUEST['action'] != 'FORM') {
	echo "<table border='0' style='width:270px;' cellspacing='0' align='center'>
	<form method='POST'>
	<input type='hidden' name='action' value='FORM' />
	<tr><td style='white-space:nowrap;'>&nbsp;Login Email : </td><td>&nbsp;<input type='text' name='up_login' value='' style='width:160px;' /></td></tr>
	<tr><td style='white-space:nowrap;'>&nbsp;Password : </td><td>&nbsp;<input type='password' name='up_pass' value='' style='width:160px;' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><br /><input type='submit' value='Upload' /></td></tr>\n";
	echo "<tr><td colspan='2' align='center'><small>*You can set it as default in <b>".basename(__FILE__)."</b></small></td></tr>\n";
	echo "</table>\n</form>\n";
} else {
	$login = $not_done = false;
	$domain = 'pcloud.com';
	$referer = "https://www.$domain/";

	// Login
	echo "<table style='width:600px;margin:auto;'>\n<tr><td align='center'>\n<div id='login' width='100%' align='center'>Login to $domain</div>\n";

	$cookie = array('cookieon' => '1');
	if (!empty($_REQUEST['up_login']) && !empty($_REQUEST['up_pass'])) {
		if (!empty($_REQUEST['A_encrypted'])) {
			$_REQUEST['up_login'] = decrypt(urldecode($_REQUEST['up_login']));
			$_REQUEST['up_pass'] = decrypt(urldecode($_REQUEST['up_pass']));
			unset($_REQUEST['A_encrypted']);
		}

		$x = 0;


$username = urlencode($_REQUEST['up_login']);
$password = urlencode($_REQUEST['up_pass']);

$login = "https://api.pcloud.com/userinfo?getauth=1&username=$username&password=$password&authexpire=15552000";

$page1 = file_get_contents($login) ;

$reply = json_decode($page1, true);

$pcauth = $reply['auth'];
$pcresult = $reply['result'];
$pcuid = $reply['userid'];

if ($pcresult)
html_error('Login failed: User/Password incorrect!');


		$login = true;
	} else html_error('Login failed: User/Password empty.');

	// Retrive upload ID
	echo "<script type='text/javascript'>document.getElementById('login').style.display='none';</script>\n<div id='info' width='100%' align='center'>Retrieving upload ID</div>\n";


	// Uploading
	echo "<script type='text/javascript'>document.getElementById('info').style.display='none';</script>\n";


$uplll = "https://api.pcloud.com/uploadfile?folderid=0&auth=$pcauth";
$url=parse_url($uplll);

$post2 = array();
        $post2['op'] = "login"; //Cutting page ;
        $post2['token'] = ""; //Cutting page ;
        $post2['rand'] = ""; //Cutting page ;

		
$upfiles = upfile($url["host"], $url["port"] ? $url["port"] : 80, $url["path"] . ($url["query"] ? "?" . $url["query"] : ""), 0, 0, $post2, $lfile, $lname, "fn");

?>
<script>document.getElementById('progressblock').style.display='none';</script>
<br />
<br />
<div id=final width=100% align=center>Generating Share Link</div>

<?php
	is_page($upfiles);



$finalpage = cut_str($upfiles,'Connection: close',''); 
$finalpage = str_replace('[', '', $finalpage);
$finalpage = str_replace(']', '', $finalpage);
$reply2 = json_decode($finalpage, true);


$pcfid = $reply2['metadata']['fileid'];


$share = "https://api.pcloud.com/getfilepublink?fileid=$pcfid&auth=$pcauth";
$pagex = file_get_contents($share) ;

$reply3 = json_decode($pagex, true);

$pcsresult = $reply3['result'];
$pcslink = $reply3['link'];

if ($pcsresult)
html_error('File Uploaded!!... Error Generating Share link!!');

	$download_link = $pcslink;
}

function Get_Reply($content) {
	if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
	if (($pos = strpos($content, "\r\n\r\n")) > 0) $content = substr($content, $pos + 4);
	$cb_pos = strpos($content, '{');
	$sb_pos = strpos($content, '[');
	if ($cb_pos === false && $sb_pos === false) html_error('Json start braces not found.');
	$sb = ($cb_pos === false || $sb_pos < $cb_pos) ? true : false;
	$content = substr($content, strpos($content, ($sb ? '[' : '{')));$content = substr($content, 0, strrpos($content, ($sb ? ']' : '}')) + 1);
	if (empty($content)) html_error('No json content.');
	$rply = json_decode($content, true);
	if (!$rply || count($rply) == 0) html_error('Error reading json.');
	return $rply;
}

// [20-7-2016]  Written by FastRapidleech.com (fastrapidleech@gmail.com)

?>