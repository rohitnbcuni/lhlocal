<?php

/*
$Id$
*/

class Basecamp {

    var $id;

    var $db;

	var $request_url;

	var $post_xml;
	
	var $config;
	    
    function __construct() 
    {
		$config = Zend_Registry::get('config');
		

		$this->db = new Rest($config->basecamp->host, $config->basecamp->username, $config->basecamp->password );
    }
    
    function activate( $id = null )
    {
        if ( ! $id ){
            return false;
        }

        $this->id = $id;
        $rs = $this->find( $this->id );
        return $this->setProperties( $rs );
    }

    function find()
    {
		$request = $this->db->request( $this->request_url, $this->post_xml );
		return $request;
    }
    
    function fetchAllSingleRow()
    {
        return $this->fetchAll( "row" );
    }

    function fetchAllArray()
    {
        return $this->fetchAll( "array" );
    }

    function fetchAllAssoc()
    {
        return $this->fetchAll( "assoc" );
    }

    
    /**
     * @param {int} $offset Starting record for fetch
     * @param {int} $limit Numbers of records to fetch
     * @param {string} $return_type Type of data to return. Can be "assoc", "array", or "row"
     */
    function fetchLimit( $offset, $limit, $return_type = "assoc" ){
        $this->offset( $offset );
        $this->limit( $limit );
        return $this->fetchAll( $return_type );
    }
    
    function fetchAll( $return_type = "assoc" )
    {
        $rs =& $this->find();
        return $this->processRecordSet( $rs, $return_type );
    }
    

    function search( $post_array = null ){
    }
    
   /**
    * Adds columns to the Extra Column array
    * 
    * @access public
    * @param mixed either array or string of columns
    * @return array updated extra columns array
    */
    function addExtraColumns( $columns ){
        if ( ! is_array( $columns ) ){
            $columns = array( $columns );
        }
        $this->extra_columns = array_merge( $this->extra_columns, $columns );
        return $this->extra_columns;
    }

   /**
    * Returns extra columns array
    * 
    * @access public
    * @return array extra columns array
    */    
    function getExtraColumns(){
        return $this->extra_columns;
    }

    function fetchDistinctColumn( $column_name = null )
    {
        if ( is_null( $column_name ) ){ return false; }
        
        $sql = "SELECT DISTINCT( $column_name ) ";

        $table = $this->getTable();
        $sql .= " FROM $table ";
        return $this->db->GetAll( $sql );
    }

    function fetchColumn( $column_name = null, $include_primary_key = false, $order_by_id = false )
    {
    }

    function fetchNamesArray( $column_name = null, $order_by_id = false ){
    }

    function addOrderBy( $column_name = null ){
    }

    function addGroupBy( $column_name = null ){
    }

    /**
     * @param {string} $column Column name
     * @param {string} $value Column value
     * @param {string} $operator Defaults to "="
     * @param {bool} $quote_value Add quotes to the value
     * @param {string} $or_group Name of "OR" group this value belongs to
     */
    function addFilter( $column = null, $value = null, 
                        $operator = "=", $quote_value = true,
                        $or_group = null )
    {
        if ( ! is_null( $column ) && ! is_null( $value ) ){
            $this->_results_filter[] = array(   'column'    => $column, 
                                                'value'     => $value, 
                                                'operator'  => $operator,
                                                'quote_value' => $quote_value,
                                                'or_group'  => $or_group
                                                 );
        }
    }
    
    /**
    *
    * @param string $foreign_table Name of the table to join with
    * @param string $on_clause Keys relationship
    */
    function addInnerJoin( $foreign_table = null, $on_clause = null ){
    }
    
    function innerJoins(){
    }
 
    /**
    *
    * @param string $foreign_table Name of the table to join with
    * @param string $on_clause Keys relationship
    */
    function addLeftJoin( $foreign_table = null, $on_clause = null ){
        if ( is_null($foreign_table) or is_null($on_clause) ) return false;
        
        $this->_left_joins[] = " LEFT JOIN $foreign_table ON $on_clause ";
    }

    function leftJoins(){
        return $this->_left_joins;
    }

    function filter()
    {
        return $this->_buildWhereFilter();
    }
    
    
    /**
    * @param string $where A where clause
    * @param string $logic 'AND' or 'OR' to seperate clause from other where clauses. Defaults to AND
    */
    function addWhere( $where = null, $logic = 'AND' ){
        if ( is_null($where) )return false;
        $this->_where_array[] = array ( $where, $logic );
    }

