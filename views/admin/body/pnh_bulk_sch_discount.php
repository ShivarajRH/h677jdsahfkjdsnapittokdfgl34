<div class="container">

<h2>Scheme Discounts for franchises</h2>

<form id="pnh_bulkschdisc_frm" method="post">
<table cellpadding="10">
<tr><td width="15%">Menu</td><td><select name="menu" data-placeholder="Choose" id="choose_menu" style="min-width: 180px;">

<option value=""></option>
<?php foreach($this->db->query("select id,name from pnh_menu where status = 1 order by name asc")->result_array() as $menu){?>
<option value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option>
<?php }?>
</select></td></tr>


<tr><td  valign="center">Franchises</td>
<td>
<div style="clear:both">
<div id="fran_list"></div>
<div class="clear"></div>
<div style="padding-top:5px;">select : <input onclick='select_all_franchises()' type="button" value="All"> <input onclick='unselect_all_franchises()' type="button" value="None"></div>
</div>
<div class="clear"></div>
</td>
</tr>
<tr><td>Scheme Type</td>
<td><select name="bulk_schtype" id="bulk_schtype" style="width:150px;">
<option value="1">Scheme Discount</option>
<option value="2">Super Scheme</option>
<option value="3">Member Scheme</option>
</select></td></tr>
<tr id="msch_type"><td>Credit Cash</td><td><input type="text" name="mscheme_val" class="inp" size=6> in <label><input type="radio" name="mscheme_type" value=1 checked="checked">%</label> <label><input type="radio" name="mscheme_type" value=0>Rs</label></td></tr>
<tr id="credit_value"><td>Credit Value</td><td><input type="text" name="credit_value"></td></tr>
<tr class="super_scheme">
<td>Target Value</td><td><input type="text" name="target_value"></td></tr>
<tr class="super_scheme"><td>Credit Percent</td><td><input type="text" name="credit_prc" size="4">%</td></tr>



<tr id="sch_disc">
<Td>Scheme Discount </td>
<td><input type="text" name="discount" value="1" size="4">%
</td></tr>
<tr><td>Category </td><td><select name="cat" id="select_cat" data-placeholder="Choose" style="width: 200px;" ></select></td></tr>
<tr><td>Brand </td><td><select name="brand" id="select_brand" data-placeholder="Choose" style="width: 200px;" ></select></td></tr>

<tr><td>Scheme Validity</td><td> From <input type="text" class="inp" size="10" name="start" id="d_start"> To  <input type="text" class="inp" size="10" name="end" id="d_end"></td></tr>
<tr id="msch_applyfrm"><td>Apply From</td><td><input type="text" class="inp" size="10" name="msch_applyfrm" id="m_applyfrm"></td></tr>
<tr><td>
Reason </td><td><textarea class="inp" name="reason" style="width:500px;height: 150px;"></textarea></td></tr>
<tr><td>Expire previous Scheme</td><td><input type="checkbox" value="1" name="expire_prevsch" checked></td></tr>
<tr><td></td><td><input type="submit" value="Add Scheme discount"></td></tr>
</table>
</form>

</div>

<style>
	.list-inlineblk{
		white-space: nowrap;
		margin-bottom: 3px;
		margin-right: 3px;
		float: left;
		padding: 3px;
		background: #f7f7f7;
		cursor: pointer;
		width: 293px;
		font-size: 12px;
		overflow: hidden;
		vertical-align: top;
		padding: 5px;
		height: 20px;
		margin:1px;
	}
	.list-inlineblk span{
		display: inline-block;
	}
	.list-inlineblk.selected{
		background: #ffffD0;
	}
	.list-inlineblk span.error_txt{
		color:#cd0000;
	}
</style>

<script>

function select_all_franchises()
{
	$('.list-inlineblk input[name="fids[]"]').attr('checked',true);
	$('.list-inlineblk').addClass('selected');
}

function unselect_all_franchises()
{
	$('.list-inlineblk input[name="fids[]"]').attr('checked',false);
	$('.list-inlineblk').removeClass('selected');
}

$('#choose_menu,#bulk_schtype,#select_cat,#select_brand').chosen();
$(function(){
	$("#d_start,#d_end").datepicker();
	$("#m_applyfrm").datepicker();
});


$('.list-inlineblk').live('click',function(e){
	e.preventDefault();
	
	fr_chk_ele = $('input[name="fids[]"]',this);
	if(fr_chk_ele.attr('checked'))
	{
		fr_chk_ele.attr('checked',false);
		$(this).removeClass('selected');
	}else
	{
		fr_chk_ele.attr('checked',true);
		$(this).addClass('selected');
	}
});


$('input[name="fids[]"]').live('change',function(e){
	e.preventDefault();
});


