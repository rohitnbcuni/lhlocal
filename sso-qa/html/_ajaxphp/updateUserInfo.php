<?PHP
	include('../_inc/config.inc');  
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);  
	$userID = $mysql->real_escape_string(@$_REQUEST['user_id']);
	$company = $mysql->real_escape_string($_REQUEST['user_company']);
	$userTitle = $mysql->real_escape_string($_REQUEST['userTitle']);
	$userVendorName = $mysql->real_escape_string($_REQUEST['userVendorName']);
	$userActiveStatus = $mysql->real_escape_string($_REQUEST['userActiveStatus']);
	$userDeletedStatus = $mysql->real_escape_string($_REQUEST['userDeletedStatus']);
	$userAdminAccess = $mysql->real_escape_string($_REQUEST['userAdminAccess']);
	$user_access_bit = $mysql->real_escape_string($_REQUEST['user_access_bit']);
	$userProgram = $mysql->real_escape_string($_REQUEST['userProgram']);
	$userRole = $mysql->real_escape_string($_REQUEST['userRole']);
	/*if(($company != '-1' )|| ($company == '') || ($company == '0')){
		$userCompany = $company;

	}*/
	$admin_id = $_SESSION['user_id'];
	$userTitles = $_REQUEST['userTitle'];
	$userTitlesArray = explode(",",$userTitles);
	if(!empty($userID))
	{
		/*$sql_users = "SELECT login_status from users id = '".$userID ."'";
		$user_status = $users$mysql->sqlordie($sql_users);
		 
		if($user_status->num_rows > 0){
			$row_user_status = $user_status->fetch_assoc());
			$pre_user_status = $user_status['login_status'];
			
			if($pre_user_status != $userAdminAccess){
				$admin_audit_log = "INSERT INTO `admin_audit_log` SET `uid` =".$admin_id."
				, `action_module` = 'userinfo', `action_id` ='$userID', 
				`action_name` = 'updated', dated ='".date('Y-m-d H:i:s')."' ";
				$mysql->sqlordie($admin_audit_log);
			
			
			}
		
		}*/		
		
		
		$update_role = "UPDATE `users` SET  `user_title`='".$userTitle."',`role`='" . $userRole."',
		`agency`='". $userVendorName ."',`program`='".$userProgram."',`active`='". $userActiveStatus."',
		`deleted`='". $userDeletedStatus."',`login_status`='". $userAdminAccess."',`user_access`='".$user_access_bit."' 
		where `id`='" . $userID ."'";
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
	$usersExistingCompany = array();
	$usersExistingCompany = getUserExistingCompanies($userID);
	$company = explode(",",$company);
	
	
	$user_info_audit_log = array();
	if(count($usersExistingCompany) > 0){
		//Check the differnce
		if(count($company) >1){
			foreach($company as $company_key => $new_company_id){
				if(!in_array($new_company_id,$usersExistingCompany)){
					$insertCompanyQuery = "INSERT INTO `users_companies` SET `user_id` =".$userID."
					, `company_id` = ".$new_company_id.", `deleted` ='0', 
					`modify_date` = '".date('Y-m-d H:i:s')."' ";
					
					$user_info_audit_log[] = addslashes($insertCompanyQuery);
					$mysql->sqlordie($insertCompanyQuery );
					
					
				}
			
			}
			
		
		}
	
	}else{//First time
		if(count($company) > 0){
			foreach($company as $company_key => $new_company_id){
				$insertCompanyQuery = "INSERT INTO `users_companies` SET `user_id` =".$userID."
				, `company_id` = ".$new_company_id.", `deleted` ='0', 
				`modify_date` = '".date('Y-m-d H:i:s')."' ";
				$user_info_audit_log[] = addslashes($insertCompanyQuery);
				$mysql->sqlordie($insertCompanyQuery );
			}
		
		}
	}
	//For removi g the company
	if(count($company) >0){
		if(count($usersExistingCompany) > 0){
			foreach($usersExistingCompany as $company_key => $new_company_id){
				if(!in_array($new_company_id,$company)){
					/*echo $uCompanyQuery = "UPDATE `users_companies` SET `deleted` ='1', 
					`modify_date` = '".date('Y-m-d H:i:s')."'  WHERE  `user_id` =".$userID."
					AND `company_id` = ".$new_company_id;
					$mysql->sqlordie($uCompanyQuery );*/
					$uCompanyQuery = "DELETE FROM `users_companies`  WHERE  `user_id` =".$userID."
					AND `company_id` = ".$new_company_id;
					$user_info_audit_log[] = addslashes($uCompanyQuery);
					$mysql->sqlordie($uCompanyQuery );
					
				}		
			
			}	
		
	
		}
	
	}
	if(count($user_info_audit_log) > 0){
		$serializeSyncQueries = serialize($user_info_audit_log);
		$admin_audit_log = "INSERT INTO `admin_audit_log` SET `uid` =".$admin_id."
						, `action_module` = 'userinfo', `action_id` ='$userID', 
						`action_name` = 'updated', dated ='".date('Y-m-d H:i:s')."', info1 = '$serializeSyncQueries' ";
						$mysql->sqlordie($admin_audit_log);
	}
	
	function getUserExistingCompanies($userID){
		global $mysql;
		$company_array = array();
		$query = "SELECT company_id from users_companies WHERE user_id=".$userID;
		$comm_result = $mysql->sqlordie($query );
		if($comm_result->num_rows > 0){
			while($comRow = $comm_result->fetch_assoc()){
				$company_array[] = $comRow['company_id'];
			
			}
		}
		return $company_array;
	
	}
	
 
?>
