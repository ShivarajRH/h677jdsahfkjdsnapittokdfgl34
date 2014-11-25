<div class="container">
	<h2 class="page_title">Edit Market Place Seller</h2>
	<?php $mseller_id=$this->uri->segment(3);?>
		<form action="<?php echo site_url("admin/process_edit_m_seller")?>" method="post" data-validate="parsley">
		<fieldset>
				<legend><b>Seller Basic Info</b></legend>
				<table cellspacing="5">
					<input type="hidden" name="m_sellerid" value="<?php echo $mseller_id?>">
					<tr><td><b>Name</b></td><td><b>:</b></td><td><input type="text" name="m_seller_name" value="<?=$mseller_det['seller_name']?>" data-required="true"></td></tr>
					<tr><td><b>Contact no</b></td><td><b>:</b></td><td><input type="text" name="mseller_phno" value="<?=$mseller_det['contact_no']?>" data-required="true" maxlength="10"></td></tr>
					<tr>
						<td><b>State</b></td>
						<td><b>:</b></td>
						<td>
							<select name="mseller_state" id="mseller_state" style="width: 150px;" data-required="true" >
								<option value="">Select State</option>
								<?php $state_list=$this->db->query("select state_id,state_name from pnh_m_states order by state_name asc"); if($state_list->num_rows()){ foreach($state_list->result_array() as $s){?>
								<?php $selected=$mseller_det['state_id']?>
								<option value="<?=$s['state_id']?>" selected="<?=$selected?>"><?=$s['state_name']?></option>
								<?php }}?>
							</select>
						</td>
					</tr>
					<tr><td><b>Territory</b></td><td><b>:</b></td><td><select name="mseller_terr" id="mseller_terr" style="width: 150px;" data-required="true" prev_selected_terrid="<?php echo $mseller_det['territory_id']?>"></select></td></tr>
					<tr><td><b>Town</b></td><td><b>:</b></td><td><select name="mseller_twn" id="mseller_twn" style="width: 150px;" prev_selected_twnid="<?php echo $mseller_det['town_id']?>"></select></td></tr>
					<tr><td><b>Address</b></td><td><b>:</b></td><td><textarea name="mseller_address" data-required="true"><?=$mseller_det['address']?></textarea></td></tr>
					<tr><td><b>City</b></td><td><b>:</b></td><td><input type="text" name="mseller_city" id="mseller_city" value="<?=$mseller_det['city']?>" data-required="true"></td></tr>
					<tr><td><b>Pincode</b></td><td><b>:</b></td><td><input type="text" name="mseller_pincode" value="<?=$mseller_det['pincode']?>" data-required="true"></td></tr>
					<tr><td><b>Enable Billing</b></td><td><b>:</b></td><td><input type="checkbox" value="<?php $mseller_det['enable_billing']?>" name="enable_billing"></td></tr>
				</table>
		</fieldset>
		<br>
		<div align="left">
			<input type="submit" value="Update Market Seller" class="button button-action button-rounded button-small">
		</div>
	</form>
</div>


<script>

$("#mseller_state").chosen();
$("#mseller_terr").chosen();
$("#mseller_twn").chosen();

//$('select[name="mseller_state"]').trigger('change');

$("#mseller_state").change(function(){
	var sel_state=$(this).val();
	prev_selected_terrid = $('select[name="mseller_terr"]').attr('prev_selected_terrid');
	$.get(site_url+'/admin/jx_toload_allterritories_bystate/'+sel_state,function(resp){
		if(resp.status=='success')
		{
			var terr_html="";
			$.each(resp.territory_list,function(i,a)
			{
				var selected = (prev_selected_terrid == a.territory_id)?'selected':'';
				terr_html+='<option value="'+a.territory_id+'" '+selected+'>'+a.territory_name+'</option>';
			});
			$("#mseller_terr").html(terr_html).trigger("liszt:updated").trigger('change');
		}
		else
		{
			alert(resp.msg);
			return false;
		}
	},'json');
}).trigger('change');

$("#mseller_terr").change(function()
{
	var sel_terr=$(this).val();
	prev_selected_twnid = $('select[name="mseller_twn"]').attr('prev_selected_twnid');
	$.get(site_url+'/admin/jx_toload_alltowns_byterritory/'+sel_terr,function(resp){
		if(resp.status=='success')
		{
			var twn_html="";
			$.each(resp.town_list,function(i,a)
			{
				var selected = (prev_selected_twnid == a.town_id)?'selected':'';
				twn_html+='<option value="'+a.town_id+'" '+selected+' >'+a.town_name+'</option>';
			});
			$("#mseller_twn").html(twn_html).trigger("liszt:updated");
		}
		else
		{
			alert(resp.msg);
			return false;
		}
		
		},'json');
});
</script>