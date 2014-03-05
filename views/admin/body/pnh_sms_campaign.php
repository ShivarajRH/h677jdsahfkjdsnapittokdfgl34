<div class="container">
<h2>Send SMS Notification</h2>
 
<form id="pnh_sms_camp_frm" method="post">

<table cellpadding="5" width="100%">
	<tr>
		<td>Territory : </td>
		<td>
			<select name="view_byterry" data-placeholder="Choose Territory"  id="chose_terry"  style="min-width: 180px;" class='chzn-select' multiple='multiple'>
			<option value="0"></option>
			<?php $tr=$this->db->query("select * from pnh_m_territory_info order by territory_name asc")->result_array();
			foreach($tr as $t){?>
			<option value="<?php echo $t['id'];?>"><?php echo $t['territory_name']?></option>
			<?php }?>
			</select>
			<a href="javascript:void(0)" onclick="load_all_territory()">Select all</a>
		</td>
	</tr>
	<tr>
		<td>Town : </td>
		<td>
			<select id="view_bytwn"  style="min-width: 180px;" name="towns[]"  data-placeholder="Choose Towns" class='chzn-select' multiple='multiple'></select>
			<span class="select_towns_link"></span>
		</td>
	</tr>
	<tr>
		<td>Menu</td>
		<td>
			<select name="view_menu" data-placeholder="Select Menu" id="choose_menu" style="min-width: 180px;" class='chzn-select' multiple='multiple'>
			<option value="0"></option>
			<?php foreach($this->db->query("select id,name from pnh_menu order by name asc")->result_array() as $menu){?>
			<option value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option>
			<?php }?>
			</select>
			<a href="javascript:void(0)" onclick="select_menus_link()">Select all</a>
		</td>
	</tr>
<tr><td width="100" valign="center">To :</td>
<td>
<div style="float:left;padding:5px;border:1px solid #aaa;">
<div id="fran_list"></div>
<?php foreach($frans as $f){?>
 <!--  <div  onclick='$("input",$(this)).toggleCheck()' style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="<?=$f['franchise_id']?>" checked> <?=$f['franchise_name']?>, <?=$f['town']?>, <?=$f['city']?>, <?=$f['territory_name']?></div>-->
<?php }?>
<div class="clear"></div>
<div style="padding-top:5px;">select : <input onclick='$("input",$(this).parent().parent()).attr("checked",true)' type="button" value="All"> <input onclick='$("input",$(this).parent().parent()).attr("checked",false)' type="button" value="None"></div>
</div>
<div class="clear"></div>
</td>
</tr>
<tr><td>Send to :</td><td><label><input checked="checked" type="radio" name="send_to" value="1"> Both login mobiles</label> &nbsp; &nbsp;   <label><input type="radio" name="send_to" value="2"> Login mobile1 only</label></td></tr>
<tr>
<td>Type</td><td>
	<select name="type">
		<option value="1" selected="selected">Notification</option>
		<option value="2">Deal Promotion</option>
	</select>
</td>
</tr>
<tr id="deal_promo_blk" >
	<td><b>Choose Deal</b></td>
	<td valign="middle" style="vertical-align: middle;">
		<div id="srch_results"></div>
		<input type="text" class="inp" style="width:320px;" id="p_srch">
		<input type="hidden" name="sel_itemid" value="0">
		MRP:<input type="text" name="sel_itemmrp" size="2" value="0"> Offer:<input type="text" size="2" name="sel_itemprice" value="0">
	</td>
</tr>
<tr id="notify_sms_tmpl" style="display: none">
<td>Notify Template</td><td>
	<select name="sms_tmpl">
		<option value="1" selected="selected">Dear [Franchise], Offer Of the day [MSG]</option>
		<option value="2" >Top 5 Franchise Sales of the Day [MSG]</option>
	</select>
</td>
</tr>
<tr id="notify_employee" style="display: none">
	<td>Notify Employees</td>
	<td>
		<input type="checkbox" value="1" name="notify_emp" > 
		&nbsp;&nbsp;<div id="emp_count" style="width:500px;max-height: 200px;overflow: auto;"></div>
	</td>
</tr>
<tr id="notify_msg_blk"><td>Message :</td><td><textarea style="width:500px;height:100px;" name="msg" maxlength="140"  id="countable1"></textarea></td></tr>
<tr><td></td><td><input type="submit" value="Send"></td></tr>
</table>
</form>
</div>

