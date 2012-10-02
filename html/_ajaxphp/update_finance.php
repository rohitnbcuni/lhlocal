<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql;
	
	$project = $mysql->real_escape_string(@$_GET['project_id']);
	$budget_code = $mysql->real_escape_string(@$_GET['budget_code']);
	
	$update_budget = "UPDATE `projects` SET `budget_code`='$budget_code' WHERE `id`='$project'";
	$mysql->sqlordie($update_budget);
	
	$fin = @$_GET['finance'];
	$fin_keys = array_keys($fin);

	if(@$_GET['action'] == "save") {
		$totalBudget = @$_GET['totalBudget'];
		$quarterOneBudget = @$_GET['quarter1'];
		$quarterTwoBudget = @$_GET['quarter2'];
		$quarterThreeBudget = @$_GET['quarter3'];
		$quarterFourBudget = @$_GET['quarter4'];
		$select_budget = "SELECT * FROM project_budget WHERE `project_id`='$project' LIMIT 1";
		$budget_result = $mysql->sqlordie($select_budget);
		if($budget_result->num_rows == 1){
			if($totalBudget > 0){
				$row = $budget_result->fetch_assoc();
				$update_project_budget = "UPDATE project_budget SET total_budget='" 
						.$totalBudget ."',quarter1_budget='" .$quarterOneBudget."',quarter2_budget='" .$quarterTwoBudget."',quarter3_budget='" .$quarterThreeBudget."',quarter4_budget='" .$quarterFourBudget."' WHERE `id`='" .$row['id'] ."'";
				@$mysql->sqlordie($update_project_budget);
			}
			
		}else if($budget_result->num_rows == 0){
			$insert_project_budget = "INSERT INTO project_budget "
						."(project_id,total_budget,quarter1_budget,quarter2_budget,quarter3_budget,quarter4_budget) "
						."VALUES "
						."('$project','" .$totalBudget ."','" .$quarterOneBudget ."','" .$quarterTwoBudget ."','" .$quarterThreeBudget ."','" .$quarterFourBudget ."')";
			@$mysql->sqlordie($insert_project_budget);
		}
		for($i = 0; $i < sizeof($fin_keys); $i++) {
			if(isset($fin[$fin_keys[$i]])) {
				$select_finance = "SELECT * FROM `project_phase_finance` WHERE `project_id`='$project' AND `phase`='" .$fin[$fin_keys[$i]]['phase'] ."' LIMIT 1";
				$finance_result = $mysql->sqlordie($select_finance);
				
				if($finance_result->num_rows == 1) {
					$row = $finance_result->fetch_assoc();
					$update_query = "UPDATE `project_phase_finance` SET `hours`='" 
						.$fin[$fin_keys[$i]]['hours'] ."',`rate`='" .$fin[$fin_keys[$i]]['rate'] 
						."' WHERE `id`='" .$row['id'] ."'";
					@$mysql->sqlordie($update_query);
				} else if($finance_result->num_rows == 0) {
					$insert_query = "INSERT INTO `project_phase_finance` "
						."(`project_id`,`phase`,`hours`,`rate`,`creation_date`) "
						."VALUES "
						."('$project','" .$fin[$fin_keys[$i]]['phase'] ."','" .$fin[$fin_keys[$i]]['hours'] ."','" .$fin[$fin_keys[$i]]['rate'] ."',NOW())";
					 @$mysql->sqlordie($insert_query);
				}
			}
		}

		$sub_phase_array = @$_GET['subphase'];
		foreach($sub_phase_array as $sp_phase => $sp_phase_content){
			foreach($sp_phase_content as $sp_subphase => $sp_subphase_content){
				$sp_updateSql = "UPDATE project_sub_phase_finance SET `hours`='" .$sp_subphase_content['hours']. "', `rate`='" .$sp_subphase_content['rate']. "' WHERE `sub_phase`='" .$sp_subphase. "' AND `phase`='" .$sp_phase. "' AND `project_id`='" .$project. "'";
				$mysql->sqlordie($sp_updateSql);
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
	} else if(@$_GET['action'] == "delete") {
		for($i = 0; $i < sizeof($fin_keys); $i++) {
			if(isset($fin[$fin_keys[$i]])) {
				$select_finance = "SELECT * FROM `project_phase_finance` WHERE `project_id`='$project' AND `phase`='" .$fin[$fin_keys[$i]]['phase'] ."' LIMIT 1";
				$finance_result = $mysql->sqlordie($select_finance);
				$delSubPhase = "DELETE FROM project_sub_phase_finance WHERE `project_id`='$project' AND `phase`='" .$fin[$fin_keys[$i]]['phase'] ."'";
				if($finance_result->num_rows == 1) {
					$row = $finance_result->fetch_assoc();
					$delete_query = "DELETE FROM `project_phase_finance` WHERE `id`='" .$row['id'] ."'";
					@$mysql->sqlordie($delete_query);
					@$mysql->sqlordie($delSubPhase);
				}
			}
		}
	}
?>