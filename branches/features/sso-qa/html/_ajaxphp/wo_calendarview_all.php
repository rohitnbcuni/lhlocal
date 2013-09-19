<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
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
			$html_see_more ='';
			$display = "block";
			$see_display = "none";
			$show_ticket_counter = ($ticket_counter>0)?$ticket_counter:"";
			if(in_array($currentDay, $week)) {
				$html .= '<!--== | START : Day ==-->
						<div id="box">';
							$html .= '	<h4>'.$show_ticket_counter.' TICKET(S) <span class="serial_no">'.$d.'</span></h4>';
							$html1 = '';
							$cell = 1;
							$page_no = 1;
							$per_page_block = PAGINATION;
							$html_see_more .='<div class="see_more_overlay" id="see_more_'.$cal_current_date.'"><div id="popup"> ';
							$html_see_more .= '<h4>'.$show_ticket_counter.' Tickets for '.$cal_formated_current_date.'<a href="#" onclick="$(\'.see_more_overlay\').css({display:\'none\'}); return false;"><img class="close" src="/_images/close_btn.gif" width="15" height="14" alt="close" /></a></h4><div class="bb">';
							if(count($day_data[1]) >0)
							{
								foreach($day_data[1] as $day_data_key => $day_data_cal)
								{
									if(count($day_data_cal) > 0)
									{
										if($day_data_cal['launch_date'] == $cal_current_date)
										{
											if($cell <= NUMBER_OF_CELL)
											{
												$html1 .='<p  style="display:'.$display.';background:'.fetStatusColor($day_data_cal['status']).';border:1px solid #D1D3D4;" title="'.$day_data_cal['tickets_title'].'"><span class="title">'.$day_data_cal['tickets_project_name'].'</span>';
												$html1 .='<span class="description"><a href="/workorders/index/edit/?wo_id='.$day_data_cal['tickets_id'].'" >'.$day_data_cal['tickets_id'].'-'.myTruncate($day_data_cal['tickets_title'],15).'</a></span>
												</p>';
											}
											$totalPages = ceil($ticket_counter / PAGINATION);
											//$html_see_more.='<input type="text"	name="pagination_count" id="pagination_count" value="'.$totalPages.'">';
											$html_see_more .= '<p title="'.$day_data_cal['tickets_title'].'" style="background:'.fetStatusColor($day_data_cal['status']).'" class ="pag_'.$page_no.'"> <a href="/workorders/index/edit/?wo_id='.$day_data_cal['tickets_id'].'"><span class="title"><b>'.$day_data_cal['tickets_project_name'].'</b>-'.$day_data_cal['tickets_id'].'</span> <span class="description">
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
		$client_sql = "";
		$employee = "";
		$client_filter_sql = " ";
		$project_filter_sql = "";
		$status_filter_sql = "";
		$assigned_to_filter_sql = "";
		$requestedby_filter_sql = "";
		$request_type_filter_sql = "";
		$date_range_filter_sql = "";
		$search_filter_sql = "";
		$page_number_filter_sql = "";
		$column_filter_sql = "";   // for sorting based on assigned_to or requested by 
		$type = '';
		$archive_sql = " AND W.`archived`='0' and W.`active`='1'";
		$project_archive = " AND W.`archived`='0'";
		if($_REQUEST['status'] =='1'){
			$archive_sql = " AND W.`archived`='0' and W.`active`='1'";
			$project_archive = " AND W.`archived`='0'";
		}else if($_REQUEST['status'] == '0'){ 
			$archive_sql = " AND W.`archived`='1' and W.`active`='1'";
			$project_archive = " AND W.`archived`='1'";
			// for archieved workorders
		}else if($_REQUEST['status'] == '-1'){    // for draft workorders
			$archive_sql = " AND W.`active`='0' AND W.`requested_by`='".$_SESSION['user_id']."'";
		}
		if(isset($_REQUEST['requested_by']) && $_REQUEST['requested_by'] != '-1'){
			$requestedby_filter_sql = " AND  W.requested_by = '".$_REQUEST['requested_by']."'";
		}
		/*if(isset($_REQUEST['proj_id']) && $_REQUEST['proj_id'] != '-1'){
  			$project_filter_sql = " AND P.`id` = ".$_REQUEST['proj_id'];
  		}*/
		if(isset($_REQUEST['client']) && $_REQUEST['client'] != '-1'){
			$client_filter_sql = " AND W.`company_id` = ".$_REQUEST['client'];
		}
		if(isset($_REQUEST['status_filter']) && $_REQUEST['status_filter'] != '-1'){  
		    $status_table_sql = "select `id` from `lnk_workorder_status_types` where name = ?";
		  	$status_result = $mysql->sqlprepare($status_table_sql, array($_REQUEST['status_filter']));
		    if($status_result->num_rows == 1){
		       $status_row = $status_result->fetch_assoc();
		    		       $status_filter_sql = " AND W.`status` = ".$status_row['id'];
		    }else{
		    	//If staus is open
		    	if($_REQUEST['status_filter'] == '99'){
		    		//on hold, Need More Info , New ,In Progress, Feedback Provided, Rejected, Reopened
		    		$status_filter_sql = " AND W.`status` IN (4,5,6,7,10,11,12) ";
		    		//if stutus is over_due 
		    	}elseif($_REQUEST['status_filter'] == 'over_due'){
		    		$status_filter_sql = " AND W.status NOT IN (1,3) AND W.launch_date < now() ";
		    	}
		    }	
	  	}
		if(isset($_REQUEST['assigned_to']) && $_REQUEST['assigned_to'] != '-1'){  
	    	$assigned_to_filter_sql = " AND W.`assigned_to` = ".$_REQUEST['assigned_to'];
	  	}
	
		//////////////////////
		if(isset($_REQUEST['column']) && isset($_REQUEST['order'])){     // for column and sorting order
    $column = $_REQUEST['column'];
    if($_REQUEST['order'] == '1'){
      $sort_order = 'ASC';
    } else if($_REQUEST['order'] == '0'){
      $sort_order = 'DESC';
    } 
    if($_REQUEST['column'] == 'open_date'){
      $column = 'creation_date';
    } else if($_REQUEST['column'] == 'due_date'){
      $column = 'launch_date';
    } else if($_REQUEST['column'] == 'assigned_to'){      // joining with users table in case of sorting by assigned_to and request_by
      $column = '`users`.`last_name`';
      $assigned_to_sort_sql = " JOIN `users` ON W.assigned_to = users.id ";
    } else if($_REQUEST['column'] == 'requested_by'){
      $column = '`users`.`last_name`';
      $requested_by_sort_table_sql = " JOIN `users` ON W.requested_by = users.id ";  
    } else if($_REQUEST['column'] == 'req_type'){
      $column = 'lc.`field_name`';    
      $req_type_sql = " JOIN `lnk_custom_fields_value` lc ON lc.`field_key` = e.`field_key` AND lc.`field_id` = e.`field_id`";      
      if($sort_order == 'ASC'){         //separate sort order only for req_type refer lnk_custom_fields_value
        $sort_order = 'DESC';
      } else if($sort_order == 'DESC'){
        $sort_order = 'ASC';
      }    
    } else if($_REQUEST['column'] == 'status'){
      $column = "lt.`name`";
      $status_sql = " JOIN `lnk_workorder_status_types` lt ON lt.`id` = W.`status`";
    }
    $column_filter_sql = ",".$column." ".$sort_order;
  }
///////
  if(isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date']) && isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])){  
    $date_range_filter_sql = " AND W.`creation_date` > '".date('Y-m-d',strtotime($_REQUEST['start_date']))."' AND b.`creation_date` < '".date('Y-m-d',strtotime($_REQUEST['end_date']))."'";
  }
  if(isset($_REQUEST['search']) && !empty($_REQUEST['search'])){
    $search_filter_table_sql = " LEFT JOIN workorder_comments wc ON wc.workorder_id = W.id";
    $search_filter_sql = " AND (`title` like '%".$_REQUEST['search']."%' OR `body` like '%".$_REQUEST['search']."%' OR `example_url` like '%".$_REQUEST['search']."%' OR `comment` like '%".$_REQUEST['search']."%')";
  }
		///////////////////////

		if(isset($_REQUEST['req_type']) && !empty($_REQUEST['req_type'])){
		    $request_types_array = array("Outage" => "1","Problem" => "2","Request" => "3","noneselected" => "999");
		    $request_array = explode(",",$_REQUEST['req_type']);
		    $request_type_string = "";
		    foreach($request_types_array as $key => $value){
		      if(in_array($key,$request_array)){
		        $request_type_string = $request_type_string.$value.",";
		      }
		    } 
		    $request_type_string = substr($request_type_string, 0, -1);
		    $workorder_custom_sql = " JOIN `workorder_custom_fields` e ON W.`id` = e.`workorder_id`";
		    $req_filter_sql =  " AND e.`field_id` IN(".$request_type_string.")";
	  	}
		$sso_user_sql = '';
		
		$workorder_list_query .= "SELECT W.id ,C.name as company_name, W.title ,W.active , W.status, DATE_FORMAT(W.launch_date,'%Y-%m-%d') as required_date  FROM `workorders` W
		INNER JOIN companies C ON (C.id = W.company_id)";
		$workorder_list_query .= "  $workorder_custom_sql WHERE 1  AND "; 
		$workorder_list_query .= " EXTRACT(MONTH FROM W.launch_date) = '$data->month'  AND EXTRACT(YEAR FROM W.launch_date) = '$data->year'  $archive_sql $status_filter_sql $assigned_to_filter_sql $req_filter_sql $client_filter_sql $requestedby_filter_sql ORDER BY W.launch_date ASC";
	  echo $workorder_list_query;
		//echo $archive_sql .$client_filter_sql;
		//echo $pjt_sql.$workorder_custom_sql.$requested_by_sort_table_sql.$search_filter_table_sql.$assigned_to_sort_sql.$req_type_sql.$status_sql.$user_pjt_sql.$where_clause. $archive_sql . $client_filter_sql .$req_filter_sql . $project_filter_sql . $status_filter_sql . $assigned_to_filter_sql . $requestedby_filter_sql .$date_range_filter_sql .$search_filter_sql;
		try{
			
			if(!$workorder_result = $mysql->sqlordie($workorder_list_query)){
				throw new Exception("MYsql Error:".mysqli_error($mysql));
			}
			$i = 0;
			//echo $workorder_result->num_rows;
			if($workorder_result->num_rows > 0) {
				while($workorder_row = $workorder_result->fetch_assoc()){
						$row[$i]['tickets_id'] = $workorder_row['id'];
						$row[$i]['tickets_project_name'] = $workorder_row['company_name'];
						$row[$i]['tickets_title'] = $workorder_row['title'];
						$row[$i]['launch_date'] = $workorder_row['required_date'];
						$row[$i]['status'] = $workorder_row['status'];
						$count_per_date[$i] = $workorder_row['required_date'];
						//$row->total_num_tickets = $workorder_result->num_rows;
				$i++;
				}
			
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
			6 => "#F7ECCD",
			3 => "#DAE7B2",
			11 => "#EBCBCC",
			4 => "#DDF2FF",
			7 =>"#FCFBF4",
			1 => "#FCFBF4",
			2 => "#FFF",
			5 => "#FFF",
			9 => "#FFF",
			10 => "#FFF",
			12 => "#FFF",
			);
		return $color_code[$val];
	}
	
	
	function checkUserComapny(){
		global $mysql;
		
		$user_id = $_SESSION['user_id'];
		$current_user = "SELECT company FROM `users` WHERE `id`= ?";
		$current_user_sql = $mysql->sqlprepare($current_user,array($user_id) );
		$rs = $current_user_sql->fetch_assoc();
		if(($rs['company'] == '') OR ($rs['company'] == NULL)){
			
			return false;
		
		}else{
		
			return true;
		}
	

	}
	//E9C8CA//DAE7B2//DDF2FF
?>
