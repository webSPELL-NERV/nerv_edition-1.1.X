<?php
/*
##########################################################################
#                                                                        #
#           Version 4       /                        /   /               #
#          -----------__---/__---__------__----__---/---/-               #
#           | /| /  /___) /   ) (_ `   /   ) /___) /   /                 #
#          _|/_|/__(___ _(___/_(__)___/___/_(___ _/___/___               #
#                       Free Content / Management System                 #
#                                   /                                    #
#                                                                        #
#                                                                        #
#   Copyright 2005-2011 by webspell.org                                  #
#                                                                        #
#   visit webSPELL.org, webspell.info to get webSPELL for free           #
#   - Script runs under the GNU GENERAL PUBLIC LICENSE                   #
#   - It's NOT allowed to remove this copyright-tag                      #
#   -- http://www.fsf.org/licensing/licenses/gpl.html                    #
#                                                                        #
#   Code based on WebSPELL Clanpackage (Michael Gruber - webspell.at),   #
#   Far Development by Development Team - webspell.org                   #
#                                                                        #
#   visit webspell.org                                                   #
#                                                                        #
##########################################################################
*/

$_language->read_module('squads');

eval ("\$title_squads = \"".gettemplate("title_squads")."\";");
echo $title_squads;
if(isset($_GET['action'])) $action = $_GET['action'];
else $action = "";
if($action=="show") {
	if($_GET['squadID']) {
		$getsquad = 'WHERE squadID="'.(int)$_GET['squadID'].'"';
	}
	else $getsquad = '';

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."squads ".$getsquad." ORDER BY sort");
	while($ds=mysql_fetch_array($ergebnis)) {

		$anzmembers=mysql_num_rows(safe_query("SELECT sqmID FROM ".PREFIX."squads_members WHERE squadID='".$ds['squadID']."'"));
		if($anzmembers == 1) $anzmembers = $anzmembers.' '.$_language->module['member'];
		else $anzmembers = $anzmembers.' '.$_language->module['members'];
		$name='&not; <b>'.$ds['name'].'</b>';
		$squadID=$ds['squadID'];
		$backlink='&raquo; <a href="'.prepareUrl('index.php?site=squads').'"><b>'.$_language->module['back_squad_overview'].'</b></a>';
		$results='';
		$awards='';
		$challenge='';
		$games='';
		
		$border=BORDER;
	
		if($ds['gamesquad']) {
			$results='[ <a href="'.prepareUrl('index.php?site=clanwars&action=showonly&id='.$squadID.'&sort=date&only=squad').'">'.$_language->module['results'].'</a> | ';
			$awards='<a href="'.prepareUrl('index.php?site=awards&action=showsquad&squadID='.$squadID.'&page=1').'">'.$_language->module['awards'].'</a> | ';
			$challenge='<a href="'.prepareUrl('index.php?site=challenge').'">'.$_language->module['challenge'].'</a> ]';
			$games = $ds['games'];
			if($games) {
				$games = str_replace(";", ", ", $games);
				$games = $_language->module['squad_plays'].": ".$games;
			}
		}

		
		$info=htmloutput($ds['info']);
		
		if($games==""){
			$games = $info;
		}
		
		$member=safe_query("SELECT * FROM ".PREFIX."squads_members s, ".PREFIX."user u WHERE s.squadID='".$ds['squadID']."' AND s.userID = u.userID ORDER BY sort");
		eval("\$squads_head = \"".gettemplate("squads_head")."\";");
		echo $squads_head;

		$i=1;
		while($dm=mysql_fetch_array($member)) {

			if($i%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}

			$country = '[flag]'.$dm['country'].'[/flag]';
			$country = flags($country);
			$nickname = '<a href="'.prepareUrl('index.php?site=profile&id='.$dm['userID'].'').'"><b>'.strip_tags(stripslashes($dm['nickname'])).'</b></a>';
			$nicknamee = strip_tags(stripslashes($dm['nickname']));
			$profilid = $dm['userID'];

			$ergebnis = safe_query("SELECT * FROM ".PREFIX."user WHERE userID='".$profilid."'");
			$ds = mysql_fetch_array($ergebnis);
			
			if($ds['about']) $about = cleartext($ds['about']);
			else $about = $_language->module['n_a'];

			
			
			if($dm['userdescription']) $userdescription=htmloutput($dm['userdescription']);
			else $userdescription=$_language->module['no_description'];

			if ($dm['userpic']!="" and file_exists("images/userpics/".$dm['userpic'])) {
				$userpic = $dm['userpic'];
				$pic_info = $dm['nickname'].' '.$_language->module['userpicture'];
			}
			else {
				$userpic = "nouserpic.gif";
				$pic_info = $_language->module['no_userpic'];
			}

			$icq = $dm['icq'];
			if(getemailhide($dm['userID'])) $email = '';
			else $email = '<a href="mailto:'.mail_protect($dm['email']).'"><i class="fa fa-envelope-o"></i></a>';

			$pm = '';
			$buddy = '';
			if ($loggedin && $dm['userID'] != $userID) {
				$pm='<a href="index.php?site=messenger&action=touser&touser='.$dm['userID'].'"><img src="images/icons/pm.gif" border="0" alt="'.$_language->module['messenger'].'" /></a>';

				if (isignored($userID, $dm['userID'])) $buddy='<a href="buddys.php?action=readd&id='.$dm['userID'].'&userID='.$userID.'"><img src="images/icons/buddy_readd.gif" border="0" alt="'.$_language->module['back_buddy'].'" /></a>';
				elseif(isbuddy($userID, $dm['userID'])) $buddy='<a href="buddys.php?action=ignore&id='.$dm['userID'].'&userID='.$userID.'"><img src="images/icons/buddy_ignore.gif" border="0" alt="'.$_language->module['ignore'].'" /></a>';
				else $buddy='<a href="buddys.php?action=add&id='.$dm['userID'].'&userID='.$userID.'"><img src="images/icons/buddy_add.gif" border="0" alt="'.$_language->module['add_buddy'].'" /></a>';
			}

			if(isonline($dm['userID'])=="offline") $statuspic='<span class="label label-danger">offline</span>';
			else $statuspic='<span class="label label-success">online</span>';

			$position = $dm['position'];
			$firstname = strip_tags($dm['firstname']);
			$lastname = strip_tags($dm['lastname']);
			$town = strip_tags($dm['town']);
			if($dm['activity']) $activity='<font color="'.$wincolor.'">'.$_language->module['active'].'</font>';
			else $activity='<font color="'.$loosecolor.'">'.$_language->module['inactive'].'</font>';

			eval ("\$squads_content = \"".gettemplate("squads_content")."\";");
			echo $squads_content;
			$i++;
		}
		eval ("\$squads_foot = \"".gettemplate("squads_foot")."\";");
		echo $squads_foot;
	}
}

