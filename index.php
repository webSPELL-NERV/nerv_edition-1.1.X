<?php
include("_mysql.php");
include("_settings.php");
include("_functions.php");
include("version.php");
nervinc("_navigations");
nervinc("_icons");
nervinc("_reCaptcha");
nervinc("_plugins");
$_language->read_module('index');
$_reCaptcha = new reCaptcha($_POST, $userID);
$_plugins = new plugins();
$index_language = $_language->module;
// end important data include
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<meta name="description" content="Clansite powered by webSPELL - NERV EDITON" />
	<meta name="author" content="Kevin Wiederstein" />
	<meta name="keywords" content="kevin, nerv, edition, webspell" />
	<meta name="copyright" content="Copyright &copy; 2016 by Kevinwie.de" />
	<meta name="generator" content="webSPELL - NERV EDITION" />

	<!-- Head & Title include -->
	<title><?php echo PAGETITLE; ?></title>
	<script src="js/jquery-2.2.1.min.js" type="text/javascript"></script>
	<script src="js/_nerv-core.js" type="text/javascript"></script>
	<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	<script src="js/background-blur.js" type="text/javascript"></script>
	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="auto.css" rel="stylesheet" type="text/css" />
	<link href="tmp/rss.xml" rel="alternate" type="application/rss+xml" title="<?php echo getinput($myclanname); ?> - RSS Feed" />
	<script src="js/bbcode.js" type="text/javascript"></script>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	<script>
		$(document).on("scroll", function(){
			var height = $(document).scrollTop();
			var header = $('#topHeader');
			console.log(height);
			if(height>1){
				if(header.css("position") != "fixed"){
					$("#invisDist").css("height", header.height());
					header.css("position", "fixed");
					header.css("top", "0");
					header.css("z-index", "999");
					header.css("width", "100%");
				}
			}else{
				$("#invisDist").css("height", "0px");
				header.css("position", "initial");
			}
		});
		
	</script>
	<?php $_icons = new icons();?>
	<?php $_plugins->getHeader();?>
</head>
<body>

		<?php
			$cookie_law = new plugins("cookie_law");
			echo $cookie_law->showWidget("cookie_law.php");
		?>

		<?php
			// $left_menu = new navigations("vertical"); 
			// $left_menu->register("left_menu", "Das ist das linke Menu in der Sidebar");
		?>
		<div id="invisDist"></div>
		<div id="topHeader">
			<div class="upper-header">
				<div class="wrapper">
					<?php include("sc_language.php");?>
				</div>
			</div>
			<div class="header">
				<div class="line1">
					<div class="wrapper floatbox">
						<div class="fltl logo">WEBSPELL</div>
						<div class="fltl nerv-logo">NERV EDITION ( <?php echo $nerv_version;?> )</small></div>
						<div class="fltr powered">powered by <a href="http://kevinwie.de" target="_blank">kevinwie.de</a></div>
					</div>
				</div>
				<div class="line2">
					<div class="wrapper">
					<?php
						$main_menu = new navigations("horizontal");
						$main_menu->register("main_menu", "Das ist die Hauptnavigation oben unterm header");
					?>
					</div>
				</div>
			</div> 
		</div>
		<div class="slider">
			<div class="image" style="background-image: url('images/featured/featured.jpg');">
			<!-- z-index: 0;
   background: rgba(0,0,0,.4);
   -webkit-filter: blur(20px);
   -moz-filter: blur(20px);
   -o-filter: blur(20px);
   -ms-filter: blur(20px);
   filter: blur(20px);-->
   
   
   <!--
   
   $sql=mysql_query("select * from Posts limit 20"); 

$response = array();
$posts = array();
$result=mysql_query($sql);
while($row=mysql_fetch_array($result)) 
{ 
$title=$row['title']; 
$url=$row['url']; 

$posts[] = array('title'=> $title, 'url'=> $url);

} 

$response['posts'] = $posts;

$fp = fopen('results.json', 'w');
fwrite($fp, json_encode($response));
fclose($fp);-->
				<div class="line1">Version <?php echo $nerv_version;?> - Preview</div>
				<div class="line2">Basierend auf die webSPELL Stable 4.2.5</div>
				<div class="line3">
					<a href="<?php echo prepareUrl("index.php?site=files");?>"><button class="btn btn-primary"><i class="fa fa-download"></i> DOWNLOAD</button></a>
				</div>
			</div>
		</div>
		
		
		<div class="wrapper content floatbox container-fluid">
			<div class="content-left col-sm-4 col-md-3 col-lg-3">
				<div class="left-box">
					<div class="left-headline">
						<i class="fa fa-bars"></i> Menu
					</div>
					<div class="left-content">
						<?php
							$left_menu = new navigations("vertical");
							$left_menu->register("left_menu", "Das ist das linke Menu in der Sidebar");
						?>
					</div>
				</div>
				<?php
					$widget_menu = new plugins();
					$widget_menu->registerWidget("Left_Side_Widget","This box is on the left", "vertical_widget_box");
				?>
			</div> 
			<div class="content-right col-sm-8 col-md-6 col-lg-6">
				<?php if($urlDecode_Error){ ?>
					<blockquote>
						<?php echo $urlDecode_Error; ?> 
					</blockquote>
				<?php } ?>
			
				<?php
					if(!isset($site)) $site="news";
					$invalide = array('\\','/','/\/',':','.');
					$site = str_replace($invalide,' ',$site);
					if($_plugins->isSite($site)){
						$site = $_plugins->isSite($site);
					}else{
						if(!file_exists($site.".php")) $site = "news";
					}
					include($site.".php");
					?>
			</div>
			<div class="col-sm-12 col-md-3 col-lg-3">
				<?php
					$widget_menu = new plugins();
					$widget_menu->registerWidget("Right_Side_Widget","This box is on the right", "vertical_widget_box");
				?>
			</div>
		</div>
		

		<div class="footer">
			<div class="wrapper inner">
				<div class="upper-line floatbox">
					<div class="fltl social-links floatbox">
						<a href="#"><i class="fa fa-facebook-official"></i></a>
						<a href="#"><i class="fa fa-twitter-square"></i></a>
						<a href="#"><i class="fa fa-github-square"></i></a>
					</div>
					<div class="fltr">
						Questions about the <a href="<?php echo prepareUrl("index.php?site=forum");?>"><b>NERV-EDITION</b></a>?
					</div>
				</div>
				<div class="under-box">
					<div class="row">
						<div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
							<?php include("about.php");?>
						</div>
						<div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
							<?php
								$footer_menu = new navigations("vertical");
								$footer_menu->register("footer_menu", "Dieses Menu befindet sich im Footer");
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="copyright">
			<div class="wrapper">
				Copyright <i class="fa fa-copyright"></i> 2015-2016 by <a href="http://www.webspell-nerv.de"><b>www.webspell-nerv.de</b></a>
			</div>
		</div>
</body>
</html>