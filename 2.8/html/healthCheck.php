<?php

include('_inc/config.inc');

	
	$link = mysql_connect(DB_SERVER.":".DB_PORT, DB_USERNAME, DB_PASSWORD);
	if (!$link) {
	    die('Could not connect: ' . mysql_error());
	}else{
		echo 'GOOD';
	}
	
	
	// make DB_DATABASE the current db
	$db_selected = mysql_select_db(DB_DATABASE, $link);
	if (!$db_selected) {
	    die ('Can\'t use Database : ' . mysql_error());
	}
	mysql_close($link);