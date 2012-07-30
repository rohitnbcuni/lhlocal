<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	$project_id = $mysql->real_escape_string(@$_REQUEST['project_id']);

	$num_briefs = 0;
	$num_complete = 0;
	$query = "SELECT * FROM `lnk_project_brief_section_types`";
	$result = $mysql->query($query);
	
	$num_briefs = $result->num_rows;
	
	while($brief_row = $result->fetch_assoc()) {
		$query_check = "SELECT * FROM `project_brief_sections` WHERE `project_id`='$project_id' AND `flag`='3' AND `section_type`='" .$brief_row['id'] ."'";
		$result_check = $mysql->query($query_check);
		
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