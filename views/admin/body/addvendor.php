<?php 
	$v=false;
	if(isset($vendor))
		$v=$vendor;
?>

<div class="container">
<h2><?=$v?"Edit":"Add new"?> Vendor</h2>

<form method="post" id="venfrm"  autocomplete="off">
<div class="tabs">

<ul>
<li><a href="#v_details">Basic Details</a></li>
<li><a href="#v_financials">Finance Details</a></li>
<li><a href="#v_extra">Extra Details</a></li>
<li><a href="#v_contacts">Contacts</a></li>
<li><a href="#v_linkbrands">Link Brands</a></li>
</ul>

<div id="v_details">
<table>
<tr><td>Name <span class="red_star">*</span> :</td><td><input type="text" name="name" class="inp val_req" value="<?=$v?"{$v['vendor_name']}":""?>"></td></tr>
<tr><td>Address Line 1 :</td><td><input type="text" name="addr1" class="inp" size="40" value="<?=$v?"{$v['address_line1']}":""?>"></td></tr>
<tr><td>Address Line 2 :</td><td><input type="text" name="addr2" class="inp" size="30" value="<?=$v?"{$v['address_line2']}":""?>"></td></tr>
<tr><td>Locality :</td><td><input type="text" name="locality" class="inp" value="<?=$v?"{$v['locality']}":""?>"></td></tr>
<tr><td>Landmark :</td><td><input type="text" name="landmark" class="inp" value="<?=$v?"{$v['landmark']}":""?>"></td></tr>
<tr><td>City <span class="red_star">*</span> :</td><td><input type="text" name="city" class="inp val_req" value="<?=$v?"{$v['city_name']}":""?>"></td></tr>
<tr><td>State :</td><td><input type="text" name="state" class="inp" value="<?=$v?"{$v['state_name']}":""?>"></td></tr>
<tr><td>Country :</td><td><input type="text" name="country" class="inp" value="<?=$v?"{$v['country']}":""?>"></td></tr>
<tr><td>Postcode :</td><td><input type="text" name="postcode" class="inp" value="<?=$v?"{$v['postcode']}":""?>"></td></tr>
</table>
</div>
<div id="v_financials">
<table>
<tr><td>Ledger ID :</td><td><input type="text" name="ledger" class="inp" value="<?=$v?"{$v['ledger_id']}":""?>"></td></tr>
<tr><td>Credit Limit :</td><td><input type="text" name="credit_limit" class="inp" value="<?=$v?"{$v['credit_limit_amount']}":""?>"></td></tr>
<tr><td>Credit Days :</td><td><input type="text" name="credit_days" class="inp" value="<?=$v?"{$v['credit_days']}":""?>"></td></tr>
<tr><td>Payment Advance :</td><td><input type="text" name="advance" class="inp" size=3 value="<?=$v?"{$v['require_payment_advance']}":""?>">%</td></tr>
<tr><td>CST :</td><td><input type="text" name="cst" class="inp" value="<?=$v?"{$v['cst_no']}":""?>"></td></tr>
<tr><td>PAN :</td><td><input type="text" name="pan" class="inp" value="<?=$v?"{$v['pan_no']}":""?>"></td></tr>
<tr><td>VAT :</td><td><input type="text" name="vat" class="inp" value="<?=$v?"{$v['vat_no']}":""?>"></td></tr>
<tr><td>Service Tax :</td><td><input type="text" name="stax" class="inp" value="<?=$v?"{$v['service_tax_no']}":""?>"></td></tr>
<tr><td>Average TAT :</td><td><input type="text" name="tat" class="inp" value="<?=$v?"{$v['avg_tat']}":""?>"></td></tr>
</table>
</div>
<div id="v_extra">
<table>
<tr><td>Return Policy :</td><td><textarea class="inp" name="rpolicy"><?=$v?"{$v['return_policy_msg']}":""?></textarea></td></tr>
<tr><td>Payment Terms :</td><td><textarea class="inp" name="payterms"><?=$v?"{$v['payment_terms_msg']}":""?></textarea></td></tr>
<tr><td>Remarks :</td><td><textarea class="inp" name="remarks"><?=$v?"{$v['remarks']}":""?></textarea></td></tr>
</table>
</div>
<div id="v_contacts">
<input type="button" value="+ new contact" onclick='clone_vcnt()'>
<div id="v_contact_cont">
<?php if($v){foreach($contacts as $c){?>

<table>
<tr><td>Name : </td><td><input type="text" class="inp" name="cnt_name[]" value="<?=$c['contact_name']?>"></td>
<td>Designation : </td><td><input type="text" class="inp" name="cnt_desgn[]" value="<?=$c['contact_designation']?>"></td>
</tr>
<tr>
<td>Mobile 1 : </td><td><input type="text" class="inp" name="cnt_mob1[]" value="<?=$c['mobile_no_1']?>"></td>
<td>Mobile 2 : </td><td><input type="text" class="inp" name="cnt_mob2[]" value="<?=$c['mobile_no_2']?>"></td>
</tr>
<tr>
<td>Telephone : </td><td><input type="text" class="inp" name="cnt_telephone[]" value="<?=$c['telephone_no']?>"></td>
<td>FAX : </td><td><input type="text" class="inp" name="cnt_fax[]" value="<?=$c['fax_no']?>"></td>
</tr>
<tr>
<td>Email 1 : </td><td><input type="text" class="inp" name="cnt_email1[]" value="<?=$c['email_id_1']?>"></td>
<td>Email 2 : </td><td><input type="text" class="inp" name="cnt_email2[]" value="<?=$c['email_id_2']?>"></td>
</tr>
</table>
<?php } }?>
</div>
</div>

