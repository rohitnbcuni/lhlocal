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
				if(!empty($search_text)){
					SearchDisplay::insertSearchLog($search_array);
				}
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
			$search_fields = array();
			$cnt = 5;
			$request = $this->getRequest();
			$search_par_all = $request->getParam('allOptions');
			$search_par_atleastone = $request->getParam('atLeastOne');
			$search_par_without = $request->getParam('without');
			$search_startdate = $request->getParam('search_startdate');
			$search_enddate = $request->getParam('search_enddate');
			$search_fields = $request->getParam('search_fields');	
			$date_range = array();
			if(!empty($search_startdate)){
				$date_range['startDate'] = $search_startdate;
			}
			if(!empty($search_enddate)){
				$date_range['endDate'] = $search_enddate;
			}
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
			//$search_text = htmlentities($search_text);
			//$search_text = $search_par_all.' OR '.$search_par_atleastone.' NOT '.$search_par_without;
			$search_par = 'All';
			
			$searchResult = SearchDisplay::advanceSearchresult($search_par_all,$search_par_atleastone,$search_par_without,$date_range,$search_fields);
		
			$user_id = $_SESSION['user_id'];
			$search_array = array( "user_id" => $user_id , "pattern" => $search_text);
			if(!empty($search_text)){
				SearchDisplay::insertSearchLog($search_array);
			}
			@setcookie("search_text", $search_text, time()+3600);
			@setcookie("search_par", $search_par, time()+3600);
			$this->view->assign("searchResult",$searchResult);
			$this->view->assign("search_text",$search_text);
			$this->view->assign("search_par",$search_par);
			
				
			
			$this->render('index');
		}
	}
			
?>
