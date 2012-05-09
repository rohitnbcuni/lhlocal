<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	$defectId = @$_GET['defectId'];

	$update_wo = "UPDATE `qa_defects` SET `closed_date`=NOW(), `completed_date`=NOW(), `status`='8' WHERE `id`='$defectId'";
	@$mysql->query($update_wo);

	$select_wo = "SELECT `closed_date`, `assigned_to`, `detected_by`, `cclist`, `project_id`, `body`, `title` FROM `qa_defects` WHERE `id`='$defectId' LIMIT 1";
	$result = @$mysql->query($select_wo);
	$row = @$result->fetch_assoc();
	
	insertWorkorderAudit($mysql,$defectId, '3', $_SESSION['user_id'],$row['assigned_to'],'8');

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

	$users_email[$row['detected_by']] = true;
	if(!empty($row['assigned_to']))
		$users_email[$row['assigned_to']] = true;

	$select_project = "SELECT * FROM `projects` WHERE `id`='" .$row['project_id'] ."'";
	$project_res = $mysql->query($select_project);
	$project_row = $project_res->fetch_assoc();

	$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
	$company_res = $mysql->query($select_company);
	$company_row = $company_res->fetch_assoc();

	$wo_status = "SELECT * FROM `lnk_qa_status_types` WHERE `id`='8'";
	$wo_status_res = $mysql->query($wo_status);
	$wo_status_row = $wo_status_res->fetch_assoc();

	$subject = "Defect: ".$wo_status_row['name']." - This is my summary";
	$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";

	/*foreach($users_email as $user => $val){
		$select_email_addr = "SELECT `email` FROM `users` WHERE `id`='" . $user ."' LIMIT 1";
		$email_addr_res = $mysql->query($select_email_addr);
		$email_addr_row = $email_addr_res->fetch_assoc();
		$to = $email_addr_row['email'];

		$link = "<a href='".BASE_URL ."/quality/index/edit/?defect_id=" .$defectId."'>".$defectId."</a>";
		$msg = "Defect [$link] has been created and is assigned to you.<br>";
		$msg.= "<b>Company: </b>" . $company_row['name'] . "<br>";
		$msg.= "<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br>";
		$msg.= "This is the defect description.<br>";
		$msg.="<hr>";
		$msg.= htmlentities($row['body']) ."<br>";

		if(!empty($to)){

			mail($to, $subject, $msg, $headers);
		}
	}*/
	echo $date .$defectId;


	function insertWorkorderAudit($mysql,$defect_id, $audit_id, $log_user_id,$assign_user_id,$status)
	{
		$insert_custom_feild = "INSERT INTO  `quality_audit` (`defect_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$defect_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
		@$mysql->query($insert_custom_feild);		
	}
?>
