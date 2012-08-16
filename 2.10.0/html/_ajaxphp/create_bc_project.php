<?PHP
	session_start();
	include("sessionHandler.php");
	$comp = explode("_", @$_GET['comp']);
	
	$name = @$_GET['code'] ." - " .@$_GET['name'];
	if($comp[0] != 2) {
		$data = "new_project_name=$name&who_access=1&client_id=".$comp[1]."&grant_access_to_all_employees=on";
	} else {
		$data = "new_project_name=$name&who_access=1&grant_access_to_all_employees=on";
	}
	//defetc#3778
    //error_reporting(E_ALL);
	
    $user = BASECAMP_USERNAME;
    $password = BASECAMP_PASSWORD;
	$cookieFile = BASECAMP_COOKIE;
	
	$loginFields = "user_name=$user&password=$password";
	
	$LOGINURL = BASECAMP_HOST."/login/authenticate";
	$GETURL   = BASECAMP_HOST."/projects";
	$POSTFIELDS = "user_name=$user&password=$password";
	
	$RETURNEDURL = "";
	
	function AUTH_SITE_COOKIE_STORE($LOGINURL,$POSTFIELDS)
	{
	    $parseURL = parse_url($LOGINURL);
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_COOKIEJAR, "$parseURL[host].cookie");
	    curl_setopt($ch, CURLOPT_URL,"$LOGINURL");
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, "$POSTFIELDS");

	    ob_start();
	    curl_exec ($ch);
	    ob_end_clean();

	    curl_close ($ch);
		
	    return "$parseURL[host].cookie";
	}
	
	function AUTH_SITE_REQUEST($GETURL,$cookieFile) {
	    $parseURL = parse_url($GETURL);

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	    curl_setopt($ch, CURLOPT_COOKIEFILE, "$cookieFile");
	    curl_setopt($ch, CURLOPT_URL,"$GETURL");
	    $result = curl_exec ($ch);
	    curl_close ($ch);
	   
	    $fp = fopen ("$parseURL[host].html", "w");
	    fwrite($fp,$result);
	    fclose ($fp);

	    return $result;
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
	
//	$cookieFile = AUTH_SITE_COOKIE_STORE($LOGINURL,$POSTFIELDS);
    //Defect#3778
	$url = @explode("/projects/", SUBMIT_FORM($GETURL, $cookieFile, $data));
	$num = @explode("\"",$url[1]);
	//echo $num[0];
	//AUTH_SITE_GET($GETURL,$cookieFile);
	//Get bc id and category id for project from base camp
	
	//$query = "INSERT INTO `projects` (`bc_id`,`project_name`,`project_code`,`company`) VALUES('" .$num[0] ."','" .@$mysql->real_escape_string($_REQUEST['name']) ."','" .@$mysql->real_escape_string($_REQUEST['code']) ."','" .$comp[0] ."')";
	//$result = $mysql->query($query);
	//echo $mysql->insert_id;
?>