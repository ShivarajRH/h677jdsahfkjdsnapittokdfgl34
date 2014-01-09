<div class="container">

<h2>Product Group : <?=$group['group_name']?></h2>

<h4 style="margin:0px;margin-top:20px;">Attribute Classes</h4>
<table class="datagrid">
<thead><tr><th>Attributes</th><th>Possible Values</th></tr></thead>
<tbody>
<?php $as=$this->db->query("select group_concat(av.attribute_value) as `values`,a.attribute_name as name from products_group_attributes a join products_group_attribute_values av on av.attribute_name_id=a.attribute_name_id where a.group_id=? group by a.attribute_name_id order by a.attribute_name_id",$group['group_id'])->result_array(); foreach($as as $a){?>
<tr>
<td><?=$a['name']?></td><Td><?=$a['values']?></Td>
</tr>
<?php }?>
</tbody>
</table>


<h4 style="margin:0px;margin-top:20px;">Products Linked</h4>

<table class="datagrid">
<thead><tr><th>Product Name</th><?php foreach($as as $a){?><th><?=$a['name']?></th><?php }?></tr></thead>
<tbody>
<?php foreach($prods as $prod){?>
<tr>
<td><a href="<?=site_url("admin/product/{$prod['product_id']}")?>" class="link"><?=$prod['product_name']?></a></td>
<?php foreach($this->db->query("select av.attribute_value as value from products_group_pids p join products_group_attribute_values av on av.attribute_value_id=p.attribute_value_id where p.group_id=? and p.product_id=?",array($group['group_id'],$prod['product_id']))->result_array() as $a){?>
<td><?=$a['value']?></td>
<?php }?>
</tr>
<?php }?>
<?php
	if($this->erpm->auth(true,true))
	{
?>
	<tr>
		<td colspan="2" align="right">
			<a class="button button-tiny button-action button-rounded" id="link_product_btn" href="javascript:void(0)"> ADD Product</a>
		</td>
	</tr>
<?php		
	}
?>
</tbody>
</table>

<br>
<h4 style="margin:0px;margin-top:20px;">Deals Linked</h4>
<table class="datagrid">
<thead><tr><th>Sno</th><th>Deal Name</th><th>MRP</th><th>Price</th></tr></thead>
<tbody>
<?php $i=1; foreach($this->db->query("select i.* from king_dealitems i join m_product_group_deal_link l on l.itemid=i.id where l.group_id=?",$group['group_id'])->result_array() as $p){?>
<tr>
<td><?=$i++?></td><td><a href="<?=site_url("admin/deal/{$p['id']}")?>"><?=$p['name']?></a></td><td><?=$p['orgprice']?></td><td><?=$p['price']?></td>
</tr>
<?php }?>
</tbody>
</table>

<div style="display: none">
<div id="link_producttogroup" title="Add Product to Group">
	<div class="error_block"></div>
	<form>
	<input type="hidden" name="group_id" value="<?=$group['group_id']?>">
	<table>
		<tr>
			<td><b>ProductID</b></td>
			<td><input type="text" name="new_prod_id" value=""></td>
		</tr>
		<?php $as=$this->db->query("select a.attribute_name_id,group_concat(av.attribute_value) as `values`,a.attribute_name as name from products_group_attributes a join products_group_attribute_values av on av.attribute_name_id=a.attribute_name_id where a.group_id=? group by a.attribute_name_id order by a.attribute_name_id",$group['group_id'])->result_array(); foreach($as as $a){?>
		<tr>
		<td><b><?=$a['name']?></b> <input type="hidden" name="new_prod_attr_names[<?=$a['attribute_name_id']?>]" value="<?=$a['name']?>">  </td><td><input type="text" name="new_prod_attr[<?=$a['attribute_name_id']?>]" value=""></td>
		</tr>
		<?php }?>
	</table>
	</form>
	
</div>
</div>

</div>

<style>
	.error_block{background: #FFFFF0;}
	.error_block p{margin:3px;color:#CD0000;font-size: 11px;padding:3px;}
</style>


<script type="text/javascript">
	
	$('#link_producttogroup').dialog({
										autoOpen:false,
										modal:true,
										width:300,
										height:'auto',
										open:function(){
											$('input[name="new_prod_id"]',this).val('');
											$('input[name="new_prod_attr"]',this).val('0');
										},buttons:{
											'Add' : function(){
												var btn = $(".ui-dialog-buttonpane button:contains('Add')");
													btn.button('disable');
													$('.ui-button-text',btn).text('Loading...');
													
												 
												$('#link_producttogroup .error_block').html("<p>Please wait...</p>");
												$.post(site_url+'/admin/jx_upd_producttogroup',$('#link_producttogroup form').serialize(),function(resp){
													if(resp.status == 'error')
													{
														$('#link_producttogroup .error_block').html(resp.error);
														$(".ui-dialog-buttonpane button:contains('Loading...')").button("enable");
														$(".ui-dialog-buttonpane button:contains('Loading...') .ui-button-text").text('Add');
													}else
													{
														$('#link_producttogroup').dialog('close');
														alert("New product linked successfully");
														location.href = location.href;
													}
												},'json');
											}
										}
									});
	$('#link_product_btn').click(function(){
		$('#link_producttogroup').dialog('open');
	});
</script>

<?php
