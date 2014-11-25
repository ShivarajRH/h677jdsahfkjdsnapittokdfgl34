/* 
 * header scripts start
 */

/**         * Admin scrips STARTS         */
$(function(){
		$('title').html($('h2:first').text());
	});
	
	function handle_quote_request_call(fr_id)
	{
		location.href = site_url+'/admin/pnh_quotes/'+fr_id;
	}
	
	function animate_strip(tick_width,tspan)
	{
		 
		$('#strip_itemlist_wrap .strip_itemlist').animate({left:-(tick_width+100)},tspan,'linear',function(){
				get_panel_alerts();
			}).queue(function(){
				var _this = $(this);
				    _this.dequeue();
			});
		$('#strip_itemlist_wrap .strip_itemlist').mouseenter(function(){
			$(this).stop();
		});
		
		$('#strip_itemlist_wrap .strip_itemlist').mouseleave(function(){
			animate_strip(tick_width,tspan);
		});
	}
	
	/*
	 * Panel Alerts
	 * 
	 */
	function get_panel_alerts()
	{
		
		 $.post(site_url+'/admin/get_panel_alerts','',function(resp){
		 	if(resp.status == 'success')
		 	{
		 		$('#strip_itemlist_wrap').show();
		 		var tick_width = resp.quote_list.length*500; 
		 		
		 		var ticker_html = '<div class="strip_itemlist" style="width:'+tick_width+'px;font-size:12px;position:relative;left:0px;">';
		 			$.each(resp.quote_list,function(a,b){
		 				ticker_html += '<div class="strip_item"  style="width:400px;display:inline;margin-right:10px;">  '+(a+1)+') '+b+' </div> ';
		 			});
		 			ticker_html += '</div>';
		 		$('#strip_itemlist_wrap').html(ticker_html);
		 		
		 		var tick_width = 0;
		 			$('#strip_itemlist_wrap .strip_item').each(function(){
		 				tick_width += ($(this).width()+10)*1;
		 			});
		 			var tspan = tick_width*20;
		 			
		 			if(tick_width < $(document).width())
		 				tick_width = $(document).width();
		 				
		 			$('#strip_itemlist_wrap .strip_itemlist').width(tick_width);
		 			
		 		animate_strip(tick_width,tspan);
		 	}else
		 	{
		 		$('#strip_itemlist_wrap').hide();
		 		setTimeout(function(){
		 			get_panel_alerts();
		 		},120000);
		 	}
		 },'json');
	}
	
	
	var cur_server_status = 1;
	
	function updateConnectionStatus(statMsg,stat)
	{
		cur_server_status = stat;
		$('#connection_state').removeClass('network_offline').hide();
		$('#connection_state').html("Network status : "+statMsg+' <a href="javascript:void(0)" onclick="upd_server_status()">Check now</a>')
		if(!stat)
		{
			$('#connection_state').addClass('network_offline').show();
			$('#connection_state').show();
			
			$(window).on('beforeunload',function() {
			    return "Server Offline";
			});	
			
		}else
		{
			$('#connection_state').hide();
			$(window).unbind('beforeunload');
		}
		
	}
	function upd_server_status()
	{
		$('#connection_state').html("Checking server status...");
		
			
		$.ajax({
	          url: site_url+'/admin/ping/<?php echo $page?>',
	          success: function(result){
	            if(result == 'pong')
	            {
	            	updateConnectionStatus("",1);	
	            }else if(result == 'nodb')
	            {
	            	updateConnectionStatus("Database Connection Failed",0);
	            }else if(result == 'loggedout')
	            {
	            	updateConnectionStatus("Login Expired - <a href='"+site_url+"/admin' target='_blank' >Click here to relogin</a>",0);
	            }
	            setTimeout(function(){
					upd_server_status();
				},2000);
	          },     
	          error: function(result){
	             updateConnectionStatus("Server Offline",0);
	             setTimeout(function(){
					upd_server_status();
				},5000);
	          }
	       });
	}
	
	$(function(){
		//upd_server_status();	
	});
        /**         * Admin scrips end         */
        
        
$(document).ready(function() {
		//Stream Notifications
        $.post(site_url+'admin/jx_get_stream_notifications/'+userid,{},function(rdata){
            if(rdata>0 && rdata!='')
                    $(".notify_block").css({"background-color": "brown", "padding":"2px 10px"}).html(rdata);
            return false;
        });
        $(".notify_block").bind("click",function() { var update=1; // print(userid);
            $.post(site_url+'admin/jx_get_stream_notifications/'+userid+'/'+update,{},function(rdata){
                if(rdata>0 && rdata!='')
                    $(".notify_block").css({"background-color": "brown"}).html(rdata); 
                return false;
            });
            return true;
        });
        return false;
    });
