<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql; 
	
	$project_id = $mysql->real_escape_string(@$_GET['project_id']);
	$project_status_id = $mysql->real_escape_string(@$_GET['project_status_id']);
	$userID = $mysql->real_escape_string(@$_GET['user_id']);
	$html = '';
	
	$project_sql = 'SELECT IFNULL(project_status, 0) AS status FROM projects WHERE id="' . $project_id . '"';
	$projectResult = $mysql->sqlordie($project_sql);
	$projectRow = $projectResult->fetch_assoc();
	
	$project_status_sql = 'SELECT * FROM lnk_project_status_types WHERE id="' . $project_status_id . '"';
	$projectStatusResult = $mysql->sqlordie($project_status_sql);
	$projectStatusRow = $projectStatusResult->fetch_assoc();

	$class_name = str_replace(" ", "", strtolower($projectStatusRow['name']));

	if($project_status_id != $projectRow['status']){
		$permissionUpdate = "";
		if($project_status_id == "6"){
			$permissionUpdate = ",`rp_permission`='0',`wo_permission`='0'";
		}
		$updateSql = 'UPDATE `projects` SET `project_status`="' . $project_status_id . '" ' . $permissionUpdate . ' WHERE id="' . $project_id . '"';
		$mysql->sqlordie($updateSql);
		$insertSql = 'INSERT INTO `project_status` (`project_id`, `status_id`, `created_user`, `created_date`) VALUES ("' . $project_id . '", "' . $project_status_id . '", "' . $userID . '", NOW())';
		$mysql->sqlordie($insertSql);
		$html = '<button class="status status_' . $class_name . '" onclick="return false;"><span>' . $projectStatusRow['name'] . '</span></button>';
	}else{
		$html = '<button class="status status_' . $class_name . '" onclick="return false;"><span>' . $projectStatusRow['name'] . '</span></button>';
	}
	echo $html;

?>