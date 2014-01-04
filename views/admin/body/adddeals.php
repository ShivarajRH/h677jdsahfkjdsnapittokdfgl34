<?php $user=$this->session->userdata("admin_user");?>

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
	$("input[name=dealtype] option:first-child").attr("checked",true);
	$(".nolimit").change(function(){
		if($(this).attr("checked")==true)
			$("input[type=text]",$(this).parent().parent()).attr("disabled",true);
		else
			$("input[type=text]",$(this).parent().parent()).attr("disabled",false);
	});
	$("input[name=dealtype]").change(function(){
		if($(this).val()=="brandsale")
		{
			$("#qlab").html("Quanity");
			$("input[name=quantity]").attr("disabled",false);
			$("#qcheck").show();
			$(".nolimit").attr("checked",false);
		}
		else
		{
			$("#qlab").html("Activation Threshold");
			$("input[name=quantity]").attr("disabled",false);
			$("#qcheck").hide();
		}
	});
	$("#categoryname").val(0);
	$("#startdatepicker, #enddatepicker").datepicker({showOn: 'both', dateFormat: 'yy-mm-dd' ,minDate: new Date(<?=date("Y")?>,<?=date("n")?>-1,<?=date("j")?>), buttonImage: '<?=base_url()?>images/calendar_old.png', buttonImageOnly: true});
	$("#categoryname").change(function(){
		$.get("<?=site_url("admin/jx_getbrand/")?>/"+$(this).val(),function(da){
			$("#brandsel").html(da);
		});
	});
	});
function disable()
{
	if ($('#toggleElement').is(':checked')) {
	    $('#uploadpic').removeAttr('disabled');
	} else {
		 $('#uploadpic').attr('disabled', true);
	}  
}
var myEditor,ed;
(function() {
    //Setup some private variables
    var Dom = YAHOO.util.Dom,
        Event = YAHOO.util.Event;

        //The SimpleEditor config
        var myConfig = {
            height: '150px',
            width: '650px',
            dompath: true,
            handleSubmit: true
        };

        myEditor = new YAHOO.widget.Editor('bdescription', myConfig);
        myEditor.render();
        ed = new YAHOO.widget.Editor('description', myConfig);
        ed.render();
        
})();

</script>
<?php 
if(isset($is_edit))
{
	echo '<script>';
	echo '$(function(){$("#uploadpic").attr("disabled",true);});';
	echo '</script>';
}
?>
<style>
.ui-datepicker{
font-size:10px;
}
#main{
font-family: arial;
font-size: 11px;
color:#000000;
}
.maindiv{
width: 750px;
margin-top: 40px;
}
<?php if(isset($is_edit)){?>
.innerdiv{
width: 500px;
float:left;
clear: both;
}
<?php } else {?>
.innerdiv{
width: 500px;
clear: both;
float:left;
}
<?php }?>
.innerdiv label{
font-weight: bold;
font-size: 13px;
}
.span{
width:150px;
float: left;
margin-bottom: 10px;
text-align:justify;
}
.span1{
width:350px;
float: left;
margin-bottom: 10px;
text-align:justify;
}
.innerdiv input{
border:1px solid #9BCD9B;
#background:#EBF8DC;
width:300px;
padding:5px;
}
.innerdiv textarea{
border:1px solid #9BCD9B;
#background: :#EBF8DC;
}
.innerdiv select{
padding:5px;
-moz-border-radius:5px;
border:1px solid #9BCD9B;
#background: #EBF8DC;
}
.amenities
{
float:left;
}
 .innerdiv .forminfo{
 color:#999;font-size:10px;
 }
.mark{
color:#f00;
font-size:12px;
}
.form{
background: url(<?=base_url()?>images/title-bg-top-small.gif) repeat-x scroll left top;
border:#CCCCCC 1px solid;
-moz-border-radius:10px;
float: left;
padding: 10px;
margin-left: 120px;
}
</style>
<?php
$e=false; 
if(isset($is_edit))
{
	$e=true;
	$d=$dealdetails;
?>
<div class="heading" style="margin-bottom:20px;">
<div  class="headingtext container">Edit deal</div>
</div>
<?php }else{?>
<div class="heading" style="margin-bottom:20px;">
<div class="headingtext container">Add new deal
<div style="float:right; font-family:'trebuchet ms';font-size:18px;color:#ffffff;margin-top: 5px;" >Step 1</div>
</div>
</div>
<?php }?>
<div id="main" class="container yui-skin-sam" style="padding:0px">
<?php 
//print_r($dealdetails);
if(isset($dealdetails)){
$catid=$dealdetails->catid;
//echo $catid;
//echo date($dealdetails->startdate);
$selstartdate=date("G",$dealdetails->startdate);
$selenddate=date("G",$dealdetails->enddate);
/*print_r($selstartdate);
print_r($selenddate);exit;*/
}
?>
<div align="left" class="form_error" style="color: red;margin-left: 20px;"><?=validation_errors("<div>","</div>")?>
		<?php if(isset($autherror))
		{
		echo $autherror;
		}?>
