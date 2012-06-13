<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	$userid = $_POST['userid'];
	$postDate = $_POST['date'];
	$postDatePart = explode("/", $postDate);
	define("NUMBER_OF_CELL",3);
	define("PAGINATION",10);
//	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	
	//p($_POST);
	
	if($_SESSION['user_id'] == $userid){
		$showSubmit = true;
	}else{
		$showSubmit = false;
	}
	$html = "";
	$week = array("mon"=>1, "tue"=>2, "wed"=>3, "thu"=>4, "fri"=>5, "sat"=>6, "sun"=>7);
	$wo_data = new stdClass();
	$currentMonth = date('n', mktime(0, 0, 0, $postDatePart[1], $postDatePart[2], $postDatePart[0])); 
	$currentYear = date('Y', mktime(0, 0, 0, $postDatePart[1], $postDatePart[2], $postDatePart[0]));

	$numberOfDays = date('t', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
	$numberOfWeeks = ceil($numberOfDays/7);
	$firstMonthDay = date('N', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
	$lastMonthDay = date('N', mktime(0, 0, 0, $currentMonth, $numberOfDays, $currentYear));

	$date_offset_array = array("1"=>4, "2"=>3, "3"=>2, "4"=>1, "5"=>0, "6"=>0, "0"=>0,);
	$weekNum = 1;
	$wo_data->month = $currentMonth;
	$wo_data->year = $currentYear;
	$day_data = getCalenderWoData($wo_data);
	//p($day_data);
	//p($day_data[1]);
	$total_tickets = array_count_values($day_data[0]);
	$ticket_counter = 0;
	$per_page_block = PAGINATION;
	//p($total_tickets);
	$open = false;
	for($d=1;$d<=$numberOfDays;$d++){
		if($d>$numberOfDays) {
			$sqlCurDay = $numberOfDays;
		} else {
			$sqlCurDay  = $d;
		}
		$cal_current_date = date("Y-m-d",mktime(0, 0, 0, $currentMonth, $d, $currentYear));
		$cal_formated_current_date = date("F j, Y",mktime(0, 0, 0, $currentMonth, $d, $currentYear));
		$currentDay = date('N', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
		if($currentDay <= 7) {
			if($d == 1 && $firstMonthDay <=7 ) {
				$sel_class = 'sel';
				$open = true;
				
				//$html .= '<div>';
				if( $firstMonthDay > 1 && $firstMonthDay <=7 ){
					for($b=1;$b<$firstMonthDay;$b++){
						$html .= '<div id="box_first"></div>';
					}
				}
			}
			if($currentDay == 1 && $currentDay != $d) {
				$sel_class = 'sel';
				$open = true;
			}
			if(array_key_exists($cal_current_date, $total_tickets)){
				$ticket_counter = $total_tickets[$cal_current_date];
			}else{
				$ticket_counter = 0;
			}
			$html2 ='';
			//p($day_data_cal);
			//echo fetStatusColor($day_data_cal['status']);
			//echo $currentDay; die;
			$display = "block";
			$see_display = "none";
			$html_see_more ='';
			$show_ticket_counter = ($ticket_counter>0)?$ticket_counter:"";
			if(in_array($currentDay, $week)) {
				$html .= '<!--== | START : Day ==-->
						<div id="box">';
							$html .= '	<h4>'.$show_ticket_counter.' PROJECT(S) <span class="serial_no">'.$d.'</span></h4>';
							$html1 = '';
							$cell = 1;
							$page_no = 1;
							$per_page_block = PAGINATION;
							$html_see_more .='<div class="see_more_overlay" id="see_more_'.$cal_current_date.'"><div id="popup"> ';
							$html_see_more .= '<h4>'.$show_ticket_counter.' Projects for '.$cal_formated_current_date.'<a href="#" onclick="$(\'.see_more_overlay\').css({display:\'none\'}); return false;"><img class="close" src="/_images/close_btn.gif" width="15" height="14" alt="close" /></a></h4><div class="bb">';
							if(count($day_data[1]) >0)
							{
								foreach($day_data[1] as $day_data_key => $day_data_cal)
								{
									//p($day_data_cal);
									
									if(count($day_data_cal) > 0)
									{
										if($day_data_cal['launch_date'] == $cal_current_date)
										{
											if($cell <= NUMBER_OF_CELL)
											{
												$html1 .='<p  style="display:'.$display.';background:'.fetStatusColor($day_data_cal['status']).';border:1px solid #D1D3D4;" title="'.$day_data_cal['tickets_title'].'"><span class="title">'.$day_data_cal['tickets_project_name'].' - </span>';
												$html1 .='<span class="description"><a href="/controltower/index/edit/?project_id='.$day_data_cal['tickets_id'].'" >'.myTruncate($day_data_cal['tickets_title'],15).'</a></span>
												</p>';
											}
											$totalPages = ceil($ticket_counter / PAGINATION);
											//$html_see_more.='<input type="text"	name="pagination_count" id="pagination_count" value="'.$totalPages.'">';
											$html_see_more .= '<p title="'.$day_data_cal['tickets_title'].'" style="background:'.fetStatusColor($day_data_cal['status']).'" class ="pag_'.$page_no.'"> <a href="/controltower/index/edit/?project_id='.$day_data_cal['tickets_id'].'"><span class="title"><b>'.$day_data_cal['tickets_project_name'].'</b> - '.'</span> <span class="description">
											  '.myTruncate($day_data_cal['tickets_title'],35).'</span></a> </p>';
											$html_see_more.='<input type="hidden"	name="pagination_per_page_'.$cal_current_date.'" id="pagination_per_page_'.$cal_current_date.'" value="1">';
											$html_see_more.='<input type="hidden"	name="pagination_count_'.$cal_current_date.'" id="pagination_count_'.$cal_current_date.'" value="'.$totalPages.'">';
											  									 											
										}else{
											continue;
										}
										if($cell == NUMBER_OF_CELL)
										{
											$display = "none";
											//break;
										}
										
										if($cell >= $per_page_block){
											$per_page_block = $per_page_block+PAGINATION;
											$page_no++;
										}
										$cell++;
										
										
										//echo count($day_data_cal);
										/*if($ticket_counter < NUMBER_OF_CELL){
											$html1 .='<p><span class="title"></span>';
											$html1 .='<span class="description"></span>
											</p>';
										}*/
										
									}
									
								}
								$html_see_more.='<input type="hidden"	name="total_records_'.$cal_current_date.'" id="total_records_'.$cal_current_date.'" value="'.$ticket_counter.'">';
							}
							
							
							if($ticket_counter > PAGINATION ){
								$html_see_more .= '</div> <p class="show_result">Showing <span class="counter_per_page">1-'.PAGINATION.'</span> of '.$ticket_counter.' Results <a href="javascript:page_pre(\''.$cal_current_date.'\');" class="arrow" ><img src="/_images/hours_arrow_left.gif" width="9" height="11" alt="previous" /></a><a href="javascript:page_next(\''.$cal_current_date.'\');" class="next" ><img src="/_images/hours_arrow_right.gif" width="9" height="11" alt="next" /></div></a></p></div>'; 
							
							}else{
								$html_see_more .= '</div> <p class="show_result"></p></div></div>';  
							
							}
							if($cell <= NUMBER_OF_CELL){
								$diff_cell = NUMBER_OF_CELL - $cell;
								for($j=0 ; $j<=$diff_cell;$j++){
									$html1 .='<p><span class="title"></span>';
									$html1 .='<span class="description"></span>
									</p>';
								} 
							}
							if($ticket_counter > NUMBER_OF_CELL){
								
								$html1 .= '<p class="see_more"><a href=javascript:call_see_more("'.$cal_current_date.'")>see more >></a></p>';						
								$html .= $html1.'</div>'.$html_see_more;
							}else{
								$html1 .= '<p class="see_more"></p>';						
								$html .= $html1.'</div>'.$html_see_more;
							}
							
					
						}
			
				}
			}

		
	$html .= '<input type="hidden" name="pagination_count" id="pagination_count" value='.PAGINATION.'>';	
	echo $html;
	
	
	
	
	function getCalenderWoData($data){
		global $mysql;
		$row = array();
		$count_per_date = array();
		$final_array = array();
		$postingList = Array();
		$userId = $_SESSION['user_id'];
		// filters from frontend for archieve workorders
		if($_SESSION['login_status'] == "client") {
			$client_sql = " AND P.`company`='".$_SESSION['company']."'";
		}else if($_POST['companyID'] && $_POST['companyID'] > 0){
			$client_sql = " AND P.`company`='".$_POST['companyID']."'";
			$companyID = $_POST['companyID'];
		}else {
			$client_sql = "";
			$companyID = 0;
		}
	
		if(array_key_exists('quarterID', $_POST)){
			$quarterID = $_POST['quarterID'];
		}else{
			$quarterID = 0;
			}
		//to default to get all projects
		$producerID = -1;
		if(array_key_exists('producerID', $_POST)){
			$lead_split = @explode("_",$_POST['producerID']);
			// to confirm a resource_type is selected
			if(count($lead_split) == 2)
			$producerID = $lead_split[1];
		}else{
			$producerID = -1;
		}

		if(array_key_exists('statusID', $_POST)){
			$statusID = $_POST['statusID'];
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
	
		if($statusID != '0'){
			$project_status_sql = " AND P.`project_status`='" . $statusID . "'";
		}
		if($ProgramID != '0'){
			if($ProgramID == '99'){
				$project_program_sql = " AND P.program is NULL";
			} else {
				$project_program_sql = " AND P.`program`='" . $ProgramID . "'";
			}
		}
		$column_filter_sql = "";   // for sorting based on assigned_to or requested by 
		$type = '';
		$producerWhere = '';
		if(ISSET($_POST['projectTitle'])){
			$projectTitle = $_POST['projectTitle'];
			if(!empty($projectTitle)){
				$projectTitleSql = " AND project_name LIKE '%$projectTitle%'";
			}
		}
		if($producerID == -1){
				$producerWhere = '';
				$producerSQL = '';
				
		}else if($producerID == 0){
			$producerFROM = " LEFT JOIN project_roles pjr ON (pjr.project_id = P.id) ";
			$producerSQL = " AND pjr.resource_type_id='" . $lead_split[0] . "' AND IFNULL(pjr.user_id, '0')='0'";
		}else{
			$producerFROM = " LEFT JOIN project_roles pjr ON (pjr.project_id = P.id) ";
			$producerSQL = " AND pjr.resource_type_id='" . $lead_split[0] . "' AND pjr.user_id='" . $producerID."'";
		}
		//echo $select_projects;
		$project_list_query .= "SELECT P.id , P.project_code , P.project_name,  DATE_FORMAT(PP.start_date,'%Y-%m-%d') as launch_date , P.project_status   FROM projects P INNER JOIN project_phases PP ON (PP.project_id = P.id) $producerFROM WHERE P.archived  = '0' AND P.active = '1' AND P.deleted ='0' AND PP.phase_type = '7'";
		$project_list_query .= " AND EXTRACT(MONTH FROM PP.start_date) = '$data->month'   AND EXTRACT(YEAR FROM PP.start_date) = '$data->year'  $client_sql $project_status_sql $project_program_sql $producerSQL $projectTitleSql $rp_date";
		//echo $project_list_query;
		//echo $client_sql;
		try{
			
			if(!$project_result = $mysql->query($project_list_query)){
				throw new Exception("MYsql Error:".mysqli_error($mysql));
			}
			$i = 0;
			//echo $workorder_result->num_rows;
			if($project_result->num_rows > 0) {
				while($project_row = $project_result->fetch_assoc()){
					//p($project_row);
						if($project_row['launch_date'] == '0000-00-00'){
							$project_row_start_date = date("Y-m-d");
						}else{
							$project_row_start_date = $project_row['launch_date'];
						}
						$row[$i]['tickets_id'] = $project_row['id'];
						$row[$i]['tickets_project_name'] = $project_row['project_code'];
						$row[$i]['tickets_title'] = $project_row['project_name'];
						$row[$i]['launch_date'] = $project_row_start_date;
						$row[$i]['status'] = $project_row['project_status'];
						$count_per_date[$i] = $project_row_start_date;
						
						//$row->total_num_tickets = $workorder_result->num_rows;
				$i++;
				}
				//p($row);
			
			}
		}catch(Exception $e){
			echo $e->getMessage();			
		}
		$final_array[0] = $count_per_date;
		$final_array[1] = $row;	
		return $final_array;	
	}
	function myTruncate($string,$len) 
	{ 
		// return with no change if string is shorter than $limit  
		/*if(strlen($string) <= $limit) return $string;
		 // is $break present between $limit and the end of the string? 
		  if(false !== ($breakpoint = strpos($string, $break, $limit))) 
		  { 
		  	if($breakpoint < strlen($string) - 1) 
		  	{ 
		  		$string = substr($string, 0, $breakpoint) . $pad;
		  	 } 
		  }*/
		 $string = substr($string,0,$len);
		
	 return $string;
	 }	
	 
	function fetStatusColor($val){
		$color_code  = array(
			6 => "#FFF",
			3 => "#FFF",
			11 => "#FF",
			4 => "#FFF",
			7 =>"#FFF",
			1 => "#FFF",
			2 => "#FFF",
			5 => "#FFF",
			9 => "#FFF",
			10 => "#FFF",
			12 => "#FFF",
			);
		return $color_code[$val];
	}
	//E9C8CA//DAE7B2//DDF2FF
?>
