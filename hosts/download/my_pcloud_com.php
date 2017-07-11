<?php
if (!defined('RAPIDLEECH')) {
    require_once("index.html");
    exit;
}

class my_pcloud_com extends DownloadClass {
private $page, $cookie, $Login;

    public function Download($link) {
$fake = "my_pcloud_com";
global $premium_acc, $Referer;
		global $premium_acc;
                        $this->pA = false ;
if (($_REQUEST['premium_acc'] == 'on' && ($this->pA || (!empty($premium_acc[$fake]['user']) && !empty($premium_acc[$fake]['pass']))))) {
			$user = ($this->pA ? $_REQUEST['premium_user'] : $premium_acc[$fake]['user']);
			$pass = ($this->pA ? $_REQUEST['premium_pass'] : $premium_acc[$fake]['pass']);
			if ($this->pA && !empty($_POST['pA_encrypted'])) {
				$user = decrypt(urldecode($user));
				$pass = decrypt(urldecode($pass));
				unset($_POST['pA_encrypted']);
			}
			
		} 
$this->olink  = $link ;
$fake = str_replace('_', '.', $fake);
$user = strtolower($user);
$code_name = base64_encode ( $user . ":" . $pass );
$filename = DOWNLOAD_DIR . basename('pcloud_' . $code_name . '_dl.php');
$this->filename = $filename  ;
$this->link = $link  ;
$this->info = $show_premium_validity ;
$this->cookie = "";
if (!preg_match('@https?://my\.pcloud\.com/publink/show\?code=\w+@i', $link, $_link)) html_error('Invalid link?.');
if (!function_exists('json_decode')) html_error('Error: Please enable JSON in php.');
$down = $this->FreeDL();
if(!$down)
return $this->CookieLogin($user, $pass);
    }
private function FreeDL() {
$link = $this->link ;
$cookie = $this->cookie ;
		$page = $this->GetPage($link);
		if (!preg_match('@var\s+publinkData\s*=\s*(\{.+?\})\s*;@s', $page, $data)) html_error('Filedata Not Found.');
		$data = json_decode($data[1], true);
		if ($data === NULL) html_error('Error while parsing Filedata JSON.');

		if (!empty($data['result'])) {
			$data['result'] = htmlspecialchars($data['result'], ENT_QUOTES);
			if (!empty($data['error'])) {
				$data['error'] = htmlspecialchars($data['error'], ENT_QUOTES);
				html_error("[Error {$data['result']}] File Error: {$data['error']}");
			}
			html_error('Unknown File Error: ' . $data['result']);
		}

		if (empty($data['downloadlink'])) 
                   { 
                     return false ;
                   } 
		return $this->RedirectDownload($data['downloadlink'], 'my_pcloud_com_placeholder');
	}
private function PremiumDL() {
$link = $this->link ;
$cookie = 'lang=en; pcauth=' . $this->cookie . '; ' ;
		$page = $this->GetPage($link, $cookie);
		if (!preg_match('@var\s+publinkData\s*=\s*(\{.+?\})\s*;@s', $page, $data)) html_error('Filedata Not Found.');
		$data = json_decode($data[1], true);
		if ($data === NULL) html_error('Error while parsing Filedata JSON.');
		if (!empty($data['result'])) {
			$data['result'] = htmlspecialchars($data['result'], ENT_QUOTES);
			if (!empty($data['error'])) {
				$data['error'] = htmlspecialchars($data['error'], ENT_QUOTES);
				html_error("[Error {$data['result']}] File Error: {$data['error']}");
			}
			html_error('Unknown File Error: ' . $data['result']);
		}
		if (empty($data['downloadlink'])) 
                    html_error('Download-Link Not Found.');                 
		return $this->RedirectDownload($data['downloadlink'], 'my_pcloud_com_placeholder');
	}
private function Login($user, $pass) {
if(empty($user))
html_error("Login Details not found!! Add Login Details in /configs/accounts.php");
if(empty($pass))
html_error("Login Details not found!! Add Login Details in /configs/accounts.php");
$cookie = "lang=en; " ;
$test = "https://api.pcloud.com/getapiserver" ;
$result = file_get_contents($test);
$info= json_decode($result, true);
$api = $info["api"][0] ;
$full_api = "https://$api/";
$logpage = "https://my.pcloud.com/#page=login";
$logpost = $full_api . "userinfo";
$stamp = round(microtime(true) * 1000);
$page0 = $this->GetPage($logpage, $cookie);
if (!preg_match('%actionLogin%', $page0, $tmp)) html_error('Login Page Not Available!!');
        $post1 = array();
        $post1['username'] = $user; //Cutting page ;
        $post1['password'] = $pass; //Cutting page ;
        $post1['getauth'] = "1"; //Cutting page ;
        $post1['_t'] = $stamp; //Cutting page ;
        $post1['logout'] = "1";
        $post1['getlastsubscription'] = "1";
$postdata = http_build_query($post1);
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded\r\n'
                     .'Cookie: '. $cookie . '\r\n'
                     .'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
        'content' => $postdata
    )
);
$context  = stream_context_create($opts);
$page2 = file_get_contents($logpost, false, $context);
$info= json_decode($page2, true);
$email_verify = $info["emailverified"] ;
$email = $info["email"] ;
$reg_date = $info["registered"] ;
$auth = $info["auth"] ;
if((!isset($auth)) OR empty($auth))
html_error("Login Failed!!");
if(!$email_verify)
html_error("Verify your email $email before using it with Rapidleech!!");
$user_info_url = $logpost . "?auth=$auth";
$inf = file_get_contents($user_info_url);
$cookie = $cookie . "  pcauth=$auth; ";
$this->CountDown(4); 
$this->cookie = "$auth";
		$this->SaveCookies(); 
		return $this->PremiumDL();
	}
private function CookieLogin($user, $pass) {
		if (empty($user) || empty($pass)) html_error('Login Failed: User or Password is empty.');
		$filename = $this->filename  ;
            if (!file_exists($filename) || ((time() - filemtime($filename)) > 60*60*24*15)) 
               {
                 return $this->Login($user, $pass);
               }				
		else {	
$myfile = file($filename);
$testcookie = $myfile[1];
$test = "https://api.pcloud.com/getapiserver" ;
$result = file_get_contents($test);
$info= json_decode($result, true);
$api = $info["api"][0] ;
$full_api = "https://$api/";
$test = $full_api . "userinfo?auth=" . $testcookie;
                        $pagec = file_get_contents($test);
                        $linfo= json_decode($pagec, true);
                        $prem = $linfo["premium"] ;
                        $login = $linfo["email"] ;
if(!$login)
 {                       return $this->Login($user, $pass); }
if(!$prem)
{
html_error('Account is not Premium.');
}
			$this->cookie = $testcookie; // Update cookies
			return $this->PremiumDL();
		}
	}
	private function SaveCookies() 
            {
$filename = $this->filename  ;
$coded = $this->cookie ;             
$myfile = fopen("$filename", "wr") or die("Unable to open file!");
fwrite($myfile, "<?php exit(); ?>" . PHP_EOL . $coded);
fclose($myfile);
            }
}

/*
 * Download Plugin By FASTRAPIDLEECH (29 May 2017)
 */

?>