</div>
<?php if(isset($error)){?>
<div style="margin-left:20px;text-align:left;color: red;"><?=$error?></div>
<?php }?>
<div class="form" style="width:97%;margin:10px;">
<form action="<?php 
if(isset($is_edit))
echo site_url('admin/updatedeal');
else
echo site_url('admin/finishdeal');?>" enctype="multipart/form-data" method="post">
<input type="hidden" name="dealid" value="<?php if(isset($dealdetails)) echo $dealdetails->dealid?>">
<input type="hidden" name="brandid" value="<?=$brandid?>">
<input type="hidden" name="catid" value="<?php if(isset($dealdetails)) echo $dealdetails->catid?>">
<?php if(isset($dealdetails)){?>
<div class="innerdiv">
<div class="span"><label>Category <span class="mark">*</span></label></div>
<div class="span1"><input type="text" readonly="readonly" style="width:300px;" name="catname" id="catname" value="<?php if(isset($dealdetails)) echo $dealdetails->name;?>"></div>
</div>
<?php } else {?>
<div class="innerdiv">
<div class="span"><label>Category<span class="mark">*</span></label></div>
<div class="span1">
<select name="categoryname" id="categoryname" style="">
<option value="0">----Select----</option>
<?php 
foreach($adm_categories as $category){
?>
<option value="<?=$category->catid?>"><?=$category->name?></option>
<?php }?>
</select>
</div>
</div>
<div class="innerdiv">
<div class="span"><label>Brand<span class="mark">*</span></label></div>
<div class="span1" id="brandsel">
<b>Choose category</b>
</div>
</div>
<div id="subcat" class="innerdiv" style="display: none;">
</div>
<?php }?>
<div class="innerdiv">
<div class="span"><label>Profile Pic<span class="mark">*</span></label><?php if(isset($dealdetails)){?>
<div style="margin-top: 3px;">
<img style="width: 75px;height: 75px;" src="<?=base_url().'images/items/'.$dealdetails->pic.'.jpg'?>">
</div>
<?php }?></div>
<div class="span1"><?php if(isset($is_edit)){?><div><label style="font-weight: normal;"><input id="toggleElement" onclick="disable()" style="width:auto;" type="checkbox" name="check"> Check here to Update image</label></div><?php }?><input id="uploadpic" type="file" name="pic"></div>

</div>
<div class="innerdiv">
<div class="span"><label>Deal Name<span class="mark">*</span></label></div>
<div class="span1"><input style="width:300px;" name="tagline" id="tagline" value="<?php if(isset($dealdetails)) echo $dealdetails->tagline; else echo $this->input->post('tagline');?>">
<div class="forminfo">Eye-catching short line for your deal</div></div>
</div>
<div class="innerdiv">
<div class="span"><label>MRP<span class="mark">*</span></label></div>
<div class="span1">Rs. <input type="text" name="mrp" style="width:70px;text-align: right;" id="mrp" value="<?php if($e) echo $d->orgprice; else echo $this->input->post("mrp")?>"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Offer Price<span class="mark">*</span></label></div>
<div class="span1">Rs. <input type="text" name="price" style="width:70px;text-align: right;"  id="price" value="<?php if($e) echo $d->price; else echo $this->input->post("price")?>"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Price for VIA<span class="mark">*</span></label></div>
<div class="span1">Rs. <input type="text" name="viaprice" style="width:70px;text-align: right;" id="viaprice" value="<?php if($e) echo $d->viaprice; else echo $this->input->post("viaprice")?>"></div>
</div>
<div class="innerdiv">
<div style="float: left;">
	<label>Deal Start Date<span class="mark">*</span></label>
	<select name="starthrs" id="starthrs" style="margin-left: 40px;">
	<?php 	for($i=0;$i<24;$i++)	{	?>
	<option <?php if(isset($dealdetails) && $selstartdate==$i) echo "selected"?> value="<?=$i?>"><?=date("g a",mktime($i))?></option>
	<?php }?>
		<option value="24">Midnight</option>
	</select>
	<input id="startdatepicker" style="width:80px;margin-left: 6px;" type="text" name="startdate" id="startdate" value="<?php if(isset($dealdetails)) echo date("Y-m-d",$dealdetails->startdate); else echo $this->input->post("startdate");?>">
