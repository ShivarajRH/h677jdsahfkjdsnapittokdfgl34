<!-- Skin CSS file -->
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.8.1/build/assets/skins/sam/skin.css">
<!-- Utility Dependencies -->
<script src="http://yui.yahooapis.com/2.8.1/build/yahoo-dom-event/yahoo-dom-event.js"></script> 
<script src="http://yui.yahooapis.com/2.8.1/build/element/element-min.js"></script> 
<!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->
<script src="http://yui.yahooapis.com/2.8.1/build/container/container_core-min.js"></script>
<script src="http://yui.yahooapis.com/2.8.1/build/menu/menu-min.js"></script>
<script src="http://yui.yahooapis.com/2.8.1/build/button/button-min.js"></script>
<!-- Source file for Rich Text Editor-->
<script src="http://yui.yahooapis.com/2.8.1/build/editor/editor-min.js"></script>
 
<script>
$(function(){
	 $('#toggleElement1').attr('checked', false);
	 $('#uploadbrandlogo1').attr('disabled',true);
});
var myEditor;
(function() {
    //Setup some private variables
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event;

        //The SimpleEditor config
        var myConfig = {
            height: '150px',
            width: '550px',
            dompath: true,
            handleSubmit: true
        };

        myEditor = new YAHOO.widget.Editor('branddescription', myConfig);
        myEditor.render();
        
})();


function disable()
{
	if ($('#toggleElement').is(':checked')) {
	    $('#uploadcatpic').attr('disabled',false);
	} else {
		 $('#uploadcatpic').attr('disabled', true);
	}  
}
function disablebrandlogo()
{
	if ($('#toggleElement1').is(':checked')) {
	    $('#uploadbrandlogo1').attr('disabled',false);
	} else {
		 $('#uploadbrandlogo1').attr('disabled', true);
	}  
}
function disablebrandlogo1()
{
	if ($('#toggleElement2').is(':checked')) {
	    $('#uploadbrandlogo2').attr('disabled',false);
	} else {
		 $('#uploadbrandlogo2').attr('disabled', true);
	}  
}
</script>
<style>
.ui-datepicker {
	font-size: 10px;
}

.container{
font-family:arial;
}

#main {
	font-family: arial;
	font-size: 11px;
	color: #000000;
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

.form input {
	border: 1px solid #9BCD9B; #
	background: #EBF8DC;
	width: 300px;
	padding:3px;
}

textarea {
	border: 1px solid #9BCD9B; #
	background: : #EBF8DC;
}

.form {
	background: #fff url(<?=base_url ()?>images/title-bg-top-small.gif )
		repeat-x scroll left top;
	border: #CCCCCC 1px solid;
	-moz-border-radius: 10px;
	float: left;
	padding: 10px;
}

