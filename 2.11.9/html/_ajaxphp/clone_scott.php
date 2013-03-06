<?PHP
    ini_set('max_execution_time', 0);
	include('../_inc/config.inc');
	
	ini_set("display_errors",1);
	
	
	//include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, 'lhdev_live2' , DB_PORT);	
	/*$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//global $mysql;
	$project_all = "SELECT * FROM `projects` WHERE  id = '18259' ";
	$project_list = $mysql->query($project_all) or writeLog($mysql,$project_all);
	
	$current_year = '2013';//'2010';//date("Y");
	if($project_list->num_rows > 0) {
	echo "<center> <b> CLONE PROJECTS <b></center>";
	echo "<br>List of Projects Cloned : ";
	echo "<br>----------------------------------------------------------<br>";
	$i=0;
	while($row = $project_list->fetch_assoc()) {
		
		$clone_project_id = $row['id'];
		$newProject = "INSERT INTO `projects` (`bc_id`,`project_code`,`bc_category_id`,`budget_code`,`project_name`,`company`,`desc`, `business_case`,`scope`,`deliverables`,`owner_approval`,`company_approval`,`metrics_tracking`,`archived`,`active`,`deleted`, `rp_permission`,`project_status`,`wo_permission`,`internal_groups`,`YEAR`,`clone_project_id`,`cclist`,`qccclist`,`program`, `qa_permission`) VALUES ('".$row['bc_id']."','OPS000.01e','".$row['bc_category_id']."','".$mysql->real_escape_string($row['budget_code'])."','(Program) Publishing Classic','".$mysql->real_escape_string($row['company'])."','".$mysql->real_escape_string($row['desc'])."','".$mysql->real_escape_string($row['business_case'])."','".$mysql->real_escape_string($row['scope'])."','".$mysql->real_escape_string($row['deliverables'])."','".$mysql->real_escape_string($row['owner_approval'])."','".$mysql->real_escape_string($row['company_approval'])."','".$mysql->real_escape_string($row['metrics_tracking'])."','".$row['archived']."','".$row['active']."','".$row['deleted']."','".$row['rp_permission']."','".$row['project_status']."','".$row['wo_permission']."','".$row['internal_groups']."','".$current_year."','".$clone_project_id."','".$row['cclist']."','".$row['qccclist']."','".$row['program']."','".$row['qa_permission']."')";

		//die;
		$mysql->query($newProject) or writeLog($mysql,$newProject);
		$newProjectID = $mysql->insert_id;
		
		if($newProjectID!=$clone_project_id && $newProjectID >0 )

		{  // echo $i++;
			if($row['archived'] =='0'){
			echo $i++;
			echo ".| project ID : ". $clone_project_id;
			echo " | Project Name : ". $row['project_name'];
			echo " | company	   : ". $row['company'];
			echo " | Active	   : ". $row['active'];
			echo " | Archived	   : ". $row['archived'];
			echo " | project Code : ". $row['project_code'];
			echo " | New Project ID : ". $newProjectID.'<br>';
			}

			//project_brief_sections
			$proj_brief_sec = "SELECT id FROM `project_brief_sections` where project_id = '".$clone_project_id."'";
			$proj_brief_sec_sql = $mysql->query($proj_brief_sec) or writeLog($mysql,$proj_brief_sec);
			if($proj_brief_sec_sql->num_rows > 0){
				while($rows = $proj_brief_sec_sql->fetch_assoc()) {
					$proj_brief_new_sec = "INSERT INTO `project_brief_sections` (`project_id` ,`desc`, `section_type`, `flag`, `active`, `deleted`) SELECT '".$newProjectID."' as `project_id`, `desc`, `section_type`, `flag`, `active`, `deleted` FROM `project_brief_sections` where id='".$rows['id']."' AND project_id = '".$clone_project_id."'";
					$mysql->query($proj_brief_new_sec) or writeLog($mysql,$proj_brief_new_sec);
				}
			}
			//end project_brief_sections
			
			//project budget
			$insert_budget="SELECT '".$newProjectID."' as `project_id`, total_budget  , quarter1_budget, quarter2_budget, quarter3_budget, quarter4_budget from project_budget where project_id = '".$clone_project_id."'";
			$insert_budget_sql = $mysql->query($insert_budget) or writeLog($mysql,$insert_budget);
			if($insert_budget_sql->num_rows > 0){
			while($rows1 = $insert_budget_sql->fetch_assoc()) {
				$project_budget = "INSERT INTO project_budget  (project_id  , total_budget  , quarter1_budget, quarter2_budget, quarter3_budget, quarter4_budget) VALUES ('".$rows1['project_id']."','".$rows1['total_budget']."','".$rows1['quarter1_budget']."','".$rows1['quarter2_budget']."','".$rows1['quarter3_budget']."','".$rows1['quarter4_budget']."') ";
				$mysql->query($project_budget) or writeLog($mysql,$project_budget);
				}
			}
			//end project budget
			
			//project_phase
			$project_phase = "SELECT * FROM project_phases WHERE project_id = '".$clone_project_id."'";
			$project_phase_sql = $mysql->query($project_phase) or writeLog($mysql,$project_phase); 
			if($project_phase_sql->num_rows > 0){
				while($rows = @$project_phase_sql->fetch_assoc()) {
				//Insert new entry in project phase
				$project_new_phase = "INSERT INTO project_phases (`project_id`, `phase_type`, `name`, `desc`, finance_flag , approval_flag, start_date, projected_end_date, active, deleted) VALUES ('".$newProjectID."', '".$rows['phase_type']."', '".$mysql->real_escape_string($rows['name'])."', '".$mysql->real_escape_string($rows['desc'])."', '".$rows['finance_flag']."' , '".$rows['approval_flag']."', '".$rows['start_date']."', '".$rows['projected_end_date']."', '".$rows['active']."', '".$rows['deleted']."')" ; 		
				$mysql->query($project_new_phase)  or writeLog($mysql,$project_new_phase); 
				}
			}
			//End Project Phase
			//Start project_phase_new_approvals
			$project_phase_approvals = "SELECT * FROM project_phase_approvals WHERE project_id = '".$clone_project_id."'";
			$project_phase_approvals_sql = $mysql->query($project_phase_approvals) or writeLog($mysql,$project_phase_approvals); 
			if($project_phase_approvals_sql->num_rows > 0){
				while($rows = @$project_phase_approvals_sql->fetch_assoc()) {
					$project_phase_new_approvals = "INSERT INTO  `project_phase_approvals` (project_id, project_phase, name, title, phone, `desc`, approval_date, approved, non_phase, active, deleted) SELECT '".$newProjectID."' as `project_id`, `project_phase`, `name`, `title`, `phone`, `desc`, `approval_date`, `approved`, `non_phase`, `active`, `deleted` from  `project_phase_approvals` where id='".$rows['id']."' AND project_id = '".$clone_project_id."'";
					$mysql->query($project_phase_new_approvals) or writeLog($mysql,$project_phase_new_approvals); 
				}
			}
			//End project_phase_new_approvals
			
			//Start Project finance
			$project_phase_finance = "SELECT *  FROM `project_phase_finance` where project_id = '".$clone_project_id."'";
			$project_phase_finance_sql = $mysql->query($project_phase_finance) or writeLog($mysql,$project_phase_finance);
			if($project_phase_finance_sql->num_rows > 0){
				while($rows = @$project_phase_finance_sql->fetch_assoc()) {
					$project_phase_new_finance = "INSERT INTO  `project_phase_finance` (project_id, phase, hours, rate, creation_date, `assigned_date`, completed_date, closed_date, active, deleted) 
					
					SELECT '".$newProjectID."' as `project_id`, `phase`, `hours`, `rate`, `creation_date`, `assigned_date`, `completed_date`, `closed_date`, `active`, `deleted`  FROM `project_phase_finance` where id = '".$rows['id']."' AND project_id = '".$clone_project_id."'";
					$mysql->query($project_phase_new_finance) or writeLog($mysql,$project_phase_new_finance);
				}
			}
			
			//End project finance
			
			//start project_risks
			$project_risks = "SELECT id from `project_risks` where project_id = '".$clone_project_id."'";
			$project_risks_sql = $mysql->query($project_risks) or writeLog($mysql,$project_risks);
			if($project_risks_sql->num_rows > 0){
			while($rows = @$project_risks_sql->fetch_assoc()) {
				$project_risk_new = "INSERT INTO `project_risks` (project_id, assigned_to_user_id, archived, active, closed_date, created_date, created_by_user_id, title, description) SELECT '".$newProjectID."' as `project_id`, `assigned_to_user_id`, `archived`, `active`, `closed_date`, `created_date`, `created_by_user_id`, `title`, `description` from `project_risks` where id ='".$rows['id']."' AND project_id = '".$clone_project_id."'";
				$mysql->query($project_risk_new) or writeLog($mysql,$project_risk_new);
			}
			}
			//End project_risks
			//project roles
			$project_roles = "SELECT id from  `project_roles` where project_id = '".$clone_project_id."'";
			$project_roles_sql = $mysql->query($project_roles) or writeLog($mysql,$project_roles);
			if($project_roles_sql->num_rows > 0){
			while($rows = @$project_roles_sql->fetch_assoc()) {
				$project_roles_new = "INSERT INTO  `project_roles` (project_id ,resource_type_id,user_id,email,phone,sort_order,active,deleted) SELECT '".$newProjectID."' as `project_id`, `resource_type_id`,`user_id`,`email`,`phone`,`sort_order`,`active`,`deleted` from  `project_roles` where id = '".$rows['id']."'  AND project_id = '".$clone_project_id."'";
				$mysql->query($project_roles_new) or writeLog($mysql,$project_roles_new);
				}
			}
			//End Project Roles
			
			
			//project_status
			$project_status = "INSERT INTO  `project_status` (project_id ,status_id,created_date,created_user) SELECT '".$newProjectID."' as `project_id`, `status_id`,`created_date`,`created_user` from  `project_status` where project_id = '".$clone_project_id."'";
			$mysql->query($project_status) or writeLog($mysql,$project_status);
			// End project_status
			
			//project_sub_phase_finance 
			$project_sub_phase_finance  = 	"SELECT * from  `project_sub_phase_finance` where project_id = '".$clone_project_id."'";
			$project_sub_phase_finance_sql = $mysql->query($project_sub_phase_finance) or writeLog($mysql,$project_sub_phase_finance);
			if($project_sub_phase_finance_sql->num_rows > 0){
				while($rows = $project_sub_phase_finance_sql->fetch_assoc()) {
					$project_sub_phase_finance_new = "INSERT INTO project_sub_phase_finance (`project_id`, `phase`,`sub_phase`, `hours`, `rate`, `creation_date`, `assigned_date`, `completed_date`, `closed_date`, `active`, `deleted`) VALUES 
					( '".$newProjectID."' , '".$mysql->real_escape_string($rows['phase'])."','".$mysql->real_escape_string($rows['sub_phase'])."', '".$rows['hours']."', '".$rows['rate']."', '".$rows['creation_date']."', '".$rows['assigned_date']."', '".$rows['completed_date']."', '".$rows['closed_date']."', '".$rows['active']."', '".$rows['deleted']."')";
					$mysql->query($project_sub_phase_finance_new) or writeLog($mysql,$project_sub_phase_finance_new);
				
				}
			}
			
					
			
			//End qa_project_version
			$qa_project_version = "UPDATE `qa_project_version` SET `project_id`='".$newProjectID."' WHERE `project_id` = '".$clone_project_id."' ";
			$mysql->query($qa_project_version) or writeLog($mysql,$qa_project_version);
			//End qa_project_version
			//End qa_project_iteration
			$qa_project_iteration = "UPDATE `qa_project_iteration` SET `project_id`='".$newProjectID."' WHERE `project_id` = '".$clone_project_id."' ";
			$mysql->query($qa_project_iteration) or writeLog($mysql,$qa_project_iteration);
			//End qa_project_iteration
			//End qa_project_product
			$qa_project_product = "UPDATE `qa_project_product` SET `project_id`='".$newProjectID."' WHERE `project_id` = '".$clone_project_id."' ";
			$mysql->query($qa_project_product) or writeLog($mysql,$qa_project_product);
			//End qa_project_product
			
			
			$user_proj_permission_new = "SELECT  user_id,active,deleted FROM `user_project_permissions` WHERE project_id = '".$clone_project_id."'";
			$permission=$mysql->query($user_proj_permission_new);
			if($permission->num_rows > 0){
			while($rows = @$permission->fetch_assoc()) {
				$user_proj_permission = "INSERT INTO  `user_project_permissions` (project_id ,user_id,active,deleted)VALUES('".$newProjectID."','".$rows['user_id']."','".$rows['active']."','".$rows['deleted']."')" ;
				$mysql->query($user_proj_permission) or writeLog($mysql,$user_proj_permission);
				}
			}
			$user_project_role  = 	"SELECT * FROM `user_project_role`  WHERE `project_id` = '".$clone_project_id."'";
			$urp=$mysql->query($user_project_role);
			if($urp->num_rows > 0){
			while($rows = @$urp->fetch_assoc()) {
				$user_project_role = "INSERT INTO  `user_project_role` (`project_id`, `flag`, `phase_subphase_id`, `user_id`, `creation_date`, `assigned_date`, `active`, `deleted`)
				VALUES( '".$newProjectID."', '".$mysql->real_escape_string($rows['flag'])."', '".$rows['phase_subphase_id']."', '".$rows['user_id']."', '".$rows['creation_date']."', '".$rows['assigned_date']."', '".$rows['active']."', '".$rows['deleted']."')";
				$mysql->query($user_project_role) or writeLog($mysql,$user_project_role);
				}
			}
			
			$mysql->query("UPDATE qa_defects SET `project_id`='".$newProjectID."' WHERE `project_id` = '".$clone_project_id."'");
			
		}

	}
}*/
	
	function writeLog($mysql, $sql='', $rootPath=''){
		$a = fopen("clone_20123.log", "a");
		fwrite($a,  "\nError No: ". $mysql->errno . " - " . 
					"\nCron: users " . 
					"\nDate: " . date("Y-m-d : H:i:s") . 
					"\nMySQL error: " . $mysql->error . 
					"\nQuery: " . $sql . "\n");
		fclose($a);
	}
	$newProjectID = '18259';
	$clone_project_id = '21071';

	//
	$mysql->query("update `qa_defects` SET `project_id` = '".$newProjectID."' where id = '".$clone_project_id."'");
		
	//End qa_project_version
	$qa_project_version = "UPDATE `qa_project_version` SET `project_id`='".$newProjectID."' WHERE `project_id` = '".$clone_project_id."' ";
	$mysql->query($qa_project_version) or writeLog($mysql,$qa_project_version);
	//End qa_project_version
	//End qa_project_iteration
	$qa_project_iteration = "UPDATE `qa_project_iteration` SET `project_id`='".$newProjectID."' WHERE `project_id` = '".$clone_project_id."' ";
	$mysql->query($qa_project_iteration) or writeLog($mysql,$qa_project_iteration);
	//End qa_project_iteration
	//End qa_project_product
	$qa_project_product = "UPDATE `qa_project_product` SET `project_id`='".$newProjectID."' WHERE `project_id` = '".$clone_project_id."' ";
	$mysql->query($qa_project_product) or writeLog($mysql,$qa_project_product);
?>

