<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	$curSec = explode("_", $mysql->real_escape_string(@$_GET['cursec']));
	
	$select_section = "SELECT * FROM `project_brief_sections` WHERE `section_type`='" 
		.$curSec[1] ."' AND `project_id`='" .$mysql->real_escape_string(@$_GET['comp_id']) ."' LIMIT 1";
	$section_result = $mysql->query($select_section);
	$row = $section_result->fetch_assoc();
	
	if($row['flag'] > 1) {
		echo 1;
	}
	
	/*switch(@$_GET['section']) {
		case 'desc': {
			$select_section = "SELECT * FROM `project_brief_sections` WHERE `section_type`='" 
				.$curSec[1] ."' AND `project_id`='" .@$_GET['comp_id'] ."' LIMIT 1";
			$section_result = mysql_query($select_section);
			$row = mysql_fetch_assoc($section_result);
			
			if($row['flag'] > 1) {
				echo 1;
			}
			
			break;
		}
		case '': {
			break;
		}
		case '': {
			break;
		}
		default: {
			break;
		}
	}*/
?>