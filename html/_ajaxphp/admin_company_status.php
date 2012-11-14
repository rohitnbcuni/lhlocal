<?PHP 
	include("../_inc/config.inc");
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
    $companyID = $_GET['company_id'];
	if($companyID == '0'){
		$sql = "";
	}else{
		$sql = " AND company='$companyID'";
	}
	$project_archived_Result = array();
	$project_active_Result = array();
	$project_perms_archived_result = $mysql->sqlordie("SELECT * FROM `projects` WHERE `company`='$companyID' AND `active`='1' AND `deleted`='0' AND `YEAR`=YEAR(CURDATE()) AND `archived`='1' order by project_name");
	
	$project_perms_active_result = $mysql->sqlordie("SELECT * FROM `projects` WHERE `company`='$companyID' AND `active`='1' AND `deleted`='0' AND `YEAR`=YEAR(CURDATE()) AND `archived`='0' order by project_name");

	if($project_perms_archived_result->num_rows > 0){
		while($project_perms_archived_row = $project_perms_archived_result->fetch_assoc()){
			$project_archived_Result[] = $project_perms_archived_row;
		}
	}

	if($project_perms_active_result->num_rows > 0){
		while($project_perms_active_row = $project_perms_active_result->fetch_assoc()){
			$project_active_Result[] = $project_perms_active_row;
		}
	}
	 
	if(($project_perms_archived_result->num_rows > 0) || ($project_perms_active_result->num_rows > 0)){
		echo	'<div class="project_status_title"><div class="expand_toggle <!--expand_toggle_closed-->" id="active_link" style="float:left;">PROJECT NAME</div>
			<button class="admin_edit_perm_submit_button" onclick="submitPermsUser();"><span>Submit</span></button>
			<button class="rp_edit admin_edit_perm_submit_button" onclick="checkall();"><span>CHECK ALL</span></button>
			<button class="rp_edit admin_edit_perm_submit_button" onclick="uncheckall();"><span>UNCHECK ALL</span></button>
			</div>';

		if($project_perms_active_result->num_rows > 0){
			  echo	'<div class="project_status_title"><div id="active_link"  class="expand_toggle <!--expand_toggle_closed-->">Active Projects</div></div>
				<div id="active_list" style="display: block;" class="<!--results_collapsed-->">';
				displayprojects($project_active_Result,"A");
			  echo	'</div>';
		}


		if($project_perms_archived_result->num_rows > 0){
			echo	'<div class="project_status_title"><div id="archive_link" class="expand_toggle">Archived Projects</div></div>
				<div style="display: block;" id="archive_list" class="<!--results_collapsed-->">';
				displayprojects($project_archived_Result,"A");
			echo	'</div>';        
		} 
		echo	'<button class="admin_edit_perm_submit_button" onclick="submitPermsUser();" style="margin-top:4px;"><span>Submit</span></button>';
	}else {
		echo '<div>NO PROJECTS TO DISPLAY</div>';
	}

	function displayprojects($displaydata,$projectstatus){
		foreach($displaydata as $key => $project){
			echo '<div class="project_results_container">';
			if($projectstatus == "A"){
				echo'<dl class="project_results"><dt>'.$project["project_name"].'</dt>';
			}else{
				echo '<dl class="project_results"><dt>'.$project["project_name"].'</dt>';
			}
			echo '<div class="access_checkbox"><input type="checkBox" class = "projects_check_box" style="width:10px;" name="'.$project["id"].'" id="'.$project["id"].'" value="" ></div></dl></div>';
		}
	}
?>
