<?PHP
	include("../_inc/config.inc");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	$selectedDate = $mysql->real_escape_string($_GET['monthSelected']);

	$date_split = @explode("/",$selectedDate);
	$numOfDays = date("t", mktime("0", "0", "0", $date_split[0], "1", $date_split[1]));
	$fromDate = "$date_split[1]-$date_split[0]-01";
	$toDate = "$date_split[1]-$date_split[0]-$numOfDays";

	$rp_query = "SELECT 
	(case MONTH(rb.datestamp)
	when 1 then 'January'
	when 2 then 'February'
	when 3 then 'March'
	when 4 then 'April'
	when 5 then 'May'
	when 6 then 'June'
	when 7 then 'July'
	when 8 then 'August'
	when 9 then 'September'
	when 10 then 'October'
	when 11 then 'November'
	when 6 then 'December'
	end) AS Month,
	rb.userid AS id,
	DAY(rb.datestamp) Date,
	pb.first_name as FirstName,
	pb.last_name LastName,
	lut.name Title,
	pb.sso SSO,
	pb.agency Agency,
	(count(rb.daypart) * 2) AS Hours,
	case rb.status
	when 0 then 'UNA'
	when 1 then 'Overhead'
	when 2 then 'Out of Office'
	when 3 then 'Scheduled'
	when 4 then 'Booked'
	else null
	end as 'Status'
	FROM resource_blocks rb
    JOIN users pb ON rb.userid = pb.id 
	LEFT JOIN `lnk_user_titles` lut ON pb.user_title = lut.id
	WHERE rb.datestamp >= '".$fromDate."' and rb.datestamp <= '".$toDate."' and rb.daypart <> '5' and rb.status <> '2' and status <> '3' and pb.deleted = '0' and pb.active = '1' 
	GROUP BY DAY(rb.datestamp),pb.first_name,pb.last_name order by pb.first_name,pb.last_name,DAY(rb.datestamp)";
	//print("rp_query  ".$rp_query);die();
	$result = @$mysql->query($rp_query);
	$userList = array();
	$userArray = array();

	$header = "Name,Vendor,";
	for($date=1; $date <= $numOfDays; $date++){
		$header .= $date . ',';
	}
	$header .= "Total \n";

	while($row = @$result->fetch_assoc())
	{
		if($row['id'] != $userID){
			$userArray[$row['id']]['name'] = $row['FirstName'] . ' ' . $row['LastName']; 
			$userArray[$row['id']]['vendor'] = $row['Agency']; 
			$effort_logged_user_ids[$row['id']] = $row['id'];
		}
		$userArray[$row['id']][$row['Date']] = $row['Hours'];
		$userID = $row['id'];
	}



	// fetch all users from LH who are not deleted and active
	$result1 = @$mysql->query("select id,agency,first_name,last_name from users where `company` = '2' and `deleted` = '0' and `active` = '1'");
	while($row = @$result1->fetch_assoc())
	{
		$users_list[$row['id']]['name'] = trim($row['first_name']) . ' ' . trim($row['last_name']); 
		$users_list[$row['id']]['vendor'] = $row['agency'];
		$ls[$row['id']] = $row['id'];
	}
	foreach($effort_logged_user_ids as $key=>$value){
		if(array_key_exists($effort_logged_user_ids[$key],$ls)){
			unset($users_list[$value]);				// users_list now contains only the deatils of users who has not filled efforts
		}
	}
	$all_users_list = $userArray + $users_list;			// all users
	uasort($all_users_list, 'compare_name'); 
	$userArray = $all_users_list;

	$overtime_sql = "SELECT rb.userid AS id, DAY(rb.datestamp) Date, rb.hours AS Hours FROM resource_blocks rb WHERE rb.datestamp >= '".$fromDate."' and rb.datestamp <= '".$toDate."' and rb.status <> '2' and rb.daypart = '5'";
	$overtime_result = $mysql->query($overtime_sql);
	if($overtime_result->num_rows > 0){
		while($overtime_row = $overtime_result->fetch_assoc()){
			if(!empty($userArray[$overtime_row['id']])){
			if(isset($userArray[$overtime_row['id']][$overtime_row['Date']])){
				$userArray[$overtime_row['id']][$overtime_row['Date']] += $overtime_row['Hours'];
			}else{
				$userArray[$overtime_row['id']][$overtime_row['Date']] = $overtime_row['Hours'];
			}
			}
		}
	}

	$userList = Array();
	foreach($userArray as $key=>$value){
		$userList[] = $key;
	}

	foreach($userList as $key=>$id){
		$html .= $userArray[$id]['name']. ',';
		$html .= $userArray[$id]['vendor']. ',';
		$totalHours = 0;
		for($date=1; $date <=$numOfDays; $date++){
			if(array_key_exists($date, $userArray[$id])){
				$html .= $userArray[$id][$date] . ',';
				$totalHours += $userArray[$id][$date];
			}else{
				$html .= ',';
			}
		}
		$html .= $totalHours . "\n";
	} 
	$contents = strip_tags($contents); // remove html and php tags etc.
//	echo "<pre>";
//	print_r($html);
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	// This one allows display or download
	header("Content-Type: application/octet-stream");
	// This one forces a download - uncomment (and comment octet-stream) if you want that
	//header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=\"rp_report.csv\";" );
	header("Content-Transfer-Encoding: binary"); 
	print $header; 
	print $html;

	function compare_name($a, $b)	// callback function for sorting name alphabetically
	{ 
		return strcasecmp($a['name'], $b['name']); 
	}  
?>
