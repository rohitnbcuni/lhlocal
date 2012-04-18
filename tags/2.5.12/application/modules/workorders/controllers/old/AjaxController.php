<?php

class Workorders_AjaxController extends Zend_Controller_Action
{
	var $que_mgr;
	var $nbcuxd_id;
	var $bc;
	var $username;

	public function init()
		
	{
		$config = Zend_Registry::get('config');
		$this->nbcuxd_id = $config->basecamp->nbcuxd_id;
		
		$credentials = new Zend_Session_Namespace('basecamp');	
		$this->username = $credentials->username;
		$this->bc = new phpBasecamp($credentials->username,$credentials->password,$config->basecamp->host);
		$this->que_mgr = $this->bc->person($config->basecamp->que_mgr);		
	}	
	
 public function archiveAction() 
 
{
	    $this->_helper->layout->disableLayout();    //disable layout 
        $this->_helper->viewRenderer->setNoRender(); //suppress auto-rendering
         
	    $params = $this->_request->getParams();
	    $id = $params["id"];
		$wodb = new Workorders();
                $workorder = $wodb->fetchRow('id='.$id);
                //Zend_Debug::dump($workorder);die;
	
	
	        try {
				if (!$this->_request->isGet()) {
					throw new Exception('Invalid action. Not a Get');
				}
				$workorder->archived = $workorder->archived ? 0 : 1;
				$workorder->save();
				$this->_forward('list');
            	            	            	
			} catch (Exception $e) {
			   Zend_Debug::dump($e->getMessage());
			   
			   
			}

}

 public function statusAction() 
 
{
	    $this->_helper->layout->disableLayout();    //disable layout 
        $this->_helper->viewRenderer->setNoRender(); //suppress auto-rendering
        $params = $this->_request->getParams();
        $id = $params['code']; 
	    $status = $params["value"];
	    //$name = $params["name"];
	    //Zend_Debug::dump($params);die;
		$wodb = new Workorders();
                $workorder = $wodb->fetchRow('id='.$id);
                //Zend_Debug::dump($workorder);die;
	
	
	        try {
				if (!$this->_request->isPost()) {
					throw new Exception('Invalid action. Not a Get');
				}
				$workorder->status = $status;
				$workorder->save();
				print $status;
            	            	            	
			} catch (Exception $e) {
			   Zend_Debug::dump($e->getMessage());die;			   			   
			}

}


 public function assignAction() 
 
{
	    $this->_helper->layout->disableLayout();    //disable layout 
        $this->_helper->viewRenderer->setNoRender(); //suppress auto-rendering
        $params = $this->_request->getParams();
        $id = $params['code']; 
	    $bcid = $params["value"];
	    $name = $params["name"];
	    //Zend_Debug::dump($params);die;
		$wodb = new Workorders();
                $workorder = $wodb->fetchRow('id='.$id);
                //Zend_Debug::dump($workorder);die;
	
	
	        try {
				if (!$this->_request->isPost()) {
					throw new Exception('Invalid action. Not a Get');
				}
				$old_bcid = $workorder->assigned_to;
				$workorder->assigned_to = $bcid;
				$workorder->save();
				
			    $resource = $this->bc->person($bcid);
                $resource_name = sprintf("%s %s", $resource->{'first-name'}, $resource->{'last-name'});
                $que_mgr_name = sprintf("%s %s", $this->que_mgr->{'first-name'}, $this->que_mgr->{'last-name'});
                
				$first_name = $resource->{"first-name"};

               
				$ref = sprintf('%s/workorders/index/edit/id/%s',$_SERVER['SERVER_NAME'],$id);
				
				$html = <<<EOD
				Hi $first_name,<br>
				
				<p>You have been assigned a new workorder in Lighthouse. 
								
				<p>Title : <a href="http://$ref">$workorder->title</a> 
				
				<p>Description: $workorder->body
				
				<p>Status: $workorder->priority
				
EOD
;  


                $update = array('category_id' => $workorder->category_id);  
                
                if($old_bcid == $this->que_mgr->id) {              
               
                  $notify = array($bcid, $this->que_mgr->id, $workorder->requested_by);
                
                } else {
                   $notify = array($old_bcid, $bcid, $this->que_mgr->id, $workorder->requested_by);
                }
               
     			$response = $this->bc->update_message($workorder->bcid, $update, $notify); 
                                
                $bcdata = array(
                'post_id' => $workorder->bcid,
                'body' => $html
                );                
                              
     			$response = $this->bc->create_comment($bcdata); 

     		    if(is_object($response)) {
     		    
     		    	
					$mail = new Zend_Mail();
					$mail->setBodyHtml($html);
					$mail->setFrom($this->que_mgr->{"email-address"},$que_mgr_name);
					$mail->addTo($resource->{"email-address"}, $resource_name);
					$mail->addCC($this->que_mgr->{"email-address"},$que_mgr_name);
					$mail->addCC("wardwelch@gmail.com","Ward Welch");				
					$mail->setSubject("$workorder->title");
					$mail->send();

				}

				print $name;            	            	            	
			} catch (Exception $e) {
			   Zend_Debug::dump($e->getMessage());die;			   			   
			}

}



    public function __call($name, $args)
    {
        throw new Exception('Sorry, the requested action does not exist');
    }

}