</div>
</div>
<div class="innerdiv">
<div style="float: left;margin-top: 10px;margin-bottom: 10px;">
	<label>Deal End Date<span class="mark">*</span></label>
	<select name="endhrs" id="endhrs" style="margin-left: 50px;">
	<?php 	for($i=0;$i<24;$i++)	{?>
	<option <?php if(isset($dealdetails) && $selenddate==$i) echo "selected"?> value="<?=$i?>"><?=date("g a",mktime($i))?></option>
	<?php }?>
	<option value="24">Midnight</option>
	</select>
	<input id="enddatepicker" style="width:80px;margin-left: 2px;" type="text" name="enddate" id="enddate" value="<?php if(isset($dealdetails)) echo date("Y-m-d",$dealdetails->enddate); else echo $this->input->post("enddate");?>">
</div>
</div>
<div class="innerdiv">
<div class="span"><label>Website link</label></div>
<div class="span1"><input style="width:300px;" name="website" id="website" value="<?php if(isset($dealdetails)) echo $dealdetails->website; else echo $this->input->post('website');?>"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Email</label></div>
<div class="span1"><input style="width:300px;" name="email" id="email" value="<?php if(isset($dealdetails)) echo $dealdetails->email; else echo $this->input->post('email');?>"></div>
</div>
<?php if(!$e){?>
<div class="innerdiv" align="left" style="background:#eee;margin-bottom:10px;">
<div style="padding:10px 0px 0px 10px;"><label>Deal Type</label></div>
<div style="padding-left:90px;"><label style="font-weight:normal;font-size:inherit"><input checked type="radio" name="dealtype" value="brandsale"><b>Brand Sale</b> - Deal is valid for all buyers</label></div>
<div style="padding-left:90px;padding-bottom:10px;"><label style="font-weight:normal;font-size:inherit"><input type="radio" name="dealtype" value="groupsale"><b>Group Sale</b> - Deal is valid only after certain number of buys</label></div>
</div>
<?php }?>
<div class="innerdiv">
<div class="span"><label id="qlab">
Quantity
<span class="mark">*</span></label></div>
<div class="span1">
<input type="text" name="quantity" style="width:70px;text-align: right;" value="<?php if($e) echo $d->quantity; else echo $this->input->post("quantity")?>"> 
<label style="font-weight:normal" id="qcheck"><input type="checkbox" class="nolimit">No Limit</label>
<div style="color:#999;font-size:10px;">Quantity of this item available for sale (brand sale)<br>(or) Minimum number of buys required to trigger the deal (Group sale)</div>
</div>
</div>
<div class="innerdiv">
<div class="span"><label>Brief Description<span class="mark">*</span></label></div>
<div class="span1"><textarea style="width:300px;height:50px;" name="bdescription" id="bdescription"><?php if($e) echo $d->description1; else echo $this->input->post("bdescription")?></textarea><div style="color:#777;font-size:10px;">Displayed in bold letters. Please make it brief and sweet.</div></div>
</div>
<div class="innerdiv">
<div class="span"><label>Extra Description<span class="mark">*</span></label></div>
<div class="span1"><textarea style="width:300px;height:50px;" name="description" id="description"><?php if($e) echo $d->description2; else echo $this->input->post("description")?></textarea><div style="color:#777;font-size:10px;">Long description about the item goes here</div></div>
</div>
<?php 
if(!isset($is_edit))
{
?>
<?php /*?>
<div class="innerdiv" style="margin-top: 20px;">
<div class="span"><label id="nocount">Items for sale<span class="mark">*</span></label></div>
<div class="span1">
<select id="rooms" name="rooms">
<option value="0" selected="selected">--Select--</option>
<?php for($i=1;$i<=20;$i++){?>
<option value="<?=$i?>"><?=$i?></option>
<?php }?>
</select>
</div>
</div>
*/?>
<input type="hidden" name="rooms" value="1">
<?php }?>
<div class="innerdiv"><input style="margin-top: 30px;margin-left:286px; width:90px; padding: 3px 5px; background:#2D6A2E none repeat scroll 0% 0%; font-family: verdana; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: rgb(239, 239, 239); font-weight: bold; font-size: 15px;" type="submit" name="continue" 
value="<?php 
if(isset($is_edit))
echo 'Update';
else
echo 'Add  Deal';
?>"></div>
</form>
</div>
<div style="float:left;">

</div>
</div>