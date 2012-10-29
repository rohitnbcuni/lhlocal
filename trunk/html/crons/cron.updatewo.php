#!php
<?PHP
	
	include "cron.config.php";
	//Production
	//$rootPath = '/var/www/lighthouse-uxd/lighthouse';
	//dev
//	$rootPath = '/var/www/lighthouse-uxd/lhdev';

	define('AJAX_CALL', '0');
	include($rootPath . '/html/_inc/config.inc');
	
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	
	$get_closed_stat = "SELECT * FROM `workorders` WHERE `status` = '1' AND `archived`='0' AND DATE_SUB(CURDATE(),INTERVAL 2 DAY) >= `closed_date`";
	$closed_stat_res = $mysql->sqlordie($get_closed_stat);
	
	if($closed_stat_res->num_rows > 0) {
		while($row = $closed_stat_res->fetch_assoc()) {
			$archive_query = "UPDATE `workorders` SET `archived`='1' WHERE `id`='" .$row['id'] ."'";
			$mysql->sqlordie($archive_query);
		}
	}

	//Archiving WO with completed status more than 5 days
	$get_completed_stat = "SELECT * FROM `workorders` WHERE `status` = '3' AND `archived`='0' AND DATE_SUB(CURDATE(),INTERVAL 2 DAY) >= `completed_date`";
	$completed_stat_res = $mysql->sqlordie($get_completed_stat);
	
	if($completed_stat_res->num_rows > 0) {
		while($row = $completed_stat_res->fetch_assoc()) {
			$archive_query = "UPDATE `workorders` SET `archived`='1',`status`='1',`closed_date`= NOW() WHERE `id`='" .$row['id'] ."'";
			$mysql->sqlordie($archive_query);
		}
	}
?>
