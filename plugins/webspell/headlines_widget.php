<?php
	$webspell_class = new plugins("webspell");
	$widget = false;
	
	
	if(isset($rubricID) and $rubricID) $only = "AND rubric='".$rubricID."'";
	else $only='';

	$widget = $webspell_class->view("headlines_widget", "header", array(), true);
	
	$maxheadlines = 5;
	
	$ergebnis=isafe_query("SELECT * FROM ".PREFIX."news WHERE published='1' ".$only." AND intern<=".isclanmember($userID)." ORDER BY date DESC LIMIT 0,".$maxheadlines);
	if(mysqli_num_rows($ergebnis)){
		$n=1;
		while($ds=mysqli_fetch_array($ergebnis)) {
			$date=date("d.m.Y", $ds['date']);
			$time=date("H:i", $ds['date']);
			$news_id=$ds['newsID'];
			
			$message_array = array();
			$query=isafe_query("SELECT n.*, c.short AS `countryCode`, c.country FROM ".PREFIX."news_contents n LEFT JOIN ".PREFIX."countries c ON c.short = n.language WHERE n.newsID='".$ds['newsID']."'");
			while($qs = mysqli_fetch_array($query)) {
				$message_array[] = array('lang' => $qs['language'], 'headline' => $qs['headline'], 'message' => $qs['content'], 'country'=> $qs['country'], 'countryShort' => $qs['countryCode']);
			}
			$showlang = select_language($message_array);
		  
			$languages='';
			$i=0;
			foreach($message_array as $val) {
				if($showlang!=$i)	$languages.='<span style="padding-left:2px"><a href="index.php?site=news_comments&amp;newsID='.$ds['newsID'].'&amp;lang='.$val['lang'].'"><img src="images/flags/'.$val['countryShort'].'.gif" width="18" height="12" border="0" alt="'.$val['country'].'" /></a></span>';
				$i++;
			}
		  
			$lang=$message_array[$showlang]['lang'];
		
			$headlines=$message_array[$showlang]['headline'];
		
			if(mb_strlen($headlines)>$maxheadlinechars) {
				$headlines=mb_substr($headlines, 0, $maxheadlinechars);
				$headlines.='...';
			}
		
			$headlines=clearfromtags($headlines);
		
			$variables = array(
				"headline_date"		  	  => $date,
				"headline_time"			  => $time,
				"headline_languages"	  => $languages,
				"headline_link" 		  => "index.php?site=news_comments&newsID=".$ds['newsID']."",
				"headline_title"		  => $headlines
			);	
			$widget .= $webspell_class->view("headlines_widget", "content", $variables, true);
					
			$n++;
		}
		unset($rubricID);
	}
	
	$widget .= $webspell_class->view("headlines_widget", "footer", array(), true);
?>