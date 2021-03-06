<?php


class commentServices {

	private static $instance;
    private $count = 0;
	public $config;
	//const MAX_UPLOAD_LIMIT = 5;
	
	
 	public static function singleton()
    {
        if (!isset(self::$instance)) {
            //echo 'Creating new instance.';
            $mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
            self::$instance = $mysql;
        }
        return self::$instance;
    }
	/**
	 * Save  Comment function
	 * 
	 * @param Object $userInfo
	 * @param Object $Workorder
	 * @return String
	 */
	 public function saveLHComment($userInfo, $Workorder) {
	 	$mysql = self::singleton();
		$attachmentError = array();
	 	$userName = $mysql->real_escape_string($userInfo->useremail);
	 	$usersSql = "SELECT id FROM `users` WHERE email = '$userName' AND active='1' and deleted ='0' LIMIT 0,1";
	 	$userCheck = $mysql->query($usersSql);
	 	$userResult = $userCheck->fetch_assoc();
		 
	 	$email_users = array();
	 	if($userCheck->num_rows == 0){
	 		$userName = str_replace("'", '',$userInfo->useremail);
	 		$usersSql = "SELECT id FROM `users` WHERE email = '$userName' AND active='1' and deleted ='0' LIMIT 0,1";
	 		$userCheck = $mysql->query($usersSql);
	 		$userResult = $userCheck->fetch_assoc();
			
	 	}
		//print_r($userResult);
		if($userCheck->num_rows > 0){
	 		$wid = $mysql->real_escape_string($Workorder->wid);
	 		$uid = $userResult['id'];
	 		$comment = $mysql->real_escape_string($this->escapewordquotes($Workorder->comment));
	 		$curDateTime = date("Y-m-d H:i:s");
	 		$bc_id_query = "SELECT  `bcid`, `project_id`, `title`,requested_by, `priority`,`status`,`assigned_to`,`body`,cclist,archived FROM `workorders` WHERE `id`='" .$mysql->real_escape_string($wid) ."'  LIMIT 1";
			$bc_id_result = $mysql->query($bc_id_query);
			$bc_id_row = $bc_id_result->fetch_assoc();
			if(count($bc_id_row) > 0){
				//if($bc_id_row['assigned_to'] != $uid){
					$email_users[] = $bc_id_row['assigned_to'];
				//}
				//if($bc_id_row['requested_by'] != $uid){
					$email_users[] = $bc_id_row['requested_by'];
				//}
				if("" != trim($bc_id_row['cclist'])){
					$cclist = explode(",", $bc_id_row['cclist']);
				}
				for($v = 0; $v < sizeof($cclist); $v++) {
					if($cclist[$v] != ''){
						//if($cclist[$v] != $uid){
							$email_users[] = $cclist[$v];
						//}
					}
				}
				//Check if Request have attachemnt
				$attachmentError = $this->updateAttachmentList($Workorder);
				
				//Fixed the issue if ticket is closed as well as archived
				//If WO is archived then change it to unarchive
				/*if($bc_id_row['archived'] == '1'){
					$update_wo_comment2 = "UPDATE  `workorders` SET  `archived` =  '0' WHERE  `id` = ".$wid." LIMIT 1 ";
					$mysql->query($update_wo_comment2);
				}*/
				//End
		 		$update_wo_comment = "INSERT INTO `workorder_comments` (`workorder_id`,`user_id`,`comment`,`date`) "
					."VALUES ('$wid','$uid','$comment','$curDateTime')";
				if(defined('BASECAMP_MAPPING')){
					if(BASECAMP_MAPPING == 'OPEN'){
						
						$basecampArray = array(
							"created_by" => $uid,
							"workorder_id" => $wid,
							"comment" => $comment 
							);
						$this->UpdateMileStoneComment($basecampArray);
						}
				}
				$mysql->query($update_wo_comment);
				$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$wid' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
				$req_type_res = $mysql->query($select_req_type_qry);
				$req_type_row = $req_type_res->fetch_assoc();
				
				$this->insertWorkorderAudit($wid, '4', $uid,$bc_id_row['assigned_to'],$bc_id_row['status'],$curDateTime );
				$this->createEmail($email_users,$Workorder,$userName);
				if(count($attachmentError) > 0){
					
					if(count($attachmentError) > 0){
						$this->sendAttachmentEmail($attachmentError,$Workorder,$userName);
						
					}
					return "SCC001";	
				
				}else{
					return "SCC001";
				}
			}else{
				return "ERR004";
			}
	 	}else{
	 		return "ERR003";
	 	}
	    $mysql->close();
	    /*return "you passed me ".$a." ".$b;*/
	 }
	private function createEmail($email_users,$Workorder,$userName){
		
		if(count($email_users) > 0){
			$mysql = self::singleton();
			
 				//$msg = $Workorder->comment;
			//echo $userName;
 			$new_commenter = "SELECT * FROM `users` WHERE `email` = '".$userName."' LIMIT 1";  
			$commenter_res = $mysql->query($new_commenter);
			$commenter_row = $commenter_res->fetch_assoc();
 				
 			$wid = $mysql->real_escape_string($Workorder->wid);
 			$bc_id_query = "SELECT  `bcid`, `project_id`, `title`, `priority`,`status`,`assigned_to`,`body`,`requested_by` FROM `workorders` WHERE `id`='" .$wid ."' LIMIT 1";
			$bc_id_result = $mysql->query($bc_id_query);
			$bc_id_row = $bc_id_result->fetch_assoc();
			
			$request_type_arr = array("Submit a Request" => "Request", "Report a Problem" => "Problem","Report an Outage" => "Outage");
			$select_project = "SELECT * FROM `projects` WHERE `id`='" .$bc_id_row['project_id'] ."'";
			$project_res = $mysql->query($select_project);
			$project_row = $project_res->fetch_assoc();

			$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
			$company_res = $mysql->query($select_company);
			$company_row = $company_res->fetch_assoc();
			
			$requestor_qry = "SELECT * FROM `users` WHERE `id`='" .$bc_id_row['requested_by'] ."'";
			$requestor_user_res = $mysql->query($requestor_qry);
			$requestor_user_row = $requestor_user_res->fetch_assoc();

			$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$wid' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
			$req_type_res = $mysql->query($select_req_type_qry);
			$req_type_row = $req_type_res->fetch_assoc();
 			
			$latest_comment = $Workorder->comment;
			//$latest_comment=(Util::($_POST['comment']));
			$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
			//$latest_comment = htmlentities($latest_comment);
			$latest_comment= $this->escapewordquotes($latest_comment);
            $latest_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($latest_comment,ENT_NOQUOTES,'UTF-8'));
			$description = $this->escapewordquotes($bc_id_row['body']);
           	$description = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES,'UTF-8'));
			
 				$subject = "WO ".$wid.": Comment - ".$req_type_row['field_name']." - " . $bc_id_row['title']. "";
				$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$wid."'>".$wid."</a>";
                
				$msg ="<b>Latest Comment:</b> " .$latest_string;
				$msg .=  "<b>Requestor: </b>" . $requestor_user_row['first_name'].' '. $requestor_user_row['last_name']. "<br><br>";
				$msg .="<b>Company: </b>" . $company_row['name'] . "<br><br>";
				$msg .="<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br><br>";
				$msg .="<b>Site: </b>" .$req_type_row['field_name'] ."<br><br>";				
				$msg .="<b>".ucfirst($commenter_row['first_name']) ." " .ucfirst($commenter_row['last_name']) ."</b> commented on work order "."<b>[" . $link . "]</b><br><br>";
				$msg .="<b>Request Type: </b>" .$request_type_arr[$req_type_row['field_name']] ."<br>";
		 
				$severity_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SEVERITY' and cu.field_key = 'SEVERITY' and cu.workorder_id = '$wid'";
				
			    $severity_name_res = $mysql->query($severity_name_qry);
			    $severity_name_row = $severity_name_res->fetch_assoc();
		
				if($request_type_arr[$req_type_row['field_name']]=='Problem')
				{
				
				$msg .="<br><b>Severity: </b>" .$severity_name_row['field_name']  ."<br><br>";
				}
				
				//End Code

                $msg .="<hr><b>Description:</b> " .$description."<br><br>";
                
 				//$conCatUserEmail = implode(",", $email_users);
				
	 			if(count($email_users) > 0){
	 				$email_users = array_unique($email_users);
	 				foreach($email_users as $toEmails){
	 					$to_sql = "SELECT email FROM `users` WHERE `id`='" .$toEmails ."'";
						$to_sql_res = $mysql->query($to_sql);
						$to_sql_result = $to_sql_res->fetch_assoc();
		 				$toEmails = $to_sql_result['email'];
		 				$from = $userName;
		 				if($toEmails != '')
	 					$this->lh_sendEmail($toEmails,$subject,$msg,$from);
	 				}
 				}
		}
	}
	 
	private function insertWorkorderAudit($wo_id, $audit_id, $log_user_id,$assign_user_id,$status,$curDateTime )
	{
		
		$mysql = self::singleton();
		$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."','".$curDateTime."')";
		@$mysql->query($insert_custom_feild);
	}
	