    /**
    * 
    * 
    */
    function _buildWhereFilter()
    {
        $where = array();
        $where_list = array();
        foreach ( $this->_results_filter as $results_array ){ //results_array is an array of ( column, value, operator )
            if ( !isset( $where_list[$results_array["or_group"]]) ){
                $where_list[$results_array["or_group"]] = array();
            }
            if ( is_numeric($results_array["value"]) || $results_array["quote_value"] == false ){
                array_push( $where_list[$results_array["or_group"]],
                        $this->column_alias($results_array["column"])." ".$results_array["operator"]." ". $results_array["value"] );
            }elseif( is_array($results_array["value"]) ){
                foreach ( $results_array["value"] as $k => $v ){
                    $results_array["value"][$k] = $this->db->qstr( $v );
                }
                array_push( $where_list[$results_array["or_group"]],
                        $this->column_alias($results_array["column"])." IN (". implode(",",$results_array["value"]).")" );
            }else{
                array_push( $where_list[$results_array["or_group"]],
                        $this->column_alias($results_array["column"])." ".$results_array["operator"]." ".$this->db->qstr( $results_array["value"] ) );
            }
        }

        foreach ( $where_list as $k => $v ){
            if ( empty( $k )){
                $where[] = implode(' AND ', $v );
            }else{
                $or = implode( ' OR ', $v );
                $where[] = " ( $or ) ";
            }
        }
        $where = implode(' AND ', $where );

        /*
        * Iterate through the array of where clauses and 
        * append each to the current where string.
        * Only add the logic operator if where was previously set.
        */
        foreach ( $this->_where_array as $where_item ){
            if ( ! empty( $where ) ){
                $where .= " $where_item[1] ";
            }
            $where .= $where_item[0];
        }

        return $where; 
    }

    function column_alias( $name = null, $value = null )
    {
        if ( ! is_null( $name ) && ! is_null ( $value ) ){
            // set the alias
            return $this->_column_alias[$name] = $value;
        }else{
            // if an alias has been set return otherwise just use the passed in name
            return array_key_exists( $name, $this->_column_alias ) ? $this->_column_alias[$name] : $name;
        }
    }
    function getQualifiedFields(){
        $table = $this->getTable();
        return explode(',',"$table.".(implode( ",$table.", $this->getFieldNames() )));
    }
    
    function getQualifiedPrimaryKey(){
        return $this->getTable().".".$this->primary_key;
    }
    
    /**
     * Returns assoc array of columns and their foreign Object name
     * 
     * @return {array} Assoc array of columns and their foreign Object name 
     */
    function getForeignKeys(){
        $fk_array = array();
        foreach( $this->getFields() as $col => $fk ){
            if ( ! is_null( $fk ) )
                $fk_array[$col] = $fk;
        }
        return $fk_array;
    }

    function delete( )
    {
        if ( ! $this->id ){
            return false;
        }
        $table = $this->getTable();
        $sql = "DELETE FROM $table WHERE ".$this->getQualifiedPrimaryKey()." = $this->id";
        return $this->db->Execute($sql) === false ? false : true;
    }

    function _insert()
    {
        $table = $this->getTable();
        $new_rs = $this->getProperties();
        if ( empty($new_rs) ){
            return true;
        }
        unset($new_rs[$this->primary_key]);
        $new_rs[$this->column_alias('DateCreated')] = "";
        $insertSQL = $this->db->GetInsertSQL($table, $new_rs, false);
        if ( $this->db->Execute($insertSQL) ){
            $this->id = $this->last_insert_id();
            $this->set("ID", $this->id );
            return true;
        }
        return false;
    }

    function _update()
    {
        //$rs_ref = & $rs;
        $rs = $this->find( $this->get( $this->primary_key ) );
        $properties = $this->getProperties();
   
        if ( $updateSQL = $this->db->GetUpdateSQL($rs, $properties, false, false) ){ 
            return $this->db->Execute($updateSQL);
        }else{
            return false;
        }
    }


    function save()
    {
        if ( $this->get( $this->primary_key ) ){
            return $this->_update();
        }else{
            return $this->_insert();
        }
    }

	function hadError(){
		return $this->getErrorNo() > 0;
	}

    function getErrorMsg(){
    	return $this->db->ErrorMsg();
    }
    
    function getErrorNo(){
    	return $this->db->ErrorNo();
    }

    function getTable()
    {
        return $this->table_alias;
    }

   /**
    * Sets the properties for this object from the passed recordset.
    * 
    * @access public
    * @param array $rs A recordset object
    * @return array all object properties
    */
    function setProperties($properties) // {{{
    { 
        if( is_object( $properties ) && strstr( strtolower(get_class( $properties )), 'adorecordset' ) ){
            $properties = $properties->fields;
        }
        $fields = $this->getFields();
        if ( $properties ){
            foreach ( $properties as $field => $value ) {
                if ( strstr($field,TABLE_COL_SEP) ){
                    list( $table, $field ) = split(TABLE_COL_SEP,$field);
                }
                
                foreach( $fields as $object_field => $val ){
                    //Allows for case-insensitive matches but will still 
                    //set the property with the intended case 
                    if ( strtolower( $field ) == strtolower( $object_field ) ) {
                        $this->set($object_field, $value); 
                        if ( $object_field == $this->primary_key ){
                        $this->id = $value;
                    }
                        break;
                }    
            }
        }
        }
        return $this->getProperties();
    } // }}}
    
