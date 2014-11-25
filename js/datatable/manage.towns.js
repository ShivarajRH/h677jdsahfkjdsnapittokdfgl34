   var state_val;
   var territory_val;
   var towns_val;
   var detailRows = [];
	function js_territory_townlist(){	
		state_val = $('#managestate_table').DataTable({
		    	"processing": true,
				"serverSide": true,
				"iDisplayLength" : 50,//pagination count
				"bAutoWidth": true,
				"bDeferRender": true,
				"dom": '<"clear">iprft',
				"sAjaxSource": site_url+"admin/jx_fetch_manage_states_list",		
				"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "State Search: "}, //for loding image
				"fnServerData": function(sSource, aoData, fnCallback,oSettings )
	            {
					//console.log(aoData.name);
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
		                	      $('#managestate_table_paginate')[0].style.display = "block";		                	        
	                	     } 
		                	 else 
	                	     {	                	    	
	                	    	 $('#managestate_table_paginate')[0].style.display = "none";		                	   
	                	     }
		                }
		              });									                	
	            },
				 "columns": [  	//{ "data": null,"class":'details-control',"defaultContent":  '' },					
				 				{ "data": "stateid" },
				 				{ "data": "statename" },				 			
				 				{ "data": "action" }							        											        
							], 
					"order": [[1, 'asc']],
					"columnDefs": [
									{
									    "targets": [2],
									    "visible": true,
									    "searchable": false,
									    "orderable":false
									}]	
			});
		//Territory datatable Page
		territory_val = $('#manageterritory_table').DataTable({
	    	"processing": true,
			"serverSide": true,
			"iDisplayLength" : 50,//pagination count
			"bAutoWidth": true,
			"bDeferRender": true,
			"dom": '<"clear">iprft',
			"sAjaxSource": site_url+"admin/jx_fetch_manage_territory_list",		
			"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Territory Search: "}, //for loding image
			"fnServerData": function(sSource, aoData, fnCallback,oSettings )
            {
				//console.log(aoData.name);
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
	                	      $('#manageterritory_table_paginate')[0].style.display = "block";		                	        
                	     } 
	                	 else 
                	     {	                	    	
                	    	 $('#manageterritory_table_paginate')[0].style.display = "none";		                	   
                	     }
	                	var build_state = 1;	
	                	// populate State list
		            	if( $("thead th.statesval select").length)
		                	if($("thead th.statesval select").val())
		                		build_state = 0;
		              		
						if(build_state)
						{
							$("thead th.statesval").each( function () {
						    	var select1 = $('<select onclick=\" event.stopPropagation();\" class="state_dropdown"><option value="">State Name</option></select>');
						        	select1.appendTo($(this).empty());
						            select1.on( 'change',function(){						            
						            	  $('.state_dropdown').val($(this).val());
						            	  territory_val.column(0).search($(this).val()).draw();
									});
					                $.each(response.stateval, function (index, data) {					           	
								    	select1.append( '<option value="'+data.stateid+'">'+data.statename+'</option>' );
									});
							});
						}
	                	
	                }
	              });									                	
            },
			 "columns": [ 	{ "data": "statename" },				 			
			 				{ "data": "terriid" },
			 				{ "data": "terriname" },
			 				{ "data": "action" }
						], 
				"order": [[2, 'asc']]
		});
		
		// Town Data Table Page
    	 towns_val = $('#managetown_table').DataTable({
										    	"processing": true,
												"serverSide": true,
												"iDisplayLength" : 50,//pagination count
												"bAutoWidth": true,
												"bDeferRender": true,
												"dom": '<"clear">iprft',
												"sAjaxSource": site_url+"admin/jx_fetch_manage_towns_list",		
												"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Town Search: "}, //for loding image
												"fnServerData": function(sSource, aoData, fnCallback,oSettings )
									            {
													//console.log(aoData.name);
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
										                	      $('#managetown_table_paginate')[0].style.display = "block";		                	        
									                	     } 
										                	 else 
									                	     {	                	    	
									                	    	 $('#managetown_table_paginate')[0].style.display = "none";		                	   
									                	     }
										                	var build_state = 1;	
										                	var build_territory = 1;		
										                	var build_city = 1;		
												             // populate State list
												            	if( $("thead th.stateval select").length)
												                	if($("thead th.stateval select").val())
												                		build_state = 0;
												              		
																if(build_state)
																{
																	$("thead th.stateval").each( function () {
																    	var select1 = $('<select onclick=\" event.stopPropagation();\" class="state_dropdown"><option value="">State</option></select>');
																        	select1.appendTo($(this).empty());
																            select1.on( 'change',function(){
																            	towns_val.column(1).search(''); 												    			    	    	 
											    			            	     $('.territory_dropdown').val('');
											    			            	     towns_val.column(2).search(''); 												    			    	    	 
											    			            	     $('.town_dropdown').val('');
											    			            	     
																            	  $('.state_dropdown').val($(this).val());
																            	  towns_val.column(0).search($(this).val()).draw();
																			});
															                $.each(response.stateval, function (index, data) {					           	
																		    	select1.append( '<option value="'+data.stateid+'">'+data.statename+'</option>' );
																			});
																	});
																}
																
																
																// populate territory list
												            	if( $("thead th.territoryval select").length)
												                	if($("thead th.territoryval select").val())
												                		build_territory = 0;
												
																if(build_territory)
												                {
												                	$("thead th.territoryval").each( function () {												               
												    			    	var select = $('<select onclick=\" event.stopPropagation();\" class="territory_dropdown"><option value="">Territory</option></select>');
												    			    	    select.appendTo( $(this).empty());
												    			    	    select.on( 'change', function () {  
												    			    	    	    towns_val.column(2).search(''); 												    			    	    	 
												    			            	     $('.town_dropdown').val('');
												    			            	     towns_val.column(1).search( $(this).val()).draw();
												    			                });
														    				$.each(response.territval, function (index, data){															    		
														    			    	select.append( '<option value="'+data.territoryid+'">'+data.territoryname+'</option>' );
																			});
																	});
																} 
																
																// populate category list
												            	if( $("thead th.townval select").length)
												                	if($("thead th.townval select").val())
												                		build_city = 0;
												              		
																if(build_city)
																{
																	$("thead th.townval").each( function () {
																    	var select1 = $('<select onclick=\" event.stopPropagation();\" class="town_dropdown"><option value="">Town</option></select>');
																        	select1.appendTo($(this).empty());
																            select1.on( 'change',function(){
																            	 $('.town_dropdown').val($(this).val());
																            	 towns_val.column(2).search($(this).val()).draw();
																			});
															                $.each(response.townval, function (index, data) 
															                {					           	
																		    	select1.append( '<option value="'+data.townid+'">'+data.townname+'</option>' );
																			});
																	});
																}
										                }
										              });									                	
									            },
						            "columns": [  	//{ "data": null,"class":'details-control',"defaultContent":  '' },					
							        				{ "data": "statename" },
							        				{ "data": "territoryname" },
							        				{ "data": "townname"},
							        				{ "data": "createdon"},
							        				{ "data": "hubval" },
							        				{ "data": "franchisecnt"},
							        				{ "data": "exectivename" },							        				
							        				{ "data": "action" }							        											        
						        			], 
						        	"order": [[2, 'asc']],
						        	"columnDefs": [
													{
													    "targets": [7],
													    "visible": true,
													    "searchable": false,
													    "orderable":false
													}],	
													
									   "initComplete": function (oSettings) {		  	
									        //	 new $.fn.dataTable.FixedHeader(towns_val, {
										       //      top:true
										          //   });						      	
							        }
		    	});
    	
    	//Hub link datatable Page
 		hublink_val = $('#managehublink_table').DataTable({
 	    	"processing": true,
 			"serverSide": true,
 			"iDisplayLength" : 20,//pagination count
 			"bAutoWidth": true,
 			"bDeferRender": true,
 			"dom": '<"clear">iprft',
 			"sAjaxSource": site_url+"admin/jx_fetch_manage_hublink_list",		
 			"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Hub  Search: "}, //for loding image
 			"fnServerData": function(sSource, aoData, fnCallback,oSettings )
             {
 				//console.log(aoData.name);
 				oSettings.jqXHR = $.ajax
 	              ({
	 	               'dataType': 'json',
	 	                'type'    : 'POST',
	 	                'url'     : sSource,
	 	                'data'    : aoData,
	 	                "success" : function(response)
	 	                {
	 	                	fnCallback(response);	
	 	                	 if ((response.recordsFiltered/20)>1) 
		                	 {
		                	      $('#managehublink_table_paginate')[0].style.display = "block";		                	        
	                	     } 
		                	 else 
	                	     {	                	    	
	                	    	 $('#managehublink_table_paginate')[0].style.display = "none";		                	   
	                	     }
	 	                	
	 	                }
 	              });									                	
             },
 			 "columns": [ 	{ "data": "hubname" },
 			 				{ "data": "linktown" },				 			
 			 				{ "data": "linkfcs" },
 			 				{ "data": "createdby" },
 			 				{ "data": "action" }
 						], 
 				"order": [[0, 'asc']],
 				"columnDefs": [
								{
								    "targets": [4],
								    "visible": true,
								    "searchable": false,
								    "orderable":false
								}],	
 		});
    	 
    	 
    	  // For Franchisee deatil
     	 $('#managetown_table tbody').on( 'click', 'td a.franchise-detail', function () {
     			var tr = $(this).closest('tr');
     			var row = towns_val.row( tr );
     			var idx = $.inArray( tr.attr('id'), detailRows );
     			if ( row.child.isShown() ) {
     				if( tr.hasClass( "shown-stock" )){
     				tr.removeClass('shown-stock');
     				row.child.hide();
     				}
     				// Remove from the 'open' array
     				detailRows.splice( idx, 1 );
     			}
     			else 
     			{
     				tr.addClass( 'shown-stock' );
     				 var town_idvalue = tr.attr('id');
     				 $.post(site_url+'/admin/jx_get_townfranchisee_list',{town_idvalue:town_idvalue},function(resp){
     					 var htmlval =  franchise_table_value(resp);
     					    row.child( htmlval ).show();
     					},'json');
     			
     				// Add to the 'open' array
     				if ( idx === -1 ) {
     					detailRows.push( tr.attr('id') );
     				}
     			}
     		 });
    	 
    	 //State Detail
    	 $('#add_statedetail').on('click',function(){   
    		 $('.serrormsg').html('');
		    	 $('#stateform').dialog({
                 autoOpen: true,
                 title: 'Add New State',
                 height: 260,
                 width: 400,
                 modal: true,
                 position: ['center',75],
                 buttons: {               
                     'Cancel': function()
                     {
                         $(this).dialog('close');
                     },
                     'Submit':function()
                     {
                    	 $('.serrormsg').html('');
                    	 var statename  = document.getElementById("statename").value;           
                    	 if(statename=="")
                    		 {
                    		    $('.serrormsg').html("Please fill state name");                  
                    		  	return;
                    		 }
                    	 else
                    		 {                 
	            				 $.ajax ({
	            	                 'dataType': 'json',
	            	                 'type'    : 'POST',
	            	                 'url'     : site_url+"admin/add_newstate_name",
	            	                 'data'    : "statename="+statename,
	            	                 "success" : function(resp)
	            	                 {
	        	                	  if(resp.errorid==1)
	        	                		{
	        	                		  $('.sdata').val('');
	        	                		   alert(resp.errormsg_val);
	        	                		   $('#stateform').dialog('close');
	        	                		   towns_val.ajax.reload();
	        	                		}
	        	                	  else
        	                			{
	        	                		  $('.serrormsg').html(resp.errormsg_val);   	        	                		 
        	                			}
	            	                 }
	            				 });                   			
                    		 }
                     }
                }
             });
 	    	
         });
    	 
    	 
    	
  
    	 //Territory  Detail
    	 $('#add_territorydetail').on('click',function(){   
    		 $('.trerrormsg').html('');
		    	 $('#territoryform').dialog({
                 autoOpen: true,
                 title: 'Add New Territory',
                 height: 260,
                 width: 400,
                 modal: true,
                 position: ['center',75],
                 buttons: {               
                     'Cancel': function()
                     {
                         $(this).dialog('close');
                     },
                     'Submit':function()
                     {
                    	 $('.serrormsg').html('');
                    	 var statename  = document.getElementById("trstatename").value;    
                    	 var territoryname  = document.getElementById("trterritoryname").value;    
                    	 if(statename=="")
                    		 {
                    		    $('.trerrormsg').html("Please fill state name");                  
                    		  	return;
                    		 }
                    	 else if(territoryname=="")
                			 {
	                    		 $('.trerrormsg').html("Please fill Territory name");                  
	                 		  	return;
                			 }
                    	 else
                    		 {                 
	            				 $.ajax ({
	            	                 'dataType': 'json',
	            	                 'type'    : 'POST',
	            	                 'url'     : site_url+"admin/add_newterritory_name",
	            	                 'data'    : "statename="+statename+"&territoryname="+territoryname,
	            	                 "success" : function(resp)
	            	                 {
	        	                	  if(resp.errorid==1)
	        	                		{
	        	                		  $('.trdata').val('');
	        	                		   alert(resp.errormsg_val);
	        	                		   $('#territoryform').dialog('close');
	        	                		   towns_val.ajax.reload();
	        	                		 
	        	                		}
	        	                	  else
        	                			{
	        	                		  $('.trerrormsg').html(resp.errormsg_val);   	        	                		 
        	                			}
	            	                 }
	            				 });                   			
                    		 }
                     }
                }
             });
 	    	
         });
    	 
    	 $('.tstatename').on('change',function(){
    		if($(this).val())
    		{
    			 $.ajax ({
	                 'dataType': 'json',
	                 'type'    : 'POST',
	                 'url'     : site_url+"admin/jx_get_statedetail",
	                 'data'    : "statename="+$(this).val(),
	                 "success" : function(resp)
	                 {
	                	 var terri_html='';
	                	 terri_html+='<option value="">Select Territory</option>';
	                	 $.each(resp, function (index, data) {	
	                		 terri_html+='<option value="'+data.trid+'">'+data.trname+'</option>';	                		
							});
	                	 $('.dterritoryval').html(terri_html);
	                	 
	                 }
    			 });
    		}
    	 });
    	 
    	 //Town Detail
    	 $('#add_towndetail').on('click',function(){   
    		 $('.terrormsg').html('');
		    	 $('#townform').dialog({
                 autoOpen: true,
                 title: 'Add New Town',
                 height: 260,
                 width: 400,
                 modal: true,
                 position: ['center',75],
                 buttons: {               
                     'Cancel': function()
                     {
                         $(this).dialog('close');
                     },
                     'Submit':function()
                     {
                    	 $('.serrormsg').html('');
                    	 var statename  = document.getElementById("tstatename").value;    
                    	 var territoryname  = document.getElementById("tterritoryname").value;    
                    	 var townname  = document.getElementById("ttownname").value;    
                    	 if(statename=="")
                    		 {
                    		    $('.terrormsg').html("Please fill state name");                  
                    		  	return;
                    		 }
                    	 else if(territoryname=="")
                			 {
	                    		 $('.terrormsg').html("Please fill Territory name");                  
	                 		  	return;
                			 }
                    	 else if(townname=="")
	            			 {
	                    		 $('.terrormsg').html("Please fill Town name");                  
	                 		  	return;
	            			 }
                    	 else
                    		 {                 
	            				 $.ajax ({
	            	                 'dataType': 'json',
	            	                 'type'    : 'POST',
	            	                 'url'     : site_url+"admin/add_newtown_name",
	            	                 'data'    : "territoryname="+territoryname+"&townname="+townname,
	            	                 "success" : function(resp)
	            	                 {
	        	                	  if(resp.errorid==1)
	        	                		{
	        	                		  $('.tdata').val('');
	        	                		   alert(resp.errormsg_val);
	        	                		   $('#townform').dialog('close');
	        	                		   towns_val.ajax.reload();
	        	                		}
	        	                	  else
        	                			{
	        	                		  $('.terrormsg').html(resp.errormsg_val);   	        	                		 
        	                			}
	            	                 }
	            				 });                   			
                    		 }
                     }
                }
             });
 	    	
         });
	}

	
	function franchise_table_value(resp)
	{
		var htmlfranchise ='<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
		htmlfranchise +='<tr><td colspan="5"><b>Franchises Detail</b></td></tr>';
		htmlfranchise +='<tr roll = "row"><th>Franchise ID</th><th>Franchise Name</th><th>State</th><th>Territory</th><th>Town</th></tr>';
	if(resp.length>0)
	{
           $.each(resp, function (index, data){					    		                        			              
        	   htmlfranchise +='<tr><td><a href="'+site_url+'admin/pnh_franchise/'+data.fid+'" target="_blank">'+data.franchiseid+'</a></td><td><a href="'+site_url+'admin/pnh_franchise/'+data.fid+'" target="_blank">'+data.franchisename+'</a></td><td>'+data.state+'</td><td>'+data.territory+'</td><td>'+data.town+'</td></tr>';
	              });
    }
    else
    {
    	htmlfranchise +='<tr><td colspan="5">No Franchises</td></tr>';
    }
	htmlfranchise +='</table>';
    return htmlfranchise;

	}
	
	function show_current_statedetail(stateid)
	{
		 $("#errormsg").html('');
		 $('#updatestateform').dialog({
			 autoOpen:true,
			 modal:true,
			 height:'200',
			 width:'400',
			autoResize:true,
             open:function(){       		
         		$.post(site_url+"admin/jx_get_state_detail",{stateid:stateid},function(res){
         			$('#ustatename').val(res.sval);
         		},'json');
         		$("#errormsg").html('');
         		$("#updatestateform").html('');
         		var table_html='';
         		table_html+='<table class="datagrid" width="100%">';
         		table_html+='	<thead><tr><span id="errormsg" style="color:red;"></span></tr></thead>';
         		table_html+='	<tbody>';
         		table_html+='       <tr>';
         		table_html+='       	<td>State Name:</td>';
         		table_html+='       	<td><input tye="text" id="ustatename" name="ustatename" ></td>';
         		table_html+='       </tr>';
         		table_html+='	</tbody>';
         		table_html+='</table>';
         		$('#updatestateform').append(table_html);
         	},
             buttons: {               
                 'Cancel': function()
                 {
                     $(this).dialog('close');
                 },
                 'Update':function()
                 {
                	 $("#errormsg").html('');
                
                	 var ustatename  = document.getElementById("ustatename").value;    
                	if(ustatename=="")
            			 {
                		$("#errormsg").html("Please fill State name");                  
                 		  	return;
            			 }
                	 else
                		 {                 
            				 $.ajax ({
            	                 'dataType': 'json',
            	                 'type'    : 'POST',
            	                 'url'     : site_url+"admin/jx_updatestate_detail",
            	                 'data'    : "stateid="+stateid+"&ustatename="+ustatename,
            	                 "success" : function(resp)
            	                 {
        	                	  if(resp.errorval==1)
        	                		{
        	                		  $('.tdata').val('');
        	                		   alert("State value Succesfully Updated");
        	                		   $('#updatestateform').dialog('close');
        	                		   state_val.ajax.reload();
        	                		}
        	                	  else
    	                			{        	                		
        	                		  $('#updatestateform').dialog('close');
    	                			}
            	                 }
            				 });                   			
                		 }
                 }
            }
         });
		
	}
	
	function show_current_territorydetail(territid)
	{
		 $(".utrerrormsg").html('');
		 $('#updateterritoryform').dialog({
			 autoOpen:true,
			 modal:true,
			 height:'200',
			 width:'400',
			autoResize:true,
             open:function(){       		
         		$.post(site_url+"admin/jx_get_territory_detail",{territid:territid},function(res){
         			$('#uterritname').val(res.tname);
         			$('#ut_statename').val(res.stid).prop('selected', true);
         		},'json');
         		
         	},
             buttons: {               
                 'Cancel': function()
                 {
                     $(this).dialog('close');
                 },
                 'Update':function()
                 {
                	 $(".utrerrormsg").html('');
                
                	 var ustateid  = document.getElementById("ut_statename").value;    
                	 var uterritname  = document.getElementById("uterritname").value;    
                	if(ustateid=="")
            			 {
	                		$(".utrerrormsg").html("Please fill State name");                  
	                 		 return;
            			 }
                	else if(uterritname=="")
                		{
	                		$(".utrerrormsg").html("Please fill Territory name");                  
	                		 return;
                		}
                	 else
                		 {                 
            				 $.ajax ({
            	                 'dataType': 'json',
            	                 'type'    : 'POST',
            	                 'url'     : site_url+"admin/jx_updateterritory_detail",
            	                 'data'    : "territid="+territid+"&stid="+ustateid+"&uterritname="+uterritname,
            	                 "success" : function(resp)
            	                 {
        	                	  if(resp.errorval==1)
        	                		{
        	                		  $('.tdata').val('');
        	                		   alert("Territory value Succesfully Updated");
        	                		   $('#updateterritoryform').dialog('close');
        	                		   territory_val.ajax.reload();
        	                		}
        	                	  else
    	                			{        	                		
        	                		  $('#updateterritoryform').dialog('close');
    	                			}
            	                 }
            				 });                   			
                		 }
                 }
            }
         });
	}
	
	function show_current_towndetail(townid)
	{
		 $(".terrormsg").html('');
		$('.tdata').val('');
		 $('#updatetownform').dialog({
			 autoOpen:true,
			 modal:true,
			 height:'260',
			 width:'400',
			autoResize:true,
             open:function(){       		
         		$.post(site_url+"admin/jx_get_town_detail",{townid:townid},function(res){
         			$('#utstatename').val(res.stid).prop('selected', true); 
         			$('#utstatename').trigger('change'); 
         			setTimeout(function() {
         				$('#utterritoryname').val(res.trid).prop('selected', true); 
         				$('#uttownname').val(res.townval);
         			}, 3000);		         			
         		},'json');
         		
         	},
             buttons: {               
                 'Cancel': function()
                 {
                     $(this).dialog('close');
                 },
                 'Update':function()
                 {
                	 $(".terrormsg").html('');
                	 var tstatename  = document.getElementById("utstatename").value;   
                	 var territid  = document.getElementById("utterritoryname").value;   
                	 var townname  = document.getElementById("uttownname").value;    
                	 if(tstatename=="")
        			 {
            			$(".terrormsg").html("Please fill State name");                  
             		  	return;
        			 }
            	 else if(territid=="")
            			 {
                			$(".terrormsg").html("Please fill Territory name");                  
                 		  	return;
            			 }
                	 else if (townname=="")
	        			 {
	             			$(".terrormsg").html("Please fill Town name");                  
	              		  	return;
	         			 }
                	 else
                		 {                 
            				 $.ajax ({
            	                 'dataType': 'json',
            	                 'type'    : 'POST',
            	                 'url'     : site_url+"admin/jx_updatetown_detail",
            	                 'data'    : "townid="+townid+"&townname="+townname+"&territid="+territid,
            	                 "success" : function(resp)
            	                 {
        	                	  if(resp.errorval==1)
        	                		{
        	                		  $('.tdata').val('');
        	                		   alert("Town value Succesfully Updated");
        	                		   $('#updatetownform').dialog('close');
        	                		   towns_val.ajax.reload();
        	                		}
        	                	  else
    	                			{        	                		
        	                		  $('#updatetownform').dialog('close');
    	                			}
            	                 }
            				 });                   			
                		 }
                 }
            }
         });
	   
	}
	


	
	function show_linkedhub_towndetail(hubid){
	
		$('#manage_delivery_hub').dialog({
			 autoOpen:true,
			 modal:true,
			 width:500,
			 height:450,
			autoResize:true,
			open:function(){
				var dlg = $(this);
				var hub_id = hubid;
					$('input[name="hub_id"]',dlg).val(hub_id);
					$('input[name="hub_name"]',dlg).val('');
					$('select[name="town_id[]"]').html('').trigger('liszt:updated');
					$('select[name="fc_id[]"]').html('').trigger('liszt:updated');
					
					dlg.dialog('option', 'title', 'Add Delivery Hub');
					
					if(hub_id)
						dlg.dialog('option', 'title', 'Edit Delivery Hub Details');
					
					$.getJSON(site_url+'/admin/jx_gethubdet/'+hub_id,function(resp){
							if(resp.status == 'error')
							{
								alert(resp.message);
							}else
							{
								var town_opthtml = fc_opthtml = '';
								
								if(resp.hubdet != undefined) 
									$('input[name="hub_name"]',dlg).val(resp.hubdet.hub_name);
									
								if(resp.town_list.length)
								{
									$.each(resp.town_list,function(a,b){
										var stat = 0;
											if(hub_id == b.hub_id && b.is_linked == 1)
												stat = 1;
											else if(b.hub_id == 0 && b.is_linked == 0)
											 	stat = 1;
										if(stat)
											town_opthtml += '<option value="'+b.id+'" '+((hub_id == b.hub_id && b.hub_id != 0 && b.is_linked == 1 )?'selected':'')+' >'+b.town_name+'</option>';	
									});
								}
								$('select[name="town_id[]"]',dlg).html(town_opthtml).trigger('liszt:updated');
								
								if(resp.fc_list.length)
								{
									$.each(resp.fc_list,function(a,b){
										fc_opthtml += '<option value="'+b.employee_id+'"  '+((b.is_linked==1)?'selected':'')+' >'+b.emp_name+'</option>';	
									});
								}
								$('select[name="fc_id[]"]',dlg).html(fc_opthtml).trigger('liszt:updated');
							}
					});
					
					
			},
			buttons:{
				'Submit' : function(){
					var frmEle = $('#manage_delivery_hub form');
					var error_msg = new Array();
					
						$('input[name="hub_name"]',frmEle).val($.trim($('input[name="hub_name"]',frmEle).val()));
						
						if(!$.trim($('input[name="hub_name"]',frmEle).val()).length)
							error_msg.push("Enter Hub name");
						
						if($('select[name="town_id[]"]',frmEle).val() == null)
							error_msg.push("Select atleast one Town");
						
					
						if(error_msg.length)
						{
							alert(error_msg.join("\r\n"));
						}else
						{
							$.post(frmEle.attr('action'),frmEle.serialize(),function(resp){
								if(resp.status == 'error')
								{
									alert(resp.error);
								}else
								{
									alert(resp.message);
									location.href = location.href ;
								}
							},'json');
						}
						
						
				},
				'Cancel' : function(){
					$('#manage_delivery_hub').dialog('close');
				}
			}
	});
}
	
	$('select[name="town_id[]"]').chosen();
	$('select[name="fc_id[]"]').chosen();	