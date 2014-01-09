<div class="container">
<h2>New Client Order</h2>
<form method="post" autocomplete="off" id="client_order_form">
<?php 
	if($cid)
	{
?>
<h4>
	<span style="background:#eee;padding:5px;">Selected Vendor : <?=$this->db->query("select client_name from m_client_info where client_id=?",$cid)->row()->client_name?></span>
	<input id="client_id_inp" type="hidden" name="cid" value="<?php echo $cid?>"/>
</h4>
<?php 
	}
	else
	{
		$client_list_res = $this->db->query("select client_id,client_name from m_client_info order by client_name ");
		if($client_list_res->num_rows())
		{
?>
	<b>Client </b>: 
	<select id="client_id_inp" name="cid" >
		<option value="">Choose</optiuon>
<?php 			
			foreach($client_list_res->result_array() as $client_det)
			{
?>
				<option value="<?php echo $client_det['client_id']?>"><?php echo $client_det['client_name']?></option>	
<?php 
			}
?>
	</select>
<?php 			
		}
	} 
?>
<h3 style="margin:0px;">Product Items</h3>



<table class="datagrid noprint"  id="pprods" style="min-width:500px;">
<thead><tr><th>Product Name</th><th>MRP</th><th colspan=2>Qty</th></tr></thead>
<tbody>
</tbody>
</table>
<div style="background:#eee;padding:5px;width:490px;">
Search &amp; add : <input type="text" class="inp" id="inp_search" style="width:300px;">
<div id="po_prod_list" class="srch_result_pop closeonclick"></div>
<div>Load products by brand : 
<span id="load_brands"><select>
<?php foreach($this->db->query("select * from king_brands order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>"><?=$b['name']?></option>
<?php }?>
</select></span><input type="button" value="Show & load" id="sl_show">
</div>
</div>

<table class="datagrid noprint" style="margin-top:20px;">
<tbody>
<tr><td>Reference No :</td><td><input type="Text" class="inp" name="ref"></td></tr>
<tr><td>Remarks :</td><td><textarea name="remarks" rows="5" cols="40"></textarea></td></tr>
</tbody>
</table>

<input type="submit" value="Place client order" style="margin-top:10px;">

</form>

</div>


<div style="display:none">
<table id="sl_prod_template">
<tbody>
<tr><td><input type="checkbox" class="sl_sel_prod"><input type="hidden" class="pid" value="%pid%"></td><td class="name">%product%</td><td>Rs <span class="mrp">%mrp%</span></td><td>%stock%</td><td class="margin">%margin%</td></tr>
</tbody>
</table>
<table id="p_clone_template">
<tr>
<td><input type="hidden" name="product[]" value="product_id">
product_name</td>
<td><input type="text" class="mrp inp" size="4" name="mrp[]" value="mrpvalue"></td>
<td><input type="text" class="qty inp" size="2" name="qty[]"  value=""></td>
<td><a href="javascript:void(0)" onclick='$(this).parent().parent().remove()'>remove</a></td>
</tr>
</table>
</div>



<div id="sl_products">
<h3>Choose and add to current order</h3>
<table class="datagrid" width="100%">
<thead>
<tr><th></th><th>Product</th><th>Mrp</th><th>Stock</th><th>Margin</th></tr>
</thead>
<tbody>
</tbody>
</table>
<input type="button" value="Load selected products" onclick='loadbrandproducts()'> <input type="button" value="Close" onclick='$("#sl_products").hide()'> 
</div>



<script>

var added_po=[];

function addproduct(id,name,mrp,margin)
{
	if($.inArray(id,added_po)!=-1)
	{
		alert("Product already added to the current Order");
		return;
	}
	i=added_po.length;
	$("#po_prod_list").html("").hide();
	template=$("#p_clone_template tbody").html();
	template=template.replace("product_id",id);
	template=template.replace("product_name",name);
	template=template.replace("mrpvalue",mrp);
	template=template.replace(/%margin%/g,margin);
	template=template.replace(/%foc%/g,"foc"+i);
	template=template.replace(/%offer%/g,"offer"+i);
	$("#pprods tbody").append(template);
	added_po.push(id);
}
var search_timer=0,jHR=0;
$(function(){
	$("#client_order_form").submit(function(){

		if(($("#client_id_inp",$(this)).val()*1)==0)
		{	
			alert("Please choose client");
			return false;	
		}
		
		if($(".qty",$(this)).length==0)
		{	alert("no products added");return false;	}
		flag=true;
		
		
		$(".qty",$(this)).each(function(){
			if($(this).val()==0)
			{
				flag=false;
				alert("please check quantity");
				return false;
			}
		});
		return flag;
	});
	$("#inp_search").keyup(function(){
		q=$(this).val();
		if(jHR!=0)
			jHR.abort();
		clearTimeout(search_timer);
		search_timer=setTimeout(function(){
		jHR=$.post("<?=site_url("admin/jx_searchproducts")?>",{q:q},function(data){
			$("#po_prod_list").html(data).show();
		});},200);
	}).focus(function(){
		if($("#po_prod_list a").length==0)
			return;
		$("#po_prod_list").show();
	}).click(function(e){
		e.stopPropagation();
	});

	$("#sl_show").click(function(){
		bid=$("#load_brands select").val();
		$.post("<?=site_url("admin/jx_getproductsforbrand")?>",{bid:bid},function(json){
			data=$.parseJSON(json);
			brand_prods=data;
			$.each(data,function(i,p){
				template=$("#sl_prod_template tbody").html();
				template=template.replace(/%id%/g,i);
				template=template.replace(/%pid%/g,p.id);
				template=template.replace(/%product%/g,p.product);
				template=template.replace(/%stock%/g,p.stock);
				template=template.replace(/%margin%/g,p.margin);
				template=template.replace(/%mrp%/g,p.mrp);
				$("#sl_products .datagrid tbody").append(template);
			});
			$("#sl_products").show();
		});
	});
	
	
});


function loadbrandproducts()
{
	$(".sl_sel_prod:checked").each(function(){
		tr=$($(this).parents("tr").get(0));
		addproduct($(".pid",tr).val(),$(".name",tr).html(),$(".mrp",tr).html(),$(".margin",tr).html());
	});
	$("#sl_products .datagrid tbody").html("");
	$("#sl_products").hide();
}


</script>

<style>
#sl_products{
position:absolute;
width:600px;
min-height:300px;
max-height:400px;
overflow:auto;
top:200px;
left:200px;
border:1px solid #aaa;
display:none;
background:#fff;
padding:10px;
}
</style>

<?php
