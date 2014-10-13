<?
	session_start();
	include("../_inc/config.inc");
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	$todayDate = date("Y-m-d");
//	$currentYear = @explode("-", $todayDate);
	$quarterID = $mysql->real_escape_string($_GET['quarterId']);
//	$quarter_select = "";
//	if($quarterID == 1){
//		$quarter_select = " and datestamp >= '".$currentYear[0]."-1-1' and datestamp <= '".$currentYear[0]."-3-31' ";
//	}else if($quarterID == 2){
//		$quarter_select = " and datestamp >= '".$currentYear[0]."-4-1' and datestamp <= '".$currentYear[0]."-6-30' ";
//	}else if($quarterID == 3){
//		$quarter_select = " and datestamp >= '".$currentYear[0]."-7-1' and datestamp <= '".$currentYear[0]."-9-30' ";
//	}else if($quarterID == 4){
//		$quarter_select = " and datestamp >= '".$currentYear[0]."-10-1' and datestamp >= '".$currentYear[0]."-12-31' ";
//	}
	$projId = (int)$mysql->real_escape_string($_GET['projID']);
	$phaseToDate = calculateToDate($projId, $mysql, $quarterID);

	$project_details_sql = "SELECT bc_id from `projects` WHERE id= ?";
	$project_details_result = $mysql->sqlprepare($project_details_sql, array($projId));
	$project_details_row = $project_details_result->fetch_assoc();

	//Do this but select the finace data and completeness
