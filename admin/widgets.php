<?php
	chdir("../");
	nervinc("_plugins");
	nervinc("_icons");

	if(!isanyadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);
	$_language->read_module('widgets');

	$icon_class	= new icons(true);
	$plugin_class = new plugins();
	
	$plugin_class->view_template("admin_widgets", "header");

	if(isset($_GET['action'])){
		$action = $_GET['action'];
		if($action == "manage" && isset($_POST['position'])){
			$position = $_POST['position'];
			if(isset($_POST['delete'])){
				if($plugin_class->deletePosition($position)){
					$title = "Geschafft!";
					$content = "Die Widget Position wurde erfolgreich gelöscht.";
					$url = "admincenter.php?site=widgets";
					$time = "2500";
					$plugin_class->redirect_with_message($title, $content, $url, $time, "", "success");
				}else{
					$title = "Fehler!";
					$content = "Die Position konnte nicht gelöscht werden.";
					$url = "admincenter.php?site=widgets";
					$time = "2500";
					$plugin_class->redirect_with_message($title, $content, $url, $time, "", "danger");
				}
			}else if(isset($_POST['add'])){
				if(isset($_POST['save'])){
					$widget_file = $_POST['selected_widget'];
					$position = $_POST['position'];
					$sort = $_POST['sort'];
					if($plugin_class->insertWidgetToPosition($position, $widget_file, $sort)){
						$title = "Geschafft!";
						$content = "Das Widget wurde erfolgreich hinzugefügt.";
						$url = "admincenter.php?site=widgets";
						$time = "2500";
						$plugin_class->redirect_with_message($title, $content, $url, $time, "", "success");
					}else{
						$title = "Fehler!";
						$content = "Das Widget konnte nicht hinzugefügt werden.";
						$url = "admincenter.php?site=widgets";
						$time = "2500";
						$plugin_class->redirect_with_message($title, $content, $url, $time, "", "danger");
					}
				}else{
					////////////////
					$all_plugins = $plugin_class->getPlugins();
					$select_options = "";
					if(count($all_plugins)>0){
						$select_options = "<select class='form-control' name='selected_widget'>";
						foreach($all_plugins as $plugin){
							$select_options .= "<optgroup label='".$plugin['plugin']['info']['folder']."'>";
								$widgets = $plugin['plugin']['widgets'];
								foreach($widgets as $widget){
									$select_options .= "<option value='$widget'>$widget</option>";
								}
							$select_options .= "</optgroup>";
						}
						$select_options .= "</select>";
					}
					//////////////////
					$sort = "<select class='form-control' name='sort'>";
						for($i = $plugin_class->countAllWidgetsOfPosition($position)+1; $i > 0; $i--){
							$sort .= "<option valuue='$i'>$i</option>";
						}
					$sort .= "</select>";
					
					$variables = array(
						"position" 	      => $position,
						"sort"	   		  => $sort,
						"avaible_widgets" => $select_options
					);
					$plugin_class->view_template("admin_widgets", "new_widget_form", $variables);
				}
			}
		}else if($action=="managemulti"){
			if(isset($_POST['delete_row'])){
				$id = $_POST['delete_row'];
				$plugin_class->deleteWidgetByID($id);
				$plugin_class->redirect("admincenter.php?site=widgets");
			}else if(isset($_POST['sorting'])){
				$sorts = $_POST['sort'];
				foreach($sorts as $id=>$sort){
					$plugin_class->sortwidget($id, $sort);
				}
				$plugin_class->redirect("admincenter.php?site=widgets");
			}
		}
	}else{
		$allPositions = $plugin_class->getAllWidgetsPositions(); 
		if(count($allPositions)>0){
			foreach($allPositions as $position){
				$header_variables = array(
					"position" 	  => $position['position'],
					"count"    	  => $plugin_class->countAllWidgetsOfPosition($position['position']),
					"description" => $position['description'],
					"createdate"  => $position['create_date']
				);
				$plugin_class->view_template("admin_widgets", "position_header", $header_variables);
				
				$widget_templates = "";
				$allWidgetsOfCurrPosition = $plugin_class->getAllWidgetsOfPosition($position['position']);
				$ctn_all_widgets_of_curr_position = count($allWidgetsOfCurrPosition);
				$widget_templates .= $plugin_class->view_template("admin_widgets", "widget_tr_header", array(), true);
				foreach($allWidgetsOfCurrPosition as $widget){
					$sort_number = $widget['sort'];
					$id = $widget['id'];
					$sort = "<select class='form-control col-sm-6' name='sort[$id]'>";
						for($i = 0; $i <= $ctn_all_widgets_of_curr_position; $i++){
							$selected = "";
							if($i==$sort_number){
								$selected = "selected";
							}
							$sort .= "<option value='$i' $selected>$i</option>";
						}
					$sort .= "</select>";
					
					$widget_variables = array(
						"plugin_folder" => $widget['plugin_folder'],
						"widget_file"	=> $widget['widget_file'],
						"createdate" 	=> $widget['create_date'],
						"id"			=> $id,
						"sort"			=> $sort
					);
					$widget_templates .= $plugin_class->view_template("admin_widgets", "widget_tr", $widget_variables, true);
				}
				$widget_templates .= $plugin_class->view_template("admin_widgets", "widget_tr_footer", array(), true);
				$content_variables = array(
					"widget_templates" => $widget_templates
				);
				$plugin_class->view_template("admin_widgets", "position_content", $content_variables);
				$plugin_class->view_template("admin_widgets", "position_footer");
			}
		}else{
			echo $_language->module['no_positions'];
		}
	}
?>