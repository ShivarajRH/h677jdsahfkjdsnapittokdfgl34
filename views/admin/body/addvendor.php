<style>.leftcont{display:none;}
.dlg_town_list div
{
	margin: 10px 0;
    vertical-align: middle;
}
.dlg_town_list span
{
	
}
</style>
<?php 
	$v=false;
	if(isset($vendor))
		$v=$vendor;
	$vendor_id=$v['vendor_id'];
?>
<div class="page_wrap">
	<div class="page_top" >
		<h2 class="page_title">
			<?php 
				echo $v?'Edit Vendor Details':'Add Vendor';
			?>
		</h2>
	</div>
	<div class="page_content">
		<form method="post" id="venfrm"  autocomplete="off">
			<div class="tab_view">
				<ul>
					<li><a href="#v_details">Basic Details</a></li>
					<li><a href="#v_financials">Finance Details</a></li>
					<li><a href="#v_extra">Extra Details</a></li>
					<li><a href="#v_contacts">Contacts</a></li>
					<li><a href="#v_linkbrands" class="link_br">Brands Margin Details</a></li>
				</ul>
				<div id="v_details">
					<div align="left">
						<table cellpadding="5" cellspacing="0">
							<tr><td>Name :<span class="red_star">*</span> </td><td><input type="text" name="name" size="30" class="inp val_req" value="<?=$v?"{$v['vendor_name']}":""?>"></td></tr>
							<tr>
								<td>Address Line 1 :</td>
								<td>
									<textarea rows="5" cols="45" name="addr1" class="inp"><?=$v?"{$v['address_line1']}":""?></textarea>
								</td>
							</tr>
							<tr>
								<td>Address Line 2 :</td>
								<td>
									<textarea rows="5" cols="45" name="addr2" class="inp"><?=$v?"{$v['address_line2']}":""?></textarea>
								</td>
							</tr>
							<tr><td>Locality :</td><td><input type="text" size="30" name="locality" class="inp" value="<?=$v?"{$v['locality']}":""?>"></td></tr>
							<tr><td>Landmark :</td><td><input type="text" size="30"  name="landmark" class="inp" value="<?=$v?"{$v['landmark']}":""?>"></td></tr>
							<tr><td>City <span class="red_star">*</span> :</td><td><input size="30"  type="text" name="city" class="inp val_req" value="<?=$v?"{$v['city_name']}":""?>"></td></tr>
							<tr><td>State :</td><td><input type="text" name="state" class="inp" size="30"  value="<?=$v?"{$v['state_name']}":""?>"></td></tr>
							<tr><td>Country :</td><td><input type="text" name="country" class="inp" size="30"  value="<?=$v?"{$v['country']}":""?>"></td></tr>
							<tr><td>Postcode :</td><td><input type="text" name="postcode" class="inp" size="8"  value="<?=$v?"{$v['postcode']}":""?>"></td></tr>
						</table>
					</div>
				</div>
				<div id="v_financials">
					<table cellpadding="5" cellspacing="0">
						<tr><td>Ledger ID :</td><td><input type="text" name="ledger" class="inp" value="<?=$v?"{$v['ledger_id']}":""?>"></td></tr>
						<tr><td>Credit Limit :<span class="red_star">*</span></td><td><input type="text" name="credit_limit" class="inp" value="<?=$v?"{$v['credit_limit_amount']}":""?>"></td></tr>
						<tr><td>Credit Days :<span class="red_star">*</span></td><td><input size="3" type="text" name="credit_days" class="inp" value="<?=$v?"{$v['credit_days']}":""?>"></td></tr>
						<tr><td>Payment Advance (%):</td><td><input type="text" name="advance" class="inp" size=3 value="<?=$v?"{$v['require_payment_advance']}":""?>"></td></tr>
						<tr><td>Payment Type :<span class="red_star">*</span></td><td>
						<select name="payment_type">
			            <option <?php if ($v['payment_type'] == 0 ) echo 'selected'; ?> value="0">Select</option>
			            <option <?php if ($v['payment_type'] == 1 ) echo 'selected'; ?> value="1">Cheque</option>
						<option <?php if ($v['payment_type'] == 2 ) echo 'selected'; ?> value="2">Cash</option>
						<option <?php if ($v['payment_type'] == 3 ) echo 'selected'; ?> value="3">DD</option>
						</select>
						</td>
						</tr>
						<tr><td>CST no:</td><td><input type="text" name="cst" class="inp" value="<?=$v?"{$v['cst_no']}":""?>"></td></tr>
						<tr><td>PAN no:</td><td><input type="text" name="pan" class="inp" value="<?=$v?"{$v['pan_no']}":""?>"></td></tr>
						<tr><td>VAT (%):</td><td><input type="text" name="vat" class="inp" value="<?=$v?"{$v['vat_no']}":""?>"></td></tr>
						<tr><td>Service Tax :</td><td><input type="text" name="stax" class="inp" value="<?=$v?"{$v['service_tax_no']}":""?>"></td></tr>
						<tr><td>Average TAT :</td><td><input type="text" name="tat" class="inp" value="<?=$v?"{$v['avg_tat']}":""?>"></td></tr>
					</table>
				</div>
				<div id="v_extra">
					<table cellpadding="5" cellspacing="0">
						<tr><td>Return Policy :</td><td><textarea rows="6" cols="60  class="inp" name="rpolicy"><?=$v?"{$v['return_policy_msg']}":""?></textarea></td></tr>
					 	<tr><td>Payment Terms :</td><td><textarea rows="6" cols="60"  class="inp" name="payterms"><?=$v?"{$v['payment_terms_msg']}":""?></textarea></td></tr>
						<tr><td>Remarks :</td><td><textarea rows="6" cols="60"  class="inp" name="remarks"><?=$v?"{$v['remarks']}":""?></textarea></td></tr>
					</table>
				</div>
				<div id="v_contacts">
					<input type="button" value="+ new contact" onclick='clone_vcnt()'>
					<div id="v_contact_cont">
						<?php if($v){foreach($contacts as $c){?>
						<table cellpadding="5" cellspacing="0">
							<tr>
								<td>Name : </td><td><input type="text" class="inp" name="cnt_name[]" value="<?=$c['contact_name']?>"></td>
								<td>Designation : </td><td><input type="text" class="inp" name="cnt_desgn[]" value="<?=$c['contact_designation']?>"></td>
							</tr>
							<tr>
								<td>Mobile 1 :<span class="red_star">*</span> </td><td><input type="text" class="inp" name="cnt_mob1[]" value="<?=$c['mobile_no_1']?>"></td>
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
					<div class="po_filter_wrap2">
						<div style="width:49%;float:left">
							<span><b style="margin:3px 5px;float: left">Filter by : </b></span> 
							<select name="fil_cat" class="fil_cat" style='width:200px;' data-placeholder='Category'>
							</select>
						</div>
						
						<div style="width:49%;float:right">
							<span><b style="margin:3px 5px;float: left">Filter by : </b></span>
							<select name='fil_brand' class='fil_brand' style="width:200px;" data-placeholder='Brand'>
							</select>
						</div>
					</div>
					
					<div style="display: inline-block;width: 10%">Category : <button type="button" class="cat_popup">Add</button></div>
					<div style="display: inline-block;width: 10%">Brand : <button type="button" class="brand_popup">Add</button></div>
					<div style="display: inline-block;width: 10%">Towns : <button type="button" class="allot_towns">Allot</button></div>
					<div id="po_category_vendor_margin"  title="Add Category">
						<div class="po_filter_blk">
							<div id="filter_prods">
								<table cellspacing='10'>
									<tr>
										<td><b style="float:left;margin:3px 5px">Category : </b></td>
										<td>
											<div class="cat_det_list"></div>
										</td>
									</tr>
									<tr class='show_brand'>
										<td><b style="float:left;margin:3px 15px">Brand : </b></td>
										<td><select name='select_brand' class='select_brand' style="width:220px;height:300px;" multiple="true" data-placeholder='Select Brand'><option value=''>select brand</option></select></td>
									</tr>
								</table>
							</div>	
						</div>
					</div>
					<div id="po_brand_vendor_margin" style="" title="Add brand">
						<div class="po_filter_blk">
							<div id="filter_prods">
								<table cellspacing='10'>
									<tr>
										<td><b style="float:left;">Brand : </b></td>
										<td><div class="brand_list"></div>
											</td></tr>
									<tr class='show_category'>
										<td><b style="float:left;">Category : </b></td>
										<td><select name='select_category' class='select_category' style="width:220px;height:300px;" multiple="true" data-placeholder='Select cat'><option value=''>select category</option></select></td>
									</tr>
								</table>
							</div>	
						</div>
					</div>
				<table class="datagrid nofooter v_lbtable" width="100%">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>Brand</th>
							<th>Category</th>
							<th>Margin %</th>
							<th>Applicable From</th>
							<th>Applicable Until</th>
							<th width="120px">Towns</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php if($v){ foreach($cat_brands as $i=>$b){
						$no_days = date_diff_days(date('Y-m-d'),$b['applicable_till']);
						$is_exp = 0;
						if($no_days>0)
							$is_exp = 1;
						
					?>
						<tr class="brands_cat_det <?php echo $is_exp?'warn':''?>">
							<td><input type="checkbox" class="edit_vblink_chk" ></td>
							<td><input type="hidden" disabled="disabled" class="inp" name="l_brand[]" value="<?=$b['brand_id']?>"><?=$b['brand_name']?></td>
							<td>
							<input type="hidden" disabled="disabled" class="inp" name="l_catid[]" value="<?=$b['cat_id']?>"><?=$b['category_name']? $b['category_name'] :'All'?>&nbsp;<a style=" font-size: 10px;"id="show_cat" href="javascript:void(0)" onclick="load_allcatsofbrand(<?php echo $b['brand_id']?>)">edit cat</a></td>
							<td><input type="text" disabled="disabled" class="inp " name="l_margin[]" value="<?=$b['brand_margin']?>"></td>
							<td><input type="text" disabled="disabled"  class="inp  datepic lb_date lb_date<?=$i?>" name="l_from[]" value="<?php echo $b['applicable_from']!=0 ? $b['applicable_from']:0;?>" readonly="readonly"></td>
							<td><input type="text" disabled="disabled"  class="inp  datepic lb_date lb_date<?=$i?>t" name="l_until[]" value="<?php echo $b['applicable_till']!=0 ? $b['applicable_till']:0;?>"readonly="readonly"></td>
							<td width="120px">
								<?php
								 $town_names='';
								 $towns=$this->db->query("select a.town_name,b.town_id from pnh_towns a join m_vendor_town_link b on b.town_id=a.id where b.vendor_id=? and brand_id=? and is_active=1",array($vid,$b['brand_id']))->result_array(); 
								foreach($towns as $t)
								{
									$town_names.=$t['town_name'];
									$town_names.=" , ";
								}
								echo rtrim($town_names, " , ");
								?>
								<!--
								<select name="town[]" class="vendor_town" data-placeholder="Select Town" style="width: 250px;" data-required="true" multiple="true"></select>
								-->
							</td>	
							
							<td><a href="javascript:void(0)" onclick="remove_catbrandvblink(this)" style="color:red">remove</a></td>
						</tr>
					<?php }}?>
					</tbody>
				</table>
				</div>
			</div>
			<input type="submit" value="<?php echo !$v?'Add Vendor':'Update '?>" style="float:right;margin-top: 11px;" class="button button-action button-rounded button-small">
		</form>
	</div>

	<div id="all_catdiv" title="Categories" style="display:none;">
		<form id="update_cat_margin"  method="post" action ="<?php echo site_url('admin/to_update_brand_marg_bycat/'.$vendor_id)?>" >
			<input type="hidden" name="brandid" id="brandid" value="">
			<table class="datagrid" id="cat_brandtable" width="100%">
				<thead>
					<th></th>
					<th>Brand</th>
					<th>Category</th>
					<th>Margin %</th>
					<th>Applicable From</th>
					<th colspan=2>Applicable Until</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</form>
	</div>

</div>

<div style="display:none">
	<table id="lb_template" class="datagrid">
		<tbody>
			<tr>
				<td>&nbsp;</td>
				<td><input type="hidden" name="l_brand[]" value="%brandid%">%brand%</td>
				<td><input type="hidden" name="l_cat[]" value="%catid%">%cat%</td>
				<td><input type="text" class="inp" name="l_margin[]" value="10"></td>
				<td><input type="text" class="inp lb_date lb_date%di%" name="l_from[]" readonly="readonly" value="<?php echo date('Y-m-d') ?>"></td>
				<td><input type="text" class="inp lb_date lb_date%di%t" name="l_until[]" readonly="readonly" value="<?php echo date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + 365 day")) ?>"></td>
				<td><a href="javascript:void(0)" onclick="remove_catbrandvblink(this)" >remove</a>
			</tr>
		</tbody>
	</table>