//	$select_project_phases = "SELECT * FROM `project_phase_finance` WHERE `project_id` = '" .@$mysql->real_escape_string($_GET['projID']) ."'";
	$select_project_phases = "SELECT ppf.phase phase, ppf.rate rate, ppf.hours hours FROM project_phase_finance ppf, lnk_project_phase_types lppt where ppf.phase = lppt.id and ppf.project_id = '" .@$mysql->real_escape_string($_GET['projID']) ."' order by lppt.sort_order";

	$result_phases = $mysql->sqlordie($select_project_phases);
	$projectDetail = '<div class="project_results_details" id="' .$mysql->real_escape_string($_GET['projID'])  .'">
			<!-- SORTING -->
			<ul class="details_sort">
				<li class="project_detail">Project detail</li>
				<li class="dates">DATES</li>
				<li class="actual">To Date</li>
				<li class="budget">Budget</li>
				<li class="completeness">PROJECT COMPLETENESS</li>
			</ul>
			<!-- SORTING -->';
	$row = 1;
	$todate = 0;
	
	while($row_phases = $result_phases->fetch_assoc()) {
		$total_finance = 0;
		$select_phase_data = "SELECT * FROM `lnk_project_phase_types` WHERE `id`='" .$row_phases['phase'] ."' LIMIT 1";
		$phase_data_res = @$mysql->sqlordie($select_phase_data);
		$phase_data_row = @$phase_data_res->fetch_assoc();
		
		$timeline_query = "SELECT * FROM `project_phases` WHERE `project_id`='" .$mysql->real_escape_string($_GET['projID'])."' AND `phase_type`='" .$row_phases['phase'] ."' LIMIT 1";
		$timeline_res = @$mysql->sqlordie($timeline_query);
		$timeline_row = @$timeline_res->fetch_assoc();
		
//		$select_user_phase = "SELECT * FROM `users` WHERE `role`='" .$phase_data_row['id'] ."'";
//		$user_phase_res = @$mysql->query($select_user_phase);
//		$todate = 0;
//		if($user_phase_res->num_rows > 0) {
//			while($user_phase_row = @$user_phase_res->fetch_assoc()) {
//				
//				//calculating the sub phase rate
//				$sup_phase_rate = 0;
//				$select_project_sub_phases = "select a.rate,a.hours from project_sub_phase_finance a, user_project_sub_phase b where a.project_id = '" .$mysql->real_escape_string($_GET['projID'])  ."' and a.phase = '" .$row_phases['phase'] ."' and  b.user_id = '" .$user_phase_row['id'] ."' and a.project_id = b.project_id and a.sub_phase = b.sub_phase_id";
//				$result_sub_phases = $mysql->query($select_project_sub_phases);
//				if($result_sub_phases->num_rows > 0){
//					while($sub_phase_row = @$result_sub_phases->fetch_assoc()){
//						$sup_phase_rate = $sub_phase_row['rate'];
//					}
//				}
//
//				$rp_data = "SELECT * FROM `resource_blocks` WHERE `userid`='" .$user_phase_row['id'] ."' AND `projectid`='" .@$mysql->real_escape_string($_GET['projID']) ."' AND `status`='4' $quarter_select";
//				$rp_res = @$mysql->query($rp_data);
//				if($rp_res->num_rows > 0) {
//					while($rp_row = $rp_res->fetch_assoc()) {
//						if($rp_row['daypart'] == 5) {
//							$todate += $rp_row['hours'] * $row_phases['rate'];
//							$todate += $rp_row['hours'] * $sup_phase_rate;
//						} else {
//							$todate += 2 * $row_phases['rate'];
//							$todate += 2 * $sup_phase_rate;
//						}
//					}
//				}
//			}
//		}
		
		if(array_key_exists($row_phases['phase'], $phaseToDate)){
			$todate = $phaseToDate[$row_phases['phase']];
		}else{
			$todate = 0;
		}

		$sub_phase_select = "select * from project_sub_phase_finance where phase='" .$row_phases['phase'] ."' and project_id='" .@$mysql->real_escape_string($_GET['projID']) ."' and active='1'";
		$result_sub_phase_select = $mysql->sqlordie($sub_phase_select);
		if($result_sub_phase_select->num_rows > 0){
			while($project_subphase_row = $result_sub_phase_select->fetch_assoc()){
				$total_finance += $project_subphase_row['hours'] * $project_subphase_row['rate'];
			}
		}else{
			$total_finance += $row_phases['hours'] * $row_phases['rate'];
		}
		$percentage = 0;
		if($todate != 0 && $total_finance != 0){
			$percentage = ($todate/$total_finance);
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
			$complete = ceil(($todate/$total_finance)*100);
//			if($complete > 100) {
//				$complete = 100;
//			}
		} else {
			$complete = 0;
		}
		
		$start_date_time = explode(" ", $timeline_row['start_date']);
		$start_date = explode("-", $start_date_time[0]);
		$end_date_time = explode(" ", $timeline_row['projected_end_date']);
		$end_date = explode("-", $end_date_time[0]);
		$display = '1';
		if($row_phases['phase'] == UNASSIGNED_PHASE)
		{
			if($todate!='0'){
				$display = '1';
			}
			else
			{
				$display = '0';
			}
		}
		if(	$display == '1'){

			if($quarterID == 0){
				$projectDetail .= '<!-- DATA -->
				<ul class="' .$class .'">
					<li class="project_detail">' .$phase_data_row['name'] .'</li>
					<li class="dates">' .phaseDateCheck($start_date[1]) ."/" .phaseDateCheck($start_date[2]) .' - <strong>' .phaseDateCheck($end_date[1]) ."/" .phaseDateCheck($end_date[2]) .'</strong></li>
					<li class="actual"><strong>$' .number_format($todate, 0, '.', ',') .'</strong></li>
					<li class="budget"><strong>$' .number_format($total_finance, 0, '.', ',') .'</strong></li>
					<li class="completeness"><div class="project_progress ' .$progress_class .'" style="width: ' .($width_calc * 130) .'px"></div><div class="percentage">' 
					.number_format($complete, 0, '.', ',') .'%</div></li>
				</ul>';
			}else{
				$projectDetail .= '<!-- DATA -->
				<ul class="' .$class .'">
					<li class="project_detail">' .$phase_data_row['name'] .'</li>
					<li class="dates">' .phaseDateCheck($start_date[1])."/" .phaseDateCheck($start_date[2]) .' - <strong>' .phaseDateCheck($end_date[1]) ."/" .phaseDateCheck($end_date[2]) .'</strong></li>
					<li class="actual"><strong>$' .number_format($todate, 0, '.', ',') .'</strong></li>
					
				</ul>';
			}
		}
		$row++;
	}
									/*$projectDetail .= '<!-- DATA -->
									<ul class="alert">
										<li class="project_detail">UI Stage</li>
										<li class="dates">03/15 - <strong>03/25</strong></li>
										<li class="actual"><strong>$25,000</strong></li>
										<li class="budget"><strong>$20,000</strong></li>
										<li class="completeness"><div class="project_progress project_progress_alert" style="width: ' .(130*1) .'px"></div><div class="percentage">125%</div></li>
									</ul>
									<ul class="">
										<li class="project_detail">Visual Stage</li>
										<li class="dates">03/15 - <strong>03/25</strong></li>
										<li class="actual"><strong>$25,000</strong></li>
										<li class="budget"><strong>$20,000</strong></li>
										<li class="completeness"><div class="project_progress" style="width: ' .(130*.35) .'px"></div><div class="percentage">35%</div></li>
									</ul>
									<ul class="">
										<li class="project_detail">Development Stage</li>
										<li class="dates">03/15 - <strong>03/25</strong></li>
										<li class="actual"><strong>$25,000</strong></li>
										<li class="budget"><strong>$20,000</strong></li>
										<li class="completeness"><div class="project_progress" style="width: ' .(130*.65) .'px"></div><div class="percentage">65%</div></li>
									</ul>
									<!-- ACTIONS -->';*/
	
	$projectDetail .= '<!-- DATA -->
					<ul class="details_actions">';
		$projectDetail .= '<li><button class="secondary" onClick="window.location = \'/controltower/index/edit/?project_id=' .$projId .'\';"><span>view/edit project</span></button></form></li>';
	if($_SESSION['login_status'] != "client") {	
		//defectID#3793
		//$projectDetail .= '<li><button class="secondary" name="duplicate" onClick="duplicateProject(\'' .$projId .'\');"><span>duplicate project</span></button></li>';
		if ($_GET['archived'] == 0) {					
			$projectDetail .= '<li><button class="secondary" name="archive" onClick="archiveConfirm(\'' .$projId .'\');"><span>archive project</span></button></li>';
		} else {
			$projectDetail .= '<li><button class="secondary" name="archive" onClick="archiveProject(\'' .$projId .'\');"><span>un-archive project</span></button></li>';
		}
		
		$projectDetail .= '<li><button class="trash" onClick="deleteConfirm(\'' .$projId .'\')"><span>delete project</span></button></li>';
		
	}
		$projectDetail .=	'<li class="open_basecamp" onClick=""><button class="secondary" onClick="window.open(\''. BASECAMP_HOST .'/projects/' . $project_details_row['bc_id'] . '/log\', \'_blank\')"><span>Open this project in Basecamp</span></button></li>';
	if ($_GET['archived'] == 0) {
	$projectDetail .=	'<li class="work_order_button" onClick=""><button class="secondary" onClick="window.location = \'/workorders/index/create/?project=' .$projId .'\'"><span>create work order</span></button></li>';
	}	
		$projectDetail .= '</ul>
					<!-- DATA -->';
	$projectDetail .= '</div>';


	function calculateToDate($projID, $mysql, $quarterID){
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

		$rp_data = "SELECT * FROM `resource_blocks` WHERE `projectid`='" .$projID ."' AND `status`='4' $quarter_select";
		$rp_res = @$mysql->sqlordie($rp_data);
		if($rp_res->num_rows > 0) {
			while($rp_row = $rp_res->fetch_assoc()) {
				$todate = 0;
				
				$select_user_project_phase = "SELECT ppf.rate rate, ppf.phase phase FROM project_phase_finance ppf, user_project_role upr WHERE ppf.project_id = upr.project_id AND ppf.phase = upr.phase_subphase_id AND upr.flag = 'phase' AND upr.user_id = '" .$rp_row['userid'] ."' AND ppf.project_id = '" .$projID ."' LIMIT 1";
				$result_user_project_phase = $mysql->sqlordie($select_user_project_phase);

				if($result_user_project_phase->num_rows > 0){
					$user_project_phase_row = $result_user_project_phase->fetch_assoc();
					if($rp_row['daypart'] == 9) {
						$todate += $rp_row['hours'] * $user_project_phase_row['rate'];
					} else {
						$todate += $user_project_phase_row['rate'];
					}
					if(array_key_exists($user_project_phase_row['phase'], $toDateArray)){
						$toDateArray[$user_project_phase_row['phase']] += $todate;
					}else{
						$toDateArray[$user_project_phase_row['phase']] = $todate;
					}
				}else{
					$select_project_sub_phases = "SELECT pspf.rate rate, pspf.phase phase FROM project_sub_phase_finance pspf, user_project_role upr WHERE pspf.project_id = upr.project_id AND pspf.sub_phase = upr.phase_subphase_id AND upr.flag = 'subphase' AND upr.user_id = '" .$rp_row['userid'] ."' AND pspf.project_id = '" .$projID ."' LIMIT 1";
					$result_sub_phases = $mysql->sqlordie($select_project_sub_phases);

					if($result_sub_phases->num_rows > 0){
						$sub_phase_row = $result_sub_phases->fetch_assoc();
						if($rp_row['daypart'] == 9) {
							$todate = $rp_row['hours'] * $sub_phase_row['rate'];
						} else {
							$todate = $sub_phase_row['rate'];
						}
						if(array_key_exists($sub_phase_row['phase'], $toDateArray)){
							$toDateArray[$sub_phase_row['phase']] += $todate;
						}else{
							$toDateArray[$sub_phase_row['phase']] = $todate;
						}
					}else{
						$select_project_phase = "SELECT * FROM `project_phase_finance` WHERE `project_id` = '" . $projID . "' AND `phase`='".UNASSIGNED_PHASE."'";  
						$result_phases = $mysql->sqlordie($select_project_phase);
						if($result_phases->num_rows > 0){
							$phase_row = $result_phases->fetch_assoc();
							if($rp_row['daypart'] == 9) {
								$todate = $rp_row['hours'] * $phase_row['rate'];
							} else {
								$todate = $phase_row['rate'];
							}
							if(array_key_exists($phase_row['phase'], $toDateArray)){
								$toDateArray[$phase_row['phase']] += $todate;
							}else{
								$toDateArray[$phase_row['phase']] = $todate;
							}
						}
					}
				}
			}
		}
		return $toDateArray;
	}

	function phaseDateCheck($phaseDate){
		if(empty($phaseDate))
		{
			$phaseDate= '00';
		}			
		return $phaseDate;
	}

	$jsonSettings = json_encode($projectDetail);

	// output correct header
	$isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ? 
	  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
	($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');

	echo $jsonSettings;
	

?>
