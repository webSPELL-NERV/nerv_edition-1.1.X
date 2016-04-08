<?php
	
	// BASEVERSION IS 1.1
	function nerv_install_base($prefix, $host, $user, $pwd, $db){
		$_database = @new mysqli($host, $user, $pwd, $db);
		if ($_database->connect_error) {
			system_error('Cannot connect to MySQL-Server');
		}
		$_database->query("SET NAMES 'utf8'");
		
		$sqls = array();
		$sqls[] = "CREATE TABLE IF NOT EXISTS `".$prefix."attachments` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `topic_id` int(11) NOT NULL,
					  `user_id` int(11) NOT NULL,
					  `filename` varchar(255) NOT NULL,
					  `description` text,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
					
		$sqls[] = "CREATE TABLE IF NOT EXISTS `".$prefix."backups` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `filename` text NOT NULL,
					  `description` text,
					  `createdby` int(11) NOT NULL DEFAULT '0',
					  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
		
		$sqls[] = "CREATE TABLE IF NOT EXISTS `".$prefix."custom_controls` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `plugin` varchar(255) NOT NULL,
					  `name` varchar(255) NOT NULL,
					  `content` text NOT NULL,
					  `description` text,
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  `delete_date` timestamp NULL DEFAULT NULL,
					  `edit_date` timestamp NULL DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28;";
		
		$sqls[] = "INSERT INTO `".$prefix."custom_controls` (`id`, `plugin`, `name`, `content`, `description`, `create_date`, `delete_date`, `edit_date`) VALUES
					(1, 'ENCRYPTION', 'enable', '0', 'description_encryption_enable', '2016-03-12 09:53:04', NULL, '2016-04-07 13:24:52'),
					(3, 'RECAPTCHA', 'theme', 'light', 'description_recaptcha_theme', '2016-03-13 14:19:08', NULL, '2016-04-07 13:24:52'),
					(4, 'RECAPTCHA', 'enable', '1', 'description_recaptcha_enable', '2016-03-13 14:40:13', NULL, '2016-04-07 13:24:52'),
					(5, 'RECAPTCHA', 'size', 'small', 'description_recaptcha_size', '2016-03-13 14:40:13', NULL, '2016-04-07 13:24:52'),
					(6, 'RECAPTCHA', 'sitekey', '6Lf_VhwTAAAAAF8uYf1zGnogHzjpYMIQ78jjpv1k', 'description_recaptcha_sitekey', '2016-03-13 14:41:33', NULL, '2016-04-07 13:24:52'),
					(19, 'ENCRYPTION', 'level', 'rijndael-256', 'description_encryption_level', '2016-03-14 12:19:49', NULL, '2016-04-07 13:24:52'),
					(9, 'ENCRYPTION', 'password', 'kevinwie', 'description_encryption_password', '2016-03-14 11:32:13', NULL, '2016-04-07 13:24:52'),
					(20, 'ENCRYPTION', 'showsite', '1', 'description_encryption_showsite', '2016-03-14 12:52:03', NULL, '2016-04-07 13:24:52'),
					(21, 'RECAPTCHA', 'secretkey', '6Lf_VhwTAAAAAOpd1kFlgXfwBDGp8Bp3jwp7e7dG', 'description_recaptcha_secretkey', '2016-03-21 11:19:37', NULL, '2016-04-07 13:24:52'),
					(23, 'IMAGESIZES', 'avatar_width', '160', 'description_IMAGESIZES_avatar_width', '2016-03-23 06:00:28', NULL, '2016-04-07 13:24:52'),
					(24, 'IMAGESIZES', 'avatar_height', '160', 'description_IMAGESIZES_avatar_height', '2016-03-23 06:00:28', NULL, '2016-04-07 13:24:52'),
					(25, 'IMAGESIZES', 'userpic_height', '230', 'description_IMAGESIZES_userpic_height', '2016-03-23 06:00:28', NULL, '2016-04-07 13:24:52'),
					(26, 'IMAGESIZES', 'userpic_width', '210', 'description_IMAGESIZES_userpic_width', '2016-03-23 06:00:28', NULL, '2016-04-07 13:24:52'),
					(27, 'IMAGESIZES', 'max_upload', '400', 'description_IMAGESIZES_max_upload', '2016-03-23 06:07:53', NULL, '2016-04-07 13:24:52');";
		
		$sqls[] = "CREATE TABLE IF NOT EXISTS `".$prefix."custom_controls_form` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `control_id` int(11) NOT NULL,
					  `form_type` varchar(128) NOT NULL,
					  `grant_values` text,
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `control_id` (`control_id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;";
		
		$sqls[] = "INSERT INTO `".$prefix."custom_controls_form` (`id`, `control_id`, `form_type`, `grant_values`, `create_date`) VALUES
					(1, 3, 'combobox', 'dark##light', '2016-03-21 12:09:59'),
					(2, 1, 'checkbox', '1##0', '2016-03-21 13:33:48'),
					(3, 4, 'checkbox', '1##0', '2016-03-21 18:07:34'),
					(4, 19, 'combobox', 'rijndael-128##rijndael-256', '2016-03-21 18:08:34'),
					(5, 20, 'checkbox', '1##0', '2016-03-21 18:09:03'),
					(6, 5, 'combobox', 'normal##small', '2016-03-21 18:09:28'),
					(7, 23, 'numeric', '', '2016-03-23 06:00:28'),
					(8, 24, 'numeric', '', '2016-03-23 06:00:28'),
					(9, 25, 'numeric', '', '2016-03-23 06:00:28'),
					(10, 26, 'numeric', '', '2016-03-23 06:00:28'),
					(11, 27, 'numeric', '', '2016-03-23 06:07:53');";
		
		$sqls[] = "CREATE TABLE IF NOT EXISTS `".$prefix."font_icons` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `description` text NOT NULL,
					  `icon_class` varchar(128) NOT NULL,
					  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  `enable` tinyint(1) DEFAULT '1',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;";
		
		$sqls[] = "INSERT INTO `".$prefix."font_icons` (`id`, `description`, `icon_class`, `createdate`, `enable`) VALUES
					(1, '&#xf015;', 'fa fa-home', '2016-03-18 07:59:40', 1),
					(2, '&#xf007;', 'fa fa-user', '2016-03-18 08:43:24', 1),
					(3, '&#xf0c0;', 'fa fa-users', '2016-03-18 08:43:24', 1),
					(4, '&#xf019;', 'fa fa-download', '2016-03-18 08:43:24', 1),
					(5, '&#xf128;', 'fa fa-question', '2016-03-18 08:43:24', 1),
					(6, '&#xf0e0;', 'fa fa-envelope', '2016-03-18 08:43:24', 1),
					(7, '&#xf003;', 'fa fa-envelope-o', '2016-03-18 08:43:24', 1),
					(8, '&#xf09b;', 'fa fa-github', '2016-03-18 08:43:24', 1),
					(9, '&#xf085;', 'fa fa-cogs', '2016-03-18 08:43:24', 1),
					(10, '&#xf086;', 'fa fa-comments', '2016-03-18 08:43:24', 1),
					(11, '&#xf075;', 'fa fa-comment', '2016-03-18 08:43:24', 1),
					(12, '&#xf013;', 'fa fa-cog', '2016-03-18 08:43:24', 1),
					(13, '&#xf0e5;', 'fa fa-comments-o', '2016-03-18 08:43:24', 1),
					(15, '&#xf0c2;', 'fa fa-cloud', '2016-03-18 08:43:24', 1),
					(16, '&#xf1b3;', 'fa fa-cubes', '2016-03-18 08:43:24', 1),
					(17, '&#xf0e8;', 'fa fa-sitemap', '2016-03-18 08:43:24', 1),
					(18, '&#xf0dc;', 'fa fa-sort', '2016-03-18 08:43:24', 1),
					(19, '&#xf090;', 'fa fa-sign-in', '2016-03-18 08:43:24', 1),
					(20, '&#xf08b;', 'fa fa-sign-out', '2016-03-18 08:43:24', 1),
					(21, '&#xf15b;', 'fa fa-file', '2016-03-18 08:43:24', 1),
					(22, '&#xf013;', 'fa fa-spin fa-cog', '2016-03-18 08:43:24', 1),
					(23, '&#xf1fe;', 'fa fa-area-chart', '2016-03-18 08:43:24', 1),
					(24, '&#xf101;', 'fa fa-angle-double-right', '2016-03-18 08:43:24', 1),
					(25, '&#xf105;', 'fa fa-angle-right', '2016-03-18 08:43:24', 1),
					(26, '&#xf0da;', 'fa fa-caret-right', '2016-03-18 08:43:24', 1),
					(27, '&#xf054;', 'fa fa-chevron-right', '2016-03-18 08:43:24', 1),
					(28, '&#xf178;', 'fa fa-long-arrow-right', '2016-03-18 08:43:24', 1),
					(29, '&#xf061;', 'fa fa-arrow-right', '2016-03-18 08:43:24', 1),
					(30, '&#xf18e;', 'fa fa-arrow-circle-o-right', '2016-03-18 08:43:24', 1);";
		
		$sqls[] = "CREATE TABLE IF NOT EXISTS `".$prefix."navigation` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `parent` int(11) NOT NULL,
					  `icon_id` int(11) DEFAULT NULL,
					  `name` varchar(128) DEFAULT NULL,
					  `content` text,
					  `href` text,
					  `sort` int(11) NOT NULL DEFAULT '0',
					  `createdby` int(11) NOT NULL,
					  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  `enabled` tinyint(1) NOT NULL DEFAULT '1',
					  PRIMARY KEY (`id`),
					  KEY `icon_id` (`icon_id`),
					  KEY `createdby` (`createdby`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
		
		$sqls[] = "CREATE TABLE IF NOT EXISTS `".$prefix."translations` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `control` varchar(256) DEFAULT NULL,
					  `de` text,
					  `cz` text,
					  `dk` text,
					  `es` text,
					  `fr` text,
					  `hr` text,
					  `hu` text,
					  `il` text,
					  `ir` text,
					  `it` text,
					  `nl` text,
					  `no` text,
					  `pl` text,
					  `pt` text,
					  `se` text,
					  `sk` text,
					  `sr` text,
					  `uk` text,
					  `createdby` int(11) DEFAULT '0',
					  `createdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  PRIMARY KEY (`id`),
					  UNIQUE KEY `control` (`control`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;";
		
			
		$sqls[] = "CREATE TABLE IF NOT EXISTS `".$prefix."widgets` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `position` varchar(255) NOT NULL, 
					  `description` varchar(1024) DEFAULT NULL,
					  `plugin_folder` varchar(255) DEFAULT NULL,
					  `widget_file` varchar(255) DEFAULT NULL,
					  `sort` int(11) DEFAULT '0',
					  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1;";
		
		$sqls[] = "ALTER TABLE `".$prefix."partners`
					ADD `isSpecial` tinyint(1) NOT NULL DEFAULT '0'";
		
		$sqls[] = "CREATE TABLE IF NOT EXISTS `".$prefix."cookie_law` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `text` varchar(512) NOT NULL,
					  `days` int(11) DEFAULT '3',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2;";
					
		$sqls[] = "INSERT INTO `".$prefix."cookie_law` (`id`, `text`, `days`) VALUES
					(1, 'We use cookies. By browsing our site you agree to our use of cookies.', 15);";
		
		$errors = array();
		$i = 0;
		foreach($sqls as $sql){
			$i++;
			$result = $_database->query($sql);
			if(!$result){
				echo "Error executing '".$sql."' at sql: ".$i;
				$errors[] = $sql;
			}
		}
		
	}

	function nerv_checkintegrity(){
		$file_array = array(
			"NERV/_attachments.php",
			"NERV/_backup.php",
			"NERV/_controls.php",
			"NERV/_file_upload.php",
			"NERV/_generateURL.php",
			"NERV/_icons.php",
			"NERV/_inclusions.php",
			"NERV/_navigations.php",
			"NERV/_plugins.php",
			"NERV/_reCaptcha.php",
			"NERV/_translation.php",
			"NERV/_urlEncryption.php",
			"_magi_class.php",
			"js/_masterpage.js",
			"js/_nerv-core.js",
			"js/jquery-2.2.1.min.js",
			"css/_main.css",
			"css/font-awesome.css",
			"bootstrap/css/bootstrap.css",
			"bootstrap/fonts/glyphicons-halflings-regular.ttf",
			"bootstrap/js/bootstrap.js"
		);
		$errors = array();
		chdir("../");
		foreach($file_array as $file){
			if(!file_exists($file)){
				$errors[] = $file;
			}else{
				echo "'".$file."' file exists.<br>";
			}
		}
		chdir("install/");
		if(count($errors)>0){
			foreach($errors as $error){
				echo "<font color='red'>Missing file '".$error."'</font><br>";
			}
			return false;
		}else{
			echo "<b>All core file exists!</b>";
		}
		return true;
	}
	
?>