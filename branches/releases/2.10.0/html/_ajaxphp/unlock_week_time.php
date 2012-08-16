<?PHP 
	include("../_inc/config.inc");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	$start_date = $_GET['start_date'];
	$week_number = $_GET['week_number'];
	$user_id = $_GET['user_id'];
	$start_date_array = explode('-', $start_date);

	$insertSql = "DELETE FROM `resource_planner_lock` where `user_id` = '$user_id' AND `week_num` = '$week_number' AND `year` =  '$start_date_array[0]'";

	$mysql->query($insertSql);
	echo $mysql->affected_rows;
?>