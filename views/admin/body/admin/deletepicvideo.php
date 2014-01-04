<script>
$(function(){
	$('.deletepic').click(deletepic);
	$('.deletevideo').click(deletevideo);
});
function deletepic()
{
	if(confirm("Are you sure want to delete this pic?")==true)
		return true;
	else
		return false;
}
function deletevideo()
{
	if(confirm("Are you sure want to delete this video?")==true)
		return true;
	else
		return false;
}
</script>
<style>
#main{
font-family: arial;
margin-top: 40px;
}
.main{
width:900px;
}
.form{
background: url(<?=base_url()?>images/title-bg-top-small.gif) repeat-x scroll left top;
border:#CCCCCC 1px solid;
-moz-border-radius:10px;
float: left;
padding: 10px;
margin-left: 200px;
}
</style>
<div class="heading" style="margin-bottom:20px;margin-top: 40px;">
<div class="headingtext container">Delete Pics or Videos</div>
</div>
<div id="main" class="main">

<?php 
/*print_r($pictures);
echo '<br>';
print_r($videos);*/
?>
	<div class="form" align="center">
	<?php if($pictures!=FALSE){ if(isset($pictures)){ foreach($pictures as $pic){?>
		<div style="float: left; margin-left: 20px;margin-top: 10px;">
			<input type="hidden" name="dealid" id="dealid" value="<?=$pic->dealid?>">
			<input type="hidden" name="roomid" id="roomid" value="<?=$pic->itemid?>">
			<img style="width: 150px;height: 150px;" src="<?=base_url().'images/items/'.$pic->id.'.jpg'?>">			
			<br><div style="margin-top: 10px;margin-bottom: 10px;"><span style="font-weight:bold;text-align: center;font-size: 12px;"><?=$pic->name?></span></div>
			<a style="font-weight: normal;text-align: center;font-size: 12px;" class="deletepic" href="<?=site_url('admin/deletehotelpic/'.$pic->id."/$pic->dealid/$pic->itemid")?>">Delete</a>
		</div>		
	<?php }}}
	else {
	echo '<div align="center" style="font-weight:bold;font-size:13px;"><span>No EXtra Pics found for this deal</span></div>';
	}?>	
	</div>
	<?php if($videos!=FALSE){?>
		<div class="form" align="center" style="margin-top: 40px;">
		Videos
	<?php if($pictures!=FALSE){ if(isset($pictures)){ foreach($videos as $ved){?>
		<div style=" margin-left: 20px;margin-top: 10px;">
		<span style="font-weight:bold;text-align: center;font-size: 12px;margin-top: 20px;"><?=$ved->name?></span><br>
			<input type="hidden" name="dealid" id="dealid" value="<?=$ved->dealid?>">
			<input type="hidden" name="roomid" id="roomid" value="<?=$ved->itemid?>">
			<img src="http://i3.ytimg.com/vi/<?=$ved->id?>/default.jpg">
			<span style="font-family: arial;font-size: 12px;">http://www.youtube.com/watch?v=</span><span style="font-family: arial;font-size: 12px;"><?=$ved->id?></span>
			<a style="font-weight: normal;text-align: center;font-size: 12px;" class="deletevideo" href="<?=site_url('admin/deletehotelvideo/'.$ved->id."/$ved->dealid"."/$ved->itemid")?>">Delete</a>
		</div>		
	<?php }}}
	else {
	echo '<div align="center" style="font-weight:bold;font-size:13px;"><span>No EXtra Pics found for this deal</span></div>';
	}?>
	</div>
	<?php }?>
</div>