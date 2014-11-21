<?php

require_once 'Zend/Loader.php';

Zend_Loader::loadClass('Zend_Gdata_AuthSub');
            Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
            Zend_Loader::loadClass('Zend_Gdata_Spreadsheets');
            Zend_Loader::loadClass('Zend_Gdata_Docs');
            echo "<pre>";
 
            //-------------------------------------------------------------------------------
            // Google user account
             
            $username = 'shobhitsingh1@gmail.com'; // Your google account username
            $password = 'viraaj16DEC'; // Your google account password
             
            //-------------------------------------------------------------------------------
            // Document key - get it from browser addres bar query key for your open spreadsheet
             
            $key = '1vugzynFReaCFOYvMVJ55-GGNqV1V2njvD5zn3wNcgx8';
             
            //---------------------------------------------------------------------------------
            // Init Zend Gdata service
             
            $service = Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME;
            $client = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
            $spreadSheetService = new Zend_Gdata_Spreadsheets($client);
             
            //--------------------------------------------------------------------------------
            // Example 1: Get cell data
             
            $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
            $query->setSpreadsheetKey($key);
            $feed = $spreadSheetService->getWorksheetFeed($query);
            //print_r($feed);
            $entries = $feed->entries[0]->getContentsAsRows();
            //print_r($entries);
            /*echo "<hr><h3>Example 1: Get cell data</h3>";
            echo var_export($entries, true);*/
             
            //----------------------------------------------------------------------------------
            // Example 2: Get column information
             
           $query = new Zend_Gdata_Spreadsheets_CellQuery();
            $query->setSpreadsheetKey($key);
           // $query->setWorksheetId(basename($feed->entries[0]->id));
            $cellFeed = $spreadSheetService->getCellFeed($query);
           // print_r($columnCount);
            foreach($cellFeed as $cellEntry) {
                  $row = $cellEntry->cell->getRow();
                  
                  
                  $col = $cellEntry->cell->getColumn();
                  $val = $cellEntry->cell->getText();
                  echo "$row, $col = $val\n";
                }
                
                
               //$spreadSheetService->updateCell(4,2,"singh",$key); 
           
             
            //-------------------------------------------------------------------------------------------------
            // Example 3: Add cell data
             
            $testData = array (
                    'test1' => 'Arif',
                    'test2' => 'MH',
                  );
                  
                  
                  $query = new Zend_Gdata_Spreadsheets_ListQuery();
                  $query->setSpreadsheetKey($key);
                    //$query->setWorksheetId($worksheetId);
                  $query->setSpreadsheetQuery("test1=\"Arifa\"");
                    //$listFeed = $spreadSheetService->getListFeed($query);
             
                   $rows = $spreadSheetService->getListFeed($query);
                    if (count($rows->entry) == 0) {
                            echo "No Data Found\n";
                    } else {
                            foreach ($rows as $row) {
                                    $rowData = $row->getCustom();
                                    foreach($rowData as $customEntry) {
                                            echo '      ' . $customEntry->getColumnName() . ': "' .
                    $customEntry->getText() . '"' . "\t";
                                    }
                                    echo "\n";
                            }
                    }
            /*foreach ($columns as $col) {
                $testData[$col] = "Dynamically added " . date("Y-m-d H:i:s") . " in column " . $col;
            }*/
                     //  $ret = $spreadSheetService->insertRow($testData, $key);
           // print_r($ret);
            
            
 ?>