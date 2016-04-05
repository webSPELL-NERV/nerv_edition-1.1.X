<?php
//nervinc("_magi_class", false);
class translation extends magi_class{
	var $_fallback = "uk";
	var $_selected_language = "";
	
	public function translation($userID){
		include_once("../src/login.php");
		//systeminc("src/login");
		$user_language = getuserlanguage($userID);
		if($user_language != ""){
			$this->_selected_language = $user_language;
		}else if($default_language!=""){
			$this->_selected_language = $default_language;
		}else{
			$this->_selected_language = $this->_fallback;
		}
	}
	
	public function insert($control_name, $languages){
		if($this->insert_control($control_name)){
			foreach($languages as $language => $content){
				$this->update_language($control_name, $language, $content);
			}
		}
		return false;
	}
	
	public function insert_default($control_name, $text){
		if($this->insert_control($control_name)){
			$language = $this->_selected_language;
			$this->update_language($control_name, $language, $text);
		}
		return false;
	}
	
	public function update_language($control_name, $language, $content){
		$update_query = "UPDATE ".PREFIX."translations SET `$language`='$content' WHERE control LIKE '$control_name'";
		$result = $this->safe_query($update_query);
		return $result;
	}
	
	private function insert_control($control_name){
		$insert_query = "INSERT INTO ".PREFIX."translations (control) VALUES ('$control_name')";
		$result = $this->safe_query($insert_query);
		return $result;
	}
	
	public function get($control_name){
		$result = $this->get_normal($control_name);
		if(!$result){
			$temp_lang = $this->_selected_language;
			$this->_selected_language = $this->_fallback;
			if($result = $this->get_normal($control_name)){
				$this->_selected_language = $temp_lang;
				return $result;
			}else{
				$this->_selected_language = $temp_lang;
				return $control_name;
			}
		}
		return $result;
	}
	
	private function get_normal($control_name){
		$lang  = $this->_selected_language;
		if($this->exists($lang)){
			$select_query = "SELECT `$lang` FROM ".PREFIX."translations WHERE control LIKE '$control_name'";
			$result 	  = $this->safe_query($select_query);
			$translation  = mysqli_fetch_array($result);
			return $translation[$lang];
		}else{
			return false;
		}
	}
	
	private function exists($language_name){
		$select_query = "SHOW COLUMNS FROM `".PREFIX."translations` LIKE '$language_name'";
		$result 	  = $this->safe_query($select_query);
		if(mysqli_num_rows($result)>0){
			return true;
		}
		return false;
	}
	
}

?>