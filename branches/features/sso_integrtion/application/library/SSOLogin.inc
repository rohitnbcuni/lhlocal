<?PHP
	/*
		SSO Authentication class
	*/
	class SSOLogin {
	
		public function checkUser($userData){
			//$userData
			$db = Zend_Registry::get('db');
			$get_user = "SELECT * FROM `users` WHERE `email`='" .$userData['mail'][0] ."' AND `active` = '1'  AND `deleted` = '0' ORDER BY  `users`.`id` DESC LIMIT 1";
			$userInfo = $db->fetchOne($get_user);
			return $userInfo;
		}
		
		
		
		
	}

?>
