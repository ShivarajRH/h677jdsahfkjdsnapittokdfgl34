    var deal_val ;
	var detailRows = [];
	function js_manage_deal(){	
    	 deal_val = $('#deal_table').DataTable({
										    	"processing": true,//for loding image
												"serverSide": true,
												"iDisplayLength" : 50,//pagination count
												"bAutoWidth": true,
												"bDeferRender": true,
												"dom": '<"clear">iTprftp',
												"ajax": {
												"url": site_url+'admin/jx_deal_fetchlist',
												"type": "post",
												"data": function (result){ if(result.search['value']!=''){  setInterval(function() {	},30000);	} }		
												 },
												 "oTableTools": {
													             "aButtons": [{
																                "sExtends": "download",
																                "sButtonText": "Download CSV",
																                "sUrl": site_url+'admin/jx_fetch_dealcsvval'
													              			  }]
												                 },
												 "oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Deal Search: "},									
												 "columns": [{
																   "class":          'details-control',
																   "data":            null,
																   "defaultContent": ''
																},
													            { "data": "itemid" },
													            { "data": "dealfor" },
																{ "data": "pnhid" },
																{ "data": "dealname" },
																{ "data": "brandname" },
																{ "data": "catname" },
																{ "data": "mrp" },
																{ "data": "offerprice" },
																{ "data": "memberprice" },
																{ "data": "stocks" },
																{ "data": "publish" },
																{ "data": "action" }
																],
													"order": [[1, 'asc']],															
													"columnDefs": [{
																	    "targets": [ 0 ],
																	    "visible": true,
																	    "searchable": false,
																	    "orderable":false
																	},
													   			  {
													                   "targets": [ 12 ],
													                   "visible": true,
													                   "searchable": false,
													                   "orderable":false
													               }],
										      		 "fnDrawCallback": function(oSettings)
										                     {
														            var dealressult = oSettings.json;	
														            var build_dealfor = 1;
											                      	var build_brands = 1;
											                      	 var build_cats = 1;
											                      	 var build_status = 1;
											                      // populate brand list 
											                     	 if( $("#deal_table thead th.dealfor select").length)
											                         if($("#deal_table thead th.dealfor select").val())
											                        	 build_dealfor = 0;
											                     	     if(build_dealfor)
											                      	    {
											                           	        $("#deal_table thead th.dealfor").each( function ( )
										                             		 {
												              					 var select = $('<select onclick=\" event.stopPropagation();\"><option value="">Deal For</option></select>');
												              					     select.appendTo( $(this).empty() );
												              					     select.on( 'change', function () 
														              	  			 {  		
													                         	  		
													        	           	     		  
													   		            	    			deal_val.column(2).search($(this).val()).draw();		              		        
												                                      });
												              						select.append( '<option value="1">PNH</option><option value="0">SNP</option>' );
										              			           	   });
										           			           		}  
											                         // populate brand list 
											                     	 if( $("#deal_table thead th.brandnameval select").length)
											                         if($("#deal_table thead th.brandnameval select").val())
											                     	  build_brands = 0;
											                     	     if(build_brands)
											                      	    {
											                           	        $("#deal_table thead th.brandnameval").each( function ( )
										                             		 {
												              					 var select1 = $('<select onclick=\" event.stopPropagation();\"><option value="">Brand</option></select>');
												              					     select1.appendTo( $(this).empty() );
												              					     select1.on( 'change', function () 
														              	  			 {  		
													                         	  			deal_val.column(6).search(''); 
													        	           	     		    $('#category_dropdown').val('');
													   		            	    			deal_val.column(5).search($(this).val()).draw();		              		        
												                                      });
												              			             $.each(dealressult.brandval, function (index, data) 
													              			         {	              			              
													              			    		   	select1.append( '<option value="'+data.brandid+'">'+data.brandname+'</option>' );
													              			    	 });
										              			           	   });
										           			           		}  
										
										                              
										                            // populate category list 		
										                               if( $("thead th.categoryname select").length)
										                               if($("thead th.categoryname select").val())
										                               build_cats = 0;
										                               if(build_cats)
										                               {
										                                   $("thead th.categoryname").each( function ( ) 
										                  			         {
											          			                  var select2 = $('<select onclick=\" event.stopPropagation();\" class="category_dropdown"><option value="">Category</option></select>');
											          			                      select2.appendTo( $(this).empty());
											          			                      select2.on( 'change', function ()
												          					           {
											          			                    	     $('.category_dropdown').val($(this).val());
												          			                    	 deal_val.column(6).search($(this).val()).draw(); 		                            
												          			                    });
											          			                       $.each(dealressult.catval, function (index, data)
																          			     {        				    
														          				            	select2.append( '<option value="'+data.catid+'">'+data.catname+'</option>' );
														          				         });   			                
										          			                   });
										                                }   
										                               if( $("thead th.statusval select").length)
											                               if($("thead th.statusval select").val())
											                            	   build_status = 0;
											                               if(build_status)
											                               {
												                               $("thead th.statusval").each( function ( ) 
											               			              {
												          			                    var select3 = $('<select onclick=\" event.stopPropagation();\" class="status_dropdown"><option value="">Status</option></select>');
												          			                        select3.appendTo($(this).empty());
												          			                        select3.on( 'change', function ()
													          					             {      $('.status_dropdown').val($(this).val());
													          			                    	    deal_val.column(11).search( $(this).val() ).draw();	          		                         
													          			                     });
											       					                      	select3.append( '<option value="1">Published</option><option value="0">Publish</option>' );
														     
											       			                      });
											                               }
													               },
													               "initComplete": function () {
															           new $.fn.dataTable.FixedHeader(deal_val, {
															               top:true
															            } ); 
															        }
													               
										    	});
    	        $('div.dataTables_filter input').addClass('form-filter');
		    	$('#deal_table tbody').on( 'click', 'td.details-control', function () {
		    		var tr = $(this).closest('tr');
		    		var row = deal_val.row( tr );
		    		var idx = $.inArray( tr.attr('id'), detailRows );
		    		if ( row.child.isShown() ) 
		        		{
			    			tr.removeClass( 'shown' );
			    			row.child.hide();
			    			detailRows.splice( idx, 1 );
		    		    }
		    		else
		        		{
			    			tr.addClass( 'shown' );
			    			 var dealitem_id = tr.attr('id');
			    			 $.post(site_url+'/admin/jx_get_dealproducts',{item_id:dealitem_id},function(resp){
			    				     var htmlval ='<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
				    				     htmlval +='<tr roll = "row"><td>Product ID</td><td>Product Name</td><td>Mrp</td><td>Quantity</td><td>Sourceable</td><td>Stock</td><td>Action</td></tr>';
				    		
				    				     if(resp.length>0)
				         				{
						    		         $.each(resp, function (index, data) 
						    			         {	              			              
						    			    		   htmlval +='<tr><td>'+data.prodid+'</td><td><a href="'+site_url+'admin/product/'+data.prodid+'" target=_blank>'+data.prodname+'</a></td><td>'+data.mrp+'</td><td>'+data.quantity+'</td><td>'+data.sourceable+'</td><td>'+data.stocks+'</td><td><a href="'+site_url+'admin/editproduct/'+data.prodid+'" target="_blank"><img src="'+site_url+'images/pencil.png" style="margin-left:10px"></><a href="'+site_url+'admin/product/'+data.prodid+'" target=_blank><img src="'+site_url+'images/preview.png" style="margin-left:10px"></a></td></tr>';
						    			    	 });
				         				 }
				 				        else
				 				        {
				 				        	htmlval +='<tr><td colspan="3">No Product</td></tr>';
				 				        }
				 						htmlval +='</table>';
				    				 row.child( htmlval ).show();
			    				},'json');
			    			if ( idx === -1 ) {detailRows.push( tr.attr('id') );}
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