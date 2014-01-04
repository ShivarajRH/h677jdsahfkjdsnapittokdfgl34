<div class="container">
<h2>Franchise Request : #<?=$quote['quote_id']?></h2>

<?php 
$has_new_prod = 0;
?>

<div style="padding-right:40px;float:right">
<h4 style="margin:0px;">Remarks</h4>
<table class="datagrid smallheader noprint">
<thead><tr><th>Remark</th><th>Time</th><th>By</th></tr></thead>
<tbody>
<?php foreach($this->db->query("select r.req_complete,r.remarks,r.time,a.name from pnh_quote_remarks r join king_admin a on a.id=r.created_by where quote_id=?",$quote['quote_id'])->result_array() as $r){?>
<tR class="<?php echo $r['req_complete']?'completed_req':'' ?>">
<td width="300"><?=($r['req_complete']?'<b>Completed : </b>':'').''.$r['remarks']?></td><td><?=date("d/m/Y h:i a",$r['time'])?></td><Td><?=$r['name']?></Td>
</tR>
<?php }?>
<tr>
<td colspan="100%" width="400">
<form id="post_quote_remark" action="<?=site_url("admin/pnh_quote_remarks")?>" method="post">
<input type="hidden" name="id" value="<?=$quote['quote_id']?>">
Add remarks : <br /> <textarea class="inp" name="remarks" style="width: 97%;"></textarea> <br /> 

<div align="right">
	<?php
		if($quote['quote_status'] == 0)
		{
			echo '<b>Request Complete</b> : <input type="checkbox" name="req_complete" value="1">';
		}
	?>	
	<input type="submit" value="Add">
</div>
</form>
</tr>
</tbody>
</table>
</div>


<table class="datagrid noprint">
<tr><td>Franchise :</td><td><a href="<?=site_url("admin/pnh_franchise/{$quote['franchise_id']}")?>"><?=$quote['franchise_name']?></a></td></tr>
<tr><td>Created On :</td><td><?=date("d/m/Y h:i a",$quote['created_on'])?></td></tr>
<tr><td>Created By :</td><td><?=$quote['admin']?></td></tr>
<tr>
	<td>Time To Respond :</td>
	<td>
		<?php
			$fr_phone_no = $this->db->query("select login_mobile1 from pnh_m_franchise_info where franchise_id = ? ",$quote['franchise_id'])->row()->login_mobile1;
			echo date('d/m/Y h:i a',$quote['created_on']+$quote['respond_in_min']*60);
			echo '<br>';
			if($quote['quote_status'] == 0)
			{
				if($quote['created_on']+$quote['respond_in_min']*60 < time())
				{
					echo '<b style="color:#cd0000"> Expired </b>';
				}else
				{
					echo " <b>(".(round(($quote['created_on']+$quote['respond_in_min']*60-time())/60))." Mins Left)</b> ";
				}
				echo '<a href="javascript:void(0)" onclick=makeacall("0'.$fr_phone_no.'") >Call</a>';
			}else
			{
				echo '<b>Responded</b>';		
			}
		?>
	</td>
</tr>
<tr><td>Updated On :</td><td><?=$quote['updated_on']==0?"na":date("g:ia d/m/y",$quote['created_on'])?></td></tr>
<tr><Td colspan="100%"><a href="<?=site_url("admin/pnh_offline_order#".$this->db->query("select pnh_franchise_id as f from pnh_m_franchise_info where franchise_id=?",$quote['franchise_id'])->row()->f)?>" target="_blank">Place Order</a></Td></tr>
</table>
<?php

$has_pnh_id_prods = false;
$has_new_prods = false;
foreach($deals as $d){
		if($d['pnh_id']!=0)
			$has_pnh_id_prods = true;
		else
			$has_new_prods = true;
}					


if($has_new_prods)
{
	
	?>
		<h3>Products without PNH ID</h3>
		<table class="datagrid">
			<thead>
				<th>Sl No</th>
				<th>Product Name</th>
				<th>Mrp</th>
				<th>Qty</th>
				<th>Quote</th>
			</thead>
			<tbody>
			<?php 
				$i=1;
				foreach($deals as $d){
					if($d['pnh_id']!=0)
						continue;
			?>
				<tr>
					<td><?php echo $i;?></td>
					<td><?php echo $d['new_product'];?></td>
					<td><?php echo $d['np_mrp'];?></td>
					<td><?php echo $d['np_qty'];?></td>
					<td><?php echo $d['np_quote']?></td>
				</tr>
				<?php $i++;
				}?>
			</tbody>
		</table>
		<?php
	} 
?>

