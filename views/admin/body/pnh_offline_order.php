<div class="container" style="padding:5px;">
<div style="position:absolute;top:0px;right:60px;"><a href="javascript:void(0)" style="background:#ffaa00;border:1px solid #aaa;border-top:0px;text-decoration:none;display:block;padding:0px 5px 1px 5px;border-radius:0px 0px 5px 5px;color:#555;" onclick='$("#hd").slideDown("slow");$(this).parent().hide();$("#prod_suggest_list").css({"top":"184px"})'>show menu</a></div>
<div style="clear: both;overflow: hidden;">
	<h2 style="margin:5px 0px;float: left;">PNH Offline Order <span id="fran_name_disp" style="font-size: 80%"></span></h2>
        <div class="offn_courier_detail"></div>
</div>
<div id="fran_select_cont">
	<select class="chzn-select" data-placeholder="Choose Franchise "  name="sel_fid" style="width:250px;" ></select>
	&nbsp;&nbsp; Or &nbsp;&nbsp;Enter Franchise ID : 
	<input maxlength="8" type="text" class="inp" id="fid_inp" size=20>
	<input type="button" value="Select" onclick='load_franchisebyid()'> &nbsp; &nbsp; &nbsp;Or &nbsp; &nbsp; &nbsp; Enter Franchise Login Mobile : <input type="text" class="inp" id="fmobile_inp" size=20><input maxlength="10" type="button" value="Select" onclick='load_franchisebymobile()'>
</div>
<div id="franchise_det">
</div>
<?php /*?>
<div id="frans_list" style="margin-top:20px;height:400px;overflow:auto;float:left;">
<table class="datagrid">
<thead><tr><th>Franchise Name</th><th>FID</th><th>Town</th><th>City</th><th>Territory</th><th>Login Mobile1</th><th>Login Mobile2</th></tr></thead>
<tbody>
<?php foreach($this->db->query("select f.franchise_id,f.pnh_franchise_id,f.login_mobile1,f.login_mobile2,f.franchise_name,f.city,t.town_name,tr.territory_name from pnh_m_franchise_info f join pnh_towns t on t.id=f.town_id join pnh_m_territory_info tr on tr.id=f.territory_id order by f.franchise_name asc")->result_array() as $f){?>
<tr onclick='$("#fid_inp").val("<?=$f['pnh_franchise_id']?>");load_franchisebyid();' style="cursor:pointer;">
<td><?=$f['franchise_name']?></td><td><a target="_blank" loc="<?=$f['town_name'].','.$f['territory_name'];?>" href="<?=site_url("admin/pnh_franchise/{$f['franchise_id']}")?>"><?=$f['pnh_franchise_id']?></a></td><td><?=$f['town_name']?></td><td><?=$f['city']?></td><td><?=$f['territory_name']?></td><td><?=$f['login_mobile1']?></td><td><?=$f['login_mobile2']?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>
<?php */?>

<div class="clear"></div>

<div id="showafter">
<div id="show_scheme_details" title="Scheme Details">
<fieldset>
<legend><b>Active Scheme Discounts</b></legend>
<table class="datagrid  noprint" id="active_scheme_discount">
<thead><th>Menu</th><th>Brand</th><th>Category</th><th>Discount(%)</th><th>Valid From</th><th>Valid To</th></thead>
<tbody></tbody>
</table>
</fieldset>
<br><br><br>
<fieldset>
<legend><b>Active Super Scheme</b></legend>
<table class="datagrid  noprint" id="active_super_scheme">
<thead><th>Menu</th><th>Brand</th><th>Category</th><th>Target Sales</th><th>Credit(%)</th><th>Valid From</th><th>Valid To</th></thead>
<tbody></tbody>
</table>
</fieldset>
<br><br><br>
<fieldset>
<legend><b>Active IMEI Scheme</b></legend>
<table class="datagrid  noprint" id="active_imei_scheme">
<thead><th>Menu</th><th>Brand</th><th>Category</th><th>Scheme Type</th><th>Credit value</th><th>Valid From</th><th>Applied From</th><th>Valid To</th></thead>
<tbody></tbody>
</table>
</fieldset>
<br><br><br>
<fieldset>
<legend><b>Alloted Menu</b></legend>
<table class="datagrid  noprint" id="menu">
<tbody></tbody>
</table>
</fieldset>
</div>

<b style="float: right;margin-right:398px;margin-top:-44px;margin-bottom:25px;font-size: 11px;background-color:<?php echo $fr_status_color?>;color:#fff;padding:2px 3px;border-radius:3px;"><?php echo $fran_status_arr[$fran_status];?></b>
<a href="javascript:void(0)" onclick="load_scheme_details()" style="float: right;margin-top: -39px;margin-right:156px;" class="button">Scheme &amp; Menu Details</a>
<a href="<?php echo site_url('/admin/pnh_deals')?>" target="_blank" style="float:left;margin-top: -38px;margin-left:1171px;" class="button">GoTo Deals</a>
<div class="fixed_bottom" id="srch_deals_cont">Search Deal : <div id="srch_results"></div><input type="text" class="inp" style="width:320px;" id="p_srch" autocomplete="off" ></div>
<div class="fixed_bottom" style="left:480px;">PNH PRODUCT ID : <input type="text" class="inp" maxlength="8" size=30 id="p_pid" autocomplete="off" ><input type="button" value="Add" class="add_product"></div>
<div class="fixed_bottom" style="left:935px;">Barcode : <input type="text" class="inp" size=20 id="p_barcode" autocomplete="off" ><input type="button" value="Add" class="add_b_product"></div>

<div style="padding:5px;">

<form method="post" id="order_form" autocomplete="off">
<input type="checkbox" id="redeem_r" name="redeem" value="1" style="display:none;">
<input type="hidden" id="redeem_p" name="redeem_points" value="0" style="display:none;">
<div id="prod_suggest_list">
<fieldset>
<legend><b>Product Data</b></legend>
<table class="datagrid smallheader noprint" id="prevorderd_prod">
<thead><th>Product name</th><th>Total Orderd</th><th>Total Shipped</th></thead>
<tbody>

</tbody>
</table>
</fieldset>

<br><br>
<fieldset>
<legend><b>Fully/Partially Shipments</b></legend>
<table class="datagrid smallheader noprint" id="prevorderd_unshipped">
<thead><th>Trans ID</th><th>Orderd Date</th></thead>
<tbody><a href="javascript:void(0)" id="view_trans"></a></tbody>
</table>
</fieldset>



</div>



<!--<div id="prod_suggest_list">

<ul>
		  <li><a href="#prod_suggest">Product Suggestions</a></li>
		<li><a href="#cancel_prod_suggest_list">Cancelled Orders</a></li>
</ul>
	<div id="prod_suggest"></div>
	<div id="cancel_prod_suggest_list"></div>
</div>-->


<div style="margin:5px 0px;background:#fcfcfc;border:1px solid #f1f1f1;padding:10px;width:68% !important;">
<div style="clear: both">
		<div id="display_fr_totals" style="float: right">
			<table width="500" cellpadding="4" cellspacing="0" style="border-collapse: collapse;">
				<tr>
					<td align="center">Credit Limit </td>
					<td align="center">Available Limit</td>
					<td align="center">Activated Members </td>
					<td align="center">Orders </td>
					<td align="center">Last OrderedOn</td>
				</tr>
				<tr>
					<td align="center">Rs <b><span id="fran_credit"></span></b></td>
					<td align="center">Rs <b><span id="fran_balance"></span></b></td>
					<td align="center"><b><span class="total_mem"></span></b></td>
					<td align="center"><b><span class="total_ord"></span></b></td>
					<td align="center"><b><span class="last_ord"></span></b></td>
				</tr>
				</table>
		</div>
		
		<h4 style="margin:0px;">Order for</h4>
		
		<select name="mid_entrytype" id="mid_entrytype" style="width:200px;"><option value="0">Registered Member</option><option value="1">Not Registered Member</option></select>
		<br><br>
		<div class="mid_blk" style="background: #F1F1F1;padding:10px;">
		Enter MEMBER ID (MID) :<input style="font-size:120%" maxlength="8"  type="text" class="mid" name="mid" size=18 >
		</div>
		<div style="clear:both;overflow: hidden;background: #fcfcfc">
			<div id="member_ids" style="float: right;clear: right;padding: 4px 6px;background: #FFFFA0;font-size: 11px;text-align: center;display:none">
				
			</div>
			<div id="mem_fran" style="float: left;background: #fcfcfc;"></div>
		</div>
