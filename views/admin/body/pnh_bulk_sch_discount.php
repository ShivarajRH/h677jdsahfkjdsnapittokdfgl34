<style>
	#franchise_det{overflow: auto;height: 408px;}
	.list-inlineblk{
			background: none repeat scroll 0 0 #F7F7F7;
		    cursor: pointer;
		    float: left;
		    font-size: 12px;
		    font-weight: 600;
		    height: 26px;
		    margin: 2px;
		    padding: 6px 5px 5px 2px;
		    vertical-align: top;
		    width: 19%;
	}
	.deallist-inlineblk{
		    background: none repeat scroll 0 0 #F7F7F7;
		    cursor: pointer;
		    float: left;
		    font-size: 12px;
		    font-weight: bold;
		    height: 70px;
		    margin: 2px;
		    padding: 4px 5px;
		    width: 32%;
	}
	.deallist-inlineblk span {
	    display: inline-block;
	    float: right;
	    margin-top: 1px;
	    text-align: left;
	    width: 88%;
	}
	.deallist-inlineblk .sel_deal_disc_type {
	     	float: right;
		    margin: 0 5px;
		    width: 18%;
	}
	.deallist-inlineblk .sel_deal_disc_val {
	     	float: right;
		    padding: 3px;
		    width: 26%;
	}
	#franchise_filter
	{
		overflow:hidden;padding:5px 3px;font-size: 11px;background: #DFDFDF;
	}
	.list-inlineblk.selected{
		background: #ffffD0;
	}
	.deallist-inlineblk.selected{
		background: #ffffD0;
	}
	.deallist-inlineblk span.error_txt{
		color:#cd0000;
	}
	.list-inlineblk span.error_txt{
		color:#cd0000;
	}
	.show_deal{display:none;}
	#msch_type,#credit_value,.super_scheme,.leftcont
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
	.highlight_option
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
	.add_row
	{
		cursor:pointer;
	}
	.remove_row
	{
		cursor:pointer;
	}
	#select_filter
	{
		margin-left: 30px;
	}
	#select_filter a
	{
		background: none repeat scroll 0 0 #FDFDFD;
	    float: right;
	    font-size: 11px;
	    font-weight: bold;
	    margin: 0 3px;
	    padding: 4px;
	    text-decoration:none;
	    cursor:pointer;
	}
</style>
<div class="container">
	<h2>Add Discounts for Franchises</h2>
	<div class="clear"></div>
	<form id="pnh_bulkschdisc_frm" method="post">
		<table cellpadding="10" width="100%">
			<tr>
				<td width="10%"><b>Discount Type</b></td>
				<td>
					<select name="bulk_schtype" id="bulk_schtype" style="width:150px;">
						<option col_text="Discount" col_title="Deals" col_extra="" value="1">Scheme Discount</option>
						<option col_text="Credit Cash" col_title="" col_extra=""  value="3">IMEI Scheme</option>
						<option col_text="Target Value" col_title="" col_extra="Credit Prec." value="2">Super Scheme</option>
					</select>
				</td>
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
				<td><b>Configure</b></td>
				<td>
					 <div id="cat_brand_input_blk">
						<table class="datagrid" cellpadding="0" cellspacing="0">
							<thead>
								<tr><th>Sl.no</th><th>Category</th><th>Brand</th><th>Type</th><th class="disc_text">Discount</th><th class="extra_col"></th><th>Franchises</th><th class="col_head">Deals</th><th>&nbsp;</th></tr>
							</thead>
							<tbody id="brand_group">
								<tr>
									<td><span>1</span></td>
									<td><select name="category[]" class="select_cat" data-placeholder="Choose" style="width: 200px;"></select></td>
									<td><select name="brand[]" class="select_brand" data-placeholder="Choose" style="width: 200px;" ></select></td>
									<td><select name="disc_type[]" class="disc_type" data-placeholder="Choose" style="width: 50px;" >
											<option value="1">%</option>
											<option value="0">Rs</option>
										</select>
									</td>
									<td><input type="text" name="disc_val[]" value="0" size="10"></td>
									<td>
										<input type="text" class="ext_input" name="scheme_val[]" value="0" size="10">
										<select name="mscheme_type[]" class="mscheme_type" data-placeholder="Choose" style="width: 50px;" >
											<option value="1">%</option>
											<option value="0">Rs</option>
										</select>
									</td>
									<td>
										<input type="hidden" class="sel_fids" name="fran_ids[]" value="" >
										<a href="javascript:void(0)" onclick="sel_fran_btn(this)" class="button button-tiny button-action sel_fran_btn">Choose</a>
										<span class="franch_selected"></span>
									</td>
									<td class="deals_td">
										<input type="hidden" class="sel_dealids" name="deal_ids[]" value="" >
										<a href="javascript:void(0)"  onclick="sel_deal_btn(this)" class="button button-tiny button-action sel_deal_btn">Choose</a>
										<span class="deals_selected"></span>
									</td>
									<td><div class="button button-tiny row_addedit_btn" >+</div></td>
								</tr>
							</tbody> 
						</table>
					</div>	
				</td>
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
				<td><b>Scheme Validity</b></td>
				<td> From <input type="text" class="inp" size="10" name="start" id="d_start"> To  <input type="text" class="inp" size="10" name="end" id="d_end"></td>
			</tr>
			<tr id="msch_applyfrm">
				<td><b>Apply From</b></td>
				<td><input type="text" class="inp" size="10" name="msch_applyfrm" id="m_applyfrm"></td>
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
				<td><input type="submit" class="button button-action button-rounded" value="Add Scheme discount"></td>
			</tr>
		</table>
	</form>
