<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
    $pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    include('../_ajaxphp/util.php');
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$woId =  $mysql->real_escape_string($_GET['woId']);
	
	$update_wo = "UPDATE `workorders` SET `closed_date`=NULL, `status`='6',`archived`='0' WHERE `id`= ?";
	@$mysql->sqlprepare($update_wo, array($woId));

	$select_wo = "SELECT * FROM `workorders` WHERE `id`= ?";
	$wo_res = $mysql->sqlprepare($select_wo, array($woId));
	$wo_row = $wo_res->fetch_assoc();

	$audit_insert_query = "INSERT INTO  `workorder_audit` (`id`,`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('','".$woId."','3','".$_SESSION['user_id']."','".$wo_row['assigned_to']."','12',NOW())";
	$mysql->sqlordie($audit_insert_query);

	$cclist = explode(",", $wo_row['cclist']);
		
	for($v = 0; $v < sizeof($cclist); $v++) {
		$users_email[$cclist[$v]] = true;
	}
	$users_email[$wo_row['assigned_to']] = true;
	$users_email[$wo_row['requested_by']] = true;

	$woAssignedTo = $wo_row['assigned_to'];
	$requestedId = $wo_row['requested_by'];
	//Update the mile stone status
	Util::updateMileStone($woAssignedTo,$woId,$wo_row['title'],$wo_row['title'],$wo_row['launch_date'],$wo_row['status']);

	$select_project = "SELECT * FROM `projects` WHERE `id`='" .$wo_row['project_id'] ."'";
	$project_res = $mysql->sqlordie($select_project);
	$project_row = $project_res->fetch_assoc();

	$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
	$company_res = $mysql->sqlordie($select_company);
	$company_row = $company_res->fetch_assoc();

	$requestor_qry = "SELECT * FROM `users` WHERE `id`='" .$requestedId ."'";
	$requestor_user_res = $mysql->sqlordie($requestor_qry);
	$requestor_user_row = $requestor_user_res->fetch_assoc();

	$site_name_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`= ? and a.field_key='SITE_NAME' and a.field_id = b.field_id";
	$site_name_res = $mysql->sqlprepare($site_name_qry, array($woId));
	$site_name_row = $site_name_res->fetch_assoc();

	$request_type_arr = array("Submit a Request" => "Request", "Report a Problem" => "Problem","Report an Outage" => "Outage");

	$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`= ? and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
	$req_type_res = $mysql->sqlprepare($select_req_type_qry, array($woId));
	$req_type_row = $req_type_res->fetch_assoc();
	
	$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=UTF-8";
	//If ticket is critical then set header as Higher Priority
	$select_req_type_qry_critical = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a INNER JOIN `lnk_custom_fields_value` b  ON (a.field_id = b.field_id) WHERE `workorder_id`='$woId' and a.field_key='CRITICAL' ";
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
	
	$select_user = "SELECT * FROM `users` WHERE `id`='" .$wo_row['assigned_to'] ."'";
	$user_res = $mysql->sqlordie($select_user);
	$assigned_user_row = $user_res->fetch_assoc();
	//LH 20679 #remove special characters from title
	 $subject = "WO ".$woId.": reopen - ".$req_type_row['field_name']." - ".$wo_row['title']. "";

	//$subject = "WO ".$woId.": reopen - ".$req_type_row['field_name']." - " . html_entity_decode($wo_row['title'],ENT_NOQUOTES,'UTF-8') . "";
	//$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";
	
	$to = $user_row['email'];
	$user_keys = array_keys($users_email);

	$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$woId."'>".$woId."</a>";
	$description=($wo_row['body']);
	//LH 20679 #remove special characters from title and decs
    $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES,'UTF-8'));
	for($u = 0; $u < sizeof($user_keys); $u++) {

				$user = $user_keys[$u];
				$emailSendFlag = false;
				if($user ==$requestedId)
				{
					$bodyTxt = " has been reopened. An engineer will review the work order and take appropriate action.";
					$emailSendFlag = true;
				}
				else if($user ==$woAssignedTo)
				{

					$bodyTxt = " has been reopened. Please see the comments in the work order to see what other work remains to be completed.";
					$emailSendFlag = true;
				}

				if($emailSendFlag)
				{
					$select_email_addr = "SELECT * FROM `users` WHERE `id`='" . $user ."' LIMIT 1";
					$email_addr_res = $mysql->sqlordie($select_email_addr);
					$email_addr_row = $email_addr_res->fetch_assoc();
					$to = $email_addr_row['email'];

					$msg =  "<b>Requestor: </b>" . $requestor_user_row['first_name'].' '. $requestor_user_row['last_name']. "<br><br>";
					$msg .="<b>Company: </b>" . $company_row['name'] . "<br><br>";
					$msg .="<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br><br>";
					$msg .="<b>Site: </b>" .$site_name_row['field_name'] ."<br><br>";				
					$msg .="<b>WO [" . $link . "] </b>".$bodyTxt."<br><br>";
					$msg .="<b>Request Type: </b>" .$request_type_arr[$req_type_row['field_name']] ."<br>"; 


//code for lh 18306


	                $severity_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SEVERITY' and cu.field_key = 'SEVERITY' and cu.workorder_id = '$woId'";
				
			        $severity_name_res = $mysql->sqlordie($severity_name_qry);
			        $severity_name_row = $severity_name_res->fetch_assoc();
		
				    if($request_type_arr[$req_type_row['field_name']]=='Problem')
				   {
				
				    $msg .="<br><b>Severity: </b>" .$severity_name_row['field_name']  ."<br>";
				    }

                     //End code




                    $msg .="<hr><b>Description:</b> " . $desc_string ."<br><br>";
					if(!empty($to)){
						lh_sendEmail($to,$subject,$msg,$headers);
					}
			 }
	}
	
	
	function lh_sendEmail($to, $subject, $msg, $headers){
		$msg = nl2br($msg);
		$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
		
		$headers .= "\r\n" .
    					"Reply-To: ".COMMENT_REPLY_TO_EMAIL. "\r\n";
		mail($to, $subject, $msg, $headers);
	}
?>
