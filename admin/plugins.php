<?php
	chdir("../");
	nervinc("_plugins");
	nervinc("_icons");
	
	if(!isanyadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);
	$_language->read_module('plugins');
	
	$icon_class	= new icons(true);
	$plugin_class = new plugins();
	
	if(isset($_GET['action'])){
		$action = $_GET['action'];
		if($action == "manage" && isset($_POST['plugin_folder'])){
			$plugin_folder = $_POST['plugin_folder'];
			// INSTALLIEREN
			if(isset($_POST['install'])){
				if($plugin_class->install($plugin_folder)){
					$title = "Erfolgreich!";
					$content = "Das Plugin wurde erfolgreich installiert!";
					$url = "admincenter.php?site=plugins";
					$time = "1500";
					$plugin_class->redirect_with_message($title, $content, $url, $time);
				}else{
					$title = "Fehler!";
					$content = "Das Plugin konnte nicht installiert werden!";
					$url = "admincenter.php?site=plugins";
					$time = "2500";
					$plugin_class->redirect_with_message($title, $content, $url, $time, "", "danger");
				}
			// LÖSCHEN
			}else if(isset($_POST['uninstall'])){
				if($plugin_class->uninstall($plugin_folder)){
					$title = "Erfolgreich!";
					$content = "Das Plugin wurde erfolgreich gelöscht!";
					$subline = "Es wurde <b>komplett gelöscht</b>, alle Dateien und Tabellen.";
					$url = "admincenter.php?site=plugins";
					$time = "2000";
					$plugin_class->redirect_with_message($title, $content, $url, $time, $subline);
				}else{
					$title = "Fehler!";
					$content = "Das Plugin konnte nicht deinstalliert werden! <br> Bitte wiederholen Sie den Vorgang, sollte sich das wiederholen löschen Sie es manuell.";
					$url = "admincenter.php?site=plugins";
					$time = "2500";
					$plugin_class->redirect_with_message($title, $content, $url, $time, "", "danger");
				}
			}
		} 
	}else{
		// SHOWS PLUGIN LIST
		echo $plugin_class->view_template("admin_plugins", "list_header");
		$all_plugins = $plugin_class->getPlugins();
		if(count($all_plugins)>0){
			foreach($all_plugins as $plugin){
				$install_button = "";
				$is_installable = "";
				$plugin_folder = $plugin['plugin']['info']['folder'];

				if($plugin['plugin']['installed']){
					$install_button = "hidden";
				}
				
				$name = $plugin['plugin']['info']['name'];
				$version = $plugin['plugin']['info']['version'];
				$pl_nerv_version = $plugin['plugin']['info']['nerv_version'];
				$description = $plugin['plugin']['info']['description'];
				
				if($pl_nerv_version < $nerv_version){
					$is_installable = "bg-danger";
					$install_button = "hidden";
				}
				
				$variables = array(
					"install_button" => $install_button,
					"name" => $name,
					"version" => $version,
					"nerv_version" => $pl_nerv_version,
					"plugin_folder" => $plugin_folder,
					"description" => $description,
					"is_installable" => $is_installable
				);
				echo $plugin_class->view_template("admin_plugins", "list_content", $variables);
			}
		}else{
			echo $plugin_class->view_template("admin_plugins", "no_list_content");
		}
		echo $plugin_class->view_template("admin_plugins", "list_footer");
	}
?>