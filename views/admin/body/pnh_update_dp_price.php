<div class="page_wrap container" style="width: 98%;">
	
	<h2 class="page_title">Update DP price</h2>
	
	<div class="page_topbar" >
		<div class="page_topbar_left fl_left" >
			<span class="total_overview">Total Deals Listed : <b></b> </span>
		</div>
		<div class="page_action_buttonss fl_right" align="right">
			Brand : 
			<select name="sel_brandid" style="width: 200px;">
				<option value="0">All</option>
				<?php
					foreach($brand_list as $brand)
					{
				?>
						<option value="<?php echo $brand['id'];?>"><?php echo $brand['name'];?></option>
				<?php 		
					}
				?>
			</select>
			
			&nbsp;
			&nbsp;
			Category 
			<select name="sel_catid" style="width: 200px;"><option value="">All</option></select>
			
			<input type="button" value="Submit" onclick="load_dealsbybrandcat('')">
			
		</div>
	</div>
	
	<div style="clear:both">&nbsp;</div>
	
	<div id="deal_list" class="page_content">
		<table  class="datagrid" width="100%">
			<thead>
				<th width="30">Slno</th>
				<th width="50"><b>ID</b></th>
				<th width="350"><b>Name</b></th>
				<th width="50"><b>MRP</b></th>
				<th width="50"><b>Offer/DP Price</b></th>
				<th width="80"><b>New DP Price (Rs)</b></th>
				<th width="130"><b>Published</b></th>
				<th width="180"><b>Product</b></th>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<div class="pagination" align="right"></div>
	</div>