</div>

<div id="franchise_det_dlg" title="Available Franchise List">
	<div id="franchise_filter" align="right">
 	 	<span class="type_fil_wrap fl_left">
			<b class="all">All</b>
			<b class="sch_allot">Scheme Alloted</b>
			<b class="sch_not_allot">Scheme Not Alloted</b>						
		</span>
		<div class="fl_right" id="ter_twn_filter">
			<select name="fil_territory"></select> &nbsp;
			<select name="fil_town"></select>
		</div>
		<div class="fl_left" id="select_filter">
			<a class="sel_all_wrap" onclick="unselect_all_franchises()"><span>Unselect All</span></a>
			<a class="unsel_all_wrap" onclick="select_all_franchises()"><span>Select All</span></a>
		</div>
	</div>
	<div id="franchise_det" title="Franchise List"></div>
</div>

<div id="deals_det_dlg" title="Available Deals List">
	<div class="deals_list" title="Deals List"></div>
</div>

<script>

$('#choose_menu,#bulk_schtype,.select_cat,.select_brand,.disc_type').chosen();

function sel_fran_btn(ele)
{
	var trele=$(ele).parents('tr:first');
	var catid=$('.select_cat',trele).val();
	var brandid=$('.select_brand',trele).val();
	var fids=$('.sel_fids',trele).val();
	
	if($('.sel_fran_btn',trele).hasClass('franlist_selected'))
		var status=0;
	else
		status=1;	
	
	if(catid !=0 && brandid !='')
		$('#franchise_det_dlg').data({'menu_id':$('.choose_menuid').val(),'trele':trele,'status':status,'fids':fids}).dialog("open");
	else
	{
		alert("Please Choose Category & Brand before Proceed");
		return false;
	}
}

function sel_deal_btn(ele)
{
	var trele=$(ele).parents('tr:first');
	var catid=$('.select_cat',trele).val()*1;
	var brandid=$('.select_brand',trele).val()*1;
	if($('.sel_deal_btn',trele).hasClass('deallist_selected'))
		var status=0;
	else
		status=1;	
	
	if(catid !=0 && brandid !=0 )
	{
		$('#deals_det_dlg').data({'menu_id':$('.choose_menuid').val(),'trele':trele,'status':status,'catid':catid,'brandid':brandid}).dialog("open");
	}
	else
	{
		alert("Please Choose Category and Brand");
		return false
	}	
}
 
$(function(){
	//prepare_daterange("d_start","d_end");
	$('#d_start').datepicker({minDate:0});
	$('#msch_applyfrm').hide();
	$('#d_end').datepicker();
	$("#m_applyfrm").datepicker();
	$('#fran_list').hide();
	$('.breadcrumb_wrap').hide();
	$('.ext_input').hide();
	$('.mscheme_type').hide();
	$('.sel_dealids').val();
	$('.sel_fids').val();
	$('#bulk_schtype').val('1');
});

