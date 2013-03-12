<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	$phases = @$_GET['phase'];
	$action = @$_GET['action'];
	$phaseKeys = array_keys($phases);
	$project = $mysql->real_escape_string(@$_GET['project_id']);
	$count = 0;
	$ids = '';
	if ($action == 'save') {
		for($i = 0; $i < sizeof($phaseKeys); $i++) {
			$phase = $mysql->real_escape_string($phases[$phaseKeys[$i]]['id']);
			$start_part = explode("/", $phases[$phaseKeys[$i]]['start']);
			$end_part = explode("/", $phases[$phaseKeys[$i]]['end']);
			$start = $mysql->real_escape_string(@$start_part[2] ."-" .@$start_part[0] ."-" .@$start_part[1]);
			$end = $mysql->real_escape_string(@$end_part[2] ."-" .@$end_part[0] ."-" .@$end_part[1]);
			
			$select_entry = "SELECT * FROM `project_phases` WHERE `project_id`='$project' AND `phase_type`='$phase' order by id DESC LIMIT 1";
			$entry_result = $mysql->sqlordie($select_entry);
			if($entry_result->num_rows == 1) {
				$select_entry_more = "SELECT id FROM `project_phases` WHERE `project_id`='$project' AND `phase_type`='$phase'";
				$entry_result_more = $mysql->sqlordie($select_entry_more);
				$user_id = $_SESSION['user_id'];
				$row = $entry_result->fetch_assoc();
				$start_date = date("Y-m-d", strtotime($row['start_date']));
				
				if ($row['start_date'] == '0000-00-00 00:00:00') $start_date = '--';
				$end_date = date("Y-m-d", strtotime($row['projected_end_date']));
				
				if ($row['projected_end_date'] == '0000-00-00 00:00:00') $end_date = '--';
				$now_date = date("Y/m/d");
				
				if (((($start_date != trim($start) && $start_date != '--') && $start != '--') ||($end_date != trim($end) && $end_date != '--' && $end != '--')) || ($entry_result_more->num_rows == 1 && (($start_date != '--' && $start_date != trim($start)) || ($end_date != '--' && $end_date != trim($end)))) || ($entry_result_more->num_rows > 1 && (($start_date != trim($start)) || ($end_date != trim($end))))) {
				  $count++;
				 
				 /* $insert_timeline_history = "INSERT INTO `project_timeline_history` "
					."(`project_id`,`user_id`,`start_date`,`end_date`,`phase_id`,`modified_date`) "
					."VALUES "."('$project','$user_id','$start_date','$end_date','$phase','$now_date')";
				  $mysql->sqlordie($insert_timeline_history);*/
				
					#Except the most recent phase entry, updating other entries as not `active`
					$update_active_deleted = "UPDATE `project_phases` SET"
					."`active`='0',"
					."`deleted`='1' "
					."WHERE `project_id`='$project' AND `phase_type`='$phase'";
					$mysql->sqlordie($update_active_deleted);
				  
				  #Inserting for every updation in timeline for history purpose.
				  $insert_timeline = "INSERT INTO `project_phases` "
					."(`project_id`,`phase_type`,`start_date`,`projected_end_date`,`updated_by`,`updated_on`) "
					."VALUES "
					."('$project','$phase','$start','$end','$user_id','$now_date')";
				  $mysql->sqlordie($insert_timeline);
				  $timeline_modified_ids = "SELECT id FROM `project_phases` ORDER BY id DESC LIMIT 1";
				  $ids_result = $mysql->sqlordie($timeline_modified_ids);
				  $idsRow = $ids_result->fetch_assoc();
				  $ids .= ','.$idsRow['id'];
				}else {
					#Updating the empty data for existing projects
					$update_timeline = "UPDATE `project_phases` SET"
					."`start_date`='$start',"
					."`projected_end_date`='$end' "
					."WHERE `id`='" .$row['id'] ."'";
					$mysql->sqlordie($update_timeline);
				}
			} else if ($entry_result->num_rows == 0 && $start != '--' && $end != '--') {
				#Inserting timeline for first time entry
				$insert_timeline = "INSERT INTO `project_phases` "
					."(`project_id`,`phase_type`,`start_date`,`projected_end_date`) "
					."VALUES "
					."('$project','$phase','$start','$end')";
				$mysql->sqlordie($insert_timeline);
			}
		}
	} /*if save end*/

	else if ($action == 'delete') {
		for($i = 0; $i < sizeof($phaseKeys); $i++) {
			$phase = $mysql->real_escape_string($phases[$phaseKeys[$i]]['id']);
			$delete_timeline = "DELETE FROM `project_phases` WHERE project_id=".$project." AND phase_type=".$phase;
			$mysql->sqlordie($delete_timeline);
		}
	}

	if ($ids != '') {
		echo $ids;
	}
	
	if(@$_GET['complete'] == 1) {
		$complete_status = 2;
	} else {
		$complete_status = 1;
	}
	$section = explode("_", $mysql->real_escape_string(@$_GET['section']));
	
	$check_complete = "SELECT * FROM `project_brief_sections` WHERE `project_id`='" 
		.$project ."' AND `section_type`='" .$section[1] ."' LIMIT 1";
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
			."('$project','" .$section[1]  ."','$complete_status')";
		@$mysql->sqlordie($insert);
	}
?>