<?php //$bank_details= $bnk_info; ?>
<div id="container">
<h2 class="pagetitle"> Bank Info 
<a href="<?php echo site_url('admin/p_editbank/'.$bank_details['id'])?>">Edit</a>
</h2>
<?php if($bank_details){?>
<table cellspacing="5">
<tr><td><b>Bank Name</b></td><td>:</td><td><?php echo $bank_details['bank_name'];?></td></tr>
<tr><td><b>Short Code</b></td><td>:</td><td><?php echo $bank_details['short_code'];?></td></tr>
<tr><td><b>Branch Name</b></td><td>:</td><td><?php echo $bank_details['branch_name'];?></td></tr>
<tr><td><b>Account Name</b></td><td>:</td><td><?php echo $bank_details['account_name'];?></td></tr>
<tr><td><b>Account Number</b></td><td>:</td><td><?php echo $bank_details['account_number'];?></td></tr>
<tr><td><b>IFSC Code</b></td><td>:</td><td><?php echo $bank_details['ifsc_code'];?></td></tr>

</table>
<?php }?>
</div>