<?
session_start();
if(!(isset($from_action) && $from_action))
include('../_inc/config.inc');
//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
global $mysql;
$postingList = Array();
// filters from frontend for archieve workorders
$client_sql = "";
$employee = "";
$client_filter_sql = "";
$project_filter_sql = "";
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


if(array_key_exists("report", $_GET)){
	$type = $_GET['report'];
}
$archive_sql = " AND b.`archived`='0' and b.`active`='1'";
$project_archive = " AND b.`archived`='0'";
$pjt_sql = " JOIN `projects` a ON a.`id`=b.`project_id`";
if('1' == $_GET['status']){         // for active workorders
	$archive_sql = " AND b.`archived`='0' and b.`active`='1'";
	$project_archive = " AND b.`archived`='0'";
}else if('0' == $_GET['status']){   // for archieved workorders
	$archive_sql = " AND b.`archived`='1' and b.`active`='1'";
	$project_archive = " AND b.`archived`='1'";
	  if(isset($_REQUEST['client']) && $_REQUEST['client'] != '-1'){
    $client_filter_sql = " AND a.`company` = ".$_REQUEST['client'];
  }
  	if(isset($_REQUEST['proj_id']) && $_REQUEST['proj_id'] != '-1'){
  	$project_filter_sql = " AND a.`id` = ".$_REQUEST['proj_id'];
  } 
  	if(isset($_REQUEST['status_filter']) && $_REQUEST['status_filter'] != '-1'){  
    $status_table_sql = "select `id` from `lnk_workorder_status_types` where name = ?";
  	$status_result = $mysql->sqlprepare($status_table_sql, array($_REQUEST['status_filter']));
	if($status_result->num_rows == 1){
       $status_row = $status_result->fetch_assoc();
    }	
    $status_filter_sql = " AND b.`status` = ".$status_row['id'];
  }
  	if(isset($_REQUEST['assigned_to']) && $_REQUEST['assigned_to'] != '-1'){  
    $assigned_to_filter_sql = " AND b.`assigned_to` = ".$_REQUEST['assigned_to'];
  }
  
  if(isset($_REQUEST['requested_by']) && $_REQUEST['requested_by'] != '-1'){  
    $requestedby_filter_sql = " AND b.`requested_by` = ".$_REQUEST['requested_by'];
  }
    
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
      $assigned_to_sort_sql = " JOIN `users` ON b.assigned_to = users.id ";
    } else if($_REQUEST['column'] == 'requested_by'){
      $column = '`users`.`last_name`';
      $requested_by_sort_table_sql = " JOIN `users` ON b.requested_by = users.id ";  
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
      $status_sql = " JOIN `lnk_workorder_status_types` lt ON lt.`id` = b.`status`";
    }
    $column_filter_sql = ",".$column." ".$sort_order;
  }
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
    $workorder_custom_sql = " JOIN `workorder_custom_fields` e ON b.`id` = e.`workorder_id`";
    $req_filter_sql =  " AND e.`field_id` IN(".$request_type_string.")";
  }
  if(isset($_REQUEST['start_date']) && !empty($_REQUEST['start_date']) && isset($_REQUEST['end_date']) && !empty($_REQUEST['end_date'])){  
  //  $date_range_filter_sql = " AND b.`creation_date` > '".date('Y-m-d',strtotime($_REQUEST['start_date']))."' AND b.`creation_date` < '".date('Y-m-d',strtotime($_REQUEST['end_date']))."'";
$date_range_filter_sql = " AND b.`closed_date` >= '".date('Y-m-d',strtotime($_REQUEST['start_date']))." 00:00:00"."' AND b.`closed_date` <= '".date('Y-m-d',strtotime($_REQUEST['end_date']))." 23:59:00"."'";
  
	}
  if(isset($_REQUEST['search']) && !empty($_REQUEST['search'])){
    $search_filter_table_sql = " LEFT JOIN workorder_comments wc ON wc.workorder_id = b.id";
    $search_filter_sql = " AND (`title` like '%".$_REQUEST['search']."%' OR `body` like '%".$_REQUEST['search']."%' OR `example_url` like '%".$_REQUEST['search']."%' OR `comment` like '%".$_REQUEST['search']."%')";
  }
  if(isset($_REQUEST['page_num'])){
    $page_num = intval($_REQUEST['page_num']);
    if($page_num <= 1){
      $page_num = 1;
    }
  } else {
    $page_num = 1;
  }

 $page_number_filter_sql = "";
 if($type != 'excel') {
	 $page_number_filter_sql = " LIMIT ".(($page_num-1)*50).",".'50';//page_size;
  }
  
  $pjt_sql = " JOIN `projects` a ON a.`id`=b.`project_id`";
  
  $count_wo = "select count(distinct b.`id`) as cnt from   `workorders` b " .$pjt_sql.$workorder_custom_sql.$requested_by_sort_table_sql.$search_filter_table_sql.$assigned_to_sort_sql.$req_type_sql.$status_sql.$user_pjt_sql.$where_clause. $archive_sql . $client_filter_sql . $project_filter_sql . $status_filter_sql . $assigned_to_filter_sql .$req_filter_sql. $date_range_filter_sql . $search_filter_sql;
  $count_result = $mysql->sqlordie($count_wo);
  if($count_result->num_rows == 1){
     $count_row = $count_result->fetch_assoc();
  }
  $count = $count_row['cnt']; 
}else if('-1' == $_GET['status']){    // for draft workorders
	$archive_sql = " AND b.`active`='0' AND b.`requested_by`='".$_SESSION['user_id']."'";
}

