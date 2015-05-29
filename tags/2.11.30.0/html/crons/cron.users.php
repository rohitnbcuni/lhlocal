#!php
<?PHP
	function getTime(){
		// Measure php execution time (for development only)
		$mtime = microtime();//print("\n time : " . $mtime);
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return $mtime;
	}
//	print("<br>\n\n\nStart of Users");
//	$starttime = getTime();
	include "cron.config.php";


	define('AJAX_CALL', '0');
	include($rootPath . '/html/_inc/config.inc');
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	if (!is_file($rootPath . "/html/crons/cron_15.log"))
	{
		if (!is_dir($rootPath . "/html/crons/"))
		{
			mkdir($rootPath . "/html/crons/",0755);
		}
		
	}
	
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

	function writeLog($mysql, $sql='', $rootPath=''){
		$a = fopen($rootPath . "/html/crons/cron_15.log", "a");
		fwrite($a,  "\nError No: ". $mysql->errno . " - " . 
					"\nCron: users " . 
					"\nDate: " . date("Y-m-d : H:i:s") . 
					"\nMySQL error: " . $mysql->error . 
					"\nQuery: " . $sql . "\n");
		fclose($a);
	}

	function readXml($set_request_url){
		$session = curl_init();   
		curl_setopt($session, CURLOPT_URL, $set_request_url); // set url to post to 
		curl_setopt($session, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//		curl_setopt($session, CURLOPT_POST, 1); 
//		curl_setopt($session, CURLOPT_POSTFIELDS, $xml); 
		$useragent = "Lighthouse Application (ots-tools-support@nbcuni.com)";
		curl_setopt($session, CURLOPT_USERAGENT, $useragent);
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session,CURLOPT_USERPWD,"resourceplanner@nbcuxd.com:r3s0urc3");

//		if(ereg("^(https)",$set_request_url)) curl_setopt($session,CURLOPT_SSL_VERIFYPEER,false);

		$response = curl_exec($session);
		curl_close($session);
		return $response;
	}

	$select_companies = "SELECT `id`,`bc_id` FROM `companies`";
	$companies_res = $mysql->sqlordie($select_companies);
	
	while($comp_row = $companies_res->fetch_assoc()) {
//		$xml = bcXML("/contacts/people/".$comp_row['bc_id'], "");
		$xml = readXml(BASECAMP_HOST."/contacts/people/".$comp_row['bc_id']);
		$xmlFix = explode("<?xml", $xml);
		if(isset($xmlFix[1])) {
			$feed = simplexml_load_string("<?xml" .$xmlFix[1]);

			foreach($feed->person as $user) {
				if($user->deleted == "false") {
					$deleted = 0;
				} else {
					$deleted = 1;
				}
				$uData['bc_id'] = $mysql->real_escape_string($user->id);
				$uData['user_name'] = $mysql->real_escape_string($user->{'user-name'});
				$uData['email'] = $mysql->real_escape_string($user->{'email-address'});
				$uData['first_name'] = $mysql->real_escape_string($user->{'first-name'});
				$uData['last_name'] = $mysql->real_escape_string($user->{'last-name'});
				//$uData['address_1'] = $mysql->real_escape_string($user->);
				//$uData['address_2'] = $mysql->real_escape_string($user->);
				//$uData['city'] = $mysql->real_escape_string($user->);
				//$uData['state_province'] = $mysql->real_escape_string($user->);
				//$uData['postal_code'] = $mysql->real_escape_string($user->);
				$uData['phone_office'] = $mysql->real_escape_string($user->{'phone-number-office'});
				$uData['phone_office_ext'] = $mysql->real_escape_string($user->{'phone-number-office-ext'});
				$uData['phone_mobile'] = $mysql->real_escape_string($user->{'phone-number-mobile'});
				$uData['phone_home'] = $mysql->real_escape_string($user->{'phone-number-home'});
				$uData['phone_fax'] = $mysql->real_escape_string($user->{'phone-number-fax'});
				$uData['title'] = $mysql->real_escape_string($user->title);
				$uData['company'] = $mysql->real_escape_string($comp_row['id']);
				$uData['bc_uuid'] = $mysql->real_escape_string($user->uuid);
				$uData['im_handle'] = $mysql->real_escape_string($user->{'im-handle'});
				$uData['im_service'] = $mysql->real_escape_string($user->{'im-service'});
				$uData['deleted'] = $mysql->real_escape_string($user->$deleted);
				$avatar_img = $mysql->real_escape_string($user->{'avatar-url'});
				$pos ='1';
				if(!empty($avatar_img))
				{
					$pos = strripos($avatar_img, '/missing/');				
				}
				
				if($pos=== false) {
					$avatar_img = $avatar_img;
				}else{
					$avatar_img = '/_images/empty_mugshot.gif';
				}
				$avatar_img = str_replace("http:", "https:", $avatar_img);
				$update_user_query = "UPDATE `users` SET "
							."`user_name`='" .$uData['user_name'] ."', "
							."`email`='" .$uData['email'] ."', "
							."`first_name`='" .$uData['first_name'] ."', "
							."`last_name`='" .$uData['last_name'] ."', "
							."`phone_office`='" .$uData['phone_office'] ."', "
							."`phone_office_ext`='" .$uData['phone_office_ext'] ."', "
							."`phone_mobile`='" .$uData['phone_mobile'] ."', "
							."`phone_home`='" .$uData['phone_home'] ."', "
							."`phone_fax`='" .$uData['phone_fax'] ."', "
							."`company`='" .$comp_row['id'] ."', "
							."`title`='" .$uData['title'] ."', ";
				if(!empty($uData['company'])) {
					$update_user_query .= "`company`='" .$uData['company'] ."', ";
				}							
				$update_user_query .= "`user_img`='" .$avatar_img ."', ";			
							
				$update_user_query .= "`bc_uuid`='" .$uData['bc_uuid'] ."', "
							."`im_handle`='" .$uData['im_handle'] ."', "
							."`im_service`='" .$uData['im_service'] ."' "
							."WHERE `bc_id`='" . $uData['bc_id'] ."'";
				
				if(!empty($uData['user_name']) && !empty($uData['email']) )
				{	
					$mysql->sqlordie($update_user_query);
				}
				
				if ($mysql->error) {
					writeLog($mysql, $update_user_query, $rootPath);
				}else{
					$strResult = explode("  ", $mysql->info);
					$matched = explode(":", $strResult[0]);
					$updatedRows = trim($matched[1]);
					if($updatedRows == '0')
					{
						$insert_user_query = "INSERT INTO `users` "
							."(`bc_id`, `user_name`, `email`, `first_name`, `last_name`, `phone_office`, "
							."`phone_office_ext`, `phone_mobile`, `phone_home`, `phone_fax`, `title`,";
							if(!empty($uData['company'])) {
								$insert_user_query .= "`company`, ";
							}
						$insert_user_query .= "`bc_uuid`, `im_handle`, `im_service`,`role`,`user_img`,`user_access`) "
							."VALUES "
							."('" .$uData['bc_id'] ."', '" .$uData['user_name'] ."', '" .$uData['email'] ."', '" .$uData['first_name'] 
							."', '" .$uData['last_name'] ."', '" .$uData['phone_office'] ."', '" .$uData['phone_office_ext'] 
							."', '" .$uData['phone_mobile'] ."', '" .$uData['phone_home'] ."', '" .$uData['phone_fax'] 
							."', '" .$uData['title'] ."',";
							$user_access_bits = "01100000"; // Default Client Access
							if(!empty($uData['company'])) {
								$insert_user_query .= "'" .$uData['company'] ."', ";

								if($uData['company'] == '2' || $uData['company'] == '136' || $uData['company'] == '141')
								{
									$user_access_bits = "11110011"; // Client Access
								}
								else
								{
									$user_access_bits = "00100011"; // Client Access
								}
							}
						$insert_user_query .= "'" .$uData['bc_uuid'] ."', '" .$uData['im_handle'] 
							."', '" .$uData['im_service'] ."','".UNASSIGNED_PHASE."','".$avatar_img."','".$user_access_bits."')";
						$mysql->sqlordie($insert_user_query);
						if ($mysql->error) {
							writeLog($mysql, $insert_user_query, $rootPath);
						}
					}
//					print("<br>\n Number of record :" . trim($matched[1]) . "\n");
				}
			
			}
		}
	}
//$endtime = getTime();
//$totaltime = ($endtime - $starttime);
//echo "<br>\ntotaltime ::: $totaltime ";
//print("<br>\nEnd of Users");
//$mysql->close();
?>
