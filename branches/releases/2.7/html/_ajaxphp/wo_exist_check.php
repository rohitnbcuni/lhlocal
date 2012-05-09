<?PHP
	session_start();
	include('../_inc/config.inc');
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	$wo_id = $mysql->real_escape_string($_GET['woId']);
	
	$wo_selecte_query = "SELECT * from  `workorders` WHERE `id`='$wo_id' and deleted='0' LIMIT 1";
	$result = $mysql->query($wo_selecte_query);
	if($result->num_rows == 1){
		echo "1";
	}else{
		echo "0";
	}
	
?>