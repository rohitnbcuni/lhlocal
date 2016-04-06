<?PHP 
	include("../_inc/config.inc");
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$projectID = $mysql->real_escape_string($_GET['project']);
	$phase = $mysql->real_escape_string($_GET['phase']);
	$project_phase = array();

	$html = '<dl class="sub_phase_content">';

	$sql = "SELECT * FROM lnk_project_sub_phase_types WHERE active='1' AND phase_id='$phase'";
	$result = $mysql->sqlordie($sql);

	$sql = "SELECT sub_phase FROM project_sub_phase_finance WHERE deleted='0' AND active='1' AND phase='$phase' AND project_id='$projectID'";
	$projectResult = $mysql->sqlordie($sql);
	if(@$projectResult->num_rows > 0) {
		while($phaseData = @$projectResult->fetch_assoc()) {
			$project_phase[$phaseData['sub_phase']] = '1';
		}
	}
	if(@$result->num_rows > 0) {
		while($subPhase = @$result->fetch_assoc()) {
			if(array_key_exists($subPhase['id'], $project_phase)){
				$html .= '<dt class="present">' . $subPhase['name'] . '</dt>';
			}else{
				$html .= '<dt class="new" onclick="add_subPhase(\'' . $subPhase['id'] . '\', \'' . $phase . '\', \'' . $projectID . '\')">' . $subPhase['name'] . '</dt>';
			}
		}
	}
	$html .= '</dl>';
	echo $html;
?>