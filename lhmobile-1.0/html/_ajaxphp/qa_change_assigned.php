<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	if(isset($_SESSION['user_id'])) {
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		//Defining Global mysql connection values
		global $mysql;

		$defect_id = $mysql->real_escape_string($_GET['id']);
		$assigned_id = $mysql->real_escape_string($_GET['user_id']);
		
		$select_qa_old = "SELECT * FROM `qa_defects` WHERE `id`= ? ";
		$qa_old_res = $mysql->sqlprepare($select_qa_old,array($defect_id));
		$qa_old_row = $qa_old_res->fetch_assoc();
		
		if($qa_old_row['assigned_to'] != $assigned_id) {
			$assigned_query = ",`assigned_date`=NOW(),`status`='1'";
		} else {
			$assigned_query = "";
		}
		
		$update_assigned = "UPDATE `qa_defects` SET `assigned_to`='$assigned_id'$assigned_query WHERE `id`='$defect_id'";
		@$mysql->sqlordie($update_assigned);
		
		/********************Email new Change*****************/

		$select_qa = "SELECT * FROM `qa_defects` WHERE `id`= ? ";
		$qa_res = $mysql->sqlprepare($select_qa,array($defect_id));
		$qa_row = $qa_res->fetch_assoc();

		insertWorkorderAudit($mysql,$defect_id, '2', $_SESSION['user_id'],$qa_row['assigned_to'],$qa_row['status']);
		
		$select_user = "SELECT * FROM `users` WHERE `id`= ? ";
		$user_res = $mysql->sqlprepare($select_user,array($assigned_id));
		$user_row = $user_res->fetch_assoc();
		
		$cclist = explode(",", $qa_row['cclist']);
		
		for($v = 0; $v < sizeof($cclist); $v++) {
			$users_email[$cclist[$v]] = true;
		}
		$users_email[$qa_row['assigned_to']] = true;
		//$users_email[$qa_row['requested_by']] = true;
		
		$user_keys = array_keys($users_email);
		$select_project = "SELECT * FROM `projects` WHERE `id`= ? ";
		$project_res = $mysql->sqlprepare($select_project,array($qa_old_row['project_id']));
		$project_row = $project_res->fetch_assoc();
		
		$wo_status = "SELECT * FROM `lnk_qa_status_types` WHERE `id`= ? ";
		$wo_status_res = $mysql->sqlprepare($wo_status,array($qa_row['status']));
		$wo_status_row = $wo_status_res->fetch_assoc();

		$select_company = "SELECT * FROM `companies` WHERE `id`= ? ";
		$company_res = $mysql->sqlprepare($select_company,array($project_row['company']));
		$company_row = $company_res->fetch_assoc();

		for($u = 0; $u < sizeof($user_keys); $u++) {
			$select_email_addr = "SELECT `email` FROM `users` WHERE `id`= ? LIMIT 1";
			$email_addr_res = $mysql->sqlprepare($select_email_addr,array($user_keys[$u]));
			$email_addr_row = $email_addr_res->fetch_assoc();
			
			$to = $email_addr_row['email'];

			$subject = "Defect ".$defect_id.": ".$wo_status_row['name']." - ".$qa_row['title'];

			$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";
			$description=($qa_row['body']);
            $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description));
			if($qa_row['assigned_to'] != $qa_old_row['assigned_to']) {
				
					$link = "<a href='".BASE_URL ."/quality/index/edit/?defect_id=" .$defect_id."'>".$defect_id."</a>";					
					$msg = "Defect [$link] has been assigned to you.<br>";
					$msg.= "<b>Company: </b>" . $company_row['name'] . "<br>";
					$msg.= "<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br>";
					$msg.="<hr>";
					$msg.= $desc_string ."<br><br>";
					$msg.= $file_list."<br>";
			}
			
			mail($to, $subject, $msg, $headers);
		}
	}

	function insertWorkorderAudit($mysql,$defect_id, $audit_id, $log_user_id,$assign_user_id,$status)
	{
		$insert_custom_feild = "INSERT INTO  `quality_audit` (`defect_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$defect_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
		@$mysql->sqlordie($insert_custom_feild);
	}
?>
