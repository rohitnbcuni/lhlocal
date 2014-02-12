<?php

include('../_inc/config.inc');
include("sessionHandler.php");

//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
global $mysql;
$result = $mysql->sqlordie("SELECT  u.*,ll.last_logged_date  FROM users u INNER JOIN companies c on (u.company = c.id) LEFT JOIN users_login_log  ll ON (ll.user_id = u.id) order by u.first_name ");
			
$OUTPUT .= "User ID, Email,First Name,Last Name,Company,Login Status,Deleted,User Access,Last Logged In \n";
while($row = $result->fetch_assoc()){
	$active = ($row['active'] == '1')?'Active':'unActive';
 	$d = ($row['deleted'] == '1')?"Deleted":'Active';
	$OUTPUT .= $row['id'].",";
	$OUTPUT .= $row['email'].",";
	$OUTPUT .= $row['first_name'].",";
	$OUTPUT .= $row['last_name'].",";
	$OUTPUT .= $row['company'].",";
	$OUTPUT .= $row['login_status'].",";
	
	$OUTPUT .= $d.",";
	$OUTPUT .= $row['user_access'].",";
	$OUTPUT .= $row['last_logged_date'].",";
	
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
header("Content-Disposition: attachment; filename=\"export_users.csv\";" );
header("Content-Transfer-Encoding: binary"); 
//print $header; 
print $OUTPUT;



?>

