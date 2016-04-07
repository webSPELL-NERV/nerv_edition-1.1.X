<?php
	$webspell_class = new plugins("webspell");
	$widget = false;
	
	if(isset($rubricID) and $rubricID) $only = "AND rubric='".$rubricID."'";
	else $only='';

	$widget  = $webspell_class->view("forumtopics_widget", "header", array(), true);
	
	$maxlatesttopics = 5;
	
	$usergroups = array();
	if($loggedin){
		$usergroups[] = 'user';
		$get = isafe_query("SELECT * FROM ".PREFIX."user_forum_groups WHERE userID='".$userID."'");
		$data = mysqli_fetch_row($get);
		for($i=2; $i<count($data);$i++){
			if($data[$i] == 1){
				$info = mysqli_fetch_field($get,$i);
				$usergroups[] = $info->name;
			}
		}
	}
	$userallowedreadgrps = array();
	$userallowedreadgrps['boardIDs'] = array();
	$userallowedreadgrps['catIDs'] = array();
	$get = isafe_query("SELECT boardID FROM ".PREFIX."forum_boards WHERE readgrps = ''");
	while($ds = mysqli_fetch_assoc($get)){
		$userallowedreadgrps['boardIDs'][] = $ds['boardID'];
	}
	$get = isafe_query("SELECT catID FROM ".PREFIX."forum_categories WHERE readgrps = ''");
	while($ds = mysqli_fetch_assoc($get)){
		$userallowedreadgrps['catIDs'][] = $ds['catID'];
	}
	if($loggedin){
		$get = isafe_query("SELECT boardID, readgrps FROM ".PREFIX."forum_boards WHERE readgrps != ''");
		while($ds = mysqli_fetch_assoc($get)){
			$groups = explode(";",$ds['readgrps']);
			$allowed = array_intersect($groups,$usergroups);
			if(!count($allowed)) continue;
			$userallowedreadgrps['boardIDs'][] = $ds['boardID'];
		}
		$get = isafe_query("SELECT catID, readgrps FROM ".PREFIX."forum_categories WHERE readgrps != ''");
		while($ds = mysqli_fetch_assoc($get)){
			$groups = explode(";",$ds['readgrps']);
			$allowed = array_intersect($groups,$usergroups);
			if(!count($allowed)) continue;
			$userallowedreadgrps['catIDs'][] = $ds['catID'];
		}
	}
	if(empty($userallowedreadgrps['catIDs'])){
		$userallowedreadgrps['catIDs'][] = 0;
	}
	if(empty($userallowedreadgrps['boardIDs'])){
		$userallowedreadgrps['boardIDs'][] = 0;
	}
	$ergebnis=isafe_query("SELECT t.*, u.nickname, b.name
							FROM ".PREFIX."forum_topics t 
					   LEFT JOIN ".PREFIX."user u ON u.userID = t.lastposter
					   LEFT JOIN ".PREFIX."forum_boards b ON b.boardID = t.boardID
						   WHERE b.category IN (".implode(",",$userallowedreadgrps['catIDs']).") AND 
								 t.boardID IN (".implode(",",$userallowedreadgrps['boardIDs']).") AND 
								 t.moveID = '0'
						ORDER BY t.lastdate DESC 
						   LIMIT 0,".$maxlatesttopics);
	$anz=mysqli_num_rows($ergebnis);
	if($anz) {
		$n=1;
		while($ds=mysqli_fetch_array($ergebnis)) {
			if($ds['readgrps'] != "") {
				$usergrps = explode(";", $ds['readgrps']);
				$usergrp = 0;
				foreach($usergrps as $value) {
					if(isinusergrp($value, $userID)) {
						$usergrp = 1;
						break;
					}
				}
				if(!$usergrp and !ismoderator($userID, $ds['boardID'])) continue;
			}
			if($n%2) {
				$bg1=BG_1;
				$bg2=BG_2;
			}
			else {
				$bg1=BG_3;
				$bg2=BG_4;
			}
		
			$topictitle_full = clearfromtags($ds['topic']);
			$topictitle	= unhtmlspecialchars($topictitle_full);
			if(mb_strlen($topictitle)>$maxlatesttopicchars) {
				$topictitle=mb_substr($topictitle, 0, $maxlatesttopicchars);
				$topictitle.='...';
			}
			$topictitle = htmlspecialchars($topictitle);
		
			$last_poster = $ds['nickname'];
			$board = $ds['name'];
			$date = date('d.m.Y - H:i', $ds['lastdate']);
			$small_date	= date('d.m H:i', $ds['lastdate']);

			$latesticon	=	'<img src="images/icons/'.$ds['icon'].'" width="15" height="15" alt="" />';
			$boardlink	=	'<a href="'.prepareUrl('index.php?site=forum&board='.$ds['boardID'].'').'">'.$board.'</a>';
			$topiclink	=	'<a href="'.prepareUrl('index.php?site=forum_topic&topic='.$ds['topicID'].'&type=ASC&page='.ceil(($ds['replys']+1)/$maxposts).'').'" onmouseover="showWMTT(\'latesttopics_'.$n.'\')" onmouseout="hideWMTT()">'.$topictitle.'</a>';
			$replys			=	$ds['replys'];
			
			$replys_text = ($replys == 1) ? $_language->module['reply'] : $_language->module['replies'];
			
			$variables = array(
				"topiclink" 	  => $topiclink,
				"date"			  => $date,
				"last_poster" 	  => $last_poster,
				"board"		  	  => $board,
				"topictitle_full" => $topictitle_full
			);
			$widget .= $webspell_class->view("forumtopics_widget", "content", $variables , true);
			
			$n++;
		}
	}
	$widget .= $webspell_class->view("forumtopics_widget", "footer", array(), true);		
?>