else {
 	$getsquad = "";
	if(isset($_GET['squadID'])) {
		$getsquad = 'WHERE squadID="'.$_GET['squadID'].'"';
	}

	$ergebnis=safe_query("SELECT * FROM ".PREFIX."squads ".$getsquad." ORDER BY sort");
	
  $i=1;
  while($ds=mysql_fetch_array($ergebnis)) {
  
    if($i%2) {
      $bg1=BG_1;
      $bg2=BG_2;
    }
    else {
      $bg1=BG_3;
      $bg2=BG_4;
    }
    
    $anzmembers=mysql_num_rows(safe_query("SELECT sqmID FROM ".PREFIX."squads_members WHERE squadID='".$ds['squadID']."'"));
		if($anzmembers == 1) $anzmembers = $anzmembers.' '.$_language->module['member'];
		else $anzmembers = $anzmembers.' '.$_language->module['members'];
		$name='&not; <a href="'.prepareUrl('index.php?site=squads&action=show&squadID='.$ds['squadID'].'').'"><b>'.$ds['name'].'</b></a>';
		if($ds['icon']) $icon='<a href="'.prepareUrl('index.php?site=squads&action=show&squadID='.$ds['squadID'].'').'"><img src="images/squadicons/'.$ds['icon'].'" border="0" alt="'.htmlspecialchars($ds['name']).'" /></a>';
		else $icon='';
		$info=htmloutput($ds['info']);
		$details='&raquo; <a href="'.prepareUrl('index.php?site=squads&action=show&squadID='.$ds['squadID'].'').'"><b>'.$_language->module['show_details'].'</b></a>';
		$squadID=$ds['squadID'];
		$results='';
		$awards='';
		$challenge='';

		if($ds['gamesquad']) {
			$results='[ <a href="'.prepareUrl('index.php?site=clanwars&action=showonly&id='.$squadID.'&sort=date&only=squad').'">'.$_language->module['results'].'</a> | ';
			$awards='<a href="'.prepareUrl('index.php?site=awards&action=showsquad&squadID='.$squadID.'&page=1').'">'.$_language->module['awards'].'</a> | ';
			$challenge='<a href="'.prepareUrl('index.php?site=challenge').'">'.$_language->module['challenge'].'</a> ]';
		}

		$bgcat=BGCAT;
		eval ("\$squads = \"".gettemplate("squads")."\";");
		echo $squads;
    
    $i++;
	}
}
?>