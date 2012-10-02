<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql; 
	
	$project_id = $mysql->real_escape_string(@$_GET['project_id']);
	$projectRPPermission = $mysql->real_escape_string(@$_GET['rppermission']);
	$currentRPPermission = $mysql->real_escape_string(@$_GET['currentRPPermission']);

	$projectWOPermission = $mysql->real_escape_string(@$_GET['wopermission']);
	$currentWOPermission = $mysql->real_escape_string(@$_GET['currentWOPermission']);

	if($projectRPPermission != $currentRPPermission || $projectWOPermission != $currentWOPermission){
		$query = "UPDATE projects set rp_permission='$projectRPPermission',wo_permission='$projectWOPermission' where `id`='$project_id'";
		@$mysql->sqlordie($query);
	}

?>