</div>
<div id="towns_edit_by_brand" title="Add town">
	<div class="dlg_town_list">
		<div class="fl_left"><span>Brand :</span> <select name="sel_brand[]" class="choose_brand" data-placeholder="Select Brand" style="width: 250px;" data-required="true" multiple="true">
				</select>
		</div>
		<div class="fl_left">		
		Town : <select name="sel_town[]" class="sel_town" data-placeholder="Select Town" style="width: 250px;" data-required="true" multiple="true">
				</select>
		</div>			
	</div>
</div>	
<table id="cnt_clone">
	<tr>
		<td>Name : </td><td><input type="text" class="inp" name="cnt_name[]"></td>
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
.cat_popup,.brand_popup
{
	margin-top: 8px 8px 8px 0;
}
.show_brand,.show_category
{
	margin-top:15px;
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
#show_cat{display:none;}
.popclose_button.b-close, .popclose_button.bClose {
    border-radius: 7px;
    box-shadow: none;
    font: bold 131% sans-serif;
   padding: 6px 10px 5px;
    position: absolute;
    right: -7px;
    top: -7px;
}
.popclose_button > span {
    font-size: 84%;
}
.popclose_button {
    background-color: #2B91AF;
    border-radius: 10px;
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.3);
    color: #FFFFFF;
    cursor: pointer;
    display: inline-block;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
}