function sort_select_list(obj)
{
	var options = $('option',obj);
	var arr = options.map(function(_, o) { return { t: $(o).text(), v: o.value }; }).get();
		arr.sort(function(o1, o2) { return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0; });
		options.each(function(i, o) {
	  		o.value = arr[i].v;
	  		$(o).text(arr[i].t);
		});
}

$("#franchise_det_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'1200',
		height:'550',
		autoResize:true,
		open:function(){
			dlg = $(this);
			var menuid=$('#choose_menu').val();
			var catid=$('.select_cat').val();
			var brandid=$('.select_brand').val();
			var type=$("#bulk_schtype").val();
			var trele=dlg.data('trele');
			var status=dlg.data('status');
			var fids=dlg.data('fids');
			var fid=$('.sel_fids',trele).val();
			fid_arr=fid.split(",");
			
			if(status != 0)	
			{
				$.getJSON(site_url+'/admin/get_franchisebymenu_id/'+menuid+'/0'+'/0/'+type,function(resp){
					if(resp.status=='error')
					{
						$('#franchise_det').html(resp.message);
					}
					else
					{
						var menufranchiselist_html='';
						$.each(resp.menu_fran_list,function(i,itm){
							
						if($.inArray(itm.fid,resp.has_nosch)!=-1)
							var no_schfid=itm.fid;
							
						menufranchiselist_html+='<div class="list-inlineblk fid_'+itm.fid+' terr_'+itm.territory_id+' twn_'+itm.town_id+' sch_'+no_schfid+' "  fid='+itm.fid+' ><input type="checkbox" class="terr_'+itm.territory_id+'" name="fids[]" value="'+itm.fid+'":checked > <span class="'+(itm.is_suspended!="0"?'error_txt':'')+'">'+itm.franchise_name+'</span></div>';
							
						if(!$('select[name="fil_territory"] option#territory_'+itm.territory_id).length){
							if(itm.territory_id!=undefined){
								$('select[name="fil_territory"]').append('<option id="territory_'+itm.territory_id+'" value="'+itm.territory_id+'">'+itm.territory_name+'</option>');
							}
						}
				
						if(!$('select[name="fil_town"] option#town_'+itm.town_id).length){
							if(itm.town_id!=undefined){
								$('select[name="fil_town"]').append('<option id="town_'+itm.town_id+'" value="'+itm.town_id+'">'+itm.town_name+'</option>');
							}
						}
					});
				$('#franchise_det').html(menufranchiselist_html);
			}
					
			$('select[name="fil_territory"]').prepend('<option value=""> All</option');
			$('select[name="fil_town"]').prepend('<option value=""> All</option');
			sort_select_list($('select[name="fil_territory"]'));
			sort_select_list($('select[name="fil_town"]'));
			$('select[name="fil_territory"]').val("");
			$('select[name="fil_town"]').val("");
			
			$.getJSON(site_url+'/admin/jx_check_has_scheme/'+menuid+'/'+catid+'/'+brandid+'/'+type,function(resp){
				if(resp.status=='success')
				{
					if(resp.has_sch_disc.length != 0)
					{
						$.each(resp.has_sch_disc,function(i,itm){
							$('#franchise_det .list-inlineblk.fid_'+itm.franchise_id).addClass('sch_disc');
							$('#franchise_det .list-inlineblk.fid_'+itm.franchise_id).show();
						});
					}else
					{
						$('#franchise_det .list-inlineblk').removeClass('sch_disc');
					}
				}
			});
		});
	}
	else
	{
		$('.list-inlineblk').removeClass('selected');
		$('.list-inlineblk:visible input[name="fids[]"]').attr('checked',false);
		
		for(var i=0;i<fid_arr.length;i++)
		{
			$('.list-inlineblk.fid_'+fid_arr[i]+' input[name="fids[]"]').attr('checked',true);
			$('.list-inlineblk.fid_'+fid_arr[i]).addClass('selected');
		}
	}
	},
	buttons: {
	    "submit": function() {
	    	dlg = $(this);
	    	$('.sel_fids',trele).val();
	    	var trele=dlg.data('trele');
	    
	    	var fids=[];
    		$('.selected',dlg).each(function(){
    			fids.push($(this).attr('fid'));
    		});
    		
    		$('.sel_fids',trele).val(fids);
    		$('.franch_selected',trele).html("<b>"+fids.length+"</b> selected");
    		$('.sel_fran_btn',trele).addClass('franlist_selected');
    		$(this).dialog('close');
	   }
	} 
});

