<?php 
$invoice=false;
$status=array("Confirmed","Processed","Shipped","Cancelled");
$s=0;
foreach($orders as $o)
	if($o['status']==2 || $o['status']==3)
		$s++;
if($s==count($orders))
	$invoice=true;
?>
<div class="container transaction" style="padding-top:10px;">
<div style="float:right"><a href="<?=site_url("profile")?>">back</a></div>
<div style="clear:right;float:right">Ordered on <b><?=date("g:ia d/m/y",$trans['init'])?></b></div>
<h1>Transaction : <?=$trans['transid']?></h1>
<h4>Payment mode : <?=$trans['mode']==1?"CASH ON DELIVERY":"CREDIT CARD/DEBIT CARD/NETBANKING"?></h4>
<h4>Amount Paid : Rs <?=$trans['amount']?></h4>
<?php 
/*<a href="javascript:void(0)" style="float:right;font-weight:bold;" onclick='reorderall()'>Reorder all products in this transaction</a>
 * 
 * */

/*
<h4 style="font-size:130%;padding-top:5px;">Invoice : <?php if(!$invoice){?><span style="font-size:80%">not available until product shipped</span><?php }else{?><a href="<?=site_url("order/{$orders[0]['id']}")?>">view/print</a><?php }?></h4>
*/
?>
<div style="padding:10px">
	<h4 style="font-size:130%;padding-top:5px;">Invoice in Transaction</h4>
	
	
	<?php
		$this->db->where('transid',$trans['transid']);
		$this->db->where('tracking_id !=',' 0 ');
		$this->db->where('invoice_status',1);
		$this->db->groupby('transid','');
		$tinv_list_res = $this->db->get("king_invoice");
		 
		if($tinv_list_res->num_rows()){
				$j=0;
				echo '<table style="margin:0px;">
						<tr>
							<th>Slno</th>
							<th>Invoice no</th>
						</tr>';
			foreach($tinv_list_res->result_array() as $tinv){
				$j++;
				echo "<tr>
							<td>$j</td>
							<td><a href='".site_url('view_invoice/'.$tinv['transid'].'/'.$tinv['invoice_no'])."'>".$tinv['invoice_no']."</a> ".($tinv['invoice_status']==1?'':'Cancelled')."</td>
					   </tr>	
					 ";
			} 		
			
			echo '</table>';
		}else{
			echo "No Invoices available";
		}
	?>
	
</div>


<table width="100%" cellpadding=10 style="background:#f9f9f9;" cellspacing=0>
<tr style="background:#dedede;">
<th>Product Name</th>
<th>Quantity</th>
<th>Status</th>
<th>Shipping details</th>
</tr>
<?php 
$itemids=array();
 foreach($orders as $o){
	$sizing=@unserialize($o['sizing']);
	$live=$this->db->query("select i.live from king_dealitems i join king_deals d on d.dealid=i.dealid and ".time()." between d.startdate and d.enddate and d.publish=1 where i.id=?",$o['itemid'])->row();
	if(!empty($live) && $live->live==1)
	{
		$itemids[]=$o['itemid'];
		$qtys[]=$o['quantity'];
	}
?>
<tr>
<td>
	<?=$o['item']?>
	<?php if(0){// if(in_array($o['itemid'], $itemids)){?>
	<div><a href="javascript:void(0)" onclick='reorder(<?=$o['itemid']?>,"<?=isset($sizing['size'])?$sizing['size']:""?>","")'>reorder</a></div>
	<?php }?>
</td>
<th><?=$o['quantity']?></th>
<th><?=$status[$o['status']]?></th>
<th>
<?php if($o['status']==2){?>
<div>Courier : <b><?=$o['medium']?></b></div>
<div>AWB Tracking No : <b><?=$o['shipid']?></b></div>
<div>Shipped on : <b><?=date("d/m/y",$o['shiptime'])?></b></div>
<?php }else echo "na";?>
</th>
</tr>
<?php }?>
</table>

<?php 
	if(!empty($fss)){
?>
<h3 style="margin-top:10px;">Free Samples with these orders</h3>
<table WIDTH=400 cellpadding=10 style="background:#f9f9f9;" cellspacing=0>
<tr style="background:#dedede;">
<th>Sample</th>
<th>Status</th>
</tr>
<?php foreach($fss as $fs){	?>
			<tr>
				<td><?=$fs['name']?></td>
				<td><b><?=$fs['invoice_no']?"Processed":"Pending"?></b></td>
 			</tr>		
	<?php	} ?>
</table>
<?php 	} ?>

<h4 align="right" style="margin-bottom:10px;">Total Amount in transaction : <span class="red" style="font-size:120%">Rs <?=$trans['amount']?></span></h4>


</div>

<script>

function reorderall()
{
	itemid=0;
	rbuyst="<?php foreach($itemids as $c=>$i){?><?=($c==0)?"":","?><?=$itemids[$c]?>-<?=$qtys[$c]?><?php }?>";
	startbuyp();
}

var itemid=0,bpid,jxsize,rbuyst="";
function reorder(i,size)
{
	itemid=i;
	jxsize=size;
	startbuyp();
}
function startbuyp(){
		$.fancybox.showActivity();
		if(itemid==0)
		{
			addtocart();
			return;
		}
		pst="qty=1&uids=&item="+itemid+"&emails=&fbs=&fbemail=&rbuys=";
		$.post("<?=site_url("jx/startbuyprocess")?>",pst,function(resp){
			bpid=resp.bpid;
			addtocart();
		},"json");
}
function addtocart()
{
	pst={
			rbuys:rbuyst,
			item:itemid,
			qty:1,
			bpid:bpid,
			size:jxsize		
		};
	$.fancybox.showActivity();
	$.post(site_url+"jx/addtocart",pst,function(resp){
		$.fancybox.hideActivity();
		$("#cartlink").click();
	});
}
</script>

<?php
