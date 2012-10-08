<?PHP

	include('search.inc');
	define('NBCDOTCOM' , 8);
	class Search_IndexController extends LighthouseController { 
		public function indexAction() {
		$cnt = 5;
             	//	$this->view->assign('calendar_yr',$year);
		 SearchDisplay::Searchresult( $_POST['search_text'],$_POST['search_par']);
		
		}
	}
			
?>
