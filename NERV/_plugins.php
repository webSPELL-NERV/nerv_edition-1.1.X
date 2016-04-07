<<<<<<< HEAD
<?php
include_once("../_magi_class.php");

class plugins extends magi_class{
	var $_plugin_folder = "";
	var $_current_language = "";
	var $_fallback = "uk";
	
	function plugins($plugin_folder = ""){
		$this->_plugin_folder = $plugin_folder;
		
		//$GLOBALS['userID'];
		if($GLOBALS['user_language'] != ""){
			$this->_current_language = $GLOBALS['user_language'];
		}else if($GLOBALS['default_language']!=""){
			$this->_current_language = $GLOBALS['default_language'];
		}
	}
	
	private function infoExists($plugin_folder){
		if(file_exists($plugin_folder."/_info.json")){
			return true;
		}
		return false;
	}
	
	private function getInfo($plugin_folder){
		if($this->infoExists("plugins/$plugin_folder")){
			$file = file_get_contents("plugins/".$plugin_folder."/_info.json");
			$json = json_decode($file, true);
			return $json['plugin'];
		}
		return false;
	}
	
	private function isComplete($plugin_folder){
		$info = $this->getInfo($plugin_folder);
		if($this->infoExists("plugins/$plugin_folder") && $info['installed']){
			return true;
		}
		return false;
	}
	
	public function isSite($sitename){
		$plugins = $this->getPlugins();
		foreach($plugins as $plugin){
			$name = $plugin['plugin']['info']['name'];
			$folder = $plugin['plugin']['info']['folder'];
			$sites = $plugin['plugin']['sites'];
			
			if(in_array($sitename, $sites)){
				return "plugins/".$folder."/".$sitename;
			}
		}
		return false;
	}
	
	public function isAdminSite($sitename){
		chdir("../");
		$plugins = $this->getPlugins();
		foreach($plugins as $plugin){
			$name = $plugin['plugin']['info']['name'];
			$folder = $plugin['plugin']['info']['folder'];
			$adminsite = $plugin['plugin']['admin'];
			if($adminsite == $sitename){
				return "plugins/".$folder."/".$sitename;
			}
		}
		return false;
	}
	
	public function getPlugins(){
		$plugins = array();
		$dirs = array_filter(glob('plugins/*'), 'is_dir');
		foreach($dirs as $dir){
			if(file_exists($dir."/_info.json")){
				$file = file_get_contents($dir."/_info.json");
				$json = json_decode($file, true);
				$plugins[] = $json;
			}
		}
		return $plugins;
	}
	
	public function getHeader(){
		echo ($this->getStyles()).($this->getScripts());
	}
	
	private function getStyles(){
		$plugins = $this->getPlugins();
		$styles = "";
		if(count($plugins)>0){
			foreach($plugins as $plugin){
				$plugin_path = $plugin['plugin']['info']['folder'];
				$plugin_styles = $plugin['plugin']['styles'];
				foreach($plugin_styles as $style){
					$styles .= ' 
					<link href="plugins/'.$plugin_path.'/css/'.$style.'" rel="stylesheet" type="text/css" /> 
					'.PHP_EOL;
				}
			}
		}
		return $styles;
	}
	
	private function getScripts(){
		$plugins = $this->getPlugins();
		$scripts = "";
		if(count($plugins)>0){
			foreach($plugins as $plugin){
				$plugin_path = $plugin['plugin']['info']['folder'];
				$plugin_scripts = $plugin['plugin']['scripts'];
				foreach($plugin_scripts as $script){
					$scripts .= ' 
					<script src="plugins/'.$plugin_path.'/js/'.$script.'" type="text/javascript"></script> 
					'.PHP_EOL;
				}
			}
		}
		return $scripts;
	}
	
