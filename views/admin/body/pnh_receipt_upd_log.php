<?php
$rcpt_stat = array('pending','Activated','Cancelled','Reversed');
?>
<div class="container">
<div style="float: right">
	<b>Filter by Date : </b><input type="text" size="10" name="sd" id="sd" value="<?php echo ($sd);?>" > - <input type="text" size="10" name="ed" id="ed" value="<?php echo ($ed);?>" >
	<input type="button" onclick="fil_bydate()" value="submit">
	
	 
</div>		
<h2>PNH Receipt Update log <?php echo $receipts?' - '.$filter_date_str:'';?></h2>
<?php if($receipts){ ?>
		
<table class="datagrid">
<thead><tr><th>Receipt ID</th><th>Franchise</th><th>Type</th><Th>Amount</Th><th>Instrument Type</th><Th>Instrument Date</Th><th>Instrument No</th><th>Remarks</th><th>Added on</th><th>Created By</th><th>Status</th></tr></thead>
<tbody>
<?php foreach($receipts as $r){?>
<tr>
<td><?=$r['receipt_id']?></td>
<td><a href="<?=site_url("admin/pnh_franchise/{$r['franchise_id']}")?>"><?=$r['franchise_name']?></a></td>
<td><?=$r['receipt_type']==0?"Security Deposit":"Topup"?></td>
<td>Rs <?=$r['receipt_amount']?></td>
<td><?php $modes=array("cash","Cheque","DD","Transfer");?><?=$modes[$r['payment_mode']]?></td>
<td><?=date("d/m/y",$r['instrument_date'])?></td>
<td><?=$r['instrument_no']?></td>
<td><?=$r['remarks']?></td>
<td><?=date("d/m/y g:ia",$r['created_on'])?></td>
<td><?=$r['admin']?></td>
<td><?=$rcpt_stat[$r['status']]?></td></tr>
<?php }?>
</tbody>
</table> 
<?php }else{echo "<h4>No receipts activated / cancelled for - $filter_date_str </h4>";}?>
</div>
<script>
	$( "#sd" ).datepicker({
	  changeMonth: true,
      onClose: function( selectedDate ) {
        $( "#ed" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#ed" ).datepicker({
      changeMonth: true,
      onClose: function( selectedDate ) {
        $( "#sd" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
    
    function fil_bydate()
    {
    	location.href = site_url+'/admin/pnh_receipt_upd_log/'+$('#sd').val()+'/'+$('#ed').val();
    }
    
</script>
<?php
