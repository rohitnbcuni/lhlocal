<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql; 
	
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
			$section = explode("_", $mysql->real_escape_string(@$_REQUEST['section']));
			$bool = 0;			
			$project_program = $mysql->real_escape_string(@$_REQUEST['project_program']);
		    if($project_program != ''){
				$update_allocation_type = "UPDATE `projects` SET `program` = '$project_program' WHERE `id`='" .$id ."'";
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
			
			echo $bool;
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