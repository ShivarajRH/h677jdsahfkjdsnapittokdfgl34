<style>
.prd_container
{
	margin:10px 0;
}
select
{
	width:177px;
}
.total_wrap
{
    display: inline-block;
    font-size: 14px;
    font-weight: bold;
    margin: 6px 0 10px;
    width: 100%;
}
.src_btn, .unsrc_btn
{
	margin:0px 10px;
}

.btn_align 
{
    margin: 2px 7px;
}
.btn_align button
{
	 margin: 0 10px;
}
.page_alert_wrap
{
	text-align:center;
	margin-top:20%;
	color:red;
}
.filters_block .filter select {
    font-size: 12px;
    margin-top: 5px;
    width: 180px;
}
.filters_block .filter label {
	display: inline-block;
    font-size: 12px;
    font-weight: bold;
    margin-right: 10px;
    text-align: right;
    width: 71px;
}
</style>

<div class="container">
	<h2>Product Deal Report</h2>
	<div class="filters_block">
		<div class="filter">
			<label>Menu :</label>
			<select name="sel_menu" id="sel_menu">
				<option value="0">All</option>
				<?php
					$pnh_menu=$this->db->query("select * from pnh_menu order by name asc")->result_array();
					$king_menu=$this->db->query("select * from king_menu order by name asc")->result_array();
					foreach($pnh_menu as $m)
					{
				?>		
					<option value="<?=$m['id']?>"><?=$m['name']?></option>
				<?php		
					}
					foreach($king_menu as $m)
					{
				?>		<option value="<?=$m['id']?>"><?=$m['name']?></option>
						
				<?php	}
				?>
		</select>
			</br>
			<label>Brand :</label>
		<select name="sel_brand" id="sel_brand">
			<option value="0">All</option>
			<?php
				$brands=$this->db->query("select * from king_brands order by name asc")->result_array();
				foreach($brands as $b)
				{
			?>		
					<option value="<?=$b['id']?>"><?=$b['name']?></option>
			<?php		
				}
			?>	
		</select>
			</br>
			<label>Category :</label>
		<select name="sel_cat" id="sel_cat">
			<option value="0">All</option>
			<?php
				$cats=$this->db->query("select * from king_categories order by name asc")->result_array();
				foreach($cats as $c)
				{
			?>		
					<option value="<?=$c['id']?>"><?=$c['name']?></option>
			<?php		
				}
			?>	
		</select>	
	 	</div>
	 	<div class="filter">
			<label>Partners :</label>
		<select name="sel_partner" id="sel_partner">
				<option value="0">Choose</option>
			<?php
				$partners=$this->db->query("select * from partner_info pt order by name;")->result_array();;
				foreach($partners as $p)
				{
			?>		
					<option value="<?=$p['id']?>"><?=$p['name']?></option>
			<?php		
				}
			?>	
		</select>	
			<br />
			<label>Menu Type :</label>
			<select name="sel_type" id="sel_type">
				<option value="-1">All</option>
				<option value="1">SK</option>
				<option value="0">SIT</option>
			</select>	
	</div>	
	
		<div class="filter">
			<label>Sourceable :</label>
		<select name="product_src">
			<option value="1">All</option>
			<option value="2">Sourceable</option>
			<option value="3">Not Sourceable</option>
		</select>
			<br />
			<label>Stock :</label>
		<select name="stock">
			<option value="1">All</option>
			<option value="2">Yes</option>
			<option value="3">No</option>
		</select>
			<br />
			<label>Publish :</label> 
		<select name="publish_stat">
			<option value="1">Choose</option>
			<option value="2">Yes</option>
			<option value="3">No</option>
		</select>
		</div>	
		<div class="filter">
			<button type="button" style="margin-top:-3px" class="btn_submit button button-rounded button-action button-tiny" onclick="product_deal_report()">Show</button>
	
		<div class="fl_right btn_align">
			<button type="button" style="margin-top:-5px" class="button button-rounded button-action button-tiny src_btn" class=""  onclick="product_deal_action(1)">Publish</button>
			<button type="button" style="margin-top:-5px" class="button button-rounded button-caution button-tiny unsrc_btn" class="" onclick="product_deal_action(0)">Un Publish</button>
		</div>
	</div>	
	<div class="total_wrap ">Total : Showing <?=$deals_cnt?>/<?=$total?> deals</div>
	</div>
	
	<div class="prd_container">
		<table class="datagrid sk_deal_blk_wrap" cellpadding="0" cellspacing="0" width="99%">
			<thead><tr>
				<th>Sl No.</th>
				<th>Deal Name</th>
				<th>Offer Price</th>
				<th>Member Price</th>
				<th>Stock</th>
				<th width="12%">Actions <a class="mark_all" style="font-size:10px;margin-left:6px;">Select All</a><br />
					
				</tr></thead><tbody>
	 				
	 			<?php
 				 $i=0;
 				foreach($deals as $d){
 					if($d['is_sourceable']==1)
						$back='background-color: rgba(170, 255, 170, 0.8)';
					else if($d['is_sourceable']==0)
						$back='background-color: #FFAAAA';
 				?>
 					<tr ><td><?=++$i?></td><td><a href="<?=site_url("admin/deal/{$d['dealid']}")?>" target="_blank"><?=$d['name']?></a></td>
 						<td><?=$d['price']?><td><?=$d['member_price']?></td>
 						<td>
 							<?php
 								if($d['is_combo'] == 0)
								{
								
									echo "n/a";
								}else
								{
							?>			
									<?=$this->db->query("select ifnull(sum(available_qty),0) as stock from t_stock_info where product_id=?",$d['product_id'])->row()->stock;?>
							<?php			
								}	
 							?>
 									
 									
 						</td>
 						<td><input type="checkbox" class="sel" value="<?=$d['dealid']?>"></td>
 					</tr>
 				<?php
 					
 					}
 				?>
			</tbody>
		</table>
	</div>
