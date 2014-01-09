<script>
$(function(){
	$(".confirmdelete").click(confirmdelete);
	$(".confirmroom").click(confirmdeleteroom);
});

function confirmdelete()
{
	if(confirm("Are you sure want to delete this hotel deal?")==true)
		return true;
	else
		return false;
}
function confirmdeleteroom()
{
	if(confirm("Are you sure want to delete this Room?")==true)
		return true;
	else
		return false;
}
</script>
<style>
#page {
#background:#FFFFFF none repeat scroll 0 0;
#border:1px solid #F0E9D6;
margin:0 auto;
padding:20px;
width:900px;
font-size: 12px;
}
.hotel{
margin-bottom: 30px;
background: #EBDECC;
border:1px solid #D2B48C;
-moz-border-radius:10px;
padding: 10px;
float: left;
}
</style>
<div class="holidaybar" style="margin-bottom:20px;">
<div style="font-family:'trebuchet ms';font-size:29px;color:#ffffff;">View Deals</div>
</div>
<div id="page">
<?php 
//print_r($deals);exit;
//print_r($dealitems);
//print_r($hoteldeals);exit;
//print_r($roomdetails);exit;
?>
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
<style>
.admhotellinks span{
padding:0px 10px;
font-size:11px;
}
</style>
<div class="hotel">
<div style="font-family: arial;font-size: 16px;font-weight: bold;margin-bottom:10px;">
<label style="color: #426C33;"><?=$deal->name?></label>
<div style="color:#872;float:left;font-size:20px;"><?php if(isset($deal->brandname)) echo $deal->brandname;?></div>
<div style="float: right;" class="admhotellinks">
<span><a class="confirmdelete" style="font-size: 11px;" href="<?=site_url('admin/removedeal/'.$deal->dealid.'/'.$deal->catid)?>">Delete</a></span>
<?php ?>
<span><a style="font-size: 11px;" href="<?=site_url('admin/edit/'.$deal->dealid).'/'.$deal->catid?>">Edit</a></span>
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
		<div><label style=" font-family:arial;font-weight: bold;">Deal Start Date :</label><label style="font-size: 13px;font-family: arial;padding: 10px;"><?=date("d/m/Y",$deal->startdate)?></label></div>
		<div><label style="font-family:arial;font-weight: bold;">Deal End Date :</label><label style="font-size: 13px;font-family: arial;padding: 10px;"><?=date("d/m/Y",$deal->enddate)?></label></div>
		<input type="hidden" name="dealid" value="<?=$deal->dealid?>">
	</div>	
	<div style="margin-top:20px;text-align:left;margin-left:40px; font-family: arial;font-size:13px;font-weight: bold;color: #426C33;float:left; width: 100%;">Item Details</div>
	<?php 
	if($dealitems!=FALSE)
	{
	foreach($dealitems[$deal->dealid] as $dealitem)
	{
	?>
	<div style="margin-top: 10px;margin-bottom: 5px;float: left;margin-left:40px; clear: both;">
	<div align="left"><span style="font-weight: bold;font-family: arial;font-size: 13px; ">Item Name :</span><span style="font-size: 13px;font-family: arial;padding: 10px;"><?=$dealitem->name?></span>
	<div style="float: right;margin-right: 450px;" class="admhotellinks">
	<?php // if($usertype!=1){?>
<span><a style="font-size: 13px;margin-left: 100px;" href="<?=site_url('admin/editroom/'.$dealitem->id).'/'.$deal->catid?>">Edit Item</a></span>
<span><a class="confirmroom" style="font-size: 11px;" href="<?=site_url('admin/removeroom/'.$dealitem->id.'/'.$deal->catid)?>">Delete Item</a></span>
<?php // }?>
</div></div>
	<div style="float: left;margin-left: 0px;"><span style="font-weight: bold;font-family: arial;font-size: 13px;">Price :</span><span style="font-size: 13px;font-family: arial;padding: 10px;">Rs. <?=$dealitem->price?></span></div>
	</div>
	<?php }}
	else
	{
		echo '<div align="center" style="font-weight: bold;font-size: 13px;color: #FFFFFF;"><span>Sorry!!!.......No rooms found for this deal</span></div>';
	}
	?>	
