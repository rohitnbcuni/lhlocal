<?php
session_start();
	include("../_inc/config.inc");
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	$perms = array();
	
	if($_SESSION['login_status'] == "client") {
		$client_sql = " AND `company`='".$_SESSION['company']."'";
	}else if($_GET['id'] && $_GET['id'] > 0){
		$client_sql = " AND `company`='".$_GET['id']."'";
		$companyID = $_GET['id'];
	}else {
		$client_sql = "";
		$companyID = 0;
	}

	if(array_key_exists('quarterID', $_GET)){
		$quarterID = $_GET['quarterID'];
	}else{
		$quarterID = 0;
	}
	//to default to get all projects
	$producerID = -1;
	if(array_key_exists('producerId', $_GET)){
		$lead_split = @explode("_",$_GET['producerId']);
		// to confirm a resource_type is selected
		if(count($lead_split) == 2)
		$producerID = $lead_split[1];
	}else{
		$producerID = -1;
	}

	if(array_key_exists('statusID', $_GET)){
		$statusID = $_GET['statusID'];
	}else{
		$statusID = 0;
	}

	if(isset($_REQUEST['ProgramID'])){
		$ProgramID = $_REQUEST['ProgramID'];
	}else{
		$ProgramID = 0;
	}
	$quarter_budget_sql = "";
	$quarter_budget_sql_producer = "";
	
	$rp_start_date = "";
	$rp_end_date = "";
	$rp_date = "";
	if($quarterID>0){
		$where_clause = "";
		if($quarterID == 1){
			 $where_clause = " where quarter1_budget <> 0 ";
			 $rp_start_date = current_year.'-01-01';
		 	 $rp_end_date = current_year.'-03-31';
			 $rp_date ="	and rb.datestamp >= '".$rp_start_date."' and rb.datestamp <= '".$rp_end_date."'";
		}else if($quarterID == 2){
			$where_clause = " where quarter2_budget <> 0 ";
			$rp_start_date = current_year.'-04-01';
			$rp_end_date = current_year.'-06-30';
 		    $rp_date ="	and rb.datestamp >= '".$rp_start_date."' and rb.datestamp <= '".$rp_end_date."'";
		}else if($quarterID == 3){
			$where_clause = " where quarter3_budget <> 0 ";
			$rp_start_date = current_year.'-07-01';
			$rp_end_date = current_year.'-09-30';
  		    $rp_date ="	and rb.datestamp >= '".$rp_start_date."' and rb.datestamp <= '".$rp_end_date."'";
		}else if($quarterID == 4){
			$where_clause = " where quarter4_budget <> 0 ";
			$rp_start_date = current_year.'-10-01';
			$rp_end_date = current_year.'-12-31';
     	    $rp_date ="	and rb.datestamp >= '".$rp_start_date."' and rb.datestamp <= '".$rp_end_date."'";
		}
		$quarter_budget_sql = " and id in (select project_id from project_budget $where_clause) ";
		$quarter_budget_sql_producer = " and pjt.id in (select project_id from project_budget $where_clause) ";
	}
	$project_status_sql = "";
