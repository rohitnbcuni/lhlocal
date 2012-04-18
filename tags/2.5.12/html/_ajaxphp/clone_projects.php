<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);	

	$project_all = "SELECT * FROM `projects` WHERE `archived` = '0' AND `active` = '1' AND `deleted` = '0'";
	$project_list = $mysql->query($project_all);
	$current_year = current_year;//'2010';//date("Y");

if(@$project_list->num_rows > 0) {
	echo "<center> <b> CLONE PROJECTS <b></center>";
	echo "<br>List of Projects Cloned : ";
	echo "<br>----------------------------------------------------------<br>";
	while($row = @$project_list->fetch_assoc()) {
		
		$clone_project_id = $row['id'];
		$newProject = "INSERT INTO `projects` (`bc_id`,`project_code`,`bc_category_id`,`budget_code`,`project_name`,`company`,`desc`, `business_case`,`scope`,`deliverables`,`owner_approval`,`company_approval`,`metrics_tracking`,`archived`,`active`,`deleted`, `rp_permission`,`project_status`,`wo_permission`,`internal_groups`,`YEAR`,`clone_project_id`) VALUES ('".$row['bc_id']."','".$row['project_code']."','".$row['bc_category_id']."','".$row['budget_code']."','".$mysql->real_escape_string($row['project_name'])."','".$row['company']."','".$row['desc']."','".$row['business_case']."','".$row['scope']."','".$row['deliverables']."','".$row['owner_approval']."','".$row['company_approval']."','".$row['metrics_tracking']."','".$row['archived']."','".$row['active']."','".$row['deleted']."','".$row['rp_permission']."','".$row['project_status']."','".$row['wo_permission']."','".$row['internal_groups']."','".$current_year."','".$clone_project_id."')";
		
		$mysql->query($newProject);
		$newProjectID = $mysql->insert_id;
		if($newProjectID!=$clone_project_id && $newProjectID >0 )
		{
			echo "<br>project ID : ". $clone_project_id;
			echo " | Project Name : ". $row['project_name'];
			echo " | company	   : ". $row['company'];
			echo " | project Code : ". $row['project_code'];
			echo " | New Project ID : ". $newProjectID;

			$proj_brief_sec = "INSERT INTO `project_brief_sections` (`project_id` ,`desc`, `section_type`, `flag`, `active`, `deleted`) SELECT '".$newProjectID."' as `project_id`, `desc`, `section_type`, `flag`, `active`, `deleted` FROM `project_brief_sections` where project_id = '".$clone_project_id."'";

			$mysql->query($proj_brief_sec);
			
			$project_risk = "INSERT INTO `project_risks` (project_id, assigned_to_user_id, archived, active, closed_date, created_date, created_by_user_id, title, description) SELECT '".$newProjectID."' as `project_id`, `assigned_to_user_id`, `archived`, `active`, `closed_date`, `created_date`, `created_by_user_id`, `title`, `description` from `project_risks` where project_id = '".$clone_project_id."'";

			$mysql->query($project_risk);

			$project_roles = "INSERT INTO  `project_roles` (project_id ,resource_type_id,user_id,email,phone,sort_order,active,deleted) SELECT '".$newProjectID."' as `project_id`, `resource_type_id`,`user_id`,`email`,`phone`,`sort_order`,`active`,`deleted` from  `project_roles` where project_id = '".$clone_project_id."'";

			$mysql->query($project_roles);

			$project_status = "INSERT INTO  `project_status` (project_id ,status_id,created_date,created_user) SELECT '".$newProjectID."' as `project_id`, `status_id`,`created_date`,`created_user` from  `project_status` where project_id = '".$clone_project_id."'";

			$mysql->query($project_status);

			$user_proj_permission = "INSERT INTO  `user_project_permissions` (project_id ,user_id,active,deleted) SELECT '".$newProjectID."' AS `project_id` , upp.user_id, upp.active, upp.deleted FROM `user_project_permissions` upp, `projects` p WHERE p.id = '".$clone_project_id."'  AND upp.project_id = p.id AND upp.user_id NOT IN ( SELECT user_id FROM users WHERE company = p.company )";

			$mysql->query($user_proj_permission);

			$project_phase_approvals = "INSERT INTO  `project_phase_approvals` (project_id, project_phase, name, title, phone, `desc`, approval_date, approved, non_phase, active, deleted) SELECT '".$newProjectID."' as `project_id`, project_phase, name, title, phone, `desc`, approval_date, approved, non_phase, active, deleted from  `project_phase_approvals` where project_id = '".$clone_project_id."'";

			$mysql->query($project_phase_approvals);
			
			$archiveCloneProject = "UPDATE `projects` set `archived` = '1' WHERE ID = '".$clone_project_id."'";
			$mysql->query($archiveCloneProject);
			
		}

	}
}
	
?>