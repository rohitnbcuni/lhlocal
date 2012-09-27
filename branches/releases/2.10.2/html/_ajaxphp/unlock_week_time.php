<?PHP 
	include("../_inc/config.inc");
	include("sessionHandler.php");
	
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$start_date = $mysql->real_escape_string($_GET['start_date']);
	$week_number = $mysql->real_escape_string($_GET['week_number']);
	$user_id = $mysql->real_escape_string($_GET['user_id']);
	$start_date_array = explode('-', $start_date);

	$insertSql = "DELETE FROM `resource_planner_lock` where `user_id` = '$user_id' AND `week_num` = '$week_number' AND `year` =  '$start_date_array[0]'";

	$mysql->sqlordie($insertSql);
	echo $mysql->affected_rows;
?>