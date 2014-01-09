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
	$('.date').datepicker({showOn: 'both', dateFormat: 'yy/mm/dd' ,minDate: new Date(<?=date("Y")?>,<?=date("n")?>-1,<?=date("j")?>), buttonImage: '<?=base_url()?>images/calendar_old.png', buttonImageOnly: true});
	$(".nolimit").change(function(){
		if($(this).attr("checked")==true)
			$("input[type=text]",$(this).parent().parent()).attr("disabled",true);
		else
			$("input[type=text]",$(this).parent().parent()).attr("disabled",false);
	});
	$("#uploadpic").attr("disabled",true);
//	$("#editform").submit(function(){
//		myEditor.saveHTML();
//		alert($("textarea[name=description1]").val());
//		ed.saveHTML();
//		return false;
//	});
	});
function add()
{
	dates_i+=2;
	$('#adddiv').append('<div><label>From Date</label><input class="date" id="startdatepicker_'+(dates_i-1)+'" style="margin-left:30px;width:80px;" type="text" name="startdate[]"><label style="margin-left:20px;">To Date</label><input class="date" id="enddatepicker_'+dates_i+'" style="margin-left:30px;width:80px;margin-bottom: 10px;" type="text" name="enddate[]"></div>');
	$('.date').datepicker({showOn: 'both', dateFormat: 'yy/mm/dd' ,minDate: new Date(<?=date("Y")?>,<?=date("n")?>-1,<?=date("j")?>), buttonImage: '<?=base_url()?>images/calendar_old.png', buttonImageOnly: true});
}
function disable()
{
	if ($('#toggleElement').is(':checked')) {
	    $('#uploadpic').removeAttr('disabled');
	} else {
		 $('#uploadpic').attr('disabled', true);
	}  
}

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
        var myEditor = new YAHOO.widget.Editor('bdescription', myConfig);
        myEditor.render();
        var ed = new YAHOO.widget.Editor('description', myConfig);
        ed.render();
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
margin-top:30px;
width: 500px;
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
width:350px;
float: left;
margin-bottom: 10px;
text-align:justify;
}
.form input{
border:1px solid #9BCD9B;
#background:#EBF8DC;
width:300px;
padding:5px;
}
.form textarea{
-moz-border-radius:5px;
border:1px solid #9BCD9B;
#background: :#EBF8DC;
}
.form select{
-moz-border-radius:5px;
border:1px solid #9BCD9B;
#background: #EBF8DC;
}
.form{
background: url(<?=base_url()?>images/title-bg-top-small.gif) repeat-x scroll left top;
border:#CCCCCC 1px solid;
-moz-border-radius:10px;
float: left;
padding: 10px;
width:850px;
margin-left: 20px;
}
</style>
<?php 
//print_r($roomdetails);
//print_r($catid);


	if(isset($roomdetails)&& isset($roomdetails->availability))
	{
	$dates=explode(',',$roomdetails->availability);
	//print_r(count($dates));exit;
	//print_r($dates);exit;
	/*$namei = strpos($route, '-' );
     $fromCityName = substr ( $route, 0, $namei );
		$toCityName = substr ( $route,$namei+1);	*/
	$sdate=array();
	$edate=array();
	for($i=0;$i<count($dates);$i++){
		$pos=strpos($dates[$i],'-');
		$sdate[$i]=substr($dates[$i],0,$pos);
		$edate[$i]=substr($dates[$i],$pos+1);
	}
	}
	//print_r($sdate);exit;

//print_r($roomdetails);exit;
?>
<script>
<?php /*?>
dates_i=<?=count($dates)*2?>;
*/?>
</script>
<div id="main" class="yui-skin-sam">
<?php // echo $catid;?>
<div class="heading" style="margin-bottom:20px;">
<div class="headingtext container"><?php if(isset($edit_room)){?>Edit Room for Hotel deal<?php }else {?> Edit Item for Deal<?php }?></div>
</div>
<div class="container">
<?php if(isset($error)){?>
<div style="text-align:center;color: red;"><span style="text-align: justify;"><?=$error?></span></div>
<?php }?>
<form id="editform" enctype="multipart/form-data" action="<?php echo site_url('admin/updateitemdetails') ?>" method="post">
<input type="hidden" name="roomid" value="<?=$roomdetails->id?>">
<input type="hidden" name="dealid" value="<?=$roomdetails->dealid?>">
<input type="hidden" name="brandid" value="<?=$roomdetails->brandid?>">
<div class="form" align="left" style="padding-left:40px;">
<div class="innerdiv">
<div class="span"><label>Item Name</label></div>
<div class="span1"><input type="text" name="roomname" value="<?php if(isset($roomdetails)) echo $roomdetails->name?>"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Brief description</label></div>
<div class="span1"><textarea style="width: 300px;" rows="6" name="description1" id="bdescription"><?php if(isset($roomdetails->description1)) echo $roomdetails->description1?></textarea></div>
</div>
<div class="innerdiv">
<div class="span"><label>Extra description</label></div>
<div class="span1"><textarea style="width: 300px;" rows="6" name="description2" id="description"><?php if(isset($roomdetails->description2)) echo $roomdetails->description2?></textarea></div>
</div>
<div class="innerdiv">
<div class="span"><label>
<?php if($roomdetails->dealtype==1){?>
Activation Threshold
<?php }else echo "Quantity";?>
</label></div>
<div class="span1">
<?php if($roomdetails->dealtype==0){?>
<?php if($roomdetails->quantity=="4294967295"){?>
<input style="display:none" type="text" name="quantity" id="quantity" value="<?php if(isset($roomdetails->quantity)) echo $roomdetails->quantity?>"> No Limit
<?php }else{?>
<input type="text" name="quantity" id="quantity" value="<?php if(isset($roomdetails->quantity)) echo $roomdetails->quantity?>">
<label style="font-weight:normal"><input type="checkbox" class="nolimit">No Limit</label>
<?php }}else{?>
<input type="text" name="quantity" id="quantity" value="<?php if(isset($roomdetails->quantity)) echo $roomdetails->quantity?>">
<?php }?>
</div>
</div>
<div class="innerdiv">
<div class="span"><label>Upload Pic</label>
<div style="margin-top: 3px;">
<img style="width: 75px;height: 75px;" src="<?=base_url().'images/items/'.$roomdetails->pic.'.jpg'?>">
</div>
</div>
<div class="span1"><div><label style="font-weight: normal;"><input id="toggleElement" onclick="disable()" style="width:auto;" type="checkbox" name="check"> Check here to Update image</label></div><input id="uploadpic" type="file" name="pic"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Price</label></div>
<div class="span1"><input type="text" name="price" id="price" value="<?php if(isset($roomdetails)) echo $roomdetails->price?>"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Original Price</label></div>
<div class="span1"><input type="text" name="originalprice" id="originalprice" value="<?php if(isset($roomdetails)) echo $roomdetails->orgprice?>"></div>
</div>
<div class="innerdiv"><input style="margin-top: 30px;margin-left:286px; width:90px; padding: 3px 5px; background: #426D34 none repeat scroll 0% 0%; font-family: verdana; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: rgb(239, 239, 239); font-weight: bold; font-size: 15px;" type="submit" name="submit" value="Update"></div>
</div>
</form>
</div>
</div>
