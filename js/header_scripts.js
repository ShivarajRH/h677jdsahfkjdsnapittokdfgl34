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
		 		},10000);
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
                $.post(site_url+"admin/makeacall",$(this).serialize(),function(data){
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
			$("#searchkeyword").val($(this).val());
			$("#searchform").submit();
		}
	});
	$("#searchtrigh").click(function(){
		if($("#searchbox").val()=="Search...")
		{
			alert("inpput!!!");return;
		}
		$("#searchkeyword").val($("#searchbox").val());
		$("#searchform").submit();
	});

	var data_request = null;

	$("#searchform").submit(function(){
		var srch_val = $.trim($("#searchbox").val());
			$("#searchbox").val(srch_val);
			$("#searchkeyword").val(srch_val);
	});
	
	
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
			
			data_request = $.post(site_url+'/admin/jx_searchbykwd', {kwd: ""+inputString+""}, function(data) { // Do an AJAX call
				$('#suggestions').html(data); // Fill the suggestions box
			});
		}
	 });
	 
	 $('#searchresults .viewall').live('click',function(){
	 	$("#searchform").submit();
	 });
	
});
/**  Header scripts ends  **/
