<link rel="stylesheet"	href="<?php echo site_url(); ?>css/datatable/manage.franchises.css" type="text/css" />
 <script type="text/javascript" charset="utf-8">  
var plans_val ;
var detailRows = [];
var build_territory = 1;		
$(document).ready(function() {
	plans_val = $('#fcomboplan_table').DataTable({
											    	"processing": true,
													"serverSide": true,
													"iDisplayLength" : 20,//pagination count
													"bAutoWidth": false,
													"bDeferRender": true,
													
													"dom": '<"clear">iprftp',
													"sAjaxSource": site_url+"admin/jx_fetch_franchise_comboplan",		
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
												                 if ((response.recordsFiltered/20)>1) {
											                	      $('#fcomboplan_table_paginate')[0].style.display = "none";
											                	      $('.dataTables_paginate').show();      	      
										                	     } else {
										                	    	 $('.dataTables_paginate').hide();
										                	    	 $('#fcomboplan_table_paginate')[0].style.display = "none";		                	   
										                	     }
												                 
												                 $('#allplandetail').html('');
												                 var htmlcat ='<table cellpadding="3" cellspacing="0" border="0" >';
												                 htmlcat  +='<tr><td colspan="3"><b>Franchise Combo Detail</b></td></tr>';
												                 htmlcat +='<tr> <th>Category Name</th><th>Brand Name</th><th>Preference Count</th></tr>';	
												                 $.each(response.catval, function (index, data){	
												                	 htmlcat +='<tr><td>'+data.catname+'</td><td>';
												                	 $.each(data.brandval, function (index, branval){	
												                	 htmlcat +='<table><tr><td>'+branval.brandname+'</td></tr></table>';	
												                 });	
												                	 htmlcat +='</td><td>';
												                	 $.each(data.brandval, function (index, cntvl){	
													                	 htmlcat +='<table><tr><td>'+cntvl.conval+'</td></tr></table>';	
													                 });	
												                	 htmlcat +='</td></tr>';	
												                 });									    				
												                 htmlcat +='</table>';
												                 $('#allplandetail').append(htmlcat);

												              // populate territory list
													            	if( $("#fcomboplan_table thead th.territoryval select").length)
													                	if($("#fcomboplan_table thead th.territoryval select").val())
													                		build_territory = 0;
													
																	if(build_territory)
													                {
													                	$("thead th.territoryval").each( function () {												               
													    			    	var select = $('<select onclick=\" event.stopPropagation();\" class="territory_dropdown"><option value="">Territory</option></select>');
													    			    	    select.appendTo( $(this).empty());
													    			    	    select.on( 'change', function () {  
													    			    	    	//franchises_val.column(4).search(''); 
													    			    	    	    $('.territory_dropdown').val($(this).val());			
													    			            	     plans_val.column(4).search( $(this).val()).draw();
													    			                });
															    				$.each(response.territval, function (index, data){															    		
															    			    	select.append( '<option value="'+data.territoryid+'">'+data.territoryname+'</option>' );
																				});
																		});
																	}  
											                }
											              });
											                	
										            },
							            "columns": [  							
								        				{ "data": "comid","class":'details-control' },
								        				{ "data": "fransid" },
								        				{ "data": "fransname" },
								        				{ "data": "mobile" },
								        				{ "data": "territoryname" },
								        				{ "data": "locality" },
								        				{ "data": "city" },
								        				{ "data": "action" }
								        			        											        
							        			], 
							        	"order": [[0, 'asc']],
							        	"columnDefs": [
														{
														    "targets": [7],
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
	    	 $('#fcomboplan_table tbody').on( 'click', 'td.details-control', function () {
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
	    			 var combo_preid = tr.attr('id');
	    			 $.post(site_url+'/admin/jx_get_combocategory_detail',{combo_preid:combo_preid},function(resp){

	    				 var htmlplan ='<table cellpadding="5" cellspacing="0" border="0" id="totdetail" style="padding-left:50px;" >';
	    				 	 htmlplan  +='<tr><td colspan="3"><b>Franchise Combo Detail</b></td><td colspan="3"><a href="'+site_url+'/admin/jx_get_fcombocategory_detail_download/'+combo_preid+'"><button class="plandownload" value="'+combo_preid+'">Download</button></a></td></tr>';
	    					 htmlplan +='<tr> <th>Gender</th><th>Catid</th><th>Category Name</th><th>Preference</th><th>Brand Id</th><th>Brand Name</th></tr>';
	    				if(resp.franchise_plan.length>0)
	    				{
	    			           $.each(resp.franchise_plan, function (index, data){		    			        	   
	    				              htmlplan +='<tr><td>'+data.gender_attr+'</td><td>'+data.catid+'</td><td>'+data.catname+'</td><td>'+data.prfno+'</td><td>'+data.brandid+'</td><td>'+data.brandname+'</td></tr>';	    				        	    				            
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

	    	/* $('#fcomboplan_table tbody').on( 'click', 'td button.plandownload', function () {
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
	<h2 class="page_title">Franchise combo plan List</h2>
<div class="container page_wrap">
	<div class="page_top">
	
	</div>
	
		<table id="fcomboplan_table" class="display " cellspacing="0">
			<thead>
				<tr>
				
					<th>Plan ID</th>
					<th>Franchise ID</th>
					<th>Franchise Name</th>
					<th>Mobile No</th>
					<th class="territoryval">Territory Name</th>
					<th>Locality</th>
					<th>City</th>
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