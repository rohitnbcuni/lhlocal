<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	
	if(isset($_GET['wo_id'])) {
		$wo_id = $mysql->real_escape_string($_GET['wo_id']);
		
		$query = "SELECT COUNT(`id`) as total FROM `workorder_comments` WHERE `workorder_id`=? AND `active`='1' AND `deleted`='0'";
		$total_res = $mysql->sqlprepare($query, array($wo_id));
		$total_row = $total_res->fetch_assoc();
		
		echo $total_row['total'];
	} else {
		echo "0";
	}
?>