//	$approvalID was replaced by $statusID
//	if($approvalID == 1){
//		$approval_project_sql = "and id in (select project_id from `project_phase_approvals` WHERE non_phase='client' and approved='1') ";
//	}else if($approvalID == 2){
//		$approval_project_sql = "and id in (select project_id from `project_phase_approvals` WHERE non_phase='client' and approved='0') ";
//	}

	if($statusID != '0'){
		$project_status_sql = " AND `project_status`='" . $statusID . "'";
	}
	if($ProgramID != '0'){
		if($ProgramID == '99'){
			$project_program_sql = " AND program is NULL";
		} else {
			$project_program_sql = " AND `program`='" . $ProgramID . "'";
		}
	}

	$select_sections = "SELECT count(`id`) as total FROM `lnk_project_brief_section_types`";
	$sections_result = $mysql->query($select_sections);
	$section_row = $sections_result->fetch_assoc();
	
	//$select_perms = "SELECT DISTINCT `project_id` FROM `user_project_permissions` WHERE `user_id`='" .$_SESSION['user_id'] ."'";
	//$perms_result = $mysql->query($select_perms);
	//if($perms_result->num_rows > 0) {
	//	while($perms_row = $perms_result->fetch_assoc()){
	//		$perms[$perms_row['project_id']] = true;
	//	}
	//}

	$status_sql = 'select * from `lnk_project_status_types`';
	$status_result = $mysql->query($status_sql);
	$status_array = array();
	if(@$status_result->num_rows > 0) {
		while($row_result = @$status_result->fetch_assoc()) {
			$status_array[$row_result['id']] = $row_result['name'];
		}
	}
	
	$archive ='0';
	if(@$_REQUEST["archive"] == 1) {
		$archive ='1';
	}
		
	if(@$_REQUEST["archive"] == 1) { 
		$postingList = Array();
		
		if($producerID == -1){
			$select_projects = "SELECT * FROM `projects` WHERE `archived` = '1' AND `active` = '1' AND `deleted` = '0' $client_sql $quarter_budget_sql $project_status_sql $project_program_sql ORDER BY `project_name` ASC";
		}else if($producerID == 0){
			$select_projects = "SELECT pjt.id id, pjt.project_name project_name, pjt.project_code project_code, pjt.company company, pjt.project_status project_status FROM projects pjt LEFT JOIN (select project_id, user_id from project_roles where resource_type_id='" . $lead_split[0] . "') pjr on pjt.id=pjr.project_id WHERE pjt.archived='1' AND pjt.active='1' AND pjt.deleted='0' $project_status_sql $project_program_sql AND IFNULL(pjr.user_id, '0')='0' group by pjt.id;";
		}else{
			$select_projects = "SELECT pjt.id id, pjt.project_name project_name, pjt.project_code project_code, pjt.company company, pjt.project_status project_status FROM projects pjt, project_roles pjr WHERE pjt.archived='1' AND pjt.active='1' AND pjt.deleted='0' AND pjr.resource_type_id='" . $lead_split[0] . "' AND pjr.user_id='" . $producerID . "' AND pjt.id=pjr.project_id $project_status_sql $project_program_sql";
		}

		$result = @$mysql->query($select_projects);
		
		if(@$result->num_rows > 0) {
			while($row = @$result->fetch_assoc()) {
				$finance_total = 0;
				$todate = 0;
				//LH#24427
				//if($companyID > 0){
					$project_rate_array = calculateToDate($row['id'], $mysql,$rp_date, $archive);
				//}			
				$select_complete = "SELECT count(`id`) as total FROM `project_brief_sections` WHERE `project_id`='" .$row['id'] ."'";
				$complete_result = $mysql->query($select_complete);
				$complete_row = $complete_result->fetch_assoc();
				
				$select_budget = "select * from project_budget where project_id='" .$row['id'] ."'";
				$budget_result = $mysql->query($select_budget);
				if($budget_result->num_rows == 1){
					$result_set = $budget_result->fetch_assoc();
					if($quarterID == 1){
						$finance_total += $result_set['quarter1_budget'];
					}else if($quarterID == 2){
						$finance_total += $result_set['quarter2_budget'];
					}else if($quarterID == 3){
						$finance_total += $result_set['quarter3_budget'];
					}else if($quarterID == 4){
						$finance_total += $result_set['quarter4_budget'];
					}else{
						$finance_total += $result_set['total_budget'];
					}
					
				}else if($budget_result->num_rows == 0){
					$select_finance = "SELECT * FROM `project_phase_finance` WHERE `project_id`='" .$row['id'] ."'";
					$finance_result = $mysql->query($select_finance);
				
					while($finance_row = $finance_result->fetch_assoc()) {
						$finance_total += ($finance_row['rate'] * $finance_row['hours']);
					}
				}

//				$select_finance = "SELECT * FROM `project_phase_finance` WHERE `project_id`='" .$row['id'] ."'";
//				$finance_result = $mysql->query($select_finance);
//				$finance_total = 0;
//				$todate = 0;
				$producer_userid = '0';
				$manager_userid = '0';
				$project_producer = "select * from users u where u.id= (SELECT user_id FROM `project_roles` WHERE `project_id`='" .$row['id'] ."' AND `resource_type_id`='2' LIMIT 1)";
				$producer_result = $mysql->query($project_producer);
				if($producer_result->num_rows == 1){
				$producer_row = $producer_result->fetch_assoc();
				$producer_userid = $producer_row['id'];
				}
				$project_manager = "select * from users u where u.id= (SELECT user_id FROM `project_roles` WHERE `project_id`='" .$row['id'] ."' AND `resource_type_id`='3' LIMIT 1)";
				$manager_result = $mysql->query($project_manager);
				if($manager_result->num_rows == 1){
					$manager_row = $manager_result->fetch_assoc();
					$manager_userid = $manager_row['id'];
				}
				
				/*while($finance_row = $finance_result->fetch_assoc()) {
					$finance_total += ($finance_row['rate'] * $finance_row['hours']);
				}
				
				$todate = getBudgetTotal($row['id'] , $mysql);*/
				$todate = number_format($project_rate_array[$row['id']],2,'.','');//getBudgetTotal_All($row['id'], $mysql, $quarterID);
				
				$completeness = 0;
				$completeness = $section_row['total'] * $complete_row['total'];
				if($finance_total > 0) {
					$percentage = ($todate/$finance_total);
				} else {
					$percentage = 0;
				}
				
				if(($percentage*100) > 100) {
					$class = "alert";
					$progress_class = " project_progress_alert";
				}
				else {
					$class = "";
					$progress_class = "";
				}
				
				$width_calc = 0;
				if($percentage < 1) {
					$width_calc = $percentage;
				} else {
					$width_calc = 1;
				}
				
				if($todate > 0 && $finance_total > 0) {
					if($finance_total > 0) {
						$complete = ceil(($todate/$finance_total)*100);
					} else {
						$complete = 0;
					}
					//if($complete > 100) {
						//$complete = 100;
					//}
				} else {
					$complete = 0;
				}
				
				$project_name = $row['project_code'] . ' - ' . $row['project_name'];
				if(strlen($project_name) > 50) {
					$elipse = "...";
				} else {
					$elipse = "";
				}
				
				//To set permissions on the Control Tower 
				//if(array_key_exists($row['id'], $perms)) {
					array_push($postingList,Array('id' => $row['id'], 'code' => $row['project_code'],'name' => substr($project_name, 0, 50).$elipse, 'full_name' => $project_name, 'todate' => $todate, 'budget' => $finance_total, 'complete' => "$complete", 'company' => $row['company'], 'producer_userid' => $producer_userid, 'manager_userid' => $manager_userid, 'class' => $class, 'progress_class' => $progress_class, 'risk' => array("riskCount" => "0"), 'status' => $row['project_status'],'status_name' => $status_array[$row['project_status']], 'approved' => '0'));
				//}
			}
		}
	} else {
		$postingList = Array();  
	
		if($producerID == -1){
			$select_projects = "SELECT * FROM `projects` WHERE `archived` = '0' AND `active` = '1' AND `deleted` = '0' $client_sql $quarter_budget_sql $project_status_sql $project_program_sql ORDER BY `project_name` ASC";
		}else if($producerID == 0){
			$select_projects = "SELECT pjt.id id, pjt.project_name project_name, pjt.project_code project_code, pjt.company company, pjt.project_status project_status FROM projects pjt LEFT JOIN (select project_id, user_id from project_roles where resource_type_id='" . $lead_split[0] . "') pjr on pjt.id=pjr.project_id WHERE pjt.archived='0' AND pjt.active='1' AND pjt.deleted='0' $project_status_sql $project_program_sql AND IFNULL(pjr.user_id, '0')='0' group by pjt.id;";
		}else{
			$select_projects = "SELECT pjt.id id, pjt.project_name project_name, pjt.project_code project_code, pjt.company company, pjt.project_status project_status FROM projects pjt, project_roles pjr WHERE pjt.archived='0' AND pjt.active='1' AND pjt.deleted='0' AND pjr.resource_type_id='" . $lead_split[0] . "' AND pjr.user_id='" . $producerID . "' AND pjt.id=pjr.project_id $project_status_sql $project_program_sql";
		}

		
		$result = @$mysql->query($select_projects);
		if(@$result->num_rows > 0) {
			while($row = @$result->fetch_assoc()) {
				$finance_total = 0;
				$todate = 0;
				//LH#24427
				//if($companyID > 0){
					$project_rate_array = calculateToDate($row['id'], $mysql,$rp_date, $archive);
				//}
				$select_complete = "SELECT count(`id`) as total FROM `project_brief_sections` WHERE `project_id`='" .$row['id'] ."' AND `flag`='3'";
				$complete_result = $mysql->query($select_complete);
				$complete_row = $complete_result->fetch_assoc();
				
				$select_budget = "select * from project_budget where project_id='" .$row['id'] ."'";
				$budget_result = $mysql->query($select_budget);
				if($budget_result->num_rows == 1){
					$result_set = $budget_result->fetch_assoc();
					if($quarterID == 1){
						$finance_total += $result_set['quarter1_budget'];
					}else if($quarterID == 2){
						$finance_total += $result_set['quarter2_budget'];
					}else if($quarterID == 3){
						$finance_total += $result_set['quarter3_budget'];
					}else if($quarterID == 4){
						$finance_total += $result_set['quarter4_budget'];
					}else{
						$finance_total += $result_set['total_budget'];
					}
					
				}else if($budget_result->num_rows == 0){
					$select_finance = "SELECT * FROM `project_phase_finance` WHERE `project_id`='" .$row['id'] ."'";
					$finance_result = $mysql->query($select_finance);
				
					while($finance_row = $finance_result->fetch_assoc()) {
						$finance_total += ($finance_row['rate'] * $finance_row['hours']);
					}
				}
				$producer_userid = '0';
				$manager_userid = '0';
				$project_producer = "select * from users u where u.id= (SELECT user_id FROM `project_roles` WHERE `project_id`='" .$row['id'] ."' AND `resource_type_id`='2' LIMIT 1)";
				$producer_result = $mysql->query($project_producer);
				if($producer_result->num_rows == 1){
				$producer_row = $producer_result->fetch_assoc();
				$producer_userid = $producer_row['id'];
				}

				$project_manager = "select * from users u where u.id= (SELECT user_id FROM `project_roles` WHERE `project_id`='" .$row['id'] ."' AND `resource_type_id`='3' LIMIT 1)";
				$manager_result = $mysql->query($project_manager);
				if($manager_result->num_rows == 1){
				$manager_row = $manager_result->fetch_assoc();
				$manager_userid = $manager_row['id'];
				}

				$project_approved = '0';
				$project_approval_sql = "SELECT * FROM `project_phase_approvals` WHERE `project_id`='" . $row['id'] . "' AND `non_phase`='client' LIMIT 1";
				$approval_result = $mysql->query($project_approval_sql);
				if($approval_result->num_rows == 1){
					$project_approvals = $approval_result->fetch_assoc();
					if($project_approvals['approved'] == 1){
						$project_approved = '1';
					}
				}

				$project_risk = "SELECT * FROM project_risks WHERE active='1' AND archived='0' AND project_id='" .$row['id'] ."' ORDER BY created_date DESC";
				$risk_result = $mysql->query($project_risk);
				$risk_row = $risk_result->fetch_assoc();
				$risk = array();
				$risk['riskCount'] = @$risk_result->num_rows;
				if($risk['riskCount'] > 0){
					$usrSql = "SELECT * FROM users WHERE id='" . $risk_row['assigned_to_user_id'] . "' AND active='1' LIMIT 1";
					$userResult = $mysql->query($usrSql);
					$risk['assigned'] = 'None';
					if($userResult->num_rows > 0) {
						$user = $userResult->fetch_assoc();
						$risk['assigned'] = $user['first_name'] . ' ' . $user['last_name'];
					}
					$risk['title'] = $risk_row['title'];
					$risk['desc'] = $risk_row['description'];
				}

//				$todate = getBudgetTotal($row['id'], $mysql, $quarterID);

				$todate = number_format($project_rate_array[$row['id']],2,'.','');//getBudgetTotal_All($row['id'], $mysql, $quarterID);
				//$todate = 0;
				
				$completeness = 0;
				$completeness = $section_row['total'] * $complete_row['total'];
				if($finance_total > 0) {
					$percentage = ($todate/$finance_total);
				} else {
					$percentage = 0;
				}
				
				$n = $percentage*100;
				switch($n) 
				{
					case $n > 100:				
						$class = "alert";
						$progress_class = " project_progress_alert";
						break;
								
					case $n > 75:				
						$class = "";
						$progress_class = "";
						break;	
								
					default:				
						$class = "";
						$progress_class = "project_progress_complete";
						break;			
				}
				
				
				$width_calc = 0;
				if($percentage < 1) {
					$width_calc = $percentage;
				} else {
					$width_calc = 1;
				}
				
				if($todate > 0 && $finance_total > 0) {
					if($finance_total > 0) {
						$complete = ceil(($todate/$finance_total)*100);
					} else {
						$complete = 0;
					}
					//if($complete > 100) {
						//$complete = 100;
					//}
				} else {
					$complete = 0;
				}
				$project_name = $row['project_code'] . ' - ' . $row['project_name'];
				if(strlen($project_name) > 50) {
					$elipse = "...";
				} else {
					$elipse = "";
				}
				
				//To set permissions on the Control Tower 
				//if(array_key_exists($row['id'], $perms)) {
					array_push($postingList,Array('id' => $row['id'], 'code' => $row['project_code'],'name' => substr($project_name, 0, 50).$elipse, 'full_name' => $project_name, 'todate' => "$todate", 'budget' => "$finance_total", 'complete' => "$complete", 'company' => $row['company'], 'producer_userid' => $producer_userid, 'manager_userid' => $manager_userid, 'class' => $class, 'progress_class' => $progress_class, 'risk' => $risk, 'status' => $row['project_status'],'status_name' => $status_array[$row['project_status']], 'approved' => $project_approved));
				//}
			}
		}
	}
	function getBudgetTotal_All($projID, $mysql, $quarterID){
		$todayDate = date("Y-m-d");
		$currentYear = @explode("-", $todayDate);
		$quarter_select = "";
		$prev_user = 0;
		$hourly_rate = 0;
		$todate = 0;
		if($quarterID == 1){
			$quarter_select = " and datestamp <= '".$currentYear[0]."-3-31' and datestamp >= '".$currentYear[0]."-1-1' ";
		}else if($quarterID == 2){
			$quarter_select = " and datestamp <= '".$currentYear[0]."-6-30' and datestamp >= '".$currentYear[0]."-4-1' ";
		}else if($quarterID == 3){
			$quarter_select = " and datestamp <= '".$currentYear[0]."-9-30' and datestamp >= '".$currentYear[0]."-7-1' ";
		}else if($quarterID == 4){
			$quarter_select = " and datestamp <= '".$currentYear[0]."-12-31' and datestamp >= '".$currentYear[0]."-10-1' ";
		}

		$rp_data = "SELECT * FROM `resource_blocks` WHERE `projectid`='" .$projID ."' AND `status`='4' $quarter_select order by userid";
		$rp_res = @$mysql->query($rp_data);
		if($rp_res->num_rows > 0) {
			$rates_array = array();
			$project_phase = "SELECT phase, rate from project_phase_finance WHERE project_id='" . $projID . "'";
			$res_project_phase = $mysql->query($project_phase);
			if($res_project_phase->num_rows > 0){
				while($project_phase_row = $res_project_phase->fetch_assoc()){
					$rates_array['phase'][$project_phase_row['phase']] = $project_phase_row['rate'];
				}
			}
			$project_sub_phase = "SELECT sub_phase, rate from project_sub_phase_finance WHERE project_id='" . $projID . "'";
			$res_project_sub_phase = $mysql->query($project_sub_phase);
			if($res_project_sub_phase->num_rows > 0){
				while($project_sub_phase_row = $res_project_sub_phase->fetch_assoc()){
					$rates_array['subphase'][$project_sub_phase_row['sub_phase']] = $project_sub_phase_row['rate'];
				}
			}
			$user_role_flag = '';
			$user_role_id = '';
			while($rp_row = $rp_res->fetch_assoc()) {
				if($prev_user != $rp_row['userid']){
					$userRole = "SELECT flag, phase_subphase_id FROM user_project_role WHERE project_id='" . $projID . "' AND user_id='" . $rp_row['userid'] . "' LIMIT 1";
					$userRole_res = $mysql->query($userRole);
					if($userRole_res->num_rows > 0){
						$userRole_row = $userRole_res->fetch_assoc();
						$user_role_flag = $userRole_row['flag'];
						$user_role_id = $userRole_row['phase_subphase_id'];
					}else{
						$role_sql = "SELECT `role` FROM users WHERE `id`='" .$rp_row['userid'] ."' LIMIT 1";
						$role_res = $mysql->query($role_sql);
						$role_row = $role_res->fetch_assoc();
						$user_role_flag = 'phase';
						$user_role_id = $role_row['role'];
					}
					if(array_key_exists($user_role_flag, $rates_array)){
						if(array_key_exists($user_role_id, $rates_array[$user_role_flag])){
							$hourly_rate = $rates_array[$user_role_flag][$user_role_id];
						}else{
							$hourly_rate = 0;
						}
					}else{
						$hourly_rate = 0;
					}
				}

				if($rp_row['daypart'] == 5) {
					$todate += $rp_row['hours'] * $hourly_rate;
				} else {
					$todate += 2 * $hourly_rate;
				}
				$prev_user = $rp_row['userid'];
			}
		}
		return number_format($todate, 2, '.', '');
	}
	
	function getBudgetTotal($projID, $mysql,$quarterID) {
		$todayDate = date("Y-m-d");
		$currentYear = @explode("-", $todayDate);
		$quarter_select = "";
		if($quarterID == 1){
			$quarter_select = " and datestamp <= '".$currentYear[0]."-3-31' and datestamp >= '".$currentYear[0]."-1-1' ";
		}else if($quarterID == 2){
			$quarter_select = " and datestamp <= '".$currentYear[0]."-6-30' and datestamp >= '".$currentYear[0]."-4-1' ";
		}else if($quarterID == 3){
			$quarter_select = " and datestamp <= '".$currentYear[0]."-9-30' and datestamp >= '".$currentYear[0]."-7-1' ";
		}else if($quarterID == 4){
			$quarter_select = " and datestamp <= '".$currentYear[0]."-12-31' and datestamp >= '".$currentYear[0]."-10-1' ";
		}
		//Do this but select the finace data and completeness
		$select_project_phases = "SELECT * FROM `project_phase_finance` WHERE `project_id` = '" .$projID ."'";
		$result_phases = $mysql->query($select_project_phases);
		
		$projectDetail = '';
		
		$row = 1;
		$todate = 0;
		while($row_phases = $result_phases->fetch_assoc()) {
			$select_phase_data = "SELECT * FROM `lnk_project_phase_types` WHERE `id`='" .$row_phases['phase'] ."' LIMIT 1";
			$phase_data_res = @$mysql->query($select_phase_data);
			$phase_data_row = @$phase_data_res->fetch_assoc();
			
			$timeline_query = "SELECT * FROM `project_phases` WHERE `project_id`='" .$projID."' AND `phase_type`='" .$row_phases['phase'] ."' LIMIT 1";
			$timeline_res = @$mysql->query($timeline_query);
			$timeline_row = @$timeline_res->fetch_assoc();
			
			$select_user_phase = "SELECT * FROM `users` WHERE `role`='" .$phase_data_row['id'] ."'";
			$user_phase_res = @$mysql->query($select_user_phase);
			//$todate = 0;
			if($user_phase_res->num_rows > 0) {
				while($user_phase_row = @$user_phase_res->fetch_assoc()) {

					//calculating the sub phase rate
					$sup_phase_rate = 0;
					$select_project_sub_phases = "select a.rate from project_sub_phase_finance a, user_project_sub_phase b where a.project_id = '" .$projID ."' and a.phase = '" .$row_phases['phase'] ."' and  b.user_id = '" .$user_phase_row['id'] ."' and a.project_id = b.project_id and a.sub_phase = b.sub_phase_id";
					$result_sub_phases = $mysql->query($select_project_sub_phases);
					if($result_sub_phases->num_rows > 0){
						while($sub_phase_row = @$result_sub_phases->fetch_assoc()){
							$sup_phase_rate = $sub_phase_row['rate'];
						}
					}
					
					$rp_data = "SELECT * FROM `resource_blocks` WHERE `userid`='" .$user_phase_row['id'] ."' AND `projectid`='" .$projID ."' AND `status`='4'$quarter_select";
					$rp_res = @$mysql->query($rp_data);
					if($rp_res->num_rows > 0) {
						while($rp_row = $rp_res->fetch_assoc()) {
							if($rp_row['daypart'] == 5) {
								$todate += $rp_row['hours'] * $row_phases['rate'];
								$todate += $rp_row['hours'] * $sup_phase_rate;
							} else {
								$todate += 2 * $row_phases['rate'];
								$todate += 2 * $sup_phase_rate;
							}
						}
					}
					
					
				}
			}
			

			$total_finance = $row_phases['hours'] * $row_phases['rate'];
			if($total_finance > 0) {
				$percentage = ($todate/$total_finance);
			} else {
				$percentage = 0;
			}
			
				$n = $percentage*100;
				switch($n) 
				{
					case $n > 100:				
						$class = "alert";
						$progress_class = " project_progress_alert";
						break;
								
					case $n > 75:				
						$class = "";
						$progress_class = "";
						break;	
								
					default:				
						$class = "";
						$progress_class = "project_progress_complete";
						break;			
				}
			
			$width_calc = 0;
			if($percentage < 1) {
				$width_calc = $percentage;
			} else {
				$width_calc = 1;
			}
			
			if($todate > 0 && $total_finance > 0) {
				if($total_finance > 0) {
					$complete = ceil(($todate/$total_finance)*100);
				} else {
					$complete = 0;
				}
				
				//if($complete > 100) {
					//$complete = 100;
				//}
			} else {
				$complete = 0;
			}
			
			$start_date_time = explode(" ", $timeline_row['start_date']);
			$start_date = explode("-", $start_date_time[0]);
			$end_date_time = explode(" ", $timeline_row['projected_end_date']);
			$end_date = explode("-", $end_date_time[0]);
			
			
		/*echo $projectDetail .= '<!-- DATA -->
				<ul class="' .$class .'">
					<li class="project_detail">' .$phase_data_row['name'] .'</li>
					<li class="dates">' .$end_date[1] ."/" .$start_date[2] .' - <strong>' .$end_date[1] ."/" .$end_date[2] .'</strong></li>
					<li class="actual"><strong>$' .number_format($todate, 2, '.', ',') .'</strong></li>
					<li class="budget"><strong>$' .number_format($total_finance, 2, '.', ',') .'</strong></li>
					<li class="completeness"><div class="project_progress ' .$progress_class .'" style="width: ' .($width_calc * 130) .'px"></div><div class="percentage">' 
					.number_format($complete, 0, '.', ',') .'%</div></li>
				</ul>';*/
			$row++;
		}
		
			
		//$projectDetail .= '</div>';


		//echo $projectDetail;
		//echo "<br /><br />";
		return number_format($todate, 2, '.', '');
	}