</div>	


<script>
$(function(){
	$('.src_btn').hide();
	$('.unsrc_btn').hide();
});

function product_deal_report()
{
	var menu=$('select[name="sel_menu"]').val();
	var brand=$('select[name="sel_brand"]').val();
	var cat=$('select[name="sel_cat"]').val();
	var partner=$('select[name="sel_partner"]').val();
	var src=$('select[name="product_src"]').val();
	var stock=$('select[name="stock"]').val();
	var publish=$('select[name="publish_stat"]').val();
	
	if(brand == 0 && cat == 0) 
	{
		alert("please choose brand or cat...");
		return false;
	}
	if(publish == 1) 
	{
		alert("please choose publish status...");
		return false;
	}
	/*
	if(src == 1)
	{
		alert("please choose sourceable status...");
		return false;
	}
	if(stock == 1)
	{
		alert("please choose stock status...");
		return false;
	}*/
	$('.prd_container').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	load_deals(0,'');
}

function load_deals(c,act)
{
	var menu=$('select[name="sel_menu"]').val();
	var brand=$('select[name="sel_brand"]').val();
	var cat=$('select[name="sel_cat"]').val();
	var src=$('select[name="product_src"]').val();
	var menu_type=$('select[name="sel_type"]').val();
	var stock=$('select[name="stock"]').val();
	var partner=$('select[name="sel_partner"]').val();
	var publish=$('select[name="publish_stat"]').val();
	
	$.getJSON(site_url+'/admin/jx_getdeals_by_prdstatus/'+'/'+menu+'/'+brand+'/'+cat+'/'+partner+'/'+src+'/'+stock+'/'+publish+'/'+menu_type+'/'+c,'',function(resp){
		if(resp.status == 'error')
		{
			$('.prd_container').html('<div class="page_alert_wrap">No Deals Found</div>');
			$('.total_wrap').hide();
		}else
		{
			var d_lst='';
			deals_lst=resp.deals;
			ttl_rows=resp.total_rows;
			disp_count=resp.disp_deals;
			var max_disp_count=parseInt(c)+parseInt(disp_count);
			ttl_pg_deals_count=resp.disp_deals;
			$('.total_wrap').show();
			
			if(c>0)
			{
				if(act='next')
				{
					ttl_pg_deals_count=parseInt(c)+parseInt(disp_count);
					disp_count=c+"-"+(parseInt(c)+parseInt(disp_count));
				}
			}
			d_lst+='<table class="datagrid sk_deal_blk_wrap" cellpadding="0" cellspacing="0" width="99%">'
			//d_lst+='<div class="log_pagination ">'+resp.deal_pagi_links+'</div>';
				d_lst+='<thead><tr>';
					d_lst+='<th>Sl No</th>';
					d_lst+='<th>Deal Name</th>';
					d_lst+='<th>Offer Price</th>';
					d_lst+='<th>Member Price</th>';
					d_lst+='<th>Stock</th>';
					d_lst+='<th width="12%">Actions <a class="mark_all" style="font-size:10px;margin-left:6px;">Select All</a><br />';
					d_lst+='</tr></thead><tbody>';
	 				
	 				$.each(deals_lst,function(i,p){
						
	 					if(p.is_sourceable == 1)
	 						background = 'background-color: rgba(170, 255, 170, 0.8)';
	 					else
	 						background = 'background-color: #FFAAAA';
	 						
	 					d_lst+='<tr >';
						if(c>0)
						{
							d_lst+='<td>'+(++c)+'</td>';
						}
						else
						{
	 					d_lst+='<td>'+(++i)+'</td>';
						}	
	 					d_lst+='<td><a  href="'+site_url+'/admin/deal/'+p.dealid+'" target="_blank">'+p.name+'</td>';
	 					d_lst+='<td>'+p.price+'</td>';
	 					d_lst+='<td>'+p.member_price+'</td>';
	 					d_lst+='<td>'+p.stock+'</td>';
	 					d_lst+='<td><input type="checkbox" class="sel" value="'+p.dealid+'"></td>';
						d_lst+='</tr>';
					});
				d_lst+='</tbody></table>';
			
			$('.prd_container').html(d_lst);
			$('.total_wrap').html('Displaying '+disp_count+'/'+ttl_rows+' deals<span class="btn_action" style="margin-left:10px;"><button type="button" class="button button-tiny button-flat-action btn_previous" style="display:none" pg="'+ttl_pg_deals_count+'" total="'+ttl_rows+'">Previous</button><button type="button" class="button button-tiny button-flat-action btn_next" pg="'+ttl_pg_deals_count+'" total="'+ttl_rows+'">Next</button></span>');
			$('.total_wrap').show();
			if(max_disp_count == ttl_rows)
			{
				$('.btn_next').hide();
			}else
			{
				$('.btn_next').show();
			}
		}
	});
	
}

