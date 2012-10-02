<?PHP
	include("../_inc/config.inc");
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql;	 

	if(isset($_REQUEST['ct_status']) && isset($_REQUEST['wo_status']) && isset($_REQUEST['quality_status']) && isset($_REQUEST['admin_select_user'])){
		$ct_status = $_REQUEST['ct_status'];
		$wo_status = $_REQUEST['wo_status'];
		$quality_status = $_REQUEST['quality_status'];
		$admin_selected_user = $_REQUEST['admin_select_user'];    
		if($ct_status == "true"){
			$ct_status = '1';
		}else{
			$ct_status = '0';
		}
		if($wo_status == "true"){
			$wo_status = '1';
		}else{
			$wo_status = '0';
		}
		if($quality_status == "true"){
			$quality_status = '1';
		}else{
			$quality_status = '0';
		}
		if(isset($_REQUEST['all_project_access'])){		
      if($_REQUEST['all_project_access'] == "true"){
        updateUserTable($admin_selected_user,$wo_status,$quality_status,$ct_status,$mysql);
      }
    } else if(isset($_REQUEST['check']) || isset($_REQUEST['uncheck'])){ 
  		if(!empty($_REQUEST['check'])){
  			$checkArray = explode(",",$_REQUEST['check']);      
  			updateDB($checkArray,$admin_selected_user,$wo_status,$quality_status,$ct_status,'1',$mysql);
  		}
  		if(!empty($_REQUEST['uncheck'])){
  			$unCheckArray = explode(",",$_REQUEST['uncheck']);     
  			updateDB($unCheckArray,$admin_selected_user,$wo_status,$quality_status,$ct_status,'0',$mysql);
  		}
  	}
  	echo "Project permissions updated successfully.";
	}

function updateDB($project_list,$admin_selected_user,$wo_status,$quality_status,$ct_status,$value,$mysql) {
	$project_list=array_unique($project_list); 
	$project_list_String = implode(",",$project_list);
	$insert_project_perms_list = array();
	$insert_project_array = array();
	$update_project_array = array();
	$select_existing_records_sql = "SELECT project_id from user_project_permissions where user_id={$admin_selected_user} and project_id in ({$project_list_String})";
	$existing_project_list = $mysql->sqlordie($select_existing_records_sql);
	if($existing_project_list->num_rows>0){
		while($row = $existing_project_list->fetch_assoc()){
			$update_project_array[] = $row['project_id'];
		}  
	}  
	$insert_project_array = array_diff($project_list,$update_project_array);
	$insert_project_array = array_values($insert_project_array);
  
	if($ct_status == "1"){
		$update_query = $update_query."control_tower = '".$value."',";
	}
	if($wo_status == "1"){
		$update_query = $update_query."workorders = '".$value."',";
	}
	if($quality_status == "1"){
		$update_query = $update_query."quality = '".$value."',";
	}
	
	$comma_separated_project_list = implode(",", $update_project_array);
	$query_update = "UPDATE `user_project_permissions` set {$update_query}`id`=`id` WHERE user_id = '{$admin_selected_user}' AND project_id IN({$comma_separated_project_list})";
	
	$mysql->sqlordie($query_update);

	if(count($insert_project_array)>0) {
		$query_insert = "INSERT INTO `user_project_permissions` (`id`,`user_id`,`project_id`,`active`,`deleted`,`workorders`,`quality`,`control_tower`) VALUES "; 
		foreach($insert_project_array as $key=>$project_id){
			$query_insert =$query_insert." (' ','{$admin_selected_user}','{$project_id}','1','0','{$wo_status}','{$quality_status}','{$ct_status}') ,";  
		}
		$query_insert = substr($query_insert, 0, -1);
		$mysql->sqlordie($query_insert);    
	}
}

function updateUserTable($admin_selected_user,$wo_status,$quality_status,$ct_status,$mysql){

	$users_permission_sql = "UPDATE `users` SET `control_tower` = '{$ct_status}' , `workorders` = '{$wo_status}' , `quality` = '{$quality_status}' WHERE `id` = '{$admin_selected_user}'";
	$mysql->sqlordie($users_permission_sql);
}
?>

