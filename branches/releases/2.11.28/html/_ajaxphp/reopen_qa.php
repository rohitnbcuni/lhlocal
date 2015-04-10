<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$defectId = $mysql->real_escape_string($_GET['defectId']);
	
	$update_wo = "UPDATE `qa_defects` SET `closed_date`=NULL, `status`='2' WHERE `id`= ? ";
	@$mysql->sqlprepare($update_wo, array($defectId));
	

	$select_wo = "SELECT * FROM `qa_defects` WHERE `id`= ? ";
	$wo_res = $mysql->sqlprepare($select_wo, array($defectId));
	$wo_row = $wo_res->fetch_assoc();

	$select_project = "SELECT * FROM `projects` WHERE `id`='" .$wo_row['project_id'] ."'";
	$project_res = $mysql->sqlordie($select_project);
	$project_row = $project_res->fetch_assoc();

	$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
	$company_res = $mysql->sqlordie($select_company);
	$company_row = $company_res->fetch_assoc();

	$select_priority = "SELECT * FROM `lnk_workorder_priority_types` WHERE `id`='" .$wo_row['priority'] ."'";
	$pri_res = $mysql->sqlordie($select_priority);
	$pri_row = $pri_res->fetch_assoc();

	$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$defectId' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
	$req_type_res = $mysql->sqlordie($select_req_type_qry);
	$req_type_row = $req_type_res->fetch_assoc();
	
	$select_user = "SELECT * FROM `users` WHERE `id`='" .$wo_row['assigned_to'] ."'";
	$user_res = $mysql->sqlordie($select_user);
	$user_row = $user_res->fetch_assoc();

	$subject = "WO: Reopen - " . $wo_row['title'] . " - Lighthouse Work Order Message";
	$headers = 'From: '.WO_EMAIL_FROM.'';
	$to = $user_row['email'];
	$description=($wo_row['body']);
    $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description));
		$msg =  "Company: " . $company_row['name'] . "\r\n"
				."Project: " . $project_row['project_code'] ." - " . $project_row['project_name'] ."\r\n"
				."Link: " .BASE_URL ."/workorders/index/edit/?wo_id=" . $defectId  ."\r\n\r\n"
				."WO [#" . $defectId . "] has been reopened by " . $_SESSION['first'] . " ". $_SESSION['last'] . "\r\n\r\n"
				."\t-Request Type: " . $req_type_row['field_name'] ."\r\n"
				."\t-Description: " .$desc_string ."\r\n\r\n"
				."..........................................................................";
		if(!empty($to)){
			
			mail($to, $subject, $msg, $headers);
		}

?>
