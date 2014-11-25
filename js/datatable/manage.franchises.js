    var franchises_val ;
	var detailRows = [];
	function js_manage_franchises(){	
    	 franchises_val = $('#franchises_table').DataTable({
										    	"processing": true,
												"serverSide": true,
												"iDisplayLength" : 50,//pagination count
												"bAutoWidth": true,
												"bDeferRender": true,
												"dom": '<"clear">iTprftp',
												"sAjaxSource": site_url+"admin/jx_fetch_franchisesval",		
												"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Franchises Search: "}, //for loding image
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
										                	var build_territory = 1;
															var build_city = 1;		
															var build_status = 1;		
																// populate brand list
												            	if( $("#franchises_table thead th.territoryval select").length)
												                	if($("#franchises_table thead th.territoryval select").val())
												                		build_territory = 0;
												
																if(build_territory)
												                {
												                	$("thead th.territoryval").each( function () {												               
												    			    	var select = $('<select onclick=\" event.stopPropagation();\" class="territory_dropdown"><option value="">Territory</option></select>');
												    			    	    select.appendTo( $(this).empty());
												    			    	    select.on( 'change', function () {  
												    			    	    	franchises_val.column(4).search(''); 
												    			    	    	    $('.territory_dropdown').val($(this).val());
												    			            	     $('.town_dropdown').val('');
												    			            	     franchises_val.column(3).search( $(this).val()).draw();
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
																            	 franchises_val.column(4).search($(this).val()).draw();
																			});
															                $.each(response.townval, function (index, data) {					           	
																		    	select1.append( '<option value="'+data.townid+'">'+data.townname+'</option>' );
																			});
																	});
																}
																// populate Sourceable list
																if( $("thead th.statusval select").length)
												                	if($("thead th.statusval select").val())
												                		build_status = 0;
												              		
																if(build_status)
																{
																 $("thead th.statusval").each( function () {
										  			                    var select2 = $('<select onclick=\"event.stopPropagation();\" class="status_dropdown"><option value="">Status</option></select>');
										  			                        select2.appendTo($(this).empty());
										  			                        select2.on( 'change', function () {
										  			                        	 $('.status_dropdown').val($(this).val());
										  			                        	franchises_val.column(7).search($(this).val()).draw();	          		                         
										      			                     }); 			                     							          			                      
													                      	select2.append( '<option value="1">Live</option><option value="0">Suspended</option>');
										   			                });		
																}								                
										                    } 
										              });
									            },
						            "columns": [  							
							        				{ "data": "fran_id" },
							        				{ "data": "franchise_name" },
							        				{ "data": "cityname" },
							        				{ "data": "territoryname"},
							        				{ "data": "townname" },
							        				{ "data": "owners" },
							        				{ "data": "regon" },
							        				{ "data": "status" },
							        				{ "data": "action" }							        
						        			], 
						        	"order": [[0, 'asc']],
						        	"columnDefs": [
													{
													    "targets": [8],
													    "visible": true,
													    "searchable": false,
													    "orderable":false
													}],								
									"initComplete": function (oSettings) {		  	
								        	 new $.fn.dataTable.FixedHeader(franchises_val, {
									              top:true
									            } );
							        	
							        }
		    	});
	}

    TableTools.BUTTONS.download = {
    	    "sAction": "text",
    	    "sTag": "default",
    	    "sFieldBoundary": "",
    	    "sFieldSeperator": "\t",
    	    "sNewLine": "<br>",
    	    "sToolTip": "",
    	    "sButtonClass": "DTTT_button_text",
    	    "sButtonClassHover": "DTTT_button_text_hover",
    	    "sButtonText": "Download",
    	    "mColumns": "all",
    	    "bHeader": true,
    	    "bFooter": true,
    	    "sDiv": "",
    	    "fnMouseover": null,
    	    "fnMouseout": null,
    	    "fnClick": function( nButton, oConfig ) {
    	        var oParams = this.s.dt.oApi._fnAjaxParameters( this.s.dt );
    	        var iframe = document.createElement('iframe');
    	        iframe.style.height = "0px";
    	        iframe.style.width = "0px";
    	        iframe.src = oConfig.sUrl+"?"+$.param(oParams);
    	        document.body.appendChild( iframe );
    	    },
    	    "fnSelect": null,
    	    "fnComplete": null,
    	    "fnInit": null
    	};