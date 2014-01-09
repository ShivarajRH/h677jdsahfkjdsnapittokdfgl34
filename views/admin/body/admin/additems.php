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
dates_i=<?=($rooms*2)?>;
$(function(){
	$('.date').datepicker({showOn: 'both', dateFormat: 'yy/mm/dd' ,minDate: new Date(<?=date("Y")?>,<?=date("n")?>-1,<?=date("j")?>), buttonImage: '<?=base_url()?>images/calendar_old.png', buttonImageOnly: true});

	$(".nolimit").change(function(){
		if($(this).attr("checked")==true)
			$("input[type=text]",$(this).parent().parent()).attr("disabled",true);
		else
			$("input[type=text]",$(this).parent().parent()).attr("disabled",false);
	});
	$("textarea").focus(function(){
		$(this).css("height","200px");
		$(this).css("width","350px");
	});
//	$("textarea").blur(function(){
//		$(this).css("height","50px");
//		$(this).css("width","300px");
//	});

	$("#itemform").submit(function(){
        <?php for($i=0;$i<$rooms;$i++){?>
        myEditor[<?=$i?>].saveHTML();
        <?php }?>
        <?php for($i=0;$i<$rooms;$i++){?>
        ed[<?=$i?>].saveHTML();
        <?php }?>
		return true;
	});
	});