</style>
<script>
$('#load_brnd_catlink').hide();
$('.show_brand').hide();
$('.show_category').hide();
$('.select_cat ').chosen();
$(".select_brand ").chosen();
$(".sel_brand ").chosen();
$(".select_category ").chosen();
$('.fil_cat').chosen();
$('.fil_brand').chosen();

//$('.datepic.lb_date').datepicker();


$('.allot_towns').click(function(){
	$('#towns_edit_by_brand').dialog('open');
});


$('.cat_popup').click(function(){
	$('#po_category_vendor_margin').dialog('open');
	$('.select_brand').val('');
});

$('.brand_popup').click(function(){
	$('#po_brand_vendor_margin').dialog('open');
	$('.select_category').val('');
});

		
$('#towns_edit_by_brand').dialog({
	modal:true,
	autoOpen:false,
	width:'400',
	height:'430',
	open:function(){
		var vid = '<?php echo $this->uri->segment(3);?>';
		$.post(site_url+'/admin/jx_load_brand_byvendor',{ven_id:vid},function(resp){
			var b_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				b_html+='<option value="0">All</option>';
				//town_html+='<option value="0">All</option>';
				$.each(resp.br_list,function(i,b){
				b_html+='<option value="'+b.brandid+'">'+b.brand_name+'</option>';
				});
			}
			
			 $('#towns_edit_by_brand .dlg_town_list .choose_brand').html(b_html).trigger("liszt:updated");
			 
		},'json');
		
		$.post(site_url+'/admin/jx_suggest_townbyterrid',{},function(resp){
			var town_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				//town_html+='<option value="0">All</option>';
				$.each(resp.towns,function(i,b){
				town_html+='<option value="'+b.id+'">'+b.town_name+'</option>';
				});
			}
			
			 $('#towns_edit_by_brand .dlg_town_list .sel_town').html(town_html).trigger("liszt:updated");
			 
		},'json');
		
		$('.sel_town').chosen();
		$('.choose_brand').chosen();
	},
	buttons:{
	'Allot Town':function(){
		town=$('.sel_town').val();
		brand=$('.choose_brand').val();
		if(brand == undefined)
		{
			alert("Please Choose Brand");
			return false;
		}
		if(town == undefined)
		{
			alert("Please Choose Towns");
			return false;
		}
		
		add_towns(brand,town,1);
		$(this).dialog('close');
	},
	'Remove Town':function(){
		town=$('.sel_town').val();
		brand=$('.choose_brand').val();
		if(brand == undefined)
		{
			alert("Please Choose Brand");
			return false;
		}
		if(town == undefined)
		{
			alert("Please Choose Towns");
			return false;
		}
		
		add_towns(brand,town,0);
		$(this).dialog('close');
	},
	'Cancel':function(){
		$(this).dialog('close');
	}
}
});	
	