<style>
#deal_promo_blk,#notify_msg_blk{display: none;}
#srch_results{
	margin-left: 2px;
	position: absolute;
	display: none;
	width: 400px;
	overflow-y: auto;
	background: #EEE;
	border: 1px solid #AAA;
	max-height: 200px;
	min-width: 300px;
	max-width: 326px;
	overflow-x: hidden;
	margin-top: 30px;
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
</style>

<script type="text/javascript">
$('.select_towns_link').html('');
$('#chose_terry').val(0);
$('#view_bytwn').val(0);
			
$('#chose_terry').chosen();
$('#view_bytwn').chosen();
$('#choose_menu').chosen();
$(".chzn-select").chosen({no_results_text: "No results matched"});

$('select[name="sms_tmpl"]').change(function(){
	if($(this).val() == 1)
	{
		$('input[name="notify_employee"]').attr('checked',false);
		$('#notify_employee').show();
	}else
	{
		$('input[name="notify_employee"]').attr('checked',true);
		$('#notify_employee').hide();
	}
}).trigger('change');


function load_allmenu()
{
	var menu_html='<option value=""></option>';
	$.getJSON(site_url+'/admin/jx_getallmenus','',function(resp){
		if(resp.status == 'error')
		{
			alert(resp.message);
		}
		else
		{
			$.each(resp.menu_list,function(i,b){
				menu_html+='<option value="'+b.id+'">'+b.name+'</option>';
			});
		}
		
		$('#choose_menu').html(menu_html).trigger("liszt:updated");
		$('#choose_menu').trigger("liszt:updated");
		
	});
}

function load_all_territory(){
	$('#chose_terry option').each(function(){
		$(this).attr('selected','selected');
	});
	$('#chose_terry_chzn').css("width",'90%');
	$('#chose_terry').trigger("liszt:updated");
	$('#chose_terry').trigger("change");
}

function load_all_towns()
{
	$('#view_bytwn option').each(function(){
		$(this).attr('selected','selected');
	});
	$('#view_bytwn_chzn').css("width",'90%');
	$('#view_bytwn').trigger("liszt:updated");
	$('#view_bytwn').trigger("change");
}

function select_menus_link()
{
	$('#choose_menu option').each(function(){
		$(this).attr('selected','selected');
	});
	$('#choose_menu_chzn').css("width",'90%');
	$('#choose_menu').trigger("liszt:updated");
	$('#choose_menu').trigger("change");
	
}

$("#chose_terry").change(function(){
	var territory_id=$(this).val();
	$('#view_bytwn').html('');
	$('#choose_menu').html('').trigger("liszt:updated");
	$('#fran_list').html('');

	//get the towns by territory
	$.post(site_url+'/admin/showtwn_lnkterr',{territoryid:territory_id},function(resp){
		var terr_linkedtwn_html='';
		if(resp.status=='errorr')
		{
			alert(resp.message);
		}
		else
		{
			terr_linkedtwn_html+='<option value="0" >Choose</option>';
			$.each(resp.town_linkedtoterry,function(i,itm){
				terr_linkedtwn_html +='<option value="'+ itm.id+'">'+itm.town_name+'</option>';
			});
		}
		$('#view_bytwn').html(terr_linkedtwn_html).trigger('liszt:updated');

		if(resp.town_linkedtoterry)
			$(".select_towns_link").html("<a href='javascript:void(0)' onclick='load_all_towns()'>Select all</a>");
		
	},'json');

	//get the menu by territory
	$.post(site_url+'/admin/get_franchisemenuby_terrid',{territoryid:territory_id},function(resp){
		var terr_linkedtwn_html='';
		if(resp.status=='errorr')
		{
			alert(resp.message);
		}
		else
		{
			terr_linkedtwn_html+='<option value="" >Choose</option>';
			$.each(resp.terrymenu_franlist,function(i,itm){
			terr_linkedtwn_html +='<option value="'+ itm.menuid+'">'+itm.name+'</option>';
			});
		}
		$('#choose_menu').html(terr_linkedtwn_html).trigger('liszt:updated');
		
	},'json');
	
	//get the franchise by territory
	$.post(site_url+"/admin/get_franchisebyterr_id",{territoryid:territory_id},function(resp){
		if(resp.status=='errorr')
		{
			alert(resp.message);
		}
		else
		{
			var franchiselist_html='';
			$.each(resp.franchise_list,function(i,itm){
				franchiselist_html+='<div style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="'+itm.franchise_id+'":checked >'+itm.franchise_name+'</div>';
					$('#fran_list').html(franchiselist_html);
					$('#fran_list').css("display","none");
			});
		}
		
	},'json');

		
});

