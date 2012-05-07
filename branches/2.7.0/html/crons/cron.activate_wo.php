#!php
<?PHP

	function getTime(){
		// Measure php execution time (for development only)
		$mtime = microtime();//print("\n time : " . $mtime);
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return $mtime;
	}
//	$starttime = getTime();
	include("cron.config.php");
//	Production
	//$rootPath = '/var/www/lighthouse-uxd/lighthouse';
//	dev
//	$rootPath = '/var/www/lighthouse-uxd/lhdev';

	define('AJAX_CALL', '0');
	include($rootPath . '/html/_inc/config.inc');
	// This file is for just sending the Email.
	include($rootPath . '/html/_ajaxphp/sendEmail.php');
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	if (!is_file($rootPath . "/html/crons/cron_15.log"))
	{
		if (!is_dir($rootPath . "/html/crons/"))
		{
			mkdir($rootPath . "/html/crons/",0755);
		}
	}
		writeLog("TEST  "," CHANDRA ", $rootPath);
	// to activate the wo 
	$getDraftWOQuery = "SELECT * FROM `workorders` WHERE `active` = '0' AND `draft_date` < NOW()";
	$draftWOArray = array();
	$wo_array = array();
	$woStatus = '6'; // Status - New 
	$draftWOArray = $mysql->query($getDraftWOQuery);
	if ($mysql->error) {
		writeLog($mysql, $getProjectQuery, $rootPath);
	}else{
		if($draftWOArray->num_rows > 0) {
			while($draftRow = $draftWOArray->fetch_assoc()){
				$id = $draftRow['id'];
				$updateQuery = "UPDATE `workorders` SET `active`='1', `creation_date`=NOW() WHERE `id`='$id'";
				$mysql->query($updateQuery);
				if ($mysql->error) {
					writeLog($mysql, $getProjectQuery, $rootPath);
				}
				insertWorkorderAudit($mysql, $draftRow['id'], '1', $draftRow['requested_by'] , $draftRow['assigned_to'], $woStatus);
				sendEmail_newRequest($mysql, $draftRow);

			}
		} 
	}	


function insertWorkorderAudit($mysql,$wo_id, $audit_id, $log_user_id,$assign_user_id,$status)
{
	$insert_custom_feild = "INSERT INTO  `workorder_audit` (`workorder_id`, `audit_id`,`log_user_id`,`assign_user_id`,`status`,`log_date`)  values ('".$wo_id."','".$audit_id."','".$log_user_id."','".$assign_user_id."','".$status."',NOW())";
	@$mysql->query($insert_custom_feild);
}

function writeLog($mysql, $sql='', $rootPath='')
{
	$a = fopen($rootPath . "/html/crons/cron_15.log", "a");
	fwrite($a,  "\nError No: ". $mysql->errno . " - " . 
				"\nCron: companies " . 
				"\nDate: " . date("Y-m-d : H:i:s") . 
				"\nMySQL error: " . $mysql->error . 
				"\nQuery: " . $sql . "\n");
	fclose($a);
}
