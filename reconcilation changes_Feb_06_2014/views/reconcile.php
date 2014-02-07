// form
<tr class="inst inst_date">
        <td class="label">Instrument Date</td><td> :</td>
        <td><input type="text" name="date" id="sec_date" size=15></td>
</tr>
<tr>
        <td>Select Invoices</td><td> :</td>
        <td>
            <a href="javascript:void(0);" class="button button-tiny_wrap cursor button-primary clone_rows_invoice">+</a>
            <a href="javascript:void(0);" class="button button-tiny_wrap cursor button-primary clone_rows_debitnote">+</a>
                <table border="0" cellspacing="0" cellpadding="2">
                    <tbody id="reconcile_row"></tbody>
                </table>
        </td>
</tr>

// tab
<?php if($this->erpm->auth(FINANCE_ROLE,true)){ ?>
    <li><a href="#security_cheques" >Security Cheque Details</a></li>
    <?php } ?>
    <li><a href="#unreconcile" onclick="load_receipts(this,'unreconcile',0,<?=$f['franchise_id']?>,100)">Un-Reconciled</a></li>

    // tab 2
    <div id="unreconcile">
            <div class="tab_content"></div>
    </div>


// dialog boxes
<div id="dlg_unreconcile_view_list" style="display:none;">
</div>
<div id="dlg_unreconcile_form" style="display:none;">
        <h3>Select invoices for reconciliation </h3>
        <form id="dl_submit_reconcile_form">
            <table class="datagrid1" width="100%">
                <tr><td width="150">Receipt #</td><th>
                        <input type="text" readonly='true' id="dg_i_receipt_id" name="dg_i_receipt_id" value="" size="6" class="inp"/></th></tr>
                <tr><td width="150">Receipt Amount</td><th>
                        Rs. <input type="text" readonly='true' id="dg_i_receipt_amount" name="dg_i_receipt_amount" value="" size="6" class="inp money"/></th></tr>
                <tr><td width="150">Unreconcile Amount</td><th>
                        Rs. <input type="text" readonly='true' id="dg_i_unreconciled_value" name="dg_i_unreconciled_value" value="" size="6" class="inp money"/></th></tr>
            </table>
            <div>&nbsp;</div>
            <div class="dg_error_status"></div>
                <table class="datagrid nofooter" width="100%">
                    <thead> <tr><th>Invoice No</th><th width="100">Invoice Amount (Rs.)</th><th width="100">Adjusted Amount (Rs.)</th><th>&nbsp;</th></tr></thead>
                    <tbody class='dlg_invs_list'>
                            <tr id='dg_reconcile_row_1' class="dg_invoice_row">
                                <td>
                                    <select size='2' name='sel_invoice[]' id='dlg_selected_invoices_1' class='dg_sel_invoices' onchange='dg_fn_inv_selected(this,1);'></select>
                                </td>
                                <td><input type='text' readonly='true' class='inp dg_amt_unreconcile money' name='amt_unreconcile[]' id='dg_amt_unreconcile_1' size=6></td>
                                <td><input type='text' class='inp dg_amt_adjusted money' name='amt_adjusted[]' id='dg_amt_adjusted_1' size=6 value=''></td>
                                <td>
                                    <a href='javascript:void(0)' class='button button-tiny_wrap button-primary' onclick='dg_add_invoice_row(this);'> + </a>
                                </td>
                            </tr>
                    </tbody>
                    <tfoot class="nofooter">
                        <tr>
                            <td colspan="2">
                                <span style="float:right;">Total reconciled (Rs.):</span><br>
                                <span style="float:right;">Un-reconciled after Reconcile (Rs.):</span>
                            </td>
                            <td align="left">
                                <input type="text" readonly='true' name="ttl_reconciled" class="dg_l_total_adjusted_val money" value="0" size="6" /><br>
                                <input type="text" readonly='true' name="ttl_unreconciled_after" class="dg_ttl_unreconciled_after money" value="0" size="6" />
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </form>
    </div>
<script>
// <![CDATA[
    var franchise_id = "<?=$franchise_id;?>";
    var frn_version_url = "<?=site_url("admin/pnh_fran_ver_change/{$fran['franchise_id']}")?>";
    var f_created_on = "<?php echo date('m/d/Y',$f['created_on'])?>";
    var franchise_name = "<?php echo $f['franchise_name']?>";
// ]]>
</script>
<script type="text/javascript" src='<?=base_url()."/js/pnh_franchise.js"; ?>'></script>
<link type="text/css" rel="stylesheet" href="<?=base_url().'/css/pnh_franchise.css';?>" />

<?php

?>
<!--Status & actions receipts page-->
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
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
                                                
           <!--Tab l receipts-->                                     
                                                
 <?php                                          
else if($type=="unreconcile")
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
<?php 
}
?>

         
         
         
         <!--Pending Receipts page-->
         
         <?php if($type==4){?>
        <td>
        <a href="javascript:void(0)" onclick='act_rec(<?=$r['receipt_id']?>)'>Realize</a> &nbsp; &nbsp;
        <a href="javascript:void(0)" onclick='can_rec(<?=$r['receipt_id']?>)'>Cancel</a> &nbsp; &nbsp;
        </td>
        <?php }elseif($type==1 || $type==2){ ?>
                <td><a href="javascript:void(0)" onclick='can_rec(<?=$r['receipt_id']?>)'>Cancel</a>
                <br>
                <?php if($r['unreconciled_value']>0) { ?>
                        <a href="javascript:void(0)" onclick="clk_reconcile_action(this,'<?=$r['receipt_id'];?>','<?=$r['franchise_id'];?>','<?=$r['receipt_amount'];?>','<?=$r['unreconciled_value'];?>')" class="button button-tiny button-action">Reconcile</a>
                <?php } ?>
                 &nbsp;
                 <?php if($r['unreconciled_value'] != $r['receipt_amount']) { ?>
                    <a href="javascript:void(0)" onclick="clk_view_reconciled(this,'<?=$r['receipt_id'];?>','<?=$r['franchise_id'];?>')" class="button button-tiny button-primary">View Reconciled</a>
                 <?php } ?>
            </td> &nbsp; &nbsp;
        <?php }?>
        </tr>
        <?php }?>