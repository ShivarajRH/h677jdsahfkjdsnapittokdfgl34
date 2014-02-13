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

<div class="dash_bar">View Stock products by brand : <select id="brand"><option value="0">select</option>
<?php foreach($this->db->query("select name,id from king_brands where id in (select p.brand_id from m_product_info p join t_stock_info s on s.product_id=p.product_id where s.available_qty>0) order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>" <?php if($this->uri->segment(4)==$b['id']){?>selected<?php }?>><?=$b['name']?></option>
<?php }?>
</select></div>
<div class="clear"></div>
<?php if(isset($products)){?>
<h3><?=$pagetitle?></h3>
<table class="datagrid" width="80%">
<thead><tr><Th>Sno</Th><th>Product ID</th><th>Product Name</th><th style="text-align: right">MRP</th><th>Stock Qty</th><th style="text-align: right">MRP Value</th><th align="center" style="text-align: right">Avg <br /> Purchase <br />Price</th><th style="text-align: right">Avg <br /> Total <br />purchase</th></tr></thead>
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
	<td align="right"><b><?=format_price($t_avg)?></b></td></tr>
</tbody>

</table>

<?php }?>

<script>

$(function(){
	$("#brand").change(function(){
		if($(this).val()=="0")
			return;
		location="<?=site_url("admin/warehouse_summary/1")?>/"+$(this).val();
	});
});

</script>


</div>
<?php
