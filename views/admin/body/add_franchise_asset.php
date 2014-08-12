<div id="container">
<h2>Add Franchise Asset</h2>
<a href="<?php echo site_url('admin/franchise_asset_list')?>" class=" button button-royal " style="float:right;margin-top: -38px;">List Franchise Assets</a>
		
<div id="add_fran_asset" width="100%">
<fieldset>
<legend><b>Add Franchise Asset</b></legend>
<form action="<?php echo site_url('admin/p_add_franasset')?>" method="post" data-validate="parsley">
<table cellspacing="6" cellpadding="0" >
<tr>
	<td><b>State</b></td><td><b>:</b></td>
	<td> 
		<select name="asset_state" id="asset_state" style="width:200px" data-required="true">
		<option value="">Choose</option>
		<?php foreach($state_list as $s){?>
		<option value="<?=$s['state_id'] ?>"><?=$s['state_name'] ?></option>
		<?php }?>
		</select>
	</td>
</tr>
<tr>
	<td><b>Territory</b></td><td><b>:</b></td>
	<td>
		<select name="asset_terr" id="asset_terr" style="width: 200px;" data-required="true">
		<option value="">Choose</option>
		<?php foreach($terry_list as $t){?>
		<option value="<?=$t['id']?>"><?=$t['territory_name']?></option>
			<?php }?>	
		</select>
		
	</td>
</tr>

<tr>
	<td><b>Franchises</b></td><td><b>:</b></td>
	<td>
		<select name="asset_fran" id="asset_fran" style="width:250px;" data-required="true">
		<option value="">Choose</option>
	</td>

</tr>

<tr>
	<td><b>Asset</b></td><td><b>:</b></td>
	<td>
	<select name="asset" id="asset"  style="width:250px;" data-required="true">
		<option value="">choose</option>
		<?php foreach($asset_list as $as){?>
		<option value="<?=$as['id'] ?>"><?=$as['asset_name'] ?></option>
		<?php }?>
	</select>
	</td>
</tr>

<tr>
	<td><b>Accessory</b></td><td><b>:</b></td>
	<td>
	<select name="accessory[]" id="accessory" style="width:250px;" multiple="multiple" data-required="true">
	<option value="">Choose</option>
	</select>
	</td>
</tr>

<tr>
	<td><b>Asset Serial number</b></td><td><b>:</b></td>
	<td><input type="text" name="asset_serialno" value="" data-required="true"></td>
</tr>

<tr>
	<td><b>Store type</b></td><td><b>:</b></td>
	<td>
		<select name="store_type" id="store_type" data-required="true">
			<option value="">Choose</option>
			<?php if($store_types){ foreach($store_types as $s){?>
			<option value="<?=$s['store_id']?>"><?=$s['store_name'] ?></option>
			<?php }}?>
		</select>
	</td>
</tr>


</table>
<div>
<input style="float:right;margin-left: 12px;"class="button button-action button-rounded button-small" type="submit" value="Add">
</div>
</form>
</fieldset>
</div>
</div>

<script>
$("#asset_terr , #asset_fran ,#asset_state ,#asset , #accessory , #store_type").chosen();

$("#asset_terr").change(function(){
	
	$("#asset_fran").html('').trigger("liszt:updated");
	var sel_stateid=$("#asset_state").val();
	var sel_terrid=$("#asset_terr").val();
	if(sel_stateid!=0)
	{
		$.post(site_url+'/admin/jx_load_all_franchise_bystate_territory',{stateid:sel_stateid,terrid:sel_terrid},function(resp){
			var state_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				state_html+='<option value=""></option>';
				
				$.each(resp.fran_bystateterry,function(i,b){
					state_html+='<option value="'+b.franchise_id+'">'+b.franchise_name+'</option>';
				});
			}
			$("#asset_fran").html(state_html).trigger("liszt:updated");
			$("#asset_fran").trigger('change');
			},'json');
	}
	
});

$("#asset").change(function(){
	
	var asset_id=$(this).val();
	$("#accessory").html('').trigger("liszt:updated");
	if(asset_id!=0)
	{
		$.post(site_url+'/admin/jx_get_all_assetaccessories',{asset_id:asset_id},function(resp){
			var state_html='';
			if(resp.status=='error')
			{
				alert(resp.message);
			}
			else
			{
				state_html+='<option value=""></option>';
			
				$.each(resp.accessory_list,function(i,b){
					state_html+='<option value="'+b.id+'">'+b.accessory_name+'</option>';
				});
			}
			$("#accessory").html(state_html).trigger("liszt:updated");
			$("#accessory").trigger('change');
			},'json');
	}
});
</script>