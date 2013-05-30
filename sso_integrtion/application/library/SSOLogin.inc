<?PHP
	/*
		SSO Authentication class
	*/
	class SSOLogin {
	
		public function checkUser($userData){
			//$userData
			$userInfo = array();
			$db = Zend_Registry::get('db');
			$get_user = "SELECT * FROM `users` WHERE `email`='" .$userData['mail'][0] ."' AND `active` = '1'  AND `deleted` = '0' ORDER BY  `users`.`id` DESC LIMIT 1";
			if(count($get_user) > 0){
				$userInfo = $db->fetchRow($get_user);
				return $userInfo;
			}else{
				return false;
			}
		}
		
		
		
		
	}

?>
