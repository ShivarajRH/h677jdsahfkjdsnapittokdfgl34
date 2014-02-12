<?php $v=$vendor;?>
<style>.leftcont{display:none;}</style>

<style>
.dash_bar, .dash_bar_right, .dash_bar_red{
	font-size: 12px;
	margin:0px;
	margin-right:10px;
}
.dash_bar span, .dash_bar_right span{
	font-size: 14px;
}
#v_contact_cont table{
	margin:10px;
	border:1px solid #ccc;
	padding:5px;
}
.vendorpg_btn{
	background-color: rgb(227, 227, 227);
    background-image: linear-gradient(to bottom, rgb(239, 239, 239), rgb(216, 216, 216));
    border: 1px solid rgba(0, 0, 0, 0.4);
    border-radius: 3px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1), 0 1px 1px rgba(255, 255, 255, 0.8) inset;
    color: rgb(76, 76, 76);
    display: inline-block;
    font-size: 13px;
    margin: 0;
    outline: medium none;
    padding: 3px 12px;
    text-align: center;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.5);

}
.tgl_linkedcats
{
 	background: none repeat scroll 0 0 rgb(245, 245, 245);
    color: green;
    display: inline-block;
    font-size: 9px;
    margin: 2px 0;
    padding: 2px;
}

.warn td{background: #FDD2D2 !important;}
</style>
<div class="page_wrap">
	<div class="page_top">
		<div class="page_top_left fl_left" style="width: 40%">
			<h2 class="page_title">Vendor Details - <?php echo $v['vendor_name']?>  </h2>
		</div>
		<div class="page_top_right fl_right" style="width: 60%;font-size: 12px;">
			<div style="float: right">
				<a href="<?=site_url("admin/editvendor/{$v['vendor_id']}")?>" class="button button-rounded button-tiny" >Edit</a>&nbsp;&nbsp;<a href="<?php echo site_url("admin/purchaseorder/{$v['vendor_id']}")?>" target="_blank" class="button button-rounded button-action button-tiny" >Create PO</a>
			</div>
			<div class="dash_bar_right" >
				<span><?=$this->db->query("select count(1) as l from t_po_info where vendor_id=?",$v['vendor_id'])->row()->l?></span>
				POs raised
			</div>
			
			<div class="dash_bar_right" >
				<span>Rs <?=number_format($this->db->query("select sum(total_value) as l from t_po_info where vendor_id=?",$v['vendor_id'])->row()->l)?></span>
				Total PO value
			</div>
		</div>
	</div>
	<div class="page_content">
<div class="tab_view">

<ul>
<li><a href="#v_details">Basic Details</a></li>
<li><a href="#v_financials">Finance Details</a></li>
<li><a href="#v_extra">Extra Details</a></li>
<li><a href="#v_contacts">Contacts</a></li>
<li><a href="#v_brands">Brands Margin Details</a></li>
<li><a href="#v_pos">POs Raised</a></li>
</ul>

<div id="v_details">
<table class="datagrid " width="400">
<tr><td width="100">Code :</td><td><?=$v['vendor_code']?></td></tr>
<tr><td>Name :</td><td><?=$v['vendor_name']?></td></tr>
<tr><td>Address Line 1 :</td><td><?=$v['address_line1']?></td></tr>
<tr><td>Address Line 2 :</td><td><?=$v['address_line2']?></td></tr>
<tr><td>Locality :</td><td><?=$v['locality']?></td></tr>
<tr><td>Landmark :</td><td><?=$v['landmark']?></td></tr>
<tr><td>City :</td><td><?=$v['city_name']?></td></tr>
<tr><td>State :</td><td><?=$v['state_name']?></td></tr>
<tr><td>Country :</td><td><?=$v['country']?></td></tr>
<tr><td>Postcode :</td><td><?=$v['postcode']?></td></tr>
<tr><td>Ledger ID :</td><td><?=$v['ledger_id']?></td></tr>
</table>
</div>
<div id="v_financials">
<table class="datagrid nofooter" width="400">
<tr><td>Credit Limit :</td><td width="250"><?=$v['credit_limit_amount']?></td></tr>
<tr><td>Credit Days :</td><td><?=$v['credit_days']?></td></tr>
<tr><td>Payment Advance (%):</td><td><?=$v['require_payment_advance']?></td></tr>
<tr><td>Payment Type :</td><td><? $pmt_types=array('','Cheque','Cash','DD'); echo $pmt_types[$v['payment_type']]?></td></tr>
<tr><td>CST :</td><td><?=$v['cst_no']?></td></tr>
<tr><td>PAN no :</td><td><?=$v['pan_no']?></td></tr>
<tr><td>VAT (%) :</td><td><?=$v['vat_no']?></td></tr>
<tr><td>Service Tax :</td><td><?=$v['service_tax_no']?></td></tr>
<tr><td>Average TAT :</td><td><?=$v['avg_tat']?></td></tr>
</table>
</div>
<div id="v_extra">
<table class="datagrid nofooter" >
<tr><td width="100">Return Policy </td><td width="300"><p><?=$v['return_policy_msg']?$v['return_policy_msg']:'-na-'?></p></td></tr>
<tr><td>Payment Terms </td><td><p><?=$v['payment_terms_msg']?$v['payment_terms_msg']:'-na-'?></p></td></tr>
<tr><td>Remarks </td><td><p><?=$v['remarks']?$v['remarks']:'-na-';?></p></td></tr>
</table>
</div>
<div id="v_contacts">
<div id="v_contact_cont">
<table class="datagrid nofooter" width="100%">
	<thead>
		<th width="20">Slno</th>
		<th width="">Name</th>
		<th width="80">Designation</th>
		<th width="150">Mobile 1</th>
		<th width="150">Mobile 2</th>
		<th width="100">Telephone</th>
		<th width="100">FAX</th>
		<th width="150">Email 1</th>
		<th width="150">Email 2</th>
	</thead>
	<tbody>
<?php
	if(count($contacts))
	{ 
		foreach($contacts as $c){
?>
	<tr>
		<td><?=$i+1?></td>
		<td><?=$c['contact_name']?></td>
		<td><?=$c['contact_designation']?></td>
		<td><?=$c['mobile_no_1']?></td>
		<td><?=$c['mobile_no_2']?></td>
		<td><?=$c['telephone_no']?></td>
		<td><?=$c['fax_no']?></td>
		<td><?=$c['email_id_1']?></td>
		<td><?=$c['email_id_2']?></td>
	</tr>
<?php 
		}
	}else
	{
?>
	<tr><td colspan="9" align="center">No Contacts Added</td></tr>
<?php
	}
?>
	</tbody>
</table>
</div>
</div>

<div id="v_brands">
	<a class="editmargin fl_right button button-rounded button-action button-small" style="color: #FFF"  href="<?php echo site_url("admin/editvendor/{$v['vendor_id']}#v_linkbrands")?>">Edit Brand Margin</a>
	<div class="po_filter_wrap2">
		<div style="width:35%;float:right;margin-top:15px;">
			<span><b style="margin:3px 5px;float: left">Filter by : </b></span>
			<select name='fil_brand' class='fil_brand' style="width:150px;" data-placeholder='Brand'>
				
			</select>
		</div>
	</div>
		<h3>Linked Brands and Category Details</h3> 
<table class="datagrid nofooter" width="100%">
<thead>
<tr><th>Sl no</th><th>Linked Brands</th><th>Linked Categorys </th><th>Total PO value</th></tr>
</thead>
<tbody>
<?php $i=1; foreach($brands as $b){ $bid=$b['id']; $vid=$b['vendor_id']?>
<tr class="vbc_link  brandid_<?php echo $b['id'];?>" vendorid="<?php echo $b['vendor_id'] ;?>" brandid="<?php echo  $b['id']?>">
	<td width="40"><?php echo $i;?></td>
	<td width="150"><a href="<?=site_url("admin/viewbrand/{$b['id']}")?>"><?=$b['name']?></a></td>
	<td>
		<div id="view_cat">
			<span class="tgl_linkedcats">
				<a href="javascript:void(0)" vendorid="<?php echo $b['vendor_id'] ;?>" brandid="<?php echo  $b['id']?>" class="tgl_linkedcats" status="1">View category</a>
			</span>
			<div class="vbc_link"></div>
		</div>
	</td>
	<td>Rs <?=number_format($this->db->query("select sum(p.purchase_price*p.order_qty) as s from t_po_info po join t_po_product_link p on p.po_id=po.po_id join m_product_info d on d.product_id=p.product_id and d.brand_id=? where po.vendor_id=?",array($b['id'],$v['vendor_id']))->row()->s)?>
</tr>
<?php $i++; }?>
</tbody>
</table>
</div>


<div id="v_pos">

<table class="datagrid nofooter" style="margin-top:10px;" width="100%">
<thead>
<tr>
<th>ID</th>
<th>Created On</th>
<th>Value</th>
<th>Purchase Status</th>
<th>Stock Status</th>
<th></th>
<th>Remarks</th>
</tr>
</thead>
<tbody>
<?php foreach($pos as $p){?>
<tr>
<td>PO<?=$p['po_id']?></td>
<td><?=date("g:ia d/m/y",strtotime($p['created_on']))?></td>
<td>Rs <?=number_format($p['total_value'])?></td>
<td><?php switch($p['po_status']){
	case 1:
	case 0: echo 'Open'; break;
	case 2: echo 'Complete'; break;
	case 3: echo 'Cancelled';
}?></td>
<td>
<?php switch($p['po_status']){
	case 0: echo 'Not received'; break;
	case 1: echo 'Partially received'; break;
	case 2: echo 'Fully received'; break;
	case 3: echo 'NA';
}?>
</td>
<td>
<a class="link" href="<?=site_url("admin/viewpo/{$p['po_id']}")?>">view</a>
<?php if($p['po_status']!=2 && $p['po_status']!=3){?>
&nbsp;&nbsp;&nbsp;<a href="<?=site_url("admin/apply_grn/{$p['po_id']}")?>">Stock Intake</a>
<?php }?>
</td>
<td><?=$p['remarks']?></td>
</tr>
<?php } if(empty($pos)){?><tr><td colspan="100%">no POs to show</td></tr><?php }?>
</tbody>
</table>

</div>
</div>
</div>
</div>
<script>
var ven_id = '<?php echo $this->uri->segment(3);?>';
function js_date_diff  (date2, date1) {
    var days = 0;
    if (date2 != null && date1 != null) {
        date1 = new Date(date1).getTime();
        date2 = new Date(date2).getTime();
        var timediff = date1 - date2;
		if (!isNaN(timediff)) {
            //day 86400000 = second = 1000,minute = second * 60,hour = minute * 60,day = hour * 24, to get day
            days = Math.floor(timediff / 86400000);
        }
    }

    return days;
}

$('.fil_brand').chosen();
$('.fil_cat').chosen();

$('#v_brands').click(function(){
	var ven_id = '<?php echo $this->uri->segment(3);?>';
	if(ven_id)
	{
		$('.po_filter_wrap2').show();
		var ven_id = '<?php echo $this->uri->segment(3);?>';
		
		
		$.post(site_url+'/admin/jx_load_brand_byvendor',{ven_id:ven_id},function(resp){
			var brand_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				brand_html+='<option value=""></option>';
				brand_html+='<option value="0">All</option>';
				$.each(resp.br_list,function(i,c){
				brand_html+='<option value="'+c.brandid+'">'+c.brand_name+'</option>';
				});
			}
			$('.fil_brand').html(brand_html).trigger("liszt:updated");
		},'json');
	}
	else
	{
		$('.po_filter_wrap2').hide();
	}
	
});




