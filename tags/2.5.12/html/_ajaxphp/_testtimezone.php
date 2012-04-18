<?php

include("../_inc/config.inc");
echo "PHP Time Zone: ".$script_tz = date_default_timezone_get();
echo "<br/>";
echo "PHP Current Time: ". date("Y-m-d H:i:s");
//date_default_timezone_set('America/New_York'); 
$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
$bc_id_result = $mysql->query("SELECT now()");
			$bc_id_row = $bc_id_result->fetch_assoc();
echo "<br/>Mysql Current Time: ";
print "<pre>";
print_r( $bc_id_row);


?>


