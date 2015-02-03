#!php
<?PHP
	$curr_year = current_year;//DATE("Y");
	function getTime(){
		// Measure php execution time (for development only)
		$mtime = microtime();//print("\n time : " . $mtime);
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return $mtime;
	}
//print("\n\n\nStart of Projects");
//$starttime = getTime();
	include "cron.config.php";
	//Production
	//$rootPath = '/var/www/lighthouse-uxd/lighthouse';

	//dev
//	$rootPath = '/var/www/lighthouse-uxd/pradeep';

	define('AJAX_CALL', '0');
	include($rootPath . '/html/_inc/config.inc');
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
//print("\n\nRoot Path in Projects Cron : " . $rootPath . "\n");die();		

	if (!is_file($rootPath . "/html/crons/cron_15.log"))
	{
		if (!is_dir($rootPath . "/html/crons/"))
		{
			mkdir($rootPath . "/html/crons/",0755);
		}
	}
	
//	include('/var/www/lighthouse_v2/html/_inc/config.inc');
	
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
					"\nCron: projects " . 
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

//$starttime_read = getTime();
//	$xml = bcXML("/projects.xml", "");
	$xml = readXml(BASECAMP_HOST.'/projects.xml');
	$xmlFix = explode("<?xml", $xml);
	$companyArray = array();
	$curr_year = current_year;//DATE("Y");
	$feed = simplexml_load_string("<?xml" .$xmlFix[1]);
//$endtime_read = getTime();
//$totaltime_read = ($endtime_read - $starttime_read);
//print("\ntime taken to read : " . $totaltime_read . "\n");
	foreach($feed->project as $project) {
		$bc_id = $mysql->real_escape_string($project->id);
		$name = $mysql->real_escape_string($project->name);
		$name_split = explode("-", $name);
		$comp_id = $mysql->real_escape_string($project->company->id);
		$select_proj = "SELECT * FROM `projects` WHERE `bc_id`='$bc_id' and `YEAR`='".$curr_year."'";
		$proj_res = $mysql->sqlordie($select_proj);
		if ($mysql->error) {
			writeLog($mysql, $select_proj, $rootPath);
		}else{
			if($project->status == "active") {
				$archive = 0;
			} else {
				$archive = 1;
			}
			if(sizeof($name_split) >= 2) {
				//$project_code = $name_split[0];
				//$project_name = $name_split[1];
				list($project_code, $project_name) = explode('-', $name,2);
			} else {
				$project_code = "";
				$project_name = $name_split[0];
			}
			$project_code = @trim($project_code);
			$project_name = @trim($project_name);
			if(array_key_exists($comp_id, $companyArray)){
				$company = $companyArray[$comp_id];
			}else{
				$select_comp = "SELECT * FROM `companies` WHERE `bc_id`='$comp_id'";
				$comp_res = $mysql->sqlordie($select_comp);
				
				if($comp_res->num_rows == 1) {
					$comp_row = $comp_res->fetch_assoc();
					$company = $comp_row['id'];
				} else {
					$company = "";
				}
				$companyArray[$comp_id] = $company;
			}

			if($proj_res->num_rows == 1) {
				$proj_row = $proj_res->fetch_assoc();
				$update_proj = "UPDATE `projects` SET `project_code`='$project_code',`project_name`='$project_name',"
				."`company`='$company' WHERE `id`='" .$proj_row['id'] ."'";
				$mysql->sqlordie($update_proj);
				if ($mysql->error) {
					writeLog($mysql, $update_proj, $rootPath);
				}
			} else if($proj_res->num_rows == 0) {
				if(!empty($bc_id)) {
					$insert_proj = "INSERT INTO `projects` "
					."(`project_code`,`project_name`,`company`,`archived`,`bc_id`, `project_status`,`YEAR`) "
					."VALUES "
					."('$project_code','$project_name','$company','$archive','$bc_id', '1','$curr_year')";
					$mysql->sqlordie($insert_proj);
					if ($mysql->error) {
						writeLog($mysql, $insert_proj, $rootPath);
					}else{
						$new_id = $mysql->insert_id;
						$statusInsertSql = 'INSERT INTO `project_status` (`project_id`, `status_id`, `created_user`, `created_date`) VALUES ("' . $new_id . '", "1", "83", NOW())';
						$mysql->sqlordie($statusInsertSql);
						if ($mysql->error) {
							writeLog($mysql, $statusInsertSql, $rootPath);
						}
						
						$unAssignedPhaseInsertSql = 'INSERT INTO `project_phase_finance` (`project_id`, `phase`, `hours`, `rate`,`creation_date`) VALUES ("'.$new_id.'", "'.UNASSIGNED_PHASE.'", "0", "'.UNASSIGNED_PHASE_RATE.'", NOW())';
						$mysql->sqlordie($unAssignedPhaseInsertSql);
						if ($mysql->error) {
							writeLog($mysql, $unAssignedPhaseInsertSql, $rootPath);
						}
					}
				}
			} else {
				//echo "more than one entry: " .$bc_id ."<br />";
			}
		}
	}
//// Measure php execution time (for development only)
//$endtime = getTime();
//$totaltime = ($endtime - $starttime);
//echo "\n<!-- $totaltime -->";
//print("\nEnd of Projects");
//$mysql->close();
?>
