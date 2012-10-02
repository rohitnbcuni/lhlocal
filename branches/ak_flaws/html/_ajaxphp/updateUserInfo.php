<?PHP
	include('../_inc/config.inc');  
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql;  

	$userID = $mysql->real_escape_string(@$_REQUEST['user_id']);
	$userTitles = $_REQUEST['userTitle'];
	$userTitlesArray = explode(",",$userTitles);
	if(!empty($userID))
	{
		$update_role = "UPDATE `users` SET `user_title`='".$_REQUEST['userTitle']."',`role`='" . $_REQUEST['userRole']."',`agency`='". $_REQUEST['userVendorName'] ."',`program`='".$_REQUEST['userProgram']."',`active`='". $_REQUEST['userActiveStatus']."',`deleted`='". $_REQUEST['userDeletedStatus']."',`login_status`='". $_REQUEST['userAdminAccess']."',`user_access`='".$_REQUEST['user_access_bit']."' where `id`='" . $userID ."'";
		$mysql->sqlordie($update_role);
	}
	if($_REQUEST['isUserTitleChanged']=='Y'){
		$deleteQuery = "DELETE FROM `user_roles` WHERE user_id=".$userID;
		$mysql->sqlordie($deleteQuery );
		$insertQuery = "INSERT INTO `user_roles` (`user_id`, `category_subcategory_id`, `flag`, `creation_date`, `active`, `deleted`) VALUES ";
		for($i=0;$i<count($userTitlesArray);$i++){ 
			$pos = strpos($userTitlesArray[$i], "subcat_");
			if($pos === false){
				$insertQuery .= "('{$userID}', ".str_replace("subcat_","",$userTitlesArray[$i]).", 'category','".date("Y-m-d H:i:s")."', '1', '0')"; 
			}else{
				$insertQuery .= "('{$userID}', ".str_replace("subcat_","",$userTitlesArray[$i]).", 'subcategory','".date("Y-m-d H:i:s")."', '1', '0')"; 
			}
			if(count($userTitlesArray)>1 && count($userTitlesArray)-1!=$i){
				$insertQuery .= ","; 
			}   
		}
		$mysql->sqlordie($insertQuery );
	}
	if($_REQUEST['isUserProjectChanged'] == 'Y'){
		$newUserProjectArray = array();
		$oldProjectArray = array();
		$deleteArrayDiff = array();
		$userProjectStr = array();
		if($_POST['userStatus'] == "client") {
			//	$comp_query = " AND a.`company` = '".$_POST['user_company'] ."'";
				 $comp_query = "";

			} else {
				$comp_query = "";
			}
		$userExistingProject = "SELECT distinct UP.project_id FROM `user_project_permissions` UP INNER JOIN `projects` a  ON (a.`id`=UP.`project_id`) WHERE UP.`user_id`='" .$userID."' AND  a.`active` = '1' AND a.`deleted` = '0' AND `archived`='0' AND a.`wo_permission` = '1' $comp_query ORDER BY a.`project_code` ASC ";
		$result_wo = $mysql->sqlordie($userExistingProject);
		if($result_wo->num_rows > 0){
		while($comRow = $result_wo->fetch_assoc()) {
					//p($comRow);
					$oldProjectArray[] = $comRow['project_id'];
			}
		$countOldProjectArray = count($oldProjectArray);
		}else{
			$countOldProjectArray = 0;
		}
		$userProjectArray = $_REQUEST['userProjectArray'];
		$newUserProjectArray = @explode(",",$userProjectArray);
		if($newUserProjectArray[0] == ''){
			$countOldProjectArray = 0;
		}else{
			$countNewUserProjectArray = count($newUserProjectArray);
		}
		if($countOldProjectArray > $countNewUserProjectArray){
			$deleteArrayDiff = array_diff($oldProjectArray,$newUserProjectArray);
			foreach($deleteArrayDiff as $pKey => $projectId){
				$update_perms = "DELETE FROM `user_project_permissions` WHERE `project_id`='$projectId' AND `user_id` ='".$mysql->real_escape_string($userID)."'";
				$mysql->sqlordie($update_perms);
				}
			
		}
		if($countOldProjectArray < $countNewUserProjectArray){
			$deleteArrayDiff = array_diff($newUserProjectArray,$oldProjectArray);
			foreach($deleteArrayDiff as $pKey => $projectId){
				$update_perms = "INSERT INTO `user_project_permissions` (`user_id`,`project_id`,`active`) VALUES";
				$update_perms .= "('" .$mysql->real_escape_string($userID) ."','$projectId','1')";
				//echo $update_perms;
				$mysql->sqlordie($update_perms);
				}
		}
		
	}
 
?>
