<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	if((isset($_SESSION['user_id'])) &&(ISSET($_POST))) {
		
		
		//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		global $mysql;
		$user = $_SESSION['lh_username'];
	    $password = $_SESSION['lh_password'];
		
		$getProjectQuery = "SELECT * FROM `projects` WHERE `id`= ? LIMIT 1";
		$projResult = $mysql->sqlprepare($getProjectQuery,array($mysql->real_escape_string($_POST['projectId'])));
		$projRow = $projResult->fetch_assoc();	
		
		$wo_query = "SELECT * FROM `workorders` WHERE `id`= ?";
		$wo_res = $mysql->sqlprepare($wo_query, array($mysql->real_escape_string($_POST['woId'])));
		$wo_row = $wo_res->fetch_assoc();
		
		$re_query = "SELECT * FROM `users` WHERE `id`= ?";
		$re_res = $mysql->sqlprepare($re_query, array($mysql->real_escape_string($_POST['requestedId'])));
		$re_row = $re_res->fetch_assoc();
		
		$select_priority = "SELECT * FROM `lnk_workorder_priority_types` WHERE `id`='" .$mysql->real_escape_string($_POST['priorityId']) ."'";
		$pri_res = $mysql->sqlprepare($select_priority);
		$pri_row = $pri_res->fetch_assoc();
		
		function bcXML($file, $body) {		
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
		
		$xml = bcXML("/projects/".$projRow['bc_id']."/categories.xml", "");
		$xmlFix = explode("<?xml", $xml);
		
		
		$feed = simplexml_load_string("<?xml" .@$xmlFix[1]);
		$catNum = 0;
		foreach($feed->category as $cat) {
			if($cat->name == "Workorder" && $cat->type == "PostCategory") {
				$catNum = $cat->id;
				break;
			}
		}
		
		$xml = " 
		<request>
		<post>
		<category-id type=\"integer\" nil=\"true\">$catNum</category-id>
		<title nil=\"true\">WO: " .$mysql->real_escape_string($_POST['woTitle']) ." [" .$pri_row['name'] ."-" .$pri_row['time'] ."] " ."</title>
		<body nil=\"true\">"
			."Example URL: " . $mysql->real_escape_string($_POST['woExampleURL'])  ."\n"
			."Desc: " .$mysql->real_escape_string($_POST['woDesc']) ."\n"
			."StartDate: " .$mysql->real_escape_string($_POST['woStartDate']) ."\n"
			."Estimated Completion Date: " .$mysql->real_escape_string($_POST['woEstDate']) ."\n";
			
			$getFilesQuery = "SELECT * FROM `workorder_files` WHERE `workorder_id`='" .$mysql->real_escape_string($_POST['woId']) ."'";
			$fileResult = $mysql->sqlordie($getFilesQuery);
			//echo $mysql->error;
			if($fileResult->num_rows > 0) {
				$xml .= "Files: ";
				while($fileRow = $fileResult->fetch_assoc()) {
					$xml.= BASE_URL . '/files/' .$_POST['woId'] .'/' .$fileRow['file_name'] ."\n";
				}
			}
		//echo "bc id: " .$bc_id_row['bcid'] .":" .$projRow['bc_id'] ."*********";	
		$xml .= "</body>
		<extended-body nil=\"true\"></extended-body>
		<milestone-id type=\"integer\">0</milestone-id>
		<private>0</private>
		</post>
		<notify>2680172</notify>
		<notify>" .$re_row['bc_id'] ."</notify>
		</request>";
		$bc_id_query = "SELECT  `bcid` FROM `workorders` WHERE `id`='" .$mysql->real_escape_string($_POST['woId']) ."' LIMIT 1";
		$bc_id_result = $mysql->sqlordie($bc_id_query);
		$bc_id_row = $bc_id_result->fetch_assoc();
		
		if(!empty($bc_id_row['bcid'])) {
			$set_request_url =BASECAMP_HOST.'/'.'posts/'.$bc_id_row['bcid'].'.xml';
			
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
		} else {
			$set_request_url =BASECAMP_HOST.'/'.'projects/'.$projRow['bc_id'].'/posts.xml';
			
			$session = curl_init();   
			curl_setopt($session, CURLOPT_URL, $set_request_url); // set url to post to 
			curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($session, CURLOPT_POST, 1); 
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
			
			$getFilesQuery = "UPDATE `workorders` SET `bcid`='$comment_id' WHERE `id`='" .$mysql->real_escape_string($_POST['woId']) ."'";
			$mysql->sqlordie($getFilesQuery);
		}
		//echo $set_request_url ."\n";
		
		$select_bc = "SELECT * FROM `workorders` WHERE `id`='" .$mysql->real_escape_string($_POST['woId']) ."' LIMIT 1";
		$bc_res = $mysql->sqlordie($select_bc);
		$bc_row = $bc_res->fetch_assoc();
		
		$select_project_bc = "SELECT * FROM `projects` WHERE `id`='" .$bc_row['project_id'] ."' LIMIT 1";
		$project_bc_res = $mysql->sqlordie($select_project_bc);
		$project_bc_row = $project_bc_res->fetch_assoc();
		
		echo $project_bc_row['bc_id'] ."/posts/" .$bc_row['bcid'];
	}
?>