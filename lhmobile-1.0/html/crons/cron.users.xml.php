<?php 
function getTime(){
		// Measure php execution time (for development only)
		$mtime = microtime();//print("\n time : " . $mtime);
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return $mtime;
	}
	include "cron.config.php";
	//Production
	//$rootPath = '/var/www/lighthouse-uxd/qa';
	//dev
	//$rootPath = str_replace("/html",'',$_SERVER['DOCUMENT_ROOT']);

	define('AJAX_CALL', '0');
	include($rootPath . '/html/_inc/config.inc');
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
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
		$a = fopen($rootPath . "/html/crons/cron_delete.log", "a");
		fwrite($a,  "\nError No: ". $mysql->errno . " - " . 
					"\nCron: users " . 
					"\nDate: " . date("Y-m-d : H:i:s") . 
					"\nMySQL error: " . $mysql->error . 
					"\nQuery: " . $sql . "\n");
		fclose($a);
	}
	function writeLogDeleteSuccess($uid, $bc_id,$cid, $cname, $rootPath=''){
		$a = fopen($rootPath . "/html/crons/cron_delete_user_lh.log", "a");
		fwrite($a,  "\nDelete User Basecamp: ". $bc_id .
					" User Info: ".$uid.
					" -Company Id " . $cid.
					" -Company Name " . $cname.
					" Cron: Delete users " . 
					"Date: " . date("Y-m-d : H:i:s") . 
					 "\n");
		fclose($a);
	}
	function writeLogDeleteError($sql='', $error='',$uid ='', $bc_id='', $cid='', $cname='', $rootPath=''){
		$a = fopen($rootPath . "/html/crons/cron_delete_user_lh.log", "a");
		fwrite($a,  "Error: ". $sql .
					"Error Message: ". $error .
					"Delete User Basecamp: ". $bc_id .
					" User Info: ".$uid.
					" -Company Id " . $cid.
					" -Company Name " . $cname.
					" Cron: Delete users " . 
					"Date: " . date("Y-m-d : H:i:s") . 
					 "\n");
		fclose($a);
	}

	function readXml($set_request_url){
		$session = curl_init();  
		$set_request_url; 
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

	$select_companies = "SELECT `id`,`bc_id`,name FROM `companies` where bc_id ='707029' ";
	$companies_res = $mysql->sqlordie($select_companies);
	
	while($comp_row = $companies_res->fetch_assoc()) {
		$baseCamp_user = array();
		$xml = bcXML("/contacts/people/".$comp_row['bc_id'], "");
		//BASECAMP_HOST."/contacts/people/".$comp_row['bc_id'];
		$xml = readXml(BASECAMP_HOST."/contacts/people/".$comp_row['bc_id']);
		print_r($xml);
		
		/*$xmlFix = explode("<?xml", $xml);
		if(isset($xmlFix[1])) {
			//$feed = simplexml_load_file('basecamp.user_new.xml');
			$feed = simplexml_load_string("<?xml" .$xmlFix[1]);
			foreach($feed->person as $user) {
				$baseCamp_user[] = $mysql->real_escape_string($user->id);	
			}
			if(count($baseCamp_user) > 0){
				$comp_row['id']."NAME::".$comp_row['name'];
				$lh_user_array = getCompanyUsers($comp_row['id']);
				if(count($lh_user_array) > 0){
					foreach($lh_user_array as $lh_key => $lh_val){
						if(!in_array($lh_val, $baseCamp_user)){
							//echo "Not In Basecamp".$lh_val."\t".$lh_key."\t".$comp_row['name']."\n";
							$cid = $comp_row['id'];
							try{
								$sql = "UPDATE users SET  active = '0' , deleted ='1' WHERE company ='$cid' AND bc_id ='$lh_val' ";
								$user_sql = $mysql->query($sql);
								if(!$user_sql){
									throw new Exception($sql);
								}else{
									writeLogDeleteSuccess($lh_key,$lh_val,$comp_row['bc_id'],$comp_row['name'],$rootPath);
								}
								
							}catch(Exception $e){
								writeLogDeleteError($sql, $e->getMessage(),$lh_key,$lh_val,$comp_row['bc_id'],$comp_row['name'],$rootPath);
							}
						}
					}
				}
			}
		}*/
	}
	
function getCompanyUsers($cid){
	global $mysql;
	$sql = "SELECT id, bc_id,user_name  FROM users WHERE company ='$cid' AND active = '1' AND deleted ='0'";
	$user_sql = $mysql->sqlordie($sql);
 	$user_array = array();
	while($user_sql_row = $user_sql->fetch_assoc()) {
		$user_array[$user_sql_row['id']."-".$user_sql_row['user_name']] = $user_sql_row['bc_id'];
	}
	return $user_array;
}	
	
$mysql->close();

?>
