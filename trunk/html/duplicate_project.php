<?PHP
	include("_inc/config.inc");
	//include(WEBPATH ."_ajaxphp/create_bc_project.php");
	
	$link = mysql_connect('localhost', 'generic', 'generic');
	mysql_select_db('nbc_lighthouse', $link);
	
	$id = mysql_real_escape_string($_POST['project_id']);
	//New data
	
	$copy_project_query = "SELECT * FROM `projects` WHERE `id`='$id' LIMIT 1";
	$copy_project_res = mysql_query($copy_project_query);
	
	if($copy_project_res) {
		$_GET['comp'] = $client;
		$_GET['code'] = $code;
		$_GET['name'] = $name;
		
		include('_ajaxphp/create_bc_project.php');
		
		$client = mysql_real_escape_string($_POST['project_client']);
		$code = mysql_real_escape_string($_POST['project_code']);
		$name = mysql_real_escape_string($_POST['project_name']);
		
		$project_copy = mysql_fetch_assoc($copy_project_res);
		//$cookieFile = AUTH_SITE_COOKIE_STORE($LOGINURL,$POSTFIELDS);
		//SUBMIT_FORM($GETURL,$cookieFile,$data);
		//Get bc id and category id for project from base camp
		$create_new_project_query = "INSERT INTO `projects` "
		."(`project_code`,`budget_code`,`project_name`,`company`,`desc`,`business_case`,"
		."`scope`,`deliverables`,`metrics_tracking`) "
		."VALUES "
		."('$code', '" .$project_copy['budget_code'] ."','$name','$client',"
		."'" .$project_copy['desc'] ."','" .$project_copy['business_case'] ."','" .$project_copy['scope'] 
		."','" .$project_copy['deliverables'] ."','" .$project_copy['metrics_tracking'] ."')";
		
		$create_new_res = mysql_query($create_new_project_query);
		$new_id = mysql_insert_id();
		
		echo mysql_error();
		
		//Copy roles data
		if($create_new_res) {
			$select_project_roles = "SELECT * FROM `project_roles` WHERE `project_id`='$id'";
			$project_roles_res = mysql_query($select_project_roles);
			
			if(mysql_num_rows($project_roles_res) > 0) {
				while($roles_row = mysql_fetch_assoc($project_roles_res)) {
					$insert_role = "INSERT INTO `project_roles` "
					."(`project_id`,`resource_type_id`,`user_id`,`email`,`phone`,`sort_order`) "
					."VALUES "
					."('$new_id','" .$roles_row['resource_type_id'] ."','" .$roles_row['user_id'] 
					."','" .$roles_row['email'] ."','" .$roles_row['phone'] ."','" .$roles_row['sort_order'] ."')";
					
					mysql_query($insert_role);
				}
			}
		}
		
		//Copy finance data
		if($create_new_res) {
			$select_project_finance = "SELECT * FROM `project_phase_finance` WHERE `project_id`='$id'";
			$project_finance_res = mysql_query($select_project_finance);
			
			if(mysql_num_rows($project_finance_res) > 0) {
				while($finrow = mysql_fetch_assoc($project_finance_res)) {
					$insert_fin = "INSERT INTO `project_phase_finance` "
					."(`project_id`,`phase`,`hours`,`rate`,`creation_date`,`assigned_date`,`completed_date`,`closed_date`) "
					."VALUES "
					."('$new_id','" .$finrow['phase'] ."','" .$finrow['hours'] ."','" .$finrow['rate'] 
					."','" .$finrow['creation_date'] ."','" .$finrow['assigned_date'] ."','" .$finrow['completed_date'] 
					."','" .$finrow['closed_date'] ."')";
					
					mysql_query($insert_fin);
				}
			}
		}
		
		//Copy phase approvals data
		if($create_new_res) {
			$select_project_appr = "SELECT * FROM `project_phase_approvals` WHERE `project_id`='$id'";
			$project_appr_res = mysql_query($select_project_appr);
			
			if(mysql_num_rows($project_appr_res) > 0) {
				while($appr_row = mysql_fetch_assoc($project_appr_res)) {
					$insert_appr = "INSERT INTO `project_phase_approvals` "
					."(`project_id`,`project_phase`,`name`,`title`,`phone`,`desc`,`approval_date`,`approved`,`non_phase`) "
					."VALUES "
					."('$new_id','" .$appr_row['project_phase'] ."','" .$appr_row['name'] ."','" .$appr_row['title'] 
					."','" .$appr_row['phone'] ."','" .$appr_row['desc'] ."','" .$appr_row['approval_date'] 
					."','" .$appr_row['approved'] ."', '" .$appr_row['non_phase'] ."')";
					
					mysql_query($insert_appr);
				}
				echo mysql_error();
			}
		}
		
		//Copy phases data
		if($create_new_res) {
			$select_project_phases = "SELECT * FROM `project_phases` WHERE `project_id`='$id'";
			$project_phase_res = mysql_query($select_project_phases);
			
			if(mysql_num_rows($project_phase_res) > 0) {
				while($phase_row = mysql_fetch_assoc($project_phase_res)) {
					$insert_phase = "INSERT INTO `project_phases` "
					."(`project_id`,`phase_type`,`name`,`desc`,`finance_flag`,`approval_flag`,`start_Date`,`projected_end_date`) "
					."VALUES "
					."('$new_id','" .$phase_row['phase_type'] ."','" .$phase_row['name'] ."','" .$phase_row['desc'] 
					."','" .$phase_row['finance_flag'] ."','" .$phase_row['approval_flag'] ."','" 
					.$phase_row['start_date'] ."','" .$phase_row['projected_end_date'] ."')";
					
					mysql_query($insert_phase);
				}
			}
		}
		
		//Copy phase approvals data
		if($create_new_res) {
			$select_project_brief = "SELECT * FROM `project_brief_sections` WHERE `project_id`='$id'";
			$project_brief_res = mysql_query($select_project_brief);
			
			if(mysql_num_rows($project_brief_res) > 0) {
				while($brief_row = mysql_fetch_assoc($project_brief_res)) {
					$insert_brief = "INSERT INTO `project_brief_sections` "
					."(`project_id`,`desc`,`section_type`,`flag`) "
					."VALUES "
					."('$new_id','" .$brief_row['desc'] ."','" .$brief_row['section_type'] ."','2')";
					
					mysql_query($insert_brief);
				}
			}
		}
		
		header("Location: /controltower/index/edit/?project_id=$new_id");
		
	} else {
		header("Location: /controltower/");
	}
?>