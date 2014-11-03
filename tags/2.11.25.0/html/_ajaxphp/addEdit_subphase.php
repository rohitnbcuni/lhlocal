<?PHP 
	include("../_inc/config.inc");
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	$projectID = $mysql->real_escape_string($_GET['project']);
	$phase = $mysql->real_escape_string($_GET['phase']);
	$subPhase = $mysql->real_escape_string($_GET['subPhase']);
	$action = $mysql->real_escape_string($_GET['action']);
	$project_phase = array();
	$html = '';

	if($action == 'add'){
		$sql = "SELECT * FROM lnk_project_sub_phase_types WHERE active='1' AND phase_id=? AND id= ?";
		$result = $mysql->sqlprepare($sql, array($phase,$subPhase));
		if(@$result->num_rows > 0){
			$subPhaseResult = $result->fetch_assoc();
			$subPhaseName = $subPhaseResult['name'];
			$subPhaseId = $subPhaseResult['id'];
		}else{
			$subPhaseId = '';
		}
		$insertSql = "INSERT INTO `project_sub_phase_finance` (`project_id`, `phase`, `sub_phase`, `hours`, `rate`, `creation_date`) VALUES ('$projectID', '$phase', '$subPhase', '0', '0', NOW())";

//		print('<br> SQL : ' . $insertSql . '<br>');
		$mysql->sqlordie($insertSql);

		$onChange = 'calcSubPhaseFinance(\'' . $phase . '\', \'' . $subPhaseId . '\', fin_' . $phase . ')';
		$html .= '<dt id="sub_phase_' . $subPhaseId . '">
				<div class="finance_budget_name" onmouseover="$(\'#sp_remove_' . $subPhaseId .'\').css({display:\'block\'});" onmouseout="$(\'#sp_remove_' . $subPhaseId .'\').css({display:\'none\'});">
					<span> - ' . $subPhaseName . '</span>
					<span class="sp_remove" id="sp_remove_' . $subPhaseId .'" onClick="remove_subPhase(\'' . $subPhaseId .'\', \'' . $phase .'\', \'' . $projectID . '\');">Remove</span>
				</div>
				<div class="finance_budget_hours"><span>Hours:</span><input type="text" name="sub_hours" id="sub_hours" onChange="'.$onChange.'" /></div>
				<div class="finance_budget_rate"><span>Rate:</span><input type="text" name="sub_rate" id="sub_rate" onChange="'.$onChange.'" /></div>
				<div class="finance_budget_total"><span>Total:</span><input type="text" name="subtotal" id="subtotal" class="readonly" readonly /></div>
				<div class="dim" style="display: block"></div>
				<div style="clear: both"></div>
			</dt>';
	}else if($action == 'remove'){
		$delSql = "DELETE FROM `project_sub_phase_finance` WHERE `sub_phase`='$subPhase' AND `phase`='$phase' AND `project_id`='$projectID'";
		$mysql->sqlordie($delSql);
//		$html .= 'This sub Phase will be deleted here.';
	}
	echo $html;
?>