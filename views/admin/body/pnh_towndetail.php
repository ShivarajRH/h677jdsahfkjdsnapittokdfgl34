 <h2 class="page_title">Town:<?php echo $towndetail['townname'];?></h2>
	        <div id="members" Title="Members Details">
							<div>
								<div class="dash_bar">
									Town Name : <span><?php echo $towndetail['townname'];?></span>
								</div>								
								<div class="dash_bar">
									Territory Name:<span><?php echo $towndetail['territoryname'];?></span>
								</div>
								<div class="dash_bar">
											State Name:<span><?php echo $towndetail['statename'];?></span>
								</div>
								<div class="dash_bar">
											Linked to Hub:<span><?php echo $towndetail['hub_name'];?></span>
								</div>
								<div class="dash_bar">
											Franchise Count:<span><?php echo $towndetail['franscount'];?></span>
								</div>
								        
							</div>
						</div>
			<div class="clear"></div>
			<div id="townlist_tab">
				  <ol>
				  		<li><a href="#franchise_list">Franchise List</a></li>				  	
				  		
				  </ol>
						  <div id="franchise_list">								
							 <table id="townfranchises" class="display " cellspacing="0" width="20%">
								      <thead>
										  <tr>					
											 <th>Franchisee ID </th><th>Franchise Name</th><th>Town</th><th>Territory</th><th>State</th>
										  </tr>
										</thead>
								  </table>						 
					     </div>		
			 </div>				  
<script type="text/javascript" charset="utf-8">  
$(document).ready(function() {
	$('#townlist_tab').tabs();

	//Franchises List
	 $('#townfranchises').DataTable({
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
			aoData.push( { "name": "townid", "value": "<?=$townid?>" } );
			oSettings.jqXHR = $.ajax
              ({
               'dataType': 'json',
                'type'    : 'POST',
                'url'     : sSource,
                'data'    : aoData,
                "success" : function(response) { 
	                 fnCallback(response); 	 
	                 if ((response.recordsFiltered/50)>1) 
                	 {
                	      $('#townfranchises_paginate')[0].style.display = "block";		                	        
            	     } 
                	 else 
            	     {	                	    	
            	    	 $('#townfranchises_paginate')[0].style.display = "none";		                	   
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
#townfranchises_wrapper {
   float:left;
    width:45%;
}
</style>
 