<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql;

	$project_all = "SELECT * FROM `projects` WHERE `archived` = '0' AND `active` = '1' AND `deleted` = '0'";
	$project_list = $mysql->sqlprepare($project_all);
	$current_year = current_year;//'2010';//date("Y");
	$i=0;
	if(@$project_list->num_rows > 0) {
		while($row = @$project_list->fetch_assoc()) {
		
		
		$project_phases_sql = "INSERT INTO project_phases SELECT '',".$row['id'].",pp.phase_type,pp.name,pp.desc,pp.finance_flag,pp.approval_flag,pp.start_date,pp.projected_end_date,pp.active,pp.deleted FROM `project_phases` pp,projects p where  p.id=pp.project_id and pp.project_id = '".$row['clone_project_id']."'" ;
	
		//$mysql->query($project_phases_sql);
		//echo "q1=".$project_phases_sql."<br>";

		$project_budget_sql = "INSERT INTO project_budget SELECT '',".$row['id'].",pp.total_budget,pp.quarter1_budget,pp.quarter2_budget,pp.quarter3_budget,pp.quarter4_budget FROM `project_budget` pp,projects p where  p.id=pp.project_id and pp.project_id = '".$row['clone_project_id']."'" ;

		//$mysql->query($project_budget_sql);
		//echo "q2=".$project_budget_sql."<br>";

		$project_phase_finance_sql = "INSERT INTO project_phase_finance SELECT '',".$row['id'].",pp.phase ,pp.hours ,pp.rate ,pp.creation_date ,pp.assigned_date ,pp.completed_date ,pp.closed_date ,pp.active ,pp.deleted FROM project_phase_finance pp,projects p where  p.id=pp.project_id and pp.project_id = '".$row['clone_project_id']."'" ;
		
		//$mysql->query($project_phase_finance_sql);
		//echo "q3=".$project_phase_finance_sql."<br>";
		
		$project_sub_phase_finance_sql = "INSERT INTO project_sub_phase_finance SELECT '',".$row['id'].",pp.phase ,pp.sub_phase ,pp.hours ,pp.rate ,pp.creation_date ,pp.assigned_date ,pp.completed_date ,pp.closed_date ,pp.active ,pp.deleted  FROM project_sub_phase_finance pp,projects p where  p.id=pp.project_id and pp.project_id = '".$row['clone_project_id']."'" ;

		//$mysql->query($project_sub_phase_finance_sql);
		//echo "q4=".$project_sub_phase_finance_sql."<br>";

		// not needed done in clone_pjts file
		/*$project_roles_sql = "INSERT INTO project_roles SELECT '',".$row['id'].",pp.resource_type_id,pp.user_id,pp.email,pp.phone,pp.sort_order,pp.active,pp.deleted FROM project_roles pp,projects p where  p.id=pp.project_id and pp.project_id = '".$row['clone_project_id']."'" ;
		//$mysql->query($project_roles_sql);  */

		$user_project_permissions_sql = "INSERT INTO user_project_permissions SELECT '', upp.user_id,".$row['id'].", upp.active, upp.deleted FROM `user_project_permissions` upp, `projects` p WHERE p.id = '".$row['clone_project_id']."'  AND upp.project_id = p.id AND upp.user_id NOT IN ( SELECT id FROM users WHERE company = 2 or company = p.company)";
		//echo "qry".$i."=".$user_project_permissions_sql."<br><br>";

		//$mysql->query($newProject5);

		$cc_update_sql = "UPDATE `projects` SET cclist=(SELECT `cclist` FROM (SELECT * from `projects` where id='".$row['clone_project_id']."') as temp ) where id='".$row['id']."'";
		//$mysql->query($cc_update_sql);
		}
	}
?>
