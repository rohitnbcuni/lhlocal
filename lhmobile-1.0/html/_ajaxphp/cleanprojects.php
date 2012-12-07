#!/usr/bin/php
<?PHP
	include('../_inc/config.inc');
	
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	exit;
	
	$update_proj = "DELETE FROM `projects` WHERE `bc_id` IS NULL";
	$mysql->sqlordie($update_proj);

	$mysql->close();
?>