<?PHP

include("../_inc/config.inc");
if(!ISSET($_SESSION['user_id'])){
	die("<b>You are not allowed to access these files</b>");
}	
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment;filename=HoursVarianceReportByProject.xls"); 
header("Content-Transfer-Encoding: binary");

global $mysql;
//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	//  To fetch the phase names

$sql = "SELECT id,name from lnk_project_phase_types";
$rp_res = @$mysql->sqlordie($sql);
if($rp_res->num_rows > 0) {
			while($rp_row = $rp_res->fetch_assoc()) {
				$phase_types[$rp_row['id']] = $rp_row['name'];
			}
}

	// To fetch all the phases for all the active projects

//$sql = "SELECT project_code,p.id,project_name,ppt.name,pp.start_date,pp.projected_end_date,ppf.hours FROM projects p JOIN project_phases pp ON p.id = pp.project_id JOIN lnk_project_phase_types ppt ON pp.phase_type = ppt.id JOIN project_phase_finance ppf ON ppf.phase =ppt.id AND ppf.project_id = p.id WHERE p.active='1' AND p.archived='0' AND p.deleted='0' AND pp.deleted = '0' AND pp.active='1' AND ppt.active='1' AND ppt.deleted='0' order by p.project_code";
$sql = "SELECT project_code, p.id, project_name, ppt.name, pp.start_date, pp.projected_end_date, ppf.hours, ln.program, c.name AS company, (

SELECT CONCAT( first_name, ' ', last_name )
FROM users, project_roles
WHERE users.id = project_roles.user_id
AND project_id = p.id
AND resource_type_id =3
) AS manager, (

SELECT name
FROM lnk_project_status_types
WHERE id = p.project_status
) as status
FROM projects p
JOIN project_phases pp ON p.id = pp.project_id
JOIN lnk_project_phase_types ppt ON pp.phase_type = ppt.id
JOIN project_phase_finance ppf ON ppf.phase = ppt.id
JOIN companies c ON c.id = p.company
AND ppf.project_id = p.id
JOIN lnk_programs ln ON ln.id = p.program
WHERE p.active = '1'
AND p.archived = '0'
AND p.deleted = '0'
AND pp.deleted = '0'
AND pp.active = '1'
AND ppt.active = '1'
AND ppt.deleted = '0'
ORDER BY p.project_code";

$res = $mysql->sqlordie($sql);
$html = "<table border=1><tr><td><b>Project Code</b></td><td><b>Project Name</b></td><td><b>Project Phase</b></td><td><b>Start</b></td><td><b>End</b></td><td><b>Hours Entered in Finance & Budget</b></td><td><b>Actual Hours in Resources</b></td><td><b>Variance</b></td><td><b>Project Manager</b></td><td><b>Program</b></td><td><b>Company</b></td><td><b>Status</b></tr>";

$i = 0;
if($res->num_rows > 0) {
			while($row = $res->fetch_assoc()) {
				$flag = 0;
				
				if($row['id'] != $old_projectid || $old_projectid == ''){
					$data = calculateToDate($row['id'],$mysql,$phase_types);		//calcluating the hours for different phases
				
					if(array_key_exists('unassigned',$data)){
						$html .= "<tr><td>".$row['project_code']."</td>";
						$html .= "<td>".$row['project_name']."</td>";
						$html .= "<td>unassigned</td>";
						$html .= "<td>".substr($row['start_date'],0,11)."</td>";
						$html .= "<td>".substr($row['projected_end_date'],0,11)."</td>";
						$html .= "<td>".$row['hours']."</td>";
						$html .= "<td>".$data['unassigned']."</td><td></td>";
						$html .= "<td>".$row['manager']."</td>";
					    $html .= "<td>".$row['program']."</td>";
					    $html .= "<td>".$row['company']."</td>";
					    $html .= "<td>".$row['status']."</td></tr>";
						
					}
				}
					$html .= "<tr><td>".$row['project_code']."</td>";
					$html .= "<td>".$row['project_name']."</td>";
					$html .= "<td>".$row['name']."</td>";
					$html .= "<td>".substr($row['start_date'],0,11)."</td>";
					$html .= "<td>".substr($row['projected_end_date'],0,11)."</td>";
					$html .= "<td>".$row['hours']."</td>";
					foreach($data as $key=>$value){			// assigning different phase values for projects
					if($row['name'] == $key){
						$html .= "<td>".$value."</td><td></td>";
						$flag = 1;
						break;
					} 
				}
				if($flag == 0){$html .=  "<td>0</td><td></td>";}
			$i++;
									$html .= "<td>".$row['manager']."</td>";
						$html .= "<td>".$row['program']."</td>";
						$html .= "<td>".$row['company']."</td>";
						$html .= "<td>".$row['status']."</td>";
			$html .= "</tr>";
		
			
			$old_projectid = $row['id'];
			}
}
$html .="</table>";
echo $html;

