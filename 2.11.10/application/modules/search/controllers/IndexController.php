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
			//print_r($searchResult);
			if(ISSET($searchResult['result'])){
				$attreibutes = $searchResult['result']->attributes();
				if($attreibutes['numFound'] == 0){
					$dym = SearchDisplay::Searchresult( $search_text,$search_par,'dym');
				
				}
				$user_id = $_SESSION['user_id'];
				$search_array = array( "user_id" => $user_id , "pattern" => $search_text);
				SearchDisplay::insertSearchLog($search_array);
				@setcookie("search_text", $search_text, time()+3600);
				@setcookie("search_par", $search_par, time()+3600);
				$this->view->assign("searchResult",$searchResult);
				$this->view->assign("didyoumean",$dym);
				$this->view->assign("search_text",$search_text);
				$this->view->assign("search_par",$search_par);
			}
		}
		public function advancesearchAction() {
			
			$searchResult = array();
			$cnt = 5;
			$request = $this->getRequest();
			$search_par_all = $request->getParam('allOptions');
			$search_par_atleastone = $request->getParam('atLeastOne');
			$search_par_without = $request->getParam('without');
			if((!empty($search_par_all)) && (!empty($search_par_atleastone)) && (!empty($search_par_without))){
				$search_text = $search_par_all.' OR '.$search_par_atleastone.' NOT '.$search_par_without;
			
			}
			if((empty($search_par_all)) && (!empty($search_par_atleastone)) && (!empty($search_par_without))){
				$search_text = $search_par_atleastone.' NOT '.$search_par_without;
			}
			if((!empty($search_par_all)) && (empty($search_par_atleastone)) && (!empty($search_par_without))){
				$search_text = $search_par_all.' NOT '.$search_par_without;
			
			}
			if((!empty($search_par_all)) && (!empty($search_par_atleastone)) && (empty($search_par_without))){
				$search_text = $search_par_all.' OR '.$search_par_atleastone;
			
			}
			if((!empty($search_par_all)) && (empty($search_par_atleastone)) && (empty($search_par_without))){
				$search_text = $search_par_all;
			
			}
			if((empty($search_par_all)) && (!empty($search_par_atleastone)) && (empty($search_par_without))){
				$search_text = $search_par_atleastone;
			
			}
			
			//$search_text = $search_par_all.' OR '.$search_par_atleastone.' NOT '.$search_par_without;
			$search_par = 'All';
			$searchResult = SearchDisplay::advanceSearchresult($search_par_all,$search_par_atleastone,$search_par_without);
		
			$user_id = $_SESSION['user_id'];
			$search_array = array( "user_id" => $user_id , "pattern" => $search_text);
			SearchDisplay::insertSearchLog($search_array);
			@setcookie("search_text", $search_text, time()+3600);
			@setcookie("search_par", $search_par, time()+3600);
			$this->view->assign("searchResult",$searchResult);
			$this->view->assign("search_text",$search_text);
			$this->view->assign("search_par",$search_par);
			
				
			
			$this->render('index');
		}
	}
			
?>
