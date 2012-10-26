<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	$startDate = $_POST['startDate'];
	$endDate = $_POST['endDate'];
	$LastDay = $_POST['lastDay'];
	$character = $_POST['showUser'];
	
	$startDatePart = explode("-", $startDate);
	$endDatePart = explode("-", $endDate);
	
	$user_rps = array();
	$otClass = "overtime";
	
	$sql_user = "SELECT * FROM `users` WHERE `company`='2' AND `deleted`='0' AND `last_name` like '$character%'  ORDER BY `last_name`";
	$user_res = $mysql->query($sql_user);
	
	if($user_res->num_rows > 0) {
		while($user_row = $user_res->fetch_assoc()) {
			$start_day = mktime(0,0,0,$startDatePart[0],$startDatePart[1],$startDatePart[2]);
			$end_day = mktime(0,0,0,$endDatePart[0],$endDatePart[1],$endDatePart[2]);
			
			$sqlrpOt = "SELECT COUNT(a.`id`) as total FROM `resource_blocks` a WHERE a.`userid`='" .$user_row['id'] ."' AND a.`datestamp` = '" .date("Y-m-d", $start_day) ." 00:00:00' AND a.`daypart` = '9' ORDER BY a.`datestamp`";
			$resrpOt = @$mysql->query($sqlrpOt);
			
			if($resrpOt->num_rows > 0) {
				$resrpOt_row = $resrpOt->fetch_assoc();
				if(@$resrpOt_row['total'] > 0) {
					$otClass = "cancel";
				} else {
					$otClass = "overtime";
				}
			} else {
				$otClass = "overtime";
			}
			
			$col = 1;
			while($start_day<=$end_day) {
				for($i = 0; $i < 8; $i++) {
					$sqlrp = "SELECT * FROM `resource_blocks` a LEFT JOIN `projects` b ON a.`projectid`=b.`id`  WHERE a.`userid`='" .$user_row['id'] ."' AND a.`datestamp` = '" .date("Y-m-d", $start_day) ." 00:00:00' AND a.`daypart` = '" .($i+1) ."' ORDER BY a.`datestamp`";
					$resrp = $mysql->query($sqlrp);

					if($resrp->num_rows == 1) {
						$rpRow = $resrp->fetch_assoc();
						$status = "";

						switch($rpRow['status']) {
							case 1: {
								$status = "overhead";
								break;
							}
							case 2: {
								$status = "outofoffice";
								break;
							}
							case 3: {
								$status = "allocated";
								break;
							}
							case 4: {
								$status = "convert";
								break;
							}
							default: {
								$status = "";
								break;
							}
						}


						if(empty($rpRow['projectid'])) {
							array_push($user_rps, Array('userid' => $user_row['id'], 'col' => $col, 'row' => ($i+1), 'class' => "$status", 'project' => '', 'overtime' => "$otClass", 'tooltip' => ''));
						} else {
							if(empty($rpRow['project_name'])) {
								$name = "";
								$tootltip = "";
							} else {
								$name = $rpRow['project_code'] .": " .$rpRow['project_name'];
								if(strlen($name) > 20) {
									$tootltip = $name;
									$name = substr($name, 0, 20) ."...";
								} else {
									$tootltip = "";
								}
							}
							array_push($user_rps, Array('userid' => $user_row['id'], 'col' => "$col", 'row' => ($i+1), 'class' => "$status", 'project' => "$name", 'overtime' => "$otClass", 'tooltip' => "$tootltip"));
						}
					} else {
						array_push($user_rps, Array('userid' => $user_row['id'], 'col' => "$col", 'row' => ($i+1), 'class' => '', 'project' => '', 'overtime' => "$otClass", 'tooltip' => ''));
					}
				}
				$start_day += 86400; //add 24 hours
				$col++;
			}
		}
		
		$jsonSettings = json_encode($user_rps);

		// output correct header
		$isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ? 
		  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
		($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');

		echo $jsonSettings;
	}
?>