<?php $user=$this->session->userdata("admin_user"); ?>
<script>
$(function(){
	$("#insearchform").submit(function(){
		var srch_val = $.trim($("#insearchbox").val());
			$("#insearchbox").val(srch_val);
	});
	var srch_val = $.trim($("#insearchbox").val());
	$("#insearchbox").val(srch_val);
});
</script>
<style>
#insearchbox{
color:#000;
width:250px;
}
.sheading{
color:#029332;
font-family:'trebuchet ms';
font-size:24px;
}
.scont{
font-family:arial;
font-size:14px;
padding:5px;
background:#eee;
margin:5px;
margin-left:30px;
overflow: hidden;
}
.scont a{
margin-left:20px;
}
</style>
<div class="heading" style="margin-bottom:20px;">
<div class="headingtext container">
Search Results
<span style="margin-left:20px;">
	<form action="<?php echo site_url('admin/search')?>" method="post">
		<input type="text" id="insearchbox" name="q" value="<?=$this->input->post("q")?>">
		<input type="submit" value="Search">
	</form>
	
</span>
</div>
</div>
<div class="container">
<?php if(empty($deals) && empty($brands) && empty($items) && empty($categories) && empty($orders) && empty($users) && empty($invoices)){?>
<div style="padding:50px;text-align:center;font-family:arial;font-size:18px;">
Your search has no results<br>Please narrow your search keyword. Avoid using two or more words
</div>
<?php }?>
<?php if(isset($deals) && !empty($deals)){?>
<div class="sheading">Deals</div>
<?php foreach($deals as $deal){?>
<div class="scont">
	<span style="float: left;width: 50%"><?=$deal['tagline']?></span> 
	&nbsp;
	

<span style="float: right;font-size: 12px;">
	<a target="_blank" href="<?=site_url("admin/ordersfordeal/".$deal['itemid'])?>">view orders</a> 
	<a target="_blank" href="<?=site_url("admin/deal/".$deal['dealid'])?>">view</a> 
	<a target="_blank" href="<?=site_url("admin/edit/".$deal['dealid'])?>">edit</a>
	<?php 
		if($deal['publish']){
			$l_stat = 1;
			$l_stat_text = 'Make It Live';
			if($deal['live']){
				$l_stat = 0;
				$l_stat_text = 'Make It Not Live';
			}
	?>
		<a target="_blank" href="<?=site_url("admin/livedeal/{$deal['dealid']}/{$deal['id']}/$l_stat")?>"><?php echo $l_stat_text?></a>
	<?php 				
		}else{
	?>
		<a target="_blank" href="<?=site_url("admin/deal/".$deal['dealid'])?>">publish</a>
	<?php 		
		}
	?> 
	
</span>

<span style="font-size: 12px;float: right">
	(
		<b>Catergoy</b>:<?=$deal['catname']?> - 
	 	<b>Brand</b>:<?=$deal['brandname']?> - 
	 	<b>MRP</b>:<?=$deal['orgprice']?>
	 )
	</span>
	
</div>
<?php }?>
<?php }?>

<?php if(isset($brands) && !empty($brands)){?>
<div class="sheading">Brands</div>
<?php foreach($brands as $brand){?>
<div class="scont"><?=$brand['name']?>
<a target="_blank" href="<?=site_url("admin/brand/	".$brand['id'])?>">view</a>
<a target="_blank" href="<?=site_url("admin/dealsforbrand/".$brand['id'])?>">view deals</a>
</div>
<?php }?>
<?php }?>

<?php if(isset($items) && !empty($items)){?>
<div class="sheading">Items</div>
<?php foreach($items as $item){?>
<div class="scont"><?=$item['name']?>
<a target="_blank" href="<?=site_url("admin/deal/	".$item['dealid'])?>">view</a>
</div>
<?php }?>
<?php }?>

<?php if(isset($categories) && !empty($categories)){?>
<div class="sheading">Categories</div>
<?php foreach($categories as $cat){?>
<div class="scont"><?=$cat['name']?>
<a target="_blank" href="<?=site_url("admin/categories/	".$cat['id'])?>">view</a>
</div>
<?php }?>
<?php }?>

<?php if(isset($users) && !empty($users)){?>
<div class="sheading">Users</div>
<?php foreach($users as $user){?>
<div class="scont"><?=$user['name']?>
<a target="_blank" href="<?=site_url("admin/user/".$user['userid'])?>">view</a>
<a target="_blank" href="<?=site_url("admin/ordersbyuser/".$user['userid'])?>">view orders</a>
</div>
<?php }?>
<?php }?>


<?php if(isset($orders) && !empty($orders)){?>
<div class="sheading">Orders by user</div>
<?php foreach($orders as $order){?>
<div class="scont">
<span style="Float:right;color:#888;">User : <?=$order['username']?></span>
<?=$order['name']?>
<a target="_blank" href="<?=site_url("admin/vieworder/	".$order['id'])?>">view order</a>
</div>
<?php }?>
<?php }?>

<?php if(isset($invoices) && !empty($invoices)){?>
<div class="sheading">List of Matching Invoices</div>
<?php foreach($invoices as $invoice){?>
<div class="scont">
	<span style="float: right">
		 
		<a target="_blank" href="<?=site_url("admin/trans/".$invoice['transid'])?>">view Transaction</a>
		
	</span>
	<span style="Float:left;color:#555;">
		Invoice : <a target="_blank" style="margin-left:0px;" href="<?=site_url("admin/invoice/	".$invoice['invoice_no'])?>"><?=$invoice['invoice_no']?></a> 
	 - <?php echo (($invoice['invoice_status']==1)?'Active':'Cancelled'); ?>  -  Transaction : <?=$invoice['transid']?></span>

</div>
<?php }?>
<?php }?>
</div>

<script type="text/javascript">
	$('.scont').hover(function(){
		$(this).css('background','#f7f7f7');
	},function(){
		$(this).css('background','#EEEEEE');
	});
</script>
<br />
<br />
