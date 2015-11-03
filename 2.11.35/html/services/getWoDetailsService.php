<?php

/* Gets Workorder Details Service
 * @author Abhilash.Kornalliose@nbcuni.com
 * @copyright NBC.com 
 * @category Service
 * @version 1.0
 * @link JAVA Service
 */

class getWoDetailsService{

	private static $instance;
    private $count = 0;

	/**
	 * DB connection
	 * singleton design pattern 
	 * @return DB object
	 * 
	 */
	public static function singleton()
    {
        if (!isset(self::$instance)) {
            //echo 'Creating new instance.';
			global $mysql;
            //$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
            self::$instance = $mysql;
        }
        return self::$instance;
    }
    /**
     * 
     * Return the WorkOrder Details by WorkOrder Id
     * @param string $workorderId
     * @return workOrder data
     */
    public function getWoDetails($workorderId){
		$responseFields = array();
    	$mysql = self::singleton();
		$requestTypeArray = array(1=>'OUTAGE', 2=>'PROBLEM', 3=>'REQUEST');
		$severityTypeArray = array(5=>'PROB_S1', 6=>'PROB_S2', 7=>'PROB_S3');

		$responseFields["wo_id"] = -1;
		if (is_numeric($workorderId)) {
			$woQuery = "SELECT wo.`id` woId, wo.`title` wosubject, usr.`email` worequestor, wcfType.`field_id` woType"
					."  FROM `workorders` wo, `users` usr, `workorder_custom_fields` wcfType" 
					." WHERE wo.`requested_by`= usr.`id`"
					."   AND wo.`id`= wcfType.`workorder_id`"
					."   AND wcfType.`field_key`='REQ_TYPE'"
					."   AND wo.`id`='" .$workorderId ."'";

			$woCheck = $mysql->query($woQuery);

			if($woCheck->num_rows >0){
				$woDetails = $woCheck->fetch_assoc();
				$responseFields["wo_id"]=$woDetails["woId"];
				$responseFields["req_email"]=$woDetails["worequestor"];
				$responseFields["subject"]=$woDetails["wosubject"];
				
				if($requestTypeArray[$woDetails["woType"]] == 'PROBLEM'){
					$probTypeQuery = "SELECT `field_id` woSeverity FROM `workorder_custom_fields` WHERE `workorder_id` ='" .$workorderId."' AND `field_key` = 'SEVERITY'";
					$probTypeCheck = $mysql->query($probTypeQuery);
					if($probTypeCheck->num_rows > 0){
						$probTypeResults = $woCheck->fetch_assoc();
						$responseFields["type"]=$severityTypeArray[$woDetails["woSeverity"]];
					}
				}
				else{
					$responseFields["type"]=$requestTypeArray[$woDetails["woType"]];
				}
			}
		}
    	return $responseFields;
    }
}


if(ISSET($_POST['lh_submit'])){
	require_once('../_inc/config.inc');
	
	define("OUTAGE","OUTAGE");
	define("REQUEST","REQUEST");
	define("PROBLEM","PROBLEM");
	define("SALT",'lighthouse');
	
	$requestorEmail = $_POST['lh_email'];

	if(ISSET($_POST['lh_utc_time']) && (!empty($_POST['lh_utc_time']))){
		//convert milli second in to second
		$apiTime = $_POST['lh_utc_time'];
		$javaTime = trim($_POST['lh_utc_time'])/1000;
	}else{
		die("UTC TIME NOT DEFINED");
	}
	if(ISSET($_POST['source_host_name']) && (!empty($_POST['source_host_name']))){
		$hostname = $_POST['source_host_name'];
	}else{
		die("HOST NAME NOT FOUND");
	}

	$tokenInput = $requestorEmail.'|'.$hostname.'|'.$apiTime.'|'.SALT;
	$cs_token = md5($tokenInput);
	if(ISSET($_POST['lh_token']) && (!empty($_POST['lh_token']))){
		
		$lh_token = $_POST['lh_token'];
	}else{
		die("TOKEN NOT FOUND");
	}

	$phpTime = time();
	$timeDiffernce = round(abs($phpTime-$javaTime)/60,2);
	
	if( $cs_token == $lh_token){
		if($timeDiffernce <= 15){
			//$woService = new getWoDetailsService();
			//$result = $woService->getWoDetails();
			$workorderId  = $_POST['workorder_id'];
			$woService = new getWoDetailsService();
			$result = $woService->getWoDetails($workorderId);

			echo json_encode($result);
		}else{
			die("TIME LIMIT EXCEED");
		}
	}else{
		die("TOKEN MISS-MATCH");
	}
}