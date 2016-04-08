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

include_once("geshi/geshi.php");

// -- SET ENCODING FOR MB-FUNCTIONS -- //

mb_internal_encoding("UTF-8");

// -- SET HTTP ENCODING -- //

header('content-type: text/html; charset=utf-8');

// -- CONNECTION TO MYSQL -- //


//mysql_query("SET NAMES 'utf8'");

// -- ERROR REPORTING -- //

define('DEBUG', "ON");
error_reporting(E_ALL); // 0 = public mode, E_ALL = development-mode
ini_set('display_errors', 'Off');

/* URL ENCRYPTION INCLUDE WEBSPELL #DO NOT EDIT */
// updateControl("ENCRYPTION", "level", MCRYPT_RIJNDAEL_128);


mysql_connect($host, $user, $pwd) or system_error('ERROR: Can not connect to MySQL-Server');
mysql_select_db($db) or system_error('ERROR: Can not connect to database "'.$db.'"');

// -- CONNECTION TO MYSQL -- //
if (!isset($GLOBALS[ '_database' ])) {
    $_database = @new mysqli($host, $user, $pwd, $db);

    if ($_database->connect_error) {
        system_error('Cannot connect to MySQL-Server');
    }
    $_database->query("SET NAMES 'utf8'");
}

nervinc("_controls");
$controls = new controls();
define('_URLENCRYPTION_PASSWORD'	, $controls->getControl("ENCRYPTION", "password"));	// The RIJNDAEL Password
define('_URLENCRYPTION_ENABLED' 	, $controls->getControl("ENCRYPTION", "enable"));	// 1 or 0 
define('_URLENCRYPTION_LEVEL'		, $controls->getControl("ENCRYPTION", "level")); 	// 256 or 128
define('_URLENCRYPTION_SHOWSITE'	, $controls->getControl("ENCRYPTION", "showsite"));	// 256 or 128
nervinc("_urlEncryption");

/* URL ENCRYPTION INCLUDE WEBSPELL #DO NOT EDIT */





// -- GENERAL PROTECTIONS -- //

if (function_exists("globalskiller") === false) {
    function globalskiller()
    {
        // kills all non-system variables
        $global = array(
            'GLOBALS',
            '_POST',
            '_GET',
            '_COOKIE',
            '_FILES',
            '_SERVER',
            '_ENV',
            '_REQUEST',
            '_SESSION',
            '_database'
        );

        foreach ($GLOBALS as $key => $val) {
            if (!in_array($key, $global)) {
                if (is_array($val)) {
                    unset_array($GLOBALS[ $key ]);
                } else {
                    unset($GLOBALS[ $key ]);
                }
            }
        }
    }
}

function unset_array($array) {

	foreach($array as $key) {
		if(is_array($key)) unset_array($key);
		else unset($key);
	}
}

globalskiller();

/* URL ENCRYPTION INCLUDE WEBSPELL #DO NOT EDIT */
/* URL ENCRYPTION INCLUDE WEBSPELL #DO NOT EDIT */
/* URL ENCRYPTION INCLUDE WEBSPELL #DO NOT EDIT */
/* URL ENCRYPTION INCLUDE WEBSPELL #DO NOT EDIT */
$urlDecode_Error = "";
$kkey = "";
if(_URLENCRYPTION_ENABLED){
	$curdir = dirname($_SERVER['REQUEST_URI']);
	if($curdir <> "/webspell/admin"){
		if(isset($_GET["_secret"])){
			$kkey = $_GET["_secret"];
			setGET($_GET["_secret"]);
		}else{
			if(count($_GET)>0){
				$urlDecode_Error = "<small><b>404</b> - Site not found. </small>";
			}
			$_GET['site'] = "news";
		}
	}
}
/* URL ENCRYPTION INCLUDE WEBSPELL #DO NOT EDIT */
/* URL ENCRYPTION INCLUDE WEBSPELL #DO NOT EDIT */
/* URL ENCRYPTION INCLUDE WEBSPELL #DO NOT EDIT */
/* URL ENCRYPTION INCLUDE WEBSPELL #DO NOT EDIT */



