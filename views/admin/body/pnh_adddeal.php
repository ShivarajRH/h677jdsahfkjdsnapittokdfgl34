<?php 
$pids=array();
$d=false;
if(isset($deal))
$d=$deal; 
?>
<div class="container">
<h2><?php if($d){?>Edit<?php }else{?>Add new<?php }?> PNH Deal</h2>
<form method="post" id="pnh_add_d_form" enctype="multipart/form-data" autocomplete="off" onsubmit="return deal_form_submit(this);">

<table cellpadding=5 id="deal">
<tr><td>Deal Name : </td><td><input type="text" name="name" class="inp" size=50 value="<?=$d?$d['name']:""?>"></td></tr>
<tr><td>Print Name : </td><td><input type="text" name="print_name" class="inp" size=50 value="<?=$d?$d['print_name']:""?>"></td></tr>
<tr><td>Tagline : </td><td><input type="text" name="tagline" class="inp" size=30 value="<?=$d?$d['tagline']:""?>"></td></tr>
<tr><td>MRP : </td><td>Rs <input type="text" name="mrp" <?=$d?"readonly":""?> class="inp" size="5" value="<?=$d?$d['orgprice']:""?>"></td></tr>
<tr><td>Offer Price : </td><td>Rs <input type="text" name="offer_price" class="inp chk_price" size="5" value="<?=$d?$d['price']:""?>"></td></tr>

<input type="hidden" name="member_price" class="inp chk_price" size="5" value="<?=$d?$d['member_price']:""?>">
<input type="hidden" name="mp_max_allow_qty" size="5" value="<?=$d?$d['mp_max_allow_qty']:""?>">
<input type="hidden" name="mp_frn_max_qty" size="5" value="<?=$d?$d['mp_frn_max_qty']:""?>">
<input type="hidden" name="mp_mem_max_qty" size="5" value="<?=$d?$d['mp_mem_max_qty']:""?>">
<!--<tr><td><b>Member Price</b> : </td><td>Rs </td></tr>-->
<!--<tr><td><b>Mem price Max qty</b>:</td><td>&nbsp;&nbsp;<b>Mem price Max order qty</b> : </td></tr>-->

