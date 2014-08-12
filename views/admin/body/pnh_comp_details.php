<div class="container">
<div class="dash_bar_right">Toll Free No : <span><?=$dets['toll_free_no']?></span></div>
<div class="dash_bar_right">SMS API No : <span><?=$dets['sms_no']?></span></div>
<h2>PNH Company details</h2>
<div class="clear"></div>

<div class="tabs">
<ul>
<li><a href="#details">Company Details</a></li>
<li><a href="#bank">Bank details</a></li>
</ul>

<div id="details">

<div style="float:right;width:400px;">
<h4>Documents</h4>
<div class="dash_bar"><a target="_blank" href="http://adm143.snapittoday.com/6dm1np2n3l/roc.pdf"></a><span>Certificate of Incorporation</span></div>
<div class="clear"></div>
<div class="dash_bar"><a target="_blank" href="http://adm143.snapittoday.com/6dm1np2n3l/vat.jpg"></a><span>VAT Certificate</span></div>
<div class="clear"></div>
<div class="dash_bar"><a target="_blank" href="http://adm143.snapittoday.com/6dm1np2n3l/pan.jpg"></a><span>PAN Card</span></div>
<div class="clear"></div>
</div>

<table class="datagrid" style="font-size:120%;width:500px;">
<thead><tr><th colspan="100%">Company details</th></tr></thead>
<tbody>
<tr><td width="150">VAT No :</td><td><b><?=$dets['vat_no']?></b></td></tr>
<tr><td>PAN No :</td><td><b><?=$dets['pan_no']?></b></td></tr>
<tr><td>ROC No :</td><td><b><?=$dets['roc_no']?></b></td></tr>
<tr><td>Toll Free No :</td><td><b><?=$dets['toll_free_no']?></b></td></tr>
<tr><td>SMS API No :</td><td><b><?=$dets['sms_no']?></b></td></tr>
</tbody>
</table>
</div>

<div id="bank">
<?php 
	foreach($this->db->query('select * from pnh_m_bank_info where is_active = 1 order by bank_name')->result_array() as $k=>$bank)
	{
?>
	<table class="datagrid" style="font-size:120%;width:450px;">
		<thead><tr><th colspan="100%">Bank details - <?php echo ($k+1);?></th></tr></thead>
		<tbody>
			<tr><td width="150">Bank Name : </td><td><b><?=$bank['bank_name']?></b></td></tr>
			<tr><td>Short Code : </td><td><b><?=$bank['short_code']?></b></td></tr>
			<tr><td>Account No : </td><td><b><?=$bank['account_number']?></b></td></tr>
			<tr><td>Account Name : </td><td><b><?=$bank['account_name']?></b></td></tr>
			<tr><td>Branch Name : </td><td><b><?=$bank['branch_name']?></b></td></tr>
			<tr><td>IFSC Code : </td><td><b><?=$bank['ifsc_code']?></b></td></tr>
		</tbody>
	</table>
<?php 
	}
?>
</div>

</div>

</div>
<?php
