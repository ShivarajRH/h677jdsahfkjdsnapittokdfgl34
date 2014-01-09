<style>
.ui-datepicker {
	font-size: 10px;
}

#main {
	font-family: arial;
	font-size: 11px;
	color: #000000;
}

.hotel {
	margin-bottom: 30px;
	background: url(<?=base_url ()?> images/ title-bg-top-small.gif )
		repeat-x scroll left top;
	border: #CCCCCC 1px solid;
	-moz-border-radius: 10px;
	padding: 10px;
	float: left;
	width: 600px;
}

.maindiv {
	width: 900px;
	margin-top: 40px;
	margin-bottom: 10px;
}

.innerdiv {
	clear: both;
}

.innerdiv label {
	font-weight: bold;
	font-size: 13px;
}

.span {
	float: left;
	margin-bottom: 10px;
	text-align: justify;
}

.span1 {
	float: left;
	margin-bottom: 10px;
	text-align: justify;
}

input {
	-moz-border-radius: 5px;
	border: 1px solid #9BCD9B; #
	background: #EBF8DC;
	width: 300px;
}

textarea {
	-moz-border-radius: 5px;
	border: 1px solid #9BCD9B; #
	background: : #EBF8DC;
}

.form {
	background: url(<?=base_url ()?>     images/ title-bg-top-small.gif )
		repeat-x scroll left top;
	border: #CCCCCC 1px solid;
	-moz-border-radius: 10px;
	float: left;
	padding: 10px;
	clear: both;
}

.sidediv {
	float: left;
}

.dialoglabel {
	font-size: 12px;
	font-family: arial;
}

.ui-dialog .ui-dialog-titlebar {
	padding: -1.5em 0.3em 0.3em 1em;
	position: relative;
}

.ui-dialog-titlebar {
	font-size: 13px;
	color: #fff;
}

.ui-dialog .ui-dialog-buttonpane button {
	font-weight: bold;
	font-size: 12px;
}
</style>
<div class="heading" style="margin-bottom: 20px;">
<div class="headingtext container">View Brand Products</div>
</div>
<div class="container">
<table><tr><td valign="top">
<div class="form">
	<?php
	//print_r($specificbranddetails);
	if ($specificbranddetails != FALSE) {
		$logoid = $specificbranddetails->logoid;
		$namei = strpos ( $logoid, ',' );
		$logo1 = substr ( $logoid, 0, $namei );
		$logo2 = substr ( $logoid, $namei + 1 );
		?>
	<div style="float: right;"><a
	style="font-size: 13px; font-weight: bold;"
	href="<?=site_url ( 'admin/loadeditbrandpage/' . $specificbranddetails->id )?>">Edit</a></div>
<br>
<div style="clear: both;">
<div style="float: left;"><img style="width: 60px; height: 60px;"
	src="<?=base_url () . 'images/brandslogo/' . $logo1 . '.jpg'?>"></div>
<div style="float: left; margin-left: 10px;" id="newcat">
<div><label style="font-size: 13px; font-weight: bold;">Brand Name :</label><span
	style="font-weight: bold; font-size: 15px; color: #426C33;"><?=$specificbranddetails->name?></span></div>
<div style="margin-top: 10px;"><label
	style="font-size: 13px; font-weight: bold;">Description :</label>
<div style="font-weight: bold; font-size: 12px; font-family: arial;"><?=$specificbranddetails->description?></div>
</div>
<?php }?>
</div>
</div>
<div style="margin-bottom: 20px;"></div>
<div align="right" style="clear: both;"><a href="<?=site_url('admin/loadaddbrandadminpage')?>" id="addnewbrand"
	style="font-family: arial; font-size: 11px; color: #2D6A2E; cursor: pointer;">Add
