    var admin_val ;
	var detailRows = [];
	function js_manage_admin(){	
    	 admin_val = $('#admin_table').DataTable({
										    	"processing": true,
												"serverSide": true,
												"iDisplayLength" : 50,//pagination count
												"bAutoWidth": true,
												"bDeferRender": true,
												"dom": '<"clear">iTprftp',
												"sAjaxSource": site_url+"admin/jx_fetch_adminval",		
												"oLanguage": {"sProcessing": "<img src='"+site_url+"/images/jx_loading.gif'>" ,"sSearch": "Adminuser Search: "}, //for loding image
												"fnServerData": function(sSource, aoData, fnCallback,oSettings )
									            {
													oSettings.jqXHR = $.ajax
										              ({
										               'dataType': 'json',
										                'type'    : 'POST',
										                'url'     : sSource,
										                'data'    : aoData,
										                "success" : function(response) { fnCallback(response); }
										              });
										                	
									            },
						            "columns": [  							
							        				{ "data": "username" },
							        				{ "data": "name" },
							        				{ "data": "email" },
							        				{ "data": "access" },
							        				{ "data": "action"}							        				        											      
						        			], 
						        	"order": [[0, 'asc']],
						        	"columnDefs": [
													{
													    "targets": [4],
													    "visible": true,
													    "searchable": false,
													    "orderable":false
													}],								
									"initComplete": function (oSettings) {		  	
								        	 new $.fn.dataTable.FixedHeader(admin_val, {
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