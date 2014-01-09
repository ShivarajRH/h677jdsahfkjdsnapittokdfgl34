<div id="container">
<h2 class="page_title">Bank
<a href="<?php echo site_url('admin/add_bankdetails')?>" style="float:right;">Add Bank</a>
</h2>

<table class="datagrid">
<thead><th>Sl no</th><th>Bank Name</th><th>Branch Name</th><th>Account Name</th><th>Account Number</th><th>IFSC Code</th><th>Actions</th></thead>
<?php if($bank_list){?>
<tbody>
<?php $i=1; foreach($bank_list as $b){?>
<tr>
<td><?php echo $i;?></td><td><?php echo $b['bank_name']?></td><td><?php echo $b['branch_name'];?></td><td><?php echo ''?></td><td><?php echo $b['account_number']?></td><td><?php echo $b['ifsc_code']?></td>
<td>
<!-- <a href="javascript:void(0)" onclick="view_bank()">View</a>-->
 <a href="<?php echo site_url('admin/view_bank/'.$b['id'])?>">View</a>&nbsp;
<a href="<?php echo site_url('admin/p_editbank/'.$b['id'])?>">Edit</a>
</td>
</tr>
</tbody>
<?php $i++; }?>
<?php }else{?>
<tr><td><?php echo 'No Details Found'; ?></td></tr>
<?php }?>
</table>

<!--  <form action="" method="post">
<div id="viewbank_dlg" title="Bank Information">
<input type="hidden" name="bank_id" value="<?php echo $b['id'];?>">
<table cellspacing="5">
<tr><td><b>Bank Name</b></td><td>:</td><td><?php echo $bank_details['bank_name'];?></td></tr>
<tr><td><b>Branch Name</b></td><td>:</td><td><?php echo $bank_details['branch_name'];?></td></tr>
<tr><td><b>Account Number</b></td><td>:</td><td><?php echo $bank_details['account_number'];?></td></tr>
<tr><td><b>IFSC Code</b></td><td>:</td><td><?php echo $bank_details['ifsc_code'];?></td></tr>
</table>

</div>
</form>-->
</div>
<script>

/*function view_bank()
{
	$("#viewbank_dlg").dialog('open');
}

$("#viewbank_dlg").dialog({
		modal:true,
			autoOpen:false,
			width:'400',
			height:'500',
			open:function(){
			}
});*/

</script>