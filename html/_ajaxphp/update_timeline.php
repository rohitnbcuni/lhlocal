<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	$phases = @$_GET['phase'];
	$phaseKeys = array_keys($phases);
	$project = $mysql->real_escape_string(@$_GET['project_id']);
	
	for($i = 0; $i < sizeof($phaseKeys); $i++) {
		$phase = $mysql->real_escape_string($phases[$phaseKeys[$i]]['id']);
		$start_part = explode("/", $phases[$phaseKeys[$i]]['start']);
		$end_part = explode("/", $phases[$phaseKeys[$i]]['end']);
		$start = $mysql->real_escape_string(@$start_part[2] ."-" .@$start_part[0] ."-" .@$start_part[1]);
		$end = $mysql->real_escape_string(@$end_part[2] ."-" .@$end_part[0] ."-" .@$end_part[1]);
		
		$select_entry = "SELECT * FROM `project_phases` WHERE `project_id`='$project' AND `phase_type`='$phase' LIMIT 1";
		$entry_result = $mysql->sqlordie($select_entry);
		
		if($entry_result->num_rows == 1) {
			$row = $entry_result->fetch_assoc();
			$update_timeline = "UPDATE `project_phases` SET"
				."`start_date`='$start',"
				."`projected_end_date`='$end' "
				."WHERE `id`='" .$row['id'] ."'";
			$mysql->sqlordie($update_timeline);
		} else if ($entry_result->num_rows == 0) {
			$insert_timeline = "INSERT INTO `project_phases` "
				."(`project_id`,`phase_type`,`start_date`,`projected_end_date`) "
				."VALUES "
				."('$project','$phase','$start','$end')";
			$mysql->sqlordie($insert_timeline);
		}
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