<?PHP 
	include("../_inc/config.inc");
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$projectID = $mysql->real_escape_string($_GET['projId']);
	$page = $mysql->real_escape_string($_GET['page']);
	$riskPerPage = 1;
	$totalRisks = 0;
	$prev = '<span class="prev">&nbsp;</span>';
	$next = '<span class="next">&nbsp;</span>';
	$numberOfPages = 0;
	$html = '';

	$sql = "SELECT count(1) AS total FROM project_risks WHERE active='1' AND archived='0' AND project_id='$projectID'";
	$result = $mysql->sqlordie($sql);
	if($result){
		$riskCountResult = $result->fetch_assoc();
		$totalRisks = $riskCountResult['total'];
	}
	$numberOfPages = ceil($totalRisks/$riskPerPage);
	$from = $riskPerPage * ($page - 1);
	$curr_page = '<span class="present">' . $page . ' of ' . $numberOfPages . '</span>';
	if($page != '1'){
		$prevNo = intval($page) - 1;
		$prev = '<span class="prev"><a href="javascript:getProjectRisks(\'' . $projectID . '\',\'' . $prevNo . '\');">Prev</a></span>';
	}

	if($page != $numberOfPages){
		$nextNo = intval($page) + 1;
		$next = '<span class="next"><a href="javascript:getProjectRisks(\'' . $projectID . '\',\'' . $nextNo . '\');">Next</a></span>';
	}

	$riskSql = "SELECT * FROM project_risks WHERE active='1' AND archived='0' AND project_id='$projectID' order by created_date desc LIMIT $from, $riskPerPage";

	$riskResult = $mysql->sqlordie($riskSql);
	if(@$riskResult->num_rows > 0) {
		while($risk = @$riskResult->fetch_assoc()) {
			$usrSql = "SELECT * FROM users WHERE id='" . $risk['assigned_to_user_id'] . "' AND active='1' LIMIT 1";
			$userName = 'None';
			$userResult = $mysql->sqlordie($usrSql);
			if($userResult->num_rows > 0) {
				$user = $userResult->fetch_assoc();
				$userName = $user['first_name'] . ' ' . $user['last_name'];
			}

			$html .= '
				<p class="risk_title">' . $risk['title'] . '</p>
				<p class="risk_assigned"> Assigned to: <span>' .$userName . '</span></p>
				<p class="risk_desc">' . strip_tags(nl2br($risk['description']),'<a><br><strong><ul><li><ol>') . '</p>
			';
		}
	}

	if($numberOfPages > 1){
	$html .= '<p class="risk_paginator">
					' . $prev . $curr_page . $next . '
				</p>';
	}
	echo $html;
?>