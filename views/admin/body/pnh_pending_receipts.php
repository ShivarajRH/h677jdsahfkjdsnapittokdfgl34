<script>
$('.leftcont').hide();
</script>
<div class="container">
<div id="srch_results"></div>
<div style="float: right;">
<fieldset><legend><b>Search By Cheque Number:</b></legend><input  type="text" class="inp" style="width:170px;" id="c_srch">
<input type="hidden" name="search_rid" value="0">
</fieldset>

</div>
	
<?php if($this->uri->segment(2)){ $type=$this->uri->segment(3); if(!$type) $type=0;?>
<div class="dash_bar<?=$type==2?" dash_bar_red":""?>"><a href="<?=site_url("admin/pnh_receiptsbytype/2")?>"></a>Post Dated Cheques</div>
<div class="dash_bar<?=$type==1?" dash_bar_red":""?>"><a href="<?=site_url("admin/pnh_receiptsbytype/1")?>"></a>Today's Receipts</div>
<div class="dash_bar<?=$type==4?" dash_bar_red":""?>"><a href="<?=site_url("admin/pnh_receiptsbytype/4")?>"></a>Processed Receipts</div>
<div class="dash_bar<?=$type==3?" dash_bar_red":""?>"><a href="<?=site_url("admin/pnh_receiptsbytype/3")?>"></a>Realized Receipts</div>
<div class="dash_bar<?=$type==5?" dash_bar_red":""?>"><a href="<?=site_url("admin/pnh_receiptsbytype/5")?>"></a>Bounced Cheques</div>
<?php }?>
</br>
<form action="<?php echo site_url('admin/pnh_receiptsbytype')?>" method="post">
		
		<div class="dash_bar" style="padding: 5px;float:left;margin-left:6px;">
			Date range: <input type="text" size="8" class="inp" id="ds_range" name="ds_range" value="<?php echo set_value('ds_range',$st_date)?>">
				 to 
				<input size="8" type="text" class="inp" id="de_range" name="de_range" value="<?php echo set_value('de_range',$en_date)?>"> 
				<input type="button" value="Show" onclick='showrange()'>
		</div>
		
		<div style="padding: 6px;float:left;" class="dash_bar">
			Export Receipt Details:<select name="export_receiptdet">
			<option value="1">Today Receipts</option>
			<option value="2">Post Dated Receipts</option>
			<option value="3">Realized Receipts</option>
			<option value="4">Processed Receipts</option>
			<option value="5">Bounced Receipts</option>
			</select>
			<input type="submit" value="Generate">
		</div>
</form>
<div class="clear"></div>
<?php $types=array("Total Receipts available with us","Today's Receipts for submission","Post Dated Cheques","Realized Receipts","Processed Receipts","Bounced Cheques");?><h2><?=$types[$type].' :: '.$total_rows .'  '.'Total Value of Rs '.formatInIndianStyle($total_value['total'])?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?=isset($pagetitle)?$pagetitle: ''?></h2>
	<?php if($type==1){?>
	<div id="submit_bank" style="float:right;margin-top:-40px;">
		With Selected:<input type="button" value="Submit To Bank" onclick="load_bankdetails()">
	</div>
<?php }?>
								<!-- Filter Block -->
<div class="dash_bar_right">
Territory :<select	id="disp_terry" name="disp_terry">
						<option value="0">All</option>
						<?php foreach ($this->db->query("SELECT a.franchise_id,b.territory_id,c.territory_name FROM pnh_t_receipt_info a JOIN `pnh_m_franchise_info`b ON b.franchise_id=a.franchise_id JOIN `pnh_m_territory_info`c ON c.id=b.territory_id WHERE b.is_suspended=0 GROUP BY territory_id order by territory_name asc")->result_array() as $terr_det){?>
		            	<option  value="<?php echo set_value('disp_terry',$terr_det['territory_id']);?>"
		            	<?php echo $terr_det['territory_id']==$territory_id?>>
		            	<?php echo $terr_det['territory_name']?>
		            	</option>
						<?php }?>
       				</select>
</div>
     	