if(isset($_GET['site'])) $site=$_GET['site'];
else $site= null;
if($site!="search") {
	$request=strtolower((decryptStringArray($kkey)));
	//die($request);
	$protarray = array(
        "union",
        "select",
        "into",
        "where",
        "update ",
        "from",
        "/*",
        "set ",
        PREFIX . "user ",
        PREFIX . "user(",
        PREFIX . "user`",
        PREFIX . "user_groups",
        "phpinfo",
        "escapeshellarg",
        "exec",
        "fopen",
        "fwrite",
        "escapeshellcmd",
        "passthru",
        "proc_close",
        "proc_get_status",
        "proc_nice",
        "proc_open",
        "proc_terminate",
        "shell_exec",
        "system",
        "telnet",
        "ssh",
        "cmd",
        "mv",
        "chmod",
        "chdir",
        "locate",
        "killall",
        "passwd",
        "kill",
        "script",
        "bash",
        "perl",
        "mysql",
        "~root",
        ".history",
        "~nobody",
        "getenv"
    );
	
	$thebadWords = "<br> &raquo; ";
	foreach($protarray as $badword) {
		$place = strpos($request, $badword);
		if (!empty($place)) {
			$thebadWords = $thebadWords.$badword.",";
		}
	}
	
	$check=str_replace($protarray, '*', $request);
	if($request != $check) system_error("Invalid request detected.<br>$thebadWords");
}


// function security_slashes(&$array)
// {

    // global $_database;

    // foreach ($array as $key => $value) {
        // if (is_array($array[ $key ])) {
            // security_slashes($array[ $key ]);
        // } else {
            // if (get_magic_quotes_gpc()) {
                // $tmp = stripslashes($value);
            // } else {
                // $tmp = $value;
            // }
            // if (function_exists("mysqli_real_escape_string")) {
                // $array[ $key ] = $_database->escape_string($tmp);
            // } else {
                // $array[ $key ] = addslashes($tmp);
            // }
            // unset($tmp);
        // }
    // }
// }

// security_slashes($_POST);
// security_slashes($_COOKIE);
// security_slashes($_GET);
// security_slashes($_REQUEST);

function security_slashes(&$array) {
	foreach($array as $key => $value) {
		if(is_array($array[$key])) {
			security_slashes($array[$key]);
		}
		else {
			if(get_magic_quotes_gpc()) {
				$tmp = stripslashes($value);
			}
			else {
				$tmp = $value;
			}
			if(function_exists("mysql_real_escape_string")) {
				$array[$key] = mysql_real_escape_string($tmp);
			}
			else {
				$array[$key] = addslashes($tmp);
			}
			unset($tmp);
		}
	}
}

security_slashes($_POST);
security_slashes($_COOKIE);
security_slashes($_GET);
security_slashes($_REQUEST);

// -- MYSQL QUERY FUNCTION -- //
$_mysql_querys = array();
function safe_query($query="") {
	global $_mysql_querys;
	if(stristr(str_replace(' ', '', $query), "unionselect")===FALSE AND stristr(str_replace(' ', '', $query), "union(select")===FALSE){
		$_mysql_querys[] = $query;
		if(empty($query)) return false;
		if(DEBUG == "OFF") $result = mysql_query($query) or die('Query failed!');
		else {
			$result = mysql_query($query) or die('Query failed: '
			.'<li>errorno='.mysql_errno()
			.'<li>error='.mysql_error()
			.'<li>query='.$query);
		}
		return $result;
	}
	else die();
}


$_mysql_querys = array();
function isafe_query($query = "")
{

    global $_database;
    global $_mysql_querys;

    if (stristr(str_replace(' ', '', $query), "unionselect") === false and
        stristr(str_replace(' ', '', $query), "union(select") === false
    ) {
        $_mysql_querys[ ] = $query;
        if (empty($query)) {
            return false;
        }
        if (DEBUG == "OFF") {
            $result = $_database->query($query) or system_error('Query failed!');
        } else {
            $result = $_database->query($query) or
            system_error(
                '<strong>Query failed</strong> ' . '<ul>' .
                '<li>MySQL error no.: <mark>' . $_database->errno . '</mark></li>' .
                '<li>MySQL error: <mark>' . $_database->error . '</mark></li>' .
                '<li>SQL: <mark>' . $query . '</mark></li>'.
                '</ul>',
                1,
                1
            );
        }
        return $result;
    } else {
        die();
    }
}

// -- SYSTEM ERROR DISPLAY -- //

