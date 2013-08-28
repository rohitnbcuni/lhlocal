<?php

session_start();
if(!(isset($from_action) && $from_action))
include('../_inc/config.inc');
//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
if(ISSET($_SESSION['user_id'])){
	global $mysql;
	$postingList = Array();
	// filters from frontend for archieve workorders
	$client_sql = "";
	$employee = "";
	$client_filter_sql = "";
	//$project_filter_sql = "";
	$status_filter_sql = "";
	$assigned_to_filter_sql = "";
	$requestedby_filter_sql = "";
	$request_type_filter_sql = "";
	$date_range_filter_sql = "";
	$search_filter_sql = "";
	$page_number_filter_sql = "";
	$column_filter_sql = "";   // for sorting based on assigned_to or requested by 
														  // query for filters start
	$type = '';

	$user_id = $_SESSION['user_id'];
	
	$companySql = "SELECT company_id FROM users_companies WHERE user_id = ".$user_id;
	$company_result = $mysql->sqlordie($companySql);
	$company_row_array = array();
	if($company_result->num_rows > 0){
		while($company_row = $company_result->fetch_assoc()){
			$company_row_array[] = $company_row['company_id'];
		
		}
	}
	if(count($company_row_array) > 0){
		$company_ids = implode(",",$company_row_array);
	
	}
	
    $workorder_list_query = "SELECT w.*, c.name as company_name FROM workorders w INNER JOIN companies c ON (c.id=w.company_id) WHERE company_id IN ($company_ids) AND w.active='1' AND w.deleted ='0' AND w.archived='0' ORDER BY w.`company_id`,  w.`title` ASC";
	$workorder_result = $mysql->sqlordie($workorder_list_query) ;
	$i=-1;

	$wo_status_array = array();
	$wo_user_list = array();
	$wo_last_comment_array = array();
	
	if($workorder_result->num_rows > 0) {

	$select_wo_status = "SELECT `id`, `name` FROM `lnk_workorder_status_types`";
	$status_result = $mysql->sqlordie($select_wo_status);
	if($status_result->num_rows > 0){
		while($status_row = $status_result->fetch_assoc()){
			$wo_status_array[$status_row['id']] = $status_row['name'];
		}
	}

	$wo_last_comment = "SELECT wc.`id`,wc.`workorder_id`,wc.`user_id`,wc.`comment`,wc.`date` FROM `workorder_comments` wc, (select max(id) id from `workorder_comments` WHERE deleted ='0' group by workorder_id ) tab1,workorders b where wc.id=tab1.id and b.id = wc.workorder_id  AND b.deleted ='0' $project_archive";
	
	$wo_last_comment_result = $mysql->sqlordie($wo_last_comment);

	if($wo_last_comment_result->num_rows > 0){
		while($last_comment_row = $wo_last_comment_result->fetch_assoc()){
			$wo_last_comment_array[$last_comment_row['workorder_id']] = $last_comment_row;
		}
	}

	$company_query = "SELECT * FROM `companies` id  IN ($company_ids)";
	$company_result = $mysql->sqlordie($company_query);
	$companyListArr;

	if($company_result->num_rows > 0){
		while($company_row = $company_result->fetch_assoc()){
			$companyListArr[$company_row['id']] = $company_row['name'];
		}
	}

	$custom_feild_arr;
	$request_type_arr = array("Submit a Request" => "Request", "Report a Problem" => "Problem","Report an Outage" => "Outage");

	$wo_custom_data = $mysql->sqlordie("SELECT `workorder_id`,a.`field_key`,a.`field_id`,c.`field_name` FROM `workorder_custom_fields` a,`workorders` b,`lnk_custom_fields_value` c where a.`workorder_id`=b.`id` and (a.`field_key`='REQ_TYPE' OR a.`field_key`='SEVERITY') and a.`field_id`= c.`field_id` $project_archive");

	while($row = $wo_custom_data->fetch_assoc()){
		$custom_feild_arr[$row['workorder_id']][$row['field_key']] = $row['field_name'];
	}

	$previous_compnay_id = '';
	$previous_key = '';
	$i = -1;
	while($workorder_row = $workorder_result->fetch_assoc()) {
		//print_r($workorder_row); die;
		if($workorder_row['company_id'] != $previous_compnay_id){
			$previous_compnay_id = $workorder_row['company_id'];
			$previous_key = $i;
			$i=$i+1;
		}
		//echo $i;
		if(!array_key_exists($i, $postingList)){
			$postingList[$i] = Array();
		}

		//$postingList[$i]['project_name'] = $workorder_row['project_name'];
		//$postingList[$i]['project_code'] = $workorder_row['project_code'];
		//$postingList[$i]['project_id'] = $workorder_row['project_id'];
		$postingList[$i]['company_name'] = $workorder_row['company_name'];
		$postingList[$i]['client'] = $workorder_row['company_id'];
		if(!array_key_exists('workorders', $postingList[$i]))
			$postingList[$i]['workorders'] = Array();
		if(!array_key_exists($workorder_row['requested_by'], $wo_user_list)){
			$select_wo_requested_by = "SELECT * FROM `users` WHERE `id`= ?";
			$requested_result = $mysql->sqlprepare($select_wo_requested_by, array($workorder_row['requested_by']));
			//if($requested_result->num_rows > 0){
			$requested_row = $requested_result->fetch_assoc();
			$userName = '';
			if(!empty($requested_row['last_name'])){
				$userName  = $requested_row['last_name'] .", " .$requested_row['first_name'];
			}else{
				$userName  = $requested_row['first_name'];
			}
			$wo_user_list[$workorder_row['requested_by']] = $userName;
		}
		if(!array_key_exists($workorder_row['assigned_to'], $wo_user_list)){
			$select_wo_assigned_to = "SELECT * FROM `users` WHERE `id`= ? ";
			$assigned_result = $mysql->sqlprepare($select_wo_assigned_to,array($workorder_row['assigned_to']));
			$assigned_row = $assigned_result->fetch_assoc();
			$userName ='';
			if(!empty($assigned_row['last_name'])){
				$userName  = $assigned_row['last_name'] .", " .$assigned_row['first_name'];
			}else{
				$userName  = $assigned_row['first_name'];
			}

			$wo_user_list[$workorder_row['assigned_to']] = $userName;
		}


		$overdue_flag = '0';
		$dlClass = '';		
		if($workorder_row['status'] == 3 || $workorder_row['status'] == 1) {
			$dlClass = 'complete';
		} else {
			if($workorder_row['status'] == 6) {
				$dlClass = 'new';
			}
			if(!empty($workorder_row['launch_date'])){
				$launch_date =  strtotime($workorder_row['launch_date']);
				$current_date =  strtotime("now");
				if(date("Y-m-d",$current_date) > date("Y-m-d",$launch_date))
				{
					$overdue_flag = '1';
					$dlClass = 'alert';
				}       
			}
		}

        	
		$REQ_TYPE = 'N/A';
		if(!empty($custom_feild_arr[$workorder_row['id']]['REQ_TYPE']))
		{
			$REQ_TYPE = $custom_feild_arr[$workorder_row['id']]['REQ_TYPE'];
			$REQ_TYPE = $request_type_arr[$REQ_TYPE];
		}

		$WO_SEVERITY = 'N/A';
		if(!empty($custom_feild_arr[$workorder_row['id']]['SEVERITY']))
		{
			$WO_SEVERITY = $custom_feild_arr[$workorder_row['id']]['SEVERITY'];
		}

		$requested = $wo_user_list[$workorder_row['requested_by']];
		$assigned = $wo_user_list[$workorder_row['assigned_to']];
        	
		if(strlen($workorder_row['title']) > 40) {
			$elipse = "...";
		} else {
			$elipse = "";
		}

		$last_comment_row =$wo_last_comment_array[$workorder_row['id']] ;

		if(!empty($last_comment_row)){
			if(!empty($last_comment_row['workorder_id'])){
				$wo_last_comment  = $last_comment_row['comment'] ;
				$wo_last_comment_user_id  = $last_comment_row['user_id'] ;
				$wo_last_comment_date  = $last_comment_row['date'] ;
			}else{
				$wo_last_comment  = 'N/A';
				$wo_last_comment_user_id  = 'N/A';
				$wo_last_comment_date  = 'N/A';
				$wo_last_comment_user = 'N/A';
			}

			if(!array_key_exists($wo_last_comment_user_id, $wo_user_list)){
				$select_wo_last_comment = "SELECT * FROM `users` WHERE `id`= ? ";
				$last_comment_result = $mysql->sqlprepare($select_wo_last_comment,array($wo_last_comment_user_id) );
				$last_comment_row = $last_comment_result->fetch_assoc();

				$userName ='';
				if(!empty($last_comment_row['last_name'])){
				$userName  = $last_comment_row['last_name'] .", " .$last_comment_row['first_name'];
				}else{
				$userName  = $last_comment_row['first_name'];
				}
				$wo_last_comment_user = $userName;
			}else{
				$wo_last_comment_user = $wo_user_list[$wo_last_comment_user_id];
			}
		}else{
			$wo_last_comment  = 'N/A';
			$wo_last_comment_user_id  = 'N/A';
			$wo_last_comment_date  = 'N/A';
			$wo_last_comment_user = 'N/A';
		}
		
		$woCompletedDate = "N/A";
		if(!empty($workorder_row['completed_date'])){
			$woCompletedDate = $workorder_row['completed_date'];
		}
	    if(!empty($workorder_row['estimated_date'])){
			$woEastimateDate = $workorder_row['estimated_date'];
		}

		if(!empty($workorder_row['estimated_date'])){
			$woEstimatedDate = format_date($workorder_row['estimated_date']);
		}else{
			$woEstimatedDate = 'N/A';
		}
		/**
		 * Ticket No 16857,19352
		 * Special Character display 
		 * @var test Comment type
		 */
		array_push($postingList[$i]['workorders'],
		Array('id' => $workorder_row['id'], 
		'title' => htmlentities(substr($workorder_row['title'], 0, 37).$elipse,ENT_NOQUOTES,'UTF-8'),
		'full_title' => htmlentities($workorder_row['title'],ENT_QUOTES,'UTF-8'),
		'status' => $wo_status_array[$workorder_row['status']],
		'status_id' => $workorder_row['status'],
		'req_type' => $REQ_TYPE,'severity'=>$WO_SEVERITY,
		'requested_by' => $requested,
		'requested_by_id' => $workorder_row['requested_by'],
		'assigned_to' => $assigned,
		'assigned_to_id' => $workorder_row['assigned_to'],
		'open_date' => format_date($workorder_row['creation_date']), 
		'assigned_date' => format_date($workorder_row['assigned_date']), 
		'completed' => format_date($woCompletedDate),
		'launch_date' => format_date($workorder_row['launch_date']), 
		'estimated_date' => format_date($woEastimateDate), 
		'class' => $dlClass,
		'overdue_flag' => $overdue_flag,
		'wo_last_comment'=> nl2br((htmlentities($wo_last_comment,ENT_NOQUOTES,'UTF-8'))),
		'wo_last_comment_user_id'=>$wo_last_comment_user_id,
		'wo_last_comment_user'=>$wo_last_comment_user ,
		'wo_last_comment_date'=>format_date($wo_last_comment_date),
		'company_name'=>$workorder_row['company_name'],
		'company_id'=>$workorder_row['company_id']));
		
		//array_push($postingList[$i]['workorders'],Array('id' => $workorder_row['id'], 'title' => htmlentities(substr($workorder_row['title'], 0, 37).$elipse), 'full_title' => htmlentities($workorder_row['title']), 'status' => $wo_status_array[$workorder_row['status']],'status_id' => $workorder_row['status'], 'req_type' => $REQ_TYPE,'severity'=>$WO_SEVERITY, 'requested_by' => $requested, 'requested_by_id' => $workorder_row['requested_by'], 'assigned_to' => $assigned,'assigned_to_id' => $workorder_row['assigned_to'],'open_date' => format_date($workorder_row['creation_date']), 'assigned_date' => format_date($workorder_row['assigned_date']), 'completed' => format_date($woCompletedDate),'launch_date' => format_date($workorder_row['launch_date']), 'estimated_date' => format_date($woEastimateDate), 'class' => $dlClass, 'overdue_flag' => $overdue_flag,'wo_last_comment'=> nl2br(htmlentities($wo_last_comment)),'wo_last_comment_user_id'=>$wo_last_comment_user_id,'wo_last_comment_user'=>$wo_last_comment_user ,'wo_last_comment_date'=>format_date($wo_last_comment_date)));
//	echo count($postingList);
	//print "<pre>";
	//print_r($postingList);
  }
}

	
	
}

$jsonSettings = json_encode($postingList);

  // output correct header
  $isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ?
  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
  ($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');
  	
  echo $jsonSettings;


function format_date($date){
	if($date != 'N/A'){
		$str_date = strtotime($date);
		return Date('Y-m-d h:i:s A', $str_date);
	}else{
		return $date;
	}
}



?>
