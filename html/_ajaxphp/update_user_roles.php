<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql;

	$projectID = $mysql->real_escape_string(@$_GET['project_id']);
		
	switch(@$_GET['action']) {
		case 'save': {
			$roles = @$_GET['roles'];
			$rolKeys = array_keys($roles);
			
			for($i = 0; $i < sizeof($rolKeys); $i++) {
				$value = explode('-', $mysql->real_escape_string($roles[$rolKeys[$i]]['sub_phase_type']));
				$userID = $mysql->real_escape_string($roles[$rolKeys[$i]]['user']);
//				if($roles[$rolKeys[$i]]['sub_phase_type'] > 0){
					$query = "SELECT * FROM `user_project_role` WHERE `project_id` = '" . $projectID . "' AND " . "`user_id`='" . $userID ."' LIMIT 1";
					$result = $mysql->sqlordie($query);

					if($result->num_rows == 1) {
						$row = $result->fetch_assoc();
						if($value[0] == '0'){
							$deleteRole = "DELETE FROM `user_project_role` WHERE `project_id`='" .$projectID ."' AND `user_id`='" .$userID ."'";
							$mysql->sqlordie($deleteRole);
						}else{
							$update_role = "UPDATE `user_project_role` SET `phase_subphase_id`='" . $value[0] . "', flag='" . $value[1] . "' WHERE " . "`project_id`='" . $projectID ."' AND `user_id`='" . $userID ."'";
							$mysql->sqlordie($update_role);
						}
					} else if($result->num_rows == 0 && $value[0] > 0) {
						$insert_sql = "INSERT INTO `user_project_role` (`project_id`, `user_id`, `phase_subphase_id`, `flag`, `creation_date`) VALUES ('" . $projectID . "','" . $userID ."','" . $value[0] ."', '" . $value[1] . "', NOW())";
							
						$mysql->sqlordie($insert_sql);
					}
//				}
			
			}
			break;	
		}

	}
?>