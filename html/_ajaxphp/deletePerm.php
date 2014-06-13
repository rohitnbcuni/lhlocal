<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$projectId = $mysql->real_escape_string($_POST['projectId']);
	$compId = $mysql->real_escape_string($_POST['compId']);
	$compPart = explode("_", $compId);
	
	$get_users = "SELECT * FROM `users` WHERE `company`='" .$compPart[1] ."'";
	$users_res = $mysql->sqlordie($get_users);
	
	if($users_res->num_rows > 0) {
		while($users_row = $users_res->fetch_assoc()) {
			$del_users = "DELETE FROM `user_project_permissions` WHERE "
				."`user_id`='" .$users_row['id'] ."' AND `project_id`='$projectId'";
			$mysql->sqlordie($del_users);
		}
	}
	
	//https://nbcuxd.grouphub.com/projects/1870128/involvements/1023800?return_to=/projects/1870128/participants
	
	$projectBc = "SELECT * FROM `projects` WHERE `id`='$projectId' LIMIT 1";
	$bcRes = $mysql->sqlordie($projectBc);
	$bcRow = $bcRes->fetch_assoc();
	
	$companyBc = "SELECT * FROM `companies` WHERE `id`='" .$compPart[1] ."' LIMIT 1";
	$bcComp = $mysql->sqlordie($companyBc);
	$bcCompRow = $bcComp->fetch_assoc();
	
	$LOGINURL = BASECAMP_HOST."/login/authenticate";
	$GETURL   = BASECAMP_HOST."/projects/" .$bcRow['bc_id'] ."/involvements/" .$bcCompRow['bc_id']
		."?return_to=/projects/" .$bcRow['bc_id'] ."/participants";
	$LOGINFIELDS = "user_name=resourceplanner@nbcuxd.com&password=r3s0urc3";
	$POSTFIELDS = "_method=delete";
	//."&company[grant_access_to_all_employees]=1&return_to=/projects/" 
	//.$bcRow['bc_id'] ."/participants&commit=Add company";
	
	$RETURNEDURL = "";
	
	function AUTH_SITE_COOKIE_STORE($LOGINURL,$LOGINFIELDS)
	{
	    $parseURL = parse_url($LOGINURL);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_COOKIEJAR, "$parseURL[host].cookie");
	    curl_setopt($ch, CURLOPT_URL,"$LOGINURL");
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, "$LOGINFIELDS");

	    ob_start();
	    curl_exec ($ch);
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
	//echo $url = SUBMIT_FORM($GETURL,$cookieFile,$POSTFIELDS);
	
	//$mysql->close();
?>