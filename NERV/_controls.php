<?php
include_once("../_magi_class.php");

class controls extends magi_class{
	var $_translation;
	
	
	public function controls($translation = ""){
		$this->_translation = $translation;
	}
	
	public function getAllPlugins(){
		$select_query = "
			SELECT plugin, count(name) AS childs
			FROM `".PREFIX."custom_controls`
			group by plugin
		";
		$result = $this->safe_query($select_query);
		$plugins = array();
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				$plugins[] = array(
					'plugin' 		=> $row['plugin'],
					'childs'		=> $row['childs']
				);
			}
		}
		return $plugins;
	}
	
	public function getAllControlsOfPlugin($plugin_name){
		$select_query = "
			SELECT id,content, create_date, delete_date, plugin, name, description
			FROM `".PREFIX."custom_controls`
			WHERE (delete_date >= CURRENT_TIMESTAMP OR delete_date IS NULL) AND plugin LIKE '$plugin_name'
		";
		$result = $this->safe_query($select_query);
		$childs = array();
		if(mysqli_num_rows($result) > 0){
			while($row = mysqli_fetch_array($result)){
				$childs[] = array(
					'id' 			=> $row['id'],
					'plugin' 		=> $row['plugin'],
					'name'			=> $row['name'],
					'content' 		=> $row['content'],
					'description' 	=> $row['description']
				);
			}
		}
		return $childs;
	}
	
	public function getControl($plugin_name, $control_name){
		$select_query = "
			SELECT content, create_date, delete_date 
			FROM `".PREFIX."custom_controls`
			WHERE plugin = '$plugin_name' AND name = '$control_name' AND (delete_date >= CURRENT_TIMESTAMP OR delete_date IS NULL)
			LIMIT 1
		";
		$currentControl = mysqli_fetch_array($this->safe_query($select_query));
		$content = $currentControl['content'];
		return $content;
	}
	
	public function getControlID($plugin_name, $control_name){
		$select_query = "
			SELECT id
			FROM `".PREFIX."custom_controls`
			WHERE plugin = '$plugin_name' AND name = '$control_name' AND (delete_date >= CURRENT_TIMESTAMP OR delete_date IS NULL)
			LIMIT 1
		";
		$currentControl = mysqli_fetch_array($this->safe_query($select_query));
		$id = $currentControl['id'];
		return $id;
	}

	public function getControlByID($ctrID){
		$select_query = "
			SELECT content, create_date, delete_date 
			FROM `".PREFIX."custom_controls`
			WHERE id LIKE '$ctrID' AND (delete_date >= CURRENT_TIMESTAMP OR delete_date IS NULL)
			LIMIT 1
		";
		$currentControl = mysqli_fetch_array($this->safe_query($select_query));
		$content = $currentControl['content'];
		return $content;
	}
	
	public function updateControl($plugin_name, $control_name, $new_content){
		$update_query = "
			UPDATE `".PREFIX."custom_controls`
			SET `content` = '$new_content', `edit_date` = CURRENT_TIMESTAMP	
			WHERE `plugin` = '$plugin_name' AND `name` = '$control_name'
		";
		
		$updateControl = $this->safe_query($update_query);
		return $updateControl;
	}

	
	public function deletePlugin($plugin_name){
		$delete_query = "DELETE FROM ".PREFIX."custom_controls WHERE plugin = '$plugin_name'";
		$result = $this->safe_query($delete_query);
		return $result;
	}
	
	public function insertControl($plugin_name, $control_name, $content,$description="", $deletedate = 'NULL'){
		$select_query = "SELECT id FROM `".PREFIX."custom_controls` WHERE `plugin` = '$plugin_name' AND `name` = '$control_name'";
		$countControls = mysqli_num_rows($this->safe_query($select_query));
		if($countControls > 0) return false;
		
		$description_lang_control = "NULL";
		if($description){
			$translation = $this->_translation;
			$description_lang_control = "description_".$plugin_name."_".$control_name."";
			$translation->insert_default("$description_lang_control", "$description");
		}
		
		$insert_query = "
			INSERT INTO `".PREFIX."custom_controls`(`plugin`, `name`, `content` , `description`, `delete_date`,`edit_date`) 
			VALUES (
				'$plugin_name',
				'$control_name',
				'$content',
				'$description_lang_control',
				$deletedate,
				CURRENT_TIMESTAMP
			)
		";
		$insertControl = $this->safe_query($insert_query);
		return $insertControl;
	}
	
	public function setControlStyle($plugin_name, $control_name, $type, $grant){
		$select_query = "SELECT id FROM `".PREFIX."custom_controls` WHERE `plugin` = '$plugin_name' AND `name` = '$control_name'";
		$countControls = mysqli_num_rows($this->safe_query($select_query));
		if($countControls <= 0) return false;
		
		$granted = $grant[0];
		for($i=1;$i<count($grant);$i++){
			$granted .= "##".$grant[$i];
		}
		
		$ctrID = $this->getControlID($plugin_name, $control_name);
		
		$insert_query = "
			INSERT INTO `".PREFIX."custom_controls_form`(`control_id`, `form_type`, `grant_values`) 
			VALUES (
				'$ctrID',
				'$type',
				'$granted'
			)
		";
		$insertControl = $this->safe_query($insert_query);
		return $insertControl;
	}
	
	
	public function evalForm($plugin_name, $control_name, $value){
		$ctrID = $this->getControlID($plugin_name, $control_name);
		$select_query = "SELECT * FROM `".PREFIX."custom_controls_form` WHERE `control_id` = '$ctrID'";
		$result = $this->safe_query($select_query);
		if(mysqli_num_rows($result)>0){
			$form 			= mysqli_fetch_array($result);
			$form_type 		= $form['form_type'];
			$grant_values	= $form['grant_values'];
			$grant_values	= explode("##", $grant_values);
			if($form_type=="checkbox"){
				if($value == "not_checked"){
					return "0";
				}else{
					return "1";
				}
			}
		}
		return $value;
	}
	
	public function generateControlHTML($ctrID, $form_name, $content, $translation = ""){
		$select_query = "SELECT * FROM `".PREFIX."custom_controls_form` WHERE `control_id` = '$ctrID'";
		$result = $this->safe_query($select_query);
		if(mysqli_num_rows($result)>0){
			$form 			= mysqli_fetch_array($result);
			$form_type 		= $form['form_type'];
			$grant_values	= $form['grant_values'];
			$grant_values	= explode("##", $grant_values);
			$return = "Design wurde nicht definiert.";
			if($form_type=="combobox"){
				$return = "<select name='$form_name' class=''>";
				foreach($grant_values as $value){
					$value = $value;
					$translation = $this->_translation;
					$value2 = $translation->get($value);
					$selected = "";
					if($value == $content){ 
						$selected = "selected";
					}
					$return .= "<option $selected value='$value'>$value2</option>";
				}
				$return .= "</select>";
				return $return;
			}else if($form_type=="checkbox"){
				$checked = "";
				if($content == "1"){ 
					$checked = "checked";
				}
				$return = "
					<input type='hidden' name='$form_name' value='not_checked' />
					<input type='checkbox' name='$form_name' value='$content' $checked />
					";
				return $return;
			}
		}
		$return = "<input type='text' name='$form_name' value='$content' />";
		return $return;
	}
	
}

?>