$('.mark_all').live('click',function(){
	$(".sel").attr('checked',true);
	$('.mark_all').addClass('unmark_all').text('Unselect All').removeClass('mark_all');
});

$('.unmark_all').live('click',function(){
	$(".sel").attr('checked',false);
	$('.unmark_all').addClass('mark_all').text('Select All').removeClass('unmark_all');
});

function product_deal_action(v)
{
	var count=0;
	$(".sel:checked").each(function(){
		count++;
	});
	
	if(count>0)
		change_status(v);
	else
	{
		alert('Please Choose atleast one product');return false;
	}
			
}
//Function to publish and unpublish deals
function change_status(act)
{
	var ids=[];
	var c=0;
	var cond='';
	
	$(".sel:checked").each(function(){
		ids.push($(this).val());
	});
	ids=ids.join(",");
	
	$.post(site_url+'/admin/jx_change_deal_status',{dealids:ids,status:act},function(resp){
		if(resp.status=='success')
		{
			alert(resp.message);
			$('.btn_submit').trigger('click');
		}
		else
		{
			load_deals(0);
		}
	},'json');
		
}

$('.btn_next').live('click',function(){
	var c=$(this).attr('pg');
	var t=$(this).attr('total');
	$('span.btn_action').html('<img style="vertical-align: middle;" src="'+base_url+'/images/preloader.gif'+'">');
	load_deals(c,'next');
});

$('.btn_previous').live('click',function(){
	var c=$(this).attr('pg');
	var t=$(this).attr('total');
	var c=parseInt(c);
	load_deals(c,'prev');
});
$('select[name="publish_stat"]').live('change',function(){
	var p=$(this).val();
	
	if(p==2)
	{
		$('.unsrc_btn').show();
		$('.src_btn').hide();
	}else if(p==3)
	{
		$('.src_btn').show();
		$('.unsrc_btn').hide();
	}
	else
	{
		$('.src_btn').hide();
		$('.unsrc_btn').hide();
	}
});

//Menu change event -- to load products for a specified brand,load categories,brands in a select filter
$('#sel_menu').live('change',function(){
	var menu=$(this).val();
	$("#sel_brand").html("Loading ...");
	$("#sel_cat").html("Loading ...");
	
	$.getJSON(site_url+'admin/jx_load_allbrandsbymenucat/'+menu+'/'+0,'',function(resp){
		var brand_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			brand_html+='<option value="0">All</option>';
			$.each(resp.cat_list,function(i,b){
				brand_html+='<option value="'+b.brandid+'">'+b.brandname+'</option>';
			});
		}
		$("#sel_brand").html(brand_html).trigger("liszt:updated");
	});
	
	$.getJSON(site_url+'admin/jx_load_allcatsbymenu/'+menu+'/'+0,'',function(resp){
		var cat_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			cat_html+='<option value="0">All</option>';
			$.each(resp.cat_list,function(i,b){
				cat_html+='<option value="'+b.catid+'">'+b.name+'</option>';
			});
		}
		$("#sel_cat").html(cat_html).trigger("liszt:updated");
	});
});

//Menu change event -- to load products for a specified brand,load categories,brands in a select filter
$('#sel_brand').live('change',function(){
	var brand=$(this).val();
	$("#sel_cat").html("Loading ...");
	
	$.getJSON(site_url+'admin/jx_load_allcatsbybrand/'+brand,'',function(resp){
		var cat_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			cat_html+='<option value="0">All</option>';
			$.each(resp.cat_list,function(i,b){
				cat_html+='<option value="'+b.catid+'">'+b.cat_name+'</option>';
			});
		}
		$("#sel_cat").html(cat_html).trigger("liszt:updated");
	});
});
</script>	