function add_towns(bid,tid,act)
{
	var vid = '<?php echo $this->uri->segment(3);?>';
	
	$.getJSON(site_url+'/admin/jx_allot_townsforvendor/'+bid+'/'+vid+'/'+tid+'/'+act,{},function(resp){
			var town_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				location.reload();	
			}
			
		});
}


$('#po_category_vendor_margin').dialog({
	modal:true,
	autoOpen:false,
	width:'400',
	height:'430',
	open:function(){
		$.post(site_url+'/admin/jx_getallcategory',{},function(resp){
			var cats_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				cats_html+='<select name="select_cat" class="select_cat" style="width:230px;">';
				cats_html+='<option value=""></option>';
				cats_html+='<option value="0">All</option>';
				$.each(resp.cat_list,function(i,c){
				cats_html+='<option value="'+c.id+'">'+c.name+'</option>';
				});
				cats_html+='</select>';
			}
			
			 $('.cat_det_list').html(cats_html).trigger("liszt:updated");
			 $('.select_cat').chosen();
		},'json');
	},
	buttons:{
	'Submit':function(){
		var select_brand=$('.select_brand').val();
		var cat_id=$('.select_cat').val();
		if(cat_id==0 || cat_id == undefined)
		{
			alert('Please select Category');
			
			return false;
		}
		else if(select_brand == undefined)
		{
			alert('Please select  atleast one Brand');
			return false;
		}
		else
		{
			update_new_cat(brandid_arr);
			$(this).dialog('close');
		}
	},
	'Cancel':function(){
		$(this).dialog('close');
	}
}
});

$('#po_brand_vendor_margin').dialog({
	modal:true,
	autoOpen:false,
	width:'400',
	height:'430',
	open:function(){
		$.post(site_url+'/admin/jx_getallbrands',{},function(resp){
			var brands_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				brands_html+='<select name="sel_brand" class="sel_brand" style="width:230px;">';
				brands_html+='<option value=""></option>';
				brands_html+='<option value="0">All</option>';
				$.each(resp.brand_list,function(i,b){
				brands_html+='<option value="'+b.id+'">'+b.name+'</option>';
				});
				brands_html+='</select>';
			}
			
			 $('.brand_list').html(brands_html).trigger("liszt:updated");
			 $('.sel_brand').chosen();
		},'json');
	},
	buttons:{
	'Submit':function(){
		var select_cat=$('.select_category').val();
		var brand_id=$('.sel_brand').val();
		if(brand_id==0 || brand_id == undefined)
		{
			alert('Please select Brand');
			
			return false;
		}
		else if(select_cat == undefined)
		{
			alert('Please select  atleast one Category');
			return false;
		}
		else
		{
			update_new_brand(catid_arr);
			$(this).dialog('close');
		}
	},
	'Cancel':function(){
		$(this).dialog('close');
	}
}
});

