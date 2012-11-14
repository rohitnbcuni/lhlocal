<?php

	session_start();	
	include("../_inc/config.inc");	
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);	
	$projID = 4265;	
	function getBudgetTotal($projID, $mysql) {
	//Do this but select the finace data and completeness		
	$select_project_phases = "SELECT * FROM `project_phase_finance` WHERE `project_id` = '" .$projID ."'";	
	$result_phases = $mysql->sqlordie($select_project_phases);				
	$projectDetail = '';		
	$row = 1;		$todate = 0;	
	while($row_phases = $result_phases->fetch_assoc()) {	
	$select_phase_data = "SELECT * FROM `lnk_project_phase_types` WHERE `id`='" .$row_phases['phase'] ."' LIMIT 1";		
	$phase_data_res = @$mysql->sqlordie($select_phase_data);			
	$phase_data_row = @$phase_data_res->fetch_assoc();			
	$timeline_query = "SELECT * FROM `project_phases` WHERE `project_id`='" .$projID."' AND `phase_type`='" .$row_phases['phase'] ."' LIMIT 1";		
	$timeline_res = @$mysql->sqlordie($timeline_query);		
	$timeline_row = @$timeline_res->fetch_assoc();			
	$select_user_phase = "SELECT * FROM `users` WHERE `role`='" .$phase_data_row['id'] ."'";	
	$user_phase_res = @$mysql->sqlordie($select_user_phase);		
	//$todate = 0;			if($user_phase_res->num_rows > 0) {	
	while($user_phase_row = @$user_phase_res->fetch_assoc()) {	
	$rp_data = "SELECT * FROM `resource_blocks` WHERE `userid`='" .$user_phase_row['id'] ."' AND `projectid`='" .$projID ."' AND `status`='4'";	
	$rp_res = @$mysql->sqlordie($rp_data);								
	if($rp_res->num_rows > 0) {				
	while($rp_row = $rp_res->fetch_assoc()) {	
	if($rp_row['daypart'] == 9) {				
	$todate += $rp_row['hours'] * $row_phases['rate'];	
	} else {				
	$todate += $row_phases['rate'];	
	}						
	}			
	}			
	}		
	}			
	$total_finance = $row_phases['hours'] * $row_phases['rate'];	
	$percentage = ($todate/$total_finance);				
	if(($percentage*100) > 100) {			
	$class = "alert";				
	$progress_class = " project_progress_alert";	
	}else {		
	$class = "";	
	$progress_class = "";		
	}				
	$width_calc = 0;
	if($percentage < 1) {	
	$width_calc = $percentage;		
	} else {		
	$width_calc = 1;	
	}					
	if($todate > 0 && $total_finance > 0) {		
	$complete = ceil(($todate/$total_finance)*100);			
	if($complete > 100) {				
	$complete = 100;		
	}			} else {	
	$complete = 0;			
	}					
	$start_date_time = explode(" ", $timeline_row['start_date']);	
	$start_date = explode("-", $start_date_time[0]);		
	$end_date_time = explode(" ", $timeline_row['projected_end_date']);	
	$end_date = explode("-", $end_date_time[0]);					
	/*echo $projectDetail .= '<!-- DATA -->				<ul class="' .$class .'">		
	<li class="project_detail">' .$phase_data_row['name'] .'</li>				
	<li class="dates">' .$end_date[1] ."/" .$start_date[2] .' - <strong>' .$end_date[1] ."/" .$end_date[2] .'</strong></li>		
	<li class="actual"><strong>$' .number_format($todate, 2, '.', ',') .'</strong></li>		
	<li class="budget"><strong>$' .number_format($total_finance, 2, '.', ',') .'</strong></li>
	<li class="completeness"><div class="project_progress ' .$progress_class .'" style="width: ' .($width_calc * 130) .'px"></div><div class="percentage">' 
	.number_format($complete, 0, '.', ',') .'%</div></li>				</ul>';*/		
	$row++;		}					
	//$projectDetail .= '</div>';		//echo $projectDetail;		//echo "<br /><br />";		return number_format($todate, 2, '.', '');	}	
	echo getBudgetTotal($projID, $mysql)?>