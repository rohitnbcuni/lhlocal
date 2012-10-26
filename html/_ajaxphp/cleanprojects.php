#!/usr/bin/php
<?PHP
	include('../_inc/config.inc');
	
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	die;
	
	$update_proj = "DELETE FROM `projects` WHERE `bc_id` IS NULL";
	$mysql->query($update_proj);

	$mysql->close();
?>