	public function showWidget($name, $curr_id = ""){
		$plugin_folder = $this->_plugin_folder;
		$plugin_path   = "plugins/$plugin_folder";
		if($this->isComplete($plugin_folder)){
			$plugin = $this->getInfo($plugin_folder);
			$widgets = $plugin['widgets'];
			if(in_array($name, $widgets)){
				$widget_file = "$plugin_path/".$name;
				include($widget_file);
				if(isset($widget)){
					return $widget;
				}
			}
		}
		return false;
	}

	
	public function registerWidget($position, $description, $template_file = "default_widget_box"){
		$select_sql = "SELECT position FROM ".PREFIX."widgets WHERE position LIKE '$position' && plugin_folder IS NULL && widget_file IS NULL";
		$select_result = $this->safe_query($select_sql);
		if(!mysqli_num_rows($select_result)>0){
			$register_sql = "INSERT INTO ".PREFIX."widgets (position, description) VALUES ('".$position."','".$description."')";
			$result = $this->safe_query($register_sql);
		}else{
			$select_all_widgets = "SELECT id,plugin_folder, widget_file, sort FROM ".PREFIX."widgets WHERE position LIKE '$position' AND plugin_folder IS NOT NULL && widget_file IS NOT NULL ORDER BY sort ASC";
			$result_all_widgets = $this->safe_query($select_all_widgets);
			$widgets_templates = "<div class='panel-body'>No Widgets added.</div>";
			$curr_widget_template = false;
			if(mysqli_num_rows($result_all_widgets)>0){
				$widgets_templates = "";
				while($widget = mysqli_fetch_array($result_all_widgets)){
					$curr_id 	= $widget['id'];
					$curr_plugin_folder = $widget['plugin_folder'];
					$curr_widget_file	= $widget['widget_file'];
					$this->_plugin_folder = $curr_plugin_folder;
					$curr_widget_template = $this->showWidget($curr_widget_file, $curr_id);
					if($curr_widget_template){
						$content = $curr_widget_template;
						$widgets_templates .= $this->view_template($template_file, "widget_box", array(
							"widgets_templates" => $content
						), true);
					}
				}
			}else{
				$curr_widget_template = true;
			}
			if($curr_widget_template){
				$this->view_template($template_file, "header");
				$variables = array(
					"widgets_templates" => $widgets_templates
				);
				$this->view_template($template_file, "content", $variables);
				$this->view_template($template_file, "footer");
			}
		}
	}

	
	public function deletePosition($position){
		$delete_sql = "DELETE FROM ".PREFIX."widgets WHERE position LIKE '$position'";
		$delete_result = $this->safe_query($delete_sql);
		return $delete_result;
	}
	
