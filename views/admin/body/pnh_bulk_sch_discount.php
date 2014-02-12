<div class="container">
	<h2>Add Discounts for Franchises</h2>
	<div class="clear"></div>
	<form id="pnh_bulkschdisc_frm" method="post">
		<table cellpadding="10" width="100%">
			<tr>
				<td width="10%"><b>Discount Type</b></td>
				<td>
					<select name="bulk_schtype" id="bulk_schtype" style="width:150px;">
						<option value="1">Scheme Discount</option>
						<option value="3">IMEI Scheme</option>
						<option value="2">Super Scheme</option>
					</select>
				</td>
			</tr>
			<tr id="msch_type">
				<td><b>Credit Cash</b></td>
				<td><input type="text" name="mscheme_val" class="inp" size=6> in <label><input type="radio" name="mscheme_type" value=1 checked="checked">%</label> <label><input type="radio" name="mscheme_type" value=0>Rs</label></td>
			</tr>
			<tr id="credit_value">
				<td><b>Credit Value</b></td>
				<td><input type="text" name="credit_value"></td>
			</tr>
			<tr class="super_scheme">
				<td><b>Target Value</b></td>
				<td><input type="text" name="target_value"></td>
			</tr>
			<tr class="super_scheme">
				<td><b>Credit Percent</b></td>
				<td><input type="text" name="credit_prc" size="4">%</td>
			</tr>
			<tr id="sch_disc">
				<td><b>Discount</b></td>
				<td><input type="text" name="discount" value="1" size="4">%</td>
			</tr>
			<tr>
				<td><b>Menu</b></td>
				<td>
					<select name="menu" data-placeholder="Choose" id="choose_menu" style="min-width: 180px;">
						<option value="0">Choose</option>
					<?php foreach($this->db->query("select id,name from pnh_menu where status = 1 order by name asc")->result_array() as $menu){?>
					<option value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option>
					<?php }?>
					</select>
				</td>
			</tr>
			<tr>
				<td><b>Category</b></td>
				<td><select name="cat" id="select_cat" data-placeholder="Choose" style="width: 200px;" ></select></td>
			</tr>
			<tr>
				<td><b>Brand</b></td>
				<td><select name="brand" id="select_brand" data-placeholder="Choose" style="width: 200px;" ></select></td>
			</tr>
			<tr class="disc_ondeal">
				<td><b>Discount on Deal</b></td>
				<td><input type="checkbox" name="disc_ondeal" value="1"></td>
			</tr>
			<tr class="show_deal">
				<td><b>Deals</b></td>
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
				<td valign="center"><b>Franchises</b></td>
				<td>
		 	 		<div id="franchise_filter" align="right" style="overflow:hidden;padding:5px 3px;font-size: 11px;display: none;background: #DFDFDF;width:95.5%">
		 	 			<span class="type_fil_wrap fl_left">
							<b class="all">All</b>
							<b class="sch_allot">Scheme Alloted</b>
							<b class="sch_not_allot">Scheme Not Alloted</b>						
						</span>
						<span class="breadcrumb_wrap fl_left">
							<b id="menu_name"></b> >> <b id="cat_name"></b> >> <b id="brand_name"></b> 
						</span>
						<div class="fl_right" id="ter_twn_filter">
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
				<td><b>Apply From</b></td>
				<td><input type="text" class="inp" size="10" name="msch_applyfrm" id="m_applyfrm"></td>
			</tr>
			<tr>
				<td><b>Scheme Validity</b></td>
				<td> From <input type="text" class="inp" size="10" name="start" id="d_start"> To  <input type="text" class="inp" size="10" name="end" id="d_end"></td>
			</tr>
			<tr>
				<td><b>Reason</b></td>
				<td><textarea class="inp" name="reason" style="width:82%;height: 150px;"></textarea></td>
			</tr>
			<tr>
				<td><b>Expire previous Scheme</b></td>
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
		  background: none repeat scroll 0 0 #F7F7F7;
		    cursor: pointer;
		    float: left;
		    font-size: 11px;
		    height: 30px;
		    margin: 1px;
		    padding: 5px 1px;
		    vertical-align: top;
		    width: 24%;
	}
	
	.list-inlineblk span {
	    display: inline-block;
	    float: right;
	    margin-top: 1px;
	    text-align: left;
	    width: 88%;
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
$('#choose_menu,#bulk_schtype,#select_cat,#select_brand').chosen();
function select_all_franchises()
{
	$('#fran_list .list-inlineblk:visible input[name="fids[]"]').attr('checked',true);
	$('#fran_list .list-inlineblk:visible').addClass('selected');
}

function unselect_all_franchises()
{
	$('#fran_list .list-inlineblk:visible input[name="fids[]"]').attr('checked',false);
	$('#fran_list .list-inlineblk:visible ').removeClass('selected');
}


function select_all_deals()
{
	$('#deal_list .list-inlineblk:visible input[name="dealids[]"]').attr('checked',true);
	$('#deal_list .list-inlineblk:visible ').addClass('selected');
}
function unselect_all_deals()
{
	$('#deal_list .list-inlineblk:visible input[name="dealids[]"]').attr('checked',false);
	$('#deal_list .list-inlineblk:visible ').removeClass('selected');
}


$(function(){
	//prepare_daterange("d_start","d_end");
	$('#d_start').datepicker({minDate:0});
	$('#d_end').datepicker();
	$("#m_applyfrm").datepicker();
	$('#franchise_filter').hide();
	$('#fran_list').hide();
	$('.breadcrumb_wrap').hide();
});
 

$('.list-inlineblk').live('click',function(e){
	e.preventDefault();
	
	fr_chk_ele = $('input[type="checkbox"]',this);
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

$("#bulk_schtype").live('change',function(){
	$('#choose_menu').val("0").trigger('click');
});

$('#choose_menu').change(function(){
	var i = document.getElementById('choose_menu');
    var p = i.options[i.selectedIndex].text;
    $('#menu_name').html(p);
	var sel_terrid=$('#chose_terry').val();
	var sel_sch_type=$("#bulk_schtype").val();
	var sel_menuid=$(this).val();
	
		if($(this).val())
		{
			$('#select_cat').html('<option value="">Loading...</option>').trigger("lizst:updated");
			$.getJSON(site_url+'/admin/jx_load_allcatsbymenu/'+$(this).val(),'',function(resp){
			var cat_html='';
			if(resp.status =='error')
			{
				alert(resp.msg);
			}
			else
			{
				cat_html+='<option value=""></option>';
				//cat_html+='<option value="0">All</option>';
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
	var i = document.getElementById('select_cat');
    var p = i.options[i.selectedIndex].text;
    $('#cat_name').html(p);
	
	if($(this).val())
	{
		$("#select_brand").html("<option value=''>Loading...</option>").trigger("lizst:updated");
		$.getJSON(site_url+'/admin/jx_load_allbrandsbycat/'+$(this).val(),'',function(resp){
		var brand_html='';
		if(resp.status=='error')
		{
			alert(resp.msg);
		}
		else
		{
			brand_html+='<option value=""></option>';
			//brand_html+='<option value="0">All</option>';
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



$("select[name='fil_type']").change(function(){
	var sel_menu=$('#choose_menu').val();
	var tid=$("select[name='fil_territory']").val();
	var townid=$("select[name='fil_town']").val();
	
	if(tid && !townid)
	{	
		if($(this).val() == '1')
		{
			$('#fran_list .list-inlineblk.terr_'+tid).show();
			
		}else
		if($(this).val() == 3)
		{
			$('#fran_list .list-inlineblk').hide();
			$('#fran_list .list-inlineblk.terr_'+tid+'.sch_undefined').show();
		
		}else
		if($(this).val() == 2)
		{
			$('#fran_list .list-inlineblk').hide();
			$('#fran_list .list-inlineblk.terr_'+tid+'.sch_disc').show();
		}
	}else if(!tid && townid)
	{	
		if($(this).val() == '1')
		{
			$('#fran_list .list-inlineblk.twn_'+townid).show();
			
		}else
		if($(this).val() == 3)
		{
			$('#fran_list .list-inlineblk').hide();
			$('#fran_list .list-inlineblk.twn_'+townid+'.sch_undefined').show();
		
		}else
		if($(this).val() == 2)
		{
			$('#fran_list .list-inlineblk').hide();
			$('#fran_list .list-inlineblk.twn_'+townid+'.sch_disc').show();
		}
	}
	else if(tid && townid)
	{	
		if($(this).val() == '1')
		{
			$('#fran_list .list-inlineblk.terr_'+tid+'.twn_'+townid).show();
			
		}else
		if($(this).val() == 3)
		{
			$('#fran_list .list-inlineblk').hide();
			$('#fran_list .list-inlineblk.terr_'+tid+'.twn_'+townid+'.sch_undefined').show();
		
		}else
		if($(this).val() == 2)
		{
			$('#fran_list .list-inlineblk').hide();
			$('#fran_list .list-inlineblk.terr_'+tid+'.twn_'+townid+'.sch_disc').show();
		}
	}
	else
	{
		if($(this).val() == '1')
		{
			$('#fran_list .list-inlineblk').show();
			
		}else
		if($(this).val() == 3)
		{
			$('#fran_list .list-inlineblk').hide();
			$('.sch_undefined').show();
		
		}else
		if($(this).val() == 2)
		{
			$('#fran_list .list-inlineblk').hide();
			$('.sch_disc').show();
		
		}
	}
	
});
$('.all').live('click',function(){
	$('.type_fil_wrap b').removeClass("selected_type");
	$('.type_fil_wrap .all').addClass("selected_type");
	var tid=$("select[name='fil_territory']").val();
	var townid=$("select[name='fil_town']").val();
	if(tid && !townid)
	{
		$('#fran_list .list-inlineblk').hide();
		$('#fran_list .list-inlineblk.terr_'+tid).show();
	}else if(tid && townid)
	{
		$('#fran_list .list-inlineblk').hide();
		$('#fran_list .list-inlineblk.terr_'+tid+'.twn_'+townid).show();
	}else if(!tid && !townid)
	{
		$('#fran_list .list-inlineblk').hide();
		$('#fran_list .list-inlineblk').show();
	}
});
$('.sch_allot').live('click',function(){
	
	
	$('.type_fil_wrap b').removeClass("selected_type");
	$('.type_fil_wrap .sch_allot').addClass("selected_type");
	var tid=$("select[name='fil_territory']").val();
	var townid=$("select[name='fil_town']").val();
	if(tid && !townid)
	{
		
		$('#fran_list .list-inlineblk').hide();
		$('#fran_list .list-inlineblk.terr_'+tid+'.sch_disc').show();
	}else if(tid && townid)
	{
		$('#fran_list .list-inlineblk').hide();
		$('#fran_list .list-inlineblk.terr_'+tid+'.twn_'+townid+'.sch_disc').show();
	}else if(!tid && !townid)
	{
		$('#fran_list .list-inlineblk').hide();
		$('.sch_disc').show();
	}
});
$('.sch_not_allot').live('click',function(){
	$('.type_fil_wrap b').removeClass("selected_type");
	$('.type_fil_wrap .sch_not_allot').addClass("selected_type");
	
	var tid=$("select[name='fil_territory']").val();
	var townid=$("select[name='fil_town']").val();
	if(tid && !townid)
	{
		$('#fran_list .list-inlineblk').hide();
		$('#fran_list .list-inlineblk.terr_'+tid+'.sch_undefined').show();
	}else if(tid && townid)
	{
		$('#fran_list .list-inlineblk').hide();
		$('#fran_list .list-inlineblk.terr_'+tid+'.twn_'+townid+'.sch_undefined').show();
	}else if(!tid && !townid)
	{
		$('#fran_list .list-inlineblk').hide();
		$('.sch_undefined').show();
	}
});




$("select[name='fil_territory']").live('change',function(){
	var sel_menuid= $('#choose_menu').val();
	var tid=$(this).val();
	$('.type_fil_wrap b').removeClass("selected_type");
	$('.type_fil_wrap .all').addClass("selected_type");
	
	$("select[name='fil_town']").html('<option value="">Loading...</option>');
	if($(this).val() == '')
	{
		$('#fran_list .list-inlineblk').show();
		$("select[name='fil_town']").html('<option value="">Choose</option>');
	}else
	{
		$('#fran_list .list-inlineblk').hide();
		$('#fran_list .list-inlineblk.terr_'+tid).show();
		
		
		//get the towns by territory
		$.post(site_url+'/admin/get_franchisebymenu_id/'+sel_menuid+'/'+tid ,function(resp){
			var terr_linkedtwn_html='';
			if(resp.status=='errorr')
			{
				alert(resp.message);
			}
			else
			{
				terr_linkedtwn_html +='<option value="">Choose</option>';
				
				$.each(resp.menu_fran_list,function(i,itm){
					if(!$('select[name="fil_town"] option#town_'+itm.town_id).length){
						terr_linkedtwn_html +='<option value="'+ itm.town_id+'">'+itm.town_name+'</option>';
						if(itm.town_id!=undefined){
							terr_linkedtwn_html +=$('select[name="fil_town"]').append('<option id="town_'+itm.town_id+'" value="'+itm.town_id+'">'+itm.town_name+'</option>');
						}
					}
				});
			}
			$("select[name='fil_town']").html(terr_linkedtwn_html);
		},'json');
	}
});

$("select[name='fil_town']").live('change',function(){

	var twn=$(this).val();
	var tid=$("select[name='fil_territory']").val();
	$('.type_fil_wrap b').removeClass("selected_type");
	$('.type_fil_wrap .all').addClass("selected_type");
	
	if($(this).val() == '')
	{
		$("select[name='fil_territory']").trigger('change');
	}else
	{
		$('#fran_list .list-inlineblk').hide();
		$('#fran_list .list-inlineblk.terr_'+tid+'.twn_'+twn).show();
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
		$('#deal_list').html('<h3 align="center"><b>Loading Deal List,Please wait...</b></h3>');
		$.getJSON(site_url+'/admin/jx_to_getdeals_bybrandcatmenu/'+sel_menu+'/'+sel_brand+'/'+sel_cat ,function(resp){
			if(resp.status=='errorr')
			{
				$('#deal_list').html(resp.message);
			}
			else
			{
				var menudeallist_html='';
				$.each(resp.deal_list,function(i,itm){
				
					menudeallist_html+='<div class="list-inlineblk"><input type="checkbox" name="dealids[]" value="'+itm.id+'":checked > <span class="'+(itm.is_sourceable=="0"?'error_txt':'')+'">'+itm.name+'<br /> [DP Price : <b>'+itm.price+'</b>]</span></div>';
					$('#deal_list').html(menudeallist_html);
				});
			}
	
			$('#deal_list').html(menudeallist_html);
		});
	}
	
});

$("#select_brand").change(function(){
	$("select[name='fil_territory']").trigger('change');
	$("select[name='fil_town']").trigger('change');
	
	var i = document.getElementById('select_brand');
    var p = i.options[i.selectedIndex].text;
    var sel_sch_type=$("#bulk_schtype").val();
    var sel_menuid=$('#choose_menu').val()*1;
    var sel_brand =$(this).val()*1;
	var sel_cat=$('#select_cat').val()*1;
    $('#brand_name').html(p);
	$('.breadcrumb_wrap').show();
	
	$('#fran_list').html('<h3 align="center"><b>Loading Franchise List,Please wait...</b></h3>');
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

				
			}
		$('#fran_list').html(menufranchiselist_html);
		$.getJSON(site_url+'/admin/jx_check_has_scheme/'+sel_menuid+'/'+sel_cat+'/'+sel_brand+'/'+sel_sch_type,function(resp){
			if(resp.status=='success')
			{
				if(resp.has_sch_disc.length != 0)
				{
					$.each(resp.has_sch_disc,function(i,itm){
						$('#fran_list .list-inlineblk.fid_'+itm.franchise_id).addClass('sch_disc');
						$('#fran_list .list-inlineblk.fid_'+itm.franchise_id).show();
					});
						
				}else
				{
					$('#fran_list .list-inlineblk').removeClass('sch_disc');
				}
			}
			$('#franchise_filter').show();
			$('#fran_list').show();
		});
	});
	
	
	
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
.type_fil_wrap b
{
	  background: none repeat scroll 0 0 #FDFDFD;
    float: left;
    margin: 0 2px;
    padding: 4px 19px;
    cursor:pointer;
}
.selected_type
{
	 background: none repeat scroll 0 0 #FF0000 !important;
	 color:#fff !important;
}
.breadcrumb_wrap
{
	padding:4px;
}
.breadcrumb_wrap b
{
	color: #777777;
    font-size: 9px;
    padding: 0;
    
}
</style>
<?php
