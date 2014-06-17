<?PHP 
	include("../_inc/config.inc");
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$riskID = $mysql->real_escape_string($_GET['riskId']);
	$page = $mysql->real_escape_string($_GET['page']);
	$divID = $mysql->real_escape_string($_GET['id']);
	$commentPerPage = $mysql->real_escape_string$_GET['perPage']);
	$totalComments = 0;
	$prev = '<div class="prev">&nbsp</div>';
	$next = '<div class="next">&nbsp</div>';
	$numberOfPages = 0;
	$html = '<dl class="risk_user_comments">';

	$sql = "SELECT count(1) AS total FROM risk_comments WHERE archived='0' AND deleted='0' AND risk_id='$riskID'";
	$result = $mysql->sqlordie($sql);
	if($result){
		$commentResult = $result->fetch_assoc();
		$totalComments = $commentResult['total'];
	}
	$numberOfPages = ceil($totalComments/$commentPerPage);
	$from = $commentPerPage * ($page - 1);
	$curr_page = '<div class="page_number">' . $page . ' of ' . $numberOfPages . '</div>';
	if($page != '1'){
		$prevNo = intval($page) - 1;
		$prev = '<div class="prev"><a href="javascript:getRiskComments(\'' . $riskID . '\',\'' . $prevNo . '\',\'' . $divID . '\', \'' . $commentPerPage . '\');">Prev</a></div>';
	}

	if($page != $numberOfPages){
		$nextNo = intval($page) + 1;
		$next = '<div class="next"><a href="javascript:getRiskComments(\'' . $riskID . '\',\'' . $nextNo . '\',\'' . $divID . '\', \'' . $commentPerPage . '\');">Next</a></div>';
	}

	$commentSql = "SELECT * FROM risk_comments WHERE archived='0' AND deleted='0' AND risk_id='$riskID' order by created_date desc LIMIT $from, $commentPerPage";

	$commentResult = $mysql->sqlordie($commentSql);
	if(@$commentResult->num_rows > 0) {
		while($comment = @$commentResult->fetch_assoc()) {
			$usrSql = "SELECT * FROM users WHERE id='" . $comment['author_id'] . "' AND active='1' LIMIT 1";
			$userName = 'None';
			$userResult = $mysql->sqlordie($usrSql);
			if($userResult) {
				$user = $userResult->fetch_assoc();
				$userName = $user['first_name'] . ' ' . $user['last_name'];
			}

			$html .= '
				<dt>
					<div class="creator">
						Commented by: <span>' . $userName . '</span>
					</div>
					<div class="comment_text">
						' . $comment['text'] . '
					</div>
				</dt>
			';
		}
	}else{
		$html .= '<dd class="comment_text"> There are no comments for this flag.</dd>';
	}
	$html .= '</dl>';
	if($numberOfPages > 1){
	$html .= '<div class="paginator">
					' . $prev . $curr_page . $next . '
				</div>';
	}
	echo $html;
?>