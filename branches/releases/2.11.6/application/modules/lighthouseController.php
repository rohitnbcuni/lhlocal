<?PHP
	Zend_Loader::loadClass('Zend_Controller_Action');
	
	class LighthouseController extends Zend_Controller_Action { 

		public function init()
		{
			$menu_url = explode("/", substr($_SERVER['REQUEST_URI'], 1));
			$_controller = $menu_url[0];
			$_action = $menu_url[2];			
		}

		public function __call($method, $args)
		{
			
		}
		public function indexAction() {
			
		}
	}
?>