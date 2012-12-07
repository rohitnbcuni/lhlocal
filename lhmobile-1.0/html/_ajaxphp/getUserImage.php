<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	if(isset($_POST['userID'])) {
		$userID = $mysql->real_escape_string($_POST['userID']);
		
		$query = "SELECT `user_img` FROM `users` WHERE `id`='$userID'";
		$user_res = $mysql->sqlordie($query);
		$user_row = $user_res->fetch_assoc();		
		echo $user_row['user_img'];
	} else {
		echo "else /_images/empty_mugshot.gif";
	}
?>