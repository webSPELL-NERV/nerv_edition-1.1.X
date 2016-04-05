<?php
	$cookie_law_class = new plugins("cookie_law");
	if(!isset($_COOKIE['eucookie']))
	{ 
		echo $cookie_law_class->getTemplate("cookie", "header");
		echo $cookie_law_class->getTemplate("cookie", "content");
		echo $cookie_law_class->getTemplate("cookie", "footer");
	}
?>