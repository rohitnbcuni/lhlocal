<?PHP
	include('../_inc/config.inc');
	
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	$html = '';
	$wo_id = $mysql->real_escape_string($_GET['id']);
	//$user_query = "SELECT id,last_name, first_name FROM `users` WHERE `company`='2' ORDER BY `last_name`";
	$user_query = "SELECT id,last_name, first_name FROM `users` WHERE `active`='1' AND `deleted`='0'  ORDER BY `last_name`";
	$user_result = $mysql->query($user_query);
	$assigned_query= "SELECT `assigned_to` FROM `workorders` WHERE `id`=$wo_id";
    $assigned_result = $mysql->query($assigned_query);
	$assigned_row = $assigned_result->fetch_assoc();

	if($user_result->num_rows > 0) {
		while($row = $user_result->fetch_assoc()) {
             if( $assigned_row['assigned_to'] == $row['id']){
				 $selected = " SELECTED";
			 }
			 else {
						$selected = "";
					}
			if(!empty($row['last_name'])){
					$userName  = $row['last_name'] .", " .$row['first_name'];
				}else{
					$userName  = $row['first_name'];
				}
			$html .= '<option value="' .$row['id'] .'"' .$selected .' >' .ucfirst($userName) .'</option>';
		}
	}
	
	echo $html;
?>