function calculateToDate($projID, $mysql,$phase_types){
	
	$toDateArray = array();
	$todate = 0;
	$rp_data = "SELECT * FROM `resource_blocks` WHERE `projectid`='" .$projID ."' AND `status`='4'";
	$rp_res = @$mysql->sqlordie($rp_data);
	if($rp_res->num_rows > 0) {
		while($rp_row = $rp_res->fetch_assoc()) {
			
			$select_user_project_phase = "SELECT ppf.phase phase FROM project_phase_finance ppf, user_project_role upr WHERE ppf.project_id = upr.project_id AND ppf.phase = upr.phase_subphase_id AND upr.flag = 'phase' AND upr.user_id = '" .$rp_row['userid'] ."' AND ppf.project_id = '" .$projID ."' LIMIT 1";
			$result_user_project_phase = $mysql->sqlordie($select_user_project_phase);

			if($result_user_project_phase->num_rows > 0){
				$user_project_phase_row = $result_user_project_phase->fetch_assoc();
				if($rp_row['daypart'] == 9) {
					$todate = $rp_row['hours'];
				} else {
					$todate = 1 ;
				}
				$toDateArray[$phase_types[$user_project_phase_row['phase']]] += $todate;
		//		echo $rp_row['userid']."<br>";
			}else{
				$select_project_sub_phases = "SELECT  pspf.phase phase FROM project_sub_phase_finance pspf, user_project_role upr WHERE pspf.project_id = upr.project_id AND pspf.sub_phase = upr.phase_subphase_id AND upr.flag = 'subphase' AND upr.user_id = '" .$rp_row['userid'] ."' AND pspf.project_id = '" .$projID ."' LIMIT 1";
				$result_sub_phases = $mysql->sqlordie($select_project_sub_phases);

				if($result_sub_phases->num_rows > 0){
					$sub_phase_row = $result_sub_phases->fetch_assoc();
					if($rp_row['daypart'] == 9) {
						$todate = $rp_row['hours'];
					} else {
						$todate = 1;
					}
					$toDateArray[$phase_types[$sub_phase_row['phase']]] += $todate;
				}else{/*
					$select_project_phase = "SELECT * FROM `project_phase_finance` WHERE `project_id` = '" . $projID . "' AND `phase`='".UNASSIGNED_PHASE."'";  
					$result_phases = $mysql->query($select_project_phase);
					if($result_phases->num_rows > 0){
						$phase_row = $result_phases->fetch_assoc();
						if($rp_row['daypart'] == 5) {
							$todate = $rp_row['hours'];
						} else {
							$todate = 2;
						}
						$toDateArray[$phase_row['phase']] += $todate;
					} */
					if($rp_row['daypart'] == 9) {
						$todate = $rp_row['hours'];
					} else {
						$todate = 1;
					}
						$toDateArray['unassigned'] += $todate;
				}
			}
		}
	}
//	echo "<pre>";print_r($toDateArray);
	return $toDateArray;
}
?>