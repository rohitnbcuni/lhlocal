<?php

/* Mysql wrapper
 * @author Shobhitsingh.Bhadauria@nbcuni.com
 * @copyright NBC.com 
 * @Mysql
 * @version 1.0
 * @link Ajax call
 */


class MySQLiDB
    {
    public $mySql;
    private static $single_state;

    // Our constructor method. A little different...
   public function __construct($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE, $DB_PORT)
        {
        $this->mySql=new mysqli($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE, $DB_PORT);

        if (mysqli_connect_errno())
            {
            echo("Connection failed : \n" . mysqli_connect_error());
            exit();
            }
        }

   /* public static function getInstance($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE, $DB_PORT)
        {
        if (!self::$single_state)
            {
            self::$single_state=new MySQLiDB($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_DATABASE, $DB_PORT);
            }
		self::$mySql = self::$single_state;
        return $this->mySql;
        }
*/

    /*
    * This is a simple Select method.
    * This method will return all the data from the selected table.
    * Method require only one argument to pass. The table name 
    */
    public function get_all_data($table, $fields = "*")
        {
        $sql   ="SELECT $fields FROM $table";
        $result=$this->sqlordie($sql);

        while ($rows=$result->fetch_assoc())
            {
            $records[]=$rows;
            }

        return $records;
        }


    /*
    * This method requires two arguments.
    * First one is the table name
    * Second one is the the returned fields. 
    * You need to pass the returned field names as an array like array('field1','field2','fieldn')
    */

    public function get_selected_fields($table, $selected_fields)
        {
        $records=NULL;

        foreach ($selected_fields as $field_name)
            {
            $field_names[]=$field_name;
            }

        $field_names=implode(',', $field_names);
        $sql        ="SELECT $field_names FROM $table";
        $result     =$this->sqlordie($sql);

        while ($rows=$result->fetch_assoc())
            {
            $records[]=$rows;
            }

        return $records;
        }

    /* Our insert method. This method will take two argument.
    * First one is the table name 
    * Second one is the array of field name and value. i.e array('field_name'=>value to insert) 
    * You can use an array to insert multiple data at a time
    * Need to sanitize the data...
    */
    public function insert_data($table, $insert_values)
        {
        foreach ($insert_values as $key => $value)
            {
            $keys[]        = $key;
            $insertvalues[]='\'' . $value . '\'';
            }

        $keys        =implode(',', $keys);

        // Sanitizing the data... 
        $insertvalues=(implode(',', $insertvalues));

        $sql         ="INSERT INTO $table ($keys) VALUES ($insertvalues)";

        $this->sqlordie($sql);
        }


    /* Our update method. This method will take two argument.
    * First one is the table name 
    * Second one is the array of field name and value. i.e array('field_name'=>value to update) 
    * You can use an array to update multiple data at a time
    */

    public function update_data($table, $keyColumnName, $id, $update_values)
        {
        foreach ($update_values as $key => $value)
            {
            $sets[]=$key . '=\'' . $value . '\'';
            }

        $sets=implode(',', $sets);

        $sql ="UPDATE $table SET $sets WHERE $keyColumnName = '$id'";

        $this->sqlordie($sql);
        }

    /*
    * This method will select a specific row from the database. It will take three arguments
    * First one is the tanle name
    * Second one is the specific column name in which we want to match the id
    * Third one is the id number of the specific row. 
    */
    public function get_record_by_ID($table, $keyColumnName, $id, $fields = "*")
        {
        $sql   ="SELECT $fields FROM $table WHERE $keyColumnName = '$id'";

        $result=$this->sqlordie($sql);

        return mysqli_fetch_assoc($result);
        }

    // this method will return a selected group from the specific table. 
    public function get_records_by_group($table,
                                             $groupKeyName, $groupID, $orderKeyName = '', $order = 'ASC', $fields = '*')
        {
        $orderSql='';

        if ($orderKeyName != '')
            $orderSql=" ORDER BY $orderKeyName $order";

        $sql   ="SELECT * FROM $table WHERE $groupKeyName = '$groupID'" . $orderSql;

        $result=$this->sqlordie($sql);

        while ($row=$result->fetch_assoc())
            {
            $records[]=$row;
            }

        return $records;
        }

    // Delete a specific row from a table.
    public function delete_record_by_ID($table, $keyColumnName, $id)
        {
        $sql   ="DELETE FROM $table WHERE $keyColumnName = '$id'";
        $result=$this->sqlordie($sql);
        echo("Affected row(s) are %d\n" . $this->mySql->affected_rows);
        }

    public function delete_record_with_special_operation() {
    // I'm still thinking about the possibilities  :)
    }

    // delete every records from the selected table

    public function clear_table_data($table)
        {
        $sql   ="DELETE FROM $table";
        $result=$this->sqlordie($sql);
        }

    /*
    *   This method requires four parameters to run.
    *   In first parameter you need to supply the table name.
    *   In second parameter you need to supply the exact field name on which you want to search.
    *   In third parameter you need to supply your desired search strings. You can supply single or multiple search string there.
    *   In case of multiple search string supply them as comma seperated. like 'string1,string2,stringn' .
    *   In the final parameter you need to supply the desired column column names that you want to display after a successful search.
    *   The search method currently not support return BLOB data type from mysql database.
    *   There is a little limitation of this method. That is, you will always need to supply the returned column names as an array
    *   otherwise the method will unable to perform.  
    *   The fifth parameter is optional. Default will be used as OR. But user can specify it like AND 
    *   Function updated
    */
    public function search_data($table, $field_name, $search_string, $returned_column = array(), $Operation = 'OR')
        {
        $column_names="*";

        if (count($returned_column) > 0)
            {
            $column_names=implode(',', $returned_column);
            }

        $search_string=explode(',', $search_string);
        $sql          ="SELECT $column_names FROM $table WHERE ";
        $isFirst      =true;

        foreach ($search_string as $key => $value)
            {
            if ($isFirst)
                {
                $sql.="$field_name LIKE '%$value%'";
                }
            else
                {
                $sql.="$Operation $field_name LIKE '%$value%'";
                }

            $result=$this->sqlordie($sql);

            if ($result)
                {
                if ($result->num_rows == 0)
                    {
                    echo "Sorry we did not find the setected string...";
                    }
                else
                    {
                    while ($row=$result->fetch_assoc())
                        {
                        $records[]=$row;
                        }

                    return $records;
                    }

                mysqli_free_result($result);
                }
            }
        }

    // To check the duplicate data. Need to make more customization...
    public function check_duplicate_data($sql)
        {
        $result=null;
        $result=$this->mySql->query($sql);
        return $row_count=$result->num_rows;
        }


    // This method is for those who love to parse their own raw query....
    // Function updated....
    public function rawQuery($sql)
        {
        $records=NULL;
        $result =$this->sqlordie($sql);

        while ($row=$result->fetch_assoc())
            {
            $records[]=$row;
            }

        return $records;
        }
        
        private function _sanitize($string)
        {
            $string = htmlspecialchars($string);
            $string=trim(rtrim(ltrim($string)));
            $string = $this->mySql->real_escape_string($string);
            return $string;
        }

    // Every query will pass through this method. Will return result sets or query error.
    public function sqlordie($sql)
        {
        $return_result=$this->mySql->query($sql);

        if ($return_result)
            {
            return $return_result;
            }
        else
            {
            //$this->sql_error($sql);
            }
        }

    private function sql_error($sql)
        {
        echo($this->mySql->error) . '<br>';
        die('error: ' . $sql);
        }
	private function refValues($arr)
    {
         if (strnatcmp(phpversion(),'5.3') >= 0)
         {
             $refs = array();
             foreach($arr as $key => $value)
                 $refs[$key] = &$arr[$key];
             return $refs;
         }
         return $arr;
    }
	//Make a prepare class	
	public function sqlprepare($query,$params=array())
	{
		for ($i = 0; $i < count($params);$i++)
			{
				if (is_int($params[$i])) $types.="i";
				elseif (is_double($params[$i])) $types.="d";
				else  $types.="s";
			}
		try
			{
				$stmt = $this->mySql->prepare($query);
				if(!$stmt){
					throw new Exception($this->sql_error($query));
				}
				if (strcmp($types, "") != 0) call_user_func_array(array($stmt, "bind_param"), $this->refValues(array_merge(array($types),$params)));
				$stmt->execute();
				$meta = $stmt->result_metadata();
				if($meta != NULL){
					while ($field = $meta->fetch_field()) {
						$parameters[] = &$row[$field->name];
					}
				}else{
					 $stmt->store_result();
				
				}
				call_user_func_array(array($stmt, 'bind_result'), $parameters);
				$results = $stmt->get_result();
				//p($results);
				/*while ( $row = $res->fetch_assoc()) {
					foreach($row as $key => $val) {
						$x[$key] = $val;
					}
					$results[] = $x;
				}*/
        	}catch(Exception $e){
				echo $this->sql_error($query);
			
			}
			return $results;
			
		}

	/**
	* if method not defined in this call
	*  It call megical function and preserve mysqli all methods
	*
	*/	

	
	public function __call($f,$arg)
	{
		return call_user_func_array(array($this->mySql,$f),$arg);
		//return $this->mySql;
	}
	
	public function __set($key,$val){
	}
	
	public function __get($variable_name){
		return $this->mySql->$variable_name;
	}
	
	
    // disconnecting the database

    public function __destruct() { $this->mySql->close(); }
    }
?>
