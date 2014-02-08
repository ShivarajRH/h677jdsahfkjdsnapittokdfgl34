<?php if($type=='pending')
{?>
	<div align="right" class="receipt_pg fl_right">
		<?php echo $pagination;?>
	</div>
	<div class="receipt_totals">Total Receipts:<?php echo $total_records;?>&nbsp;&nbsp;Total value:Rs <?php echo formatInIndianStyle($pending_ttlvalue['total'])?></div>
	<div class="clear"></div>
	<table class="datagrid smallheader" width="100%">
		<thead>
			<tr>
				<th>Receipt Details</th>
				<th>Amount Details</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($pending_receipts as $pr){?>
			<tr>
				<td>
					<div id="receipt_det">
					<table class="datagrid1" >
						<tr>
							<td><b>Receipt Id</b></td><td><b>:</b></td>
							<td><?php echo $pr['receipt_id'];?></td>
						</tr>
						<tr>
							<td><b>created on</b></td>
							<td><b>:</b></td>
							<td><?php echo date("g:ia d/m/y",$pr['created_on'])?></td>
						</tr>
						<tr>
							<td><b>created by</b></td>
							<td><b>:</b></td>
							<td><b><?=$pr['admin']?></b></td>
						</tr>
					</table>
				</div>
			</td>
				
				<td>
					<div id="cash_det">
					<table class="datagrid1" width="100%">
					<tr><td><b><?php $modes=array("cash","Cheque","DD","Transfer");?><b>Payment Mode</b></td><td><b>:</b></td><td><?=$modes[$pr['payment_mode']]?> <b>Rs <?=$pr['receipt_amount']?> </b> </td></tr>
					<tr><td><b>Type</b></td><td><b>:</b></td><td> <?=$pr['receipt_type']==0?"Deposit":"Topup"?></td></tr>
					<?php if($pr['payment_mode']>=0){?>
					<?php if($pr['bank_name']){?><tr><td><b>Bank</b></td><td><b>:</b></td><td><?=$pr['bank_name']?></td></tr><?php }?>
					<?php if($pr['payment_mode']>0){?><tr><td><b>Cheque no/DD no</b></td><td><b>:</b></td><td><?=$pr['instrument_no']?></td></tr><?php }?>
					<tr><td><?php $transit_types=array("In Hand","Via Courier","With Executive");?><b>Transit Type</b></td><td><b>:</b></td><td><?=$transit_types[$pr['in_transit']]?></td>
					<?php }?>
					<?php if($pr['in_transit']!=0){?>
					<td><a class="transit_link" href="javascript:void(0)" onclick="change_status(<?=$pr['receipt_id']?>)">Change to In Hand</a></td>
					<?php }?>
					</tr>
					<?php if($pr['modified_on']){?>
					<tr><td><b>Transit Status Modified By</b></td><td><b>:</b></td><td><?php echo $pr['modifiedby']?></td></tr>
					<tr><td><b>Transit Status Modified On</b></td><td><b>:</b></td><td><?php echo date("g:ia d/m/Y",$pr['modified_on'])?></td></tr>
					<?php }?>
					<tr><td><b>Payment Date</b></td><td><b>:</b></td><td><?=$pr['instrument_date']!=0?date("d/m/y",$pr['instrument_date']):""?></td></tr>
					<tr><td><b>Remarks</b></td><td><b>:</b></td><td><?=$pr['remarks']?></td></tr>
                    <tr>
                        <td>
                            <b>Actions</b>
                        </td>
                        <td>:</td>
                        <td>
                            <?php if($r['unreconciled_value']>0) { ?>
                               <a href="javascript:void(0)" onclick="clk_reconcile_action(this,'<?=$r['receipt_id'];?>','<?=$r['franchise_id'];?>','<?=$r['receipt_amount'];?>','<?=$r['unreconciled_value'];?>')" class="button button-tiny button-action">Reconcile</a>
                        
                            <?php } ?>
                             &nbsp;
                             <?php if($r['unreconciled_value'] != $r['receipt_amount']) { ?>
                                <a href="javascript:void(0)" onclick="clk_view_reconciled(this,'<?=$r['receipt_id'];?>','<?=$r['franchise_id'];?>')" class="button button-tiny button-primary">View Reconciled</a>
                             <?php } ?>
                        </td>
                    </tr>
					</table>
					</div>
				</td>
				<td><b><?php if($pr['status']==1) echo 'Activated'; else if($pr['status']==0) echo 'Pending'; else if($r['status']==3) echo 'Reversed'; else echo 'Cancelled';?></b>
					<?php if($pr['status']==1 && $pr['receipt_type']==1){?> <br> <br> 
					<a class="danger_link"
					href="<?=site_url("admin/pnh_reverse_receipt/{$pr['receipt_id']}")?>">reverse</a>
					<?php }?>
				</td>
			</tr>
			<?php }?>
			</tbody>
	</table>
	
<?php 
} else if($type=='processed')
{
?>
	<div align="right" class="receipt_pg fl_right">
		<?php echo $pagination;?>
	</div>
	<div class="receipt_totals">Total Receipts:<?php echo $total_records;?>&nbsp;&nbsp;Total value:Rs <?php echo formatInIndianStyle($processed_ttlvalue['total'])?></div>
	<div class="clear"></div>
	<table class="datagrid smallheader"  width="100%">
		<thead>
			<tr>
				<th>Receipt Details</th>
				<Th>Payment Details</Th>
				<th>Status</th>
				<th>Processed Details</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($processed_receipts as $pro_r){?>
			<tr>
				<td>
				<div id="receipt_det">
				<table class="datagrid1">
					<tr><td><b>Receipt Id</b></td><td><b>:</b></td><td><?php echo $pro_r['receipt_id'];?></td></tr> 
					<tr><td><b>created on</b></td><td><b>:</b></td><td><?php echo date("g:ia d/m/y",$pro_r['created_on'])?></td></tr> 
					<tr><td><b>created by</b></td><td><b>:</b></td><td> <b><?=$pro_r['admin']?></b></td></tr> 
					</table>
				</div>
				</td>
				
				<td>
				<div id="cash_det" width="50%">
					<table class="datagrid1" width="100%" cellspacing="3">
						<tr>
							<td><b><?php $modes=array("cash","Cheque","DD","Transfer");?>AmountType</b></td><td><b>:</b></td>
							<td><?=$modes[$pro_r['payment_mode']]?>&nbsp;&nbsp;<b>Rs <?=$pro_r['receipt_amount']?></b></td>
						</tr>
						<tr>
							<td><b>Type</b></td><td><b>:</b></td>
							<td><?=$pro_r['receipt_type']==0?"Deposit":"Topup"?></td>
						</tr>
						<tr>
							<?php if($pro_r['bank_name']){?>
							<td><b>Bank</b></td><td><b>:</b></td>
							<td><?=$pro_r['bank_name']?></td>
							<?php }?>
						</tr>
						<?php if($pro_r['payment_mode']!=0){?>
						<tr>
							<td><b>Cheque no</b></td><td><b>:</b></td>
							<td><?=$pro_r['instrument_no']?></td>
						</tr>
						<?php }?>
						<tr>
							<td><b>Payment Date</b></td><td><b>:</b></td>
							<td><?=$pro_r['instrument_date']!=0?date("d/m/y",$pro_r['instrument_date']):""?></td>
						</tr>
						<tr>
							<td><b>Remarks</b></td><td><b>:</b></td>
							<td><?=$pro_r['remarks']?></td>
						</tr>
					</table>
				</div>
			</td>
				<td><b><?php if($pro_r['status']==1) echo 'Activated'; else if($pro_r['status']==0) echo 'Pending'; else if($pro_r['status']==3) echo 'Reversed'; else echo 'Cancelled';?>
					<?php if($pro_r['status']==1 && $pro_r['receipt_type']==1){?> <br> <br> 
					<a class="danger_link" href="<?=site_url("admin/pnh_reverse_receipt/{$pro_r['receipt_id']}")?>">reverse</a>
					
					<?php }?>
				</b></td>
				<td>
				<div id="process_det">
					<table class="datagrid1" width="100%" cellspacing="3">
						<tr>
							<td><b>Processed On</b></td><td><b>:</b></td>
							<td><?php echo format_date($pro_r['submitted_on'])?></td>
						</tr>
						<tr>
							<td><b>Processed By</b></td><td><b>:</b></td>
							<td><?php echo $pro_r['submittedby']?></td>
						</tr>
						<tr>
							<td><b>Processed Bank</b></td><td><b>:</b></td>
							<td><?php echo $pro_r['submit_bankname']?></td>
						</tr>
						<tr>
							<td><b>Remarks</b></td><td><b>:</b></td><td><?php echo $pro_r['submittedremarks']?></td>
						</tr>
                                                <tr><td><b>Un-Reconciled Status</b></td><td><b>:</b></td>
                                                    <td><?php 
                                                        echo $r['unreconciled_status']; 
                                                        echo " ( ".$r['unreconciled_value']." )";?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <b>Actions</b>
                                                    </td>
                                                    <td>:</td>
                                                    <td>
                                                        <?php 
                                                            echo ''.$r['unreconciled_value'];
                                                        if($r['unreconciled_value']>0) { ?>
                                                        <a href="javascript:void(0)" onclick="clk_reconcile_action(this,'<?=$r['receipt_id'];?>','<?=$r['franchise_id'];?>','<?=$r['receipt_amount'];?>','<?=$r['unreconciled_value'];?>')" class="button button-tiny button-action">Reconcile</a>
                                                        <?php } ?>
                                                         &nbsp;
                                                         <?php if($r['unreconciled_value'] != $r['receipt_amount']) { ?>
                                                            <a href="javascript:void(0)" onclick="clk_view_reconciled(this,'<?=$r['receipt_id'];?>','<?=$r['franchise_id'];?>')" class="button button-tiny button-primary">View Reconciled</a>
                                                         <?php } ?>
                                                    </td>
                                                </tr>
					</table>
				</div>
			</td>
			</tr>
			<?php }?>
		</tbody>
	</table>
<?php 
}else if($type=='realized')
{
?>
	<div align="right" class="receipt_pg fl_right">
		<?php echo $pagination;?>
	</div>
	<div class="receipt_totals"><b>Total Receipts: </b><?php echo $total_records;?>&nbsp;&nbsp;<b>Total value:</b> Rs <?php echo formatInIndianStyle($realized_ttlvalue['total'])?></div>
	 <div class="clear"></div>
	 <table class="datagrid smallheader"  width="100%" >
		<thead>
			<tr>
				<th>Receipt Details</th>
				<Th>Amount Details</Th>
				<th>Processed Details</th>
				<th>Realized Details</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($realized_receipts as $r){?>
			<tr>
				<td>
				<div id="receipt_det">
					<table class="datagrid1" >
						<tr><td><b>Receipt Id</b></td><td><b>:</b></td><td><?php echo $r['receipt_id']?></td></tr>
						<tr><td><b>created on</b></td><td><b>:</b></td><td><?php echo date("g:ia d/m/y",$r['created_on'])?></td></tr>
						<tr><td><b>Created by</b></td><td><b>:</b></td><td><b><?=$r['admin']?></b></td></tr>
					</table>
				</div>
				</td>
				<td >
				<div id="cash_det">
				<table class="datagrid1">
					<tr><td><b><?php $modes=array("cash","Cheque","DD","Transfer");?>Payment Mode</b></td><td><b>:</b></td><td><?=$modes[$r['payment_mode']]?>&nbsp;&nbsp;<b>Rs <?=$r['receipt_amount']?></b></td></tr>
					<tr><td><b>Type</b></td><td><b>:</b></td><td><?=$r['receipt_type']==0?"Deposit":"Topup"?></td></tr>
					<?php if($r['payment_mode']!=0){?>
					<tr><td><b>Cheque no</b></td><td><b>:</b></td><td><?=$r['instrument_no']?></td></tr>
					<?php }?>
					<?php if($r['bank_name']){?><tr><td><b>Bank</b></td><td><b>:</b></td><td><?=$r['bank_name']?></td></tr><?php }?>
					<tr><td><b>Payment Date</b></td><td><b>:</b></td><td><?=$r['instrument_date']!=0?date("d/m/Y",$r['instrument_date']):""?></td></tr>
					<tr><td><b>Remarks</b></td><td><b>:</b></td><td><?=$r['remarks']?></td></tr>
				</table>
				</div>
				</td>
			<td>
				<div id="processed_det">
					<table class="datagrid1">
					<tr><td><b>Bank</b></td><td>:</td><td><?=$r['submit_bankname']?></td></tr>
					<tr><td><b>Remarks</b></td><td><b>:</b></td><td><?=$r['submittedremarks']?></td></tr>
					<tr><td><b>Deposited On</b></td><td>:</td><td><?=format_date($r['submitted_on'])?></td></tr>
					<tr><td><b>Deposited By</b></td><td>:</td><td><?=$r['submitted_by']?></td></tr>
					</table>
				
				</div>
			</td>
				<td>
					<div id="realize_det">
						<table class="datagrid1">
						<tr><td><b>Realized On</b></td><td><b>:</b></td><td><?=$r['cheq_realized_on']!=null?format_date($r['cheq_realized_on']):format_date_ts($r['activated_on']);?></td></tr>
						<tr><td><b>Remarks</b></td><td>:</td><td><?=$r['reason']?></td></tr>
						<tr><td><b>Updated By</b></td><td>:</td><td><?=$r['activated_by']?></td></tr>
						<tr><td><b>Updated On</b></td><td>:</td><td><?=format_date_ts($r['activated_on'])?></td></tr>
						<tr><td><b>Un-Reconciled Status</b></td><td><b>:</b></td><td><?php 
                                echo $r['unreconciled_status']; 
                                echo " ( ".$r['unreconciled_value']." )";
                            ?></td>
		                </tr>
		                <tr>
		                        <td>
		                            <b>Actions</b>
		                        </td>
		                        <td>:</td>
		                        <td>
		                            <?php if($r['unreconciled_value']>0) { ?>
		                                <a href="javascript:void(0)" onclick="clk_reconcile_action(this,'<?=$r['receipt_id'];?>','<?=$r['franchise_id'];?>','<?=$r['receipt_amount'];?>','<?=$r['unreconciled_value'];?>')" class="button button-tiny button-action">Reconcile</a>
		                            <?php } ?>
		                             &nbsp;
		                             <?php if($r['unreconciled_value'] != $r['receipt_amount']) { ?>
		                                <a href="javascript:void(0)" onclick="clk_view_reconciled(this,'<?=$r['receipt_id'];?>','<?=$r['franchise_id'];?>')" class="button button-tiny button-primary">View Reconciled</a>
		                             <?php } ?>
		                        </td>
		                    </tr>
						</table>
					</div>
				</td>
				<td><b><?php if($r['status']==1) echo 'Activated'; else if($r['status']==0) echo 'Pending'; else if($r['status']==3) echo 'Reversed'; else echo 'Cancelled';?></b>
					<?php if($r['status']==1 && $r['receipt_type']==1){?> <br> <br> 
					<a class="danger_link" href="<?=site_url("admin/pnh_reverse_receipt/{$r['receipt_id']}")?>">reverse</a>
					<?php }?>
				</td>
			</tr>
			<?php }?>
		</tbody>
	</table>
	
<?php 
}else if($type=='cancelled')
{
?>	
	<div align="right" class="receipt_pg fl_right">
		<?php echo $pagination;?>
	</div>
	<div class="receipt_totals">Total Receipts:<?php echo $total_records;?>&nbsp;&nbsp;Total value:Rs <?php echo formatInIndianStyle($cancelled_ttlvalue['total'])?></div>
	 <div class="clear"></div>
	 <table class="datagrid smallheader" width="100%" >
		<thead>
			<tr>
				<th>Receipt Details</th>
				<Th>Payment Details</Th>
				<th>Processed Details</th>
				<th>Cancelled Details</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($cancelled_receipts as $cancel){?>
		<tr>
			<td>
				<div id="receipt_det">
					<table class="datagrid1" >
							<tr>
								<td><b>Receipt Id</b></td>
								<td><b>:</b></td>
								<td><?php echo $cancel['receipt_id']?>
								</td>
							</tr>
							<tr>
								<td><b>created on</b></td>
								<td><b>:</b></td>
								<td><?php echo date("g:ia d/m/y",$cancel['created_on'])?>
								</td>
							</tr>

							<tr>
								<td><b>created by</b></td>
								<td><b>:</b></td>
								<td><b><?=$cancel['admin']?> </b>
								</td>
							</tr>
					</table>
				</div>
			</td>
			<td>
				<div id="cash_det">
					<table class="datagrid1" >
						<tr>
							<td><?php $modes=array("cash","Cheque","DD","Transfer");?><b>Payment 
									Mode</b></td>
							<td><b>:</b></td>
							<td><?=$modes[$cancel['payment_mode']]?>&nbsp;&nbsp;<b>Rs <?=$cancel['receipt_amount']?>
							</b></td>
						</tr>
						<tr>
							<td><b>Type</b></td>
							<td><b>:</b></td>
							<td><?=$cancel['receipt_type']==0?"Deposit":"Topup"?></td>
						</tr>
						<tr>
							<td><b>Bank</b></td>
							<td><b>:</b></td>
							<td><?=$cancel['bank_name']?></td>
						</tr>
						<tr>
							<td><b>Cheque Date</b></td>
							<td><b>:</b></td>
							<td><?=$cancel['instrument_date']!=0?date("d/m/Y",$cancel['instrument_date']):""?>
							</td>
						</tr>
						<tr>
							<td><b>Cheque no</b></td>
							<td><b>:</b></td>
							<td><?=$cancel['instrument_no']?></td>
						</tr>
						<tr>
							<td><b>Remarks</b></td>
							<td><b>:</b></td>
							<td><?=$cancel['remarks']?></td>
						</tr>
					</table>
		</div>
		</td>
		<td>
			<div>
				<table class="datagrid1" cellpadding="0" cellspacing="0">
				<tr><td><b>Bank</b></td><td>:</td><td><?=$cancel['submit_bankname']?></td></tr>
				<tr><td><b>Remarks</b></td><td><b>:</b></td><td><?=$cancel['submittedremarks']?></td></tr>
				<tr><td><b>Deposited On</b></td><td>:</td><td><?=format_date($cancel['submitted_on'])?></td></tr>
				<tr><td><b>Deposited By</b></td><td>:</td><td><?=$cancel['submitted_by']?></td></tr>
				</table>
			</div>
		</td>
		<td>
			<div>
				<table class="datagrid1" cellpadding="0" cellspacing="0">
				<tr><td width="80"><b>Cancel Status</b></td><td><b>:</b></td><td><?php $cstatus=array("Reversed","Return","Bounce");?><?=$cstatus[$cancel['cancel_status']];?></td></tr>
				<tr><td><b>Cheque Cancelled On</b></td><td><b>:</b></td><td><?=$cancel['cheq_cancelled_on']!=null?format_date($cancel['cheq_cancelled_on']):format_datetime($cancel['modified_on'])?></td></tr>
				<tr><td><b>Remarks</b></td><td><b>:</b></td><td><?=$cancel['cancel_reason']?$cancel['cancel_reason']:$cancel['reason']?></td></tr>
				<tr><td><b>Updated By</b></td><td><b>:</b></td><td><?= $cancel['reversed_by']?$cancel['reversed_by']:$cancel['activated_by']?> </td></tr>
				<tr><td><b>Updated On</b></td><td><b>:</b></td><td><?=$cancel['cancelled_on']!=null?format_datetime_ts($cancel['cancelled_on']):format_date_ts($cancel['modified_on'])?></td></tr>
				</table>
			</div>
		</td>
			<td><b><?php if($cancel['status']==1) echo 'Activated'; else if($cancel['status']==0) echo 'Pending'; else if($cancel['status']==3) echo 'Reversed'; else echo 'Cancelled';?></b>
					<?php if($cancel['status']==1 && $cancel['receipt_type']==1){?> <br> <br> 
					<a class="danger_link"
					href="<?=site_url("admin/pnh_reverse_receipt/{$cancel['receipt_id']}")?>">reverse</a>
					<?php }?>
				</td>
			</tr>
			<?php }?>
		</tbody>
	</table> 
	
<?php 
}else if($type=='acct_stat')
{
?>
	<b>Account Statement Correction Log</b>
	<?php 
		if($account_stat)
		{
	?>	
	<table class="datagrid">
            <thead><th>Description</th><th>Credit (Rs)</th><th>Debit (Rs)</th><th>Corrected On</th><th>Action</th></thead>
	<tbody>
	<?php foreach($account_stat as $ac_st){
	?>
	<tr>
		<td><?php echo $ac_st['remarks']?></td>
		<td><?php echo $ac_st['credit_amt']?></td>
		<td><?php echo $ac_st['debit_amt']?></td>
		<td><?php echo format_datetime($ac_st['created_on'])?></td>
                <td>
                    <?php
                    if($ac_st['type'] == '0' && $ac_st['credit_amt'] > 0 ) { //Only if debit entry
                        echo '<a href="javascript:void(0);" onclick="reconcile_cr_amount(this,\''.$ac_st["acc_correc_id"].'\',\''.$ac_st["credit_amt"].'\',\''.$ac_st["unreconciled_amount"].'\')" class="button button-tiny_wrap cursor button-primary">Reconcile</a>';
                    }
                    else {
                        echo '--';
                    }?>
                </td>
	</tr>
	<?php }?>
	</tbody>
	</table>
	<?php 
		}else
		{
			echo 'No Data found';
		}
	?>		
<?php 
}else if($type=="actions")
{
?>	
	<table class="datagrid" width="100%">
		<thead>
			<tr>
				<th>Credit Added</th>
				<th>New credit limit</th>
				<th>Reason</th>
				<th>Added by</th>
				<th>Added On</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($credit_log as $c){?>
			<tr>
				<td><?=$c['credit_added']?></td>
				<td><?=$c['new_credit_limit']?></td>
				<td><?=$c['reason']?></td>
				<td><?=$c['admin']?></td>
				<td><?=date("g:ia d/m/y",$c['created_on'])?></td>
			</tr>
			<?php }?>
		</tbody>
	</table>
<?php 
}
else if($type=="unreconcile")
{
?>	
	<div align="right" class="receipt_pg fl_right">
		<?php echo $pagination;?>
	</div>
        <div class="clear"></div>
         <div id="tabs-unreconcile" style="float:left;">
             <ul>
                    <li><a class="receipts" href="#un-receipts" >Receipts</a></li>
                    <li><a class="cr_notes" href="#un-credits">Credit Notes</a></li>
            </ul>
             
            <div id="un-receipts"> 
             
                    <div class="receipt_totals"><b>Total Receipts: </b><?php echo $total_records;?>&nbsp;&nbsp;<b>Total value:</b> Rs <?php echo formatInIndianStyle($realized_ttlvalue['total'])?></div>
	 
                    <table class="datagrid smallheader"  width="100%" >
                           <thead>
                                   <tr>
                                           <th>Receipt Details</th>
                                           <Th>Amount Details</Th>
                                           <th>Status</th>
                                           <th>Realized Details</th>
                                   </tr>
                           </thead>
                           <tbody>
                                   <?php foreach($receipt_log as $r){?>
                                   <tr>
                                           <td>
                                           <div id="receipt_det">
                                                   <table class="datagrid1" >
                                                           <tr><td><b>Receipt Id</b></td><td><b>:</b></td><td><?php echo $r['receipt_id']?></td></tr>
                                                           <tr><td><b>created on</b></td><td><b>:</b></td><td><?php echo date("g:ia d/m/y",$r['created_on'])?></td></tr>
                                                           <tr><td><b>Created by</b></td><td><b>:</b></td><td><b><?=$r['admin']?></b></td></tr>
                                                   </table>
                                           </div>
                                           </td>
                                           <td >
                                           <div id="cash_det">
                                           <table class="datagrid1">
                                                   <tr><td><b><?php $modes=array("cash","Cheque","DD","Transfer");?>Payment Mode</b></td><td><b>:</b></td><td><?=$modes[$r['payment_mode']]?>&nbsp;&nbsp;<b>Rs <?=$r['receipt_amount']?></b></td></tr>
                                                   <tr><td><b>Type</b></td><td><b>:</b></td><td><?=$r['receipt_type']==0?"Deposit":"Topup"?></td></tr>
                                                   <?php if($r['payment_mode']!=0){?>
                                                   <tr><td><b>Cheque no</b></td><td><b>:</b></td><td><?=$r['instrument_no']?></td></tr>
                                                   <?php }?>
                                                   <?php if($r['bank_name']){?><tr><td><b>Bank</b></td><td><b>:</b></td><td><?=$r['bank_name']?></td></tr><?php }?>
                                                   <tr><td><b>Payment Date</b></td><td><b>:</b></td><td><?=$r['instrument_date']!=0?date("d/m/Y",$r['instrument_date']):""?></td></tr>
                                                   <tr><td><b>Remarks</b></td><td><b>:</b></td><td><?=$r['remarks']?></td></tr>
                                           </table>
                                           </div>
                                           </td>
                                           <td><b><?php if($r['status']==1) echo 'Activated'; else if($r['status']==0) echo 'Pending'; else if($r['status']==3) echo 'Reversed'; else echo 'Cancelled';?></b>
                                                   <?php if($r['status']==1 && $r['receipt_type']==1){?> <br> <br> 
                                                   <a class="danger_link"
                                                   href="<?=site_url("admin/pnh_reverse_receipt/{$r['receipt_id']}")?>">reverse</a>
                                                   <?php }?>
                                           </td>
                                           <td>
                                           <div id="realize_det">
                                           <table class="datagrid1">
                                               <tr><td><b>Realized On</b></td><td><b>:</b></td><td><?=format_date_ts($r['activated_on'])?></td></tr>
                                               <tr><td><b>Realized By</b></td><td><b>:</b></td><td><?=$r['activated_by']?></td></tr>
                                               <tr><td><b>Remarks</b></td><td><b>:</b></td><td><?=$r['reason']?></td></tr>
                                               <tr><td><b>Un-Reconciled Status</b></td><td><b>:</b></td><td><?php 
                                                               echo $r['unreconciled_status']; 
                                                               echo " ( ".$r['unreconciled_value']." )";
                                                           ?></td>
                                               </tr>
                                               <tr>
                                                   <td>
                                                       <b>Actions</b>
                                                   </td>
                                                   <td>:</td>
                                                   <td>
                                                       <?php if($r['unreconciled_value']>0) { ?>
                                                           <!--<a href="javascript:void(0)" id="clk_reconcile_action" receipt_id="<?=$r['receipt_id'];?>" franchise_id='<?=$r['franchise_id'];?>' receipt_amount='<?=$r['receipt_amount'];?>' unreconciled_value='<?=$r['unreconciled_value'];?>' class="button button-tiny button-action">Reconcile</a>-->
                                                               <a href="javascript:void(0)" onclick="clk_reconcile_action(this,'<?=$r['receipt_id'];?>','<?=$r['franchise_id'];?>','<?=$r['receipt_amount'];?>','<?=$r['unreconciled_value'];?>')" class="button button-tiny button-action">Reconcile</a>
                                                       <?php } ?>
                                                        &nbsp;
                                                        <?php if($r['unreconciled_value'] != $r['receipt_amount']) { ?>
                                                           <a href="javascript:void(0)" onclick="clk_view_reconciled(this,'<?=$r['receipt_id'];?>','<?=$r['franchise_id'];?>')" class="button button-tiny button-primary">View Reconciled</a>
                                                        <?php } ?>
                                                   </td>
                                               </tr>
                                           </table>
                                           </div>
                                           </td>
                                   </tr>
                                   <?php }?>
                           </tbody>
                   </table>
            </div>
         
            <div id="un-credits">
                
                <div class="receipt_totals"><b>Total Credit notes: </b><?php echo $total_credit_records;?>&nbsp;&nbsp;<b>Total value:</b> Rs <?php echo formatInIndianStyle($realized_ttlvalue['total'])?></div>
                <div class="clear"></div>
                <b>Credit Notes Log</b>
               <?php 
                       if($credits_log)
                       {
               ?>	
               <table class="datagrid">
                   <thead><th>Credit Note Id</th><th>Description</th><th>Credit (Rs)</th><th>Un-Reconciled Amount (Rs)</th><th>Corrected On</th><th>Action</th></thead>
               <tbody>
               <?php foreach($credits_log as $ac_st){
               ?>
               <tr>
                       <td><?php echo $ac_st['credit_note_id']?></td>
                       <td><?php echo $ac_st['desc']?></td>
                       <td><?php echo $ac_st['amount']?></td>
                       <td><?php echo $ac_st['unreconciled_amount']?></td>
                       <td><?php echo format_datetime($ac_st['created_on'])?></td>
                       <td>
                           <?php
                           if($ac_st['type'] == '0' && $ac_st['amount'] > 0 ) { //Only if debit entry
                               echo '<a href="javascript:void(0);" onclick="reconcile_cr_amount(this,\''.$ac_st["credit_note_id"].'\',\''.$ac_st["amount"].'\',\''.$ac_st["unreconciled_amount"].'\')" class="button button-tiny_wrap cursor button-primary">Reconcile</a>';
                           }
                           else {
                               echo '--';
                           }?>
                       </td>
               </tr>
               <?php }?>
               </tbody>
               </table>
               <?php 
                       }else
                       {
                               echo 'No Data found';
                       }
               ?>
         </div>
         
         <script>
            $( "#tabs-unreconcile" ).tabs();
        </script>
    </div>
<?php 
}
?>

<div align="right" class="receipt_pg">
	<?php echo $pagination;?>
</div>