New Brand</a></div>
</div>
</td>
<td>
<?php 
$usertype=$this->session->userdata("usertype");
//echo $usertype;
if(isset($deals)&& isset($dealitems) && $deals!=FALSE)
{
foreach($deals as $deal)
{
	$catid=$deal->catid;
	//print_r($catid);exit;
	//echo $catid;
	if($catid!=4)
	{
?>
<div class="hotel">
<div style="font-family: arial;font-size: 16px;font-weight: bold;margin-bottom:10px;">
<div style="float:left;"><img style="float:left; width: 100px;height: 100px;" src="<?=base_url().'images/items/'.$deal->pic.'.jpg'?>"></div>
<label>Category :</label><label style="color: #426C33;"><?=$deal->name?></label>
<div style="float: right;" class="admhotellinks">
<span><a class="confirmdelete" style="font-size: 11px;" href="<?=site_url('admin/removedeal/'.$deal->dealid.'/'.$deal->catid)?>">Delete</a></span>
<?php ?>
<span><a style="font-size: 11px;" href="<?=site_url('admin/edit/'.$deal->dealid).'/'.$deal->catid.'/'.$deal->brandid?>">Edit</a></span>
<span><a style="font-size: 11px;" href="<?=site_url('admin/getpicsandvideos/'.$deal->dealid.'/'.$deal->catid)?>">Delete Photos & Videos</a></span>
<span><a style="font-size: 11px;" href="<?=site_url('admin/addpicsandvideos/'.$deal->dealid.'/'.$deal->catid)?>">Add Photos & Videos</a></span>
<?php if($this->session->userdata("usertype")==1) { if($deal->publish==0){?>
<span><a style="font-size: 11px;" href="<?=site_url('admin/publishdeal/'.$deal->dealid.'/'.$deal->catid.'/'.$deal->publish)?>">Publish</a></span>
<?php }else {?>
<span><a style="font-size: 11px;" href="<?=site_url('admin/publishdeal/'.$deal->dealid.'/'.$deal->catid.'/'.$deal->publish)?>">Unpublish</a></span>
<?php }}?> 
</div>
</div>
	<div>
	<div style="color:#872;float:left;font-size:20px;margin-left:75px;margin-top:15px;"><?php if(isset($deal->brandname)) echo $deal->brandname;?></div>
	<div style="float: right;margin-top:10px;">
		<div><label style=" font-family:arial;font-weight: bold;">Deal Start Date :</label><label style="font-size: 13px;font-family: arial;padding: 0px;"><?=date("d/m/Y",$deal->startdate)?></label></div>
		<div><label style="font-family:arial;font-weight: bold;">Deal End Date :</label><label style="font-size: 13px;font-family: arial;padding: 0px;"><?=date("d/m/Y",$deal->enddate)?></label></div>
		<input type="hidden" name="dealid" value="<?=$deal->dealid?>">
	</div>
	</div>	
	<div style="margin-top:20px;text-align:left;margin-left:10px; font-family: arial;font-size:13px;font-weight: bold;color: #426C33;float:left; width: 100%;">Item Details</div>
<div style="float: left;">
<table style="width:100%;">	

<?php 
/*print_r($deal->dealid);
echo '<br>';
print_r($dealitems);exit;*/
	if($dealitems!=FALSE)
	{
		$rc=0;
	foreach($dealitems[$deal->dealid] as $dealitem)
	{
		if($rc==0 || $rc%2==0){echo '<tr><td>';}else{ echo '<td>';}
	?>
	<div style="margin-top: 10px;margin-bottom: 0px;float: left;margin-left:10px;border:0px solid;padding: 8px;">
	<div style="float:left;"><img style="width: 50px;height: 50px;" src="<?=base_url().'images/items/'.$dealitem->pic.'.jpg'?>"></div>
    <div style="float: right;" class="admhotellinks">
		<span><a style="font-size: 11px;" href="<?=site_url('admin/editroom/'.$dealitem->id).'/'.$deal->catid?>">Edit</a></span>
		<span><a class="confirmroom" style="font-size: 11px;" href="<?=site_url('admin/removeroom/'.$dealitem->id.'/'.$deal->catid)?>">Delete</a></span>
	</div>	
	<div style="float:left;margin-left: 20px;margin-top: 10px;padding: 4px;">
		<div align="left"><span style="font-weight: bold;font-family: arial;font-size: 11px; ">Item Name :</span><span style="font-size: 11px;font-family: arial;padding: 0px;"><?=$dealitem->name?></span></div>
		<div style="float: left;margin-left: 0px;"><span style="font-weight: bold;font-family: arial;font-size: 11px;">Price :</span><span style="font-size: 11px;font-family: arial;padding: 0px;">Rs. <?=$dealitem->price?></span></div>
    </div>
	</div>
	</td>
	<?php $rc++;}?>
	</tr></table></div>
	<?php }
	else
	{
		echo '<div align="center" style="font-weight: bold;font-size: 13px;color: #FFFFFF;"><span>Sorry!!!.......No items found for this deal</span></div>';
	}
	?>	
</div>
<?php } } }?>
</td>
</tr>
</table>
</div>