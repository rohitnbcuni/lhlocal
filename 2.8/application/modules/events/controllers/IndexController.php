<?PHP
ob_start();
	include('_ajaxphp/util.php');
	include('Events.inc');
	
	class Events_IndexController extends LighthouseController  { 
		
		static private $opsComapny = array(2,136,141);
		//need to replace on prod
		//LH id 2429 OTS Support
		//LH id 1206 MPAT Hosting Web Team
		static private $opsDls = array(2429,1206);
		//static private $opsDls = array(1328,1206);
		//static private $opsDls = array(1734,1932);
		
		
		
		public function indexAction() {
		
			$this->_redirect("events/index/calendarview");
			
		}
		public function createAction(){
			
			$userId = $_SESSION['user_id'];
			$userComapny = $_SESSION['company'];
			
			if(in_array($userComapny,self::$opsComapny)){
				$this->view->assign("app","allow");
			 }else{
			 	$this->view->assign("app","notallow");
			 }
			$this->view->assign("User_id", $userId);
			$userInfo = array();
		
			//operation team company
			
			
			//Disabled to all user on new event
			$this->view->assign("affectedBrandDisplay","none");
			//for affected brand
			//for affected brand
			//$this->view->assign('disabled',"disabled='disabled'");
			$this->view->assign("saveButton","Submit");
			$this->view->assign('affectedBrand',"disabled='disabled'");
			$this->view->assign('ev_dimmer',"block");
			
			if(isset($_GET['eId'])) {
				$eId = $_GET['eId'];   
				$eventInfo = EventDisplay::getEventInfo($eId, '*');
				$this->view->assign("saveButton","Save Changes");
				
				
				if(count($eventInfo) == 0){
					//redirect to event listing page
					$this->_redirect("events");
				}
				//list comment
				$commentInfo = EventDisplay::getComments($eId, '*');
				$this->view->assign("commentInfo", $commentInfo);
				
				if($userId != $eventInfo[0]['requested_by']){
					$this->view->assign('disabled',"disabled='disabled'");
					$this->view->assign('ev_buttons',"none");
					//$this->view->assign('affectedBrand',"block");	
				}
				if(in_array($userComapny,self::$opsComapny)){
					$this->view->assign('disabled',"");	
					$this->view->assign('ev_dimmer',"none");
					$this->view->assign('ev_buttons',"block");
					$opSCompantArr = implode(",", self::$opsComapny);
					$this->view->assign("opsPerson", $opSCompantArr);
					$this->view->assign('affectedBrand',"");
					//enabled to all opsmember
					$this->view->assign("affectedBrandDisplay","block");
				}else{
					//$this->view->assign('disabled',"disabled");
				}
				$this->view->assign('eventInfo',$eventInfo[0]);
				$this->view->assign('eId', $eId);
				if(count($eventInfo) > 0){
					$this->view->assign('cclist',$eventInfo[0]['cclist']);
					$ccListArray = explode(",", $eventInfo[0]['cclist']);
					//p($ccListArray);
					foreach($ccListArray as $ccKey => $ccVal){
						if(!empty($ccVal)){
							$userInfo[] = EventDisplay::getRequestorInfo($ccVal);
						}
						//$ccListArray =  	
					}
					$this->view->assign('cc_user_row', $userInfo);
					//$
				}
			}else{
				//$this->_redirect('noaccess');
			}
				//$this->_helper->layout->disableLayout();
		}
		
		public function userrequesterAction(){
			$this->view->assign('user_id',$this->_request->getParam('user_id'));
			
			$this->_helper->layout->disableLayout();
			
			
		}
		public function addccuserAction(){
			$eventId = $this->_request->getParam('eId');
			$cc = $this->_request->getParam('cc');
			$addcc = $this->_request->getParam('addcc');
			$ccArray = array();
			$list = explode(",", $cc);
			for($i = 0; $i < sizeof($list); $i++) {
				if(!empty($list[$i]) && !isset($ccArray[$list[$i]])) {
					if($list[$i] != $this->getParam('remove'))
					$ccArray[$list[$i]]=true;
				}
			}
			$listKeys = array_keys($ccArray);
			
			$arrayData = "";
			$list = "";
			$userInfo = array();
			for($z = 0; $z < sizeof($listKeys); $z++) {
				$arrayData .= $listKeys[$z] .",";
			
				if(!empty($listKeys[$z])) {
					$userInfo[] = EventDisplay::getRequestorInfo($listKeys[$z]);
				}
				
			}
			if(!empty($eventId)){
				if(!empty($addcc)){
					$addccuserInfo = EventDisplay::getTableData("users","concat_ws(',',last_name,first_name) as fullname,email","id=$addcc");
					$eventInfo = EventDisplay::getTableData("events","*","id = $eventId");
					$requestedByuser = $eventInfo['requested_by'];
					$createoruserInfo = EventDisplay::getTableData("users","concat_ws(',',last_name,first_name) as fullname,email","id=$requestedByuser");
					$brandName = EventDisplay::getTableData('companies', "name", "id=".$eventInfo['company_id']);
					//$updateCClist = implode(",",$listKeys);
					$eventEmailInfo = array(
						"requestedBy" => $createoruserInfo['fullname'],
						"brand"		=> $brandName['name'],
						"event_id"	=> $eventId,
						"desc"	=> $eventInfo['body'],
					);
					$html = new Zend_View();
					$html->setScriptPath(APPLICATION_PATH."/modules/email/views/");	
					$html->assign('eventInfo',$eventEmailInfo);
					//$html->assign('site', 'limespace.de');
					// create mail object
					$mail = new Zend_Mail('utf-8');
					// render view
					$bodyText = $html->render('ccusers.phtml');
					// configure base stuff
					$mail->addTo($addccuserInfo['email'],$addccuserInfo['fullname']);
					$subject = "Event ".$eventId.": You Have Been CC'd on Event - ". html_entity_decode($eventInfo['title'],ENT_NOQUOTES,'UTF-8') . "";
					$mail->setSubject($subject);
					
					$mail->setFrom(WO_EMAIL_FROM,'lighthouse@nbcuots.com');
					$mail->setBodyHtml($bodyText);
					//p($mail);
					$mail->send();
				}
				
				
				EventDisplay::updateEventCcList($eventId, $arrayData);
			}
			$this->view->assign('cc_user_row', $userInfo);
			$this->_helper->layout->disableLayout();
		}
		
		public function addeventAction(){
			
			
			$userId = $_SESSION['user_id'];
			$startdate = Util::dateTimeToSql($_POST['ev_start_date'],$_POST['ev_start_hr'],$_POST['ev_ampm'],$_POST['ev_start_min']);
			$enddate = Util::dateTimeToSql($_POST['ev_end_date'],$_POST['ev_end_hr'],$_POST['ev_end_ampm'],$_POST['ev_end_min']);
		    $title = Util::escapewordquotes(strip_tags($_POST['ev_title']));
		    EventDisplay::changeTimeZone($startdate,$_POST['ev_start_time_zone']);
			$data = array(
				"company_id" => $_POST['ev_brand_name'],
				'assigned_to' => $_POST['ev_assigned_to'],
				'status' => $_POST['ev_eventStatus'],
				'archived' => 0,
				'title' => $title,
				'example_url' => strip_tags($_POST['ev_url']),
				'anticipated_traffic' => $_POST['ev_traffic'],
				'load_test_status' => $_POST['ev_loadTest'],
				'body' => Util::escapewordquotes($_POST['ev_desc']),
				'requested_by' => $_POST['ev_requested_by'],
				'cclist' => $_POST['cclist'],
				'start_date' => $startdate,
				'completed_date'  =>$enddate,
				'time_zone'  => $_POST['start_time_zone'],
				'est_start_datetime' => EventDisplay::changeTimeZone($startdate,$_POST['start_time_zone']),
				'est_end_datetime' => EventDisplay::changeTimeZone($enddate, $_POST['start_time_zone']),
				'creation_date' => date("Y-m-d H:i:s"),
				'active' => 1,
				'deleted'  => 0
			);
			if(!empty($_REQUEST['event_id'])){
				$eid = $_POST['event_id'];
				$eventChangeFilter = '';
				//case for changing event status
				/*a.	WHEN user updates the following information
				THEN the event status is changed back to Pending
				AND event is no longer displayed on the calendar
				•	Date Change
				•	URL Change
				•	Anticipated Traffic increase*/
				$preStartDate = $_POST['pre_start_date'];
				$preEndDate = $_POST['pre_end_date'];
				$preUrl = $_POST['pre_url'];
				$preTraffic = $_POST['pre_traffic'];
				$prevBrandId = $_POST['prev_brand_name'];
				if($preStartDate != strtotime($startdate)){
					$eventChangeFilter = 'A';
				}
				if($preEndDate != strtotime($enddate)){
					$eventChangeFilter = 'A';
				}
				if($preUrl != $_POST['ev_url']){
					$eventChangeFilter = 'A';
				}
				if($preTraffic != $_POST['ev_traffic']){
					$eventChangeFilter = 'A';
				}
				$eventStatus = '';
				if($eventChangeFilter == 'A'){
					$eventS = EventDisplay::getTableData('lnk_event_status_types', "id", "name='Pending'");
					$eventStatus = $eventS['id'];
				}else{
					$eventStatus = $_POST['ev_eventStatus'];
				}
				
				$data1 = array(
					"company_id" => $_POST['ev_brand_name'],
					'assigned_to' => $_POST['ev_assigned_to'],
					'status' => $eventStatus,
					'title' => $title,
					'example_url' => strip_tags($_POST['ev_url']),
					'anticipated_traffic' => $_POST['ev_traffic'],
					'load_test_status' => $_POST['ev_loadTest'],
					'body' => Util::escapewordquotes($_POST['ev_desc']),
					'requested_by' => $_POST['ev_requested_by'],
					'cclist' => $_POST['cclist'],
					'start_date' => $startdate,
					'completed_date'  =>$enddate,
					'time_zone'  => $_POST['start_time_zone'],
					'est_start_datetime' => EventDisplay::changeTimeZone($startdate,$_POST['start_time_zone']),
					'est_end_datetime' => EventDisplay::changeTimeZone($enddate, $_POST['start_time_zone'])
				
				);
				
				//echo $eventChangeFilter;
				$prevStatus = $_POST['prevStatus'];
				$prevAssigned = $_POST['prevAssigned'];
				$preEventInfo = EventDisplay::getTableData("events","*","id = $eid");
				$eventId = EventDisplay::updateEvent($data1,$eid, $prevStatus,$prevAssigned);
				$userId = $_SESSION['user_id'];
				$userComapny = $_SESSION['company'];
				if(in_array($userComapny,self::$opsComapny)){
					if(ISSET($_POST['brandContainer'])){
						$arrayBrand = array('affected_company_list' => $_POST['brandContainer']);
						EventDisplay::updateAffectedBrand($arrayBrand, $eid);
					}
				}
			###########Email to update events#######################	
			$brandName = EventDisplay::getTableData('companies', "name", "id=".$data1['company_id']);
			$prevBrandName = EventDisplay::getTableData('companies', "name", "id=".$prevBrandId); 
			$html = new Zend_View();
			$html->setScriptPath(APPLICATION_PATH."/modules/email/views/");	
			$requestedByuser = $data1['requested_by'];
			$createoruserInfo = EventDisplay::getTableData("users","concat_ws(', ',last_name,first_name) as fullname,email","id=$requestedByuser");
			$eventStatusName = EventDisplay::getTableData("lnk_event_status_types","name","id=".$eventStatus);
			$currentUserInfo = EventDisplay::getTableData("users","concat_ws(', ',last_name,first_name) as fullname,email","id=$userId");
			$eventEmailInfo = array(
				"requestedBy" => $createoruserInfo['fullname'],
				"brand"		=> $brandName['name'],
				"event_id"	=> $eid,
				"status"	=> $eventStatusName['name'],
				"desc"	=> $data1['body'],
				"pre_event_values" => $preEventInfo,
				"new_event_values" => $data1,
				"new_affected_brand" => $arrayBrand,
				"currentUser"      => $currentUserInfo['fullname'] 
				
			);
			
			$html->assign('eventInfo',$eventEmailInfo);
			$bodyText = $html->render('statusevent.phtml');
					//requestor,creator,assign to
			$emailUsers = array();
			$emailUsers[] = $userId;
			if($userId != $data1['requested_by']){
				$emailUsers[] = $data1['requested_by'];
			}
			$emailUsers[] = $data1['assigned_to'];
			//email to all users (Ops DL, Assign to , Request by, cc)
			$emailtoAll = '';
			//email to only cc, requestor and assign to
			$emailtousers = '';
			//email to only OPS team DL
			$emailtoOpsDls = '';
			$emailFlag = '';
			if(count($emailUsers) > 0){
				$explodeCclist = array();
				if(count($data1['cclist']) > 0){
						$explodeCclist = explode(",",$data1['cclist']);
				}
				
				if($preEventInfo['company_id'] != $data1['company_id']){
					$emailtoAll = 'Yes';
					//$emailtousers = 'No';
					//$emailtoOpsDls ='No';
					$emailFlag = 'Yes';
					//$emailAllusers = array_unique(array_merge($emailUsers,$explodeCclist, self::$opsDls));
				}
				//if affected brand updated then mail will send to all opsDL and cc, assign and requestor
				if(($preEventInfo['affected_company_list'] != $arrayBrand['affected_company_list']) || (count($preEventInfo['affected_company_list']) == 0)){
					$emailtoAll = 'Yes';
					//$emailtousers = 'No';
					//$emailtoOpsDls ='No';
					$emailFlag = 'Yes';
					//$emailAllusers = array_unique(array_merge($emailUsers,$explodeCclist, self::$opsDls));
				}
				//if event duration was changed 
				if(($preEventInfo['est_start_datetime'] != $data1['est_start_datetime']) ||($preEventInfo['est_end_datetime'] != $data1['est_end_datetime'])){
				
					$emailtoAll = 'Yes';
					//$emailtousers = 'No';
					//$emailtoOpsDls ='No';
					$emailFlag = 'Yes';
				}
				if($preEventInfo['example_url'] != $data1['example_url']){
					$emailtoAll = 'Yes';
					//$emailtousers = 'No';
					//$emailtoOpsDls ='No';
					$emailFlag = 'Yes';
				}
				if($preEventInfo['anticipated_traffic'] != $data1['anticipated_traffic']){
					//$emailtoAll = 'No';
					//$emailtousers = 'No';
					$emailtoOpsDls ='Yes';
					$emailFlag = 'Yes';
				}
				if($preEventInfo['example_url'] != $data1['example_url']){
					$emailtoAll = 'Yes';
					//$emailtousers = 'No';
					//$emailtoOpsDls ='No';
					$emailFlag = 'Yes';
				}
				/*if($preEventInfo['anticipated_traffic'] != $data1['anticipated_traffic']){
					$emailtoAll = 'Yes';
					$emailtousers = 'No';
					$emailtoOpsDls ='No';
				}*/
				if($preEventInfo['title'] != $data1['title']){
					//$emailtoAll = 'No';
					$emailtousers = 'Yes';
					//$emailtoOpsDls = 'No';
					$emailFlag = 'Yes';
				}
				if($preEventInfo['requested_by'] != $data1['requested_by']){
					//$emailtoAll = 'No';
					$emailtousers = 'Yes';
					//$emailtoOpsDls ='No';
					$emailFlag = 'Yes';
				}
				if($preEventInfo['assigned_to'] != $data['assigned_to']){
					//$emailtoAll = 'No';
					//$emailtousers = 'No';
					$emailtoOpsDls ='Yes';
					$emailFlag = 'Yes';
				}
				if($preEventInfo['status'] != $data['status']){
					$emailtoAll = 'Yes';
					//$emailtousers = 'No';
					//$emailtoOpsDls ='No';
					$emailFlag = 'Yes';
				}
				
				if($emailFlag == 'Yes'){
					if($emailtoAll == 'Yes'){
						$emailAllusers = array_unique(array_merge($emailUsers,$explodeCclist, self::$opsDls));
					}else if($emailtoOpsDls == 'Yes' && $emailtousers == 'Yes'){
						//$emailAllusers = self::$opsDls;
						$emailAllusers = array_unique(array_merge($emailUsers,$explodeCclist, self::$opsDls));
					}else if($emailtoOpsDls == 'Yes'){
						$emailAllusers = self::$opsDls;
						//$emailAllusers = array_unique(array_merge($emailUsers,$explodeCclist));
					}else if($emailtousers ='Yes'){
						$emailAllusers = array_unique(array_merge($emailUsers,$explodeCclist));
					}
					/*echo $emailtoAll;
					echo "<br/>";
					echo $emailtousers;
					echo "<br/>";
					echo $emailtoOpsDls;
					echo "<br/>";
					p($emailAllusers);*/
					foreach($emailAllusers as $emailUserId){
						if(!empty($emailUserId)){
						$userInfo = EventDisplay::getTableData("users","concat_ws(', ',last_name,first_name) as fullname,email","id=$emailUserId");
						$mail = new Zend_Mail('utf-8');
						$mail->addTo($userInfo['email'],$userInfo['fullname']);
						$subject = "Event ".$eventId.": Updated - ". html_entity_decode($data1['title'],ENT_NOQUOTES,'UTF-8') . "";
						$mail->setSubject($subject);
					
						$mail->setFrom(WO_EMAIL_FROM,'lighthouse@nbcuots.com');
						$mail->setBodyHtml($bodyText);
						//p($mail);
						$mail->send();
						}
						//$mail->send();
					}
				}
			}
			}else{	
				
				//Email for new event
				$eventId = EventDisplay::insertEvent($data);
				try{
					
					$brandName = EventDisplay::getTableData('companies', "name", "id=".$data['company_id']);
					$html = new Zend_View();
					$html->setScriptPath(APPLICATION_PATH."/modules/email/views/");	
					$requestedByuser = $data['requested_by'];
					$createoruserInfo = EventDisplay::getTableData("users","concat_ws(', ',last_name,first_name) as fullname,email","id=$requestedByuser");
					$eventEmailInfo = array(
						"requestedBy" => $createoruserInfo['fullname'],
						"brand"		=> $brandName['name'],
						"event_id"	=> $eventId,
						"desc"	=> $data['body'],
					);
					$html->assign('eventInfo',$eventEmailInfo);
					$bodyText = $html->render('addevent.phtml');
					//requestor,creator,assign to
					$emailUsers = array();
					$emailUsers[] = $userId;
					if($userId != $data['requested_by']){
						$emailUsers[] = $data['requested_by'];
					}
					
					$emailUsers[] = $data['assigned_to'];
					
					
					//send email to cc users
					if(count($emailUsers) > 0){
						$explodeCclist = array();
						if(count($data['cclist']) > 0){
							$explodeCclist = explode(",",$data['cclist']);
						}
						$emailAllusers = array_unique(array_merge($emailUsers,$explodeCclist,self::$opsDls));
						
						foreach($emailAllusers as $emailUserId){
							if($emailUserId != ''){
								$userInfo = EventDisplay::getTableData("users","concat_ws(', ',last_name,first_name) as fullname,email","id=$emailUserId");
								$mail = new Zend_Mail('utf-8');
								$mail->addTo($userInfo['email'],$userInfo['fullname']);
								$subject = "Event ".$eventId.": New - ". html_entity_decode($data['title'],ENT_NOQUOTES,'UTF-8') . "";
								$mail->setSubject($subject);
							
								$mail->setFrom(WO_EMAIL_FROM,'lighthouse@nbcuots.com');
								$mail->setBodyHtml($bodyText);
								//p($mail);
								$mail->send();
							}
							//
						}
					}
					//$this->_redirect("events/index/calendarview/?edate=".date("Y-m-d",strtotime($startdate))."#event_date");
				}catch(Exception $e){
					echo $e->getMessage();
				}
			}
			$this->_helper->layout->disableLayout();
		}
		
		public function deleteeventAction(){
			$eventId = $this->_request->getParam('eId');
			$ev_eventStatus = $this->_request->getParam('ev_eventStatus');
			$ev_assigned_to = $this->_request->getParam('ev_assigned_to');
			$userId = $_SESSION['user_id'];
			$dataArray = array('active' => 0,
			 'deleted'=> 1,
			);
			$eventInfo = EventDisplay::getEventInfo($eventId, '*');
			$eventInfo = $eventInfo[0];
			//p($eventInfo);
			######################Delete email##############################################
			$brandName = EventDisplay::getTableData('companies', "name", "id=".$eventInfo['company_id']);
			$html = new Zend_View();
			$html->setScriptPath(APPLICATION_PATH."/modules/email/views/");	
			$requestedByuser = $eventInfo['requested_by'];
			$createoruserInfo = EventDisplay::getTableData("users","concat_ws(', ',last_name,first_name) as fullname,email","id=$requestedByuser");
			$currentUserInfo = EventDisplay::getTableData("users","concat_ws(', ',last_name,first_name) as fullname,email","id=$userId");
			$eventEmailInfo = array(
				"requestedBy" => $createoruserInfo['fullname'],
				"brand"		=> $brandName['name'],
				"event_id"	=> $eventId,
				"desc"	=> $eventInfo['body'],
				"currentUserInfo" => $currentUserInfo['fullname']
			);
			$html->assign('eventInfo',$eventEmailInfo);
			$bodyText = $html->render('deleteevent.phtml');
			//requestor,creator,assign to
			$emailUsers = array();
			$emailUsers[] = $userId;
			if($userId != $eventInfo['requested_by']){
				$emailUsers[] = $eventInfo['requested_by'];
			}
			
			$emailUsers[] = $eventInfo['assigned_to'];
			//send email to cc users
			if(count($emailUsers) > 0){
				$explodeCclist = array();
				if(count($eventInfo['cclist']) > 0){
					$explodeCclist = explode(",",$eventInfo['cclist']);
				}
				$emailAllusers = array_unique(array_merge($emailUsers,$explodeCclist,self::$opsDls));
				foreach($emailAllusers as $emailUserId){
					if(!empty($emailUserId)){
						$userInfo = EventDisplay::getTableData("users","concat_ws(', ',last_name,first_name) as fullname,email","id=$emailUserId");
						$mail = new Zend_Mail('utf-8');
						$mail->addTo($userInfo['email'],$userInfo['fullname']);
						$subject = "Event ".$eventId.": Deleted - ". html_entity_decode($eventInfo['title'],ENT_NOQUOTES,'UTF-8') . "";
						$mail->setSubject($subject);
						//$mail->setFrom($createoruserInfo['email'],$createoruserInfo['fullname']);
						$mail->setFrom(WO_EMAIL_FROM,'lighthouse@nbcuots.com');
						$mail->setBodyHtml($bodyText);
						//p($mail);
						$mail->send();
					}
					
				}
			}
			################################################################################
			EventDisplay::deleteEvent($dataArray, $eventId);
			EventDisplay::deleteEventAudit($ev_eventStatus,$ev_assigned_to , $eventId,$userId);	
			$this->_helper->layout->disableLayout();
		}
		
		public function addcommentAction(){
			$comment = $_POST['comment'];
			$ev_assigned_to = $_POST['ev_assigned_to'];
			$ev_eventStatus = $_POST['ev_eventStatus'];
			$event_id = $_POST['event_id'];
			$userId = $_SESSION['user_id'];
			$commanrArray = array(
				"event_id" =>$event_id,
				"user_id" => $userId,
				"comment" => Util::escapewordquotes($comment),
				"date"	  => date("Y-m-d H:i:s"),
				'active'  => 1,
				'deleted'  => 0
			);	
			
			
			$commentid = EventDisplay::addComment($commanrArray);
			#################################3Comment Email#########################
			if($commentid > 0){
				$aduitId = 'Comment Added';
				EventDisplay::addCommentAudit($event_id,$aduitId, $userId,$ev_assigned_to,$ev_eventStatus);
				 //send mail to all cc user and requested by and assigned to users 
				 $eventInfo = EventDisplay::getTableData("events","*","id=$event_id");
				 $requestorInfo = EventDisplay::getTableData("users","concat_ws(', ',last_name,first_name) as fullname,email","id=".$eventInfo['requested_by']);
				 $brandName = EventDisplay::getTableData('companies', "name", "id=".$eventInfo['company_id']);
				 $comment_body = array();
				 $comment_body = array(
				 "event_id" => $event_id,
				 "latest_commet" => $comment,
				 "requestor" => $requestorInfo['fullname'],
				 "brand"   =>   $brandName['name'],
				// "affected_brand" => $eventInfo['affected_company_list'],
				 "body" => $eventInfo['body']
				 );
				 $emailusers = array();
				 $emailusers = @explode(",", $eventInfo['cclist']);
				 //requestor
				 $emailusers[] = $eventInfo['requested_by'];
				 $emailusers[] = $eventInfo['assigned_to'];
				 if($eventInfo['requested_by'] != $userId){
				 	 $emailusers[] = $userId;
				 }
				 $emailusers = array_unique(array_merge_recursive($emailusers, self::$opsDls));
				 //p($emailusers);
				 $html = new Zend_View();
				 $html->setScriptPath(APPLICATION_PATH."/modules/email/views/");	
				 $html->assign('eventInfo',$comment_body);
				
				 //sender info
				 $senderInfo = EventDisplay::getTableData("users","concat_ws(', ',last_name,first_name) as fullname,email","id=$userId");
				 $html->assign('commentorInfo',$senderInfo);
				 // render view
				 $bodyText = $html->render('commentevent.phtml');
				 // configure base stuff
				
				 foreach($emailusers as $emailUserId){
				 	if($emailUserId != ''){
					 	$userInfo = EventDisplay::getTableData("users","concat_ws(', ',last_name,first_name) as fullname,email","id=$emailUserId");
						$mail = new Zend_Mail('utf-8');
						$mail->addTo($userInfo['email'],$userInfo['fullname']);
						$subject = "Event ".$event_id.": Comment - ". html_entity_decode($eventInfo['title'],ENT_NOQUOTES,'UTF-8') . "";
						$mail->setSubject($subject);
						
						$mail->setFrom($senderInfo['email'],$senderInfo['fullname']);
						$mail->setBodyHtml($bodyText);
						//p($mail);
						$mail->send();
				 	}
					 
				 }
				 
				 //End Email
				
			}
			#############################################################
			$commentInfo = EventDisplay::getComments($event_id, '*');
			$this->view->assign("commentInfo", $commentInfo);
			
			$this->_helper->layout->disableLayout();
		}
		
		public function checkdateAction(){
			$startdate = Util::dateTimeToSql($_POST['ev_start_date'],$_POST['ev_start_hr'],$_POST['ev_ampm'],$_POST['ev_start_min']);
			$enddate = Util::dateTimeToSql($_POST['ev_end_date'],$_POST['ev_end_hr'],$_POST['ev_end_ampm'],$_POST['ev_end_min']);
			$timestamp_start_time = strtotime($startdate);
			$timestamp_end_time = strtotime($enddate);
			if($timestamp_start_time < $timestamp_end_time){
				echo "ok";
			}else{
				echo "failed";
			}
			$this->_helper->layout->disableLayout();
		}
		
		public function detailAction(){
			$userId = $_SESSION['user_id'];
			$userComapny = $_SESSION['company'];
			$this->view->assign("User_id", $userId);
			$userInfo = array();
			//operation team company
						
			//for affected brand
			$this->view->assign('affectedBrand',"disabled='disabled'");
			$this->view->assign('ev_dimmer',"block");
			
			if(isset($_GET['eId'])) {
				$eId = $_GET['eId'];   
				$eventInfo = EventDisplay::getEventInfo($eId, '*');
				
				
				if(count($eventInfo) == 0){
					//redirect to event listing page
					$this->_redirect("events");
				}
				//list comment
				$commentInfo = EventDisplay::getComments($eId, '*');
				$this->view->assign("commentInfo", $commentInfo);
				$this->view->assign('disabled',"disabled='disabled'");
				$this->view->assign('ev_buttons',"none");
				
				$this->view->assign('eventInfo',$eventInfo[0]);
				$this->view->assign('eId', $eId);
				if(count($eventInfo) > 0){
					$this->view->assign('cclist',$eventInfo[0]['cclist']);
					$ccListArray = explode(",", $eventInfo[0]['cclist']);
					//p($ccListArray);
					foreach($ccListArray as $ccKey => $ccVal){
						if(!empty($ccVal)){
							$userInfo[] = EventDisplay::getRequestorInfo($ccVal);
						}
						//$ccListArray =  	
					}
					$this->view->assign('cc_user_row', $userInfo);
					
				}
			}
				
			
		}
		
		public function listdataAction(){
			$month = $_GET['month'];
			$this->view->assign('company_id', $_GET['company']);
			$status_id = $_GET['status'];
			$monthnameHtml = EventDisplay::getMonthHTML($month);
			
			$this->view->assign("monthnameHtml", $monthnameHtml);
			
			$eventStatus = EventDisplay::getEventStatusList($status_id);
			$this->view->assign("eventStatus", $eventStatus);
			echo $year =  $_GET['year'];
			
			$yearList = EventDisplay::getyearHTML($year);
			$this->view->assign("yearList", $yearList);
			
			$query="SELECT * FROM events WHERE active ='1' AND deleted = '0'";
			
			
			if(isset($_GET['month']) && $_GET['month']!="All"):
			 	$query .=" AND (MONTH(est_start_datetime)= ".$_GET['month'];
			 	$query .=" OR MONTH(est_end_datetime)= ".$_GET['month'].")";
			endif;
			
			if((isset($_GET['year']) && $_GET['year']!="All")):
			 	$query .=" AND (YEAR(est_start_datetime)= ".$_GET['year'];
			 	$query .=" OR YEAR(est_end_datetime)= ".$_GET['year'].")";
			 	$flag=false;
			endif;
			
			if((isset($_GET['company']) && $_GET['company']!="-1")):
				$query .=" AND (FIND_IN_SET( ".$_GET['company'].", affected_company_list ) !=0";
				$query .=" OR company_id =".$_GET['company'].")";
			endif;
			
			if((isset($_GET['status']) && $_GET['status']!="0")):
				$query .=" AND status = ".$_GET['status'];
			endif;
			
			if(isset($_GET['pagenum'])):
				$pagenum = $_GET['pagenum'];
			endif;
			
			$totalResult = EventDisplay::getQuery($query);
			$totalData = count($totalResult);
			
			$this->view->assign("totalData", $totalData);
			
			$page_rows = EventDisplay::$page_row;
						
			$lastpage = ceil($totalData/$page_rows);
			
			if($pagenum<1):
				$pagenum=1;
			elseif($pagenum>$lastpage):
				$pagenum = $lastpage;
			endif;
			
			$this->view->assign("pagenum", $pagenum);
			$this->view->assign("lastpage", $lastpage);
			
			$query .=" ORDER BY est_start_datetime DESC";
			
			$max = " limit " .($pagenum - 1) * $page_rows ."," .$page_rows; 

			$query .= $max;
			
			//echo $query;
				
			$result = EventDisplay::getQuery($query);
			$this->view->assign("data",$result);
			
			$this->_helper->layout->disableLayout();
			
		}

		public function listviewAction(){
			
			if(isset($_GET['p']) && $_GET['p']=='ajax'):
				
				$userId = $_SESSION['user_id'];
				
				$userCompany = $_SESSION['company'];
				
				$editEventFlag=false;
				
				$this->view->assign("userId", $userId); 
				
				if(in_array($userCompany,self::$opsComapny)):
					$editEventFlag=true;	
				endif;
			
				$this->view->assign("editEventFlag", $editEventFlag);
				
			   	$month = $_GET['month'];
			   	$this->view->assign("month", $month);
			   	
				$this->view->assign('company_id', $_GET['company']);
				$status_id = $_GET['status'];
				
				$monthnameHtml = EventDisplay::getMonthHTML($month);
				
				$this->view->assign("monthnameHtml", $monthnameHtml);
				
				$eventStatus = EventDisplay::getEventStatusList($status_id);
				
				$this->view->assign("eventStatus", $eventStatus);
				$year =  $_GET['year'];
				$this->view->assign("year", $year);
				
				$yearList = EventDisplay::getyearHTML($year);
				$this->view->assign("yearList", $yearList);
				
				$query="SELECT * FROM events WHERE active ='1' AND deleted = '0'";
			
				
				
				if(isset($_GET['month']) && $_GET['month']!="All"):
				 	$query .=" AND (MONTH(est_start_datetime)= ".$_GET['month'];
				 	$query .=" OR MONTH(est_end_datetime)= ".$_GET['month'].")";
				endif;
				
				if((isset($_GET['year']) && $_GET['year']!="All")):
				 	$query .=" AND (YEAR(est_start_datetime)= ".$_GET['year'];
				 	$query .=" OR YEAR(est_end_datetime)= ".$_GET['year'].")";
				endif;
				
				if((isset($_GET['company']) && $_GET['company']!="-1")):
					$query .=" AND (FIND_IN_SET( ".$_GET['company'].", affected_company_list ) !=0";
					$query .=" OR company_id =".$_GET['company'].")";
				endif;
				
				if((isset($_GET['status']) && $_GET['status']!="0")):
					$query .=" AND status = ".$_GET['status'];
				endif;
				
				if(isset($_GET['pagenum'])):
					$pagenum = $_GET['pagenum'];
				endif;
				
				$totalResult = EventDisplay::getQuery($query);
				$totalData = count($totalResult);
				
				$this->view->assign("totalData", $totalData);
				
				$page_rows = EventDisplay::$page_row;
							
				$lastpage = ceil($totalData/$page_rows);
				
				if($pagenum<1):
					$pagenum=1;
				elseif($pagenum>$lastpage):
					$pagenum = $lastpage;
				endif;
				
				$this->view->assign("pagenum", $pagenum);
				$this->view->assign("lastpage", $lastpage);
				
				$query .=" ORDER BY CASE WHEN UNIX_TIMESTAMP(est_start_datetime) > UNIX_TIMESTAMP(NOW()) THEN est_start_datetime END DESC, CASE WHEN UNIX_TIMESTAMP(est_start_datetime)< UNIX_TIMESTAMP(NOW()) THEN est_start_datetime END DESC";
				
				$max = " limit " .($pagenum - 1) * $page_rows ."," .$page_rows; 
	
				$query .= $max;
								
				//echo $query;
					
				$result = EventDisplay::getQuery($query);
				$this->view->assign("data",$result);
				
				$this->_helper->layout->disableLayout();
			else:
				
				$userId = $_SESSION['user_id'];
				$userCompany = $_SESSION['company'];
				
				$editEventFlag=false;
				
				$this->view->assign("userId", $userId); 
				
				if(in_array($userCompany,self::$opsComapny)):
					$editEventFlag=true;	
				endif;
			
				$this->view->assign("editEventFlag", $editEventFlag);
				
				$date = $this->_getParam("date");
				if(isset($date) && $date!=""):
					list($month,$day,$year)=explode("-",$date);
				else:
					list($month,$day,$year)=explode("-",date('m-d-Y',time()));
				endif;	
				
				$this->view->assign("month", $month);
				
				$monthnameHtml = EventDisplay::getMonthHTML($month);
				$this->view->assign("monthnameHtml", $monthnameHtml);
				
				$eventStatus = EventDisplay::getEventStatusList();
				$this->view->assign("eventStatus", $eventStatus);
				
				$this->view->assign("year", $year);
				$yearList = EventDisplay::getyearHTML($year);
				$this->view->assign("yearList", $yearList);
				
				$query="SELECT * FROM events WHERE active ='1' AND deleted = '0'";
				
				$query .=" AND (MONTH(est_start_datetime)= ".$month;
				$query .=" OR MONTH(est_end_datetime)= ".$month.")";
				$query .=" AND (YEAR(est_start_datetime)= ".$year;
				$query .=" OR YEAR(est_end_datetime)= ".$year.")";
				if(isset($date) && $date!=""):
					$query .=" AND DAY(est_start_datetime)= ".$day;
				endif;
				
				$totalResult = EventDisplay::getQuery($query);
				$totalData = count($totalResult);
				
				$this->view->assign("totalData", $totalData);
				
				$pagenum = 1;
				$this->view->assign("pagenum", $pagenum);
				
				$page_rows = EventDisplay::$page_row;
				
				$lastpage = ceil($totalData/$page_rows);
				$this->view->assign("lastpage", $lastpage);
				
				//$query .=" ORDER BY est_start_datetime (CASE WHEN (UNIX_TIMESTAMP(est_start_datetime)> NOW()) DESC ELSE ASC END)";
				$query .=" ORDER BY CASE WHEN UNIX_TIMESTAMP(est_start_datetime) > UNIX_TIMESTAMP(NOW()) THEN est_start_datetime END DESC, CASE WHEN UNIX_TIMESTAMP(est_start_datetime)< UNIX_TIMESTAMP(NOW()) THEN est_start_datetime END DESC";
											 
				$max = " limit " .($pagenum - 1) * $page_rows ."," .$page_rows;
				
	
				$query .= $max;
				
				
				//echo $query;
				
										
				$result = EventDisplay::getQuery($query);
				
				
				
				
				$this->view->assign("data",$result);
				
			endif;
				
		}		
		public function calendarviewAction()
			{
				$p ='';
				$p = $this->_request->getParam('p');
				$year = $this->_request->getParam('year');
				$comp_id = $this->_request->getParam('comp_id');
								
				
				if($p=='ajax'){
					$this->view->assign('calendar_yr',$year);
					$this->view->assign('comp_id',$comp_id);
					
					$yearList = EventDisplay::getCalyearHTML($year);
					$this->view->assign("yearList", $yearList);
				
					$evntInfo = EventDisplay::get_upcoming_event($year,$comp_id);
					$this->_helper->layout->disableLayout();
					
					
				}else{
					   $date = $this->_getParam("edate");
                        if(isset($date) && $date!=""):
                        	 $dateArray = explode("#",$date);	
                        	  $year = trim(date("Y",strtotime($dateArray[0])));
                              
                        else:
                              $year = date("Y");
                        endif;
                       // echo $year;
                    $yearList = EventDisplay::getCalyearHTML($year);
					//$yearList = EventDisplay::getCalviewyearHTML($year);
                        $this->view->assign("yearList", $yearList);
					
					$this->view->assign('calendar_yr',$year);
					$this->view->assign('comp_id',-1);
					$evntInfo = EventDisplay::get_upcoming_event($year,-1);
				}
				$this->view->assign('evntInfo',$evntInfo);
				
			
			
			}
			public function calendarviewajaxAction()
			{
				$userId = $_SESSION['user_id'];
			    $userCompany = $_SESSION['company'];
			    if(!ISSET($_GET['slider_date'])){
			    	$year = date('Y-m');
			    }else{
			    	$year = $_GET['slider_date'];
			    }
			    
			    $editEventFlag = false;
			    
			    $this->view->assign("userId", $userId);
			    $this->view->assign("selectedYear", $year);
			    if(in_array($userCompany,self::$opsComapny)):
			    	$editEventFlag = true; 
			    endif;
			    
			    $this->view->assign("editEventFlag", $editEventFlag);
			    $this->_helper->layout->disableLayout();
			}
		public function eventauditAction(){
			$eId = $_GET['eId']; 
			$db = Zend_Registry::get('db');
			$sql = "SELECT *  FROM event_audit WHERE  event_id  = '$eId' order by log_date"; 
			$rs = $db->fetchAll($sql);
			print "<pre>";
			print_r($rs);
			print"</pre>";
			//die;
			
		}
		
		
	}		
?>
