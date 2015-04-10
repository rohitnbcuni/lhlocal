<?PHP
	session_start();
	include('../_inc/config.inc');
	include('../_ajaxphp/sendEmail.php');
	include('../_ajaxphp/util.php');
	
	$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	if(isset($_SESSION['user_id'])) {
		$user = $_SESSION['lh_username'];
	    $password = $_SESSION['lh_password'];
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		global $mysql;
		//$sanitized['woTitle'] = filter_input( INPUT_POST, 'woTitle', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
		//$sanitized['woExampleURL'] = filter_input( INPUT_POST, 'woExampleURL', FILTER_SANITIZE_URL, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
		//$sanitized['woDesc'] = filter_input( INPUT_POST, 'woDesc', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
				
		$woId = $mysql->real_escape_string(@$_POST['woId']);
		$dirName = $mysql->real_escape_string(@$_POST['dirName']);
		$requestedId = $mysql->real_escape_string(@$_POST['requestedId']);
		$projectId = $mysql->real_escape_string(@$_POST['projectId']);
		$woTypeId = $mysql->real_escape_string(@$_POST['woTypeId']);
		$priorityId = $mysql->real_escape_string(@$_POST['priorityId']);
		$timeSens = $mysql->real_escape_string(@$_POST['timeSens']);
		$timeSensDate = $mysql->real_escape_string(@$_POST['timeSensDate']);
		$timeSensTime = $mysql->real_escape_string(@$_POST['timeSensTime']);
		$ampm = $mysql->real_escape_string(@$_POST['ampm']);
		$min=$mysql->real_escape_string(@$_POST['currmin']);
		$wo_draft = $mysql->real_escape_string(@$_POST['wo_draft']);
		$timeSensDate_draft = $mysql->real_escape_string(@$_POST['timeSensDate_draft']);
		$timeSensTime_draft = $mysql->real_escape_string(@$_POST['timeSensTime_draft']);
		$ampm_draft = $mysql->real_escape_string(@$_POST['ampm_draft']);

		$woTitle = $mysql->real_escape_string(Util::escapewordquotes(@$_POST['woTitle']));
		$woExampleURL = $mysql->real_escape_string(Util::escapewordquotes(@$_POST['woExampleURL']));
		$woDesc = $mysql->real_escape_string(Util::escapewordquotes(@$_POST['woDesc']));
		$completed_by = $mysql->real_escape_string(@$_POST['completed_by']);
		/**
		*Ticket #60723
		* Match the word from  addNewDefaultCc array
		*
		*/
		$addNewDefaultCc = array("video","mpx","anvato","episode");
		$ccCounter = 0;
		foreach($addNewDefaultCc as $newCC){
			if (preg_match("/\b$newCC\b/", $woTitle." ".$woDesc)) {
				$ccCounter++;
			
			}
		
		}
        /**
		 * Ticket #16857
		 * change escapewordquotes func
		 * with nonPritable func
		 * And wrap up with htmlentities function  
		 */
		//$woDesc = $mysql->real_escape_string(htmlentities(Util::nonPrintableChar(@$_POST['woDesc'])));  
		$woAssignedTo = $mysql->real_escape_string(@$_POST['woAssignedTo']);
		$woStatus = $mysql->real_escape_string(@$_POST['woStatus']);
		$woStartDate = $mysql->real_escape_string(@$_POST['woStartDate']);
		$woEstDate = $mysql->real_escape_string(@$_POST['woEstDate']);
		$rallyType = $mysql->real_escape_string(@$_POST['rallyType']);
		$rallyProject = $mysql->real_escape_string(@$_POST['rallyProject']);
		$rallyFlag = $mysql->real_escape_string(@$_POST['rallyFlag']);

		$woREQ_TYPE = $mysql->real_escape_string(@$_POST['woREQ_TYPE']);
		$woSEVERITY = $mysql->real_escape_string(@$_POST['woSEVERITY']);
		$woSITE_NAME = $mysql->real_escape_string(@$_POST['woSITE_NAME']);
		$woINFRA_TYPE = $mysql->real_escape_string(@$_POST['woINFRA_TYPE']);
		$woCRITICAL = $mysql->real_escape_string(@$_POST['woCRITICAL']);
		$woCCList = $mysql->real_escape_string(@$_POST['woCCList']);
		$woStatusIdHidden = $mysql->real_escape_string(@$_POST['woStatusIdHidden']);
		//Related Issue Ids
		$woRelatedIds = $mysql->real_escape_string(@$_POST['wo_related_ids']);
		$dfRelatedIds = $mysql->real_escape_string(@$_POST['df_related_ids']);
		//end

		$updatesql_draft_date = '';
		$insertsql_draft_date = '';

		if($wo_draft == 'true') {
			$isActive = 0;
		} else {
			$isActive = 1;
		}   
    
		if(empty($woAssignedTo)) {
			$woAssignedTo = 97;
		}	

		if($timeSens == "true") {

		$sql_date = dateTimeToSql_new($timeSensDate,$timeSensTime,$ampm,$min);
		if($isActive == 0){
			$updatesql_draft_date = "`draft_date`='".dateTimeToSql_new($timeSensDate_draft,$timeSensTime_draft,$ampm_draft,'00')."', ";
			$insertsql_draft_date = dateTimeToSql_new($timeSensDate_draft,$timeSensTime_draft,$ampm_draft,'00');
		} 
		$select_wo_old = "SELECT * FROM `workorders` WHERE `id`= ?";
		$wo_old_res = $mysql->sqlprepare($select_wo_old, array($woId));
		$wo_old_row = $wo_old_res->fetch_assoc();

		$commentSubmit = $mysql->real_escape_string(@$_POST['commentSubmit']);
		if(isset($commentSubmit) && !empty($commentSubmit) && $commentSubmit == 'comment' && !empty($woId)){
			$needFBStatusId = '5';
			if($woAssignedTo == $_SESSION['user_id'] && $woStatus == $needFBStatusId && $woStatus == $wo_old_row['status']){
				// When the user is posting a Feedback to the WO, we auto change the status and assigned_to
				$sql = "SELECT `id`, `log_user_id`, `assign_user_id`, `audit_id`, `status`, `log_date` FROM `workorder_audit` WHERE `workorder_id` = ? ORDER BY `log_date` DESC";

				$resultObj = $mysql->sqlprepare($sql,array($woId));
				$audit_prev_row = array();
				$audit_curr_row = array();
				while($auditRow = $resultObj->fetch_assoc()){
					if($auditRow['status'] == $needFBStatusId){
						$audit_prev_row = $auditRow;
					}else if($auditRow['status'] != $needFBStatusId && $audit_prev_row['status'] == $needFBStatusId){
						$audit_curr_row = $audit_prev_row;
						break;
					}else if($auditRow['status'] != $needFBStatusId){
						$audit_prev_row = $auditRow;
					}
				}
				$woStatus = '10';
				$woAssignedTo = $audit_curr_row['log_user_id'];
			}
		}

		$getWoId = '';
		
		if(!empty($woAssignedTo)) {
			$assignedDate = 'NOW()';
		} else {
			$assignedDate = '\'\'';
		}
		//Set $woStatus == -1 to $woStatus == 6 to change new status to assinged status
		if((empty($woStatus) || $woStatus < 1) && !empty($woAssignedTo) && $woAssignedTo != 97) {
			$woStatus = 6;
		} else {
			if(empty($woId)) {
				$woStatus = 6;
			}
		}
		if(empty($woTypeId)) {
			$woTypeId = "NULL";
		} else {
			$woTypeId = "'" .$woTypeId ."'";
		}
		
		if(empty($woId)) {
			$dt_part = @explode("/", $woEstDate);
			$dt_part_start = @explode("/", $woStartDate);
			
			if(!empty($woEstDate)) {
				$dtEst = "'" .@$dt_part[2] ."-" .@$dt_part[0] ."-" .@$dt_part[1] ."'";
			} else {
				$dtEst = "null";
			}
			
			if(!empty($woStartDate)) {
				$dtStart = "'" .@$dt_part_start[2] ."-" .@$dt_part_start[0] ."-" .@$dt_part_start[1] .' ' .date('H:i:s') ."' ";
			} else {
				$dtStart = "NOW()";
			}
		

			$proj_default_cc = "SELECT `cclist` FROM `projects` WHERE `id`= ?";
			$proj_default_cc_res = $mysql->sqlprepare($proj_default_cc, array($projectId));
			$proj_default_cc_row = $proj_default_cc_res->fetch_assoc();
			$defaultCC = $proj_default_cc_row['cclist'];

			$company_default_cc = "SELECT c.`cclist` FROM `companies` c,`projects` p WHERE p.`id`= ? AND p.`company` = c.`id`";
			$company_default_cc_res = $mysql->sqlprepare($company_default_cc,array($projectId));
			$company_default_cc_row = $company_default_cc_res->fetch_assoc();
			$companyDefaultCC = $company_default_cc_row['cclist'];

			if(empty($woCCList))
			{
				if(!empty($defaultCC))
				{
					$woCCList = $defaultCC;
				}
			}
			else{
				if(!empty($defaultCC))
				{
					$woCCList = $woCCList.$defaultCC;
				}
			}

			if(empty($woCCList))
			{
				if(!empty($companyDefaultCC))
				{
					$woCCList = $companyDefaultCC;
				}
			}
			else
			{
				if(!empty($companyDefaultCC))
				{
					$woCCList = $woCCList.$companyDefaultCC;
				}
			}
			if($ccCounter > 0){
				//Add digitaltransmission@nbcuni.com in CC
				$woCCList = $woCCList.NEW_DEFAUTL_CC;
			
			}

			$ccArray =  array();
			$cc_list_array = explode(",",$woCCList);
			for($i = 0; $i < sizeof($cc_list_array); $i++) {
				$ccArray[$cc_list_array[$i]]=true;
			}
			
			$listKeys = array_keys($ccArray);
			$cc_arrayData = "";
			
			for($z = 0; $z < sizeof($listKeys); $z++) {
				if(!empty($listKeys[$z]))
				{
					$cc_arrayData .= $listKeys[$z] .",";
				}
			}

			$insert_wo = "INSERT INTO `workorders` "
				."(`project_id`,`assigned_to`,`type`,`status`,`title`,"
				."`example_url`,`body`,`requested_by`,`assigned_date`,`estimated_date`,"
				."`creation_date`,`rally_type`,`rally_project_id`,`launch_date`,`cclist`,`draft_date`,`active`) "
				."VALUES "
				."('$projectId','$woAssignedTo',$woTypeId,'$woStatus','$woTitle',"
				."'$woExampleURL','$woDesc','$requestedId',$assignedDate,'$sql_date',"
				."NOW(), '$rallyType','$rallyProject','$sql_date','$cc_arrayData','$insertsql_draft_date','$isActive')";
			@$mysql->sqlordie($insert_wo);
			$getWoId = $mysql->insert_id;
			
			//Create  a  Milestone 
			//Update Milestone
			Util::createMileStone($woAssignedTo,$getWoId,$woTitle,$sql_date);
			
			
			if(!empty($getWoId))
			{
				if(!empty($woREQ_TYPE) && $woREQ_TYPE!='_blank' && $woREQ_TYPE!='disable')
				{
					insertCustomFeild($mysql,"REQ_TYPE",$woREQ_TYPE,$getWoId);	
				}
				if(!empty($woSEVERITY) && $woSEVERITY!='_blank' && $woSEVERITY!='disable')
				{
					insertCustomFeild($mysql,"SEVERITY",$woSEVERITY,$getWoId);	
				}
				if(!empty($woSITE_NAME) && $woSITE_NAME!='_blank' && $woSITE_NAME!='disable')
				{
					insertCustomFeild($mysql,"SITE_NAME",$woSITE_NAME,$getWoId);	
				}
				if(!empty($woINFRA_TYPE) && $woINFRA_TYPE!='_blank' && $woINFRA_TYPE!='disable')
				{
					insertCustomFeild($mysql,"INFRA_TYPE",$woINFRA_TYPE,$getWoId);
				}
				if(!empty($woREQ_TYPE) && $woREQ_TYPE!='_blank' && $woREQ_TYPE!='disable' && $woREQ_TYPE=='3')
				{
					$QRY_MASTER_SELECT ="SELECT `field_name`,`field_id` FROM `lnk_custom_fields_value` cfv,`lnk_custom_fields` cf where cfv.`field_key` ='CRITICAL' and cfv.field_key = cf.field_key and cf.active='1' and cf.deleted='0' order by sort_order";

					$fields_list = $mysql->sqlordie($QRY_MASTER_SELECT);

					$critical_feild_arr;
					while($row = $fields_list->fetch_assoc()){
						$critical_feild_arr[$row['field_name']] = $row['field_id'];
					}
					
					if(!empty($woCRITICAL) && $woCRITICAL =='TRUE'){
						insertCustomFeild($mysql,"CRITICAL",$critical_feild_arr['TRUE'],$getWoId);	
					}
					else
					{
						insertCustomFeild($mysql,"CRITICAL",$critical_feild_arr['FALSE'],$getWoId);	
					}
				}
				
				//Related Issues WO and Defect
				if(!empty($woRelatedIds)){
					$woRelatedIdsArray = explode(",",$woRelatedIds);
					if(count($woRelatedIdsArray) > 0){
						foreach($woRelatedIdsArray as $woRelatedValues){
						
							if(!empty($woRelatedValues)){
								//check if Woid is valid
								$select_wo_related= "SELECT id FROM `workorders` WHERE `id`= ? LIMIT 1";
								$select_wo_related_res = $mysql->sqlprepare($select_wo_related, array($woRelatedValues));
								if($select_wo_related_res->num_rows > 0 ){
									$insert_wo_related_issue = "INSERT INTO workorder_related_issues SET wid ='".$getWoId."', issue_type = 'WO', related_id = '".$woRelatedValues."'"; 
									$mysql->sqlordie($insert_wo_related_issue);
									
									}
									
									
								
								}
							
							}
						
						
						}
					
					}
					
					//Related Issue Defect
					if(!empty($dfRelatedIds)){
					$dfRelatedIdsArray = explode(",",$dfRelatedIds);
					if(count($dfRelatedIdsArray) > 0){
						foreach($dfRelatedIdsArray as $dfRelatedValues){
						
							if(!empty($dfRelatedValues)){
								//check if Woid is valid
								$select_df_related= "SELECT id FROM `qa_defects` WHERE `id`= ? LIMIT 1";
								$select_df_related_res = $mysql->sqlprepare($select_df_related, array($dfRelatedValues));
								if($select_df_related_res->num_rows > 0 ){
									$insert_df_related_issue = "INSERT INTO workorder_related_issues SET wid ='".$getWoId."', issue_type = 'DF', related_id = '".$dfRelatedValues."'"; 
									$mysql->sqlordie($insert_df_related_issue);
									
									}
									
									
								
								}
							
							}
						
						
						}
					
					}
					
					

			}
			insertWorkorderAudit_req_type($mysql,$getWoId,'6',$_SESSION['user_id'],$woAssignedTo,$woStatus,$woREQ_TYPE);
		} else {
			if(!empty($woEstDate)) {
				$dt_part = @explode("/", $woEstDate);
				$dt = "'" .@$dt_part[2] ."-" .@$dt_part[0] ."-" .@$dt_part[1] ."'";
			} else {
				$dt = "null";
			}

				//If ticket is fixed
				/*
				$displayStatusArray = array();

				//1-Closed, 3-Fixed,4-On Hold,5-Need More Info,6-New,7-In Progress,10-Feedback Provided,11-Rejected,12-Reopened
				
				*/
			//echo $wo_old_row['status']."---".$woStatus;
			if($wo_old_row['status'] != $woStatusIdHidden && $commentSubmit == 'comment'){
			
				$woStatus = $wo_old_row['status'];
				$woAssignedTo = $wo_old_row['assigned_to'];
			}			
		
			
			if($wo_old_row['assigned_to'] != $woAssignedTo) {
				$assigned_date = "`assigned_date`=NOW(), ";
			}

			$close_date = "";
			if(($woStatusIdHidden != $woStatus) && ($woStatus == 1)){
				$close_date = "`closed_date`=NOW(), ";
			}
			
			$complete_date = "";
			if( ($woStatusIdHidden != $woStatus) && ($woStatus == 3)){
				$complete_date = "`completed_date`=NOW(), ";
			}
			$estimated_date = '';
			if($woREQ_TYPE == '3'){
				$estimated_date =  " `estimated_date` = '$sql_date', ";
			}
			// When a draft WO is getting activated
			$createdDate = "";
			if($wo_old_row['active'] == '0' && $isActive == '1'){
				$createdDate = "`creation_date`=NOW(), ";
			}
			
			
			$update_wo = "UPDATE `workorders` SET "
				."`project_id`='$projectId', "
				."`assigned_to`='$woAssignedTo', "
				.$assigned_date
				.$close_date
				.$complete_date
				.$createdDate
				.$estimated_date
				."`type`=$woTypeId, "
				."`status`='$woStatus', "
				."`title`='$woTitle', "
				."`example_url`='$woExampleURL', "
				."`body`='$woDesc', "
				."`requested_by`='$requestedId', "
				."`completed_by`='$completed_by', "
				."`launch_date`='$sql_date', "
				."`rally_type`='$rallyType', "
				."`rally_project_id`='$rallyProject', "
				."`cclist`='$woCCList', "
				.$updatesql_draft_date
				."`active`='$isActive' "
				."WHERE `id`='$woId'";
			@$mysql->sqlordie($update_wo);
			$getWoId = $woId;
			Util::updateMileStone($woAssignedTo,$getWoId,$woTitle,$sql_date,$woStatus);
			$select_wo_req_type = "SELECT field_id from workorder_custom_fields where field_id IN('1','2','3') and workorder_id IN('$woId')";
			$old_req_type = $mysql->sqlordie($select_wo_req_type);
			$old_req_type_row = $old_req_type->fetch_assoc();
			if($woREQ_TYPE != $old_req_type_row['field_id']){
				insertWorkorderAudit_req_type($mysql,$woId,'6',$_SESSION['user_id'],$woAssignedTo,$woStatus,$woREQ_TYPE);	
			}

			if(!empty($woREQ_TYPE) && $woREQ_TYPE!='_blank' && $woREQ_TYPE!='disable')
			{
				updateCustomFeild($mysql,"REQ_TYPE",$woREQ_TYPE,$getWoId);
			}
			if(!empty($woSEVERITY) && $woSEVERITY!='_blank' && $woSEVERITY!='disable')
			{
				updateCustomFeild($mysql,"SEVERITY",$woSEVERITY,$getWoId);
			}
			if(!empty($woSITE_NAME) && $woSITE_NAME!='_blank' && $woSITE_NAME!='disable')
			{
				updateCustomFeild($mysql,"SITE_NAME",$woSITE_NAME,$getWoId);
			}
			if(!empty($woINFRA_TYPE) && $woINFRA_TYPE!='_blank' && $woINFRA_TYPE!='disable')
			{
				updateCustomFeild($mysql,"INFRA_TYPE",$woINFRA_TYPE,$getWoId);
			}
			if(!empty($woREQ_TYPE) && $woREQ_TYPE!='_blank' && $woREQ_TYPE!='disable' && $woREQ_TYPE=='3')
			{
					$QRY_MASTER_SELECT ="SELECT `field_name`,`field_id` FROM `lnk_custom_fields_value` cfv,`lnk_custom_fields` cf where cfv.`field_key` ='CRITICAL' and cfv.field_key = cf.field_key and cf.active='1' and cf.deleted='0' order by sort_order";

					$fields_list = $mysql->sqlprepare($QRY_MASTER_SELECT);

					$critical_feild_arr;
					while($row = $fields_list->fetch_assoc()){
						$critical_feild_arr[$row['field_name']] = $row['field_id'];
					}
					
					if(!empty($woCRITICAL) && $woCRITICAL =='TRUE'){
						updateCustomFeild($mysql,"CRITICAL",$critical_feild_arr['TRUE'],$getWoId);	
					}
					else
					{
						updateCustomFeild($mysql,"CRITICAL",$critical_feild_arr['FALSE'],$getWoId);	
					}
			}
		}			
			

		}
		
		if(!empty($getWoId) && $getWoId > 0) {
			@rename($_SERVER['DOCUMENT_ROOT']."/files/" .$dirName,  $_SERVER['DOCUMENT_ROOT']."/files/" .$getWoId);
			
			$update_files = "UPDATE `workorder_files` SET `workorder_id`='$getWoId', `directory`='$getWoId' WHERE `directory`='" .str_replace("/", "", $dirName) ."'";
			@$mysql->sqlordie($update_files);
		}
		
		$select_wo = "SELECT * FROM `workorders` WHERE `id`= ?";
		$wo_res = $mysql->sqlprepare($select_wo,array($getWoId));
		$wo_row = $wo_res->fetch_assoc();
		$select_user = "SELECT * FROM `users` WHERE `id`= ?";
		$user_res = $mysql->sqlprepare($select_user,array($woAssignedTo));
		$assigned_user_row = $user_res->fetch_assoc();

		// To change the assigned To field when the client is providing a feedback.
		if($_SESSION['login_status'] == 'client' && ($wo_row['assigned_to'] != $wo_old_row['assigned_to'])){
			$assigned_option_html = '<option value="'.$assigned_user_row['id'].'">'. $assigned_user_row['last_name'] .', '. $assigned_user_row['first_name'] .'</option>';
		}else{
			$assigned_option_html = '';
		}
		
		if($wo_row['launch_date'] != $wo_old_row['launch_date'] && ($wo_old_row['launch_date']) != ''){
				//echo $wo_row['launch_date'];
			if(($wo_old_row['launch_date'] != '0000-00-00 00:00:00') || ( $wo_old_row['launch_date'] != '') || ($wo_row['launch_date'] != '0000-00-00 00:00:00')||( $wo_old_row['launch_date'] != '')){
				insertWorkorderAudit($mysql,$getWoId, '10', $_SESSION['user_id'],$wo_row['assigned_to'],$woStatus);
				$last_audit_id = $mysql->insert_id;
				$mysql->sqlordie("INSERT INTO `workorder_date_log` SET previous_launch_date = '".$wo_old_row['launch_date']."' , audit_id = '".$last_audit_id."',  new_launch_date = '".$wo_row['launch_date']."' , user_id ='".$_SESSION['user_id']."' , wid ='".$getWoId."'");
			}
		}
		if(($wo_row['completed_by'] != $wo_old_row['completed_by']) && ($woStatus == '3')){
			if(empty($wo_old_row['completed_by'])){
			
				insertWorkorderAudit($mysql,$getWoId, '11', $_SESSION['user_id'],$wo_row['completed_by'],$woStatus);
			}else{
				insertWorkorderAudit($mysql,$getWoId, '12', $_SESSION['user_id'],$wo_row['completed_by'],$woStatus);
			
			}
		
		
		
		}
		//echo $wo_row['completed_by']." ".$wo_old_row['completed_by'];
		if($wo_row['launch_date'] != $wo_old_row['launch_date'] && ($wo_old_row['launch_date']) != ''){
				//echo $wo_row['launch_date'];
			if(($wo_old_row['launch_date'] != '0000-00-00 00:00:00') || ( $wo_old_row['launch_date'] != '') || ($wo_row['launch_date'] != '0000-00-00 00:00:00')||( $wo_old_row['launch_date'] != '')){
				insertWorkorderAudit($mysql,$getWoId, '10', $_SESSION['user_id'],$wo_row['assigned_to'],$woStatus);
				$last_audit_id = $mysql->insert_id;
				$mysql->sqlordie("INSERT INTO `workorder_date_log` SET previous_launch_date = '".$wo_old_row['launch_date']."' , audit_id = '".$last_audit_id."',  new_launch_date = '".$wo_row['launch_date']."' , user_id ='".$_SESSION['user_id']."' , wid ='".$getWoId."'");
			}
		}
		$cclist = array();
		if("" != trim($wo_row['cclist'])){
			$cclist = explode(",", $wo_row['cclist']);
		}

		for($v = 0; $v < sizeof($cclist); $v++) {
			if($cclist[$v] != '')
				$users_email[$cclist[$v]] = true;
		}
		$users_email[$requestedId] = true;
		$users_email[$woAssignedTo] = true;

		$user_keys = array_keys($users_email);
		$request_type_arr = array("Submit a Request" => "Request", "Report a Problem" => "Problem","Report an Outage" => "Outage");
		$select_project = "SELECT * FROM `projects` WHERE `id`= ?";
		$project_res = $mysql->sqlprepare($select_project,array($wo_row['project_id']) );
		$project_row = $project_res->fetch_assoc();
		//p($project_row);
		$select_company = "SELECT * FROM `companies` WHERE `id`= ?";
		$company_res = $mysql->sqlprepare($select_company ,array($project_row['company']));
		$company_row = $company_res->fetch_assoc();
		$wo_status = "SELECT * FROM `lnk_workorder_status_types` WHERE `id`= ?";
		$wo_status_res = $mysql->sqlprepare($wo_status,array($woStatus));
		$wo_status_row = $wo_status_res->fetch_assoc();

		$wo_req_type = "SELECT * FROM `lnk_custom_fields_value` WHERE `field_id`= ?";
		$wo_req_type_res = $mysql->sqlprepare($wo_req_type, array($woREQ_TYPE));
		$wo_req_type_row = $wo_req_type_res->fetch_assoc();

		//$subject = "".$getWoId." - ".$wo_req_type_row['field_name']." - " . $woDesc . "";
		//$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";
		$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=UTF-8";

		$requestor_qry = "SELECT * FROM `users` WHERE `id`= ?";
		$requestor_user_res = $mysql->sqlprepare($requestor_qry, array($requestedId ));
		$requestor_user_row = $requestor_user_res->fetch_assoc();


		$site_name_qry = "SELECT `field_name`,`field_id` FROM `lnk_custom_fields_value` cfv,`lnk_custom_fields` cf where cfv.`field_key` ='SITE_NAME' and cfv.field_key = cf.field_key and cf.active='1' and cf.deleted='0' and cfv.`field_id` = '".$woSITE_NAME."' order by sort_order";
		$site_name_res = $mysql->sqlordie($site_name_qry);
		$site_name_row = $site_name_res->fetch_assoc();

			$file_list = "";
			$select_file = "SELECT * FROM `workorder_files` WHERE workorder_id= ? order by upload_date desc";
			$file_res = $mysql->sqlprepare($select_file, array($getWoId ));
			if($file_res->num_rows > 0) {
				$file_list = "<u><b>Attachment:</b></u><br>";
				$fileCount = 1;
				while($file_row = $file_res->fetch_assoc()){
					//$file_list .= "" . $fileCount . ". <a href='".BASE_URL . "/files/" . $file_row['directory'] . "/" .$file_row['file_name']."'>" . $file_row['file_name'] . "</a><br>";
				$file_list .= "" . $fileCount . ". <a target='_blank' href='".BASE_URL_FILE_PATH . "files/" . $file_row['directory'] . "/" .$file_row['file_name']."'>" . $file_row['file_name'] . "</a><br>";
	
				$fileCount += 1;
				}
			}
		if($isActive == 0)
		{	  	
			// this block is for any draft work orders
		}else	if(empty($woId) || ($wo_row['active'] == 1 && $wo_old_row['active'] == 0)){
			// When a new WO is created
			sendEmail_newRequest($mysql, $wo_row);			
			insertWorkorderAudit($mysql,$getWoId, '1', $_SESSION['user_id'],$user,$woStatus);
		}else if($wo_row['assigned_to'] != $wo_old_row['assigned_to']){

			sendEmail_assignedTO($mysql, $wo_row);			
			insertWorkorderAudit($mysql,$getWoId, '2', $_SESSION['user_id'],$woAssignedTo,$woStatus);
		}
		else if($wo_row['status'] != $wo_old_row['status']){
		//1-Closed, 3-Fixed,4-On Hold,5-Need More Info,6-New,7-In Progress,10-Feedback Provided,11-Rejected,12-Reopened			

			if($woStatus == '1' || $woStatus == '3' || $woStatus == '4' ||  $woStatus == '7' || $woStatus == '10'|| $woStatus == '11' || $woStatus == '12'){
							
				$woStatusText = $wo_status_row['name'];

				//LH 20679 #remove special characters from title
				$subject = "WO ".$getWoId.": ".$woStatusText." - ".$wo_req_type_row['field_name']." - " . $wo_row['title'] . "";
				$description=($wo_row['body']);
                $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES,'UTF-8'));
				foreach($users_email as $user => $val){
					$emailSendFlag = false;
					if($woStatus =='7' && $user ==$requestedId )
					{
						$emailSendFlag = true;
						$bodyTxt = " is now being actively worked on by an engineer.  You will be updated via work order notes or will be notified when it is completed via the Lighthouse system.";

					}
					else if($woStatus =='4')
					{
						$emailSendFlag = true;
						$bodyTxt = " has been put On Hold. If you feel this is in error, please comment in the work order.";
						
					}
					else if ($woStatus =='10' && $user ==$woAssignedTo  )
					{
						$emailSendFlag = true;
						$bodyTxt = " has feedback provided.  Please check the request to ensure all the information needed is now available.";
					}
					else if ($woStatus =='3')
					{
						$emailSendFlag = true;
						if($user ==$requestedId)
						{					
							$bodyTxt = " has been completed.  Please validate the work done and if it meets your acceptance close the work order.  If it has been marked completed in error, please reject the work order.  This will assign it back to the engineer. If no action is taken, the work order will automatically close in 3 days.";
						}
						else if($user ==$woAssignedTo)
						{
							$bodyTxt = " has been completed. The requestor will validate the work and take appropriate action. The work order will automatically close in 3 days.";
						}
						else
						{
							$bodyTxt = " has been completed.  The requestor will validate the work and take appropriate action.  The work order will automatically close in 3 days if no action is taken by the requestor.";
						}
					}
					else if ($woStatus =='1' && $user ==$requestedId)
					{
						$emailSendFlag = true;
						$bodyTxt = " is now closed. Thank you for contacting Digital Products and Services.";
					}
					else if ($woStatus =='11' && $user == $woAssignedTo)
					{
						$emailSendFlag = true;
						$bodyTxt = " has been rejected by the requestor. Please see the comments by the requestor or reach out to the requestor to see what other work remains to be completed.";
					}
					else if ($woStatus =='12')
					{
						$emailSendFlag = true;

						if($user ==$requestedId)
						{					
							$bodyTxt = " has been reopened.  An engineer will review the work order and take appropriate action.";
						}
						else if($user ==$woAssignedTo)
						{
							$bodyTxt = " has been reopened.  Please see the comments in the work order to see what other work remains to be completed.";
						}					
					}
					
					if($emailSendFlag)
					{
						$select_email_addr = "SELECT * FROM `users` WHERE `id`= ? LIMIT 1";
						$email_addr_res = $mysql->sqlprepare($select_email_addr, array($user));
						$email_addr_row = $email_addr_res->fetch_assoc();
						$to = $email_addr_row['email'];

						$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$getWoId."'>".$getWoId."</a>";
							
						$msg =  "<b>Requestor: </b>" . $requestor_user_row['first_name'].' '. $requestor_user_row['last_name']. "<br><br>";
						$msg .="<b>Company: </b>" . $company_row['name'] . "<br><br>";
						$msg .="<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br><br>";
						$msg .="<b>Site: </b>" .$site_name_row['field_name'] ."<br><br>";				
						$msg .="<b>WO [" . $link . "] </b>".$bodyTxt."<br><br>";
						$msg .="<b>Request Type: </b>" .$request_type_arr[$wo_req_type_row['field_name']] ."<br>";

						//If ticket is critical then set header as Higher Priority
						$select_req_type_qry_critical = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a INNER JOIN `lnk_custom_fields_value` b  ON (a.field_id = b.field_id) WHERE `workorder_id`='$getWoId' and a.field_key='CRITICAL' ";
						$req_type_res_cri = $mysql->sqlordie($select_req_type_qry_critical);
						$req_type_row_critical = $req_type_res_cri->fetch_assoc();
						
						if($req_type_row_critical['field_id']== '13'){
							$headers .= "\r\n";
							$headers .= "X-Priority: 1 (Highest)";
							$headers .= "\r\n";
							$headers .= "X-MSMail-Priority: High";
							$headers .= "\r\n";
							$headers .= "Importance: High";
						}
						///////////////////END




//code for lh 18306 
							
					    $severity_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SEVERITY' and cu.field_key = 'SEVERITY' and cu.workorder_id = '$getWoId'";
							
							
				
			            $severity_name_res = $mysql->sqlordie($severity_name_qry);
			            $severity_name_row = $severity_name_res->fetch_assoc();
		
				        if($request_type_arr[$wo_req_type_row['field_name']]=='Problem')
				       {
				
				       $msg .="<br><b>Severity: </b>" .$severity_name_row['field_name']  ."<br>";
				        }
	//End Code	






						$msg .="<hr><b>Description:</b> " . $desc_string ."<br><br>";
						$msg .=$file_list;

						if(!empty($to)){					
							sendEmail($to, $subject, $msg, $headers);
						}
									
					}
							
				}
			
			}
			
			insertWorkorderAudit($mysql,$getWoId, '3', $_SESSION['user_id'],$wo_row['assigned_to'],$woStatus);
			
		}
		
		
