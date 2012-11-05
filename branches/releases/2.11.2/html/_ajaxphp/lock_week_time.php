<?PHP 
	include("../_inc/config.inc");
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$start_date = $mysql->real_escape_string($_GET['start_date']);
	$week_number = $mysql->real_escape_string($_GET['week_number']);
	$user_id = $mysql->real_escape_string($_GET['user_id']);
	$start_date_array = explode('-', $start_date);

	$insertSql = "INSERT INTO `resource_planner_lock` (`user_id`, `week_num`, `year`, `start_date`) VALUES ('$user_id', '$week_number', '$start_date_array[0]', '$start_date')";

	$mysql->sqlordie($insertSql);
	echo $mysql->insert_id;
?>