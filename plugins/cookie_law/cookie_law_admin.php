<?php
	ini_set('display_errors', 'On');
	$cookie_law_class = new plugins("cookie_law", getuserlanguage($userID));

	
	if(isset($_GET['action'])){
		$action = $_GET['action'];
		if($action=="save"){
			if(isset($_POST['description']) && isset($_POST['days'])){
				$id = $_POST['id'];
				$new_description = $_POST['description'];
				$new_days = $_POST['days'];
				
				$update_query = "UPDATE ".PREFIX."cookie_law SET
									text = '$new_description',
									days = $new_days
								 WHERE id = '$id'";
				
				$result = isafe_query($update_query);
				if($result){
					$title = $cookie_law_class->getTranslation("message_success_title");
					$content = $cookie_law_class->getTranslation("message_success_content");
					$cookie_law_class->redirect_with_message($title, $content, "admincenter.php?site=cookie_law_admin", 2000,"","success");
				}
			}
		}
	}else{
		// HEADER TEMPLATE
		$cookie_law_class->view("admin_cookie", "form_header");
		
		// CONTENT TEMPLATE
		$select_query = "SELECT id,text, days FROM ".PREFIX."cookie_law LIMIT 1";
		$infos = mysqli_fetch_array(isafe_query($select_query));
		
		$variables = array(
			"description" => $infos['text'],
			"days"		  => $infos['days'],
			"id"		  => $infos['id'],
			"form_url"	  => "admincenter.php?site=cookie_law_admin&action=save"
		);
		$cookie_law_class->view("admin_cookie", "form_content", $variables);
		
		// FOOTER TEMPLATE
		$cookie_law_class->view("admin_cookie", "form_footer");
	}
?>
