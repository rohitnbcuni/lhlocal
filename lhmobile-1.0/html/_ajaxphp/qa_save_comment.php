<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	include('../_ajaxphp/util.php');
	if(isset($_SESSION['user_id'])) {
		
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		//Defining Global mysql connection values
		global $mysql;
		$user = $_SESSION['lh_username'];
	    $password = $_SESSION['lh_password'];
		
		$defectId = $mysql->real_escape_string($_POST['defectId']);
		$userId = $mysql->real_escape_string($_POST['userId']);
		$comment = $mysql->real_escape_string(Util::escapewordquotes($_POST['comment']));
		$comment_html = "";
		$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";	
		if($userId != $_SESSION['user_id']) {
			$userId = $_SESSION['user_id'];
		}
		
		if(!empty($comment)){

			$bc_id_query = "SELECT  `project_id`, `title`, `status`,`assigned_to` FROM  `qa_defects` WHERE `id`= ? LIMIT 1";
			$bc_id_result = $mysql->sqlprepare($bc_id_query,array($defectId));
			$bc_id_row = $bc_id_result->fetch_assoc();


			$update_wo_comment = "INSERT INTO `qa_comments` "
				."(`defect_id`,`user_id`,`comment`,`date`) "
				."VALUES "
				."('$defectId','$userId','$comment',NOW())";
			@$mysql->sqlordie($update_wo_comment);
		
			//insertWorkorderAudit($mysql,$defectId, '4', $_SESSION['user_id'],$bc_id_row['assigned_to'],$bc_id_row['status']);
		}
		
		$select_comments = "SELECT * FROM `qa_comments` WHERE `defect_id`= ? order by date desc";
		$comm_result = @$mysql->sqlprepare($select_comments,array($defectId));

		
		while($comRow = $comm_result->fetch_assoc()) {
			$select_user = "SELECT * FROM `users` WHERE `id`= ? LIMIT 1";
			$user_result = @$mysql->sqlprepare($select_user,array($comRow['user_id']));
			$user_row = $user_result->fetch_assoc();
			
			$date_time_split = explode(" ", $comRow['date']);
			$date_split = explode("-", $date_time_split[0]);
			$time_split = explode(":", $date_time_split[1]);
			$date = date("D M j \a\\t g:i a", mktime($time_split[0],$time_split[1],$time_split[2],$date_split[1],$date_split[2],$date_split[0]));

			$cmnt = $comRow['comment'];
			$text_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($cmnt,ENT_NOQUOTES,'UTF-8'));
			$text_string=nl2br($text_string);
			$comment_html .= '<li>
				<img src="'.$user_row['user_img'].'" class="comment_photo" />
				<div class="comment_body">
					<p><strong>' .ucfirst($user_row['first_name']) ." " .ucfirst($user_row['last_name']) .'</strong><br><em>' .$date .'</em></p>
					<p>'.$text_string.'</p>
				</div>
			</li>';
		}
		
		$select_email_users = "SELECT * FROM `qa_defects` WHERE `id`= ? LIMIT 1";
		$email_res = $mysql->sqlprepare($select_email_users,array($defectId));
		if($email_res->num_rows > 0) {
			$new_commenter = "SELECT * FROM `users` WHERE `id`= ? LIMIT 1";
			$commenter_res = $mysql->sqlprepare($new_commenter,array($userId));
			$commenter_row = $commenter_res->fetch_assoc();
		
			$email_row = $email_res->fetch_assoc();
			
			$cc_list = $email_row['cclist'];
			$cc_list_part = explode(",", $cc_list);
			$at = $email_row['assigned_to'];
			$rb = $email_row['requested_by'];
			
			$title = $email_row['title'];

			$status = $email_row['status'];

			$users_email[$at] = true;
			$users_email[$rb] = true;
			
			for($e = 0; $e < sizeof($cc_list_part); $e++) {
				if(!empty($cc_list_part[$e])) {
					$users_email[$cc_list_part[$e]] = true;
				}
			}
			$user_keys = array_keys($users_email);
			$select_project = "SELECT * FROM `projects` WHERE `id`= ? ";
			$project_res = $mysql->sqlprepare($select_project,array($bc_id_row['project_id']));
			$project_row = $project_res->fetch_assoc();

			$select_company = "SELECT * FROM `companies` WHERE `id`= ? ";
			$company_res = $mysql->sqlprepare($select_company,array($project_row['company']));
			$company_row = $company_res->fetch_assoc();

			$qa_status = "SELECT * FROM `lnk_qa_status_types` WHERE `id`= ? ";
			$qa_status_res = $mysql->sqlprepare($qa_status,array($status));
			$qa_status_row = $qa_status_res->fetch_assoc();
            $description=($email_row['body']);
            $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES,'UTF-8'));
			for($u = 0; $u < sizeof($user_keys); $u++) {
				if($commenter_row['id'] != $user_keys[$u] )
				{
					// No Email for the person who posts comments
					$select_email_addr = "SELECT `email` FROM `users` WHERE `id`= ? LIMIT 1";
					$email_addr_res = $mysql->sqlprepare($select_email_addr,array($user_keys[$u]));
					$email_addr_row = $email_addr_res->fetch_assoc();
					
					$to = $email_addr_row['email'];
					//$subject = "Defect ".$defectId.": ".$qa_status_row['name']." - ".$title;
					$subject = "Defect ".$defectId.": Comment Added  - ".$title;
                                        $subject='=?UTF-8?B?'.base64_encode($subject).'?=';
                                                     
					$headers = "From: ".$commenter_row['email']."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";
						$link = "<a href='".BASE_URL ."/quality/index/edit/?defect_id=" .$defectId."'>".$defectId."</a>";
						$msg = "Defect [$link] comment added.<br><br>";
					    $latest_comment=(Util::escapewordquotes($_POST['comment']));
						$latest_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($latest_comment,ENT_NOQUOTES,'UTF-8'));
						$msg.= "<b>Latest Comment: </b>" .$latest_string ."<br><br>";
						$msg.= "<b>Company: </b>" . $company_row['name'] . "<br><br>";
						$msg.= "<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br><br>";
                        $msg.= "<b>Summary: </b>" .htmlentities($title,ENT_NOQUOTES,'UTF-8') ."<br><br>";
						$msg.= "<b>Description: </b>" .$desc_string ."<br>";
					    if(!empty($to)) {
						sendEmail($to, $subject, $msg, $headers);
					}
				}
			} 
		}
		
		echo $comment_html;
	}

	function sendEmail($to, $subject, $msg, $headers){
		$msg = nl2br($msg);
		mail($to, $subject, $msg, $headers);
	}

	function insertWorkorderAudit($mysql,$wo_id, $audit_id, $log_user_id,$assign_user_id,$status)
	{
		$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
		@$mysql->sqlordie($insert_custom_feild);
	}
?>