.sidediv {
	float: left;
}
.dialoglabel{
font-size: 12px;
font-family: arial;
}
.ui-dialog .ui-dialog-titlebar {
padding:-1.5em 0.3em 0.3em 1em;
position:relative;
}
.ui-dialog-titlebar{
font-size:13px;
color:#fff;
}
.ui-dialog .ui-dialog-buttonpane button{
font-weight:bold;
font-size:12px;
}
</style>
<div class="heading" style="margin-bottom: 20px;">
<div class="headingtext container"><?php if(isset($edit_brand)){?>Edit Brand<?php } else {?>Edit Category<?php }?></div>
</div>
<div class="container yui-skin-sam">
<div align="center">
<div align="center" style="color:#00f;"><?php if(isset($info)) echo $info;?></div>
<div class="form" id="catdiv" style="margin-left: 30px;width:800px;">
<form action="<?php if(isset($edit_brand)) { echo site_url('admin/updatebrand');} else {echo site_url('admin/updatecategory'); }?>" method="post" enctype="multipart/form-data">
<?php
if (isset($edit_brand) && $specificbranddetails!=FALSE) {
	$e=$specificbranddetails;
	?>
<input name="brandid" type="hidden" value="<?=$specificbranddetails->id?>">
<table cellspacing=10">
<tr>
<td><label>Brand Name</label></td><td>:</td>
<td><input style="width: 300px;" name="brandname"
	id="brandname" value="<?=$specificbranddetails->name?>"></div>
</td></tr>
<tr>
<td><label>Description</label></td><td>:</td>
<td><textarea style="width: 300px;" name="branddescription"
	id="branddescription"><?=$specificbranddetails->description?></textarea></td>
	</tr>
	<tr>
<td><label>Website</label></td>
<td>:</td>
<td><input type="text" style="width: 300px;" name="brandwebsite"
	id="brandwebsite" value="<?=$specificbranddetails->website?>"></td>
</tr>
<tr>
<td><label>Email</label></td>
<td>:</td>
<td><input type="text" style="width: 300px;" name="brandemail"
	id="brandemail" value="<?=$specificbranddetails->email?>"></td>
</tr>
<tr>
<td>Featured Start</td>
<td>:</td>
<td>
<select name="fstarthrs" id="fstarthrs">
	<?php 	for($i=0;$i<24;$i++)	{?>
	<option <?php if(isset($e) && date("G",$e->featured_start)==$i) echo "selected"?> value="<?=$i?>"><?=date("g a",mktime($i))?></option>
	<?php }?>
	</select>
	<input id="fstartdatepicker" style="width:80px;margin-left: 2px;" type="text" name="fstartdate" id="fstartdate" value="<?php if(isset($e)) echo date("Y-m-d",$e->featured_start); else echo $this->input->post("fenddate");?>">
</td>
</tr>
<tr>
<td>Featured Start</td>
<td>:</td>
<td>
<select name="fendhrs" id="fendhrs">
	<?php 	for($i=0;$i<24;$i++)	{?>
	<option <?php if(isset($e) && date("G",$e->featured_start)==$i) echo "selected"?> value="<?=$i?>"><?=date("g a",mktime($i))?></option>
	<?php }?>
	</select>
	<input id="fenddatepicker" style="width:80px;margin-left: 2px;" type="text" name="fenddate" id="fenddate" value="<?php if(isset($e)) echo date("Y-m-d",$e->featured_end); else echo $this->input->post("fenddate");?>">
</td>
</tr>
<tr>
<td><label>Brand Logo</label></td><td>:</td>
<td>
<?php 
$logoid=$specificbranddetails->logoid;
?>
<img src="<?=base_url().'images/brands/'.$logoid.'.jpg'?>">
<div style="clear:both;"><div><label style="font-weight: normal;"><input id="toggleElement1" onclick="disablebrandlogo()" style="width:auto;" type="checkbox" name="check"> Check here to Update brandlogo</label></div><input id="uploadbrandlogo1" type="file" name="brandlogo"></div>
</td>
</tr>

</table>
<script>
$(function(){
	$("#fstartdatepicker, #fenddatepicker").datepicker({showOn: 'both', dateFormat: 'yy-mm-dd' ,minDate: new Date(<?=date("Y")?>,<?=date("n")?>-1,<?=date("j")?>), buttonImage: '<?=base_url()?>images/calendar_old.png', buttonImageOnly: true});
});
</script>	
		<?php
}
?>
<?php
//print_r($specificcatdetails);
if (isset($edit_cat) && $specificcatdetails!=FALSE) {
	?>
<input name="catid" type="hidden" value="<?=$specificcatdetails->id?>">
<input name="cattype" type="hidden" value="<?=$specificcatdetails->id?>">
<div class="innerdiv">
<div class="span"><label>Category Name</label></div>
<br>
<div class="span1"><input style="width: 300px;" name="catname"
	id="catname" value="<?=$specificcatdetails->name?>"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Description</label></div>
<br>
<div class="span1" style="margin-left: -60px;"><textarea style="width: 300px;" name="description"
	id="description"><?=$specificcatdetails->description?></textarea></div>
</div>
<div class="innerdiv">
<div class="span"><label>Category Pic</label>
<div style="margin-top: 3px;">
<input type="hidden" name="hidcatimg" id="hidcatimg" value="<?=$specificcatdetails->catimage ?>">
<img style="width: 75px;height: 75px;" src="<?=base_url().'images/catlogo/'.$specificcatdetails->catimage.'.jpg'?>">
</div>
</div>
<div class="span1"><div><label style="font-weight: normal;"><input id="toggleElement" onclick="disable()" style="width:auto;" type="checkbox" name="check"> Check here to Update image</label></div><input id="uploadcatpic" type="file" name="catpic"></div>

</div>		
<?php }?>
<div class="innerdiv" style="margin-top:10px;">
<div><input style="width: auto; float: right;" name="btnsubmit"
	type="submit" value="Update"></div>
</div>
</form>
</div>
</div>
</div>