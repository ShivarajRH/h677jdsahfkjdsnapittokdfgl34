<div id="container">
	<h2>Franchise Asset List</h2>
	<?php //echo $store_type;exit;?>
	<a href="<?php echo site_url('admin/add_franchise_asset')?>" class=" button button-royal " style="float:right;margin-bottom: 17px;margin-top: -38px;">Add Franchise Assets</a>
<?php $asset_id=$fasset_list[0]['asset_id']?>
	<table class="datagrid" width="100%">
		<thead>
			<th>Slno</th>
			<th>Franchise Name</th>
			<th>Store Type</th>
			<th>Asset Name</th>
			<th>Asset serial number</th>
			<th>Accessories</th>
			<th>Created on</th>
			<th>Created By</th>
			<th>Modified On</th>
			<th>Modified By</th>
			<th>Actions</th>
		</thead>
		<?php if($fasset_list){?>
		<tbody>
		<?php foreach($fasset_list as $fasset){ $i=1;?>
		<tr>
			<td><?php echo $i;?></td>
			<td><?php echo $fasset['franchise_name'];?></td>
			<td><?php echo $this->db->query("SELECT l.store_id,m.store_name  FROM m_franchise_store_link l JOIN m_apk_store_types m ON m.id=l.store_id WHERE l.is_active=1 AND l.franchise_id=?",$fasset['franchise_id'])->row()->store_name;?></td>
			<td><?php echo $fasset['asset_name'];?></td>
			<td><?php echo $fasset['asset_serialno'];?></td>
			<?php $accessory_list=$this->db->query("SELECT GROUP_CONCAT(a.accessory_name) AS accessory_name  FROM m_franchise_asset_link l  JOIN `m_asset_accessory_info` a ON a.id=l.accessory_id WHERE a.is_active=1 AND l.is_active=1 AND franchise_id=?",$fasset['franchise_id'])->row()->accessory_name;?>
			<td><?php echo $accessory_list?></td>
			<td><?php echo format_datetime($fasset['created_on'])?></td>
			<td><?php echo $fasset['createdby']?></td>
			<td><?php echo $fasset['modified_on']?format_datetime($fasset['modified_on']):'na';?></td>
			<td><?php echo $fasset['modifiedbyname']?$fasset['modifiedbyname']:'na'?></td>
			<td><a onclick="update_franasset(<?php echo $fasset['franchise_id']?>)" style="cursor: pointer;">Edit</a></td>
		</tr>
		</tbody>
		<?php $i++; }?>
		<?php }?>
	</table>

<div id="franasset_edit_div" title="Edit Franchise Asset" style="display:none;">
	<form action="<?php echo site_url('admin/p_edit_franasset') ?>" method="post" form-validate="parsley" id="update_franasset_form">
	
		<table cellspacing="5">
		
			<tr><td><input type="hidden" value="" name="franchise_id"></td></tr>
				<tr>
					<td><b>Asset</b></td><td><b>:</b></td>
						<td><select name="asset_id" id="asset_id" style="width:250px;" data-required="true">
						<?php $asset_list=$this->db->query("select id,asset_name from m_asset_info order by asset_name asc ")->result_array();?>
							<?php foreach($asset_list as $as){
								$selected = ($as['id']==$fasset_list[0]['asset_id'])?true:false;
							?>
							<option value="<?=$as['id']?>" <?php echo set_select('id',$as['id'],$selected)?> ><?=$as['asset_name']?></option>
							<?php }?>
						</select>
					</td>
				</tr>
				<tr>
					<td><b>Serial no</b></td>
					<td><b>:</b></td>
					<td><input type="text" name="asset_slno" value="<?php echo $fasset_list[0]['asset_serialno'] ?>" data-required="true"></td></tr>
				<tr>
				<tr>
					<td><b>Accessory List</b></td>
					<td><b>:</b></td>
					<td>
						<?php 
							$accessorys_list=$this->db->query("select id,accessory_name from m_asset_accessory_info  where asset_id=? group by id order by accessory_name asc",$fasset_list[0]['asset_id'])->result_array();
							
							$fran_accessory_list=$this->db->query("select group_concat(accessory_id) as accessory_id from  m_franchise_asset_link where franchise_id=? and is_active=1",$fasset_list[0]['franchise_id'])->row()->accessory_id;
						?>
						<select class="chzn-select"  name="accessory[]" id="accessory" multiple="multiple" style="width:250px;" data-required="true" prev_selected_accessids="<?php echo $fran_accessory_list;?>"></select>
					</td>
				</tr>
				<tr>
					<td><b>Store Type</b></td>
					<td><b>:</b></td>
					<td>
					<?php 
						$store_types=$this->db->query("select * from m_apk_store_types order by store_name asc")->result_array();
						
						$selected_storetype=$this->db->query("SELECT l.store_id FROM m_franchise_store_link l JOIN m_apk_store_types m ON m.id=l.store_id WHERE l.is_active=1 AND l.franchise_id=?",$fasset_list[0]['franchise_id'])->row()->store_id;
					?>
					<select name="store_type" id="store_type" style="width:250px;" data-required="true">
					<?php foreach($store_types as $s){
						$selected=$s['id']==$selected_storetype?true:false;
					?>
					<option value="<?=$s['id'] ?>" <?php echo set_select('store_type',$as['id'],$selected)?>"><?php echo $s['store_name']?></option>
					<?php }?>
					</select>
					</td>
				</tr>
		</table>
	
	</form>
</div>

</div>

<script>

$("#asset_id ,#accessory ,#store_type").chosen();
function update_franasset(franid)
{
	
	$("#franasset_edit_div").data("franid",franid).dialog("open");
	$("#asset_id").trigger("change");
}

$("#franasset_edit_div").dialog({
	modal:true,
	autoOpen:false,
	width:'500',
	height:'500',
	open:function()
	{
		var dlg=$(this);
		
	
		$("input[name='franchise_id']").val($(this).data('franid'));
		$("#asset_id").change(function() {
		$.post(site_url+'/admin/jx_get_all_assetaccessories',{asset_id:$("#asset_id").val()},function(resp){
			if(resp.status == 'error'){
				alert(resp.message);
			}else{
				var acc_list_html = '';
				 accessoriesids=$('select[name="accessory[]"]').attr('prev_selected_accessids');
				var prev_selected_accessids_arr = accessoriesids.split(',')
					$.each(resp.accessory_list,function(i,itm){
						var selected = ($.inArray(itm.id,prev_selected_accessids_arr) != -1)?'selected':'';
						acc_list_html += '<option value="'+itm.id+'" '+selected+' > '+itm.accessory_name+'</option>';
					});
					$('select[name="accessory[]"]').html(acc_list_html).trigger("liszt:updated");
			}
		},'json');

		});
	},
	buttons:{
		'Update':function(){
			var update_form=$("#update_franasset_form");
			if(update_form.parsley('validate'))
				update_form.submit();
			},
		'Cancel':function(){
			$(this).dialog('close');
			}
		}
});
</script>