$('.fil_cat').change(function(){
	var catid=$(this).val();
	var ven_id = '<?php echo $this->uri->segment(3);?>';
	$(".fil_brand").html('').trigger("liszt:updated");
	$.post(site_url+'/admin/jx_load_brand_byvendor_bycatid',{catid:catid,vid:ven_id},function(resp){
		var brands_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			brands_html+='<option value=""></option>';
			brands_html+='<option value="0">All</option>';
			$.each(resp.brd_list,function(i,b){
			brands_html+='<option value="'+b.brandid+'">'+b.brand_name+'</option>';
			});
		}
		 $('.fil_brand').html(brands_html).trigger("liszt:updated");
	},'json');
	
	$.post(site_url+'/admin/jx_load_vendor_cat',{catid:catid,vid:ven_id},function(resp){
		if(resp.status=="success")
		{
			$(this).attr('disabled',true);
			var template='';
			$.each(resp.res_list,function(i,c){
				template +=	"<tr>"
							+"<td><input type='checkbox' class='edit_vblink_chk' checked='checked' ></td>"
							+"<td><input type='hidden'  class='inp' name='l_brand[]' value='"+c.brandid+"'>"+c.brand_name+"</td>"
							+"<td><input type='hidden'  class='inp' name='l_catid[]' value='"+c.catid+"'>"+c.category_name+"</td>"
							+"<td><input type='text'   class='inp' name='l_margin[]' value='"+c.brand_margin+"'></td>"
							+"<td><input type='text'   class='inp datepic lb_date' name='l_from[]' value='"+c.applicable_from+"' readonly='readonly'></td>"
							+"<td><input type='text'   class='inp datepic lb_date' name='l_until[]' value='"+c.applicable_till+"' readonly='readonly'></td>"
							+"<td><a href='javascript:void(0)' onclick='remove_catbrandvblink(this)' >remove</a>"
							+"</tr>";
				
			});
			$('.v_lbtable tbody').html(template);
			$('.v_lbtable .lb_date').each(function(i,dpEle){
				if(!$(this).hasClass('hasDatepicker'))
					$(this).datepicker();
			});
			
		}else
		{
			alert(resp.msg);
			$(this).attr('disabled',false);
		}
	},'json');
});