$('#franchise_filter .all').live('click',function(){
	$('.type_fil_wrap b').removeClass("selected_type");
	$('.type_fil_wrap .all').addClass("selected_type");
	var tid=$("select[name='fil_territory']").val();
	var townid=$("select[name='fil_town']").val();
	
	if(tid && !townid)
	{
		$('.list-inlineblk').hide();
		$('.list-inlineblk.terr_'+tid).show();
	}else if(tid && townid)
	{
		$('.list-inlineblk').hide();
		$('.list-inlineblk.terr_'+tid+'.twn_'+townid).show();
	}else if(!tid && !townid)
	{
		$('.list-inlineblk').show();
	}
});


$('#franchise_filter .sch_allot').live('click',function(){
	$('.type_fil_wrap b').removeClass("selected_type");
	$('.type_fil_wrap .sch_allot').addClass("selected_type");
	var tid=$("select[name='fil_territory']").val();
	var townid=$("select[name='fil_town']").val();
	
	if(tid && !townid)
	{
		$('.list-inlineblk').hide();
		$('.list-inlineblk.terr_'+tid+'.sch_disc').show();
	}else if(tid && townid)
	{
		
		$('.list-inlineblk').hide();
		$('.list-inlineblk.terr_'+tid+'.twn_'+townid+'.sch_disc').show();
	}else if(!tid && !townid)
	{
		$('.list-inlineblk').hide();
		$('.sch_disc').show();
	}
});

$('#franchise_filter  .sch_not_allot').live('click',function(){
	$('.type_fil_wrap b').removeClass("selected_type");
	$('.type_fil_wrap .sch_not_allot').addClass("selected_type");
	
	var tid=$("select[name='fil_territory']").val();
	var townid=$("select[name='fil_town']").val();
	if(tid && !townid)
	{
		$('.list-inlineblk').hide();
		$('.list-inlineblk.terr_'+tid+'.sch_undefined').show();
	}else if(tid && townid)
	{
		$('.list-inlineblk').hide();
		$('.list-inlineblk.terr_'+tid+'.twn_'+townid+'.sch_undefined').show();
	}else if(!tid && !townid)
	{
		$('.list-inlineblk').hide();
		$('.sch_undefined').show();
	}
});

