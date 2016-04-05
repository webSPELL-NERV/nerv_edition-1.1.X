<?php
	error_reporting(E_ALL);
	include("_mysql.php");
	include("_settings.php");
	include("_functions.php");
	include("_reCaptcha.php");

	if(isset($_GET['u'])){
		echo prepareUrl($_GET['u']);
	}
	
	if(isset($_GET['d'])){
		print_r(decryptStringArray($_GET['d']));
	}
?>