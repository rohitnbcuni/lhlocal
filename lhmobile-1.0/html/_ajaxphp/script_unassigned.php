<?PHP
	include('../_inc/config.inc');
	
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);	
	global $mysql;
	
	exit;
	echo "<center> <b> UNASSIGNED PROJECTS PHASE UPDATE <b></center> 1111";	
	$project_all = "SELECT * FROM `projects` ";
	$project_list = $mysql->sqlordie($project_all);
		echo "<center> <b> UNASSIGNED PROJECTS PHASE UPDATE <b></center> 1111";	
	if(@$project_list->num_rows > 0) {
		echo "<center> <b> UNASSIGNED PROJECTS PHASE UPDATE <b></center>";
		echo "<br>List of Projects : ";
		echo "<br>----------------------------------------------------------<br>";
		while($row = @$project_list->fetch_assoc()) {
		
		$project_id = $row['id'];

		$unassigned_prj_phase = "SELECT * FROM `project_phase_finance` where `project_id` ='".$project_id."' and phase='".UNASSIGNED_PHASE."'";
		$unassigned_prj_list = $mysql->sqlordie($unassigned_prj_phase);

		if(@$unassigned_prj_list->num_rows == 0) {
			$unAssignedPhaseInsertSql = 'INSERT INTO `project_phase_finance` (`project_id`, `phase`, `hours`, `rate`,`creation_date`) VALUES ("'.$project_id.'", "'.UNASSIGNED_PHASE.'", "0", "'.UNASSIGNED_PHASE_RATE.'", NOW())';
			$mysql->sqlordie($unAssignedPhaseInsertSql);
		echo "<br> UNASSIGNED PHASE inserted for the project - ".$project_id;
		}
		}
	}	
?>