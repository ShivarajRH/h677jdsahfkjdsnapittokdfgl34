<div class="container">
<h1 class="page_title">PNH Executive Paid Log</h1>
<div align="right">
	<span style="float: left;font-weight: bold;font-size: 13px;">
		Total Listed : <?php echo $pnh_exec_account_details->num_rows() ?>
	</span>
	Filter by date <input type="text" id="inp_date" size="10" name="inp_date" value="<?php echo $fil_date?>" >
	<input type="button" value="submit" onclick="show_executivelog()">
</div>
<?php  if($pnh_exec_account_details->num_rows()){?>
<table class="datagrid datasorter" width="100%">
<thead>
<th>Sl no</th><th>From</th><th>Employee Name</th><th>Message</th><th>Reciept Status</th><th>Updated by</th><th>Updated on</th><th>Logged On</th>
</thead>
<?php 
$i=1;
foreach($pnh_exec_account_details->result_array() as $account_det){
?>
<tbody>
<tr>
	<td><?php echo $i;?></td>
	<td><?php echo $account_det['contact_no'];?></td>
	<td><?php echo $account_det['employee_name'];?></td>
	<td width="400"><?php echo $account_det['msg'];?></td>
	<td>
		<?php 
			if($account_det['reciept_status'])
			{
				echo '<p>'.$account_det['remarks'].'</p>';
			}else{
		?>
			<div align="right"><a href="javascript:void(0)" onclick='update_exec_account_log("<?=$account_det['log_id']?>")'>Update Status</a> &nbsp;</div>
		<?php } ?>
	</td>
	<td><?php echo $account_det['updatedby_name'];?></td>
	<td><?php echo $account_det['updated_on'];?></td>
	<td><?php echo $account_det['logged_on'];?></td>
</tr>
</tbody>
<?php $i++;}?>
</table>

<?php }else
{
	echo '<h3 align="left">No data found for - '.format_date($fil_date).'</h3>';
}?>
</div>
<div id="update_reciept_stat" title="Update receipt status">
	<form id="update_form" method="post">
		<input type="hidden" name="log_id" value="">
		<b>Remarks</b> : <br />
		<textarea rows="3" cols="35" name="remarks"></textarea>
	</form>
</div>

<script>
$('#inp_date').datepicker();

function show_executivelog()
{
	location.href = site_url+'/admin/pnh_executive_account_log/'+$('#inp_date').val();
}

$('#update_reciept_stat').dialog({autoOpen:false,modal:true,width:'auto',height:'auto',
									open:function(){
										$('#update_reciept_stat textarea[name="remarks"]').val('');
										$('#update_reciept_stat input[name="log_id"]').val($(this).data('logid'));	
									},
									buttons:{
										'Update':function(){
											var remarks = $('#update_reciept_stat textarea[name="remarks"]').val();
												if(!remarks.length)
												{
													alert("Please enter remarks");
												}else
												{
													$('#update_reciept_stat form').submit();
												}
										},'Close':function(){
											$('#update_reciept_stat').dialog('close');											
										}
									}
									});
									
function update_exec_account_log(id)
{
	$('#update_reciept_stat').data('logid',id).dialog('open');
}


</script>