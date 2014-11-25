    var orders_val;
	var detailRows = [];
	  //$( "#delete-dialog" ).dialog( "open" );

	function js_members_planlist(){	
    	 orders_val = $('#memberplan_table').DataTable({
										    	"processing": true,
												"serverSide": true,
												"iDisplayLength" : 50,//pagination count
												"bAutoWidth": true,
												"bDeferRender": true,
												"dom": '<"clear">iprfTtp',
												"sAjaxSource": site_url+"admin/jx_fetch_membersplan_detail",		
												"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Members Search: "}, //for loding image
												"fnServerData": function(sSource, aoData, fnCallback,oSettings )
									            {
													//console.log(aoData.name);
													oSettings.jqXHR = $.ajax
										              ({
										               'dataType': 'json',
										                'type'    : 'POST',
										                'url'     : sSource,
										                'data'    : aoData,
										                "success" : function(response) { 
										                	fnCallback(response);
										                	//for pagenation link hide
										                	if ((response.recordsFiltered/50)>1) {
											                	      $('#memberplan_table_paginate')[0].style.display = "none";
											                	      $('.dataTables_paginate').show();      	      
										                	     } else {
										                	    	 $('.dataTables_paginate').hide();
										                	    	 $('#memberplan_table_paginate')[0].style.display = "none";		                	   
										                	     }
										                var build_franchise = 1;		
										             // populate category list
										            	if( $("thead th.franchisename select").length)
										                	if($("thead th.franchisename select").val())
										                		build_franchise = 0;
										              		
														if(build_franchise)
														{
															$("thead th.franchisename").each( function () {
														    	var select1 = $('<select onclick=\" event.stopPropagation();\" class="franchise_dropdown"><option value="">Franchises Name</option></select>');
														        	select1.appendTo($(this).empty());
														            select1.on( 'change',function(){	
														            	  $('.franchise_dropdown').val($(this).val());
														            	 orders_val.column(1).search($(this).val()).draw();
																	});
													                $.each(response.franchise_list, function (index, data) {					           	
																    	select1.append( '<option value="'+data.franchiseid+'">'+data.franchisename+'</option>' );
																	});
															});
														}
										                }
										              });									                	
									            },
						            "columns": [  	{ "data": "memplanid","class":'details-control'},					
							        				{ "data": "memberid" },
							        				{ "data": "fransname" },
							        				{ "data": "memname" },
							        				{ "data": "memmobile" },
							        				{ "data": "planname"},
							        				{ "data": "installment" },
							        				{ "data": "planamount" },
							        				{ "data": "receiptamt" },
							        				{ "data": "statdate" },
							        				{ "data": "enddate" },
							        				{ "data": "paystatus" },
							        				{ "data": "bulkactive" },
							        				{ "data": "action" }							        											        
						        			], 
						        	"order": [[0, 'asc']],
						        	"columnDefs": [
													{
													    "targets": [12],
													    "visible": true,
													    "searchable": false,
													    "orderable":false
													},
													{
													    "targets": [13],
													    "visible": true,
													    "searchable": false,
													    "orderable":false
													}],	
													"oTableTools": {
											            "aButtons": [{
														                "sExtends": "activeplan",
														                "sButtonText": "Active Plan"
														              		         
												                        }
											                       ],
											                        
											        },
									   "initComplete": function (oSettings) {		  	
									        //	 new $.fn.dataTable.FixedHeader(orders_val, {
										       //      top:true
										          //   });						      	
							        }
		    	});
    	 
    	 $('#ToolTables_memberplan_table_0').on('click',function(){   
    		 var tpvalue =0;
    		 var memdetail = [];  	
    		 var franchiseid1 = $('.franchise_dropdown').val();
 	    	 var columnData = orders_val.$(".call-checkbox:checked", {"page": "all"});
 	    	 columnData.each(function(index,elem){
 	    		memdetail.push( $(this).attr('name'));
 	    		 var pvalue = $(elem).val();
 	    		 tpvalue += parseInt(pvalue);	    	
 	    	 });
 	    	 if(tpvalue>0)
 	    	 {
 	    		 $('#balanceval').val('');
 	    		 $('#receiptval').val('');
 	    		 $('.errorbal').hide();$('.errormsg1').hide();
 	    		 $('#validatereceipt').show();
		 	   	 $('#totalamount').empty();
		    	 $('#totalamount').val(tpvalue);
		    	 $('#dialog-form').dialog({
                 autoOpen: true,
                 title: 'Total Plan Amount Detail',
                 height: 350,
                 width: 470,
                 modal: true,
                 position: ['center',75],
                 buttons: {               
                     'Canel': function()
                     {
                         $(this).dialog('close');
                     },
                     'Reconcile':function() {
                    	 var membamount  = document.getElementById("totalamount").value;
                    	 var receiptval  = document.getElementById("receiptval").value;      
                    	 var balanceval  = document.getElementById("balanceval").value;  
                    	 var receiptidval  = document.getElementById("receiptidval").value;
                    	 var totamount = balanceval - membamount;
                    	 var franchiseid = $('.franchise_dropdown').val();
                    	 if(receiptval=="")
                    		 {
                    		    $('.errormsg').show();
                    		  	return;
                    		 }else if(totamount<0)
                    			 {
                    			    $('.errorbal').show();
                        		  	return;
                    			 }else{                 
                    				 $.ajax ({
                    	                 'dataType': 'json',
                    	                 'type'    : 'POST',
                    	                 'url'     : site_url+"admin/update_planamount_detail",
                    	                 'data'    : "receiptval="+receiptidval+"&franchiseid="+franchiseid+"&balanceval="+balanceval+"&unreconcileamt="+totamount+"&memdetail="+memdetail,
                    	                 "success" : function(resp) {
                    	                	 if(resp.errorval==1)
                    	                		 {
                    	                		 	window.location=site_url+"admin/subscriptionplan_members_list";
                    	                		 }
                    	                 }
                    				 });                   			
                    		 }
                     }
                }
             });
 	    	}else if(franchiseid1=="")
 	    		{
 	    		 alert("please select Franchise");
 	    		}
 	        	else 
 	    		{
 	        		 alert("please select Member active box");
 	    	}
         });
    	 
    	 $('#memberplan_table').on('click','#flowcheckall',function(){   
    		 var franchiseid = $('.franchise_dropdown').val();
    		 if(franchiseid=="")
			 {
			        alert('Please select Franchise');
			        return false;
			 }else
				 {
				 
				  $('#memberplan_table tbody input[type="checkbox"]').prop('checked', this.checked);
				 }
    	 });
    	 
    	 $('#memberplan_table').on('click','.call-checkbox',function(){   
    		 var franchiseid = $('.franchise_dropdown').val();
    		 if(franchiseid=="")
			 {
			        alert('Please select Franchise');
			        return false;
			 }
    	 });
    	 
    	 $( "#receiptidval" ).keyup(function( event ) {
    		 this.value = this.value.replace(/[^0-9\.]/g,'');
    	 });
    	 $('#validatereceipt').click(function(){
    		 $('.errormsg1').hide();
    		 var membamount  = document.getElementById("totalamount").value;
    		 var franchiseid = $('.franchise_dropdown').val();
    		 var receiptidval  = document.getElementById("receiptidval").value; 
    		 if(receiptidval=="")
    			 {
	    			  $('.errormsg1').show();
	    			  $('.errormsg1').html('Receipt ID value field is empty');
    			 }
    		 else if(franchiseid=="")
    			 {
    			    $('.errormsg1').show();
   			        $('.errormsg1').html('please select Franchise');
    			 }else
    				 {
    				 	$.ajax ({
    	                 'dataType': 'json',
    	                 'type'    : 'POST',
    	                 'url'     : site_url+"admin/jx_franchise_receipt_validate",
    	                 'data'    : "receiptval="+receiptidval+"&franchiseid="+franchiseid,
    	                 "success" : function(resp) {
    	                	if(resp.errorcode=='1')
    	                		{
    	                		      $('#receiptval').val(resp.reciptamt);
    	                		      var totamount = resp.unreconcilamt - membamount;
    	                		      $('#balanceval').val('');
			    	                  $('#balanceval').val(resp.unreconcilamt);
    	                		      if(totamount>=0){
			    	                	 $('#validatereceipt').hide();
			    	                	 $('#receiptval').val('');
			    	                	 $('#receiptval').val(resp.reciptamt);
    	                		      }else
    	                		    	  {
    	                		    	    $('.errormsg1').show();
    	                 			        $('.errormsg1').html('Franchise Receipt balance < franchise member balance please check.');
    	                		    	  }
    	                		}
    	                	else
    	                		{
    	                		  $('.errormsg1').show();
    	         			      $('.errormsg1').html(resp.mgsvalue);   	                		
    	                		}
    	                	 
    	                 }
    	             });
    				 
    				 }
    	 });
    		
    	// For Receipt Deatail
    	 $('#memberplan_table tbody').on( 'click', 'td.details-control', function () {
    		var tr = $(this).closest('tr');
    		var row = orders_val.row( tr );
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
    			 var memberplan_id = tr.attr('id');
    			 $.post(site_url+'/admin/jx_get_member_plan_payment_detail',{memberplan_id:memberplan_id},function(resp){
    				 var htmlval =  memberplan_table_value(resp);
    				    row.child( htmlval ).show();
    				},'json');
    		
    			// Add to the 'open' array
    			if ( idx === -1 ) {
    				detailRows.push( tr.attr('id') );
    			}
    		}
    	 });
    	 
	}

  /*  TableTools.BUTTONS.bulkactive = {
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
    	    "fnClick": function() {
    	    	//$('#allplanvalue').hide();
    	      //  var column = orders_val.column(9);
    	       // column.visible(! column.visible() );   
    	    },
    	    "fnSelect": null,
    	    "fnComplete": null,
    	    "fnInit": null
    	};*/
    
    TableTools.BUTTONS.activeplan = {
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
    	    "fnClick": function() {
    	    /*	var tpvalue =0;
    	    	var columnData = orders_val.$(".call-checkbox:checked", {"page": "all"});
    	    	columnData.each(function(index,elem){
    	    	    var pvalue = $(elem).val();
    	    	    tpvalue += parseInt(pvalue);
    	    	});
    	    if(tpvalue>0)
    	    	{
    	    	$('#totalamount').empty();
    	    	$('#allplanvalue').show();
    	    	$('#totalamount').append('Rs:'+tpvalue);
    	    	}*/
    	    	
    	    },
    	    "fnSelect": null,
    	    "fnComplete": null,
    	    "fnInit": null
    	};
    
    function memberplan_table_value(resp)
    {
    	var planval = resp.planval;
    	var familyval = resp.familyval;
    	 var htmldeal ='<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    		 htmldeal  +='<tr><td colspan=3><b>Plan Receipt Detail</b></td></tr>';
    		 htmldeal +='<tr roll = "row"><td>S:No</td><td>Paid Status</td><td>Receipt ID</td><td>Receipt Amount</td><td>Paid Date</td></tr>';
    		if(planval.length>0)
    		{
    			
    	           $.each(planval, function (index, data){	
    		             	    var sno= index+1;		                        			              
    		              htmldeal +='<tr><td>'+sno+'</td><td>'+data.statusval+'</td><td>'+data.receiptid+'</td><td>'+data.amount+'</td><td>'+data.date+'</td></tr>';
    		             });
    	    }
    	    else
    	    {
    	    	htmldeal +='<tr><td colspan="3">No Receipt detail</td></tr>';
    	    }
    		htmldeal +='</table>';
    		htmldeal +='<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
    		htmldeal  +='<tr><td colspan=3><b>Member Family Detail</b></td></tr>';
    		htmldeal +='<tr roll = "row"><td>S:No</td><td>Name</td><td>Relationship</td><td>Age</td></tr>';
    		if(familyval.length>0)
    		{
    			
    	           $.each(familyval, function (index, data){	
    		             	    var sno= index+1;		                        			              
    		              htmldeal +='<tr><td>'+sno+'</td><td>'+data.name+'</td><td>'+data.relationship+'</td><td>'+data.age+'</td></tr>';
    		             });
    	    }
    	    else
    	    {
    	    	htmldeal +='<tr><td colspan="3">No Family member Detail</td></tr>';
    	    }
    		htmldeal +='</table>';
    	    return htmldeal;
    }
   
   
