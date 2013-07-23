<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$woId = @$_GET['woId'];
	
	$update_wo = "UPDATE `workorders` SET `closed_date`=NOW(), `completed_date`=NOW(), `status`='1' WHERE `id`='$woId'";
	@$mysql->sqlordie($update_wo);
	
	$select_wo = "SELECT `closed_date`, `assigned_to`, `requested_by`, `cclist`, `project_id`, `body`, `priority`, `title` FROM `workorders` WHERE `id`='$woId' LIMIT 1";
	$result = @$mysql->sqlordie($select_wo);
	$row = @$result->fetch_assoc();

	
	insertWorkorderAudit($mysql,$woId, '3', $_SESSION['user_id'],$row['assigned_to'],'1');
	$date_time_part = explode(" ", $row['closed_date']);
	$date_part = explode("-", $date_time_part[0]);
	if(!empty($date_part[0])) {
		$date = @$date_part[1] ."/" .@$date_part[2] ."/" .@$date_part[0];
	} else {
		$date = "";
	}
//	For Sending a mail for closing the WO
	$cclist = array();
	$users_email = array();
	if("" != trim($row['cclist'])){
		$cclist = explode(",", $row['cclist']);
	}

	for($v = 0; $v < sizeof($cclist); $v++) {
		if($cclist[$v] != '')
			$users_email[$cclist[$v]] = true;
	}
	$users_email[$row['requested_by']] = true;
	if(!empty($row['assigned_to']))
		$users_email[$row['assigned_to']] = true;

	$woAssignedTo = $row['assigned_to'];
	$requestedId = $row['requested_by'];

	$select_project = "SELECT * FROM `projects` WHERE `id`='" .$row['project_id'] ."'";
	$project_res = $mysql->sqlordie($select_project);
	$project_row = $project_res->fetch_assoc();

	$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
	$company_res = $mysql->sqlordie($select_company);
	$company_row = $company_res->fetch_assoc();

	$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$woId' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
	$req_type_res = $mysql->sqlordie($select_req_type_qry);
	$req_type_row = $req_type_res->fetch_assoc();

	$wo_status = "SELECT * FROM `lnk_workorder_status_types` WHERE `id`='1'";
	$wo_status_res = $mysql->sqlordie($wo_status);
	$wo_status_row = $wo_status_res->fetch_assoc();

	$site_name_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$woId' and a.field_key='SITE_NAME' and a.field_id = b.field_id";
	$site_name_res = $mysql->sqlordie($site_name_qry);
	$site_name_row = $site_name_res->fetch_assoc();

	$requestor_qry = "SELECT * FROM `users` WHERE `id`='" .$requestedId ."'";
	$requestor_user_res = $mysql->sqlordie($requestor_qry);
	$requestor_user_row = $requestor_user_res->fetch_assoc();		
	//LH 20679 #remove special characters from title
	$subject = "WO ".$woId.": Closed - ".$req_type_row['field_name']." - " . html_entity_decode($row['title'],ENT_NOQUOTES,'UTF-8') . "";
	//$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";
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
	$request_type_arr = array("Submit a Request" => "Request", "Report a Problem" => "Problem","Report an Outage" => "Outage");
    $description=($row['body']);
    $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES,'UTF-8'));
	foreach($users_email as $user => $val){
		if($user==$requestedId)
		{
			$select_email_addr = "SELECT `email` FROM `users` WHERE `id`= ? LIMIT 1";
			$email_addr_res = $mysql->sqlprepare($select_email_addr,array($user));
			$email_addr_row = $email_addr_res->fetch_assoc();
			$to = $email_addr_row['email'];

			$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$woId."'>".$woId."</a>";		
			$msg =  "<b>Requestor: </b>" . $requestor_user_row['first_name'].' '. $requestor_user_row['last_name']. "<br><br>";
			$msg .="<b>Company: </b>" . $company_row['name'] . "<br><br>";
			$msg .="<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br><br>";
			$msg .="<b>Site: </b>" .$site_name_row['field_name'] ."<br><br>";				
			$msg .="<b>WO [" . $link . "] </b> is now closed. Thank you for contacting Digital Products and Services.<br><br>";			
			$msg .="<b>Request Type: </b>" .$request_type_arr[$req_type_row['field_name']] ."<br>";


//code for lh 18306

	               $severity_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SEVERITY' and cu.field_key = 'SEVERITY' and cu.workorder_id = ?";
				
			$severity_name_res = $mysql->sqlprepare($severity_name_qry, array($woId));
			$severity_name_row = $severity_name_res->fetch_assoc();
		
				if($request_type_arr[$req_type_row['field_name']]=='Problem')
				{
				
				$msg .="<br><b>Severity: </b>" .$severity_name_row['field_name']  ."<br>";
				}

            //End code



			$msg .="<hr><b>Description:</b> " . $desc_string ."<br><br>";
			if(!empty($to)){
				$msg = nl2br($msg);
				mail($to, $subject, $msg, $headers);
			}
		}
	}
	echo $date .$woId;


	function insertWorkorderAudit($mysql,$wo_id, $audit_id, $log_user_id,$assign_user_id,$status)
	{
		$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
		@$mysql->sqlordie($insert_custom_feild);
	}
?>
