<?php
class Workorders extends Zend_Db_Table_Abstract
{
    protected $_name = "workorders";
    protected $_enums = array();
    
    
    
    // grab possible SET/ENUM values and return an array
    private function loadEnums(){
    	$info = self::info();
    	foreach($info["metadata"] as $cols)
    	{
    		if(strpos($cols['DATA_TYPE'],'enum') !== false)
    		{
    			$items = explode("','",preg_replace("/(enum)\('(.+?)'\)/","\\2",$info['metadata'][$cols['COLUMN_NAME']]['DATA_TYPE']));
    			foreach($items as $item)
    			{
    				$this->_enums[$cols['COLUMN_NAME']][$item] = $item;	
    			}
    		}
    	}
    	
	}
	
	
	    public function getEnums($col)
	    {
			if(count($this->_enums) == 0 )
			{
				$this->loadEnums();
			}
			
			return $this->_enums[$col];
    	
	}
	
	public function getLOV($key,$val) 
	{
	
		$select = $this->select();
		$select->from($this, array('id', 'name'))
			   ->where('archived = ?', '0')
			   ->order('name');
		
		$result =  $this->getAdapter()->fetchPairs($select);
		
		Zend_Debug::dump($result);		
	}

   	     
}