<div id="v_linkbrands">

<div style="padding-bottom:20px;">
Search : <input type="text" class="inp" id="v_lbsearch">
<div id="v_searchresb" class="srch_result_pop"></div>
</div>

<table class="datagrid" id="v_lbtable">
<thead>
<tr>
<th>
	&nbsp;
</th>
<th>Brand</th>
<th>Margin %</th>
<th>Applicable From</th>
<th colspan=2>Applicable Until</th>
</tr>
</thead>
<tbody>
<?php if($v){ foreach($brands as $i=>$b){
?>
<tr>
<td><input type="checkbox" class="edit_vblink_chk" ></td>
<td><input type="hidden" disabled="disabled" class="inp" name="l_brand[]" value="<?=$b['brand_id']?>"><?=$b['name']?></td>
<td><input type="text" disabled="disabled"  class="inp" name="l_margin[]" value="<?=$b['brand_margin']?>"></td>
<td><input type="text" disabled="disabled"  class="inp datepic lb_date<?=$i?>" name="l_from[]" value="<?=date("Y-m-d",$b['applicable_from'])?>"></td>
<td><input type="text" disabled="disabled"  class="inp datepic lb_date<?=$i?>t" name="l_until[]" value="<?=date("Y-m-d",$b['applicable_till'])?>"></td>
<td>
	<a href="javascript:void(0)" onclick="remove_vblink(this)" >remove</a>
</tr>
<?php }}?>
</tbody>
</table>

</div>

</div>
<input type="submit" value="Submit">
</form>

</div>

<div style="display:none">
<table id="lb_template">
<tbody>
<tr>
<td>&nbsp;</td>
<td><input type="hidden" name="l_brand[]" value="%brandid%">%brand%</td>
<td><input type="text" class="inp" name="l_margin[]" value="10"></td>
<td><input type="text" class="inp lb_date%di%" name="l_from[]"></td>
<td><input type="text" class="inp lb_date%di%t" name="l_until[]"></td>
<td><a href="javascript:void(0)" onclick="remove_vblink(this)" >remove</a>
</tr>
</tbody>
</table>
</div>

<table id="cnt_clone">
<tr><td>Name : </td><td><input type="text" class="inp" name="cnt_name[]"></td>
<td>Designation : </td><td><input type="text" class="inp" name="cnt_desgn[]"></td>
</tr>
<tr>
<td>Mobile 1 : </td><td><input type="text" class="inp" name="cnt_mob1[]"></td>
<td>Mobile 2 : </td><td><input type="text" class="inp" name="cnt_mob2[]"></td>
</tr>
<tr>
<td>Telephone : </td><td><input type="text" class="inp" name="cnt_telephone[]"></td>
<td>FAX : </td><td><input type="text" class="inp" name="cnt_fax[]"></td>
</tr>
<tr>
<td>Email 1 : </td><td><input type="text" class="inp" name="cnt_email1[]"></td>
<td>Email 2 : </td><td><input type="text" class="inp" name="cnt_email2[]"></td>
</tr>
</table>

