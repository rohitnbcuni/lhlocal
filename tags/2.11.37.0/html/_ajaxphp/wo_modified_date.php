<?php



	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$woId = $mysql->real_escape_string($_POST['woId']);
	 $page_modified_date = trim($mysql->real_escape_string($_POST['page_modified_date']));
	
	if(!empty($woId)){
		$wo_status_query = "SELECT modified_date FROM `workorders` WHERE id = '$woId'"; 
		
		$wo_status_result = $mysql->sqlordie($wo_status_query);
		//echo $wo_status_result->num_rows;
		if($wo_status_result->num_rows > 0){
			$row = $wo_status_result->fetch_assoc() ;
			//print_r($row);
			$wo_last_modifed = trim(strtotime($row['modified_date']));
			if($page_modified_date === $wo_last_modifed){
			
				echo "ok";
			}else{
			
				echo "error";
			}
		}
	}
?>