$("#deals_det_dlg" ).dialog({
		modal:true,
		autoOpen:false,
		width:'1200',
		height:'550',
		autoResize:true,
		open:function(){
			dlg = $(this);
			var menuid=$('#choose_menu').val();
			var type=$("#bulk_schtype").val();
			var catid=dlg.data('catid');
			var brandid=dlg.data('brandid');
			var trele=dlg.data('trele');
			var status=dlg.data('status');
			var dealid=$('.sel_dealids',trele).val();
			dealids_arr=dealid.split(",");
			
			if(status!=0)
			{
				$.getJSON(site_url+'/admin/jx_to_getdeals_bybrandcatmenu/'+menuid+'/'+brandid+'/'+catid ,function(resp){
					if(resp.status=='errorr')
					{
						$('#deal_list').html(resp.message);
					}
					else
					{
						var menudeallist_html='';
							$.each(resp.deal_list,function(i,itm){
								menudeallist_html+='<div class="deallist-inlineblk dealid_'+itm.id+'"  dealid='+itm.id+' >';
								menudeallist_html+='<input type="checkbox" name="dealids[]" value="'+itm.id+'":checked dealid='+itm.id+'>'; 
								menudeallist_html+='<span class="'+(itm.is_sourceable=="0"?'error_txt':'')+'">'+itm.name+'<br /> [DP Price : <b>'+itm.price+'</b>]</span> <select class="sel_deal_disc_type"><option value="1">%</option><option value="0">Rs</option></select> <input type="text" class="sel_deal_disc_val" value=""> ';
								menudeallist_html+='</div>';
							});
						$('.deals_list').html(menudeallist_html);
					}
					
				});
			}
			else
			{
				$('.deallist-inlineblk:visible').removeClass('selected');
				$('.deallist-inlineblk:visible input[name="fids[]"]').attr('checked',false);
				
				for(var i=0;i<dealids_arr.length;i++)
				{
					$('.deallist-inlineblk.dealid_'+dealids_arr[i]+' input[name="fids[]"]').attr('checked',true);
					$('.deallist-inlineblk.dealid_'+dealids_arr[i]).addClass('selected');
				}
			}			
	},
	buttons: {
	    "submit": function() {
	    	dlg = $(this);
	    	var trele=dlg.data('trele');
	    	var dealids=[];
	    	var ids=[];
	    	
	    		$('.selected',dlg).each(function(){
	    			dealids.push($(this).attr('dealid')+':'+$('.sel_deal_disc_type',this).val()+':'+$('.sel_deal_disc_val',this).val());
	    			ids.push($(this).attr('dealid'));
	    		});
	    		
	    		$('.sel_dealids',trele).val(dealids);
	    		$('.deals_selected',trele).html("<b>"+dealids.length+"</b> selected");
	    		$('.sel_deal_btn',trele).addClass('deallist_selected');
	    	$(this).dialog('close');
	   }
	} 
});

$('#cat_brand_input_blk table tbody .row_addedit_btn').live('click',function(e){
	e.preventDefault();
	
	var tbody = $(this).parents('tbody:first');
	var trele = $(this).parents('tr:first');
	
	if($(this).hasClass('rmv'))
	{
		if(confirm("Are you sure do you want to remove this row"))
		{
			trele.remove();
			$('tr',tbody).each(function(i,j){
				$('td:first span',this).html(i+1);
			});
		}
	}else
	{
		var tr_tmpl = '<tr>'+trele.html()+'</tr>'; 
			tbody.append(tr_tmpl);
		var new_trele = $('tr:last',tbody);
			$('.chzn-container',new_trele).remove();
			
			$('.chzn-container',new_trele).remove();
			$('.chzn-done',new_trele).attr("id","").removeClass('chzn-done').show();
			
			$('.select_brand',new_trele).html("");
			$('.sel_fids',new_trele).val('');
			$('input[name="disc_type"]',new_trele).val("");
			$('input[name="disc_val"]',new_trele).val("");
			$('.ext_input',new_trele).val("");
			$('.sel_fran_btn',new_trele).removeClass('franlist_selected');
			$('.sel_deal_btn',new_trele).removeClass('deallist_selected');
			$('.franch_selected',new_trele).html("");
			$('.deals_selected',new_trele).html("");
			$('.select_cat',new_trele).val("").chosen();
			$('.select_brand',new_trele).val("").chosen();
			$('.disc_type',new_trele).val("").chosen();
			$('.row_addedit_btn',new_trele).html("-").addClass('rmv');
			$('td:first span',new_trele).html($('tr',tbody).length);
	}
});

$('.list-inlineblk').live('click',function(e){
	var fr_chk_ele=$(this).attr('fid');
	
	if(!$('.list-inlineblk.fid_'+fr_chk_ele+' input').attr('checked'))

		$('.list-inlineblk.fid_'+fr_chk_ele+'').removeClass('selected');
	else
		$('.list-inlineblk.fid_'+fr_chk_ele+'').addClass('selected');
});

$('.deallist-inlineblk input[name="dealids[]"]').live('click',function(e){
	//e.preventDefault();
	var fr_chk_ele=$(this).attr('dealid');
	
	if(!$('.deallist-inlineblk.dealid_'+fr_chk_ele+' input').attr('checked'))

		$('.deallist-inlineblk.dealid_'+fr_chk_ele+'').removeClass('selected');
	else
		$('.deallist-inlineblk.dealid_'+fr_chk_ele+'').addClass('selected');
});