$('#view_bytwn').change(function(){
	$('#fran_list').html('');
	var sel_twnid=$(this).val();
	var sel_menuid=$('#choose_menu').val();
	$('#choose_menu').html('').trigger("liszt:updated");


	//get the menu list
	$.post(site_url+'/admin/get_franchisemenuby_twnid',{townid:sel_twnid},function(resp){
		var terr_linkedtwn_html='';
		if(resp.status=='errorr')
		{
			//alert(resp.message);
		}
		else
		{
			terr_linkedtwn_html+='<option value="" >Choose</option>';
			$.each(resp.townmenu_franlist,function(i,itm){
				terr_linkedtwn_html +='<option value="'+ itm.menuid+'">'+itm.name+'</option>';
			});
		}
		$('#choose_menu').html(terr_linkedtwn_html).trigger('liszt:updated');
	},'json');
	
	
	//get the franchise  menus and town
	if(sel_menuid)
	{
		$.post(site_url+'/admin/get_franchisemenu_townid',{menuid:sel_menuid,townid:sel_twnid},function(resp){
			if(resp.status=='errorr')
			{
				//alert(resp.message);
			}
			else
			{
				var franchiselist_html='';
				$.each(resp.menu_fran_list,function(i,itm){
					franchiselist_html+='<div style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="'+itm.franchise_id+'":checked >'+itm.franchise_name+'</div>';
						$('#fran_list').html(franchiselist_html);
						$('#fran_list').css("display","none");
				});
			}
		},'json');
	}
	

	//get the franchise
	$.post(site_url+'/admin/get_franchisebytwn_id',{townid:sel_twnid},function(resp){
		if(resp.status=='errorr')
		{
			//alert(resp.message);
		}
		else
		{
			var franchiselist_html='';
			$.each(resp.franchise_list,function(i,itm){
				franchiselist_html+='<div style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="'+itm.franchise_id+'":checked >'+itm.franchise_name+'</div>';
					$('#fran_list').html(franchiselist_html);
					$('#fran_list').css("display","none");
			});
		}
	},'json');

	
});


$('#choose_menu').change(function(){
	var sel_terrid=$('#chose_terry').val();
	var sel_menuid=$(this).val();
	var sel_townid=$('#view_bytwn').val();
	$('#fran_list').html('').trigger('liszt:updated');

	$.post(site_url+'/admin/get_franchisebymenu_id',{menuid:sel_menuid,territoryid:sel_terrid,townid:sel_townid},function(resp){
		if(resp.status=='errorr')
		{
				//alert(resp.message);
		}
		else
		{
			var menufranchiselist_html='';
			$.each(resp.menu_fran_list,function(i,itm){
				menufranchiselist_html+='<div style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="'+itm.fid+'":checked >'+itm.franchise_name+'</div>';
					$('#fran_list').html(menufranchiselist_html);
					$('#fran_list').show();
			});
		}
	
			$('#fran_list').html(menufranchiselist_html).trigger('liszt:updated');
			$('#fran_list').trigger('change');
	},'json');
});

$("input[name='notify_emp']").click(function(){
	$("#emp_count").html('');
	


	if($(this).is(":checked"))
	{
		if($("#fran_list input[name='fids[]']:checked").length)
		{
			var fids= new Array();
			$.each($("#fran_list input[name='fids[]']:checked"),function(a,b){
				fids.push($(this).val());
			});

			var html_cnt='';
			$.post(site_url+'/admin/jx_get_empcount_by_fids',{fids:fids},function(resp){
				html_cnt+='<fieldset><legend>Employees list&nbsp;<input type="checkbox" id="select_all_emp"></legend>';
				$.each(resp.count_list,function(a,b){
					var role_name='';
					if(a==5)
						role_name='Bussiness Executive';
					else if(a==4)
						role_name='Territory Manager';
					html_cnt+="<div style='width:200px;float;left;'><b>"+role_name +'</b> : '+ b+"&nbsp";	
					html_cnt+="  <ul style='padding:1px;list-style:none;'>";
						$.each(resp.emp_list[a],function(b,c){
							html_cnt+="<li>"+c.name+"<input type='checkbox' name='emplist[]' value='"+c.employee_id+"'></li>";		
						});
					html_cnt+="  </ul>"; 
					html_cnt+="</div>";
				});
				html_cnt+='</fieldset><div class="clear"></div>';
				$("#emp_count").html(html_cnt);
			},'json');
		}else{
			alert("Please select franchise");
			$(this).attr("checked",false);
		}
	}
});

$("#select_all_emp").live('click',function(){

	if($(this).is(":checked"))
		$("input[name='emplist[]']").attr("checked",true);
	else
		$("input[name='emplist[]']").attr("checked",false);
	
});