function system_error($text,$system=1) {
	if($system) {
		include('version.php');
		$info='<b>webSPELL Version:</b> '.$version.'<br /><b>PHP Version:</b> '.phpversion().'<br /><b>MySQL Version:</b> '.mysqli_get_server_info().'<br /><hr>';
	} else {
		$info = '';
	}
	die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	  <meta name="description" content="Clanpage using webSPELL 4 - NERV EDTION CMS" />
	  <meta name="author" content="webspell.org" />
	  <meta name="keywords" content="webspell, webspell4, clan, cms" />
	  <meta name="copyright" content="Copyright &copy; 2005 - 2011 by webspell.org" />
	  <meta name="generator" content="webSPELL" />
	  <script src="js/jquery-2.2.1.min.js" type="text/javascript"></script>
	<script src="js/_nerv-core.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="js/background-blur.js" type="text/javascript"></script>
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	  <title>webSPELL</title>
  </head>
  <body style="width: 480px; margin: 0 auto;margin-top: 30px">
  <div class="panel panel-primary">
	<div class="panel-heading"><b>A System error appears!</b><br><small>Oww! we catched an error, look here:</small></div>
	<div class="panel-body">
		<div style="color:#333333;font-family:Tahoma,Verdana,Arial;font-size:11px;padding:5px;">'.$info.'<br />
			<div class="alert alert-danger" style="font-weight: bold">'.$text.'!</div>
			</div>
	  </div>
	  <div class="panel-footer">For support visit <a href="http://www.webspell-nerv.de" target="_blank"><b>www.webspell-nerv.de</b></a></div>
	 </div>
  </body>
  </html>');
}

// -- SYSTEM FILE INCLUDE -- //

function systeminc($file) {
	if(!include_once('src/'.$file.'.php')) system_error('Could not get system file for '.$file);
}
 
function nervinc($file, $maindir = false){
	 @chdir("NERV");
	 if(!include_once($file.'.php')) system_error('Could not get system file for '.$file);
	 @chdir("../");
	// if($maindir){
		// if(strstr(dirname(__FILE__),"NERV")){
			// if(!include_once("../".$file.".php")) system_error('could not get system file for '.$file);
		// }else if(strstr(dirname(__FILE__),"admin")){
			// if(!include_once("../".$file.".php")) system_error('could not get system file for '.$file);
		// }else{
			// if(!include_once(dirname(__FILE__)."/NERV".$file.".php")) system_error('could not get system file for '.$file);
		// }
	// }else{
		// if(strstr(dirname(__FILE__),"NERV")){
			// if(!include_once($file.".php")) system_error('could not get system file for '.$file);
		// }else if(strstr(dirname(__FILE__),"admin")){
			// if(!include_once("../NERV/".$file.".php")) system_error('could not get system file for '.$file);
		// }else{
			// if(!include_once("NERV/".$file.".php")) system_error('could not get system file for '.$file);
		// }
	// }
}

// -- IGNORED USERS -- //

function isignored($userID, $buddy) {
	$anz=mysql_num_rows(safe_query("SELECT userID FROM ".PREFIX."buddys WHERE buddy='$buddy' AND userID='$userID' "));
	if($anz) {
		$ergebnis=safe_query("SELECT * FROM ".PREFIX."buddys WHERE buddy='$buddy' AND userID='$userID' ");
		$ds=mysql_fetch_array($ergebnis);
		if($ds['banned']==1) return 1;
		else return 0;
	}
	else return 0;
}

// -- GLOBAL SETTINGS -- //

$ds = mysql_fetch_array(safe_query("SELECT * FROM ".PREFIX."settings"));

