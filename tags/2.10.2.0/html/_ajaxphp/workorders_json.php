<?
	include('../_inc/config.inc');
	//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	$link = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_PORT);
	mysql_select_db('nbc_lighthouse', $link);

	$postingList = Array();
	
	$select_project = "SELECT * FROM `projects` ORDER BY `company`"
	$result_project = @mysql_query($select_project);
	
	$select_wo = "SELECT * FROM `workorders` WHERE `project_id`='' AND `archived` = '1' AND `active` = '1' AND `deleted` = '0' ORDER BY `project_name` ASC";
	$result_wo = @mysql_query($select_wo);
	
	if(@mysql_num_rows($result_wo) > 0) {
		while($row = @mysql_fetch_assoc($result_wo)) {
			//array_push($postingList,Array('id' => $row['id'], 'code' => $row['project_code'],'name' => $row['project_name'], 'todate' => "1200", 'budget' => "2000", 'complete' => "25", 'company' => $row['company']));
		}
	}
	
	array_push($postingList,Array('id' => '2', 'code' => '4456423','name' => 'test', 'todate' => "1200", 'budget' => "2000", 'complete' => "25", 'company' => '5'));
	
	$jsonSettings = json_encode($postingList);

	// output correct header
	$isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ? 
	  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
	($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');

	echo $jsonSettings;

?>