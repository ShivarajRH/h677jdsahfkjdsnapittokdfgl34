<div class="container">
	<h2>Add Discounts for Franchises</h2>
	<div class="clear"></div>
	<form id="pnh_bulkschdisc_frm" method="post">
		<table cellpadding="10">
			<tr>
				<td>Discount Type</td>
				<td>
					<select name="bulk_schtype" id="bulk_schtype" style="width:150px;">
						<option value="1">Scheme Discount</option>
						<option value="3">IMEI Scheme</option>
						<option value="2">Super Scheme</option>
					</select>
				</td>
			</tr>
			<tr id="msch_type">
				<td>Credit Cash</td>
				<td><input type="text" name="mscheme_val" class="inp" size=6> in <label><input type="radio" name="mscheme_type" value=1 checked="checked">%</label> <label><input type="radio" name="mscheme_type" value=0>Rs</label></td>
			</tr>
			<tr id="credit_value">
				<td>Credit Value</td>
				<td><input type="text" name="credit_value"></td>
			</tr>
			<tr class="super_scheme">
				<td>Target Value</td>
				<td><input type="text" name="target_value"></td>
			</tr>
			<tr class="super_scheme">
				<td>Credit Percent</td>
				<td><input type="text" name="credit_prc" size="4">%</td>
			</tr>
			<tr id="sch_disc">
				<td>Discount </td>
				<td><input type="text" name="discount" value="1" size="4">%</td>
			</tr>
			<tr>
				<td width="15%">Menu</td>
				<td>
					<select name="menu" data-placeholder="Choose" id="choose_menu" style="min-width: 180px;">
						<option value=""></option>
					<?php foreach($this->db->query("select id,name from pnh_menu where status = 1 order by name asc")->result_array() as $menu){?>
					<option value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option>
					<?php }?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Category </td>
				<td><select name="cat" id="select_cat" data-placeholder="Choose" style="width: 200px;" ></select></td>
			</tr>
			<tr>
				<td>Brand </td>
				<td><select name="brand" id="select_brand" data-placeholder="Choose" style="width: 200px;" ></select></td>
			</tr>
			<tr class="disc_ondeal">
				<td><b>Discount on Deal</b></td>
				<td><input type="checkbox" name="disc_ondeal" value="1"></td>
			</tr>
			<tr class="show_deal">
				<td>Deals</td>
				<td>
					<div style="clear: both">
						<div id="deal_list"></div>
						<div class="clear"></div>
						<div style="padding-top: 5px;">
							select : <input onclick='select_all_deals()' type="button"
								value="All"> <input onclick='unselect_all_deals()' type="button"
								value="None">
						</div>
					</div>
					<div class="clear"></div>
				</td>
			</tr>
			
			
			<tr>
				<td valign="center">Franchises</td>
				<td>
		 	 		<div id="franchise_filter" align="right" style="overflow:hidden;padding:5px 10px;font-size: 11px;display: none;background: #DFDFDF;margin-right:179px;">
		 	 			<span class="fl_left">
							<b>Type&nbsp;</b>
							<select name="fil_type"  style="font-size: 11px;"><option value="">All</option><option value="1">Scheme Not Alloted</option></select>
						</span>
						<div class="fl_right">
							<span>
								<b>Territory&nbsp;</b> <select name="fil_territory" style="font-size: 11px;"><option value="">All</option></select> 
							</span> &nbsp; 
							<span>
								<b>Town&nbsp; </b> 
								<select name="fil_town"  style="font-size: 11px;"><option value="">All</option></select>
							</span>
						</div> 
					</div>
					<div style="clear:both">
						<div id="fran_list"></div>
						<div class="clear"></div>
						<div style="padding-top:5px;">select : <input onclick='select_all_franchises()' type="button" value="All"> <input onclick='unselect_all_franchises()' type="button" value="None"></div>
					</div>
					<div class="clear"></div>
				</td>
			</tr>
			<tr id="msch_applyfrm">
				<td>Apply From</td>
				<td><input type="text" class="inp" size="10" name="msch_applyfrm" id="m_applyfrm"></td>
			</tr>
			<tr>
				<td>Scheme Validity</td>
				<td> From <input type="text" class="inp" size="10" name="start" id="d_start"> To  <input type="text" class="inp" size="10" name="end" id="d_end"></td>
			</tr>
			<tr>
				<td>Reason </td>
				<td><textarea class="inp" name="reason" style="width:500px;height: 150px;"></textarea></td>
			</tr>
			<tr>
				<td>Expire previous Scheme</td>
				<td><input type="checkbox" value="1" name="expire_prevsch" checked></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="Add Scheme discount"></td>
			</tr>
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
		/*width:350px;*/
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
		.show_deal{display:none;}