$maxshownnews				=	$ds['news']; 				if(empty($maxshownnews)) $maxshownnews = 10;
$maxnewsarchiv				=	$ds['newsarchiv']; 			if(empty($maxnewsarchiv)) $maxnewsarchiv = 20;
$maxheadlines				=	$ds['headlines']; 			if(empty($maxheadlines)) $maxheadlines = 10;
$maxheadlinechars			=	$ds['headlineschars']; 		if(empty($maxheadlinechars)) $maxheadlinechars = 18;
$maxtopnewschars			=	$ds['topnewschars']; 		if(empty($maxtopnewschars)) $maxtopnewschars = 200;
$maxarticles				=	$ds['articles']; 			if(empty($maxarticles)) $maxarticles = 20;
$latestarticles				=	$ds['latestarticles']; 		if(empty($latestarticles)) $latestarticles = 5;
$articleschars				=	$ds['articleschars']; 		if(empty($articleschars)) $articleschars = 18;
$maxclanwars				=	$ds['clanwars']; 			if(empty($maxclanwars)) $maxclanwars = 20;
$maxresults					=	$ds['results']; 			if(empty($maxresults)) $maxresults = 5;
$maxupcoming				=	$ds['upcoming']; 			if(empty($maxupcoming)) $maxupcoming = 5;
$maxguestbook				=	$ds['guestbook']; 			if(empty($maxguestbook)) $maxguestbook = 20;
$maxshoutbox				=	$ds['shoutbox']; 			if(empty($maxshoutbox)) $maxshoutbox = 5;
$maxsball					=	$ds['sball']; 				if(empty($maxsball)) $maxsball = 5;
$sbrefresh					=	$ds['sbrefresh']; 			if(empty($sbrefresh)) $sbrefresh = 60;
$maxtopics					=	$ds['topics']; 				if(empty($maxtopics)) $maxtopics = 20;
$maxposts					=	$ds['posts']; 				if(empty($maxposts)) $maxposts = 10;
$maxlatesttopics			=	$ds['latesttopics']; 		if(empty($maxlatesttopics)) $maxlatesttopics = 10;
$maxlatesttopicchars		=	$ds['latesttopicchars']; 	if(empty($maxlatesttopicchars)) $maxlatesttopicchars = 18;
$maxfeedback				=	$ds['feedback']; 			if(empty($maxfeedback)) $maxfeedback = 5;
$maxmessages				=	$ds['messages']; 			if(empty($maxmessages)) $maxmessages = 5;
$maxusers					=	$ds['users']; 				if(empty($maxusers)) $maxusers = 5;
$hp_url						=	$ds['hpurl'];
$admin_name					=	$ds['adminname'];
$admin_email				=	$ds['adminemail'];
$myclantag					=	$ds['clantag'];
$myclanname					=	$ds['clanname'];
$maxarticles				=	$ds['articles']; 			if(empty($maxarticles)) $maxarticles = 5;
$maxawards					=	$ds['awards']; 				if(empty($maxawards)) $maxawards = 20;
$maxdemos					=	$ds['demos']; 				if(empty($maxdemos)) $maxdemos = 20;
$profilelast				=	$ds['profilelast']; 		if(empty($profilelast)) $profilelast = 20;
$topnewsID					=	$ds['topnewsID'];
$sessionduration			=	$ds['sessionduration']; 	if(empty($sessionduration)) $sessionduration = 24;
$closed						=	(int)$ds['closed'];
$gb_info					=	$ds['gb_info'];
$imprint_type				=	$ds['imprint'];
$picsize_l					=	$ds['picsize_l'];			if(empty($picsize_l)) $picsize_l = 9999;
$picsize_h					=	$ds['picsize_h'];			if(empty($picsize_h)) $picsize_h = 9999;
$gallerypictures			=	$ds['pictures'];
$publicadmin				=	$ds['publicadmin'];
$thumbwidth					=	$ds['thumbwidth'];  		if(empty($thumbwidth)) $thumbwidth = 120;
$usergalleries				=	$ds['usergalleries'];
$maxusergalleries			=	$ds['maxusergalleries'];
$default_language			=	$ds['default_language']; 	if(empty($default_language)) $default_language = 'uk';
$rss_default_language		=	$ds['default_language']; 	if(empty($rss_default_language)) $rss_default_language = 'uk';
$search_min_len				=	$ds['search_min_len']; 		if(empty($search_min_len)) $search_min_len = '4';
$autoresize 				= 	$ds['autoresize']; 			if(!isset($autoresize)) $autoresize = 2;
$max_wrong_pw 				= 	$ds['max_wrong_pw']; 		if(empty($max_wrong_pw)) $max_wrong_pw = 3;
$lastBanCheck 				= 	$ds['bancheck']; 
$insertlinks				=	$ds['insertlinks'];
$new_chmod = 0666;

// -- STYLES -- //

$ergebnis=safe_query("SELECT * FROM ".PREFIX."styles");
$ds=mysql_fetch_array($ergebnis);

define('PAGEBG', $ds['bgpage']);
define('BORDER', $ds['border']);
define('BGHEAD', $ds['bghead']);
define('BGCAT', $ds['bgcat']);
define('BG_1', $ds['bg1']);
define('BG_2', $ds['bg2']);
define('BG_3', $ds['bg3']);
define('BG_4', $ds['bg4']);

$hp_title = stripslashes($ds['title']);
$pagebg = PAGEBG;
$border = BORDER;
$bghead = BGHEAD;
$bgcat  = BGCAT;

$wincolor = $ds['win'];
$loosecolor = $ds['loose'];
$drawcolor = $ds['draw'];



?>