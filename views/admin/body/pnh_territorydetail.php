 <h2 class="page_title">Territory :<?php echo $trdetail['territoryname'];?></h2>
	        <div id="members" Title="Members Details">
							<div>
													
								<div class="dash_bar">
									Territory Name:<span><?php echo $trdetail['territoryname'];?></span>
								</div>
								<div class="dash_bar">
											State Name:<span><?php echo $trdetail['statename'];?></span>
								</div>
								<div class="dash_bar">
											Towncount:<span><?php echo $trdetail['towncount'];?></span>
								</div>
								<div class="dash_bar">
											Franchise Count:<span><?php echo $trdetail['franscount'];?></span>
								</div>
								        
							</div>
						</div>
			<div class="clear"></div>
			<div id="townlist_tab">
				  <ol>
				  		<li><a href="#townlist">Town List</a></li>
				  		<li><a href="#franchise_list">Franchise List</a></li>				  	
				  </ol>
						   <div id="townlist">							
							 <table id="territorytown" class="display " cellspacing="0" width="20%">
								      <thead>
										  <tr>					
											  <th>town</th><th>Territory</th><th>State</th>			
										  </tr>
										</thead>
								  </table>	 
						   </div>	
						  <div id="franchise_list">						
							 <table id="territoryfranchises" class="display " cellspacing="0" width="20%">
								      <thead>
										  <tr>					
											 <th>Franchisee ID </th><th>Franchise Name</th><th>Town</th><th>Territory</th><th>State</th>
										  </tr>
										</thead>
								  </table>															 
					     </div>							  
			 </div>
				  
<script type="text/javascript" charset="utf-8">  
var territory_fval;
var territory_townval;
$(document).ready(function() {
	$('#townlist_tab').tabs();

	//Town List
	territory_townval = $('#territorytown').DataTable({
    	"processing": true,
		"serverSide": true,
		"iDisplayLength" : 50,//pagination count
		"bAutoWidth": false,
		"bDeferRender": true,
		"dom": 'i<"clear">prft',
		"sAjaxSource": site_url+"admin/jx_fetch_location_townval",		
		"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Town Search: "}, //for loding image
		"fnServerData": function(sSource, aoData, fnCallback,oSettings )
				        {
							aoData.push( { "name": "territoryid", "value": "<?=$territoryid?>" } );
							oSettings.jqXHR = $.ajax
				              ({
				               'dataType': 'json',
				                'type'    : 'POST',
				                'url'     : sSource,
				                'data'    : aoData,
				                "success" : function(response) 
				                { 
					                 fnCallback(response); 	    
					                 if ((response.recordsFiltered/50)>1) 
				                	 {
				                	      $('#territorytown_paginate')[0].style.display = "block";		                	        
				            	     } 
				                	 else 
				            	     {	                	    	
				            	    	 $('#territorytown_paginate')[0].style.display = "none";		                	   
				            	     }   
					            }
				              });											                
				        },
				"columns": [  	
							{ "data": "townname" },
							{ "data": "territoryname" },
							{ "data": "statename" }					        
					       ], 
				"order": [[0, 'asc']]
		});
	
	//Franchises List
	territory_fval = $('#territoryfranchises').DataTable({
    	"processing": true,
		"serverSide": true,
		"iDisplayLength" : 50,//pagination count
		"bAutoWidth": false,
		"bDeferRender": true,
		"dom": 'i<"clear">prft',
		"sAjaxSource": site_url+"admin/jx_fetch_location_franchiseeval",		
		"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Franchisee Search: "}, //for loding image
		"fnServerData": function(sSource, aoData, fnCallback,oSettings )
			        {
						aoData.push( { "name": "territoryid", "value": "<?=$territoryid?>" } );
						oSettings.jqXHR = $.ajax
			              ({
			               'dataType': 'json',
			                'type'    : 'POST',
			                'url'     : sSource,
			                'data'    : aoData,
			                "success" : function(response)
			                 { 
				                 fnCallback(response); 	  
				                 if ((response.recordsFiltered/50)>1) 
			                	 {
			                	      $('#territoryfranchises_paginate')[0].style.display = "block";		                	        
			            	     } 
			                	 else 
			            	     {	                	    	
			            	    	 $('#territoryfranchises_paginate')[0].style.display = "none";		                	   
			            	     }     
				              }
			              });											                
			        },
			"columns": [  	
						{ "data": "franchiseid" },
						{ "data": "franchisename" },
						{ "data": "townname" },
						{ "data": "territoryname" },
						{ "data": "statename" }					        
				       ], 
			"order": [[0, 'asc']]
			});
});
</script>
<style>	
#territorytown_wrapper {
   float:left;
    width:30%;
}
#territoryfranchises_wrapper {
   float:left;
    width:45%;
}
 </style>