//	echo '<br> Result : <pre>';print_r($postingList);echo '</pre>';die();
	$jsonSettings = json_encode($postingList);
//	setcookie("lighthouse_ct_data", urlencode($companyID . '~' . $producerID . '~' . $quarterID), time()+220752000, '/controltower');
	// output correct header
	$isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ? 
	  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
	($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');

	echo $jsonSettings;
function calculateToDate($projID, $mysql, $rp_date,$archive){
	$result_array =array();
	
	//	$todayDate = date("Y-m-d");
	//	$currentYear = @explode("-", $todayDate);
		$toDateArray = array();
		$quarter_select = "";
		$todate = 0;
		if($quarterID == 1){
			$quarter_select = " and datestamp <= '".current_year."-3-31' and datestamp >= '".current_year."-1-1' ";
		}else if($quarterID == 2){
			$quarter_select = " and datestamp <= '".current_year."-6-30' and datestamp >= '".current_year."-4-1' ";
		}else if($quarterID == 3){
			$quarter_select = " and datestamp <= '".current_year."-9-30' and datestamp >= '".current_year."-7-1' ";
		}else if($quarterID == 4){
			$quarter_select = " and datestamp <= '".current_year."-12-31' and datestamp >= '".current_year."-10-1' ";
		}
		 $sql = "Select tab3.projectid, tab3.userid, sum(tab3.Total) AS Total from (Select rb.projectid, rb.userid, count(1)*2 AS Total  
		from projects pj, resource_blocks rb where pj.id=rb.projectid and pj.active='1' and pj.deleted='0' and pj.archived='".$archive."'  
		$rp_date and rb.status='4' and rb.daypart <> 5 AND rb.projectid = '".$projID."' group by pj.id, rb.userid 
		UNION ALL 
		Select rb.projectid, rb.userid, rb.hours AS Total  from projects pj, resource_blocks rb 
		where pj.id=rb.projectid and pj.active='1' and pj.deleted='0' and pj.archived='".$archive."' $rp_date and 
		rb.status='4' and rb.daypart = 5 AND rb.projectid = '".$projID."' group by pj.id, rb.userid) tab3  group by tab3.projectid, tab3.userid";
		
		//$rp_data = "SELECT daypart, hours,userid FROM `resource_blocks` WHERE `projectid`='" .$projID ."' AND `status`='4' $quarter_select";
		$rp_res = $mysql->query($sql);
		if($rp_res->num_rows > 0) {
			while($rp_row = $rp_res->fetch_assoc()) {
				//$todate = 0;
				//print_r($rp_row);
				$select_user_project_phase = "SELECT ppf.rate rate, ppf.phase phase FROM project_phase_finance ppf INNER JOIN user_project_role upr ON (ppf.project_id = upr.project_id AND ppf.phase = upr.phase_subphase_id AND upr.flag = 'phase') WHERE upr.user_id = '" .$rp_row['userid'] ."' AND ppf.project_id = '" .$projID ."' LIMIT 1";
				$result_user_project_phase = $mysql->query($select_user_project_phase);

				if($result_user_project_phase->num_rows > 0){
					$user_project_phase_row = $result_user_project_phase->fetch_assoc();
					$todate += $rp_row['Total'] * $user_project_phase_row['rate'];
										
				}else{
					$select_project_sub_phases = "SELECT pspf.rate rate, pspf.phase phase FROM project_sub_phase_finance pspf INNER JOIN user_project_role upr ON (pspf.project_id = upr.project_id AND pspf.sub_phase = upr.phase_subphase_id AND upr.flag = 'subphase') WHERE  upr.user_id = '" .$rp_row['userid'] ."' AND pspf.project_id = '" .$projID ."' LIMIT 1";
					$result_sub_phases = $mysql->query($select_project_sub_phases);

					if($result_sub_phases->num_rows > 0){
						$sub_phase_row = $result_sub_phases->fetch_assoc();
						$todate += $rp_row['Total'] * $sub_phase_row['rate'];
					}else{
						$select_project_phase = "SELECT rate,phase FROM `project_phase_finance` WHERE `project_id` = '" . $projID . "' AND `phase`='".UNASSIGNED_PHASE."'";  
						$result_phases = $mysql->query($select_project_phase);
						if($result_phases->num_rows > 0){
							$phase_row = $result_phases->fetch_assoc();
							$todate += $rp_row['Total'] * $phase_row['rate'];
						}
					}
				}
			}
		}
		$result_array[$projID] = $todate;
		return $result_array;
	}
?>