$('.link_br').click(function(){
	var ven_id = '<?php echo $this->uri->segment(3);?>';
	if(ven_id)
	{
		$('.po_filter_wrap2').show();
		var ven_id = '<?php echo $this->uri->segment(3);?>';
		$(".fil_cat").html('').trigger("liszt:updated");
		$.post(site_url+'/admin/jx_load_cat_byvendor',{ven_id:ven_id},function(resp){
			var cat_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				cat_html+='<option value=""></option>';
				cat_html+='<option value="0">All</option>';
				$.each(resp.ct_list,function(i,c){
				cat_html+='<option value="'+c.catid+'">'+c.category_name+'</option>';
				});
			}
			 $('.fil_cat').html(cat_html).trigger("liszt:updated");
		},'json');
		
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

$('.fil_brand').change(function(){
	var catid=$('select[name="fil_cat"]').val();
	var brand_id=$('select[name="fil_brand"]').val();
	var ven_id = '<?php echo $this->uri->segment(3);?>';
	
	$.post(site_url+'/admin/jx_load_vendordet_bybrand',{catid:catid,brand_id:brand_id,ven_id:ven_id},function(resp){
		if(resp.status=="success")
		{
			$(this).attr('disabled',true);
			var temp='';
			$.each(resp.res_list,function(i,c){
				temp += "<tr>"
							+"<td><input type='checkbox' class='edit_vblink_chk' checked='checked' ></td>"
							+"<td><input type='hidden'  class='inp' name='l_brand[]' value='"+c.brandid+"'>"+c.brand_name+"</td>"
							+"<td><input type='hidden'  class='inp' name='l_catid[]' value='"+c.catid+"'>"+c.category_name+"</td>"
							+"<td><input type='text'   class='inp' name='l_margin[]' value='"+c.brand_margin+"'></td>"
							+"<td><input type='text'   class='inp datepic lb_date' name='l_from[]' value='"+c.applicable_from+"'readonly='readonly'></td>"
							+"<td><input type='text'   class='inp datepic lb_date' name='l_until[]' value='"+c.applicable_till+"'readonly='readonly'></td>"
							+"<td><a href='javascript:void(0)' onclick='remove_catbrandvblink(this)' >remove</a>"
							+"</tr>";
			});
			$('.v_lbtable tbody').html(temp);
			$('.v_lbtable .lb_date').each(function(i,dpEle){
				if(!$(this).hasClass('hasDatepicker'))
					$(this).datepicker();
			});
			
		}else
		{
			alert(resp.msg);
			$(this).attr('disabled',false);
		}
	},'json');
});

var brandid_arr=[];
$('.select_cat').live('change',function(){
	var sel_catid=$(this).val();
	if(sel_catid!='0')
	{
		$('.show_brand').show();
		$('#load_brnd_catlink').show();
		$(".select_brand").html('').trigger("liszt:updated");
		$.getJSON(site_url+'/admin/jx_load_allbrandsbycat/'+sel_catid,'',function(resp){
		var brands_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			brands_html+='<option value=""></option>';
			brands_html+='<option value="0">All</option>';
			$.each(resp.brand_list,function(i,b){
			brands_html+='<option value="'+b.brandid+'">'+b.name+'</option>';
			brandid_arr.push(b.brandid);
			});
		}
		 $('.select_brand').html(brands_html).trigger("liszt:updated");
		
		});
		
	}
	
});

var catid_arr=[];
$('.sel_brand').live('change',function(){
	
	var sel_bid=$(this).val();
	if(sel_bid!='0')
	{
		$('.show_category').show();
		//$('#load_brnd_catlink').show();
		$(".select_category").html('').trigger("liszt:updated");
		$.getJSON(site_url+'/admin/jx_load_allcatsbybrand/'+sel_bid,'',function(resp){
		var cat_html='';
		if(resp.status=='error')
		{
			alert(resp.message);
		}
		else
		{
			cat_html+='<option value=""></option>';
			cat_html+='<option value="0">All</option>';
			$.each(resp.cat_list,function(i,c){
			cat_html+='<option value="'+c.catid+'">'+c.cat_name+'</option>';
			catid_arr.push(c.catid);
			});
		}
		 $('.select_category').html(cat_html).trigger("liszt:updated");
		
		});
		
	}
	
});

$('#venfrm').submit(function()
{
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

	if(!$('.v_lbtable tbody tr',this).length)
	{
		alert("Link atlease one brand for this vendor");
		return false;
	}
	var payment_error_status=0;

	var payment_type=$('select[name="payment_type"]').val()*1;
	var credit_days =$('input[name="credit_days"]').val()*1;
	var credit_limit = $('input[name="credit_limit"]').val()*1;

	if(payment_type==0)
	{
		payment_error_status=1;
		$('select[name="payment_type"]').addClass('error_inp');
	}

	if(isNaN(credit_days))
	{
		payment_error_status=1;
		$('input[name="credit_days"]').addClass('error_inp');
	}
	if(isNaN(credit_limit))
	{
		payment_error_status=1;
		$('input[name="credit_limit"]').addClass('error_inp');
	}
	if(payment_error_status)
	{
		$('.error_inp:first').focus();
		alert("Invalid Finance Details");
		return false;
	}
	var bm_error_status = 0;
	$('.edit_vblink_chk:checked').each(function(){
		var $r = $(this).parents('tr:first');
		var margin=$('input[name="l_margin[]"]',$r).val()*1;
		var appl_frm=$('input[name="l_from[]"]',$r).val();
		var appl_to=$('input[name="l_until[]"]',$r).val();
		
			if(isNaN(margin))
			{
				bm_error_status = 1;
				$('input[name="l_margin[]"]',$r).addClass('error_inp');
			}else if(margin == 0)
			{
				bm_error_status = 1;
				$('input[name="l_margin[]"]',$r).addClass('error_inp');
			}

			if(appl_frm == "")
			{
				bm_error_status = 1;
				$('input[name="l_from[]"]',$r).addClass('error_inp');
			}
			if(appl_to == "")
			{
				bm_error_status = 1;
				$('input[name="l_until[]"]',$r).addClass('error_inp');
			}
			
	});

	if(bm_error_status)
	{
		$('.error_inp:first').focus();
		alert("Invalid Margin value and Dates selected");
		return false;
	}

});

var p_added=[],b_added=[];
var ven_id = '<?php echo $this->uri->segment(3);?>';
<?php if($v){foreach($brands as $b){?>
b_added.push(<?=$b['brand_id']?>);
<?php }}?>

//$('#load_brnd_catlink').click(function(){
function update_new_cat(bid)
{	
	var select_brand=$('.select_brand').val();
	var cat_id=$('.select_cat').val();
	var ven_id = '<?php echo $this->uri->segment(3);?>';
	if(select_brand == 0)
	{
		select_brand=bid;
	}
	$.post(site_url+'/admin/update_vendor_catgory_brand',{catid:cat_id,brandid:select_brand,vendorid:ven_id},function(resp){
		if(resp.status=="success")
		{
			$(this).attr('disabled',true);
			
			$.each(resp.brnd_cat_res,function(i,c){
				var template=''
							+"<tr>"
							+"<td><input type='checkbox' class='edit_vblink_chk' checked='checked' ></td>"
							+"<td><input type='hidden'  class='inp' name='l_brand[]' value='"+c.brandid+"'>"+c.brand_name+"</td>"
							+"<td><input type='hidden'  class='inp' name='l_catid[]' value='"+c.catid+"'>"+c.category_name+"</td>"
							+"<td><input type='text'   class='inp' name='l_margin[]' value=''></td>"
							+"<td><input type='text'   class='inp datepic lb_date' name='l_from[]' value='<?php echo date('Y-m-d') ?>' readonly='readonly'></td>"
							+"<td><input type='text'   class='inp datepic lb_date' name='l_until[]' value='<?php echo date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + 365 day")) ?>' readonly='readonly'></td>"
							+"<td><a href='javascript:void(0)' onclick='remove_catbrandvblink(this)' >remove</a>"
							+"</tr>"
				$(template).prependTo(".v_lbtable tbody");
				
			});

			$('.v_lbtable .lb_date').each(function(i,dpEle){
				if(!$(this).hasClass('hasDatepicker'))
					$(this).datepicker();
			});
			
		}else
		{
			alert(resp.msg);
			$(this).attr('disabled',false);
		}
	},'json');
}