	public function insertWidgetToPosition($position, $widget_file, $sort){
		$plugins = $this->getPlugins();
		$plugin_folder = false;
		foreach($plugins as $plugin){
			if(in_array($widget_file, $plugin['plugin']['widgets'])){
				$plugin_folder = $plugin['plugin']['info']['folder'];
				break;
			}
		}
		if($plugin_folder){
			$insert_sql = "INSERT INTO ".PREFIX."widgets (
				position,
				plugin_folder,
				widget_file,
				sort
			) VALUES (
				'$position',
				'$plugin_folder',
				'$widget_file',
				$sort
			)";
			$result = $this->safe_query($insert_sql);
			return $result;
		}else{
			echo "plugin not found";
			return false;
		}
	}
	
	public function sortwidget($id, $sort){
		$update_sql =  "UPDATE ".PREFIX."widgets SET sort=$sort WHERE id LIKE '$id' ";
		$update_result = $this->safe_query($update_sql);
		return $update_result;
	}
	
	public function countAllWidgetsOfPosition($position){
		$select_query = "SELECT id FROM ".PREFIX."widgets WHERE position LIKE '$position' && plugin_folder IS NOT NULL && widget_file IS NOT NULL";
		$select_result = $this->safe_query($select_query);
		return mysqli_num_rows($select_result);
	}
	
	public function deleteWidgetByID($id){
		$delete_sql = "DELETE FROM ".PREFIX."widgets WHERE id LIKE '$id'";
		$delete_result = $this->safe_query($delete_sql);
		return $delete_result;
	}
	
	public function getAllWidgetsOfPosition($position){
		$select_query = "SELECT id,description,position,plugin_folder, widget_file,create_date, sort FROM ".PREFIX."widgets WHERE position LIKE '$position' && plugin_folder IS NOT NULL && widget_file IS NOT NULL ORDER BY sort ASC";
		$select_result = $this->safe_query($select_query);
		$widgets = array();
		while($widget = mysqli_fetch_array($select_result)){
			$widgets[] = $widget;
		}
		return $widgets;
	}
	
	public function getAllWidgetsPositions(){
		$select_query = "SELECT id,description,position,create_date FROM ".PREFIX."widgets WHERE position IS NOT NULL && plugin_folder IS NULL && widget_file IS NULL";
		$select_result = $this->safe_query($select_query);
		$positions = array();
		while($position = mysqli_fetch_array($select_result)){
			$positions[] = $position;
		}
		return $positions;
	}
	
	
	public function view($template, $section, $variables = array(), $echo = false){
		$template = $this->get_Template($template, $section);
		foreach($variables as $key=>$value){
			$template = str_replace("__".$key, $value, $template);
		}
		$template = $this->replaceLanguage($template);
		if($echo){
			return $template;
		}
		echo $template;
	}
	
	
	public function get_Template($template, $section){
		$plugin_folder = $this->_plugin_folder;
		$plugin_path   = "plugins/$plugin_folder";
		if($this->isComplete($plugin_folder)){
			$template_file = $plugin_path."/templates/".$template.".html";
			if(file_exists($template_file)){
				$file = file_get_contents($template_file);
				$section = strtoupper($section);
				$section_part = $this->get_string_between($file, "<!-- ".$section."_START -->", "<!-- ".$section."_END -->");
				return $section_part;
			}
		}
		return false;
	}
	
	public function getAdminMenu(){
		chdir("../");
		$plugins = $this->getPlugins();
		$menu = "";
		if(count($plugins)>0){
			foreach($plugins as $plugin){
				if($this->isComplete($plugin['plugin']['info']['folder'])){
					$name = $plugin['plugin']['info']['name'];
					$folder = $plugin['plugin']['info']['folder'];
					$adminsite = $plugin['plugin']['admin'];
					if(file_exists("plugins/$folder/$adminsite.php")){
						$menu .= "<li><a href='admincenter.php?site=$adminsite'>$name</a></li>";
					}
				}
			}
		}
		chdir("admin");
		echo $menu;
	}
	
	public function install($plugin_folder = ""){
		if($plugin_folder==""){
			$plugin_folder = $this->_plugin_folder;
		}else{
			$this->_plugin_folder = $plugin_folder;
		}
		
		if(!$this->isInstalled()){
			$plugin_path = "plugins/".$plugin_folder."/";
			if(file_exists($plugin_path.$plugin_folder."_install.php")){
				include($plugin_path.$plugin_folder."_install.php");
				if(isset($install_result)){
					if($install_result){
						@unlink($plugin_path.$plugin_folder."_install.php");
						
						$jsonString = file_get_contents($plugin_path.'_info.json');
						$data = json_decode($jsonString, true);
				
						$data["plugin"]["installed"] = 1;
						
						$newJsonString = json_encode($data);
						file_put_contents($plugin_path.'_info.json', $newJsonString);
						
						return $install_result;
					}else{
						echo "Not installed.";
					}
				}else{
					echo "No variable.";
				}
			}else{
				echo "File not exists";
			}
		}else{
			echo "It is just installed.";
		}
		return false;
	}
	
	public function uninstall($plugin_folder = ""){
		if($plugin_folder==""){
			$plugin_folder = $this->_plugin_folder;
		}else{
			$this->_plugin_folder = $plugin_folder;
		}
		if($this->isInstalled()){
			$plugin_path = "plugins/".$plugin_folder."/";
			$infos = $this->getInfo($plugin_folder);
			$db_tables = $infos["tables"];
			foreach($db_tables as $table){
				$delete_query = "DROP TABLE IF EXISTS ".PREFIX.$table."";
				if(!$result = $this->safe_query($delete_query)){
					return false;
				}
			}
			if($this->Delete_Folder('plugins/'.$plugin_folder.'/')){
				return true;
			}
			return false;
		}else{
			echo "It is not installed.";
		}
		return false;
	}
	
	public function isInstalled($plugin_folder = ""){
		if($plugin_folder==""){
			$plugin_folder = $this->_plugin_folder;
		}else{
			$this->_plugin_folder = $plugin_folder;
		}
		$json = $this->getInfo($plugin_folder);
		$isInstalled = $json['installed'];
		return $isInstalled && !file_exists("../plugins/".$plugin_folder."/".$plugin_folder."_install.php");
	}
	
	public function replaceLanguage($inputString){
		$lang_array_fallback = $this->getLanguageFile($this->_fallback);
		$lang_array = $this->getLanguageFile($this->_current_language);
		$lang_array = array_merge($lang_array_fallback, $lang_array);
		foreach($lang_array as $control=>$word){
			$inputString = str_replace("%".$control."%", $word, $inputString);
		}
		return $inputString;
	}
	
	public function getTranslation($inputControl){
		$lang_array_fallback = $this->getLanguageFile($this->_fallback);
		$lang_array = $this->getLanguageFile($this->_current_language);
		$lang_array = array_merge($lang_array_fallback, $lang_array);
		if(array_key_exists ( $inputControl , $lang_array )){
			return $lang_array[$inputControl];
		}
		return false;
	}
	
	private function getLanguageFile($language){
		$plugin_folder = $this->_plugin_folder;
		$plugin_info = $this->getInfo($plugin_folder);
		$plugin_path = "plugins/".$plugin_folder."/languages/";
		if(file_exists($plugin_path.$language.".php")){
			include($plugin_path.$language.".php");
			if(isset($_languages)){
				return $_languages;
			}
		}else{
			// fallback
			include($plugin_path.$plugin_info["default_language"].".php");
			if(isset($_languages)){
				return $_languages;
			}
		}
		return false;
	}
	
}
=======
<?php
include_once("../_magi_class.php");

