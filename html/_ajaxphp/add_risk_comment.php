<?PHP 
	include("../_inc/config.inc");
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

	$riskId = (int)$mysql->real_escape_string($_POST['riskId']);
	$userId = $mysql->real_escape_string($_POST['userId']);
	$commentText = $mysql->real_escape_string($_POST['comment']);

	$insertSql = "INSERT INTO risk_comments (risk_id, text, author_id, last_changed_date) VALUES ('$riskId', '$commentText', '$userId', CURRENT_TIMESTAMP)";

	$mysql->sqlordie($insertSql);
	$count = 0;
	$commentResult = $mysql->sqlordie("SELECT count(1) AS comment_count FROM risk_comments WHERE risk_id='$riskId' AND deleted='0' AND archived='0'");

	if($commentResult->num_rows > 0) {
		$comment = $commentResult->fetch_assoc();
		$count = $comment['comment_count'];
	}
	echo $count;

?>