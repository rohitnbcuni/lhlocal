<?PHP
	/*
		SSO Authentication class
	*/
	class SSOLogin {
	
		public function checkUser($userData){
			//$userData
			$userInfo = array();
			$db = Zend_Registry::get('db');
			echo $get_user = "SELECT * FROM `users` WHERE `email`='" .$userData['mail'][0] ."' AND `active` = '1'  AND `deleted` = '0' ORDER BY  `users`.`id` DESC LIMIT 1";
			$userInfo = $db->fetchRow($get_user);
			print_r($userInfo);
			return $userInfo;
		}
		
		
		
		
	}

?>