if(checkUserComapny() == false){
	$user_id = $_SESSION['user_id'];
	$sso_user_sql =  " AND b.requested_by = $user_id";

}else{
	$user_pjt_sql = " JOIN `user_project_permissions` c ON a.`id`=c.`project_id`";
	$where_clause = " WHERE c.`user_id`='" .$_SESSION['user_id']."' ";

}


$distinct_project_list_sql = "select distinct project_name,a.id,project_code from `workorders` b".$pjt_sql.$workorder_custom_sql.$requested_by_sort_table_sql.$search_filter_table_sql.$assigned_to_sort_sql.$req_type_sql.$status_sql.$user_pjt_sql.$where_clause. $archive_sql . $client_sql . $client_filter_sql .$req_filter_sql. $status_filter_sql . $date_range_filter_sql  . "order by `project_code`"; //  $project_filter_sql   $assigned_to_filter_sql  $search_filter_sql
$distinct_project_list_result = $mysql->sqlordie($distinct_project_list_sql);
	if($distinct_project_list_result->num_rows > 0){
		while($project_list_row = $distinct_project_list_result->fetch_assoc()){
		  $project_id_array[] = $project_list_row['id'];
			$project_name_array[] = $project_list_row['project_name'];
			$project_code_array[] = $project_list_row['project_code'];
		}
		$wo_distinct_values_projects = array($project_id_array,$project_code_array,$project_name_array);
	}
//	echo "pjt+".$distinct_project_list_sql;
	
$distinct_assigned_to_sql = "select distinct assigned_to as assigned from `workorders` b".$pjt_sql.$workorder_custom_sql.$requested_by_sort_table_sql.$search_filter_table_sql.$assigned_to_sort_sql.$req_type_sql.$status_sql.$user_pjt_sql.$where_clause. $archive_sql . $client_sql . $client_filter_sql .$req_filter_sql. $status_filter_sql . $date_range_filter_sql ; //  $project_filter_sql $assigned_to_filter_sql  $search_filter_sql
$distinct_assigned_to_sql = "select id,last_name,first_name from (".$distinct_assigned_to_sql.") as assigned_table,users where users.id = assigned order by last_name";
$distinct_assigned_to_sql_result = $mysql->sqlordie($distinct_assigned_to_sql);
	if($distinct_assigned_to_sql_result->num_rows > 0){
		while($assigned_to_row = $distinct_assigned_to_sql_result->fetch_assoc()){
			$assigned_to_user_id_array[] = $assigned_to_row['id'];
			$assigned_to_user_name_array[] = $assigned_to_row['last_name'].",".$assigned_to_row['first_name'];
		}
		$wo_distinct_values_assigned_to = array($assigned_to_user_id_array,$assigned_to_user_name_array);
		}	