$('.sel_all_wrap').live('click',function(){
	$('#select_filter a').removeClass('highlight_option');
	$('.sel_all_wrap').addClass('highlight_option');	
});
$('.unsel_all_wrap').live('click',function(){
	$('#select_filter a').removeClass('highlight_option');
	$('.unsel_all_wrap').addClass('highlight_option');	
});



function select_all_deals()
{
	$('.deallist-inlineblk:visible input').attr('checked',true);
	$('.deallist-inlineblk:visible').addClass('selected');
}

function unselect_all_deals()
{
	$('.deallist-inlineblk:visible input').attr('checked',false);
	$('.deallist-inlineblk:visible').removeClass('selected');
}

function select_all_franchises()
{
	$('.list-inlineblk:visible input').attr('checked',true);
	$('.list-inlineblk:visible').addClass('selected');
}

function unselect_all_franchises()
{
	$('.list-inlineblk:visible input').attr('checked',false);
	$('.list-inlineblk:visible').removeClass('selected');
}


$('#pnh_bulkschdisc_frm').submit(function(){
	
	var error_inp = new Array();
	var cat_req = 0;
	var brand_req = 0;
	var discount_err = 0;
	var max_discount_err = 0;
	var is_valid_daterange = 1;
	var s_discount_err = 0;
	var fids_err = 0;
	
		if($('#choose_menu').val() == "0")
			error_inp.push("Please Choose menu ");
		
		$('select[name="category[]"]',this).each(function(){
			if(!$(this).val())
				cat_req = 1;
		});
		
		if(cat_req)
			error_inp.push("Please Choose Category ");
		
		$('select[name="brand[]"]',this).each(function(){
			if(!$(this).val())
				brand_req = 1;
		});
	
		if(brand_req)
			error_inp.push("Please Choose Brand ");
		
		$('input[name="disc_val[]"]',this).each(function(){
			sch_disc = $(this).val()*1;
			if(isNaN(sch_disc))
				discount_err = 1;
			else if((sch_disc > 10) && ($('#bulk_schtype',this).val() == 1))
				max_discount_err = 1;
		});
	
		if(discount_err && ($('#bulk_schtype',this).val() == 1))
			error_inp.push("Invalid Discounts Entered");
		else
			if(discount_err && ($('#bulk_schtype',this).val() == 2))
				error_inp.push("Invalid Target Value Entered");
		
		if(max_discount_err)
			error_inp.push("Maximum 10% discount is allowed");
	
		$('input[name="scheme_val[]"]:visible',this).each(function(){
			trg_val = $(this).val()*1;
			if(isNaN(trg_val))
				s_discount_err = 1;
			else if(!trg_val)
				s_discount_err = 1;
		});

		if(s_discount_err)
			error_inp.push("Invalid Credit Precentage Entered");

		$('input[name="fran_ids[]"]',this).each(function(){
			fval = $(this).val();
			if(fval == "")
				fids_err = 1;
		});
		
		if(fids_err)
			error_inp.push("Please Choose atleast one franchise..");
		
		if(!$('#d_start',this).val().length)
			is_valid_daterange = 0;
		if(!$('#d_end',this).val().length)
			is_valid_daterange = 0;
				
		if(!is_valid_daterange)
			error_inp.push("Scheme Validity is required");
		
		if(!$('input[name="expire_prevsch"]:checked',this).length)
			error_inp.push("Please check to expire previous schemes");
			
		if(error_inp.length)
		{
			alert(error_inp.join("\n"));
			return false;
		}		
		
		if(!confirm("Are you sure want to give this scheme discount"))
			return false; 
});

