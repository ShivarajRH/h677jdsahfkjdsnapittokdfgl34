<?php
	$user = $this->erpm->auth();
?>
<html>
<head>
<title>Product procurement list</title>
<style>
	body{font-family:arial;font-size:14px;}
	.onscreen{width:850px;margin:10px auto;}
	@media print
	  {
	  	.onscreen {width:100%}
	  }
</style>
</head>
<body class="onscreen">
<div>
	
	<div class="block">	
		<div style="float:right;">
			<b>Date :</b> <?=Date('d-m-Y'); ?><br />
			<b>By :</b> <?php echo $user['username'] ?>
		</div> 
		<div style="float: left">
			<h2 style="margin-bottom: 5px;">Product procurement list for BATCH<?=$this->uri->segment(3)?></h2>	
		</div>
		
	</div>
	
	<div id="wrapper" style="clear: both">
		<table border=1 style="font-family:arial;font-size:13px;width: 100%"  cellpadding=3>
		<tr style="background:#aaa">
			<th>Product ID</th><th>Product Name</th><th>Qty</th><Th>MRP</Th><th>Location</th>
		</tr>
		<?php $i=0; foreach($prods as $p){?>
			<tr <?php if($i%2==0){?>style="background:#eee;"<?php }?>>
				<td  width="80"><a target="_blank" href="<?php echo site_url('admin/product/'.$p['product_id'])?>"><?=$p['product_id']?></a></td>
				<td><?=$p['product']?></td>
				<td width="20" align="center"><?=$p['qty']?></td>
				<?php list($loc,$mrp) = explode('::',$p['location']);?>
				<td width="30" ><?=$mrp?></td>
				<td width="150"><?=$loc?>&nbsp;</td>
			</tr>
		<?php $i++;
		}?>
		</table>
	</div>
	

	<div class="block">
		<br>
		<span style="margin:22px 0px 0px;float:right;"><b>Validated By</b> : _______________<br /></span><br />
		<span style="margin:7px 0px;float:left;;"><b>Picked By</b> : _____________________<br /></span>
	</div> 

</body>
</html>
<?php