    /**
    * Returns the allowed fields of this object. 
    *
    * @access public
    * @return array The fields array
    */
    function getFields() // {{{
    {
        #if (!$this->isRead && $this->isPersistent) {
         #   $this->read();
        #}
        return $this->fields;
    } // }}}

    /**
    * Returns the allowed fields of this object. 
    *
    * @access public
    * @return array Array if all field names
    */
    function getFieldNames() // {{{
    {
        return array_keys( $this->fields );
    } // }}}


    /**
    * Returns the properties of this object. Reads the object from database
    * if necessary.
    *
    * @access public
    * @return array The properties array
    */
    function getProperties() // {{{
    {
        #if (!$this->isRead && $this->isPersistent) {
         #   $this->read();
        #}
        return $this->properties;
    } // }}}

    /**
    * Updates the object properties with specified array values. 
    *
    * @access private
    * @param array $properties The properties values array
    */
    function _updateProperties($properties = array()) // {{{
    {
        if (!empty($properties) && sizeof($properties)) {
            foreach ($properties as $key => $value) {
                if (array_key_exists($key, $this->fields) &&
                    !array_key_exists($key, $this->properties))
                {
                    $this->set( $key, $properties[$key] );
                }
            }
        }
    } // }}}

    function isProperty( $key ){
        return array_key_exists( $key, $this->fields );
    }

    function last_insert_id()
    {
        return Database::last_insert_id( $this->getTable() );
    }
    
    function processRecordSet( $rs = null, $return_type = "assoc" ){
"*****processRecordSet****";
//print_r(XMLToArray(simplexml_load_string($rs)));
return Rest::simplexml2ISOarray(simplexml_load_string($rs));



        if ( ! $rs ){
            return false;
        }
        $rows = false;
        if ( $rs->RecordCount() > 0 ){
            switch( strtolower( $return_type ) ){
                case "array":
                    $rows = $rs->GetArray();
                    break;
                case "row":
                    $rows = $rs->GetArray();
                    $rows = $rows[0];
                    break;
                case "assoc":
                default:
                    $rows = $rs->GetAssoc();
                    break;
        }
        }
        $rs->Close();
        return $rows;
    }

    function processRecordSetOLD( $rs = null, $return_assoc = false ){
        if ( ! $rs ){
            return false;
        }
        $objects    = array();
        $class      = get_class($this);
        if ( $rs->RecordCount() > 0 ){
            while ( !$rs->EOF ){
                //$object = new $class;
                $object =& Factory::getInstance($class);
                $object->setProperties($rs);
                if ( $return_assoc ){
                    $objects[] = $object->getProperties();
                }else{
                    $objects[] = $object;
                }
                $rs->MoveNext();
            }
        }
        $rs->Close();
        return $objects;
    }
    
    function assocToObjects( $assoc = null ){
        if ( ! $assoc ){
            return false;
        }
        $class = get_class($this);
        $objects = array();
        foreach ( $assoc as $key => $array ){
            //$object = new $class;
            $object =& Factory::getInstance($class);
            $object->setProperties( $array );
            $objects[] = $object;
        }
        if ( count($objects) == 1 ){
            return $objects[0];
        }else{
            return $objects;
        }
    }
    
    function groupBy(){
        return $this->group_by;
    }

    function orderBy(){
        return $this->order_by;
    }

    function offset( $offset = null ){
        if ( $offset ){
            $this->offset = $offset;
        }
        return $this->offset;
    }

    function limit( $limit = null ){
        if ( $limit ){
            $this->limit = $limit;
        }
        return $this->limit;
    }
    
    function totalRecordsReturned( $total = null ){
        if ( is_null( $total ) ){
            return $this->total_records_returned;
        }
        $this->total_records_returned = $total;
        return $this->total_records_returned;
    }
    
    function addDefaultStatusFilter(){
    
    }
    
    function addClassificationFilter(){
    
    }
    
    function getDisplay( &$request = null ){
        if ( ! $this->_display_object ){
            $this->_display_object =& Factory::getInstance( $this->_display_object_name, $request );
        } 
        return $this->_display_object;
    }
    
    /*
    * Functions to handle children of Objects
    */
    function fetchDetails( $detail_type = null ){
        $detail_obj_type = $this->getDetailObjType( $detail_type );
        //$many = new $detail_obj_type;
        //$linker = new LinkOneToMany( $this, $many );
        $many =& Factory::getInstance( $detail_obj_type );
        $linker =& Factory::getInstance( 'LinkOneToMany', $this, $many );
        return $linker->fetchMany();
    }

