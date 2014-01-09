<div class="BID_get_START_div">
	<div style="float:left;width:18%">
		<ul>
			<li class="current" alt="1"><a href="javascript:void(0);">1. Login</a></li>
			<li alt="2"><a href="javascript:void(0);">2. Select product</a></li>
			<li alt="3"><a href="javascript:void(0);">3. Browse through Menus</a></li>
			<li alt="4"><a href="javascript:void(0);">4. Catalogue Page</a></li>
			<li alt="5"><a href="javascript:void(0);">5. Tell Me Your Price</a></li>
			<li alt="6"><a href="javascript:void(0);">6. Select Item Quantity</a></li>
			
			<li alt="7"><a href="javascript:void(0);">7. Confirm Order</a></li>
			<li alt="8"><a href="javascript:void(0);">8. View Order Summary</a></li>
			<li alt="9"><a href="javascript:void(0);">9. Enter Contact Info</a></li>
			<li alt="10"><a href="javascript:void(0);">10. Place Order</a></li>
			<li alt="11"><a href="javascript:void(0);">11. Cofirm Payment</a></li>
			<li alt="12"><a href="javascript:void(0);">12. Order Placed</a></li>
			<li alt="13"><a href="javascript:void(0);">13. View Your Orders</a></li>
			
			<li alt="14"><a href="javascript:void(0);">14. View Order Details</a></li>
			<li alt="15"><a href="javascript:void(0);">15. View Sales Report</a></li>
			<li alt="16"><a href="javascript:void(0);">16. Generates Sales Report</a></li>
		</ul>
	</div>
	<div style="float:left;width:81%" class='BID_content_DIV'>
		<div class="BID_content_DIV_header"></div>
		<div id="how_it_works" style="height:440px;">
			<div id="1" class='content_Div'><img src="<?= base_url();?>images/viascreenshot/1-Agent Login.png" ></div>
			<div id="2" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/2-Search Product.jpg" ></div>
			<div id="3" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/3-Browse through Menus.jpg" ></div>
			<div id="4" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/4-Catalogue Page.jpg" ></div>
			<div id="5" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/5-Tell me your Price.jpg" ></div>
			<div id="6" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/6-Select Item Quantity.jpg" ></div>
			
			<div id="7" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/7-Confirm Order.png" ></div>
			<div id="8" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/8-View Order Summary.jpg" ></div>
			<div id="9" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/9- Enter Contact Info.jpg" ></div>
			<div id="10" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/10-Place Order.jpg" ></div>
			<div id="11" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/11-Confirm Payment.jpg" ></div>
			<div id="12" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/12-Order Placed.jpg" ></div>
			<div id="13" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/13-View Your Orders.jpg" ></div>
			
			<div id="14" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/14-View Order Details.jpg" ></div>
			<div id="15" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/15-View Sales report.jpg" ></div>
			<div id="16" class='content_Div hide'><img src="<?= base_url();?>images/viascreenshot/16-Generates Sales Report.jpg" ></div>
		</div>
		<div class="BID_content_DIV_header">
			<div class="BID_autoplay_DIV"><a href="javascript:void(0);"></a></div>
			<div class="BID_nextprev_DIV" style="float: right;"><a id="prev_a">Previous</a><a id="next_a">Next</a></div>
		</div>
	</div>
</div>

<style type="text/css">
	.BID_get_START_div{margin-left:-15px;background:#DDECEF;width:960px;padding:10px;float:left;font-family:verdana;padding-right:5px;}
	.BID_get_START_div ul{list-style:none outside none;margin:0;padding:0px 0 0;}
	.BID_get_START_div li{height:35px;line-height:35px;margin:0;padding:0;border-bottom:1px solid #ccc;cursor:pointer;font-weight:bold;-moz-border-radius:5px 0px 0px 5px;}
	.current{background:url("<?= base_url();?>images/viascreenshot/bg.gif") repeat-x transparent;}
	.BID_get_START_div li a{color:#000;text-decoration:none;padding-left:1px;outline:none;font-size:11px;}
	.hide{display:none;}
	.BID_get_START_div li.current a{color:#fff;}
	
	.BID_content_DIV{background:#5B93B4;padding:4px;}
	.BID_content_DIV_header{background:url('<?= base_url();?>images/viascreenshot/bg.gif') repeat-x;height:40px;padding-right:30px;}
	.content_Div{height:440px;background:#fff;}
	.content_Div img{width:100%;max-height:440px;}
	#prev_a, #next_a {background:url("<?= base_url();?>images/viascreenshot/slide_btn.gif") no-repeat scroll 0 0 transparent;
		color:#FFFFFF;cursor:pointer;display:inline;float:left;font-size:13px;font-weight:bold;height:30px;line-height:30px;margin:7px 0 0 60px;padding:0 0 0 25px;text-align:center;}
	#next_a {background-position:0 -30px;}
	#prev_a {background-position:0 0;}
</style>
<script type="text/javascript">
	var s_n = 1;
	var s_p = 1;
	var s_c =0;
	$().ready(function (){
		$('.BID_get_START_div li').click(function (){
			var a=$(this).attr('alt');
			$('.content_Div').hide();
			$('#'+a).show();
			$('li').removeClass('current');
			$(this).addClass('current');
		});
		
		$('#next_a').click(function (){
			if(s_n < 13){
				if(s_c == 1){
					s_n = s_p + 1;
				}else{
					s_n = s_n + 1;
				}
				$('.content_Div').fadeOut('slow');
				window.setTimeout("$('#'+s_n).fadeIn('slow')",500);
				s_c =0;
			}
		});
		$('#prev_a').click(function (){
			if(s_n > 2 || s_n < 13){
				s_c = 1;
				s_p = s_n - 1;
				if(s_n >1)
					s_n = s_n-1;
				if(s_p > 0){
					$('.content_Div').fadeOut('slow');
					window.setTimeout("$('#'+s_p).fadeIn('slow')",500);
				}
			}
		});
	});
</script>