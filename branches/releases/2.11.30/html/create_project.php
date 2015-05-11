<?PHP
	include("_inc/config.inc");
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$id = $mysql->real_escape_string($_POST['project_id']);
	
	//New data
	$_GET['comp'] = $mysql->real_escape_string($_POST['project_client']);
	$_GET['code'] = $mysql->real_escape_string($_POST['project_code']);
	$_GET['name'] = $mysql->real_escape_string($_POST['project_name']);

	include('_ajaxphp/create_bc_project.php');

	$client = $mysql->real_escape_string($_POST['project_client']);
	//$code = $mysql->real_escape_string($_POST['project_code']);
	//$name = $mysql->real_escape_string($_POST['project_name']);
         //Defect#3955#3954
	$code = $mysql->real_escape_string(strip_tags($_POST['project_code']));
	$name = $mysql->real_escape_string(strip_tags($_POST['project_name']));
	//End
	$_YEAR = $mysql->real_escape_string($_POST['project_year']);
	$client_part = explode("_", $client);
	
	$create_new_project_query = "INSERT INTO `projects` "
		."(`bc_id`,`project_code`,`project_name`,`company`, `project_status`,`YEAR`) "
		."VALUES "
		."('" .$num[0] ."','$code','$name','" .$client_part[0] ."', '1','".$_YEAR."')";
	
	$create_new_res = $mysql->sqlordie($create_new_project_query);
	$new_id = $mysql->insert_id;

	/************Project Status*************/
	$statusInsertSql = 'INSERT INTO `project_status` (`project_id`, `status_id`, `created_user`, `created_date`) VALUES ("' . $new_id . '", "1", "83", NOW())';
	$mysql->sqlordie($statusInsertSql);
	/************Create Perms***************/
	/*
	//Commenting out ths as the permissions are given from the admin tab
	$default_users = "SELECT * FROM `users` WHERE `company`='2' OR `company`='" .$client_part[0] ."'";
	$default_res = $mysql->query($default_users);
	$mysql->error;
	//echo $default_res->num_rows;
	if($default_res->num_rows > 0) {
		while($default_user_row = $default_res->fetch_assoc()) {
			$check_perms = "SELECT * FROM `user_project_permissions` WHERE "
				."`user_id`='" .$default_user_row['id'] ."' AND `project_id`='" .$new_id ."' LIMIT 1";
			$check_res = $mysql->query($check_perms);
			
			if($check_res->num_rows == 0) {
				$insert_perms = "INSERT INTO `user_project_permissions` (`user_id`,`project_id`) VALUES "
				."('" .$default_user_row['id'] ."','" .$new_id ."')";
				
				$mysql->query($insert_perms);
			}
		}
	}
	*/
	/***********************************/
	
	if(!empty($new_id)) {
		header("Location: /controltower/index/edit/?project_id=$new_id");
	} else {
		header("Location: /controltower/");
	}
?>
