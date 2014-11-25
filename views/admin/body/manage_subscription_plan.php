<script type="text/javascript" charset="utf-8">  
var plans_val ;
var detailRows = [];
var build_territory = 1;		
$(document).ready(function() {
	plans_val = $('#manageplan_table').DataTable({
											    	"processing": true,
													"serverSide": true,
													"iDisplayLength" : 50,//pagination count
													"bAutoWidth": false,
													"bDeferRender": true,
													
													"dom": '<"clear">iprftp',
													"sAjaxSource": site_url+"admin/jx_fetch_subscription_planlist",		
													"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Plans Search: "}, //for loding image
													"fnServerData": function(sSource, aoData, fnCallback,oSettings )
										            {
														oSettings.jqXHR = $.ajax
											              ({
											               'dataType': 'json',
											                'type'    : 'POST',
											                'url'     : sSource,
											                'data'    : aoData,
											                "success" : function(response) { 

												                fnCallback(response); 
												                if ((response.recordsFiltered/50)>1) {
											                	      $('#manageplan_table_paginate')[0].style.display = "none";
											                	      $('.dataTables_paginate').show();      	      
										                	     } else {
										                	    	 $('.dataTables_paginate').hide();
										                	    	 $('#manageplan_table_paginate')[0].style.display = "none";		                	   
										                	     }
												                }
											              });											                
										            },
							            "columns": [  															        			
								        				{ "data": "splanid" },
								        				{ "data": "planname" },
								        				{ "data": "planamt" },
								        				{ "data": "itemid", "class":'editable-itemid' },
								        				{ "data": "installment" },								        			
								        				{ "data": "totalinstall" },
								        				{ "data": "offermont" },
								        				{ "data": "offerqty" },
								        				{ "data": "monthplanamt" },
								        				{ "data": "totalmonth" }
								        			        											        
							        			], 
							        	"order": [[0, 'asc']]
			    	});

	 // Update Item Id
	  $('#manageplan_table tbody').on( 'click', 'td.editable-itemid', function () {
		   var product_id_value = $(this).closest('tr').attr('id');
	       $("td.editable-itemid").attr('id', product_id_value);
	       var row = plans_val.row( $(this).closest('tr') );
		   $("td.editable-itemid").editable(site_url+'admin/jx_updateplanamt_detail', { 
		          indicator : "<img src="+site_url+"images/ajax-loader.gif>",
		          type   : 'text',
		          event  : "dblclick",
			      submit : 'Update',
			      onsubmit: function(settings, original) {
			    	    if (confirm("Are you sure? You want change the Item Id") == true) {
			    	      return true;
			    	    } else {
			    	      return false;
			    	    }
		      		},
			       callback : function(value, settings) {
			          var tr = $(this).closest('tr');
			    	  var row = plans_val.row( tr );
			    	  var product_idvalue = tr.attr('id');
			    	  if ( row.child.isShown() ) {					     
			    			 $.post(site_url+'/admin/jx_get_productbydeals',{product_idvalue:product_idvalue},function(resp){
			    				 var htmlval =  deal_table_value(resp);
			    				 row.child(htmlval).show();
			    				},'json');
			    	  }
			      }
		   });	  
		});
	
});
</script>
<div class="container page_wrap">
<h2 class="page_title">Manage member subscription plan</h2>

</div>

				  <table id="manageplan_table" class="display " cellspacing="0" width="100%">
			      <thead>
				  <tr>					
					    <th>Plan ID</th>
						<th>Plan Name</th>
						<th>Plan Amount</th>
						<th>Item ID</th>						
						<th>Installment</th>
						<th>Totel Installment</th>				
						<th>Offered Month</th>						
						<th>Offer Qty</th>		
						<th>Plan Amount by Month</th>		
						<th>Total Month</th>					
				  </tr>
				</thead>
				</table>
	