$('#countable1').jqEasyCounter({
	'maxChars': 140,
	'maxCharsWarning': 100,
	'msgFontSize': '10px',
	'msgFontColor': '#000',
	'msgFontFamily': 'arial',
	'msgTextAlign': 'left',
	'msgWarningColor': '#F00',
	'msgAppendMethod': 'insertBefore'				
});
/*
$('#chose_terry').change(function(){
	var sel_terid=$(this).val();
	$('#view_bytwn').html('').trigger("liszt:updated");
	$.getJSON(site_url+'/admin/showtwn_lnkterr/'+sel_terid,'',function(resp){
	var terr_linkedtwn_html='';
	if(resp.status=='errorr')
	{
		alert(resp.message);
	}
	else
	{
		terr_linkedtwn_html+='<option value="" >Choose</option>';
		$.each(resp.town_linkedtoterry,function(i,itm){
		terr_linkedtwn_html +='<option value="'+ itm.id+'">'+itm.town_name+'</option>';
		});
	}
	$('#view_bytwn').html(terr_linkedtwn_html).trigger('liszt:updated');
	$('#view_bytwn').trigger('change');

	});

	$.getJSON(site_url+'/admin/get_franchisebyterr_id/'+sel_terid+'',function(resp){
		if(resp.status=='errorr')
		{
			alert(resp.message);
		}
		else
		{
			var franchiselist_html='';
			$.each(resp.franchise_list,function(i,itm){
				franchiselist_html+='<div style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="'+itm.franchise_id+'":checked >'+itm.franchise_name+'</div>';
					$('#fran_list').html(franchiselist_html);
			});
		}
	});
	
});


$('#chose_terry').change(function(){
	var sel_terid=$(this).val();
	$('#choose_menu').html('').trigger("liszt:updated");
	$.getJSON(site_url+'/admin/get_franchisemenuby_terrid/'+sel_terid,'',function(resp){
	var terr_linkedtwn_html='';
	if(resp.status=='errorr')
	{
		//alert(resp.message);
	}
	else
	{
		terr_linkedtwn_html+='<option value="" >Choose</option>';
		$.each(resp.terrymenu_franlist,function(i,itm){
		terr_linkedtwn_html +='<option value="'+ itm.menuid+'">'+itm.name+'</option>';
		});
	}
	$('#choose_menu').html(terr_linkedtwn_html).trigger('liszt:updated');
	$('#choose_menu').trigger('change');

	});

	$.getJSON(site_url+'/admin/get_franchisebyterr_id/'+sel_terid+'',function(resp){
		if(resp.status=='errorr')
		{
			//alert(resp.message);
		}
		else
		{
			var franchiselist_html='';
			$.each(resp.franchise_list,function(i,itm){
				franchiselist_html+='<div style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="'+itm.franchise_id+'":checked >'+itm.franchise_name+'</div>';
					$('#fran_list').html(franchiselist_html);
			});
		}
	});
});
*/

