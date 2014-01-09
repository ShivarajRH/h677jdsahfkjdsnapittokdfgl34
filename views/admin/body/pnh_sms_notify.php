<div class="container">
<h2>Send SMS Notification</h2>
<form id="pnh_sms_notify_frm" method="post">




<table cellpadding="5" width="100%">
	<tr>
			<td>Territory</td>
			<td>
				 <select name="view_byterry" data-placeholder="Choose Territory"  id="chose_terry"  style="min-width: 180px;" >
							<option value="0"></option>
						<?php $tr=$this->db->query("select * from pnh_m_territory_info order by territory_name asc")->result_array();
						foreach($tr as $t){?>
						<option value="<?php echo $t['id'];?>"><?php echo $t['territory_name']?></option>
						<?php }?>
						</select>
			</td>
		</tr>
		<tr>	
			<td>Town</td>
			<td><select id="view_bytwn"  style="min-width: 180px;" name="towns[]"  data-placeholder="Choose Towns" ></select></td>			
		</tr>
		<tr style="display: none">	
			<td>Menu</td>
			<td>
				<select name="view_menu" data-placeholder="Select Menu" id="choose_menu" style="min-width: 180px;">
				<option value=""></option>
				<?php foreach($this->db->query("select id,name from pnh_menu order by name asc")->result_array() as $menu){?>
				<option value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option>
				<?php }?>
				</select>
			</td>			
		</tr>
<tr><td width="100" valign="center">To :</td>
<td>
<div style="float:left;padding:5px;border:1px solid #aaa;">
<div id="fran_list"></div>
<div class="clear"></div>
<div style="padding-top:5px;">select : <input onclick='$("input",$(this).parent().parent()).attr("checked",true)' type="button" value="All"> <input onclick='$("input",$(this).parent().parent()).attr("checked",false)' type="button" value="None"></div>
</div>
<div class="clear"></div>
</td>
</tr>
<tr><td>Send to :</td><td><label><input checked="checked" type="radio" name="send_to" value="1"> Both login mobiles</label> &nbsp; &nbsp;   <label><input type="radio" name="send_to" value="2"> Login mobile1 only</label></td></tr>
<tr>
<td>Template</td><td>
	<select name="type">
		<option value="1" selected="selected">Top 5 Franchise Sales of the Day </option>
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
<tr id="notify_msg_blk"><td>Message :</td><td><textarea style="width:400px;height:80px;" name="msg"></textarea>
<div id="preview_sms_post" style="padding:10px;background: #ffffa0;width: 400px;margin:5px 0px;font-size: 10px;display: none">
	<b>Preview SMS</b>
	<p>Top 5 Franchise Sales of the Day </p>
</div>
</td></tr>
<tr><td></td><td><input type="submit" value="Send"></td></tr>
</table>
</form>
</div>

<style>
#preview_sms_post p{background: #fcfcfc;padding:5px;margin:5px 0px;border:1px dotted #cdcdcd}
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
$('#chose_terry').chosen();
$('#view_bytwn').chosen();
$('#choose_menu').chosen();

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
/*
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
*/



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
		/*
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
		*/
});

$('#choose_menu').change(function(){
	
	var sel_menuid=$(this).val();
		return;
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

var jHR=0,search_timer;

$('select[name="type"]').change(function(){
	if($(this).val() == 2)
	{
		$('#deal_promo_blk').show();
		$('#notify_msg_blk').hide();
	}else
	{
		$('#deal_promo_blk').hide();
		$('#notify_msg_blk').show();
	}
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

$('#pnh_sms_notify_frm').submit(function(){
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
