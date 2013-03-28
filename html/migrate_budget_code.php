<?php

include("_inc/config.inc");
include("sessionHandler.php");
 
global $mysql;

$create_new_project_query = "SELECT budget_code,id FROM projects";
$create_new_res = $mysql->sqlordie($create_new_project_query);

if($create_new_res->num_rows > 0) {
	while($response = $create_new_res->fetch_assoc()) {
		$budget_update_sql = "UPDATE project_budget SET `budget_code`='".trim($response["budget_code"])."' WHERE `project_id` = ".$response["id"];
		
		//echo $budget_update_sql."\n";
		
		$update_response = $mysql->sqlordie($budget_update_sql);
		$response = "";
	}

	echo "migration finished...";
	//print_r($create_new_res->fetch_assoc());
}

?>