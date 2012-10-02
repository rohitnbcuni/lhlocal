<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql;

	$html = '';
	
	$user_query = "SELECT * FROM `users`";
	$user_result = $mysql->sqlordie($user_query);
	
	if($user_result->num_rows > 0) {
		while($row = $user_result->fetch_assoc()) {
			$html .= '<option value="' .$row['id'] .'">' .ucfirst($row['first_name']) .' ' .ucfirst($row['last_name']) .'</option>';
		}
	}
	
	echo $html;
?>