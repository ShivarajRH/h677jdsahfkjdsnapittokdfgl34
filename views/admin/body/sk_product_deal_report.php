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
    margin: 23px 0 10px;
    width: 25%;
}
.src_btn, .unsrc_btn
{
	margin:0px 10px;
}

.btn_align 
{
    margin: 25px 7px;
}
.btn_align button
{
	 margin: 0 10px;
}
.page_alert_wrap
{
	text-align:center;
	margin-top:10%;
	color:red;
}
</style>
<div class="container">
	<h2>Product Deal Report</h2>
	<b>Menu :</b> <select name="sel_menu" id="sel_menu">
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
	
	<b>Brand :</b> <select name="sel_brand" id="sel_brand">
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
	
	<b>Sourceable :</b> <select name="product_src"><option value="1">All</option><option value="2">Sourceable</option><option value="3">Not Sourceable</option></select>
	<b>Stock :</b> <select name="stock"><option value="1">All</option><option value="2">Yes</option><option value="3">No</option></select>
	<b>Publish :</b> <select name="publish_stat"><option value="1">All</option><option value="2">Yes</option><option value="3">No</option></select>
	<button type="button" style="margin-top:-5px" class="btn_submit button button-rounded button-action button-tiny" onclick="product_deal_report()">Show</button>
	<div class="fl_right btn_align">
		<button type="button" style="margin-top:-5px" class="button button-rounded button-action button-tiny src_btn" class=""  onclick="product_deal_action(1)">Publish</button>
		<button type="button" style="margin-top:-5px" class="button button-rounded button-caution button-tiny unsrc_btn" class="" onclick="product_deal_action(0)">Un Publish</button>
	</div>
	<div class="total_wrap ">Total : Showing <?=$deals_cnt?>/<?=$total?> deals</div>
	<div class="prd_container">
			<table class="datagrid sk_deal_blk_wrap" cellpadding="0" cellspacing="0" width="99%">
				<thead><tr>
					<th>Sl No.</th>
					<th>Deal Name</th>
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
	 						
	 						<td><input type="checkbox" class="sel" value="<?=$d['dealid']?>"></td>
	 					</tr>
	 				<?php
	 					
	 					}
	 				?>
			</tbody></table>
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
	var src=$('select[name="product_src"]').val();
	var stock=$('select[name="stock"]').val();
	var publish=$('select[name="publish_stat"]').val();
	
	if(brand == 0)
	{
		alert("please choose brand...");
		return false;
	}
	if(src == 1)
	{
		alert("please choose sourceable status...");
		return false;
	}
	if(stock == 1)
	{
		alert("please choose stock status...");
		return false;
	}
	$('.prd_container').html('<div class="page_alert_wrap"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
	$.getJSON(site_url+'/admin/jx_getdeals_by_prdstatus/'+'/'+menu+'/'+brand+'/'+src+'/'+stock+'/'+publish,'',function(resp){
		if(resp.status == 'error')
		{
			//alert(resp.error);
			$('.prd_container').html('<div class="page_alert_wrap">No Deals Found</div>');
			$('.total_wrap').hide();
		}else
		{
			var d_lst='';
			deals_lst=resp.deals;
			ttl_rows=resp.total;
			
			d_lst+='<table class="datagrid sk_deal_blk_wrap" cellpadding="0" cellspacing="0" width="99%">'
			//d_lst+='<div class="log_pagination ">'+resp.deal_pagi_links+'</div>';
				d_lst+='<thead><tr>';
					d_lst+='<th>Sl No</th>';
					d_lst+='<th>Deal Name</th>';
					d_lst+='<th width="12%">Actions <a class="mark_all" style="font-size:10px;margin-left:6px;">Select All</a><br />';
					d_lst+='</tr></thead><tbody>';
	 				
	 				$.each(deals_lst,function(i,p){
	 					if(p.is_sourceable == 1)
	 						background = 'background-color: rgba(170, 255, 170, 0.8)';
	 					else
	 						background = 'background-color: #FFAAAA';
	 						
	 					d_lst+='<tr >';
	 					d_lst+='<td>'+(++i)+'</td>';
	 					d_lst+='<td><a  href="'+site_url+'/admin/deal/'+p.dealid+'" target="_blank">'+p.name+'</td>';
	 					d_lst+='<td><input type="checkbox" class="sel" value="'+p.dealid+'"></td>';
						d_lst+='</tr>';
					});
				d_lst+='</tbody></table>';
			//d_lst+='</div>';
			
			//polist_byvendor(vids[0]);
			$('.prd_container').html(d_lst);
			$('.total_wrap').html('Total : '+resp.total_rows);
			$('.total_wrap').show();
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
	},'json');
		
}
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
});
</script>	