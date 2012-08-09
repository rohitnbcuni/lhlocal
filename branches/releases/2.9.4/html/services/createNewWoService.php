<?php

/* Workorder creation via EMail Service
 * @author Shobhitsingh.Bhadauria@nbcuni.com
 * @copyright NBC.com 
 * @category Service
 * @version 1.0
 * @link JAVA Service
 */

class createNewWoService{

	private static $instance;
    private $count = 0;

	/**
	 * DB connection
	 * singleton design pattern 
	 * @return DB object
	 * 
	 */
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
     * 
     * Return the company default project
     * @param string $useremail
     * @return project data
     */
    public function getDefaultProjectName($useremail){
    	$mysql = self::singleton();
    	$projectDataArr = array();
        $userEmail = $mysql->real_escape_string($useremail);
	 	$usersSql = "SELECT id,company FROM `users` WHERE email = '$userEmail' AND active='1' and deleted ='0' LIMIT 0,1";
	 	$userCheck = $mysql->query($usersSql);
	 	$email_users = array();
	 	if($userCheck->num_rows > 0){
	 		$userResult = $userCheck->fetch_assoc();
	 		$userCompany = $userResult['company'];
	 		$requesterUserID = $userResult['id'];
	 		$projectSql = "SELECT id,project_code, project_name FROM `projects` WHERE `company` = '$userCompany' AND `id` 
	 		IN (SELECT `project_id` FROM  `user_project_permissions` WHERE  `user_id` = '$requesterUserID') AND 
	 		archived ='0' and active ='1' and deleted ='0' ORDER BY project_name";
	 		$projectResult = $mysql->query($projectSql);
			if($projectResult->num_rows >0){
				$projectData = $projectResult->fetch_assoc();
				$projectDataArr['projectID'] =  $projectData['id'];
				$projectDataArr['requestedId'] =  $requesterUserID;
			}else{
				$projectDataArr['ERROR'] = "USER-DONT-HAVE-ANY-PROJECT-PERMISSION";
			}
	 	}else{
	 		$projectDataArr['ERROR'] = "USER-NOT-FOUND";
	 	}
    	
    	return $projectDataArr;
    }
    /**
     * 
     * generic function to return the table data
     * @param $table
     * @param $where
     * @param $start
     * @return table result
     */
    function getTableWhereData($table,$where,$start){
    	$mysql = self::singleton();
    	$projectData = array();
    	$projectSql = "SELECT $start FROM $table WHERE $where";
 		$projectResult = $mysql->query($projectSql);
		if($projectResult->num_rows >0){
			$projectData = $projectResult->fetch_assoc();
		}
		return $projectData;
    
    }
    /**
     * 
     * fetching the cc list
     * @param string $ccEmails
     */
    public function getCCList($ccEmails){
    	$mysql = self::singleton();
    	$ccEMailsArr = array();
    	$ccEMailsArrIds = array();
    	$ccEMailsArr = @explode(",",$ccEmails);
    	foreach($ccEMailsArr as $k => $emails){
    		if($emails != ''){
	    		$emails = str_replace("'", '',strtolower($emails));
		    	$userSql = "SELECT id FROM users WHERE email ='$emails' AND active ='1' AND deleted = '0'";
		 		$userResult = $mysql->query($userSql);
				if($userResult->num_rows >0){
					$userData = $userResult->fetch_assoc();
					$ccEMailsArrIds[] = $userData['id'];
				}
	    	}
    	}
    	return $ccEMailsArrIds;
    }
    /**
     * 
     * Save the workorder
     * @param object $wo
     * @return flag
     */
    public function saveWorkorder($wo){
    	$mysql = self::singleton();
    	$attachmentError = array();
    	/*$cclist = implode(",", $wo->ccLists);
		if(!empty($cclist)){
			$cclist = $cclist.",";
		}
	*/
	$cclistStr = '';
	if($wo->ccLists != ''){
	    	$cclist = implode(",", $wo->ccLists);
		if(!empty($cclist)){
			$cclistStr = $cclist.",";
		}else{
			$cclistStr = '';
		}
    	}
    	 $insert_wo = "INSERT INTO `workorders` " 
				."(`project_id`,`assigned_to`,`status`,`title`,"
				."`example_url`,`body`,`requested_by`,`assigned_date`,`estimated_date`,"
				."`creation_date`,`launch_date`,`cclist`) "
				."VALUES "
				."('$wo->projectId','$wo->woAssignedTo','$wo->woStatus','$wo->woTitle',"
				."'$woExampleURL','$wo->woDesc','$wo->requestedId','$wo->woStartDate','$wo->woEstDate',"
				."'$wo->creation_date', '$wo->launch_date','$cclistStr')";
		$mysql->query($insert_wo);
		$getWoId = $mysql->insert_id;
		if($getWoId > 0){
			$insert_audit2 = "INSERT INTO  `workorder_audit`
			SET `workorder_id` ='$getWoId', `audit_id` = '6',`log_user_id` = '$wo->requestedId' ,assign_user_id='$wo->woAssignedTo',
			`status`= '6', Request_type = '$wo->reqType'";
			$mysql->query($insert_audit2);
			$insert_audit = "INSERT INTO  `workorder_audit`
			SET `workorder_id` ='$getWoId', `audit_id` = '1',`log_user_id` = '$wo->requestedId' ,
			`status`= $wo->woStatus";
			$mysql->query($insert_audit);
			$insert_custom_field = "INSERT INTO  `workorder_custom_fields`
			SET `workorder_id` ='$getWoId', `field_key` = 'REQ_TYPE',`field_id` = '$wo->reqType'";
			$mysql->query($insert_custom_field) ;
			if($wo->reqType == '2'){
				//6 for severity 2 Ticket
				$insert_custom_field1 = "INSERT INTO  `workorder_custom_fields`
				SET `workorder_id` ='$getWoId', `field_key` = 'SEVERITY',`field_id` = '$wo->severity'";
				$mysql->query($insert_custom_field1) ;
			}
			//SET default SITE NAME
			$insert_custom_field1 = "INSERT INTO  `workorder_custom_fields`
			SET `workorder_id` ='$getWoId', `field_key` = 'SITE_NAME',`field_id` = '45'";
			$mysql->query($insert_custom_field1) ;
			$attchmentObj = new stdClass();
			$attchmentObj->wid = $getWoId;
			$attchmentObj->attachmentList = $wo->attachmentList;
			$attachmentError = $this->updateAttachmentList($attchmentObj);
			if(count($attachmentError) > 0){
				$this->sendAttachmentEmail($attachmentError,$wo,$wo->requestorEmail);
			}
			//Send Email Notification
			$select_wo = "SELECT * FROM `workorders` WHERE `id`='" .$getWoId ."'";
			$wo_res = $mysql->query($select_wo);
			$wo_row = $wo_res->fetch_assoc();
			include_once("../_ajaxphp/sendEmail.php");
			sendEmail_newRequest($mysql, $wo_row);
			return true;
			}else{
				return false;
			}
    }
    
