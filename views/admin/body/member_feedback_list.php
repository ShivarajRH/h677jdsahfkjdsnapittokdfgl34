<link rel="stylesheet"	href="<?php echo site_url(); ?>css/datatable/manage.franchises.css" type="text/css" />
 <script type="text/javascript" charset="utf-8">  
var feedback_val ;
var detailRows = [];
var build_territory = 1;		
$(document).ready(function() {
	feedback_val = $('#mfeedback_table').DataTable({
											    	"processing": true,
													"serverSide": true,
													"iDisplayLength" : 20,//pagination count
													"bAutoWidth": false,
													"bDeferRender": true,
													
													"dom": '<"clear">iprftp',
													"sAjaxSource": site_url+"admin/jx_fetch_memberfeedback_detail",		
													"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Member Search: "}, //for loding image
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
												                 if ((response.recordsFiltered/20)>1) {
											                	      $('#mfeedback_table_paginate')[0].style.display = "none";
											                	      $('.dataTables_paginate').show();      	      
										                	     } else {
										                	    	 $('.dataTables_paginate').hide();
										                	    	 $('#mfeedback_table_paginate')[0].style.display = "none";		                	   
										                	     }
												                 
												                 $('#allplandetail').html('');
												                 var htmlcat ='<table cellpadding="3" cellspacing="0" border="0" >';
												                 htmlcat  +='<tr><td colspan="3"><b>Member Feedback Detail</b></td></tr>';
												                 htmlcat +='<tr> <th>Feedback Member Count:</th><th>'+response.tmemcnt+'</th></tr>';	
												                 htmlcat +='<tr> <th>1st Feedback Count:</th><th>'+response.tf1cnt+'</th></tr>';	
												                 htmlcat +='<tr> <th>2nd Feedback Count:</th><th>'+response.tf2cnt+'</th></tr>';	
												                 htmlcat +='<tr> <th>3rd Feedback Count:</th><th>'+response.tf3cnt+'</th></tr>';	
												               								    				
												                 htmlcat +='</table>';
												                 $('#allplandetail').append(htmlcat);
											                }
											              });
											                	
										            },
							            "columns": [  							
								        				{ "data": null,"class":'details-control', "defaultContent":  '' },
								        				{ "data": "memberid" },
								        				{ "data": "membername" },
								        				{ "data": "mobile" },
								        				{ "data": "cretedon" },								        	
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
														}],								
										"initComplete": function (oSettings) {		  	
									        	 new $.fn.dataTable.FixedHeader(feedback_val, {
										              top:true
										            } );
								        	
								        }
			    	});
	    	// For combo plan detail
	    	 $('#mfeedback_table tbody').on( 'click', 'td.details-control', function () {
	    		var tr = $(this).closest('tr');
	    		var row = feedback_val.row( tr );
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
	    			 var memberid = tr.attr('id');
	    			 $.post(site_url+'/admin/jx_get_memberfeedback_detail',{memberid:memberid},function(resp){

	    				 var htmlplan ='<table cellpadding="6" cellspacing="0" border="0" id="totdetail" style="padding-left:50px;" >';
	    				 	 htmlplan  +='<tr><td colspan="6"><b>Franchise Combo Detail</b></td></tr>';
	    					 htmlplan +='<tr><th>S no:</th> <th>Feedback ID</th><th>Product Name</th><th>Price Range</th><th>Brand Name</th><th>Color</th></tr>';
	    				if(resp.member_feedback.length>0)
	    				{
	    			           $.each(resp.member_feedback, function (index, data){		
	    			        	       var sno= index+1;	    			        	   
	    				              htmlplan +='<tr><td>'+sno+'</td><td>'+data.feedback_id+'</td><td>'+data.product_name+'</td><td>'+data.price_range+'</td><td>'+data.brand_name+'</td><td>'+data.color+'</td></tr>';	    				        	    				            
	    					  });
	    			    }
	    			    else
	    			    {
	    			    	htmlplan +='<tr><td colspan="3">No Combo plan</td></tr>';
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

	    	/* $('#mfeedback_table tbody').on( 'click', 'td button.plandownload', function () {
		    	 var sUrl = site_url+'/admin/jx_get_fcombocategory_detail_download?combo_preid='+this.value;
	    		 var iframe = document.createElement('iframe');
	 	        iframe.style.height = "0px";
	 	        iframe.style.width = "0px";
	 	        iframe.src = sUrl;
	 	        document.body.appendChild( iframe );
	    		// $.post(site_url+'/admin/jx_get_fcombocategory_detail_download',{combo_preid:this.value},function(){
		    		 
	    		// });
	    	 }); */
});
</script>
	<h2 class="page_title">Member Feedback List</h2>
<div class="container page_wrap">
	<div class="page_top">
	
	</div>
	
		<table id="mfeedback_table" class="display " cellspacing="0">
			<thead>
				<tr>
				
					<th></th>
					<th>Member ID</th>
					<th>Member Name</th>
					<th>Mobile No</th>
					<th >Created On</th>		
					<th>Action</th>
					
				</tr>
			</thead>
		</table>
</div>
<div id="allplandetail">

</div>
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