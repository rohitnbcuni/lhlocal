<?php
	include("../_inc/config.inc");
	$filter_query="";

	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	if($_REQUEST['ct_status'] == "false" && $_REQUEST['wo_status'] == "false" && $_REQUEST['quality_status'] == "false"){
		$filter_query=" 1 = 2 AND ";
	} else {
		if($_REQUEST['ct_status'] == "true"){
			$filter_query .= "control_tower = '1' AND ";
		}
		if($_REQUEST['wo_status'] == "true"){
			$filter_query .= "workorders = '1' AND ";
		}
		if($_REQUEST['quality_status'] == "true"){
			$filter_query .= "quality = '1' AND ";
		}
	}
	if($_REQUEST['details_users'] == '1'){
		$checkbox_status_sql = "SELECT `id` FROM `users` WHERE ".$filter_query." `id` = ".$_REQUEST['admin_select_user'];
		$checkbox_value_result = @$mysql->query($checkbox_status_sql); 

		if($checkbox_value_result->num_rows > 0){
			$value = "true";
		} else {
			$value = "false";
		}
		$jsonResponse = json_encode($value);
	} else {
		$checkbox_status_sql = "SELECT p.id FROM user_project_permissions u, projects p WHERE u.user_id = ".$_REQUEST['admin_select_user']." AND p.`year` = YEAR(CURDATE()) AND u.project_id = p.id AND ".$filter_query." p.company = ".$_REQUEST['company_id']."  ORDER BY p.project_name ASC";
	  
		$project_list_result = @$mysql->query($checkbox_status_sql); 
		$project_list = array();
	  
		if($project_list_result->num_rows > 0){
			while($project_list_result_row = $project_list_result->fetch_assoc()){
				$project_list[] = $project_list_result_row['id'];
			}
		}
		$jsonResponse = json_encode($project_list);  
	}
	echo $jsonResponse; 
?>