function update_new_brand(cid){	
	var select_brand=$('.sel_brand').val();
	var cat_id=$('.select_category').val();
	var ven_id = '<?php echo $this->uri->segment(3);?>';
	if(cat_id == 0)
	{
		cat_id=cid;
	}
	$.post(site_url+'/admin/update_vendor_brand_category',{catid:cat_id,brandid:select_brand,vendorid:ven_id},function(resp){
		if(resp.status=="success")
		{
			$(this).attr('disabled',true);
			
			$.each(resp.brnd_cat_res,function(i,c){
				var template=''
							+"<tr>"
							+"<td><input type='checkbox' class='edit_vblink_chk' checked='checked' ></td>"
							+"<td><input type='hidden'  class='inp' name='l_brand[]' value='"+c.brandid+"'>"+c.brand_name+"</td>"
							+"<td><input type='hidden'  class='inp' name='l_catid[]' value='"+c.catid+"'>"+c.category_name+"</td>"
							+"<td><input type='text'   class='inp' name='l_margin[]' value=''></td>"
							+"<td><input type='text'   class='inp datepic lb_date' name='l_from[]' value='<?php echo date('Y-m-d') ?>' readonly='readonly'></td>"
							+"<td><input type='text'   class='inp datepic lb_date' name='l_until[]' value='<?php echo date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + 365 day")) ?>' readonly='readonly'></td>"
							+"<td><a href='javascript:void(0)' onclick='remove_catbrandvblink(this)' >remove</a>"
							+"</tr>"
				$(template).prependTo(".v_lbtable tbody");
				
			});

			$('.v_lbtable .lb_date').each(function(i,dpEle){
				if(!$(this).hasClass('hasDatepicker'))
					$(this).datepicker();
			});
			
		}else
		{
			alert(resp.msg);
			$(this).attr('disabled',false);
		}
	},'json');
}
	
$('.edit_vblink_chk').live('change',function(){
	var tds = $(this).parent().parent().find('td');
	if($(this).attr("checked"))
	{
		tds.addClass('selected');
		tds.find(".inp").attr("disabled",false);
		var catid_val=tds.find('input[name="l_catid[]"]').val();
		if(catid_val == 0)
			tds.find('#show_cat').show();
		else
			tds.find('#show_cat').hide();
	}else
	{
		tds.removeClass('selected');
		tds.find(".inp").attr("disabled",true);
		tds.find('#show_cat').hide();
		
	}
});

