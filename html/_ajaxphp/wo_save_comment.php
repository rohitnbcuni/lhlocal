<?PHP
	session_start();
	include('../_inc/config.inc');
	include('../_ajaxphp/util.php');
		
	if(isset($_SESSION['user_id'])) {
		
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		global $mysql;
		$user = $_SESSION['lh_username'];
	    $password = $_SESSION['lh_password'];
		$woId = $mysql->real_escape_string($_POST['woId']);
		//LH#23699
		//$userId = $mysql->real_escape_string($_POST['userId']);
		$userId = $_SESSION['user_id'];
		//$comment = $mysql->real_escape_string(Util::escapewordquotes($_POST['comment']));
		/**
		 * Ticket No 16857,19352
		 * Special Character display 
		 * @var test Comment type
		 */
		 $comment = $mysql->real_escape_string(Util::escapewordquotes($_POST['comment']));
		//$comment = $mysql->real_escape_string((htmlentities(Util::nonPrintableChar($_POST['comment']),ENT_QUOTES,'ISO-8859-1')));
		//End Ticket
		$comment_html = "";
		
		if($userId != $_SESSION['user_id']) {
			$userId = $_SESSION['user_id'];
		}
		
		if(!empty($comment)){
			$update_wo_comment = "INSERT INTO `workorder_comments` "
				."(`workorder_id`,`user_id`,`comment`,`date`) "
				."VALUES "
				."('$woId','$userId','$comment',NOW())";
			@$mysql->sqlordie($update_wo_comment);
			
			$xml = '<comment>
					  <body>' . $_POST['comment'] .'</body>
					</comment>';
			
			$bc_id_query = "SELECT  `bcid`, `project_id`, `title`, `priority`,`status`,`assigned_to`,`body` FROM `workorders` WHERE `id`='" .$mysql->real_escape_string($woId) ."' LIMIT 1";
			$bc_id_result = $mysql->sqlordie($bc_id_query);
			$bc_id_row = $bc_id_result->fetch_assoc();
			

			$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$woId' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
			$req_type_res = $mysql->sqlordie($select_req_type_qry);
			$req_type_row = $req_type_res->fetch_assoc();


			
			insertWorkorderAudit($mysql,$woId, '4', $_SESSION['user_id'],$bc_id_row['assigned_to'],$bc_id_row['status']);
		
			
				
			
		/*	$set_request_url = BASECAMP_HOST.'/'.'posts/'.$bc_id_row['bcid'].'/comments.xml';
			
			$session = curl_init();   
			
			curl_setopt($session, CURLOPT_URL, $set_request_url); // set url to post to 
			curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($session, CURLOPT_POST, 1); 
			curl_setopt($session, CURLOPT_POSTFIELDS, $xml); 
			curl_setopt($session, CURLOPT_HEADER, true);
			curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml','Expect: '));
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session,CURLOPT_USERPWD,$user . ":" . $password);

			if(ereg("^(https)",$set_request_url)) curl_setopt($session,CURLOPT_SSL_VERIFYPEER,false);

			$response = curl_exec($session);
			$newNumPart1 = explode("/posts/", $response);
			$newNumPart2 = explode(".xml", @$newNumPart1[1]);
			
			$comment_id = $newNumPart2[0];
			curl_close($session); */
		}
		$counts = 1;
		$comment_id =0;
		$comment_id_row = array();
		$largest_comment_id =0;
		$select_comments = "SELECT * FROM `workorder_comments` WHERE `workorder_id`='$woId' AND active ='1' AND deleted ='0' order by date Desc";
		$comm_result = @$mysql->sqlordie($select_comments);
		$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";	
		while($comRow = $comm_result->fetch_assoc()) {
			$comment_id = $comRow["id"]; 
			$comment_id_row[] = $comment_id;
			$select_user = "SELECT * FROM `users` WHERE `id`='" .$comRow['user_id'] ."' LIMIT 1";
			$user_result = @$mysql->sqlordie($select_user);
			$user_row = $user_result->fetch_assoc();
			
			$date_time_split = explode(" ", $comRow['date']);
			$date_split = explode("-", $date_time_split[0]);
			$time_split = explode(":", $date_time_split[1]);
			$date = date("D M j \a\\t g:i a", mktime($time_split[0],$time_split[1],$time_split[2],$date_split[1],$date_split[2],$date_split[0]));
			$cmnt = $comRow['comment'];
			//$text_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",str_replace('&#129;','&#153;',htmlentities($cmnt)));
			/**
			 * Ticket No 16857,19352
			 * Special Character display 
			 * @var test Comment type
			 */
			 $text_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",str_replace('&#129;','&#153;',htmlentities($cmnt,ENT_NOQUOTES, 'UTF-8')));
			//$text_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",str_replace('&#129;','&#153;',html_entity_decode($cmnt,ENT_QUOTES,'ISO-8859-1')));
			$text_string=nl2br($text_string);
			$comment_delete = '';
			$comment_update_box = '';
			$date_diff = Util::dateDiffComment($comRow['date']);
			if($date_diff['years'] == 0 && $date_diff['days'] == 0 && $date_diff['months'] == 0 && $date_diff['hours'] == 0 && $date_diff['minuts'] <= 15){

				if($comRow['user_id'] == $_SESSION['user_id']){
					if($counts == 1){
						$comment_delete = "<div id='edit_pannel_".$comment_id."'> <span id='comment_edit' style='padding-left:10px;' onclick='displayCommentBox(".$comment_id.");'> <img src='/_images/b_edit.png' alt='Edit' title='Edit'></span><span style='padding-left:10px;' id='comment_delete' onclick='deleteComment(".$comment_id.");'><img src='/_images/b_drop.png' alt='Delete' title='Delete'></span></div>";
					}else{
						$comment_delete = '';
					}
					$comment_update_box ='<div id="comment_id_li_body_'.$comment_id.'" class="panel" >
							
							<textarea id="comment_id_li_textarea_'.$comment_id.'"  class ="field_large" >'.htmlentities($cmnt,ENT_NOQUOTES, 'UTF-8').'</textarea><br>
							<div class="new_comment_actions">
							<br/>															
							<button onclick="updateComment('.$comment_id.'); return false;" class="secondary" style="padding-left:190px;">
								<span>Update Comment</span>
							</button>
							</div>

							
							<input type="hidden" class="comment_id_li_comment_id" name="comment_id_li_comment_id[]"  value="'.$comment_id.'">
							<input type="hidden" id="comment_id_li_comment_time_'.$comment_id.'"  name="comment_id_li_comment_time_'.$comment_id.'"  value="'.$comRow['date'].'">
							</div>';
				}
				$counts++;
			}
			$comment_html .= '<li id="comment_id_li_'.$comment_id.'">
				<img src="'.$user_row['user_img'].'" class="comment_photo" />
				<div class="comment_body">
					<p><strong>' .ucfirst($user_row['first_name']) ." " .ucfirst($user_row['last_name']) .'</strong><br><em>' .$date .' '.$comment_delete.'</em></p>
					<p id="comment_id_li_msg_'.$comment_id.'">' . $text_string .'</p>
					'.$comment_update_box.'
				</div>
			</li>';
		}
		if(count($comment_id_row) > 0){
			@rsort($comment_id_row);
			//$comment_id_row = arsort($comment_id_row);
			$largest_comment_id = $comment_id_row[0];
		}
		$comment_html .= '<li style="border-bottom:none;display:none;"><input type="hidden" id="last_comment_id" name="last_comment_id" value="'.$largest_comment_id.'"></li>';
		
		$select_email_users = "SELECT * FROM `workorders` WHERE `id`='$woId' LIMIT 1";
		$email_res = $mysql->sqlordie($select_email_users);


		if($email_res->num_rows > 0) {
			$new_commenter = "SELECT * FROM `users` WHERE `id`='$userId' LIMIT 1";
			$commenter_res = $mysql->sqlordie($new_commenter);
			$commenter_row = $commenter_res->fetch_assoc();
		
			$email_row = $email_res->fetch_assoc();
			
			$cc_list = $email_row['cclist'];
			$cc_list_part = explode(",", $cc_list);
			$at = $email_row['assigned_to'];
			$rb = $email_row['requested_by'];

			$woAssignedTo = $email_row['assigned_to'];
			$requestedId = $email_row['requested_by'];
			
			//basecamp Comment on Milestone
			checkBasecampComment($woId,$userId,$woAssignedTo,$comment);
			
			$requestor_qry = "SELECT * FROM `users` WHERE `id`='" .$requestedId ."'";
			$requestor_user_res = $mysql->sqlordie($requestor_qry);
			$requestor_user_row = $requestor_user_res->fetch_assoc();
		
			$site_name_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$woId' and a.field_key='SITE_NAME' and a.field_id = b.field_id";
			$site_name_res = $mysql->sqlordie($site_name_qry);
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
			$project_res = $mysql->sqlordie($select_project);
			$project_row = $project_res->fetch_assoc();

			$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
			$company_res = $mysql->sqlordie($select_company);
			$company_row = $company_res->fetch_assoc();
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
				$email_addr_res = $mysql->sqlordie($select_email_addr);
				$email_addr_row = $email_addr_res->fetch_assoc();
				
				$to = $email_addr_row['email'];

				//LH#20679
				$subject = "WO ".$woId.": Comment - ".$req_type_row['field_name']." - " . html_entity_decode($bc_id_row['title'],ENT_NOQUOTES,'UTF-8'). "";
				//$headers = "From: ".$commenter_row['email']."\nMIME-Version: 1.0\nContent-type: text/html; charset=UTF-8";
				$headers = "From: ".ucfirst($commenter_row['first_name']) ." " .ucfirst($commenter_row['last_name'])."<".WO_EMAIL_FROM.">"."\nMIME-Version: 1.0\nContent-type: text/html; charset=UTF-8";
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
				
			    $severity_name_res = $mysql->sqlordie($severity_name_qry);
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
		
		echo $comment_html;
	}

	function sendEmail($to, $subject, $msg, $headers){
		$msg = nl2br($msg);
		$subject='=?UTF-8?B?'.base64_encode($subject).'?=';	
		$headers .= "\r\n" .
    				"Reply-To: ".COMMENT_REPLY_TO_EMAIL. "\r\n";
		@mail($to, $subject, $msg, $headers);
	}

	function insertWorkorderAudit($mysql,$wo_id, $audit_id, $log_user_id,$assign_user_id,$status)
	{
		$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
		@$mysql->sqlordie($insert_custom_feild);
	}
	
	function checkBasecampComment($woId,$userId,$woAssignedTo,$comment){
		global $mysql;
		
		if(defined('BASECAMP_MAPPING')){
			if(BASECAMP_MAPPING == 'OPEN'){
			
				//Check WorkOrder ID Existing in Basecamp Mapping Table
				$bs_mapping = "SELECT * FROM lh_basecamp_mapping WHERE assigned_to = '$woAssignedTo' LIMIT 1";
				$us_bs = $mysql->sqlordie($bs_mapping);
				//$us_bs->num_rows;
				if($us_bs->num_rows > 0) {
					$bsArray = array();
					$bsArray['created_by'] = $userId;
					$bsArray['workorder_id'] = $woId;
					$bsArray['comment'] = $comment;
					Util::UpdateMileStoneComment($bsArray);
				
				}
		
			}
		}
	}
		
	
	
?>
