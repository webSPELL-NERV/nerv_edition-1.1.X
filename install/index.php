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

session_name("ws_session");
session_start();
header('content-type: text/html; charset=utf-8');
include("../src/func/language.php");
include("../version.php");
include("_nerv_install.php");
$_language = new Language();

if(!isset($_SESSION['language'])){
	$_SESSION['language'] = "uk";
}

if(isset($_GET['lang'])){
	if($_language->set_language($_GET['lang'])) $_SESSION['language'] = $_GET['lang'];
	header("Location: index.php");
	exit();
}

$_language->set_language($_SESSION['language']);
$_language->read_module('index');

if(isset($_GET['step'])) $_language->read_module('step'.(int)$_GET['step'],true);
else $_language->read_module('step0',true);

if(!isset($_GET['step'])){
	$_GET['step'] = "";
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Clanpage using webSPELL 4 CMS" />
<meta name="author" content="webspell.org" />
<meta name="keywords" content="webspell, webspell4, clan, cms" />
<meta name="copyright" content="Copyright &copy; 2005 - 2011 by webspell.org" />
<meta name="generator" content="webSPELL" />
<title>webSPELL-NERV Installation</title>
<title><?php echo PAGETITLE; ?></title>
<script src="../js/jquery-2.2.1.min.js" type="text/javascript"></script>
<script src="../js/_nerv-core.js" type="text/javascript"></script>
<script src="../bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../js/background-blur.js" type="text/javascript"></script>
<link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body style="width: 700px;margin:0 auto;margin-top: 30px;">
<div class="panel panel-primary">
	<div class="panel-heading"><b>webSPELL-NERV Setup</b>&nbsp;<small>(Version: <?php echo $nerv_version;?>)</small></div>
	<div class="panel-body">
	   <?php
	   echo '<tr><td colspan="2"><form action="index.php?step='.($_GET['step']+1).'" method="post" name="ws_install" /></td></tr>';
	   include('step0'.$_GET['step'].'.php');
	   ?>
	</div>
</div>
</body>
</html>