$("#bulk_schtype").live('change',function(){
	$('#choose_menu').val("0").trigger('click');
	
	$('.disc_text').html($('option:selected',this).attr('col_text'));
	$('.extra_col').html($('option:selected',this).attr('col_extra'));
	$('.col_head').html($('option:selected',this).attr('col_title'));
	
	if($("#bulk_schtype").val() == 3 )
	{
		$('#msch_applyfrm').show();
	 	$('.ext_input').hide();
	 	$('.mscheme_type').hide();
	 	$('.sel_deal_btn').hide();
	 	$('.col_head').hide();
	 	$('.deals_td').hide();
	}else if($("#bulk_schtype").val() == 2 )
	{
		$('#msch_applyfrm').hide();
	 	$('.ext_input').show();
	 	$('.mscheme_type').hide();
	 	$('.sel_deal_btn').hide();
	 	$('.col_head').hide();
	 	$('.deals_td').hide();
	}
	else
	{
		$('#msch_applyfrm').hide();
		$('.ext_input').hide();
		$('.mscheme_type').hide();
		$('.sel_deal_btn').show();
		$('.col_head').show();
		$('.deals_td').show();
	}
});



$('#choose_menu').change(function(){
	var i = document.getElementById('choose_menu');
    var p = i.options[i.selectedIndex].text;
    $('#menu_name').html(p);
	var sel_terrid=$('#chose_terry').val();
	var sel_sch_type=$("#bulk_schtype").val();
	var sel_menuid=$(this).val();
		if($(this).val()*1)
		{
			$('.select_cat').html('<option value="">Loading...</option>').trigger("lizst:updated");
			$.getJSON(site_url+'/admin/jx_load_allcatsbymenu/'+$(this).val(),'',function(resp){
				var cat_html='';
					if(resp.status =='error')
					{
						alert(resp.msg);
					}
					else
					{
						cat_html = '<option value=""></option>';
						//cat_html+='<option value="0">All</option>';
						$.each(resp.cat_list,function(i,b){
							cat_html+='<option value="'+b.catid+'">'+b.name+'</option>';
						});
					}
				$('#brand_group tr:gt(0)').remove();
				//$('#brand_group tr:eq(0)').css('visibility','visible');		
				$('.select_cat').html(cat_html).trigger("liszt:updated");
				$('.select_cat').trigger('change');
				$('.choose_menuid').val(sel_menuid);
				$('.choose_sch_type').val(sel_sch_type);
				//$('.sel_fran_btn').addClass('choose_menuid').addClass('choose_sch_type');
			});
		}else
		{
			$('#brand_group tr:gt(0)').remove();
			$('#brand_group tr:eq(0)').css('visibility','hidden');
		}
});

$('.select_cat').live('change',function(){
	var trele = $(this).parents('tr:first');
	$(".select_brand",trele).html("<option value=''>Loading...</option>").trigger("lizst:updated");	

	if($(this).val())
	{
		$.getJSON(site_url+'/admin/jx_load_allbrandsbycat/'+$(this).val(),'',function(resp){
			var brand_html='';
			if(resp.status=='error')
			{
				alert(resp.msg);
			}
			else
			{
				brand_html ='<option value=""></option>';
				brand_html+='<option value="0">All</option>';
				$.each(resp.brand_list,function(i,b){
					brand_html+='<option value="'+b.brandid+'">'+b.name+'</option>';
				});
			}
			$('.select_brand',trele).html(brand_html).trigger('liszt:updated');
		});
	}else
	{
		$('.select_brand',trele).html("").trigger('liszt:updated');
	}
});

$('#bulk_schtype').change(function(){
	if($(this).val()==1)
		$('.disc_ondeal').show();
	else
		$('.disc_ondeal').hide();
});

$("select[name='fil_territory']").live('change',function(){
	var sel_menuid= $('#choose_menu').val();
	var tid=$(this).val();
	$('.type_fil_wrap b').removeClass("selected_type");
	$('.type_fil_wrap .all').addClass("selected_type");
	
	$("select[name='fil_town']").html('<option value="">Loading...</option>');
	if($(this).val() == '')
	{
		$('.list-inlineblk').show();
		$("select[name='fil_town']").html('<option value=""> All</option>');
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
				terr_linkedtwn_html +='<option value=""> All</option>';
				
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
		$('.list-inlineblk').hide();
		$('.list-inlineblk.terr_'+tid+'.twn_'+twn).show();
	}
});


$('.remove_row').live('click',function(){
	
	var trele = $(this).parents('tr:first');
	if(confirm("Do you want to delete"))
	{
		$(this).parents('tr:first').fadeOut().remove();
	}else
	{
		return false;
	}
});

</script>

<?php
