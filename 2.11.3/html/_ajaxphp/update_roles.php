<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;	
	switch(@$_GET['action']) {
		case 'entry': {
			switch(@$_GET['data']) {
				case 'phone': {
					$query = "SELECT `phone_office` FROM  `users` WHERE `id` = '" .$mysql->real_escape_string(@$_GET['user']) ."'";
					$result = $mysql->sqlordie($query);
					
					$row = $result->fetch_assoc();
					echo $row['phone_office'];
					break;
				}
				case 'email': {
					$query = "SELECT `email` FROM  `users` WHERE `id` = '" .$mysql->real_escape_string(@$_GET['user']) ."'";
					$result = $mysql->sqlordie($query);
					
					$row = $result->fetch_assoc();
					echo $row['email'];
					break;
				}
				case 'name': {
					$query = "SELECT `first_name`, `last_name` FROM  `users` WHERE `id` = '" .$mysql->real_escape_string(@$_GET['user']) ."'";
					$result = $mysql->sqlordie($query);
					
					$row = $result->fetch_assoc();
					echo $row['first_name'] .' ' .$row['last_name'];
					break;
				}
				case 'title': {
					$query = "SELECT `title` FROM  `users` WHERE `id` = '" .$mysql->real_escape_string(@$_GET['user']) ."'";
					$result = $mysql->sqlordie($query);
					
					$row = $result->fetch_assoc();
					echo $row['title'];
					break;
				}
			}
			break;
		}
		case 'save': {
			$roles = @$_GET['roles'];
			$rolKeys = array_keys($roles);
			
			for($i = 0; $i < sizeof($rolKeys); $i++) {
				
				$query = "SELECT * FROM `project_roles` WHERE `project_id` = '" .$mysql->real_escape_string(@$_GET['project_id']) ."' AND "
				."`resource_type_id`='" .$mysql->real_escape_string($roles[$rolKeys[$i]]['resource_type']) ."' LIMIT 1";
				$result = $mysql->sqlordie($query);
				
				if($result->num_rows == 1) {
					$row = $result->fetch_assoc();
					//if(!empty($roles[$rolKeys[$i]]['user'])) {
						$update_role = "UPDATE `project_roles` SET `user_id`='" .$mysql->real_escape_string($roles[$rolKeys[$i]]['user']) 
							."',`phone`='" .$mysql->real_escape_string($roles[$rolKeys[$i]]['phone']) 
							."',`email`='" .$mysql->real_escape_string($roles[$rolKeys[$i]]['email']) ."' WHERE "
							."`project_id`='" .$mysql->real_escape_string(@$_GET['project_id']) ."' AND `resource_type_id`='" 
							.$mysql->real_escape_string($roles[$rolKeys[$i]]['resource_type']) ."'";
						$mysql->sqlordie($update_role);
					//} else {
					//	$delete_row = "DELETE FROM `project_roles` WHERE `id`='" .$row['id'] ."'";
					//	mysql_query($delete_row);
					//}
				} else if($result->num_rows == 0) {
					$insert_role = "INSERT INTO `project_roles` "
						."(`project_id`,`resource_type_id`,`user_id`,`email`,`phone`) "
						."VALUES "
						."('" .$mysql->real_escape_string(@$_GET['project_id']) ."','" .$mysql->real_escape_string($roles[$rolKeys[$i]]['resource_type']) ."','" 
						.$mysql->real_escape_string($roles[$rolKeys[$i]]['user']) ."','" .$mysql->real_escape_string($roles[$rolKeys[$i]]['email']) ."','" 
						.$mysql->real_escape_string($roles[$rolKeys[$i]]['phone']) ."')";
					$mysql->sqlordie($insert_role);
				}
			}
			
			if(@$_GET['complete'] == 1) {
				$complete_status = 2;
			} else {
				$complete_status = 1;
			}
			$section = explode("_", @$_GET['section']);
			
			$check_complete = "SELECT * FROM `project_brief_sections` WHERE `project_id`='" 
				.$mysql->real_escape_string(@$_GET['project_id']) ."' AND `section_type`='" .$mysql->real_escape_string($section[1]) ."' LIMIT 1";
			$complete_res = $mysql->sqlordie($check_complete);
			
			if($complete_res->num_rows == 1) {
				$row = $complete_res->fetch_assoc();
				if($complete_status > 1) {
					if(!isset($_GET['status'])) {
						if($row['flag'] == 3) {
							$flag = 3;
						} else {
							$flag = $complete_status;
						}
					} else {
						$flag = 2;
					}
					$update = "UPDATE `project_brief_sections` set `flag`='$flag' WHERE `id`='" .$row['id'] ."'";
					@$mysql->sqlordie($update);
				} else {
					$update = "UPDATE `project_brief_sections` set `flag`='1' WHERE `id`='" .$row['id'] ."'";
					@$mysql->sqlordie($update);
				}
			} else if($complete_res->num_rows == 0) {
				$insert = "INSERT INTO `project_brief_sections` "
					."(`project_id`,`section_type`,`flag`) "
					."VALUES "
					."('" .$mysql->real_escape_string(@$_GET['project_id']) ."','" .$mysql->real_escape_string($section[1])  ."','$complete_status')";
				@$mysql->sqlordie($insert);
			}
			break;
		}
		case 'delete': {
			$roles = array();
			$rolKeys = array();
			$roles = @$_GET['roles'];
			$rolKeys = array_keys($roles);
			
			for($i = 0; $i < sizeof($rolKeys); $i++) {
				$query = "SELECT * FROM `project_roles` WHERE `project_id` = '" .$mysql->real_escape_string(@$_GET['project_id']) ."' AND "
				."`resource_type_id`='" .$mysql->real_escape_string($roles[$rolKeys[$i]]['resource_type']) ."' LIMIT 1";
				$result = $mysql->sqlordie($query);
				
				if($result->num_rows == 1) {
					$row = $result->fetch_assoc();
					$delete_row = "DELETE FROM `project_roles` WHERE `id`='" .$row['id'] ."'";
					$mysql->sqlordie($delete_row);
				}
			}
			break;
		}
		/*default: {
			$project_id = $_GET['project_id'];
			$roles = $_GET['roles'];
			$keys = array();
			
			$keys = array_keys($roles);
			
			$delete_query = "DELETE FROM `ct_roles` WHERE `project_id` = '$project_id'";
			mysql_query($delete_query);
			
			for($i = 0; $i < sizeof($keys); $i++) {
				$resource_type = $roles[$keys[$i]]['resource_type'];
				$email = $roles[$keys[$i]]['email'];
				$phone = $roles[$keys[$i]]['phone'];
				$user = $roles[$keys[$i]]['user'];
				
				$query = "INSERT INTO `ct_roles` "
					."(`project_id`,`resource_type_id`,`user_id`,`email`,`phone`,`sort_order`) "
					."VALUES "
					."('$project_id','$resource_type','$user','$email','$phone','" .($i+1) ."')";
				$result = mysql_query($query);
			}
			break;
		}*/
	}
?>