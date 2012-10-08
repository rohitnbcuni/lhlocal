<?PHP
	session_start();
	include('../_inc/config.inc');
	
	if(isset($_SESSION['user_id'])) {
		$user = $_SESSION['lh_username'];
	    $password = $_SESSION['lh_password'];
		$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		
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
		$woTitle = $mysql->real_escape_string(@$_POST['woTitle']);
		$woExampleURL = $mysql->real_escape_string(@$_POST['woExampleURL']);
		$woDesc = $mysql->real_escape_string(htmlentities(@$_POST['woDesc']));
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

		if(empty($woAssignedTo)) {
			$woAssignedTo = 97;
		}	

		if($timeSens == "true") {
			if(!empty($timeSensDate)) {
				$dt_part = @explode("/", $timeSensDate);
				
				if(!empty($timeSensTime)) {
					$tm_part = @explode(":", $timeSensTime);
					
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
				$sql_date = "'" .@date("Y-n-j G:i:s", @mktime(@$tm_part[0]+$tmAdd , @$tm_part[1], @$tm_part[2], @$dt_part[0], @$dt_part[1], @$dt_part[2])) ."'";
			} else {
				$dt_part[0] = 0;
				$dt_part[1] = 0;
				$dt_part[2] = 0;
				
				$tm_part[0] = 0;
				$tm_part[1] = 0;
				$tm_part[2] = 0;
				
				$tmAdd = 0;
				
				$sql_date = "null";
			}
			$sql_date = @date("Y-n-j G:i:s", @mktime(@$tm_part[0]+$tmAdd , @$tm_part[1], @$tm_part[2], @$dt_part[0], @$dt_part[1], @$dt_part[2]));

		$select_wo_old = "SELECT * FROM `workorders` WHERE `id`='" .$woId ."'";
		$wo_old_res = $mysql->query($select_wo_old);
		$wo_old_row = $wo_old_res->fetch_assoc();
		
		$select_priority = "SELECT * FROM `lnk_workorder_priority_types` WHERE `id`='" .$priorityId ."'";
		$pri_res = $mysql->query($select_priority);
		$pri_row = $pri_res->fetch_assoc();
		
		$getWoId = '';
		
		if(!empty($woAssignedTo)) {
			$assignedDate = 'NOW()';
		} else {
			$assignedDate = '\'\'';
		}
		//Set $woStatus == -1 to $woStatus == 6 to change new status to assinged status
		if((empty($woStatus) || $woStatus < 1) && !empty($woAssignedTo) && $woAssignedTo != 97) {
			$woStatus = 2;
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
		
		
			$insert_wo = "INSERT INTO `workorders` "
				."(`project_id`,`assigned_to`,`type`,`status`,`title`,"
				."`example_url`,`body`,`requested_by`,`assigned_date`,"
				."`creation_date`,`rally_type`,`rally_project_id`,`launch_date`) "
				."VALUES "
				."('$projectId','$woAssignedTo',$woTypeId,'$woStatus','$woTitle',"
				."'$woExampleURL','$woDesc','$requestedId',$assignedDate,"
				."$dtStart,'$rallyType','$rallyProject','$sql_date')";
			@$mysql->query($insert_wo);
			$getWoId = $mysql->insert_id;
			
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

					$fields_list = $mysql->query($QRY_MASTER_SELECT);

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

			}
		} else {
			if(!empty($woEstDate)) {
				$dt_part = @explode("/", $woEstDate);
				$dt = "'" .@$dt_part[2] ."-" .@$dt_part[0] ."-" .@$dt_part[1] ."'";
			} else {
				$dt = "null";
			}
			$check_assigned_query = "SELECT `assigned_to` FROM `workorders` WHERE `id`='$woId' LIMIT 1";
			$check_assigned_res = $mysql->query($check_assigned_query);
			$assigned_date = "";
			if($check_assigned_res > 0) {
				$check_assigned_row = $check_assigned_res->fetch_assoc();
				
				if($check_assigned_row['assigned_to'] != $woAssignedTo) {
					$assigned_date = "`assigned_date`=NOW(), ";
					$woStatus = 2;
				}
			}
			
			$close_date = "";
			if($woStatus == 1){
				$close_date = "`closed_date`=NOW(), ";
			}
			
			$complete_date = "";
			if($woStatus == 3){
				$complete_date = "`completed_date`=NOW(), ";
			}
			$update_wo = "UPDATE `workorders` SET "
				."`project_id`='$projectId', "
				."`assigned_to`='$woAssignedTo', "
				.$assigned_date
				.$close_date
				.$complete_date
				."`type`=$woTypeId, "
				."`status`='$woStatus', "
				."`title`='$woTitle', "
				."`example_url`='$woExampleURL', "
				."`body`='$woDesc', "
				."`requested_by`='$requestedId', "
				."`launch_date`='$sql_date', "
				."`rally_type`='$rallyType', "
				."`rally_project_id`='$rallyProject' "
				."WHERE `id`='$woId'";
			@$mysql->query($update_wo);
			$getWoId = $woId;


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

					$fields_list = $mysql->query($QRY_MASTER_SELECT);

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
			@$mysql->query($update_files);
		}
		
		$select_wo = "SELECT * FROM `workorders` WHERE `id`='" .$getWoId ."'";
		$wo_res = $mysql->query($select_wo);
		$wo_row = $wo_res->fetch_assoc();
		$select_user = "SELECT * FROM `users` WHERE `id`='" .$woAssignedTo ."'";
		$user_res = $mysql->query($select_user);
		$user_row = $user_res->fetch_assoc();
		
		
		
		if($wo_row['assigned_to'] != $wo_old_row['assigned_to']) {
			$xml = '<comment>
				  <body>
					Hi ' .ucfirst($user_row['first_name']) .',
					Link: ' .BASE_URL .'/workorders/index/edit/?wo_id=' .$getWoId  .'
					You have been assigned a new workorder in Lighthouse.
					' .$wo_row['body'] .'
				  </body>
				</comment>';
					
			$set_request_url =BASECAMP_HOST.'/'.'posts/'.$wo_row['bcid'].'/comments.xml';
			
			$session = curl_init();   
			curl_setopt($session, CURLOPT_URL, $set_request_url); // set url to post to 
			curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($session, CURLOPT_POST, 1); 
			curl_setopt($session, CURLOPT_POSTFIELDS, $xml); 
			curl_setopt($session, CURLOPT_HEADER, true);
			curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session,CURLOPT_USERPWD,$user . ":" . $password);

			if(ereg("^(https)",$set_request_url)) curl_setopt($session,CURLOPT_SSL_VERIFYPEER,false);

			$response = curl_exec($session);
			//echo $response;
			$newNumPart1 = explode("/posts/", $response);
			$newNumPart2 = explode(".xml", @$newNumPart1[1]);
			
			$comment_id = $newNumPart2[0];
			curl_close($session);
		} else {
			$xml = '<comment>
				  <body>
					Link: ' .BASE_URL .'/workorders/index/edit/?wo_id=' .$getWoId  .'
					Changes have been made to this workorder in Lighthouse.
					' .$wo_row['body'] .'
				  </body>
				</comment>';
					
			$set_request_url =BASECAMP_HOST.'/'.'posts/'.$wo_row['bcid'].'/comments.xml';
			
			$session = curl_init();   
			curl_setopt($session, CURLOPT_URL, $set_request_url); // set url to post to 
			curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($session, CURLOPT_POST, 1); 
			curl_setopt($session, CURLOPT_POSTFIELDS, $xml); 
			curl_setopt($session, CURLOPT_HEADER, true);
			curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session,CURLOPT_USERPWD,$user . ":" . $password);

			if(ereg("^(https)",$set_request_url)) curl_setopt($session,CURLOPT_SSL_VERIFYPEER,false);

			$response = curl_exec($session);
			//echo $response;
			$newNumPart1 = explode("/posts/", $response);
			$newNumPart2 = explode(".xml", @$newNumPart1[1]);
			
			$comment_id = $newNumPart2[0];
			curl_close($session);
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

		$select_project = "SELECT * FROM `projects` WHERE `id`='" .$wo_row['project_id'] ."'";
		$project_res = $mysql->query($select_project);
		$project_row = $project_res->fetch_assoc();

		$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
		$company_res = $mysql->query($select_company);
		$company_row = $company_res->fetch_assoc();

		$wo_status = "SELECT * FROM `lnk_workorder_status_types` WHERE `id`='" .$woStatus ."'";
		$wo_status_res = $mysql->query($wo_status);
		$wo_status_row = $wo_status_res->fetch_assoc();

		$wo_req_type = "SELECT * FROM `lnk_custom_fields_value` WHERE `field_id`='".$woREQ_TYPE."'";
		$wo_req_type_res = $mysql->query($wo_req_type);
		$wo_req_type_row = $wo_req_type_res->fetch_assoc();
		
		$subject = "WO: ".$wo_status_row['name']." - " . $wo_row['title'] . " - Lighthouse Work Order Message";
		$headers = 'From: '.WO_EMAIL_FROM.'';
		if(empty($woId)){
			// When a new WO is created
			foreach($users_email as $user => $val){
				$select_email_addr = "SELECT `email` FROM `users` WHERE `id`='" . $user ."' LIMIT 1";
				$email_addr_res = $mysql->query($select_email_addr);
				$email_addr_row = $email_addr_res->fetch_assoc();
				$to = $email_addr_row['email'];
				$msg =  "Company: " . $company_row['name'] . "\r\n"
						."Project: " .$project_row['project_code'] ." - " .$project_row['project_name'] ."\r\n"
						."Link: " .BASE_URL ."/workorders/index/edit/?wo_id=" .$getWoId  ."\r\n\r\n"
						."WO [#" . $getWoId . "] has been created and is in the process of being assigned.\r\n\r\n"
						."\t-Request Type: " .$wo_req_type_row['field_name'] ."\r\n"
						."\t-Description: " . $wo_row['body'] ."\r\n\r\n"
						."..........................................................................";
				if(!empty($to)){  
					sendEmail($to, $subject, $msg, $headers);
				}
			}
			insertWorkorderAudit($mysql,$getWoId, '1', $_SESSION['user_id'],$user,$woStatus);
		}else if($wo_row['assigned_to'] != $wo_old_row['assigned_to']){
			// When the WO is assinged to a new person
			$sendList = array($wo_row['assigned_to'], $wo_row['requested_by']);
			$file_list = "";
			$select_file = "SELECT * FROM `workorder_files` WHERE workorder_id='" . $getWoId . "' order by upload_date desc";
			$file_res = $mysql->query($select_file);
			if($file_res->num_rows > 0) {
				$file_list = "\t-Attachment:\r\n";
				$fileCount = 1;
				while($file_row = $file_res->fetch_assoc()){
					$file_list .= "\t\t" . $fileCount . ". " . $file_row['file_name'] . "\r\n\t\t   " . BASE_URL . "/files/" . $file_row['directory'] . "/" .$file_row['file_name'] . "\r\n";
					$fileCount += 1;
				}
			}
			$sendList = array_unique($sendList);
			foreach($sendList as $user){
				$select_email_addr = "SELECT `email` FROM `users` WHERE `id`='" . $user ."' LIMIT 1";
				$email_addr_res = $mysql->query($select_email_addr);
				$email_addr_row = $email_addr_res->fetch_assoc();
				$to = $email_addr_row['email'];
				$msg =  "Company: " . $company_row['name'] . "\r\n"
						."Project: " . $project_row['project_code'] ." - " . $project_row['project_name'] ."\r\n"
						."Link: " .BASE_URL ."/workorders/index/edit/?wo_id=" . $getWoId  ."\r\n\r\n"
						."WO [#" . $getWoId . "] has been assigned to " . $user_row['email'] . "\r\n\r\n"
						."\t-Request Type: " . $wo_req_type_row['field_name'] ."\r\n"
						."\t-Description: " . $wo_row['body'] ."\r\n"
						.$file_list . "\r\n"
						."..........................................................................";
				if(!empty($to)){
					sendEmail($to, $subject, $msg, $headers);
				}
			}
			insertWorkorderAudit($mysql,$getWoId, '2', $_SESSION['user_id'],$user,$woStatus);
		}else if($woStatus == '1' || $woStatus == '3'){
			// When the WO is closed(1) or completed(3)
			if($woStatus == '1')
				$woStatusText = 'closed';
			else if($woStatus == '3')
				$woStatusText = 'completed';

			foreach($users_email as $user => $val){
				$select_email_addr = "SELECT `email` FROM `users` WHERE `id`='" . $user ."' LIMIT 1";
				$email_addr_res = $mysql->query($select_email_addr);
				$email_addr_row = $email_addr_res->fetch_assoc();
				$to = $email_addr_row['email'];
				$msg =  "Company: " . $company_row['name'] . "\r\n"
						."Project: " . $project_row['project_code'] ." - " . $project_row['project_name'] ."\r\n"
						."Link: " .BASE_URL ."/workorders/index/edit/?wo_id=" . $getWoId  ."\r\n\r\n"
						."WO [#" . $getWoId . "] has been " . $woStatusText . " by " . $_SESSION['first'] . " ". $_SESSION['last'] . "\r\n\r\n"
						."\t-Request Type: " . $wo_req_type_row['field_name'] . "\r\n"
						."\t-Description: " . $wo_row['body'] ."\r\n\r\n"
						."..........................................................................";
				if(!empty($to)){
					sendEmail($to, $subject, $msg, $headers);
				}
			}
			insertWorkorderAudit($mysql,$getWoId, '3', $_SESSION['user_id'],$wo_row['assigned_to'],$woStatus);
		}
		else if($wo_row['status'] != $wo_old_row['status'])
		{
			insertWorkorderAudit($mysql,$getWoId, '3', $_SESSION['user_id'],$wo_row['assigned_to'],$woStatus);
		}
		
		echo $getWoId;
	}

	function sendEmail($to, $subject, $msg, $headers){
		mail($to, $subject, $msg, $headers);
	}

	function insertCustomFeild($mysql,$field_key, $field_id, $wo_id)
	{
		$insert_custom_feild = "INSERT INTO  `workorder_custom_fields` (`field_key`,`field_id`,	`workorder_id`)  values ('".$field_key."','".$field_id."','".$wo_id."')";
		@$mysql->query($insert_custom_feild);
	}

	function updateCustomFeild($mysql,$field_key, $field_id, $wo_id)
	{
		$update_custom_feild = "select * from `workorder_custom_fields` where workorder_id = '".$wo_id."' and field_key = '".$field_key."'";
		$updateFlag = @$mysql->query($update_custom_feild);
		
		if($updateFlag->num_rows == 1) {
			$update_custom_feild = "UPDATE `workorder_custom_fields` set `field_id` = '".$field_id."' where workorder_id = '".$wo_id."' and field_key = '".$field_key."'";
			@$mysql->query($update_custom_feild);		
		}
		else if($updateFlag->num_rows == 0)
		{
			insertCustomFeild($mysql,$field_key, $field_id, $wo_id);
		}
	}

	function insertWorkorderAudit($mysql,$wo_id, $audit_id, $log_user_id,$assign_user_id,$status)
	{
		$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
		@$mysql->query($insert_custom_feild);
	}
	
?>