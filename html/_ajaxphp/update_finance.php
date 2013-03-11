<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$project = $mysql->real_escape_string(@$_GET['project_id']);
	$budget_code = $mysql->real_escape_string(@$_GET['budget_code']);
	
	$insert_new_budget_code = "INSERT INTO `project_budget` (project_id,budget_code,total_budget,quarter1_budget,quarter2_budget,quarter3_budget,quarter4_budget,note,updated_by,updated_on) VALUES"; 
	
	$fin = @$_GET['finance'];
	$fin_keys = array_keys($fin);
	$final_response = "";

	$user_id = $_SESSION['user_id'];

	
	#Saving the note & Updating the database
	if(@$_GET['action'] == "savenote") {
		$update_id = "";$note = "";
		$update_id = @$_GET['budget_update_id'];
		$note = @$_GET['note'];

		if($update_id != "" && trim($note) != "") {
				$update_budget = "UPDATE `project_budget` SET `note` = '".$note."' WHERE `id` IN(".$update_id.")";
				//echo $update_budget;
				$result = $mysql->sqlordie($update_budget);
		}
		exit;
	}

	
	#Setting the flag for the Original Budget Code
	if(@$_GET['action'] == "setFlag") {

		if(trim($project) != "") {
				$update_budget = "UPDATE project_budget SET delete_flag = 1 WHERE project_id=".$project." AND id NOT IN (SELECT * from (SELECT max(`id`) AS `id` FROM `project_budget` where project_id=".$project." GROUP BY `budget_code` ORDER BY `updated_on` limit 1) as ids)";
				//echo $update_budget;
				$result = $mysql->sqlordie($update_budget);
		}
		exit;
	}


	#Handling Multiple Budget Code Save
	if(@$_GET['action'] == "save") {

		if(@$_GET['requestFrom'] == "budget") {

		$budget_array = "";
		$budget_code = "";

		$select_budget = "SELECT * FROM project_budget WHERE `project_id`='$project' LIMIT 1";
		$budget_result = $mysql->sqlordie($select_budget);

		$budget_array = @$_GET['budget'];
		$budget_array = array_values($budget_array);
		$budget_code = @$_GET['budgetcode'];

		if($budget_result->num_rows == 1){
			//if(1){
				//$row = $budget_result->fetch_assoc();
				//$budget_array['note'] = "test";

				if(is_array($budget_array) && count($budget_array) > 0)	
				{
					foreach($budget_array as $key => $budget) {	

						$result = "";$bc_response = "";

						$check_recent_budget = "SELECT * FROM `project_budget` WHERE `project_id`='$project' AND `budget_code`='".trim($budget_code[$key]["original_budget_code"])."' ORDER BY `updated_on` DESC LIMIT 1";
						$result = $mysql->sqlordie($check_recent_budget);
						$bc_response = $result->fetch_assoc();
						
						if(trim($bc_response["total_budget"]) == trim($budget['totalBudget']) && trim($bc_response["quarter1_budget"]) == trim($budget['quarter1']) && trim($bc_response["quarter2_budget"]) == trim($budget['quarter2']) && trim($bc_response["quarter3_budget"]) == trim($budget['quarter3']) && trim($bc_response["quarter4_budget"]) == trim($budget['quarter4'])) {
							continue;
						}else {
								$check_new_budget = "SELECT `id` FROM `project_budget` WHERE `project_id`='$project' AND `budget_code`='".trim($budget_code[$key]["original_budget_code"])."'";
								$new_budget_result = $mysql->sqlordie($check_new_budget);
	
								if($new_budget_result->num_rows > 0) {
								
									$insert_project_budget = "INSERT INTO project_budget "	."(project_id,total_budget,quarter1_budget,quarter2_budget,quarter3_budget,quarter4_budget,updated_by,updated_on,budget_code,note)"
												."VALUES "
												."('$project','" .trim($budget['totalBudget'])."','" .trim($budget['quarter1'])."','" .trim($budget['quarter2'])."','" .trim($budget['quarter3'])."','" .trim($budget['quarter4'])."'," .trim($user_id).",NOW(),'".trim($budget_code[$key]["original_budget_code"])."','')";
									//echo $insert_project_budget;
									@$mysql->sqlordie($insert_project_budget);

									$recent_id = "";
									$obj = "";
									$recent_id_sql = "SELECT MAX(id) as id FROM `project_budget` WHERE `project_id`='$project'";

									$obj = @$mysql->sqlordie($recent_id_sql);
									$recent_id = $obj->fetch_assoc();
									$final_response .= $recent_id["id"].",";
							}else {
									$insert_project_budget = "INSERT INTO project_budget "	."(project_id,total_budget,quarter1_budget,quarter2_budget,quarter3_budget,quarter4_budget,updated_by,updated_on,budget_code,note)"
												."VALUES "
												."('$project','" .trim($budget['totalBudget'])."','" .trim($budget['quarter1'])."','" .trim($budget['quarter2'])."','" .trim($budget['quarter3'])."','" .trim($budget['quarter4'])."'," .trim($user_id).",NOW(),'".trim($budget_code[$key]["original_budget_code"])."','')";
									//echo $insert_project_budget;
									@$mysql->sqlordie($insert_project_budget);
							}
							
						}

					}

					if($final_response != "") {
						$final_response = rtrim($final_response,",");
					}

					if(is_array($budget_code) && count($budget_code) > 0) {
						foreach($budget_code as $value) {
							$update_query = "";
							$update_query = "UPDATE `project_budget` SET `budget_code`='".trim($value["budget_code"])."' WHERE `project_id`=".$project." AND `budget_code`='".trim($value["original_budget_code"])."'";
							$update_query_result = $mysql->sqlordie($update_query);
						}
					}

				}
			//}
			
		}else if($budget_result->num_rows == 0){

			if(is_array($budget_array) && count($budget_array) > 0)	
			{
				foreach($budget_array as $key => $budget) {	

				$insert_project_budget = "INSERT INTO project_budget "	."(project_id,total_budget,quarter1_budget,quarter2_budget,quarter3_budget,quarter4_budget,updated_by,updated_on,budget_code,note)"
							."VALUES "
							."('$project','" .$budget['totalBudget'] ."','" .$budget['quarter1'] ."','" .$budget['quarter2'] ."','" .$budget['quarter3'] ."','" .$budget['quarter4'] ."'," .$user_id .",NOW(),'".trim($budget_code[$key]["budget_code"])."','')";
							//echo $insert_project_budget;
				@$mysql->sqlordie($insert_project_budget);
				}
			}
		}

		/*
		$budgetCodeCount = @$_GET['budget_count'];
		$budgetQuery = "";

		#Inserting the newly added Budget Code
		if($budgetCodeCount > 0) {
			for($budgetLoop=1;$budgetLoop<=$budgetCodeCount;$budgetLoop++) {
				$bc = "";
				
				if(@$_GET['budget_code_'.$budgetLoop] != "") {
					$bc = explode(",",@$_GET['budget_code_'.$budgetLoop]);
				}
				$budgetQuery = $budgetQuery.$project.",";

				if(is_array($bc) && count($bc) > 0) {
					foreach($bc as $budgetValue) {
						if(!is_numeric($budgetValue)) {
							$budgetQuery .= "'".$budgetValue."',";
						}else{
							$budgetQuery .= $budgetValue.",";
						}
					}
				}

				$budgetQuery .= "NOW()";
				$budgetQuery .= "),(";			
			}
				$budgetQuery = rtrim($budgetQuery, ",(");
				$insert_new_budget_code .= " (".$budgetQuery;
				@$mysql->sqlordie($insert_new_budget_code);				
		}*/
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

	echo $final_response;
?>