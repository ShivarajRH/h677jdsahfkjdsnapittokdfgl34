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

<?php if($this->erpm->auth(STOCK_INTAKE_ROLE,true)){?>
<li><a href="#prod_grn_list">Purchase Products List</a></li>
<?php } ?>
</ul>

<?php if($this->erpm->auth(STOCK_INTAKE_ROLE,true)){?>
<div id="prod_grn_list" style="overflow: hidden;">
	<div class="opt_bar" style="padding:5px;float: right;" align="right">
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td>
					Brand : 
					<select name="sel_brand_id">
						<option value="999999999" <?php echo ($this->uri->segment(4)=='999999999'?'selected':'') ?>>Choose</option>
						<option value="0" <?php echo ($this->uri->segment(4)==0?'selected':'') ?> >All</option>
						<?php 
							foreach($this->db->query("select distinct b.id,b.name from m_vendor_brand_link a join king_brands b on a.brand_id = b.id where vendor_id = ? order by b.name ",$v['vendor_id'])->result_array() as $vb)
							{
								echo '<option value="'.($vb['id']).'" >'.($vb['name']).'</option>';
							}
						?>
					</select>
				</td>
				<td>
					<div>
						&nbsp;<b>From</b> :<input size="8" id="grn_frm" type="text" name="grn_frm" value="<?php echo $this->uri->segment(5)?$this->uri->segment(5):date('Y-m-01')?>"> &nbsp;
						<b>To</b> :<input size="8" id="grn_to" type="text" name="grn_to" value="<?php echo $this->uri->segment(6)?$this->uri->segment(6):date('Y-m-d')?>"> &nbsp;
					</div>
				<td>
				<td>
					<input type="button" onclick="fil_vb_products()" value="View Products">
				</td>	
			</tr>
		</table>
	</div>
	<div style="padding:5px;">
		<?php 
			$cond = '';
			if($this->uri->segment(4) == '')
			{
				$grn_frm = '2012-01-01';
				$grn_to = date('Y-m-d');
			}else
			{
				if($this->uri->segment(4))
					$cond = ' and c.brand_id = "'.($this->uri->segment(4)).'"';
				else
					$cond = ' and 1 ';
				
				$grn_frm = $this->uri->segment(5);
				$grn_to = $this->uri->segment(6);
			}
				

			if($cond != '')
			{
				$vb_plist_res  = $this->db->query("select a.grn_id,date(b.created_on) as grn_date,a.product_id,d.name as brand,d.id as brandid,c.product_name,a.mrp,a.dp_price,a.tax_percent,margin,scheme_discount_value,scheme_discunt_type,a.purchase_price,a.received_qty,a.purchase_price*a.received_qty as subtotal
														from t_grn_product_link a 
														join t_grn_info b on a.grn_id = b.grn_id 
														join m_product_info c on c.product_id = a.product_id 
														join king_brands d on d.id = c.brand_id 
														where b.vendor_id = ? and a.received_qty > 0 and date(b.created_on) between date(?) and date(?)  
														group by a.grn_id,a.product_id 
														order by grn_id desc",array($v['vendor_id'],$grn_frm,$grn_to));
			
			 
			if($vb_plist_res->num_rows())
			{
		?>
		<div>
			<b>Total Listed</b> : <?php echo $vb_plist_res->num_rows()?>
		</div>
		<table class="datagrid" width="100%">
			<thead>
				<tr>
					<th width="20" align="center">Slno</th>
					<th width="60" align="left">GRNID</th>
					<th width="80" align="left">GRN Date</th>
					<th width="100" style="text-align:left">Brand</th>
					<th>Product</th>
					<th width="60" style="text-align:center">Tax</th>
					<th width="80" style="text-align:right">MRP</th>
					<th width="80" style="text-align:right">DP</th>
					<th width="80" style="text-align:right">Purchase Price</th>
					<th width="50" style="text-align:right">Purchased Qty</th>
					<th width="80" style="text-align:right">Subtotal</th>
					<?php /*?>
					<th>Margin</th>
					<?php /*/?>
					
				</tr>
			</thead>
			<tbody>
				<?php 
					$ttl_pqty = $ttl_pprice = 0;
					foreach($vb_plist_res->result_array() as $i=>$vb_pdet)
					{
						$ttl_pqty += ($vb_pdet['received_qty']);
						$ttl_pprice += ($vb_pdet['purchase_price']*$vb_pdet['received_qty']);
				?>
						<tr>
							<td><?php echo $i+1;?></td>
							<td><a target="_blank" href="<?php echo site_url('admin/viewgrn/'.$vb_pdet['grn_id'])?>"><?php echo $vb_pdet['grn_id']?></a></td>
							<td><?php echo format_date($vb_pdet['grn_date'])?></td>
							<td><a target="_blank" href="<?php echo site_url('admin/viewbrand/'.$vb_pdet['brandid'])?>"><?php echo $vb_pdet['brand']?></a></td>
							<td><a target="_blank" href="<?php echo site_url('admin/product/'.$vb_pdet['product_id'])?>"><?php echo $vb_pdet['product_name']?></a></td>
							<td  align="center"><?php echo ($vb_pdet['tax_percent'])?></td>
							<td align="right"><?php echo format_price($vb_pdet['mrp'])?></td>
							<td align="right"><?php echo format_price($vb_pdet['dp_price'])?></td>
							<td align="right" ><?php echo format_price($vb_pdet['purchase_price'])?></td>
							<td align="right"><?php echo ($vb_pdet['received_qty'])?></td>
							<?php /*?>
							<td><?php echo ($vb_pdet['margin'])?></td>
							<?php /*/?>
							<td align="right"><?php echo format_price($vb_pdet['purchase_price']*$vb_pdet['received_qty'])?></td>
						</tr>
				<?php
					}
				?>
				<tr>
					<td  class="subtotals_row" colspan="9">Total</td>
					<td  class="subtotals_row" align="right" ><?php echo $ttl_pqty;?></td>
					<td  class="subtotals_row" align="right"><?php echo format_price($ttl_pprice,2);?></td>
				</tr>
			</tbody>
			
		</table>
		<?php }else
			{
				echo "<div align='center' style='clear:both'><b>No Data found</b></div>";	
			}
			}
		?>
	</div>
</div>

<script>
	var ven_id = <?php echo $v['vendor_id']?>;
	function fil_vb_products()
	{
		location.href = site_url+'/admin/vendor/'+ven_id+'/'+$('select[name="sel_brand_id"]').val()+'/'+$('#grn_frm').val()+'/'+$('#grn_to').val()+'#prod_grn_list';
	}
</script>

<?php } ?>


<style>
		td.subtotals_row{background: #ffffD0 !important;}
</style>

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
	<script>prepare_daterange('grn_frm','grn_to');</script>
<?php
