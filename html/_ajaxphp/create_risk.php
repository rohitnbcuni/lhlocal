<?PHP 
	include("../_inc/config.inc");
	include("sessionHandler.php");
	global $mysql;
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	$insertSql = ' test ';
	$title = $mysql->real_escape_string($_POST['title']);
	$desc = $mysql->real_escape_string($_POST['desc']);
	$assigned = $_POST['assignedTo'];
	$createdBy = $mysql->real_escape_string($_POST['createdBy']);
	$projectId = $_POST['projectId'];
	
	if($assigned != '-1'){
		$insertSql = "INSERT INTO project_risks (project_id, assigned_to_user_id, created_by_user_id, title, description) VALUES ('$projectId', '$assigned', '$createdBy', '$title', '$desc')";
	}else{
		$insertSql = "INSERT INTO project_risks (project_id, created_by_user_id, title, description) VALUES ('$projectId', '$createdBy', '$title', '$desc')";
	}

	$mysql->sqlordie($insertSql);

	// html for the newly created risk
	$getSql = "SELECT * FROM project_risks WHERE title='$title' AND created_by_user_id='$createdBy' AND project_id='$projectId' ORDER BY created_date DESC LIMIT 1";
	$html = '';
	$getResult = $mysql->sqlordie($getSql);
	if($getResult) {
		$risks = $getResult->fetch_assoc();
		$userName = 'None';
		$image = '/_images/flag_icon_red.png';

		$usrSql = "SELECT * FROM users WHERE id='" . $risks['assigned_to_user_id'] . "' AND active='1' LIMIT 1";
		$userResult = $mysql->sqlordie($usrSql);
		if($userResult->num_rows > 0) {
			$user = $userResult->fetch_assoc();
			$userName = $user['first_name'] . ' ' . $user['last_name'];
		}
		$html .= '<li id="risk_' . $risks['id'] . '">
			<div class="riskLeftCol">
				<p class="risk_name"><img src="' . $image . '"/>' . $risks['title'] . '</p>
				<p class="risk_assigned">Assigned to: <span class="assigned">' . $userName . '</span> </p>
				<div>
					<p class="risk_done">
						<input type="checkbox" id="risk_complete_' . $risks['id'] . '" name="risk_complete" class="checkbox" onclick="changeRiskStatus(\'active\',\'' . $risks['id'] . '\');"/> Done <span class="closed_date_' . $risks['id'] . '"></span>
					</p>
					<p class="risk_delete">
						<button class="status status_delete" onclick="changeRiskStatus(\'archive\',\'' . $risks['id'] . '\'); return false; "><span>Delete</span></button>
					</p>
				</div>
			</div>
			<div class="riskRightCol">
				<textarea class="risk_comment" name="risk_comment">' . $risks['description'] . '</textarea>
				<div class="risk_comments_block" id="risk_comment_' . $risks['id'] . '">
					<p class="risk_comment_count" onClick="riskComment(\'' . $risks['id'] . '\', this); return false;">Comments (<span class="count_' . $risks['id'] . '">0</span>)</p>
					<p class="risk_add_comment" onClick="addComment(\'' . $risks['id'] . '\', \'\'); return false;">Add Comments</p>
				</div>
			</div>
			<div class="risk_comment_container" id="add_comment_block_' . $risks['id'] . '" style="display:none;">
				<div class="label">New Comment:</div>
				<div class="content"><textarea class="add_comment" id="add_comment_content_' . $risks['id'] . '" name="add_comment_content_' . $risks['id'] . '"></textarea></div>
				<div class="submit">
					<button onclick="$(\'#add_comment_block_' . $risks['id'] . '\').hide(\'slide\',{direction:\'up\'},500); return false;"><span>Cancel</span></button>
					<button onclick="submitComment(\'' . $risks['id'] . '\', \'' . $createdBy . '\', \'add_comment_content_\'); return false;"><span>Submit Comment</span></button>
				</div>
			</div>
			<div class="uploaded_risk_comment_container" id="read_comment_block_' . $risks['id'] . '" style="display:none;">
				<div class="risk_comments">
				</div>
				<div class="submit">
					<button onclick="$(\'#read_comment_block_' . $risks['id'] . '\').hide(\'slide\', { direction: \'up\' }, 500); return false;"><span>Cancel</span></button>
				</div>
			</div>
		</li>';
	}
	echo $html;

?>