    /**
     * Add attchemnt in New WO
     * 
     * @param $wobj
     */
	private function updateAttachmentList($wobj){
			$wid = $wobj->wid;
			$attachmentList = $wobj->attachmentList;
			$attamentAllError = array();
			//p($attachmentList); die();
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
		
	/**
	 * 
	 * Send notification email error to requestor
	 * @param array $attachmentError
	 * @param object $workorder
	 * @param string $userName
	 */
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
			}
		}
	/**
	 * 
	 * Send email
	 * @param $to
	 * @param $subject
	 * @param $msg
	 * @param $from
	 * @param $attachmentMail
	 */		
		
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
			//echo $headers."<br/>".$msg."<br/>".$subject."<br/>".$to;
			try{
				//$result = @mail($to, $subject, $msg, $headers);
			
			}catch(Exception $e){
				echo $e->getMessage();	
			}
		}
    
}

	
	if(ISSET($_POST['lh_submit'])){
		//print_r($_POST);
		require_once('../_inc/config.inc');
		
		define("OUTAGE","OUTAGE");
		define("REQUEST","REQUEST");
		define("PROBLEM","PROBLEM");
		define("SALT",'lighthouse');
		
		
		$customFieldsArray = array('OUTAGE' =>1,'PROBLEM' => 2,'REQUEST'=> 3);
		
		$workOrderObj = new stdClass();
		$wo = new createNewWoService();
		$statusArray = $wo->getTableWhereData("lnk_workorder_status_types","name ='New'", "id"); 
		
		include_once("../_ajaxphp/util.php");
	    $requestorEmail = $_POST['lh_email']; 
		if(!empty($requestorEmail)){
			$workOrderObj->requestorEmail = $requestorEmail;
			$projectData = $wo->getDefaultProjectName($requestorEmail);
			if(array_key_exists("ERROR", $projectData)== true){
				die($projectData['ERROR']);
			}else{
				$workOrderObj->projectId = $projectData['projectID'];
				$workOrderObj->requestedId = $projectData['requestedId'];
			}
		}else{
			die("INVALID REQUESTOR EMAIL ID");
		}
		//echo date("Y-m-d h:i:s",mktime("3","25","23"+(int)date("m"),(int)date("d"),(int)date("Y")));
		//echo date("Y-m-d h:i:s",mktime((int)date("h")+2,(int)date("i")+(int)date("s")+(int)date("m"),(int)date("d"),(int)date("Y")));
		if(ISSET($_POST['lh_type'])){
			if(trim($_POST['lh_type']) != ''){
				$woType = $_POST['lh_type']; 
				if($woType == OUTAGE){
					$assignedTo = json_decode(WO_CREATE_OUTAGE);
					$workOrderObj->woAssignedTo = $assignedTo[0]->id;
					$workOrderObj->woStartDate = date("Y-m-d H:i:s");
					$workOrderObj->woEstDate = date("Y-m-d H:i:s",mktime(date("H")+2,date("i"),date("s"),date("m"),date("d"),date("Y")));
					$workOrderObj->creation_date = $workOrderObj->woStartDate;
					$workOrderObj->launch_date = $workOrderObj->woEstDate;
					
				}
				if($woType == PROBLEM){
					$assignedTo = json_decode(WO_CREATE_PROBLEM);
					$workOrderObj->woAssignedTo = $assignedTo[0]->id;
					$workOrderObj->woStartDate = date("Y-m-d H:i:s");
					$workOrderObj->woEstDate = date("Y-m-d H:i:s",mktime(date("H")+48,date("i"),date("s"),date("m"),date("d"),date("Y")));
					$workOrderObj->creation_date = $workOrderObj->woStartDate;
					$workOrderObj->launch_date = $workOrderObj->woEstDate;
				}
				if($woType == REQUEST){
					$assignedTo = json_decode(WO_CREATE_CHANGE);
					$workOrderObj->woAssignedTo = $assignedTo[0]->id;
					$workOrderObj->woStartDate = date("Y-m-d H:i:s");
					$workOrderObj->woEstDate = date("Y-m-d H:i:s",mktime(date("H"),date("i"),date("s"),date("m"),date("d")+14,date("Y")));
					$workOrderObj->creation_date = $workOrderObj->woStartDate;
					$workOrderObj->launch_date = $workOrderObj->woEstDate;
					
				}
			}else{ 
				die("REQUEST TYPE MUST HAVE SOME VALUE");
			}
		}else{
			die("REQUEST TYPE NOT FOUND");
		}
		//print_r($_FILES);
		if(ISSET($_FILES['upload_file'])){
				$workOrderObj->attachmentList = $_FILES['upload_file'];
			}
		if(count($statusArray) > 0){
			$workOrderObj->woStatus = $statusArray['id'];
		}else{
			die("INVALID STATUS");
		}
		if(ISSET($_POST['woTitle']) && (!empty($_POST['woTitle']))){
			$workOrderObj->woTitle = Util::escapewordquotes(urldecode($_POST['woTitle']));	
		}else{
			die("SUBJECT IS EMPTY");
		}
		
		//$workOrderObj->workOrderObj->woExampleURL = Util::escapewordquotes(@$_POST['woExampleURL']);
		if(ISSET($_POST['woDesc']) && (!empty($_POST['woDesc']))){
			$workOrderObj->woDesc = Util::escapewordquotes(urldecode($_POST['woDesc']));
		}else{
			die("MAIL BODY MUST HAVE SOME VALUE");
		}
		if(ISSET($_POST['ccList']) && (!empty($_POST['ccList']))){
			$workOrderObj->ccLists = $wo->getCCList($_POST['ccList']);
		}
		/******************************************************************/
		if(ISSET($_POST['lh_utc_time']) && (!empty($_POST['lh_utc_time']))){
			//convert milli second in to second
			$apiTime = $_POST['lh_utc_time'];
			$javaTime = trim($_POST['lh_utc_time'])/1000;
		}else{
			die("UTC TIME NOT DEFINED");
		}
		if(ISSET($_POST['source_host_name']) && (!empty($_POST['source_host_name']))){
			$hostname = $_POST['source_host_name'];
		}else{
			die("HOST NAME NOT FOUND");
		}
		
		$tokenInput = $requestorEmail.'|'.$hostname.'|'.$apiTime.'|'.SALT;
	        //print_r($workOrderObj);
		
		$cs_token = md5($tokenInput);
		
		if(ISSET($_POST['lh_token']) && (!empty($_POST['lh_token']))){
			
			$lh_token = $_POST['lh_token'];
		}else{
			die("TOKEN NOT FOUND");
		}
		$phpTime = time();
		/*************************************************************/
		$workOrderObj->reqType = $customFieldsArray[$woType];
		// 2 = PROBLEM
		if($workOrderObj->reqType == 2){
			if(ISSET($_POST['lh_severity']) && (!empty($_POST['lh_severity']))){
				$workOrderObj->severity = getSeverityValue($_POST['lh_severity']);
			}else{
				$workOrderObj->severity = 6;
			}
		}
		//calculte time difference
		$timeDiffernce = round(abs($phpTime-$javaTime)/60,2);
		
		if( $cs_token == $lh_token){
			if($timeDiffernce <= 15){
				$result = $wo->saveWorkorder($workOrderObj);
			}else{
				die("TIME LIMIT EXCEED");
			}
		}else{
			die("TOKEN MISS-MATCH");
		}
		if($result == TRUE){
			die("SCC001");
		}else{
			die("FAILED");
		}
	
		
	}

	function getSeverityValue($sev){
		
		//Severity 1 =5 , sev2  =6, sev3 =7
		switch($sev){
		 case 5:
			return 5;
			break;
		case 6:
			return 6;
			break;
		case 7:
			return 7;
			break;
		default :
			return 6;
			}
	}