$('.vbc_link a.tgl_linkedcats').click(function(e){
	e.preventDefault();
	var ele  = $(this);
	var brand_id=$(this).attr('brandid');
	var vendor_id=$(this).attr('vendorid');
	if($(this).attr('status') == 1)
	{
		var qcktiphtml='';
		$(this).attr('status',0);
		$(this).text('close');
		$.post(site_url+'/admin/to_get_linkedcatbybrandvendor',{brandid:brand_id,vendorid:vendor_id},function(resp){
			if(resp.status == 'error')
			{
				$('.vbc_link #category_det',ele.parent().parent()).html("No Details found").hide();
			}
			else
			{
				qcktiphtml += '<div style="max-height:200px;overflow:auto;clear:both" id="#catbrand">';
				qcktiphtml += '<table width="100%" border=1 class="datagrid">';
				qcktiphtml += '<thead><tr><th>Category</th><th>Brand Margin</th><th>Applicable from</th><th>Applicable To</th></tr></thead><tbody>';
				$.each(resp.l_catlist,function(a,b){
					var date = new Date();
					var d = date.getDate();
					var m = date.getMonth();
					m=m==0?'01':m;
					var y = date.getFullYear();
					var today_dt = y+"-"+m+"-"+d;
					var is_exp = js_date_diff(today_dt,b.applicable_till);
					if(is_exp<0)
					{qcktiphtml+='<tr class=warn>';}
					else
					{qcktiphtml+='<tr>';}
					if(b.category_name==null)
						b.category_name='All';
					
					qcktiphtml+='	<td>'+b.category_name+'</td>';
					qcktiphtml+='	<td>'+b.brand_margin+'</td>';
					qcktiphtml+='	<td>'+b.applicable_from+ '</td>';
					qcktiphtml+='	<td>'+b.applicable_till+ '</td>';
					qcktiphtml+='</tr>';
				});
				qcktiphtml += '</tbody></table></div>';
				$('.vbc_link',ele.parent().parent()).html(qcktiphtml).show();
				
		}
	},'json');
	}
	else
	{
		$(this).attr('status',1);
		$(this).text('View category');
		$('.vbc_link',ele.parent().parent()).html(qcktiphtml).hide(); 
	}
});




$('.fil_brand').change(function(){
	if($(this).val()==0)
	{
		$('#v_brands .datagrid tbody tr').show();
	}
	else if($(this).val()>0)
	{
		$('#v_brands .datagrid tbody tr').hide();
		$('#v_brands .datagrid tbody tr.brandid_'+$(this).val()).show();
	}
});

$('.tab_view').tabs();



$(function(){
	
});



</script>


<?php
