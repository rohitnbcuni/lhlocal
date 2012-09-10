<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	$projId = $mysql->real_escape_string(@$_REQUEST['compid']);
	$split = explode("_", $mysql->real_escape_string(@$_REQUEST['section']));

	$query = "SELECT * FROM `project_brief_sections` WHERE `project_id`='$projId' AND `section_type` = '" .$split[1] ."' LIMIT 1";
	$result = $mysql->query($query);
	
	if($result) {
		$row = $result->fetch_assoc();
		
		echo $row['flag'];
	}
?>