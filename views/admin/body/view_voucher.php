<?php $v=$voucher;?>
<div class="container">
<h2>Voucher : <?=$voucher['voucher_id']?></h2>

<table class="datagrid">
<thead>
<tr><th>Voucher ID</th><th>Voucher date</th><th>Category</th><th>Voucher Value</th><th>Payment Mode</th><th>Instrument No</th><th>Instrument Date</th><th>Bank</th><th>Narration</th><th>Created On</th><th>Created By</th></tr>
</thead>
<tbody>
<tr><td><?=$v['voucher_id']?></td><td><?=$v['voucher_date']?></td><td><?=count($expense)!=0?"Expense":"Payment"?></td>
<td>Rs <b><?=$v['voucher_value']?></b></td>
<td>
<?php switch($v['payment_mode']){
	case 0: echo "Cash";break;
	case 1: echo "Cheque";break;
	case 2: echo "DD";break;
	case 3: echo "Bank Transfer";break;
}?>
</td>
<td><?=$v['instrument_no']?></td>
<td><?=$v['instrument_date']?></td>
<td><?=$v['instrument_issued_bank']?></td>
<td><?=$v['narration']?></td>
<td><?=$v['created_on']?></td>
<td><?=$v['created_by']?></td>
</tr>
</tbody>
</table>

<?php if(!empty($expense)){?>
<h2>Expense Details</h2>
<table class="datagrid">
<thead><tr><th>Bill No</th><th>Expense Type</th></tr></thead>
<tbody>
<tr><td><?=$expense['bill_no']?></td><td>
<?php $type=array("Staff welfare","Printing & Stationery","Vehicle maintenance","Courier & postal","Traveling","OPEX","CAPEX","Others");?>
<?=$type[$expense['expense_type']]?>
</td>
</tbody>
</table>
<?php }?>


<?php if(!empty($doc_grn)){?>
<h3>Payments for Stocks</h3>
<table class="datagrid">
<thead><tr><th>GRN</th><th>Stock Invoice Value</th><th>Adjusted amount in voucher</th></tr></thead>
<tbody>
<?php foreach($doc_grn as $g){?>
<tr>
<td><a class="link" href="<?=site_url("admin/viewgrn/{$g['ref_doc_id']}")?>">GRN<?=$g['ref_doc_id']?></a></td>
<td>Rs <b><?=$this->db->query("select sum(purchase_inv_value) as s from t_grn_invoice_link where grn_id=?",$g['ref_doc_id'])->row()->s?></b></td>
<tD>Rs <b><?=$g['adjusted_amount']?></b></tD>
</tr>
<?php }?>
</tbody>
</table>
<?php }?>

<?php if(!empty($doc_po)){?>
<h3>Advance Payments for POs</h3>
<table class="datagrid">
<thead><tr><th>PO</th><th>PO Value</th><th>Adjusted amount in voucher</th></tr></thead>
<tbody>
<?php foreach($doc_po as $g){?>
<tr>
<td><a class="link" href="<?=site_url("admin/viewpo/{$g['ref_doc_id']}")?>">PO<?=$g['ref_doc_id']?></a></td>
<td>Rs <b><?=$this->db->query("select total_value as s from t_po_info where po_id=?",$g['ref_doc_id'])->row()->s?></b></td>
<tD>Rs <b><?=$g['adjusted_amount']?></b></tD>
</tr>
<?php }?>
</tbody>
</table>
<?php }?>

</div>
<?php
