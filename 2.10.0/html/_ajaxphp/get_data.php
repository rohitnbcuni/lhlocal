<?PHP 
	include_once("_inc/config.inc");

	function get_user_risks($user_id, $mysql){
		$risksCount = 0;
		$risks = $mysql->query("SELECT count(1) as total FROM project_risks WHERE archived='0' AND active='1' AND assigned_to_user_id='$user_id'");
		if($risks){
			$count = $risks->fetch_assoc();
			$risksCount = $count['total'];
		}
		return $risksCount;
	}

?>