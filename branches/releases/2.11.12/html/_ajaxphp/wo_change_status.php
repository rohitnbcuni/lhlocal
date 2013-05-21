<?PHP
	session_start();
	include('../_inc/config.inc');
	$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	if(isset($_SESSION['user_id'])) {
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		global $mysql;
		$wo_id = $mysql->real_escape_string($_GET['id']);
		$status_id = $mysql->real_escape_string($_GET['status_id']);
		
		$select_wo_old = "SELECT * FROM `workorders` WHERE `id`= ?";
		$wo_old_res = $mysql->sqlprepare($select_wo_old, array($wo_id));
		$wo_old_row = $wo_old_res->fetch_assoc();
		

		$close_date = "";
		if($status_id == 1){
			$close_date = "`closed_date`=NOW(), ";
		}

		$complete_date = "";
		if($status_id == 3){
			$complete_date = "`completed_date`=NOW(), ";
		}

		$update_status = "UPDATE `workorders` SET $close_date $complete_date `status`='$status_id' WHERE `id`='$wo_id'";
		@$mysql->sqlordie($update_status);
		
		/********************Email new Change*****************/
		
		/*$select_priority = "SELECT * FROM `lnk_workorder_priority_types` WHERE `id`='" .$wo_old_row['priority'] ."'";
		$pri_res = $mysql->query($select_priority);
		$pri_row = $pri_res->fetch_assoc();*/

		$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`= ? and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
		$req_type_res = $mysql->sqlprepare($select_req_type_qry, array($wo_id));
		$req_type_row = $req_type_res->fetch_assoc();

		
		$select_wo = "SELECT * FROM `workorders` WHERE `id`= ?";
		$wo_res = $mysql->sqlprepare($select_wo, array($wo_id));
		$wo_row = $wo_res->fetch_assoc();
		

		insertWorkorderAudit($mysql,$wo_id, '3', $_SESSION['user_id'],$wo_row['assigned_to'],$wo_row['status']);
		$date_time_part = explode(" ", $row['closed_date']);
		$select_user = "SELECT * FROM `users` WHERE `id`= ?";
		$user_res = $mysql->sqlprepare($select_user, array($assigned_id));
		$user_row = $user_res->fetch_assoc();
		
		$cclist = explode(",", $wo_row['cclist']);
		
		for($v = 0; $v < sizeof($cclist); $v++) {
			$users_email[$cclist[$v]] = true;
		}
		$users_email[$wo_row['assigned_to']] = true;
		$users_email[$wo_row['requested_by']] = true;

		$woAssignedTo = $wo_row['assigned_to'];
		$requestedId = $wo_row['requested_by'];
		
		$user_keys = array_keys($users_email);
		$select_project = "SELECT * FROM `projects` WHERE `id`= ?";
		$project_res = $mysql->sqlprepare($select_project, array($wo_old_row['project_id']));
		$project_row = $project_res->fetch_assoc();

		$select_company = "SELECT * FROM `companies` WHERE `id`= ?";
		$company_res = $mysql->sqlprepare($select_company, array($project_row['company']));
		$company_row = $company_res->fetch_assoc();

		
		$request_type_arr = array("Submit a Request" => "Request", "Report a Problem" => "Problem","Report an Outage" => "Outage");				


		$requestor_qry = "SELECT * FROM `users` WHERE `id`= ?";
		$requestor_user_res = $mysql->sqlprepare($requestor_qry, array($requestedId));
		$requestor_user_row = $requestor_user_res->fetch_assoc();
		

		$site_name_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`= ? and a.field_key='SITE_NAME' and a.field_id = b.field_id";
		$site_name_res = $mysql->sqlprepare($site_name_qry, array($wo_id));
		$site_name_row = $site_name_res->fetch_assoc();
		
		$woStatus = $wo_row['status'];

		$wo_status = "SELECT * FROM `lnk_workorder_status_types` WHERE `id`=  ?";
		$wo_status_res = $mysql->sqlprepare($wo_status, array($woStatus));
		$wo_status_row = $wo_status_res->fetch_assoc();

		$woStatusText = $wo_status_row['name'];
        $description=($wo_row['body']);
        $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES,'UTF-8'));

		for($u = 0; $u < sizeof($user_keys); $u++) {
			$emailSendFlag = false;
			$user = $user_keys[$u];
			if($woStatus =='7' && $user ==$requestedId )
			{
				$emailSendFlag = true;
				$bodyTxt = " is now being actively worked on by an engineer.  You will be updated via work order notes or will be notified when it is completed via the Lighthouse system.";

			}
			else if($woStatus =='4')
			{
				$emailSendFlag = true;
				$bodyTxt = " has been put On Hold.  If you feel this is in error, please comment in the work order.";
				
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
					$bodyTxt = " has been reopened. An engineer will review the work order and take appropriate action.";
				}
				else if($user ==$woAssignedTo)
				{
					$bodyTxt = " has been reopened. Please see the comments in the work order to see what other work remains to be completed.";
				}
			}
			if($emailSendFlag)
			{
				$select_email_addr = "SELECT `email` FROM `users` WHERE `id`= ? LIMIT 1";
				$email_addr_res = $mysql->sqlprepare($select_email_addr, array($user));
				$email_addr_row = $email_addr_res->fetch_assoc();				
				$to = $email_addr_row['email'];

				//LH 20679 #remove special characters from title
				$subject = "WO ".$wo_id.": ".$woStatusText." - ".$req_type_row['field_name']." - " . html_entity_decode($wo_row['title'],ENT_NOQUOTES,'UTF-8') . "";
				//$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";
				$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=UTF-8";					
				$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$wo_id."'>".$wo_id."</a>";			

				$msg =  "<b>Requestor: </b>" . $requestor_user_row['first_name'].' '. $requestor_user_row['last_name']. "<br><br>";
				$msg .="<b>Company: </b>" . $company_row['name'] . "<br><br>";
				$msg .="<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br><br>";
				$msg .="<b>Site: </b>" .$site_name_row['field_name'] ."<br><br>";				
				$msg .="<b>WO [" . $link . "] </b>".$bodyTxt."<br><br>";										
				$msg .="<b>Request Type: </b>" .$request_type_arr[$req_type_row['field_name']] ."<br>";

//code for lh 18306
				$severity_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SEVERITY' and cu.field_key = 'SEVERITY' and cu.workorder_id = ?";
			    $severity_name_res = $mysql->sqlprepare($severity_name_qry, array($wo_id));
			    $severity_name_row = $severity_name_res->fetch_assoc();
				
				if($request_type_arr[$req_type_row['field_name']]=='Problem')
				{
				
				$msg .="<br><b>Severity: </b>" .$severity_name_row['field_name']  ."<br>";
				}
				
				// End code




                $msg .="<hr><b>Description:</b> " . $desc_string ."<br><br>";
				$msg = nl2br($msg);
				$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
			    mail($to, $subject, $msg, $headers);
			}
		}
	}


	function insertWorkorderAudit($mysql,$wo_id, $audit_id, $log_user_id,$assign_user_id,$status)
	{
		$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
		@$mysql->sqlordie($insert_custom_feild);
	}
?>
