<?php
/* dynamic navigations by kevinwiederstein */
include_once("../_magi_class.php");

	class navigations extends magi_class{
		var $nav_direction = ""; // or 'horizontal' or 'vertical'
		
		function navigations($direction="horizontal"){
			$this->nav_direction = $direction;
		}
		
		function register($navigation_name, $navigation_description = "", $user_id = 1){
			$nav_id = $this->getIDOfNavigation($navigation_name);
			if(!$nav_id){
				$this->insertNewNavigation($navigation_name, $navigation_description, $user_id);
				$this->show_error("'$navigation_name' wurde angelegt.");
			}else{
				if($this->isEnabled($nav_id)){
					$childs = $this->getChildsOfNavigation($nav_id);
					if($childs && count($childs)>0){
						$navigationType = $this->nav_direction;
						eval ("\$navigations_header = \"".$this->gettemplate("navigations_header")."\";");
						echo $navigations_header;

						foreach($childs as $child){
							$is_selected = ""; // selected
							$icon_class 	= $child['icon_class'];
							$href 			= $child['href'];
							if($this->childIsCurrentSite($href)){
								$is_selected = "selected";
							}
							$href_url 		= $this->prepareUrl($href);
							$content 		= $child['content'];
							$id 			= $child['id'];
							eval ("\$navigations_content = \"".$this->gettemplate("navigations_content")."\";");
							echo $navigations_content;
						}
						
						eval ("\$navigations_footer = \"".$this->gettemplate("navigations_footer")."\";");
						echo $navigations_footer;
					}else{
						return false;
					}
				}
			}
		}
		
		function childIsCurrentSite($href){
			if((strpos($a, ':') == false) ){
				$href = (($this->getHpUrl()).$href);
			}
			preg_match_all("~[\?&]([^&]+)=([^&]+)~", $href, $urlParts);
			$currentSite = $_GET['site'];
			if($currentSite == $urlParts[2][0]){
				return true;
			}
			return false;
		}
		
		function isEnabled($nav_id){
			$nav = $this->getNav($nav_id);
			return $nav['enabled'];
		}
		
		function getNav($id){
			$select_query = "SELECT id,name,content,href,icon_id,createdby,sort,enabled
			FROM ".PREFIX."navigation
			WHERE id LIKE '$id' LIMIT 1";
			$result = $this->safe_query($select_query);
			if(mysqli_num_rows($result)>0){
				while ($row = mysqli_fetch_array($result))  
				{
					$curr_id 			= $row['id'];
					$curr_name 			= $row['name'];
					$curr_content		= $row['content'];
					$curr_href 			= $row['href'];
					$curr_icon 			= $row['icon_id'];
					$curr_usrid 		= $row['createdby'];
					$curr_sort			= $row['sort'];
					$curr_enabled		= $row['enabled'];
					$main_navs 		= array(
												'id'			=>	$curr_id, 
												'name'			=>	$curr_name,
												'description'	=>	$curr_content,
												'href'			=>	$curr_href,
												'icon_id'		=>	$curr_icon,
												'createdby'		=>	$curr_usrid,
												'sort'			=>  $curr_sort,
												'enabled'		=>  $curr_enabled
												);
				}
				return $main_navs;
			}
			return false;
		}
		
		function getMainNavs(){
			$select_query = "SELECT id,name,content,enabled
			FROM ".PREFIX."navigation
			WHERE parent LIKE '0'";
			$result = $this->safe_query($select_query);
			if(mysqli_num_rows($result)>0){
				$main_navs = array();
				while ($row = mysqli_fetch_array($result))  
				{
					$curr_id 			= $row['id'];
					$curr_name 			= $row['name'];
					$curr_content		= $row['content'];
					$curr_enabled		= $row['enabled'];
					$main_navs[] 		= array(
												'id'			=>	$curr_id, 
												'name'			=>	$curr_name,
												'description'	=>	$curr_content,
												'enabled'		=>	$curr_enabled
												);
				}
				return $main_navs;
			}
			return false;
		}
		
		function insertNewChild($parent_id, $icon_id, $navigation_content, $href, $user_id){
			$insert_query = "INSERT INTO ".PREFIX."navigation (parent,icon_id,name,content,href,createdby) VALUES ($parent_id,$icon_id,'NULL','$navigation_content','$href','$user_id')";
			$result = $this->safe_query($insert_query);
			return $result;
		}
		
		function updateChild($nav_id, $icon_id, $navigation_content, $href, $user_id){
			$update_query = "UPDATE ".PREFIX."navigation SET
				icon_id='$icon_id',
				content='$navigation_content',
				href='$href'
				WHERE id LIKE '$nav_id'";
			$result = $this->safe_query($update_query);
			return $result;
		}
		
		function insertNewNavigation($navigation_name, $navigation_description = "", $user_id = 1){
			$insert_query = "INSERT INTO ".PREFIX."navigation (parent,icon_id,name,content,createdby) VALUES ('0',NULL,'$navigation_name','$navigation_description','$user_id')";
			$result = $this->safe_query($insert_query);
			return $result;
		}
		
		function deleteNavigation($nav_id){
			$delete_query = "DELETE FROM ".PREFIX."navigation WHERE id LIKE '$nav_id'";
			$result = $this->safe_query($delete_query);
			return $result;
		}
		
		function updateSort($ele_id, $sort){
			$update_query = "UPDATE ".PREFIX."navigation SET sort = '$sort' WHERE id LIKE '$ele_id'";
			$result = $this->safe_query($update_query);
			return $result;
		}
		
		function disable($nav_id){
			$update_query = "UPDATE ".PREFIX."navigation SET enabled = 0 WHERE id LIKE '$nav_id'";
			$result = $this->safe_query($update_query);
			return $result;
		}
		
		function enable($nav_id){
			$update_query = "UPDATE ".PREFIX."navigation SET enabled = 1 WHERE id LIKE '$nav_id'";
			$result = $this->safe_query($update_query);
			return $result;
		}
		
		
		function countChilds($parent_id){
			$select_query = "SELECT id 
			FROM ".PREFIX."navigation 			
			WHERE parent LIKE '$parent_id'";
			$result = $this->safe_query($select_query);
			return mysqli_num_rows($result);
		}
		
		function getChildsOfNavigation($id){
			$select_query = "SELECT N.id AS N_id,N.content AS N_content,N.href AS N_href, FI.icon_class AS FI_icon_class , N.sort AS N_sort
			FROM ".PREFIX."navigation AS N left join ".PREFIX."font_icons AS FI ON N.icon_id = FI.id			
			WHERE N.parent LIKE '$id' ORDER BY N.sort ASC";
			$result = $this->safe_query($select_query);
			if(mysqli_num_rows($result)>0){
				$childs = array();
				while ($row = mysqli_fetch_array($result))  
				{
					$curr_id 			= $row['N_id'];
					$curr_content 		= $row['N_content'];
					$curr_icon_class 	= $row['FI_icon_class'];
					$curr_href 			= $row['N_href'];
					$curr_sort			= $row['N_sort'];
					$childs[] 			= array(
												'id'=>$curr_id, 
												'content'=>$curr_content,
												'icon_class'=>$curr_icon_class, 
												'href'=>$curr_href,
												'sort'=>$curr_sort
												);
				}
				return $childs;
			}
			//$this->show_error("No childs.");
			return false;
		}
		
		function getIDOfNavigation($navigation_name){
			$select_query = "SELECT id FROM ".PREFIX."navigation WHERE name LIKE '$navigation_name'";
			$result = $this->safe_query($select_query);
			if(mysqli_num_rows($result)>0){
				$res2 = mysqli_fetch_array($result);
				return $res2['id'];
			}
			return false;
		}
		
		
	}

?>