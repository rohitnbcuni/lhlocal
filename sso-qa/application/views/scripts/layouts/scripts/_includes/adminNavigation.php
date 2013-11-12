			<!--==| START: Bucket |==-->
			<div class="title_lrg">
				 
				<center><h4 style="margin:0px;padding-top:22px;">User Info</h4></center>
			</div>
			<!--==| END: Bucket |==-->
			<!--==| START: Bucket |==-->
			<div class="control_tower_main">	
			
			<div class="contentCol" id="create_columns" >
						<div class="leftCol" id="section_menu" style="min-height:400px;">
						<ul id="create_sections">
							<li class="alt active" id="fetchUser" onClick="fetchUser();">
							<img src="/_images/yellow_status.gif">User Info
							</li>
							<li class="alt" id="createCompany" onClick="companyList();">
								<img src="/_images/yellow_status.gif">Business List
							</li>
							<li class="alt" id="fetchProjVersion" onClick="fetchProjVersion();">
							<img src="/_images/yellow_status.gif">QA Project Versions
							</li>
							<li class="alt" id="fetchProjIteration" onClick="fetchProjIteration();">
								<img src="/_images/yellow_status.gif">QA Project Iterations
							</li>
							<li class="alt" id="fetchProjProduct" onClick="fetchProjProduct();">
								<img src="/_images/yellow_status.gif">QA Project Products
							</li>							
							<li class="alt" id="companyDefaultCC" onClick="companyDefaultCC();">
							<img src="/_images/yellow_status.gif">WO Business Default CC
							</li>	
							<li class="alt" id="QCprojectDefaultCC" onClick="QCprojectDefaultCC();">
							<img src="/_images/yellow_status.gif">Quality Project Default CC
							</li>
							<li class="alt" id="workorderSLAReport" onClick="workorderSLAReport();">
								<img src="/_images/yellow_status.gif">Work order SLA Report
							</li>							
							<li class="alt" id="customFieldName" onClick="customFieldName();">
								<img src="/_images/yellow_status.gif">Custom List
							</li>
							<li class="alt" id="QualityGridDisplay" onClick="qaGrid();">
								<img src="/_images/yellow_status.gif">Quality Grid Display
							</li>
							<li class="alt" id="rallyLHProjects" onClick="rallyProjects();">
								<img src="/_images/yellow_status.gif">Rally/LH Project Mapping
							</li>
							<li class="alt" id="searchLHProjects" onClick="solrSearchLog();">
								<img src="/_images/yellow_status.gif">Solr Search Log
							</li>	
						</ul>
					</div>
			<? echo $this->layout()->content; ?>
			</div>
			<!--==| END: Bucket |==-->
