<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2011 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

include("_mysql.php");
include("_settings.php");

// copy pagelock information for session test + deactivated pagelock for checklogin
$closed_tmp = $closed;
$closed = 0;

include("_functions.php");

//settings

$sleep = 1; //idle status for script if password is wrong?

//settings end
$_language->read_module('checklogin');

$get = safe_query("SELECT * FROM ".PREFIX."banned_ips WHERE ip='".$GLOBALS['ip']."'");
if(mysql_num_rows($get) == 0){
	$ws_pwd = md5(stripslashes($_POST['pwd']));
	$ws_user = $_POST['ws_user'];
	
	$check = safe_query("SELECT * FROM ".PREFIX."user WHERE username='".$ws_user."'");
	$anz = mysql_num_rows($check);
	$login = 0;
	
	if(!$closed_tmp AND !isset($_SESSION['ws_sessiontest'])) {
		$error = $_language->module['session_error'];
	}
	else {
		if($anz) {
		
			$check = safe_query("SELECT * FROM ".PREFIX."user WHERE username='".$ws_user."' AND activated='1'");
			if(mysql_num_rows($check)) {
		
				$ds=mysql_fetch_array($check);
		
				// check password
				$login = 0;
				if($ws_pwd == $ds['password']) {
		
					//session
					$_SESSION['ws_auth'] = $ds['userID'].":".$ws_pwd;
					$_SESSION['ws_lastlogin'] = $ds['lastlogin'];
					$_SESSION['referer'] = $_SERVER['HTTP_REFERER'];
					//remove sessiontest variable
					if(isset($_SESSION['ws_sessiontest'])) unset($_SESSION['ws_sessiontest']);
					//cookie
					setcookie("ws_auth", $ds['userID'].":".$ws_pwd, time()+($sessionduration*60*60));					
					//Delete visitor with same IP from whoisonline
					safe_query("DELETE FROM ".PREFIX."whoisonline WHERE ip='".$GLOBALS['ip']."'");
					//Delete IP from failed logins
					safe_query("DELETE FROM ".PREFIX."failed_login_attempts WHERE ip = '".$GLOBALS['ip']."'");
					$login = 1;
					$error = $_language->module['login_successful'];
				}
				elseif(!($ws_pwd == $ds['password'])) {
					if($sleep) sleep(5);
					$get = safe_query("SELECT wrong FROM ".PREFIX."failed_login_attempts WHERE ip = '".$GLOBALS['ip']."'");
					if(mysql_num_rows($get)){
						safe_query("UPDATE ".PREFIX."failed_login_attempts SET wrong = wrong+1 WHERE ip = '".$GLOBALS['ip']."'");
					}
					else{
						safe_query("INSERT INTO ".PREFIX."failed_login_attempts (ip,wrong) VALUES ('".$GLOBALS['ip']."',1)");
					}
					$get = safe_query("SELECT wrong FROM ".PREFIX."failed_login_attempts WHERE ip = '".$GLOBALS['ip']."'");
					if(mysql_num_rows($get)){
						$ban = mysql_fetch_assoc($get);
						if($ban['wrong'] == $max_wrong_pw){
							$bantime = time() + (60*60*3); // 3 hours
							safe_query("INSERT INTO ".PREFIX."banned_ips (ip,deltime,reason) VALUES ('".$GLOBALS['ip']."',".$bantime.",'Possible brute force attack')");
							safe_query("DELETE FROM ".PREFIX."failed_login_attempts WHERE ip = '".$GLOBALS['ip']."'");
						}
					}
					$_SESSION['ws_login']['error'] = $_language->module['invalid_password'];
				}
			}
			else $_SESSION['ws_login']['error'] = $_language->module['not_activated'];
		
		}
		else $_SESSION['ws_login']['error'] = str_replace('%username%', htmlspecialchars($ws_user), $_language->module['no_user']);
	}
}
else{
	$login = 0;
	$data = mysql_fetch_assoc($get);
	$_SESSION['ws_login']['error'] = str_replace('%reason%', $data['reason'], $_language->module['ip_banned']);
}

if($login){
	Header("Location: ".prepareUrl('index.php?site=loginoverview')."");
}else{
	Header("Location: ".prepareUrl('index.php')."");
}

?>
