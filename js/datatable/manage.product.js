    var product_val ;                               //Js Manage Product file
function js_manage_product(){
	var detailRows = [];
	//var button_detail;
    product_val = $('#product_table').DataTable({
			"processing": true,
     		"serverSide": true,
     		"bAutoWidth": true,
			"iDisplayLength" : 50,									
			"bDeferRender": true,
			"dom": '<"toolbar">iTprftp',
			"ajax": {
				"url": site_url+'admin/jx_prdouct_fetchlist',
				"type": "POST",
				"data": function (result) {	if(result.search['value']!=''){  setInterval(function() {	},30000);	} }		
			 },
			 "oTableTools": {
		            "aButtons": [{
				                "sExtends": "download",
				                "sButtonText": "Download CSV",
				                "sUrl": site_url+'admin/jx_fetch_productcsvval'				         
		                        }]
		        },
		    "oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Product Search: "},
			"columns": [  
				{
				   "class":          'details-control',
				   "data":            null,
				   "defaultContent":  ''
				},
				{ "data": "productid" },
				{ "data": "productname" },
				{ "data": "prodmrp", "class":'editable-mrp' },
				{ "data": "brandname" },
				{ "data": "catname" },
				{ "data": "deal_quantity" },
				{ "data": "stocks"},
				{ "data": "sourceable", "class":'editable-source' },
				{ "data": "active" , "class":'editable-active' },
				{ "data": "action"}
			],  // for dropdown
			"order": [[1, 'asc']],												
			"columnDefs": [
							{
							    "targets": [0],
							    "visible": true,
							    "searchable": false,
							    "orderable":false
							},
			               {
			                   "targets": [ 7 ],
			                   "visible": true,
			                   "searchable": false,
			                   "orderable":false
			               },
			               {
			                   "targets": [ 10 ],
			                   "visible": true,
			                   "searchable": false,
			                   "orderable":false
			               }],
			"fnDrawCallback": function(oSettings)
			{  
					var productressult = oSettings.json;														
					var build_brands = 1;
					var build_cats = 1;		
					var build_source = 1;		
						// populate brand list
		            	if( $("#product_table thead th.brandnameval select").length)
		                	if($("#product_table thead th.brandnameval select").val())
		                    	build_brands = 0;
		
						if(build_brands)
		                {
		                	$("#product_table thead th.brandnameval").each( function () {
		    			    	var select = $('<select onclick=\" event.stopPropagation();\" class="brand_dropdown"><option value="">Brand</option></select>');
		    			    	    select.appendTo( $(this).empty());
		    			    	    select.on( 'change', function () {  
		    			            	     product_val.column(5).search(''); 
		    			            	     $('.category_dropdown').val('');
				                	         product_val.column(4).search( $(this).val()).draw();
		    			                });
				    				$.each(productressult.brandval, function (index, data){															    		
				    			    	select.append( '<option value="'+data.brandid+'">'+data.brandname+'</option>' );
									});
							});
						}  
						// populate category list
		            	if( $("thead th.categoryname select").length)
		                	if($("thead th.categoryname select").val())
		                    	build_cats = 0;
		              		
						if(build_cats)
						{
							$("thead th.categoryname").each( function () {
						    	var select1 = $('<select onclick=\" event.stopPropagation();\" class="category_dropdown"><option value="">Category</option></select>');
						        	select1.appendTo($(this).empty());
						            select1.on( 'change',function(){
						            	 $('.category_dropdown').val($(this).val());
										 product_val.column(5).search($(this).val()).draw();
									});
					                $.each(productressult.catval, function (index, data) {					           	
								    	select1.append( '<option value="'+data.catid+'">'+data.catname+'</option>' );
									});
							});
						}
						// populate Sourceable list
						if( $("thead th.sourceableval select").length)
		                	if($("thead th.sourceableval select").val())
		                		build_source = 0;
		              		
						if(build_source)
						{
						 $("thead th.sourceableval").each( function () {
  			                    var select2 = $('<select onclick=\"event.stopPropagation();\" class="source_dropdown"><option value="">Sourceable Status</option></select>');
  			                        select2.appendTo($(this).empty());
  			                        select2.on( 'change', function () {
  			                        	 $('.source_dropdown').val($(this).val());
  			                        	 product_val.column(8).search($(this).val()).draw();	          		                         
      			                     }); 			                     							          			                      
			                      	select2.append( '<option value="1">Sourceable</option><option value="0">Not Sourceable</option>');
   			                });		
						}
		       },
		        "initComplete": function (oSettings) {		  	
		        	 new $.fn.dataTable.FixedHeader(product_val, {
			              top:true
			            } );
		        	
		        }
		      
     });  // Datatable Closed

     $('div.dataTables_filter input').addClass('form-filter');
     // For Deal Deatail
	 $('#product_table tbody').on( 'click', 'td.details-control', function () {
		var tr = $(this).closest('tr');
		var row = product_val.row( tr );
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
			 var product_idvalue = tr.attr('id');
			 $.post(site_url+'/admin/jx_get_productbydeals',{product_idvalue:product_idvalue},function(resp){
				 var htmlval =  deal_table_value(resp);
				    row.child( htmlval ).show();
				},'json');
		
			// Add to the 'open' array
			if ( idx === -1 ) {
				detailRows.push( tr.attr('id') );
			}
		}
	 });
	 // For Stock deatil
	 $('#product_table tbody').on( 'click', 'td a.stock-detail', function () {
			var tr = $(this).closest('tr');
			var row = product_val.row( tr );
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
				 var product_idvalue = tr.attr('id');
				 $.post(site_url+'/admin/jx_get_productbystock',{product_idvalue:product_idvalue},function(resp){
					 var htmlval =  stock_table_value(resp);
					    row.child( htmlval ).show();
					},'json');
			
				// Add to the 'open' array
				if ( idx === -1 ) {
					detailRows.push( tr.attr('id') );
				}
			}
		 });
    // MRP
  $('#product_table tbody').on( 'click', 'td.editable-mrp', function () {
	   var product_id_value = $(this).closest('tr').attr('id');
       $("td.editable-mrp").attr('id', product_id_value);
       var row = product_val.row( $(this).closest('tr') );
	   $("td.editable-mrp").editable(site_url+'admin/jx_updatemrp_detail', { 
	          indicator : "<img src="+site_url+"images/ajax-loader.gif>",
	          type   : 'text',
	          event  : "dblclick",
		      submit : 'Update',
		      onsubmit: function(settings, original) {
		    	    if (confirm("Are you sure? You want change the Product Mrp") == true) {
		    	      return true;
		    	    } else {
		    	      return false;
		    	    }
	      		},
		       callback : function(value, settings) {
		          var tr = $(this).closest('tr');
		    	  var row = product_val.row( tr );
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
   // Sourceable 
  
   $('#product_table').on( 'click', 'td.editable-source', function () {
        var product_idforsource = $(this).closest('tr').attr('id');
        $("td.editable-source").attr('id', product_idforsource);	
         var actval = $(this).closest('tr').find('td.editable-active').text();
       // alert(actval);
        if(actval =="No")
        { alert("Product active status is disable");
          return false;
        }
	     $("td.editable-source").editable(site_url+'admin/jx_updatesourceable_detail', { 
		      indicator : "<img src="+site_url+"images/ajax-loader.gif>",
		      data   : "{'1':'Yes','0':'No'}",
		      type   : "select",
		      event  : "dblclick",
		      submit : "Update",
		      onsubmit: function(settings, original) {
			    	    if (confirm("Are you sure? You want change the product sourceable status") == true) {
			    	      return true;
			    	    } else {
			    	      return false;
			    	    }
		      		},
		      callback : function(value, settings) {
		    	  $(this).closest('tr').find('.editable-source').html('');
		    	  if(value=='No')
    			      $(this).closest('tr').find('.editable-source').append('<span class="notifications red">No</span>');
		    	  else
		    		  $(this).closest('tr').find('.editable-source').append('<span class="notifications green">Yes</span>');	  
		          var tr = $(this).closest('tr');
		    	  var row = product_val.row( tr );
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

 // Isactive
   $('#product_table').on( 'click', 'td.editable-active', function () {
	    var product_idforactive = $(this).closest('tr').attr('id');
	     $("td.editable-active").attr('id', product_idforactive);			
		 $("td.editable-active").editable(site_url+'admin/jx_updateisactive_detail', { 
		      indicator : "<img src="+site_url+"images/ajax-loader.gif>",
		      data   : "{'1':'Yes','0':'No'}",
		      type   : "select",
		      event  : "dblclick",
		      submit : "Update",
		      onsubmit: function(settings, original) {
		    	    if (confirm("Are you sure? You want change the product active status") == true) {
		    	      return true;
		    	    } else {
		    	      return false;
		    	    }
	      		},
	    	  callback : function(value, settings) {
	    		  $(this).closest('tr').find('.editable-active').html('');
	    		  if(value=='No')
	    			  {
	    			  $(this).closest('tr').find('.editable-source').html('');
	    			  $(this).closest('tr').find('.editable-source').append('<span class="notifications red">No</span>');
	    			  $(this).closest('tr').find('.editable-active').append('<span class="notifications red">No</span>');
	    			  }
	    		  else
	    			  {
	    			  $(this).closest('tr').find('.editable-active').append('<span class="notifications green">Yes</span>');
	    			  }
	    		  var tr = $(this).closest('tr');
		    	  var row = product_val.row( tr );
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
} 
 // main function closed
		   
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
		//console.log(oConfig);
		var oParams = this.s.dt.oApi._fnAjaxParameters( this.s.dt);
		//console.log($.param(oParams));
	    var iframe = document.createElement('iframe');
	        iframe.style.height = "0px";
	        iframe.style.width = "0px";
	        iframe.src = oConfig.sUrl+"?"+$.param(oParams);
	        //iframe.src = oConfig.sUrl;
	        document.body.appendChild( iframe );
	},
	"fnSelect": null,
	"fnComplete": null,
	"fnInit": null
};

function deal_table_value(resp)
{
	 var deal_for;
	 var htmldeal ='<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
		 htmldeal  +='<tr><td colspan=3><b>Deal Detail</b></td></tr>';
		 htmldeal +='<tr roll = "row"><td>Deal For</td><td>Deal ID</td><td>PNH ID</td><td>Deal Name</td><td>Mrp</td><td>Offer/DP Price</td><td>Member Price</td><td>Linked <br>Product Quantity</td><td>Deal Status</td><td>Deal Sourceablity</td><td>Action</td></tr>';
		if(resp.length>0)
		{
	           $.each(resp, function (index, data){	
		              if(data.pnhstatus == '1')
		            	  deal_for = "PNH";
		              else
		            	  deal_for = "SNP";						    		                        			              
		              htmldeal +='<tr><td>'+deal_for+'</td><td>'+data.dealid+'</td><td>'+data.pnhid+'</td><td><a href="'+site_url+'admin/deal/'+data.dealid+'" target=_blank>'+data.dealname+'</a></td>';
		              htmldeal +='<td>'+data.mrp+'</td><td>'+data.offerprice+'</td><td>'+data.memberprice+'</td><td>'+data.qty+'</td><td>'+data.dealstatus+'</td><td>'+data.availablilty+'</td>';
		              htmldeal +='<td><a href="'+site_url+'admin/pnh_editdeal/'+data.dealid+'" target="_blank"><img src="'+site_url+'images/pencil.png" style="margin-left:10px"></><a href="'+site_url+'admin/deal/'+data.dealid+'" target=_blank><img src="'+site_url+'images/preview.png" style="margin-left:10px"></a></td></tr>';
			  });
	    }
	    else
	    {
	    	htmldeal +='<tr><td colspan="3">No Deal</td></tr>';
	    }
		htmldeal +='</table>';
	    return htmldeal;
}

function stock_table_value(resp)
{
		var htmlstock ='<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
		    htmlstock +='<tr><td colspan="5"><b>Stock Detail</b></td></tr>';
		    htmlstock +='<tr roll = "row"><td>Stock ID</td><td>Mrp</td><td>Expiry On</td><td>Quantity</td><td>Location Id</td></tr>';
		if(resp.length>0)
		{
	           $.each(resp, function (index, data){					    		                        			              
		              htmlstock +='<tr><td>'+data.stock_id+'</td><td>'+data.mrp+'</td><td>'+data.expiry_on+'</td><td>'+data.qty+'</td><td>'+data.location_id+'</td></tr>';
		              });
	    }
	    else
	    {
	    	htmlstock +='<tr><td colspan="5">No Stock</td></tr>';
	    }
		htmlstock +='</table>';
	    return htmlstock;
}
		
		