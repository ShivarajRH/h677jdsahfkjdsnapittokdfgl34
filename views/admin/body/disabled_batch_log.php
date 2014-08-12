<div class="container">
	<h2>Disabled Batch List</h2>
	
	<div style="float: right">
	
	<div class="dash_bar" >
		Date range: <input type="text" size="8" class="inp" id="ds_range" value="<?=$this->uri->segment(4)?>"> to <input size="8" type="text" class="inp"id="de_range" value="<?=$this->uri->segment(5)?>"> <input type="button" value="Show" onclick='showrange()'>
	</div>
	</div>
	<div style="float: left;">
	<div class="dash_bar" style="max-width: 500px;">
	Franchisee :<select name="fid" id="fid" >
	<option value="">All</option>
		<?php $fran_list=$this->db->query("select franchise_id,franchise_name from pnh_m_franchise_info order by franchise_name asc")->result_array();?>
		<?php foreach($fran_list as $f){?>
		<option value="<?php echo $f['franchise_id']?>"><?php echo $f['franchise_name'] ?></option>
		<?php }?>
	</select>	
	
	</div>
	</div>
	<table class="datagrid" width="100%">
		<thead>
			<th>Slno</th>
			<th>Franchise Name</th>
			<th>Transid</th>
			<th>Status</th>
			<th>Remarks</th>
			<th>Updated By</th>
			<th>Updated On</th>
		</thead>
		<tbody>
<?php //echo '<pre>';print_r($disabled_batch_det);die();?>		
<?php if($disabled_batch_det) { $i=1; foreach($disabled_batch_det as $disabled_batch){?>
<tr>
<td><?=$i;?></td>
<td><a href="<?php echo site_url("admin/pnh_franchise/{$disabled_batch['franchise_id']}")?>" target="_blank"><?=$disabled_batch['franchise_name']?></a></td>
<td><a href="<?php echo site_url("admin/trans/{$disabled_batch['transid']}")?>" target="_blank"><?=$disabled_batch['transid']?></a></td>
<td>
	<?php if($disabled_batch['batch_enabled']==0){
		echo "Batch Disabled";?>&nbsp;
		<a href="javascript:void(0)" onclick=enable_batch("<?php echo $disabled_batch['transid']?>")>Enable</a>
	<?php }else{
		echo "Batch Enabled";
	}
	?>
</td>
<td><?=$disabled_batch['remarks'];?></td>
<td><?=$disabled_batch['name'];?></td>
<td><?=format_date($disabled_batch['created_on']);?></td>
</tr>
<?php $i++;} }else{?>
<tr><td><div align="center"><b><?php echo "No Data Found";?></b></div></td></tr>
<?php }?>
</tbody>
</table>

</div>
<script>
function enable_batch(transid)
{
	if(transid)
	{
		if(confirm("Are you sure want to enable batch ?"))
		{
			$.post(site_url+"admin/to_enable_batch",{transid:transid},function(resp){
				if(resp.status=='success')
					window.location.reload();
			},'json');
		}
	}
	
}
$("#ds_range,#de_range").datepicker();

$("#fid").change(function(){
	location="<?=site_url("admin/disable_batch_log")?>/"+$("#fid").val();
});

function showrange()
{
	if($("#ds_range").val().length==0 ||$("#ds_range").val().length==0)
	{
		alert("Pls enter date range");
		return;
	}
	location='<?=site_url("admin/disable_batch_log/".(!$this->uri->segment(3)?"0":$this->uri->segment(3)))?>/'+$("#ds_range").val()+"/"+$("#de_range").val(); 
}
</script>