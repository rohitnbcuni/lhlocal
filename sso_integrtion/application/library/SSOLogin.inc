<?PHP
	/*
		SSO Authentication class
	*/
	class SSOLogin {
	
		public function checkUser($userData){
			//$userData
			$userInfo = array();
			$db = Zend_Registry::get('db');
			//Check sso user in LH DB
			$get_user = "SELECT * FROM `users` WHERE `sso`='" .$userData['uid'][0] ."' AND `active` = '1'  AND `deleted` = '0' ORDER BY  `users`.`id` DESC LIMIT 1";
			$userInfo = $db->fetchRow($get_user);
			if(count($userInfo) == 0){
			//Check User Is Active with email ID in DB
			$get_user = "SELECT * FROM `users` WHERE `email`='" .$userData['mail'][0] ."' AND `active` = '1'  AND `deleted` = '0' ORDER BY  `users`.`id` DESC LIMIT 1";
			$userInfo = $db->fetchRow($get_user);
			if(count($userInfo) > 0){
				
				return $userInfo;
			}else{
				//Check USER in LH but In deleted mode
				$get_user = "SELECT * FROM `users` WHERE `email`='" .$userData['mail'][0] ."' AND deleted ='1' ORDER BY  `users`.`id` DESC LIMIT 1";
				$userInfo = $db->fetchRow($get_user);
				if(count($userInfo) == 0){
					//SSO is NEW in LH: Inser Event
					$uData = array();
					//	$uData['bc_id'] = $userData;
					$uData['user_name'] = $userData['mail'][0];
					$uData['email'] = $userData['mail'][0];
					$uData['first_name'] = $userData['FirstName'][0];
					$uData['last_name'] = $userData['LastName'][0];
					$uData['address_1'] = $userData['Street'][0];
					$uData['city'] = $userData['Location'][0];
					$uData['state_province'] = $userData['State'][0];
					$uData['postal_code'] = $userData['Postalcode'][0];
					$uData['phone_office'] = $userData['Telephonenumber'][0];
					$uData['phone_mobile'] = $userData['mobile'][0];
					$uData['user_img'] = '/_images/empty_mugshot.gif';
					$uData['sso'] = $userData['uid'][0];
					$uData['user_access'] =  "01000000";
					$uData['role'] =  UNASSIGNED_PHASE;
					try{
						$db->insert("users", $uData);
					}catch(Exception $e){
						
						echo "USers Insert ERROR".$e->getMessage();
					}
					return $uData;
				}else{
					//User is deleted in LH Application
					$userInfo['error'] = "Invalid User";
					
				}
			
			}
		}else{
			return $userInfo;
		
		
		}
		
		
		
		
	}

?>