$(function(){
        $("#phone_booth form").submit(function(){
                if(!is_required($(".pb_customer",$(this)).val()))
                {
                        alert("Please enter Customer number");
                        return false;
                }
                if(!is_required($(".pb_agent",$(this)).val()))
                {
                        alert("Please enter Agent number");
                        return false;
                }
                $(".loading",$(this)).show();
                $.post(site_url+"/admin/makeacall",$(this).serialize(),function(data){
                        $("#phone_booth .loading").hide();
                        if(data=="0")
                                show_popup("Error in initiating call");
                        else
                        {
                                $("#phone_booth").hide();
                                show_popup("Call Initiated");
                        }
                });
                return false;
        });
});
$(function(){
	//Search Input events 
	$("#searchbox").focus(function(){
		sr=$(this);
		if(sr.val()=="Search...")
		{
			sr.css("color","#000");
			sr.val("");
		}
	});
	$("#searchbox").blur(function(){
		sr=$(this);
		if(sr.val().length==0)
		{
			sr.css("color","#aaa");
			sr.val("Search...");
		}
	});
	$("#searchbox").keypress(function(e){
		if(e.which==13)
		{
			var v=$(this).val();
			$("#searchkeyword").val(v);
			prod_cnt='';
			partner_srch=0;
			if($('input[name="srch_opt"]:checked').val()==1)
			{
				$.post(site_url+'/admin/jx_prd_srch_bybarcode',{chr:v},function(resp){
				var prod_det=resp.p_det;	
				if(resp.status == 'error')//If response status is error[barcode not found]
				{
					$('#prd_det_dlg').html('<div class="page_alert_wrap">No Details Found</div>');
						$('#prd_det_dlg').dialog('open');
				}
				else
				{
						//If it is not a imei search 
						if(resp.partner_srch == 1)
							partner_srch=1;
							
						product_det(prod_det,'barc',partner_srch);
				}
					if(resp.partner_srch == 1)
						$('.ui-dialog-title').html(v);
					else
					$('.ui-dialog-title').html('Barcode : '+v);
				},'json');
		 	}
		 	else if($('input[name="srch_opt"]:checked').val()==2)
			{
				$.post(site_url+'/admin/jx_prd_srch_byimei',{chr:v},function(resp){
				var prod_det=resp.p_det;	
					if(resp.status == 'error')//If response status is error[imei not found]
				{
					$('#prd_det_dlg').html('<div class="page_alert_wrap">No Details Found</div>');
						$('#prd_det_dlg').dialog('open');
				}
				else
				{
						//If it is not a imei search 
						if(resp.partner_srch == 1)
							partner_srch=1;
						
						product_det(prod_det,'imei',partner_srch);
				}
					if(resp.partner_srch == 1)
						$('.ui-dialog-title').html(v);
					else
				$('.ui-dialog-title').html('IMEI : '+v);
				},'json');
		 	}
		 	else
		 	{
			$("#searchform").submit();
		}
		}
	});
	
	//Search Submit events
	$("#searchtrigh").click(function(){
		if($("#searchbox").val()=="Search...")
		{
			alert("inpput!!!");return;
		}
		var v=$("#searchbox").val();
		$("#searchkeyword").val(v);
		partner_srch=0;
		prod_cnt='';
		//Condition for search type ---1.Barcode,2.IMEI
		if($('input[name="srch_opt"]:checked').val()==1)
		{
			$.post(site_url+'/admin/jx_prd_srch_bybarcode',{chr:v},function(resp){
			var prod_det=resp.p_det;	
			if(resp.status == 'error')//If response status is error[barcode not found]
			{
				$('#prd_det_dlg').html('<div class="page_alert_wrap">No Details Found</div>');
					$('#prd_det_dlg').dialog('open');
			}
			else
			{
					//If it is not a imei search 
					if(resp.partner_srch == 1)
						partner_srch=1;
						
					product_det(prod_det,'barc',partner_srch);
			}
				if(resp.partner_srch == 1)
					$('.ui-dialog-title').html(v);
				else
				$('.ui-dialog-title').html('Barcode : '+v);
			},'json');
	 	}
	 	//Condition for search type ---1.Barcode,2.IMEI
	 	else if($('input[name="srch_opt"]:checked').val()==2)
		{
			$.post(site_url+'/admin/jx_prd_srch_byimei',{chr:v},function(resp){
			var prod_det=resp.p_det;	
				if(resp.status == 'error')//If response status is error[imei not found]
			{
				$('#prd_det_dlg').html('<div class="page_alert_wrap">No Details Found</div>');
					$('#prd_det_dlg').dialog('open');
			}
			else
			{
					//If it is not a imei search 
					if(resp.partner_srch == 1)
						partner_srch=1;
						
					product_det(prod_det,'imei',partner_srch);
				}
				if(resp.partner_srch == 1)
					$('.ui-dialog-title').html(v);
				else
				$('.ui-dialog-title').html('IMEI : '+v);
			},'json');
	 	}
		else
	 	{
	 		$("#searchform").submit();
	 	}
	});

	var data_request = null;

	$("#searchform").submit(function(){
		var srch_val = $.trim($("#searchbox").val());
			$("#searchbox").val(srch_val);
			$("#searchkeyword").val(srch_val);
	});
	
	//Function to append data for barcode,imei search
	function product_det(prod_det,srch_type,is_partner_srch)
	{
				$.each(prod_det,function(i,p){
					//pname+=p.product_name;
					prod_cnt+='<div class="cont">';
					prod_cnt+='		<h4 class="title"><a target="_blank" href="'+site_url+'/admin/product/'+p.product_id+'">'+p.product_name+'</a></h4>';
					prod_cnt+='		<div class="img_blk">';
					prod_cnt+='			<img src="'+p.image_url+'">';
			prod_cnt+='			<div class="serial_numb_blk">';
			prod_cnt+='				<div class="sales">';
			prod_cnt+='					<div><span>30 day Sales : </span><b>'+p.sales+' Qty</b></div>';
			prod_cnt+='				</div>';
			prod_cnt+='			</div>';
					prod_cnt+='		</div>';
					prod_cnt+='		<div class="desc_blk">';
			prod_cnt+='			<div><span>Stock :</span><b>'+p.stock+'</b></div>';
			prod_cnt+='			<div><span>MRP :</span><b>'+p.mrp+'</b></div>';
			//prod_cnt+='			<div><span>Mem. Price :</span><b>'+p.member_price+'</b></div>';
			prod_cnt+='			<div><span>Brand :</span><b><a target="_blank" href="'+site_url+'/admin/viewbrand/'+p.brandid+'">'+p.brand_name+'</a></b></div>';
			prod_cnt+='			<div><span>Category :</span><b><a target="_blank" href="'+site_url+'/admin/viewcat/'+p.catid+'">'+p.cat_name+'</a></b></div>';
			
			//Stock Details 
			prod_cnt+='				<h4 style="margin-top:4%; ">Stock Details :</h4>';
			prod_cnt+='				<table class="datagrid fl_left" width="100%">';
			prod_cnt+='					<thead>';
			prod_cnt+='						<th width="100">Barcode</th>';
			prod_cnt+='						<th width="100">MRP</th>';
			prod_cnt+='						<th width="100">Rackbin</th>';
			prod_cnt+='						<th width="100">Stock</th>';
			prod_cnt+='					</thead>';
			prod_cnt+='					<tbody>';
			$.each(p.stk_det,function(i,d){
				if(d.s > 0)
				{
					prod_cnt+='						<tr>';
					prod_cnt+='							<td>';
					if(d.pbarcode)
						prod_cnt+=''+d.pbarcode;
					else
						prod_cnt+='n/a';	
					prod_cnt+='							</td>';
					prod_cnt+='							<td>'+format_indianprice(d.mrp)+'</td>';
					prod_cnt+='							<td>'+d.rbname+'</td>';
					prod_cnt+='							<td>'+d.s+'</td>';
					prod_cnt+='						</tr>';
				}
				
			});
			prod_cnt+='					</tbody>';
			prod_cnt+='				</table>';
			
			//Linked Deals 
			prod_cnt+='				<h4 style="margin-top:4%; display: inline-block;">Linked Deals :</h4>';
			prod_cnt+='					<table class="datagrid"  width="100%">';
			prod_cnt+='						<thead><th>Deal Name</th><th width="100">Deal Type</th><th width="200">Partner Ids</th></thead><tbody>';
			
			prod_cnt+='						<tr><td><a target="_blank" href="'+site_url+'/admin/deal/'+p.dealid+'">'+p.name+'</a></td>';
			if(p.is_pnh ==1)
					prod_cnt+='						<td>PNH</td>';
				else
					prod_cnt+='						<td>SNP</td>';	
				
			prod_cnt+='							<td>';
			
			if(p.deal_det.length)
			{
				$.each(p.deal_det,function(i,d){
					prod_cnt+='							'+d.name+' : <b>'+d.partner_ref_no+'</b><br />';
					});
			}else
			{
				prod_cnt+=' N/A';
			}
			
			prod_cnt+='						</td>';
			prod_cnt+='					</tr>';
			
			prod_cnt+='				</tbody></table>';
			
			if(srch_type == 'imei' && is_partner_srch==0)
			{
				//Vendor,grn details
				prod_cnt+='				<h4 style="margin-top:4%; display: inline-block;">Vendor Details :</h4>';
				prod_cnt+='				<table class="datagrid"  width="100%">';
				prod_cnt+='						<thead><th>Vendor Name</th><th width="100">GRN ID</th><th width="200">Intake On</th></thead><tbody>';
				prod_cnt+='						<tr>';
				prod_cnt+='							<td><a target="_blank" href="'+site_url+'/admin/vendor/'+p.vendor_det.vendor_id+'">'+p.vendor_det.vendor_name+'</a></td>';
				prod_cnt+='							<td><a target="_blank" href="'+site_url+'/admin/viewgrn/'+p.vendor_det.grn_id+'">'+p.vendor_det.grn_id+'</a></td> <td>'+p.vendor_det.created_on+'</td>';
				prod_cnt+='						</tr>';
				prod_cnt+='				</tbody></table>';
				
				//Invoice Details
				prod_cnt+='				<h4 style="margin-top:4%; display: inline-block;">Invoice Details :</h4>';
				prod_cnt+='				<table class="datagrid"  width="100%">';
				prod_cnt+='						<thead><th width="100">Franchise</th><th width="100">Invoice No</th><th width="100">Shipped On</th><th width="100">Delivered On</th></thead><tbody>';
				prod_cnt+='						<tr>';
				prod_cnt+='							<td><a target="_blank" href="'+site_url+'/admin/pnh_franchise/'+p.invoice_det.franchise_id+'">'+p.invoice_det.franchise_name+'</a></td>';
				prod_cnt+='							<td><a target="_blank" href="'+site_url+'/admin/invoice/'+p.invoice_det.invoice_no+'">'+p.invoice_det.invoice_no+'</a></td><td>'+p.invoice_det.shipped_on+'</td><td>'+p.invoice_det.delivered_on+'</td>';
				prod_cnt+='						</tr>';
			
				prod_cnt+='				</tbody></table>';	
			}
			
			
			prod_cnt+='		</div>';
			prod_cnt+='			<div class="more"><a target="_blank" href="'+site_url+'/admin/product/'+p.product_id+'">More Info >></a></div>';
					prod_cnt+='</div>';
				});	
				$('#prd_det_dlg').html(prod_cnt);
			$('#prd_det_dlg').dialog('open');
	 	}
	
	$("#suggestions").css({ 'box-shadow' : '#888 5px 10px 10px', // Added when CSS3 is standard
		'-webkit-box-shadow' : '#888 5px 10px 10px', // Safari
		'-moz-box-shadow' : '#888 5px 10px 10px'});
	
	// Fade out the suggestions box when not active
	 $("#searchbox").blur(function(){
	 	$('#suggestions').fadeOut();
	 }).keyup(function(){
	 	inputString = $(this).val();
	 	
	 	if(inputString.length == 0) {
			$('#suggestions').fadeOut(); // Hide the suggestions box
		} else {
			$('#suggestions').show(); // Show the suggestions box
			$('#suggestions').html("<p id='searchresults'><span class='category'>Loading...</span></p>");
			
			if(data_request)
				data_request.abort();
			if($('input[name="srch_opt"]:checked').val()==0)
			{
			data_request = $.post(site_url+'/admin/jx_searchbykwd', {kwd: ""+inputString+""}, function(data) { // Do an AJAX call
				$('#suggestions').html(data); // Fill the suggestions box
			});
			}
		}
	 });
	 
	 $('#searchresults .viewall').live('click',function(){
	 	$("#searchform").submit();
	 });
});
/**  Header scripts ends  **/
