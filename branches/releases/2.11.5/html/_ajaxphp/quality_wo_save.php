<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	include('../_ajaxphp/util.php');
	$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	
	if(isset($_SESSION['user_id'])) {
		$user = $_SESSION['lh_username'];
	    $password = $_SESSION['lh_password'];
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		//Defining Global mysql connection values
		global $mysql;
			
		$defectID = $mysql->real_escape_string(@$_POST['defectID']);
		$dirName = $mysql->real_escape_string(@$_POST['dirName']);

		$projectId = $mysql->real_escape_string(@$_POST['projectId']);
		$priorityId = $mysql->real_escape_string(@$_POST['priorityId']);
		$timeSens = $mysql->real_escape_string(@$_POST['timeSens']);
		$timeSensDate = $mysql->real_escape_string(@$_POST['timeSensDate']);
		$timeSensTime = $mysql->real_escape_string(@$_POST['timeSensTime']);
		$ampm = $mysql->real_escape_string(@$_POST['ampm']);
		$woTitle = $mysql->real_escape_string(Util::escapewordquotes(@$_POST['woTitle']));
		$woExampleURL = $mysql->real_escape_string(Util::escapewordquotes(@$_POST['woExampleURL']));
		$woDesc = $mysql->real_escape_string(Util::escapewordquotes(@$_POST['woDesc']));
		$woAssignedTo = $mysql->real_escape_string(@$_POST['woAssignedTo']);
		$woStatus = $mysql->real_escape_string(@$_POST['woStatus']);
		$woStartDate = $mysql->real_escape_string(@$_POST['woStartDate']);

		$qaCATEGORY = $mysql->real_escape_string(@$_POST['qaCATEGORY']);
		$qaSEVERITY = $mysql->real_escape_string(@$_POST['qaSEVERITY']);
		$qaOS = $mysql->real_escape_string(@$_POST['qaOS']);
		$qaBROWSER = $mysql->real_escape_string(@$_POST['qaBROWSER']);
		$qaVERSION = $mysql->real_escape_string(@$_POST['qaVERSION']);
		$qaORIGIN = $mysql->real_escape_string(@$_POST['qaORIGIN']);
		$qaDETECTED_BY = $mysql->real_escape_string(@$_POST['qaDETECTED_BY']);
		$requestedId = $mysql->real_escape_string(@$_POST['requestedId']);
		$qaCCList  = $mysql->real_escape_string(@$_POST['qaCCList']);
		//LH#28522
		$qaITERATION = $mysql->real_escape_string(@$_POST['qaITERATION']);
		$qaPRODUCT = $mysql->real_escape_string(@$_POST['qaPRODUCT']);
		if($timeSens == "true") {

		$select_wo_old = "SELECT * FROM `qa_defects` WHERE `id`= ? ";
		$wo_old_res = $mysql->sqlprepare($select_wo_old,array($defectID));
		$wo_old_row = $wo_old_res->fetch_assoc();		

		

		$getdefectID = '';
		
		if(!empty($woAssignedTo)) {
			$assignedDate = 'NOW()';
		} else {
			$assignedDate = '\'\'';
		}

		if(empty($defectID) || empty($woStatus)) {
			$woStatus = 1;
		}


		if(empty($defectID)) {
			
			$dt_part_start = @explode("/", $woStartDate);
			
			if(!empty($woStartDate)) {
				$dtStart = "'" .@$dt_part_start[2] ."-" .@$dt_part_start[0] ."-" .@$dt_part_start[1] .' ' .date('H:i:s') ."' ";
			} else {
				$dtStart = "NOW()";
			}
		
			$dtEnd = "'" .(date('Y')+5) ."-12-31 " .date('H:i:s') ."'";

			$insert_qa = "INSERT INTO `qa_defects` "
				."(`project_id`, `status`, `title`, `example_url`, `body`, `assigned_to`, `requested_by`, `detected_by`, `assigned_date`,`cclist`, `category`, `os`,`origin`, `browser`,  `severity`, `version`,  `creation_date`, `launch_date`,`iteration`,`product`)"
				."VALUES "
				."('$projectId','$woStatus','$woTitle','$woExampleURL','$woDesc','$woAssignedTo','$requestedId','$qaDETECTED_BY',$assignedDate,'$qaCCList','$qaCATEGORY','$qaOS','$qaORIGIN','$qaBROWSER','$qaSEVERITY','$qaVERSION',$dtStart,$dtEnd,'$qaITERATION','$qaPRODUCT')";
				@$mysql->sqlordie($insert_qa);
				$getdefectID = $mysql->insert_id;			
				$update_wo = "UPDATE `projects` SET `qa_permission`='1' WHERE `id`='$projectId'";
			        @$mysql->sqlordie($update_wo);	
		} else {			
			$qaDETECTED_BY = $wo_old_row['detected_by'];
			$requestedId = $wo_old_row['requested_by'] ;

			$close_date = "";
			if($woStatus == 8){
				$close_date = "`closed_date`=NOW(), ";
			}
			
			$complete_date = "";
			if($woStatus == 3){
				$complete_date = "`completed_date`=NOW(), ";
			}

			$update_wo = "UPDATE `qa_defects` SET "
				."`project_id`='$projectId', "
				."`status`='$woStatus', "
				."`title`='$woTitle', "
				."`example_url`='$woExampleURL', "
				."`body`='$woDesc', "
				."`assigned_to`='$woAssignedTo', "
				."`requested_by`='$requestedId', "
				."`detected_by`='$qaDETECTED_BY', "
				."`category`='$qaCATEGORY', "
				."`cclist`='$qaCCList', "
				."`os`='$qaOS', "
				."`origin`='$qaORIGIN', "
				."`browser`='$qaBROWSER', "
				."`severity`='$qaSEVERITY', "
				.$assigned_date
				.$close_date
				.$complete_date
				."`version`='$qaVERSION',"
				."`iteration`='$qaITERATION',"
				."`product`='$qaPRODUCT'"
				."WHERE `id`='$defectID'";

			@$mysql->sqlordie($update_wo);
			$getdefectID = $defectID;		

		}					

		}
		
		if(!empty($getdefectID) && $getdefectID > 0) {
			@rename($_SERVER['DOCUMENT_ROOT']."/qafiles/" .$dirName,  $_SERVER['DOCUMENT_ROOT']."/qafiles/" .$getdefectID);
			
			$update_files = "UPDATE `qa_files` SET `defect_id`='$getdefectID', `directory`='$getdefectID' WHERE `directory`='" .str_replace("/", "", $dirName) ."'";
			@$mysql->sqlordie($update_files);
		}
		
		$select_wo = "SELECT * FROM `qa_defects` WHERE `id`= ? ";
		$wo_res = $mysql->sqlprepare($select_wo,array($getdefectID));
		$wo_row = $wo_res->fetch_assoc();
		$select_user = "SELECT * FROM `users` WHERE `id`= ? ";
		$user_res = $mysql->sqlprepare($select_user,array($woAssignedTo));
		$user_row = $user_res->fetch_assoc();		
		
		$cclist = array();
		if("" != trim($wo_row['cclist'])){
			$cclist = explode(",", $wo_row['cclist']);
		}

		for($v = 0; $v < sizeof($cclist); $v++) {
			if($cclist[$v] != '')
				$users_email[$cclist[$v]] = true;
		}
              
		//$users_email[$requestedId] = true;
		$users_email[$woAssignedTo] = true;

		$user_keys = array_keys($users_email);

		$select_project = "SELECT * FROM `projects` WHERE `id`= ? ";
		$project_res = $mysql->sqlprepare($select_project,array($wo_row['project_id']));
		$project_row = $project_res->fetch_assoc();

		$select_company = "SELECT * FROM `companies` WHERE `id`= ? ";
		$company_res = $mysql->sqlprepare($select_company,array($project_row['company']));
		$company_row = $company_res->fetch_assoc();

		$wo_status = "SELECT * FROM `lnk_qa_status_types` WHERE `id`= ? ";
		$wo_status_res = $mysql->sqlprepare($wo_status,array($woStatus));
		$wo_status_row = $wo_status_res->fetch_assoc();

		$subject = "Defect ".$getdefectID.": ".$wo_status_row['name']." - ".$wo_row['title'];
		$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";


		$file_list = "";
		$select_file = "SELECT * FROM `qa_files` WHERE defect_id= ? order by upload_date desc";
		$file_res = $mysql->sqlprepare($select_file,array($getdefectID));
		if($file_res->num_rows > 0) {
			$file_list = "<u><b>Attachment:</b></u><br>";
			$fileCount = 1;
			while($file_row = $file_res->fetch_assoc()){
				//$file_list .= "" . $fileCount . ". <a href='".BASE_URL . "/qafiles/" . $file_row['directory'] . "/" .$file_row['file_name']."'>" . $file_row['file_name'] . "</a><br>";
			$file_list .= "" . $fileCount . ". <a href='".BASE_URL_FILE_PATH . "qafiles/" . $file_row['directory'] . "/" .$file_row['file_name']."'>" . $file_row['file_name'] . "</a><br>";
	
			$fileCount += 1;
			}
		}
		if(empty($defectID)){
			// When a new Defect is created
			$users_email[$requestedId] = true;
            $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($wo_row['body'],ENT_NOQUOTES,'UTF-8'));
	
	foreach($users_email as $user => $val){
				
		         	//if($user == $woAssignedTo)
				//lh24569	
				 if($user!= $requestedId)
				{

					$select_email_addr = "SELECT `email` FROM `users` WHERE `id`= ? LIMIT 1";
					$email_addr_res = $mysql->sqlprepare($select_email_addr,array($user));
					$email_addr_row = $email_addr_res->fetch_assoc();
					$to = $email_addr_row['email'];
					$link = "<a href='".BASE_URL ."/quality/index/edit/?defect_id=" .$getdefectID."'>".$getdefectID."</a>";
				
				if($user == $woAssignedTo){$msg = "Defect [$link] has been created and is assigned to you.<br>";}
 				else
					{$msg = "Defect [$link] has been created and You have been assigned to the CC'd list.<br>";}
					$msg.= "<b>Company: </b>" . $company_row['name'] . "<br>";
					$msg.= "<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br>";
					$msg.= "<b>Summary: </b>" .htmlentities($wo_row['title'],ENT_NOQUOTES,'UTF-8') ."<br>";
					$msg.= "<b>Description: </b>" .$desc_string ."<br>";
					$msg.= $file_list."<br>";

					if(!empty($to)){  
						sendEmail($to, $subject, $msg, $headers);
					}
					//break;
				}
			}
			insertWorkorderAudit($mysql,$getdefectID, '1', $_SESSION['user_id'],$user,$woStatus);
		}else if($wo_row['assigned_to'] != $wo_old_row['assigned_to']){
					$select_email_addr = "SELECT `email` FROM `users` WHERE `id`= ? LIMIT 1";
					$email_addr_res = $mysql->sqlprepare($select_email_addr,array($wo_row['assigned_to']));
					$email_addr_row = $email_addr_res->fetch_assoc();
					$to = $email_addr_row['email'];
			//if($user == $woAssignedTo){
					$msg = "Defect [$link] is assigned to you.<br>";
			//}
					$msg.= "<b>Company: </b>" . $company_row['name'] . "<br>";
					$msg.= "<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br>";
					$msg.= "<b>Summary: </b>" .htmlentities($wo_row['title'],ENT_NOQUOTES,'UTF-8') ."<br>";
					$msg.= "<b>Description: </b>" .$desc_string ."<br>";
					$msg.= $file_list."<br>";

					if(!empty($to)){  
						sendEmail($to, $subject, $msg, $headers);
					}
		
			insertWorkorderAudit($mysql,$getdefectID, '2', $_SESSION['user_id'],$wo_row['assigned_to'],$woStatus);
		} 
		else if($wo_row['status'] != $wo_old_row['status'])
		{
			//if($woStatus=='3' || $woStatus=='4' || $woStatus=='5' || $woStatus=='6' || $woStatus=='10'){
		// When the WO is assinged to a new person
			$description=($wo_row['body']);
			$desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES,'UTF-8'));
			$sendList = array_unique($users_email);
			$sendList = array_keys($sendList);
			$msg = '';
			foreach($sendList as $user){

				$select_email_addr = "SELECT `email` FROM `users` WHERE `id`= ? LIMIT 1";
				$email_addr_res = $mysql->sqlprepare($select_email_addr,array($user));
				$email_addr_row = $email_addr_res->fetch_assoc();
				$to = $email_addr_row['email'];

				$link = "<a href='".BASE_URL ."/quality/index/edit/?defect_id=" .$getdefectID."'>".$getdefectID."</a>";
				if($woStatus=='3')
				{
					$msg = "Defect [$link] has been fixed and is assigned to you for a regression test.<br>";
				}
				else if($woStatus=='4')
				{
					$msg = "Defect [$link] has been rejected by QA because they could not verify the fix. This defect is assigned to you for a fix.<br>";
				}
				else if($woStatus=='5')
				{
					$msg = "Defect [$link] has been reopened and is assigned to you for a fix.<br>";
				}
				else if($woStatus=='6')
				{
					$msg = "Defect [$link] requires more information from you.<br>";
				}
				else if($woStatus=='10')
				{
					$msg = "Defect [$link] Feed back provided to you.<br>";
				}
				if(!empty($msg))
				{
					$msg.= "<b>Company: </b>" . $company_row['name'] . "<br>";
					$msg.= "<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br>";
                    $msg.= "<b>Summary: </b>" .htmlentities($wo_row['title'],ENT_NOQUOTES,'UTF-8') ."<br>";
                    $msg.= "<b>Description: </b>" .$desc_string ."<br>";
					$msg.= $file_list."<br>";

					if(!empty($to)){
						sendEmail($to, $subject, $msg, $headers);
					}
				}
			}
			insertWorkorderAudit($mysql,$getdefectID, '3', $_SESSION['user_id'],$wo_row['assigned_to'],$woStatus);
		}
		
		echo $getdefectID;
	}

	function sendEmail($to, $subject, $msg, $headers){
		$msg = nl2br($msg);
		$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
		mail($to, $subject, $msg, $headers);
	}		

	function insertWorkorderAudit($mysql,$defect_id, $audit_id, $log_user_id,$assign_user_id,$status)
	{
		$insert_custom_feild = "INSERT INTO  `quality_audit` (`defect_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$defect_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
		@$mysql->sqlordie($insert_custom_feild);
	}
	
?>