<div class="dash_bar" style="max-width:495px;">
Franchisee:<select id="disp_fran" style="width: 250px;">
				<option value="0" >All</option>
				<?php foreach ($this->db->query("SELECT a.franchise_id,b.franchise_name FROM pnh_t_receipt_info a JOIN `pnh_m_franchise_info`b ON b.franchise_id=a.franchise_id GROUP BY franchise_id
												 order by franchise_name asc")->result_array() as $fran_det){?>
            	<option id="fil_opt_fr_<?php echo $fran_det['franchise_id']; ?>"  value="<?php echo $fran_det['franchise_id'];?>"
            	<?php echo $fran_det['franchise_id']==$this->uri->segment(4)?"selected":""?>>
            	<?php echo $fran_det['franchise_name']?>
            	</option>
				<?php }?>
       	</select>
</div>

<div class="dash_bar" style="max-width:250px;">
Receipt Type :<select name="r_type" id="r_type">
				<option value=" ">All</option>
				<option value="0">Security Deposit</option>
				<option value="1">Topup</option>
			</select>
</div>
<?php if($type==4){?>
<div class="dash_bar">
 Bank:<select id="disp_bank" name="disp_bank" style="width: 120px;">
				<option value="0">All</option>
				<?php foreach ($this->db->query("select id,bank_name from pnh_m_bank_info order by bank_name asc")->result_array() as $bank_det){?>
            	<option value="<?php echo set_value('disp_bank',$bank_det['id']);?>"
            	<?php echo $bank_det['id']==$bank_id?>>
            	<?php echo $bank_det['bank_name']?>
            	</option>
				<?php }?>
       	</select>
</div>
<?php }?>
<?php if($type<=2){?>
<!--  <div class="dash_bar">
Amount Type:<select id="disp_type" name="disp_type"  style="width: 150px;">
				<option value=" ">All</option>
				<option value="0">Cash</option>
				<option value="1">Cheque</option>
				<option value="2">DD</option>
				<option value="3">Transfer</option>
	</select>
</div>-->

<div class="dash_bar">
Transit Type:<select id="t_type" name="t_type"  style="width: 150px;">
				<option value=" ">All</option>
				<option value="0">In Hand</option>
				<option value="1">Via Courier</option>
				<option value="2">With Executive</option>
			</select>
</div>
<?php }?>
				<!-- End Of Filter Block -->
<div class="clear"></div>

<?php if($type<=2){?>
<table class="datagrid" cellspacing="5" cellpadding="5" width="100%" >
<thead><tr><?php if ($type==1){?><th><input class="chk_all" type="checkbox"></th><?php }else{?><th></th><?php }?><th>Receipt Details</th><th>Franchise</th><Th>Type</Th><Th>Payment Details</Th><th>Payment Date</th><th>Remarks</th><th></th></tr></thead>
<?php if($receipts){?>
<tbody>
<?php foreach($receipts as $r){?>
<tr class="fr_receipt_det" fr_id="<?php echo $r['franchise_id']?>">
<?php if ($type=='1' && $r['in_transit']=='0'){?><td><input type="checkbox" value="<?php echo $r['receipt_id'];?>" class="receipt_check"></td><?php }else{?><td></td><?php }?>
<td width="200">
<div>
<table class="datagrid1" cellpadding="0" cellspacing="0">
<tr><td width="65"><b>Receipt Id</b></td><td><b>:</b></td><td width="180"><?=$r['receipt_id']?></td></tr>
<tr><td><b>Added on</b></td><td><b>:</b></td><td><?=format_datetime_ts($r['created_on'])?></td></tr>
<tr><td><b>Added by</b></td><td><b>:<b></td><td><?=$r['admin']?></td></tr>
</table>
</div>
</td>

<td><a href="<?=site_url("admin/pnh_franchise/{$r['franchise_id']}")?>"><b><?=$r['franchise_name']?></b></a></td>
<td><?=$r['receipt_type']==0?"Security Deposit":"Topup"?></td>
<td valign="top">
	<div>
		<table class="datagrid1" cellpadding="0" cellspacing="0">
			<tr><td width="80"><b>Mode</b></td><td><b>:</b></td><td><?php $modes=array("cash","Cheque","DD","Transfer");?><?=$modes[$r['payment_mode']]?></td></tr>
			<tr><td><b>Amount</b></td><td><b>:</b></td><td>Rs <?=$r['receipt_amount']?></td></tr> 
			<?php if($r['payment_mode']!=0){?><tr><td><b>Cheque no</b></td><td><b>:</b></td><td><?=$r['instrument_no']?></td></tr><?php }?>
			<?php if($r['bank_name']){?><tr><td><b>Bank</b></td><td><b>:</b></td><td><?=$r['bank_name']?></td></tr><?php }?>
			<tr><td><?php $transit_types=array("In Hand","Via Courier","With Executive");?><b>Transit Type</b></td><td><b>:</b></td><td><?=$transit_types[$r['in_transit']]?></td></tr>
			<?php if($r['modified_on']){?>
			<tr><td><b>Transit Status Modified By</b></td><td><b>:</b></td><td><?php echo $r['modifiedby']?></td></tr>
			<tr><td><b>Transit Status Modified On</b></td><td><b>:</b></td><td><?php echo format_datetime_ts($r['modified_on'])?></td></tr>
			<?php }?>
		</table>
	</div>
</td>
<td><?=date("d/m/Y",$r['instrument_date'])?></td>
<?php if($r['is_submitted']==1){?>
	<td><?=$r['reason']?></td>
<?php }else{?>
	<td><?=$r['remarks']?></td>
<?php }?>

<?php if($type==4){?>
<td>
<a href="javascript:void(0)" onclick='act_rec(<?=$r['receipt_id']?>)'>Realize</a> &nbsp; &nbsp;
<a href="javascript:void(0)" onclick='can_rec(<?=$r['receipt_id']?>)'>Cancel</a> &nbsp; &nbsp;
</td>
<?php }elseif($type==1 || $type==2){ ?>
	<td><a href="javascript:void(0)" onclick='can_rec(<?=$r['receipt_id']?>)'>Cancel</a></td> &nbsp; &nbsp;
<?php }?>
</tr>
<?php }?>
<?php }else{?>
<tr><td colspan="100%">no receipts to show</td></tr><?php }?>
</tbody>
</table>

<?php }elseif($type==4){?>
<table class="datagrid" cellspacing="5" cellpadding="5" width="100%">
<thead >
<th>Receipt Details</th><th>Franchisee Name</th><th>Payment Details</th><th>Payment Date</th><th>Submitted Bank Name</th><th>Remarks</th><th>Processed On</th><th>Processed By</th><th>Actions</th>
</thead>
<tbody>
<?php foreach($receipts as $r){?>
<?php
$no_days = date_diff_days(date('Y-m-d'),$r['submitted_on']);
$is_receipt_exp = 0;
if($no_days >=4 && $r['activated_on'] == 0)
	$is_receipt_exp = 1;
?>
<tr class="fr_receipt_det <?php echo $is_receipt_exp?'warn':''?>" fr_id="<?php echo $r['franchise_id']?>" >

<td>
	<div>
		<table class="datagrid1" cellpadding="0" cellspacing="0" width="100%">
			<tr><td width="65"><b>Receipt Id</b></td><td><b>:</b></td><td><?=$r['receipt_id']?></td></tr>
			<tr><td><b>Added on</b></td><td><b>:</b></td><td><?=format_datetime_ts($r['created_on'])?></td></tr>
			<tr><td><b>Added by</b></td><td><b>:<b></td><td><?=$r['admin']?></td></tr>
		</table>
	</div>
</td>
<td><a href="<?=site_url("admin/pnh_franchise/{$r['franchise_id']}")?>"><?=$r['franchise_name']?></a></td>
<td>
	<div>
		<table class="datagrid1" cellpadding="0" cellspacing="0" width="100%">
			<tr><td width="80"><b>Mode</b></td><td><b>:</b></td><td><?php $modes=array("cash","Cheque","DD","Transfer");?><?=$modes[$r['payment_mode']]?></td></tr>
			<tr><td><b>Type</b></td><td><b>:</b></td><td><?=$r['receipt_type']==0?"Security Deposit":"Topup"?></td>
			<tr><td><b>Amount</b></td><td><b>:</b></td><td>Rs <?=$r['receipt_amount']?></td></tr> 
			<?php if($r['payment_mode']==1){?><tr><td><b>Cheque no</b></td><td><b>:</b></td><td><?=$r['instrument_no']?></td></tr><?php }?>
			<?php if($r['bank_name']){?><tr><td><b>Bank</b></td><td><b>:</b></td><td><?=$r['bank_name']?></td></tr><?php }?>
		</table>
	</div>
</td>
<td><?php if($r['instrument_date']){?><?=date("d/m/Y",$r['instrument_date'])?><?php }?></td>
<td><?php if($r['submit_bankname']){?><?=$r['submit_bankname']?><?php }?></td>
<td><?php if($r['submittedremarks']){?><?=$r['submittedremarks']?><?php }?></td>
<td><?php if($r['submitted_on']){?><?=format_date($r['submitted_on'])?><?php }?></td>
<td><?php if($r['submittedby']){?><?=$r['submittedby']?><?php }?></td>
<td>
<?php if(($r['is_submitted']==1 && $r['status']==0)|| ($r['activated_on']==0)){?>
<a href="javascript:void(0)" onclick='act_rec(<?=$r['receipt_id']?>)'>Realize</a> &nbsp; &nbsp;
<a href="javascript:void(0)" onclick='cancel_onprocessd(<?=$r['receipt_id']?>)'>Cancel</a>&nbsp; &nbsp;
<?php if ($r['is_deposited']==0){?>
<a href="javascript:void(0)" onclick="upload_depositslip(<?=$r['receipt_id']?>)">Upload</a><?php }else{?> 
<a href="javascript:void(0)" onclick="view_uploaddetails(<?=$r['receipt_id']?>)">View Upload</a>&nbsp;&nbsp;<?php }?>
<?php } ?>
</td>
</tr>
<?php }?>
</tbody>
</table>

<?php }elseif ($type==3){?>
<table class="datagrid" width="100%">
<thead><th>Receipt Details</th><th>Franchisee Name</th><th>Payment Deatils</th><th>Processed Details</th><th>Realized Details</th><!-- <th>Remarks</th><th>Realized On</th><th>Realized By</th> --></thead>
<tbody>
<?php foreach($receipts as $r){?>
<tr class="fr_receipt_det" fr_id="<?php echo $r['franchise_id']?>">
<td>
	<div>
		<table class="datagrid1" cellpadding="0" cellspacing="0">
		<tr><td width="65"><b>Receipt Id</b></td><td><b>:</b></td><td><?=$r['receipt_id']?></td></tr>
		<tr><td><b>Added on</b></td><td><b>:</b></td><td><?=format_datetime_ts($r['created_on'])?></td></tr>
		<tr><td><b>Added by</b></td><td><b>:<b></td><td><?=$r['admin']?></td></tr>
		</table>
	</div>
</td>
<td><a href="<?=site_url("admin/pnh_franchise/{$r['franchise_id']}")?>"><?=$r['franchise_name']?></a></td>
<td>
	<div>
		<table class="datagrid1" cellpadding="0" cellspacing="0">
			<tr><td width="80"><b>Mode</b></td><td><b>:</b></td><td><?php $modes=array("cash","Cheque","DD","Transfer");?><?=$modes[$r['payment_mode']]?></td></tr>
			<tr><td><b>Type</b></td><td><b>:</b></td><td><?=$r['receipt_type']==0?"Security Deposit":"Topup"?></td>
			<tr><td><b>Amount</b></td><td><b>:</b></td><td>Rs <?=$r['receipt_amount']?></td></tr> 
			<?php if($r['payment_mode']==1){?><tr><td><b>Cheque no</b></td><td><b>:</b></td><td><?=$r['instrument_no']?></td></tr><?php }?>
			<?php if($r['bank_name']){?><tr><td><b>Bank</b></td><td><b>:</b></td><td><?=$r['bank_name']?></td></tr><?php }?>
			<tr><td><b>Instrument Date</b></td><td><b>:</b></td><td><?php if($r['instrument_date']){?><?=date("d/m/Y",$r['instrument_date'])?><?php }?></td></tr>
		</table>
	</div>
</td>

<td>
<div>
	<table class="datagrid1" cellpadding="0" cellspacing="0">
		<tr><td><b>Bank</b></td><td>:</td><td><?=$r['submit_bankname']?></td></tr>
		<tr><td><b>Remarks</b></td><td><b>:</b></td><td><?=$r['submittedremarks']?></td></tr>
		<tr><td><b>Deposited On</b></td><td>:</td><td><?=format_date($r['submitted_on'])?></td></tr>
		<tr><td><b>Deposited By</b></td><td>:</td><td><?=$r['submitted_by']?></td></tr>
	</table>
</div>
</td>
<td>
	<div>
		<table class="datagrid1" cellpadding="0" cellspacing="0">
		<tr><td><b>Realized On</b></td><td><b>:</b></td><td><?=$r['cheq_realized_on']!=null?format_date($r['cheq_realized_on']):format_date_ts($r['activated_on']);?></td></tr>
		<tr><td><b>Remarks</b></td><td>:</td><td><?=$r['reason']?></td></tr>
		<tr><td><b>Updated By</b></td><td>:</td><td><?=$r['activated_by']?></td></tr>
		<tr><td><b>Updated On</b></td><td>:</td><td><?=format_date_ts($r['activated_on'])?></td></tr>
		</table>

	</div>
</td>
</tr>
<?php }?>
</tbody>
</table>
<?php } elseif ($type==5){?>
<table class="datagrid" width="100%">
<?php if($receipts){?>
<thead><th>Receipt Details</th><th>Franchisee Name</th><th>Payment Details</th><th>Processed Details</th><th>Cancelled Details</th><!-- <th>Cancelled On</th><th>Cancelled By</th> --></thead>
<tbody>
<?php foreach($receipts as $r){?>
<tr class="fr_receipt_det" fr_id="<?php echo $r['franchise_id']?>">
<td class="receipt_det">
<div>
<table class="datagrid1" cellpadding="0" cellspacing="0">
<tr><td width="65"><b>Receipt Id</b></td><td><b>:</b></td><td><?=$r['receipt_id']?></td></tr>
<tr><td><b>Added on</b></td><td><b>:</b></td><td><?=format_datetime_ts($r['created_on'])?></td></tr>
<tr><td><b>Added by</b></td><td><b>:<b></td><td><?=$r['admin']?></td></tr>
</table>
</div>
</td>
<td><a href="<?=site_url("admin/pnh_franchise/{$r['franchise_id']}")?>"><?=$r['franchise_name']?></a></td>
<td>
	<div>
		<table class="datagrid1" cellpadding="0" cellspacing="0">
			<tr><td width="80"><b>Mode</b></td><td><b>:</b></td><td><?php $modes=array("cash","Cheque","DD","Transfer");?><?=$modes[$r['payment_mode']]?></td></tr>
			<tr><td><b>Type</b></td><td><b>:</b></td><td><?=$r['receipt_type']==0?"Security Deposit":"Topup"?></td>
			<tr><td><b>Amount</b></td><td><b>:</b></td><td>Rs <?=$r['receipt_amount']?></td></tr> 
			<tr><td><b>Payment Date</b></td><td><b>:</b></td><td><?=date("d/m/Y",$r['instrument_date'])?></td></tr>
			<?php if($r['payment_mode']==1){?><tr><td><b>Cheque no</b></td><td><b>:</b></td><td><?=$r['instrument_no']?></td></tr><?php }?>
			<?php if($r['bank_name']){?><tr><td><b>Bank</b></td><td><b>:</b></td><td><?=$r['bank_name']?></td></tr><?php }?>
		</table>
	</div>
</td>

<td>
	<div>
		<table class="datagrid1" cellpadding="0" cellspacing="0">
		<tr><td><b>Bank</b></td><td>:</td><td><?=$r['submit_bankname']?></td></tr>
		<tr><td><b>Remarks</b></td><td><b>:</b></td><td><?=$r['submittedremarks']?></td></tr>
		<tr><td><b>Deposited On</b></td><td>:</td><td><?=format_date($r['submitted_on'])?></td></tr>
		<tr><td><b>Deposited By</b></td><td>:</td><td><?=$r['submitted_by']?></td></tr>
		</table>
	</div>

</td>
<td>
	<div>
		<table class="datagrid1" cellpadding="0" cellspacing="0">
		<tr><td width="80"><b>Cancel Status</b></td><td><b>:</b></td><td><?php $cstatus=array("Reversed","Return","Bounce");?><?=$cstatus[$r['cancel_status']];?></td></tr>
		<tr><td><b>Cheque Cancelled On</b></td><td><b>:</b></td><td><?=$r['cheq_cancelled_on']!=null?format_date($r['cheq_cancelled_on']):format_datetime($r['modified_on'])?></td></tr>
		<tr><td><b>Remarks</b></td><td><b>:</b></td><td><?=$r['cancel_reason']?$r['cancel_reason']:$r['reason']?></td></tr>
		<tr><td><b>Updated By</b></td><td><b>:</b></td><td><?= $r['activated_by']?$r['activated_by']:$r['reversed_by']?> </td></tr>
		<tr><td><b>Updated On</b></td><td><b>:</b></td><td><?=$r['cancelled_on']!=null?format_datetime($r['cancelled_on']):format_datetime_ts($r['modified_on'])?></td></tr>
		</table>
	</div>
</td>
</tr>
</tbody>
<?php }?>
<?php }?>
</table>
<?php }?>
&nbsp;&nbsp;
<?php if($type==1){?>
<div id="submit_bank">With Selected:<input type="button" value="Submit To Bank" onclick="load_bankdetails()"></div>
<?php }?>
<div id="submit_tobank"title="Submit To Bank"  data-placeholder="Select Bank" style="display:none;">
<form id="submit_cheks" action="<?php echo site_url('admin/update_check_issubmitted');?>" method="post" data-validate="parsley">
<input type="hidden" name="rids" class="rids" value= "" >
<input type="hidden" name="action" class="action" value="1">
<table cellspacing="5" >
			<tr>
				<td>Total cheques Selected</td>
				<td>:</td>
				<td><b id="ttl_selected_rids">0</b></td>
			</tr>	
			<tr>
				<td>Select bank</td>
				<td>:</td>
				<td><select name="bank_id" id="choose_bank"
					style="width: 180px;" data-required="true">
						<?php foreach($this->db->query("select * from pnh_m_bank_info order by bank_name asc")->result_array() as $bank){?>
						<option value="<?php echo $bank['id'];?>">
							<?php echo $bank['bank_name']?>
						</option>
						<?php }?>
				</select>
				</td>
			</tr>
			<tr>
			<td>Choose Date of submission</td>
			<td>:</td>
			<td><input type="text" name="check_date" class="check_date" style="width:90px;height:25px;" value="<?php echo set_value('check_date');?>"  data-required="true"/></td>
			</tr>				
		
			<tr>
				<td>Remarks (if any)</td>
				<td>:</td>
				<td><textarea name="checkreamarks" data-required="true"></textarea></td>
			</tr>
		</table>
</form>
</div>

<div id="upload_depositeslips" title="Upload scanned deposited slip">
	<form action="<?php echo site_url('admin/upload_depositedslips')?>" method="post" id="upload_depositdslips_form" target="hndl_upload_depositdslips_form" enctype="multipart/form-data" >
	<table>
	<tr><td>Deposited Reference Number</td><td>:</td><td id="receipt_id"><b></b><input type="hidden" name="receipt_id" id="inp_receipt_id" ></td></tr>
	<tr><td>Receipt Ids</td><td>:</td><td id="process_id"><b></b><input type="hidden" name="process_id" id="inp_process_id" ></td></tr>
	<tr><td>Upload Files</td><td>:</td><td><input type="file" name="image" id="file" data-required="true"></td></tr>
	<tr><td>Remarks</td><td>:</td><td><textarea name="deposited_remarks"></textarea></td></tr>
	</table>
	</form>
	<iframe class="hndl_upload_depositdslips_form" name="hndl_upload_depositdslips_form" style="width: 1px;height: 1px;border:0px;"></iframe>
</div>


<div id="view_uploaddet" Title="Uploaded Deposited Slip Details">
<table id="view_upload_deposited">
<tr><td>Deposited Reference Number</td><td>:</td><td id="uploaded_depositedrefno"><b></b></td></tr>
<tr><td>Receipt Id</td><td>:</td><td id="deposited_receiptid"><b></b></td><td><input type="hidden" name="receipt_ids"></td></tr>
<tr><td>Uploaded file</td><td>:</td><td id="deposited_uploadedimage"></td></tr>
<tr><td>Remarks</td><td>:</td><td id="deposited_remarks"><b></b></td></tr>
<tr><td>Uploaded By</td><td>:</td><td id="deposited_uploadedby"><b></b></td></tr>
<tr><td>Uploaded On</td><td>:</td><td id="deposited_uploadedon"><b></b></td></tr>
</table>
</div>

<div id="cancel_receipt" title="Receipt Cancellation">
<form id="cancel_receipt_frm" method="post">
<table width="100%">
<tr><td><input type="hidden" name="can_receiptid" id="can_receiptid"></td></tr>
<tr><td>Franchise Name</td><td>:</td><td id="can_recptfran"><b></b></td></tr>
<tr><td>Receipt ID</td><td>:</td><td id="can_recptid"><b></b></td></tr>
<tr><td>Cheque Number</td><td>:</td><td id="can_recptchqueno"><b></b></td></tr>
<tr><td>Select Cheque Return Status</td><td>:<span class="red_star">*</span></td><td><select name="cancel_status" id="cancel_status" data-required="true"><option value=" ">Choose</option><option value="1">Return</option><option value="2">Bounce</option></select></td></tr>
<tr><td>Cheque Returned On</td><td>:<span class="red_star">*</span></td><td><input type="text" name="cheq_canceled_on" value="" data-required="true"  readonly="readonly"></td></tr>
<tr id="dbt_amt"><td>Bounce Charges</td><td>:</td><td><input type="text" name="debit_amt" id="debit_amt" > Rs </td></tr>
<tr><td>Remarks</td><td>:<span class="red_star">*</span></td><td><textarea name="act_remarks" id="act_remarks" data-required="true"></textarea></td></tr>
<tr><td><b>Notify via SMS</b></td></tr>
<tr><td>Franchisee</td><td>:</td><td><input type="checkbox" name="sms" value="1"></td></tr>
<tr><td>Territory Manager</td><td>:</td><td><input type="checkbox" name="tm_sms" value="1"></td></tr>
</table>
</form>
</div>

<div id="load_searchcheq" title="Cheque Details">
<table  id="searchcheq_details">
<tbody></tbody>
</table>
</div>

<div id="realize" title="Realize">
<form id="ac_form" action="<?php echo site_url('admin/pnh_pending_receipts')?>" method="post">
<table>
    <tr>
        <td><b>Receipt ID</b></td>
        <td><b>:</b></td>
        <td id="r_receiptid"><b></b></td>
    </tr>
    <tr>
        <td style="vertical-align: bottom;"><b>Realized On</b></td>
        <td style="vertical-align: bottom;"><b>:</b><span class="red_star">*</span></td>
        <td><input type="text" name="cheq_realizedon" value=""  data-required="true" readonly="readonly"></td>
    </tr>
    <tr>
        <td style="vertical-align: top;"><b>Remarks</b></td>
        <td style="vertical-align: top;"><b>:</b><span class="red_star">*</span></td>
        <td><textarea name="msg" class="a_reason" data-required="true" style="width: 350px; height: 171px;"></textarea></td>
    </tr>
</table>
<input type="hidden" name="type" class="a_type" value="">
<input type="hidden" name="rid" class="a_rid" value="">
</form>
</div>


</div>
<?php if(isset($pagination)){?>
<div class="pagination" align="right">
	<?php echo $pagination;?>
</div>
<?php } ?>

<script>
//$('input[name="cheq_realizedon"]').datepicker({maxDate: '+0d'});
$('input[name="cheq_realizedon"]').datepicker({beforeShowDay: onlyPastdays});

$('input[name="cheq_canceled_on"]').datepicker({beforeShowDay: onlyPastdays});

function onlyPastdays(date) {
	var day = date.getDay();
	var today = new Date();
	today.setDate(today.getDate());
	return [(date <today), ''];
	}

$("#ds_range,#de_range").datepicker();
$(".check_date").datepicker({minDate:0});

$('#disp_fran option:gt(0)').addClass('valid_opt').hide();
$(function(){
	$('.fr_receipt_det').each(function(){
		$('#fil_opt_fr_'+$(this).attr('fr_id')).removeClass('valid_opt').show();
	});
	$('#disp_fran option.valid_opt').remove();	
});


function showrange()
{
	if($("#ds_range").val().length==0 ||$("#ds_range").val().length==0)
	{
		alert("Pls enter date range");
		return;
	}
	location='<?=site_url("admin/pnh_receiptsbytype/".(!$this->uri->segment(3)?$type:$this->uri->segment(3)))?>/'+$("#ds_range").val()+"/"+$("#de_range").val(); 
}


function act_rec(rid)
{
	$('#realize').data('receipt_id',rid).dialog('open');
}

$('#realize').dialog({

	model:true,
	autoOpen:false,
	width:'500',
	height:'330',
	open:function(){
		dlg = $(this);
		$('#ac_form input[name="rid"]',this).val(dlg.data('receipt_id'));
		$(".a_type").val("act");
		$("#r_receiptid b",this).html(dlg.data('receipt_id'));
		
	},
	buttons:{
		'Submit':function(){
			var realize_frm = $("#ac_form",this);
			 if(realize_frm.parsley('validate'))
				 {
	
					 $('#ac_form').submit();
					$(this).dialog('close');
				}
		       else
		       {
		       	alert('All Fields required!!!');
		       }
			
		},
		'Cancel':function()
		{
			$(this).dialog('close');
		},
	}
	
});
function can_rec(rid)
{
	var reason=prompt("Reason For Cancel");
	if(!reason || reason.length==0)
		return false;
        
	$(".a_rid").val(rid);
	$(".a_type").val("can");
	$(".a_reason").val(reason);
	$("#ac_form").submit();
}

function cancel_onprocessd(rid)
{
	$("#cancel_receipt").data('receipt_id',rid).dialog('open');
}

$("#cancel_receipt").dialog({
	modal:true,
	autoOpen:false,
	width:'550',
	height:'450',
	open:function(){
		dlg = $(this);
		$("#dbt_amt").hide();
		$('#cancel_receipt_frm input[name="can_receiptid"]',this).val(dlg.data('receipt_id'));
		//$('#cancel_receipt_frm select[name="cancel_status"]',this).val("");
		$('#can_recptfran b').html("");
		$('#can_recptchqueno b').html("");
		$('#can_recptid b').html("");
		$('#cancel_status').val("")
		$('#debit_amt').val("");
		$('#act_remarks').val("");
		$.post(site_url+'/admin/load_frandet_ofcanreceipt',{receipt_id:dlg.data('receipt_id')},function(result){
			   if(result.status == 'error')
				{
					alert("Receipt details not found");
					
			  	}
			   else
			   {
				   $('#can_recptfran b').html(result.fran_receiptdet.franchise_name);
				   $('#can_recptchqueno b').html(result.fran_receiptdet.instrument_no);
				   $('#can_recptid b').html(result.fran_receiptdet.receipt_id);
				}
	},'json');
},
	buttons:{
		'Submit':function(){
			var dlg= $(this);
			var c=confirm("Are you sure you want to cancel");
			if(c)
			{
                                var frm_cancel = $("#cancel_receipt_frm",this);
			 	if(frm_cancel.parsley('validate'))
				{
			 		 $.post(site_url+'/admin/jx_cancel_processedreceipts',frm_cancel.serialize(),function(resp){
                                            if(resp.status == 'success')
                                            {
                                                    $("#cancel_receipt").dialog('close');
                                                    location.href = $(location).attr('href');
                                            }
                                            else {
                                                print(resp);
                                            }
                                        },'json');
		 		}
                                else
                                {
                                 alert('All Fields Required!!!');
                                }
			}
			else
				return false;
			}
	}
	
});


function upload_depositslip(rid)
{
	$('#upload_depositeslips').data('receipt_id',rid).dialog('open');
}

$('#upload_depositeslips').dialog({
	modal:true,
	autoOpen:false,
	width:'500',
	height:'300',
		open:function(){
			dlg = $(this);
			$("#process_id b").text("");
			$("#receipt_id b").text("");
			$("#inp_process_id b").val("");
			$("#inp_receipt_id b").val("");

			// ajax request fetch receipt details
			   $.post(site_url+'/admin/jx_load_receiptdet',{receipt_id:dlg.data('receipt_id')},function(result){
			   if(result.status == 'error')
				{
					alert("Receipt details not found");
					dlg.dialog('close');
					
			    }
			   else
			   {
				   $("#process_id b").text(result.receipt_det.receipt_id);
				   $("#receipt_id b").text(result.receipt_det.process_id);
				   $("#inp_process_id").val(result.receipt_det.process_id);
				   $("#inp_receipt_id").val(result.receipt_det.receipt_id);
				   
				}
			    
		},'json');
	},
	buttons:{
		'Submit':function(){
			var dlg= $(this);
			
			var frm_upload_depositslips = $("#upload_depositdslips_form",this);
				 if(frm_upload_depositslips.parsley('validate')){

					 frm_upload_depositslips.submit();
					 $('#upload_depositeslips').dialog('close');
				}
	            else
	            {
	            	alert('Files Need to be uploaded!!!');
	            }
		},
		'Cancel':function(){
			$(this).dialog('close');
		}
	}
		
});

function view_uploaddetails(rid)
{
	$("#view_uploaddet").data('receipt_id',rid).dialog('open');
}

$("#view_uploaddet").dialog({
	modal:true,
	autoOpen:false,
	width:'800',
	height:'300',
	open:function(){
		dlg = $(this);
		$("#uploaded_depositedrefno b").html("");
		$("#deposited_receiptid b").html("");
		$("#deposited_uploadedimage b").html("");
		$("#deposited_remarks b").html("");
		$("#deposited_uploadedby b").html("");
		$("#deposited_uploadedon b").html("");
	$.post(site_url+'/admin/jx_load_depositeddetails',{deposited_receiptid:dlg.data('receipt_id')},function(result){
			if(result.status=="error")
			{
				alert(result.message);
				
			}
			else
			{
				$("#uploaded_depositedrefno b").html(result.deposited_det.deposited_reference_no);
				$("#deposited_receiptid b").html(result.deposited_det.receipt_ids);
				$("#deposited_uploadedimage").html(base_url+'resources/employee_assets/image/'+result.deposited_det.scanned_url);
				//$("#deposited_uploadedimage").html(result.deposited_det.scanned_url);
				$("#deposited_remarks b").html(result.deposited_det.remarks);
				$("#deposited_uploadedby b").html(result.deposited_det.uploadedby);
				$("#deposited_uploadedon b").html(result.deposited_det.uploaded_on);
				
			}
		
	},'json');
	},
	buttons:{
		'Close':function(){
			$(this).dialog('close');
		}
	}
		

	
});

function hndl_formsubmit_inframe(resp)
{
	alert(resp.message);
	
	if(resp.status == 'success')
		$('#upload_depositeslips').dialog('close');
	
}

function load_bankdetails()
{
	
	if($('.receipt_check:checked').length == 0 )
	{
	    alert('Select cheques to be submitted');
	    return false;
    }
	else
	{
		$('#submit_tobank').dialog('open');
	}
}


$('#submit_tobank').dialog({
	modal:true,
	autoOpen:false,
	width:'500',
	height:'400',
	open:function(){
		dlg = $(this);
		$('#ttl_selected_rids',dlg).text($(".receipt_check:checked").length);
		
	},
	buttons:{
		'Cancel' :function(){
		 $(this).dialog('close');
		},
		'Submit':function(){
			var dlg = $(this);
			var rids=[];
			$(".receipt_check:checked").each(function(){
				rids.push($(this).val());
			});
			rids=rids.join(",");
			$('#submit_tobank input[name="rids"]').val(rids);
			var frm_checksubmit = $("#submit_cheks",this);
			 if(frm_checksubmit.parsley('validate')){
				 $.post(site_url+'/admin/update_check_issubmitted',frm_checksubmit.serialize(),function(resp){
			          if(resp.status == 'success')
                      {
                      	location.href = $(location).attr('href');
                      	dlg.dialog('close');
                      }
                },'json');
            }else
            {
             	alert('All Fields are required!!!');
            }
          
    },
}
});

$(".chk_all").click(function(){
	if($(this).attr("checked"))
		$(".receipt_check").attr("checked",true);
	else
		$(".receipt_check").attr("checked",false);
});

//$("#disp_fran").chosen();

$("#disp_terry").change(function(){
	v=$(this).val();
	if(v != 0) 
		location="<?=site_url("admin/pnh_receiptsbyterritory/".$type)?>/"+v;
	else	
		location="<?=site_url("admin/pnh_receiptsbytype/".$type)?>";
});

$("#disp_fran").change(function(){
	f=$(this).val();
	if(f != 0) 
		location="<?=site_url("admin/pnh_receiptsbyfranchise/".$type)?>/"+f;
	else
		location="<?=site_url("admin/pnh_receiptsbytype/".$type)?>";		
});

$("#disp_bank").change(function(){
	b=$(this).val();
	if(b != 0) 
		location="<?=site_url("admin/pnh_receiptsbybank/".$type)?>/"+b;
	else
		location="<?=site_url("admin/pnh_receiptsbytype/".$type)?>"; 
	
});

$("#disp_type").change(function(){
	t=$(this).val();

	if(t)
	
		location="<?=site_url("admin/pnh_receiptsbycashtype/".$type)?>/"+t;
	else 
		location="<?=site_url("admin/pnh_receiptsbytype/".$type)?>"; 
		
	
});



$("#r_type").change(function(){
	r=$(this).val();

	if(r)
		location="<?=site_url("admin/pnh_receiptsbyr_type/".$type)?>/"+r;
	else 
		location="<?=site_url("admin/pnh_receiptsbytype/".$type)?>"; 
});

$("#t_type").change(function(){
	t=$(this).val();
	//alert(t);
	if(t)
		location="<?=site_url("admin/pnh_receiptsbytrans_type/".$type)?>/"+t;
	else 
		location="<?=site_url("admin/pnh_receiptsbytype/".$type)?>"; 
		
	
});

$("#cancel_status").change(function(){
	$("#dbt_amt").hide();

	if($(this).val()=='2'){
		$("#dbt_amt").show();
	}
	else 
	{
		$("#dbt_amt").hide();
	}
});

var jHR=0,search_timer;
$("#c_srch").keyup(function(){
	q=$(this).val();
	if(q.length<3)
		return true;
	if(jHR!=0)
		jHR.abort();
	window.clearTimeout(search_timer);
	search_timer=window.setTimeout(function(){
	jHR=$.post('<?=site_url("admin/pnh_jx_searchcheque")?>',{q:q},function(data){
		$("#srch_results").html(data).show();
	});},200);
});

function cheque_detail_callb(receipt_id,instrument_date,status,created_on,instrument_no)
{
	$('#srch_results').html('').hide();
	$("#c_srch").val(instrument_no).focus();
	$("input[name='search_rid']").val(receipt_id);
	load_receiptdet(receipt_id);
}
function load_receiptdet(receipt_id)
{
	$("#load_searchcheq").data('receipt_id',receipt_id).dialog('open');
}

$("#load_searchcheq").dialog({
	modal:true,
	width:'1000',
	height:'450',
	autoResize:true,
	autoOpen:false,
	open:function(){
		dlg = $(this);
		$('#searchcheq_details tbody').html("");
		$.post(site_url+'/admin/jx_request_chequedetails/',{search_receiptid:dlg.data('receipt_id')},function(result){
			if(result.status == 'error')
			{
				alert("Cheque Details not found");
				dlg.dialog('close');
		    }
			else
			{
				var r = result.receipt_det;
				var tbl_row=
					  	"<tr>"
					  	+"<td><div><fieldset><legend><b>Franchisee Details</b></legend><table><tr><td><b>Name</b></td><td><b>:</b></td><td>"+r.franchise_name+"</td></tr><tr><td><b>Territorry|Town</b></td><td><b>:</b></td><td>"+r.territory_name+"|"+r.town_name+"</td></tr><tr><td><b>Contact no</b></td><td><b>:</b></td><td>"+r.login_mobile1+","+r.login_mobile2+"</td></tr></fieldset></div></table>"
					  	+"</tr>"
					  	+"<tr>"
					  	 tbl_row+="<td width=25%><div><fieldset><legend><b>Cheque Details</b></legend><table class=datagrid1><tr><td><b>Cheque no</b></td><td><b>:</b></td><td>"+r.instrument_no+"</td></tr><tr><td><b>Cheque Date</b></td><td><b>:</b></td><td>"+r.instrument_date+"</td></tr><tr><td><b>Amount</b></td><td><b>:</b></td><td>Rs "+r.receipt_amount+"</td></tr><tr><td><b>Bank</b></td><td><b>:</b></td><td>"+r.bank_name+"</td></tr><tr><td><b>Created On</b></td><td><b>:</b></td><td>"+r.created_on+"</td></tr><tr><td><b>Created by</b></td><td><b>:</b></td><td>"+r.admin+"</td></tr><tr><td><b>Remarks</b></td><td><b>:</b></td><td>"+r.remarks+"</td></tr></fieldset></div></table></td>";
						
						if(r.submitted_on != undefined)
						{
							tbl_row += "<td width=25%><div><fieldset><legend><b>Processed Details</b></legend><table class=datagrid1><tr><td><b>Submitted Bank Name</b></td><td><b>:</b></td><td>"+r.submit_bankname+"</td></tr><tr><td><b>submitted On</b></td><td><b>:</b></td><td>"+r.submittedon+"</td></tr><tr><td><b>Submitted by</b></td><td><b>:</b></td><td>"+r.submittedby+"</td></tr><tr><td><b>Remarks</b></td><td><b>:</b></td><td>"+r.submittedremarks+"</td></tr></table></fieldset></div></td>";
						}
						 if(r.status == '1')
						{
							tbl_row +="<td width=25%><div><fieldset><legend><b>Realized Details</b></legend><table class=datagrid1><tr><td><b>Realized On</b></td><td><b>:</b></td><td>"+r.activated_on+"</td></tr><tr><td><b>Realized By</b></td><td><b>:</b></td><td>"+r.activatedby+"</td></tr><tr><td><b>Remarks</b></td><td><b>:</b></td><td>"+r.reason+"</td></tr></table></fieldset></div></td>"
					 	}
						  if(r.status == '2')
						{
						tbl_row +="<td width=25%><div><fieldset><legend><b>Cancelled/Bounced Details</b></legend><table class=datagrid1><tr><td><b>Cancelled On</b></td><td><b>:</b></td><td>"+r.activated_on+"</td></tr><tr><td><b>Cancelled By</b></td><td><b>:</b></td><td>"+r.activatedby+"</td></tr><tr><td><b>Remarks</b></td><td><b>:</b></td><td>"+r.reason+"</td></tr></table></fieldset></div></td>";
						}
						+"</tr>";
						
					  $(tbl_row).appendTo("#searchcheq_details tbody");
				
			}
			},'json');
		},
		buttons:{
		'Close':function(){
		 $(this).dialog('close');
	},
	}
	
});



</script>

<style>
input[type="checkbox"]{cursor: pointer;}
.warn td{background: #FDD2D2 !important;}
.datagrid th{padding:12px 7px}
.datagrid1 {border-collapse: collapse;border:none !important}
.datagrid1 th{border:none !important;font-size: 13px;padding:0px 0px;}
.datagrid1 td{border-right:none;border-left:none;border-bottom:none;font-size: 12px;padding:2px;color: #444;text-transform: capitalize}
.datagrid1 td a{text-transform: capitalize}
.datagrid1 td b{font-weight: bold;font-size: 11px;}

td{vertical-align: top;}

#srch_results{
	margin-left: 1147px;
	position: absolute;
	display: none;
	width: 400px;
	overflow-y: auto;
	background: #EEE;
	border: 1px solid #AAA;
	max-height: 200px;
	min-width: 140px;
	max-width: 176px;
	overflow-x: hidden;
	margin-top: 46px;
}
#srch_results a{
	display: block;
	padding: 5px 6px;
	font-size: 12px;
	display: inline-table;
	width: 300px;
	text-transform: capitalize;
	border-bottom: 1px dotted #DDD;
	background: white;
} 
#srch_results a:hover{
background: #CCC;
color: black;
text-decoration: none;
}
</style>

<?php
