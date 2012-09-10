<?PHP
	include('../_inc/config.inc');
	include("sessionHandler.php");
	$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
	$project_id= $_GET['project_id'] ;

	$QRY_MASTER_SELECT ="SELECT `id`,`project_id`,`product_name` FROM `qa_project_product` where `project_id` in ('".$project_id."','0') AND active ='1' AND deleted ='0' order by project_id ='0' DESC, product_name ASC ";

	$result = $mysql->query($QRY_MASTER_SELECT);	
	$versionList = Array();
	if($result->num_rows > 0) {
		$i = '0';
		while($row = $result->fetch_assoc()) {
			array_push($versionList,$row);
			$i++;
		}

		  $jsonSettings = json_encode($versionList);

		  // output correct header
		  $isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ?
		  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
		  ($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');
  	
		  echo $jsonSettings;

	}
	
?>