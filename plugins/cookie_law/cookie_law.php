<?php
	$cookie_law_class = new plugins("cookie_law");
	$widget = false;
	if(!isset($_COOKIE['eucookie']))
	{ 
		$widget = "";
		$select_query = "SELECT id,text, days FROM ".PREFIX."cookie_law LIMIT 1";
		$infos = mysqli_fetch_array(isafe_query($select_query));
		
		$variables = array(
			"description" => $infos['text'],
			"days"		  => $infos['days']
		);
			
		$widget .= "
			<script>
				$(document).ready(function(){
					$('.removecookie').click(function () {
						createCookie('eucookie','eucookie',".$infos['days'].");
						$('.cookielaw').remove();
					});
				});
			</script>
		";
		$widget .= $cookie_law_class->view("cookie", "header");
		$widget .= $cookie_law_class->view("cookie", "content", $variables);
		$widget .= $cookie_law_class->view("cookie", "footer");
	}
?>