//	echo "assign+".$distinct_assigned_to_sql;die();

$distinct_requested_by_sql = "select distinct requested_by as requested_by from `workorders` b".$pjt_sql.$workorder_custom_sql.$requested_by_sort_table_sql.$search_filter_table_sql.$requested_by_sort_sql.$req_type_sql.$status_sql.$user_pjt_sql.$where_clause. $archive_sql . $client_sql . $client_filter_sql .$req_filter_sql. $status_filter_sql . $date_range_filter_sql ; //  $project_filter_sql $assigned_to_filter_sql  $search_filter_sql
$distinct_requested_by_sql = "select id,last_name,first_name from (".$distinct_requested_by_sql.") as assigned_table,users where users.id = requested_by order by last_name";
$distinct_requested_by_sql_result = $mysql->sqlordie($distinct_requested_by_sql);
	if($distinct_requested_by_sql_result->num_rows > 0){
		while($requested_by_row = $distinct_requested_by_sql_result->fetch_assoc()){
			$requested_by_user_id_array[] = $requested_by_row['id'];
			$requested_by_user_name_array[] = $requested_by_row['last_name'].",".$requested_by_row['first_name'];
		}
		
		$wo_distinct_values_requested_by = array($requested_by_user_id_array,$requested_by_user_name_array);
	}	


if('0' == $_GET['status']){ 
  $workorder_list_query = "SELECT distinct b.`id` ,a.`id` AS project_id, a.`project_name` AS project_name, a.`project_code` AS project_code, a.`company` AS project_company , b.`title` , b.`assigned_to` , b.`status` , b.`example_url` , b.`body` , b.`requested_by` , b.`assigned_date` ,b.`estimated_date`, b.`completed_date` , b.`creation_date` , b.`launch_date` , b.`draft_date`,b.`closed_date` FROM  `workorders` b".$pjt_sql.$workorder_custom_sql.$requested_by_sort_table_sql.$search_filter_table_sql.$assigned_to_sort_sql.$req_type_sql.$status_sql.$user_pjt_sql.$where_clause. $archive_sql . $client_filter_sql .$req_filter_sql . $project_filter_sql . $status_filter_sql . $assigned_to_filter_sql . $requestedby_filter_sql .$date_range_filter_sql .$search_filter_sql.$sso_user_sql." ORDER BY a.`company`, a.`project_name`" . $column_filter_sql . $page_number_filter_sql;
} else {
	if(checkUserComapny() == false){
		$workorder_list_query = "SELECT distinct b.`id` ,a.`id` AS project_id, a.`project_name` AS project_name, a.`project_code` AS project_code, a.`company` AS project_company , b.`title` , b.`assigned_to` , b.`status` , b.`example_url` , b.`body` , b.`requested_by` , b.`assigned_date` ,b.`estimated_date`, b.`completed_date` , b.`creation_date` , b.`launch_date` , b.`draft_date`,b.`closed_date` FROM `projects` a INNER JOIN `workorders` b ON (a.`id`=b.`project_id`) WHERE  1  " . $archive_sql .$sso_user_sql. " ORDER BY a.`company`, a.`project_name`, b.`title` ASC";
	}else{	
		$workorder_list_query = "SELECT distinct b.`id` ,a.`id` AS project_id, a.`project_name` AS project_name, a.`project_code` AS project_code, a.`company` AS project_company , b.`title` , b.`assigned_to` , b.`status` , b.`example_url` , b.`body` , b.`requested_by` , b.`assigned_date` ,b.`estimated_date`, b.`completed_date` , b.`creation_date` , b.`launch_date` , b.`draft_date`,b.`closed_date` FROM `projects` a, `workorders` b, `user_project_permissions` c WHERE a.`id`=b.`project_id` AND a.`id`=c.`project_id` AND c.`user_id`='" .$_SESSION['user_id'] ."'  " . $archive_sql . " ORDER BY a.`company`, a.`project_name`, b.`title` ASC";
	}
}	

