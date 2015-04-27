<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	global $mysql;
	$project = $mysql->real_escape_string(@$_GET['project_id']);
	//echo $project ."<br /><br />";
	$sections = array();
	$section_keys = array();
	$sections = @$_POST['sections'];
	$section_keys = array_keys($sections);
	
	for($i = 0; $i < sizeof($section_keys); $i++)	 {
		$section_key_part = explode("_", $section_keys[$i]);
		//echo $section_key_part[1] ."<br />";
		
		$check_complete = "SELECT * FROM `project_brief_sections` WHERE `project_id`='" 
			.$project ."' AND `section_type`='" .$mysql->real_escape_string($section_key_part[1]) ."' LIMIT 1";
		$complete_res = $mysql->sqlordie($check_complete);
		
		if($complete_res->num_rows == 1) {
			$row = $complete_res->fetch_assoc();
			$update = "UPDATE `project_brief_sections` set `flag`='3' WHERE `id`='" .$row['id'] ."'";
			if($mysql->sqlordie($update)) {
				echo 1;
			}
		} else if($complete_res->num_rows == 0) {
			$insert = "INSERT INTO `project_brief_sections` "
				."(`project_id`,`section_type`,`flag`) "
				."VALUES "
				."('$project','" .$section_key_part[1]  ."','3')";
			if($mysql->sqlordie($insert)) {
				echo 1;
			}
		}
	}
?>