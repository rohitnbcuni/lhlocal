<?PHP
require_once('../html/_inc/config.inc');
require_once ('modules/lighthouseController.php');
require_once('recaptchalib.php');
class LighthouseApp {
	private $includePaths;
	private $frontController;
	private $view;
	private $configuration;
	private $registry;
	
	/*
	Lighthouse application constructor
	*/
	public function LighthouseApp() {
		//set_include_path(ZENDLIB .':' .APPLIB .':' .APPPATH .':' .FCKPATH .':' .WEBPATH);
	}
	
	/*
	Lighthouse app initilaizer
	*/
	public function init() {
		try {

			$menu_url = explode("/", substr($_SERVER['REQUEST_URI'], 1));
			$_controller = $menu_url[0];
			$_action = $menu_url[2];

			//Create the Zend Front Controller, dispatch the The views, and load used classes
			Zend_Loader::loadClass('Zend_Controller_Front');
			Zend_Loader::loadClass('Zend_Rest_Client');
			Zend_Loader::loadClass('Zend_Session_Namespace');
			Zend_Loader::loadClass('Zend_Cache');
			Zend_Loader::loadClass('Zend_Layout');
			Zend_Loader::loadClass('Zend_Config_Ini');
			Zend_Loader::loadClass('Zend_Registry');
			Zend_Loader::loadClass('Zend_Db');
			Zend_Loader::loadClass('Zend_Db_Adapter_Pdo_Mysql');
			
			$time = 60*60*24*365; /*= 31536000 */

			
			$this->configuration = new Zend_Config_Ini(
			    APPPATH . '/config/app.ini', 
			    APPLICATION_ENVIRONMENT
			);			

			Zend_Layout::startMvc(APPPATH . '/views/scripts/layouts/scripts/');

			$this->frontController = Zend_Controller_Front::getInstance();
			$this->view = Zend_Layout::getMvcInstance()->getView();
			$this->view->doctype('XHTML1_TRANSITIONAL');
			
			$this->frontController->throwExceptions(true);
			$this->frontController->setParam('noViewRendered', true);
			$this->frontController->setParam('noErrorHandler', true);
			$this->frontController->setControllerDirectory(APPPATH .'/controllers/');
			$this->frontController->addModuleDirectory(APPPATH . '/modules');
			$this->frontController->setBaseURL("/");			
		
			try {
				$_SESSION['db'] = Zend_Db::factory('Pdo_Mysql', array(
					'host'     => $this->configuration->database->params->host,
				    'username' => $this->configuration->database->params->username,
				    'password' => $this->configuration->database->params->password,
				    'dbname'   => $this->configuration->database->params->dbname,
					'port'   => $this->configuration->database->params->dbport
				));
				$_SESSION['db']->getConnection();
				//Store connection in registry LH#20736
				Zend_Registry::set('db', $_SESSION['db']);
			} catch (Zend_Db_Adapter_Exception $e) {
				echo "Database connection failure";
			}

			$bc = new basecamp();

			$_session = new Zend_Session_Namespace('Zend_BC_Auth');

			//include(WEBPATH."/html/mobile_redirects.php");
			//$this->view->mobileuser = mobile_device_detect();

			if(isset($_GET['signout'])) {
				setcookie("lighthouse_id", '', time() - 3600, '/');
				setcookie("lighthouse_xp", '', time() - 3600, '/');
				setcookie("lh_user", '', time() - 3600, '/');
				setcookie("lighthouse_rp_data", '', time() - 3600, '/resourceplanner');
				setcookie("lighthouse_ct_data", '', time() - 3600, '/controltower');
				setcookie("lighthouse_create_wo_data", '', time() - 3600, '/workorders');
				setcookie("lighthouse_wo_data", '', time() - 3600, '/');
				
				unset($_COOKIE);
				unset($_session->loggedin);
				unset($_SESSION);
				Zend_Session::destroy();
			} else {					
							
				if(!isset($_session->loggedin) || !$_session->loggedin) {
								
					Zend_Session::regenerateId();
					if(!$bc->ssoLogin()) {
					//if($_session->loggedin == false){
						if($_controller != "login" && $_controller != "loginindexmobilelogin") {
							if($_SERVER['REQUEST_URI'] != '/favicon.ico' && $_SERVER['REQUEST_URI'] != '/'){

								$_session->lh_ru = (string)$_SERVER['REQUEST_URI']; // 'http://' . $_SERVER['HTTP_HOST'] . 
								$bc->set_session((string)$_SERVER['REQUEST_URI'], "lighthouse_ru");								
								session_write_close();
							}
							
						}
						
					} else {
						
						// When User Name and Password gets authenticated it will be redirected to appropriate page.

						$user_access_bits = $_SESSION['user_access_bits'];

						if(!empty($user_access_bits)){
							global $USER_ACCESS;
							global $USER_ACCESS_MENU;
							$menu_array  = $this->getUserAccess($USER_ACCESS,$USER_ACCESS_MENU,$user_access_bits);
							$_SESSION['menu_array'] = $menu_array;
						}
						
						if($_controller == "login") {											
							
							if(is_array($menu_array) && !empty($menu_array))
							{
								$redirect_controller = $this->getRedirectController($menu_array);
							}

							if($redirect_controller == 'resourceplanner')
							{
								$redirect = '/resourceplanner/?userid='.$_SESSION['user_id'];
							}
							else
							{
								if(!empty($redirect_controller))
								{
									$redirect = '/'.$redirect_controller.'/';
								}
								else
								{
										$redirect = '/noaccess/';
								}
							}
							$redirect_url = $bc->get_session_value('lighthouse_ru');
	
							if(!empty($redirect_url))
							{
								$redirect = $redirect_url;
								setcookie("lighthouse_ru", '', time() - 3600, '/');	
							}
							session_write_close();
							header('Location: ' . $redirect);							
						}else if($_controller == "loginindexmobilelogin"){
							
							if(array_key_exists("url_redirect", $_POST)){
								$redirect = base64_decode($_POST["url_redirect"]);
								header('Location: ' . $redirect);
							}else{
								header('Location: /workorders/index/list');
							}
						}												
					}
				} 
				else 
				{	
					// Logged in User is Navigating in differect Tabs or URLs.
					$bc->get_session("lh_user");
					$user_access_bits = $_SESSION['user_access_bits'];

					if(!empty($user_access_bits)){
						global $USER_ACCESS;
						global $USER_ACCESS_MENU;
						$menu_array  = $this->getUserAccess($USER_ACCESS,$USER_ACCESS_MENU,$user_access_bits);
						$_SESSION['menu_array'] = $menu_array;
					}

					if($_controller == "login") 
					{
						$redirect_controller = $this->getRedirectController($menu_array);

						if($redirect_controller == 'resourceplanner')
						{
							$redirect = '/resourceplanner/?userid='.$_SESSION['user_id'];							
						}
						else
						{
							if(!empty($redirect_controller))
							{
								$redirect = '/'.$redirect_controller.'/';								
							}
							else
							{
									$redirect = '/noaccess/';
							}
						}
			      
				header('Location: ' . $redirect);						
				}					
				}
			}
			
			$this->registry = Zend_Registry::getInstance();
			$this->frontController->setParam('USER_HAS_ACCESS', false);						
				
			if(!empty($menu_array[$_controller]))
			{

				if($menu_array[$_controller]['user_access'] =='1' || $_controller == 'login' )
				{							
					$this->frontController->setParam('USER_HAS_ACCESS', true);				
				}		
			}			
			
			if(!$this->frontController->getParam('USER_HAS_ACCESS') && $_controller != 'noaccess' && $_controller != 'login' && !empty($_controller) && (isset($_session->loggedin) && $_session->loggedin))
			{
				header('Location: /noaccess/');
			}
			
			if($_controller != 'login' && $_controller != 'noaccess' && empty($user_access_bits))
			{
				header('Location: /login/');
			}
			
			$this->frontController->dispatch();
			unset($this->frontController, $this->view, $this->configuration, $this->registry);
			
		} catch(Exception $exp) {
			//Echo the errors for the bootstrap
			//LH#23703
			$_SESSION['error_handler']  = $exp;
			header('Location: /noaccess/index/error/'); 
			/*$contentType = 'text/html';
			
			header("Content-Type: $contentType; charse=utf-8");
			echo 'An unexpected error occurred. in bootstrap init. Controller did not initialize';
			echo '<h3>Unexpected Exception: ' .$exp->getMessage() .'</h3><br /><pre>';
			echo $exp->getTraceAsString();*/
		}
	}
	
	public function dbAdapter() {
	}


	public function getUserAccess($USER_ACCESS,$USER_ACCESS_MENU,$user_access_bits)
	{
		$menu_array = array();
		$i = 0;

		foreach($USER_ACCESS as $controller => $access)
		{
			
			$user_access_bit = $user_access_bits[$i];
			if($access == '1' && $user_access_bit == '1')
			{
				$menu_array[$USER_ACCESS_MENU[$controller]['url']] = $USER_ACCESS_MENU[$controller];
				$menu_array[$USER_ACCESS_MENU[$controller]['url']]['user_access'] = $user_access_bit;
			}			
			$i++;
		}

		return $menu_array;
	}

	public function getRedirectController($menu_array)
	{
		$_controller = '';
		foreach($menu_array as $controller => $access)
		{
			if($access['user_access']=='1')
			{
				$_controller = $controller;
				break;
			}
		}
		return $_controller;
	}
}
?>
