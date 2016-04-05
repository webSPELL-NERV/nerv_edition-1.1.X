<?php

	define('DEBUG', "ON");
	error_reporting(E_ALL); // 0 = public mode, E_ALL = development-mode
	ini_set('display_errors', 'Off');

	chdir('../');
	nervinc("_backup");
	nervinc("_icons");
	chdir('admin');

	echo '
		<style>
			form{display: inline;}
			select{display: inline;}
			input{display: inline;}
		</style>
	';
	
	$fullBackupAjax = "admincenter.php?site=backups&action=fullbackup";
	$deleteAllAjax	= "admincenter.php?site=backups&action=deleteAll";
	$sqlBackupAjax	= "admincenter.php?site=backups&action=sqlbackup";
	$fileBackupAjax = "admincenter.php?site=backups&action=filesbackup";
	$back_url		= "admincenter.php?site=backups";
	
	
	if(!ispageadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);
	echo'<h1>&curren; System backups</h1>';

	$backups = new backup($userID);
	$icons	 = new icons(true);

	chdir('../');

	if(isset($_GET['fid']) && isset($_GET['action'])){
		$fid 		= $_GET['fid'];
		$action 	= $_GET['action'];
		if($action == "download"){
			Header("Location: backups_download.php?bid='.$fid.'");
			//echo '<script>window.location.href = "admincenter.php?site=backups";</script>';
		}else if($action == "delete"){
			$backups->deleteBackup($fid);
		}
	}else if(isset($_GET['action'])){
		$action 	= $_GET['action'];
		if($action=="fullbackup"){
			$backups->doIT();
		}else if($action=="deleteAll"){
			$backups->deleteAll();
		}else if($action=="sqlbackup"){
			$backups->SQLBackup();
		}else if($action=="filesbackup"){
			$backups->FILESBackup();
		}
	}
	
	
	// SHOW ALL BACKUPS

	eval ("\$admin_backups_head = \"".gettemplate("admin_backups_head")."\";");
	echo $admin_backups_head;

	$allBackups = $backups->getAllBackups();
	$ct = 1;
	$first_line = "";
	foreach($allBackups as $currBackup){
		$c = ($ct%2)+1;
		$ct++;
		//if($ct == 1  || $ct == 2){$first_line="style='background-color: #bfff80' ";$ct++;}
		$id		 		= $currBackup['id'];
		$filename 		= $currBackup['filename'];
		$description	= $currBackup['description'];
		$createdby		= (getnickname($currBackup['createdby'])) ?: "System";
		$createdate		= date("d/m/Y h:i:sa", strtotime($currBackup['createdate']));
		$file_exists 	= "<img src='../images/icons/offline.gif' alt='' />";
		$download_url 	= "backups_download.php?bid=$id";
		$filesize		= "0.00";
		$status 		= "style='display: none'";
		if(file_exists($filename)){
			$file_exists 	= "<img src='../images/icons/online.gif' alt='' />";
			$filesize		= round(filesize($filename)/1048576, 3);
			$status 		= "";
		}
		
		eval ("\$admin_backups_content = \"".gettemplate("admin_backups_content")."\";");
		echo $admin_backups_content;
		$first_line = "";
	}
	
	eval ("\$admin_backups_footer = \"".gettemplate("admin_backups_footer")."\";");
	echo $admin_backups_footer;

	//$backups->doIT();
	// $files = array(
				// 'admin/backups/db-backup-1458479470-1419cc62d9761c96800ba45177f35b36.zip' => '',
				// 'admin/backups/ws-backup-1458479470-1419cc62d9761c96800ba45177f35b36.zip' => ''
		// );
		// $backups->insertBackups($files);
?>