</style>

<script>

function select_all_franchises()
{
	$('.list-inlineblk input[name="fids[]"]').attr('checked',true);
	$('.list-inlineblk ').addClass('selected');
}

function unselect_all_franchises()
{
	$('.list-inlineblk input[name="fids[]"]').attr('checked',false);
	$('.list-inlineblk ').removeClass('selected');
}


function select_all_deals()
{
	$('.list-inlineblk input[name="dealids[]"]').attr('checked',true);
	$('.list-inlineblk ').addClass('selected');
}
function unselect_all_deals()
{
	$('.list-inlineblk input[name="dealids[]"]').attr('checked',false);
	$('.list-inlineblk ').removeClass('selected');
}

$('#choose_menu,#bulk_schtype,#select_cat,#select_brand').chosen();
$(function(){
	prepare_daterange("d_start","d_end");
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

	deal_chk_ele=$('input[name="dealids[]"]',this);
	if(deal_chk_ele.attr('checked'))
	{
		deal_chk_ele.attr('checked',false);
		$(this).removeClass('selected');
	}else
	{
		deal_chk_ele.attr('checked',true);
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
	if(!$('select[name="cat"]',this).val())
	{
		error_inp.push("Please Choose Category ");
		
	}
	if(!$('select[name="brand"]',this).val())
	{
		error_inp.push("Please Choose Brand ");
		
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
	var sel_sch_type=$("#bulk_schtype").val();
	var sel_menuid=$(this).val();
		$('#franchise_filter').hide();
		$('#fran_list').html('').trigger('liszt:updated');
		$.getJSON(site_url+'/admin/get_franchisebymenu_id/'+sel_menuid+'/0'+'/0/'+sel_sch_type,function(resp){
			if(resp.status=='error')
			{
				$('#fran_list').html(resp.message);
				
			}
			else
			{
				var menufranchiselist_html='';
				$.each(resp.menu_fran_list,function(i,itm){
						
					if($.inArray(itm.fid,resp.has_nosch)!=-1)
					{ var no_schfid=itm.fid;}

					
					
					
					menufranchiselist_html+='<div class="list-inlineblk fid_'+itm.fid+' terr_'+itm.territory_id+' twn_'+itm.town_id+' sch_'+no_schfid+' "  ><input type="checkbox" class="terr_'+itm.territory_id+'" name="fids[]" value="'+itm.fid+'":checked > <span class="'+(itm.is_suspended!="0"?'error_txt':'')+'">'+itm.franchise_name+'</span></div>';
					
					$('#fran_list').html(menufranchiselist_html);
					if(!$('select[name="fil_territory"] option#territory_'+itm.territory_id).length){
						if(itm.territory_id!=undefined){
							$('select[name="fil_territory"]').append('<option id="territory_'+itm.territory_id+'" value="'+itm.territory_id+'">'+itm.territory_name+'</option>');
						}
					}

					if(!$('select[name="fil_town"] option#town_'+itm.town_id).length){
						if(itm.territory_id!=undefined){
							$('select[name="fil_town"]').append('<option id="town_'+itm.town_id+'" value="'+itm.town_id+'">'+itm.town_name+'</option>');
						}
					}
				});

				$('#franchise_filter').show();
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
		if(resp.status =='error')
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
				$('#fran_list').trigger('change');
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
		$('.disc_ondeal').hide();
	}
	if(schtype==2)
	{
		$('#msch_type').hide();
		$('#sch_disc').hide();
		$('#credit_value').hide();
		$('#msch_applyfrm').hide();
		$('.super_scheme').show();
		$('.disc_ondeal').hide();
	}
	else if(schtype==1)
	{
		$('#sch_disc').show();
		$('#msch_type').hide();
		$('#credit_value').hide();
		$('#msch_applyfrm').hide();
		$('.super_scheme').hide();
		$('.disc_ondeal').show();
	}
});

$(".close_filters").toggle(function() {
    $(".close_filters .close_btn").html("Hide");
    $("#filter_prods").slideDown();

},function() {
    $("#filter_prods").slideUp();
    
    $(".close_filters .close_btn").html("Show");
   
});

$("select[name='fil_territory']").live('change',function(){
	var sel_menuid= $('#choose_menu').val();
	var tid=$(this).val();
	$("select[name='fil_town']").html('').trigger('liszt:updated');
	if($(this).val() == '')
	{
		$('.list-inlineblk').show();
	}else
	{
		$('.list-inlineblk').hide();
		$('.list-inlineblk.terr_'+tid).show();

		//get the towns by territory
		$.post(site_url+'/admin/get_franchisebymenu_id/'+sel_menuid+'/'+tid ,function(resp){
			var terr_linkedtwn_html='';
			if(resp.status=='errorr')
			{
				alert(resp.message);
			}
			else
			{
				terr_linkedtwn_html +='<option value=" ">Choose</option>';
				
				$.each(resp.menu_fran_list,function(i,itm){
					


					if(!$('select[name="fil_town"] option#town_'+itm.town_id).length){
						terr_linkedtwn_html +='<option value="'+ itm.town_id+'">'+itm.town_name+'</option>';
						if(itm.town_id!=undefined){
							terr_linkedtwn_html +=$('select[name="fil_town"]').append('<option id="town_'+itm.town_id+'" value="'+itm.town_id+'">'+itm.town_name+'</option>');
						}
					}
				});
			}
			$("select[name='fil_town']").html(terr_linkedtwn_html).trigger('liszt:updated');
		},'json');
	}
});

$("select[name='fil_town']").live('change',function(){

	var twn=$(this).val();
	if($(this).val() == '')
	{
		$('.list-inlineblk').show();
	}else
	{
		$('.list-inlineblk').hide();
		$('.list-inlineblk.twn_'+twn).show();
	}
});


$("select[name='fil_type']").change(function(){
	var sel_menu=$('#choose_menu').val();
	if($(this).val() == '')
	{
		$('.list-inlineblk').show();
		
	}else
	if($(this).val() == 1)
	{
		$('.list-inlineblk').hide();
		$('.sch_undefined').show();
	
	}
	
});

$('.disc_ondeal').live('click',function(){

	if ($('input[name="disc_ondeal"]:checked').length == 0) 
	{
		$('.show_deal').hide();
	}else
	{
		var sel_menu=$('#choose_menu').val()*1;
		var sel_brand=$('#select_brand').val()*1;
		var sel_cat=$('#select_cat').val()*1;
			
			if(!$('select[name="menu"]').val())
			{
				alert("Please Choose Menu");
				return false;
			}
			
			if(!$('select[name="cat"]').val() || $('select[name="cat"]').val()==0)
			{
				alert("Please Choose Category");
				return false;
			}
			if(!$('select[name="brand"]').val() || $('select[name="brand"]').val()==0)
			{
				alert("Please Choose Brand");
				return false;
			}
		
		$('#sourceble_filter').show();
		$('.show_deal').show();
		$('#deal_list').html('').trigger('liszt:updated');
		$.getJSON(site_url+'/admin/jx_to_getdeals_bybrandcatmenu/'+sel_menu+'/'+sel_brand+'/'+sel_cat ,function(resp){
			if(resp.status=='errorr')
			{
				$('#deal_list').html(resp.message);
			}
			else
			{
				var menudeallist_html='';
				$.each(resp.deal_list,function(i,itm){
				
					menudeallist_html+='<div class="list-inlineblk"><input type="checkbox" name="dealids[]" value="'+itm.id+'":checked > <span class="'+(itm.is_sourceable=="0"?'error_txt':'')+'">'+itm.name+'</span></div>';
					$('#deal_list').html(menudeallist_html);
				});
			}
	
			$('#deal_list').html(menudeallist_html).trigger('liszt:updated');
			$('#deal_list').trigger('change');
		});
	}
	
});

$("#select_brand").change(function(){
	$('#fran_list').trigger('change');
	if($(this).val()!='')
	{
		var sel_menu=$('#choose_menu').val()*1;
		var sel_brand =$(this).val()*1;
		var sel_cat=$('#select_cat').val()*1;
		var sch_type=$('#bulk_schtype').val()*1;
		$.getJSON(site_url+'/admin/jx_check_has_scheme/'+sel_menu+'/'+sel_cat+'/'+sel_brand+'/'+sch_type,function(resp){
			if(resp.status=='success')
			{
				$.each(resp.has_sch_disc,function(i,itm){
				$('.list-inlineblk.fid_'+itm.franchise_id).addClass('sch_disc');
				});
			}

			});
	}
});
</script>
<style>
#msch_type,#credit_value,#msch_applyfrm,.super_scheme,.leftcont
{display: none;}
.sch_disc
{background-color:#AAFFAA;}
.nombrsch
{background-color:#FFAAAA;}
.nosupersch
{background-color:#DCEBF9;}

</style>
<?php
