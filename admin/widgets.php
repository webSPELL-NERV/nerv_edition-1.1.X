<?php
	chdir("../");
	nervinc("_plugins");
	nervinc("_icons");
	
	if(!isanyadmin($userID) OR mb_substr(basename($_SERVER['REQUEST_URI']),0,15) != "admincenter.php") die($_language->module['access_denied']);
	$_language->read_module('widgets');

	$icon_class	= new icons(true);
	$plugin_class = new plugins();
	
?>