<?php

/*
$Id:$
*/

class Basecamp_User extends Basecamp{

    var $fields = array(
		"ID"	=>	null,
		"Name"	=>	null,
	    );
            
	var $request_url = "/contacts/people/707029";

    function __construct( $id = null, $options = null )
    {
        parent::__construct( $id, $options );
        
        $this->_initialize();
    }
    
    function _initialize(){
    }

	function fetchAll($return_type = 'assoc'){
		$data = parent::fetchAll();
		return $data["person"];
	}
	
	
	function getCompanies($id = '707029')
	{
		    $this->request_url = sprintf("/contacts/companies/%s",$id);
		    $this->fetchAll();

	}
	
		function getProjects($id = null)
	{
			    $this->request_url = sprintf("/contacts/people/%s",$id);
		   	    $this->fetchAll();

	}


}

?>
