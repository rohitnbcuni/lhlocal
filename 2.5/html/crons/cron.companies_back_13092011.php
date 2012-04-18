#!/usr/bin/php
<?PHP

	function getTime(){
		// Measure php execution time (for development only)
		$mtime = microtime();//print("\n time : " . $mtime);
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return $mtime;
	}
//print("\n\n\nStart of Companies");
//$starttime = getTime();

	//Production
	$rootPath = '/var/www/lighthouse-uxd/lighthouse';
	//dev
//	$rootPath = '/var/www/lighthouse-uxd/lhdev';

	define('AJAX_CALL', '0');
	include($rootPath . '/html/_inc/config.inc');
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
//print("\n\nRoot Path in Companies Cron : " . $rootPath . "\n");die();	

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
					"\nCron: companies " . 
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
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($session,CURLOPT_USERPWD,"resourceplanner@nbcuxd.com:r3s0urc3");

//		if(ereg("^(https)",$set_request_url)) curl_setopt($session,CURLOPT_SSL_VERIFYPEER,false);

		$response = curl_exec($session);
		curl_close($session);
		return $response;
	}

//$starttime_read = getTime();
//	$xml = bcXML("/companies.xml", "");
	$xml = readXml(BASECAMP_HOST.'/companies.xml');
	$xmlFix = explode("<?xml", $xml);
	
	$feed = simplexml_load_string("<?xml" .$xmlFix[1]);
//$endtime_read = getTime();
//$totaltime_read = ($endtime_read - $starttime_read);
//print("\ntime taken to read : " . $totaltime_read . "\n");

	foreach($feed->company as $comp) {
		$select_state = "SELECT * FROM `lnk_iso_state_codes` WHERE `iso_code`='" .$mysql->real_escape_string($comp->state) ."' LIMIT 1";
		$select_country = "SELECT * FROM `lnk_iso_country_codes` WHERE LOWER(`name`)=LOWER('" .$mysql->real_escape_string($comp->country) ."') LIMIT 1";
		$state_res = $mysql->query($select_state);
		$country_res = $mysql->query($select_country);
		if($state_res->num_rows == 1) {
			$state_row = $state_res->fetch_assoc();
		}
		if($country_res->num_rows == 1) {
			$country_row = $country_res->fetch_assoc();
		}
		
		if(empty($state_row)) {
			$state_row['id'] = '';
		}
		if(empty($country_row)) {
			$country_row['id'] = 233;
		}
		
		$cData['bc_id'] = $mysql->real_escape_string($comp->id);
		$cData['name'] = $mysql->real_escape_string($comp->name);
		$cData['street_addr1'] = $mysql->real_escape_string($comp->{'address-one'});
		$cData['street_addr2'] = $mysql->real_escape_string($comp->{'address-two'});
		$cData['city'] = $mysql->real_escape_string($comp->city);
		$cData['state_province'] = $mysql->real_escape_string($state_row['id']);
		$cData['postal_code'] = $mysql->real_escape_string($comp->zip);
		$cData['country'] = $mysql->real_escape_string($country_row['id']);
		$cData['client_of'] = $mysql->real_escape_string($comp->{'client-of'});
		$cData['web_address'] = $mysql->real_escape_string($comp->{'web-address'});
		$cData['phone'] = $mysql->real_escape_string($comp->{'phone-number-office'});
		$cData['fax'] = $mysql->real_escape_string($comp->{'phone-number-fax'});
		$cData['time_zone'] = $mysql->real_escape_string($comp->{'time-zone-id'});
		$cData['bc_uuid'] = $mysql->real_escape_string($comp->uuid);
		
		$query_check = "SELECT `id` FROM `companies` WHERE `bc_id`='" .$cData['bc_id'] ."'";

		$check_res = $mysql->query($query_check);
		if ($mysql->error) {
			writeLog($mysql, $query_check, $rootPath);
		}else{
			if($check_res->num_rows == 1) {
				$row = $check_res->fetch_assoc();
				$update_query = "UPDATE `companies` SET "
					."`name`='" .$cData['name'] ."', "
					."`street_addr1`='" .$cData['street_addr1'] ."', "
					."`street_addr2`='" .$cData['street_addr2'] ."', "
					."`city`='" .$cData['city'] ."', "
					."`state_province`='" .$cData['state_province'] ."', "
					."`postal_code`='" .$cData['postal_code'] ."', "
					."`country`='" .$cData['country'] ."', "
					."`client_of`='" .$cData['client_of'] ."', "
					."`web_address`='" .$cData['web_address'] ."', "
					."`phone`='" .$cData['phone'] ."', "
					."`fax`='" .$cData['fax'] ."', "
					."`time_zone`='" .$cData['time_zone'] ."', "
					."`bc_uuid`='" .$cData['bc_uuid'] ."' "
					."WHERE `id`='" .$row['id'] ."'";
					
				$mysql->query($update_query);
				if ($mysql->error) {
					writeLog($mysql, $update_query, $rootPath);
				}
			} else {
				if(!empty($cData['bc_id'])) {
					$insert_query = "INSERT INTO `companies` "
						."(`bc_id`,`name`,`street_addr1`,`street_addr2`,`city`,`state_province`,"
						."`postal_code`,`country`,`client_of`,`web_address`,`phone`,`fax`,`time_zone`,"
						."`bc_uuid`) "
						."VALUES "
						."('" .$cData['bc_id'] ."','" .$cData['name'] ."','" .$cData['street_addr1'] 
						."','" .$cData['street_addr2'] ."','" .$cData['city'] ."','" .$cData['state_province'] 
						."','" .$cData['postal_code'] ."','" .$cData['country'] ."','" .$cData['client_of'] 
						."','" .$cData['web_address'] ."','" .$cData['phone'] ."','" .$cData['fax'] 
						."','" .$cData['time_zone'] ."','" .$cData['bc_uuid'] ."')";
						
					$mysql->query($insert_query);
					if ($mysql->error) {
						writeLog($mysql, $insert_query, $rootPath);
					}
				}
			}
		}
	}

//$endtime = getTime();
//$totaltime = ($endtime - $starttime);
//echo "\n<!-- $totaltime -->";
//print("\nEnd of Companies");
$mysql->close();
?>
