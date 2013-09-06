<?PHP
	/*
		SSO Authentication class
	*/
	class SSOLogin {
	
		public function checkUser($userData){
			//$userData
			$nbc_employee_ids = array('2');
			$userInfo = array();
			$db = Zend_Registry::get('db');
			//Check sso user in LH DB
			$get_user = "SELECT * FROM `users` WHERE `sso`='" .$userData['uid'][0] ."' AND `active` = '1'  AND `deleted` = '0' ORDER BY  `users`.`id` DESC LIMIT 1";
			$userInfo = $db->fetchRow($get_user);
			if($userInfo == false){
			//Check User Is Active with email ID in DB
			$get_user = "SELECT * FROM `users` WHERE `email`='" .$userData['mail'][0] ."' AND `active` = '1'  AND `deleted` = '0' ORDER BY  `users`.`id` DESC LIMIT 1";
			$userInfo = $db->fetchRow($get_user);
			
			if($userInfo == true){
				$where[] = "email =  '".$userData['mail'][0]."'";
				$dataArray = array('sso' => $userData['uid'][0]);
				//If login status
				if($userInfo['login_status'] == ''){
					$data_array_login_status =  array();
					//@TODO: Need to change with Config variable
					if(in_array($userInfo['company'],$nbc_employee_ids){
						$data_array_login_status = array("login_status" => 'employee');
					
					}else{
						$data_array_login_status = array("login_status" => 'client');
					
					}
					$dataArray = array_merge($dataArray,$data_array_login_status);	
				}
				$db->update("users", $dataArray, $where);			
				return $userInfo;
			}else{
				//Check USER in LH but In deleted mode
				$get_user = "SELECT * FROM `users` WHERE `email`='" .$userData['mail'][0] ."' AND deleted ='1' ORDER BY  `users`.`id` DESC LIMIT 1";
				$userInfo = $db->fetchRow($get_user);
				if($userInfo == false){
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
					
					//By Default Assign Client Role to all New Users
					$uData['login_status'] = 'client';
					$uData['user_access'] =  "00100011";
					$uData['role'] =  UNASSIGNED_PHASE;
					$uData['active'] = '1';
					$uData['deleted'] = '0';
					try{
						$db->insert("users", $uData);
						
						$get_user = "SELECT * FROM `users` WHERE `sso`='" .$userData['uid'][0] ."' AND `active` = '1'  AND `deleted` = '0' ORDER BY  `users`.`id` DESC LIMIT 1";
						$userInfo = $db->fetchRow($get_user);
					}catch(Exception $e){
						
						//echo "USers Insert ERROR".$e->getMessage();
					}
					return $userInfo;
				}else{
					//User is deleted in LH Application
					//$userInfo['error'] = "Invalid User";
					
				}
			
			}
		}else{
			return $userInfo;
		
		
		}
		
	}
	
	function updateUserImage($userImage, $userId){
		$db = Zend_Registry::get('db');
		$where[] = 'id =  '.$userId;
		$dataArray = array('user_img' => $userImage);
		$db->update("users", $dataArray, $where);
	
	
	}
	
	
	
	}

?>
