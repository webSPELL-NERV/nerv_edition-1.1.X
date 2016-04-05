<?php
	chdir('../');
	include_once("_mysql.php");
	include_once("_settings.php");
	include_once("_functions.php");
	nervinc("_backup");
	nervinc("_icons");
	//chdir('admin');
	 
	$backups = new backup();
	//$icons	 = new icons(true);
	
	if(ispageadmin($userID) && isset($_GET['bid'])) {
		$bid = $_GET['bid'];
		$backup = $backups->getBackup($bid);
		$filename = $backup['filename'];
		$backups->download($filename,0);
		//echo '<script>window.location.href = "admincenter.php?site=backups";</script>';
	}
?>