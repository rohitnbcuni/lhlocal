<?PHP
	include('Search.inc');
	include('WorkOrders.inc');
	include("_ajaxphp/util.php");
	include('Quality.inc');
	class Search_IndexController extends LighthouseController { 
		public function indexAction() {
			$searchResult = array();
			$cnt = 5;
			$request = $this->getRequest();
			$search_par = $request->getParam('search_par');
			$search_text = $request->getParam('search_text');
			$searchResult = SearchDisplay::Searchresult( $search_text,$search_par);
			
			$attreibutes = $searchResult['result']->attributes();
			if($attreibutes['numFound'] == 0){
				$dym = SearchDisplay::Searchresult( $search_text,$search_par,'dym');
			
			}
			
			@setcookie("search_text", $search_text, time()+3600);
			@setcookie("search_par", $search_par, time()+3600);
			$this->view->assign("searchResult",$searchResult);
			$this->view->assign("didyoumean",$dym);
			$this->view->assign("search_text",$search_text);
			$this->view->assign("search_par",$search_par);
		}
	}
			
?>