//echo "qry".$workorder_list_query;
$workorder_result = $mysql->sqlordie($workorder_list_query);
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

	$company_query = "SELECT * FROM `companies`";
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

	$previous_project_id = '';
	$previous_key = '';
	while($workorder_row = $workorder_result->fetch_assoc()) {

		if($workorder_row['project_id'] != $previous_project_id){
			$previous_project_id = $workorder_row['project_id'];
			$previous_key = $i;
			$i=$i+1;
		}

		if(!array_key_exists($i, $postingList)){
			$postingList[$i] = Array();
		}

		$postingList[$i]['project_name'] = $workorder_row['project_name'];
		$postingList[$i]['project_code'] = $workorder_row['project_code'];
		$postingList[$i]['project_id'] = $workorder_row['project_id'];
		$postingList[$i]['company_name'] = $companyListArr[$workorder_row['project_company']];
		$postingList[$i]['client'] = $workorder_row['project_company'];
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
		array_push($postingList[$i]['workorders'],Array('id' => $workorder_row['id'], 'title' => htmlentities(substr($workorder_row['title'], 0, 37).$elipse,ENT_NOQUOTES,'UTF-8'), 'full_title' => htmlentities($workorder_row['title'],ENT_QUOTES,'UTF-8'), 'status' => $wo_status_array[$workorder_row['status']],'status_id' => $workorder_row['status'], 'req_type' => $REQ_TYPE,'severity'=>$WO_SEVERITY, 'requested_by' => $requested, 'requested_by_id' => $workorder_row['requested_by'], 'assigned_to' => $assigned,'assigned_to_id' => $workorder_row['assigned_to'],'open_date' => format_date($workorder_row['creation_date']), 'assigned_date' => format_date($workorder_row['assigned_date']), 'completed' => format_date($woCompletedDate),'launch_date' => format_date($workorder_row['launch_date']), 'estimated_date' => format_date($woEastimateDate), 'class' => $dlClass, 'overdue_flag' => $overdue_flag,'wo_last_comment'=> nl2br((htmlentities($wo_last_comment,ENT_NOQUOTES,'UTF-8'))),'wo_last_comment_user_id'=>$wo_last_comment_user_id,'wo_last_comment_user'=>$wo_last_comment_user ,'wo_last_comment_date'=>format_date($wo_last_comment_date)));
		//array_push($postingList[$i]['workorders'],Array('id' => $workorder_row['id'], 'title' => htmlentities(substr($workorder_row['title'], 0, 37).$elipse), 'full_title' => htmlentities($workorder_row['title']), 'status' => $wo_status_array[$workorder_row['status']],'status_id' => $workorder_row['status'], 'req_type' => $REQ_TYPE,'severity'=>$WO_SEVERITY, 'requested_by' => $requested, 'requested_by_id' => $workorder_row['requested_by'], 'assigned_to' => $assigned,'assigned_to_id' => $workorder_row['assigned_to'],'open_date' => format_date($workorder_row['creation_date']), 'assigned_date' => format_date($workorder_row['assigned_date']), 'completed' => format_date($woCompletedDate),'launch_date' => format_date($workorder_row['launch_date']), 'estimated_date' => format_date($woEastimateDate), 'class' => $dlClass, 'overdue_flag' => $overdue_flag,'wo_last_comment'=> nl2br(htmlentities($wo_last_comment)),'wo_last_comment_user_id'=>$wo_last_comment_user_id,'wo_last_comment_user'=>$wo_last_comment_user ,'wo_last_comment_date'=>format_date($wo_last_comment_date)));
//	echo count($postingList);
  }
}
if('0' == $_GET['status']){
//	print("<pre>");
//	var_dump($wo_distinct_values);
//	echo "qry1".$distinct_project_list_sql."<br>";
//	echo "qry2".$distinct_assigned_to_sql."<br>";
//	print("</pre>");die();
  $postingList = array($postingList,$count,$wo_distinct_values_projects,$wo_distinct_values_assigned_to,$wo_distinct_values_requested_by);
}  

