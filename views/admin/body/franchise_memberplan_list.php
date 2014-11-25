

<script type="text/javascript" charset="utf-8">  
var plans_val ;
var detailRows = [];
var build_territory = 1;		
$(document).ready(function() {
	plans_val = $('#fmemberplan_table').DataTable({
											    	"processing": true,
													"serverSide": true,
													"iDisplayLength" : 50,//pagination count
													"bAutoWidth": false,
													"bDeferRender": true,
													
													"dom": '<"clear">iprftp',
													"sAjaxSource": site_url+"admin/jx_fetch_franchise_memberplan",		
													"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Franchisee Search: "}, //for loding image
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
											                	      $('#fmemberplan_table_paginate')[0].style.display = "none";
											                	      $('.dataTables_paginate').show();      	      
										                	     } else {
										                	    	 $('.dataTables_paginate').hide();
										                	    	 $('#fmemberplan_table_paginate')[0].style.display = "none";		                	   
										                	     }
												                
												                }
											              });											                
										            },
							            "columns": [  							
								        				{ "data": "null","class":'details-control', "defaultContent":  '' },
								        				{ "data": "fransid" },
								        				{ "data": "fransname" },
								        				{ "data": "locality" },
								        				{ "data": "city" },
								        				{ "data": "mplancnt" },
								        				{ "data": "action" }
								        			        											        
							        			], 
							        	"order": [[1, 'asc']],
							        	"columnDefs": [
														{
														    "targets": [0],
														    "visible": true,
														    "searchable": false,
														    "orderable":false
														},
														{
														    "targets": [5],
														    "visible": true,
														    "searchable": false,
														    "orderable":false
														},
														{
														    "targets": [6],
														    "visible": true,
														    "searchable": false,
														    "orderable":false
														}],								
										"initComplete": function (oSettings) {		  	
									        	 new $.fn.dataTable.FixedHeader(plans_val, {
										              top:true
										            } );
								        	
								        }
			    	});
	    	// For combo plan detail
	    	 $('#fmemberplan_table tbody').on( 'click', 'td.details-control', function () {
	    		var tr = $(this).closest('tr');
	    		var row = plans_val.row( tr );
	    		var idx = $.inArray( tr.attr('id'), detailRows );
	    		if ( row.child.isShown() ) {
	    			if( tr.hasClass( "shown" )){
	    			tr.removeClass('shown');
	    			row.child.hide();
	    		}
	    			// Remove from the 'open' array
	    			detailRows.splice( idx, 1 );
	    		}
	    		else 
	    		{
	    			tr.addClass( 'shown' );
	    			 var franchise_id = tr.attr('id');
	    			 $.post(site_url+'/admin/jx_get_memberplan_cntvalue',{franchise_id:franchise_id},function(resp){

	    				 var htmlplan ='<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;" >';
	    				 	 htmlplan  +='<tr><td colspan="6"><b>Member Plan Detail</b></td></tr>';
	    				 	 htmlplan  +='<tr><td colspan="6"><b>Plan Total Count: '+resp.totcount+'</b></td></tr>';
	    					 htmlplan +='<tr> <th>Plan Name</th><th>Plan Count</th></tr>';
	    				if(resp.plancount.length>0)
	    				{
	    			           $.each(resp.plancount, function (index, data){		    			        	   
	    				              htmlplan +='<tr><td>'+data.plan_name+'</td><td>'+data.pcnt+'</td></tr>';	    				        	    				            
	    					  });
	    			    }
	    			    else
	    			    {
	    			    	htmlplan +='<tr><td colspan="3">No Plan Count</td></tr>';
	    			    }
	    				htmlplan +='</table></br>';
	    				htmlplan +='<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;" >';
   				 	    htmlplan  +='<tr><td colspan="7"><b>Member Detail</b></td></tr>';
   					    htmlplan +='<tr> <th>Member ID</th><th>Member Name</th><th>Plan Type</th><th>Started Month</th><th>End Month</th></tr>';
   					  
	   					if(resp.memberdt.length>0)
	    				{
	    			           $.each(resp.memberdt, function (index, data){		    			        				    			        	   
	    			        	   htmlplan +='<tr><td>'+data.pnh_member_id+'</td><td>'+data.first_name+'</td><td>'+data.plan_name+'</td><td>'+data.stdate+'</td><td>'+data.enddate+'</td></tr>';    				        	    				            
	    					  });
	    			    }
	    			    else
	    			    {
	    			    	htmlplan +='<tr><td colspan="3">No Plan Count</td></tr>';
	    			    }
	    				htmlplan +='</table>';
	    				    row.child( htmlplan ).show();
	    				},'json');
	    		
	    			// Add to the 'open' array
	    			if ( idx === -1 ) {
	    				detailRows.push( tr.attr('id') );
	    			}
	    		}
	    	 });
});
</script>
<h2 class="page_title">Franchise member plan List</h2>
<div class="container page_wrap">
	<div class="page_top">		
		
	</div>
	<section>
		<table id="fmemberplan_table" class="display " cellspacing="0" width="100%">
			<thead>
				<tr>
				<th></th>
					<th>Franchise ID</th>
					<th>Franchise Name</th>
					<th>Locality</th>
					<th>City</th>
					<th>Member Plan Count</th>					
					<th>Action</th>
				</tr>
			</thead>
		</table>
	</section>
</div>
<?php /* ?>
<style>
.dataTables_wrapper {
   float:left;
    width:70%;
}
div#allplandetail {
    float: right;
    margin-top: 66px;
    vertical-align: top;
    width: 25%;
}
</style>
<?php */?>
