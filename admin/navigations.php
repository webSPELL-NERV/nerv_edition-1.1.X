<?php

define('DEBUG', "ON");
error_reporting(E_ALL); // 0 = public mode, E_ALL = development-mode
ini_set('display_errors', 'On');

echo "
	<style>
		form{display: inline;}
		select{display: inline;}
		input{display: inline;}
	</style>
";

chdir('../');
nervinc("_navigations");
nervinc("_icons");
chdir('admin');

if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);

echo'<h1>&curren; Navigations Einstellungen</h1>';

$icons = new icons(true);
$nav = new navigations();


$eleid = 0;
$navid = 0;

if(isset($_GET['eleid']) && $_GET['eleid'] != "" && isset($_GET['action'])){
	$action = $_GET['action'];
	$eleid = $_GET['eleid'];
	if($action=="edit"){
		if(isset($_POST['ele_id']) && isset($_POST['icon_id'])
			&& isset($_POST['content']) && isset($_POST['href'])){
				$ele_id 	=  $_POST['ele_id'];
				$iconID 	=  $_POST['icon_id'];
				$content 	=  $_POST['content'];
				$href 		=  $_POST['href'];
				if($nav->updateChild($ele_id, $iconID, $content, $href, $userID)){
					$message = "Erfolgreich geupdated.";
				}else{
					$message = "Nicht geupdated.";
				}
				redirect("admincenter.php?site=navigations", $message);
		}else{
			$curr_nav = $nav->getNav($eleid);
			echo "
				<h4 style='display: inline;'>Editiere Menupunkt</h4></br>
				<form action='admincenter.php?site=navigations&eleid=$eleid&action=edit' method='post'>
					<input type='hidden' value='$eleid' name='ele_id' />
					<table width='60%' border='0' cellspacing='1' cellpadding='3'>
					<tr>
					  <td align='right'><b>IconID</b></td>
					  <td width='37%'>
						<select name='icon_id' style='font-family: FontAwesome, Arial'>
							";
							$all_icons = $icons->showAll();
							foreach($all_icons as $icon){
								$selected = "";
								if($icon['id'] == $curr_nav['icon_id']){$selected = "selected";}
								echo "<option value='".$icon['id']."' $selected>".$icon['description']." ".$icon['icon_class']."</option>";
							}
							
					echo "
						</select>
					  </td>
					</tr>
					<tr>
					  <td align='right'><b>Beschriftung</b></td>
					  <td width='37%'><input type='text' value='".$curr_nav['description']."' name='content' /></td>
					</tr>
					<tr>
					  <td align='right'><b>Hyperlink</b></td>
					  <td width='37%'><input type='text' value='".$curr_nav['href']."' name='href' /></td>
					</tr>
					<tr>
					  <td align='right'><b>Editieren</b></td>
					  <td width='37%'><input type='submit' name='submit' value='Senden' /></td>
					</tr>
				</table></br></br>
				<span style='font-weight: bold; color: #333;display: block; text-align: center;'>
				Interne verlinkungen wie '<span style='color: red'>index.php?site=news</span>' ohne http / https schreiben, </br>
				Externe damit. ('<span style='color: red'>http://google.de</span>')</span></br></br>
				</form>
			";
			$icons->showAdminIcons();
		}
	}else if($action=="delete"){
		if($nav->deleteNavigation($eleid)){
			$message = "Erfolgreich gelöscht.";
		}else{
			$message = "Nicht gelöscht.";
		}
		redirect("admincenter.php?site=navigations", $message);
	}else if($action=="sort" && isset($_POST['sort'])){
		$new_sort = $_POST['sort'];
		echo $new_sort;
		if($nav->updateSort($eleid, $new_sort)){
			echo '<meta http-equiv="refresh" content="0;url=admincenter.php?site=navigations">';
		}else{
			$message = "Nicht sortiert.";
			redirect("admincenter.php?site=navigations", $message);
		}
	}else{
		$nav->show_error("Keine Aktion gefunden.");
	}
}else if(isset($_GET['navid'])  && $_GET['navid'] != "" && isset($_GET['action'])) {
	$action = $_GET['action'];
	$navid = $_GET['navid'];
	if($action=="show"){
		if($nav->enable($navid)){
			$message = "Erfolgreich aktiviert.";
		}else{
			$message = "Nicht aktiviert.";
		}
		redirect("admincenter.php?site=navigations", $message);
	}else if($action=="delete"){
		if($nav->deleteNavigation($navid)){
			$message = "Erfolgreich gelöscht.";
		}else{
			$message = "Nicht gelöscht.";
		}
		redirect("admincenter.php?site=navigations", $message);
	}else if($action=="hide"){
		if($nav->disable($navid)){
			$message = "Erfolgreich versteckt.";
		}else{
			$message = "Nicht versteckt.";
		}
		redirect("admincenter.php?site=navigations", $message);
	} else if($action=="new"){
		if(isset($_POST['parent_id']) && isset($_POST['icon_id'])
			&& isset($_POST['content']) && isset($_POST['href'])){
				$parentID 	=  $_POST['parent_id'];
				$iconID 	=  $_POST['icon_id'];
				$content 	=  $_POST['content'];
				$href 		=  $_POST['href'];
				if($nav->insertNewChild($parentID, $iconID, $content, $href, $userID)){
					$message = "Erfolgreich erstellt.";
				}else{
					$message = "Nicht erstellt.";
				}
				redirect("admincenter.php?site=navigations", $message);
		}else{
			$curr_nav = $nav->getNav($navid);
			echo "
				<h4 style='display: inline;'>Neuer Menüpunkt für: <h3 style='display: inline;color: red'>'".$curr_nav['name']."'</h3></h4></br>
				<form action='admincenter.php?site=navigations&navid=$navid&action=new' method='post'>
					<input type='hidden' value='$navid' name='parent_id' />
					<table width='60%' border='0' cellspacing='1' cellpadding='3'>
					<tr>
					  <td align='right'><b>IconID</b></td>
					  <td width='37%'>
						<select name='icon_id' style='font-family: FontAwesome, Arial'>
							";
							$all_icons = $icons->showAll();
							foreach($all_icons as $icon){
								echo "<option value='".$icon['id']."'>".$icon['description']." ".$icon['icon_class']."</option>";
							}
							
					echo "
						</select>
					  </td>
					</tr>
					<tr>
					  <td align='right'><b>Beschriftung</b></td>
					  <td width='37%'><input type='text' value='' name='content' /></td>
					</tr>
					<tr>
					  <td align='right'><b>Hyperlink</b></td>
					  <td width='37%'><input type='text' value='' name='href' /></td>
					</tr>
					<tr>
					  <td align='right'><b>Hinzufügen</b></td>
					  <td width='37%'><input type='submit' name='submit' value='Senden' /></td>
					</tr>
				</table></br></br>
				<span style='font-weight: bold; color: #333;display: block; text-align: center;'>
				Interne verlinkungen wie '<span style='color: red'>index.php?site=news</span>' ohne http / https schreiben, </br>
				Externe damit. ('<span style='color: red'>http://google.de</span>')</span></br></br>
				</form>
			";
			$icons->showAdminIcons();
		}
	}
}else{

	$mainnavs = $nav->getMainNavs();
	echo'<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#DDDDDD">
			<tr>
			  <td width="2%" class="title"><b>#</b></td>
			  <td width="8%" class="title"><b>ID</b></td>
			  <td colspan="2" width="70%" class="title"><b>Navigation</b></td>
			  <td width="20%" class="title" align="right"><b>
				Actions
			  </b></td>
			</tr>
			<tr><td colspan="5" bgcolor="#ffe6e6" style="font-weight: bold; color: #333; line-height: 20px;padding: 10px;">
				Hier könnt Ihr eine neue Navigation sehen sobald das erste mal die Seite wo sie eingebunden ist aufgerufen wird, und somit hier registriert wird.
			</td></tr>
			';
	
	foreach($mainnavs as $mainNav){
		$nav_id = $mainNav['id'];
		$nav_name = $mainNav['name'];
		$nav_description = $mainNav['description'];
		$nav_enabled = $mainNav['enabled'];
		$status_img = "<img src='../images/icons/offline.gif' alt='Menu offline' />";
		if($nav_enabled){
			$status_img = "<img src='../images/icons/online.gif' alt='Menu online' />";
		}
		
		$action_form = "show";
		$value_form = "&#xf205;";
		if($nav_enabled){
			$action_form = "hide";
			$value_form = "&#xf204;";
		}
		
		echo "<tr>
				<td class='td_head' align='center'>$status_img</td>
				<td class='td_head'>$nav_id</td>
				<td class='td_head'><b>$nav_name</b></td>
				<td class='td_head'>$nav_description</td>
				<td class='td_head' align='right'><b>
					<form method='post' action='admincenter.php?site=navigations&navid=$nav_id&action=$action_form'>
						<input style='font-family: FontAwesome' type='submit' name='$action_form' value='$value_form'/>
					</form>
					<form method='post' action='admincenter.php?site=navigations&navid=$nav_id&action=new'>
						<input style='font-family: FontAwesome' type='submit' name='new' value='&#xf0fe;'/>
					</form>
					<form method='post' action='admincenter.php?site=navigations&navid=$nav_id&action=delete'>
						<input style='font-family: FontAwesome' type='submit' name='delete' value='&#xf1f8;'/>
					</form>
			  </b></td>
			  </tr>";
		
		echo'<tr><td colspan="5">
			<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#fff" style="border-top: 3px solid gray;border-bottom: 3px solid gray">
			<tr>
			  <td width="10%" class="title"><b>ID</b></td>
			  <td width="20%" class="title"><b>Icon</b></td>
			  <td width="30%" class="title"><b>Hyperlink</b></td>
			  <td width="20%" class="title"><b>Beschriftung</b></td>
			  <td width="20%" class="title"><b>Aktionen</b></td>
			</tr>';
		
			$childs = $nav->getChildsOfNavigation($nav_id);
			if($childs && count($childs)>0){
				foreach($childs as $child){
					$is_selected 	= ""; // selected
					$icon_class 	= $child['icon_class'];
					$href_url 		= $child['href'];
					$content 		= $child['content'];
					$id 			= $child['id'];
					$sort			= $child['sort'];
					echo "<tr>
							<td>$id</td>
							<td><i class='$icon_class'></i> <!--$icon_class--></td>
							<td><a href='$href_url' target='_blank'>$href_url</a></td>
							<td><b>$content</b></td>
							<td align='right'>
								<form method='post' action='admincenter.php?site=navigations&eleid=$id&action=delete'>
									<input  style='font-family: FontAwesome' type='submit' name='delete' value='&#xf1f8;'/>
								</form>
								<form method='post' action='admincenter.php?site=navigations&eleid=$id&action=edit'>
									<input  style='font-family: FontAwesome' type='submit' name='edit' value='&#xf044;'/>
								</form>
								<form method='post' action='admincenter.php?site=navigations&eleid=$id&action=sort'>
									<select name='sort'>";
									$max_childs = $nav->countChilds($nav_id);
									for($i = 0; $i < $max_childs; $i++){
										$selected = "";
										if($i == $sort){$selected = "selected";}
										echo "<option value='$i' $selected>$i</option>";

									}
									echo "
									</select>
									<input style='font-family: FontAwesome' type='submit' name='sortsubmit' value='&#xf0dc;'/>
								</form>
							</td>
						  </tr>";
				}
			}else{
				echo "<tr>
							<td colspan='5'>No Childs.</td>
					  </tr>";
			}
		
		echo '</table></td></tr>';
	}
	
	echo '</table>';
	
	// SHOW AVAIBLE ICONS
	//$icons->showAdminIcons();
}
?>