/*
$('#view_bytwn').change(function(){
	var sel_twnid=$(this).val();	
	$.getJSON(site_url+'/admin/get_franchisebytwn_id/'+sel_twnid+'',function(resp){
		if(resp.status=='errorr')
		{
			//alert(resp.message);
		}
		else
		{
			var franchiselist_html='';
			$.each(resp.franchise_list,function(i,itm){
				franchiselist_html+='<div style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="'+itm.franchise_id+'":checked >'+itm.franchise_name+'</div>';
					$('#fran_list').html(franchiselist_html);
			});
		}
	});
});

$('#view_bytwn').change(function(){
	var sel_twnid=$(this).val();	
	var sel_menuid=$('#choose_menu').val();
	$.getJSON(site_url+'/admin/get_franchisemenu_townid/'+sel_menuid+'/'+sel_twnid+'',function(resp){
		if(resp.status=='errorr')
		{
			//alert(resp.message);
		}
		else
		{
			var franchiselist_html='';
			$.each(resp.menu_fran_list,function(i,itm){
				franchiselist_html+='<div style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="'+itm.franchise_id+'":checked >'+itm.franchise_name+'</div>';
					$('#fran_list').html(franchiselist_html);
			});
		}
	});
});



	$('#view_bytwn').change(function(){
		var sel_twnid=$(this).val();
		$('#choose_menu').html('').trigger("liszt:updated");
		$.getJSON(site_url+'/admin/get_franchisemenuby_twnid/'+sel_twnid,'',function(resp){
		var terr_linkedtwn_html='';
		if(resp.status=='errorr')
		{
			//alert(resp.message);
		}
		else
		{
			terr_linkedtwn_html+='<option value="" >Choose</option>';
			$.each(resp.townmenu_franlist,function(i,itm){
			terr_linkedtwn_html +='<option value="'+ itm.menuid+'">'+itm.name+'</option>';
			});
		}
		$('#choose_menu').html(terr_linkedtwn_html).trigger('liszt:updated');
		$('#choose_menu').trigger('change');
	
		});
	
		$.getJSON(site_url+'/admin/get_franchisebytwn_id/'+sel_twnid+'',function(resp){
			if(resp.status=='errorr')
			{
				//alert(resp.message);
			}
			else
			{
				var franchiselist_html='';
				$.each(resp.franchise_list,function(i,itm){
					franchiselist_html+='<div style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="'+itm.franchise_id+'":checked >'+itm.franchise_name+'</div>';
						$('#fran_list').html(franchiselist_html);
				});
			}
		});
	});



$('#choose_menu').change(function(){
	var sel_terrid=$('#chose_terry').val();
	var sel_menuid=$(this).val();
	
		$('#fran_list').html('').trigger('liszt:updated');
		$.getJSON(site_url+'/admin/get_franchisebymenu_id/'+sel_menuid+'/'+sel_terrid+'',function(resp){
			if(resp.status=='errorr')
			{
				//alert(resp.message);
			}
			else
			{
				var menufranchiselist_html='';
				$.each(resp.menu_fran_list,function(i,itm){
					menufranchiselist_html+='<div style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="'+itm.fid+'":checked >'+itm.franchise_name+'</div>';
					$('#fran_list').html(menufranchiselist_html);
				});
			}
	
			$('#fran_list').html(menufranchiselist_html).trigger('liszt:updated');
			$('#fran_list').trigger('change');
		});
});

$('#choose_menu').change(function(){
	
	var sel_menuid=$(this).val();
	
		$('#fran_list').html('').trigger('liszt:updated');
		$.getJSON(site_url+'/admin/jx_loadfranby_menuid/'+sel_menuid+'',function(resp){
			if(resp.status=='errorr')
			{
				//alert(resp.message);
			}
			else
			{
				var menufranchiselist_html='';
				$.each(resp.franlist_bymenu,function(i,itm){
					menufranchiselist_html+='<div style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="'+itm.fid+'":checked >'+itm.franchise_name+'</div>';
					$('#fran_list').html(menufranchiselist_html);
				});
			}
	
			$('#fran_list').html(menufranchiselist_html).trigger('liszt:updated');
			$('#fran_list').trigger('change');
		});
});
*/
var jHR=0,search_timer;

$('select[name="type"]').change(function(){
	if($(this).val() == 2)
	{
		$('#deal_promo_blk').show();
		$('#notify_msg_blk').hide();
		$('#notify_sms_tmpl').hide();
		$('select[name="sms_tmpl"]').val(1);
	}else
	{
		$('#deal_promo_blk').hide();
		$('#notify_msg_blk').show();
		$('#notify_sms_tmpl').show();
		
	}
	$('select[name="sms_tmpl"]').val(1).trigger('change');
	
}).trigger('change');

function add_deal_callb(name,pid,mrp,price,store_price)
{
	$('#srch_results').html('').hide();
	
	$("#p_srch").val(name).focus();
	$("input[name='sel_itemid']").val(pid);
	$("input[name='sel_itemmrp']").val(mrp);
	$("input[name='sel_itemprice']").val(price);
	
}

$("#p_srch").keyup(function(){
	q=$(this).val();
	if(q.length<3)
		return true;
	if(jHR!=0)
		jHR.abort();
	window.clearTimeout(search_timer);
	search_timer=window.setTimeout(function(){
	jHR=$.post('<?=site_url("admin/pnh_jx_searchdeals")?>',{q:q},function(data){
		$("#srch_results").html(data).show();
	});},200);
});

$('#pnh_sms_camp_frm').submit(function(){
	var error_msg = '';

		if(!$('input[name="fids[]"]:checked').length)
		{
			alert("Please select atleast one franchise");
			return false;
		}
	
		if($('select[name="type"]').val() == 1)
		{
			if($.trim($('textarea[name="msg"]').val()) == '')
			{
				alert("Please enter message for campaign");
				return false;
			}	
		}else
		{
			if(!($('input[name="sel_itemid"]').val()*1))
			{
				alert("Please choose a deal for promotion");
				return false;
			}
		}

		if(!confirm("Are you sure want to proceed ? "))
		{
			return false;
		}
		
});

</script>

<?php
