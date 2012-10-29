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
	//$rootPath = "E:/lighthouse/";
	//$rootPath = '/var/www/lighthouse-uxd/qa';
	//$rootPath = str_replace("html/crons", "", dirname(__FILE__));
    //$rootPath = '/var/www/lighthouse-uxd/lhdev';

	define('AJAX_CALL', '0');
	include($rootPath . '/html/_inc/config.inc');
	// This file is for just sending the Email.
	include($rootPath . '/html/_ajaxphp/sendEmail.php');
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	if (!is_file($rootPath . "/html/crons/cron_wo_alerts.log"))
	{
		if (!is_dir($rootPath . "/html/crons/"))
		{
			mkdir($rootPath . "/html/crons/",0755);
		}
	}
	//1 hr for befor alert;
	$timeConstant = 1; 
	$minConstant = 15; 
	
	$nestHrDate = date("Y-m-d H:i:s",mktime(date('H')+$timeConstant, date('i'), date('s'), date('m'), date('d'),date("Y")));
	$nestHrDateTime = date("Y-m-d H:i:s",mktime(date('H')+$timeConstant, date('i')+$minConstant, date('s'), date('m'), date('d'),date("Y")));
	$getWOQuery = "SELECT * FROM `workorders` WHERE `active` = '1'  AND archived ='0' AND status NOT IN (1,3) AND   launch_date BETWEEN '$nestHrDate' AND '$nestHrDateTime' LIMIT 0,1";
	$woArray = $mysql->sqlordie($getWOQuery);
	if ($mysql->error) {
		writeLog($mysql, $getWOQuery, $rootPath);
	}else{
		if($woArray->num_rows > 0) {
			while($woRow = $woArray->fetch_assoc()){
				//print_r($woRow);
				sendEmail_overDueAlerts($mysql, $woRow);
			}
		}else{
			echo "No record found";
		}
	}


function writeLog($mysql, $sql='', $rootPath='')
{
	$a = fopen($rootPath . "/html/crons/cron_wo_alerts.log", "a");
	fwrite($a,  "\nError No: ". $mysql->errno . " - " . 
				"\nCron: companies " . 
				"\nDate: " . date("Y-m-d : H:i:s") . 
				"\nMySQL error: " . $mysql->error . 
				"\nQuery: " . $sql . "\n");
	fclose($a);
}
