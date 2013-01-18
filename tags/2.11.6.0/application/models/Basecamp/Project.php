<?php

/*
$Id:$
*/

class Basecamp_Project extends Basecamp{

    var $fields = array(
		"ID"	=>	null,
		"Name"	=>	null,
	    );
            
	var $request_url = "/project/list";

	var $projects = null;

    function __construct( $id = null, $options = null )
    {
        parent::__construct( $id, $options );
        
        $this->_initialize();
    }
    
    function _initialize(){
    }

	function fetchAll($return_type = 'assoc'){
		if ( ! $this->projects ){
			$data = parent::fetchAll();
			$data = $data["project"];

			$projects = array();
			foreach( $data as $item ){
				$projects[$item["id"]] = $item;
			}
			$this->projects = $projects;
		}
		return $this->projects;
	}

	function fetchAllByCompany(){
		$data = $this->fetchAll();
		foreach( $data as $item ){
			$return[$item["company"]["name"]][$item["name"]] = $item;
		}
		#foreach($return as $array){
		#	uksort($array, 'strnatcasecmp');
		#}
		uksort($return, 'strnatcasecmp');
		return $return;
	}

}

?>
