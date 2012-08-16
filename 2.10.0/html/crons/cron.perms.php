#!php
<?PHP
	function getTime(){
		// Measure php execution time (for development only)
		$mtime = microtime();//print("\n time : " . $mtime);
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return $mtime;
	}
//print("\n\n\nStart of Permissions");
//$starttime = getTime();
	include "cron.config.php";
	//Production
	//$rootPath = '/var/www/lighthouse-uxd/lighthouse';
	//dev
//	$rootPath = '/var/www/lighthouse-uxd/lhdev';

	define('AJAX_CALL', '0');
	include($rootPath . '/html/_inc/config.inc');
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
//print("\n\nRoot Path in Perms Cron : " . $rootPath . "\n");die();		

	if (!is_file($rootPath . "/html/crons/cron_15.log"))
	{
		if (!is_dir($rootPath . "/html/crons/"))
		{
			mkdir($rootPath . "/html/crons/",0755);
		}
		
	}
	function execute($user, $project, $mysql){
			$insert_perm = "INSERT INTO `user_project_permissions` (`user_id`,`project_id`) VALUES "
			."('" . $user ."','" . $project ."')";
			$mysql->query($insert_perm);
	}
	function writeLog($mysql, $sql='', $rootPath=''){
		$a = fopen($rootPath . "/html/crons/cron_15.log", "a");
		fwrite($a,  "\nError No: ". $mysql->errno . " - " . 
					"\nCron: Permissions " . 
					"\nDate: " . date("Y-m-d : H:i:s") . 
					"\nMySQL error: " . $mysql->error . 
					"\nQuery: " . $sql . "\n");
		fclose($a);
	}
//	include('/var/www/lighthouse_v2/html/_inc/config.inc');
	$getProjectQuery = "SELECT * FROM `projects` WHERE active='1' AND deleted='0' AND archived='0'";

	$projCOunt = 0;
	$projResult = $mysql->query($getProjectQuery);
	if ($mysql->error) {
			writeLog($mysql, $getProjectQuery, $rootPath);
	}else{
		if($projResult->num_rows > 0) {
			while($projRow = $projResult->fetch_assoc()) {
				$read = '';
				$usrCount = 0;
				$getUsers = "SELECT id FROM `users` WHERE id NOT IN (SELECT user_id FROM `user_project_permissions` WHERE project_id='" . $projRow['id'] . "')  AND `company`='" . $projRow['company'] . "'";
				$userResult = $mysql->query($getUsers);
				if ($mysql->error) {
						writeLog($mysql, $getUsers, $rootPath);
				}else{
					$projCOunt += 1;
					$read = "from SQL";
					if($userResult->num_rows > 0) {
						while($userRow = $userResult->fetch_assoc()) {
							execute($userRow['id'], $projRow['id'], $mysql);
						}
					}
				}
				if($projRow['company'] != '2')
				{
					$read = 'for company 2';
					$getUsers = "SELECT id FROM `users` WHERE id NOT IN (SELECT user_id FROM `user_project_permissions` WHERE project_id='" . $projRow['id'] . "')  AND `company`='2'";
					$userResult = $mysql->query($getUsers);
					if ($mysql->error) {
						writeLog($mysql, $getUsers, $rootPath);
					}else{
						if($userResult->num_rows > 0) {
							while($userRow = $userResult->fetch_assoc()) {
								execute($userRow['id'], $projRow['id'], $mysql);
								$usrCount += 1;
							}
						}
					}
				}
//print("\n Row : " . $projCOunt . "\tProject=" . $projRow['id'] . "\t" . $read . "\t Count=" . $usrCount);
			}
		}
	}
//$endtime = getTime();
//$totaltime = ($endtime - $starttime);
//echo "\n<!-- $totaltime -->";
//print("\nEnd of Permissions\n\n\n");
$mysql->close();
		writeLog("TEST  "," end of permissions cron ", $rootPath);
?>
