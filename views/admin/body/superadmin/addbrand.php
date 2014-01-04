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
 
<style>
.container{
font-family:arial;
}
#main{
font: 11px;
font-family:arial;
}
.form{
background: url(<?=base_url()?>images/title-bg-top-small.gif) repeat-x scroll left top;
border:#CCCCCC 1px solid;
-moz-border-radius:10px;
padding: 10px;
margin-left:200px;
width: 550px;
}
.innerdiv{
width: 500px;
font-size:13px;
font-family:arial;
clear: both;
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
width:300px;
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
.mark{
color:#f00;
font-size:11px;
}
.cats{
width:auto !important;
}
</style>
<script type="text/javascript">
<!--
$(function(){
	$("#addform").submit(function(){
		if($(".cats:checked").length==0)
		{
			alert("Please assign atleast one category for the new brand");
			return false;
		}
		st="";
		$(".cats:checked").each(function(){
			st+=","+$(this).val();
		});
		$("#cats").val(st);
	});
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

//-->
</script>
<div class="heading" style="margin-bottom:10px;margin-top: 40px;">
<div class="headingtext container">Add Brand</div>
</div>
<div class="container yui-skin-sam" style="margin-top:30px;" align="center">
<div class="form" style="width:800px;margin-left:0px;">
<span style="text-align:right;width:200px;"><? if(strlen(validation_errors())>0) echo '<span style="margin-left:5px;color:#f00;float:right">:(</span>'.validation_errors('<span style="font-size:11px;color:#f00;">','</span><br>');?></span>
<form id="addform" action="<?=site_url("admin/procaddbrand")?>" method="post" enctype="multipart/form-data">
<div style="clear:both;font-family:'trebuchet ms';">Brand Details</div>
<div style="padding-left:20px;padding-top:10px;">
<div class="innerdiv">
<div class="span"><label>Brand Name<span class="mark">*</span></label></div>
<div class="span1"><input value="<?=set_value("username")?>" name="brandname" type="text"></div> 
</div>
<div class="innerdiv">
<div class="span"><label>Brand Logo</label></div>
<div class="span1"><input name="brandlogo" id="brandlogo" type="file"></div>
</div>
<div class="innerdiv">
<div class="span"><label>Description</label></div>
<div class="span1"><textarea rows="3" name="branddescription" id="branddescription" style="width: 300px;"></textarea></div>
</div>
<div class="innerdiv">
<div class="span"><label>Website</label></div>
<div class="span1"><input value="<?=set_value("website")?>"name="website" type="text" id="website"></div> 
</div>
<div class="innerdiv">
<div class="span"><label>Email</label></div>
<div class="span1"><input value="<?=set_value("email")?>" name="email" type="text" id="email"></div> 
</div>
</div>
<div style="clear:both;font-family:'trebuchet ms';">Assign category</div>
<div style="padding-left:20px;padding-top:10px;">
<div class="innerdiv">
<div class="span"><label>Categories<span class="mark">*</span> </label></div>
<div class="span1" id="assigncat"><i>assign category</i></div>
</div>
<div style="font-family:arial;font-size:13px;clear:both;">
<?php 
foreach($category as $cat){
?>
<span style="padding:3px;float:none;display:inline-block;border:1px solid #aaa;margin:3px;"><nobr><label><input type="checkbox" class="cats" value="<?=$cat->id?>"><?=$cat->name?></label></nobr></span>
<?php }?>
</div>
</div>
<input type="hidden" id="cats" name="cats" value="">
<div style="margin-top:10px;margin-left: 370px;"><input type="submit" style="width:110px;font-family:verdana;background:#426C33;padding:3px 5px;color:#efefef;font-weight:bold;font-size:15px;" value="Add Brand"></div>
</form>
</div>
</div>