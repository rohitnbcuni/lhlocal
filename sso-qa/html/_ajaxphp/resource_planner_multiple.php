<?php
			$users = Array();
			include('../_inc/config.inc');
			include("sessionHandler.php");
			global $mysql;
			//$mysql = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
			$result = $mysql->sqlordie("SELECT * FROM `users`");
			
			while($row = @$result->fetch_assoc()) {
				$rs_res = $mysql->sqlprepare("SELECT * FROM `resource_types` WHERE `id`= ? LIMIT 1", array($row['resource']) );
				$rs_row = $rs_res->fetch_assoc();
				$row['resource_name'] = $rs_row['name'];
				array_push($users,$row);
			}
				
			$jsonSettings = json_encode($users);

			// output correct header
			$isXmlHttpRequest = (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) ? 
			  (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false: false;
			($isXmlHttpRequest) ? header('Content-type: application/json') : header('Content-type: text/plain');
		
			echo $jsonSettings;
?>