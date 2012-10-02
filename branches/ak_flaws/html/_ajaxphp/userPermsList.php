<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	//Defining Global mysql connection values
	global $mysql;
	
	$compId = @$mysql->real_escape_string($_POST['company']);
	
	$html = "";
	
	if(!empty($compId)) {
		$query = "SELECT * FROM `users` WHERE `company`='$compId'";
		$res = $mysql->sqlordie($query);
		
		if($res->num_rows > 0) {
			$html .= '<select id="control_3" name="control_3[]" multiple="multiple" size="5">';
			$html .= '<option value="" SELECTED></option>';
			while($row = $res->fetch_assoc()) {
				$html .= '<option value="' .$row['id'] .'" SELECTED>' .ucfirst($row['first_name']) .' ' .ucfirst($row['last_name']) .'</option>';
			}
			$html .= '</select>';
		}
	}
	
	echo $html;
?>