</div>
<?php 
}
else 
{
	foreach($hoteldeals[$deal->dealid] as $hotel)
{
	?>
<div class="hotel">
<div style="font-family: arial;font-size: 16px;font-weight: bold;margin-bottom:10px;">
<label style="color: #426C33;"><?=$hotel->heading?></label>
<div style="float: right;" class="admhotellinks">
<span><a class="confirmdelete" style="font-size: 11px;" href="<?=site_url('admin/removedeal/'.$hotel->dealid.'/'.$hotel->catid)?>">Delete</a></span>
<?php ?>
<span><a style="font-size: 11px;" href="<?=site_url('admin/edit/'.$hotel->dealid.'/'.$hotel->catid)?>">Edit</a></span>
<span><a style="font-size: 11px;" href="<?=site_url('admin/getpicsandvideos/'.$hotel->dealid.'/'.$hotel->catid)?>">Delete Photos & Videos</a></span>
<span><a style="font-size: 11px;" href="<?=site_url('admin/addpicsandvideos/'.$hotel->dealid.'/'.$hotel->catid)?>">Add Photos & Videos</a></span>
<?php  if($this->session->userdata("usertype")=="1") {if($hotel->publish==0){?>
<span><a style="font-size: 11px;" href="<?=site_url('admin/publishdeal/'.$hotel->dealid.'/'.$hotel->catid).'/'.$hotel->publish?>">Publish</a></span>
<?php }else{?>
	<span><a style="font-size: 11px;" href="<?=site_url('admin/publishdeal/'.$hotel->dealid.'/'.$hotel->catid.'/'.$hotel->publish)?>">Unpublish</a></span>
<?php }}?>
</div>
</div>
	<div>
		<div><label style=" font-family:arial; font-weight: bold;">Address :</label><span style="font-size: 13px;font-family: arial;padding: 10px;"><?=$hotel->address ?></span></div>
		<div><label style=" font-family:arial;font-weight: bold;">City :</label><label style="font-size: 13px;font-family: arial;padding: 10px;"><?=$hotel->city?></label></div>
		<div><label style=" font-family:arial;font-weight: bold;">Deal Start Date :</label><label style="font-size: 13px;font-family: arial;padding: 10px;"><?=date("d/m/Y",$hotel->startdate)?></label></div>
		<div><label style="font-family:arial;font-weight: bold;">Deal End Date :</label><label style="font-size: 13px;font-family: arial;padding: 10px;"><?=date("d/m/Y",$hotel->enddate)?></label></div>
		<input type="hidden" name="dealid" value="<?=$hotel->dealid?>">
	</div>	
	<div style="margin-top:20px;text-align:left;margin-left:40px; font-family: arial;font-size:13px;font-weight: bold;color: #426C33;float:left; width: 100%;">Room Details</div>
	<?php 
	if($roomdetails!=FALSE)
	{
	foreach($roomdetails[$hotel->dealid] as $dealitem)
	{
	?>
	<div style="margin-top: 10px;margin-bottom: 10px;">
	<div><span style="font-weight: bold;font-family: arial;font-size: 13px;">Room Name :</span><span style="font-size: 13px;font-family: arial;padding: 10px;"><?=$dealitem->name?></span>
<div style="float: right;margin-right: 450px;" class="admhotellinks">
<?php //if($usertype!=1){?>
<span><a style="font-size: 13px;" href="<?=site_url('admin/editroom/'.$dealitem->id).'/'.$hotel->catid?>">Edit Room</a></span>
<span><a class="confirmroom" style="font-size: 11px;" href="<?=site_url('admin/removeroom/'.$dealitem->id.'/'.$hotel->catid)?>">Delete Room</a></span>
<?php //}?>
</div>
</div>
	<div style="float: left;margin-left: 0px;"><span style="font-weight: bold;font-family: arial;font-size: 13px;">Room Price :</span><span style="font-size: 13px;font-family: arial;padding: 10px;">Rs. <?=$dealitem->price?></span></div>
	</div>
	<?php }}
	else
	{
		echo '<div align="center" style="font-weight: bold;font-size: 13px;color: #FFFFFF;"><span>Sorry!!!.......No rooms found for this deal</span></div>';
	}
	?>	
</div>	
<?php 
}
}
}
}

else {
?>
<div align="center" style="font-weight: bold;font-size: 13px;color: #000000;"><span>Sorry!!!.......No Deals found</span></div>
<?php }?>
</div>