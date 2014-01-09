<?php 
$user=$this->session->userdata("admin_user");
?>
<style>
	.dash_bar{
	color:blue;
	font-weight:bold;
	text-align:center;
	background:#f1faf1;
	padding:10px;
	font-size:15px;
	margin:5px;
	font-family:trebuchet ms;
	float:left;
	width:205px;
	}
	.dash_bar .count{
	font-size:18px;
	font-weight:bold;
	}
	.dash_bar a{
	font-size:12px;
	float:right;
	padding:0px 5px;
	}
	.cplinks{
	padding:5px 30px;
	margin:10px;
	background:#eee;
	text-decoration:none;
	-moz-border-radius:5px;
	display:block;
	clear:both;
	float:left;
	}
	#cpform input.txtbox{
	width:60px;
	}
</style>
<script>
cps=new Array();
$(function(){
	$("#printcp").click(function(){
		printwin=window.open("","",'width=400,height=300');
		printwin.document.write(cps.join("<br>"));
		printwin.focus();
		printwin.print();
	});
	$("#cpform").submit(function(){
		from=$("#getfrom");
		to=$("#getto");
		f=parseInt(from.val());
		t=parseInt(to.val());
		if(isNaN(f)){
			alert("Please enter 'from' serial number");return false;} 
		if(isNaN(t)){
			alert("Please enter 'to' serial number");return false;}
		location.href="<?=site_url("admin/getcoupons/")?>/"+f+"/"+t; 
		return false;
	});
});
</script>
<div class="heading" style="margin-bottom:0px;">
<div class="headingtext container">
<?php if(isset($pagetitle)) echo $pagetitle; else echo "Coupons";?>
</div>
</div>
<div class="container" style="margin-top:10px;">
<div>
<a href="<?=site_url("admin/gencoupons")?>"><div class="dash_bar">Generate Coupons</div></a>
<a href="<?=site_url("admin/getcoupons")?>"><div class="dash_bar">Get Coupons</div></a>
</div>
<?php if(!isset($couponshistory)){?>
<div class="headingtext" style="padding-top:10px;color:#606060;cleaR:both;">Generate Coupons</div>
<div style="font-family:arial;font-size:14px;margin-top:10px;padding:20px 0px;color:#606060;">
<form action="<?=site_url("admin/procgencoupons")?>" method="post">
<div style="margin:5px 60px;background:#eee;padding:10px;">
Generate <input type="text" name="number" style="width:80px;"></input> coupons with <input type="text" name="per1" style="width:40px;" maxlength="3" value="1"></input> <b>.</b> <input type="text" value="00" name="per2" maxlength="2" style="width:40px;"></input> % discount for
<?php if($user['usertype']==1){?>
<select name="brand">
<?php foreach($brands as $brand){?>
<option value="<?=$brand->id?>"><?=$brand->name?></option>
<?php }?>
</select>
brand in
<?php }?>
<select name="category">
<?php foreach($categories as $cat){?>
<option value="<?=$cat->id?>"><?=$cat->name?></option>
<?php }?>
</select>
category
</div>
<div align="right" style="padding-top:20px;padding-right:40px;">
<input type="submit" value="Generate"></input>
</div> 
</form>
</div>
<?php }else{?>
<?php if(isset($coupons)){?>
<div class="headingtext" style="font-size:20px;padding-top:10px;color:#606060;clear:both;">Coupons from <?=$start?> to <?=$end?> <span style="margin-left:40px;font-size:12px;">showing only unused coupons</span></div>
<div style="font-family:arial;font-size:13px;float:left;background:#eee;padding:5px;margin:10px 30px;max-height:250px;width:600px;overflow:scroll;">
<table width="100%">
<tr>
<th>SNo</th>
<th>Coupon ID</th>
<th>Discount</th>
<th>Brand</th>
<th>Category</th>
</tr>
<?php foreach($coupons as $c){?>
<tr>
<td><?=$c['sno']?></td>
<td><?=$c['id']?></td>
<td><?php printf("%.2f",($c['value']/100))?>%</td>
<td><?=$c['brandname']?></td>
<td><?=$c['catname']?></td>
</tr>
<script>cps.push("<?=$c['id']?>")</script>
<?php }?>
</table>
<?php if(count($coupons)==0){?>
No unused coupons available <?php if($user['brandid']!=0) echo "for your brand";?>
<?php }?>
</div>
<div style="float:right;padding:20px;font-family:arial;">
<a class="cplinks" href="javascript:void(0)" id="printcp">Print Coupons</a>
<a class="cplinks" href="<?=site_url("admin/dndcoupons/1/$start/$end")?>">Download as CSV</a>
<a class="cplinks" href="<?=site_url("admin/dndcoupons/2/$start/$end")?>">Download as TXT</a>
</div>
<br style="clear:both"></br>
<?php }?>
<div class="headingtext" style="font-size:20px;padding-top:10px;color:#606060;clear:both;">Get Coupons</div>
<div style="float:right;background:#eee;padding:5px;font-family:arial;font-size:13px;">
<form id="cpform">
Get coupons from <input type="text" id="getfrom" class="txtbox"></input> to <input class="txtbox" type="text" id="getto"></input> <input type="submit" value="Go"></input>
</form> 
</div>
<div style="font-family:arial;margin:10px 40px;font-size:13px;">
based on creation history
<?php foreach($couponshistory as $h){?>
<div style="margin-left:10px;padding-top:2px;"><a href="<?=site_url("admin/getcoupons/{$h['start']}/{$h['end']}")?>"><?=$h['num']?> coupons on <i><?=date("g:ia d/m/y",$h['time'])?></i> by <i><?=$h['name']?></i> for <i><?=$h['brandname']?></i></a></div>
<?php }?>
</div>
<?php }?>
</div>
