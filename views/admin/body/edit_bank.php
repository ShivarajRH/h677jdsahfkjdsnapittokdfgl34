<div class="container">
<h2>Edit Bank Details</h2>
 <form action="<?php echo site_url('admin/p_editbank/'.$bank_details['id']);?>" method="post">
	<table cellspacing="5">
		<tr><td>Bank Name</td><td>:</td><td><input type="text" name="bank_name" value="<?php echo set_value('bank_name',$bank_details['bank_name'])?>" size="50"><?php echo form_error('bank_name','<div class="error">','</div>')?></td></tr>
		<tr><td>Short Code</td><td>:</td><td><input type="text" name="short_code" value="<?php echo set_value('short_code',$bank_details['short_code']);?>" size="20"><?php echo form_error('short_code','<div class="error">','</div>')?></td></tr>
		<tr><td>Branch Name</td><td>:</td><td><input type="text" name="branch_name" value="<?php echo set_value('branch_name',$bank_details['branch_name']);?>" size="50"><?php echo form_error('branch_name','<div class="error">','</div>')?></td></tr>
		<tr><td>Account Name</td><td>:</td><td><input type="text" name="account_name" value="<?php echo set_value('account_name',$bank_details['account_name']);?>" size="50"><?php echo form_error('account_name','<div class="error">','</div>')?></td></tr>
		<tr><td>Account Number</td><td>:</td><td><input type="text" name="account_number" value="<?php echo set_value('account_number',$bank_details['account_number']);?>" size="30"><?php echo form_error('account_number','<div class="error">','</div>')?></td></tr>
		<tr><td>IFSC Code</td><td>:</td><td><input type="text" name="ifsc_code" value="<?php echo set_value('ifsc_code',$bank_details['ifsc_code']);?>" size="20"><?php echo form_error('ifsc_code','<div class="error">','</div>')?></td></tr>
		<tr><td>Remarks </td><td>:</td><td><textarea name="remarks" style="width:100%;height: 100px;"><?php echo set_value('remarks',$bank_details['remarks']);?></textarea><?php echo form_error('remarks','<div class="error">','</div>')?></td></tr>
		<tr><td align="center"><input type="submit" value="Update Bank Details"></td></tr>
	</table>
 </form>
</div>