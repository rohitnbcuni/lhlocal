<?PHP

	include('Search.inc');
	class Search_IndexController extends LighthouseController { 
		public function indexAction() {
			$searchResult = array();
			$cnt = 5;
			$request = $this->getRequest();
			$search_par = $request->getParam('search_par');
			$search_text = $request->getParam('search_text')
						
			$searchResult = SearchDisplay::Searchresult( $search_text,$search_par);
			
			$this->view->assign("searchResult",$searchResult);
		}
	}
			
?>
