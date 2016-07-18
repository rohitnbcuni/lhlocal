<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$pattern = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";	
	if(isset($_SESSION['user_id'])) {		
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		global $mysql;
		$user = $_SESSION['lh_username'];
	    $password = $_SESSION['lh_password'];
		
		$woId = $mysql->real_escape_string($_GET['woId']);

		$cc = $mysql->real_escape_string($_GET['cc']);
		$addCC = $mysql->real_escape_string($_GET['addCC']);
		$ccArray = array();
		$list = explode(",", $cc);
		if(!empty($woId))
		{			
			$wo_query = "SELECT * FROM `workorders` WHERE `id`='$woId' LIMIT 1";
			$wo_result = $mysql->sqlordie($wo_query);
			$wo_row = $wo_result->fetch_assoc();
			
			$getProjectQuery = "SELECT * FROM `projects` WHERE `id`='" .$wo_row['project_id'] ."' LIMIT 1";
			$projResult = $mysql->sqlordie($getProjectQuery);
			$projRow = $projResult->fetch_assoc();
			

			
			/*function bcXML($file, $body) {
				//echo "Ses Vars: (" .$this->_session->lh_username .")<br />";
				//echo "Ses Vars: (" .$this->_session->lh_password .")<br />";
				
				$out  = "GET $file HTTP/1.1\r\n";
				$out .= "Host: ".BASECAMP_HOST_URL."t\r\n";
				$out .= "Connection: Close\r\n";
				$out .= "Accept: application/xml\n";
				$out .= "Content-Type: application/xml\r\n";
				$out .= "Authorization: Basic ".base64_encode("resourceplanner@nbcuxd.com".":"."r3s0urc3")."\r\n";
				$out .= "\r\n";
				
				$out .= $body;
				
				//Open port to basecamp
				if (!$conex = @fsockopen("ssl://".BASECAMP_HOST_URL."", 443, $errno, $errstr, 10)) {
					return 0;
				}
				
				//Gather data from basecamp connection
				fwrite($conex, $out);
				$data = '';
				
				
				while (!feof($conex)) {
					$data .= fgets($conex, 512);
				}
				fclose($conex);
				
				return $data;
			}
			
			function readXml($set_request_url){
				$session = curl_init();   
				curl_setopt($session, CURLOPT_URL, $set_request_url); // set url to post to 
				curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	//			curl_setopt($session, CURLOPT_POST, 1); 
	//			curl_setopt($session, CURLOPT_POSTFIELDS, $xml); 
				curl_setopt($session, CURLOPT_HEADER, true);
				curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
				curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($session,CURLOPT_USERPWD,"resourceplanner@nbcuxd.com:r3s0urc3");

	//			if(ereg("^(https)",$set_request_url)) curl_setopt($session,CURLOPT_SSL_VERIFYPEER,false);

				$response = curl_exec($session);
				curl_close($session);
				return $response;
			}*/
			
			/*$xml = readXml(BASECAMP_HOST."/projects/".$projRow['bc_id']."/categories.xml");
	//		$xml = bcXML("/projects/".$projRow['bc_id']."/categories.xml", "");


			$xmlFix = explode("<?xml", $xml);
			
			if(!empty($xmlFix[1]))
			{
								
				$feed = @simplexml_load_string("<?xml" .@$xmlFix[1]);
				$catNum = 0;
				foreach($feed->category as $cat) {
					if($cat->name == "Assets") {
						$catNum = $cat->id;
						break;
					}
				}
			}*/
			
			
			//print_r($list);
			for($i = 0; $i < sizeof($list); $i++) {
				if(!empty($list[$i]) && !isset($ccArray[$list[$i]])) {
					if($list[$i] != @$_GET['remove'])
					$ccArray[$list[$i]]=true;
				}
			}
			
			$listKeys = array_keys($ccArray);
			$arrayData = "";
			
			for($z = 0; $z < sizeof($listKeys); $z++) {
				$arrayData .= $listKeys[$z] .",";
			}
			if(ISSET($_GET['addCC'])){
			$addCCNew =  $addCC.",";
			
			$update_cc = "UPDATE `workorders` SET `cclist`= CONCAT(cclist,'$addCCNew') WHERE `id`='$woId'";
			@$mysql->sqlordie($update_cc);
			}
			if(ISSET($_GET['remove'])){
			$removeCCNew =  $_GET['remove'].",";
			
			$update_cc = "UPDATE `workorders` SET `cclist`= REPLACE(`cclist`,'$removeCCNew','') WHERE `id`='$woId'";
			@$mysql->sqlordie($update_cc);
			
			
			
			}
			
			//$update_cc = "UPDATE `workorders` SET `cclist`='$arrayData' WHERE `id`='$woId'";
			//@$mysql->sqlordie($update_cc);
				
				

				/*if(!empty($catNum))
				{
					$xml = '<request>
					<post>
						<category-id>' .$catNum .'</category-id>
						<title>' .$wo_row['title'] .'</title>
						<body>' .$wo_row['body'] .'</body>
						<private>0</private>
					</post>';
					for($z = 0; $z < sizeof($listKeys); $z++) {
						$select_user_bc = "SELECT * FROM `users` WHERE `id`='" .$listKeys[$z] ."' LIMIT 1";
						$user_bc_res = $mysql->sqlordie($select_user_bc);
						$user_bc_row = $user_bc_res->fetch_assoc();
						
						if(!empty($user_bc_row['bc_id'])){
							$xml .= '<notify>' .$user_bc_row['bc_id'] .'</notify>';
						}
					}
					$getRB_AT_users = "SELECT * FROM `users` WHERE `id`='" .$wo_row['assigned_to'] . "' OR `id`='" .$wo_row['requested_by'] . "'";
					$RB_AT_res = $mysql->sqlordie($getRB_AT_users);
					if($RB_AT_res->num_rows > 0) {
						while($rb_at_row = $RB_AT_res->fetch_assoc()) {
							$xml .= '<notify>' .$rb_at_row['bc_id'] .'</notify>';
						}
					}
					
					$xml .= '
						<notify>2680172</notify>
					</request>';
					//echo $xml;
					$set_request_url =BASECAMP_HOST.'/'.'posts/'.$wo_row['bcid'].'.xml';
					
					$session = curl_init();
					curl_setopt($session, CURLOPT_URL, $set_request_url); // set url to post to 
					curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
					//curl_setopt($session, CURLOPT_POST, 1);
					curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'PUT');
					curl_setopt($session, CURLOPT_POSTFIELDS, $xml); 
					curl_setopt($session, CURLOPT_HEADER, true);
					curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
					curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($session,CURLOPT_USERPWD,$user . ":" . $password);

					if(ereg("^(https)",$set_request_url)) curl_setopt($session,CURLOPT_SSL_VERIFYPEER,false);

					$response = curl_exec($session);
					//echo $response;
					$newNumPart1 = explode("/posts/", $response);
					$newNumPart2 = explode(".xml", @$newNumPart1[1]);
					
					$comment_id = $newNumPart2[0];
					curl_close($session);
				}*/
			$select_cc = "SELECT * FROM `workorders` WHERE `id`='$woId' LIMIT 1";
			$result = @$mysql->sqlordie($select_cc);
			$row = @$result->fetch_assoc();

			$select_project = "SELECT * FROM `projects` WHERE `id`='" .$row['project_id'] ."'";
			$project_res = $mysql->sqlordie($select_project);
			$project_row = $project_res->fetch_assoc();

			$select_company = "SELECT * FROM `companies` WHERE `id`='" . $project_row['company'] . "'";
			$company_res = $mysql->sqlordie($select_company);
			$company_row = $company_res->fetch_assoc();

			$wo_status = "SELECT * FROM `lnk_workorder_status_types` WHERE `id`='" .$row['status'] ."'";
			$wo_status_res = $mysql->sqlordie($wo_status);
			$wo_status_row = $wo_status_res->fetch_assoc();

			$select_req_type_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$woId' and a.field_key='REQ_TYPE' and a.field_id = b.field_id";
			$req_type_res = $mysql->sqlordie($select_req_type_qry);
			$req_type_row = $req_type_res->fetch_assoc();

			$requestedId = $row['requested_by'];

			$site_name_qry = "SELECT a.field_key,a.field_id,b.field_name,a.field_key FROM `workorder_custom_fields` a,`lnk_custom_fields_value` b WHERE `workorder_id`='$woId' and a.field_key='SITE_NAME' and a.field_id = b.field_id";
			$site_name_res = $mysql->sqlordie($site_name_qry);
			$site_name_row = $site_name_res->fetch_assoc();

			$requestor_qry = "SELECT * FROM `users` WHERE `id`='" .$requestedId ."'";
			$requestor_user_res = $mysql->sqlordie($requestor_qry);
			$requestor_user_row = $requestor_user_res->fetch_assoc();

			if($result->num_rows > 0) {		
				$new_list = explode(",", $row['cclist']);
				$list = "";
				//$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=iso-8859-1";	
				$headers = "From: ".WO_EMAIL_FROM."\nMIME-Version: 1.0\nContent-type: text/html; charset=UTF-8";
				
				$request_type_arr = array("Submit a Request" => "Request", "Report a Problem" => "Problem","Report an Outage" => "Outage");
                $description=($wo_row['body']);
				//LH 20679 #remove special characters from desc
                $desc_string = preg_replace($pattern, "<a href=\"\\0\"?phpMyAdmin=uMSzDU7o3aUDmXyBqXX6JVQaIO3&phpMyAdmin=8d6c4cc61727t4d965138r21cd rel=\"nofollow\" target='_blank'>\\0</a>",htmlentities($description,ENT_NOQUOTES,'UTF-8'));
				for($x = 0; $x < sizeof($new_list); $x++) {
					if(!empty($new_list[$x])) {
						$select_cc_user = "SELECT * FROM `users` WHERE `id`= ? LIMIT 1";
						$cc_user_result = @$mysql->sqlprepare($select_cc_user,array($new_list[$x]) );
						$cc_user_row = @$cc_user_result->fetch_assoc();
						
						$list .= "<li>"
							."<div class=\"cclist_name\">" .ucfirst($cc_user_row['first_name']) ." " .ucfirst($cc_user_row['last_name']) ."</div>"
							."<button class=\"status cclist_remover\" onClick=\"removeCcUser(" .$new_list[$x] ."); return false;\"><span>remove</span></button>"
						."</li>";

						if($new_list[$x] == $addCC )
						{
							$to = $cc_user_row['email'];

							$link = "<a href='".BASE_URL ."/workorders/index/edit/?wo_id=" .$woId."'>".$woId."</a>";
							$msg =  "<b>Requestor: </b>" . $requestor_user_row['first_name'].' '. $requestor_user_row['last_name']. "<br><br>";
							$msg .="<b>Company: </b>" . $company_row['name'] . "<br><br>";
							$msg .="<b>Project: </b>" .$project_row['project_code'] ." - " .$project_row['project_name'] ."<br><br>";
							$msg .="<b>Site: </b>" .$site_name_row['field_name'] ."<br><br>";				
							$msg .="<b>WO [" . $link . "] </b> You have been added to the CC list of this ticket.<br><br>";
							$msg .="<b>Request Type: </b>" .$request_type_arr[$req_type_row['field_name']] ."<br>";



//Code for LH 18306
							
							 $severity_name_qry = "select field_name from lnk_custom_fields_value ln,workorder_custom_fields cu where ln.field_id = cu.field_id and ln.field_key = 'SEVERITY' and cu.field_key = 'SEVERITY' and cu.workorder_id = '$woId'";
				
			                 $severity_name_res = $mysql->sqlordie($severity_name_qry);
			                 $severity_name_row = $severity_name_res->fetch_assoc();
		
				             if($request_type_arr[$req_type_row['field_name']]=='Problem')
				            {
				
				             $msg .="<br><b>Severity: </b>" .$severity_name_row['field_name']  ."<br>";
				            }
	                        //End Code




							$msg .="<hr><b>Description:</b> " . $desc_string ."<br><br>";
                            $subject = "WO ".$woId.": You Have Been CC'd on Ticket - ".$req_type_row['field_name']." - " . html_entity_decode($row['title'],ENT_NOQUOTES,'UTF-8') . "";
							if(!empty($to)){ 
								sendEmail($to, $subject, $msg, $headers);
							}
						}
					}
				}				
				echo $list;				
			}
		}
		else
		{

			for($i = 0; $i < sizeof($list); $i++) {
				if(!empty($list[$i]) && !isset($ccArray[$list[$i]])) {
					if($list[$i] != @$_GET['remove'])
					$ccArray[$list[$i]]=true;
				}
			}
			
			$listKeys = array_keys($ccArray);
			$arrayData = "";
			$list = "";
			for($z = 0; $z < sizeof($listKeys); $z++) {
				$arrayData .= $listKeys[$z] .",";
			
				if(!empty($listKeys[$z])) {
					$select_cc_user = "SELECT * FROM `users` WHERE `id`='" .$listKeys[$z] ."' LIMIT 1";
					$cc_user_result = @$mysql->sqlordie($select_cc_user);
					$cc_user_row = @$cc_user_result->fetch_assoc();
					
					$list .= "<li>"
						."<div class=\"cclist_name\">" .ucfirst($cc_user_row['first_name']) ." " .ucfirst($cc_user_row['last_name']) ."</div>"
						."<button class=\"status cclist_remover\" onClick=\"removeCcUser(" .$listKeys[$z] ."); return false;\"><span>remove</span></button>"
					."</li>";

				}
			}
			echo $list;

		}
		//echo $row['cclist'];
		
	}

	function sendEmail($to, $subject, $msg, $headers){
				$msg = nl2br($msg);
				$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
				$headers .= "\r\n" .
    					"Reply-To: ".COMMENT_REPLY_TO_EMAIL. "\r\n";
				mail($to, $subject, $msg, $headers);
	}
?>
