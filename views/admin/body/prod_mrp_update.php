<div class="container">
<h2>Product MRP bulk update</h2>
		 Brand : <select id="sel_brand">
			<option value="">All</option>
			<?php foreach($this->db->query("select id,name from king_brands order by name asc")->result_array() as $b)
				{?>
					<option value="<?=$b['id']?>" <?php echo (($bid == $b['id'])?'selected':'');?> ><?=$b['name']?></option>
			<?php }?>
		</select>
				
		 Category : <select id="sel_category">
			<option value="">All</option>
			
			<?php
				$cond = '';
				if($bid)
				{
					$cond = ' and b.brandid = '.$bid;
				}
			?>
			
			<?php foreach($this->db->query("select distinct a.id,a.name from king_categories a join king_deals b on a.id = b.catid where 1 $cond  order by a.name asc")->result_array() as $c)
				{
			?>
				<option value="<?=$c['id']?>" <?php echo (($cid == $c['id'])?'selected':'');?> ><?=$c['name']?></option>
			<?php }?>
		</select>
		
		<input type="button"  value="Submit" onclick="load_filtered_products()" > 

		<?php if($prods) { ?>
			<div style="padding:20px;">
				<form id="prodmrpnstat_frm" method="post">
					<?php if(isset($prods)){?>
						<div class="products_total">Total Products : <?=count($prods)?></div>
						
						Select All Products : <input type="checkbox" class="chkall_sel" value="1">
						
						<table class="datagrid datagridsort">
							<theaD>
								<tr>
									<th>Product Name</th>
									<th>MRP</th>
									<th>New Mrp</th>
									<th>Sourceable</th>
									<th>
										
									</th>
								</tr>
							</theaD>
						<tbody>
					<?php foreach($prods as $k=>$p)
							{?>
								<tr class="<?php echo ($p['is_sourceable']*1)?'src':'nsrc' ?>" >
									
									<td><?=$p['product_name']?></td>
									<td align="right"><?=($p['mrp']*1)?></td>
									<td><input type="hidden" tabindex="<?php echo $k+1;?>" name="pid[]" value="<?=$p['product_id']?>"><input type="text" name="mrp[]" size=8 value=""></td>
									<td class="psrcstat"><span class="src_stat"><?php echo $p['is_sourceable']?'Yes':'No' ?></span> <a href="javascript:void(0)" prod_id="<?php echo $p['product_id'];?>" onclick="upd_prdsourceablestat(this)" nsrc="<?php echo $p['is_sourceable']*1;?>" >Change</a> </td>
									<td>
										<input type="hidden" value="<?php echo $p['is_sourceable'];?>" id="prod_stat_<?php echo $p['product_id'];?>" class="prodnstat" name="prod[<?php echo $p['product_id'];?>]" >
										<input type="checkbox" <?php echo $p['is_sourceable']?'checked':'';?> class="hndl_pstat" pid="<?php echo $p['product_id'];?>">
									</td>
								</tr>
							<?php }?>
						</tbody>
					</table>
							<input type="submit" value="Update MRPs">
							<input type="button" onclick="upd_bulkprodsrcstat()" value="Update Product Status">
					<?php }?>			
				</form>
			</div>
		<?php } ?> 
	</div>


<style>
	.products_total {color: #000000;font-size: 16px;font-weight: bold;margin-bottom: 10px;}
	.src{	background:#afa; }
	.nsrc{ background:#faa; }
	#sl_products td {text-align: center;}
	.psrcstat a{font-size: 10px;color: blue;}
</style>


<script>
$('.datagrid').tablesorter({sortList:[[1,0]]});
 $(".datagrid thead th:eq(5)").data("sorter", false);




$('.hndl_pstat').change(function(){
	var sel_pid = $(this).attr('pid');
		if($(this).attr('checked'))
			$('#prod_stat_'+sel_pid).val(1);
		else
			$('#prod_stat_'+sel_pid).val(0);
});

$('.chkall_sel').change(function(){
	$('.hndl_pstat').attr('checked',$(this).attr('checked')?true:false).trigger('change');
});

function upd_prdsourceablestat(ele)
{
	nsrc = $(ele).attr('nsrc');
	prod_id = $(ele).attr('prod_id');
	if(confirm("Are you sure want to mark this product "+((nsrc==1)?'Not':'')+' Sourceable ?'))
	{
		$.post(site_url+'/admin/jx_upd_prodsrcstatus',{pid:prod_id,stat:nsrc},function(resp){
			if(resp.status)
			{
				var src_disp_ele = $(ele).parent().find('.src_stat');
				if(nsrc==1)
					{
						$(ele).parent().parent().removeClass('src').addClass('nsrc');
						src_disp_ele.text("No");	
						$(ele).attr('nsrc',0);
					}else
					{
						$(ele).parent().parent().removeClass('nsrc').addClass('src');
						src_disp_ele.text("Yes"); 
						$(ele).attr('nsrc',1);	
					}	
			}
		},'json');
			
	}
}

function upd_bulkprodsrcstat()
{
	if(confirm("Are you sure want proceed ?"))
	{
		$('body').css('opacity','0.5');
		$.post(site_url+'/admin/jx_upd_bulkprodsrcstatus',$('#prodmrpnstat_frm').serialize(),function(resp){
			if(resp.status)
			{
				alert("Products new statuses updated");
			}
				
			$('body').css('opacity','1');	
			
			location.href = location.href;
			
		},'json');
	}
}

function load_filtered_products()
{
	bid = $('#sel_brand').val()*1;
	cid = $('#sel_category').val()*1;
	location.href = site_url+'/admin/prod_mrp_update/'+bid+'/'+cid;
}
 
$(function(){
	$("#sel_brand").change(function(){
		var bid = $(this).val();
			$("#sel_category").html('<option>loading...</option>');
			if(bid)
			{
				$.getJSON(site_url+'/admin/jx_getcatbybrand/'+bid,'',function(resp){
					var catlist_html = '<option value="0">All</option>';
						if(resp.cat_list.length)
						{
							$.each(resp.cat_list,function(a,b){
								catlist_html += '<option value="'+b.id+'">'+b.name+'</option>';
							});
						}
					$("#sel_category").html(catlist_html);	
				});
			}
	});
	
	
});
</script>
<?php