public static function escapewordquotes ($text) {
		$pre = chr(226).chr(128);
		$badwordchars=array('�','�','�','apos;',"#039;","�","�",'&#233;','&#8216;','&#8217;',
		'&#8230;',
		'&#8217;',
		'&#8220;',
		'&#8221;',
		'&#8212;',
		'#8212;',
		'#&8211;',
		'#8211;',
		'amp;',
		'&#160;',
		'#160;'
			
		);
		$fixedwordchars = array('','"','"',"'","'",",","'", "e","'","'",'~','~','','','_','-','-','-','','');
	    $text = str_replace($badwordchars,$fixedwordchars,$text);                         
		$text=str_replace('�',"'",$text); 
	    $text=str_replace('�',"'",$text); 
	    $text=str_replace('&amp;rsquo;',"'",$text); 
//    $text = preg_replace('/[^(\x20-\x7F)]*/','', $text );  
		$text = str_replace("&#8216;","",$text);
		//LH#    20679
		$text = str_replace(array("“","’","”"),array('"',"'",'"'),$text);
//		$text = str_replace("&","",$text);
//		$text = preg_replace('/[^\x00-\x7f]/','',$text);
		return $text;


	}
	public function lh_sendEmail($to, $subject, $msg,$from,$attachmentMail = ''){
			$headers = '';
			//$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
			if($attachmentMail  == ''){
				$headers .= 'From:'.$from . "\r\n" .
 			   "Reply-To: ".COMMENT_REPLY_TO_EMAIL. "\r\n";
			    'X-Mailer: PHP/' . phpversion();
			}else{
				$headers .= 'From:'.$from . "\r\n" ;
			}	
			//$headers = " From: ".$from."\nMIME-Version: 1.0\nContent-type: text/html; ";
			$msg = nl2br($msg);
			//$subject = ($subject);
	
			$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
			//$headers .= "\r\n".'Reply-To: lighthouse.comments@nbcuni.com' . "\r\n";
			$headers."<br/>".$msg."<br/>".$subject."<br/>".$to;
			try{
				$result = @mail($to, $subject, $msg, $headers);
			
			}catch(Exception $e){
				echo $e->getMessage();	
			}
		}
		
		
		private function updateAttachmentList($wobj){
			$wid = $wobj->wid;
			$attachmentList = $wobj->attachmentList;
			$attamentAllError = array();
			
			if(count($attachmentList['name']) > 0){
				for($i =0; $i< count($attachmentList['name']); $i++){
					$errorArray = array();
					$attachmentListfileName = $attachmentList['name'];
					$attachmentListTmpfileName = $attachmentList['tmp_name'];
					$attachmentListSize = $attachmentList['size'];
					if($attachmentList['name'][$i] != ''){
				//foreach($attachmentList['name'] as $attachmrntKey => $attachmentValues){ 
						$mysql = self::singleton();
						$cleaned_filename = str_replace("'", "_", $attachmentListfileName[$i]);
						$ext = unserialize(ALLOWED_FILE_EXTENSION);
							
						if(!in_array(strrchr($cleaned_filename,'.'),$ext)){
							$errorArray[$cleaned_filename] ="INVALID EXTENSION";
							
							//die ("INVALID EXTENSION"); 
						}
						if ($attachmentListSize[$i] > MAX_UPLOAD_FILE_SIZE){
							$errorArray[$cleaned_filename] ="EXCEED-FILE-SIZE-";
							
						}			
						$dirName = $wid; 
						if(!is_dir($_SERVER['DOCUMENT_ROOT'] .'/files/' .$dirName)){
							@mkdir($_SERVER['DOCUMENT_ROOT'] .'/files/' .$dirName);
						
						}
						if($i > 4){
							$errorArray[$cleaned_filename] = "EXCEED-FILE-UPLOAD-LIMIT";
											
						}
						if(count($errorArray) == 0 && $i < 5){
							if (!@move_uploaded_file($attachmentListTmpfileName[$i], $_SERVER['DOCUMENT_ROOT'] .'/files/' .$dirName ."/".$cleaned_filename)) {
								//die("");
								$errorArray[$cleaned_filename]['UPLOAD'] ="UNABLE-TO-UPLOAD";
								
							} else {
							
								$select_file = "SELECT * FROM `workorder_files` WHERE `directory`='" .$wid ."' AND `file_name`='" .$cleaned_filename ."' LIMIT 1";
								$result = $mysql->query($select_file);
							
								@chmod($_SERVER['DOCUMENT_ROOT'] .'/files/' .$dirName ."/".$cleaned_filename, 0744);
								if($result->num_rows == 1) {
									$row = $result->fetch_assoc();
									$update_row = "UPDATE `workorder_files` SET `workorder_id`='$wid', `upload_date`=NOW() WHERE `id`='" .$row['id'] ."'";
									$mysql->query($update_row);
								} else {
									$insert_image = "INSERT INTO `workorder_files` "
									."(`workorder_id`,`directory`,`file_name`,`upload_date`,`deleted`) "
									."VALUES "
									."($wid,'" .str_replace("/", "", $dirName) ."','" .$cleaned_filename ."',NOW(),'1')";
									$mysql->query($insert_image);
									
								}
							
							}
						}
					}
					$attamentAllError[] = $errorArray;
				}
			}
			return $attamentAllError;
		}
		
		
		private function sendAttachmentEmail($attachmentError,$workorder,$userName){
			if(count($attachmentError) > 0){
				 $to = $userName;
				
				$from = WO_EMAIL_FROM;
				$msgStr = "";
				$wid = $workorder->wid;
				$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$wid."'>".$wid."</a>";
				
				$subject = "WO ".$wid.":  Attachment Error ";
				
				foreach($attachmentError as $attachemntErrorKey => $attachmentErrorVal){
							if(count($attachmentErrorVal) > 0){
							
								foreach($attachmentErrorVal as $attachmentErrorValKey => $attachmentErrorValVal){
										//$msg = "$workorder->wid"
										$msgStr = "Ok";
								
								}
							
							}
				
				}
				if($msgStr == 'Ok'){
					$msg = "Your email attachment for WO [".$link."] has one, or more, of the following errors ";
					$msg .= "<br/><br/><br/>";	
					$msg .= "-There are more than 5 files being attached<br/>";
					$msg .="-The file size of the attachment is more than 10MB<br/>";
                                        $msg .="-The file extension is not supported by Lighthouse<br/>";
                                        $msg .="<br/><br/>";
                                        $msg .=" Please take the appropriate action and re-submit your request";

					$this->lh_sendEmail($to, $subject, $msg,$from,$attachmentError = true);
				}
				//
			
			
			}
		
		
		}
		
		
		private function UpdateMileStoneComment($arrayBasecamp){
			global $mysql;
			include_once('../../application/library/Basecamp.class.php');
			$bc = new Basecamp(BASECAMP_HOST,BASECAMP_USERNAME,BASECAMP_PASSWORD,'xml');
					//createMilteStone($woAssignedTo,$getWoId,$woTitle,$woDesc,$require_date);
			if(count($arrayBasecamp) > 1){
				$us_bs = $mysql->sqlprepare("SELECT bc_id,CONCAT_WS(' ', first_name,last_name) as user_name FROM users WHERE id = ?",array($arrayBasecamp['created_by']));
				$us_bs_row = $us_bs->fetch_assoc();
				$responsible_party_id = $us_bs_row['bc_id'];
				$comment_msg = "<b>".ucfirst($us_bs_row['user_name']) ."</b> has commented ";
				//$message_id = '44850944';
				//echo "SELECT milestone_id FROM bs_milestone WHERE wid = '".$arrayBasecamp['workorder_id']."'"; die;
				//Get Milestone ID from bs_mapping
				
				$bs_milestone = $mysql->sqlprepare("SELECT milestone_id FROM bs_milestone WHERE wid = ?",array($arrayBasecamp['workorder_id']));
				//If workorder  Mapped with Basecam Milestone
				if($bs_milestone->num_rows > 0){
					$bs_milestone_row = $bs_milestone->fetch_assoc();
					$bs_milestone_id = $bs_milestone_row['milestone_id'];
					$body = $comment_msg."<br/>".str_replace(array("\r\n","\r","\n","\\r","\\n","\\r\\n"),"<br/>",$arrayBasecamp['comment']);
					$result = $bc->createCommentForMilestone($bs_milestone_id,$body);
				}
			
			}
			
			
			
			
		}
		
	}
		
		if($_POST['lh_submit']){
		 	/* $handle = fopen('comment_service.log', 'a');
	   		 // Write $somecontent to our opened file.
	   		if (fwrite($handle, $_POST['lh_comment'] ) === FALSE) {
	     			   echo "Cannot write to file ($filename)";
	       			 exit;
	    	}*/
	    	//End
			define("SALT",'lighthouse');
			require_once('../_inc/config.inc');
			$c = new commentServices();
			$u = new stdClass();
	    	$w = new stdClass();
	    	$u->useremail = $_POST['lh_email'];
	        $w->wid = $_POST['lh_wid'];
	     	$w->comment =urldecode($_POST['lh_comment']);
	     	$w->subject = $_POST['lh_subject'];
			$hostname = $_POST['source_host_name'];
			//tokenInput =from+"|"+messageId+"|"+getHostName()+"|"+currentTime
			$currentTime = $_POST['lh_utc_time'];
			if(ISSET($_FILES['upload_file'])){
				$w->attachmentList = $_FILES['upload_file'];
			}
				
			$tokenInput = $u->useremail.'|'.$w->wid.'|'.$hostname.'|'.$currentTime.'|'.SALT;
			//echo $c->saveLHComment($u,$w); die;
			$cs_token = md5($tokenInput);
            $lh_token = $_POST['lh_token'];
			if(trim($w->subject) == ''){
	     		echo "ERR006";
	     		exit;
	     	}
	     	if(trim($w->comment) == ''){
	     		echo "ERR005";
	     		exit;
	     	}else{
	     		$w->comment = str_replace("-----Original Message-----", "", $w->comment);
	     	}
	     	$minute = date("i");
			$minute_add=$minute+15;
            $match_minute=strtotime('u',$minute_add);
			$extended_time = $currentTime + $match_minute;
			if(trim($lh_token) == ''){
				echo "ERR0746"; 
				exit;
			}
			if(trim($currentTime) == ''){
				echo "ERR0746"; 
				exit;
			}
			if( $cs_token == $lh_token){
			if ($currentTime <= $extended_time){
				echo $c->saveLHComment($u,$w);
			}
			else{ echo "ERR0744"; };
			}
			else{ echo "ERR0745"; };
				
		}
		else{
			echo "ERR001";
		}

?>
 
