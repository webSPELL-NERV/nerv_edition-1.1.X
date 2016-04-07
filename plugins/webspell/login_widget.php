<<<<<<< HEAD
<?php

	$webspell_class = new plugins("webspell");
	$widget = false;

	$userID = $GLOBALS['userID'];
	$loggedin = $GLOBALS['loggedin'];

	if($loggedin){
		$username='<a href="'.prepareUrl('index.php?site=profile&id='.$userID.'').'"><b>'.strip_tags(getnickname($userID)).'</b></a>';
		if(isanyadmin($userID)) $admin='<i class="fa fa-user-secret"></i> <a href="admin/admincenter.php" target="_blank">'.$webspell_class->getTranslation("admin").'</a><br />';
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
		
		$content_variables = array(
			"cashbox" 			=> $cashbox,
			"l_avatar" 			=> $l_avatar,
			"url_avatar" 		=> $url_avatar,
			"url_usergallery" 	=> $url_usergallery,
			"url_messenger" 	=> $url_messenger,
			"url_myprofile" 	=> $url_myprofile,
			"url_overview" 		=> $url_overview,
			"newmessages" 		=> $newmessages,
			"username" 			=> $username,
			"admin" 			=> $admin
		);
		
		$widget  =  $webspell_class->view("login_widget", "logged_header", array(), true);
		$widget .=  $webspell_class->view("login_widget", "logged_content", $content_variables, true);
		$widget .=  $webspell_class->view("login_widget", "logged_footer", array(), true);
	}else{
		//set sessiontest variable (checks if session works correctly)
		$_SESSION['ws_sessiontest'] = true;
		
		$error_show = "style='display: none;'";
		$error_message = "";
		if(isset($_SESSION['ws_login']['error']) && $_SESSION['ws_login']['error'] != ""){
			$error_show = "";
			$error_message = $_SESSION['ws_login']['error'];
			$_SESSION['ws_login']['error'] = "";
		}

		
		$link_register = prepareUrl("index.php?site=register");
		$link_lostpassword = prepareUrl("index.php?site=lostpassword");
		
		$content_variables = array(
			"error_show"		=> $error_show,
			"error_message"		=> $error_message
		);
		
		$footer_variables = array(
			"link_register"		=> $link_register,
			"link_lostpassword" => $link_lostpassword
		);
		
		$widget  =  $webspell_class->view("login_widget", "login_form_header", array(), true);
		$widget .=  $webspell_class->view("login_widget", "login_form_content", $content_variables, true);
		$widget .=  $webspell_class->view("login_widget", "login_form_footer", $footer_variables, true);
	}


=======
<?php

	$webspell_class = new plugins("webspell");
	$widget = false;

	$userID = $GLOBALS['userID'];
	$loggedin = $GLOBALS['loggedin'];

	if($loggedin){
		$username='<a href="'.prepareUrl('index.php?site=profile&id='.$userID.'').'"><b>'.strip_tags(getnickname($userID)).'</b></a>';
		if(isanyadmin($userID)) $admin='<i class="fa fa-user-secret"></i> <a href="admin/admincenter.php" target="_blank">'.$webspell_class->getTranslation("admin").'</a><br />';
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
		
		$content_variables = array(
			"cashbox" 			=> $cashbox,
			"l_avatar" 			=> $l_avatar,
			"url_avatar" 		=> $url_avatar,
			"url_usergallery" 	=> $url_usergallery,
			"url_messenger" 	=> $url_messenger,
			"url_myprofile" 	=> $url_myprofile,
			"url_overview" 		=> $url_overview,
			"newmessages" 		=> $newmessages,
			"username" 			=> $username,
			"admin" 			=> $admin
		);
		
		$widget  =  $webspell_class->view("login_widget", "logged_header", array(), true);
		$widget .=  $webspell_class->view("login_widget", "logged_content", $content_variables, true);
		$widget .=  $webspell_class->view("login_widget", "logged_footer", array(), true);
	}else{
		//set sessiontest variable (checks if session works correctly)
		$_SESSION['ws_sessiontest'] = true;
		
		$error_show = "style='display: none;'";
		$error_message = "";
		if(isset($_SESSION['ws_login']['error']) && $_SESSION['ws_login']['error'] != ""){
			$error_show = "";
			$error_message = $_SESSION['ws_login']['error'];
			$_SESSION['ws_login']['error'] = "";
		}

		
		$link_register = prepareUrl("index.php?site=register");
		$link_lostpassword = prepareUrl("index.php?site=lostpassword");
		
		$content_variables = array(
			"error_show"		=> $error_show,
			"error_message"		=> $error_message
		);
		
		$footer_variables = array(
			"link_register"		=> $link_register,
			"link_lostpassword" => $link_lostpassword
		);
		
		$widget  =  $webspell_class->view("login_widget", "login_form_header", array(), true);
		$widget .=  $webspell_class->view("login_widget", "login_form_content", $content_variables, true);
		$widget .=  $webspell_class->view("login_widget", "login_form_footer", $footer_variables, true);
	}


>>>>>>> origin/master
?>