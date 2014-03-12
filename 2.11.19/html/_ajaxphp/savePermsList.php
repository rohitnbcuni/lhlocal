<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	$user = $_SESSION['lh_username'];
	$password = $_SESSION['lh_password'];
	
	//$_POST = $_GET;
	$companyPerms = $mysql->real_escape_string($_POST['companyPerms']);
	$users = $mysql->real_escape_string($_POST['control_3']);
	$projectId = $mysql->real_escape_string($_POST['projectId']);
	if(isset($_POST['edit_0'])) {
	$delete_query = "DELETE FROM `user_project_permissions` WHERE `project_id`='$projectId'";
	$mysql->sqlordie($delete_query);
	}
	if(sizeof($users) > 0) {		
		for($i = 0; $i < sizeof($users); $i++) {
			//$get_perms = "SELECT * FROM `user_project_permissions` WHERE `user_id`='" .$mysql->real_escape_string($users[$i]) ."' AND `project_id`='$projectId'";
			//$res = $mysql->query($get_perms);
			
			//if($res->num_rows == 0) {
				$update_perms = "INSERT INTO `user_project_permissions` (`user_id`,`project_id`,`active`) VALUES";
				$update_perms .= "('" .$mysql->real_escape_string($users[$i]) ."','$projectId','1')";
				
				//echo $update_perms;
				$mysql->sqlordie($update_perms);
				//echo $mysql->error;
			//}
			//$delete_query .= "`user_id`!='" .$mysql->real_escape_string($users[$i]) ."'";
			
			//$delete_query .= " AND ";
		}
	}
	//echo $delete_query." `project_id`='$projectId'";
	//$mysql->query($delete_query." `project_id`='$projectId'");
	//echo $mysql->error;
	
	$post_keys = array_keys($_POST);
	//print_r($post_keys);
	for($k = 0; $k < sizeof($post_keys); $k++) {
		$split_test = explode("_",$post_keys[$k]);
		if($split_test[0] == "edit") {
			$users = $_POST[$post_keys[$k]];
			if(sizeof($users) > 0) {
				for($i = 0; $i < sizeof($users); $i++) {
					$update_perms = "INSERT INTO `user_project_permissions` (`user_id`,`project_id`,`active`) VALUES";
					$update_perms .= "('" .$users[$i] ."','$projectId','1')";
				
					//echo $update_perms;
					$mysql->sqlordie($update_perms);
					//echo $mysql->error;
				}
			}
		}
	}
	
	$projectBc = "SELECT * FROM `projects` WHERE `id`='$projectId' LIMIT 1";
	$bcRes = $mysql->sqlordie($projectBc);
	$bcRow = $bcRes->fetch_assoc();
	
	$companyBc = "SELECT * FROM `companies` WHERE `id`='$companyPerms' LIMIT 1";
	$bcComp = $mysql->sqlordie($companyBc);
	$bcCompRow = $bcComp->fetch_assoc();
	
	/*$LOGINURL = BASECAMP_HOST."/login/authenticate";
	echo $GETURL   = BASECAMP_HOST."/projects/" .$bcRow['bc_id'] ."/involvements";
	$LOGINFIELDS = "user_name=resourceplanner@nbcuxd.com&password=r3s0urc3";
	echo $POSTFIELDS = "company[id]=" .$bcCompRow['bc_id'] 
	."&company[grant_access_to_all_employees]=1&return_to=/projects/" 
	.$bcRow['bc_id'] ."/participants&commit=Add company";
	
	$RETURNEDURL = "";*/
	
	function AUTH_SITE_COOKIE_STORE($LOGINURL,$LOGINFIELDS)
	{
	    $parseURL = parse_url($LOGINURL);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_COOKIEJAR, "$parseURL[host].cookie");
	    curl_setopt($ch, CURLOPT_URL,"$LOGINURL");
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, "$LOGINFIELDS");

	    ob_start();
	   echo curl_exec ($ch);
	    ob_end_clean();

	    curl_close ($ch);
		
	    return "$parseURL[host].cookie";
	}
	function SUBMIT_FORM($GETURL, $cookieFile, $POSTFIELDS) {
		$parseURL = parse_url($GETURL);
		
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_COOKIEFILE, "$cookieFile");
		curl_setopt($ch, CURLOPT_URL,"$GETURL");
		curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, "$POSTFIELDS");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		ob_start();
	    $result = curl_exec ($ch);
	    ob_end_clean();
		$info = curl_getinfo($ch);
	    curl_close ($ch);

	    return $result;
	}
	
	//$cookieFile = AUTH_SITE_COOKIE_STORE($LOGINURL,$LOGINFIELDS);
	//$url = SUBMIT_FORM($GETURL,$cookieFile,$POSTFIELDS);
	
	//$mysql->close();
?>