function remove_vblink(ele){
	
	var trEle = $(ele).parent().parent();
	if(ven_id)
	{
		
		//if(confirm("Want to remove "+$('td:eq(1)',trEle).text()+" from this vendor ?"))
			if(confirm("Are you sure want to remove?"))
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

function remove_catbrandvblink(ele)
{
	var trEle = $(ele).parent().parent();
	if(ven_id)
	{
		
		//if(confirm("Want to remove "+$('td:eq(2)',trEle).text()+" category" +$('td:eq(1)',trEle).text() +" Brand  from this vendor ?"))
		if(confirm("Are you sure want to remove ?"))
		{
			brand_id = $('input[name="l_brand[]"]',trEle).val();
			cat_id = $('input[name="l_catid[]"]',trEle).val();
			$.post(site_url+'/admin/jx_remove_vendor_catbrand_link','vendor_id='+ven_id+'&brand_id='+brand_id+'&cat_id='+cat_id,function(resp){
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

$(function(){
	
	$(".lb_date").each(function(){
		$(this).datepicker();
	});
	
	if(b_added.length==0)
	clone_vcnt();
	$("#v_lpsearch").keyup(function(){
		$.post("<?=site_url("admin/searchproducts")?>",{q:$(this).val()},function(data){
			$("#v_searchres").html(data).show();
		});
	});
	$("#v_lbsearch").keyup(function(){
		$.post("<?=site_url("admin/jx_searchcategory")?>",{q:$(this).val()},function(data){
			$("#v_searchresb").html(data).show();
		});
	}).focus(function(){
		if($("#v_searchresb").html().length!=0)
			$("#v_searchresb").show();
	});
	
	//var date='<?php echo date('Y-m-d') ?>';
	//$('.datepic').val(date);
});


function load_allcatsofbrand(brandid)
{
	$('#all_catdiv').data('bid',brandid).dialog('open');
}

$("#all_catdiv").dialog({
	modal:true,
	autoOpen:false,
	 width:'800',
	height:'400',
		open:function(){
			dlg = $(this);
			$('#brandid').val("");
			 $('#categories').html("").trigger("liszt:updated");
			
			$(".selected_brand b").val("");
			//var cat_tbl_html="";
			$('input[name="brandid"]').val(dlg.data('bid'));
			 $("#cat_brandtable tbody").html("");
			$.post(site_url+'/admin/getbrand_cat_details',{brandid:dlg.data('bid'),vendorid:'<?php echo $vendor_id ;?>'},function(resp){
			
				$('.selected_brand').val(resp.brand_name);
				
				if(resp.status == 'success')
				{
					$.each(resp.cat_list,function(i,c){
						
						var	cat_tbl_html =""
										+"<tr >"
										+"<td class='selected'><input type='checkbox' checked='checked' class='edit_vblink_chk'></td>"
										+"<td class='selected'><input type='hidden' class='inp' name='l_brand[]' value='"+c.brandid+"'>"+resp.brand_name+"</td>"
										+"<td class='selected'><input type='hidden'  class='inp' name='l_catid[]' value='"+c.catid+"'>"+c.cat_name+"</td>"
										+"<td class='selected'><input type='text'    class='inp' name='l_margin[]' value='"+resp.brand_margin+"'></td>"
										+"<td class='selected'><input type='text'  class='inp datepic from_date' name='l_from[]' value='"+resp.from_dt+"'readonly='readonly'></td>"
										+"<td class='selected'><input type='text'   class='inp datepic to_date' name='l_until[]' value='"+resp.to_dt+"' readonly='readonly'></td>"
										+"<td class='selected'><a href='javascript:void(0)' onclick='remove_catbrandvblink(this)' >remove</a>"
										+"</tr>"
										$("#cat_brandtable tbody").append(cat_tbl_html);
							 //$(cat_tbl_html).appendTo("#cat_brandtable tbody");	
									
						});

					$('#cat_brandtable .from_date').each(function(i,dpEle){
						if(!$(this).hasClass('hasDatepicker'))
							$(this).datepicker();
						
						if(!$('#cat_brandtable .to_date:eq('+i+')').hasClass('hasDatepicker'))
							$('#cat_brandtable .to_date:eq('+i+')').datepicker();
						
					});
					
				}
			},'json');
			},
			buttons:{
				'Submit':function(){
					var bm_error_status = 0;
					
					$('.edit_vblink_chk:checked').each(function(){
						var $r = $(this).parents('tr:first');
						var margin=$('input[name="l_margin[]"]',$r).val()*1;
						var appl_frm=$('input[name="l_from[]"]',$r).val();
						var appl_to=$('input[name="l_until[]"]',$r).val();
						
							if(isNaN(margin))
							{
								bm_error_status = 1;
								$('input[name="l_margin[]"]',$r).addClass('error_inp');
							}else if(margin == 0)
							{
								bm_error_status = 1;
								$('input[name="l_margin[]"]',$r).addClass('error_inp');
							}

							if(appl_frm == "")
							{
								bm_error_status = 1;
								$('input[name="l_from[]"]',$r).addClass('error_inp');
							}
							if(appl_to == "")
							{
								bm_error_status = 1;
								$('input[name="l_until[]"]',$r).addClass('error_inp');
							}
					});
					if(bm_error_status)
					{
						$('.error_inp:first').focus();
						alert("Invalid Margin value and Dates selected");
						return false;
					}
					else
					{
						$('#update_cat_margin').submit();
						$(this).dialog('close');
						
					}
				}
			}
});

$('.tab_view').tabs();

 

$(".close_btn").click(function() {

	if($("#filter_prods").is(':visible'))
	{
		$(".po_filter_head .close_btn").html("<img src='<?php echo IMAGES_URL?>acc_plus.png'>");
		$("#filter_prods").slideUp();	
	}else
	{
		$(".po_filter_head .close_btn").html("<img src='<?php echo IMAGES_URL?>acc_minus.png'>");
	    $("#filter_prods").slideDown();
	}
});

$(".close_brcbtn").click(function() {

	if($("#brnd_cat_fltrs").is(':visible'))
	{
		$(".po_br_cat_filter_head .close_brcbtn").html("<img src='<?php echo IMAGES_URL?>acc_plus.png'>");
		$("#brnd_cat_fltrs").slideUp();	
	}else
	{
		$(".po_br_cat_filter_head .close_brcbtn").html("<img src='<?php echo IMAGES_URL?>acc_minus.png'>");
	    $("#brnd_cat_fltrs").slideDown();
	}
});

if(location.hash)
{
	$('a[href="'+location.hash+'"]').trigger('click');	
}
</script>

<style>
.po_filters {
	background-color: rgb(223, 224, 240);
	 
  	padding:10px 0px;
	 
}
.po_head_blk
{
	color: #000000;
    font-size: 13px;
    font-weight: bold;
}
.po_filter_wrap2
{
	float: right;
	width:52%;
	margin-bottom:10px;
}

.po_filter_head
 {
    border-bottom: 1px solid #777777;
    padding: 7px 0 7px 12px;
}
.po_br_cat_filter_head
{
	border-bottom: 1px solid #777777;
    padding: 7px 0 7px 12px;
}

.close_btn {
    float: right;
    margin-right: 8px;
    cursor:pointer;
    color:#D50C0C;
    font-weight: bold;
    font-size: 11px;
}
#filter_prods{
	
}
h3.filter_heading { margin-bottom: 0px; margin-top: 0;width: 788px;font-size: 11px;}


/* .po_br_cat_filter_head {
    display: table;
    width: 99%;
    float: right;
    cursor:pointer;
    background-color: #DFE0F0;
    padding: 2px 7px 2px 6px;
} */
.close_brcbtn {
    float: right;
    margin-right: 8px;
    cursor:pointer;
    color:#D50C0C;
    font-weight: bold;
    font-size: 11px;
}
#brnd_cat_fltrs{
	padding:7px 10px 9px;
}


.error_inp{border:1px solid #cd0000 !important;}

.warn td{background: #FDD2D2 !important;}
</style>
<?php