<style>
#cnt_clone{
display:none;
}
#v_contact_cont table{
margin:10px;
border:1px solid #ccc;
padding:5px;
}
#v_searchres{
display:none;
position:absolute;
width:200px;
height:80px;
overflow:auto;
background:#eee;
border:1px solid #aaa;
}
#v_searchres a{
display:block;
padding:5px;
}
#v_searchres a:hover{
background:blue;
color:#fff;
}
td.selected{
	background: #b4defe !important;
}
.required_inp{border:1px solid #cd0000 !important;}
.red_star{color: #cd0000;font-size: 20px;font-weight: bold;margin-left: 5px;}
</style>
<script>

 
$('#venfrm').submit(function(){
	$('.val_req',this).each(function(){
		$(this).val($.trim($(this).val()));
		if($(this).val())
			$(this).removeClass('required_inp');
		else
			$(this).addClass('required_inp');
	});


	if($('.required_inp',this).length)
	{
		alert("Vendor name and city is required");
		return false;
	}
	
	if(!$('#v_lbtable tbody tr',this).length)
	{
		alert("Link atlease one brand for this vendor");
		return false;
	}
	
});

var p_added=[],b_added=[];
var ven_id = '<?php echo $this->uri->segment(3);?>';
<?php if($v){foreach($brands as $b){?>
b_added.push(<?=$b['brand_id']?>);
<?php }}?>

$('.edit_vblink_chk').live('change',function(){
	var tds = $(this).parent().parent().find('td');
	if($(this).attr("checked")){
		tds.addClass('selected');
		tds.find(".inp").attr("disabled",false);
		
	}else
	{
		tds.removeClass('selected');
		tds.find(".inp").attr("disabled",true);
	}
});

function remove_vblink(ele){
	var trEle = $(ele).parent().parent();
	if(ven_id)
	{
		if(confirm("Want to remove "+$('td:eq(1)',trEle).text()+" from this vendor ?"))
		{
			brand_id = $('input[name="l_brand[]"]',trEle).val();
			$.post(site_url+'/admin/jx_remove_vendor_brand_link','vendor_id='+ven_id+'&brand_id='+brand_id,function(resp){
				if(resp.status == 'error')
				{
					alert(resp.error);
				}else
				{
					trEle.fadeOut().remove();
				}
			},'json');
		}
	}
	else
	{
		trEle.fadeOut().remove();
	}		
}

function clone_vcnt()
{
	$("#v_contact_cont").append("<table>"+$("#cnt_clone").html()+"</table>");
}
function addproduct(id,name,mrp,tax)
{
	$("#v_searchres").hide();
	if($.inArray(id,p_added)!=-1)
	{
		alert("Product already added");
		return;
	}
	p_added.push(id);
	template='<tr><td><input type="hidden" name="pproduct[]" value="'+id+'">'+name+'</td><td><input class="inp" type="text" name="pmrp[]" value="'+mrp+'"></td><td><input type="text" class="inp" name="pprice[]"></td><td><input type="text" class="inp" name="ptax[]" value="'+tax+'"></td><td><input type="text" class="inp" name="pminorder[]"></td><td><input type="text" name="ptat" class="inp"></td><td><input type="text" class="inp" name="premarks[]"></td></tr>';
	$("#v_lptable").append(template);
	$("#v_lpsearch").val("");
}
function addbrand(name,id)
{
	$("#v_searchresb").hide();
	if($.inArray(id,b_added)!=-1)
	{
		alert("brand already added");
		return;
	}
	b_added.push(id);
	i=b_added.length;
	template=$("#lb_template tbody").html();
	template=template.replace(/%brandid%/g,id);
	template=template.replace(/%brand%/g,name);
	template=template.replace(/%di%/g,i);
	$("#v_lbtable tbody").append(template);
	$(".lb_date"+i).datepicker();
	$(".lb_date"+i+"t").datepicker();

	
	
}
$(function(){
	for(i=0;i<b_added.length;i++)
		$(".lb_date"+i+", .lb_date"+i+"t").datepicker();
	if(b_added.length==0)
	clone_vcnt();
	$("#v_lpsearch").keyup(function(){
		$.post("<?=site_url("admin/searchproducts")?>",{q:$(this).val()},function(data){
			$("#v_searchres").html(data).show();
		});
	});
	$("#v_lbsearch").keyup(function(){
		$.post("<?=site_url("admin/jx_searchbrands")?>",{q:$(this).val()},function(data){
			$("#v_searchresb").html(data).show();
		});
	}).focus(function(){
		if($("#v_searchresb").html().length!=0)
			$("#v_searchresb").show();
	});
});
</script>
<?php
