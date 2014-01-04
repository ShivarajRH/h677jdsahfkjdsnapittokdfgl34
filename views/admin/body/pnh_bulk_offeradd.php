<div class="container">
<h2>Manage Offers</h2>
<form id="pnh_bulkoffer_frm" method="post">
<table cellpadding=5>
<tr><td><b>Offer Text</b></td><td><b>:</b></td><td><input type="text" name="offer_txt" size="100px"></td></tr>
<tr><td><b>Menu</b></td><td><b>:</b></td><td><select name="menu" data-placeholder="Select Menu" id="choose_menu" style="min-width: 180px;">
<?php foreach($this->db->query("select id,name from pnh_menu where status = 1 order by name asc")->result_array() as $menu){?>
<option value="<?php echo $menu['id']?>"><?php echo $menu['name']?></option>
<?php }?>
<tr><td width="100" valign="center"><b>Franchises</b></td><td><b>:</b></td>
<td>
<div style="float:left;padding:5px;border:1px solid #dfdfdf;min-width: 180px;">
<div id="fran_list"></div>
<?php foreach($this->db->query("select id,name from pnh_menu order by name asc")->result_array() as $menu){?>
 <?php }?>
<div class="clear"></div>
<div style="padding-top:5px;">select : <input onclick='$("input",$(this).parent().parent()).attr("checked",true)' type="button" value="All"> <input onclick='$("input",$(this).parent().parent()).attr("checked",false)' type="button" value="None"></div>
</div>
<div class="clear"></div>
</td>
</tr>
<tr>
<td><b>Category</b></td><td><b>:</b></td>
<td><select name="cat" class="select_cat"  data-placeholder="Select Category" style="width:250px;" data-required="true"></select>
</select>
</td>
</tr>
<tr>
	<td><b>Brand</b></td><td><b>:</b></td>
	<td>
		<select name="brand"  class="select_brand"  data-placeholder="Select Brand" style="width:250px;" data-required="true"></select>
	</td>
</tr>
<tr><td width="18%"><b>Immediate Payment</b></td><td><b>:</b></td><td><input type="checkbox" value="1" name="immediate_payment"></td></tr>
<tr><td><b>Validity</b></td><td><b>:</b></td><td><input type="text" id="offr_frm" placeholder=" From" name="offer_start"> <b>TO</b> <input type="text" id="offr_to" placeholder="To" name="offer_end"></td></tr>
<tr><td align="center"><input type="submit" value="Add Offer"></td></tr>
</table>
</form>
</div>
<script>
$('#choose_menu').chosen();
$('.select_brand').chosen();
$('.select_cat').chosen();

$("#offr_frm,#offr_to").datepicker();
$('#choose_menu').change(function(){

	var sel_menuid=$(this).val();

	if(sel_menuid != 0)
	{
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
					menufranchiselist_html+='<div style="white-space:nowrap;margin-bottom:3px;margin-right:3px;float:left;padding:3px;background:#eee;cursor:pointer;"><input type="checkbox" name="fids[]" value="'+itm.fid+'":checked >'+itm.franchise_name+'</div>';
					$('#fran_list').html(menufranchiselist_html);
				});
			}
	
			$('#fran_list').html(menufranchiselist_html).trigger('liszt:updated');
			$('#fran_list').trigger('change');
		});

		$(".select_cat").html('').trigger("liszt:updated");
		$.getJSON(site_url+'/admin/jx_load_allcatsbymenu/'+sel_menuid,'',function(resp){
			var cats_html='';
				if(resp.status=='error')
				{
					alert(resp.message);
				}
				else
				{
					cats_html+='<option value=""></option>';
					cats_html+='<option value="0">All</option>';
					$.each(resp.cat_list,function(i,b){
					cats_html+='<option value="'+b.catid+'">'+b.name+'</option>';
					});
				}
		 	$('.select_cat').html(cats_html).trigger("liszt:updated");
		 	$('.select_cat').trigger('change');
		});
	}
	
});
	$('.select_cat').change(function(){
		sel_catid=$(this).val();
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
			});
		}
		 $('.select_brand').html(brands_html).trigger("liszt:updated");
		 $('.select_brand').trigger('change');
		});
	});



</script>