</div>

<div style="clear:left;"></div>

<input type="hidden" name="fid" id="i_fid">

<table class="datagrid nofooter" width="100%" id="prods_order" border="0">
<thead><tr><th>Sno</th><th>Product Image-PID</th><th>Product Name</th><th>MRP</th><th>Offer price / <br> DP price</th>
			<th>MemberShip Fee</th>
			<th>Qty</th>
			<th>Sub Total</th><th>Quote</th><th>Actions</th></tr></thead>
<tbody>
</tbody>
<tfoot>
    <tr>
        <td colspan="7" style="text-align:right;">Sub Total Amount :</td>
        <td><span style="font-weight: bold;" class="ttl_subtotal">0</span></td>
        <td colspan="2"></td>
    </tr>
</tfoot>
</table>

<div style="padding:5px 0px 0px 0px;">
    <div style="float:right;"><input type="submit" style="font-size:120%;border: 0px;border: 1px solid #FFF;font-size: 115%;padding: 5px 10px;border-radius: 3px;font-weight: normal;background: #68a54b;color: #FFF;cursor: pointer" id="pnh_ordeR_submit" value="Place order"  class="myButton_placeorder1"></div>
    <div><input type="button" value="Request From Franchisee" id="reqq_but" style="font-size:120%;border: 0px;border: 1px solid #FFF;font-size: 115%;padding: 5px 10px;border-radius: 3px;font-weight: normal;cursor: pointer;background: #68a54b;color: #FFF" onclick='request_quote()' class="myButton_reqfran1" style="font-size:12px;">
</div>

<div class="clear"></div>
</form>
</div>
</div>
<div id="last_confirm" style="display:none">

<div id="price_changes" style="float:right;margin-right:10px;width:800px;margin-top:-15px">
</div>

<table class="datagrid noprint">
<thead><tr><th colspan="100%">Order Confirmation</th></tr></thead>
<tbody>
<tr><td>Total Amount : </td><td>Rs <span id="final_amount"></span></td></tr>
<tr><td>Commission : </td><td>Rs <span id="final_com"></span></td></tr>
<tr><td>Amount to be deducted</td><td>Rs <span id="final_ded"></span></td></tr>
<tr><td>Current Balance : </td><td>Rs <span id="final_bal"></span></td></tr>
<tr><td>Balance After deduction : </td><td>Rs <span id="final_abal"></span></td></tr>
<tr><td><input type="button" value="Confirm" onclick='final_confirm()'></td><td><input type="button" value="Cancel" onclick='final_cancel()'></td></tr>
</tbody>
</table>
<div class="clear"></div>

</div>


</div>

</div>

<table id="template" style="display:none">
<tbody>
<tr pid="%pid%"  pimage="%pimage% %pid%" pname="%pname%" mrp="%mrp%" price="%price%" lcost="%lcost%" margin="%margin%" >
<td>%sno%</td>
<td style="text-align: center;padding:10px 0px 0px;width: 100px;background: #FFF;"><img alt="" height="100" src="<?=IMAGES_URL?>items/%pimage%.jpg"><b style="display: block;background: #f7f7f7;padding:2px;;text-align: center">%pid%</b></td>
<td><input class="pids" type="hidden" name="pid[]" value="%pid%"><span class="title"><a href="<?=site_url("admin/pnh_deal")?>/%pid%" target="_blank">%pname%</a></span>
<input type="hidden" name="menu[]" value="%menuid%" class="menuids">
<div style="margin-top: 5px;font-size: 12px;">
	<div class="p_extra"><b>Category :</b> %cat%</div>
	<div class="p_extra"><b>Brand:</b> %brand%</div>
	
	<div class="p_stk">Stock Suggestion: %stock%</div>
	<div class="p_attr">%attr%</div>
	<div class="p_attr">%confirm_stock%</div>
</div>
</td>

<td>
	<b style="font-size: 13px">%mrp%</b>
	<div class="p_extra" style="display: %dspmrp%;
font-size: 11px;
margin-top: 10px;
line-height: 19px;
padding: 10px;
font-weight: bold;
background: wheat !important;
text-align: center;width: 60px;"><b>OldMRP:</b> <span style="color: #cd0000;font-size: 13px;">%oldmrp%</span></div>
</td>
<td><span class="price">%price%</span></td>
<td>
	<span style="display:none" class="lcost"><b>%lcost%</b></span>
	Rs %margin_amt% <br><br>
	(%margin%%) <br>
	<b>Rs %lcost%</b>
	%imei_sch_disc%  
</td>
<!--<td>%src%</td>-->
<td>
	<input type="text" class="qty" pmax_ord_qty="%max_oqty%" size=2 name="qty[]" value="1">
	%max_ord_qty%
</td>
<td><span class="stotal">%lcost%</span></td>
<td><input type="text" name="quote[]" size=4></td>
<td><a href="javascript:void(0)" onclick='remove_psel(this)'>remove</a><br>
<!--<a href="<?=site_url("admin/pnh_deal")?>/%pid%" target="_blank">view</a>-->
</td>
</tr>
</tbody> 
</table>

<script>
	function remove_psel(ele)
	{
		var sel_pid = $(ele).parents("tr:first").attr('pid');
			$($(ele).parents("tr").get(0)).remove();
                remove_pid(sel_pid);
                
		$('#prods_order tbody tr').each(function(i,itm){
			$('td:first',this).text(i+1);
		});
		
	}
	
</script>
<style>

#display_fr_totals{
	display: none;
	color: #454545;
	padding: 0px 10px;
	margin-left: 10px;
	border: 1px dotted #CCC;
	font-size: 13px;
	background: #FFFFE0;
}

