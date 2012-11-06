<?PHP
	include("../_inc/config.inc");
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	// year month day

	$month = intval($_GET['month']);
	$year = intval($_GET['year']);

	$fromDate = $year."-".$month."-01";
	$toDate = $year."-".($month+1)."-01";
	$numOfDays = 31;
 
	$rp_query = "select title,status,project_code,(select program from lnk_programs where id = prog) as program,(select CONCAT(first_name, ' ', last_name) from users where id = mgr) as manager,id,Date,FirstName,LastName,sum(Hours) as Hours,Status from (SELECT 
	project_code,
	p.project_name as title,
	rb.userid AS id,
	DAY(rb.datestamp) Date,
	pb.first_name as FirstName,
	pb.last_name LastName,
	p.program prog,
	(count(rb.daypart)) AS Hours,
	pr.user_id as mgr,
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
	JOIN projects p ON p.id = rb.projectid
	JOIN project_roles pr ON pr.project_id = p.id AND pr.resource_type_id = 3 
	WHERE rb.datestamp >= '".$fromDate."' and rb.datestamp < '".$toDate."' and rb.daypart <> '9' and rb.status <> '2' and status <> '3' and pb.deleted = '0' and pb.active = '1' and pb.program='9' and  p.archived ='0' and p.active = '1' and p.deleted ='0' GROUP BY project_code,DAY(rb.datestamp),pb.first_name,pb.last_name
UNION 
SELECT 
	project_code,
	p.project_name as title,
	rb.userid AS id,
	DAY(rb.datestamp) Date,
	pb.first_name as FirstName,
	pb.last_name LastName,
	p.program prog,
	rb.hours AS Hours,
	pr.user_id as mgr,
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
	JOIN projects p ON p.id = rb.projectid
	JOIN project_roles pr ON pr.project_id = p.id AND pr.resource_type_id = 3 
	WHERE rb.datestamp >= '".$fromDate."' and rb.datestamp < '".$toDate."' and rb.daypart = '9' and rb.status <> '2' and status <> '3' and pb.deleted = '0' and pb.active = '1' and pb.program='9' and p.archived ='0' and p.active = '1' and p.deleted ='0' GROUP BY project_code,DAY(rb.datestamp),pb.first_name,pb.last_name) as tab group by tab.project_code,tab.Date,tab.FirstName,tab.LastName order by tab.FirstName,tab.LastName,tab.project_code,tab.Date";

		$result = @$mysql->sqlordie($rp_query);
	$userList = array();
	$userArray = array();
	
	$i = 0;
	while($row = @$result->fetch_assoc())
	{
		if($row['id'] == $old_id && $row['project_code'] == $old_project){
				$userArray[$i]['date'][$row['Date']] = $row['Hours'];
				$old_id = $row['id'];
				$old_project = $row['project_code'];
				continue;
			} else {
				$i++;
			}
			$userArray[$i]['id'] = $row['id'];
			$userArray[$i]['title'] = $row['title'];
			$userArray[$i]['manager'] = $row['manager'];
			$userArray[$i]['status'] = $row['status'];
			$userArray[$i]['name'] = $row['FirstName'] . ' ' . $row['LastName']; 
			$userArray[$i]['program'] = $row['program'];
            $userArray[$i]['project_code'] = $row['project_code'];
			$userArray[$i]['date'][$row['Date']] = $row['Hours'];
			$old_id = $row['id'];
			$old_project = $row['project_code'];
	}


$numOfDays = date("t", mktime("0", "0", "0", $month, "1", $year));

$html = "<table border='1'><tr><td></td><td></td><td></td><td></td><td></td><td></td>";
for($date=1; $date <= $numOfDays; $date++){
		$html .= "<td><b>".$date."/".$month."/".$year ."</b></td>";
}
$html .= "<td><b>Total</b></td></tr>"; 
$html .= "<tr><td><b>Project</b></td><td><b>Project Title</b></td><td><b>Project Manager</b></td><td><b>Resource</b></td><td><b>program</b></td><td><b>Status</b></td>";
for($date=1; $date <= $numOfDays; $date++){
		$html .= "<td><b>".date("l", mktime(0, 0, 0, $month,$date, $year)) . "</b></td>";
}
$html .= "</tr>";



	foreach($userArray as $key=>$value){
		$html .= "<tr><td>".$value['project_code']. '</td>';
		$html .= "<td>".$value['title']. '</td>';
		$html .= "<td>".$value['manager']. '</td>';
		$html .= "<td>".$value['name']. '</td>';
		$html .= "<td>".$value['program']. '</td>';
		$html .= "<td>".$value['status']. '</td>';
		$totalHours = 0;
		for($date=1; $date <=$numOfDays; $date++){
			if(array_key_exists($date, $value['date'])){
				$html .= "<td>".$value['date'][$date] . '</td>';
				$totalHours += $value['date'][$date];
			}else{
				$html .= '<td></td>';
			}
		}
		$html .= "<td>".$totalHours . "</td></tr>";
	}
	$html .="</table>";
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"Site Solutions resources.xls\";" );
	header("Content-Transfer-Encoding: binary"); 
	print $html;
?>

