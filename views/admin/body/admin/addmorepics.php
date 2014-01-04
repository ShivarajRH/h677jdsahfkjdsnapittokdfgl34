<script>
piccount=1;
function add()
{
	if(piccount==10)
	{
		alert('You can upload only 10 pics');
		return;
	}
	$('#morepics').append('<div style="margin-left:115px;padding:2px 0px;"><input type="file" name="pic_'+piccount+'"></div>');
	piccount++;
}
function uploadvideo()
{
	$('#uploadvideo').append('<div class="videos" style="padding:2px 0px;"><label style="color: #4876FF;">http://www.youtube.com/watch?v=</label><input  id="video" style="margin-left:5px; width:170px;" type="text" name="video[]"></div>');
}
</script>
<style>
#main{
font-family: arial;
font-size: 11px;
color:#000000;
}
.maindiv{
font-size:12px;
width:550px;
margin-bottom:10px;
}
.innerdiv{
font-family:arial;
margin-top:30px;
clear: both;
}
.innerdiv label{
font-weight: bold;
}
.span{
width:150px;
float: left;
margin-bottom: 10px;
text-align:justify;
}
.span1{
float: left;
margin-bottom: 10px;
text-align:justify;
}
input{
-moz-border-radius:5px;
border:1px solid #9BCD9B;
#background:#EBF8DC;
width:225px;
}
textarea{
-moz-border-radius:5px;
border:1px solid #9BCD9B;
#background: :#EBF8DC;
}
select{
-moz-border-radius:5px;
border:1px solid #9BCD9B;
#background: #EBF8DC;
}
.form{
background: url(<?=base_url()?>images/title-bg-top-small.gif) repeat-x scroll left top;
border:#CCCCCC 1px solid;
-moz-border-radius:10px;
float: left;
margin-left:250px;
padding: 10px;
font-size:12px;
}
</style>
<?php 
//print_r($rooms);
//print_r($itemdetails);exit;
//echo '<br>';
//print_r($dealid);
//exit;
?>

<div class="heading" style="margin-bottom:20px;">
<div class="container headingtext">Add Pics and Videos</div>
</div>
<div class="container" align="center">
<div style="float:right;"><span style="text-align:right;width:200px;"><? if(strlen(validation_errors())>0) echo '<span style="margin-left:5px;color:#f00;float:right">:(</span>'.validation_errors('<span style="font-size:12px;color:#f00;">','</span><br>');?></span></div>
<form enctype="multipart/form-data" action="<?= site_url('admin/addresources') ?>" method="post">
<input type="hidden" name="dealid" value="<?php if(isset($rooms)) echo $id; else echo $dealid;?>">
<div class="form">
<div class="innerdiv">
<div class="span"><label>Select Item</label></div>
<div class="span1">
<select id="roomtype" name="roomtype">
<?php 
if(isset($rooms))
{
	foreach($rooms as $det)
	{

?>
<option selected="selected" value="<?=$det->id?>"><?=$det->name?></option>
<?php 
	}
}
else
{
foreach($itemdetails as $details)
	{
?>
<option selected="selected" value="<?=$details->id?>"><?=$details->name?></option>
<?php 
	}
	}?>
</select>
</div>
</div>
<div class="innerdiv" id="morepics" style="width:500px;">
<a href="javascript:void(0)" style="float:right;font-size: 12px;color:#4169E1;" id="addpics" onclick="add()">Upload More Pics</a>
<div class="pics">
<label>Upload Pics</label>
<input style="margin-left: 49px;" type="file" name="pic_0">
</div>
</div>
<div class="innerdiv">
<p style="font-weight: bold;">Upload videos</p>
<div style="float: left;margin-left: 0px;" id="uploadvideo">
<div class="videos" style="width:500px; margin-bottom: 10px;">
	<label style="color: #4876FF;">http://www.youtube.com/watch?v=</label><input  id="video" style="margin-left:5px; width:170px;" type="text" name="video[]">	
</div>
<a href="javascript:void(0)" style="float:right;font-size: 12px;color:#4169E1;" id="addvideos" onclick="uploadvideo()">Add More Videos</a>
</div>
</div>
<div class="innerdiv">
<div>
		<label><input style="width:auto;" type="checkbox" checked="checked" value="yes" name="picpage"/><span style="margin-left:5px;color: #000;font-size:12px;">Continue uploading photos for other items</span></label>
</div>
</div>
<div class="innerdiv"><input style="margin-left:340px; width:90px; padding: 3px 5px; background: #2D6A2E none repeat scroll 0% 0%; font-family: verdana; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: rgb(239, 239, 239); font-weight: bold; font-size: 15px;" type="submit" name="submit" value="submit"></div>
</div>

</form>
</div>