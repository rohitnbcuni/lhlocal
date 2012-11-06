<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$project_id = $mysql->real_escape_string(@$_REQUEST['project_id']);

	$num_briefs = 0;
	$num_complete = 0;
	$query = "SELECT * FROM `lnk_project_brief_section_types`";
	$result = $mysql->sqlordie($query);
	
	$num_briefs = $result->num_rows;
	
	while($brief_row = $result->fetch_assoc()) {
		$query_check = "SELECT * FROM `project_brief_sections` WHERE `project_id`= ? AND `flag`= ? AND `section_type`= ?";
		$result_check = $mysql->sqlprepare($query_check, array($project_id,3,$brief_row['id'] ));
		
		if($result_check->num_rows == 1) {
			$num_complete++;
		}
	}
	if($num_briefs > 0) {
		$calc = ceil((($num_complete/$num_briefs)*100));
		
		if($calc > 100) {
			$calc = 100;
		}
	} else {
		$calc = 0;
	}
	
	echo $calc;
?>