class plugins extends magi_class{
	var $_plugin_folder = "";
	var $_current_language = "";
	var $_fallback = "uk";
	
	function plugins($plugin_folder = ""){
		$this->_plugin_folder = $plugin_folder;
		
		//$GLOBALS['userID'];
		if($GLOBALS['user_language'] != ""){
			$this->_current_language = $GLOBALS['user_language'];
		}else if($GLOBALS['default_language']!=""){
			$this->_current_language = $GLOBALS['default_language'];
		}
	}
	
	private function infoExists($plugin_folder){
		if(file_exists($plugin_folder."/_info.json")){
			return true;
		}
		return false;
	}
	
	private function getInfo($plugin_folder){
		if($this->infoExists("plugins/$plugin_folder")){
			$file = file_get_contents("plugins/".$plugin_folder."/_info.json");
			$json = json_decode($file, true);
			return $json['plugin'];
		}
		return false;
	}
	
	private function isComplete($plugin_folder){
		$info = $this->getInfo($plugin_folder);
		if($this->infoExists("plugins/$plugin_folder") && $info['installed']){
			return true;
		}
		return false;
	}
	
	public function isSite($sitename){
		$plugins = $this->getPlugins();
		foreach($plugins as $plugin){
			$name = $plugin['plugin']['info']['name'];
			$folder = $plugin['plugin']['info']['folder'];
			$sites = $plugin['plugin']['sites'];
			
			if(in_array($sitename, $sites)){
				return "plugins/".$folder."/".$sitename;
			}
		}
		return false;
	}
	
	public function isAdminSite($sitename){
		chdir("../");
		$plugins = $this->getPlugins();
		foreach($plugins as $plugin){
			$name = $plugin['plugin']['info']['name'];
			$folder = $plugin['plugin']['info']['folder'];
			$adminsite = $plugin['plugin']['admin'];
			if($adminsite == $sitename){
				return "plugins/".$folder."/".$sitename;
			}
		}
		return false;
	}
	