    function fetchDetailsIDs( $detail_type = null ){
        $detail_obj_type = $this->getDetailObjType( $detail_type );
        //$many = new $detail_obj_type;
        //$linker = new LinkOneToMany( $this, $many );
        $many =& Factory::getInstance( $detail_obj_type );
        $linker =& Factory::getInstance( 'LinkOneToMany', $this, $many );
        return $linker->fetchManyIDs();
    }

    function hasDetail( $detail_type, $id ){
        foreach ( $this->fetchDetails( $detail_type ) as $item ){
            if ( $item->id == $id ){
                return true;
            }
        }
        return false;
    }
    
    /**
    * Get count of specific detail
    *
    * @param    string  detail type to count
    * @return   int     count   
    * @access   public
    */
    function detailCount( $detail_type = null ){
        $detail_obj_type = $this->getDetailObjType( $detail_type );
        //$many = new $detail_obj_type;
        //$linker = new LinkOneToMany( $this, $many );
        $many =& Factory::getInstance( $detail_obj_type );
        $linker =& Factory::getInstance( 'LinkOneToMany', $this, $many );
        return $linker->fetchCountMany();
    }

    function _saveDetail( $detail_type = null, $ids = null, $replace_existing = false ){
        $detail_obj_type = $this->getDetailObjType( $detail_type );

        //$many = new $detail_obj_type;
        //$linker = new LinkOneToMany( $this, $many );
        $many =& Factory::getInstance( $detail_obj_type );
        $linker =& Factory::getInstance( 'LinkOneToMany', $this, $many );
        if ( $replace_existing ){
            return $linker->replaceMany( $ids );
        }else{
            return $linker->addMany( $ids );
        }
    }
    
    function addDetail( $detail_type, $ids ){
        return $this->_saveDetail( $detail_type, $ids, false );
    }
    
    /**
     * @param {string} $detail_type Object type to use as a detail
     * @param {array} $ids Array of ids to use as detail IDs
     */
    function replaceDetail( $detail_type, $ids ){
        return $this->_saveDetail( $detail_type, $ids, true );
    }
    
    function removeDetail( $detail_type, $id ){
        $detail_obj_type = $this->getDetailObjType( $detail_type );
        //$many = new $detail_obj_type;
        //$linker = new LinkOneToMany( $this, $many );
        $many =& Factory::getInstance( $detail_obj_type );
        $linker =& Factory::getInstance( 'LinkOneToMany', $this, $many );
        return $linker->deleteOne( $id );
    }
    
    function getDetailObjType( $detail_type ){
        if ( isset ( $this->detail_mappings[ $detail_type ] ) ){
            return $this->detail_mappings[ $detail_type ];
        }
        return $detail_type;
    }
    
    function fetchAllWithThisDetail( $detail_type, $detail_value = null ){
        $detail_obj_type = $this->getDetailObjType( $detail_type );
        //$many = new $detail_obj_type;
        //$linker = new LinkOneToMany( $this, $many );
        $many =& Factory::getInstance( $detail_obj_type );
        $linker =& Factory::getInstance( 'LinkOneToMany', $this, $many );
        $return_array = null;
        if ( $detail_value == -1 ){
            $return_array = $linker->fetchOnesWithoutMany();
        }else{
            $return_array = $linker->fetchOnesFromMany( $detail_value );
        }
        return $return_array;        
    }

    function fetchCountAllThisDetail( $detail_type ){
        $detail_obj_type = $this->getDetailObjType( $detail_type );
        //$many = new $detail_obj_type;
        //$linker = new LinkOneToMany( $this, $many );
        $many =& Factory::getInstance( $detail_obj_type );
        $linker =& Factory::getInstance( 'LinkOneToMany', $this, $many );
        $return_array = $linker->fetchCountAllMany();
        return $return_array;        
    }        

    /**
    * Gets the value of a database backed property. 
    * This method does not retrieve values from the database, 
    * only from the object. Will only get properties pre-defined in the class.
    *
    * @param string $name propety name
    * @return string value of field
    * @access public 
    */
    function get( $name = null )
    {
        if ( array_key_exists( $name, $this->fields ) && array_key_exists( $name, $this->properties )){
            return $this->properties[$name];
        }
        return false;
    }

    /**
    * Sets the value of a database backed property. 
    * This method does not store values in the database, 
    * only from the object. Will only set properties pre-defined in the class.
    *
    * @param string $name propety name
    * @return string value of field
    * @access public 
    */
    function set( $name = null, $value = null )
    {
        if ( strtolower( $name ) == "id" ) $this->id = $value;
        if ( array_key_exists( $name, $this->fields )){
            return $this->properties[$name] = $value;
        }
    }
    
}
?>
