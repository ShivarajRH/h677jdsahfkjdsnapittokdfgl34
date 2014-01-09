<div class="container">
<h2>Scan &amp; pack invoice </h2>

<div style="padding:0px;">
Scan Barcode : <input class="inp" id="scan_barcode" style="padding:5px;"> <input type="button" value="Go" onclick='validate_barcode()'>
</div>

<table class="datagrid" style="margin-top:20px;" width=600>
<thead><tr><th>Product name</th><th>Qty</th><th>Scanned</th><th>Status</th></tr></thead>
<tbody>
<?php 
foreach($invoice as $i){?>
<tr class="bars bar<?=$i['barcode']?>">
<td class="prod"><?=$i['product_name']?></td>
<td><?=$i['qty']?><span class="qty" style="display:none;">1</span></td>
<td class="have">0</td>
<td class="status">PENDING</td>
</tr>
<?php }?>
</tbody>
</table>

<div style="margin-top:20px;">
<input type="button" value="Check" style="padding:7px 10px;" onclick='checknprompt()'>
</div>

<script>

function checkall()
{
	var f=true;
	$(".bars").each(function(){
		p=$(this);
		prod=$(".prod",p).html();
		needed=parseInt($(".qty",p).html());
		have=parseInt($(".have",p).html());
		if(needed!=have)
		{
			f=false;
			return false;
		}
	});

	msg='<form id="packform" method="post"><?php $r=rand(303,34243234);?><input type="hidden" name="pass" value="<?=md5("$r {$invoice[0]['invoice_no']} svs snp33tdy")?>">	<input type="hidden" name="key" value="<?=$r?>">	<input type="hidden" name="invoice" value="<?=$invoice[0]['invoice_no']?>">	</form>';
	$(".container").append(msg);
	if(f==true)
		$("#packform").submit();
	return f;
}

function checknprompt()
{
	if(checkall()==false)
		alert("'"+prod+"' is insufficient");
}

function validate_barcode()
{
	p=$(".bar"+$("#scan_barcode").val());
	$("#scan_barcode").val("");
	if(p.length==0)
	{
		alert("The product is not in invoice");
		return;
	}
	checkprod(p);
}
function checkprod(p)
{
	needed=parseInt($(".qty",p).html());
	have=parseInt($(".have",p).html());
	if(needed<=have)
	{
		alert("Required qty is already scanned");
		return;
	}
	have=have+1;
	$(".have",p).html(have);
	if(have==needed)
		$(".status",p).html("OK");
	checkall();
}

$(function(){
	$("#scan_barcode").keyup(function(e){
		if(e.which==13)
			validate_barcode();
	});
});
</script>

</div>
<?php
