<?php
class Workorders_IndexController extends Zend_Controller_Action {

	var $person;
	var $projects;
	var $companies;
	var $resources;
	var $que_mgr;
	var $nbcuxd_id;
	var $bc;
	var $username;
	var $name;

	var $function_name;

	public function init() {
		//$config = Zend_Registry :: get('config');
		$config = new Zend_Config_Ini(
				    APPPATH . '/config/app.ini', 
				    APPLICATION_ENVIRONMENT
				);
		$this->nbcuxd_id = $config->basecamp->nbcuxd_id;
		
		
		$credentials = new Zend_Session_Namespace('basecamp');
		
		if(is_null($credentials->username)) {
			//$this->_redirect("/login");
		}
		//echo "here";
		//die();
			
		
		$this->username = $credentials->username;
		$this->cache = Zend_Cache :: factory('Core', 'File', array (
			'caching' => false,
			'lifetime' => 7200,
			'automatic_serialization' => true
		), array (
			'cache_dir' => "/tmp/",
		"file_name_prefix" => session_id()));

		$this->bc = new phpBasecamp($credentials->username, $credentials->password, $config->basecamp->host);

		$this->que_mgr = $this->bc->person($config->basecamp->que_mgr);

		$this->load("projects");

		//Zend_Debug::dump($this->projects, 'projects');

		$this->load("companies");

		//Zend_Debug::dump($this->companies, 'companies');

		//$this->load("categories");

		//Zend_Debug::dump($this->categories,'categories');

		$this->load("resources");

		//Zend_Debug::dump($this->resources, 'resources');

		$this->load("person");

		//Zend_Debug::dump($this->person, 'person');

		$this->name = sprintf("%s %s", $this->person-> {
			"first-name" }, $this->person-> {
			"last-name" });
	}
	

	public function load($var) {

		if (@ $this-> {
			"var" }
		=== null) {
			$caps = ucfirst($var);

			$this->function_name = "get$caps";

			if (!$this-> $var = $this->cache->load("var")) {
				$this-> $var = $this-> {
					$this->function_name }
				();

				$this->cache->save($this-> $var, "$var");

			} else {

				print "I gotta hit!";

			}

		}
	}

	public function getPerson() {
		//Zend_Debug::dump($this->companies);
		foreach ($this->companies as $key => $val) {
			$people = $this->bc->people($key);

			//Zend_Debug::dump($people);

			if (is_array($people)) {
				foreach ($people as $person) {
					if (isset ($person-> {
						"user-name" }) && (strtoupper($person-> {
						"user-name" }) == strtoupper($this->username))) {
						return $person;
					}
				}
			}
		}
	}

	public function getProjects() {
		return $this->bc->projects();
	}

	public function getPeoplePerProject($project_id, $company_id) {
		return $this->bc->people_per_project($project_id, $company_id);
	}

	public function getCompanies() {
		foreach ($this->projects as $project) {
			$companies[$project->company->id] = array (
				"id" => $project->company->id,
				"name" => $project->company->name
			);
		}

		return $companies;
	}

	public function getWorkorderCategory($project) {

		$categories = $this->bc->message_categories($project);
		foreach ($categories as $category) {
			if ($category->name == 'Workorder') {
				return $category->id;
			}

		}
		print "Error: You need a category in Basecamp called Workorder";
		die;
	}

	public function getResources() {
		return $this->bc->people($this->nbcuxd_id);
	}

	public function getComments($message_id) {
		return $this->bc->comments($message_id);
	}

	public function indexAction() {

		if (App_Auth_Adapter_Basecamp :: isClient()) {
			$this->_forward('list');
		} else {
			$this->_forward('list');
		}

	}

	public function archiveAction() {
		$this->_helper->layout->disableLayout(); //disable layout
		$this->_helper->viewRenderer->setNoRender(); //suppress auto-rendering

		$params = $this->_request->getParams();
		$id = $params["id"];
		$wodb = new Workorders();
		$workorder = $wodb->fetchRow('id=' . $id);
		//Zend_Debug::dump($workorder);die;

		try {
			if (!$this->_request->isGet()) {
				throw new Exception('Invalid action. Not a Get');
			}
			$workorder->archived = $workorder->archived ? 0 : 1;
			$workorder->save();
			$this->_forward('list');

		} catch (Exception $e) {
			Zend_Debug :: dump($e->getMessage());

		}

	}