/*		else
		{
			insertWorkorderAudit($mysql,$getWoId, '3', $_SESSION['user_id'],$wo_row['assigned_to'],$woStatus);
		}
*/
		// Format : wo_id~statusId~assignedTo~assignedToHtml (assignedToHtml : is applicable only for client updates)
		echo $getWoId.'~'.$woStatus.'~'.$woAssignedTo.'~'.$assigned_option_html;
	}


	function sendEmail($to, $subject, $msg, $headers){
		$msg = nl2br($msg);
		$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
		$headers .= "\r\n" .
    					"Reply-To: ".COMMENT_REPLY_TO_EMAIL. "\r\n";
		mail($to, $subject, $msg, $headers);
	}

	function insertCustomFeild($mysql,$field_key, $field_id, $wo_id)
	{
		$insert_custom_feild = "INSERT INTO  `workorder_custom_fields` (`field_key`,`field_id`,	`workorder_id`)  values ('".$field_key."','".$field_id."','".$wo_id."')";
		@$mysql->sqlordie($insert_custom_feild);
	}

	function updateCustomFeild($mysql,$field_key, $field_id, $wo_id)
	{
		$update_custom_feild = "select * from `workorder_custom_fields` where workorder_id = '".$wo_id."' and field_key = '".$field_key."'";
		$updateFlag = @$mysql->sqlordie($update_custom_feild);
		
		if($updateFlag->num_rows == 1) {
			$update_custom_feild = "UPDATE `workorder_custom_fields` set `field_id` = '".$field_id."' where workorder_id = '".$wo_id."' and field_key = '".$field_key."'";
			@$mysql->sqlordie($update_custom_feild);		
		}
		else if($updateFlag->num_rows == 0)
		{
			insertCustomFeild($mysql,$field_key, $field_id, $wo_id);
		}
	}

	function insertWorkorderAudit($mysql,$wo_id, $audit_id, $log_user_id,$assign_user_id,$status)
	{
		
		$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
//		echo "qry=".$insert_custom_feild;die();
		$mysql->sqlordie($insert_custom_feild);
	}

	function insertWorkorderAudit_req_type($mysql,$wo_id, $audit_id, $log_user_id,$assign_user_id,$status,$request_type)
	{
		
		$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`,`Request_type`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW(),'".$request_type."')";
		$mysql->sqlordie($insert_custom_feild)  ;
	}
	
	function dateTimeToSql($date,$time,$ampm,$min)
	{
		if(!empty($date)) 
		{
			$dt_part = @explode("/", $date);				

			if(!empty($time)) 
			{
				$tm_part = @explode(":", $time);				
				if($ampm == "pm") {
					if($tm_part[0] < 12) {
						$tmAdd = 12;
					} else {
						$tmAdd = 0;
					}
				} else {
					if($ampm == "am") {
						if($tm_part[0] == 12) {
							$tmAdd = -12;
						} else {
							$tmAdd = 0;
						}
					} else {
						$tmAdd = 0;
					}
				}
			} else {
				$tm_part[0] = 0;
				$tm_part[1] = 0;
				$tm_part[2] = 0;					
				$tmAdd = 0;
			}
			$sql_date = "'" .@date("Y-n-j G:i:s", @mktime(@$tm_part[0]+$tmAdd , @$tm_part[1]+$min, @$tm_part[2], @$dt_part[0], @$dt_part[1], @$dt_part[2])) ."'";
		} 
		else 
		{
			$dt_part[0] = 0;
			$dt_part[1] = 0;
			$dt_part[2] = 0;
			
			$tm_part[0] = 0;
			$tm_part[1] = 0;
			$tm_part[2] = 0;
			
			$tmAdd = 0;
			
			$sql_date = "null";
		}
		$sql_date = @date("Y-n-j G:i:s", @mktime(@$tm_part[0]+$tmAdd , @$tm_part[1]+$min, @$tm_part[2], @$dt_part[0], @$dt_part[1], @$dt_part[2]));
		return $sql_date;
  }
  
  function dateTimeToSql_new($date,$time,$ampm,$min)
	{
		$dt_part = @explode("/", $date);
		$tm_part = @explode(":", $time);
		
		if(strpos($tm_part[1]," PM") > 1){
			if($tm_part[0] < 12) {
				$tmAdd = 12;
			} else {
				$tmAdd = 0;
			}
			$tm_part[1] = str_replace(" PM","",$tm_part[1]);
		}else{
			if($tm_part[0] == 12) {
				$tmAdd = -12;
			} else {
				$tmAdd = 0;
			} 
			$tm_part[1] = str_replace(" AM","",$tm_part[1]);
		}
	
		$sql_date = @date("Y-n-j G:i:s", @mktime(@$tm_part[0]+$tmAdd , @$tm_part[1], @$tm_part[2], @$dt_part[0], @$dt_part[1], @$dt_part[2]));
		return $sql_date;
  }
?>
