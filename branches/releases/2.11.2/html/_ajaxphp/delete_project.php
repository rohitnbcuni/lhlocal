<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	if(isset($_SESSION['user_id'])) {
		$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		
		$project_id = $mysql->real_escape_string(@$_GET['project_id']);
		
		$set_delete_query = "UPDATE `projects` SET `deleted`='1' WHERE `id`='$project_id'";
		@$mysql->query($set_delete_query);
		
		$select_project = "SELECT * FROM `projects` WHERE `id`='$project_id'";
		$proj_res = $mysql->query($select_project);
		
		if($proj_res->num_rows > 0) {
			$proj = $proj_res->fetch_assoc();
			if($proj['deleted'] == 1 && !empty($proj['bc_id'])) {
//				$user = $_SESSION['lh_username'];
//			    $password = $_SESSION['lh_password'];
				$user = BASECAMP_USERNAME;
				$password = BASECAMP_PASSWORD;

				$LOGINURL = "https://nbcuxd.grouphub.com/login/authenticate";
				$GETURL   = "https://nbcuxd.grouphub.com/projects/" .$proj['bc_id'] ."/project/delete_project";
				$POSTFIELDS = "user_name=$user&password=$password";
				$data = "";
				
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
				function AUTH_SITE_GET($GETURL,$cookieFile) {
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
				$cookieFile = AUTH_SITE_COOKIE_STORE($LOGINURL,$POSTFIELDS);
				SUBMIT_FORM($GETURL,$cookieFile,$data);
				//AUTH_SITE_GET($GETURL,$cookieFile);
			}
		}
	}
?>