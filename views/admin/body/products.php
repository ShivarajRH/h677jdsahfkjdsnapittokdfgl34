<div class="container">

<div class="dash_bar">
<a href="<?=site_url("admin/products")?>"></a>
<span><?=$this->db->query("select count(1) as l from m_product_info")->row()->l?></span> Total products
</div>

<div class="dash_bar">
Showing <span><?=count($products)?></span> products
</div>

<div class="dash_bar">
view by brand : <select id="prod_disp_brand">
<option value="0">select</option>
<?php foreach($this->db->query("select id,name from king_brands order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>" <?=$b['id']==$this->uri->segment(3)?"selected":""?>><?=$b['name']?></option>
<?php }?>
</select>
</div>

<div class="dash_bar">
view by Tax : <select id="prod_disp_tax">
<option value="select">select</option>
<?php foreach($this->db->query("select vat as tax from m_product_info group by tax")->result_array() as $b){?>
<option value="<?=$b['tax']?>" <?=$b['tax']==$this->uri->segment(3)?"selected":""?>><?=$b['tax']?></option>
<?php }?>
</select>
</div>

<div class="clear"></div>

<h2><?=!isset($brand)&&!isset($tax)?"New ":""?>Products <?=isset($brand)?" of $brand brand":(isset($tax)?" with $tax% tax":"")?></h2>
<div style="float:right">
<span style="background:#faa;height:15px;width:15px;display:inline-block;">&nbsp;</span>-Not sourceable &nbsp; &nbsp; &nbsp;
<span style="background:#afa;height:15px;width:15px;display:inline-block;">&nbsp;</span>-sourceable &nbsp; &nbsp; &nbsp;
</div>

<a href="<?=site_url("admin/addproduct")?>">Add new product</a>
<table class="datagrid" width="100%">
<thead>
<tr>
<th width="20"><input type="checkbox" class="chk_all"></th>
<th width="450">Product Name</th>
<th width="50">MRP</th>
<th width="100" >Stock</th>
<th width="100">Barcode</th>
<th width="150">Brand</th>
<th ></th>
</tr>
</thead>
<?php foreach($products as $p){?>
<tr style="background:<?=$p['is_sourceable']?'rgba(170, 255, 170, 0.80)':'rgba(255, 170, 170, 0.80)'?>;">
<td ><input type="checkbox" value="<?=$p['product_id']?>" class="p_check"></td>
<td ><a class="link" href="<?=site_url("admin/product/{$p['product_id']}")?>"><?=$p['product_name']?></a></td>
<td><?=$p['mrp']?></td>
<td style="padding:0px 5px">
	<?=$p['stock']*1?>
	<?php
		$p_mrpstk_arr = $this->db->query("select mrp,sum(available_qty) as qty from t_stock_info where product_id = ? and available_qty > 0 group by mrp order by mrp asc ",$p['product_id'])->result_array();
		if($p_mrpstk_arr)
		{
			echo '<div style="background:#ffffe0;font-size:10px;padding:2px 5px;min-width:60px;border-radius:3px;">';
			foreach($p_mrpstk_arr as $p_mrpstk)
				echo '<div class="clearboth"><b>Rs '.round($p_mrpstk['mrp']).'</b>  <b class="fl_right">'.($p_mrpstk['qty']).'</b></div>';
			echo '</div>';
		}
			
		
	?>
</td>
<td>
<img src="<?=IMAGES_URL?>loading_maroon.gif" class="busy">
<form action="<?=site_url("admin/update_barcode")?>" method="post" class="barcode_forms">
<input type="hidden" name="pid" value="<?=$p['product_id']?>">
	<input type="text" class="barcode_inp" name="barcode" value="<?=(string)$p['barcode']?>" size=15>
</form>
</td>
<td width="100"><?=$p['brand']?></td>
<td>
<a href="<?=site_url("admin/editproduct/{$p['product_id']}")?>">edit</a> &nbsp;&nbsp;&nbsp;&nbsp; 
<a href="<?=site_url("admin/viewlinkeddeals/{$p['product_id']}")?>">view linked deals</a>
</td>
</tr>
<?php }?>
<tr>
	<td colspan="8" align="left" class="pagination"><?php echo $pagination;?></td>
</tr>
</table>
<div>With Selected : <input type="button" value="Mark it as Sourcable" onclick='mark_src("1")'> <input type="button" value="Mark it as Not-Sourcable" onclick='mark_src("0")'></div>
</div>
<form id="src_form" action="<?=site_url("admin/mark_src_products")?>" method="post">
<input type="hidden" name="pids" class="pids">
<input type="hidden" name="action" class="action" value="1">
</form>
<style>
.busy{
display:none;
}
</style>
<script>
function mark_src(act)
{
	var pids=[];
	$(".p_check:checked").each(function(){
		pids.push($(this).val());
	});
	pids=pids.join(",");
	$("#src_form .action").val(act);
	$("#src_form .pids").val(pids);
	$("#src_form").submit();
}
$(function(){
	$(".barcode_forms").submit(function(){
		$(".busy",$(this).parent()).show();
		$(this).hide();
		f=$(this);
		$.post(f.attr("action"),f.serialize(),function(data){
			f.show();
			$(".busy",f.parent()).hide();
		});
		return false;
	});
	$(".chk_all").click(function(){
		if($(this).attr("checked"))
			$(".p_check").attr("checked",true);
		else
			$(".p_check").attr("checked",false);
	});
	$("#prod_disp_tax").change(function(){
		v=$(this).val();
		if(v!="select")
			location='<?=site_url("admin/productsbytax")?>/'+v;
	});
	$(".barcode_inp").focus(function(){
		$(this).data("ol_val",$(this).val());
		$(this).val("");
	}).blur(function(){
		if($(this).val().length==0)
			$(this).val($(this).data("ol_val"));
	});
	$("#prod_disp_brand").change(function(){
		v=$(this).val();
		if(v!=0)
			location='<?=site_url("admin/productsbybrand")?>/'+v;
	});
});
</script>
<?php
