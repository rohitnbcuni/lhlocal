<?PHP
    //ini_set('max_execution_time', 60000);
	include('../_inc/config.inc');
	//include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, 'lhdev_live2' , DB_PORT);	
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$project_all = "SELECT * FROM `projects` WHERE `archived` = '0' AND `active` = '1' AND `deleted` = '0'";
	$project_list = $mysql->sqlordie($project_all);
	
	$current_year = current_year;//'2010';//date("Y");
	if($project_list->num_rows > 0) {
	echo "<center> <b> CLONE PROJECTS <b></center>";
	echo "<br>List of Projects Cloned : ";
	echo "<br>----------------------------------------------------------<br>";
	$i=0;
	while($row = $project_list->fetch_assoc()) {
		
		$clone_project_id = $row['id'];
		$newProject = "INSERT INTO `projects` (`bc_id`,`project_code`,`bc_category_id`,`budget_code`,`project_name`,`company`,`desc`, `business_case`,`scope`,`deliverables`,`owner_approval`,`company_approval`,`metrics_tracking`,`archived`,`active`,`deleted`, `rp_permission`,`project_status`,`wo_permission`,`internal_groups`,`YEAR`,`clone_project_id`,`cclist`,`qccclist`,`program`) VALUES ('".$row['bc_id']."','".$row['project_code']."','".$row['bc_category_id']."','".$row['budget_code']."','".$mysql->real_escape_string($row['project_name'])."','".$row['company']."','".$row['desc']."','".$row['business_case']."','".$row['scope']."','".$row['deliverables']."','".$row['owner_approval']."','".$row['company_approval']."','".$row['metrics_tracking']."','".$row['archived']."','".$row['active']."','".$row['deleted']."','".$row['rp_permission']."','".$row['project_status']."','".$row['wo_permission']."','".$row['internal_groups']."','".$current_year."','".$clone_project_id."','".$row['cclist']."','".$row['qccclist']."','".$row['program']."')";
		
		$mysql->sqlordie($newProject);
		$newProjectID = $mysql->insert_id;
	
		if($newProjectID!=$clone_project_id && $newProjectID >0 )
		{   echo $i++;
			echo ".) project ID : ". $clone_project_id;
			echo " | Project Name : ". $row['project_name'];
			echo " | company	   : ". $row['company'];
			echo " | project Code : ". $row['project_code'];
			echo " | New Project ID : ". $newProjectID.'<br>';

			//project_brief_sections
			$proj_brief_sec = "SELECT id FROM `project_brief_sections` where project_id = '".$clone_project_id."'";
			$proj_brief_sec_sql = $mysql->sqlordie($proj_brief_sec);
			if($proj_brief_sec_sql->num_rows > 0){
				while($rows = $proj_brief_sec_sql->fetch_assoc()) {
					$proj_brief_new_sec = "INSERT INTO `project_brief_sections` (`project_id` ,`desc`, `section_type`, `flag`, `active`, `deleted`) SELECT '".$newProjectID."' as `project_id`, `desc`, `section_type`, `flag`, `active`, `deleted` FROM `project_brief_sections` where id='".$rows['id']."' AND project_id = '".$clone_project_id."'";
					$mysql->sqlordie($proj_brief_new_sec);
				}
			}
			//end project_brief_sections
			
			//project budget
			$insert_budget="SELECT '".$newProjectID."' as `project_id`, total_budget  , quarter1_budget, quarter2_budget, quarter3_budget, quarter4_budget from project_budget where project_id = '".$clone_project_id."'";
			$insert_budget_sql = $mysql->sqlordie($insert_budget);
			if($insert_budget_sql->num_rows > 0){
			while($rows1 = $insert_budget_sql->fetch_assoc()) {
			$project_budget = "INSERT INTO project_budget  (project_id  , total_budget  , quarter1_budget, quarter2_budget, quarter3_budget, quarter4_budget) VALUES ('".$rows1['project_id']."','".$rows1['total_budget']."','".$rows1['quarter1_budget']."','".$rows1['quarter2_budget']."','".$rows1['quarter3_budget']."','".$rows1['quarter4_budget']."') ";
			$mysql->sqlordie($project_budget);
				}
			}
			//end project budget
			
			//project_phase
			$project_phase = "SELECT * FROM project_phases WHERE project_id = '".$clone_project_id."'";
			$project_phase_sql = $mysql->sqlordie($project_phase);
			if($project_phase_sql->num_rows > 0){
				while($rows = @$project_phase_sql->fetch_assoc()) {
				//Insert new entry in project phase
				$project_new_phase = "INSERT INTO project_phases (`project_id`, `phase_type`, `name`, `desc`, finance_flag , approval_flag, start_date, projected_end_date, active, deleted) VALUES ('".$newProjectID."', '".$rows['phase_type']."', '".$rows['name']."', '".$rows['desc']."', '".$rows['finance_flag']."' , '".$rows['approval_flag']."', '".$rows['start_date']."', '".$rows['projected_end_date']."', '".$rows['active']."', '".$rows['deleted']."')" ; 		
				$mysql->sqlordie($project_new_phase);
				}
			}
			//End Project Phase
			//Start project_phase_new_approvals
			$project_phase_approvals = "SELECT * FROM project_phase_approvals WHERE project_id = '".$clone_project_id."'";
			$project_phase_approvals_sql = $mysql->sqlordie($project_phase_approvals);
			if($project_phase_approvals_sql->num_rows > 0){
				while($rows = @$project_phase_approvals_sql->fetch_assoc()) {
					$project_phase_new_approvals = "INSERT INTO  `project_phase_approvals` (project_id, project_phase, name, title, phone, `desc`, approval_date, approved, non_phase, active, deleted) SELECT '".$newProjectID."' as `project_id`, `project_phase`, `name`, `title`, `phone`, `desc`, `approval_date`, `approved`, `non_phase`, `active`, `deleted` from  `project_phase_approvals` where id='".$rows['id']."' AND project_id = '".$clone_project_id."'";
					$mysql->sqlordie($project_phase_new_approvals);
				}
			}
			//End project_phase_new_approvals
			
			//Start Project finance
			$project_phase_finance = "SELECT *  FROM `project_phase_finance` where project_id = '".$clone_project_id."'";
			$project_phase_finance_sql = $mysql->sqlordie($project_phase_finance);
			if($project_phase_finance_sql->num_rows > 0){
				while($rows = @$project_phase_finance_sql->fetch_assoc()) {
					$project_phase_new_finance = "INSERT INTO  `project_phase_finance` (project_id, phase, hours, rate, creation_date, `assigned_date`, completed_date, closed_date, active, deleted) 
					
					SELECT '".$newProjectID."' as `project_id`, `phase`, `hours`, `rate`, `creation_date`, `assigned_date`, `completed_date`, `closed_date`, `active`, `deleted`  FROM `project_phase_finance` where id = '".$rows['id']."' AND project_id = '".$clone_project_id."'";
					$mysql->sqlordie($project_phase_new_finance);
				}
			}
			
			//End project finance
			
			//start project_risks
			$project_risks = "SELECT id from `project_risks` where project_id = '".$clone_project_id."'";
			$project_risks_sql = $mysql->sqlordie($project_risks);
			if($project_risks_sql->num_rows > 0){
			while($rows = @$project_risks_sql->fetch_assoc()) {
				$project_risk_new = "INSERT INTO `project_risks` (project_id, assigned_to_user_id, archived, active, closed_date, created_date, created_by_user_id, title, description) SELECT '".$newProjectID."' as `project_id`, `assigned_to_user_id`, `archived`, `active`, `closed_date`, `created_date`, `created_by_user_id`, `title`, `description` from `project_risks` where id ='".$rows['id']."' AND project_id = '".$clone_project_id."'";
				$mysql->sqlordie($project_risk_new);
			}
			}
			//End project_risks
			//project roles
			$project_roles = "SELECT id from  `project_roles` where project_id = '".$clone_project_id."'";
			$project_roles_sql = $mysql->sqlordie($project_roles);
			if($project_roles_sql->num_rows > 0){
			while($rows = @$project_roles_sql->fetch_assoc()) {
				$project_roles_new = "INSERT INTO  `project_roles` (project_id ,resource_type_id,user_id,email,phone,sort_order,active,deleted) SELECT '".$newProjectID."' as `project_id`, `resource_type_id`,`user_id`,`email`,`phone`,`sort_order`,`active`,`deleted` from  `project_roles` where id = '".$rows['id']."'  AND project_id = '".$clone_project_id."'";
				$mysql->sqlordie($project_roles_new);
			}
			}
			//End Project Roles
			
			//user_project_role
				//$user_project_role = "INSERT INTO  `user_project_role` (`project_id`, `flag`, `phase_subphase_id`, `user_id`, `creation_date`, `assigned_date`, `active`, `deleted`) SELECT '".$newProjectID."' as `project_id`, `flag`, `phase_subphase_id`, `user_id`, `creation_date`, `assigned_date`, `active`, `deleted` from  `user_project_role` where project_id = '".$clone_project_id."'";
				//echo $user_project_role;
				//$mysql->sqlordie($user_project_role);
			
			//
			//project_status
			$project_status = "INSERT INTO  `project_status` (project_id ,status_id,created_date,created_user) SELECT '".$newProjectID."' as `project_id`, `status_id`,`created_date`,`created_user` from  `project_status` where project_id = '".$clone_project_id."'";
			$mysql->sqlordie($project_status);
			// End project_status
			
			//project_sub_phase_finance 
			$project_sub_phase_finance  = 	"SELECT * from  `project_sub_phase_finance` where project_id = '".$clone_project_id."'";
			$project_sub_phase_finance_sql = $mysql->sqlordie($project_sub_phase_finance);
			if($project_sub_phase_finance_sql->num_rows > 0){
				while($rows = $project_sub_phase_finance_sql->fetch_assoc()) {
					$project_sub_phase_finance_new = "INSERT INTO project_sub_phase_finance (`project_id`, `phase`,`sub_phase`, `hours`, `rate`, `creation_date`, `assigned_date`, `completed_date`, `closed_date`, `active`, `deleted`) VALUES 
					( '".$newProjectID."' , '".$rows['phase']."','".$rows['sub_phase']."', '".$rows['hours']."', '".$rows['rate']."', '".$rows['creation_date']."', '".$rows['assigned_date']."', '".$rows['completed_date']."', '".$rows['closed_date']."', '".$rows['active']."', '".$rows['deleted']."')";
					$mysql->sqlordie($project_sub_phase_finance_new);
				
				}
			}
			
			//End project_sub_phase_finance 
			
			//Start qa_project_version
			/*$qa_project_version = "SELECT id from qa_project_version WHERE project_id = '".$clone_project_id."'";
			$qa_project_version_sql = $mysql->sqlordie($project_sub_phase_finance);
			if($qa_project_version_sql->num_rows > 0){
				while($rows = @$qa_project_version_sql->fetch_assoc()) {
					$project_sub_phase_finance_new = "INSERT INTO qa_project_version (project_id, phase,sub_phase, hours, rate, creation_date, assigned_date, completed_date, closed_date, active, deleted) SELECT '".$newProjectID."' as `project_id`, phase,sub_phase, hours, rate, creation_date, assigned_date, completed_date, closed_date, active, deleted FROM project_sub_phase_finance where id = '".$rows['id']."'  AND project_id = '".$clone_project_id."'";
					$mysql->sqlordie($project_sub_phase_finance_new);
					
				}
			}*/	
			
			
			//End qa_project_version
			$qa_project_version = "UPDATE `qa_project_version` SET `project_id`='".$newProjectID."' WHERE `project_id` = '".$clone_project_id."' AND `active` = '1' AND `deleted` = '0'";
			$mysql->sqlordie($qa_project_version);
			//End qa_project_version
			
			//resource_blocks
		    $project_sub_phase_role  = 	"SELECT * FROM `resource_blocks`  WHERE `projectid` = '".$clone_project_id."'";
		    $project_sub_phase_role_sql = $mysql->sqlordie($project_sub_phase_role);
			if($project_sub_phase_role_sql->num_rows > 0){
				while($rows = @$project_sub_phase_role_sql->fetch_assoc()) {
					$project_sub_phase_row_new = "INSERT INTO `resource_blocks` (`userid`, `projectid`, `daypart`, `status`,`datestamp`,`dateadded`, `active`, `deleted`, `notes`, `hours`) VALUES ('".$rows['userid']."', '".$newProjectID."', '".$rows['daypart']."','".$rows['status']."','".$rows['datestamp']."','".$rows['dateadded']."', '".$rows['active']."','".$rows['deleted']."','".$rows['notes']."', '".$rows['hours']."')";
					$mysql->sqlordie($project_sub_phase_row_new);
				
				}
			}
			//$resource_blocks = "UPDATE `resource_blocks` SET `project_id`='".$newProjectID."' WHERE `project_id` = '".$clone_project_id."'";
			//$mysql->sqlordie($resource_blocks);
			
			//End resource_blocks
						
			
			//$user_proj_permission = "INSERT INTO  `user_project_permissions` (project_id ,user_id,active,deleted) SELECT '".$newProjectID."' AS `project_id` , upp.user_id, upp.active, upp.deleted FROM `user_project_permissions` upp, `projects` p WHERE p.id = '".$clone_project_id."'  AND upp.project_id = p.id AND upp.user_id NOT IN ( SELECT user_id FROM users WHERE company = p.company )";
			$user_proj_permission_new = "SELECT  user_id,active,deleted FROM `user_project_permissions` WHERE project_id = '".$clone_project_id."'";
			$permission=$mysql->sqlordie($user_proj_permission_new);
			if($permission->num_rows > 0){
			while($rows = @$permission->fetch_assoc()) {
			$user_proj_permission = "INSERT INTO  `user_project_permissions` (project_id ,user_id,active,deleted)VALUES('".$newProjectID."','".$rows['user_id']."','".$rows['active']."','".$rows['deleted']."')" ;
			$mysql->sqlordie($user_proj_permission);
			}
			}
			
			$user_project_role  = 	"SELECT * FROM `user_project_role`  WHERE `project_id` = '".$clone_project_id."'";
			$urp=$mysql->sqlordie($user_project_role);
			if($urp->num_rows > 0){
			while($rows = @$urp->fetch_assoc()) {
				$user_project_role = "INSERT INTO  `user_project_role` (`project_id`, `flag`, `phase_subphase_id`, `user_id`, `creation_date`, `assigned_date`, `active`, `deleted`)
				VALUES( '".$newProjectID."', '".$rows['flag']."', '".$rows['phase_subphase_id']."', '".$rows['user_id']."', '".$rows['creation_date']."', '".$rows['assigned_date']."', '".$rows['active']."', '".$rows['deleted']."')";
				$mysql->sqlordie($user_project_role);
				}
			}
			
			
			
			$archiveCloneProject = "UPDATE `projects` set `archived` = '1' WHERE ID = '".$clone_project_id."'";
			$mysql->sqlordie($archiveCloneProject);
			
			
		
			$deleteoldProject = "update `projects` SET `deleted`='1' where `YEAR`='2009'";
			$mysql->sqlordie($deleteoldProject);
			$deleteoldProject2010 = "update `projects` SET `deleted`='1' where `YEAR`='2010'";
			$mysql->sqlordie($deleteoldProject2010);
	
			$mysql->sqlordie("UPDATE workorders SET `project_id`='".$newProjectID."' WHERE `project_id` = '".$clone_project_id."' and status!='1'");
			$mysql->sqlordie("UPDATE qa_defects SET `project_id`='".$newProjectID."' WHERE `project_id` = '".$clone_project_id."' and status!='8'");
			
		}

	}
}
	
?>

