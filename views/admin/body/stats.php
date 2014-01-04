<script>
$(function(){
	$("#f_start,#f_end").datepicker({dateFormat:'dd-mm-yy'});
	$("#f_go").click(function(){
		if($("#f_start").val().length==0 || $("#f_start").val().length==0)
		{
			alert("Input!");
			return;
		}
		location="<?=site_url("admin/stats")?>/"+$("#f_start").val()+"/"+$("#f_end").val();
	});
});
</script>

<div class="container">

<h2>Stats <?php if($this->uri->segment(3) && $this->uri->segment(4)){?>from <?=$this->uri->segment(3)?> to <?=$this->uri->segment(4)?><?php }?></h2>

<div style="font-size:12px;padding:5px;margin-bottom:10px;display:inline-block;" class="infobar">
From <input type="text" id="f_start" value=""> to <input type="text" id="f_end"><input type="button" value="Go" id="f_go">
</div>


<table class="datagrid" cellpadding=5 class="datagrid">
<tr>
<th colspan=3>Buyprocesses</th>
</tr>
<tr>
<td>Total products added to cart</td>
<td><?=$addtocarts?></td>
</tr>

<tr>
<td>Total buys</td>
<td><?=$bought?></td>
</tr>

<tr>
<td>Added to cart but didn't make it</td>
<td><?=$addtocarts-$bought?></td>
</tr>

<tr>
<td>Most popular product</td>
<td><?=$mostpopular?></td>
</tr>

<tr>
<td>Most Bought</td>
<td><?=$mostbought?></td>
</tr>

<tr>
<td>Most unlucky product</td>
<td><?=$mostunlucky?></td>
</tr>

</table>


<div style="float:left;margin:20px;">


<table style="background:#fff;" cellpadding=5 class="datagrid">

<tr>
<th colspan=3>Transactions</th>
</tr>


<tr>
<td>Total Transactions</td>
<td><?=$trans?></td>
</tr>

<tr>
<td>Successful Transactions</td>
<td><?=$strans?></td>
</tr>

<tr>
<td>PONR Transactions</td>
<td><?=$trans-$strans?></td>
</tr>

<tr>
<td>PG Transactions</td>
<td><?=$pgs?></td>
</tr>

<tr>
<td>Successful PG Transactions</td>
<td><?=$spgs?></td>
</tr>

<tr>
<td>UNsuccessful PG Transactions</td>
<td><?=$pgs-$spgs?></td>
</tr>

<tr>
<td>COD Transactions</td>
<td><?=$cods?></td>
</tr>

</table>

</div>

<div style="float:left;margin:20px;">

<table style="background:#fff;" cellpadding=5 class="datagrid">

<tr>
<th colspan=3>Amount</th>
</tr>

<tr>
<td>Total Amount involved in all transactions</td>
<td>Rs <?=number_format($amount)?></td>
</tr>

<tr>
<td>Amount involved in all PG transactions</td>
<td>Rs <?=number_format($pgamount)?></td>
</tr>

<tr>
<td>Amount involved in successful PG transactions</td>
<td>Rs <?=number_format($spgamount)?></td>
</tr>

<tr>
<td>Amount involved in COD transactions</td>
<td>Rs <?=number_format($codamount)?></td>
</tr>

</table>
</div>

<table style="background:#fff;margin:20px;" cellpadding=5 class="datagrid">
<tr>
<th colspan=3>Orders</th>
</tr>
<tr>
<td>Total Orders</td>
<td><?=$orders?></td>
</tr>

<tr>
<td>Pending Orders</td>
<td><?=$porders?></td>
</tr>

<tr>
<td>Processed Orders</td>
<td><?=$prorders?></td>
</tr>

<tr>
<td>Shipped Orders</td>
<td><?=$sorders?></td>
</tr>

<tr>
<td>Rejected Orders</td>
<td><?=$rorders?></td>
</tr>

</table>

</div>
<style>
.datagrid tr td:last-child{
font-weight:bold;
}
</style>
<?php