function add(i)
{
	dates_i+=2;
	$('#adddiv'+i).append('<div style="margin-left:-110px;"><label>From Date</label><input class="date" id="startdatepicker_'+(dates_i-1)+'" style="margin-left:90px;width:80px;" type="text" name="startdate_'+i+'[]"><label style="margin-left:20px;">To Date</label><input class="date" id="enddatepicker_'+dates_i+'" style="margin-left:30px;width:80px;margin-bottom: 10px;" type="text" name="enddate_'+i+'[]"></div>');
	$('.date').datepicker({showOn: 'both', dateFormat: 'yy/mm/dd' ,minDate: new Date(<?=date("Y")?>,<?=date("n")?>-1,<?=date("j")?>), buttonImage: '<?=base_url()?>images/calendar_old.png', buttonImageOnly: true});
}
function nextdiv(i)
{
	var a=i;
	$('#form_'+a).hide();
	$('#form_'+(a+1)).show();
	
}
function prevdiv(i)
{
	var a=i;
	$('#form_'+a).hide();
	$('#form_'+(a-1)).show();
}
myEditor=ed=new Array();
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

    //Now let's load the SimpleEditor..
        <?php for($i=0;$i<$rooms;$i++){?>
        myEditor[<?=$i?>] = new YAHOO.widget.Editor('bdescription_<?=$i?>', myConfig);
        myEditor[<?=$i?>].render();
        <?php }?>
        <?php for($i=0;$i<$rooms;$i++){?>
        ed[<?=$i?>] = new YAHOO.widget.Editor('description_<?=$i?>', myConfig);
        ed[<?=$i?>].render();
        <?php }?>
})();
</script>
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
width:900px;
margin-top: 40px;
margin-bottom:10px;
}
.innerdiv{
width: auto;
clear: both;
}
.innerdiv label{
font-weight: bold;
font-size: 13px;
}
.span{
width:180px;
margin-left:30px;
float: left;
margin-bottom: 10px;
}
.span1{
width:350px;
float: left;
margin-bottom: 10px;
text-align:justify;
}
.mark{
color:#f00;
font-size:12px;
}
.innerdiv input{
border:1px solid #9BCD9B;
width:300px;
padding:5px;
}
textarea{
border:1px solid #9BCD9B;
}
.form{
background: url(<?=base_url()?>images/title-bg-top-small.gif) repeat-x scroll left top;
border:#CCCCCC 1px solid;
-moz-border-radius:10px;
float: left;
width:900px;
padding: 10px;
}
.sidediv{
float:left;
}
</style>
<div id="main" class="yui-skin-sam">
<div class="heading" style="margin-bottom:20px;">
<div class="headingtext container">Add Items
<div style="float:right; font-family:'trebuchet ms';font-size:18px;color:#ffffff;margin-top: 5px;" >Step 2</div>
</div>
</div>
<div class="container">
<?php if(isset($error)){?>
<div align="left" style="color: red;"><span style="text-align: left;"><?=$error?></span></div>
<?php }?>
<form id="itemform" enctype="multipart/form-data" action="<?php echo site_url('admin/finishdeal') ?>" method="post">
<?php foreach($_POST as $pname=>$pval){?>
<input type="hidden" name="<?=$pname?>" value="<?=$pval?>">
<?php }?>
<input type="hidden" name="norooms" value="<?=$rooms?>">
<?php
//print_r($dealdetails);
//echo $dealid;
//echo $rooms;
if(isset($rooms))
{
	for($i=0;$i<$rooms;$i++)
	{
?>
<div class="form" id="form_<?=$i?>" style="margin-left: 30px;margin-bottom: 10px;<?php if($i>0) {?> display: none;<?php } ?>">
<span style="float:right;color:#ffcc00;font-size:12px"><span style="font-size:12px;font-weight:bold;">Adding Item : </span> 
<?php for($i2=0;$i2<$rooms;$i2++) {?>
<span <?php if($i==$i2) {?>style="font-weight:bold;color:#ff9900;font-size:14px;"<?php }?>><?php if($i2>0) echo '<span style="font-weight:normal">&raquo;</span>'?> <?=($i2+1)?></span>
<?php }?>
</span>
<p style="font-size: 12px;"><span style="margin-bottom: 15px;float:left;font-family: arial;font-weight: bold;font-size: 24px;color: #F78C0D;border-bottom: 1px solid #F78C0D;"><?php echo 'Item '.($i+1);?></span></p>
<div class="innerdiv">
<div class="span"><label>Item name<span class="mark">*</span> : </label></div>
<div class="span1"><input type="text" name="roomname_<?=$i?>" value="<?=$this->input->post("roomname_$i")?>"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Upload Pic<span class="mark">*</span></label></div>
<div class="span1"><input type="file" name="pic_<?=$i?>"></div>
</div>
<?php /*if($catid==4){?>
<div class="innerdiv">
<div class="span"><label>Heading</label></div>
<div class="span1"><input type="text" name="heading_<?=$i?>" id="heading_<?=$i?>" value="<?=$this->input->post("heading_$i")?>"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Tagline</label></div>
<div class="span1"><input type="text" name="tagline_<?=$i?>" id="tagline_<?=$i?>" value="<?=$this->input->post("tagline$i")?>"></div>
</div>
<div class="innerdiv">
<p align="left" style="font-weight: bold;color:#F79B0A;"> <span style="border-bottom: 1px solid #CCCCCC;"> Avalibility Dates </span></p>
<div style="float: left;" id="adddiv<?=$i?>">
<a href="javascript:void(0)" style="float:right;font-size: 12px;color:#4169E1;" id="add_<?=$i?>" onclick="add(<?=$i?>)">Add Availability dates</a>
<div class="dates<?=$i?>" style="width:575px;">
	<label>From Date</label><input class="date" id="startdatepicker_<?=$i?>" style="margin-left:90px; width:80px;" type="text" name="startdate_<?=$i?>[]">
	<label style="margin-left: 20px;">To Date</label><input class="date" id="enddatepicker_<?=$i?>" style="margin-left:30px; width:80px;margin-bottom: 10px;" type="text" name="enddate_<?=$i?>[]">
</div>
</div>
</div>
<?php }
else
{*/?>
<div class="innerdiv">
<div class="span"><label>
<?php if($this->input->post("dealtype")=="brandsale") echo "Quantity"; else echo "Activation Threshold";?>
<span class="mark">*</span></label></div>
<div class="span1">
<input type="text" name="noofitems_<?=$i?>" style="width:70px;text-align: right;" value="<?=$this->input->post("noofitems_$i")?>"> 
<?php if($this->input->post("dealtype")=="brandsale") {?>
<label style="font-weight:normal"><input type="checkbox" class="nolimit">No Limit</label>
<?php }?>
<div style="color:#999;font-size:10px;"><?php if($this->input->post("dealtype")=="brandsale") echo "Quantity of this item available for sale"; else echo "Minimum number of buys required to trigger the deal";?></div>
</div>
</div>
<?php //}?>
<div class="innerdiv">
<div class="span"><label>Price<span class="mark">*</span></label></div>
<div class="span1">Rs. <input type="text" name="price_<?=$i?>" style="width:70px;text-align: right;"  id="price_<?=$i?>" value="<?=$this->input->post("price_$i")?>"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Original Price<span class="mark">*</span></label></div>
<div class="span1">Rs. <input type="text" name="originalprice_<?=$i?>" style="width:70px;text-align: right;" id="originalprice_<?=$i?>" value="<?=$this->input->post("originalprice_$i")?>"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Brief Description<span class="mark">*</span></label></div>
<div class="span1"><textarea style="width:300px;height:50px;" name="bdescription_<?=$i?>" id="bdescription_<?=$i?>"><?=$this->input->post("bdescription_$i")?></textarea><div style="color:#777;font-size:10px;">Displayed in bold letters. Please make it brief and sweet.</div></div>
</div>
<div class="innerdiv">
<div class="span"><label>Extra Description<span class="mark">*</span></label></div>
<div class="span1"><textarea style="width:300px;height:50px;" name="description_<?=$i?>" id="description_<?=$i?>"><?=$this->input->post("description_$i")?></textarea><div style="color:#777;font-size:10px;">Long description about the item goes here</div></div>
</div>
<div class="innerdiv" align="right">
<?php if($i!=0){?>
<input style="float:left;width:auto; padding: 3px 5px; background: #35f none repeat scroll 0% 0%; font-family: verdana; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: rgb(239, 239, 239); font-weight: bold; font-size: 13px;" type="button" value="Back" onclick="prevdiv(<?=$i?>)">
<?php }?>
<input style="width:90px; padding: 3px 5px; background: #2D6A2E none repeat scroll 0% 0%; font-family: verdana; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: rgb(239, 239, 239); font-weight: bold; font-size: 15px;" <?php if(($i+1)==$rooms) echo 'type="submit"'; else echo 'type="button"';?>  name="submit" value="Continue" <?php if(($i+1)!=$rooms){?>onclick="nextdiv(<?=$i?>);"<?php }?>>
</div>
</div>
<?php 
	}
	}
?>
</form>
</div>
</div>