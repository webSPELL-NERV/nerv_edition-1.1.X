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


$error_show = "style='display: none;'";
$error_message = "";
if(isset($_SESSION['ws_login']['error']) && $_SESSION['ws_login']['error'] != ""){
	$error_show = "";
	$error_message = $_SESSION['ws_login']['error'];
	$_SESSION['ws_login']['error'] = "";
}

$_language->read_module('login');

if($loggedin) {
	$username='<a href="'.prepareUrl('index.php?site=profile&id='.$userID.'').'"><b>'.strip_tags(getnickname($userID)).'</b></a>';
	if(isanyadmin($userID)) $admin='&#8226; <a href="admin/admincenter.php" target="_blank">'.$_language->module['admin'].'</a><br />';
	else $admin='';
	if(isclanmember($userID) or iscashadmin($userID)) $cashbox='&#8226; <a href="'.prepareUrl('index.php?site=cash_box').'">'.$_language->module['cash-box'].'</a><br />';
	else $cashbox='';
	$anz=getnewmessages($userID);
	if($anz) {
		$newmessages=' (<b>'.$anz.'</b>)';
	}
	else $newmessages='';
	
	if($getavatar = getavatar($userID)) $l_avatar='<img src="images/avatars/'.$getavatar.'?rand='.date("Ymdgis").'" alt="Avatar" class="img-circle img-thumbnail" />';
	else $l_avatar=$_language->module['n_a'];

	$url_avatar = 'images/avatars/'.$getavatar.'?rand='.date("Ymdgis");
	
	$url_overview		= prepareUrl("index.php?site=loginoverview");
	$url_myprofile		= prepareUrl("index.php?site=myprofile");
	$url_messenger		= prepareUrl("index.php?site=messenger");
	$url_usergallery 	= prepareUrl("index.php?site=usergallery");
	
	eval ("\$logged = \"".gettemplate("logged")."\";");
	echo $logged;
}
else {
	//set sessiontest variable (checks if session works correctly)
	$_SESSION['ws_sessiontest'] = true;
	
	$link_register = prepareUrl("index.php?site=register");
	$link_lostpassword = prepareUrl("index.php?site=lostpassword");
	
	eval ("\$loginform = \"".gettemplate("login")."\";");
	echo $loginform;
}

?>