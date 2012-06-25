<?PHP
	//Production
	$rootPath = '/var/www/lighthouse-uxd/lighthouse';
	//dev
//	$rootPath = '/var/www/lighthouse-uxd/lhdev';

	define('AJAX_CALL', '0');
	include($rootPath . '/html/_inc/config.inc');
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	$logFileName = $rootPath . "/html/crons/rally_timestamp.log.old";

	function getResult($set_request_url){
		$session = curl_init();
		curl_setopt($session, CURLOPT_URL, $set_request_url); // set url to post to 
		curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_USERPWD, RALLY_USERNAME . ":" . RALLY_PASSWORD);
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);

		$response = curl_exec($session);
		curl_close($session);

		if (($xml = strstr($response, '<?xml')))
		{
			try 
			{
				$parseXml = new SimpleXMLElement($xml);  //ok, we have xml, now try to parse it
				if(empty($parseXml->Errors)){
					$result['isValid'] = true;
					$result['objXml'] = $parseXml;
				}else{
					$result['isValid'] = false;
					$result['objXml'] = null;
				}
			} 
			catch (Exception $e)
			{
			   // handle the error
				$result['isValid'] = false;
				$result['objXml'] = null;
			}
		}else{
			$result['isValid'] = false;
			$result['objXml'] = null;
		}
		return $result;
	}
	
	$lh_priority =array("low"=>"1", 
						"medium"=>"2", 
						"high"=>"3", 
						"urgent"=>"4", 
						"critical"=>"4");

	$lh_status = array( "open"=>"6",
						"new"=>"6",
						"needfeedback"=>"5",
						"investigating"=>"7",
						"fixunderstood"=>"7",
						"fixinprogress"=>"7",
						"fixsubmittedindev"=>"8",
						"openindev"=>"6",
						"verifiedindev"=>"8",
						"movedtostaging"=>"9",
						"openinstaging"=>"6",
						"verifiedinstaging"=>"9",
						"movedtoprod"=>"3",
						"openinprod"=>"6",
						"closed"=>"3",
						"onhold"=>"4",
						"cannotreproduce"=>"5",
						"worksasdesigned"=>"5",
						"movedtoqa"=>"8",
						"backlog"=>"6",
						"defined"=>"7",
						"in-progress"=>"7",
						"completed"=>"3",
						"accepted"=>"3");

	$month_array = array("january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december");

	if(file_exists ($logFileName) && filesize($logFileName) > 0)
	{   
		$read = fopen($logFileName, "r");
		$lastUpdatedTime = fread($read, filesize($logFileName));
		fclose($read);
	}
	else
	{
		$lastUpdatedTime = gmdate("Y-m-d H:i:s");
	}
	$lastUpdatedDate = str_replace(" ", "T", $lastUpdatedTime).".00Z";
	$rally = array(
		"defect" => "https://rally1.rallydev.com/slm/webservice/1.08/defect?query=%20((LastUpdateDate%20%3E=%20%22%date%%22)%20AND%20(LighthouseID%20!=%20%22null%22))",
		"enhancement" => "https://rally1.rallydev.com/slm/webservice/1.08/hierarchicalrequirement?query=%20((LastUpdateDate%20%3E=%20%22%date%%22)%20AND%20(LighthouseID%20!=%20%22null%22))"
	);

	$user_sql = 'SELECT `id` FROM `users` WHERE `user_name`="' . BASECAMP_RALLY_USERNAME . '"';
	$user_result = $mysql->query($user_sql);
	$user_row = $user_result->fetch_assoc();

	foreach($rally as $type=>$url){
		$url = str_replace("%date%",$lastUpdatedDate, $url);
		$result = getResult($url);

		if($result['isValid']){
			foreach($result['objXml']->Results->Object as $object){
				foreach($object->attributes() as $key=>$value){
					$result_array['Results'][(string)$key] = (string)$value;
				}

				$defectURL = $result_array['Results']['ref'];
				$defect_result = getResult($defectURL);
				$sendMail = false;
				if($defect_result['isValid']){
					$lh_wo = $defect_result['objXml'];
					if(array_key_exists("Release", $lh_wo)){
						foreach($lh_wo->Release->attributes() as $key=>$value){
							$result_array['Release'][(string)$key] = (string)$value;
						}
					}else{
						$result_array['Release']['refObjectName'] = '';
					}
					$lh_wo_id = $lh_wo->LighthouseID;
					$defect_rally_comment = $lh_wo->Notes;
					if($type == "defect"){
						$priority = strtolower($lh_wo->Priority);
						$status = str_replace(" ", "", strtolower($lh_wo->State));
					}else if($type == "enhancement"){
						$status = strtolower($lh_wo->ScheduleState);
					}

					$defect_rally_release = strtolower($result_array['Release']['refObjectName']);
					$release_month = "";
					$release_type = "";
					if($defect_rally_release != ""){
						foreach($month_array as $each_month){
							if(stristr($defect_rally_release, $each_month)){
								$release_month = $each_month;
								if(stripos($defect_rally_release, "Release")){
									$release_type = "last week";
								}else if(stripos($defect_rally_release, "Bug Fix")){
									$release_type = "second week";
								}
								break;
							}
						}
					}
					
					$bc_id_query = "SELECT  `bcid`, `project_id`, `title`, `priority`, `status`, `body` FROM `workorders` WHERE `id`='" . $lh_wo_id ."' LIMIT 1";
					$bc_id_result = $mysql->query($bc_id_query);
					$bc_id_row_before = $bc_id_result->fetch_assoc();

					$update_wo_sql = 'UPDATE `workorders` SET ';
					if($type == "defect"){
						$update_wo_sql .= ' `priority`="' . $lh_priority[$priority] . '", ';
					}

					$wo_sql = "SELECT * FROM `workorders` WHERE `id`='" . $lh_wo_id . "'";
					$wo_result = $mysql->query($wo_sql);
					$wo_row = $wo_result->fetch_assoc();
					if($defect_rally_release != "" && $defect_rally_release != $wo_row['rally_release']){
						$update_wo_sql .= ' `rally_release`="' . $defect_rally_release . '", ';
						$sendMail = true;
//						$release_comment = "This will be moved to production on " . $release_type . " of " . ucfirst($release_month) . " by platform team.";
						$release_comment = "This ticket is being reviewed and a timeline for implementation will be reported out.";
					}
					$update_wo_sql .= ' `status`="' . $lh_status[$status] . '" WHERE `id`="' . $lh_wo_id . '"';
					$mysql->query($update_wo_sql);

					$bc_id_query = "SELECT  `bcid`, `project_id`, `title`, `priority`, `status`, `body` FROM `workorders` WHERE `id`='" . $lh_wo_id ."' LIMIT 1";
					$bc_id_result = $mysql->query($bc_id_query);
					$bc_id_row = $bc_id_result->fetch_assoc();

					$notes_flag="0";
					if($lh_status[$status] == "3" && $bc_id_row_before['status'] != '3'){
						sendMail($lh_wo_id, $user_row['id'], $bc_id_row, '', $mysql, 'completed');
					}
					if($sendMail){
						sendMessage($lh_wo_id, $user_row['id'], $release_comment, $bc_id_row, $mysql,$notes_flag);
					}

					if($defect_rally_comment != ""){
						$lh_wo_comment_sql = 'SELECT `comment`,`id` FROM `workorder_comments` WHERE `user_id`="' . $user_row['id'] . '" AND `workorder_id`="' . $lh_wo_id . '" AND `rally_notes_flag`="1" ORDER BY `date` DESC LIMIT 1';
						$lh_wo_comment_result = $mysql->query($lh_wo_comment_sql);
						$lh_wo_comment_row = $lh_wo_comment_result->fetch_assoc();
					
						if($lh_wo_comment_result->num_rows > 0){
							if(strcasecmp($defect_rally_comment, $lh_wo_comment_row['comment']) != 0){
								updateWOComment($lh_wo_comment_row['id'],$lh_wo_id, $user_row['id'], $defect_rally_comment, $bc_id_row, $mysql);
							}
						}else{
								$notes_flag = "1";
								sendMessage($lh_wo_id, $user_row['id'], $defect_rally_comment, $bc_id_row, $mysql,$notes_flag);
							
						}

					}

//					if(strcasecmp($defect_rally_comment, $lh_wo_comment_row['comment']) != 0 && $defect_rally_comment != ""){
//						if($lh_wo_comment_row->num_rows > 0){
//							print("in second if ");
//							//updateWOComment($lh_wo_comment_row['id'],$lh_wo_id, $user_row['id'], $defect_rally_comment, $bc_id_row, $mysql);
//						}else{
//							print("in else of second if ");
//							$notes_flag = "1";
//							//sendMessage($lh_wo_id, $user_row['id'], $defect_rally_comment, $bc_id_row, $mysql,$notes_flag);
//						}
//						
//					}

				}
				unset($result_array);
			}
		}
	}

	$write = fopen($logFileName, "w+");
	fwrite($write, gmdate("Y-m-d H:i:s"));
	fclose($write);

	function sendBasecampMessage($comment, $bc_id){

		$comment_post_url = BASECAMP_HOST . '/posts/'.$bc_id.'/comments.xml';
		$comment_xml = '<comment><body>' . $comment . '</body></comment>';
		
		$session = curl_init();   

		curl_setopt($session, CURLOPT_URL, $comment_post_url); // set url to post to 
		curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($session, CURLOPT_POST, 1); 
		curl_setopt($session, CURLOPT_POSTFIELDS, $comment_xml); 
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml','Expect: '));
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session, CURLOPT_USERPWD, BASECAMP_RALLY_USERNAME . ":" . BASECAMP_RALLY_PASSWORD);

		if(ereg("^(https)",$comment_post_url)) 
			curl_setopt($session,CURLOPT_SSL_VERIFYPEER,false);

		$response = curl_exec($session);
		curl_close($session);
	}

	function sendMail($wo_id, $userID, $bc_id_row, $comment, $mysql, $updateType=''){

		$select_priority = "SELECT * FROM `lnk_workorder_priority_types` WHERE `id`='" . $bc_id_row['priority'] ."'";
		$pri_res = $mysql->query($select_priority);
		$pri_row = $pri_res->fetch_assoc();
		
		$select_email_users = "SELECT * FROM `workorders` WHERE `id`='" . $wo_id . "' LIMIT 1";
		$email_res = $mysql->query($select_email_users);
		if($email_res->num_rows > 0) {
			$new_commenter = "SELECT * FROM `users` WHERE `id`='" . $userID . "' LIMIT 1";
			$commenter_res = $mysql->query($new_commenter);
			$commenter_row = $commenter_res->fetch_assoc();

			$email_row = $email_res->fetch_assoc();

			$cc_list = $email_row['cclist'];
			$cc_list_part = explode(",", $cc_list);
			$at = $email_row['assigned_to'];
			$rb = $email_row['requested_by'];

			$users_email[$at] = true;
			$users_email[$rb] = true;

			for($e = 0; $e < sizeof($cc_list_part); $e++) {
				if(!empty($cc_list_part[$e])) {
					$users_email[$cc_list_part[$e]] = true;
				}
			}
			$user_keys = array_keys($users_email);
			$select_project = "SELECT * FROM `projects` WHERE `id`='" .$bc_id_row['project_id'] ."'";
			$project_res = $mysql->query($select_project);
			$project_row = $project_res->fetch_assoc();

			$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
			$company_res = $mysql->query($select_company);
			$company_row = $company_res->fetch_assoc();

			for($u = 0; $u < sizeof($user_keys); $u++) {
				$select_email_addr = "SELECT `email` FROM `users` WHERE `id`='" .$user_keys[$u] ."' LIMIT 1";
				$email_addr_res = $mysql->query($select_email_addr);
				$email_addr_row = $email_addr_res->fetch_assoc();
				
				$to = $email_addr_row['email'];
				$subject = "WO: " .$bc_id_row['title'] . " - Lighthouse Work Order Message";
				$headers = 'From: ' .$commenter_row['email'];
				if($updateType == ''){
					$msg = "Company: " . $company_row['name'] . "\r\n"
							."Project: " .$project_row['project_code'] ." - " .$project_row['project_name'] ."\r\n"
							."Link: " .BASE_URL ."/workorders/index/edit/?wo_id=" . $wo_id  ."\r\n\r\n"
							.ucfirst($commenter_row['first_name']) ." " .ucfirst($commenter_row['last_name']) ." commented on work order [#" . $wo_id . "]\r\n\r\n"
							."\t- Priority: " .$pri_row['name'] ."-" .$pri_row['time'] ."\r\n"
							."\t- Comment: " . strip_tags($comment, '<a><br><strong><ul><li><ol>') ."\r\n\r\n"
							."..........................................................................";
				}else{
					$msg =  "Company: " . $company_row['name'] . "\r\n"
							."Project: " . $project_row['project_code'] ." - " . $project_row['project_name'] ."\r\n"
							."Link: " .BASE_URL ."/workorders/index/edit/?wo_id=" . $wo_id  ."\r\n\r\n"
							."WO [#" . $wo_id . "] has been completed by " . $commenter_row['first_name'] . " ". $commenter_row['last_name'] . "\r\n\r\n"
							."\t-Priority: " . $pri_row['name'] ."-" . $pri_row['time'] ."\r\n"
							."\t-Description: " . $bc_id_row['body'] ."\r\n\r\n"
							."..........................................................................";
				}
				if(!empty($to)) {
					mail($to, $subject, $msg, $headers);
				}
			}
		}
	}

	function sendMessage($wo_id, $userID, $comment, $bc_id_row, $mysql,$notes_flag){

		$wo_comment_sql = 'INSERT INTO `workorder_comments` (`workorder_id`, `user_id`, `comment`, `rally_notes_flag`, `date`) VALUES ("' . $wo_id . '", "' . $userID . '", "' . $comment . '", "' . $notes_flag . '", NOW())';
		$mysql->query($wo_comment_sql);
		sendBasecampMessage($comment, $bc_id_row['bcid']);
		sendMail($wo_id, $userID, $bc_id_row, $comment, $mysql);
	}

	function updateWOComment($comment_id,$wo_id, $userID, $comment, $bc_id_row, $mysql){

		$wo_comment_sql = 'update `workorder_comments` set `comment`="' . $comment . '", `date`= NOW() where `id`="' . $comment_id . '"';
		$mysql->query($wo_comment_sql);
		sendBasecampMessage($comment, $bc_id_row['bcid']);
		sendMail($wo_id, $userID, $bc_id_row, $comment, $mysql);
	}

?>