$('#pnh_bulkschdisc_frm').submit(function(){
	var error_inp = new Array();
	
	if(!$('select[name="menu"]',this).val())
	{
		error_inp.push("Please Choose menu ");
		
	}
	if(!$('input[name="expire_prevsch"]:checked',this).length)
	{
		error_inp.push("Please check to expire previous schemes");
	}
	
	if(!$('input[name="fids[]"]:checked',this).length)
	{
		error_inp.push("Please Choose atleast one franchise");
	}
	
	var sch_disc = $('input[name="discount"]',this).val();
		$('input[name="discount"]',this).val($.trim(sch_disc));
		sch_disc = $.trim(sch_disc)*1;
		if(isNaN(sch_disc))
		{
			error_inp.push("Invalid Discount Entered");
		}else if(sch_disc > 10)
		{
			error_inp.push("Maximum 10% discount is allowed");
		}
	var is_valid_daterange = 1;	
		if(!$('#d_start',this).val().length)
			is_valid_daterange = 0;
		if(!$('#d_end',this).val().length)
			is_valid_daterange = 0;
			
		if(!is_valid_daterange)
		{
			error_inp.push("Scheme Validity is required");
		}
		
	if(error_inp.length)
	{
		alert(error_inp.join("\n"));
		return false;
	}		
	
	if(!confirm("Are you sure want to give this scheme discount"))
	{
		return false; 
	}
});

$('#choose_menu').change(function(){
	var sel_terrid=$('#chose_terry').val();
	var sel_menuid=$(this).val();
	
		$('#fran_list').html('').trigger('liszt:updated');
		$.getJSON(site_url+'/admin/get_franchisebymenu_id/'+sel_menuid+'',function(resp){
			if(resp.status=='errorr')
			{
				$('#fran_list').html(resp.message);
			}
			else
			{
				var menufranchiselist_html='';
				$.each(resp.menu_fran_list,function(i,itm){
					menufranchiselist_html+='<div class="list-inlineblk"><input type="checkbox" name="fids[]" value="'+itm.fid+'":checked > <span class="'+(itm.is_suspended!="0"?'error_txt':'')+'">'+itm.franchise_name+'</span></div>';
					$('#fran_list').html(menufranchiselist_html);
				});
			}
	
			$('#fran_list').html(menufranchiselist_html).trigger('liszt:updated');
			$('#fran_list').trigger('change');
		});
});
$('#choose_menu').change(function(){
	
	if($(this).val())
	{
		$('#select_cat').html("").trigger("lizst:updated");
		$.getJSON(site_url+'/admin/jx_load_allcatsbymenu/'+$(this).val(),'',function(resp){
		var cat_html='';
		if(resp.status=='error')
		{
			alert(resp.msg);
		}
		else
		{
			cat_html+='<option value=""></option>';
			cat_html+='<option value="0">All</option>';
			$.each(resp.cat_list,function(i,b){
			cat_html+='<option value="'+b.catid+'">'+b.name+'</option>';
			});
		}
			$('#select_cat').html(cat_html).trigger("liszt:updated");
		 	$('#select_cat').trigger('change');
		});
		
	}
});

$('#select_cat').change(function(){

	if($(this).val())
	{
		$("#select_brand").html("").trigger("lizst:updated");
		$.getJSON(site_url+'/admin/jx_load_allbrandsbycat/'+$(this).val(),'',function(resp){
		var brand_html='';
		if(resp.status=='error')
		{
			alert(resp.msg);
		}
		else
		{
			brand_html+='<option value=""></option>';
			brand_html+='<option value="0">All</option>';
			$.each(resp.brand_list,function(i,b){
				brand_html+='<option value="'+b.brandid+'">'+b.name+'</option>';
			});
		}
		$('#select_brand').html(brand_html).trigger("liszt:updated");
	 	$('#select_brand').trigger('change');

		});
	}
});
$('#bulk_schtype').change(function(){
	var schtype=$(this).val();
	//alert($(this).val());
	if(schtype==3)
	{
		
		$('#msch_type').show();
		$('#credit_value').hide();
		$('#sch_disc').hide();
		$('#msch_applyfrm').show();
		$('.super_scheme').hide();
	}
	if(schtype==2)
	{
		$('#msch_type').hide();
		$('#sch_disc').hide();
		$('#credit_value').hide();
		$('#msch_applyfrm').hide();
		$('.super_scheme').show();
	}
	else if(schtype==1)
	{
		$('#sch_disc').show();
		$('#msch_type').hide();
		$('#credit_value').hide();
		$('#msch_applyfrm').hide();
		$('.super_scheme').hide();
	}
});

</script>
<style>
#msch_type,#credit_value,#msch_applyfrm,.super_scheme
{display: none;}
</style>
<?php
