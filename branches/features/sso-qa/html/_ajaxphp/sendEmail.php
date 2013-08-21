<?PHP
	// to send email
	function sendEmail_newRequest($mysql, $wo_row)
	{	

			$requestedId = $wo_row['requested_by'];
			$woAssignedTo = $wo_row['assigned_to'];
			//LH 20679 #remove special characters from title
			$woTitle = $wo_row['title'];
			$woStatus = $wo_row['status'];
			$woREQ_TYPE = $wo_row['type'];
			$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

		    $wo_id = $wo_row['id'];

			$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$wo_id' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
			$req_type_res = $mysql->sqlordie($select_req_type_qry);
			$req_type_row = $req_type_res->fetch_assoc();		

			$cclist = array();	  	

			if("" != trim($wo_row['cclist'])){
				$cclist = explode(",", $wo_row['cclist']);
			}

			for($v = 0; $v < sizeof($cclist); $v++) {
				if($cclist[$v] != ''){
					$users_email[$cclist[$v]] = true;
				}
			}

			$users_email[$requestedId] = true;
			$users_email[$woAssignedTo] = true;
	  
			$user_keys = array_keys($users_email);
			$request_type_arr = array(	"Submit a Request" => "Request",
										"Report a Problem" => "Problem",
										"Report an Outage" => "Outage");
			/*$select_project = "SELECT * FROM `projects` WHERE `id`='" .$wo_row['project_id'] ."'";
			$project_res = $mysql->sqlordie($select_project);
			$project_row = $project_res->fetch_assoc();*/
	  
			$select_company = "SELECT * FROM `companies` WHERE `id`='" . $wo_row['company_id'] . "'";
			$company_res = $mysql->sqlordie($select_company);
			$company_row = $company_res->fetch_assoc();
	  
			$wo_status = "SELECT * FROM `lnk_workorder_status_types` WHERE `id`='" .$woStatus ."'";
			$wo_status_res = $mysql->sqlordie($wo_status);
			$wo_status_row = $wo_status_res->fetch_assoc();			
			
			//$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";
			$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=UTF-8";
			//If ticket is critical then set header as Higher Priority
			$select_req_type_qry_critical = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a INNER JOIN `lnk_custom_fields_value` b  ON (a.field_id = b.field_id) WHERE `workorder_id`='$wo_id' and a.field_key='CRITICAL' ";
			$req_type_res_cri = $mysql->sqlordie($select_req_type_qry_critical);
			$req_type_row_critical = $req_type_res_cri->fetch_assoc();
			
			if($req_type_row_critical['field_id']== '13'){
				$headers .= "\r\n";
				$headers .= "X-Priority: 1 (Highest)";
				$headers .= "\r\n";
				$headers .= "X-MSMail-Priority: High";
				$headers .= "\r\n";
				$headers .= "Importance: High";
			}
			///////////////////END
			$requestor_qry = "SELECT * FROM `users` WHERE `id`='" .$requestedId ."'";
			$requestor_user_res = $mysql->sqlordie($requestor_qry);
			$requestor_user_row = $requestor_user_res->fetch_assoc();
			
			$site_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SITE_NAME' and cu.field_key = 'SITE_NAME' and cu.workorder_id = '" .$wo_id ."'";
			$site_name_res = $mysql->sqlordie($site_name_qry);
			$site_name_row = $site_name_res->fetch_assoc();
	  
			$file_list = "";
			$select_file = "SELECT * FROM `workorder_files` WHERE workorder_id='" . $wo_id . "' order by upload_date desc";
			$file_res = $mysql->sqlordie($select_file);
			if($file_res->num_rows > 0) {
				$file_list = "<u><b>Attachment:</b></u><br>";
				$fileCount = 1;
				while($file_row = $file_res->fetch_assoc()){
					//$file_list .= "" . $fileCount . ". <a href='".BASE_URL . "/files/" . $file_row['directory'] . "/" .$file_row['file_name']."'>" . $file_row['file_name'] . "</a><br>";
				$file_list .= "" . $fileCount . ". <a target='_blank' href='".BASE_URL_FILE_PATH . "files/" . $file_row['directory'] . "/" .$file_row['file_name']."'>" . $file_row['file_name'] . "</a><br>";	
				$fileCount += 1;
				}
			}

			$woStatusText = $wo_status_row['name'];
			$description=($wo_row['body']);
            $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES,'UTF-8'));
			foreach($users_email as $user => $val){
				$select_email_addr = "SELECT email FROM `users` WHERE `id`='" . $user ."' LIMIT 1";
				$email_addr_res = $mysql->sqlordie($select_email_addr);
				$email_addr_row = $email_addr_res->fetch_assoc();
				$to = $email_addr_row['email'];
				$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$wo_id."'>".$wo_id."</a>";
	  
				$msg =  "<b>Requestor: </b>" . $requestor_user_row['first_name'].' '. $requestor_user_row['last_name']. "<br><br>";
				$msg .="<b>Company: </b>" . $company_row['name'] . "<br><br>";
				//$msg .="<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br><br>";
				$msg .="<b>Site: </b>" .$site_name_row['field_name'] ."<br><br>";
				
				if($user == $requestedId )
				{
					$msg .="<b>WO [" . $link . "] </b> has been created. You will be updated automatically when an engineer begins working on it.<br><br>";
				}
				else if($user == $woAssignedTo)
				{
					$msg .="<b>WO [" . $link . "] </b> has been created. Please review the request and take appropriate action.<br><br>";
				}
				else
				{
					$msg .="<b>WO [" . $link . "] </b> has been created. You were cc'd on this request.<br><br>";
				}

				$msg .="<b>Request Type: </b>" .$request_type_arr[$req_type_row['field_name']] ."<br>";
				


//code for lh 18306

$severity_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SEVERITY' and cu.field_key = 'SEVERITY' and cu.workorder_id = '" .$wo_id ."'";
			$severity_name_res = $mysql->sqlordie($severity_name_qry);
			$severity_name_row = $severity_name_res->fetch_assoc();
				
				if($request_type_arr[$req_type_row['field_name']]=='Problem')
				{
				
				$msg .="<br><b>Severity: </b>" .$severity_name_row['field_name']  ."<br>";
				}
                $msg .="<hr><b>Description:</b> " . $desc_string ."<br><br>";
				$msg .=$file_list;
				$subject = "WO ".$wo_id.": ".$woStatusText." - ".$req_type_row['field_name']." - " . html_entity_decode($woTitle,ENT_NOQUOTES,'UTF-8') . "";
				if(!empty($to)){ 
					lh_sendEmail($to, $subject, $msg, $headers); 		
				}
			}
	}	

	function sendEmail_assignedTO($mysql, $wo_row)
	{	

			$requestedId = $wo_row['requested_by'];
			$woAssignedTo = $wo_row['assigned_to'];
			//LH 20679 #remove special characters from title
			$woTitle = $wo_row['title'];
			$woStatus = $wo_row['status'];
			$woREQ_TYPE = $wo_row['type'];
			$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

		    $wo_id = $wo_row['id'];

			$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$wo_id' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
			$req_type_res = $mysql->sqlordie($select_req_type_qry);
			$req_type_row = $req_type_res->fetch_assoc();		

			$cclist = array();	  	

			if("" != trim($wo_row['cclist'])){
				$cclist = explode(",", $wo_row['cclist']);
			}

			for($v = 0; $v < sizeof($cclist); $v++) {
				if($cclist[$v] != ''){
					$users_email[$cclist[$v]] = true;
				}
			}

			$users_email[$requestedId] = true;
			$users_email[$woAssignedTo] = true;
	  
			$user_keys = array_keys($users_email);
			$request_type_arr = array(	"Submit a Request" => "Request",
										"Report a Problem" => "Problem",
										"Report an Outage" => "Outage");
			/*$select_project = "SELECT * FROM `projects` WHERE `id`='" .$wo_row['project_id'] ."'";
			$project_res = $mysql->sqlordie($select_project);
			$project_row = $project_res->fetch_assoc();*/
	  
			$select_company = "SELECT * FROM `companies` WHERE `id`='" . $wo_row['company_id']. "'";
			$company_res = $mysql->sqlordie($select_company);
			$company_row = $company_res->fetch_assoc();
	  
			$wo_status = "SELECT * FROM `lnk_workorder_status_types` WHERE `id`='" .$woStatus ."'";
			$wo_status_res = $mysql->sqlordie($wo_status);
			$wo_status_row = $wo_status_res->fetch_assoc();			
			$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=UTF-8";
			//$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";
	 		//If ticket is critical then set header as Higher Priority
			$select_req_type_qry_critical = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a INNER JOIN `lnk_custom_fields_value` b  ON (a.field_id = b.field_id) WHERE `workorder_id`='$wo_id' and a.field_key='CRITICAL' ";
			$req_type_res_cri = $mysql->sqlordie($select_req_type_qry_critical);
			$req_type_row_critical = $req_type_res_cri->fetch_assoc();
			
			if($req_type_row_critical['field_id']== '13'){
				$headers .= "\r\n";
				$headers .= "X-Priority: 1 (Highest)";
				$headers .= "\r\n";
				$headers .= "X-MSMail-Priority: High";
				$headers .= "\r\n";
				$headers .= "Importance: High";
			}
			///////////////////END 
			$requestor_qry = "SELECT * FROM `users` WHERE `id`='" .$requestedId ."'";
			$requestor_user_res = $mysql->sqlordie($requestor_qry);
			$requestor_user_row = $requestor_user_res->fetch_assoc();

			$select_user = "SELECT * FROM `users` WHERE `id`='" .$woAssignedTo ."'";
			$user_res = $mysql->sqlordie($select_user);
			$assigned_user_row = $user_res->fetch_assoc();
			
			$site_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SITE_NAME' and cu.field_key = 'SITE_NAME' and cu.workorder_id = '" .$wo_id ."'";
			$site_name_res = $mysql->sqlordie($site_name_qry);
			$site_name_row = $site_name_res->fetch_assoc();
	  
			$file_list = "";
			$select_file = "SELECT * FROM `workorder_files` WHERE workorder_id='" . $wo_id . "' order by upload_date desc";
			$file_res = $mysql->sqlordie($select_file);
			if($file_res->num_rows > 0) {
				$file_list = "<u><b>Attachment:</b></u><br>";
				$fileCount = 1;
				while($file_row = $file_res->fetch_assoc()){
					//$file_list .= "" . $fileCount . ". <a href='".BASE_URL . "/files/" . $file_row['directory'] . "/" .$file_row['file_name']."'>" . $file_row['file_name'] . "</a><br>";
				$file_list .= "" . $fileCount . ". <a  target='_blank' href='".BASE_URL_FILE_PATH . "files/" . $file_row['directory'] . "/" .$file_row['file_name']."'>" . $file_row['file_name'] . "</a><br>";	
				$fileCount += 1;
				}
			}

			$woStatusText = $wo_status_row['name'];

			$subject = "WO ".$wo_id.": ".$woStatusText." - ".$req_type_row['field_name']." - " . html_entity_decode($woTitle,ENT_NOQUOTES,'UTF-8') . "";
			$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$wo_id."'>".$wo_id."</a>";
            $description=($wo_row['body']);
            $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES,'UTF-8'));
			//foreach($users_email as $user => $val){
				$user = $woAssignedTo; // Send email only to the assigned person.
				$select_email_addr = "SELECT `email` FROM `users` WHERE `id`='" . $user ."' LIMIT 1";
				$email_addr_res = $mysql->sqlordie($select_email_addr);
				$email_addr_row = $email_addr_res->fetch_assoc();
				$to = $email_addr_row['email'];
				$msg =  "<b>Requestor: </b>" . $requestor_user_row['first_name'].' '. $requestor_user_row['last_name']. "<br><br>";
				$msg .="<b>Company: </b>" . $company_row['name'] . "<br><br>";
				//$msg .="<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br><br>";
				$msg .="<b>Site: </b>" .$site_name_row['field_name'] ."<br><br>";


				/*if($user == $requestedId)
				{
					$msg .="<b>WO [" . $link . "] </b> has been assigned to ".$assigned_user_row['first_name'].' '.$assigned_user_row['last_name'].".<br><br>";
				}
				else if($user == $woAssignedTo)
				{
					$msg .="<b>WO [" . $link . "] </b> has been assigned to you. Please begin work on this ticket and update the status and notes as appropriate as you complete your work.<br><br>";
				}
				else
				{
					$msg .="<b>WO [" . $link . "] </b> has been assigned to ".$assigned_user_row['first_name'].' '.$assigned_user_row['last_name'].".<br><br>";
				}*/
				$msg .="<b>WO [" . $link . "] </b> has been assigned to you. Please begin work on this ticket and update the status and notes as appropriate as you complete your work.<br><br>";
				$msg .="<b>Request Type: </b>" .$request_type_arr[$req_type_row['field_name']] ."<br>";
			