	public function assignAction() {
		$this->_helper->layout->disableLayout(); //disable layout
		$this->_helper->viewRenderer->setNoRender(); //suppress auto-rendering
		$params = $this->_request->getParams();
		$id = $params['woid'];
		$bcid = $params["bcid"];
		$resource = $params['resource'];
		//Zend_Debug::dump($params);die;
		$wodb = new Workorders();
		$workorder = $wodb->fetchRow('id=' . $id);
		//Zend_Debug::dump($workorder);die;

		try {
			if (!$this->_request->isPost()) {
				throw new Exception('Invalid action. Not a Get');
			}
			$workorder->assigned_to = $bcid;
			$workorder->save();
			print $resource;

		} catch (Exception $e) {
			Zend_Debug :: dump($e->getMessage());
			die;
		}

	}

	public function listAction() {
$session = new Zend_Session_Namespace("basecamp");

		if ($this->_request->isGet()) {
			$params = $this->_request->getParams();
			$filter = isset ($params['filter']) ? $params['filter'] : $session->filter;
			$session->filter = $filter;
		} else {
			$filter = "Assigned to Me";
			$session->filter = $filter;
		}

		$config = Zend_Registry :: get('config');
		$list = array ();

		$db = Zend_Db :: factory($config->database->adapter, $config->database->params->toArray());

		foreach ($this->projects as $project) {

			$select = $db->select()->from("v_workorders");

			switch ($filter) {

				case "New" :

					$select->Where("status = 'New'");
					//$select->orWhere("status is NULL");

					break;

				case "Open" :

					$select->where("status != 'Closed'");

					break;

				case "Closed" :

					$select->where("status = 'Closed'");

					break;

				case "Assigned to Me" :

					$select->where("assigned_to = ?", $this->person->id);

					break;

				case "Requested by Me" :

					$select->where("requested_by = ?", $this->person->id);

					break;

				case "Oldies" :

					$select->where("est_due_date < ?", date('Y-m-d'));
					$select->where("status not in ('Closed','Hold')");

					break;

			}

			$select->where("project_id = ? ", $project->id);
			if ($filter != "Archived") {
				$select->where("archived = ? ", 0);
			}

			$wodb = $db->query($select);
			$db->setFetchMode(Zend_Db :: FETCH_OBJ);
			$local = $wodb->fetchAll();

			if (!empty ($local))
				$list[$project->company->id][] = array (
					"company" => $project->company->name,
					"project" => $project->name,
					"id" => $project->id,
					"status" => $project->status,
					"workorders" => $local
				);

		}

		foreach ($this->resources as $person) {
			$options2[$person->id] = $person-> {
				'last-name' }
			 . ", " . $person-> {
				'first-name' };
		}

		asort($options2);

		$this->view->resources = $options2;

		$wodb = new Workorders();

		$this->view->status = $wodb->getEnums('status');

		$this->view->isClient = $session->isClient;
		$this->view->filter = $filter;
		$this->view->companies = $this->companies;

		$this->view->projects = $list;

	}

function editAction() {
		$this->view->title = "Edit Workorder";

		$form = new WorkorderForm();
		$form->submit->setLabel('Save');

if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				$workorders = new Workorders();
				$id = (int) $form->getValue('id');
				$row = $workorders->fetchRow('id=' . $id);
				$assigned_to = $row->assigned_to;
				$status = $row->status;
				$resource = $this->bc->person($form->getValue('assigned_to'));
				$row->bcid = $form->getValue('bcid');
				$row->project_id = $form->getValue('project_id');
				$row->category_id = $form->getValue('category_id');
				$row->type = $form->getValue('type');
				$row->status = $form->getValue('status');
				$row->priority = $form->getValue('priority');
				$row->est_due_date = $form->getValue('est_due_date');
				$row->title = $form->getValue('title');
				$row->body = $form->getValue('body');
				$row->extended_body = $form->getValue('extended_body');
				$row->requested_by = $form->getValue('requested_by');
				$row->assigned_to = $form->getValue('assigned_to');
				$row->cclist = $form->getValue('cclist');
				$row->start_date = $form->getValue('start_date');
				$row->closed_date = $form->getValue('closed_date');
				$row->start_date = $form->getValue('start_date');
				$row->save();

				$resource_name = sprintf("%s %s", $resource-> {
					'first-name' }, $resource-> {
					'last-name' });
				$que_mgr_name = sprintf("%s %s", $this->que_mgr-> {
					'first-name' }, $this->que_mgr-> {
					'last-name' });

				$first_name = $resource-> {
					"first-name" };
				$ref = sprintf('%s%s', $_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI']);

				if ($status != $row->status) {
					$html =<<<EOD
					<p>The ticket status has changed. From $status to $row->status. Please note.
					<p>Title : <a href="http://$ref">$row->title</a>
					<p>Description: $row->body
					<p>Priority: $row->priority
EOD;
				}

				if ($assigned_to != $row->assigned_to) {

					$html =<<<EOD
				Hi $first_name,<br>

				<p>You have been assigned a new workorder in Lighthouse.

				<p>Title : <a href="http://$ref">$row->title</a>

				<p>Description: $row->body

				<p>Status: $row->priority

EOD;
				}

				$update = array (
					'category_id' => $row->category_id
				);

				$cclist = explode("|", $row->cclist);

				$notify = array_merge(array (
					$resource->id,
					$this->que_mgr->id,
					$row->requested_by
				), $cclist);

				$response = $this->bc->update_message($row->bcid, $update, $notify);

				$bcdata = array (
					'post_id' => $row->bcid,
					'body' => $html
				);

				$response = $this->bc->create_comment($bcdata);

				if (is_object($response)) {

					$mail = new Zend_Mail();
					$mail->setBodyHtml($html);
					$mail->setFrom($this->que_mgr-> {
						"email-address" }, $que_mgr_name);
					$mail->addTo($resource-> {
						"email-address" }, $resource_name);
					$mail->addCC($this->que_mgr-> {
						"email-address" }, $que_mgr_name);
					$mail->addCC("wardwelch@gmail.com", "Ward Welch");
					$mail->setSubject("$row->title");
					//$mail->send();

					$this->_redirect('/workorders/index/list');
				} else {
					Zend_Debug :: dump($response);
					die;
					$form->populate($formData);
				}
			$this->_redirect('/workorders/index/list');
		} else {
			$form->populate($formData);
		}
		}else{
			// workorder id is expected in $params['id']
			$id = (int) $this->_request->getParam('id', 0);
		if ($id > 0) {
			$wodb = new Workorders();
			$workorder = $wodb->fetchRow('id=' . $id);
			//Zend_Debug::dump($workorder);die;
			$project_id = $workorder->project_id;
			$form->populate($workorder->toArray());
			foreach ($this->projects as $project) {
				$options1[$project->id] = $project->name;
				if ($project->id == $project_id) {

					$company_id = $project->company->id;
				}
			}
			$form->project_id->setMultiOptions(array (
				$workorder->project_id => $options1[$workorder->project_id]
			));
			$form->project_id->setValue($workorder->project_id);
			$requester = $this->bc->person($workorder->requested_by);
			$requester->name = sprintf("%s %s", $requester-> {
				"first-name" }, $requester-> {
				"last-name" });
			$comments = $this->getComments($workorder->bcid);
			$people = $this->bc->people_per_project($workorder->project_id, $company_id);
			foreach ($people as $peep) {
				$options3[$peep->id] = $peep-> {
					'last-name' }
				 . ",&nbsp;" . $peep-> {
					'first-name' };
			}
			asort($options3);
			$form->requested_by->setMultiOptions(array (
				$workorder->requested_by => $requester->name
			));
			$form->requested_by->setValue($workorder->requested_by);

			foreach ($this->resources as $person) {
				$options2[$person->id] = $person-> {
					'last-name' }
				 . ", " . $person-> {
					'first-name' };
				//if(strtoupper($this->username) == strtoupper($person->{'user-name'})) $this->person =  $person;
			}

			asort($options2);

			$form->assigned_to->setMultiOptions($options2);
			$form->assigned_to->setValue($workorder->assigned_to);

			$form->type->setMultiOptions($wodb->getEnums('type'));
			$form->type->setValue($workorder->type);

			$form->priority->setMultiOptions($wodb->getEnums('priority'));
			$form->priority->setValue($workorder->priority);

			$form->extra_resources->setMultiOptions($wodb->getEnums('extra_resources'));
			$form->extra_resources->setValue($workorder->extra_resources);

			$form->status->setMultiOptions($wodb->getEnums('status'));
			$form->status->setValue($workorder->status);
			if (is_object($comments)) {
				$this->view->comments = array (
					$comments
				);

			} else {
				$this->view->comments = $comments;
			}
			$this->view->form = $form;
			$this->view->requester = $requester;
			if(!empty($workorder->cclist)) {
				$this->view->ccArray = explode("|", $workorder->cclist);
			}
			$this->view->people = $options3;
			$this->view->bc = $this->bc;
			}
		}
	}

	public function preDispatch() {

		$auth = Zend_Auth :: getInstance();

		if (!$auth->hasIdentity()) {
			$this->_redirect("/login");

		}

	}

	public function deleteAction() {
		$this->view->title = "Delete Workorder Confirm";

		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($formData['del'] == 'Yes') {
				$workorders = new Workorders();
				$id = (int) $formData['id'];
				$workorder = $workorders->fetchRow($workorders->select()->where('id = ?', $id));
				Zend_Debug :: dump($workorder->bcid);
				$response = $this->bc->delete_message($workorder->bcid);

				if (is_object($response)) {
					$workorder->delete();
					Zend_Debug :: dump($response);
					die;
					$this->_redirect('/workorders/index/list');
				} else {
					Zend_Debug :: dump($response);
					die;
				}
			}

		} else {
			// workorder id is expected in $params['id']
			$id = (int) $this->_request->getParam('id', 0);
			if ($id > 0) {
				$wodb = new Workorders();
				$workorder = $wodb->fetchRow('id=' . $id);
				$this->view->workorder = $workorder;
			}
		}

	}

	public function createAction() {

		$date = new Zend_Date();
		$this->view->title = "Add New Workorder";

		$form = new WorkorderForm();
		$form->setDecorators(array (
			array (
				'viewScript',
				array (
					'viewScript' => 'index/create.phtml'
				)
			)
		));

		if ($this->_request->isPost()) {
			$formData = $this->_request->getPost();
			if ($form->isValid($formData)) {
				$wodb = new Workorders();
				$category_id = $this->getWorkOrderCategory($formData['project_id']);
				$data = array (
					'bcid' => $formData['bcid'],
					'project_id' => $formData['project_id'],
					'category_id' => $category_id,
					'type' => $formData['type'],
					'status' => $formData['status'],
					'priority' => $formData['priority'],
					'est_due_date' => $formData['est_due_date'],
					'title' => (!strpos("WO:",
					$formData['title']
				) ? "WO: " : "") . $formData['title'] . ' [' . $formData['priority'] . ']', 'body' => $formData['body'], 'file' => $_FILES['file']['name'], 'requested_by' => $formData['requested_by'], 'assigned_to' => $formData['assigned_to'], 'extra_resources' => $formData['extra_resources'], 'start_date' => $date->getIso(), 'created_date' => $date->getIso(), 'modified_date' => $date->getIso(), 'created_by' => $this->bc->user, 'modified_by' => $this->bc->user);

				$bcdata = array (
					'project_id' => $data['project_id'],
					'category_id' => $category_id,
					'title' => $data['title'],
					'body' => $data['body'],
					'extended_body' => ''
				);
				$client = new Zend_Http_Client("http://nbc.grouphub.com/upload");
				$client->setHeaders(array (
					'Host: nbcuxd.grouphub.com',
					'Accept: application/xml',
					'Content-Type: application/octet-stream',
					'Content-Length: ' . $_FILES['file']['size']
				));
				$stream = file_get_contents($_FILES['file']['tmp_name']);
				$client->setRawData($stream, 'text/xml')->request('POST');

				$response = $client->getLastResponse();
				preg_match('/\<upload\>\<id\>([0-9A-Za-z.]+)\<\/id\>\<\/upload\>/', $response->getBody(), $found);

				if (!$found[1]) {
					Zend_Debug :: dump($client->getLastResponse());
					exit;
				}

				$attachments = array (
					'name' => $_FILES['file']['name'],
					'file' => array (
						'file' => $found[1],
						'content-type' => $_FILES['file']['type'],
						'original-filename' => $_FILES['file']['name']
					)
				);

				$notify = array (
					$this->person->id,
					$this->que_mgr->id
				);

				$response = $this->bc->create_message($formData['project_id'], $bcdata, $notify, $attachments);

				if (is_object($response)) {
					$data['bcid'] = $response->id;
					$row = $wodb->createRow($data);
					$row->save();
					$this->add_person_local($notify);
					$this->_redirect('/workorders/index/success');
					//$this->view->render('index/success.phtml');
				} else {
					Zend_Debug :: dump($response);
					die;
					$form->populate($formData);
				}
			} else {
				$form->populate($formData);
			}
		}

		foreach ($this->projects as $project) {
			if ($project->status == 'active') {
				$proj = explode("-", $project->name, 2);
				$options1[$project->id] = (isset ($proj[1]) ? $proj[1] : '') . (' [' . isset ($proj[0]) ? $proj[0] : '' . ']');
			}
		}
		asort($options1);

		$form->project_id->setMultiOptions($options1);

		foreach ($this->resources as $person) {
			$options2[$person->id] = $person-> {
				'last-name' }
			 . ", " . $person-> {
				'first-name' };
			if ($this->bc->user == $person-> {
				'user-name' })
			$this->person = $person;
		}

		asort($options2);

		$form->assigned_to->setMultiOptions($options2);

		$wodb = new Workorders;
		$form->type->setMultiOptions($wodb->getEnums('type'));
		$form->priority->setMultiOptions($wodb->getEnums('priority'));
		$form->extra_resources->setMultiOptions($wodb->getEnums('extra_resources'));
		$form->status->setMultiOptions($wodb->getEnums('status'));
		$form->requested_by->setMultiOptions(array (
			$this->person->id => $this->name
		));
		$form->requested_by->setValue($this->person->id);
		$form->assigned_to->setValue($this->que_mgr);
		$form->start_date->setValue($date->getIso());
		$this->view->form = $form;
	}

	public function successAction() {
	}

	public function getProjectLOV() {
		$wodb = new Workorders();
		$lov = $wodb->getLOV("id", "name");
		Zend_Debug :: dump($lov);
		die;

	}

	public function add_person_local($bcids = array ()) {
		$peopledb = new People();

		foreach ($bcids as $bcid) {
			$row = $peopledb->fetchRow($peopledb->select()->where("bcid = ?", $bcid));
			if (!$row) {
				$person = $this->bc->person($bcid);
				$data = array (
					'bcid' => $person-> {
						"id" },
					'username' => $person-> {
						"user-name" },
					'first_name' => $person-> {
						"first-name" },
					'last_name' => $person-> {
						"last-name" },
					'email' => $person-> {
						"email-address" },
					'im' => $person-> {
						"im-handle" },
					'role' => "client"
				);
				$row = $peopledb->createRow($data);
				$row->save();
			}
		}
	}

	public function closeAction() {
		$this->_helper->layout->disableLayout(); //disable layout
		$this->_helper->viewRenderer->setNoRender(); //suppress auto-rendering

		$formdata = $this->_request->getPost();
		$id = $formdata['id'];
		$body = $formdata['new_comment'];
		$wodb = new Workorders();
		$workorder = $wodb->fetchRow('id=' . $id);

		try {
			if (!$this->_request->isPost() && false) {
				throw new Exception('Invalid action. Not post.');
			}
			$workorder->status = "Closed";
			$workorder-> {
				"closed_date" }
			= date('Y-m-d');
			$workorder->save();

			$bcdata = array (
				'post_id' => $workorder->bcid,
				'body' => $body
			);

			$response = $this->bc->create_comment($bcdata);

			if (!is_object($response)) {
				throw new Exception($response);
			}

		} catch (Exception $e) {
			Zend_Debug :: dump($e->getMessage());
		}

	}

	public function commentAction() {
		$this->_helper->layout->disableLayout(); //disable layout
		$this->_helper->viewRenderer->setNoRender(); //suppress auto-rendering

		$formdata = $this->_request->getPost();
		$bcid = $formdata['bcid'];
		$body = $formdata['new_comment'];
		try {
			if (!$this->_request->isPost() && false) {
				throw new Exception('Invalid action. Not post.');
			}

			$bcdata = array (
				'post_id' => $bcid,
				'body' => $body
			);

			$response = $this->bc->create_comment($bcdata);

			if (!is_object($response)) {
				throw new Exception('Invalid action. Not post.');
			}

			$tmp = $this->getComments($bcid);

			if (is_object($tmp)) {
				$comments = array (
					$tmp
				);

			} else {
				$comments = $tmp;
			}

			foreach ($comments as $comment) {
				$author = $this->bc->person($comment-> {
					"author-id" });
				$comment-> {
					"name" }
				= sprintf("%s, %s", $author-> {
					"first-name" }, $author-> {
					"last-name" });
				$comment-> {
					"posted" }
				= $comment-> {
					"posted-on" };
				$comment-> {
					"attachments" }
				= $comment-> {
					"attachments-count" };
				//javascript will parf on these property names...
				unset ($comment-> {
					"author-id" });
				unset ($comment-> {
					"first-name" });
				unset ($comment-> {
					"last-name-name" });
				unset ($comment-> {
					"posted-on" });
				unset ($comment-> {
					"attachments-count" });
			}

			$data = array ();
			$data = $comments;
			$json = Zend_Json :: encode($data); //basically, $data array will also be available in the JS.
			echo $json; //this will echo JSON to the Javascript
			unset ($json);
			unset ($data);
		} catch (Exception $e) {
			Zend_Debug :: dump($e->getMessage());
		}

	}

	function testAction() {

		$update = array (
			'category_id' => $row->category_id
		);

		$cclist = explode("|", "");

		$notify = array (
			$this->que_mgr
		);

		$response = $this->bc->update_message('16093735', $update, $notify);

		Zend_Debug :: dump($response);
		die;

		$ref = sprintf('%s%s', $_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI']);
		Zend_Debug :: dump("http://$ref");
		die;

		$this->getProjectLOV();

		$config = Zend_Registry :: get('config');

		$db = Zend_Db :: factory($config->database->adapter, $config->database->params->toArray());

		foreach ($this->projects as $project) {

			$temp = "";
			$wodb = $db->query("SELECT * from v_workorders where project_id  = ?", array (
				$project->id
			));
			$db->setFetchMode(Zend_Db :: FETCH_OBJ);
			$local = $wodb->fetchAll();
			//Zend_Debug::dump(array($project->id, $this->person->id));

			if (!empty ($local))
				$list[$project->company->id][] = array (
					"company" => $project->company->name,
					"project" => $project->name,
					"id" => $project->id,
					"status" => $project->status,
					"workorders" => $local
				);

		}

		$this->view->companies = $this->companies;
		$this->view->projects = $list;

		//$response = $this->bc->delete_message("13470326");
		//Zend_Debug::dump($response);die;

		foreach ($this->resources as $resource) {
			$ids[] = $resource->id;
		}
		$this->add_person_local($ids);

		/////

		$response = $this->bc->delete_message('13431725');
		Zend_Debug :: dump($response);
		die;

		//$person = new Person;
		//$person->fetchAll();
		$company = new Company;
		$compny->fetchAll();
		$project = new Project;
		$project->fetchAll();
		$project_company = new ProjectCompany;
		$person_company = new PersonCompany;
		$person_project = new PersonProject;
		$projects = $this->bc->projects();
		foreach ($projects as $obj) {
			$id = $obj->id;
			$name = $obj->name;
			$status = $obj->status;
			$company_id = $obj->company->id;
			$company_name = $obj->company->name;

			$project->id = $id;
			$project->name = $name;
			$project->status = $status;

			$project->save();

			$company->id = $company_id;
			$company->name = $company_name;
			$company->save();

			$project_company->project_id = $project->id;
			$project_company->company_id = $company->id;
			$project_company->save();

		}

	}

	public function __call($name, $args) {
		throw new Exception('Sorry, the requested action does not exist');
	}

}
