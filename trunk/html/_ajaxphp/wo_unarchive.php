<?PHP
	session_start();
	include('../_inc/config.inc');
	$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	if(isset($_SESSION['user_id'])) {
		$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

		//$wo_id = $mysql->real_escape_string($_GET['id']);

		$workorderList =explode(",", @$_GET['id']);

		foreach($workorderList as $wo_id)
		{ 
			if(!empty($wo_id))
			{
		
			$unarchive_query = "UPDATE `workorders` SET `archived`='0', `status`='6',`closed_date`=NULL WHERE `id`='$wo_id'";
			@$mysql->query($unarchive_query);

			$select_wo = "SELECT * FROM `workorders` WHERE `id`='" .$wo_id ."'";
			$wo_res = $mysql->query($select_wo);
			$wo_row = $wo_res->fetch_assoc();

			$audit_insert_query = "INSERT INTO  `workorder_audit` (`id`,`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('','".$wo_id."','3','".$_SESSION['user_id']."','".$wo_row['assigned_to']."','12',NOW())";
			$mysql->query($audit_insert_query);
			
			$select_user = "SELECT * FROM `users` WHERE `id`='" .$wo_row['assigned_to'] ."'";
			$user_res = $mysql->query($select_user);
			$user_row = $user_res->fetch_assoc();

			$select_project = "SELECT * FROM `projects` WHERE `id`='" .$wo_row['project_id'] ."'";
			$project_res = $mysql->query($select_project);
			$project_row = $project_res->fetch_assoc();

			$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
			$company_res = $mysql->query($select_company);
			$company_row = $company_res->fetch_assoc();
			
			$request_type_arr = array("Submit a Request" => "Request", "Report a Problem" => "Problem","Report an Outage" => "Outage");

			$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$wo_id' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
			$req_type_res = $mysql->query($select_req_type_qry);
			$req_type_row = $req_type_res->fetch_assoc();
	        $description=($wo_row['body']);
            $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description));
			$subject = "WO ".$wo_id.": Unarchived - ".$req_type_row['field_name']." - " . $wo_row['title'] . "";
			$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";
    
			$to = $user_row['email'];
			$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$wo_id."'>".$wo_id."</a>";
			$msg =  "<b>Company: </b>" . $company_row['name'] . "<br>"
					."<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br>"
					."<b>WO [" . $link . "] </b> has been unarchived and assigned to  <b>" . $user_row['email'] ."</b> by " . $_SESSION['first'] . " ". $_SESSION['last']."<br><br>"
					."<b>Request Type: </b>" .$request_type_arr[$req_type_row['field_name']] ."<br>"
					."<hr><b>Description: </b>" . $desc_string ."<br>"
					."";	
			$msg = nl2br($msg);
		
			mail($to, $subject, $msg, $headers);

		}
	  
	  }

	}
?>
