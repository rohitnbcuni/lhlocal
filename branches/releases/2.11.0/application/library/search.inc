<?PHP
	/*
		Control Tower classes parent and child
	*/
	class Search {
		protected static $configuration;
		
		public function setIniFile() {
			$this->configuration = new Zend_Config_Ini(
			    APPPATH . '/config/app.ini', 
			    APPLICATION_ENVIRONMENT
			);
		}
		
		public function getIniFile() {   
			return $this->configuration;
		}
		/*
			Work Orders Setters
		*/
		/*
			Work Orders Getters
		*/
	}
	class SearchDisplay extends Search {
		public function Searchresult($query,$search_par) {
		if($search_par[0]=='All'){
		$string='';
		}
		elseif($search_par[0]=='Defect'){
                $string='%20AND%20categories:quality';
                }
		elseif($search_par[0]=='WorkO'){
                $string='%20AND%20categories:workorder';
                }
		$url = 'http://ec2-75-101-162-191.compute-1.amazonaws.com:8080/solr/lighthouse_active/select?q='.urlencode($query).$string.'&start=0&rows=2000';
		$ch = curl_init();
                $request='<request>'.$request.'</request>';
                curl_setopt($ch, CURLOPT_URL, $url);
                if(defined('HTTP_PROXY') && HTTP_PROXY!='')
                {
                        curl_setopt($ch,CURLOPT_PROXY,HTTP_PROXY);
                        curl_setopt($ch,CURLOPT_HTTPPROXYTUNNEL,true);
                        curl_setopt($ch,CURLOPT_PROXYTYPE,CURLPROXY_HTTP);
                }
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt ($ch, CURLOPT_POST, 1);
                curl_setopt ($ch, CURLOPT_POSTFIELDS, $request);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/xml', 'Content-Type: application/xml'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch,CURLOPT_USERPWD,BC_USER . ":" . BC_PASSWORD);
                if(preg_match('/^(http)/',$url)) curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
                $response = curl_exec($ch);
                curl_close($ch);

        $xml = (array)simplexml_load_string($response);
        $titleDesc = array();
        $desc = array();
	$count_qa=0;$count_wo=0;$i=1;$wo=1; 
        if(count($xml) > 0){
        foreach($xml as $key =>$val){
                if($key == 'result'){
		$result_found = $val[numFound];
                                $searchResult =  $val->doc;
                                if(count($searchResult) > 0){
                                foreach($searchResult as $k => $v){
                                        if(count($searchResult) > 0){
                                                foreach($searchResult as $sKey => $eVal){
                                                       /* $comments='';
                                                        for($i=0;$i<count($eVal->arr[1]->str);$i++)
                                                        {$no = $i+1;
                                                        $comments .= $no.'.'. $eVal->arr[1]->str[$i].'</br>';
                                                         }*/
                                                                                        $id[] =   $eVal->str[0];
                                                                                        $project_id[] =  $eVal->str[1];
                                                                                        $title[] = $eVal->str[2];
                                                                                        $urllink[] =  $eVal->arr->str[1];
                                                                                        $desc[] =  $eVal->str[4];
                                                                                        $cat[] =  $eVal->str[5];
                                                                                       /* $comment[] =  (array) $comments ;*/
						if( $eVal->str[5]=='quality'){
						$count_qa=$i++;}
						else{$count_wo=$wo++; }

                                                }

                                        }
                                }

                        }
                }

        }
        }

 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
$pageURL .= $_SERVER["SERVER_NAME"];

?>
                <div class="title_med2 workorders_filter">
                                <INPUT TYPE="hidden" ID="requestTypeFilter" VALUE"" />
                                       <?php if($result_found!=0){ ?> <label for="project_status_filter" style="color: #fff;float: left; margin-top: 10px; font-size: 15px;" id="project_status_filter_label"><?php echo $result_found;?> Search results for "<?php echo  ucfirst($query); ?>"</label><?php } else{?>
<label for="project_status_filter" style="color: #fff;float: left; margin-top: 10px; font-size: 15px;" id="project_status_filter_label">No result found</label>
<?php }?>
                                        </div>
                <div id='search_wrapper' class='search_wrapper'>
                <div id='search_results_wrapper' class='search_results_wrapper' style='margin-top:20px;'>
                       <?php if ($result_found > 0){?> <div class='search_results_nav'>
                	       <ul>
                                        <li  <?php if($search_par[0]!='All'){echo "style='background-color:#FFFFFF; color: #358FCE;;'";} else {echo "class='active'";} ?>><a href='#'  <?php if($search_par[0]!='All'){echo " onclick='return false;'";}else{ echo ' onclick="load_all_search();"';};?>>All</a></li>
                                        <li  class='active_defect' <?php if($search_par[0]=='Defect'){echo "style='background-color:#848484; color: #FFFFFF;'";} ?>><a href='#'  <?php if($search_par[0]=='WorkO'){echo " onclick='return false;'";}else{ echo ' onclick="load_defect_search();"';}; if($search_par[0]=='Defect'){echo "style='color: #FFFFFF;'";} ?> >Defects (<?php echo $count_qa;?>)</a></li>
                                        <li class="active_wo"  <?php if($search_par[0]=='WorkO'){echo "style='background-color:#848484;'";} ?>><a href='#'  <?php if($search_par[0]=='Defect'){echo " onclick='return false;'";}else{ echo ' onclick="load_workorder_search();"';}; if($search_par[0]=='WorkO'){echo "style='color: #FFFFFF;'";} ?>>Work Orders(<?php echo $count_wo;?>)</a></li>
                                </ul>
                        </div><!-- END search_results_nav -->
                        <div class='search_results_content'><div id="wrapper">
			<div id="paging_container7">
				<ul class="content">
<?php   


for($i =0 ; $i<count($id); $i++){ 
if($cat[$i][0]=='quality'){$link = $pageURL.'/quality/index/edit/?defect_id=';$text='DEFECTS';}else{$link = $pageURL.'/workorders/index/edit/?wo_id=';$text='WORKORDERS'; }?>
                                <li> <div class='search_result'>
                                        <h2><a href='<?php echo $link.$id[$i][0]; ?>'><span class='search_txt'  style="color: #358FCE;"><?php echo ucfirst(substr($title[$i][0],0,70));if(strlen($title[$i][0])>70){echo '...';}?></a></span></h2>
                                        <span class='defect_url'><span class='defect_txt'><?php echo $text; ?></span><a href='<?php echo $link.$id[$i][0]; ?>'><?php echo $link.$id[$i][0]; ?></a></span>
                                        <p class='defect_desc'><?php echo  ucfirst(substr($desc[$i][0],0,300));if(strlen($desc[$i][0])>300){echo '...';}?></p>
                                </div> <!-- END search_result --></li>
<?php } ?></ul>	
<div class="page_navigation"></div>	
	</div>				
            <div class="search_results_content_defect" style="display:none">
                        <div id="paging_container71">
                                <ul class="content">
<?php


for($i =0 ; $i<count($id); $i++){
if($cat[$i][0]=='quality'){
$link = $pageURL.'/quality/index/edit/?defect_id=';$text='DEFECTS';?>
                                <li> <div class='search_result'>
                                        <h2><a href='<?php echo $link.$id[$i][0]; ?>'><span class='search_txt'  style="color: #358FCE;"><?php echo ucfirst(substr($title[$i][0],0,70));if(strlen($title[$i][0])>70){echo '...';}?></a></span></h2>
                                        <span class='defect_url'><span class='defect_txt'><?php echo $text; ?></span><a href='<?php echo $link.$id[$i][0]; ?>'><?php echo $link.$id[$i][0]; ?></a></span>
                                        <p class='defect_desc'><?php echo  ucfirst(substr($desc[$i][0],0,300));if(strlen($desc[$i][0])>300){echo '...';}?></p>
                                </div> <!-- END search_result --></li>
<?php } }?></ul>
<div class="page_navigation"></div>
                        </div>
</div>
<div class="search_results_content_wo" style="display:none" >
<div id="paging_container72">
                                <ul class="content">
<?php


for($i =0 ; $i<count($id); $i++){
if($cat[$i][0]=='workorder'){$link = $pageURL.'/workorders/index/edit/?wo_id=';$text='WORKORDERS'; ?>
                                <li> <div class='search_result'>
                                        <h2><a href='<?php echo $link.$id[$i][0]; ?>'><span class='search_txt'  style="color: #358FCE;"><?php echo ucfirst(substr($title[$i][0],0,70));if(strlen($title[$i][0])>70){echo '...';}?></a></span></h2>
                                        <span class='defect_url'><span class='defect_txt'><?php echo $text; ?></span><a href='<?php echo $link.$id[$i][0]; ?>'><?php echo $link.$id[$i][0]; ?></a></span>
                                        <p class='defect_desc'><?php echo  ucfirst(substr($desc[$i][0],0,300));if(strlen($desc[$i][0])>300){echo '...';}?></p>
                                </div> <!-- END search_result --></li>
<?php } } ?></ul>
<div class="page_navigation"></div>
        </div>
</div>
    		    
		</div>
		</div><!-- END search_results_content -->
                <?php } ?>
		</div> <!-- END search_results_wrapper -->

<?php	 }
		}

?>
