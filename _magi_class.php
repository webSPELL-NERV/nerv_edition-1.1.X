<?php
	include_once("_settings.php");
	class magi_class {
		// WEBSPELL BASE FUNCTIONS
		function safe_query($query){
			include_once("_settings.php");
			return isafe_query($query); 
		}
		
		function safe_query2($query){
			include_once("_settings.php");
			return safe_query2($query);
		}
		
		function gettemplate($template,$endung="html", $calledfrom="root") {
			include_once("_settings.php");
			include_once("_functions.php");
			return gettemplate($template,$endung="html", $calledfrom="root");
		}
		
		function show_error($errorcontent){
			echo $errorcontent;
		}
		
		
		// WEBSELL NERV FUNCTIONS
		function prepareUrl($url){
			include_once("_settings.php");
			return prepareUrl($url);
		}
		
		function getHpUrl(){
			include_once("_settings.php");
			return $hp_url;
		}
		
		function getControl($plugin_name, $control_name){
			include_once("_settings.php");
			return getControl($plugin_name, $control_name);
		}
		
		function updateControl($plugin_name, $control_name, $new_content){
			include_once("_settings.php");
			return updateControl($plugin_name, $control_name, $new_content);
		}
		
		function insertControl($plugin_name, $control_name, $content, $deletedate = 'NULL'){
			include_once("_settings.php");
			return insertControl($plugin_name, $control_name, $content, $deletedate = 'NULL');
		}
		
		function download($file, $extern = false) {
			if(!$extern) {
				$filename = basename($file);

				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-Type: application/force-download");
				header("Content-Description: File Transfer");

				header("Content-Disposition: attachment; filename=".str_replace(' ', '_', $filename).";");
				header("Content-Length: ".filesize($file));
				header("Content-Transfer-Encoding: binary");

				@readfile($file);
				exit;
			}
			else header("Location: ".$file);
		}
		
		function get_string_between($string, $start, $end){
			$string = ' ' . $string;
			$ini = strpos($string, $start);
			if ($ini == 0) return '';
			$ini += strlen($start);
			$len = strpos($string, $end, $ini) - $ini;
			return substr($string, $ini, $len);
		}
		
		function redirect_with_message($title, $content, $url, $time,$subline="", $type = "primary"){
			eval ("\$messagebox = \"".$this->gettemplate("messagebox")."\";");
			echo $messagebox;
			$this->redirect($url, $time);
		}
		
		function redirect($url, $time = 0){
			$redirectstring = "
			<script>
				setTimeout(function () {
				   window.location.href= '$url'; 
				},$time);
			</script>
			";
			echo $redirectstring;
		}
		
		public function view_template($template, $section, $variables = array(), $variable=false){
			$template = $this->get_Template_view($template, $section);
			foreach($variables as $key=>$value){
				$template = str_replace("__".$key, $value, $template);
			}
			$template = $GLOBALS['_language']->replace($template);
			if($variable){
				return $template;
			}
			echo $template;
			return true;
		}
		
		private function get_Template_view($template, $section, $admin = false){
			if($admin){
				$template_file = "../templates/".$template.".html";
			}else{
				$template_file = "templates/".$template.".html";
			}
			if(file_exists($template_file)){
				$file = file_get_contents($template_file);
				$section = strtoupper($section);
				$section_part = $this->get_string_between($file, "<!-- ".$section."_START -->", "<!-- ".$section."_END -->");
				return $section_part;
			}
			return false;
		}
		
		function Delete_Folder($path)
		{
			if (is_dir($path) === true)
			{
				$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);

				foreach ($files as $file)
				{
					if (in_array($file->getBasename(), array('.', '..')) !== true)
					{
						if ($file->isDir() === true)
						{
							rmdir($file->getPathName());
						}

						else if (($file->isFile() === true) || ($file->isLink() === true))
						{
							unlink($file->getPathname());
						}
					}
				}

				return rmdir($path);
			}
			else if ((is_file($path) === true) || (is_link($path) === true))
			{
				return unlink($path);
			}
		return false;
		}
		
	}
?>