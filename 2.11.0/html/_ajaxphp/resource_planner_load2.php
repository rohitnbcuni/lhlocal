<?
//session_start();

//$blocks = Array();

$mysql = new mysqli('localhost', 'generic', 'generic', 'nbc_lighthouse');
			
$start = date("Y-m-d") . " 00:00:00";
$start_date_part = explode("-", date("Y-m-d"));
$end = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+4, date("Y"))) ." 23:59:59";
$end_date_part = explode("-", date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+4, date("Y"))));

$sql_user = "SELECT * FROM `users` ORDER BY `last_name`";
$user_res = $mysql->query($sql_user);

$row = 1;
if($user_res->num_rows > 0) {
	while($user_row = $user_res->fetch_assoc()) {
		$sql_resource = "SELECT * FROM `resource_types` WHERE `id`='" .$user_row['resource'] ."' LIMIT 1";
		$resource_res = $mysql->query($sql_resource);
		$resource_row = $resource_res->fetch_assoc();
		
		echo '<div class="schedules_row">
				<div class="schedule_owner">
					<strong>' .$user_row['first_name'] .'<br>' .$user_row['last_name'] .'</strong>
					<em>Site Developer</em>
					<button class="secondary"><span>view month</span></button>
					<button class="overtime"><span>+overtime</span></button>
				</div>
				<ul class="schedule">';
				
				$next_day = "";
				$day = 1;
				$hour = 1;
				
				$start_day = mktime(0,0,0,$start_date_part[1],$start_date_part[2],$start_date_part[0]);
				$end_day = mktime(0,0,0,$end_date_part[1],$end_date_part[2],$end_date_part[0]);
				
				while($start_day<=$end_day) {
					echo '<li class="schedule_day">
						<ul>';
							
					$sqlrp = "SELECT COUNT(a.`id`) FROM `resource_blocks` a LEFT JOIN `projects` b ON a.`projectid`=b.`id`  WHERE a.`userid`='" .$user_row['id'] ."' AND a.`datestamp` = '" .date("Y-m-d", $start_day) ." 00:00:00' ORDER BY a.`datestamp`, a.`daypart`";
					$resrp = $mysql->query($sqlrp);
					
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
									echo '<li id="dayblock' .$row ."_" .($i+1) .'" class="' .$status .'"><div class="slot_label"></div></li>';
								} else {
									if(empty($rpRow['project_code']) && empty($rpRow['project_name'])) {
										$name = "";
									} else {
										$name = $rpRow['project_code'] .": " .$rpRow['project_name'];
										if(strlen($name) > 20) {
											$name = substr($name, 0, 20) ."...";
										}
									}
									echo '<li id="dayblock' .$row ."_" .($i+1) .'" class="' .$status .'"><div class="slot_label">' .$name .'</div></li>';
								}
							} else {
								echo '<li id="dayblock' .$row ."_" .($i+1) .'"><div class="slot_label"></div></li>';
							}
						}
							
						echo '</ul>
					</li>';
					$start_day += 86400; //add 24 hours
				}
				
				echo '</ul>
			</div>';
		$row++;
	}
}
?>