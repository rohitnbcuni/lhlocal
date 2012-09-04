<?php
//ini_set('include_path',ini_get('include_path').':/var/www/php_lib:');
ini_set('include_path','/var/www/php_lib/');
//require_once 'Zend/Cache.php';
require_once('/var/www/tools/application/models/loadModel.php');
require_once('/var/www/php_lib/User/Solid/Model/ResourceBlock.php');
//require_once('/var/www/php_lib/User/Basecamp/Basecamp.php');

class Resourceplanner_IndexController extends Zend_Controller_Action
{
    public function init()
	{
	


		$this->db = new Zend_Db_Adapter_Pdo_Mysql(array(
			'host'     => DB_SERVER,
			'username' => DB_USERNAME,
			'password' => DB_PASSWORD,
			'dbname'   => DB_DATABASE
			));
			
		$bc=new Basecamp();
		$bc->basecamp();
		$resourceblocks = new User_Solid_Model_ResourceBlock;
		//$users = new User_Solid_Model_Basecamp_User;
		//$projects = new User_Solid_Model_Basecamp_Project;
		$date = $this->getRequest()->getParam('date');
		$resourceblocks->active_date = $date;

		$data = array();
		
		$data["resourceblocks"] = $resourceblocks->fetchAll();
		//$data["users"] = $users->fetchAll();
		$data["users"] = $bc->get_all_users();
		//$data["projects"] = $projects->fetchAll();
		$data['projects']=$bc->get_all_projects();
		//$data["projects_by_company"] = $projects->fetchAllByCompany();
		$data['projects_by_company']=$bc->get_all_projects_by_company();
		$data["dayparts"] = $resourceblocks->getDayparts();
		$data["weekdays"] = $resourceblocks->getWeekdays();
		$data["previous_week"] = $resourceblocks->previous_week;
		$data["next_week"] = $resourceblocks->next_week;
		$load=new loadModel($resourceblocks->db);
		$data["load"]=$load->getLoad($date);
		$data['resource_types']=$bc->get_person_extra_list('type');
		$data['bc']=$bc;
		$this->view->items = $data;
		
		$this->initCache();
		
		
	}

	
	function initCache(){
		$frontendOptions = array(
			'lifetime' =>'7200',
			'automatic_serialization' => true
		);

		$backendOptions = array(
			'cache_dir' => '/tmp/'
		);

		// getting a Zend_Cache_Core object
		$this->cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
	}
	

    public function indexAction()
	{			
		$config = $this->configuration = new Zend_Config_Ini(
			APPPATH . '/config/app.ini', 
			APPLICATION_ENVIRONMENT
		);
		
		$db = Zend_Db::factory('Pdo_Mysql', array(
				'host'     => $config->database->params->host,
				'username' => $config->database->params->username,
				'password' => $config->database->params->password,
				'dbname'   => 'tools'
			));
		$db->getConnection();
		$query = "SELECT pbc.bc_id,pbc.first_name,pbc.last_name,pbc.title,pe.type,pe.agency,pe.sso FROM person_basecamp pbc LEFT JOIN person_extras pe ON (pbc.bc_id=pe.bc_id) ORDER BY pbc.last_name";
		$data = $db->fetchAll($query);
		
		//$result=$this->bc->query("SELECT pbc.bc_id,pbc.first_name,pbc.last_name,pbc.title,pe.type,pe.agency,pe.sso FROM person_basecamp pbc LEFT JOIN person_extras pe ON (pbc.bc_id=pe.bc_id) ORDER BY pbc.last_name");
		$items=array();
		for($i = 0; $i < sizeof($data); $i++)
		{
			
			$row = $data[$i];
			$items[$row['bc_id']]=array(
				'first_name'=>$row['first_name'],
				'last_name'=>$row['last_name'],
				'title'=>$row['title'],
				'type'=>$row['type'],
				'agency'=>$row['agency'],
				'sso'=>$row['sso'],
				);
		}
		$this->view->items=$items;
		
		$layout = Zend_Layout::getMvcInstance();

		//$layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		//$layout->header = $this->view->render('resourceplanner/header.phtml');		
		$encountered_letters=array();
		//$resource_types=$items['resource_types'];
		$rt='phone-number-fax';
		foreach ($items["users"] as $user)
		{
			$first_name = "first-name";
			if(!in_array(strtolower($user[$first_name][0]),$encountered_letters))
			{
				$encountered_letters[]=strtolower($user[$first_name][0]);
			}
			sort($encountered_letters);
		}
		//sort($resource_types);
		
		echo "<form id=\"resource_type_form\">"
			."<label for=\"resource_types\">Show Resource Types:</label>"
			."<select id=\"resource_types\" name=\"rt\" onChange=\"displayTypes()\">"
				."<option value=\"\">All</option>";
				
				foreach($resource_types as $r)
				{
					echo "<option value=\"$r\">$r</option>";
				}
				echo "<option value=\"Uncategorized\">Uncategorized</option>"
			."</select>"
		."</form>";
		
		$layout->resourcelist = $this->view->render('resourceblock/list.phtml');
		
		
		$layout->companylist = $this->view->render('company/list.phtml');
		
		/*echo  "<ul id=\"CompanyListU\" class=\"first-of-type\">";
		foreach ($this->items["projects_by_company"] as $company => $projects){
			$counter = 1;
			echo "<li class=\"CoList yuimenuitem\">"
				."<a href=\"javascript:void(0);\" class=\"yuimenuitemlabel\"><span>" .$company ."</span></a>"
				."<div class=\"yuimenu\">"
					."<div class=\"bd\">"
						."<ul class=\"ProjectList\" id=\"co" .$counter++ ."\">";
						foreach ($projects as $project) {
							echo "<li><a href=\"javascript:void(0);\" onclick=\"var hrs=$('BookingHours_book').checked ? app.ACT : app.ASN; app.saveDayparts(hrs,'" .$project["id"] ."',this);\"><span>" .$project["name"] ."</span></a></li>";
						}
						echo "</ul>"
					."</div>"
				."</div>"
			."</li>";
		}
		echo "</ul>";*/
		
/*	 
		if (!($result = $this->cache->load('companyList'))){
			$this->cache->save( $this->view->render('company/list.phtml'),'companyList');		
		}
		$layout->companylist =  $this->cache->load('companyList');
*/

	}

	public function companyAction()
	{
		$layout = Zend_Layout::getMvcInstance();
		$layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		//print $this->view->render('company/list.phtml');	


	}
	public function resourceAction()
	{
		$layout = Zend_Layout::getMvcInstance();
		$layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		print $this->view->render('resourceblock/list.phtml');
	}

	public function updatecompanyAction()
	{
		$layout = Zend_Layout::getMvcInstance();
		$layout->disableLayout();
		//print_r($this-view->items["projects"]);exit;
		
	}
}
