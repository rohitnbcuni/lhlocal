<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	switch(@$_REQUEST['data_set']) {
		case 'project_code': {
			//$link = mysql_connect('localhost', 'generic', 'generic');
			//mysql_select_db('nbc_lighthouse', $link);
			$query = "SELECT * FROM `projects` WHERE `company` = '" .$mysql->real_escape_string(@$_REQUEST['comp_id']) ."' ORDER BY `project_code` DESC LIMIT 1";
			$result = $mysql->sqlordie($query);
			$data = $result->fetch_assoc();
			
			//echo "testcode001:" .@$_REQUEST['comp_id'] .":" .mysql_num_rows($result);
			
			$code = $mysql->real_escape_string($data['project_code']);
			
			if(is_numeric(substr($code,-1))) {
				$return_code = new_num($code);
			} else {
				$return_code = $code ."1";
			}
			
			echo $return_code;
			$mysql->close();
			
			break;
		}
		case 'project_description': {
			$id = $mysql->real_escape_string(@$_REQUEST['comp_id']);
			$desc = $mysql->real_escape_string(@$_REQUEST['desc']);
			$scope = $mysql->real_escape_string(@$_REQUEST['scope']);
			$project_charter = $mysql->real_escape_string(@$_REQUEST['project_charter']);
			$user_id = $mysql->real_escape_string(@$_REQUEST['user_id']);
			$note = $mysql->real_escape_string(@$_REQUEST['note']);
			$status_id = $mysql->real_escape_string(@$_REQUEST['status_id']);
			$section = explode("_", $mysql->real_escape_string(@$_REQUEST['section']));
			$bool = 0;			
			$project_program = $mysql->real_escape_string(@$_REQUEST['project_program']);
			if($status_id != '' && $user_id != '' && $id != ''){
					$project_sql = 'SELECT IFNULL(project_status, 0) AS status FROM projects WHERE id="' . $id . '"';
					$projectResult = $mysql->sqlordie($project_sql);
					$projectRow = $projectResult->fetch_assoc();
					
					$project_status_sql = 'SELECT * FROM lnk_project_status_types WHERE id="' . $status_id . '"';
					$projectStatusResult = $mysql->sqlordie($project_status_sql);
					$projectStatusRow = $projectStatusResult->fetch_assoc();

					if($status_id != $projectRow['status']){
						$permissionUpdate = "";
						if($status_id == "6"){
							$permissionUpdate = ",`rp_permission`='0',`wo_permission`='0'";
						}
						$updateSql = 'UPDATE `projects` SET `project_status`="' . $status_id . '" ' . $permissionUpdate . ' WHERE id="' . $id . '"';
						$mysql->sqlordie($updateSql);
						$insertSql = 'INSERT INTO `project_status` (`project_id`, `status_id`, `created_user`, `created_date`) VALUES ("' . $id . '", "' . $status_id . '", "' . $user_id . '", NOW())';
						$mysql->sqlordie($insertSql);
						$inserted = "SELECT id FROM `project_status` order by id DESC LIMIT 1";
						$inserted_id = $mysql->sqlordie($inserted)->fetch_assoc();	
					}
			 }
		    if($project_program != '' || $scope !='' || $project_charter != ''){
				$update_allocation_type = "UPDATE `projects` SET `program` = '$project_program', `project_scope` = '$scope', `project_charter` = '$project_charter' WHERE `id`='" .$id ."'";
				$mysql->sqlordie($update_allocation_type);
			}
			$query = "SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$id ."' AND `section_type`='" .$section[1] ."'";
			$result = $mysql->sqlordie($query);		
			
			if($result) {
				if($result->num_rows == 1) {
					$row = $result->fetch_assoc();
					if(!empty($desc)) {
						if(!isset($_REQUEST['status'])) {
							if($row['flag'] == 3) {
								$flag = 3;
							} else {
								$flag = 2;
							}
						} else {
							$flag = 2;
						}
						$update_desc = "UPDATE `project_brief_sections` SET "
							."`desc` = '$desc', `flag`='$flag' WHERE `id`='" .$row['id'] ."'";
						$mysql->sqlordie($update_desc);
						$bool = 1;
					} else {
						$update_desc = "UPDATE `project_brief_sections` SET "
							."`desc` = '$desc', `flag`='1' WHERE `id`='" .$row['id'] ."'";
						$mysql->sqlordie($update_desc);
						$bool = 1;
					}
				} else if(mysql_num_rows($result) == 0) {
					if(!empty($desc)) {
						$insert_desc = "INSERT INTO `project_brief_sections` "
							."(`project_id`,`desc`,`section_type`,`flag`) "
							."VALUES "
							."('$id','$desc','" .$section[1] ."','2')";
						$mysql->sqlordie($insert_desc);
						$bool = 1;
					} else {
						$insert_desc = "INSERT INTO `project_brief_sections` "
							."(`project_id`,`desc`,`section_type`,`flag`) "
							."VALUES "
							."('$id','$desc','" .$section[1] ."','1')";
						$mysql->sqlordie($insert_desc);
						$bool = 1;
					}
				}
			}
			
			echo $inserted_id['id'];
			break;
		}
		case 'project_bcase': {
			$id = $mysql->real_escape_string(@$_REQUEST['comp_id']);
			$desc = $mysql->real_escape_string(@$_REQUEST['desc']);
			$section = explode("_", $mysql->real_escape_string(@$_REQUEST['section']));
			$bool = 0;
			
			$query = "SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$id ."' AND `section_type`='" .$section[1] ."'";
			$result = $mysql->sqlordie($query);
			
			if($result) {
				if($result->num_rows == 1) {
					$row = $result->fetch_assoc();
					if(!empty($desc)) {
						if(!isset($_REQUEST['status'])) {
							if($row['flag'] == 3) {
								$flag = 3;
							} else {
								$flag = 2;
							}
						} else {
							$flag = 2;
						}
						$update_desc = "UPDATE `project_brief_sections` SET "
							."`desc` = '$desc', `flag`='$flag' WHERE `id`='" .$row['id'] ."'";
						$mysql->sqlordie($update_desc);
						$bool = 1;
					} else {
						$update_desc = "UPDATE `project_brief_sections` SET "
							."`desc` = '$desc', `flag`='1' WHERE `id`='" .$row['id'] ."'";
						$mysql->sqlordie($update_desc);
						$bool = 1;
					}
				} else if($result->num_rows == 0) {
					if(!empty($desc)) {
						$insert_desc = "INSERT INTO `project_brief_sections` "
							."(`project_id`,`desc`,`section_type`,`flag`) "
							."VALUES "
							."('$id','$desc','" .$section[1] ."','2')";
						$mysql->sqlordie($insert_desc);
						$bool = 1;
					} else {
						$insert_desc = "INSERT INTO `project_brief_sections` "
							."(`project_id`,`desc`,`section_type`,`flag`) "
							."VALUES "
							."('$id','$desc','" .$section[1] ."','1')";
						$mysql->sqlordie($insert_desc);
						$bool = 1;
					}
				}
			}
			
			echo $bool;
			break;
		}
		case 'project_metrics': {
			$id = $mysql->real_escape_string(@$_REQUEST['comp_id']);
			$desc = $mysql->real_escape_string(@$_REQUEST['desc']);
			$section = explode("_", $mysql->real_escape_string(@$_REQUEST['section']));
			$bool = 0;
			
			$query = "SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$id ."' AND `section_type`='" .$section[1] ."'";
			$result = $mysql->sqlordie($query);
			
			if($result) {
				if($result->num_rows == 1) {
					$row = $result->fetch_assoc();
					if(!empty($desc)) {
						if(!isset($_REQUEST['status'])) {
							if($row['flag'] == 3) {
								$flag = 3;
							} else {
								$flag = 2;
							}
						} else {
							$flag = 2;
						}
						$update_desc = "UPDATE `project_brief_sections` SET "
							."`desc` = '$desc', `flag`='$flag' WHERE `id`='" .$row['id'] ."'";
						$mysql->sqlordie($update_desc);
						$bool = 1;
					} else {
						$update_desc = "UPDATE `project_brief_sections` SET "
							."`desc` = '$desc', `flag`='1' WHERE `id`='" .$row['id'] ."'";
						$mysql->sqlordie($update_desc);
						$bool = 1;
					}
				} else if($result->num_rows == 0) {
					if(!empty($desc)) {
						$insert_desc = "INSERT INTO `project_brief_sections` "
							."(`project_id`,`desc`,`section_type`,`flag`) "
							."VALUES "
							."('$id','$desc','" .$section[1] ."','2')";
						$mysql->sqlordie($insert_desc);
						$bool = 1;
					} else {
						$insert_desc = "INSERT INTO `project_brief_sections` "
							."(`project_id`,`desc`,`section_type`,`flag`) "
							."VALUES "
							."('$id','$desc','" .$section[1] ."','1')";
						$mysql->sqlordie($insert_desc);
						$bool = 1;
					}
				}
			}
			
			echo $bool;
			break;
		}
		case 'project_deliverables': {
			$id = $mysql->real_escape_string(@$_REQUEST['comp_id']);
			$desc = $mysql->real_escape_string(@$_REQUEST['desc']);
			$section = explode("_", $mysql->real_escape_string(@$_REQUEST['section']));
			$bool = 0;
			
			$query = "SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$id ."' AND `section_type`='" .$section[1] ."'";
			$result = $mysql->sqlordie($query);
			
			if($result) {
				if($result->num_rows == 1) {
					$row = $result->fetch_assoc();
					if(!empty($desc)) {
						if(!isset($_REQUEST['status'])) {
							if($row['flag'] == 3) {
								$flag = 3;
							} else {
								$flag = 2;
							}
						} else {
							$flag = 2;
						}
						$update_desc = "UPDATE `project_brief_sections` SET "
							."`desc` = '$desc', `flag`='$flag' WHERE `id`='" .$row['id'] ."'";
						$mysql->sqlordie($update_desc);
						$bool = 1;
					} else {
						$update_desc = "UPDATE `project_brief_sections` SET "
							."`desc` = '$desc', `flag`='1' WHERE `id`='" .$row['id'] ."'";
						$mysql->sqlordie($update_desc);
						$bool = 1;
					}
				} else if($result->num_rows == 0) {
					if(!empty($desc)) {
						$insert_desc = "INSERT INTO `project_brief_sections` "
							."(`project_id`,`desc`,`section_type`,`flag`) "
							."VALUES "
							."('$id','$desc','" .$section[1] ."','2')";
						$mysql->sqlordie($insert_desc);
						$bool = 1;
					} else {
						$insert_desc = "INSERT INTO `project_brief_sections` "
							."(`project_id`,`desc`,`section_type`,`flag`) "
							."VALUES "
							."('$id','$desc','" .$section[1] ."','1')";
						$mysql->sqlordie($insert_desc);
						$bool = 1;
					}
				}
			}
			
			echo $bool;
			break;
		}
		case 'project_scope': {
			$id = $mysql->real_escape_string(@$_REQUEST['comp_id']);
			$desc = $mysql->real_escape_string(@$_REQUEST['desc']);
			$section = explode("_", $mysql->real_escape_string(@$_REQUEST['section']));
			$bool = 0;
			
			$query = "SELECT * FROM `project_brief_sections` WHERE `project_id`='" .$id ."' AND `section_type`='" .$section[1] ."'";
			$result = $mysql->sqlordie($query);
			
			if($result) {
				if($result->num_rows == 1) {
					$row = $result->fetch_assoc();
					if(!empty($desc)) {
						if(!isset($_REQUEST['status'])) {
							if($row['flag'] == 3) {
								$flag = 3;
							} else {
								$flag = 2;
							}
						} else {
							$flag = 2;
						}
						$update_desc = "UPDATE `project_brief_sections` SET "
							."`desc` = '$desc', `flag`='$flag' WHERE `id`='" .$row['id'] ."'";
						$mysql->sqlordie($update_desc);
						$bool = 1;
					} else {
						$update_desc = "UPDATE `project_brief_sections` SET "
							."`desc` = '$desc', `flag`='1' WHERE `id`='" .$row['id'] ."'";
						$mysql->sqlordie($update_desc);
						$bool = 1;
					}
				} else if($result->num_rows == 0) {
					if(!empty($desc)) {
						$insert_desc = "INSERT INTO `project_brief_sections` "
							."(`project_id`,`desc`,`section_type`,`flag`) "
							."VALUES "
							."('$id','$desc','" .$section[1] ."','2')";
						$mysql->sqlordie($insert_desc);
						$bool = 1;
					} else {
						$insert_desc = "INSERT INTO `project_brief_sections` "
							."(`project_id`,`desc`,`section_type`,`flag`) "
							."VALUES "
							."('$id','$desc','" .$section[1] ."','1')";
						$mysql->sqlordie($insert_desc);
						$bool = 1;
					}
				}
			}
			
			echo $bool;
			break;
		}
		case 'project_history_note': {
		  $id = $mysql->real_escape_string(@$_REQUEST['id']);
		  $project_id = $mysql->real_escape_string(@$_REQUEST['project_id']);
		  $user_id = $mysql->real_escape_string(@$_REQUEST['user_id']);
		  $note = $mysql->real_escape_string(@$_REQUEST['note']);
		  $status_id = $mysql->real_escape_string(@$_REQUEST['status_id']);
		  
		  $update_note = "UPDATE `project_status` SET `note` = '$note' WHERE `id`='" .$id ."'";
		  $mysql->sqlordie($update_note);
		  /*$project_sql = 'SELECT IFNULL(project_status, 0) AS status FROM projects WHERE id="' . $project_id . '"';
		  $projectResult = $mysql->sqlordie($project_sql);
		  $projectRow = $projectResult->fetch_assoc();
		
		  $project_status_sql = 'SELECT * FROM lnk_project_status_types WHERE id="' . $status_id . '"';
		  $projectStatusResult = $mysql->sqlordie($project_status_sql);
		  $projectStatusRow = $projectStatusResult->fetch_assoc();
		  $project_status_history = 'SELECT created_date FROM project_status WHERE status_id="' . $status_id . '" AND project_id="' . $project_id . '"';
		  $project_status_history_result = $mysql->sqlordie($project_status_history);
		  $projectStatusHistoryRow = $project_status_history_result->fetch_assoc();
	      $date = date("m/d/Y", strtotime($projectStatusHistoryRow['created_date']));

		  $user = 'SELECT user_name FROM users WHERE id="' . $user_id.'"';
		  $user_result = $mysql->sqlordie($user);
		  $userRow = $user_result->fetch_assoc();
		  $history = '<li><div>'.$projectStatusRow['name'].'</div><div><textarea readonly rows="2" cols="18">'.$note.'</textarea></div><div>Updated by '.$userRow['user_name'].' on '.$date.'</div></li>';
		  echo $history;*/
		  break;
		}
		case 'project_status': {
		  $project_status_id = $mysql->real_escape_string(@$_REQUEST['project_status_id']);
		  $project_id = $mysql->real_escape_string(@$_REQUEST['project_id']);
		  $user_id = $mysql->real_escape_string(@$_REQUEST['user_id']);
		  
		  $project_status_sql = 'SELECT name FROM lnk_project_status_types WHERE id="' . $project_status_id . '"';
		  $project_status = $mysql->sqlordie($project_status_sql);
		  $projectStatusRow = $project_status->fetch_assoc();
		  
		  $project_sql = 'SELECT IFNULL(project_status, 0) AS status FROM projects WHERE id="' . $project_id . '"';
		  $projectResult = $mysql->sqlordie($project_sql);
		  $projectRow = $projectResult->fetch_assoc();
		  
		  $class_name = str_replace(" ", "", strtolower($projectStatusRow['name']));
		  $attr = '';
		  if( $project_status_id != $projectRow['status'])
		     $attr = 'user_id="'.$user_id.'" status_id="'.$project_status_id.'"';
		  $html = '<button class="status status_' . $class_name . '" onclick="return false;" '.$attr.'><span>' . $projectStatusRow['name'] . '</span></button>';
		 
		  echo $html;
		  break;
		}
		case 'timeline_history_note': {
			$note = $mysql->real_escape_string(@$_REQUEST['note']);
			$ids = $mysql->real_escape_string(@$_REQUEST['ids']);
			$individual_ids = explode(',',$ids);
			foreach($individual_ids as $id) {
				if(!empty($id)) {
				  $update_note = "UPDATE `project_phases` SET `note` = '$note' WHERE `id`='" .$id ."'";
				  $mysql->sqlordie($update_note);
				}
			}
			break;
		}
		//case '': {
		//	break;
		//}
		default: {
			//$project = @$_REQUEST['project'];
	
			//$keys = array_keys($project);
			
			//for($i = 0; $i < sizeof($keys); $i++) {
			//	echo "Key: " .$keys[$i] ." - Data: " .$project[$keys[$i]] ."<br />";
			//}
			break;
		}
	}
	
	function new_num($code, $offset = -1) {
		//Use somthing like this to have trailing zeros
		if(is_numeric(substr($code,($offset-1))) && substr($code,($offset-1))) {
			return new_num($code, ($offset-1));
		}
		else {
			$r_code = substr($code,0,$offset);
			$inc = substr($code,$offset)+1;
			$per = $inc/100;
			if($per < .1) {
				$r_code .=  "00" .$inc;
			} elseif($per < 1) {
				$r_code .=  "0" .$inc;
			} else {
				$r_code .= $inc;
			}
			return $r_code;
		}
	}
?>