	public function getPlugins(){
		$plugins = array();
		$dirs = array_filter(glob('plugins/*'), 'is_dir');
		foreach($dirs as $dir){
			if(file_exists($dir."/_info.json")){
				$file = file_get_contents($dir."/_info.json");
				$json = json_decode($file, true);
				$plugins[] = $json;
			}
		}
		return $plugins;
	}
	
	public function getHeader(){
		echo ($this->getStyles()).($this->getScripts());
	}
	
	private function getStyles(){
		$plugins = $this->getPlugins();
		$styles = "";
		if(count($plugins)>0){
			foreach($plugins as $plugin){
				$plugin_path = $plugin['plugin']['info']['folder'];
				$plugin_styles = $plugin['plugin']['styles'];
				foreach($plugin_styles as $style){
					$styles .= ' 
					<link href="plugins/'.$plugin_path.'/css/'.$style.'" rel="stylesheet" type="text/css" /> 
					'.PHP_EOL;
				}
			}
		}
		return $styles;
	}
	
	private function getScripts(){
		$plugins = $this->getPlugins();
		$scripts = "";
		if(count($plugins)>0){
			foreach($plugins as $plugin){
				$plugin_path = $plugin['plugin']['info']['folder'];
				$plugin_scripts = $plugin['plugin']['scripts'];
				foreach($plugin_scripts as $script){
					$scripts .= ' 
					<script src="plugins/'.$plugin_path.'/js/'.$script.'" type="text/javascript"></script> 
					'.PHP_EOL;
				}
			}
		}
		return $scripts;
	}
	
	public function showWidget($name, $curr_id = ""){
		$plugin_folder = $this->_plugin_folder;
		$plugin_path   = "plugins/$plugin_folder";
		if($this->isComplete($plugin_folder)){
			$plugin = $this->getInfo($plugin_folder);
			$widgets = $plugin['widgets'];
			if(in_array($name, $widgets)){
				$widget_file = "$plugin_path/".$name;
				include($widget_file);
				if(isset($widget)){
					return $widget;
				}
			}
		}
		return false;
	}

	
	public function registerWidget($position, $description, $template_file = "default_widget_box"){
		$select_sql = "SELECT position FROM ".PREFIX."widgets WHERE position LIKE '$position' && plugin_folder IS NULL && widget_file IS NULL";
		$select_result = $this->safe_query($select_sql);
		if(!mysqli_num_rows($select_result)>0){
			$register_sql = "INSERT INTO ".PREFIX."widgets (position, description) VALUES ('".$position."','".$description."')";
			$result = $this->safe_query($register_sql);
		}else{
			$select_all_widgets = "SELECT id,plugin_folder, widget_file, sort FROM ".PREFIX."widgets WHERE position LIKE '$position' AND plugin_folder IS NOT NULL && widget_file IS NOT NULL ORDER BY sort ASC";
			$result_all_widgets = $this->safe_query($select_all_widgets);
			$widgets_templates = "<div class='panel-body'>No Widgets added.</div>";
			$curr_widget_template = false;
			if(mysqli_num_rows($result_all_widgets)>0){
				$widgets_templates = "";
				while($widget = mysqli_fetch_array($result_all_widgets)){
					$curr_id 	= $widget['id'];
					$curr_plugin_folder = $widget['plugin_folder'];
					$curr_widget_file	= $widget['widget_file'];
					$this->_plugin_folder = $curr_plugin_folder;
					$curr_widget_template = $this->showWidget($curr_widget_file, $curr_id);
					if($curr_widget_template){
						$content = $curr_widget_template;
						$widgets_templates .= $this->view_template($template_file, "widget_box", array(
							"widgets_templates" => $content
						), true);
					}
				}
			}else{
				$curr_widget_template = true;
			}
			if($curr_widget_template){
				$this->view_template($template_file, "header");
				$variables = array(
					"widgets_templates" => $widgets_templates
				);
				$this->view_template($template_file, "content", $variables);
				$this->view_template($template_file, "footer");
			}
		}
	}

	
	public function deletePosition($position){
		$delete_sql = "DELETE FROM ".PREFIX."widgets WHERE position LIKE '$position'";
		$delete_result = $this->safe_query($delete_sql);
		return $delete_result;
	}
	
