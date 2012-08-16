<?PHP 
	include("../_inc/config.inc");
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	$userID = $_GET['userID'];
	$newRiskId = $_GET['newRiskID'];
	$html = '';
	if($newRiskId != ''){
		$sql = "SELECT * FROM project_risks WHERE archived='0' AND active='1' AND id='$newRiskId'";
	}else{
		$sql = "SELECT * FROM project_risks WHERE archived='0' AND active='1' AND assigned_to_user_id='$userID'";
	}
	$result = $mysql->query($sql);

	if(@$result->num_rows > 0) {
		while($risks = @$result->fetch_assoc()) {
			$userName = 'None';
			$closedDate = '';
			$classComplete = '';
			$image = '/_images/flag_icon_red.png';
			$usrSql = "SELECT * FROM users WHERE id='" . $risks['assigned_to_user_id'] . "' AND active='1' LIMIT 1";
			$commentsSql = "SELECT count(1) as total FROM risk_comments WHERE risk_id='" . $risks['id'] . "' AND archived='0'";
			$projectSql = "SELECT project_name, id from projects where id='" . $risks['project_id'] . "'";

			$userResult = $mysql->query($usrSql);
			if($userResult) {
				$user = $userResult->fetch_assoc();
				$userName = $user['first_name'] . ' ' . $user['last_name'];
			}

			$comments = $mysql->query($commentsSql);
			if($comments){
				$riskComments = $comments->fetch_assoc();
				$totalComments = $riskComments['total'];
			}
			$projects = $mysql->query($projectSql);
			if($projects){
				$project = $projects->fetch_assoc();
				$projectName = $project['project_name'];
				$projectId = $project['id'];
			}

			if (strlen(trim($risks['title'])) > 70)
			{
				$title =  substr(trim($risks['title']), 0, 70)."...";
			}
			else
			{
				$title = trim($risks['title']);
			}
	/*				if($risks['active'] == '0'){
				$classComplete = ' class="complete"';
				$image = '/_images/status_complete.gif';
				$date_time_split = explode(" ", $risks['closed_date']);
				$date_split = explode("-", $date_time_split[0]);
				
				$closedDate = number_format($date_split[1]) ."/" .number_format($date_split[2]) ."/" .$date_split[0];
			}
	*/				
			$html .= '<li ' . $classComplete . ' id="pp_risk_' . $risks['id'] . '">
				<div class="riskLeftCol">
					<p class="risk_pjt_name"><a href="/controltower/index/edit/?project_id=' . $projectId . '">' . $projectName . '</a></p>
					<p class="risk_name"><img src="' . $image . '"/><a href="/controltower/index/edit/?project_id=' . $projectId . '" title="' . $risks['title'] . '">' . $title . '</a></p>
					<p class="risk_assigned">Assigned to: <span class="assigned">' . $userName . '</span> </p>
					<div>
						<p class="risk_done">
							<input type="checkbox" id="pp_risk_complete_' . $risks['id'] . '" name="risk_complete" onclick="changeRiskStatus(\'active\',\'' . $risks['id'] . '\');" class="checkbox"/> Done <span class="closed_date_' . $risks['id'] . '">' . $closedDate . '</span>
						</p>
						<p class="risk_delete">
							<button class="status status_delete" onclick="changeRiskStatus(\'archive\',\'' . $risks['id'] . '\'); return false;"><span>Delete</span></button>
						</p>
					</div>
				</div>
				<div class="riskRightCol">
					<textarea class="risk_comment" name="risk_comment">' . $risks['description'] . '</textarea>
					<div class="risk_comments_block" id="pp_risk_comment_' . $risks['id'] . '">
						<p class="risk_comment_count" onClick="pp_riskComment(\'' . $risks['id'] . '\', \'\'); return false;">Comments (<span class="count_' . $risks['id'] . '">' . $totalComments . '</span>)</p>
						<p class="risk_add_comment" onClick="pp_addComment(\'' . $risks['id'] . '\', \'\'); return false;">Add Comments</p>
					</div>
				</div>
				<div class="risk_comment_container" id="pp_add_comment_block_' . $risks['id'] . '" style="display:none;">
					<div class="label">New Comment:</div>
					<div class="content"><textarea class="add_comment" id="pp_add_comment_content_' . $risks['id'] . '" name="pp_add_comment_content_' . $risks['id'] . '"></textarea></div>
					<div class="submit">
						<button onclick="$(\'#pp_add_comment_block_' . $risks['id'] . '\').hide(\'slide\', { direction: \'up\' }, 500); return false;"><span>Cancel</span></button>
						<button onclick="submitComment(\'' . $risks['id'] . '\', \'' . $userID . '\', \'pp_add_comment_content_\'); return false;"><span>Submit Comment</span></button>
					</div>
				</div>
				<div class="uploaded_risk_comment_container" id="pp_read_comment_block_' . $risks['id'] . '" style="display:none;">
					<div class="risk_comments">
					</div>
					<div class="submit">
						<button onclick="$(\'#pp_read_comment_block_' . $risks['id'] . '\').hide(\'slide\', { direction: \'up\' }, 500); return false;"><span>Cancel</span></button>
					</div>
				</div>
			</li>';
		}
	}else{
		$html .= '<li class="complete no_risk"><p>There are no risk(s) assigned to you.</p></li>';
	}
	echo $html;
?>