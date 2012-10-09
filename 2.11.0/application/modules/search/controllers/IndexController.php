<?PHP

	include('search.inc');
	define('NBCDOTCOM' , 8);
	class Search_IndexController extends LighthouseController { 
		public function indexAction() {
		$cnt = 5;
        $searchResult = SearchDisplay::Searchresult( $_POST['search_text'],$_POST['search_par']);
		
		$this->view->assign("searchResult",$searchResult);
		}
	}
			
?>
