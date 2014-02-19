
<div class="container">
	<h2>Warehouse Summary</h2>
	<div class="dash_bar">Total Products in Stock :<span><?=$this->db->query("select count(distinct p.product_id) as n from m_product_info p join t_stock_info s on s.product_id=p.product_id where available_qty>0")->row()->n?></span></div>

	<?php 
		if($this->erpm->auth(true,true)){
	?>
		<div class="dash_bar">Total Value : <span>Rs <?=format_price($this->db->query("select sum(p.mrp*s.available_qty) as n from m_product_info p join t_stock_info s on s.product_id=p.product_id where s.available_qty>0")->row()->n,2)?></span></div>
	<?php } ?>

	<div class="dash_bar">Brands in stock : <span><?=$this->db->query("select count(distinct p.brand_id) as n from m_product_info p join t_stock_info s on s.product_id=p.product_id where s.available_qty>0")->row()->n?></span></div>
	<div class="clear"></div>

	<div class="dash_bar">Menu :
		<select name="menu" data-placeholder="Choose" id="choose_menu" sel_menu="<?php echo $menuid?>">
			<option value="0">Choose</option>
				<?php foreach($this->db->query("select id,name from pnh_menu where status = 1 order by name asc")->result_array() as $menu){?>
						<option value="<?php echo $menu['id']?>" <?php echo set_select('menu',$menu['id'],($menuid == $menu['id']))?>><?php echo $menu['name']?></option>
				<?php }?>
		</select>
	</div>

	<div class="dash_bar" >Category :
		<select name="sel_cat" data-placeholder="Choose" id="select_cat" sel_cat="<?php echo $catid?>">
			<option value="0">Choose</option>
		</select>
	</div>

	<div class="dash_bar">
		View Stock products by brand : 
			<select id="brand" sel_brand="<?php echo $brandid?>">
				<option value="">select</option>
				<?php foreach($this->db->query("select name,id from king_brands where id in (select p.brand_id from m_product_info p join t_stock_info s on s.product_id=p.product_id where s.available_qty>0) order by name asc")->result_array() as $b){?>
					<option value="<?=$b['id']?>" <?php if($this->uri->segment(4)==$b['id']){?>selected<?php }?>><?=$b['name']?></option>
				<?php }?>
			</select>
	</div>
	<div class="clear"></div>

	<?php if(isset($products)){?>
			<div class="clear">
				<div class="fl_left"><h3><?=$pagetitle?></h3></div>
				<div class="fl_right"><a class="button button-tiny button-rounded" href="javascript:void(0)" onclick="print_warehouse_summary()" >Print summary</a></div>
			</div>
			<div class="clear">
				<table class="datagrid" width="100%">
					<thead>
						<tr>
							<Th>Sno</Th><th>Product ID</th><th>Product Name</th><th style="text-align: right">MRP</th><th>Stock Qty</th><th style="text-align: right">MRP Value</th><th align="center" style="text-align: right">Avg <br /> Purchase <br />Price</th><th style="text-align: right">Avg <br /> Total <br />purchase</th>
						</tr>
					</thead>
					
					<tbody>
						<?php $t_sv=$t_avg=0;$i=1; $qty_t = 0; foreach($products as $p){?>
							<tr>
								<td><?=$i++?></td>
								<td><a class="link" target="_blank" href="<?=site_url("admin/product/{$p['product_id']}")?>"><?=$p['product_id']?></a></td>
								<td><a class="link" target="_blank"  href="<?=site_url("admin/product/{$p['product_id']}")?>"><?=$p['product_name']?></a></td>
								<td align="right"><?=format_price($p['mrp'],0)?></td>
								<td>
									<?=$p['stock']*1?>
									<?php
										$p_mrpstk_arr = $this->db->query("select mrp,sum(available_qty) as qty from t_stock_info where product_id = ? and available_qty > 0 group by mrp order by mrp asc ",$p['product_id'])->result_array();
										if($p_mrpstk_arr)
										{
											echo '<div style="background:#ffffa0;font-size:10px;padding:2px 5px;min-width:60px;border-radius:3px;">';
											foreach($p_mrpstk_arr as $p_mrpstk)
												echo '<div class="clearboth"><b>Rs '.format_price($p_mrpstk['mrp'],0).'</b>  <b class="fl_right" style="float:right">'.($p_mrpstk['qty']).'</b></div>';
											echo '</div>';
										}
										
										$qty_t += $p['stock']*1;
									?>
								</td>
								<td align="right"><?=format_price($p['stock_value'],0)?></td>
								<td align="right"><?php $avg=round($this->db->query("select avg(purchase_price) as a from t_grn_product_link where product_id=?",$p['product_id'])->row()->a,2); echo format_price($avg);?></td>
								<td align="right"><?=format_price($p['stock']*$avg,2)?></td>
							</tr>
						<?php $t_sv+=$p['stock_value']; $t_avg+=$p['stock']*$avg; }?>
							<tr>
								<td colspan="4" align="right">Total </td>
								<td><?=$qty_t;?> Qtys</td>
								<td align="right"><b><?=format_price($t_sv)?></b></td>
								<td></td>
								<td align="right"><b><?=format_price($t_avg)?></b></td>
							</tr>
					</tbody>
				</table>
			</div>
	<?php }?>
</div>
<script>

$('#choose_menu').change(function(){
	var sel_menuid=$('#choose_menu').val();
	
	if($(this).val())
	{
		$('#select_cat').html('<option value="">Loading...</option>').trigger("lizst:updated");
		$.getJSON(site_url+'/admin/jx_load_allcatsbymenu/'+$(this).val(),'',function(resp){
			var cat_html='';
			if(resp.status =='error')
			{
				alert(resp.msg);
			}
			else
			{
				cat_html+='<option value="0">Choose</option>';
				$.each(resp.cat_list,function(i,c){
					cat_html+='<option value="'+c.catid+'">'+c.name+'</option>';
				});
			}
			$('#select_cat').html(cat_html).trigger("liszt:updated");
			var sel_catid = $('#select_cat').attr('sel_cat');
				$('#select_cat').val(sel_catid).trigger('change');
		});
	}
}).trigger('change');


$('#select_cat').change(function(){
	var sel_catid=$('#select_cat').val();
	
	if($(this).val())
	{
		$('#brand').html('<option value="">Loading...</option>').trigger("lizst:updated");
		$.getJSON(site_url+'/admin/jx_load_allbrandsbycat/'+$(this).val(),'',function(resp){
		var cat_html='';
		if(resp.status =='error')
		{
			alert(resp.msg);
		}
		else
		{
			cat_html+='<option value="0">Choose</option>';
			cat_html+='<option value="all">All</option>';
			$.each(resp.brand_list,function(i,b){
			cat_html+='<option value="'+b.brandid+'">'+b.name+'</option>';
			
			});
		}
			$('#brand').html(cat_html).trigger("liszt:updated");
			var sel_brandid = $('#brand').attr('sel_brand');
				$('#brand').val(sel_brandid);
		});
	}
});


$("#brand").live('change',function(){
		var menuid=$('#choose_menu').val();
		var catid=$('#select_cat').val();
		if($(this).val()=="0")
			return;
		location=site_url+"/admin/warehouse_summary/1/"+$(this).val()+'/'+catid+'/'+menuid;
});
	
function print_warehouse_summary()
{
	var brandid=$('#brand').val();
	var catid=$('#select_cat').val();
	var menuid=$('#choose_menu').val();
	var print_url = site_url+'/admin/print_brands_summary/'+brandid+'/'+catid+'/'+menuid;
		window.open(print_url);
}

</script>


<?php