function format_date($date){
	if($date != 'N/A'){
		$str_date = strtotime($date);
		return Date('Y-m-d h:i:s A', $str_date);
	}else{
		return $date;
	}
}

if(isset($from_action) && $from_action){
  //		return $postingList;
}else if($type == 'excel'){
   
  $header = "Id\t Title\t Project\t Company\t Request Type\t Severity\t Status\t Requested By\t Assigned To\t Open Date\t Assigned Date\t Completed Date\t Due Date\t Estimate Date\t Last Commented By\t Last Commented Date\n";
  $excel_body = '';
  $lhwoValue = $_COOKIE['lighthouse_wo_data'];
  $lhwoArray = explode('~',$lhwoValue);
  $clientId = $lhwoArray[0];
  $projectId = $lhwoArray[1];
  $statusId = $lhwoArray[2];
  $assignedTo = $lhwoArray[3];
  $request_type = $lhwoArray[4];

  $req_Type_Arr = Array();
  $statusActiveArray = Array();
  if(!empty($request_type))
  {
	  $requestTypeFilter_all = explode(",", @$request_type);
	  for($u = 0; $u < sizeof($requestTypeFilter_all); $u++) {
	  	  if(!empty($requestTypeFilter_all[$u]))
		  {
		 	  $req_Type_Arr[$requestTypeFilter_all[$u]] = $requestTypeFilter_all[$u];
		  }
	  }
  }


	if($statusId=='99')
	{
		$statusActiveArray["Feedback Provided"] = "Feedback Provided";
		$statusActiveArray["On Hold"] = "On Hold";
		$statusActiveArray["In Progress"] = "In Progress";
		$statusActiveArray["Need More Info"] = "Need More Info";
		$statusActiveArray["New"] = "New";
		$statusActiveArray["Rejected"] = "Rejected";
		$statusActiveArray["Reopened"] = "Reopened";
	}else{
		$statusActiveArray[$statusId] = $statusId;
	}
	if('0' == $_GET['status']){
		$projectList = $postingList[0];
	} else {
		$projectList = $postingList;
	}
  foreach($projectList as $project){

    if(($clientId < 0 || $clientId == $project['client']) && ($projectId < 0 || $projectId == $project['project_id'])){
      foreach($project['workorders'] as $wo){
         if((($statusActiveArray[$statusId] < 0 || $statusActiveArray[$wo['status']] == $wo['status']) || ($statusId =='over_due' && $wo['overdue_flag'] =='1' ) ) && ($assignedTo < 0 || $assignedTo == $wo['assigned_to_id']) && ($req_Type_Arr[$wo['req_type']]==$wo['req_type'])){
		  if($wo['req_type'] == 'Problem'){
			$severity = $wo['severity'];
		  } else {
			$severity = 'N/A';
		  }
    
		  $excel_body .=  $wo['id'] . "\t " .
          $wo['full_title'] . "\t " .
          $project['project_code'] . " : " . $project['project_name'] . "\t " .
          $project['company_name'] . "\t " .
          $wo['req_type'] . "\t " .
          $severity . "\t " .     
          $wo['status'] . "\t " .
          $wo['requested_by'] . "\t " .
          $wo['assigned_to'] . "\t " .
          $wo['open_date'] . "\t " .
          $wo['assigned_date'] . "\t " .
          $wo['completed'] . "\t".
          $wo['launch_date'] . "\t".
		  $wo['estimated_date'] . "\t".
          $wo['wo_last_comment_user'] . "\t".
          $wo['wo_last_comment_date'] . "\n";
        }
      }
    }
  }
//  Header("Content-Disposition: attachment; filename=wo_report.xls");

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
   header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment;filename=wo_report.xls"); 
    header("Content-Transfer-Encoding: binary ");
  echo $header;
  echo $excel_body;

}else{
  $jsonSettings = json_encode($postingList);

  // output correct header
  $isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ?
  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
  ($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');
  	
  echo $jsonSettings;
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


?>
