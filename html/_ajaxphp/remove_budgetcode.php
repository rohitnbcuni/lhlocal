<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	$budget_code = trim($mysql->real_escape_string(@$_GET['budget_code']));
	$project_id = trim($mysql->real_escape_string(@$_GET['project_id']));	

	if($budget_code != '') {
		$delete_budget_code_sql = "DELETE FROM `project_budget` where `project_id`= ".$project_id." AND `budget_code` = '".$budget_code."'";
	
		$response = $mysql->sqlordie($delete_budget_code_sql);
		echo "success";
	}
	

?>