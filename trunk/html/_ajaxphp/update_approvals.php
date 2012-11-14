<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	$project = $mysql->real_escape_string(@$_GET['project_id']);
	
	$appr = @$_GET['appr'];
	
	switch(@$_GET['action']) {
		case 'save': {
			$appr_keys = array_keys($appr);
			for($i = 0; $i < sizeof($appr_keys); $i++) {
				$appr_date_part = explode("/", $appr[$appr_keys[$i]]['date']);
				$appr_date = @$appr_date_part[2] ."-" .@$appr_date_part[0] ."-" .@$appr_date_part[1];
				if($appr[$appr_keys[$i]]['approved'] == "yes") {
					$approved = 1;
				} else {
					$approved = 0;
				}
				if($appr[$appr_keys[$i]]['phase'] == "nbcux" || $appr[$appr_keys[$i]]['phase'] == "client") {
					switch($appr[$appr_keys[$i]]['phase']) {
						case 'nbcux': {
							$select_non_phase_nbc = "SELECT * FROM `project_phase_approvals` WHERE `project_id`='$project' AND `non_phase`='nbcuxd'";
							$non_phase_result_nbc = $mysql->sqlordie($select_non_phase_nbc);
							
							if($non_phase_result_nbc->num_rows == 1) {
								$row = $non_phase_result_nbc->fetch_assoc();
								$update_uxd = "UPDATE `project_phase_approvals` SET "
									."`name`='" .$appr[$appr_keys[$i]]['name'] ."',`title`='" 
									.$appr[$appr_keys[$i]]['title'] ."',`phone`='" .$mysql->real_escape_string($appr[$appr_keys[$i]]['phone'])
									."',`approval_date`='$appr_date',`approved`='$approved' "
									."WHERE `id`='" .$row['id'] ."'";
								@$mysql->sqlordie($update_uxd);
							} else if(mysql_num_rows($non_phase_result_nbc) == 0) {
								$insert_uxd = "INSERT INTO `project_phase_approvals` "
									."(`project_id`,`name`,`title`,`phone`,`approval_date`,`approved`,`non_phase`) "
									."VALUES "
									."('$project','" .$mysql->real_escape_string($appr[$appr_keys[$i]]['name']) ."','" 
									.$mysql->real_escape_string($appr[$appr_keys[$i]]['title']) ."','" 
									.$mysql->real_escape_string($appr[$appr_keys[$i]]['phone']) ."','$appr_date','$approved','nbcuxd')";
								@$mysql->sqlordie($insert_uxd);
							}
							
							break;
						}
						case 'client': {
							$select_non_phase_cli = "SELECT * FROM `project_phase_approvals` WHERE `project_id`='$project' AND `non_phase`='client'";
							$non_phase_result_cli = $mysql->sqlordie($select_non_phase_cli);
							
							if($non_phase_result_cli->num_rows == 1) {
								$row = $non_phase_result_cli->fetch_assoc();
								$update_cli = "UPDATE `project_phase_approvals` SET "
									."`name`='" .$mysql->real_escape_string($appr[$appr_keys[$i]]['name']) ."',`title`='" 
									.$mysql->real_escape_string($appr[$appr_keys[$i]]['title']) ."',`phone`='" 
									.$mysql->real_escape_string($appr[$appr_keys[$i]]['phone']) 
									."',`approval_date`='$appr_date',`approved`='$approved' "
									."WHERE `id`='" .$row['id'] ."'";
								@$mysql->sqlordie($update_cli);
							} else if($non_phase_result_cli->num_rows == 0) {
								$insert_cli = "INSERT INTO `project_phase_approvals` "
									."(`project_id`,`name`,`title`,`phone`,`approval_date`,`approved`,`non_phase`) "
									."VALUES "
									."('$project','" .$mysql->real_escape_string($appr[$appr_keys[$i]]['name']) ."','" 
									.$mysql->real_escape_string($appr[$appr_keys[$i]]['title']) ."','" 
									.$mysql->real_escape_string($appr[$appr_keys[$i]]['phone']) ."','$appr_date','$approved','client')";
								@$mysql->sqlordie($insert_cli);
							}
							
							break;
						}
					}
				} else {
					echo $select_phase = "SELECT * FROM `project_phase_approvals` WHERE `project_id`='$project' AND `project_phase`='" .$mysql->real_escape_string($appr[$appr_keys[$i]]['phase']) ."'";
					$phase_result = $mysql->sqlordie($select_phase);
					
					if($phase_result->num_rows == 1) {
						$row = $phase_result->fetch_assoc();
						echo $update_phase = "UPDATE `project_phase_approvals` SET "
							."`name`='" .$mysql->real_escape_string($appr[$appr_keys[$i]]['name']) ."',`title`='" 
							.$mysql->real_escape_string($appr[$appr_keys[$i]]['title']) ."',`phone`='" 
							.$mysql->real_escape_string($appr[$appr_keys[$i]]['phone'])
							."',`approval_date`='$appr_date',`approved`='$approved' "
							."WHERE `id`='" .$row['id'] ."'";
						@$mysql->sqlordie($update_phase);
					} else if($phase_result->num_rows == 0) {
						echo $insert_phase = "INSERT INTO `project_phase_approvals` "
							."(`project_id`,`name`,`title`,`phone`,`approval_date`,`approved`,`project_phase`) "
							."VALUES "
							."('$project','" .$mysql->real_escape_string($appr[$appr_keys[$i]]['name']) ."','" 
							.$mysql->real_escape_string($appr[$appr_keys[$i]]['title']) ."','" 
							.$mysql->real_escape_string($appr[$appr_keys[$i]]['phone']) ."','$appr_date','$approved','" 
							.$mysql->real_escape_string($appr[$appr_keys[$i]]['phase']) ."')";
						@$mysql->sqlordie($insert_phase);
					}
				}
			}
			if(@$_GET['complete'] == 1) {
				$complete_status = 2;
			} else {
				$complete_status = 1;
			}
			$section = explode("_", @$_GET['section']);
			
			$check_complete = "SELECT * FROM `project_brief_sections` WHERE `project_id`='" 
				.$project ."' AND `section_type`='" .$mysql->real_escape_string($section[1]) ."' LIMIT 1";
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
					."('$project','" .$mysql->real_escape_string($section[1])  ."','$complete_status')";
				@$mysql->sqlordie($insert);
			}
			break;
		}
		case 'delete': {
			$appr_keys = array_keys($appr);
			for($i = 0; $i < sizeof($appr_keys); $i++) {
				$delete_phase = "DELETE FROM `project_phase_approvals` WHERE `project_id`='$project' AND `project_phase`='" .$mysql->real_escape_string($appr[$appr_keys[$i]]['phase']) ."'";
				@$mysql->sqlordie($delete_phase);
			}
			break;
		}
	}
?>