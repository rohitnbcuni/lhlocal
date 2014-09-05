<?PHP 
	include_once("_inc/config.inc");
	//include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	function get_user_risks($user_id, $mysql){
	global $mysql;
		$risksCount = 0;
		$risks = $mysql->sqlordie("SELECT count(1) as total FROM project_risks WHERE archived='0' AND active='1' AND assigned_to_user_id='$user_id'");
		if($risks){
			$count = $risks->fetch_assoc();
			$risksCount = $count['total'];
		}
		return $risksCount;
	}

?>