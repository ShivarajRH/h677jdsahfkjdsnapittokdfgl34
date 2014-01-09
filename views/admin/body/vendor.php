<?php $v=$vendor;?>
<div class="container">
<div class="dash_bar_right">
<span><?=$this->db->query("select count(1) as l from t_po_info where vendor_id=?",$v['vendor_id'])->row()->l?></span>
POs raised
</div>
<div class="dash_bar_right">
<span>Rs <?=number_format($this->db->query("select sum(total_value) as l from t_po_info where vendor_id=?",$v['vendor_id'])->row()->l)?></span>
Total PO value
</div>
<h2>Vendor Details</h2>
<a href="<?=site_url("admin/editvendor/{$v['vendor_id']}")?>">edit this vendor</a>

<div class="tabs">

<ul>
<li><a href="#v_details">Basic Details</a></li>
<li><a href="#v_financials">Finance Details</a></li>
<li><a href="#v_extra">Extra Details</a></li>
<li><a href="#v_contacts">Contacts</a></li>
<li><a href="#v_brands">Brands</a></li>
<li><a href="#v_pos">POs Raised</a></li>
</ul>

<div id="v_details">
<table class="datagrid" width="400">
<tr><td>Code :</td><td><?=$v['vendor_code']?></td></tr>
<tr><td>Name :</td><td><?=$v['vendor_name']?></td></tr>
<tr><td>Address Line 1 :</td><td><?=$v['address_line1']?></td></tr>
<tr><td>Address Line 2 :</td><td><?=$v['address_line2']?></td></tr>
<tr><td>Locality :</td><td><?=$v['locality']?></td></tr>
<tr><td>Landmark :</td><td><?=$v['landmark']?></td></tr>
<tr><td>City :</td><td><?=$v['city_name']?></td></tr>
<tr><td>State :</td><td><?=$v['state_name']?></td></tr>
<tr><td>Country :</td><td><?=$v['country']?></td></tr>
<tr><td>Postcode :</td><td><?=$v['postcode']?></td></tr>
<tr><td>Ledger ID :</td><td><?=$v['ledger_id']?></td></tr>
</table>
</div>
<div id="v_financials">
<table class="datagrid" width="400">
<tr><td>Credit Limit :</td><td width="250"><?=$v['credit_limit_amount']?></td></tr>
<tr><td>Credit Days :</td><td><?=$v['credit_days']?></td></tr>
<tr><td>Payment Advance :</td><td><?=$v['require_payment_advance']?>%</td></tr>
<tr><td>CST :</td><td><?=$v['cst_no']?></td></tr>
<tr><td>PAN :</td><td><?=$v['pan_no']?></td></tr>
<tr><td>VAT :</td><td><?=$v['vat_no']?></td></tr>
<tr><td>Service Tax :</td><td><?=$v['service_tax_no']?></td></tr>
<tr><td>Average TAT :</td><td><?=$v['avg_tat']?></td></tr>
</table>
</div>
<div id="v_extra">
<table class="datagrid" width="400">
<tr><td>Return Policy :</td><td width="300"><?=$v['return_policy_msg']?></td></tr>
<tr><td>Payment Terms :</td><td><?=$v['payment_terms_msg']?></td></tr>
<tr><td>Remarks :</td><td><?=$v['remarks']?></td></tr>
</table>
</div>
<div id="v_contacts">
<div id="v_contact_cont">

<?php foreach($contacts as $c){?>
<table class="datagrid" width="400">
<tr><td><div>Name : <?=$c['contact_name']?></div></td>
<td><div>Designation : <?=$c['contact_designation']?></div></td>
</tr>
<tr>
<td><div>Mobile 1 : <?=$c['mobile_no_1']?></div></td>
<td><div>Mobile 2 : <?=$c['mobile_no_2']?></div></td>
</tr>
<tr>
<td><div>Telephone : <?=$c['telephone_no']?></div></td>
<td><div>FAX : <?=$c['fax_no']?></div></td>
</tr>
<tr>
<td><div>Email 1 : <?=$c['email_id_1']?></div></td>
<td><div>Email 2 : <?=$c['email_id_2']?></div></td>
</tr>
</table>
<?php }?>
</div>
</div>

<div id="v_brands">
<table class="datagrid">
<thead>
<tr><th>Brand</th><th>Margin</th><th>Total PO value</th></tr>
</thead>
<tbody>
<?php foreach($brands as $b){?>
<tr>
<td><a class="link" href="<?=site_url("admin/viewbrand/{$b['id']}")?>"><?=$b['name']?></a></td>
<td><?=$b['brand_margin']?>%</td>
<td>Rs <?=number_format($this->db->query("select sum(p.purchase_price*p.order_qty) as s from t_po_info po join t_po_product_link p on p.po_id=po.po_id join m_product_info d on d.product_id=p.product_id and d.brand_id=? where po.vendor_id=?",array($b['id'],$v['vendor_id']))->row()->s)?>
</tr>
<?php }?>
</tbody>
</table>
</div>


<div id="v_pos">

<table class="datagrid" style="margin-top:10px;">
<thead>
<tr>
<th>ID</th>
<th>Created On</th>
<th>Value</th>
<th>Purchase Status</th>
<th>Stock Status</th>
<th></th>
<th>Remarks</th>
</tr>
</thead>
<tbody>
<?php foreach($pos as $p){?>
<tr>
<td>PO<?=$p['po_id']?></td>
<td><?=date("g:ia d/m/y",strtotime($p['created_on']))?></td>
<td>Rs <?=number_format($p['total_value'])?></td>
<td><?php switch($p['po_status']){
	case 1:
	case 0: echo 'Open'; break;
	case 2: echo 'Complete'; break;
	case 3: echo 'Cancelled';
}?></td>
<td>
<?php switch($p['po_status']){
	case 0: echo 'Not received'; break;
	case 1: echo 'Partially received'; break;
	case 2: echo 'Fully received'; break;
	case 3: echo 'NA';
}?>
</td>
<td>
<a class="link" href="<?=site_url("admin/viewpo/{$p['po_id']}")?>">view</a>
<?php if($p['po_status']!=2 && $p['po_status']!=3){?>
&nbsp;&nbsp;&nbsp;<a href="<?=site_url("admin/apply_grn/{$p['po_id']}")?>">Stock Intake</a>
<?php }?>
</td>
<td><?=$p['remarks']?></td>
</tr>
<?php } if(empty($pos)){?><tr><td colspan="100%">no POs to show</td></tr><?php }?>
</tbody>
</table>

</div>


</div>

</div>


<style>
#v_contact_cont table{
margin:10px;
border:1px solid #ccc;
padding:5px;
}
</style>

<?php
