<?PHP
	include('../_inc/config.inc');
	
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);	
	echo "<center> <b> UNASSIGNED PROJECTS PHASE UPDATE <b></center> 1111";	
	$project_all = "SELECT * FROM `projects` where 	archived = '0' and active='1' and deleted='0' and year='2010'";


	$project_list = $mysql->query($project_all);
		echo "<center> <b>  PROJECTS version <b></center> 1111";	
	if(@$project_list->num_rows > 0) {
		echo "<center> <b> UNASSIGNED PROJECTS PHASE UPDATE <b></center>";
		echo "<br>List of Projects : ";
		echo "<br>----------------------------------------------------------<br>";
		while($row = @$project_list->fetch_assoc()) {
		
			$project_id = $row['id'];
			$project_code = $row['project_code'];

			$project_version = 'INSERT INTO `qa_project_version` (`project_id`, `version_name`, `active`, `deleted`) VALUES ("'.$project_id.'", "'.$project_code.'", "1", "0" )';
			$mysql->query($project_version);
			echo "<br> UNASSIGNED PHASE inserted for the project - ".$project_id ;
			echo " Project Code- ".$project_code ;  

		}
	}	
?>