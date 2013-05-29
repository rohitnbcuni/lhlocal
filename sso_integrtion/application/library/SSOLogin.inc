<?PHP
	/*
		SSO Authentication class
	*/
	class SSOLogin {
	
		public function checkUser($userData){
			$db = Zend_Registry::get('db');
			$get_user = "SELECT * FROM `users` WHERE `email`='" .$mysql->real_escape_string($email) ."' AND `active` = '1'  AND `deleted` = '0' LIMIT 1";
			$userInfo = $db->fetchOne($get_user);
			return $userInfo;
		}
		
		
		
		
	}

?>
