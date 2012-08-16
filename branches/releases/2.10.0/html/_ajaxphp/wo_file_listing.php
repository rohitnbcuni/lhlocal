<?PHP
	include('../_inc/config.inc');
	
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	
	$dirname = str_replace("/", "", $mysql->real_escape_string(@$_GET['dirname']));
	$html = "";
	
	$select_files_in_dir = "SELECT * FROM `workorder_files` WHERE `directory`='$dirname'";
	$result = $mysql->query($select_files_in_dir);
	
	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$html .= "<li id=\"file_" .$row['id'] ."\">"
					."<a target=\"_blank\" href=\"/_ajaxphp/download.php?path=files/$dirname/" .$row['file_name'] ."\">"
						.$row['file_name']
						."&nbsp;"
					."</a>"
					."<a href=\"\" onclick=\"removeFile(" .$row['id'] ."); return false;\">remove</a>"
					."</li>";
		}
	} 
	echo $html;
?>
