<?php

include('../_inc/config.inc');
include("sessionHandler.php");
include('../_ajaxphp/rally_function.php');
//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
global $mysql;
$result = $mysql->sqlordie("SELECT l.pattern, CONCAT_WS(' ', first_name,last_name) as user_name from search_log l INNER JOIN users u ON l.user_id = u.id");
$OUTPUT .= "User Name, Search Strings\n";
while($row = $result->fetch_assoc()){
	$OUTPUT .= $row['user_name'].",";
	$OUTPUT .= (strlen($row['pattern']) > 50)?substr($row['pattern'],0,50)."...":$row['pattern'];
	$OUTPUT .= "\n";

}


	


header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
// This one allows display or download
header("Content-Type: application/octet-stream");
// This one forces a download - uncomment (and comment octet-stream) if you want that
//header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"search_log.csv\";" );
header("Content-Transfer-Encoding: binary"); 
//print $header; 
print $OUTPUT;



?>

