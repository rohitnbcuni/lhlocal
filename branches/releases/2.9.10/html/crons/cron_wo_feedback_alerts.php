<?PHP
//#!/usr/bin/php
	function getTime(){
		// Measure php execution time (for development only)
		$mtime = microtime();//print("\n time : " . $mtime);
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return $mtime;
	}
//	$starttime = getTime();
	include "cron.config.php";
//	Production
	//$rootPath = '/var/www/lighthouse-uxd/lighthouse';
//	dev
	//$rootPath = str_replace("html\crons", "", dirname(__FILE__));
    	//$rootPath = '/var/www/lighthouse-uxd/qa';

	define('AJAX_CALL', '0');
	include($rootPath . '/html/_inc/config.inc');
	// This file is for just sending the Email.
	include($rootPath . '/html/_ajaxphp/sendEmail.php');
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	if (!is_file($rootPath . "/html/crons/cron_wo_feedback_alerts.log"))
	{
		if (!is_dir($rootPath . "/html/crons/"))
		{
			mkdir($rootPath . "/html/crons/",0755);
		}
	}
	
	//check for status need info and feedback provided
	$curDate = time();
	$getWOQuery = "SELECT * FROM `workorders` WHERE `active` = '1'  AND archived ='0' AND status IN (5,10) ";
	$woArray = $mysql->query($getWOQuery);
	
	if ($mysql->error) {
		writeLog($mysql, $getWOQuery, $rootPath);
	}else{
		if($woArray->num_rows > 0) {
			while($woRow = $woArray->fetch_assoc()){
				$woId = $woRow['id'];
						
				$getWOAuditQuery = "SELECT log_date  FROM `workorder_audit` WHERE `workorder_id` = $woId ORDER BY id DESC LIMIT 0,1";
				$woAuArray = $mysql->query($getWOAuditQuery);
				if ($mysql->error) {
					writeLog($mysql, $getWOAuditQuery, $rootPath);
				}else{
					if($woAuArray->num_rows > 0) {
						while($woAuRow = $woAuArray->fetch_assoc()){
							//dateTimeDiff($woAuRow['log_date']);
							$logdate = strtotime($woAuRow['log_date']);
							$interval = new stdClass();

							$dateDiff = $curDate - $logdate;
							
							$interval->d = floor($dateDiff/(60*60*24));
						    	$interval->h = floor(($dateDiff-($interval->d*60*60*24))/(60*60));
							$interval->i = floor(($dateDiff-($interval->d*60*60*24)-($interval->h*60*60))/60);
							
							//condition 1 day 0 hour 0 to 59 in betwwen min
							if(($interval->d == 1) &&($interval->h  < 1) &&($interval->i >= 0) &&($interval->i < 60)){
							//echo "shobhit";
								sendEmail_feedbackAlerts($mysql, $woRow);
						
							}
							
							
							//									
						}
					}
						
				}	
			}
		}
	}


function writeLog($mysql, $sql='', $rootPath='')
{
	$a = fopen($rootPath . "/html/crons/cron_wo_feedback_alerts.log", "a");
	fwrite($a,  "\nError No: ". $mysql->errno . " - " . 
				"\nCron: companies " . 
				"\nDate: " . date("Y-m-d : H:i:s") . 
				"\nMySQL error: " . $mysql->error . 
				"\nQuery: " . $sql . "\n");
	fclose($a);
}
