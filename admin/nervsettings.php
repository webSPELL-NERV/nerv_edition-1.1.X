<?php

define('DEBUG', "ON");
error_reporting(E_ALL); // 0 = public mode, E_ALL = development-mode
ini_set('display_errors', 'On');

chdir('../');
nervinc("_navigations");
nervinc("_icons");
nervinc("_translation");
chdir('admin');

$icons = new icons(true);
$translations = new translation($userID);


echo "<style>
		*{
			box-sizing: border-box;
		}
		input{
			width: 100%;
			height: 15px;
		}
		input[type='checkbox']{
			width: 20px;
		}
		input[type='text'], select{
			height: 30px;
			line-height: 30px;
			padding-left: 10px;
			padding-right: 10px;
		}
		input[type='submit']{
			margin-top: 10px;
			height: 30px;
			line-height: 30px;
			padding-left: 10px;
			font-weight: bold;
			margin-bottom: 10px;
		}
	  </style>
	  
	  ";

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

if(isset($_GET['action'])){
	$action = $_GET['action'];
	if($action == "save"){
		$controlClass = new controls();
		$controls = $_POST['controls'];
		$cpy = $controls;
		
		$errors = array();
		foreach($cpy as $index => $controls){
			foreach($controls as $name => $content){				
				$evalValue = $controlClass->evalForm($index, $name, $content);
				if(!$controlClass->updateControl($index, $name, $evalValue)){
					$errors[] = "$index, $name, $evalValue </br>";
				}
			}
		}
		if(count($errors)>0){
			foreach($errors as $error){
				echo $error;
			}
		}else{
			echo '<script>window.location.href = "admincenter.php?site=nervsettings";</script>';
		}
	}else if($action == "delete"){
		if(isset($_GET['pname'])){
			$plugin_name = $_GET['pname'];
			$controlClass = new controls();
			$controlClass->deletePlugin($plugin_name);
			echo '<script>window.location.href = "admincenter.php?site=nervsettings";</script>';
		}
	}
}

echo'<h1>&curren; Nerv Einstellungen</h1>';

echo "<form action='admincenter.php?site=nervsettings&action=save' method='POST'>";
$controls = new controls($translations);

$allPlugins = $controls->getAllPlugins();
foreach($allPlugins as $plugin){
	$currPlugin		 = $plugin['plugin'];
	$currPluginCount = $plugin['childs'];
	$url 			 = "admincenter.php?site=nervsettings&action=delete&pname=$currPlugin";
	$back_url 		 = "admincenter.php?site=nervsettings";
	echo "<h1><i class='fa fa-wrench'></i> ".$currPlugin." (".$currPluginCount." properties) <span style='float: right'> <a href='$url'><button type='button' style='color: white;background-color: transparent; border: 0px;cursor: pointer;'><i class='fa fa-trash'></i></button></a> &nbsp;&nbsp;</span></h1><br>";
	
	echo'<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD" style="margin-bottom: 20px;">
			<tr>
			  <td width="20%" class="title" align="right"><b>Property</b></td>
			  <td width="40%" class="title" algin="left"><b>Value</b></td>
			  <td width="40%" class="title" algin="left"><b>Description</b></td>
			</tr>
			';
	
	
	$childs = $controls->getAllControlsOfPlugin($currPlugin);
	//echo "##".$_SESSION['childCtn']."##</br>";
	$i=1;
	foreach($childs as $control){
		$i++;
		$i = $i%2;
		$ctrHtml = $controls->generateControlHTML($control['id'], "controls[$currPlugin][".$control['name']."]", $control['content'], $translations);
		
		echo "<tr>
				<td class='td".($i+1)."' align='right'><b>".$translations->get($control['name'])."</b></td>
				<td class='td".($i+1)."' align='left'>$ctrHtml</td>
				<td class='td".($i+1)."' align='left'>".$translations->get($control['description'])."</td>
			  </tr>";
		
	}
	
	echo "</table>";
}
echo "<input type='submit' name='submit' value='".$translations->get("save")."' />";
echo "</form>";
?>  