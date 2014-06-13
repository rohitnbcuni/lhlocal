<?php

	include('../_inc/config.inc');
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//print('<pre>');
	exit;
	/*$sub_phase_list = "'Project Manager', 'Engagement Lead', 'PSX'";

	$new_phase_sql = "SELECT p.`id` AS 'phase_id', sp.`id` AS 'sub_phase_id' FROM `lnk_project_phase_types` p, `lnk_project_sub_phase_types` sp WHERE  sp.phase_id=p.id and sp.name in (".$sub_phase_list.")";
	$new_phase_result = $mysql->sqlordie($new_phase_sql);
	while($row = $new_phase_result->fetch_assoc()){
		//array[sub_phase_id] => phase_id
		$new_phase_details[$row['sub_phase_id']] = $row['phase_id'];
	}
	//print_r($new_phase_details);

	$project_list = "SELECT `id`, `project_id`, `phase`, `sub_phase`, `hours`, `rate` FROM `project_sub_phase_finance`
		WHERE `sub_phase` IN (SELECT `id` FROM `lnk_project_sub_phase_types` WHERE `name`
		IN (".$sub_phase_list.")) GROUP BY `project_id`, `sub_phase` ORDER BY `project_id`";

		//AND `project_id` IN ('17663', '17664')

	$prev_proj_id = '';
	$prev_proj_phase_id = array();
	$prev_proj_phase_hours = array();
	$update_sql = array();

	$result = $mysql->sqlordie($project_list);
	if($result->num_rows > 0){
		while($row = $result->fetch_assoc()){
			if(!empty($row['project_id'])){
				print('<br>NEW RECORD<br>');print_r($row);
				print("<br>OLD:".$prev_proj_id."\tNEW:".$row['project_id']);
				$new_phase_id = $new_phase_details[$row['sub_phase']];
				print('<br>New Phase ID : '. $new_phase_id . ' - Old Phase : ' . $row['phase']);
				if($row['project_id'] != $prev_proj_id && !empty($prev_proj_id)){
					//save the previous proj details
					foreach($prev_proj_phase_hours as $new_phase => $prev_phase_hour){
						print('<br>Prev Phase ID : ' . $prev_proj_phase_id[$new_phase]. '--- New Phase ID : ' . $new_phase);
						save_phase_data($mysql, $prev_proj_id, $prev_phase_hour, $prev_proj_phase_id[$new_phase], $new_phase, $update_sql[$new_phase], 'inside');
					}

					//reset the value for the new project
					$prev_proj_id = $row['project_id'];
					$prev_proj_phase_id = array();
					$prev_proj_phase_hours = array();
					$update_sql = array();
				}
				if(empty($prev_proj_id)){
					$prev_proj_id = $row['project_id'];
					$prev_proj_phase_id[$new_phase_id] = $row['phase'];
				}

				$prev_proj_phase_id[$new_phase_id] = $row['phase'];

				if(array_key_exists($new_phase_id, $prev_proj_phase_hours)){
					$prev_proj_phase_hours[$new_phase_id] += $row['hours'];
				}else{
					$prev_proj_phase_hours[$new_phase_id] = $row['hours'];
				}

				$update_sql[$new_phase_id][] = "UPDATE `project_sub_phase_finance` SET `phase`= ".$new_phase_id." WHERE `id`=".$row['id'];
			}
		}
		//print_r($prev_proj_phase_id);
		foreach($prev_proj_phase_hours as $new_phase => $prev_phase_hour){
			save_phase_data($mysql, $prev_proj_id, $prev_phase_hour, $prev_proj_phase_id[$new_phase], $new_phase, $update_sql[$new_phase], 'outside');
		}
	}


	function save_phase_data($mysql, $prev_proj_id, $prev_proj_phase_hours, $prev_proj_phase_id, $new_phase_id, $update_sql, $flag){

		if($prev_proj_phase_id == $new_phase_id){
			//The phase change is already done
			return;
		}

		//print("<br>In save_phase_data from ". $flag ." for Project :" . $prev_proj_id);
		$insert_phase_sql = "INSERT INTO `project_phase_finance` (`project_id`, `phase`, `hours`, `rate`, `creation_date`) VALUES (".$prev_proj_id.", ".$new_phase_id.", ".$prev_proj_phase_hours.", '0',now())";
		print("<br>INSERT => " . $insert_phase_sql);
		$mysql->sqlordie($insert_phase_sql);

		$sub_phase_present_sql = "SELECT count(1) as `record_count` FROM `project_sub_phase_finance` WHERE `project_id`=".$prev_proj_id." AND `phase`=".$prev_proj_phase_id;
		print("<br>COUNT => " . $sub_phase_present_sql);
		$sub_phase_present_result = $mysql->sqlordie($sub_phase_present_sql)->fetch_assoc();
		print("<br>PRESENT COUNT : " . $sub_phase_present_result['record_count']);

		if($sub_phase_present_result['record_count'] > count($update_sql)){
			$proj_phase_hours_sql = "SELECT `hours` FROM `project_phase_finance` WHERE `project_id`=".$prev_proj_id." AND `phase`=".$prev_proj_phase_id;
			print('<br>Hours SQL : ' . $proj_phase_hours_sql);
			$proj_phase_hours = $mysql->sqlordie($proj_phase_hours_sql)->fetch_assoc();
			//print('<br>Present Hours : ' . $proj_phase_hours['hours']);

			$update_phase_sql = "UPDATE `project_phase_finance` SET `hours`=".($proj_phase_hours['hours']-$prev_proj_phase_hours)." WHERE `project_id`=".$prev_proj_id." AND `phase`=".$prev_proj_phase_id;
			print("<br>UPDATE => " . $update_phase_sql);
			$mysql->sqlordie($update_phase_sql);
		}else{
			//delete
			$delete_phase_sql = "DELETE FROM `project_phase_finance` WHERE `project_id`=".$prev_proj_id." AND `phase`=".$prev_proj_phase_id;
			print("<br>UPDATE => " . $delete_phase_sql);
			$mysql->sqlordie($delete_phase_sql);
		}
		foreach($update_sql as $key => $sql){
			print("<br>UPDATE *=> " . $sql);
			$mysql->sqlordie($sql);
		}
	}*/

?>