<tr><td>Store Offer Price : </td><td>Rs <input type="text" name="store_offer_price" class="inp chk_price" size="5" value="<?=$d?$d['store_price']:""?>"></td></tr>
<tr><td>NYP Offer Price : </td><td>Rs <input type="text" name="nyp_offer_price" class="inp chk_price" size="5" value="<?=$d?$d['nyp_price']:""?>"></td></tr>
<tr><td>Bill on order price : </td><td><input type="checkbox" name="billon_orderprice" class="inp" value="1" <?=$d?($d['billon_orderprice']?"checked":""):""?> ></td></tr>
<tr><td>Gender Attribute : </td><td><input type="text" name="gender_attr" class="inp" size=15 value="<?=$d?$d['gender_attr']:""?>"></td></tr>
<tr><td>Tax : </td><td><input type="text" class="inp" name="tax" size=2 value="<?=$d?($d['tax']/100):""?>"> %</td></tr>
<tr><td>Is Combo : </td><td><input type="checkbox" name="is_combo" value="1" <?=$d&&$d['is_combo']?"checked":""?>></td></tr>
<tr><td>Is Grouped : </td><td><input type="checkbox" name="is_group" id="is_group" value="1" <?=$d&&$d['is_group']?"checked":""?> /></td></tr>
<tr><td>Max Allowed Qty <br> (for franchise per day) : </td><td><input type="text" name="max_allowed_qty" class="inp" size=4 value="<?=$d?$d['max_allowed_qty']:""?>"></td></tr>
<tr><td><b>Has Insurance</b> : </td><td><input type="checkbox" name="has_insurance" value="1" <?=$d&&$d['has_insurance']?"checked":""?>></td></tr>
<tr><td>Image : </td><td><input type="file" name="pic" class="inp">
<?php if($d){?>To replace, upload a image<?php }?>
</td></tr>
<tr><td>Menu : </td><td>
<select name="menu">
<?php foreach($this->db->query("select * from pnh_menu order by name asc")->result_array() as $m){?>
<option value="<?=$m['id']?>" <?=$d&&$d['menuid']==$m['id']?"selected":""?>><?=$m['name']?></option>
<?php }?>
</select>
</td></tr>
<tr><td>Brand : </td><td>
<select name="brand">
<?php foreach($this->db->query("select id,name from king_brands order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>" <?=$d&&$d['brandid']==$b['id']?"selected":""?>><?=$b['name']?></option>
<?php }?>
</select>
</td>
</tr>
<tr><td>Category : </td><td>
<select name="category">
<?php foreach($this->db->query("select id,name from king_categories order by name asc")->result_array() as $b){?>
<option value="<?=$b['id']?>" <?=$d&&$d['catid']==$b['id']?"selected":""?>><?=$b['name']?></option>
<?php }?>
</select>
</td>
</tr>
<tr>
<td>Description :</td><td><textarea name="description" rows=10 cols=60><?=$d?$d['description']:""?></textarea>
</tr>
<tr>
<td>Keywords :</td><td><textarea name="keywords" rows=5 cols=60><?=$d?$d['keywords']:""?></textarea>
</tr>
<tr>
<td>Ships in :</td><td>
		<!--<input type="text" name="shipsin" value="<?=$d?$d['shipsin']:""?>">-->
		<select name="shipsin" class="shipsin" style="width:103px;">
				<option value="0"> -- Not Set -- </option>
				<option value="24-48 Hrs" <?=$d? ($d['shipsin']=='24-48 Hrs'?'selected':'')	:""?> >24-48 Hrs</option>
				<option value="48-72 Hrs" <?=$d? ($d['shipsin']=='48-72 Hrs'?'selected':'')	:""?>>48-72 Hrs</option>
				<option value="72-96 Hrs" <?=$d? ($d['shipsin']=='72-96 Hrs'?'selected':'')	:""?>>72-96 Hrs</option>
				<option value="4-5 Days" <?=$d? ($d['shipsin']=='4-5 Days'?'selected':'')	:""?>>4-5 Days</option>
		</select>
	</td>
</tr>
</table>

<?php $superadmin=$this->erpm->auth(TRUE,TRUE);?>

<fieldset style="width:600px;">
<legend><h4>Link Products</h4></legend>
	<?php if(!$d || ($d && $superadmin)){?>
		Search : <input type="text" class="inp" size=60 id="po_search">
		<div id="po_prod_list" class="closeonclick">
		</div>
	<?php }?>
	<table id="pprods" width="500" class="datagrid smallheader" style="margin-top:10px;">
		<thead>
			<tr>
				<th>Product Name</th>
				<th>MRP</th>
				<th>Qty</th>
			</tr>
		</thead>
		<tbody>
			<?php if($d)
					{
						$pids=array();
						$link_prds_det=$this->db->query("select p.mrp,l.product_id,l.qty,p.product_name from m_product_deal_link l join m_product_info p on p.product_id=l.product_id where itemid=?",$d['id'])->result_array();
						foreach($link_prds_det as $p){
				?>
			<tr>
				<td>
					<input type="hidden" name="pid[]" value="<?=$p['product_id']?>" class="al_pids"><?=$p['product_name']?>
				</td>
				<td>
					<input type="hidden" class="inp"  name="p_price[]" value="<?=$p['mrp']?>"><?=$p['mrp']?>
				</td>
				<td>
					<input type="hidden" class="inp" size=3 name="qty[]" value="<?=$p['qty']?>"><?=$p['qty']?>
				</td>
				<td colspan="2" style="<?php echo ($superadmin)?'':'display:none;';?>">
					<a href="<?php echo site_url('/admin/remove_pnhdeal_linked_prd/'.$d['id'].'/'.$p['product_id'])?>" class="remove_prd">Remove</a>
				</td>
			</tr>
			<?php $pids[]=$p['product_id']; } }?>
		</tbody>
	</table>
</fieldset>


<fieldset style="width:600px;">
<legend><h4>Link Product Groups</h4></legend>
<?php if(!$d || ($d && $superadmin)){?>
Search : <input type="text" class="inp" size=60 id="po_g_search">
<div id="po_g_prod_list" class="closeonclick">
</div>
<?php }?>

<table id="pprods_g" width="500" class="datagrid smallheader" style="margin-top:10px;">
<thead><tr><th>Group Name</th><th>Qty</th></tr></thead>
<tbody>
<?php 
	if($d)
	{
			$pids=array();
			foreach($this->db->query("select a.group_id,group_name,qty 
	from m_product_group_deal_link a 
	join products_group b on a.group_id = b.group_id 
	where itemid = ? ",$d['id'])->result_array() as $p)
			{
	?>
<tr>
<td><input type="hidden" name="pid_g[]" value="<?=$p['group_id']?>"><?=$p['group_name']?></td>
<td><input type="hidden" class="inp al_pids" size=3 name="qty_g[]" value="<?=$p['qty']?>"><?=$p['qty']?></td>
</tr>
<?php $pids[]=$p['group_id']; 
			} 
	}?>
</tbody>
</table>
</fieldset>
 

<div style="padding:10px 0px;">
<input type="submit" value="Submit">
</div>

</form>
</div>

<div style="display:none" id="p_clone_template">
<table>
<tbody>
<tr>
<td><input type="hidden" class="p_pids" name="pid[]" value="%pid%">%name%</td>
<td>%mrp%</td>
<td><input type="text" class="inp" size=3 name="qty[]" value="1"></td>
<td class="added_ped_remove"></td>
</tr>
</tbody>
</table>
</div>

<div style="display:none" id="p_clone_template_g">
<table>
<tbody>
<tr>
<td><input type="hidden" class="p_pids" name="pid_g[]" value="%pid%">%name%</td>
<td><input type="text" class="inp" size=3 name="qty_g[]" value="1"></td>
</tr>
</tbody>
</table>
</div>
<style>
.error_inp{border:1px solid #cd0000 !important;}
</style>
<script>

$('a.remove_prd').click(function(e) {
    e.preventDefault();
    if (confirm('Are you sure want to remove this product?')) {
        window.location.href = $(this).attr('href');
    }
});

function remove_added_product(e)
{
	if (confirm('Are you sure want to remove this product?')) {
		$(e).closest('tr').remove(); 
    }
}

var added_po=new Array();
var added_po_g=new Array();
<?php foreach($pids as $p){?>
added_po.push(<?=$p?>);
<?php }?>

$('.chk_price').change(function(){
	var mrp = $('input[name="mrp"]').val()*1;
	var newp = $(this).val()*1;
	if(isNaN(newp))
	{
		alert("Invalid input");
		$(this).focus();
		$(this).addClass("error_inp");
	}else if(newp > mrp){
		$(this).focus();
		alert(($(this).parent().prev().text().replace(' :',''))+" Cannot be more than MRP ");
		$(this).addClass("error_inp");
	}else
	{
		$(this).removeClass("error_inp");
	}
}); 

function addproduct(id,name,mrp,margin)
{
	id = parseInt(id);
	if($.inArray(parseInt(id),added_po)!=-1)
	{
		alert("Product already added to the current Order");
		return;
	}
	i=added_po.length;
	$("#po_prod_list").hide();
	template=$("#p_clone_template tbody").html();
	template=template.replace(/%pid%/,id);
	template=template.replace(/%name%/,name);
	template=template.replace(/%mrp%/,mrp);
	template=template.replace(/%sno%/g,i+1);
	$("#pprods tbody").append(template);
	if($(".added_ped_remove a").length)
	{
		$(".added_ped_remove a").remove();
	}
	$(".added_ped_remove").append("<a href='javascript:void(0)' onClick='remove_added_product(this)'>Remove</a>");
	added_po.push(id);
}

function addproductg(id,name,mrp,margin)
{
	id = parseInt(id);
	if($.inArray(id,added_po_g)!=-1)
	{
		alert("Product group already added to the current Order");
		return;
	}
	i=added_po.length;
	$("#po_prod_list").hide();
	template=$("#p_clone_template_g tbody").html();
	template=template.replace(/%pid%/,id);
	template=template.replace(/%name%/,name);
	template=template.replace(/%mrp%/,mrp);
	template=template.replace(/%sno%/g,i+1);
	$("#pprods_g tbody").append(template);
	added_po_g.push(id);
}



	//$("#pnh_add_d_form").submit(function(){
	function deal_form_submit(elt)
	{
		
		if($('.error_inp').length){
			alert("Invalid prices Entered");
			return false;
		}
		
		if($(".p_pids",$(elt)).length==0 && $(".al_pids",$(elt)).length==0)
		{
			alert("No products or group linked");
			return false;
		}

		if( $(".shipsin",$(elt)).val()=="0")
		{
			alert("Please select ships in time");
			$(".shipsin",$(elt)).focus();
			return false;
		}
		
		if($(".p_pids",$(elt)).length!=0)
		{
			if(!confirm("Are you sure you want to add new deal,with "+$(".p_pids",this).length+" Products Linked"))
			{
				return false;
			}
		}
		
		/*if( $("#is_group").is(":checked") && $(".p_pids",$("#pprods_g")).length == 0  ) {
		    alert("Is group is checked please add some some products for deal");
		    $("#po_g_search",this).focus();
		    return false;
		}*/
		
		return true;
	}

var jHR=0,search_timer=0;
$(function(){

	$("#po_search").keyup(function(){
		q=$(this).val();
		if(q.length<3)
			return true;
		if(jHR!=0)
			jHR.abort();
		window.clearTimeout(search_timer);
		search_timer=window.setTimeout(function(){
		jHR=$.post("<?=site_url("admin/jx_searchproductsfordeal")?>",{q:q,type:'prod'},function(data){
			$("#po_prod_list").html(data).show();
		});
		},200);
	}).focus(function(){
		if($("#po_prod_list a").length==0)
			return;
		$("#po_prod_list").show();
	}).click(function(e){
		e.stopPropagation();
	});
	$("#po_g_search").keyup(function(){
		q=$(this).val();
		if(q.length<3)
			return true;
		if(jHR!=0)
			jHR.abort();
		window.clearTimeout(search_timer);
		search_timer=window.setTimeout(function(){
		jHR=$.post("<?=site_url("admin/jx_searchproductsfordeal")?>",{q:q,type:'group'},function(data){
			$("#po_g_prod_list").html(data).show();
		});
		},200);
	}).focus(function(){
		if($("#po_g_prod_list a").length==0)
			return;
		$("#po_g_prod_list").show();
	}).click(function(e){
		e.stopPropagation();
	});
});

</script>

<style>
fieldset{
border:1px solid #ccc;
}
#po_prod_list,#po_g_prod_list{
display:none;
position:absolute;
width:600px;
max-height:230px;
overflow:auto;
background:#eee;
border:1px solid #aaa;
}
#po_prod_list a,#po_g_prod_list a{
display:block;
padding:5px;
}
#po_prod_list a:hover,#po_g_prod_list a:hover{
background:blue;
color:#fff;
}
#deal td{
vertical-align:middle !important;
}
</style>
<?php

