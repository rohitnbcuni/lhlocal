<?PHP
	session_start();
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	$userid = $_POST['userid'];
	$postDate = $_POST['date'];
	$postDatePart = explode("/", $postDate);

//	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	if($_SESSION['user_id'] == $userid){
		$showSubmit = true;
	}else{
		$showSubmit = false;
	}
	$html = "";
	$week = array("mon"=>1, "tue"=>2, "wed"=>3, "thu"=>4, "fri"=>5);

	//$currentMonth = date('n');
	//$currentYear = date('Y');

	$currentMonth = date('n', mktime(0, 0, 0, $postDatePart[1], $postDatePart[2], $postDatePart[0]));
	$currentYear = date('Y', mktime(0, 0, 0, $postDatePart[1], $postDatePart[2], $postDatePart[0]));

	$numberOfDays = date('t', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
	$numberOfWeeks = ceil($numberOfDays/7);
	$firstMonthDay = date('N', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
	$lastMonthDay = date('N', mktime(0, 0, 0, $currentMonth, $numberOfDays, $currentYear));

	$date_offset_array = array("1"=>4, "2"=>3, "3"=>2, "4"=>1, "5"=>0, "6"=>0, "0"=>0,);
	$weekNum = 1;

	//$html .= '<div class="schedules_row schedules_row_wide">';
	$open = false;
	for($d=1;$d<=$numberOfDays;$d++){
		if($d>$numberOfDays) {
			$sqlCurDay = $numberOfDays;
		} else {
			$sqlCurDay  = $d;
		}

		$currentDay = date('N', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
		if($currentDay < 6) {
			if($d == 1 && $firstMonthDay < 7) {
				$sel_class = 'sel';
				$open = true;
				$sqlOt = "SELECT * FROM `resource_blocks` WHERE `userid`='$userid' AND `datestamp` = '"
				.date('Y/n/j', mktime(0, 0, 0, $currentMonth, $d, $currentYear))  ."' AND `daypart` = '5' AND `hours` > 0";
				$resOt = $mysql->query($sqlOt);
				if($resOt->num_rows > 0) {
					$otClass = "cancel";
				} else {
					$otClass = "overtime";
				}
				$html .= '<div class="schedules_row schedules_row_wide">
				<div class="schedule_weekof">
						<strong>'.date('M', mktime(0, 0, 0, $currentMonth, $d, $currentYear)).' '.$d.'-';
						if(($d+4)>$numberOfDays) { $html .= $numberOfDays; } else { $html .= ($d + $date_offset_array[$currentDay]); }
						$overtimeID = 'week_' .$userid .'_' .date('n-j-y', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
						$html .= '</strong>
						<button onclick="displayOvertime(\'' . $overtimeID . '\')" class="' .$otClass .'" id="' . $overtimeID . '"><span>+overtime</span></button>';
						$week_start_date = date('n-j-Y', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
						$week_number = date('W', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
						$returnString = resourceBlockLockHtml($week_start_date, $week_number, $userid, '1', $mysql);
						if(stristr($returnString, 'rp_edit') === FALSE && $_SESSION['login_status']!='admin'){
							$sel_class = '';
						}
						if($showSubmit || $_SESSION['login_status']=='admin'){
							$html .= $returnString;
						}
						$html .= '</div><ul class="schedule" id="week_num_' .$week_number. '">';
				if( $firstMonthDay > 1 && $firstMonthDay < 6 ){
					for($b=1;$b<$firstMonthDay;$b++){
						$html .= '<li class="schedule_day"><ul><li class="no_select"></li><li class="no_select"></li><li class="no_select"></li><li class="no_select"></li></ul></li>';
					}
				}
			}
			if($currentDay == 1 && $currentDay != $d) {
				$sel_class = 'sel';
				$open = true;
				$sqlOt = "SELECT * FROM `resource_blocks` WHERE `userid`='$userid' AND `datestamp` = '"
				.date('Y/n/j', mktime(0, 0, 0, $currentMonth, $d, $currentYear))  ."' AND `daypart` = '5' AND `hours` > 0";
				$resOt = $mysql->query($sqlOt);
				if($resOt->num_rows > 0) {
					$otClass = "cancel";
				} else {
					$otClass = "overtime";
				}

				$html .= '<div class="schedules_row schedules_row_wide">
					<div class="schedule_weekof">
						<strong>'.date('M', mktime(0, 0, 0, $currentMonth, $d, $currentYear)).' '.$d.'-';
						if(($d+4)>$numberOfDays) { $html .= $numberOfDays; } else { $html .= ($d+4); }
						$overtimeID = 'week_' .$userid .'_' .date('n-j-y', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
						$html .= '</strong>
						<button onclick="displayOvertime(\'' . $overtimeID . '\')" class="' .$otClass .'" id="' . $overtimeID . '"><span>+overtime</span></button>';
						$week_start_date = date('n-j-Y', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
						$week_number = date('W', mktime(0, 0, 0, $currentMonth, $d, $currentYear));
						$returnString = resourceBlockLockHtml($week_start_date, $week_number, $userid, '0', $mysql);
						if(stristr($returnString, 'rp_edit') === FALSE && $_SESSION['login_status']!='admin'){
							$sel_class = '';
						}
						if($showSubmit || $_SESSION['login_status']=='admin'){
							$html .= $returnString;
						}
						$html .= '</div><ul class="schedule" id="week_num_' .$week_number. '">';
			}
			if(in_array($currentDay, $week)) {
				$dayDate = date('Y/n/j', mktime(0,0,0,$currentMonth,$sqlCurDay,$currentYear));
				$dayDateDisplay = date('n/j/Y', mktime(0,0,0,$currentMonth,$sqlCurDay,$currentYear));
				$html .= '<!--== | START : Day ==-->
					<li class="schedule_day">
						<ul>';
						for($it = 0; $it < 4; $it++) {

							$sqlrp = "SELECT * FROM `resource_blocks` a LEFT JOIN `projects` b ON a.`projectid`=b.`id`  WHERE a.`userid`='$userid' AND a.`datestamp` = '"
							.$dayDate  ."' AND a.`daypart` = '" .($it+1) ."'";
							$resrp = $mysql->query($sqlrp);
							$rpRow = $resrp->fetch_assoc();

							$status = "";
							$name = "";
							$tootltip = "";

							if($resrp->num_rows == 1) {
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

								if(empty($rpRow['project_name'])) {
									$name = "";
									$tootltip = "";
								} else {
									$name = $rpRow['project_code'] .": " .$rpRow['project_name'];
									$full_name = $name;
									if(strlen($name) > 20) {
										$tootltip = $name;
										$name = substr($name, 0, 20) ."...";
									} else {
										$tootltip = "";
									}
								}
							}

							$html .= '<li class="' . $sel_class .' '. $status .'" id="userday_' .$userid ."_" .$dayDateDisplay ."_" .($it+1) .'"><div class="slot_label" style="float: left; width: 76px;" title="' . $full_name . '">' .$name .'</div><div class="slot_title">' .$tootltip .'</div>';
							if($it == 0) {
								$html .= '<div class="schedule_day_date" style="float: left; width: 10px;">'.$d.'</div>';
							}
							$html .= '</li>';
						}
						$html .= '</ul>
					</li>
					<!--== | END : Day ==-->';
			}
			if( $currentDay < 5 && $d != $numberOfDays ){ $html .=  ""; }
			//if( $currentDay == 7 && ($d > 2 && $firstMonthDay < 6)) {
			//	$html .= '</ul></div>
			//	<!--== | END: Row | ==-->';
			//}
			if( ($currentDay == 5 || $d == $numberOfDays) && $open) {
				$open = false;
				$html .= '</ul></div>
				<!--== | END: Row | ==-->';
			}
			//if( $currentDay < 5 && $d != $numberOfDays ){ $html .=  ""; }
		}
	}

	function resourceBlockLockHtml($date, $week, $user, $first_week_flag, $mysql){
		$date_array = explode("-", $date);
		$resourceBlockSql = 'SELECT 1 from `resource_planner_lock` WHERE `user_id`="' .$user. '" AND `week_num`="' .$week. '" AND `year`="' .$date_array[2]. '" AND `active`="1"';
		$result = $mysql->query($resourceBlockSql);
		$offset = array("1" => '0', "2" => '1', "3" => '2', "4" => '3', "5" => '4');
		if($first_week_flag == '1'){
			$start_day = date('w', mktime(0, 0, 0, $date_array[0], $date_array[1], $date_array[2]));
			$start_date = date('Y-n-j', mktime(0, 0, 0, $date_array[0], $date_array[1]-$offset[$start_day], $date_array[2]));
		}else{
			$start_date = date('Y-n-j', mktime(0, 0, 0, $date_array[0], $date_array[1], $date_array[2]));
		}
		if($result->num_rows == 0){
			$html = '<button class="rp_edit" id="lock_week_'.$week.'" onclick="completeWeek(\'' .$start_date. '\', \'' .$week. '\', \'' .$user. '\')"><span>Submit</span></button>';
		}else{
			if($_SESSION['login_status']=='admin'){
				$html = '<button class="rp_edit" id="lock_week_'.$week.'" onclick="unSubmitWeek(\'' .$start_date. '\', \'' .$week. '\', \'' .$user. '\',\''.$_SESSION['login_status']. '\')"><span>Un Submit</span></button>';
			}else{
				$html = '<button class="rp_complete" ><span>Submit</span></button>';
			}
		}
		return $html;
	}

	$weekday = 1;
	echo $html;
?>