//code for lh no 18306


$severity_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SEVERITY' and cu.field_key = 'SEVERITY' and cu.workorder_id = '" .$wo_id ."'";
			$severity_name_res = $mysql->sqlordie($severity_name_qry);
			$severity_name_row = $severity_name_res->fetch_assoc();
				
				if($request_type_arr[$req_type_row['field_name']]=='Problem')
				{
				
				$msg .="<br><b>Severity: </b>" .$severity_name_row['field_name']  ."<br>";
				}
	$msg .="<hr><b>Description:</b> " . $desc_string ."<br><br>";
				$msg .=$file_list;
				
				if(!empty($to)){
					
					lh_sendEmail($to, $subject, $msg, $headers);
				
				}
			//}
	}
	function sendEmail_overDueAlerts($mysql, $wo_row){
			$requestedId = $wo_row['requested_by'];
			$woAssignedTo = $wo_row['assigned_to'];
			$launch_date = $wo_row['launch_date'];
			//LH 20679 #remove special characters from title
			$woTitle = $wo_row['title'];
			$woStatus = $wo_row['status'];
			$woREQ_TYPE = $wo_row['type'];
			$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

		    $wo_id = $wo_row['id'];

			$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$wo_id' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
			$req_type_res = $mysql->sqlordie($select_req_type_qry);
			$req_type_row = $req_type_res->fetch_assoc();		

			/*$cclist = array();	  	

			if("" != trim($wo_row['cclist'])){
				$cclist = explode(",", $wo_row['cclist']);
			}

			for($v = 0; $v < sizeof($cclist); $v++) {
				if($cclist[$v] != ''){
					$users_email[$cclist[$v]] = true;
				}
			}

			$users_email[$requestedId] = true;*/
			$users_email[$woAssignedTo] = true;
	  
			$user_keys = array_keys($users_email);
			$request_type_arr = array(	"Submit a Request" => "Request",
										"Report a Problem" => "Problem",
										"Report an Outage" => "Outage");
			$select_project = "SELECT * FROM `projects` WHERE `id`='" .$wo_row['project_id'] ."'";
			$project_res = $mysql->sqlordie($select_project);
			$project_row = $project_res->fetch_assoc();
	  
			$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
			$company_res = $mysql->sqlordie($select_company);
			$company_row = $company_res->fetch_assoc();
	  
			$wo_status = "SELECT * FROM `lnk_workorder_status_types` WHERE `id`='" .$woStatus ."'";
			$wo_status_res = $mysql->sqlordie($wo_status);
			$wo_status_row = $wo_status_res->fetch_assoc();			
			
			//$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";
			$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=UTF-8";
			$requestor_qry = "SELECT * FROM `users` WHERE `id`='" .$requestedId ."'";
			$requestor_user_res = $mysql->sqlordie($requestor_qry);
			$requestor_user_row = $requestor_user_res->fetch_assoc();
			
			$site_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SITE_NAME' and cu.field_key = 'SITE_NAME' and cu.workorder_id = '" .$wo_id ."'";
			$site_name_res = $mysql->sqlordie($site_name_qry);
			$site_name_row = $site_name_res->fetch_assoc();
			/*$file_list = "";
			$select_file = "SELECT * FROM `workorder_files` WHERE workorder_id='" . $wo_id . "' order by upload_date desc";
			$file_res = $mysql->query($select_file);
			if($file_res->num_rows > 0) {
				$file_list = "<u><b>Attachment:</b></u><br>";
				$fileCount = 1;
				while($file_row = $file_res->fetch_assoc()){
					$file_list .= "" . $fileCount . ". <a href='".BASE_URL . "/files/" . $file_row['directory'] . "/" .$file_row['file_name']."'>" . $file_row['file_name'] . "</a><br>";
					$fileCount += 1;
				}
			}*/
			$woStatusText = $wo_status_row['name'];
			$description=($wo_row['body']);
            $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES,'UTF-8'));
			foreach($users_email as $user => $val){
				$select_email_addr = "SELECT email FROM `users` WHERE `id`='" . $user ."' LIMIT 1";
				$email_addr_res = $mysql->sqlordie($select_email_addr);
				$email_addr_row = $email_addr_res->fetch_assoc();
				$to = $email_addr_row['email'];
				$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$wo_id."'>".$wo_id."</a>";
	  
				$msg =  "<b>Requestor: </b>" . $requestor_user_row['first_name'].' '. $requestor_user_row['last_name']. "<br><br>";
				$msg .="<b>Company: </b>" . $company_row['name'] . "<br><br>";
				//$msg .="<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br><br>";
				$msg .="<b>Site: </b>" .$site_name_row['field_name'] ."<br><br>";
				if($user == $woAssignedTo)
				{
					$msg .="This email is to notify you that  <b>WO [" . $link . "] </b> will go overdue in 1 hour. Please review the request and take appropriate action.<br><br>";
				}
			   $msg .="<b>Request Type: </b>" .$request_type_arr[$req_type_row['field_name']] ."<br>";
			   //code for lh 18306
			   $severity_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SEVERITY' and cu.field_key = 'SEVERITY' and cu.workorder_id = '" .$wo_id ."'";
			   $severity_name_res = $mysql->sqlordie($severity_name_qry);
			   $severity_name_row = $severity_name_res->fetch_assoc();
				
				if($request_type_arr[$req_type_row['field_name']]=='Problem')
				{
				
				$msg .="<b>Severity: </b>" .$severity_name_row['field_name']  ."<br>";
				}

				//End code
                $msg .="<hr><b>Description:</b> " . $desc_string ."<br><br>";
				//$msg .=$file_list;
				$subject = "WO ".$wo_id.": Going Overdue in 1 hour - ".$req_type_row['field_name']." - " . $woTitle ;
				if(!empty($to)){ 
					//echo $to."<br/>".$subject."<br/>".$msg."<br/>".$headers;
					lh_sendEmail($to, $subject, $msg, $headers); 		
				}
			}
		
	}
	
	function sendEmail_feedbackAlerts($mysql, $wo_row){
			$requestedId = $wo_row['requested_by'];
			$woAssignedTo = $wo_row['assigned_to'];
			$launch_date = $wo_row['launch_date'];
			//LH 20679 #remove special characters from title
			$woTitle = $wo_row['title'];
			$woStatus = $wo_row['status'];
			$woREQ_TYPE = $wo_row['type'];
			$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

		    $wo_id = $wo_row['id'];

			$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$wo_id' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
			$req_type_res = $mysql->sqlordie($select_req_type_qry);
			$req_type_row = $req_type_res->fetch_assoc();		

			/*$cclist = array();	  	

			if("" != trim($wo_row['cclist'])){
				$cclist = explode(",", $wo_row['cclist']);
			}

			for($v = 0; $v < sizeof($cclist); $v++) {
				if($cclist[$v] != ''){
					$users_email[$cclist[$v]] = true;
				}
			}

			$users_email[$requestedId] = true;*/
			$users_email[$woAssignedTo] = true;
	  
			$user_keys = array_keys($users_email);
			$request_type_arr = array(	"Submit a Request" => "Request",
										"Report a Problem" => "Problem",
										"Report an Outage" => "Outage");
			$select_project = "SELECT * FROM `projects` WHERE `id`='" .$wo_row['project_id'] ."'";
			$project_res = $mysql->sqlordie($select_project);
			$project_row = $project_res->fetch_assoc();
	  
			$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
			$company_res = $mysql->sqlordie($select_company);
			$company_row = $company_res->fetch_assoc();
	  
			$wo_status = "SELECT * FROM `lnk_workorder_status_types` WHERE `id`='" .$woStatus ."'";
			$wo_status_res = $mysql->sqlordie($wo_status);
			$wo_status_row = $wo_status_res->fetch_assoc();			
			//LH 20679 change the header type
			//$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";
			$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=UTF-8";
			$requestor_qry = "SELECT * FROM `users` WHERE `id`='" .$requestedId ."'";
			$requestor_user_res = $mysql->sqlordie($requestor_qry);
			$requestor_user_row = $requestor_user_res->fetch_assoc();
			
			$site_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SITE_NAME' and cu.field_key = 'SITE_NAME' and cu.workorder_id = '" .$wo_id ."'";
			$site_name_res = $mysql->sqlordie($site_name_qry);
			$site_name_row = $site_name_res->fetch_assoc();
			/*$file_list = "";
			$select_file = "SELECT * FROM `workorder_files` WHERE workorder_id='" . $wo_id . "' order by upload_date desc";
			$file_res = $mysql->query($select_file);
			if($file_res->num_rows > 0) {
				$file_list = "<u><b>Attachment:</b></u><br>";
				$fileCount = 1;
				while($file_row = $file_res->fetch_assoc()){
					$file_list .= "" . $fileCount . ". <a href='".BASE_URL . "/files/" . $file_row['directory'] . "/" .$file_row['file_name']."'>" . $file_row['file_name'] . "</a><br>";
					$fileCount += 1;
				}
			}*/
			$woStatusText = $wo_status_row['name'];
			$description=($wo_row['body']);
            $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES,'UTF-8'));
			foreach($users_email as $user => $val){
				$select_email_addr = "SELECT email FROM `users` WHERE `id`='" . $user ."' LIMIT 1";
				$email_addr_res = $mysql->sqlordie($select_email_addr);
				$email_addr_row = $email_addr_res->fetch_assoc();
				$to = $email_addr_row['email'];
				$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$wo_id."'>".$wo_id."</a>";
	  
				$msg =  "<b>Requestor: </b>" . $requestor_user_row['first_name'].' '. $requestor_user_row['last_name']. "<br><br>";
				$msg .="<b>Company: </b>" . $company_row['name'] . "<br><br>";
				//$msg .="<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br><br>";
				$msg .="<b>Site: </b>" .$site_name_row['field_name'] ."<br><br>";
				if($woStatus == 5){
					$feedbackType = "marked Needs More Info";
				}else{
					$feedbackType = "marked Feedback Provided";
				}
				if($user == $woAssignedTo)
				{
					$msg .="This email is to notify you that <b>WO [" . $link . "] </b> has been ".$feedbackType." for over 24 hours . Please review the request and take appropriate action.<br><br>";
				}
			   $msg .="<b>Request Type: </b>" .$request_type_arr[$req_type_row['field_name']] ."<br>";
			   //code for lh 18306
			   $severity_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SEVERITY' and cu.field_key = 'SEVERITY' and cu.workorder_id = '" .$wo_id ."'";
			   $severity_name_res = $mysql->sqlordie($severity_name_qry);
			   $severity_name_row = $severity_name_res->fetch_assoc();
				
				if($request_type_arr[$req_type_row['field_name']]=='Problem')
				{
				
				$msg .="<b>Severity: </b>" .$severity_name_row['field_name']  ."<br>";
				}

				//End code
                $msg .="<hr><b>Description:</b> " . $desc_string ."<br><br>";
				//$msg .=$file_list;
				//LH 20679 decode html entity in title
				$subject = "WO ".$wo_id." - ".$woStatusText." - ".$req_type_row['field_name']." - " . $woTitle  ;
				if(!empty($to)){ 
					//echo $to."<br/>".$subject."<br/>".$msg."<br/>".$headers;
					lh_sendEmail($to, $subject, $msg, $headers); 		
				}
			}
	}
	function lh_sendEmail($to, $subject, $msg, $headers){
		$msg = nl2br($msg);
		$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
		$headers .= "\r\n" .
    					"Reply-To: ".COMMENT_REPLY_TO_EMAIL. "\r\n";
		mail($to, $subject, $msg, $headers);
	}
?>
