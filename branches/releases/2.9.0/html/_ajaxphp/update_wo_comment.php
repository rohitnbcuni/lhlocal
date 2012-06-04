<?PHP
	session_start();
	include('../_inc/config.inc');
	include('../_ajaxphp/util.php');
		
	if(isset($_SESSION['user_id'])) {
		
		$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		
		//$user = $_SESSION['lh_username'];
	    //$password = $_SESSION['lh_password'];
		//$woId = $mysql->real_escape_string($_POST['woId']);
		$comment_id = $mysql->real_escape_string($_POST['comment_id']);
		//LH#23699
		//$userId = $mysql->real_escape_string($_POST['userId']);
		$userId = $_SESSION['user_id'];
		/**
		 * Ticket No 16857,19352
		 * Special Character display 
		 * @var test Comment type
		 */
		 $comment = $mysql->real_escape_string(Util::escapewordquotes($_POST['comment']));
		 //Delete comment and making audit log
		 if(ISSET($_POST['del'])){
			if($_POST['del'] == 'lhcommentdelete'){
				$select_comments = "SELECT workorder_id FROM `workorder_comments` WHERE `id`='$comment_id' LIMIT 1";
				$comm_result = @$mysql->query($select_comments);
				if($comm_result->num_rows > 0){
					$wo_res = $comm_result->fetch_assoc();
					$woId = $wo_res['workorder_id'];
					$update_wo_comment = "UPDATE `workorder_comments`  SET `active` = '0',`deleted` = '1' WHERE id ='$comment_id'";
					@$mysql->query($update_wo_comment);
					$bc_id_query = "SELECT  `bcid`, `project_id`, `title`, `priority`,`status`,`assigned_to`,`body` FROM `workorders` WHERE `id`='" .$mysql->real_escape_string($woId) ."' LIMIT 1";
					$bc_id_result = $mysql->query($bc_id_query);
					$bc_id_row = $bc_id_result->fetch_assoc();
					$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$woId' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
					$req_type_res = $mysql->query($select_req_type_qry);
					$req_type_row = $req_type_res->fetch_assoc();
					insertWorkorderAudit($mysql,$woId, '8', $_SESSION['user_id'],$bc_id_row['assigned_to'],$bc_id_row['status']);
					die("ok");
				}else{
					die("Comment id not found");
				}


			}else{
				echo "Fail";
				die();
		 	 }
		 }
		$comment_html = "";
		
		if($userId != $_SESSION['user_id']) {
			$userId = $_SESSION['user_id'];
		}
		$select_comments = "SELECT workorder_id FROM `workorder_comments` WHERE `id`='$comment_id' LIMIT 1";
		$comm_result = @$mysql->query($select_comments);
		if($comm_result->num_rows > 0){
			$wo_res = $comm_result->fetch_assoc();
			$woId = $wo_res['workorder_id'];
					
		}
		
		if((!empty($comment)) && ($woId != '')){
			$update_wo_comment = "UPDATE `workorder_comments`  SET `comment` = '$comment',`date`=NOW() WHERE id ='$comment_id'";
			@$mysql->query($update_wo_comment);
						
			$bc_id_query = "SELECT  `bcid`, `project_id`, `title`, `priority`,`status`,`assigned_to`,`body` FROM `workorders` WHERE `id`='" .$mysql->real_escape_string($woId) ."' LIMIT 1";
			$bc_id_result = $mysql->query($bc_id_query);
			$bc_id_row = $bc_id_result->fetch_assoc();
			

			$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$woId' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
			$req_type_res = $mysql->query($select_req_type_qry);
			$req_type_row = $req_type_res->fetch_assoc();
			insertWorkorderAudit($mysql,$woId, '9', $_SESSION['user_id'],$bc_id_row['assigned_to'],$bc_id_row['status']);
			$select_email_users = "SELECT * FROM `workorders` WHERE `id`='$woId' LIMIT 1";
			$email_res = $mysql->query($select_email_users);


		if($email_res->num_rows > 0) {
			$new_commenter = "SELECT * FROM `users` WHERE `id`='$userId' LIMIT 1";
			$commenter_res = $mysql->query($new_commenter);
			$commenter_row = $commenter_res->fetch_assoc();
		
			$email_row = $email_res->fetch_assoc();
			
			$cc_list = $email_row['cclist'];
			$cc_list_part = explode(",", $cc_list);
			$at = $email_row['assigned_to'];
			$rb = $email_row['requested_by'];

			$woAssignedTo = $email_row['assigned_to'];
			$requestedId = $email_row['requested_by'];

			$requestor_qry = "SELECT * FROM `users` WHERE `id`='" .$requestedId ."'";
			$requestor_user_res = $mysql->query($requestor_qry);
			$requestor_user_row = $requestor_user_res->fetch_assoc();
		
			$site_name_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$woId' and a.field_key='SITE_NAME' and a.field_id = b.field_id";
			$site_name_res = $mysql->query($site_name_qry);
			$site_name_row = $site_name_res->fetch_assoc();
			
			$users_email[$at] = true;
			$users_email[$rb] = true;
			
			for($e = 0; $e < sizeof($cc_list_part); $e++) {
				if(!empty($cc_list_part[$e])) {
					$users_email[$cc_list_part[$e]] = true;
				}
			}
			$user_keys = array_keys($users_email);
			$request_type_arr = array("Submit a Request" => "Request", "Report a Problem" => "Problem","Report an Outage" => "Outage");
			$select_project = "SELECT * FROM `projects` WHERE `id`='" .$bc_id_row['project_id'] ."'";
			$project_res = $mysql->query($select_project);
			$project_row = $project_res->fetch_assoc();

			$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
			$company_res = $mysql->query($select_company);
			$company_row = $company_res->fetch_assoc();
			$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
            /*$latest_comment=(Util::escapewordquotes($_POST['comment']));
            $latest_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($latest_comment));
            $description=(Util::escapewordquotes($email_row['body']));
            $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description));*/
			 /**
             * Ticket No 18657,19352
             * remove wild char from Comment and Desc  ...
             * @var unknown_type
             */
			$latest_comment=(Util::escapewordquotes($_POST['comment']));
            $latest_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($latest_comment,ENT_NOQUOTES, 'UTF-8'));
            $description=(Util::escapewordquotes($email_row['body']));
            $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES, 'UTF-8'));
			for($u = 0; $u < sizeof($user_keys); $u++) {
				$select_email_addr = "SELECT `email` FROM `users` WHERE `id`='" .$user_keys[$u] ."' LIMIT 1";
				$email_addr_res = $mysql->query($select_email_addr);
				$email_addr_row = $email_addr_res->fetch_assoc();
				
				$to = $email_addr_row['email'];

				//LH#20679
				$subject = "WO ".$woId.": Comment - ".$req_type_row['field_name']." - " . html_entity_decode($bc_id_row['title'],ENT_NOQUOTES,'UTF-8'). "";
				$headers = "From: ".$commenter_row['email']."\nMIME-Version: 1.0\nContent-type: text/html; charset=UTF-8";
				//$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=UTF-8";

				$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$woId."'>".$woId."</a>";
                
				$msg ="<b>Latest Comment:</b> " .$latest_string ."<br><br>";
				$msg .=  "<b>Requestor: </b>" . $requestor_user_row['first_name'].' '. $requestor_user_row['last_name']. "<br><br>";
				$msg .="<b>Company: </b>" . $company_row['name'] . "<br><br>";
				$msg .="<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br><br>";
				$msg .="<b>Site: </b>" .$site_name_row['field_name'] ."<br><br>";				
				$msg .="<b>".ucfirst($commenter_row['first_name']) ." " .ucfirst($commenter_row['last_name']) ."</b> commented on work order "."<b>[" . $link . "]</b><br><br>";
				$msg .="<b>Request Type: </b>" .$request_type_arr[$req_type_row['field_name']] ."<br>";

//code for lh 18306		 
				$severity_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SEVERITY' and cu.field_key = 'SEVERITY' and cu.workorder_id = '$woId'";
				
			    $severity_name_res = $mysql->query($severity_name_qry);
			    $severity_name_row = $severity_name_res->fetch_assoc();
		
				if($request_type_arr[$req_type_row['field_name']]=='Problem')
				{
				
				$msg .="<br><b>Severity: </b>" .$severity_name_row['field_name']  ."<br><br>";
				}
				
				//End Code

              $msg .="<hr><b>Description:</b> " .$desc_string."<br><br>"; 
				if(!empty($to)) {
					sendEmail($to, $subject, $msg, $headers);
				}
			}
		}
		$text_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($_POST['comment'],ENT_NOQUOTES, 'UTF-8'));
		//End ticket
		
		$text_string = nl2br($text_string);
		echo $text_string;
		
	}
	}

	function sendEmail($to, $subject, $msg, $headers){
		$msg = nl2br($msg);
		$subject='=?UTF-8?B?'.base64_encode($subject).'?=';	
		$headers .= "\r\n" .
    				"Reply-To: ".COMMENT_REPLY_TO_EMAIL. "\r\n";
		//echo "$to, $subject, $msg, $headers";
		@mail($to, $subject, $msg, $headers);
	}

	function insertWorkorderAudit($mysql,$wo_id, $audit_id, $log_user_id,$assign_user_id,$status)
	{
		$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
		@$mysql->query($insert_custom_feild);
	}
?>
