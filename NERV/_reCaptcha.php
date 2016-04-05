<?php
include_once("../_magi_class.php");
include_once("_controls.php");

class reCaptcha extends magi_class{
	var $_theme		= "";
	var $_enable 	= "";
	var $_sitekey	= "";
	var $_secretkey = "";
	var $_size		= "";
	var $_user_id	= "";
	var $_controls	= "";
	var $_POSTS		= array();
	
	public function reCaptcha($_POSTS,$userID=0){
		$controls 			= new controls();
		$this->_controls	= $controls;
		$this->_theme		= $controls->getControl("RECAPTCHA", "theme");
		$this->_enable 		= $controls->getControl("RECAPTCHA", "enable");
		$this->_sitekey		= $controls->getControl("RECAPTCHA", "sitekey");
		$this->_secretkey	= $controls->getControl("RECAPTCHA", "secretkey");
		$this->_size		= $controls->getControl("RECAPTCHA", "size");
		$this->_user_id 	= $userID;
		$this->_POSTS		= $_POSTS;
		$this->_init_defaults();
	}
	
	public function _init_defaults(){
		$controls	= $this->_controls;
		$theme 		= $this->_theme;
		$size		= $this->_size;
		$sitekey	= $this->_sitekey;
		$secretkey	= $this->_secretkey;
		$enable		= $this->_enable;
		//6Le3bBUTAAAAALsg0Bt1hAl1ZVxtlFbkR70bRrd6
		//$controls->insertControl("RECAPTCHA", "secretkey", "6Le3bBUTAAAAALsg0Bt1hAl1ZVxtlFbkR70bRrd6");
		if(!$theme){
			$controls->insertControl("RECAPTCHA", "theme", "dark");
			$controls->setControlStyle("RECAPTCHA", "theme", "combobox", array("dark","light"));
			$this->_theme = $controls->getControl("RECAPTCHA", "theme");
		}
		if(!$enable){
			$controls->insertControl("RECAPTCHA", "enable", "1");
			$controls->setControlStyle("RECAPTCHA", "enable", "checkbox", array(1,0));
			$this->_enable = $controls->getControl("RECAPTCHA", "enable");
		}
		if(!$sitekey || !$secretkey){
			$this->_enable = false;
		}
		if(!$size){
			$controls->insertControl("RECAPTCHA", "size", "normal");
			$controls->setControlStyle("RECAPTCHA", "size", "combobox", array("normal","small"));
			$this->_size = $controls->getControl("RECAPTCHA", "size");
		}
	}
	
	public function generate(){
		$_POST 		= $this->_POSTS;
		$theme 		= $this->_theme;
		$size		= $this->_size;
		$sitekey	= $this->_sitekey;
		$enable		= $this->_enable;
		$userID		= $this->_user_id;
		
		if($enable && !($userID)){
			return  '<div class="g-recaptcha" data-size="'.$size.'" data-theme="'.$theme.'" data-sitekey="'.$sitekey.'"></div>';
		}else{
			$_POST['g-recaptcha-response'] = true;
			return "<div></div>";
		}
	}
	
	public function isHuman(){
		$_POST 		= $this->_POSTS;
		$sitekey	= $this->_sitekey;
		$secretkey  = $this->_secretkey;
		
		if(isset($_POST['g-recaptcha-response'])){
			$captcha		=	$_POST['g-recaptcha-response'];
			$ip				=	$_SERVER['REMOTE_ADDR'];
			$response		=	file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretkey."&response=".$captcha."&remoteip=".$ip);
			$responseKeys 	=	json_decode($response,true);
			if(intval($responseKeys["success"]) !== 1) {
				return false;
			} else {
				return true;
			}
		}
	}
}
?>