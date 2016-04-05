<?php
	$install_result = false;

	$create_query = "
		CREATE TABLE IF NOT EXISTS ".PREFIX."cookie_law (
			id int auto_increment primary key,
			text varchar(512) not null,
			days int default 3
		)";
		
	$insert_query =	"INSERT INTO ".PREFIX."cookie_law (
			text,
			days
		) VALUES (
			'We use cookies. By browsing our site you agree to our use of cookies.',
			3
		);
	";
	
	$create = isafe_query($create_query);
	$insert = isafe_query($insert_query);
	if($create && $insert){
		$install_result = true;
	}

?>