.p_attr, .p_attr select{
	font-size:100%;
}
.p_extra{
	font-size:10px;
}
.p_stk{
	color:maroon;
	font-weight: bold;
	padding:3px 0px;
}
#showafter{
display:none;
margin-top:10px;
background:#f1f1f1;
border:1px solid #dfdfdf;
padding:5px;
height:1200px;
}
#showafter h3{
margin:0px;
}
#franchise_det{
background:#fafafa;
padding:5px;
border:1px solid #ccc;
display:none;
margin-top:10px;
}
#franchise_det h3,#franchise_det h4{
margin:0px;
}
#franchise_det div{
padding:5px;
}
#fran_schemes,#fran_special_margins{
display:none;
position:fixed;
right:10px;
top:300px;
min-width:400px;
background:#fff;
border:1px solid #C97033;
}
#fran_schemes table{
border:0px;
}
#fran_schemes table td,#fran_schemes table th,#fran_special_margins table td,#fran_special_margins table th{
color:#000;
border:0px;
border-right:1px solid #bbb;
border-bottom:1px solid #bbb;
}
#fran_schemes table th,#fran_special_margins table th{
background:#eee;
}
#fran_schemes_table,#fran_special_margins_table{
max-height:130px;
overflow:auto;
}
    .myButton {
        
        -moz-box-shadow:inset 0px 1px 0px 0px #7a7a7a;
        -webkit-box-shadow:inset 0px 1px 0px 0px #7a7a7a;
        box-shadow:inset 0px 1px 0px 0px #7a7a7a;
        
        background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #999999), color-stop(1, #666666));
        background:-moz-linear-gradient(top, #999999 5%, #666666 100%);
        background:-webkit-linear-gradient(top, #999999 5%, #666666 100%);
        background:-o-linear-gradient(top, #999999 5%, #666666 100%);
        background:-ms-linear-gradient(top, #999999 5%, #666666 100%);
        background:linear-gradient(to bottom, #999999 5%, #666666 100%);
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#999999', endColorstr='#666666',GradientType=0);
        
        background-color:#999999;
        
        border:1px solid #787878;
        
        display:inline-block;
        color:#ffffff;
        font-family:arial;
        font-size:14px;
        font-weight:bold;
        padding:6px 12px;
        text-decoration:none;
        
    }
    .myButton:hover {
        
        background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #666666), color-stop(1, #999999));
        background:-moz-linear-gradient(top, #666666 5%, #999999 100%);
        background:-webkit-linear-gradient(top, #666666 5%, #999999 100%);
        background:-o-linear-gradient(top, #666666 5%, #999999 100%);
        background:-ms-linear-gradient(top, #666666 5%, #999999 100%);
        background:linear-gradient(to bottom, #666666 5%, #999999 100%);
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#666666', endColorstr='#999999',GradientType=0);
        
        background-color:#666666;
    }
    .myButton:active {
        position:relative;
        top:1px;
    }
    
    
    .myButton_reqfran {
        
        -moz-box-shadow:inset 0px 0px 0px 0px #9acc85;
        -webkit-box-shadow:inset 0px 0px 0px 0px #9acc85;
        box-shadow:inset 0px 0px 0px 0px #9acc85;
        
        background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #74ad5a), color-stop(1, #68a54b));
        background:-moz-linear-gradient(top, #74ad5a 5%, #68a54b 100%);
        background:-webkit-linear-gradient(top, #74ad5a 5%, #68a54b 100%);
        background:-o-linear-gradient(top, #74ad5a 5%, #68a54b 100%);
        background:-ms-linear-gradient(top, #74ad5a 5%, #68a54b 100%);
        background:linear-gradient(to bottom, #74ad5a 5%, #68a54b 100%);
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#74ad5a', endColorstr='#68a54b',GradientType=0);
        
        background-color:#74ad5a;
        
        border:1px solid #3b6e22;
        
        display:inline-block;
        color:#ffffff;
        font-family:arial;
        font-size:13px;
        font-weight:bold;
        padding:6px 19px;
        text-decoration:none;
        
    }
    .myButton_reqfran:hover {
        
        background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #68a54b), color-stop(1, #74ad5a));
        background:-moz-linear-gradient(top, #68a54b 5%, #74ad5a 100%);
        background:-webkit-linear-gradient(top, #68a54b 5%, #74ad5a 100%);
        background:-o-linear-gradient(top, #68a54b 5%, #74ad5a 100%);
        background:-ms-linear-gradient(top, #68a54b 5%, #74ad5a 100%);
        background:linear-gradient(to bottom, #68a54b 5%, #74ad5a 100%);
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#68a54b', endColorstr='#74ad5a',GradientType=0);
        
        background-color:#68a54b;
    }
    .myButton_reqfran:active {
        position:relative;
        top:1px;
    }
    
    
    .myButton_placeorder {
        
       background:none repeat scroll 0 0 rgb(107, 120, 180);
      
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#fe1a00', endColorstr='#ce0100',GradientType=0);
        
        background-color:none repeat scroll 0 0 rgb(107, 120, 180);
        display:inline-block;
        color:#ffffff;
        font-family:arial;
        font-size:9px;
        font-weight:bold;
        padding:4px 24px;
        text-decoration:none;
    }
    .myButton_placeorder:hover {
        background:none repeat scroll 0 0 rgb(107, 120, 180);;
        filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ce0100', endColorstr='#fe1a00',GradientType=0);
        background-color:none repeat scroll 0 0 rgb(107, 120, 180);
    }
    .myButton_placeorder:active {
        position:relative;
        top:1px;
    }
    
</style>
<?php /*?>
<div id="fran_schemes">
<a href="javascript:void(0);" onclick='$("#fran_schemes").hide()' style="float:right;display:block;padding:3px;color:#fff;font-weight:bold;">X</a>
<h4 style="background:#C97033;padding:5px;color:#fff;margin:0px;">Scheme Discounts running</h4>
<div id="fran_schemes_table">
</div>
</div>
<?php */ ?>
<?php /*?>
<div id="fran_special_margins">
<a href="javascript:void(0);" onclick='$("#fran_special_margins").hide()' style="float:right;display:block;padding:3px;color:#fff;font-weight:bold;">X</a>
<h4 style="background:#C97033;padding:5px;color:#fff;margin:0px;">Special Margins running</h4>
<div id="fran_special_margins_table">
<table class="datagrid noprint smallheader">
<thead><tr><th>Deal</th><th>Mrp</th><th>Offer Price</th><th>Special Margin</th><th>Landing Cost</th></tr></thead>
<tbody>
<?php
		$sm=$this->db->query("select i.orgprice,i.price,s.from,s.to,i.pnh_id,i.name,i.id,s.special_margin from king_dealitems i join pnh_special_margin_deals s on s.itemid=i.id where ? between s.from and s.to order by name asc",time())->result_array();?>
<?php foreach($sm as $d){?>
<tr style="cursor:pointer;" onclick='trig_loadpnh("<?=$d['pnh_id']?>")'>
<td><a href="<?=site_url("admin/pnh_deal/{$d['id']}")?>"></a><?=$d['name']?></td>
<td>Rs <?=$d['orgprice']?></td>
<td>Rs <?=$d['price']?></td>
<td><?=$d['special_margin']?>%</td>
<td>Rs <?=round($d['price']-($d['price']/100*$d['special_margin']),2)?></td>
</tr>
<?php }?>
</tbody>
</table>
</div>
</div>
<?php */ ?>

<form id="quote_form" action="<?=site_url("admin/pnh_place_quote")?>" method="post">
</form>

<script>

$('#mid_entrytype').change(function(){
	$('input[name="mid"]').val("");
	if($(this).val()==0)
	{
		$('.mid_blk').show();
		$('#mem_fran').html('');
	}
	else
	{
		$('.mid_blk').hide();
		$('#mem_fran').html('');
	}
});
function get_pnh_franchises(){
	var f_sel_html = '<option value=""></option>';
	$.getJSON(site_url+'/admin/jx_get_franchiselist','',function(resp){
		if(resp.status == 'error'){
			alert(resp.message);
		}else{
			$.each(resp.f_list,function(a,item){
				f_sel_html+='<option value="'+item.pnh_franchise_id+'">'+item.franchise_name+'</option>';	
			});
		}
		$('select[name="sel_fid"]').html(f_sel_html);
		$('select[name="sel_fid"]').trigger("liszt:updated");
		
	});
	
}
$(function(){
	$('#fid_inp').change(function(){
		$('select[name="sel_fid"]').val($(this).val()).trigger("liszt:updated");
	}).inlineclick({onlySelect:false});
	get_pnh_franchises();

	$('select[name="sel_fid"]').chosen().change(function(){
		$('#fid_inp').val($(this).val());

		load_franchisebyid();
		
		
	});
});
</script>

<div id="req_quote_dlg" title="Franchise Request/Quotes ">
	<form id='fr_req_quote_frm' method="post" action="<?=site_url('admin/pnh_place_quote') ?>">
		<input type="hidden" name="fid" value="0" >
		<div>
			<h4 style="margin:5px 0px !important;" id="req_frname"></h4>
			<div id="req_prodlist" style="margin: 5px 0px;">
				<table width="100%" cellpadding="3" cellspacing="0" class="datagrid">
					<thead>
						<tr>
							<th><b>Slno</b></th>
							<th><b>PID</b></th>
							<th><b>Deal</b></th>
							<th><b>MRP</b></th>
							<th><b>Offer</b></th>
							<th><b>Landing</b></th>
							<th><b>Qty</b></th>
							<th><b>Quote</b></th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
			<span style="float:right;" class="dash_bar_right"><b>Add New Product:</b><input type="checkbox" name="add_newprod" id="add_newprod"></span>
			<br><br><br>
			<div id="fran_reqlist" style="margin: 5px 0px;">
				<table width="100%" cellpadding="3" cellspacing="0" class="datagrid">
					<thead>
						<tr>
							<th><b>Product Name</b></th>
							<th><b>Price Range</b></th>
							<th><b>Qty</b></th>
							<th><b>Quote</b></th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						
					</tbody>
				</table>
			</div>
			
			<div style="margin:5px 0px;">
				<b>Respond To Request In:</b><br />
				<select name="req_respond_time" >
					<option value="5">5 Min</option>
					<option value="10">10 Min</option>
					<option value="15">15 Min</option>
					<option value="30">30 Min</option>
					<option value="45">45 Min</option>
					<option value="60">1 Hour</option>
				</select>
			</div>
			<div style="margin:5px 0px;">
				<b>Have you asked for options, so we can fulfill franchise needs ?</b>
						: <input type="checkbox" value="1" name="cnfrm_req_opts" /> 
			</div>
			
			<div style="margin:5px 0px;">
				<b>Remarks/notes on request</b><br />
				 <textarea name="req_remark" style="width: 98%;height: 80px;"></textarea>
			</div>
		</div>
		<input type="hidden" name="product_name" id="product_name">
		<input type="hidden" name="mrp" id="mrp">
	</form>
	
</div>

<div id="reg_mem_dlg" title="Instant Member Registration">
	<form id="reg_mem_frm" action="<?php echo site_url('admin/jx_reg_newmem')?>" method="post">
		<input type="hidden" name="franchise_id" value="" id="memreg_fid">
		<table>
			<tr><td>Member Name</td><td>:<span class="red_star">*</span></td><td><input type="text" name="memreg_name" id="memreg_name" ></td></tr>
			<tr><td>Mobile Number</td><td>:<span class="red_star">*</span></td><td><input type="text" name="memreg_mobno" id="memreg_mobno" data-required="true" maxlength="10"></td></tr>
		</table>
	</form>
</div>

<script>

$('#req_quote_dlg').dialog({
								autoOpen:false,
								width:1000,
								modal:true,
								height:'auto',
								open:function(){
									
										$('#fran_reqlist thead').hide();
										 $('#fran_reqlist tbody').hide();
										 $('#fran_reqlist tfoot').hide();
										
									
									$('input[name="cnfrm_req_opts"]').attr('checked',false);
									
									$('#req_frname').html($('#fran_name_disp a').text());
									
									$('input[name="fid"]',this).val($('#i_fid').val());
									
									var req_plist = '';
										if($('#prods_order tbody tr').length)
										{
											$('#req_prodlist').show();
											// prepare selected data for display in request quote form
											$('#prods_order tbody tr').each(function(j,itm){
												req_plist += '<tr>';
												req_plist += '	<td>'+(j+1)+'</td>';
												req_plist += '	<td><input type="hidden" size="4" name="pid[]" value="'+$('input[name="pid[]"]',this).val()+'">'+$(this).attr('pid')+'</td>';
												req_plist += '	<td style="display:none"><input type="hidden" size="4" name="menuid[]" value="'+$('input[name="menuid[]"]',this).val()+'">'+$(this).attr('menuid')+'</td>';
												req_plist += '	<td>'+$(this).attr('pname')+'</td>';
												req_plist += '	<td>'+$(this).attr('mrp')+'</td>';
												req_plist += '	<td>'+$(this).attr('price')+'</td>';
												req_plist += '	<td><span style="background-color:#89c403;">'+format_number($(this).attr('lcost'))+'</span></td>';
												req_plist += '	<td><input type="text" size="4" name="qty[]" value="'+$('input[name="qty[]"]',this).val()+'"></td>';
												req_plist += '	<td><input type="text" size="4" name="quote[]" value="'+$('input[name="quote[]"]',this).val()+'"></td>';
												req_plist += '	<td><a href="javascript:void(0)" class="remove_btn"><b>[X]</b></a> </td>';
												req_plist += '</tr>';
											});
										}else
										{
											$('#req_prodlist').hide();		
										}
										
										$('#req_prodlist tbody').html(req_plist);
										$('#add_newprod').change(function(){
											var $check = $(this),
									        $div = $check.parent();
										 if ($check.prop('checked')){
											var fran_req = '';
												fran_req+='<div><tbody><tr><td><input type="text" name="produc_name[]" class="required input" size="35px"></td><td><input type="text" name="np_mrp[]" size="20px" class="required input"></td><td><input type="text" name="np_qty[]" size="20px" class="required input"></td><td><input type="text" name="np_quote[]" size="20px" class="required input"></td><td><input type="button" class="add_tblrow" value="+"></td></tr></tbody></div>';
												 $('#fran_reqlist thead').show()
												 $('#fran_reqlist tbody').html(fran_req);
												 $('#fran_reqlist tbody').show();
												 $('#req_prodlist thead').show();
											 }
										 else
									 			{
												 $('#fran_reqlist thead').hide();
												 $('#fran_reqlist tbody').hide();
												 $('#fran_reqlist tfoot').hide();
										 		}
										});
								},
								close:function(){
									$("#reqq_but").attr("disabled",false);
								},
								buttons:{
									'Place Request':function(){

										// Check if atleast one product request
										
										var req_d_ttl = $('#req_prodlist input[name="quote[]"]').length;
										var req_p_ttl = $('#fran_reqlist input[name="np_quote[]"]').length;
										
										if((req_d_ttl+req_p_ttl) == 0)
										{
											alert("Please enter atleast one product in request list to proceed");
											return false;
										}

										if ($(this).find('input[name="add_newprod"]:checked').length != 0) {
											 if($(".required input").val() == '') {
									                alert("Please fill in all the required fields (indicated by *)");
									                $(".required").addClass('error_msg_text');
									                
									                return false;
									            }
										   //   return false;
										}
										
										if(!$('input[name="cnfrm_req_opts"]').attr('checked'))
										{
											alert("Please check if you have asked for options, so we can fulfill franchise needs ?")
										}else
										{
											var stat = 1;
											var req_remark = $.trim($('textarea[name="req_remark"]').val()); 
												if(!req_remark.length)
													if(!confirm("Are you sure want to proceed with out placing remarks ?"))
														stat = 0;
											if(stat)
											{
												$.post("<?=site_url("admin/pnh_place_quote")?>",$("#fr_req_quote_frm").serialize(),function(data){
													location=data;
												});	
													
											}	
												
										}
									},
									'Cancel':function(){
										$("#reqq_but").attr("disabled",false);
										 $(this).dialog('close');
									}	
								}
						});

var pids=[];

/*function requst_frmfran()
{
	var x;
	var r=confirm("Are You sure there is no requsted product in search list");
	if(r==true)
	{
	 product_name=prompt("Enter Product name");
	 price=prompt("Enter Price");
	 if(product_name.length==0)
		 return;
	 $('#product_name').val(product_name);
	 $('#mrp').val(price);
	 $('#fr_req_quote_frm').submit();
	}else
	{
		return false;
	}
}*/

function request_quote()
{
	$("#reqq_but").attr("disabled",true); 
	$('#req_quote_dlg').dialog('open');
}

$('.remove_btn').live('click',function(){
	if(confirm("Are you sure want to remove from the request list ? "))
		$(this).parent().parent().remove();
});

$(".add_tblrow").live('click',function(){
    var tmpl = $(this).parent().parent().html();
   	 	$(this).parent().parent().parent().append('<tr>'+tmpl+'</tr>');
   	 $(this).addClass('remove_tblrow').removeClass('add_tblrow').val('-');
  });

$(".remove_tblrow").live('click',function(){
	$(this).parent().parent().remove();
  });

function show_schemes()
{
	fid=$("#i_fid").val();
	$.post("<?=site_url("admin/pnh_jx_show_schemes")?>",{fid:fid},function(data){
		$("#fran_schemes_table").html(data);
		$("#fran_schemes").show();
		
	});
}

var is_new_fran = 0;

function select_fran(fid)
{
	$('#display_fr_totals').hide();
	$.post("<?=site_url("admin/pnh_jx_getfranbalance")?>",{id:fid},function(data){
		o=$.parseJSON(data);
		credit=parseInt(o.credit);
		balance= format_number(o.balance);
		if(balance < 5000)
			$("#fran_balance").addClass('warning');
		else
			$("#fran_balance").removeClass('warning');
			
		$("#fran_balance").text(balance);
		$("#fran_credit").text(credit);

		$('#display_fr_totals .total_ord').text(o.total_ord);
		$('#display_fr_totals .last_ord').text(o.last_ordon);
		$('#display_fr_totals .total_mem').text(o.total_mem);
		$('#display_fr_totals').show();
		var fran_status=[];
		fran_status[2]='Payment Suspension';
		fran_status[3]='Tempervary Suspension';
		var fran_type_msg = '';
		var fran_sus_msg = '';
		if(o.reg_days <=30)
		{
			is_new_fran = 1;
			if(is_new_fran)
				fran_type_msg = '<span class="blinker" style="background-color:#cd0000;color:#FFF;font-size:11px;padding:2px 10px">Newbie</span>';
		}
		if(o.is_suspended >=2 )
		{
			var f=fran_status[o.is_suspended];
			fran_type_msg = '<span class="blinker" style="background-color:#cd0000;color:#FFF;font-size:11px;padding:2px 10px">'+f+'</span>';
			fran_sus_msg = '<div style="float:right;font-size:11px;color:red;margin-right:-125px;margin-top:-1px">Batch will be disabled for '+o.reason+'</div>';
		}
                var courier_msg='';
                if(o.courier === false) courier_msg='Not set';
                else {
                    courier_msg += '<span class="">1. <b>'+o.courier['c1']['courier_name']+'</b> - ';
                    courier_msg += " / <b>"+o.courier['c1']['delivery_hours_1']+" Hrs</b> - ";
                    courier_msg += " / Deliver <b>"+get_delivery_type_msg(o.courier['c1']['delivery_type_priority1'])+"</b></span>";
                    
                    courier_msg +='<span class="">2. ';
                    if(o.courier['c2'] === false) { courier_msg +='Not set'; } else {
                        courier_msg += '<b>'+o.courier['c2']['courier_name']+'</b>';
                        courier_msg += " - <b>"+o.courier['c2']['delivery_hours_2']+" Hrs</b>";
                        courier_msg += " / Deliver <b>"+get_delivery_type_msg(o.courier['c2']['delivery_type_priority2'])+"</b>";
                    }
                    courier_msg +=  "</span>";
                    
                    courier_msg +='<span class="">3. ';
                    if(o.courier['c3'] === false) { courier_msg +='Not set'; } else {
                        courier_msg += '<b>'+o.courier['c3']['courier_name']+'</b>';
                        courier_msg += " / <b>"+o.courier['c3']['delivery_hours_3']+" Hrs</b>";
                        courier_msg += " / Deliver <b>"+get_delivery_type_msg(o.courier['c3']['delivery_type_priority3'])+"</b>";
                    }
                    courier_msg +=  "</span>";
                }
		$("#fran_name_disp").html(" for '<a href='<?=site_url("admin/pnh_franchise")?>/"+$("#i_fid").val()+"' target='_blank'>"+$("#franchise_det h4 a").text()+' '+$("#franchise_det h4 a").attr('loc')+"</a>' - Mobile: "+$(".ff_mob").text()+' '+fran_type_msg+'<br>'+fran_sus_msg);
                $(".offn_courier_detail").html('Courier Priorities: '+courier_msg);
		
		
		if(is_new_fran)
			$('#newbie_confirm_dlg').show();
		else
			$('#newbie_confirm_dlg').hide();
		
				
	});
	$("#i_fid").val(fid);
	$("#fran_select_cont,#auth_cont,#frans_list,#franchise_det").hide();
	$("#showafter").show();
	$("#p_pid").focus();
	
	
	show_schemes();
	show_memids(fid);
	$("#fran_special_margins").show();
	
	$('#hd').hide();
	
	
}
function get_delivery_type_msg(delivery_type) { var type_msg='';
        if(delivery_type =='') {type_msg += "Not Set";} 
        else if(delivery_type == 0)  {type_msg += "By Hand";} 
        else if(delivery_type == 1) { type_msg += "To Door";}
        else { type_msg += "--";}
        return type_msg;
}
function show_memids(frid)
{
	$("#member_ids").html("Loading...");
	$.post("<?=site_url("admin/pnh_jx_loadmemids")?>",{fid:frid},function(resp){
		if(resp.status == 'success')
		{
			//$("#member_ids").html('<b>Allotted Member Ids:</b> <div>'+resp.mem_range.mid_start+'-'+resp.mem_range.mid_end+'</div>');
			$("#member_ids").html('');
		}else
		{
			$("#member_ids").html(resp.error);
		}
	},'json');
}
function remove_pid(pid)
{
	var t_pids=pids;
	pids=[];
	for(i=0;i<t_pids.length;i++)
		if(pid!=t_pids[i])
			pids.push(t_pids[i]);
                    
        change_total_subtotal(); 
}

function load_franchisebyid()
{
	fid=$("#fid_inp").val();
	if(fid.length==0)
	{
		alert("Enter fid");return;
	}
	$.post("<?=site_url("admin/pnh_jx_loadfranchisebyid")?>",{fid:fid},function(data){
		$("#franchise_det").html(data).show();
	});
}
function load_franchisebymobile()
{
	fmobile=$("#fmobile_inp").val();
	if(fmobile.length==0)
	{
		alert("Enter login mobile");return;
	}
	$.post("<?=site_url("admin/pnh_jx_loadfranchisebymobile")?>",{mobile:fmobile},function(data){
		$("#franchise_det").html(data).show();
	});
}

function mem_reg(fid)
{
	
	$('#reg_mem_dlg').dialog('open');
}
$('#reg_mem_dlg').dialog({
			autoOpen:false,
			width:300,
			modal:true,
			height:'auto',
			open:function(){
				var dlg=$(this);
				var fid=$("#fid_inp").val();
					$('#reg_mem_frm input[name="franchise_id"]',this).val(fid)
				},
				buttons:{
					'Register':function(){
						$(this)
						var error_list = new Array();
						// register member 
						var mem_regname = $.trim($('input[name="memreg_name"]').val());
						var mem_mobno = $.trim($('input[name="memreg_mobno"]').val());
                        if(mem_regname.length == 0)
                                error_list.push("Please Enter Member name.");
						
                        if(mem_mobno.length == 0)
                                error_list.push("Please Enter Mobile Number.");
                        else
                        {
                                mem_mobno = mem_mobno*1	
                                if(isNaN(mem_mobno))
                                        error_list.push("Please Enter valid Mobile number.");	
                        }	

                        if(error_list.length)
                        {
                                alert(error_list.join("\r\n"));
                        }else
                        {
                                $.post(site_url+'/admin/jx_reg_newmem',$('#reg_mem_frm').serialize(),function(resp){
                                        if(resp.status == 'success')
                                        {
                                                $('input[name="mid"]').val(resp.mid);
                                                $('#mid_entrytype').val(0);
                                                $('.mid').trigger('change');
                                                $('#reg_mem_dlg').dialog('close');
                                        }else
                                        {
                                                $('input[name="mid"]').val('');
                                                alert(resp.error);
                                        }
                                },'json');
                        }
					},
					'Cancel':function(){
						$(this).dialog('close');
					}
				}
});

var balance=0,credit=0;
var goodtogo=0;

function final_cancel()
{
	goodtogo=0;
	$("#order_form").show();
	$("#last_confirm").hide();
	$('#newbie_confirm_dlg').removeClass('bottomleft_newbieblk');
}

function final_confirm()
{
	goodtogo=1;
	$("#redeem_r").attr("checked",$("#redeem_cont").attr("checked"));
	if($(".redeem_points").val()>150)
	{
		alert("Max redeemable points is 150");
		return false
	}
	if($(".price_c_com").length==1 && !$(".price_c_com").attr("checked"))
	{
		alert("Is price change communicated to franchise?");
		return false;
	}
	$("#redeem_p").val($(".redeem_points").val());
	$("#order_form").submit();
	$("#pnh_order_submit").attr("disabled",true);
}

var jHR=0,search_timer;

$(function(){

	$("#p_pid").keydown(function(e){
		if(e.which==13)
		{
			$(".add_product").click();
			e.preventDefault();
			e.stopPropagation();
			return false;
		}
		return true;
	});

	$("#p_barcode").keydown(function(e){
		if(e.which==13)
		{
			$(".add_b_product").click();
			e.preventDefault();
			e.stopPropagation();
			return false;
		}
		return true;
	}).focus(function(){
		$(this).val("");
	});

	$("#order_form").submit(function(){
		if($('#fran_name_disp .blinker').length)
		{
			var is_all_newbie_cond_ticked = 1;
				$('.nb_cond:visible').each(function(){
					if(!$(this).attr('checked'))
						is_all_newbie_cond_ticked = 0;
				});
			
			if(!is_all_newbie_cond_ticked)
			{
				alert("Please confirm all points from New franchisee notes before proceeding to Place order.");
				return false;
			}
				
		}
		
		if($('input[name="m_name"]').length)
		{
			$('input[name="m_name"]').val($.trim($('input[name="m_name"]').val()));
			if(!$('input[name="m_name"]').val())
			{
				alert("Please enter member name");
				return false;
			}
		}
		
		if($('input[name="m_mobile"]').length)
		{
			$('input[name="m_mobile"]').val($.trim($('input[name="m_mobile"]').val()));
			if(!$('input[name="m_mobile"]').val())
			{
				alert("Please enter member mobile no");
				return false;
			}
		}
			
		if(goodtogo==1)
			return true;

		
		var mid_type = $('#mid_entrytype').val();
		var mid_length=$("input[name='mid']",$(this)).val().length;		
		
		if(mid_type==0 && mid_length==0)
		{
			alert("Enter MID");
			return false;
		}
	
		if(pids.length==0)
		{alert("There are no products in the order");return false;}
		
		total=0;
		ppids=[];
		qty=[];
		menuids=[];
		
		$("#prods_order .stotal").each(function(){
			total+= format_number( $(this).html() );
		});
		
		$("#prods_order .pids").each(function(){
			ppids.push($(this).val());
		});
		$("#prods_order .menuids").each(function(){
			menuids.push($(this).val());
		});
		
		$("#prods_order .qty").each(function(){
			qty.push($(this).val());
		});
		var menu_qty=qty;
		var menuid=menuids;
		var mid = $("input[name='mid']",$(this)).val().length;
		
		for (var i = 0; i < menuids.length; i++)
		 {
			var menu_id=menuids[i];
			var menu_qty=qty[i];
			if(menu_qty>1 && mid!=0 && menu_id  == 112)
			{
				alert("More than 1 qty of Electronics Item for 1 member can't be processed");
				return false;
			}

			if(mid==0 && menu_id != 112)
			{
				if(confirm("Instant Registration is required because Other than Mobile & Tablet items are there in the Cart"))
					 mem_reg();
				 return false;
			}
		 }
		
		var stk_confirm_prods = $('input[name="confirm_stock"]').length;
		var stk_confirm_prods_checked = $('input[name="confirm_stock"]:checked').length;
		
		if(stk_confirm_prods != stk_confirm_prods_checked && stk_confirm_prods > 0)
		{
			alert("Please verify whether stock for the footwear is available?");
			return false;
		}
			
		
		if(confirm("Total order value : Rs "+total+"\nAre you sure want to place the order?"))
		{
			
			$("#order_form").hide();
			attr=$(".attr").serialize();
			
			$.post("<?=site_url("admin/pnh_jx_checkstock_order")?>",{attr:attr,pids:ppids.join(","),qty:qty.join(","),fid:$('#i_fid').val(),mid:$("input[name='mid']",$(this)).val()},function(data){
				obj=$.parseJSON(data);
				if(obj.e==0)
				{
					goodtogo=1;
					$("#final_amount").text( format_number(obj.total) );
					$("#final_ded").text(format_number(obj.d_total));
					$("#final_com").text( format_number(obj.com) );
					$("#final_bal").text( format_number(obj.bal) );
					$("#final_abal").text( format_number(obj.abal) );
					$("#price_changes").html(obj.pc);
					$("#last_confirm").show();
					
					$('#newbie_confirm_dlg').addClass('bottomleft_newbieblk');
					//$('#prod_suggest_list').hide();
				}
				else
				{
					$('#newbie_confirm_dlg').removeClass('bottomleft_newbieblk');
					
					$("#order_form").show();
					alert("ERROR!\n"+obj.msg);
					return false;
				}
			});
		}
		return false;
	});
        
        
	$("#prods_order .qty").live("change",function(){
		p=$(this).parents("tr").get(0);
		
		var qty_e = $(".qty",p).val()*1;
		
			if(isNaN(qty_e*1))
			{
				alert("Invalid Qty Entered.");
				qty_e = 0;
			}else if(qty_e*1 <= 0)
			{
				alert("Invalid Qty Entered,Please enter atleast one quantity.");
				qty_e = 0;
			}
		
		var qty_m = $(".qty",p).attr('pmax_ord_qty')*1;
		
			if(qty_e > qty_m)
			{
				alert("Maximum "+qty_m+" Qty can be Ordered ");
				qty_e = qty_m;
			}
			
			if(qty_e == 0 && qty_e > 0)
			{
				qty_e = 1;
			}
			
			$(".qty",p).val(qty_e);
		
		//$(".stotal",p).html(parseInt($(".price",p).html())*parseInt($(".qty",p).val()));
		//$(".landing_cost",p).html(parseInt($(".lcost",p).html())*parseInt($(".qty",p).val()));
                var sub_total = parseFloat( $(".lcost",p).text())*parseInt($(".qty",p).val() ) ;
		$(".stotal",p).html( format_number( sub_total ) );
                change_total_subtotal();
	});
        
	$("#p_srch").keyup(function(){
		fid=$("#fid_inp").val();
		q=$(this).val();
		if(q.length<3)
			return true;
		if(jHR!=0)
			jHR.abort();
		window.clearTimeout(search_timer);
		search_timer=window.setTimeout(function(){
			$('.loading_srch').remove();
			$('#srch_results').html('<div class="loading_srch" style="display:block;text-align:center;background:#FFF"><img src="'+base_url+'/images/jx_loading.gif'+'"></div>');
		jHR=$.post('<?=site_url("admin/pnh_jx_searchdeals")?>',{fid:$("#i_fid").val(),q:q},function(data){
			$("#srch_results").html(data).show();
			//$("#srch_results").css("margin-top","-"+($("#srch_results").height()+30)+"px");
			$('.loading_srch').remove();
		});},200);
	});

	
	$(".add_b_product").click(function(){
		fid=$("#fid_inp").val();
		barcode=$("#p_barcode").val();
		$("#p_barcode").attr("disabled",true);
		$.post("<?=site_url("admin/pnh_jx_loadpnhprodbybarcode")?>",{fid:$("#i_fid").val(),barcode:barcode},function(data){
			$("#p_barcode").attr("disabled",false);
			obj=p=$.parseJSON(data);
			if(obj.pid==0)
			{alert("Product not found by barcode");return;}
			$("#p_barcode").attr("disabled",false).val("");
			$("#p_pid").val(obj.pid);
			$(".add_product").click();
		});
		
	});
	
	
	$(".add_product").click(function(){
		
		pid=$("#p_pid").val();
		if($.inArray(pid,pids)!=-1)
		{
			alert("Product already added");return;
		}
		if(pid.length==0)
		{alert("Enter product id");return;}
		$("#p_pid").attr("disabled",true);
		
		$(this).val('Loading...');
		
		$.post("<?=site_url("admin/pnh_jx_loadpnhprod")?>",{pid:pid,fid:$("#i_fid").val()},function(data){
			i=pids.length;
			obj=p=$.parseJSON(data);
			$("#p_pid").attr("disabled",false);
			$(".add_product").val('Add');
			if(obj.length==0)
			{	alert("The product is DISABLED \nor\nNo product available for given id");return;}
			
			if(obj.error != undefined)
			{
				alert(obj.error);
				return ;
			}
			
			//show_prod_suggestion(p.pid);
			load_frans_cancelledorders(pid);
			if(p.live==0)
			{	alert("The product is out of stock or not sourceable");return false; }
			$("#p_pid").val("");
			template=$("#template tbody").html();
			template=template.replace(/%sno%/g,($("#prods_order tbody tr").length+1));
			template=template.replace(/%pimage%/g,p.pic);
			template=template.replace(/%pid%/g,p.pid);
			template=template.replace(/%menuid%/g,p.menuid);
			template=template.replace(/%attr%/g,p.attr);
			template=template.replace(/%pname%/g,p.name);
			template=template.replace(/%cat%/g,p.cat);
			template=template.replace(/%brand%/g,p.brand);
			template=template.replace(/%margin%/g,p.margin);
			if(p.oldmrp == '-')
				template=template.replace(/%dspmrp%/g,'none');
			else
				template=template.replace(/%dspmrp%/g,'block');
			
			template=template.replace(/%oldmrp%/g,p.oldmrp);
			template=template.replace(/%newmrp%/g,p.mrp);
			template=template.replace(/%mrp%/g,p.mrp);
			template=template.replace(/%price%/g,p.price);
			template=template.replace(/%lcost%/g,p.lcost);
			template=template.replace(/%stock%/g,p.stock);
			template=template.replace(/%confirm_stock%/g,p.confirm_stock);
			template=template.replace(/%margin_amt%/g,Math.ceil(p.price-p.lcost));
			
			//template=template.replace(/%lcost%/g,p.imei_actv_schem);
			
			if(p.max_allowed_qty*1 == 0)
			{
				template=template.replace(/%max_oqty%/g,500);
				template=template.replace(/%max_ord_qty%/g,"");
			}else
			{
				template=template.replace(/%max_oqty%/g,p.max_ord_qty);
				template=template.replace(/%max_ord_qty%/g,"<br><span style='font-size:10px;color:#cd0000'>Allowed: <br>("+p.max_ord_qty+" Qty)</span>");
			}
			
			if(p.imei_disc*1 != 0)
			{
				template=template.replace(/%imei_sch_disc%/g,"<br><br><span style='font-size:11px;'>IMEI Scheme: <br>("+p.imei_disc+") </span> ");
			}else
			{
				template=template.replace(/%imei_sch_disc%/g,"");
			}
			
//			template=template.replace(/%src%/g,p.src);
			template=template.replace(/%mrp%/g,p.mrp);
			$("#prods_order tbody").append(template);
			pids.push(p.pid);
			
			show_prev_orderd();
                        
                        change_total_subtotal();
                        
			show_prevorderd_unshipped();
			
		});
	});

	


	if(location.hash.length!=0)
	{
		fid=location.hash.slice(1);
		$("#fid_inp").val(fid);
		load_franchisebyid(fid);
		show_memids(fid);
		load_scheme_details();
	}
	
});

$(".mid").change(function(){
		$.post("<?=site_url("admin/jx_pnh_getmid")?>",{mid:$(this).val(),more:1},function(data){
			$("#mem_fran").html(data).show();
		});
	});


function change_total_subtotal() {
    var ttl_subtotal=0;
    if($("#prods_order .stotal").html() == '') {
        ttl_subtotal=0;
    }
    else {
        $("#prods_order .stotal").each(function(){
                ttl_subtotal += format_number($(this).html());
        });
    }
    $(".ttl_subtotal").html(ttl_subtotal);
}

function trig_loadpnh(pid)
{
	$("#p_pid").val(pid);
	$(".add_product").click();
}

/*function show_prod_suggestion(pid)
{
	$.post("<?//=site_url("admin/jx_pnh_prod_suggestion")?>",{pid:pid,fid:$('#i_fid').val()},function(data){
		$("#prod_suggest").html(data);
	});
}*/

function show_prev_orderd()
{
	ppids=[];
	
	$("#prods_order .pids").each(function(){
		ppids.push($(this).val());
	
	});
	
	if(!ppids.length)
		return false;
	
	$("#prevorderd_prod tbody").html("");
	
	$.post(site_url+"/admin/jx_to_load_productdata",{pids:ppids.join(","),fid:$("#i_fid").val()},function(result){
		if(result.status=='error')
		{
			//alert('no data found');
		}
		else
		{
			if(result.ttl_orderd !=  undefined)
			{
				
				$.each(result.ttl_orderd,function(k,v){
					var prev_orderdrow =
										"<tr>"
										+"<td>"+v.name+"</td>"
										+"<td>"+v.ttl_orders+"</td>"
										+"<td>"+v.ttl_shipped_orders+"</td>";
										+"</tr>";
				 	$(prev_orderdrow).appendTo("#prevorderd_prod tbody");
				});
			}
		}
	},'json');

	

}

function show_prevorderd_unshipped()
{
	
	$("#prevorderd_unshipped tbody").html("");
	
	$.post(site_url+"/admin/jx_pnh_ord_prod_unshipped",{fid:$("#i_fid").val()},function(result){
		if(result.status=='error')
		{
			//alert('no data found');
		}
		else
		{
				$.each(result.unship_det,function(k,v){
				var prevorderd_unshippedrow=
				 "<tr>"
				  +"<td><a  href='"+site_url+'/admin/trans/'+v.transid+"' 'target'='_blank'>"+v.transid+"</a></td>"
				  +"<td>"+v.orderdon+"</td>"
				 +"</tr>";
				  $(prevorderd_unshippedrow).appendTo("#prevorderd_unshipped tbody");
			});
			
		}
	},'json');

	
}

function add_deal_callb(name,pid,mrp,price,store_price)
{
	$('#srch_results').html('').hide();
	
	$("#p_srch").val("").focus();
	$("#p_pid").val(pid);
	$(".add_product").click();
	
}

$('#p_srch').mouseover(function(){
	if($(this).val().length)
		$('#srch_results').show();
	else
		$('#srch_results').html('').hide();
}).focus(function(){
	$('#srch_results').show();
});

$('#srch_results').mouseleave(function(){
	$('#srch_results').hide(); 
});

$('#prod_suggest_list').tabs();

function load_frans_cancelledorders(pid)
{
	$.post("<?=site_url("admin/jx_pnh_fran_cancelledorders")?>",{pid:pid,fid:$("#i_fid").val()},function(data){
		$("#cancel_prod_suggest_list").html(data);
	});
}
function load_scheme_details()
{
	$("#show_scheme_details").dialog('open');
}

$("#show_scheme_details").dialog({
	model:true,
	autoOpen:false,
	width:'800',
	height:'500',
	open:function(){
		dlg = $(this);
		$('#active_scheme_discount tbody').html("");
		$('#active_super_scheme tbody').html("");
		$('#active_imei_scheme tbody').html("");
		$('#menu tbody').html("");
		fid=$("#i_fid").val();
		$.post("<?=site_url("admin/pnh_jx_load_scheme_details")?>",{fid:fid},function(result){
			/*if(result.status=='error')
			{
				//alert('No Schemes applied');
			//	dlg.dialog('close');
			}
			else
			{*/
				 
				if(result.active_schdisc != undefined)
				{
					$.each(result.active_schdisc,function(k,v){
						if(v.brand_name == undefined)
						{v.brand_name='All brands'; }
						if(v.cat_name== undefined)
						{v.cat_name='All categories';}
						 var activesch_row =
							 "<tr>"
							  +"<td>"+v.menu_name+"</td>"
							  +"<td>"+v.brand_name+"</td>"
							  +"<td>"+v.cat_name+"</td>"
							  +"<td>"+v.discount+"</td>"
							  +"<td>"+v.validfrom+"</td>"
							  +"<td>"+v.validto+"</td>"
							  +"</tr>";
							  $("#active_scheme_discount tbody").append(activesch_row);
					});
				}

				  
				if(result.active_supersch != undefined)
				{
					$.each(result.active_supersch,function(k,v){
						if(v.brand_name == undefined)
						{v.brand_name='All brands'; }
						if(v.cat_name == undefined)
						{v.cat_name='All categories';}
						 var supersch_row =
							 "<tr>"
							  +"<td>"+v.menu_name+"</td>"
							  +"<td>"+v.brand_name+"</td>"
							  +"<td>"+v.cat_name+"</td>"
							  +"<td>"+v.target_value+"</td>"
							  +"<td>"+v.credit_prc+"</td>"
							  +"<td>"+v.validfrom+"</td>"
							  +"<td>"+v.validto+"</td>"
							  +"<td></td>"
							  +"</tr>";
							  $("#active_super_scheme tbody").append(supersch_row);
							
					});
				}

				if(result.active_imeischeme != undefined)
				{
					$.each(result.active_imeischeme,function(k,v){
						if(v.brand_name == undefined)
						{v.brand_name='All brands'; }
						if(v.cat_name == undefined)
						{v.cat_name='All categories';}
						if(v.scheme_type == 1)
						{v.scheme_type='Percentage'; }
						if(v.scheme_type == 0)
						{v.scheme_type='Fixed Value'; }
						 var imeisch_row =
							 "<tr>"
							  +"<td>"+v.menu_name+"</td>"
							  +"<td>"+v.brand_name+"</td>"
							  +"<td>"+v.cat_name+"</td>"
							  +"<td>"+v.scheme_type+"</td>"
							  +"<td>"+v.credit_value+"</td>"
							  +"<td>"+v.validfrom+"</td>"
							  +"<td>"+v.apply_from+"</td>"
							  +"<td>"+v.validto+"</td>"
							  +"<td></td>"
							  +"</tr>";
							  $("#active_imei_scheme tbody").append(imeisch_row);
							
					});
				}

				if(result.menu != undefined)
				{
					$.each(result.menu,function(k,v){
						 var menutbl_row =
							 "<tr>"
							  +"<td>"+v.menu+"</td>"
							  +"</tr>";
							  $("#menu tbody").append(menutbl_row);
					});
				}
			//}
			
		},'json');
	},
	buttons:{
		'Close' :function(){
			 $(this).dialog('close');
			}
		}
});

function format_number(num,decimal) {
    var deci = (decimal === undefined || decimal === null ) ? 2 : decimal;
    num = parseFloat(num);
    
    var final_num = ( num.toString().indexOf(".") !== -1) ? num.toFixed(deci) : num;
    return parseFloat(final_num);
}


</script>

<div id="newbie_confirm_dlg" title="New franchisee note" >
	<h4>New franchisee note</h4>
	<div class="hexa">
		<div><b>Please take a note, this is a new franchisee and we should treat them very well</b></div>
		<div><input type="checkbox" class="nb_cond" value="1" ><b>Dont say no to products</b></div>
		<div><input type="checkbox" class="nb_cond" value="1" ><b>Contact TM or executive in office for any information</b></div>
		<div><input type="checkbox" class="nb_cond" value="1" ><b>Dont commit shipment date (post dated)</b></div>
		<div><input type="checkbox" class="nb_cond" value="1" ><b>Be gentle with policy and treat them as a child</b></div>
	</div>
</div>

<style>

#newbie_confirm_dlg{
position: absolute;
top: 66px;
right: 19px;
background: #FFFFA0;
width: 347px;
display: none;
}
#newbie_confirm_dlg h4{margin: 0px;
margin-bottom: 0px;
text-align: center;
background: #FFF;
padding: 8px;
font-size: 120%;}
#newbie_confirm_dlg div.hexa{padding:10px;font-size: 12px;}
#newbie_confirm_dlg div.hexa div{margin-bottom: 5px;}
#prod_suggest,#cancel_prod_suggest_list{
max-height:410px;
overflow:auto;
}
.bottomleft_newbieblk{
	top:325px !important;
	left:31px  !important;
}
#prod_suggest_list{
	float:right;
	width:25%;
	padding:10px;
	border:1px solid #FFF;
	background:#fafafa;
	position: fixed;
	right: 18px;
	top: 76px;
}

.ui-widget-header{background: none;}

.ui-tabs .ui-tabs-panel{
	padding: 3px !important;
}
.ui-tabs .ui-tabs-nav li a {
	padding: .2em 0.5em !important;
	font-size: 85% !important;
}
.smallheader td{font-size: 11px;}

.fixed_bottom{
padding:3px 10px 1px 10px;color:#fff;position:fixed;bottom:0px;
left:20px;background:#C97033;border-radius:5px 5px 0px 0px;
}
.footerlinks{
display:none;
}
.contenttable .leftcont{
display:none;
}
#srch_results{
	margin-left: 85px;
	position: absolute;
	display: none;
	width: 400px;
	overflow-y: auto;
	background: #EEE;
	border: 1px solid #AAA;
	bottom: 27px;
	max-height: 500px;
	min-width: 300px;
	max-width: 326px;
	overflow-x: hidden;
	z-index:1;
}
#srch_results a{
	display: block;
	padding: 5px 6px;
	font-size: 12px;
	display: inline-table;
	width: 300px;
	text-transform: capitalize;
	border-bottom: 1px dotted #DDD;
	background: white;
} 
#srch_results a:hover{
background: #CCC;
color: black;
text-decoration: none;
}
#hd{
	display:block;
}
#mem_fran{
display:none;
background:#eee;
padding:5px;
font-size:80%;
font-weight:bold;
margin:5px 0px;
}
#fran_special_margins{
top:470px;
}

#req_prodlist tfoot{display: none !important}
#req_prodlist th{padding:1px !important;}
.add_tblrow,.remove_tblrow{
padding:3px 6px !important ;
}
.small_input{
font-size: 11px !important;
padding:3px 6px !important ;
}
.error_msg_text{color: #cd0000;font-size: 11px;display: block;}
.warning{background: #cd0000;color:#FFF}

.button {
   border-top: 1px solid #96d1f8;
   background: #28597a;
   -webkit-border-radius: 3px;
   -moz-border-radius: 3px;
   color:#FFF;
}
.button:hover {
   border-top-color: #28597a;
   background: #28597a;
   color: #FFF;
}
.module{
	width: 100%;
	margin-bottom: 7px;
}
.module h3,.module h4{font-size: 14px;}
 
.blinker {
    -webkit-animation: blinker 1s linear infinite;
    -moz-animation: blinker 1s linear infinite;
    -ms-animation: blinker 1s linear infinite;
    -o-animation: blinker 1s linear infinite;
    animation: blinker 1s linear infinite;
}

@-webkit-keyframes blinker {
    0% { opacity: 1; }
    50% { opacity: 1; }
    50.01% { opacity: 0; }
    100% { opacity: 0; }
}

@-moz-keyframes blinker {
    0% { opacity: 1; }
    50% { opacity: 1; }
    50.01% { opacity: 0; }
    100% { opacity: 0; }
}

@-ms-keyframes blinker {
    0% { opacity: 1; }
    50% { opacity: 1; }
    50.01% { opacity: 0; }
    100% { opacity: 0; }
}

@-o-keyframes blinker {
    0% { opacity: 1; }
    50% { opacity: 1; }
    50.01% { opacity: 0; }
    100% { opacity: 0; }
}

@keyframes blinker {
    0% { opacity: 1; }
    50% { opacity: 1; }
    50.01% { opacity: 0; }
    100% { opacity: 0; }
}

.landing_cost{
background-color:#89c403;display:block;padding:12px 15px;
}
.offn_courier_detail {
    margin: 5px; font-size: 12px; clear: both;
}
.offn_courier_detail span {
    margin:0 10px;
}
.title a { text-decoration: none; color: #161EE7; }
</style>
<?php
