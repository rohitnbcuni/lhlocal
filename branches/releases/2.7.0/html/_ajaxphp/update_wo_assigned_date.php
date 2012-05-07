<?PHP
	include('../_inc/config.inc');
	
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	$wo_id = $mysql->real_escape_string($_GET['wo_id']);
	if(!empty($wo_id)) {
		$wo_res = $mysql->query("SELECT * FROM `workorders` WHERE `id`='$wo_id' LIMIT 1");
		$wo = $wo_res->fetch_assoc();
		$date_time_split = explode(" ", $wo['assigned_date']);
		$date_split = explode("-", $date_time_split[0]);
		
		$dt = number_format($date_split[1]) ."/" .number_format($date_split[2]) ."/" .$date_split[0];
		
		echo $dt;
	}
?>