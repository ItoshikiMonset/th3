<?php

/**
 *  Plugin Name: Google Drive Upload Plugin
 *  Written By: FastRapidLeech.Com
 *  Date : 07-April-2018
 */

if(isset($_POST['fastrapidleech_response'])){
$html = base64_decode($_POST['html']);
echo $html;
exit();
}
require_once 'rl_init.php';
require(TEMPLATE_DIR . '/header.php');
global $options;
$pname = 'google_drive_upload_setting.php' ;
$account_info_file = 'configs/' . $pname;   /// list files for each users..
if(file_exists($account_info_file)) {
  $account_info = file_get_contents($account_info_file);
  $account_info = json_decode($account_info, 1);
}
$myurl = gdriveapi() ;
$apiurl = 'http://fastrapidleech.com/api/google/?license_user=susutun';
$force_show_info = true ;
$gid_valid = false ;
$usertype = 'paid';
function get_active_token($url, $gid) {
   $data = FGET($url . '&gettoken=' . $gid);
   $json_data = json_decode($data, 1);
   return $json_data;
}
function make_public($url, $token, $fid) {
   $data = FGET($url . '&token=' . $token . '&make_public=' . $fid);
   $status = json_decode($data, 1);
   if($status["status"])
     return true;
   else return false ;
}
$C = 'UG93ZXJlZCBieSBGYXN0UmFwaWRsZWVjaC5Db20=';
 function gdriveapi()
	{
    $cp = $_SERVER['PHP_SELF']; 
    $pi = pathinfo($cp); 
    $hn = $_SERVER['HTTP_HOST']; 
    $pl = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https'?'https':'http';
    $fw =  $pl.'://'.$hn.$pi['dirname']."/";
    return $fw;
	}