<?php 
	if($has_pnh_id_prods)
	{
?>
<h3>Products With PNH ID</h3>
<form method="post" action="<?=site_url("admin/pnh_update_quote/{$quote['quote_id']}")?>">
<input type="hidden" value="<?php echo $quote['franchise_id'];?>" name="fid" />
<table class="datagrid">
<thead><tr><th>PNH ID</th><th>Item Name</th><th>MRP</th><th>Offer Price</th><th>DP Price</th><th>Final Price (Rs)</th><th>Qty</th><th>Status</th><th>Order Status</th><th>Transid</th><th>Price Authorized By</th><th>Updated On</th><th>Last Update by</th></tr></thead>
<tbody>
<?php $colors=array("10"=>"#11EE11","11"=>"#AAFFAA","00"=>"#FFAAAA","01"=>"#FFAAAA"); ?>
<?php foreach($deals as $d){ 
		$qqid=$d['id']; ?>
<?php if($d['pnh_id']!=0){?>
<tr style="background:<?=$colors[$d['status'].$d['order_status']]?>">
<td><?=$d['pnh_id']?><input type="hidden" name="id[]" value="<?=$d['id']?>"></td>
<td><a href="<?=site_url("admin/pnh_deal/{$d['pnh_id']}")?>" target="_blank"><?=$d['name']?></a></td>
<td><?=$d['mrp']?></td>
<td><?=$d['price']?></td>
<td><?=$d['dp_price']==0?"not given":$d['dp_price']?></td>
<td><input type="text" size=4 name="final[]" class="inp"  value="<?=$d['final_price']?>">
	<div style="width: 120px;">
		<input type="checkbox" class="upd_mrgn" name="up_sm<?=$d['id']?>" value="1"><label>update margin</label><br />
		<input type="checkbox" class="notify_sm" name="notify_sm<?=$d['id']?>" value="1"><label>send notification</label>
	</div>
	
</td>
<td><?=$d['qty']?></td>
<td><?=$d['status']?"Price Updated":"pending"?></td>
<td>
	<span><?=$d['order_status']?"Order placed":"Order not placed"?></span>
	<?php if($d['status']==1 && $d['order_status']==0){?>
	<br><a href="javascript:void(0)" onclick='mark_ordered(<?=$d['id']?>,this)' style="font-size:85%;">mark it as order placed</a>
	<?php }?>
</td>
<td><label class="transid"><a href="<?=site_url("admin/trans/{$d['transid']}")?>"><?=$d['transid']?></a></label></td>
<td><?=$d['price_updated_by']?$this->db->query("select name from king_admin where id=?",$d['price_updated_by'])->row()->name:"na"?></td>
<td><?=$d['updated_on']?date("g:ia d/m/y",$d['updated_on']):""?></td>
<td><?=$d['updated_by']?$this->db->query("select name from king_admin where id=?",$d['updated_by'])->row()->name:"na"?>
</tr>
<?php if($this->erpm->auth(true,true)){?>
<tr>
<td colspan="100%">

<div style="float:left;">
<h4 style="margin:0px;">Recent Special Margin history</h4>
<table class="datagrid smallheader noprint">
<thead><tr><Th>Special Margin</Th><th>From</th><th>To</th><th>Assigned on</th><th>Assigned by</th></tr></thead>
<tbodY>
<?php 
$itemid=$this->db->query("select id from king_dealitems where pnh_id=?",$d['pnh_id'])->row()->id;
foreach($this->db->query("select s.*,a.name as admin from pnh_special_margin_deals s join king_admin a on a.id=s.created_by where s.itemid=? order by id desc limit 10",$itemid)->result_array() as $s){?>
<tr><td><?=$s['special_margin']?>%</td><td><b><?=date("d/m/y",$s['from'])?></b></td><td><b><?=date("d/m/y",$s['to'])?></b></td><td><?=date("g:ia d/m/y",$s['created_on'])?></td><td><?=$s['admin']?></td></tr>
<?php }?>
</tbodY>
</table>
</div>

<div style="padding-left:20px;float:left;">
<h4 style="margin:0px;">Recent Orders</h4>
<table class="datagrid smallheader noprint">
<thead><tr><th>Transid</th><th>Franchise</th><th>Landing Cost</th><th>Amount</th><th>Date</th></tr></thead>
<tbody>
<?php foreach($this->db->query("select o.i_price-o.i_coup_discount as price,o.time,f.franchise_name,f.franchise_id,o.transid,t.amount from king_orders o join king_transactions t on t.transid=o.transid join pnh_m_franchise_info f on f.franchise_id=t.franchise_id where o.itemid=? order by o.time desc limit 5",$itemid)->result_array() as $d1){?>
<tR>
<td><a class="link" href="<?=site_url("admin/trans/{$d1['transid']}")?>"><?=$d1['transid']?></a></td>
<td><a href="<?=site_url("admin/pnh_franchise/{$d1['franchise_id']}")?>"><?=$d1['franchise_name']?></a></td>
<td>Rs <?=$d1['price']?></td>
<td>Rs <?=$d1['amount']?></td>
<td><?=date("g:ia d/m/y",$d1['time'])?></td>
</tR>
<?php }?>
</tbody>
</table>
</div>

<div class="clear"></div>
</td>
</tr>
<?php }?>
<?php }?>
</tbody>
<?php 
	
		

}
?>

</table>


<div style="padding:5px 0px;text-align: right;padding-right:5px;">
<input type="submit" value="Update final price">
</div>
</form>
<?php } ?>

</div>

<style>
	.completed_req td{background: rgb(189, 226, 140) !important}
	
</style>

<script>

$('#post_quote_remark').submit(function(){
	 
	 var remark = $.trim($('textarea[name="remarks"]',this).val());
		$('textarea[name="remarks"]',this).val(remark);
		if(!remark)
		{
			alert("Please enter remark, before you submit");
			return false;
		}
		 
	if($('input[name="req_complete"]',this).length)
	{
		if(!$('input[name="req_complete"]',this).attr('checked'))
		{
			if(!confirm("Do you want to proceed with out marking request as complete ?"))
			{
				return false;
			}
		}
	}
	
	
	
});

function mark_ordered(id,obj)
{
	transid=prompt("Enter Transid of the order placed");
	if(transid.length==0)
		return;
	$(obj).hide();
	$.post("<?=site_url("admin/pnh_quote/{$quote['quote_id']}")?>",{id:id,transid:transid});
	$("span",$(obj).parent()).text("Order placed");
	$(".transid",$(obj).parents("tr").get(0)).text(transid);
}
$('.notify_sm').change(function(){
	if($(this).attr('checked'))
		$(this).parent().find('.upd_mrgn').attr('checked',true);
	else
		$(this).parent().find('.upd_mrgn').attr('checked',false);
});
</script>

<?php