	public function insertWidgetToPosition($position, $widget_file, $sort){
		$plugins = $this->getPlugins();
		$plugin_folder = false;
		foreach($plugins as $plugin){
			if(in_array($widget_file, $plugin['plugin']['widgets'])){
				$plugin_folder = $plugin['plugin']['info']['folder'];
				break;
			}
		}
		if($plugin_folder){
			$insert_sql = "INSERT INTO ".PREFIX."widgets (
				position,
				plugin_folder,
				widget_file,
				sort
			) VALUES (
				'$position',
				'$plugin_folder',
				'$widget_file',
				$sort
			)";
			$result = $this->safe_query($insert_sql);
			return $result;
		}else{
			echo "plugin not found";
			return false;
		}
	}
	
	public function sortwidget($id, $sort){
		$update_sql =  "UPDATE ".PREFIX."widgets SET sort=$sort WHERE id LIKE '$id' ";
		$update_result = $this->safe_query($update_sql);
		return $update_result;
	}
	
	public function countAllWidgetsOfPosition($position){
		$select_query = "SELECT id FROM ".PREFIX."widgets WHERE position LIKE '$position' && plugin_folder IS NOT NULL && widget_file IS NOT NULL";
		$select_result = $this->safe_query($select_query);
		return mysqli_num_rows($select_result);
	}
	
	public function deleteWidgetByID($id){
		$delete_sql = "DELETE FROM ".PREFIX."widgets WHERE id LIKE '$id'";
		$delete_result = $this->safe_query($delete_sql);
		return $delete_result;
	}
	
	public function getAllWidgetsOfPosition($position){
		$select_query = "SELECT id,description,position,plugin_folder, widget_file,create_date, sort FROM ".PREFIX."widgets WHERE position LIKE '$position' && plugin_folder IS NOT NULL && widget_file IS NOT NULL ORDER BY sort ASC";
		$select_result = $this->safe_query($select_query);
		$widgets = array();
		while($widget = mysqli_fetch_array($select_result)){
			$widgets[] = $widget;
		}
		return $widgets;
	}
	
	public function getAllWidgetsPositions(){
		$select_query = "SELECT id,description,position,create_date FROM ".PREFIX."widgets WHERE position IS NOT NULL && plugin_folder IS NULL && widget_file IS NULL";
		$select_result = $this->safe_query($select_query);
		$positions = array();
		while($position = mysqli_fetch_array($select_result)){
			$positions[] = $position;
		}
		return $positions;
	}
	
	
	public function view($template, $section, $variables = array(), $echo = false){
		$template = $this->get_Template($template, $section);
		foreach($variables as $key=>$value){
			$template = str_replace("__".$key, $value, $template);
		}
		$template = $this->replaceLanguage($template);
		if($echo){
			return $template;
		}
		echo $template;
	}
	
	
	public function get_Template($template, $section){
		$plugin_folder = $this->_plugin_folder;
		$plugin_path   = "plugins/$plugin_folder";
		if($this->isComplete($plugin_folder)){
			$template_file = $plugin_path."/templates/".$template.".html";
			if(file_exists($template_file)){
				$file = file_get_contents($template_file);
				$section = strtoupper($section);
				$section_part = $this->get_string_between($file, "<!-- ".$section."_START -->", "<!-- ".$section."_END -->");
				return $section_part;
			}
		}
		return false;
	}
	
	public function getAdminMenu(){
		chdir("../");
		$plugins = $this->getPlugins();
		$menu = "";
		if(count($plugins)>0){
			foreach($plugins as $plugin){
				if($this->isComplete($plugin['plugin']['info']['folder'])){
					$name = $plugin['plugin']['info']['name'];
					$folder = $plugin['plugin']['info']['folder'];
					$adminsite = $plugin['plugin']['admin'];
					if(file_exists("plugins/$folder/$adminsite.php")){
						$menu .= "<li><a href='admincenter.php?site=$adminsite'>$name</a></li>";
					}
				}
			}
		}
		chdir("admin");
		echo $menu;
	}
	
	public function install($plugin_folder = ""){
		if($plugin_folder==""){
			$plugin_folder = $this->_plugin_folder;
		}else{
			$this->_plugin_folder = $plugin_folder;
		}
		
		if(!$this->isInstalled()){
			$plugin_path = "plugins/".$plugin_folder."/";
			if(file_exists($plugin_path.$plugin_folder."_install.php")){
				include($plugin_path.$plugin_folder."_install.php");
				if(isset($install_result)){
					if($install_result){
						@unlink($plugin_path.$plugin_folder."_install.php");
						
						$jsonString = file_get_contents($plugin_path.'_info.json');
						$data = json_decode($jsonString, true);
				
						$data["plugin"]["installed"] = 1;
						
						$newJsonString = json_encode($data);
						file_put_contents($plugin_path.'_info.json', $newJsonString);
						
						return $install_result;
					}else{
						echo "Not installed.";
					}
				}else{
					echo "No variable.";
				}
			}else{
				echo "File not exists";
			}
		}else{
			echo "It is just installed.";
		}
		return false;
	}
	
	public function uninstall($plugin_folder = ""){
		if($plugin_folder==""){
			$plugin_folder = $this->_plugin_folder;
		}else{
			$this->_plugin_folder = $plugin_folder;
		}
		if($this->isInstalled()){
			$plugin_path = "plugins/".$plugin_folder."/";
			$infos = $this->getInfo($plugin_folder);
			$db_tables = $infos["tables"];
			foreach($db_tables as $table){
				$delete_query = "DROP TABLE IF EXISTS ".PREFIX.$table."";
				if(!$result = $this->safe_query($delete_query)){
					return false;
				}
			}
			if($this->Delete_Folder('plugins/'.$plugin_folder.'/')){
				return true;
			}
			return false;
		}else{
			echo "It is not installed.";
		}
		return false;
	}
	
	public function isInstalled($plugin_folder = ""){
		if($plugin_folder==""){
			$plugin_folder = $this->_plugin_folder;
		}else{
			$this->_plugin_folder = $plugin_folder;
		}
		$json = $this->getInfo($plugin_folder);
		$isInstalled = $json['installed'];
		return $isInstalled && !file_exists("../plugins/".$plugin_folder."/".$plugin_folder."_install.php");
	}
	
	public function replaceLanguage($inputString){
		$lang_array_fallback = $this->getLanguageFile($this->_fallback);
		$lang_array = $this->getLanguageFile($this->_current_language);
		$lang_array = array_merge($lang_array_fallback, $lang_array);
		foreach($lang_array as $control=>$word){
			$inputString = str_replace("%".$control."%", $word, $inputString);
		}
		return $inputString;
	}
	
	public function getTranslation($inputControl){
		$lang_array_fallback = $this->getLanguageFile($this->_fallback);
		$lang_array = $this->getLanguageFile($this->_current_language);
		$lang_array = array_merge($lang_array_fallback, $lang_array);
		if(array_key_exists ( $inputControl , $lang_array )){
			return $lang_array[$inputControl];
		}
		return false;
	}
	
	private function getLanguageFile($language){
		$plugin_folder = $this->_plugin_folder;
		$plugin_info = $this->getInfo($plugin_folder);
		$plugin_path = "plugins/".$plugin_folder."/languages/";
		if(file_exists($plugin_path.$language.".php")){
			include($plugin_path.$language.".php");
			if(isset($_languages)){
				return $_languages;
			}
		}else{
			// fallback
			include($plugin_path.$plugin_info["default_language"].".php");
			if(isset($_languages)){
				return $_languages;
			}
		}
		return false;
	}
	
}
>>>>>>> origin/master
?>