if($_POST["action"] == 'FORM') {
$account_info['newgid'] = null ;
$account_info['gid'] = null ;
$account_info['name'] = null ;
$account_info['email'] = null ;
$account_info['upload_dir'] = null ;
$account_info['make_public'] = null ;
$account_info['newgid'] = $_POST['g_fid'] ;
$account_info['gid'] = $_POST['g_fid'] ;
$account_info['name'] = $_POST['g_name'] ;
$account_info['email'] = $_POST['g_email'] ;
$account_info['upload_dir'] = $_POST['upload_dir'] ;
$make_it_public = 0 ;
if(isset($_POST['make_public']))
$make_it_public = 1 ;
else $make_it_public = 0 ;
$account_info['make_public'] =  $make_it_public;
$account_info['updatetime'] = time() ;
$account_info['folder_list'] = $_POST['folder_list'] ;
$account_info['token'] = null ;
$account_info['token_timeleft'] = null ;
$account_info['token_updatetime'] = null ;
$token_info = get_active_token($apiurl, $account_info['gid']);
if(isset($token_info["status"])){
 if($token_info["status"] == false){
   if(file_exists($account_info_file)) unlink($account_info_file);
   html_error( "Unable to Save!, User Logged out! You need to Login Again.");
 }
 else if($token_info["status"] == true){
$token = $token_info["token"];
$account_info['token'] = $token_info["token"];
$account_info['token_exp_time'] = time() + $token_info["exptime"] ;
$account_info['folder_list'] = $token_info["folderlist"];
if($account_info['upload_dir'] == 'root')
$folder = false ;
else $folder = $account_info['upload_dir'];
$account_info['token_updatetime'] = time() ;
file_put_contents($account_info_file, json_encode($account_info));
 }
}
else {
html_error("FAST API Error , Try again after some time. #249176");
}
echo "\n\n<center><div class='div_title'>Saved!</div>\n\n <br> <a href=''><b>View Current Setting</b></a><br> or <br>\n\n<a href='index.php'><b>Go to Rapidleech</b></a></center><br><br>\n";
echo "
<style>
.div_title {
	color: #FFB000;
	font-size: larger;
	font-weight: bold;
	margin-bottom: 5px;
}
</style>
";
echo "
<center>
					<tr><td colspan=2 align=center><small> <b> " . base64_decode($C) . "</b></small>
                                        </td></tr>
</center>
";
exit();
}
else {
echo "<script> var Dfolder = '" . $account_info['upload_dir'] . "'; </script>" ;
?>
<style type="text/css">

#opt_actions_table table td { padding-right: 15px; text-align: left; }
#opt_presentation_table table td { padding-right: 10px; text-align: left; }
#opt_advanced_table table td { min-width: 80px; text-align: left; }
#opt_login_table thead td { padding-bottom: 5px; }
#opt_login_table td { text-align: center; }
.div_error {
	font-weight: bold; font-size: large; text-align: center; color:#FF0000;
}
.div_opt {
	text-align: left;
	padding-bottom: 5px;
}
.table_cat {
	min-width: 300px;
}
.table_opt {
	width: 100%;
}
.div_main {
	text-align: center;
	border: 1px white ridge;
	padding: 5px;
	margin-top:5px;
}
.div_message {
	color: #FFB000;
	font-weight: bold;
	font-size: larger;
	text-align: center;
	margin: 10px;
}
.div_setup {
	color: #FF7700;
	font-weight: bold;
	font-size: large;
	text-align: center;
}
.div_title {
	color: #FFB000;
	font-size: larger;
	font-weight: bold;
	margin-bottom: 5px;
}

</style>

<script type="text/javascript">
    var glogin;
    function GloginAuth() {
        glogin = window.open("<?php echo $apiurl . '&return=' . $myurl . 'google.php'; ?>&login<?php if(strpos(basename(__FILE__), '_') !== false) echo 'folder' ; ?>", "GLogin", "height=500,width=370,top=70,left=200");
        glogin.focus();
    }

var select, arrrry;
var user_type = '<?php echo $usertype ; ?>';
function updatefolderlist(arry) {
select = document.getElementById( 'upload_dir' );
 for(i = select.options.length - 1 ; i >= 0 ; i--){
  select.remove(i);
 }
listfolder(arry);
}
function listfolder(items) {
for(var index in items) {
    var option = document.createElement( 'option' );
    option.value = index;
    if(index == 'root') 
     { option.text = '/' + items[index]; }
    else 
     { option.text = items[index]; }
    if(user_type == 'free' && items[index] != 'FastRapidleech')
    { option.disabled = 'disabled' ; }
    if(index == Dfolder )
    { option.selected = 'selected' ; }
    select.add( option );
}
}
</script>
<form method="post" >
<input type="hidden" name="action" value='FORM' />
<input type="hidden" value="<?php if(!empty($account_info['gid'])) echo $account_info['gid'] ; ?>"  id="g_fid" name="g_fid"/>
<input type="hidden" value=""  id="folder_list" name="folder_list"/>

<table align="center" class="table_cat" id="login__form" <?php if(strlen($account_info['gid']) > 10 ) echo ' style="display:none" ';?> >
	<tr><td>

		<div class="div_main">
			<div class="div_title">Login Required</div>
			<div class="div_opt">
				<table class="table_opt">
                                        <tr><td colspan=2 align=center><input type="button"  value="Login" onclick="GloginAuth()"/>
                                        </td></tr>
				</table>
			</div>
		</div>

	</td></tr>
</table>
<table align="center" class="table_cat"  id="logged__form" <?php if(strlen($account_info['gid']) <= 10 ) echo ' style="display:none" ';?> >
	<tr><td>

		<div class="div_main">
			<div class="div_title">Logged In As</div>
			<div class="div_opt">
				<table class="table_opt">
					<tr><td>Name</td><td><input type="text" value="<?php if(!empty($account_info['name'])) echo $account_info['name'] ; ?>" size="56" id="g_name" name="g_name" readonly="readonly"/></td></tr>
					<tr><td>Email</td><td><input type="text" value="<?php if(!empty($account_info['email'])) echo $account_info['email'] ; ?>" size="56"  id="g_email" name="g_email" readonly="readonly"/></td></tr>
                                        <tr><td colspan=2 align=center><input type="button" value="Change Account" onclick="GloginAuth()"/>
                                        </td></tr>
				</table>
			</div>
		</div>

	</td></tr>
</table>
<table align="center" class="table_cat"  id="upload__form" <?php if(strlen($account_info['gid']) <= 10 ) echo ' style="display:none" ';?> >
	<tr><td>

		<div class="div_main">
			<div class="div_title">Upload Setting</div>
			<div class="div_opt">
				<table class="table_opt">

					<tr><td>Upload Dir : </td>
						<td><select size="1" name="upload_dir" id="upload_dir"></select></td>
					</tr>
					<tr><td>Make download link public : </td><td><input type="checkbox" name="make_public" id="make_public" <?php if($account_info['make_public'] !== 0) echo ' checked="checked" '; ?>/></td></tr>
 				</table>
			</div>
		</div>
	</td></tr>
        <tr><td>
<br><br>
	</td></tr>

                                       <tr><td colspan=2 align=center><input type='submit'  id='submit_gupload' value='Save Setting' <?php if(strlen($account_info['gid']) <= 10 ) echo ' style="display:none" ';?> />
                                        </td></tr>
        <tr><td>
<br><br>
	</td></tr>
					<tr><td colspan=2 align=center><small> <b> <?php echo base64_decode($C) ; ?></b></small>
                                        </td></tr>

</table>
</form>
<script type="text/javascript">
<?php
echo "var Pinfo = '" . basename(__FILE__) . "'; " ;
echo "var Ainfo = ''; " ;
if(isset($account_info['folder_list'])) {
?>
arrrry   = JSON.parse('<?php echo $account_info['folder_list'] ; ?>');
updatefolderlist(arrrry);
<?php
} 
echo '</script>';
}
function FGET($url) {
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.79 Safari/537.36');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    if (curl_errno($ch)) {
        html_error( 'Unable to connect. API Error');
    }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
if($code != 200) html_error('FAST API Error , Try again after some time. #201726');
    return $data;
}


/**
 *  Plugin Name: Google Drive Upload Plugin
 *  Written By: FastRapidLeech.Com
 *  Date : 07-April-2018
 */

?>