</div>
<script type="text/javascript">
	$('.chg_dp_price').live('focus',function(){
		$('.highlight_row').removeClass('highlight_row');
		$(this).parents('tr:first').addClass('highlight_row');
	});
	$('select[name="sel_brandid"]').change(function(){
		var bid = $(this).val();
		$('select[name="sel_catid"]').html('<option>loading...</option>');
			if(bid)
			{
				$.getJSON(site_url+'/admin/jx_getcatbybrand/'+$(this).val(),'',function(resp){
					var catlist_html = '<option value="0">All</option>';
						if(resp.cat_list.length)
						{
							$.each(resp.cat_list,function(a,b){
								catlist_html += '<option value="'+b.id+'">'+b.name+'</option>';
							});
						}
					$('select[name="sel_catid"]').html(catlist_html);	
				});
			}
	});
	
	function load_dealsbybrandcat(pagi_url)
	{
		var bid = $('select[name="sel_brandid"]').val()*1;
		var cid = $('select[name="sel_catid"]').val()*1;
		
			bid = isNaN(bid)?0:bid;
			cid = isNaN(cid)?0:cid;
		
			$('#deal_list tbody').html('<div align="center" style="padding:5px;"><img src="'+base_url+'/images/loading_bar.gif'+'"></div>');
			$('#deal_list .pagination').html('');
			
			if(pagi_url == '')
				url = site_url+'/admin/jx_getdealsbybrandcat/'+bid+'/'+cid+'/0';
			else
				url = pagi_url;	
			
			$.getJSON(url,'',function(resp){
					 var deallist_html = '';
						if(resp.deal_list.length)
						{
							$.each(resp.deal_list,function(a,b){
								deallist_html += '<tr class="'+((b.publish*1 == 1)?'published':'unpublished')+'" >';
								deallist_html += '	<td>'+(resp.pg*1+a*1+1)+'</td>';
								deallist_html += '	<td><b>'+b.pnh_id+'</b></td>';
								deallist_html += '	<td><a target="_blank" href="'+site_url+'/admin/pnh_deal/'+b.id+'">'+b.name+'</a></td>';
								deallist_html += '	<td class="orgprice">'+b.orgprice+'</td>';
								deallist_html += '	<td class="price">'+b.price+'</td>';
								deallist_html += '	<td><input type="text" size="6" tabindex="'+a+'" class="chg_dp_price" itmid="'+b.id+'" orgprice="'+b.orgprice+'" value="'+b.price+'"></td>';
								deallist_html += '	<td><span class="deal_status">'+(b.publish==1?'published':'not published')+'</span> <a class="upd_deal_pub"  item_id="'+b.id+'" publish="'+b.publish+'" href="javascript:void(0)" style="font-size:10px;color:blue">Change</a> </td>';
								deallist_html += '	<td>';
								deallist_html += '		<a target="_blank" href="'+site_url+'/admin/pnh_products_by_deal/'+b.id+'" style="font-size:10px;color:blue">View Product</a>';
								deallist_html += '	- <span class="p_src_status" st>'+((b.is_sourceable*1)?"Sourceable":"Not Sourceable")+'</span> <a target="_blank" class="upd_prodstatus" href="'+site_url+'/admin/pnh_upd_prod_status/'+b.product_id+'" style="font-size:10px;color:blue">Change</a> ';	
								deallist_html += '	</td>';
								deallist_html += '</tr>';
							});
						}
						
						$('#deal_list tbody').html(deallist_html);
						$('#deal_list .pagination').html(resp.pagination);
						
						$('.page_topbar .total_overview b').html(resp.deal_ttl);
				 
			});	
	}
	
	$('#deal_list .pagination a').live('click',function(e){
		e.preventDefault();
		load_dealsbybrandcat($(this).attr('href'));
	});
	
	$('.upd_prodstatus').live('click',function(e){
		e.preventDefault();
		if(confirm("Are you sure want to change the product status?"))
		{
			var trEle = $(this).parents('tr:first');
				$('.p_src_status',trEle).text("Updating...");
			var upd_url = $(this).attr('href');
				$.getJSON(upd_url,'',function(resp){
					$('.p_src_status',trEle).text(resp.pstatus);	
				});
		}
	});
	
	$('select[name="sel_catid"]').change(function(){
		 
	});
	
	$('.chg_dp_price').live('change',function(){
		var ele = $(this);
			ele.css('border','1px solid #eee');
		var is_perc = 0;	
			if(ele.hasClass('chg_dp_price_perc'))
				is_perc = 1;
				
		var oprc = $(this).attr('orgprice')*1;
		var price = $.trim($(this).val())*1;
			
		 	if(isNaN(price))
		 	{
		 		alert("Invalid Price entered,please check");
		 		return ;
		 	}
		 	
		 	if(price == 0)
		 	{
		 		alert("Price cannot be 0,please check");
		 		return ;
		 	}
		 	
			
			if(price > oprc)
			{
				alert("Price "+price+" is greater than MRP price "+oprc+" ");
			}else 
			{
				$.post(site_url+'/admin/jx_upd_dealdpprice','price='+price+'&id='+$(this).attr('itmid')+'&is_perc=0',function(resp){
					if(resp.status == 'success')
					{
						ele.css('border','2px solid green');
					}else
					{
						ele.css('border','3px solid #cd0000');
					}
					
				},'json');
			}
			
		
		
	});
	
	load_dealsbybrandcat('');

  $(".view_product").live('click',function(e){
  		e.preventDefault();
  		
  		var item_id=$(this).attr('item_id');
		var product_id=0;
		
			$.post(site_url+'/admin/jx_products_by_deal/'+item_id+'/1',{},function(res){
				$('body').append('<a target="_blank" class="red_newtab" href="'+site_url+'/admin/product/'+res.prods[0].product_id+'"></a>');
				$('.red_newtab').trigger('click');
				$('.red_newtab').remove();
			},'json');
		
	});

	$(".upd_deal_pub").live('click',function(e){
			e.preventDefault();
			var ele =$(this);
			var item_id=$(this).attr('item_id');
			var is_published=$(this).attr('publish');
			var status='';
			if(confirm("Are you sure want to change the status?"))
			{
				$.post(site_url+'/admin/pnh_pub_deal/'+item_id+'/'+is_published+'/0',{},function(res){
						status = '';
						if(res.status=='success')
						{
							if(res.is_published==0)
								status='not published';
							else
								status='published';
								
							ele.attr('publish',res.is_published?1:0);
							$(".deal_status",ele.parent()).html(status);
							
							ele.parents('tr:first').removeClass('published');
							ele.parents('tr:first').removeClass('unpublished');
							if(res.is_published==0)
							{
								ele.parents('tr:first').addClass('unpublished');
							}else
							{
								ele.parents('tr:first').addClass('published');
							}
							
						}else
						{
							alert(resp.error);
						}
						
					
					},'json');
			}
			
		});	
	
</script>
<style>
	.leftcont{display: none}
	.highlight_row td{background: #F7E8E5 !important}
	.pagination a{padding:5px 10px;background: #dfdfdf;color: #000;font-weight: bold;font-size: 13px;}
	
	.published td{background: rgba(0, 128, 0, 0.2) !important}
	.unpublished td{background: rgba(205, 0, 0, 0.2) !important}
	
</style>