<?php
include_once("../_magi_class.php");

	class icons extends magi_class{

		function icons($isadmin=false){
			if($isadmin){
				echo "<link rel='stylesheet' href='../css/font-awesome.min.css' />";
			}else{
				echo "<link rel='stylesheet' href='css/font-awesome.min.css' />";
			}
		}
		
		function showAll(){
			$select_query = "SELECT id, description, icon_class, createdate
							 FROM ".PREFIX."font_icons WHERE enable LIKE '1'";
			$result = $this->safe_query($select_query);
			if(mysqli_num_rows($result)>0){
				$icons = array();
				while ($row = mysqli_fetch_array($result))  {
					$id 			= $row['id'];
					$description 	= $row['description'];
					$icon_class 	= $row['icon_class'];
					$createdate 	= $row['createdate'];
					$icons[]		= array(
											'id' => $id,
											'description' => $description,
											'icon_class' => $icon_class,
											'createdate' => $createdate
											);
				}
				return $icons;
			}
			return false;
		}
		
		function showAdminIcons(){
			$icons = $this->showAll();
			$num_icons = count($icons);
			
			echo'</br><h1>&curren; Verwendbare Icons</h1>';
			echo'</br>
			<table width="100%" border="0" cellspacing="1" cellpadding="3" bgcolor="#FFF" style="border: 4px solid #DDDDDD">
			<tr>
			  <td width="8%" class="title"><b>ID</b></td>
			  <td width="20%" class="title"><b>ICONCLASS</b></td>
			   <td width="20%" class="title"><b>CHEATSHEET</b></td>
			  <td width="20%" class="title" align="left"><b>ICON</b></td>
			</tr>';
			
			foreach($icons as $icon){
				echo "
					<tr>
					<td>".$icon['id']."</td>
					<td>".$icon['icon_class']."</td>
					<td>".htmlspecialchars($icon['description'])."</td>
					<td align='left'><i class='".$icon['icon_class']."'></i></td>
				  </tr>
				";
			}
			
			echo '</table>';
			
		}
		
	}
		
		
?>